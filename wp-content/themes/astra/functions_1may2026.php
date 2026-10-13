<?php
/**
 * Astra functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define Constants
 */
define( 'ASTRA_THEME_VERSION', '4.11.15' );
define( 'ASTRA_THEME_SETTINGS', 'astra-settings' );
define( 'ASTRA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ASTRA_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );
define( 'ASTRA_THEME_ORG_VERSION', file_exists( ASTRA_THEME_DIR . 'inc/w-org-version.php' ) );

/**
 * Minimum Version requirement of the Astra Pro addon.
 * This constant will be used to display the notice asking user to update the Astra addon to the version defined below.
 */
define( 'ASTRA_EXT_MIN_VER', '4.11.6' );

/**
 * Load in-house compatibility.
 */
if ( ASTRA_THEME_ORG_VERSION ) {
	require_once ASTRA_THEME_DIR . 'inc/w-org-version.php';
}

/**
 * Setup helper functions of Astra.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-theme-options.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-theme-strings.php';
require_once ASTRA_THEME_DIR . 'inc/core/common-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-icons.php';

define( 'ASTRA_WEBSITE_BASE_URL', 'https://wpastra.com' );

/**
 * Deprecate constants in future versions as they are no longer used in the codebase.
 */
define( 'ASTRA_PRO_UPGRADE_URL', ASTRA_THEME_ORG_VERSION ? astra_get_pro_url( '/pricing/', 'free-theme', 'dashboard', 'upgrade' ) : 'https://woocommerce.com/products/astra-pro/' );
define( 'ASTRA_PRO_CUSTOMIZER_UPGRADE_URL', ASTRA_THEME_ORG_VERSION ? astra_get_pro_url( '/pricing/', 'free-theme', 'customizer', 'upgrade' ) : 'https://woocommerce.com/products/astra-pro/' );

/**
 * Update theme
 */
require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-update-functions.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-background-updater.php';

/**
 * Fonts Files
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-font-families.php';
if ( is_admin() ) {
	require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts-data.php';
}

require_once ASTRA_THEME_DIR . 'inc/lib/webfont/class-astra-webfont-loader.php';
require_once ASTRA_THEME_DIR . 'inc/lib/docs/class-astra-docs-loader.php';
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts.php';

require_once ASTRA_THEME_DIR . 'inc/dynamic-css/custom-menu-old-header.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/container-layouts.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/astra-icons.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-walker-page.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-enqueue-scripts.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-gutenberg-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-wp-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/block-editor-compatibility.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/inline-on-mobile.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/content-background.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/dark-mode.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-dynamic-css.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-global-palette.php';

// Enable NPS Survey only if the starter templates version is < 4.3.7 or > 4.4.4 to prevent fatal error.
if ( ! defined( 'ASTRA_SITES_VER' ) || version_compare( ASTRA_SITES_VER, '4.3.7', '<' ) || version_compare( ASTRA_SITES_VER, '4.4.4', '>' ) ) {
	// NPS Survey Integration
	require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-notice.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/class-astra-nps-survey.php';
}

/**
 * Custom template tags for this theme.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-attr.php';
require_once ASTRA_THEME_DIR . 'inc/template-tags.php';

require_once ASTRA_THEME_DIR . 'inc/widgets.php';
require_once ASTRA_THEME_DIR . 'inc/core/theme-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/admin-functions.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-memory-limit-notice.php';
require_once ASTRA_THEME_DIR . 'inc/core/sidebar-manager.php';

/**
 * Markup Functions
 */
require_once ASTRA_THEME_DIR . 'inc/markup-extras.php';
require_once ASTRA_THEME_DIR . 'inc/extras.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog-config.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog.php';
require_once ASTRA_THEME_DIR . 'inc/blog/single-blog.php';

/**
 * Markup Files
 */
require_once ASTRA_THEME_DIR . 'inc/template-parts.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-loop.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-mobile-header.php';

/**
 * Functions and definitions.
 */
require_once ASTRA_THEME_DIR . 'inc/class-astra-after-setup-theme.php';

// Required files.
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-helper.php';

require_once ASTRA_THEME_DIR . 'inc/schema/class-astra-schema.php';

/* Setup API */
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-api-init.php';

if ( is_admin() ) {
	/**
	 * Admin Menu Settings
	 */
	require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-settings.php';
	require_once ASTRA_THEME_DIR . 'admin/class-astra-admin-loader.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/astra-notices/class-astra-notices.php';
}

/**
 * Metabox additions.
 */
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-boxes.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-box-operations.php';
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-elementor-editor-settings.php';

/**
 * Customizer additions.
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-customizer.php';

/**
 * Astra Modules.
 */
require_once ASTRA_THEME_DIR . 'inc/modules/posts-structures/class-astra-post-structures.php';
require_once ASTRA_THEME_DIR . 'inc/modules/related-posts/class-astra-related-posts.php';

/**
 * Compatibility
 */
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gutenberg.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-jetpack.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/woocommerce/class-astra-woocommerce.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/class-astra-edd.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/lifterlms/class-astra-lifterlms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/learndash/class-astra-learndash.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bb-ultimate-addon.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-contact-form-7.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-visual-composer.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-site-origin.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gravity-forms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bne-flyout.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-ubermeu.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-divi-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-amp.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-yoast-seo.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/surecart/class-astra-surecart.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-starter-content.php';
require_once ASTRA_THEME_DIR . 'inc/addons/transparent-header/class-astra-ext-transparent-header.php';
require_once ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/class-astra-breadcrumbs.php';
require_once ASTRA_THEME_DIR . 'inc/addons/scroll-to-top/class-astra-scroll-to-top.php';
require_once ASTRA_THEME_DIR . 'inc/addons/heading-colors/class-astra-heading-colors.php';
require_once ASTRA_THEME_DIR . 'inc/builder/class-astra-builder-loader.php';

// Elementor Compatibility requires PHP 5.4 for namespaces.
if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor-pro.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-web-stories.php';
}

// Beaver Themer compatibility requires PHP 5.3 for anonymous functions.
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-themer.php';
}

require_once ASTRA_THEME_DIR . 'inc/core/markup/class-astra-markup.php';

/**
 * Load deprecated functions
 */
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-filters.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-functions.php';



function add_tailwind_to_wp() {
    $tailwind_path = get_template_directory() . '/assets/css/tailwind.generated.css';
    $tailwind_version = file_exists( $tailwind_path ) ? filemtime( $tailwind_path ) : ASTRA_THEME_VERSION;

    wp_enqueue_style(
        'astra-tailwind',
        get_template_directory_uri() . '/assets/css/tailwind.generated.css',
        array(),
        $tailwind_version
    );
}
add_action('wp_enqueue_scripts', 'add_tailwind_to_wp');


/* Remove image width & height attributes */
add_filter( 'wp_get_attachment_image_attributes', function( $attr ) {
    unset( $attr['width'] );
    unset( $attr['height'] );
    return $attr;
}, 10 );
















/* Remove Gutenberg image block classes */
function outr_remove_wp_block_image_classes($content){

    // remove wp-block-image and size-full
    $content = preg_replace('/wp-block-image/', 'post-image', $content);
    $content = preg_replace('/size-full/', '', $content);

    return $content;
}
add_filter('the_content', 'outr_remove_wp_block_image_classes', 99);


