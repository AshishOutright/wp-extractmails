<?php
/**
 * Default featured image support.
 *
 * @package MailsExtract
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the Media Library attachment ID used as the default image.
 *
 * The bundled image is registered in the Media Library automatically. The
 * filter allows a child theme or plugin to override its attachment ID.
 *
 * @return int
 */
function mailsextract_get_default_featured_image_id() {
	$attachment_id = (int) get_option( 'mailsextract_default_featured_image_id', 0 );

	return (int) apply_filters( 'mailsextract_default_featured_image_id', $attachment_id );
}

/**
 * Register the bundled default image in the Media Library when necessary.
 */
function mailsextract_register_default_featured_image() {
	if ( mailsextract_get_valid_default_featured_image_id() ) {
		return;
	}

	$source_file = MAILSEXTRACT_THEME_PATH . '/assets/images/default-featured-image.png';

	if ( ! file_exists( $source_file ) ) {
		return;
	}

	$upload = wp_upload_bits( 'mailsextract-default-featured-image.png', null, file_get_contents( $source_file ) );

	if ( ! empty( $upload['error'] ) ) {
		return;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => 'MailsExtract Default Featured Image',
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_option( 'mailsextract_default_featured_image_id', $attachment_id, false );
}
add_action( 'admin_init', 'mailsextract_register_default_featured_image' );

/**
 * Check whether the configured attachment exists and is an image.
 *
 * @return int Valid attachment ID, or zero when it is not valid.
 */
function mailsextract_get_valid_default_featured_image_id() {
	$attachment_id = mailsextract_get_default_featured_image_id();

	if ( $attachment_id < 1 || 'attachment' !== get_post_type( $attachment_id ) || ! wp_attachment_is_image( $attachment_id ) ) {
		return 0;
	}

	return $attachment_id;
}

/**
 * Assign the default featured image when a blog post is saved without one.
 *
 * @param int $post_id Post ID being saved.
 */
function mailsextract_set_default_featured_image( $post_id ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( 'post' !== get_post_type( $post_id ) || has_post_thumbnail( $post_id ) ) {
		return;
	}

	$attachment_id = mailsextract_get_valid_default_featured_image_id();

	if ( $attachment_id ) {
		set_post_thumbnail( $post_id, $attachment_id );
	}
}
add_action( 'save_post_post', 'mailsextract_set_default_featured_image' );

/**
 * Preselect the default image in Gutenberg for posts without a thumbnail.
 */
function mailsextract_preselect_default_featured_image() {
	$screen = get_current_screen();

	if ( ! $screen || 'post' !== $screen->post_type || ! $screen->is_block_editor() ) {
		return;
	}

	$attachment_id = mailsextract_get_valid_default_featured_image_id();

	if ( ! $attachment_id ) {
		return;
	}

	$script = sprintf(
		'wp.domReady(function(){var select=wp.data.select("core/editor"),dispatch=wp.data.dispatch("core/editor");if(!select.getEditedPostAttribute("featured_media")){dispatch.editPost({featured_media:%d});}});',
		$attachment_id
	);

	wp_add_inline_script( 'wp-edit-post', $script, 'after' );
}
add_action( 'enqueue_block_editor_assets', 'mailsextract_preselect_default_featured_image' );
