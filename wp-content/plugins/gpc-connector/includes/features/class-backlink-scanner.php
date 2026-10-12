<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Finds outbound links in posts, pages and comments, lists them under
 * Tools → Backlink Scanner and at /backlinks/, and lets editors unlink them.
 * Replaces the "Backlink Scanner" plugin and keeps its wp_found_backlinks table.
 */
final class GPC_Connector_Backlink_Scanner
{
    const REWRITE_REGEX = '^backlinks/?$';
    const AJAX_ACTION   = 'gpc_connector_remove_backlink';
    const NONCE         = 'remove_backlink_nonce';

    private $excluded_domains = [
        'outrightsystems.org',
        'outrightcrm.com',
        'store.outrightcrm.com',
        'extractmails.com',
        'guestpostcrm.com',
        'wp-1click.com',
        'facebook.com',
        'twitter.com',
        'instagram.com',
        'youtube.com',
        'pintrest.com',
        'github.com',
        'whatsapp.com',
        'linkedin.com',
    ];

    private $notification_email = 'promotion@outrightcrm.com';

    public function __construct()
    {
        add_action('init', [__CLASS__, 'add_rewrite_rule']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('template_redirect', [$this, 'handle_backlinks_page']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_ajax_' . self::AJAX_ACTION, [$this, 'ajax_remove_backlink']);
    }

    public static function table_name()
    {
        global $wpdb;
        return $wpdb->prefix . 'found_backlinks';
    }

    public static function create_table()
    {
        global $wpdb;

        $table_name      = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            source_url varchar(500) NOT NULL,
            target_url varchar(500) NOT NULL,
            anchor_text text,
            post_id bigint(20),
            found_date datetime DEFAULT CURRENT_TIMESTAMP,
            is_external tinyint(1) DEFAULT 1,
            PRIMARY KEY  (id),
            KEY source_url (source_url),
            KEY target_url (target_url),
            KEY found_date (found_date)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function add_rewrite_rule()
    {
        add_rewrite_rule(self::REWRITE_REGEX, 'index.php?backlinks_page=1', 'top');
    }

    public function add_query_vars($vars)
    {
        $vars[] = 'backlinks_page';
        return $vars;
    }

    public function add_admin_menu()
    {
        add_management_page(
            'Backlink Scanner',
            'Backlink Scanner',
            'manage_options',
            'backlink-scanner',
            [$this, 'admin_page']
        );
    }

    public function admin_page()
    {
        if (isset($_POST['scan_backlinks'])) {
            check_admin_referer('gpc_connector_scanner');
            $this->scan_all_content();
            echo '<div class="notice notice-success"><p>Backlink scan completed!</p></div>';
        }

        if (isset($_POST['flush_rules'])) {
            check_admin_referer('gpc_connector_scanner');
            flush_rewrite_rules();
            echo '<div class="notice notice-success"><p>Rewrite rules flushed! Try the backlinks report link again.</p></div>';
        }
        ?>
<div class="wrap">
    <h1>Backlink Scanner</h1>

    <div class="card" style="margin-bottom: 20px;">
        <h2>Troubleshooting</h2>
        <p>If the "View Backlinks Report" link shows a 404 error, click the button below to flush rewrite rules:</p>
        <form method="post" style="display: inline;">
            <?php wp_nonce_field('gpc_connector_scanner'); ?>
            <?php submit_button('Flush Rewrite Rules', 'secondary', 'flush_rules', false); ?>
        </form>
    </div>

    <form method="post">
        <?php wp_nonce_field('gpc_connector_scanner'); ?>
        <p>Click the button below to scan all your content for backlinks:</p>
        <p><strong>Note:</strong> This will scan all posts, pages, and comments for external links. Existing
            backlinks will be preserved.</p>
        <?php submit_button('Scan for Backlinks', 'primary', 'scan_backlinks'); ?>
    </form>

    <h2>View Results</h2>
    <p><a href="<?php echo esc_url(home_url('/backlinks/')); ?>" target="_blank" class="button">View Backlinks Report</a></p>
    <p><em>Note: If the above link shows a 404 error, use the "Flush Rewrite Rules" button above.</em></p>

    <?php $this->display_recent_backlinks(); ?>
</div>
<?php
    }

    public function ajax_remove_backlink()
    {
        if (!check_ajax_referer(self::NONCE, 'nonce', false)) {
            wp_send_json_error('Security check failed');
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('You do not have permission to perform this action');
        }

        global $wpdb;
        $table_name  = self::table_name();
        $backlink_id = isset($_POST['backlink_id']) ? absint($_POST['backlink_id']) : 0;

        $backlink = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $backlink_id));

        if (!$backlink) {
            wp_send_json_error('Backlink not found');
        }

        if ($backlink->post_id && !current_user_can('edit_post', $backlink->post_id)) {
            wp_send_json_error('You do not have permission to edit this post');
        }

        $this->remove_link_from_content($backlink);

        if (!$wpdb->delete($table_name, ['id' => $backlink_id], ['%d'])) {
            wp_send_json_error('Failed to remove link from database');
        }

        $this->send_removal_notification($backlink);
        wp_send_json_success('Link removed successfully');
    }

    /** Replaces the link in the post with its anchor text. */
    private function remove_link_from_content($backlink)
    {
        if (!$backlink->post_id) {
            return false;
        }

        $post = get_post($backlink->post_id);
        if (!$post) {
            return false;
        }

        $target_url  = preg_quote($backlink->target_url, '/');
        $anchor_text = preg_quote($backlink->anchor_text, '/');
        $pattern     = '/<a[^>]*href=["\']' . $target_url . '["\'][^>]*>' . $anchor_text . '<\/a>/i';

        wp_update_post([
            'ID'           => $backlink->post_id,
            'post_content' => preg_replace($pattern, $backlink->anchor_text, $post->post_content),
        ]);

        return true;
    }

    private function send_removal_notification($backlink)
    {
        $current_user = wp_get_current_user();
        $user_name    = $current_user->display_name ? $current_user->display_name : $current_user->user_login;

        $message  = "Hey,\n\n";
        $message .= 'Link "' . $backlink->target_url . '" has been removed by ' . $user_name . "\n";
        $message .= 'at ' . current_time('F j, Y g:i A') . "\n";
        $message .= 'anchor text: ' . $backlink->anchor_text . "\n\n";
        $message .= 'Source page: ' . $backlink->source_url . "\n\n";
        $message .= "Best regards,\n";
        $message .= get_bloginfo('name');

        wp_mail($this->notification_email, 'Backlink Removed - ' . get_bloginfo('name'), $message);
    }

    public function scan_all_content()
    {
        $posts = get_posts([
            'post_type'   => ['post', 'page'],
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);

        foreach ($posts as $post) {
            $this->extract_links_from_content($post->post_content, get_permalink($post->ID), $post->ID);
        }

        foreach (get_comments(['status' => 'approve']) as $comment) {
            $source_url = get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID;
            $this->extract_links_from_content($comment->comment_content, $source_url, $comment->comment_post_ID);
        }
    }

    /** Stores each new external link. Links found by earlier scans are kept. */
    private function extract_links_from_content($content, $source_url, $post_id)
    {
        global $wpdb;

        if (trim((string) $content) === '') {
            return;
        }

        $table_name = self::table_name();

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        foreach ($dom->getElementsByTagName('a') as $link) {
            $href        = $link->getAttribute('href');
            $anchor_text = trim($link->textContent);

            if (empty($href) || $href === '#' || $this->is_excluded_domain($href) || !$this->is_external_link($href)) {
                continue;
            }

            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE source_url = %s AND target_url = %s AND anchor_text = %s",
                $source_url,
                $href,
                $anchor_text
            ));

            if (!$existing) {
                $wpdb->insert(
                    $table_name,
                    [
                        'source_url'  => $source_url,
                        'target_url'  => $href,
                        'anchor_text' => $anchor_text,
                        'post_id'     => $post_id,
                        'is_external' => 1,
                        'found_date'  => current_time('mysql'),
                    ],
                    ['%s', '%s', '%s', '%d', '%d', '%s']
                );
            }
        }
    }