/* Remove image width/height attributes */
function outr_remove_image_dimensions($attr){
    unset($attr['width']);
    unset($attr['height']);
    return $attr;
}
add_filter('wp_get_attachment_image_attributes','outr_remove_image_dimensions',10);










// Add Google login button to the login form
function add_google_login_button() {
    $client_id = '1024749291174-k4ucerqo215lr6n6348gj52mj5noh3u0.apps.googleusercontent.com'; // Replace with your actual Client ID
    $redirect_uri = 'https://www.extractmails.com';
    
    $login_url = "https://accounts.google.com/o/oauth2/auth?" . http_build_query([
        'response_type' => 'code',
        'client_id'     => $client_id,
        'redirect_uri'  => $redirect_uri,
        'scope'         => 'openid email profile',
        'access_type'   => 'offline',
        'prompt'        => 'consent'
    ]);
    
   ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<div class="login-container">
    <div class="login-box">
        <h3>Sign in to Continue</h3>
        <a href="<?php echo esc_url($login_url); ?>" class="google-button">
		<i class="fa-brands fa-google"></i>
            Continue with Google
        </a>
    </div>
</div>

<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa;
    }

    .login-box {
        text-align: center;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 320px;
    }

    .login-box h3 {
        margin-bottom: 20px;
        font-size: 20px;
        color: #333;
    }

    .google-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: 500;
        background-color: #ffffff;
        border: 1px solid #dcdcdc;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        color: #333;
        transition: all 0.3s ease;
    }

    .google-button img {
        width: 20px;
        height: 20px;
    }

    .google-button:hover {
        background-color: #f1f1f1;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }
</style>

<?php
}
add_action('login_form', 'add_google_login_button');

//kamals code
//kamals code
function microsoft_login_button() {
    $client_id = 'f8344f13-2784-41c2-93d9-d7c8ad3b4be3';
    $redirect_uri = 'https://www.extractmails.com/microsoft_oauth.php';
    
    // OAuth authorization URL
    $auth_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    
    // Scopes needed for Microsoft Graph API
    $scopes = [
        'openid',
        'profile',
        'email',
        'User.Read'
    ];
    
    // Build the authorization URL
    $params = [
        'client_id' => $client_id,
        'response_type' => 'code',
        'redirect_uri' => $redirect_uri,
        'scope' => implode(' ', $scopes),
        'response_mode' => 'query',
        'state' => wp_create_nonce('microsoft-oauth')
    ];
    
    $auth_link = $auth_url . '?' . http_build_query($params);
    
    // HTML for the button
    $button_html = '
    <div class="microsoft-login-container">
        <a href="' . esc_url($auth_link) . '" class="microsoft-login-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 23 23">
                <rect x="1" y="1" width="10" height="10" fill="#f25022"/>
                <rect x="12" y="1" width="10" height="10" fill="#7fba00"/>
                <rect x="1" y="12" width="10" height="10" fill="#00a4ef"/>
                <rect x="12" y="12" width="10" height="10" fill="#ffb900"/>
            </svg>
            Continue with Microsoft
        </a>
    </div>
    <style>
        .microsoft-login-container {
            margin: 15px 0;
        }
        .microsoft-login-button {
            display: flex;
            align-items: center;
            background-color: #2f2f2f;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            font-size: 16px;
            width: fit-content;
            transition: background-color 0.3s;
        }
        .microsoft-login-button svg {
            margin-right: 10px;
        }
        .microsoft-login-button:hover {
            background-color: #404040;
            color: white;
            text-decoration: none;
        }
    </style>
    ';
    
    return $button_html;
}

// Shortcode to display the login button
function microsoft_login_shortcode() {
    return microsoft_login_button();
}
add_shortcode('microsoft_login', 'microsoft_login_shortcode');

// Function to add the login button to the login form
function add_microsoft_login_to_form() {
    echo microsoft_login_button();
}
add_action('login_form', 'add_microsoft_login_to_form');
add_action('register_form', 'add_microsoft_login_to_form');

// Define a function to handle Microsoft API errors
function handle_microsoft_api_error($error_message) {
    // Log the error
    error_log('Microsoft OAuth Error: ' . $error_message);
    
    // Redirect to login page with error
    wp_redirect(wp_login_url() . '?microsoft_error=' . urlencode($error_message));
    exit;
}

// Add message for Microsoft login errors
function microsoft_login_error_message() {
    if (isset($_GET['microsoft_error'])) {
        echo '<div class="login-error">' . esc_html(urldecode($_GET['microsoft_error'])) . '</div>';
    }
}
add_action('login_message', 'microsoft_login_error_message');

// Optional: Add button to a widget area
function register_microsoft_login_widget() {
    register_widget('Microsoft_Login_Widget');
}
add_action('widgets_init', 'register_microsoft_login_widget');

class Microsoft_Login_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'microsoft_login_widget',
            'Microsoft Login Button',
            array('description' => 'Displays a "Continue with Microsoft" button')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        echo microsoft_login_button();
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Login with Microsoft';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

// Add filter to check if user is already logged in with Microsoft
function check_microsoft_user_login($user, $username, $password) {
    // Check if login is being processed by Microsoft OAuth
    if (isset($_SESSION['microsoft_processing_login']) && $_SESSION['microsoft_processing_login'] === true) {
        // Clear the flag
        unset($_SESSION['microsoft_processing_login']);
        return $user;
    }
    
    // Check if user is trying to login with Microsoft credentials
    if (!empty($username) && !empty($password)) {
        $user_obj = get_user_by('login', $username);
        if ($user_obj && get_user_meta($user_obj->ID, 'microsoft_user_id', true)) {
            // This is a Microsoft user, so we can proceed with normal login
            return $user;
        }
    }
    
    return $user;
}
add_filter('authenticate', 'check_microsoft_user_login', 30, 3);

// Add action to initialize session if needed
function start_session_for_microsoft_login() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session_for_microsoft_login');

// NEW: Hook to add user to Brevo when Microsoft user registers
function add_microsoft_user_to_brevo($user_id) {
    // Check if this is a Microsoft user by looking for microsoft_user_id meta
    $microsoft_user_id = get_user_meta($user_id, 'microsoft_user_id', true);
    
    if (!empty($microsoft_user_id)) {
        $user = get_userdata($user_id);
        
        if ($user) {
            // Extract user information
            $email = $user->user_email;
            $first_name = get_user_meta($user_id, 'first_name', true);
            $last_name = get_user_meta($user_id, 'last_name', true);
            $display_name = $user->display_name;
            
            // Call the Brevo function if it exists
            if (function_exists('add_user_to_brevo')) {
                add_user_to_brevo($email, $first_name, $last_name, $display_name, $user_id);
                
                // Log for debugging
                error_log('Microsoft user added to Brevo: ' . $email);
            } else {
                error_log('add_user_to_brevo function not found');
            }
        }
    }
}

// Hook into user registration for new users
add_action('user_register', 'add_microsoft_user_to_brevo');

