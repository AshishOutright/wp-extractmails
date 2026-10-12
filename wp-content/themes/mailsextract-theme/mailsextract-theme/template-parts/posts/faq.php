<?php
/** Post FAQ section and its matching structured data. */

$post_id = get_the_ID();
$faqs    = mailsextract_get_post_faqs( $post_id );

if ( ! $faqs ) {
	return;
}

$title = get_post_meta( $post_id, '_mailsextract_post_faq_title', true );
$intro = get_post_meta( $post_id, '_mailsextract_post_faq_intro', true );
$title = $title ? $title : 'Frequently Asked Questions';

$schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array_map(
		static function ( $faq ) {
			return array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $faq['question'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $faq['answer'] ),
				),
			);
		},
		$faqs
	),
);
?>

<section class="post-faq" aria-labelledby="post-faq-title-<?php echo esc_attr( $post_id ); ?>">
	<div class="post-faq-header">
		<h2 id="post-faq-title-<?php echo esc_attr( $post_id ); ?>"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $intro ) : ?>
			<p><?php echo nl2br( esc_html( $intro ) ); ?></p>
		<?php endif; ?>
	</div>
	<div class="post-faq-list">
		<?php foreach ( $faqs as $index => $faq ) : ?>
			<?php $answer_id = 'post-faq-answer-' . $post_id . '-' . $index; ?>
			<div class="post-faq-item">
				<button class="post-faq-question" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $answer_id ); ?>">
					<span><?php echo esc_html( $faq['question'] ); ?></span>
					<span class="post-faq-plus" aria-hidden="true">+</span>
				</button>
				<div class="post-faq-answer" id="<?php echo esc_attr( $answer_id ); ?>">
					<div><?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<script type="application/ld+json">
	<?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
