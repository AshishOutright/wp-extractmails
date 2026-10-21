<?php
/**
 * Add this code snippet to your ExtractMails theme's functions.php
 */

/**
 * Render ExtractMails App Launch Floating Toast Popup in footer.
 */
function extractmails_render_app_popup() {
	// Exclude admin or API requests
	if ( is_admin() || ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) ) {
		return;
	}

	get_template_part( 'template-parts/components/extractmails-app-popup' );
}
add_action( 'wp_footer', 'extractmails_render_app_popup', 50 );
