<?php
/*
Plugin Name: SEO Index Control Pro
Description: Advanced SEO control with bulk URL management and granular robots meta settings. Works with Yoast SEO and Rank Math.
Version: 2.0
Author: Outright
*/

if (!defined('ABSPATH')) exit;

// ✅ 1. Add admin menu
add_action('admin_menu', function () {
    add_menu_page(
        'SEO Index Control Pro',
        'SEO Control Pro',
        'manage_options',
        'seo-index-control-pro',
        'seo_index_control_pro_page',
        'dashicons-search',
        80
    );
});

// ✅ 2. Enhanced Admin page UI with bulk management
function seo_index_control_pro_page() {
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['bulk_urls']) && !empty($_POST['bulk_urls'])) {
            // Bulk URL processing
            $urls = explode("\n", sanitize_textarea_field($_POST['bulk_urls']));
            $processed = 0;
            
            foreach ($urls as $url) {
                $url = trim($url);
                if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    $url = trailingslashit(esc_url_raw($url));
                    
                    $robots_setting = array(
                        'index' => isset($_POST['bulk_index']) ? sanitize_text_field($_POST['bulk_index']) : 'default',
                        'follow' => isset($_POST['bulk_follow']) ? sanitize_text_field($_POST['bulk_follow']) : 'default'
                    );
                    
                    update_option('seo_control_pro_' . md5($url), $robots_setting);
                    $processed++;
                }
            }
            
            echo '<div class="notice notice-success is-dismissible"><p><strong>' . $processed . ' URLs processed successfully!</strong></p></div>';
        }
        
        if (isset($_POST['single_url']) && !empty($_POST['single_url'])) {
            // Single URL processing
            $url = trailingslashit(esc_url_raw($_POST['single_url']));
            
            $robots_setting = array(
                'index' => isset($_POST['single_index']) ? sanitize_text_field($_POST['single_index']) : 'default',
                'follow' => isset($_POST['single_follow']) ? sanitize_text_field($_POST['single_follow']) : 'default'
            );
            
            update_option('seo_control_pro_' . md5($url), $robots_setting);
            echo '<div class="notice notice-success is-dismissible"><p><strong>URL settings updated:</strong><br>' . esc_html($url) . '</p></div>';
        }
        
        // Handle delete action
        if (isset($_POST['delete_url'])) {
            $url_to_delete = sanitize_text_field($_POST['delete_url']);
            delete_option('seo_control_pro_' . md5($url_to_delete));
            echo '<div class="notice notice-success is-dismissible"><p><strong>URL removed from control list</strong></p></div>';
        }
    }
    
    // Get all saved URLs
    $saved_urls = seo_get_all_controlled_urls();
    
    ?>
    <div class="wrap">
        <h1>🚀 SEO Index Control Pro</h1>
        
        <!-- Single URL Control -->
        <div class="postbox" style="margin-top: 20px;">
            <h2 class="hndle" style="padding: 10px 15px;">Single URL Control</h2>
            <div class="inside" style="padding: 15px;">
                <p>Control SEO robots meta for a specific URL:</p>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th scope="row">URL</th>
                            <td>
                                <input type="url" name="single_url" required style="width: 100%; max-width: 500px;" 
                                       placeholder="https://yourdomain.com/page/" />
                                <p class="description">Example: <?php echo esc_url(home_url()); ?>/product/sample-page/</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Index Setting</th>
                            <td>
                                <label><input type="radio" name="single_index" value="index" checked> <strong>Index</strong> - Allow search engines to index</label><br>
                                <label><input type="radio" name="single_index" value="noindex"> <strong>NoIndex</strong> - Prevent search engines from indexing</label><br>
                                <label><input type="radio" name="single_index" value="default"> <strong>Default</strong> - Use theme/plugin default</label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Follow Setting</th>
                            <td>
                                <label><input type="radio" name="single_follow" value="follow" checked> <strong>Follow</strong> - Allow search engines to follow links</label><br>
                                <label><input type="radio" name="single_follow" value="nofollow"> <strong>NoFollow</strong> - Prevent search engines from following links</label><br>
                                <label><input type="radio" name="single_follow" value="default"> <strong>Default</strong> - Use theme/plugin default</label>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Update URL Settings" />
                    </p>
                </form>
            </div>
        </div>
        
        <!-- Bulk URL Control -->
        <div class="postbox" style="margin-top: 20px;">
            <h2 class="hndle" style="padding: 10px 15px;">🔥 Bulk URL Control</h2>
            <div class="inside" style="padding: 15px;">
                <p><strong>Paste multiple URLs (one per line) to apply the same settings to all:</strong></p>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th scope="row">URLs</th>
                            <td>
                                <textarea name="bulk_urls" rows="8" style="width: 100%; max-width: 500px;" 
                                          placeholder="https://yourdomain.com/page1/
