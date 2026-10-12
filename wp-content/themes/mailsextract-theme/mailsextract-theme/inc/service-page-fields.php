<?php
/**
 * Editable fields for the reusable Service Page template.
 *
 * @package MailsExtract
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions used by the metabox and save handler.
 *
 * @return array<string,string>
 */
function mailsextract_service_fields() {
	return array(
		'hero_badge'       => 'Hero badge',
		'hero_title'       => 'Hero title',
		'hero_description' => 'Hero description',
		'hero_image'       => 'Hero image URL',
		'cta_text'         => 'Button text',
		'cta_url'          => 'Button URL',
		'parser_badge'     => 'Parser badge',
		'parser_title'     => 'Parser section title',
		'parser_text'      => 'Parser section description',
		'parser_text_2'    => 'Parser second paragraph',
		'parser_left_image'=> 'Parser left image URL',
		'parser_image'     => 'Parser main right image URL',
		'why_businesses_badge' => 'Section badge',
		'why_businesses_title' => 'Section title',
		'why_businesses_text'  => 'Section description',
		'why_businesses_1_title' => 'Item 1 title',
		'why_businesses_1_text'  => 'Item 1 description',
		'why_businesses_1_image' => 'Item 1 icon URL',
		'why_businesses_2_title' => 'Item 2 title',
		'why_businesses_2_text'  => 'Item 2 description',
		'why_businesses_2_image' => 'Item 2 icon URL',
		'why_businesses_3_title' => 'Item 3 title',
		'why_businesses_3_text'  => 'Item 3 description',
		'why_businesses_3_image' => 'Item 3 icon URL',
		'why_businesses_4_title' => 'Item 4 title',
		'why_businesses_4_text'  => 'Item 4 description',
		'why_businesses_4_image' => 'Item 4 icon URL',
		'why_businesses_5_title' => 'Item 5 title',
		'why_businesses_5_text'  => 'Item 5 description',
		'why_businesses_5_image' => 'Item 5 icon URL',
		'why_businesses_6_title' => 'Item 6 title',
		'why_businesses_6_text'  => 'Item 6 description',
		'why_businesses_6_image' => 'Item 6 icon URL',
		'why_businesses_7_title' => 'Item 7 title',
		'why_businesses_7_text'  => 'Item 7 description',
		'why_businesses_7_image' => 'Item 7 icon URL',
		'how_extracts_badge'   => 'Section badge',
		'how_extracts_title'   => 'Section title',
		'how_extracts_text'    => 'Section description',
		'how_extracts_1_title' => 'Card 1 title',
		'how_extracts_1_text'  => 'Card 1 description',
		'how_extracts_1_image' => 'Card 1 icon URL',
		'how_extracts_2_title' => 'Card 2 title',
		'how_extracts_2_text'  => 'Card 2 description',
		'how_extracts_2_image' => 'Card 2 icon URL',
		'how_extracts_3_title' => 'Card 3 title',
		'how_extracts_3_text'  => 'Card 3 description',
		'how_extracts_3_image' => 'Card 3 icon URL',
		'how_extracts_4_title' => 'Card 4 title',
		'how_extracts_4_text'  => 'Card 4 description',
		'how_extracts_4_image' => 'Card 4 icon URL',
		'all_steps_badge'   => 'Section badge',
		'all_steps_title'   => 'Section title',
		'all_steps_text'    => 'Section introduction',
		'all_steps_1_title' => 'Step 1 title',
		'all_steps_1_text'  => 'Step 1 description',
		'all_steps_1_image' => 'Step 1 image URL',
		'all_steps_2_title' => 'Step 2 title',
		'all_steps_2_text'  => 'Step 2 description',
		'all_steps_2_image' => 'Step 2 image URL',
		'all_steps_3_title' => 'Step 3 title',
		'all_steps_3_text'  => 'Step 3 description',
		'all_steps_3_image' => 'Step 3 image URL',
		'all_steps_4_title' => 'Step 4 title',
		'all_steps_4_text'  => 'Step 4 description',
		'all_steps_4_image' => 'Step 4 image URL',
		'why_badge'        => 'Business section badge',
		'why_title'        => 'Business section title',
		'why_text'         => 'Business section description',
		'why_image'        => 'Business section image URL',
		'benefit_1'        => 'Benefit 1',
		'benefit_2'        => 'Benefit 2',
		'benefit_3'        => 'Benefit 3',
		'benefit_4'        => 'Benefit 4',
		'faq_title'        => 'FAQ section title',
		'faq_intro'        => 'FAQ introduction',
		'faq_1_question'   => 'FAQ 1 question',
		'faq_1_answer'     => 'FAQ 1 answer',
		'faq_2_question'   => 'FAQ 2 question',
		'faq_2_answer'     => 'FAQ 2 answer',
		'faq_3_question'   => 'FAQ 3 question',
		'faq_3_answer'     => 'FAQ 3 answer',
		'faq_4_question'   => 'FAQ 4 question',
		'faq_4_answer'     => 'FAQ 4 answer',
		'faq_5_question'   => 'FAQ 5 question',
		'faq_5_answer'     => 'FAQ 5 answer',
		'faq_6_question'   => 'FAQ 6 question',
		'faq_6_answer'     => 'FAQ 6 answer',
		'faq_7_question'   => 'FAQ 7 question',
		'faq_7_answer'     => 'FAQ 7 answer',
		'faq_8_question'   => 'FAQ 8 question',
		'faq_8_answer'     => 'FAQ 8 answer',
		'faq_9_question'   => 'FAQ 9 question',
		'faq_9_answer'     => 'FAQ 9 answer',
		'faq_10_question'  => 'FAQ 10 question',
		'faq_10_answer'    => 'FAQ 10 answer',
	);
}

