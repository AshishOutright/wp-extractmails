<?php
/**
 * Enhanced Auto-Scan WordPress Backlink Scanner
 * Place this file in your WordPress root directory and access it via browser
 * It will automatically scan and display all external backlinks with fresh data on each scan
 */

// Include WordPress configuration
require_once('wp-config.php');

// Initialize WordPress database connection
global $wpdb;
if (!isset($wpdb)) {
    $wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
}

// Set table prefix if not already set
if (!isset($wpdb->prefix)) {
    $wpdb->prefix = (isset($table_prefix)) ? $table_prefix : 'wp_';
}

// Check if JSON output is requested
$output_json = isset($_GET['format']) && $_GET['format'] === 'json';

// Get filter parameters
$filter_from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$filter_to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$filter_days = isset($_GET['days']) ? intval($_GET['days']) : 0;

// Set default date range if 'days' filter is used
if ($filter_days > 0) {
    $filter_from_date = date('Y-m-d', strtotime("-{$filter_days} days"));
    $filter_to_date = date('Y-m-d');
}

// Excluded domains (customize these)
$excluded_domains = [
    'outrightsystems.org',
    'outrightcrm.com', 
    'store.outrightcrm.com',
    'guestpostcrm.com'
];

// Get site URL
$site_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];

// Function to check if domain is excluded
function is_excluded_domain($url, $excluded_domains) {
    foreach ($excluded_domains as $domain) {
        if (strpos($url, $domain) !== false) {
            return true;
        }
    }
    return false;
}

// Function to check if link is external
function is_external_link($url, $site_url) {
    // If URL doesn't start with http, it's relative
    if (!preg_match('/^https?:\/\//', $url)) {
        return false;
    }
    
    $parsed_site = parse_url($site_url);
    $parsed_url = parse_url($url);
    
    return isset($parsed_url['host']) && $parsed_url['host'] !== $parsed_site['host'];
}

// Function to extract links from content
function extract_links_from_content($content, $source_url, $excluded_domains, $site_url) {
    $backlinks = [];
    
    // Simple regex to find links (more reliable than DOMDocument for fragments)
    preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $href = $match[1];
        $anchor_text = trim(strip_tags($match[2]));
        
        if (empty($href) || $href === '#') {
            continue;
        }
        
        // Convert relative URLs to absolute
        if (strpos($href, 'http') !== 0) {
            if (strpos($href, '/') === 0) {
                $href = $site_url . $href;
            } else {
                continue; // Skip other relative URLs
            }
        }
        
        // Skip if it's one of our excluded domains
        if (is_excluded_domain($href, $excluded_domains)) {
            continue;
        }
        
        // Check if it's an external link
        if (is_external_link($href, $site_url)) {
            $backlinks[] = [
                'source_url' => $source_url,
                'target_url' => $href,
                'anchor_text' => $anchor_text,
                'scan_timestamp' => date('Y-m-d H:i:s') // Current scan time instead of found_date
            ];
        }
    }
    
    return $backlinks;
}

// Enhanced auto-scan function with fresh data and date filtering
function auto_scan_backlinks($excluded_domains, $site_url, $filter_from_date = '', $filter_to_date = '') {
    global $wpdb;
    
    $all_backlinks = [];
    $current_timestamp = date('Y-m-d H:i:s');
    
    // Build date filter conditions for posts
    $date_where = "";
    if (!empty($filter_from_date) && !empty($filter_to_date)) {
        $date_where = $wpdb->prepare(" AND post_modified BETWEEN %s AND %s", 
            $filter_from_date . ' 00:00:00', 
            $filter_to_date . ' 23:59:59'
        );
    } elseif (!empty($filter_from_date)) {
        $date_where = $wpdb->prepare(" AND post_modified >= %s", $filter_from_date . ' 00:00:00');
    } elseif (!empty($filter_to_date)) {
        $date_where = $wpdb->prepare(" AND post_modified <= %s", $filter_to_date . ' 23:59:59');
    }
    
    // Get ALL published posts and pages with date filtering
    $posts_query = "
        SELECT ID, post_title, post_content, post_name, post_type, post_date, post_modified
        FROM {$wpdb->posts} 
        WHERE post_status = 'publish' 
        AND post_type IN ('post', 'page')
        AND post_content != ''
        AND post_content IS NOT NULL
        {$date_where}
        ORDER BY post_modified DESC
    ";
    
    $posts = $wpdb->get_results($posts_query, OBJECT);
    
    foreach ($posts as $post) {
        // Create source URL
        if ($post->post_type === 'page') {
            $source_url = $site_url . '/' . $post->post_name . '/';
        } else {
            $source_url = $site_url . '/' . $post->post_name . '/';
        }
        
        $links = extract_links_from_content($post->post_content, $source_url, $excluded_domains, $site_url);
        
        // Add post metadata to each link
        foreach ($links as &$link) {
            $link['post_date'] = $post->post_date;
            $link['post_modified'] = $post->post_modified;
            $link['post_title'] = $post->post_title;
            $link['post_type'] = $post->post_type;
            $link['post_id'] = $post->ID;
        }
        
        $all_backlinks = array_merge($all_backlinks, $links);
    }
    
    // Scan comments with date filtering (based on related post modification)
    $comments_query = "
        SELECT c.comment_ID, c.comment_post_ID, c.comment_content, c.comment_date, 
               c.comment_author, p.post_name, p.post_title, p.post_modified
        FROM {$wpdb->comments} c
        JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
        WHERE c.comment_approved = '1'
        AND c.comment_content != ''
        AND c.comment_content IS NOT NULL
        {$date_where}
        ORDER BY p.post_modified DESC
    ";
    
    $comments = $wpdb->get_results($comments_query, OBJECT);
    
    foreach ($comments as $comment) {
        $comment_url = $site_url . '/' . $comment->post_name . '/#comment-' . $comment->comment_ID;
        $links = extract_links_from_content($comment->comment_content, $comment_url, $excluded_domains, $site_url);
        
        // Add comment metadata to each link
        foreach ($links as &$link) {
            $link['comment_date'] = $comment->comment_date;
            $link['comment_id'] = $comment->comment_ID;
            $link['comment_author'] = $comment->comment_author;
            $link['post_title'] = $comment->post_title;
            $link['content_type'] = 'comment';
        }
        
        $all_backlinks = array_merge($all_backlinks, $links);
    }
    
    return $all_backlinks;
}

