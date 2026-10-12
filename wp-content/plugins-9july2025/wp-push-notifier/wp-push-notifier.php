<?php
/**
 * Plugin Name: WP Push Notifier
 * Description: Sends push notifications to users when a post is published.
 * Version: 1.0
 * Author: Your Name
 */

// Load JS to ask for permission
add_action('wp_enqueue_scripts', 'wpn_enqueue_script');
function wpn_enqueue_script() {
    wp_enqueue_script('wpn-push', plugin_dir_url(__FILE__) . 'push.js', [], null, true);
}

// Serve the service worker
add_action('init', function () {
    if (strpos($_SERVER['REQUEST_URI'], '/wp-push-notifier/sw.js') !== false) {
        header('Content-Type: application/javascript');
        readfile(__DIR__ . '/sw.js');
        exit;
    }
});

// Save post details to JSON
add_action('publish_post', 'wpn_save_notification_data', 10, 2);
function wpn_save_notification_data($ID, $post) {
    $data = [
        'title' => get_the_title($ID),
        'body' => 'New post published: ' . get_the_title($ID),
        'url' => get_permalink($ID),
        'icon' => plugin_dir_url(__FILE__) . 'icon.png'
    ];
    file_put_contents(__DIR__ . '/latest.json', json_encode($data));
}