function mailsextract_add_service_metabox() {
	add_meta_box( 'mailsextract-service-fields', 'Service Page Content', 'mailsextract_render_service_metabox', 'page', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mailsextract_add_service_metabox' );

/**
 * Keep the service builder in Gutenberg's supported main metabox area.
 *
 * A legacy drag operation can save this box in the sidebar, where Gutenberg
 * may render an empty Meta Boxes panel instead of the fields.
 *
 * @param mixed $order Saved page metabox order.
 * @return array
 */
function mailsextract_normalize_service_metabox_order( $order ) {
	$order = is_array( $order ) ? $order : array();

	foreach ( array( 'normal', 'advanced', 'side' ) as $context ) {
		$boxes = isset( $order[ $context ] ) ? array_filter( explode( ',', $order[ $context ] ) ) : array();
		$boxes = array_diff( $boxes, array( 'mailsextract-service-fields' ) );
		if ( 'normal' === $context ) {
			array_unshift( $boxes, 'mailsextract-service-fields' );
		}
		$order[ $context ] = implode( ',', array_unique( $boxes ) );
	}

	return $order;
}
add_filter( 'get_user_option_meta-box-order_page', 'mailsextract_normalize_service_metabox_order' );

function mailsextract_render_service_metabox( $post ) {
	wp_nonce_field( 'mailsextract_save_service_fields', 'mailsextract_service_nonce' );
	$fields = mailsextract_service_fields();
	$groups = array(
		'hero'     => array( 'title' => '01 - Hero section', 'description' => 'Main heading, introduction, artwork and call-to-action.', 'fields' => array( 'hero_badge', 'hero_title', 'hero_description', 'hero_image', 'cta_text', 'cta_url' ) ),
		'parser'   => array( 'title' => '02 - After hero section', 'description' => 'Introduction content, smaller left image and main right image.', 'fields' => array( 'parser_badge', 'parser_title', 'parser_text', 'parser_text_2', 'parser_left_image', 'parser_image' ) ),
		'why_businesses' => array( 'title' => '03 - Why_Businesses', 'description' => 'Carousel heading, content and seven editable icons.', 'fields' => array( 'why_businesses_badge', 'why_businesses_title', 'why_businesses_text', 'why_businesses_1_title', 'why_businesses_1_text', 'why_businesses_1_image', 'why_businesses_2_title', 'why_businesses_2_text', 'why_businesses_2_image', 'why_businesses_3_title', 'why_businesses_3_text', 'why_businesses_3_image', 'why_businesses_4_title', 'why_businesses_4_text', 'why_businesses_4_image', 'why_businesses_5_title', 'why_businesses_5_text', 'why_businesses_5_image', 'why_businesses_6_title', 'why_businesses_6_text', 'why_businesses_6_image', 'why_businesses_7_title', 'why_businesses_7_text', 'why_businesses_7_image' ) ),
		'how_extracts' => array( 'title' => '04 - How_It_Extracts', 'description' => 'Section content and four editable icon cards.', 'fields' => array( 'how_extracts_badge', 'how_extracts_title', 'how_extracts_text', 'how_extracts_1_title', 'how_extracts_1_text', 'how_extracts_1_image', 'how_extracts_2_title', 'how_extracts_2_text', 'how_extracts_2_image', 'how_extracts_3_title', 'how_extracts_3_text', 'how_extracts_3_image', 'how_extracts_4_title', 'how_extracts_4_text', 'how_extracts_4_image' ) ),
		'all_steps' => array( 'title' => '05 - All_The_Steps', 'description' => 'Section content and four alternating step images.', 'fields' => array( 'all_steps_badge', 'all_steps_title', 'all_steps_text', 'all_steps_1_title', 'all_steps_1_text', 'all_steps_1_image', 'all_steps_2_title', 'all_steps_2_text', 'all_steps_2_image', 'all_steps_3_title', 'all_steps_3_text', 'all_steps_3_image', 'all_steps_4_title', 'all_steps_4_text', 'all_steps_4_image' ) ),
		'business' => array( 'title' => '06 - Why businesses choose it', 'description' => 'Business value, supporting artwork and trust message.', 'fields' => array( 'why_badge', 'why_title', 'why_text', 'why_image' ) ),
		'benefits' => array( 'title' => '07 - Business benefits', 'description' => 'Four concise benefits displayed in the business section.', 'fields' => array( 'benefit_1', 'benefit_2', 'benefit_3', 'benefit_4' ) ),
		'faq'      => array( 'title' => '08 - FAQs', 'description' => 'Frequently asked questions shown as an accordion. Add up to ten.', 'fields' => array( 'faq_title', 'faq_intro', 'faq_1_question', 'faq_1_answer', 'faq_2_question', 'faq_2_answer', 'faq_3_question', 'faq_3_answer', 'faq_4_question', 'faq_4_answer', 'faq_5_question', 'faq_5_answer', 'faq_6_question', 'faq_6_answer', 'faq_7_question', 'faq_7_answer', 'faq_8_question', 'faq_8_answer', 'faq_9_question', 'faq_9_answer', 'faq_10_question', 'faq_10_answer' ) ),
	);

	echo '<div class="mx-admin-builder"><p class="mx-admin-intro">Select <strong>Service Page</strong> as the template, then complete each section below. Changes appear on the public page after you update it.</p>';

	foreach ( $groups as $group_index => $group ) {
		$is_open = 'hero' === $group_index;
		echo '<section class="mx-admin-section' . ( $is_open ? ' is-open' : '' ) . '">';
		echo '<button type="button" class="mx-admin-section-toggle" aria-expanded="' . ( $is_open ? 'true' : 'false' ) . '"><span><strong>' . esc_html( $group['title'] ) . '</strong><small>' . esc_html( $group['description'] ) . '</small></span><b aria-hidden="true">+</b></button>';
		echo '<div class="mx-admin-section-content"><div class="mx-admin-grid">';

		foreach ( $group['fields'] as $key ) {
			$label       = $fields[ $key ];
			$value       = get_post_meta( $post->ID, '_mailsextract_' . $key, true );
			$is_textarea = in_array( $key, array( 'hero_description', 'parser_text', 'parser_text_2', 'why_businesses_text', 'why_businesses_1_text', 'why_businesses_2_text', 'why_businesses_3_text', 'why_businesses_4_text', 'why_businesses_5_text', 'why_businesses_6_text', 'why_businesses_7_text', 'how_extracts_text', 'how_extracts_1_text', 'how_extracts_2_text', 'how_extracts_3_text', 'how_extracts_4_text', 'all_steps_text', 'all_steps_1_text', 'all_steps_2_text', 'all_steps_3_text', 'all_steps_4_text', 'why_text', 'faq_intro', 'faq_1_answer', 'faq_2_answer', 'faq_3_answer', 'faq_4_answer', 'faq_5_answer', 'faq_6_answer', 'faq_7_answer', 'faq_8_answer', 'faq_9_answer', 'faq_10_answer' ), true );
			$is_image    = false !== strpos( $key, 'image' );
			$wide_class  = $is_textarea || $is_image ? ' mx-admin-field-wide' : '';
			echo '<div class="mx-admin-field' . esc_attr( $wide_class ) . '"><label for="mailsextract_' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label>';

			if ( $is_image ) {
				echo '<div class="mx-admin-image-control"><div class="mx-admin-image-preview">';
				if ( $value ) {
					echo '<img src="' . esc_url( $value ) . '" alt="">';
				} else {
					echo '<span>No image<br>selected</span>';
				}
				echo '</div><div><input type="hidden" id="mailsextract_' . esc_attr( $key ) . '" name="mailsextract_service[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
				echo '<button type="button" class="button button-primary mailsextract-select-image" data-target="mailsextract_' . esc_attr( $key ) . '">' . ( $value ? 'Replace image' : 'Choose image' ) . '</button> ';
				echo '<button type="button" class="button mailsextract-remove-image" data-target="mailsextract_' . esc_attr( $key ) . '">Remove</button></div></div>';
			} elseif ( $is_textarea ) {
				echo '<textarea class="widefat" rows="4" id="mailsextract_' . esc_attr( $key ) . '" name="mailsextract_service[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
			} else {
				echo '<input class="widefat" type="text" id="mailsextract_' . esc_attr( $key ) . '" name="mailsextract_service[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
			}
			echo '</div>';
		}

		echo '</div></div></section>';
	}

	echo '</div>';
}

function mailsextract_save_service_fields( $post_id ) {
	if ( ! isset( $_POST['mailsextract_service_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mailsextract_service_nonce'] ) ), 'mailsextract_save_service_fields' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) || ! isset( $_POST['mailsextract_service'] ) || ! is_array( $_POST['mailsextract_service'] ) ) {
		return;
	}

	$submitted = wp_unslash( $_POST['mailsextract_service'] );
	foreach ( mailsextract_service_fields() as $key => $label ) {
		$value = isset( $submitted[ $key ] ) ? $submitted[ $key ] : '';
		$value = false !== strpos( $key, 'url' ) || false !== strpos( $key, 'image' ) ? esc_url_raw( $value ) : wp_kses_post( $value );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_mailsextract_' . $key );
		} else {
			update_post_meta( $post_id, '_mailsextract_' . $key, $value );
		}
	}
}
add_action( 'save_post_page', 'mailsextract_save_service_fields' );

