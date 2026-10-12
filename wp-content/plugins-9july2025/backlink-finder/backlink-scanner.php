<?php
/**
 * Plugin Name: Backlink Scanner
 * Description: Scans and displays backlinks from your WordPress site excluding specified domains with removal and edit features
 * Version: 1.2
 * Author: Custom Development
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class BacklinkScanner {
    
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
        'linkedin.com'
    ];
    
    private $notification_email = 'promotion@outrightcrm.com';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_remove_backlink', array($this, 'ajax_remove_backlink'));
        add_action('wp_ajax_nopriv_remove_backlink', array($this, 'ajax_remove_backlink'));
        register_activation_hook(__FILE__, array($this, 'activate_plugin'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate_plugin'));
    }
    
    public function init() {
        // Add custom endpoint for viewing backlinks
        add_rewrite_rule('^backlinks/?$', 'index.php?backlinks_page=1', 'top');
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'handle_backlinks_page'));
    }
    
    public function add_query_vars($vars) {
        $vars[] = 'backlinks_page';
        return $vars;
    }
    
    // Combined activation function
    public function activate_plugin() {
        $this->create_backlinks_table();
        $this->init(); // Make sure rewrite rules are added
        flush_rewrite_rules(); // Flush rewrite rules on activation
    }
    
    // Deactivation function
    public function deactivate_plugin() {
        flush_rewrite_rules(); // Clean up rewrite rules on deactivation
    }
    
    public function create_backlinks_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'found_backlinks';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    source_url varchar(500) NOT NULL,
    target_url varchar(500) NOT NULL,
    anchor_text text,
    post_id bigint(20),
    post_published_date datetime,
    found_date datetime DEFAULT CURRENT_TIMESTAMP,
    is_external tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    KEY source_url (source_url),
    KEY target_url (target_url),
    KEY found_date (found_date),
    KEY post_published_date (post_published_date)
) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function add_admin_menu() {
        add_management_page(
            'Backlink Scanner',
            'Backlink Scanner', 
            'manage_options',
            'backlink-scanner',
            array($this, 'admin_page')
        );
    }
    
    public function admin_page() {
        if (isset($_POST['scan_backlinks'])) {
            $this->scan_all_content();
            echo '<div class="notice notice-success"><p>Backlink scan completed!</p></div>';
        }
        
        if (isset($_POST['flush_rules'])) {
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
                    <?php submit_button('Flush Rewrite Rules', 'secondary', 'flush_rules', false); ?>
                </form>
            </div>
            
            <form method="post">
                <p>Click the button below to scan all your content for backlinks:</p>
                <p><strong>Note:</strong> This will scan all posts, pages, and comments for external links. Existing backlinks will be preserved.</p>
                <?php submit_button('Scan for Backlinks', 'primary', 'scan_backlinks'); ?>
            </form>
            
            <h2>View Results</h2>
            <p><a href="<?php echo home_url('/backlinks/'); ?>" target="_blank" class="button">View Backlinks Report</a></p>
            <p><em>Note: If the above link shows a 404 error, use the "Flush Rewrite Rules" button above.</em></p>
            
            <?php $this->display_recent_backlinks(); ?>
        </div>
        <?php
    }
    
    public function ajax_remove_backlink() {
        // Check nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'remove_backlink_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check if user has permission
        if (!current_user_can('edit_posts')) {
            wp_die('You do not have permission to perform this action');
        }
        
        global $wpdb;
        $backlink_id = intval($_POST['backlink_id']);
        $table_name = $wpdb->prefix . 'found_backlinks';
        
        // Get backlink details before removal for email
        $backlink = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d", $backlink_id
        ));
        
        if (!$backlink) {
            wp_die('Backlink not found');
        }
        
        // Remove the link from the actual post content
        $this->remove_link_from_content($backlink);
        
        // Remove from database
        $deleted = $wpdb->delete($table_name, array('id' => $backlink_id), array('%d'));
        
        if ($deleted) {
            // Send email notification
            $this->send_removal_notification($backlink);
            
            wp_send_json_success('Link removed successfully');
        } else {
            wp_send_json_error('Failed to remove link from database');
        }
    }
    
    private function remove_link_from_content($backlink) {
        if (!$backlink->post_id) {
            return false;
        }
        
        $post = get_post($backlink->post_id);
        if (!$post) {
            return false;
        }
        
        $content = $post->post_content;
        
        // Create a pattern to match the specific link
        $target_url = preg_quote($backlink->target_url, '/');
        $anchor_text = preg_quote($backlink->anchor_text, '/');
        
        // Pattern to match the link with the specific URL and anchor text
        $pattern = '/<a[^>]*href=["\']' . $target_url . '["\'][^>]*>' . $anchor_text . '<\/a>/i';
        
        // Replace the link with just the anchor text
        $new_content = preg_replace($pattern, $backlink->anchor_text, $content);
        
        // Update the post
        wp_update_post(array(
            'ID' => $backlink->post_id,
            'post_content' => $new_content
        ));
        
        return true;
    }
    
    private function send_removal_notification($backlink) {
        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name ? $current_user->display_name : $current_user->user_login;
        
        $subject = 'Backlink Removed - ' . get_bloginfo('name');
        
        $message = "Hey,\n\n";
        $message .= "Link \"" . $backlink->target_url . "\" has been removed by " . $user_name . "\n";
        $message .= "at " . current_time('F j, Y g:i A') . "\n";
        $message .= "anchor text: " . $backlink->anchor_text . "\n\n";
        $message .= "Source page: " . $backlink->source_url . "\n\n";
        $message .= "Best regards,\n";
        $message .= get_bloginfo('name');
        
        wp_mail($this->notification_email, $subject, $message);
    }
    
    public function scan_all_content() {
        global $wpdb;
        
        // Don't truncate - instead update existing or insert new
        $table_name = $wpdb->prefix . 'found_backlinks';
        
        // Get all published posts and pages
        $posts = get_posts(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => array()
        ));
        
        foreach ($posts as $post) {
            $this->scan_post_content($post);
        }
        
        // Also scan comments
        $comments = get_comments(array('status' => 'approve'));
        foreach ($comments as $comment) {
            $this->scan_comment_content($comment);
        }
    }
    
    private function scan_post_content($post) {
        $content = $post->post_content;
        $source_url = get_permalink($post->ID);
        
        $this->extract_links_from_content($content, $source_url, $post->ID);
    }
    
    private function scan_comment_content($comment) {
        $content = $comment->comment_content;
        $source_url = get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID;
        
        $this->extract_links_from_content($content, $source_url, $comment->comment_post_ID);
    }
    
    private function extract_links_from_content($content, $source_url, $post_id) {
    global $wpdb;
    
    // Use DOMDocument to parse HTML and extract links
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
    $links = $dom->getElementsByTagName('a');
    
    // Get post published date
    $post_published_date = null;
    if ($post_id) {
        $post = get_post($post_id);
        if ($post) {
            $post_published_date = $post->post_date;
        }
    }
    
    foreach ($links as $link) {
        $href = $link->getAttribute('href');
        $anchor_text = trim($link->textContent);
        
        if (empty($href) || $href === '#') {
            continue;
        }
        
        // Skip if it's one of our excluded domains
        if ($this->is_excluded_domain($href)) {
            continue;
        }
        
        // Check if it's an external link
        $is_external = $this->is_external_link($href);
        
        if ($is_external) {
            $table_name = $wpdb->prefix . 'found_backlinks';
            
            // Check if this exact backlink already exists
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE source_url = %s AND target_url = %s AND anchor_text = %s",
                $source_url, $href, $anchor_text
            ));
            
            // Only insert if it doesn't already exist
            if (!$existing) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'source_url' => $source_url,
                        'target_url' => $href,
                        'anchor_text' => $anchor_text,
                        'post_id' => $post_id,
                        'post_published_date' => $post_published_date,
                        'is_external' => 1,
                        'found_date' => current_time('mysql')
                    ),
                    array('%s', '%s', '%s', '%d', '%s', '%d', '%s')
                );
            }
        }
    }
}
    
    private function is_excluded_domain($url) {
        foreach ($this->excluded_domains as $domain) {
            if (strpos($url, $domain) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function is_external_link($url) {
        $home_url = home_url();
        $parsed_home = parse_url($home_url);
        $home_domain = $parsed_home['host'];
        
        // If URL doesn't start with http, it's relative
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }
        
        $parsed_url = parse_url($url);
        return isset($parsed_url['host']) && $parsed_url['host'] !== $home_domain;
    }
    
    public function handle_backlinks_page() {
        if (get_query_var('backlinks_page')) {
            $this->display_backlinks_page();
            exit;
        }
    }
    
   public function display_backlinks_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'found_backlinks';
    
    // Get filter parameters
    $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
    $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
    
    // Build query with date filtering - only filter blog posts (posts with post_id and published date)
    $where_clause = "WHERE is_external = 1";
    $query_params = array();
    
    if (!empty($date_from)) {
        $where_clause .= " AND (post_published_date >= %s OR post_published_date IS NULL)";
        $query_params[] = $date_from . ' 00:00:00';
    }
    
    if (!empty($date_to)) {
        $where_clause .= " AND (post_published_date <= %s OR post_published_date IS NULL)";
        $query_params[] = $date_to . ' 23:59:59';
    }
    
    // When date filter is applied, prioritize blog posts, but still show non-blog URLs
    if (!empty($date_from) || !empty($date_to)) {
        $query = "SELECT id, source_url, target_url, anchor_text, found_date, post_id, post_published_date
                  FROM $table_name 
                  $where_clause 
                  ORDER BY 
                    CASE WHEN post_published_date IS NOT NULL THEN 0 ELSE 1 END,
                    post_published_date DESC, 
                    source_url ASC, 
                    target_url ASC";
    } else {
        $query = "SELECT id, source_url, target_url, anchor_text, found_date, post_id, post_published_date
                  FROM $table_name 
                  $where_clause 
                  ORDER BY 
                    CASE WHEN post_published_date IS NOT NULL THEN 0 ELSE 1 END,
                    post_published_date DESC, 
                    source_url ASC, 
                    target_url ASC";
    }
    
    if (!empty($query_params)) {
        $backlinks = $wpdb->get_results($wpdb->prepare($query, $query_params));
    } else {
        $backlinks = $wpdb->get_results($query);
    }
    
    // Filter results to show only matching blog posts when date filter is applied
    if (!empty($date_from) || !empty($date_to)) {
        $filtered_backlinks = array();
        foreach ($backlinks as $link) {
            // Always include non-blog URLs (pages, comments, etc.)
            if (!$link->post_published_date) {
                $filtered_backlinks[] = $link;
                continue;
            }
            
            // For blog posts, check date range
            $published_timestamp = strtotime($link->post_published_date);
            $include_link = true;
            
            if (!empty($date_from)) {
                $from_timestamp = strtotime($date_from . ' 00:00:00');
                if ($published_timestamp < $from_timestamp) {
                    $include_link = false;
                }
            }
            
            if (!empty($date_to) && $include_link) {
                $to_timestamp = strtotime($date_to . ' 23:59:59');
                if ($published_timestamp > $to_timestamp) {
                    $include_link = false;
                }
            }
            
            if ($include_link) {
                $filtered_backlinks[] = $link;
            }
        }
        $backlinks = $filtered_backlinks;
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
            .source-url { font-weight: bold; color: #0073aa; margin-bottom: 5px; }
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
            .filter-form { background: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #0073aa; }
            .filter-form input[type="date"] { padding: 5px; margin: 0 5px; border: 1px solid #ccc; border-radius: 3px; }
            .filter-form button { padding: 5px 15px; background: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer; margin: 0 5px; }
            .filter-form button:hover { background: #005a87; }
            .published-date { font-size: 12px; color: #666; margin-bottom: 5px; }
        </style>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <div class="container">
            <h1>🔗 Backlinks Report</h1>
            <a href="<?php echo admin_url('tools.php?page=backlink-scanner'); ?>" class="back-link">← Back to Admin</a>
            
            <!-- Date Filter Form -->
            <div class="filter-form">
                <form method="GET" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <label><strong>📅 Filter Blog Posts by Published Date:</strong></label>
                    <label>From: <input type="date" name="date_from" value="<?php echo esc_attr($date_from); ?>"></label>
                    <label>To: <input type="date" name="date_to" value="<?php echo esc_attr($date_to); ?>"></label>
                    <button type="submit">Apply Filter</button>
                    <a href="<?php echo remove_query_arg(array('date_from', 'date_to')); ?>" style="text-decoration: none;">
                        <button type="button">Clear Filter</button>
                    </a>
                </form>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #666; font-style: italic;">
                    * Filter applies only to blog post URLs. Other URLs (pages, comments) will always be shown.
                </p>
            </div>
            
            <?php
            $total_backlinks = count($backlinks);
            $blog_posts_count = 0;
            $other_urls_count = 0;
            $unique_domains = array();
            
            foreach ($backlinks as $link) {
                $domain = parse_url($link->target_url, PHP_URL_HOST);
                if ($domain) $unique_domains[$domain] = true;
                
                if ($link->post_published_date) {
                    $blog_posts_count++;
                } else {
                    $other_urls_count++;
                }
            }
            
            $filter_info = '';
            if (!empty($date_from) || !empty($date_to)) {
                $filter_info = ' (Blog Posts Filtered';
                if (!empty($date_from)) $filter_info .= ' from ' . date('M j, Y', strtotime($date_from));
                if (!empty($date_to)) $filter_info .= ' to ' . date('M j, Y', strtotime($date_to));
                $filter_info .= ')';
            }
            ?>
            
            <div class="stats">
                <strong>📊 Statistics<?php echo $filter_info; ?>:</strong><br>
                Total External Backlinks Found: <?php echo $total_backlinks; ?><br>
                └─ Blog Posts: <?php echo $blog_posts_count; ?> | Other URLs: <?php echo $other_urls_count; ?><br>
                Unique External Domains: <?php echo count($unique_domains); ?><br>
                Last Scan: <?php echo current_time('F j, Y g:i A'); ?>
            </div>
            
            <?php
            if (empty($backlinks)) {
                echo '<div class="no-backlinks">No external backlinks found for the selected criteria.</div>';
            } else {
                // Group backlinks by source URL for better organization
                $grouped_backlinks = array();
                foreach ($backlinks as $link) {
                    $grouped_backlinks[$link->source_url][] = $link;
                }
                
                foreach ($grouped_backlinks as $source_url => $links) {
                    echo '<div class="date-group">';
                    echo '<div class="date-header">📄 ' . esc_html($source_url) . ' (' . count($links) . ' outbound links)</div>';
                    
                    foreach ($links as $link) {
                        echo '<div class="backlink-item" id="backlink-' . $link->id . '">';
                        
                        // Show published date ONLY for blog posts (posts with post_published_date)
                        if ($link->post_published_date) {
                            echo '<div class="published-date">📅 Blog Published: ' . esc_html(date('F j, Y', strtotime($link->post_published_date))) . '</div>';
                        }
                        
                        echo '<div class="target-url">🔗 Links to: <a href="' . esc_url($link->target_url) . '" target="_blank">' . esc_html($link->target_url) . '</a></div>';
                        if (!empty($link->anchor_text)) {
                            echo '<div class="anchor-text">Anchor Text: "' . esc_html($link->anchor_text) . '"</div>';
                        }
                        echo '<div style="font-size: 12px; color: #999; margin-bottom: 10px;">Found: ' . esc_html($link->found_date) . '</div>';
                        
                        // Action buttons
                        echo '<div class="action-buttons">';
                        if ($link->post_id) {
                            echo '<a href="' . admin_url('post.php?post=' . $link->post_id . '&action=edit') . '" class="btn btn-edit" target="_blank">✏️ Edit Post</a>';
                        }
                        echo '<button class="btn btn-remove" onclick="removeBacklink(' . $link->id . ')">🗑️ Remove Link</button>';
                        echo '<span class="loading" id="loading-' . $link->id . '">Processing...</span>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            ?>
            
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
                <small>Generated by Backlink Scanner Plugin | <?php echo get_bloginfo('name'); ?></small>
            </div>
        </div>
        
        <script>
        function removeBacklink(backlinkId) {
            if (!confirm('Are you sure you want to remove this link? This will also remove it from the post content.')) {
                return;
            }
            
            $('#loading-' + backlinkId).show();
            $('.btn-remove').prop('disabled', true);
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'remove_backlink',
                    backlink_id: backlinkId,
                    nonce: '<?php echo wp_create_nonce('remove_backlink_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#backlink-' + backlinkId).fadeOut();
                        alert('Link removed successfully and email notification sent!');
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred while removing the link.');
                },
                complete: function() {
                    $('#loading-' + backlinkId).hide();
                    $('.btn-remove').prop('disabled', false);
                }
            });
        }
        </script>
    </body>
    </html>
    <?php
}
    
    private function display_recent_backlinks() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'found_backlinks';
       $recent_backlinks = $wpdb->get_results("
    SELECT id, source_url, target_url, anchor_text, found_date, post_id, post_published_date
    FROM $table_name 
    WHERE is_external = 1 
    ORDER BY found_date DESC 
    LIMIT 10
");
        
        if (!empty($recent_backlinks)) {
            echo '<h3>Recent Backlinks Found</h3>';
            echo '<table class="wp-list-table widefat fixed striped">';
            // And update the table display part:
echo '<thead><tr><th>Source</th><th>Target</th><th>Anchor Text</th><th>Published Date</th><th>Found Date</th><th>Actions</th></tr></thead>';
echo '<tbody>';
            
           foreach ($recent_backlinks as $link) {
    echo '<tr id="admin-backlink-' . $link->id . '">';
    echo '<td><a href="' . esc_url($link->source_url) . '" target="_blank">' . esc_html(wp_trim_words($link->source_url, 8)) . '</a></td>';
    echo '<td><a href="' . esc_url($link->target_url) . '" target="_blank">' . esc_html(wp_trim_words($link->target_url, 8)) . '</a></td>';
    echo '<td>' . esc_html($link->anchor_text) . '</td>';
    echo '<td>' . ($link->post_published_date ? esc_html(date('M j, Y', strtotime($link->post_published_date))) : 'N/A') . '</td>';
    echo '<td>' . esc_html($link->found_date) . '</td>';
    echo '<td>';
    if ($link->post_id) {
        echo '<a href="' . admin_url('post.php?post=' . $link->post_id . '&action=edit') . '" class="button button-small" target="_blank">Edit Post</a> ';
    }
    echo '<button class="button button-small button-link-delete" onclick="removeBacklinkAdmin(' . $link->id . ')">Remove</button>';
    echo '</td>';
    echo '</tr>';
}
?>
            <script>
            function removeBacklinkAdmin(backlinkId) {
                if (!confirm('Are you sure you want to remove this link?')) {
                    return;
                }
                
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'remove_backlink',
                        backlink_id: backlinkId,
                        nonce: '<?php echo wp_create_nonce('remove_backlink_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            jQuery('#admin-backlink-' + backlinkId).fadeOut();
                            alert('Link removed successfully!');
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('An error occurred while removing the link.');
                    }
                });
            }
            </script>
            <?php
        }
    }
}

// Initialize the plugin
new BacklinkScanner();
?>