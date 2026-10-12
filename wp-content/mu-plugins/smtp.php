<?php
/**
 * Plugin Name: Custom Code SMTP
 * Description: Forces WordPress to use authenticated SMTP instead of local sendmail.
 */

add_action( 'phpmailer_init', 'setup_custom_smtp' );

function setup_custom_smtp( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->SMTPSecure = 'tls';
    
    // Your Gmail Address
    $phpmailer->Username   = 'notification.outright@gmail.com';
    
    // Your 16-character Gmail App Password (no spaces)
    $phpmailer->Password   = 'bgbucbidtodufemg';
    
    // Who the email should look like it's from
    $phpmailer->From       = 'notification.outright@gmail.com';
    $phpmailer->FromName   = 'Indexing Alerts';
}