// NEW: Alternative hook for when Microsoft login is completed
function handle_microsoft_login_success($user_id, $is_new_user = false) {
    // This function should be called from your microsoft_oauth.php file
    // when a user successfully logs in via Microsoft
    
    if ($is_new_user) {
        // This is a new user registration via Microsoft
        $user = get_userdata($user_id);
        
        if ($user) {
            $email = $user->user_email;
            $first_name = get_user_meta($user_id, 'first_name', true);
            $last_name = get_user_meta($user_id, 'last_name', true);
            $display_name = $user->display_name;
            
            // Call the Brevo function if it exists
            if (function_exists('add_user_to_brevo')) {
                add_user_to_brevo($email, $first_name, $last_name, $display_name, $user_id);
                error_log('New Microsoft user added to Brevo: ' . $email);
            }
        }
    }
}

// NEW: Hook into wp_login to catch Microsoft users logging in
function check_and_add_microsoft_user_to_brevo($user_login, $user) {
    // Check if this user has Microsoft OAuth data and hasn't been added to Brevo yet
    $microsoft_user_id = get_user_meta($user->ID, 'microsoft_user_id', true);
    $brevo_added = get_user_meta($user->ID, 'brevo_added', true);
    
    if (!empty($microsoft_user_id) && empty($brevo_added)) {
        $email = $user->user_email;
        $first_name = get_user_meta($user->ID, 'first_name', true);
        $last_name = get_user_meta($user->ID, 'last_name', true);
        $display_name = $user->display_name;
        
        // Call the Brevo function if it exists
        if (function_exists('add_user_to_brevo')) {
            $result = add_user_to_brevo($email, $first_name, $last_name, $display_name, $user->ID);
            
            // Mark user as added to Brevo to avoid duplicate additions
            if ($result !== false) {
                update_user_meta($user->ID, 'brevo_added', '1');
                error_log('Microsoft user added to Brevo on login: ' . $email);
            }
        }
    }
}
add_action('wp_login', 'check_and_add_microsoft_user_to_brevo', 10, 2);

//kamals microsoft login code end

//microsoft login logout button start code 
// Microsoft login/logout button function
function Microsoft_login_logout_button() {
    if (is_user_logged_in()) {
        $button_text = 'Logout';
        $button_link = wp_logout_url(home_url());
        $button_class = 'logout-btn';
    } else {
        $client_id = 'f8344f13-2784-41c2-93d9-d7c8ad3b4be3';
        $redirect_uri = 'https://www.extractmails.com/microsoft_oauth.php';
        
        // OAuth authorization URL
        $auth_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
        
        // Scopes needed for Microsoft Graph API
        $scopes = [
            'openid',
            'profile',
            'email',
            'User.Read'
        ];
        
        // Build the authorization URL
        $params = [
            'client_id' => $client_id,
            'response_type' => 'code',
            'redirect_uri' => $redirect_uri,
            'scope' => implode(' ', $scopes),
            'response_mode' => 'query',
            'state' => wp_create_nonce('microsoft-oauth')
        ];
        
        $auth_link = $auth_url . '?' . http_build_query($params);
        
        $button_text = 'Sign in with Microsoft';
        $button_link = $auth_link;
        $button_class = 'microsoft-btn';
    }
    
    // Create the button with Microsoft logo for login
    if (!is_user_logged_in()) {
        echo '<a href="' . esc_url($button_link) . '" class="btn btn-primary ' . esc_attr($button_class) . '">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 23 23" style="margin-right: 8px; vertical-align: middle;">
                <rect x="1" y="1" width="10" height="10" fill="#f25022"/>
                <rect x="12" y="1" width="10" height="10" fill="#7fba00"/>
                <rect x="1" y="12" width="10" height="10" fill="#00a4ef"/>
                <rect x="12" y="12" width="10" height="10" fill="#ffb900"/>
            </svg>
            ' . esc_html($button_text) . '
        </a>';
    } else {
        echo '<a href="' . esc_url($button_link) . '" class="btn btn-primary ' . esc_attr($button_class) . '">' . esc_html($button_text) . '</a>';
    }
}

// Hook to make sure styles are available
add_action('wp_footer', 'Microsoft_auth_button_styles');

function Microsoft_auth_button_styles() {
    echo '<style>
        .microsoft-btn, .logout-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }
        .microsoft-btn {
            background-color: #2f2f2f;
            color: #fff;
            border: 2px solid #2f2f2f;
        }
        .microsoft-btn:hover {
            background-color: #404040;
            border-color: #404040;
            color: white;
            text-decoration: none;
        }
        .logout-btn {
            background-color: #ff4d4d;
            color: #fff;
            border: 2px solid #ff4d4d;
        }
        .logout-btn:hover {
            background-color: #e60000;
            border-color: #e60000;
        }
    </style>';
}
// micosoft login logout button end
// Function to generate the Google OAuth URL
function google_oauth_url() {
    $client_id = '1024749291174-k4ucerqo215lr6n6348gj52mj5noh3u0.apps.googleusercontent.com'; // Your Google Client ID
    $redirect_uri = 'https://www.extractmails.com'; // The redirect URI must match the one registered in the Google Developer Console
    return "https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=$client_id&redirect_uri=" . urlencode($redirect_uri) . "&scope=email%20profile";
}

// Function to handle Google login
function handle_google_login() {
    if (isset($_GET['code'])) {
        $client_id = '1024749291174-k4ucerqo215lr6n6348gj52mj5noh3u0.apps.googleusercontent.com'; // Your Google Client ID
        $client_secret = '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'; // Your Google Client Secret
        $redirect_uri = 'https://www.extractmails.com'; // The redirect URI must match the one registered in the Google Developer Console

        // Make a POST request to exchange the code for an access token
        $response = wp_remote_post('https://oauth2.googleapis.com/token', [
            'body' => [
                'code' => $_GET['code'],
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code',
            ],
        ]);

        // Error handling for the response
        if (is_wp_error($response)) {
            wp_die('Error retrieving token');
        }

        // Parse the response to extract the token
        $token_response = json_decode(wp_remote_retrieve_body($response), true);

        // Check if we have an ID token
        if (isset($token_response['id_token'])) {
            // Decode the ID token to get user information
            $user_info = json_decode(base64_decode(explode('.', $token_response['id_token'])[1]), true);
            $email = $user_info['email'];
            $is_new_user = false;

            // Check if the user already exists in WordPress
            $user = get_user_by('email', $email);

            if (!$user) {
                // Create a new user if one does not exist
                $password = wp_generate_password(); // Generate a random password for new users
                $user_id = wp_create_user($email, $password, $email);
                
                if (is_wp_error($user_id)) {
                    wp_die('User creation failed');
                }

                // Assign default role (subscriber) to the new user
                wp_update_user(['ID' => $user_id, 'role' => 'subscriber']);
                $user = get_user_by('ID', $user_id);
                $is_new_user = true;
            }

            // Assign admin role if email matches a specific admin email
            if ($email === 'ashish@outrightcrm.com') {
                $user->set_role('administrator');
            }

            // If this is a new user, send data to Brevo or SendGrid if necessary
            if ($is_new_user) {
                $name = isset($user_info['name']) ? $user_info['name'] : '';
                $given_name = isset($user_info['given_name']) ? $user_info['given_name'] : '';
                $family_name = isset($user_info['family_name']) ? $user_info['family_name'] : '';

                // Add user to Brevo (optional)
                add_user_to_brevo($email, $given_name, $family_name, $name, $user->ID);

                // add user to rightee

                get_set_blog('outr_website_new_user',['web_user_id_c' => $user->ID],['module' => 'outr_website_new_user', 'web_user_id_c' => $user->ID, 'user_name_c' => $given_name, 'user_email_c' => $email, 'web_source_c' => 'Extractmails.com-sign-up'], 0);

                // Add user to SendGrid (optional)
                add_user_to_sendgrid($email, $given_name, $family_name, $name, $user->ID);
            }

            // Log in the user by setting authentication cookies
            wp_set_auth_cookie($user->ID, true);
            wp_set_current_user($user->ID);
            do_action('wp_login', $user->user_login, $user);

            // Redirect to the dashboard
            wp_redirect(admin_url());
            exit;
        } else {
            wp_die('Error retrieving ID token');
        }
    }
}

