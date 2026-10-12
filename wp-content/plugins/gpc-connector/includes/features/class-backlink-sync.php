<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sends the links added to a post to the CRM backlink module when the post
 * is saved. Links the live post already had are not sent again. Replaces the
 * "Rightee Backlink Tracker" plugin.
 */
final class GPC_Connector_Backlink_Sync
{
    const MODULE = 'outr_seo_backlinksmain';

    /** Content of the published version each post had before this save, by post ID. */
    private $previous_live_content = [];

    private $excluded_domains = [
        'outrightsystems.org',
        'outrightcrm.com',
        'store.outrightcrm.com',
        'guestpostcrm.com',
        'extractmails.com',
        'mailsextract.com',
        'twitter.com',
        'facebook.com',
        'linkedin.com',
        'instagram.com',
    ];

    public function __construct()
    {
        // post_updated fires just before save_post and passes the previous
        // version of the post, which is what tells new links from old ones.
        add_action('post_updated', [$this, 'remember_previous_version'], 10, 3);
        add_action('save_post', [$this, 'handle_post_save'], 10, 3);
    }

    /**
     * Only the published version counts as existing links. A draft has nothing
     * live yet, so all its links are new; that also re-sends them on first
     * publish, when their records get the public URL instead of ?p=123.
     */
    public function remember_previous_version($post_id, $post_after, $post_before)
    {
        $this->previous_live_content[$post_id] = $post_before->post_status === 'publish'
            ? $post_before->post_content
            : '';
    }

    public function handle_post_save($post_id, $post, $update)
    {
        // New posts never fire post_updated, so they have no previous version.
        $previous_content = $this->previous_live_content[$post_id] ?? '';
        unset($this->previous_live_content[$post_id]);

        if ($post->post_type !== 'post') {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Also skips saves made by the CRM through the REST API, which run without a user.
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $all_links = $this->new_links($post->post_content, $previous_content);

        // Nothing added. Also stops the block editor's second save request
        // (for meta boxes) from replacing the first request's result in the popup.
        if (!$all_links) {
            return;
        }

        $links  = $this->filter_excluded_links($all_links);
        $synced = 0;
        $errors = [];

        if ($links) {
            $crms = GPC_Connector_CRM_Client::targets(self::MODULE);

            if (is_wp_error($crms)) {
                $errors[] = $crms->get_error_message();
            } else {
                $site_name   = GPC_Connector_Util::site_name();
                $unreachable = [];

                foreach ($links as $link) {
                    // Same record name the old tracker used, so existing CRM
                    // records are updated instead of duplicated.
                    $record_name = $site_name . '_' . $post_id . '_' . md5($link['url']);
                    $record      = $this->build_record($post, $link);
                    $link_synced = true;

                    foreach ($crms as $crm_name => $endpoint) {
                        // A CRM that timed out once is skipped for the other
                        // links rather than stalling the save for each one.
                        if (isset($unreachable[$crm_name])) {
                            $link_synced = false;
                            continue;
                        }

                        $result = GPC_Connector_CRM_Client::upsert($endpoint, self::MODULE, 'name', $record_name, $record);

                        if (is_wp_error($result)) {
                            $link_synced       = false;
                            $errors[$crm_name] = $crm_name . ': ' . $result->get_error_message();
                            GPC_Connector::log("Backlink sync to {$crm_name} failed for post {$post_id} ({$link['url']}): " . $result->get_error_message());

                            if ($result->get_error_code() === 'gpc_gateway_unreachable') {
                                $unreachable[$crm_name] = true;
                            }
                        }
                    }

                    if ($link_synced) {
                        $synced++;
                    }
                }
            }
        }

        GPC_Connector_Save_Notice::add('backlinks', [
            'post_title' => $post->post_title,
            'is_update'  => $update,
            'processed'  => count($links),
            'excluded'   => count($all_links) - count($links),
            'synced'     => $synced,
            'errors'     => array_values($errors),
        ]);
    }

    /**
     * CRM fields for one link. date_entered, date_modified and deleted are
     * left to the CRM so an update does not reset when the record was created.
     */
    private function build_record($post, $link)
    {
        $author_name = get_the_author_meta('display_name', $post->post_author);
        $permalink   = get_permalink($post->ID);

        return [
            'description'         => 'Backlink extracted from WordPress post: ' . $post->post_title,
            'backlink_url_c'      => $link['url'],
            'da_c'                => 0,
            'dr_c'                => 0,
            'status_c'            => 'Added',
            'target_url_c'        => $link['url'],
            'anchor_text_c'       => $link['text'] ?: 'No anchor text',
            'post_id_c'           => (string) $post->ID,
            'source_url_c'        => $permalink,
            'author_c'            => $author_name,
            'post_author_id_c'    => (string) $post->post_author,
            'post_author_name_c'  => $author_name,
            'post_author_email_c' => get_the_author_meta('email', $post->post_author),
            'gp_li_date_c'        => date('Y-m-d'),
            'type_c'              => $this->determine_link_type($link['url']),
            'gp_doc_url_c'        => $permalink,
            'expiry_date_c'       => date('Y-m-d', strtotime('+1 year')),
            'reseller_email_c'    => get_option('admin_email'),
            'link_amount_c'       => '0.00',
            'currency_id'         => 'USD',
            'spam_score_c'        => '0',
            'follow_status_c'     => $this->check_nofollow_status($link),

            // Extra WordPress context.
            'wp_post_id'          => $post->ID,
            'wp_post_title'       => $post->post_title,
            'wp_post_url'         => $permalink,
            'wp_site_url'         => get_site_url(),
            'link_target'         => $link['target'] ?: '',
            'link_rel'            => $link['rel'] ?: '',
            'link_title_attr'     => $link['title'] ?: '',
            'extraction_date'     => date('Y-m-d H:i:s'),
        ];
    }

    private function is_domain_excluded($url)
    {
        $domain = parse_url($url, PHP_URL_HOST);
        if (!$domain) {
            return false;
        }

        $domain = preg_replace('/^www\./', '', strtolower($domain));

        foreach ($this->excluded_domains as $excluded_domain) {
            if ($domain === preg_replace('/^www\./', '', strtolower($excluded_domain))) {
                return true;
            }
        }

        return false;
    }

    private function filter_excluded_links($links)
    {
        $filtered_links = [];

        foreach ($links as $link) {
            if (!$this->is_domain_excluded($link['url'])) {
                $filtered_links[] = $link;
            }
        }

        return $filtered_links;
    }

    /**
     * Links in $content whose URL was not in $previous_content. URLs are
     * compared normalized, so a changed trailing slash or anchor text does not
     * make an existing link count as new.
     */
    private function new_links($content, $previous_content)
    {
        $previous_urls = array_map(
            ['GPC_Connector_Util', 'normalize_link_url'],
            array_column($this->extract_links($previous_content), 'url')
        );

        return array_values(array_filter($this->extract_links($content), function ($link) use ($previous_urls) {
            return !in_array(GPC_Connector_Util::normalize_link_url($link['url']), $previous_urls, true);
        }));
    }

    private function extract_links($content)
    {
        $links = [];

        if (trim((string) $content) === '') {
            return $links;
        }

        // Use the rendered content so links added by blocks and shortcodes are included.
        $processed_content = apply_filters('the_content', $content);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $processed_content);
        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $href = $anchor->getAttribute('href');
            if (empty($href)) {
                continue;
            }

            $clean_url = esc_url($href);
            if (!empty($clean_url) && filter_var($clean_url, FILTER_VALIDATE_URL)) {
                $links[] = [
                    'url'    => $clean_url,
                    'text'   => trim($anchor->textContent),
                    'title'  => $anchor->getAttribute('title'),
                    'target' => $anchor->getAttribute('target'),
                    'rel'    => $anchor->getAttribute('rel'),
                ];
            }
        }