    private function is_excluded_domain($url)
    {
        foreach ($this->excluded_domains as $domain) {
            if (strpos($url, $domain) !== false) {
                return true;
            }
        }

        return false;
    }

    private function is_external_link($url)
    {
        // Relative URLs point at this site.
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }

        $parsed_url = parse_url($url);
        return isset($parsed_url['host']) && $parsed_url['host'] !== parse_url(home_url(), PHP_URL_HOST);
    }

    public function handle_backlinks_page()
    {
        if (!get_query_var('backlinks_page')) {
            return;
        }

        // The report lists every outbound link and offers removal, so only
        // people who can edit posts may see it.
        if (!is_user_logged_in()) {
            auth_redirect();
        }

        if (!current_user_can('edit_posts')) {
            wp_die('You do not have permission to view the backlinks report.', 403);
        }

        $this->display_backlinks_page();
        exit;
    }

    public function display_backlinks_page()
    {
        global $wpdb;

        $table_name = self::table_name();
        $backlinks  = $wpdb->get_results("
            SELECT id, source_url, target_url, anchor_text, found_date, post_id
            FROM {$table_name}
            WHERE is_external = 1
            ORDER BY source_url ASC, target_url ASC
        ");

        $unique_domains = [];
        foreach ($backlinks as $link) {
            $domain = parse_url($link->target_url, PHP_URL_HOST);
            if ($domain) {
                $unique_domains[$domain] = true;
            }
        }
        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Backlinks Report - <?php bloginfo('name'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        .date-group { margin-bottom: 30px; }
        .date-header { background: #0073aa; color: white; padding: 10px 15px; margin: 20px 0 10px 0; border-radius: 5px; font-weight: bold; }
        .backlink-item { background: #f9f9f9; margin: 5px 0; padding: 15px; border-left: 4px solid #0073aa; border-radius: 3px; position: relative; }
        .target-url { color: #666; margin-bottom: 5px; }
        .anchor-text { font-style: italic; color: #333; background: #e7f3ff; padding: 3px 6px; border-radius: 3px; margin-bottom: 10px; }
        .stats { background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .no-backlinks { text-align: center; padding: 40px; color: #666; font-style: italic; }
        a { color: #0073aa; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .back-link { display: inline-block; margin-bottom: 20px; padding: 8px 15px; background: #0073aa; color: white; border-radius: 3px; }
        .action-buttons { margin-top: 10px; }
        .btn { padding: 5px 10px; margin-right: 5px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-remove { background: #dc3545; color: white; }
        .btn-remove:hover { background: #c82333; }
        .btn-edit { background: #28a745; color: white; }
        .btn-edit:hover { background: #218838; }
        .loading { display: none; color: #666; }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔗 Backlinks Report</h1>
        <a href="<?php echo esc_url(admin_url('tools.php?page=backlink-scanner')); ?>" class="back-link">← Back to Admin</a>

        <div class="stats">
            <strong>📊 Statistics:</strong><br>
            Total External Backlinks Found: <?php echo count($backlinks); ?><br>
            Unique External Domains: <?php echo count($unique_domains); ?><br>
            Last Scan: <?php echo esc_html(current_time('F j, Y g:i A')); ?>
        </div>

        <?php if (empty($backlinks)) : ?>
        <div class="no-backlinks">No external backlinks found. Try running a scan from the admin panel.</div>
        <?php else : ?>
        <?php
        $grouped_backlinks = [];
        foreach ($backlinks as $link) {
            $grouped_backlinks[$link->source_url][] = $link;
        }
        ?>
        <?php foreach ($grouped_backlinks as $source_url => $links) : ?>
        <div class="date-group">
            <div class="date-header">📄 <?php echo esc_html($source_url); ?> (<?php echo count($links); ?> outbound links)</div>

            <?php foreach ($links as $link) : ?>
            <div class="backlink-item" id="backlink-<?php echo (int) $link->id; ?>">
                <div class="target-url">🔗 Links to: <a href="<?php echo esc_url($link->target_url); ?>" target="_blank"><?php echo esc_html($link->target_url); ?></a></div>
                <?php if (!empty($link->anchor_text)) : ?>
                <div class="anchor-text">Anchor Text: "<?php echo esc_html($link->anchor_text); ?>"</div>
                <?php endif; ?>
                <div style="font-size: 12px; color: #999; margin-bottom: 10px;">Found: <?php echo esc_html($link->found_date); ?></div>

                <div class="action-buttons">
                    <?php if ($link->post_id) : ?>
                    <a href="<?php echo esc_url(admin_url('post.php?post=' . (int) $link->post_id . '&action=edit')); ?>" class="btn btn-edit" target="_blank">✏️ Edit Post</a>
                    <?php endif; ?>
                    <button class="btn btn-remove gpc-remove-backlink" onclick="gpcRemoveBacklink(<?php echo (int) $link->id; ?>)">🗑️ Remove Link</button>
                    <span class="loading" id="loading-<?php echo (int) $link->id; ?>">Processing...</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
            <small>Generated by GPC Connector | <?php echo esc_html(get_bloginfo('name')); ?></small>
        </div>
    </div>

    <?php
    $this->print_remove_script(
        'backlink-',
        'Are you sure you want to remove this link? This will also remove it from the post content.',
        'Link removed successfully and email notification sent!'
    );
    ?>
</body>

</html>
<?php
    }

    private function display_recent_backlinks()
    {
        global $wpdb;

        $table_name       = self::table_name();
        $recent_backlinks = $wpdb->get_results("
            SELECT id, source_url, target_url, anchor_text, found_date, post_id
            FROM {$table_name}
            WHERE is_external = 1
            ORDER BY found_date DESC
            LIMIT 10
        ");

        if (empty($recent_backlinks)) {
            return;
        }
        ?>
<h3>Recent Backlinks Found</h3>
<table class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th>Source</th>
            <th>Target</th>
            <th>Anchor Text</th>
            <th>Found Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recent_backlinks as $link) : ?>
        <tr id="admin-backlink-<?php echo (int) $link->id; ?>">
            <td><a href="<?php echo esc_url($link->source_url); ?>" target="_blank"><?php echo esc_html(wp_trim_words($link->source_url, 8)); ?></a></td>
            <td><a href="<?php echo esc_url($link->target_url); ?>" target="_blank"><?php echo esc_html(wp_trim_words($link->target_url, 8)); ?></a></td>
            <td><?php echo esc_html($link->anchor_text); ?></td>
            <td><?php echo esc_html($link->found_date); ?></td>
            <td>
                <?php if ($link->post_id) : ?>
                <a href="<?php echo esc_url(admin_url('post.php?post=' . (int) $link->post_id . '&action=edit')); ?>" class="button button-small" target="_blank">Edit Post</a>
                <?php endif; ?>
                <button type="button" class="button button-small button-link-delete gpc-remove-backlink" onclick="gpcRemoveBacklink(<?php echo (int) $link->id; ?>)">Remove</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
        $this->print_remove_script('admin-backlink-', 'Are you sure you want to remove this link?', 'Link removed successfully!');
    }

    /**
     * Prints gpcRemoveBacklink(id), which removes the link through admin-ajax
     * and then drops the element with id $row_prefix + id.
     */
    private function print_remove_script($row_prefix, $confirm_text, $success_text)
    {
        ?>
<script>
    function gpcRemoveBacklink(backlinkId) {
        if (!confirm(<?php echo wp_json_encode($confirm_text); ?>)) {
            return;
        }

        var loading = document.getElementById('loading-' + backlinkId);
        var buttons = document.querySelectorAll('.gpc-remove-backlink');
        var data = new FormData();

        data.append('action', <?php echo wp_json_encode(self::AJAX_ACTION); ?>);
        data.append('backlink_id', backlinkId);
        data.append('nonce', <?php echo wp_json_encode(wp_create_nonce(self::NONCE)); ?>);

        if (loading) {
            loading.style.display = 'inline';
        }
        buttons.forEach(function(button) {
            button.disabled = true;
        });

        fetch(<?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>, {
                method: 'POST',
                body: data,
                credentials: 'same-origin'
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(response) {
                if (response.success) {
                    var row = document.getElementById(<?php echo wp_json_encode($row_prefix); ?> + backlinkId);
                    if (row) {
                        row.remove();
                    }
                    alert(<?php echo wp_json_encode($success_text); ?>);
                } else {
                    alert('Error: ' + response.data);
                }
            })
            .catch(function() {
                alert('An error occurred while removing the link.');
            })
            .then(function() {
                if (loading) {
                    loading.style.display = 'none';
                }
                buttons.forEach(function(button) {
                    button.disabled = false;
                });
            });
    }
</script>
<?php
    }
}