// Hook into WordPress init action to handle Google login
add_action('init', 'handle_google_login');
/**
 * Add user to Brevo contact list
 *
 * @param string $email User email
 * @param string $first_name User first name
 * @param string $last_name User last name
 * @param string $display_name User display name
 * @param int $user_id WordPress user ID
 * @return bool Success or failure
 */
function add_user_to_brevo($email, $first_name = '', $last_name = '', $display_name = '', $user_id = 0) {
    // Brevo API credentials
    $api_key = "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";
    $list_id = "57";
    $api_url = "https://api.brevo.com/v3/contacts";
    
    // Prepare the payload
    $payload = [
        'email' => $email,
        'SOURCE' => 'extractmails.com-signup',
        'attributes' => [
            'FIRSTNAME' => $first_name,
            'LASTNAME' => $last_name,
            'DISPLAYNAME' => $display_name,
            'WPUSERID' => (string)$user_id,
            'REGISTRATION_SOURCE' => 'google_oauth',
            'REGISTRATION_DATE' => current_time('mysql')
        ],
        'listIds' => [(int)$list_id],
        'updateEnabled' => true
    ];
    
    // Convert payload to JSON
    $json_payload = json_encode($payload);
    
    // Set up cURL request
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $api_key,
        'Content-Length: ' . strlen($json_payload)
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Check for errors
    if (curl_errno($ch)) {
        error_log('Brevo API Error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    // Log the response for debugging
    error_log('Brevo API Response (' . $http_code . '): ' . $response);
    
    // Store Brevo response data in user meta
    if ($user_id && ($http_code == 201 || $http_code == 204)) {
        update_user_meta($user_id, 'brevo_contact_added', 'yes');
        update_user_meta($user_id, 'brevo_contact_date', current_time('mysql'));
    }
    
    return ($http_code == 201 || $http_code == 204);
}

// sendgrid code 
/**
 * Add user to SendGrid contact list
 *
 * @param string $email User email
 * @param string $first_name User first name
 * @param string $last_name User last name
 * @param string $display_name User display name
 * @param int $user_id WordPress user ID
 * @return bool Success or failure
 */
function add_user_to_sendgrid($email, $first_name = '', $last_name = '', $display_name = '', $user_id = 0) {
    // SendGrid API credentials
    $api_key = "SG._OCwtYSFTWGhlARlEMsOwQ.APxlgAblMRYt11ht_qRvUAvEsviaqVtDKSAR7E51On4";
    $list_id = "9e03c0fc-dfa2-44bf-96d3-48193f4930fc";
    $api_url = "https://api.sendgrid.com/v3/marketing/contacts";
    
    // Match the payload structure from your working AppScript
    // Note: list_ids first, then contacts
    $payload = [
        'list_ids' => [$list_id],
        'contacts' => [
            [
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name
                // Note: Removing custom fields for initial test as they aren't in your AppScript
            ]
        ]
    ];
    
    // Convert payload to JSON
    $json_payload = json_encode($payload);
    
    // Enhanced logging for debugging
    error_log('SendGrid API Request Payload: ' . $json_payload);
    
    // Set up cURL request
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Check for errors and provide detailed logging
    if (curl_errno($ch)) {
        error_log('SendGrid API cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    // More detailed logging
    error_log('SendGrid API Response Code: ' . $http_code);
    error_log('SendGrid API Response Body: ' . $response);
    
    // Check for success - note that 202 is the expected success code for SendGrid
    if ($http_code == 202) {
        if ($user_id) {
            update_user_meta($user_id, 'sendgrid_contact_added', 'yes');
            update_user_meta($user_id, 'sendgrid_contact_date', current_time('mysql'));
        }
        return true;
    } else {
        // Store failure information for debugging
        if ($user_id) {
            update_user_meta($user_id, 'sendgrid_contact_error', $response);
            update_user_meta($user_id, 'sendgrid_contact_error_code', $http_code);
        }
        return false;
    }
}

// wp-login.php not accesaable
function custom_login_redirect() {
    $allowed_slug = '4Dx-one'; // Change this to your custom login slug
    $current_url = $_SERVER['REQUEST_URI'];

    if (strpos($current_url, 'wp-login.php') !== false) {
        // Handle logout properly
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            wp_logout(); // Logs out the user
            wp_redirect(home_url('?logged_out=1')); // Redirect to homepage with a query param
            exit;
        }

        // Block direct access unless using the correct slug
        if (strpos($current_url, $allowed_slug) === false) {
            wp_die(
                '<div style="text-align: center; padding: 50px; background-color: #121212; color: white; font-family: Arial, sans-serif; border-radius: 10px;">
                    <h1 style="font-size: 32px; font-weight: bold; color: #ffcc00;">🔒 Access Denied!</h1>
                    <p style="font-size: 18px;">Hey, this is the <strong>most secure website</strong> on WordPress. You need to do better to harm this website.</p>
                    <p style="font-size: 16px;">🚀 Keep trying, but you won’t get through! 😉</p>
                    <a href="' . home_url() . '" style="display: inline-block; padding: 10px 20px; font-size: 18px; font-weight: bold; background-color: #ffcc00; color: black; text-decoration: none; border-radius: 5px; margin-top: 20px;">Go Back Home</a>
                </div>', 
                'Access Restricted'
            );
        }
    }
}
add_action('init', 'custom_login_redirect');

// Show custom message after logout
function custom_logout_message() {
    if (isset($_GET['logged_out']) && $_GET['logged_out'] == 1) {
        echo "<script>alert('Hey, You logged out from a most secure website on WordPress.');</script>";
    }
}
add_action('wp_footer', 'custom_logout_message');


// Add custom login slug
// Add Google One Tap Sign-In
function add_google_one_tap_signin() {
    // Your Google OAuth Client ID - replace with your actual Client ID from Google Cloud Console
    $client_id = '568468862727-9bph90edbsed5dhr95ga15quhfuebap6.apps.googleusercontent.com';
    
    // Add Google One Tap script to wp_head
    add_action('wp_head', function() use ($client_id) {
        // Only show to non-logged in users
        if (is_user_logged_in()) {
            return;
        }
        ?>
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <div id="g_id_onload"
             data-client_id="<?php echo esc_attr($client_id); ?>"
             data-callback="handleCredentialResponse"
             data-auto_prompt="true">
        </div>
        <script>
            function handleCredentialResponse(response) {
                // Send the credential to your server
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const result = JSON.parse(xhr.responseText);
                        if (result.success) {
                            // Refresh the page or redirect after successful login
                            window.location.reload();
                        } else {
                            console.error('Login failed:', result.data);
                        }
                    }
                };
                xhr.send('action=google_one_tap_login&credential=' + response.credential);
            }
        </script>
        <?php
    });
    
    // Handle the AJAX request
    add_action('wp_ajax_nopriv_google_one_tap_login', 'handle_google_one_tap_login');
}
add_action('after_setup_theme', 'add_google_one_tap_signin');

// Handle Google One Tap Login
// Handle Google One Tap Login
function handle_google_one_tap_login() {
    if (!isset($_POST['credential'])) {
        wp_send_json_error('No credential received');
        return;
    }
    
    $jwt = $_POST['credential'];
    
    // Basic JWT decoding for demonstration
    // In production, you should verify the token properly using Google's libraries
    $jwt_parts = explode('.', $jwt);
    if (count($jwt_parts) !== 3) {
        wp_send_json_error('Invalid JWT format');
        return;
    }
    
    $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', $jwt_parts[1]))), true);
    
    if (!$payload || !isset($payload['email'])) {
        wp_send_json_error('Invalid payload');
        return;
    }
    
    // Extract user data from payload
    $email = sanitize_email($payload['email']);
    $name = isset($payload['name']) ? sanitize_text_field($payload['name']) : '';
    $given_name = isset($payload['given_name']) ? sanitize_text_field($payload['given_name']) : '';
    $family_name = isset($payload['family_name']) ? sanitize_text_field($payload['family_name']) : '';
    $picture = isset($payload['picture']) ? esc_url_raw($payload['picture']) : '';
    $user_id_from_google = isset($payload['sub']) ? sanitize_text_field($payload['sub']) : '';
    
    // Check if user exists
    $user = get_user_by('email', $email);
    
    if (!$user) {
        // Create new user
        $username = sanitize_user(explode('@', $email)[0], true);
        $username = username_exists($username) ? $username . rand(1, 999) : $username;
        
        $random_password = wp_generate_password(12, false);
        $user_id = wp_create_user($username, $random_password, $email);
        
        if (is_wp_error($user_id)) {
            wp_send_json_error('Could not create user: ' . $user_id->get_error_message());
            return;
        }
        
        $user = get_user_by('id', $user_id);
        
        // Set user role
        $user->set_role('subscriber');
        
        // Update user meta
        if ($given_name) update_user_meta($user_id, 'first_name', $given_name);
        if ($family_name) update_user_meta($user_id, 'last_name', $family_name);
        if ($name) update_user_meta($user_id, 'display_name', $name);
        if ($picture) update_user_meta($user_id, 'google_profile_picture', $picture);
        if ($user_id_from_google) update_user_meta($user_id, 'google_user_id', $user_id_from_google);
        
        // Optional: Notify admin about new user
        wp_new_user_notification($user_id, null, 'admin');
        
        // Add user to Brevo (optional)
        add_user_to_brevo($email, $given_name, $family_name, $name, $user->ID);



        // start by aditya
              //Add user to Rightee

              get_set_blog('outr_website_new_user',['web_user_id_c' => $user->ID],['module' => 'outr_website_new_user', 'web_user_id_c' => $user->ID, 'user_name_c' => $given_name, 'user_email_c' => $email, 'web_source_c' => 'Extractmails.com-sign-up'], 0);

              // end by aditya



        // Add user to SendGrid (optional)
        add_user_to_sendgrid($email, $given_name, $family_name, $name, $user->ID);
    }
    
    // Log the user in
    wp_set_auth_cookie($user->ID, true);
    wp_send_json_success(['message' => 'Login successful']);
    
    wp_die();
}
// Optional: Add profile picture from Google to WordPress user avatar
function google_user_profile_picture($avatar, $id_or_email, $size, $default, $alt) {
    $user = false;
    
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
        $user = get_user_by('id', $user_id);
    } elseif (is_object($id_or_email)) {
        if (!empty($id_or_email->user_id)) {
            $user = get_user_by('id', $id_or_email->user_id);
        }
    } else {
        $user = get_user_by('email', $id_or_email);
    }
    
    if ($user && is_object($user)) {
        $google_profile_picture = get_user_meta($user->ID, 'google_profile_picture', true);
        if ($google_profile_picture) {
            $avatar = "<img alt='{$alt}' src='{$google_profile_picture}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' loading='lazy' decoding='async'/>";
        }
    }
    
    return $avatar;
}
add_filter('get_avatar', 'google_user_profile_picture', 10, 5);