function mailsextract_service_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_register_style( 'mailsextract-service-admin', false, array(), '1.0' );
	wp_enqueue_style( 'mailsextract-service-admin' );
	wp_add_inline_style( 'mailsextract-service-admin', '.mx-admin-builder{background:#f2f6fa;margin:-6px -12px -12px;padding:18px}.mx-admin-intro{margin:0 0 16px;color:#44546a}.mx-admin-section{background:#fff;border:1px solid #dbe3eb;border-radius:12px;margin:0 0 14px;box-shadow:0 2px 6px rgba(24,45,70,.07);overflow:hidden}.mx-admin-section-toggle{width:100%;display:flex;align-items:center;justify-content:space-between;padding:17px 20px;border:0;background:#fff;cursor:pointer;text-align:left}.mx-admin-section-toggle small{display:block;margin-top:4px;color:#68788e;font-weight:400}.mx-admin-section-toggle b{font-size:20px;color:#1769e0;transition:transform .2s}.mx-admin-section.is-open .mx-admin-section-toggle b{transform:rotate(45deg)}.mx-admin-section-content{display:none;padding:4px 20px 22px;border-top:1px solid #edf1f5}.mx-admin-section.is-open .mx-admin-section-content{display:block}.mx-admin-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px 22px}.mx-admin-field{padding-top:15px}.mx-admin-field-wide{grid-column:1/-1}.mx-admin-field label{display:block;font-weight:600;margin-bottom:7px;color:#14213d}.mx-admin-field textarea,.mx-admin-field input[type=text]{border-color:#c9d5e2;border-radius:6px;padding:9px}.mx-admin-image-control{display:flex;align-items:center;gap:16px;min-height:130px;padding:14px;border:1px dashed #b8c9da;border-radius:9px;background:#f8fafc}.mx-admin-image-preview{width:130px;height:100px;display:grid;place-items:center;border:1px solid #d8e1ea;border-radius:7px;background:#fff;overflow:hidden;text-align:center;color:#78879b}.mx-admin-image-preview img{width:100%;height:100%;object-fit:contain}@media(max-width:782px){.mx-admin-grid{grid-template-columns:1fr}.mx-admin-field-wide{grid-column:auto}}' );
	wp_add_inline_script( 'media-editor', 'jQuery(function($){$(document).on("click",".mx-admin-section-toggle",function(){var section=$(this).closest(".mx-admin-section"),open=!section.hasClass("is-open");section.toggleClass("is-open",open);$(this).attr("aria-expanded",open?"true":"false");});$(document).on("click",".mailsextract-select-image",function(e){e.preventDefault();var button=$(this),target=button.data("target"),frame=wp.media({title:"Choose service image",button:{text:"Use this image"},multiple:false});frame.on("select",function(){var item=frame.state().get("selection").first().toJSON(),control=button.closest(".mx-admin-image-control");$("#"+target).val(item.url);control.find(".mx-admin-image-preview").html($("<img>",{src:item.url,alt:""}));button.text("Replace image");});frame.open();});$(document).on("click",".mailsextract-remove-image",function(){var target=$(this).data("target"),control=$(this).closest(".mx-admin-image-control");$("#"+target).val("");control.find(".mx-admin-image-preview").html("<span>No image<br>selected</span>");control.find(".mailsextract-select-image").text("Choose image");});});' );
}
add_action( 'admin_enqueue_scripts', 'mailsextract_service_admin_assets' );

