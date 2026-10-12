<?php
/**
 * FAQ fields and assets for blog posts.
 *
 * @package MailsExtract
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return complete FAQs saved against a post.
 *
 * @param int $post_id Post ID.
 * @return array<int,array<string,string>>
 */
function mailsextract_get_post_faqs( $post_id ) {
	$faqs = array();

	for ( $index = 1; $index <= 10; $index++ ) {
		$question = get_post_meta( $post_id, '_mailsextract_post_faq_' . $index . '_question', true );
		$answer   = get_post_meta( $post_id, '_mailsextract_post_faq_' . $index . '_answer', true );

		if ( '' !== trim( $question ) && '' !== trim( wp_strip_all_tags( $answer ) ) ) {
			$faqs[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}
	}

	return $faqs;
}

/** Register the FAQ editor panel on posts. */
function mailsextract_add_post_faq_metabox() {
	add_meta_box(
		'mailsextract-post-faqs',
		'Post FAQs and FAQ Schema',
		'mailsextract_render_post_faq_metabox',
		'post',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes_post', 'mailsextract_add_post_faq_metabox' );

/**
 * Render the post FAQ editor.
 *
 * @param WP_Post $post Current post.
 */
function mailsextract_render_post_faq_metabox( $post ) {
	wp_nonce_field( 'mailsextract_save_post_faqs', 'mailsextract_post_faq_nonce' );

	$title = get_post_meta( $post->ID, '_mailsextract_post_faq_title', true );
	$intro = get_post_meta( $post->ID, '_mailsextract_post_faq_intro', true );
	?>
	<p>Enter only FAQs that should be visible on this post. Complete pairs are displayed below the article and added to its FAQPage schema.</p>
	<p>
		<label for="mailsextract-post-faq-title"><strong>Section title</strong></label>
		<input class="widefat" id="mailsextract-post-faq-title" name="mailsextract_post_faq[title]" type="text" value="<?php echo esc_attr( $title ); ?>" placeholder="Frequently Asked Questions">
	</p>
	<p>
		<label for="mailsextract-post-faq-intro"><strong>Introduction (optional)</strong></label>
		<textarea class="widefat" id="mailsextract-post-faq-intro" name="mailsextract_post_faq[intro]" rows="2"><?php echo esc_textarea( $intro ); ?></textarea>
	</p>
	<hr>
	<?php for ( $index = 1; $index <= 10; $index++ ) : ?>
		<?php
		$question = get_post_meta( $post->ID, '_mailsextract_post_faq_' . $index . '_question', true );
		$answer   = get_post_meta( $post->ID, '_mailsextract_post_faq_' . $index . '_answer', true );
		?>
		<div style="padding:10px 0;border-bottom:1px solid #ddd">
			<p><strong>FAQ <?php echo esc_html( $index ); ?></strong></p>
			<p>
				<label for="mailsextract-post-faq-question-<?php echo esc_attr( $index ); ?>">Question</label>
				<input class="widefat" id="mailsextract-post-faq-question-<?php echo esc_attr( $index ); ?>" name="mailsextract_post_faq[<?php echo esc_attr( $index ); ?>][question]" type="text" value="<?php echo esc_attr( $question ); ?>">
			</p>
			<p>
				<label for="mailsextract-post-faq-answer-<?php echo esc_attr( $index ); ?>">Answer</label>
				<textarea class="widefat" id="mailsextract-post-faq-answer-<?php echo esc_attr( $index ); ?>" name="mailsextract_post_faq[<?php echo esc_attr( $index ); ?>][answer]" rows="4"><?php echo esc_textarea( $answer ); ?></textarea>
			</p>
		</div>
	<?php endfor; ?>
	<?php
}

/**
 * Save post FAQ fields.
 *
 * @param int $post_id Post ID.
 */
function mailsextract_save_post_faqs( $post_id ) {
	if ( ! isset( $_POST['mailsextract_post_faq_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mailsextract_post_faq_nonce'] ) ), 'mailsextract_save_post_faqs' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['mailsextract_post_faq'] ) || ! is_array( $_POST['mailsextract_post_faq'] ) ) {
		return;
	}

	$submitted = wp_unslash( $_POST['mailsextract_post_faq'] );
	$title     = isset( $submitted['title'] ) ? sanitize_text_field( $submitted['title'] ) : '';
	$intro     = isset( $submitted['intro'] ) ? sanitize_textarea_field( $submitted['intro'] ) : '';

	mailsextract_update_or_delete_post_meta( $post_id, '_mailsextract_post_faq_title', $title );
	mailsextract_update_or_delete_post_meta( $post_id, '_mailsextract_post_faq_intro', $intro );

	for ( $index = 1; $index <= 10; $index++ ) {
		$item     = isset( $submitted[ $index ] ) && is_array( $submitted[ $index ] ) ? $submitted[ $index ] : array();
		$question = isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '';
		$answer   = isset( $item['answer'] ) ? wp_kses_post( $item['answer'] ) : '';

		mailsextract_update_or_delete_post_meta( $post_id, '_mailsextract_post_faq_' . $index . '_question', $question );
		mailsextract_update_or_delete_post_meta( $post_id, '_mailsextract_post_faq_' . $index . '_answer', $answer );
	}
}
add_action( 'save_post_post', 'mailsextract_save_post_faqs' );

/** Update a meta value, or remove it when blank. */
function mailsextract_update_or_delete_post_meta( $post_id, $key, $value ) {
	if ( '' === $value ) {
		delete_post_meta( $post_id, $key );
	} else {
		update_post_meta( $post_id, $key, $value );
	}
}

/** Load FAQ assets only when the current post has FAQs. */
function mailsextract_post_faq_assets() {
	if ( ! is_singular( 'post' ) || ! mailsextract_get_post_faqs( get_queried_object_id() ) ) {
		return;
	}

	$css_path = MAILSEXTRACT_THEME_PATH . '/assets/css/post-faq.css';
	$js_path  = MAILSEXTRACT_THEME_PATH . '/assets/js/post-faq.js';
	wp_enqueue_style( 'mailsextract-post-faq', get_template_directory_uri() . '/assets/css/post-faq.css', array(), filemtime( $css_path ) );
	wp_enqueue_script( 'mailsextract-post-faq', get_template_directory_uri() . '/assets/js/post-faq.js', array(), filemtime( $js_path ), true );
}
add_action( 'wp_enqueue_scripts', 'mailsextract_post_faq_assets' );
