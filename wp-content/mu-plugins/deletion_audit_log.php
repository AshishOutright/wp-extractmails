<?php
/*
Plugin Name: Custom Deletion Audit Log
Description: Silently logs trashed and permanently deleted posts/pages with the username and timestamp.
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function audit_log_post_action( $post_id, $action_type ) {
    // Get the user triggering the action
    $current_user = wp_get_current_user();
    $username = ( $current_user->exists() ) ? $current_user->user_login : 'System/Automated_Script';
    
    // Retrieve the post details
    $post = get_post( $post_id );
    
    // Ignore autosaves and revisions to keep the log clean
    if ( ! $post || $post->post_type === 'revision' ) {
        return;
    }

    // Capture the exact server time
    $time = current_time( 'mysql' );
    
    // Format the log entry
    $log_entry = sprintf(
        "[%s] ACTION: %s | ID: %d | Type: %s | Title: '%s' | Executed By: %s\n",
        $time,
        $action_type,
        $post_id,
        $post->post_type,
        $post->post_title,
        $username
    );

    // Define the log file location (creates deletion_audit.log in wp-content)
    $log_file = WP_CONTENT_DIR . '/deletion_audit.log';
    
    // Write to the file securely
    file_put_contents( $log_file, $log_entry, FILE_APPEND | LOCK_EX );
}

// Hook into the Trash action
add_action( 'wp_trash_post', function( $post_id ) { 
    audit_log_post_action( $post_id, 'TRASHED' ); 
});

// Hook into the Permanent Deletion action
add_action( 'before_delete_post', function( $post_id ) { 
    audit_log_post_action( $post_id, 'PERMANENTLY DELETED' ); 
});
?>