// add code main
?>
<?php
// Enhanced function to detect non-English content and prevent publishing
function prevent_non_english_publishing($post_id, $post, $update) {
    // Skip if this is an autosave or revision
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Only check posts (modify this array to include other post types if needed)
    if (!in_array($post->post_type, ['post'])) {
        return;
    }
    
    // Check if language detection is overridden for this post
    $override = get_post_meta($post_id, '_language_override', true);
    if ($override === '1') {
        return;
    }
    
    // Only act when post is being published or is already published
    if (!in_array($post->post_status, ['publish'])) {
        return;
    }
    
    // Get post content and title
    $content = $post->post_content;
    $title = $post->post_title;
    $excerpt = $post->post_excerpt;
    
    // Combine all text content
    $full_text = $title . ' ' . $content . ' ' . $excerpt;
    
    // Remove HTML tags and clean the text
    $clean_text = wp_strip_all_tags($full_text);
    $clean_text = preg_replace('/\s+/', ' ', $clean_text);
    $clean_text = trim($clean_text);
    
    // Skip if content is too short to analyze
    if (strlen($clean_text) < 10) {
        return;
    }
    
    // Check if content is non-English
    if (!is_english_content_enhanced($clean_text)) {
        // Remove the hook temporarily to avoid infinite loop
        remove_action('wp_insert_post', 'prevent_non_english_publishing', 10, 3);
        remove_action('save_post', 'prevent_non_english_publishing', 10, 3);
        
        // Update post status to draft
        wp_update_post([
            'ID' => $post_id,
            'post_status' => 'draft'
        ], false, false);
        
        // Add a meta field to track why it was moved to draft
        update_post_meta($post_id, '_moved_to_draft_reason', 'non_english_content');
        update_post_meta($post_id, '_moved_to_draft_time', current_time('mysql'));
        
        // Store admin notice in transient for next page load
        set_transient('non_english_post_notice_' . get_current_user_id(), [
            'post_title' => $post->post_title,
            'post_id' => $post_id
        ], 30);
        
        // Re-add the hooks
        add_action('wp_insert_post', 'prevent_non_english_publishing', 10, 3);
        add_action('save_post', 'prevent_non_english_publishing', 10, 3);
        
        // If this is an AJAX request, send response
        if (wp_doing_ajax()) {
            wp_die(json_encode([
                'success' => false,
                'message' => 'Post contains non-English content and has been moved to draft.'
            ]), 400);
        }
    }
}

