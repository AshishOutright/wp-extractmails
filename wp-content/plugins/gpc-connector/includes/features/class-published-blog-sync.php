<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sends a post's details to the CRM published blogs module when it is
 * published, and again on every update while it stays published. Replaces
 * the "Published Blog Notification" plugin.
 */
final class GPC_Connector_Published_Blog_Sync
{
    const MODULE = 'outr_published_blogs';

    public function __construct()
    {
        add_action('transition_post_status', [$this, 'handle_post_published'], 10, 3);
    }

    public function handle_post_published($new_status, $old_status, $post)
    {
        if ($new_status !== 'publish' || $post->post_type !== 'post') {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post->ID)) {
            return;
        }

        $is_new_publish = ($old_status !== 'publish');
        $blog_data      = $this->prepare_blog_data($post, $is_new_publish);
        $errors         = [];

        $crms = GPC_Connector_CRM_Client::targets(self::MODULE);

        if (is_wp_error($crms)) {
            $errors[] = $crms->get_error_message();
        } else {
            foreach ($crms as $crm_name => $endpoint) {
                // Matched on blog_url so republishing updates the existing record.
                $result = GPC_Connector_CRM_Client::upsert($endpoint, self::MODULE, 'blog_url', $blog_data['blog_url'], $blog_data);

                if (is_wp_error($result)) {
                    $errors[] = $crm_name . ': ' . $result->get_error_message();
                }
            }
        }

        foreach ($errors as $error) {
            GPC_Connector::log("Published blog sync failed for post {$post->ID}: {$error}");
        }

        GPC_Connector_Save_Notice::add('published', [
            'post_title'     => $post->post_title,
            'is_new_publish' => $is_new_publish,
            'errors'         => $errors,
        ]);
    }

    /**
     * CRM fields for the post. date_entered, date_modified and deleted are
     * left to the CRM so an update does not reset when the record was created.
     */
    private function prepare_blog_data($post, $is_new_publish)
    {
        $author_id = $post->post_author;
        $content   = strip_tags($post->post_content);
        $site_url  = get_site_url();

        $rankmath_score = '';
        if (defined('RANK_MATH_VERSION')) {
            $rankmath_data  = get_post_meta($post->ID, 'rank_math_score', true);
            $rankmath_score = is_numeric($rankmath_data) ? $rankmath_data : '';
        }

        return [
            'name'              => 'guestpostcrm.com',
            'description'       => !empty($post->post_excerpt) ? $post->post_excerpt : wp_trim_words($content, 30, '...'),
            'website'           => $site_url,
            'blog_url'          => get_permalink($post->ID),
            'author'            => get_the_author_meta('display_name', $author_id),
            'publisher_email'   => get_the_author_meta('email', $author_id),
            'indexing_c'        => '0',

            'post_word_count_c' => (string) str_word_count($content),
            'post_id_c'         => (string) $post->ID,
            'source_url_c'      => get_permalink($post->ID),
            'post_authore_id_c' => (string) $author_id,
            'total_links_c'     => (string) $this->count_links_in_post($post),
            'external_links_c'  => (string) $this->count_external_links_in_post($post),
            'rankmath_score_c'  => $rankmath_score,
            'websitename_c'     => get_bloginfo('name'),

            // Extra WordPress context.
            'post_title'        => $post->post_title,
            'post_content'      => $post->post_content,
            'post_status'       => 'Added',
            'post_type'         => $post->post_type,
            'post_date'         => $post->post_date,
            'post_modified'     => $post->post_modified,
            'post_slug'         => $post->post_name,
            'site_domain'       => parse_url($site_url, PHP_URL_HOST),
            'admin_email'       => get_option('admin_email'),
            'is_new_publish'    => $is_new_publish ? '1' : '0',
            'processing_date'   => date('Y-m-d H:i:s'),
        ];
    }

    private function count_links_in_post($post)
    {
        $content = $post->post_content;

        preg_match_all('/<a\s+[^>]*href=["\'][^"\']*["\'][^>]*>/i', $content, $html_links);
        preg_match_all('/https?:\/\/[^\s<>"{}|\\^`\[\]]+/', strip_tags($content), $plain_urls);

        // Plain-text URLs that are not already the href of a link.
        $unique_plain_count = 0;
        foreach ($plain_urls[0] as $url) {
            if (
                strpos($content, 'href="' . $url . '"') === false &&
                strpos($content, "href='" . $url . "'") === false
            ) {
                $unique_plain_count++;
            }
        }

        return count($html_links[0]) + $unique_plain_count;
    }

    private function count_external_links_in_post($post)
    {
        $site_domain    = parse_url(get_site_url(), PHP_URL_HOST);
        $external_count = 0;

        preg_match_all('/https?:\/\/[^\s<>"{}|\\^`\[\]]+/', $post->post_content, $all_urls);

        foreach ($all_urls[0] as $url) {
            $url_domain = parse_url($url, PHP_URL_HOST);
            if ($url_domain && $url_domain !== $site_domain) {
                $external_count++;
            }
        }

        return $external_count;
    }
}
