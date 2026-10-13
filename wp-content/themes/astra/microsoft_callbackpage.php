<?php
/*template name: microsoft-callback*/

// IMPORTANT: No output should be sent before redirects
// For debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
if (!session_id()) {
    session_start();
}

// Process before any output happens (before get_header())
$should_redirect = false;
$redirect_url = '';
$needs_loading_page = true;

// Check if we have Microsoft user data in the session OR URL parameter
$user_data = null;

// First check session
if (isset($_SESSION['microsoft_oauth_user_data'])) {
    $user_data = $_SESSION['microsoft_oauth_user_data'];
}
// Then check URL parameter
elseif (isset($_GET['user_data'])) {
    $encoded_data = $_GET['user_data'];
    $user_data = json_decode(base64_decode($encoded_data), true);
    
    // Store it in session for consistency
    $_SESSION['microsoft_oauth_user_data'] = $user_data;
}

// Redirect if no user data found
if (empty($user_data)) {
    $should_redirect = true;
    $redirect_url = home_url();
    $needs_loading_page = false;
} else {
    // Process the Microsoft login
    $user_email = isset($user_data['mail']) ? $user_data['mail'] : 
                 (isset($user_data['userPrincipalName']) ? $user_data['userPrincipalName'] : '');

    if (empty($user_email)) {
        // Can't continue without email
        $error_message = "Could not get email from Microsoft account.";
    } else {
        // Debug info (can be added to page later)
        $debug_info = "Processing login for email: " . esc_html($user_email);
        
        // Check if user exists
        $user = get_user_by('email', $user_email);

        if ($user) {
            // User exists, log them in
            $debug_info .= " - User exists with ID: " . esc_html($user->ID);
            
            // Store Microsoft user ID in user meta
            update_user_meta($user->ID, 'microsoft_user_id', $user_data['id']);
            
            // Set a flag in session to indicate Microsoft login is in process
            $_SESSION['microsoft_processing_login'] = true;
            
            // Clear auth cookies first
            wp_clear_auth_cookie();
            
            // Set auth cookies
            wp_set_auth_cookie($user->ID, true);
            wp_set_current_user($user->ID);
            
            // Clean up session
            unset($_SESSION['microsoft_oauth_user_data']);
            
            // Set redirect URL
            $should_redirect = true;
            $redirect_url = admin_url();
            $needs_loading_page = false;
        } else {
            // User doesn't exist, create a new account
            $debug_info .= " - Creating new user";
            
            $username = sanitize_user(strtolower(str_replace(' ', '_', $user_data['displayName'])), true);
            
            // Make sure username is unique
            $username_exists = username_exists($username);
            if ($username_exists) {
                $count = 1;
                $new_username = $username . $count;
                while (username_exists($new_username)) {
                    $count++;
                    $new_username = $username . $count;
                }
                $username = $new_username;
            }
            
            // Generate a random password
            $password = wp_generate_password(16, false);
            
            // Create the user
            $user_id = wp_create_user($username, $password, $user_email);
            
            if (!is_wp_error($user_id)) {
                $debug_info .= " - User created with ID: " . esc_html($user_id);
                
                // Add user meta data
                update_user_meta($user_id, 'microsoft_user_id', $user_data['id']);
                if (isset($user_data['displayName'])) {
                    $name_parts = explode(' ', $user_data['displayName']);
                    if (count($name_parts) > 1) {
                        $last_name = array_pop($name_parts);
                        $first_name = implode(' ', $name_parts);
                        update_user_meta($user_id, 'first_name', $first_name);
                        update_user_meta($user_id, 'last_name', $last_name);
                    } else {
                        update_user_meta($user_id, 'first_name', $user_data['displayName']);
                    }
                }
                
                // Set a flag in session to indicate Microsoft login is in process
                $_SESSION['microsoft_processing_login'] = true;
                
                // Clear auth cookies first
                wp_clear_auth_cookie();
                
                // Set auth cookies and current user
                wp_set_auth_cookie($user_id, true);
                wp_set_current_user($user_id);
                
                // Clean up session
                unset($_SESSION['microsoft_oauth_user_data']);
                
                // Set redirect URL
                $should_redirect = true;
                $redirect_url = admin_url();
                $needs_loading_page = false;
            } else {
                $error_message = "Error creating user: " . $user_id->get_error_message();
            }
        }
    }
}

// Perform any redirects before any HTML output
if ($should_redirect) {
    // If we have a redirect URL, go there
    wp_redirect($redirect_url);
    exit;
}

// Now it's safe to include the header
get_header();

// Display any error messages
if (isset($error_message)) {
    wp_die($error_message);
}

// If we get here, we need to show the loading page
if ($needs_loading_page):
?>
<div class="microsoft-login-process-container">
    <div class="content-area">
        <h1>Processing your Microsoft login...</h1>
        <p>Please wait while we complete your login. You will be redirected shortly.</p>
        
        <div class="loading-spinner">
            <div class="spinner"></div>
        </div>
        
        <!-- Force redirect after 5 seconds just in case -->
        <script>
        setTimeout(function() {
            window.location.href = '<?php echo esc_url(admin_url()); ?>';
        }, 5000);
        </script>
    </div>
</div>

<style>
    .microsoft-login-process-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 40px 20px;
        text-align: center;
    }
    .loading-spinner {
        margin: 40px auto;
    }
    .spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid #0078d4;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<?php
endif;

get_footer();
?>