// Enhanced function to detect if content is in English
function is_english_content_enhanced($text) {
    // Method 1: Check for non-Latin characters (improved detection)
    $non_latin_pattern = '/[^\p{Latin}\p{Common}\p{Inherited}]/u';
    if (preg_match_all($non_latin_pattern, $text, $matches)) {
        $non_latin_count = count($matches[0]);
        $total_chars = mb_strlen($text, 'UTF-8');
        
        // If more than 15% non-Latin characters, likely not English
        if ($non_latin_count > ($total_chars * 0.15)) {
            return false;
        }
    }
    
    // Method 2: Enhanced common English words list
    $common_english_words = [
        'the', 'be', 'to', 'of', 'and', 'a', 'in', 'that', 'have', 'i',
        'it', 'for', 'not', 'on', 'with', 'he', 'as', 'you', 'do', 'at',
        'this', 'but', 'his', 'by', 'from', 'they', 'we', 'say', 'her', 'she',
        'or', 'an', 'will', 'my', 'one', 'all', 'would', 'there', 'their',
        'what', 'so', 'up', 'out', 'if', 'about', 'who', 'get', 'which',
        'go', 'me', 'when', 'make', 'can', 'like', 'time', 'no', 'just',
        'him', 'know', 'take', 'people', 'into', 'year', 'your', 'good',
        'some', 'could', 'them', 'see', 'other', 'than', 'then', 'now',
        'look', 'only', 'come', 'its', 'over', 'think', 'also', 'back',
        'after', 'use', 'two', 'how', 'our', 'work', 'first', 'well',
        'way', 'even', 'new', 'want', 'because', 'any', 'these', 'give',
        'day', 'most', 'us', 'is', 'are', 'was', 'were', 'been', 'being',
        'has', 'had', 'does', 'did', 'doing', 'will', 'would', 'should',
        'could', 'may', 'might', 'must', 'shall', 'can', 'cannot', 'won\'t',
        'don\'t', 'doesn\'t', 'didn\'t', 'haven\'t', 'hasn\'t', 'hadn\'t',
        'wouldn\'t', 'shouldn\'t', 'couldn\'t', 'isn\'t', 'aren\'t', 'wasn\'t',
        'weren\'t', 'am', 'going', 'very', 'much', 'many', 'more', 'most',
        'here', 'where', 'why', 'how', 'when', 'what', 'who', 'which',
        'every', 'each', 'both', 'either', 'neither', 'all', 'any', 'some',
        'few', 'several', 'enough', 'too', 'quite', 'rather', 'pretty'
    ];
    
    // Method 3: Check for English-specific patterns
    $english_patterns = [
        '/\b(the|a|an)\s+\w+/',  // Articles followed by words
        '/\w+ing\b/',             // -ing endings
        '/\w+ed\b/',              // -ed endings
        '/\w+ly\b/',              // -ly endings
        '/\w+\'s\b/',             // Possessive 's
        '/\w+n\'t\b/',            // Contractions with n't
    ];
    
    $pattern_matches = 0;
    foreach ($english_patterns as $pattern) {
        if (preg_match_all($pattern, strtolower($text), $matches)) {
            $pattern_matches += count($matches[0]);
        }
    }
    
    // Convert to lowercase and split into words
    $words = preg_split('/\s+/', strtolower($text));
    $words = array_filter($words); // Remove empty elements
    $word_count = count($words);
    
    if ($word_count < 3) {
        return true; // Too short to analyze reliably
    }
    
    // Count how many common English words are found
    $english_word_count = 0;
    foreach ($words as $word) {
        $clean_word = preg_replace('/[^\w\']/', '', $word);
        if (in_array($clean_word, $common_english_words)) {
            $english_word_count++;
        }
    }
    
    // Calculate ratios
    $english_ratio = $english_word_count / $word_count;
    $pattern_ratio = $pattern_matches / $word_count;
    
    // More strict detection - require higher threshold
    return ($english_ratio >= 0.20) || ($pattern_ratio >= 0.15);
}

// Hook the function to post creation and updates
add_action('wp_insert_post', 'prevent_non_english_publishing', 10, 3);
add_action('save_post', 'prevent_non_english_publishing', 10, 3);

// Additional hook for the publish transition
add_action('transition_post_status', 'check_publish_transition', 10, 3);

function check_publish_transition($new_status, $old_status, $post) {
    if ($new_status === 'publish' && $old_status !== 'publish') {
        prevent_non_english_publishing($post->ID, $post, false);
    }
}

// Display admin notice when a post is moved to draft
add_action('admin_notices', 'show_non_english_notice');

function show_non_english_notice() {
    $notice = get_transient('non_english_post_notice_' . get_current_user_id());
    if ($notice) {
        delete_transient('non_english_post_notice_' . get_current_user_id());
        ?>
        <div class="notice notice-error is-dismissible">
            <p><strong>Warning:</strong> The post "<?php echo esc_html($notice['post_title']); ?>" was moved to draft because it contains non-English content. <a href="<?php echo get_edit_post_link($notice['post_id']); ?>">Edit Post</a></p>
        </div>
        <?php
    }
}

// Add meta box to override language detection
add_action('add_meta_boxes', 'add_language_override_meta_box');

function add_language_override_meta_box() {
    add_meta_box(
        'language_override',
        'Language Detection Override',
        'language_override_callback',
        'post',
        'side',
        'high'
    );
}

function language_override_callback($post) {
    wp_nonce_field('language_override_nonce', 'language_override_nonce');
    $override = get_post_meta($post->ID, '_language_override', true);
    $moved_reason = get_post_meta($post->ID, '_moved_to_draft_reason', true);
    $moved_time = get_post_meta($post->ID, '_moved_to_draft_time', true);
    
    ?>
    <p>
        <label for="language_override">
            <input type="checkbox" id="language_override" name="language_override" value="1" <?php checked($override, '1'); ?>>
            <strong>Skip language detection for this post</strong>
        </label>
    </p>
    
    <?php if ($moved_reason === 'non_english_content'): ?>
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 8px; margin-top: 10px; border-radius: 3px;">
            <p><strong>⚠️ This post was automatically moved to draft</strong></p>
            <p><small>Reason: Non-English content detected<br>
            Time: <?php echo esc_html($moved_time); ?></small></p>
        </div>
    <?php endif; ?>
    
    <p><small><em>Check this box to allow publishing of non-English content for this specific post.</em></small></p>
    <?php
}

// Save the language override setting
add_action('save_post', 'save_language_override');