/**
 * Get an editable service value with a template default.
 */
function mailsextract_service_value( $key, $default = '' ) {
	$value = get_post_meta( get_the_ID(), '_mailsextract_' . $key, true );
	return '' !== $value ? $value : $default;
}

/**
 * Allow safe inline formatting and links in editable service text.
 *
 * @param string $value       Editable field value.
 * @param bool   $line_breaks Convert new lines to <br> elements.
 * @return string
 */
function mailsextract_service_inline_html( $value, $line_breaks = false ) {
	$allowed = array(
		'a'      => array(
			'href'   => true,
			'target' => true,
			'rel'    => true,
			'title'  => true,
			'class'  => true,
		),
		'br'     => array(),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'span'   => array( 'class' => true ),
	);

	if ( $line_breaks ) {
		$value = nl2br( $value );
	}

	return wp_kses( $value, $allowed );
}

/**
 * Load service assets only on pages using the shared service template.
 */
function mailsextract_enqueue_service_assets() {
	if ( ! is_page_template( 'page-service.php' ) ) {
		return;
	}

	$css_file = MAILSEXTRACT_THEME_PATH . '/assets/css/service-page.css';
	$js_file  = MAILSEXTRACT_THEME_PATH . '/assets/js/service-page.js';

	wp_enqueue_style( 'mailsextract-service-page', get_template_directory_uri() . '/assets/css/service-page.css', array(), filemtime( $css_file ) );
	wp_enqueue_script( 'mailsextract-service-page', get_template_directory_uri() . '/assets/js/service-page.js', array(), filemtime( $js_file ), true );
}
add_action( 'wp_enqueue_scripts', 'mailsextract_enqueue_service_assets' );
