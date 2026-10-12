



<?php

if ( ! defined( 'MAILSEXTRACT_THEME_PATH' ) ) {
    define( 'MAILSEXTRACT_THEME_PATH', get_template_directory() );
}

function mailsextract_theme_setup() {
    add_theme_support( 'post-thumbnails' );
}

add_action( 'after_setup_theme', 'mailsextract_theme_setup' );

function mailsextract_scripts() {

    wp_enqueue_style(
        'mailsextract-tailwind-css',
        get_template_directory_uri() . '/assets/css/output.css',
        array(),
        filemtime(get_template_directory() . '/assets/css/output.css')
    );

}

add_action( 'wp_enqueue_scripts', 'mailsextract_scripts' );

require_once MAILSEXTRACT_THEME_PATH . '/inc/default-featured-image.php';
require_once MAILSEXTRACT_THEME_PATH . '/inc/service-page-fields.php';
require_once MAILSEXTRACT_THEME_PATH . '/inc/post-faq-fields.php';



// function add_tailwind_to_wp() {
//     wp_register_script( 'tailwind-cdn', 'https://cdn.tailwindcss.com', array(), null, false );

//     wp_add_inline_script(
//         'tailwind-cdn',
//         'window.tailwind = { config: { corePlugins: { preflight: false } } };',
//         'before'
//     );

//     wp_enqueue_script( 'tailwind-cdn' );
// }
// add_action( 'wp_enqueue_scripts', 'add_tailwind_to_wp' );


// /* Remove image width & height attributes */
// add_filter( 'wp_get_attachment_image_attributes', function( $attr ) {
//     unset( $attr['width'] );
//     unset( $attr['height'] );
//     return $attr;
// }, 10 );


/**
 * Microsoft OAuth login URL for the custom theme.
 *
 * @return string
 */
function mailsextract_microsoft_oauth_url() {
    $client_id = 'f8344f13-2784-41c2-93d9-d7c8ad3b4be3';
    $redirect_uri = 'https://www.mailsextract.com/microsoft_oauth.php';
    $auth_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';

    $params = array(
        'client_id' => $client_id,
        'response_type' => 'code',
        'redirect_uri' => $redirect_uri,
        'scope' => 'openid profile email User.Read',
        'response_mode' => 'query',
        'state' => wp_create_nonce( 'microsoft_oauth' ),
    );

    return $auth_url . '?' . http_build_query( $params );
}

/**
 * Return the Microsoft login button HTML.
 *
 * @param string $extra_class Optional additional CSS class.
 * @return string
 */
function mailsextract_microsoft_login_button( $extra_class = '' ) {
    return sprintf(
        '<a href="%1$s" class="inline-flex w-full items-center justify-center gap-3 rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-50 %2$s">%3$sContinue with Microsoft</a>',
        esc_url( mailsextract_microsoft_oauth_url() ),
        esc_attr( $extra_class ),
        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 23 23" class="h-5 w-5"><rect x="1" y="1" width="10" height="10" fill="#f25022"/><rect x="12" y="1" width="10" height="10" fill="#7fba00"/><rect x="1" y="12" width="10" height="10" fill="#00a4ef"/><rect x="12" y="12" width="10" height="10" fill="#ffb900"/></svg>'
    );
}