function save_language_override($post_id) {
    if (!isset($_POST['language_override_nonce']) || !wp_verify_nonce($_POST['language_override_nonce'], 'language_override_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $override = isset($_POST['language_override']) ? '1' : '0';
    update_post_meta($post_id, '_language_override', $override);
    
    // Clear the moved to draft reason if override is enabled
    if ($override === '1') {
        delete_post_meta($post_id, '_moved_to_draft_reason');
        delete_post_meta($post_id, '_moved_to_draft_time');
    }
}

// Add a column to the posts list to show language detection status
add_filter('manage_posts_columns', 'add_language_status_column');
add_action('manage_posts_custom_column', 'show_language_status_column', 10, 2);

function add_language_status_column($columns) {
    $columns['language_status'] = 'Language';
    return $columns;
}

function show_language_status_column($column, $post_id) {
    if ($column === 'language_status') {
        $override = get_post_meta($post_id, '_language_override', true);
        $moved_reason = get_post_meta($post_id, '_moved_to_draft_reason', true);
        
        if ($override === '1') {
            echo '<span style="color: #0073aa;">Override</span>';
        } elseif ($moved_reason === 'non_english_content') {
            echo '<span style="color: #d63638;">Non-English</span>';
        } else {
            echo '<span style="color: #00a32a;">English</span>';
        }
    }
}

// Optional: Add settings page for configuration
add_action('admin_menu', 'add_language_detection_settings');

function add_language_detection_settings() {
    add_options_page(
        'Language Detection Settings',
        'Language Detection',
        'manage_options',
        'language-detection',
        'language_detection_settings_page'
    );
}

function language_detection_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('lang_detect_post_types', $_POST['post_types'] ?? ['post']);
        update_option('lang_detect_threshold', floatval($_POST['threshold'] ?? 0.20));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $post_types = get_option('lang_detect_post_types', ['post']);
    $threshold = get_option('lang_detect_threshold', 0.20);
    ?>
    <div class="wrap">
        <h1>Language Detection Settings</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row">Post Types to Check</th>
                    <td>
                        <?php foreach (get_post_types(['public' => true], 'objects') as $post_type): ?>
                            <label>
                                <input type="checkbox" name="post_types[]" value="<?php echo esc_attr($post_type->name); ?>" 
                                       <?php checked(in_array($post_type->name, $post_types)); ?>>
                                <?php echo esc_html($post_type->label); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">English Word Threshold</th>
                    <td>
                        <input type="number" name="threshold" value="<?php echo esc_attr($threshold); ?>" 
                               step="0.01" min="0.1" max="0.5">
                        <p class="description">Minimum ratio of English words required (0.1 to 0.5)</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}










// by kajal for user manager


add_action('admin_menu', 'custom_manage_all_roles_menu');

function custom_manage_all_roles_menu() {
    add_menu_page(
        'Manage Users by Role',
        'User Manager',
        'manage_options',
        'manage-users-by-role',
        'custom_manage_users_by_role_page',
        'dashicons-groups',
        25
    );
}
function custom_manage_users_by_role_page() {
    if (!current_user_can('manage_options')) return;

    // Handle bulk deletion
    if (isset($_POST['bulk_delete']) && isset($_POST['user_ids']) && is_array($_POST['user_ids'])) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        foreach ($_POST['user_ids'] as $user_id) {
            $user = get_userdata($user_id);
            if ($user) {
                wp_delete_user($user_id);
            }
        }
        echo '<div class="updated"><p>Selected users deleted successfully.</p></div>';
    }

    // Handle single deletion
    if (isset($_GET['delete_user'])) {
        $user_id = intval($_GET['delete_user']);
        $user = get_userdata($user_id);
        if ($user) {
            require_once(ABSPATH . 'wp-admin/includes/user.php');
            wp_delete_user($user_id);
            echo '<div class="updated"><p>User deleted successfully.</p></div>';
        }
    }

    // Selected role
    $selected_role = isset($_GET['role']) ? sanitize_text_field($_GET['role']) : 'administrator';

    // Roles list
    global $wp_roles;
    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);

    echo '<div class="wrap"><h1>Manage Users by Role</h1>';

    // Role selector
    echo '<form method="get" style="margin-bottom:20px;">';
    echo '<input type="hidden" name="page" value="manage-users-by-role">';
    echo '<label for="role"><strong>Select Role: </strong></label>';
    echo '<select name="role" id="role" onchange="this.form.submit();">';
    foreach ($editable_roles as $role_slug => $role_details) {
        $selected = ($role_slug === $selected_role) ? 'selected' : '';
        echo '<option value="' . esc_attr($role_slug) . '" ' . $selected . '>' . esc_html($role_details['name']) . '</option>';
    }
    echo '</select>';
    echo '</form>';

    // Fetch users of selected role
    $users = get_users(array('role' => $selected_role));

    // Show total
    echo '<p style="font-size: 16px; margin: 10px 0; text-align: right;"><strong>Total ' . ucfirst($selected_role) . 's:</strong> ' . count($users) . '</p>';

    // Bulk delete form
    echo '<form method="post">';
    echo '<input type="hidden" name="role" value="' . esc_attr($selected_role) . '">';
    echo '<table class="wp-list-table widefat fixed striped users">';
    echo '<thead>
        <tr>
            <th style="width: 30px;"><input type="checkbox" id="select_all"></th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead><tbody>';

    if (!empty($users)) {
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td><input type="checkbox" name="user_ids[]" value="' . esc_attr($user->ID) . '"></td>';
            echo '<td>' . esc_html($user->ID) . '</td>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td>' . esc_html($user->user_email) . '</td>';
            echo '<td>
                <a href="' . esc_url(get_edit_user_link($user->ID)) . '" class="button">Edit</a>
                <a href="' . esc_url(admin_url('admin.php?page=manage-users-by-role&role=' . $selected_role . '&delete_user=' . $user->ID)) . '" class="button" onclick="return confirm(\'Are you sure?\')">Delete</a>
            </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">No users found for this role.</td></tr>';
    }

    echo '</tbody></table>';

    // Bulk delete button
    if (!empty($users)) {
        echo '<p><input type="submit" name="bulk_delete" class="button button-primary" value="Delete Selected" onclick="return confirm(\'Are you sure you want to delete the selected users?\');"></p>';
    }

    echo '</form></div>';

    // JS to handle Select All
    echo '<script>
    document.getElementById("select_all").addEventListener("click", function() {
        const checkboxes = document.querySelectorAll(\'input[name="user_ids[]"]\');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
    </script>';
}














// by kjl
function get_relevant_youtube_video_data($post_id) {
    $meta_key = '_matched_youtube_video';

    // 1. Cache check
    $cached = get_post_meta($post_id, $meta_key, true);
    if (!empty($cached) && is_array($cached)) {
        $cached['source'] = 'cache';
        return $cached;
    }

    // API credentials
    $chatApiKey = '5f155d5a75f54c17bfa9b947a8e000df';
    $chatApiUrl = 'https://outright-openai-1.openai.azure.com/openai/deployments/chatgpt-40/chat/completions?api-version=2024-02-15-preview';
    $youtubeApiKey = 'AIzaSyC8UW0G0hRXP3QvYathzVS4LcJeOAFiCAQ';
    $playlist_id = 'UUb8i7ywCZeclmuArIvHv-zA';

    // 2. Blog info
    $post = get_post($post_id);
    if (!$post) return null;

    $blog_title = strtolower(strip_tags($post->post_title));
    $blog_slug = strtolower(basename(get_permalink($post_id)));
    $blog_keywords = strtolower(get_post_meta($post_id, 'meta_keywords', true));
    if (empty($blog_keywords)) {
        $blog_keywords = implode(', ', wp_list_pluck(get_the_tags($post_id) ?: [], 'name'));
    }

    // 3. Get all videos (cached 1hr)
    $videoList = get_transient('outright_youtube_all_videos');
    if (!$videoList) {
        $videoList = [];
        $pageToken = '';
        do {
            $api_url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId={$playlist_id}&key={$youtubeApiKey}&pageToken={$pageToken}";
            $response = wp_remote_get($api_url);
            if (is_wp_error($response)) break;
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (empty($data['items'])) break;

            foreach ($data['items'] as $item) {
                $videoList[] = [
                    'id' => $item['snippet']['resourceId']['videoId'],
                    'title' => $item['snippet']['title'],
                    'description' => $item['snippet']['description'],
                ];
            }

            $pageToken = $data['nextPageToken'] ?? '';
        } while (!empty($pageToken));

        set_transient('outright_youtube_all_videos', $videoList, HOUR_IN_SECONDS);
    }

    // 4. Improved Title Word Match
    $matched_video = null;
    $blog_words = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/i', '', $blog_title));
    $blog_words = array_filter(array_map('strtolower', $blog_words));

    foreach ($videoList as $video) {
        $title = strtolower($video['title']);
        $video_words = preg_split('/\s+/', preg_replace('/[^a-z0-9 ]/i', '', $title));
        $video_words = array_filter(array_map('strtolower', $video_words));

        $matches = array_intersect($blog_words, $video_words);
        if (count($matches) >= 2) {
            $matched_video = $video;
            $matched_video['source'] = 'keyword_title_match';
            error_log("✅ Matched by keywords: " . implode(', ', $matches));
            break;
        }
    }

    // 5. ChatGPT if no match
    if (!$matched_video) {
        $video_summary = "";
        foreach ($videoList as $v) {
            $video_summary .= "- [{$v['id']}] {$v['title']}: {$v['description']}\n";
        }

        $prompt = <<<EOT
You are an assistant that matches blog posts to the most relevant YouTube video using this logic:

Step 1: Match blog title with video title  
Step 2: Match blog title with video description  
Step 3: Match blog keywords or slug with video title or description  
Step 4: If nothing matches, return "none"

Blog Title: "{$blog_title}"  
Blog Slug: "{$blog_slug}"  
Blog Keywords: "{$blog_keywords}"  

Videos:
{$video_summary}
EOT;

        $chat_response = wp_remote_post($chatApiUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'api-key' => $chatApiKey,
            ],
            'body' => json_encode([
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that finds related YouTube videos for blog posts.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2,
                'max_tokens' => 20,
            ])
        ]);

        if (!is_wp_error($chat_response)) {
            $json = json_decode(wp_remote_retrieve_body($chat_response), true);
            $reply = trim($json['choices'][0]['message']['content'] ?? '');
            error_log("🤖 ChatGPT Suggested ID: $reply");

            foreach ($videoList as $v) {
                if ($v['id'] === $reply) {
                    $matched_video = $v;
                    $matched_video['source'] = 'chatgpt';
                    break;
                }
            }
        }
    }

    // 6. Fallback
    if (!$matched_video) {
        $fallback_videos = [
            '9dmKFFCpjdA',
            'yOltLvk6kco',
            'LM0NQcdZUjg',
            'OTdUKSo-XhI',
            'oEhkwHfDRPI',
        ];
        $index = get_post_meta($post_id, '_fallback_video_index', true);
        if ($index === '') {
            $index = mt_rand(0, count($fallback_videos) - 1);
            update_post_meta($post_id, '_fallback_video_index', $index);
        }

        $fallback_id = $fallback_videos[$index];
        $matched_video = [
            'id' => $fallback_id,
            'title' => 'Sponsored Video',
            'source' => 'fallback',
        ];
    }

    // 7. Stats
    $stats_url = "https://www.googleapis.com/youtube/v3/videos?part=statistics&id={$matched_video['id']}&key={$youtubeApiKey}";
    $stats_response = wp_remote_get($stats_url);
    $stats_data = json_decode(wp_remote_retrieve_body($stats_response), true);
    $stats = $stats_data['items'][0]['statistics'] ?? [];

    $final_data = [
        'id' => $matched_video['id'],
        'title' => $matched_video['title'],
        'views' => $stats['viewCount'] ?? 0,
        'likes' => $stats['likeCount'] ?? 0,
        'comments' => $stats['commentCount'] ?? 0,
        'source' => $matched_video['source'],
    ];

    update_post_meta($post_id, $meta_key, $final_data);
    return $final_data;
}