        // Also pick up bare URLs written as plain text.
        preg_match_all('/https?:\/\/[^\s<>"{}|\\^`\[\]]+/', strip_tags($content), $plain_urls);

        foreach ($plain_urls[0] as $url) {
            $clean_url = esc_url($url);
            if (empty($clean_url) || !filter_var($clean_url, FILTER_VALIDATE_URL)) {
                continue;
            }

            if (!in_array($clean_url, array_column($links, 'url'), true)) {
                $links[] = [
                    'url'    => $clean_url,
                    'text'   => '',
                    'title'  => '',
                    'target' => '',
                    'rel'    => '',
                    'type'   => 'plain_text',
                ];
            }
        }

        return array_values(array_unique($links, SORT_REGULAR));
    }

    private function determine_link_type($url)
    {
        $domain      = (string) parse_url($url, PHP_URL_HOST);
        $site_domain = parse_url(get_site_url(), PHP_URL_HOST);

        if ($domain === $site_domain) {
            return 'internal';
        }

        foreach (['facebook.com', 'twitter.com', 'linkedin.com', 'instagram.com'] as $social) {
            if (strpos($domain, $social) !== false) {
                return 'social';
            }
        }

        foreach (['.pdf', '.doc', '.zip'] as $extension) {
            if (strpos($url, $extension) !== false) {
                return 'document';
            }
        }

        return 'external';
    }

    private function check_nofollow_status($link)
    {
        $rel = strtolower($link['rel'] ?? '');

        if (strpos($rel, 'nofollow') !== false) {
            return 'nofollow';
        }

        if (strpos($rel, 'dofollow') !== false) {
            return 'dofollow';
        }

        return empty($rel) ? 'dofollow' : 'follow';
    }
}