// Start scanning immediately with fresh data and filters
$start_time = microtime(true);
$backlinks = auto_scan_backlinks($excluded_domains, $site_url, $filter_from_date, $filter_to_date);
$end_time = microtime(true);
$scan_time = round(($end_time - $start_time), 2);

// Remove duplicates but keep the most recent scan of each link
$unique_backlinks = [];
$seen = [];
foreach ($backlinks as $link) {
    $key = $link['source_url'] . '|' . $link['target_url'] . '|' . $link['anchor_text'];
    if (!isset($seen[$key])) {
        $unique_backlinks[] = $link;
        $seen[$key] = true;
    }
}

// Sort by post modification date (newest first to show recently updated content)
usort($unique_backlinks, function($a, $b) {
    $date_a = isset($a['post_modified']) ? $a['post_modified'] : (isset($a['post_date']) ? $a['post_date'] : (isset($a['comment_date']) ? $a['comment_date'] : $a['scan_timestamp']));
    $date_b = isset($b['post_modified']) ? $b['post_modified'] : (isset($b['post_date']) ? $b['post_date'] : (isset($b['comment_date']) ? $b['comment_date'] : $b['scan_timestamp']));
    return strtotime($date_b) - strtotime($date_a); // Newest first
});

// Get statistics
$unique_domains = [];
foreach ($unique_backlinks as $link) {
    $domain = parse_url($link['target_url'], PHP_URL_HOST);
    if ($domain) $unique_domains[$domain] = true;
}

// Group backlinks by source URL
$grouped_backlinks = [];
foreach ($unique_backlinks as $link) {
    $grouped_backlinks[$link['source_url']][] = $link;
}

// Get date range of content (based on post modification dates)
$oldest_modified = null;
$newest_modified = null;
foreach ($unique_backlinks as $link) {
    $mod_date = isset($link['post_modified']) ? $link['post_modified'] : (isset($link['post_date']) ? $link['post_date'] : (isset($link['comment_date']) ? $link['comment_date'] : $link['scan_timestamp']));
    if (!$oldest_modified || strtotime($mod_date) < strtotime($oldest_modified)) {
        $oldest_modified = $mod_date;
    }
    if (!$newest_modified || strtotime($mod_date) > strtotime($newest_modified)) {
        $newest_modified = $mod_date;
    }
}