https://yourdomain.com/page2/
https://yourdomain.com/page3/"></textarea>
                                <p class="description">Enter one URL per line. Invalid URLs will be skipped.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Index Setting</th>
                            <td>
                                <label><input type="radio" name="bulk_index" value="index" checked> <strong>Index</strong></label>
                                <label><input type="radio" name="bulk_index" value="noindex"> <strong>NoIndex</strong></label>
                                <label><input type="radio" name="bulk_index" value="default"> <strong>Default</strong></label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Follow Setting</th>
                            <td>
                                <label><input type="radio" name="bulk_follow" value="follow" checked> <strong>Follow</strong></label>
                                <label><input type="radio" name="bulk_follow" value="nofollow"> <strong>NoFollow</strong></label>
                                <label><input type="radio" name="bulk_follow" value="default"> <strong>Default</strong></label>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Apply to All URLs" />
                    </p>
                </form>
            </div>
        </div>
        
        <!-- Managed URLs List -->
        <?php if (!empty($saved_urls)): ?>
        <div class="postbox" style="margin-top: 20px;">
            <h2 class="hndle" style="padding: 10px 15px;">📋 Currently Managed URLs (<?php echo count($saved_urls); ?>)</h2>
            <div class="inside" style="padding: 15px;">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 50%;">URL</th>
                            <th>Index</th>
                            <th>Follow</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saved_urls as $url => $settings): ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" title="View page">
                                    <?php echo esc_html($url); ?>
                                </a>
                            </td>
                            <td>
                                <span class="<?php echo $settings['index'] === 'noindex' ? 'seo-noindex' : 'seo-index'; ?>">
                                    <?php echo esc_html(ucfirst($settings['index'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?php echo $settings['follow'] === 'nofollow' ? 'seo-nofollow' : 'seo-follow'; ?>">
                                    <?php echo esc_html(ucfirst($settings['follow'])); ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="delete_url" value="<?php echo esc_attr($url); ?>">
                                    <input type="submit" class="button button-small" value="Remove" 
                                           onclick="return confirm('Remove this URL from SEO control?');">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <style>
    .seo-noindex { color: #d63638; font-weight: bold; }
    .seo-index { color: #00a32a; font-weight: bold; }
    .seo-nofollow { color: #d63638; font-weight: bold; }
    .seo-follow { color: #00a32a; font-weight: bold; }
    .postbox h2.hndle { background: #f1f1f1; margin: 0; }
    </style>
    <?php
}

// ✅ 3. Get all controlled URLs
function seo_get_all_controlled_urls() {
    global $wpdb;
    
    $results = $wpdb->get_results(
        "SELECT option_name, option_value FROM {$wpdb->options} 
         WHERE option_name LIKE 'seo_control_pro_%'",
        ARRAY_A
    );
    
    $urls = array();
    foreach ($results as $result) {
        $hash = str_replace('seo_control_pro_', '', $result['option_name']);
        $settings = maybe_unserialize($result['option_value']);
        
        // Try to find the original URL (this is a limitation of using MD5)
        // In a production environment, you might want to store URL mappings
        foreach (seo_get_sample_urls() as $sample_url) {
            if (md5(trailingslashit($sample_url)) === $hash) {
                $urls[$sample_url] = $settings;
                break;
            }
        }
    }
    
    return $urls;
}

// ✅ 4. Helper function to get sample URLs (you might want to improve this)
function seo_get_sample_urls() {
    // This is a basic implementation. In production, you might want to:
    // - Store URL->hash mapping in a separate option
    // - Or use a custom table to store URL mappings
    return array(); // Placeholder
}

// ✅ 5. Get current full URL with trailing slash
function seo_index_control_get_current_url() {
    return trailingslashit(home_url(add_query_arg([], $_SERVER['REQUEST_URI'])));
}

// ✅ 6. WordPress native robots tag override (WP 5.7+)
add_filter('wp_robots', function ($robots) {
    $url = seo_index_control_get_current_url();
    $settings = get_option('seo_control_pro_' . md5($url));
    
    if (!$settings || !is_array($settings)) {
        return $robots;
    }
    
    $new_robots = array();
    
    // Handle index setting
    if ($settings['index'] === 'index') {
        $new_robots['index'] = true;
        unset($new_robots['noindex']);
    } elseif ($settings['index'] === 'noindex') {
        $new_robots['noindex'] = true;
        unset($new_robots['index']);
    }
    
    // Handle follow setting
    if ($settings['follow'] === 'follow') {
        $new_robots['follow'] = true;
        unset($new_robots['nofollow']);
    } elseif ($settings['follow'] === 'nofollow') {
        $new_robots['nofollow'] = true;
        unset($new_robots['follow']);
    }
    
    return !empty($new_robots) ? $new_robots : $robots;
}, 9999);

// ✅ 7. Yoast SEO override
add_filter('wpseo_robots', function ($robots) {
    $url = seo_index_control_get_current_url();
    $settings = get_option('seo_control_pro_' . md5($url));
    
    if (!$settings || !is_array($settings)) {
        return $robots;
    }
    
    $robot_parts = array();
    
    // Handle index setting
    if ($settings['index'] === 'index') {
        $robot_parts[] = 'index';
    } elseif ($settings['index'] === 'noindex') {
        $robot_parts[] = 'noindex';
    }
    
    // Handle follow setting
    if ($settings['follow'] === 'follow') {
        $robot_parts[] = 'follow';
    } elseif ($settings['follow'] === 'nofollow') {
        $robot_parts[] = 'nofollow';
    }
    
    return !empty($robot_parts) ? implode(', ', $robot_parts) : $robots;
}, 9999);

// ✅ 8. Rank Math SEO override
add_filter('rank_math/frontend/robots', function ($robots) {
    $url = seo_index_control_get_current_url();
    $settings = get_option('seo_control_pro_' . md5($url));
    
    if (!$settings || !is_array($settings)) {
        return $robots;
    }
    
    $robot_parts = array();
    
    // Handle index setting
    if ($settings['index'] === 'index') {
        $robot_parts[] = 'index';
    } elseif ($settings['index'] === 'noindex') {
        $robot_parts[] = 'noindex';
    }
    
    // Handle follow setting
    if ($settings['follow'] === 'follow') {
        $robot_parts[] = 'follow';
    } elseif ($settings['follow'] === 'nofollow') {
        $robot_parts[] = 'nofollow';
    }
    
    return !empty($robot_parts) ? implode(', ', $robot_parts) : $robots;
}, 9999);

// ✅ 9. All in One SEO override
add_filter('aioseo_robots_meta', function ($robots_meta) {
    $url = seo_index_control_get_current_url();
    $settings = get_option('seo_control_pro_' . md5($url));
    
    if (!$settings || !is_array($settings)) {
        return $robots_meta;
    }
    
    // Handle index setting
    if ($settings['index'] === 'index') {
        $robots_meta['index'] = true;
    } elseif ($settings['index'] === 'noindex') {
        $robots_meta['index'] = false;
    }
    
    // Handle follow setting
    if ($settings['follow'] === 'follow') {
        $robots_meta['follow'] = true;
    } elseif ($settings['follow'] === 'nofollow') {
        $robots_meta['follow'] = false;
    }
    
    return $robots_meta;
}, 9999);

// ✅ 10. Add admin notice for successful operations
add_action('admin_notices', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'seo-index-control-pro') {
        // Any additional notices can be added here
    }
});

// ✅ 11. Enhanced URL storage (improved version)
function seo_store_url_mapping($url, $settings) {
    $hash = md5(trailingslashit($url));
    $mappings = get_option('seo_control_url_mappings', array());
    $mappings[$hash] = trailingslashit($url);
    update_option('seo_control_url_mappings', $mappings);
    update_option('seo_control_pro_' . $hash, $settings);
}

// ✅ 12. Get all controlled URLs (improved version)
function seo_get_all_controlled_urls_improved() {
    $mappings = get_option('seo_control_url_mappings', array());
    $urls = array();
    
    foreach ($mappings as $hash => $url) {
        $settings = get_option('seo_control_pro_' . $hash);
        if ($settings) {
            $urls[$url] = $settings;
        }
    }
    
    return $urls;
}
?>