// by kjl for latest vdo show on blog post page 



function get_latest_youtube_video_id($post_id) {
    $meta_key_video = '_latest_youtube_video_id';
    $meta_key_time  = '_latest_youtube_video_time';

    $last_update = get_post_meta($post_id, $meta_key_time, true);
    $video_id    = get_post_meta($post_id, $meta_key_video, true);

    $update_needed = true;

    if ($last_update) {
        $elapsed = time() - intval($last_update);
        if ($elapsed < 24 * 3600 && $video_id) { // less than 24 hours, use cached
            $update_needed = false;
        }
    }

    if ($update_needed) {
        $youtubeApiKey = 'AIzaSyC8UW0G0hRXP3QvYathzVS4LcJeOAFiCAQ';
        $playlist_id = 'UUb8i7ywCZeclmuArIvHv-zA';

        $api_url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=1&playlistId='
                   . $playlist_id . '&key=' . $youtubeApiKey;

        $response = wp_remote_get($api_url);

        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            if (isset($data['items'][0]['snippet']['resourceId']['videoId'])) {
                $video_id = $data['items'][0]['snippet']['resourceId']['videoId'];

                // update post meta
                update_post_meta($post_id, $meta_key_video, $video_id);
                update_post_meta($post_id, $meta_key_time, time());
            }
        }
    }

    return $video_id ?: 'BlncQHArvrk'; // fallback video if API fails
}



// end by kjl

// end latest vdo code

// start by aditya


// SHORTCODE FOR SUMMARY LIST
function sixty_sec_summary_shortcode($atts, $content = null) {
 
    // Clean bullet content (remove • and spaces)
    $cleaned = str_replace("•", "", $content);
 
    // Split into lines
    $lines = array_filter(array_map('trim', explode("\n", strip_tags($cleaned))));
 
    $output = '
<div class="sixty-sec-box" style="border:1px solid red;">
<h3 class="sixty-sec-title">⏱️ 60-Second Summary</h3>
<div class="sixty-sec-content">';
 
    foreach ($lines as $line) {
        if (!empty($line)) {
 
            // Generate ID from text
            $id = sanitize_title($line);
 
            // Create clickable link
            $output .= '<p><a href="#'.$id.'" class="sixty-link">'.$line.'</a></p>';
        }
    }
 
    $output .= '</div></div>';
 
    return $output;
}
add_shortcode('sixty-summary', 'sixty_sec_summary_shortcode');
 
 
// AUTO-ADD IDs TO HEADINGS (H2/H3/H4)
function sixty_add_anchor_ids($content) {
 
    return preg_replace_callback('/<h([2-4])[^>]*>(.*?)<\/h[2-4]>/', function($matches) {
 
        $tag  = $matches[1];
        $text = wp_strip_all_tags($matches[2]);
        $id   = sanitize_title($text);
 
        return '<h'.$tag.' id="'.$id.'">'.$matches[2].'</h'.$tag.'>';
 
    }, $content);
}
add_filter('the_content', 'sixty_add_anchor_ids');


// end by aditya


// start redirect blog by  nk
add_action('template_redirect', function () {

    $log_file = WP_CONTENT_DIR . '/redirects.log';

    if (!file_exists($log_file)) {
        return;
    }

    $current_url = home_url($_SERVER['REQUEST_URI']);

    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        list($old, $new) = array_map('trim', explode('|', $line));

        if (rtrim($old, '/') === rtrim($current_url, '/')) {
            wp_redirect($new, 301);
            exit;
        }
    }
});



function custom_301_redirect_old_blog_url() {

    $request_uri = $_SERVER['REQUEST_URI'];

    // Normalize (remove trailing slash issues)
    $request_uri = rtrim($request_uri, '/');

    if ($request_uri === '/blog/how-to-delete-bulk-emails-in-gmail') {
        wp_redirect('https://www.extractmails.com/blog/how-to-mass-delete-emails-on-gmail/', 301);
        exit;
    }
}
add_action('template_redirect', 'custom_301_redirect_old_blog_url');
?>