// Prepare JSON data
$json_data = [
    'scan_info' => [
        'scan_timestamp' => date('Y-m-d H:i:s'),
        'scan_time_seconds' => $scan_time,
        'total_backlinks' => count($unique_backlinks),
        'unique_domains' => count($unique_domains),
        'excluded_domains' => $excluded_domains,
        'filter_applied' => [
            'from_date' => $filter_from_date,
            'to_date' => $filter_to_date,
            'days_filter' => $filter_days
        ],
        'content_modification_range' => [
            'oldest' => $oldest_modified,
            'newest' => $newest_modified
        ],
        'scanned_posts' => count($wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ('post', 'page')" . str_replace('post_modified', 'post_modified', $date_where ?? ''))),
        'scanned_comments' => count($wpdb->get_results("SELECT c.comment_ID FROM {$wpdb->comments} c JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID WHERE c.comment_approved = '1'" . str_replace('post_modified', 'p.post_modified', $date_where ?? '')))
    ],
    'unique_domains' => array_keys($unique_domains),
    'backlinks' => $unique_backlinks,
    'grouped_by_source' => $grouped_backlinks
];

// If JSON output is requested, return JSON and exit
if ($output_json) {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    echo json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>WordPress Backlinks Report - <?php echo date('M j, Y H:i:s'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; margin-bottom: 20px; }
        .controls { margin-bottom: 20px; }
        .filter-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #dee2e6; }
        .filter-row { display: flex; gap: 15px; align-items: end; flex-wrap: wrap; margin-bottom: 15px; }
        .filter-group { display: flex; flex-direction: column; }
        .filter-group label { font-weight: bold; margin-bottom: 5px; color: #333; }
        .filter-group input, .filter-group select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .quick-filters { margin-top: 10px; }
        .quick-filter { display: inline-block; padding: 5px 10px; margin-right: 8px; margin-bottom: 5px; background: #e9ecef; border: 1px solid #ced4da; border-radius: 3px; text-decoration: none; color: #495057; font-size: 12px; }
        .quick-filter:hover, .quick-filter.active { background: #0073aa; color: white; }
        .btn { display: inline-block; padding: 8px 16px; margin-right: 10px; border-radius: 4px; text-decoration: none; font-weight: bold; }
        .btn-primary { background: #0073aa; color: white; }
        .btn-secondary { background: #666; color: white; }
        .btn:hover { opacity: 0.8; }
        .stats { background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 25px; }
        .stats strong { color: #0073aa; }
        .scan-time { background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .backlink-item { background: #f9f9f9; margin: 8px 0; padding: 15px; border-left: 4px solid #0073aa; border-radius: 3px; }
        .target-url { color: #666; margin-bottom: 5px; word-break: break-all; }
        .anchor-text { font-style: italic; color: #333; background: #e7f3ff; padding: 3px 8px; border-radius: 3px; display: inline-block; margin-top: 5px; }
        .no-backlinks { text-align: center; padding: 40px; color: #666; font-style: italic; font-size: 18px; }
        a { color: #0073aa; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .group-header { background: #0073aa; color: white; padding: 12px 15px; margin: 25px 0 10px 0; border-radius: 5px; font-weight: bold; }
        .excluded-domains { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .timestamp { font-size: 12px; color: #999; margin-top: 8px; }
        .json-preview { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 20px 0; max-height: 300px; overflow-y: auto; }
        .json-preview pre { margin: 0; font-size: 12px; }
        @media (max-width: 768px) {
            .container { margin: 10px; padding: 15px; }
            .btn { display: block; margin: 5px 0; text-align: center; }
            .filter-row { flex-direction: column; align-items: stretch; }
            .filter-group { margin-bottom: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 WordPress Backlinks Report</h1>
        
        <div class="filter-section">
            <h3>📅 Filter by Post Modification Date</h3>
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="from_date">From Date:</label>
                        <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($filter_from_date); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="to_date">To Date:</label>
                        <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($filter_to_date); ?>">
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">🔍 Apply Filter</button>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">🗑️ Clear Filter</a>
                    </div>
                </div>
                
                <div class="quick-filters">
                    <strong>Quick Filters:</strong>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?days=1" class="quick-filter <?php echo ($filter_days == 1) ? 'active' : ''; ?>">Last 24 Hours</a>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?days=7" class="quick-filter <?php echo ($filter_days == 7) ? 'active' : ''; ?>">Last 7 Days</a>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?days=30" class="quick-filter <?php echo ($filter_days == 30) ? 'active' : ''; ?>">Last 30 Days</a>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?days=90" class="quick-filter <?php echo ($filter_days == 90) ? 'active' : ''; ?>">Last 90 Days</a>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?from_date=<?php echo date('Y-m-d', strtotime('-1 year')); ?>&to_date=<?php echo date('Y-m-d'); ?>" class="quick-filter">Last Year</a>
                </div>
            </form>
        </div>

        <div class="controls">
            <a href="<?php echo $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']; ?>" class="btn btn-primary">🔄 Refresh Scan</a>
            <a href="<?php echo $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&format=json'; ?>" class="btn btn-secondary" target="_blank">📄 View JSON</a>
            <a href="javascript:void(0)" onclick="downloadJSON()" class="btn btn-secondary">💾 Download JSON</a>
        </div>

        <div class="scan-time">
            <strong>🕒 Fresh Scan Completed:</strong> <?php echo date('F j, Y - H:i:s'); ?> (Scan took <?php echo $scan_time; ?> seconds)
            <?php if (!empty($filter_from_date) || !empty($filter_to_date) || $filter_days > 0): ?>
                <br><strong>🔽 Filter Applied:</strong> 
                <?php if ($filter_days > 0): ?>
                    Last <?php echo $filter_days; ?> days
                <?php else: ?>
                    <?php echo !empty($filter_from_date) ? 'From: ' . date('M j, Y', strtotime($filter_from_date)) : ''; ?>
                    <?php echo !empty($filter_to_date) ? ' To: ' . date('M j, Y', strtotime($filter_to_date)) : ''; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="excluded-domains">
            <strong>🚫 Excluded Domains:</strong> <?php echo implode(', ', $excluded_domains); ?>
        </div>
        
        <div class="stats">
            <strong>📊 Real-Time Site Scan Results:</strong><br>
            ✅ Total External Backlinks Found: <strong><?php echo count($unique_backlinks); ?></strong><br>
            🌐 Unique External Domains: <strong><?php echo count($unique_domains); ?></strong><br>
            📅 Content Modification Range: <strong><?php echo $oldest_modified ? date('M j, Y', strtotime($oldest_modified)) : 'N/A'; ?></strong> to <strong><?php echo $newest_modified ? date('M j, Y', strtotime($newest_modified)) : 'N/A'; ?></strong><br>
            🔍 Scanned: <strong><?php echo count($wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ('post', 'page')" . (!empty($date_where) ? str_replace('post_modified', 'post_modified', $date_where) : ''))); ?> posts/pages</strong> + <strong><?php echo count($wpdb->get_results("SELECT c.comment_ID FROM {$wpdb->comments} c JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID WHERE c.comment_approved = '1'" . (!empty($date_where) ? str_replace('post_modified', 'p.post_modified', $date_where) : ''))); ?> comments</strong>
        </div>
        
        <?php if (empty($unique_backlinks)): ?>
            <div class="no-backlinks">
                ❌ No external backlinks found in your WordPress content.<br>
                <small>Either no external links exist, or all links point to excluded domains.</small>
            </div>
        <?php else: ?>
            
            <h2>📋 Found Backlinks (<?php echo count($unique_backlinks); ?> total)</h2>
            
            <?php foreach ($grouped_backlinks as $source_url => $links): ?>
                <div class="group-header">
                    📄 <?php echo htmlspecialchars($source_url); ?> 
                    <span style="font-weight: normal;">(<?php echo count($links); ?> outbound links)</span>
                </div>
                
                <?php foreach ($links as $link): ?>
                    <div class="backlink-item">
                        <div class="target-url">
                            🔗 <strong>Target:</strong> <a href="<?php echo htmlspecialchars($link['target_url']); ?>" target="_blank"><?php echo htmlspecialchars($link['target_url']); ?></a>
                        </div>
                        <?php if (!empty($link['anchor_text'])): ?>
                            <div class="anchor-text">
                                "<?php echo htmlspecialchars($link['anchor_text']); ?>"
                            </div>
                        <?php endif; ?>
                        <div class="timestamp">
                            <?php 
                            $content_date = isset($link['post_date']) ? $link['post_date'] : (isset($link['comment_date']) ? $link['comment_date'] : $link['scan_timestamp']);
                            $modified_date = isset($link['post_modified']) ? $link['post_modified'] : null;
                            $content_type = isset($link['post_date']) ? 'Post' : (isset($link['comment_date']) ? 'Comment' : 'Content');
                            ?>
                            📅 <?php echo $content_type; ?> created: <?php echo date('M j, Y H:i', strtotime($content_date)); ?>
                            <?php if (isset($link['post_title'])): ?>
                                | 📝 <?php echo htmlspecialchars($link['post_title']); ?>
                            <?php endif; ?>
                            <?php if ($modified_date && $modified_date !== $content_date): ?>
                                | ✏️ <strong>Modified: <?php echo date('M j, Y H:i', strtotime($modified_date)); ?></strong>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
            <?php endforeach; ?>
            
        <?php endif; ?>

        <div class="json-preview">
            <h3>📋 JSON Preview (first 10 backlinks)</h3>
            <pre><code><?php 
                $preview_data = $json_data;
                $preview_data['backlinks'] = array_slice($json_data['backlinks'], 0, 10);
                echo htmlspecialchars(json_encode($preview_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            ?></code></pre>
            <p><small>View complete JSON data using the "View JSON" button above.</small></p>
        </div>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
            <small>WordPress Enhanced Backlink Scanner | Last scan: <?php echo date('F j, Y - H:i:s'); ?></small>
        </div>
    </div>

    <script>
        function downloadJSON() {
            const jsonData = <?php echo json_encode($json_data, JSON_UNESCAPED_SLASHES); ?>;
            const blob = new Blob([JSON.stringify(jsonData, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'wordpress-backlinks-' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        // Auto-refresh every 5 minutes (optional)
        // setTimeout(() => window.location.reload(), 300000);
    </script>
</body>
</html>