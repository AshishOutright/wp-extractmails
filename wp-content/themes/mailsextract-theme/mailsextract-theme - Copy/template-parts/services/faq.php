<?php
/**
 * Service FAQ section.
 *
 * @package MailsExtract
 */

$faqs = array();
for ( $index = 1; $index <= 10; $index++ ) {
	$question = mailsextract_service_value( 'faq_' . $index . '_question' );
	$answer   = mailsextract_service_value( 'faq_' . $index . '_answer' );
	if ( $question && $answer ) {
		$faqs[] = array( 'question' => $question, 'answer' => $answer );
	}
}

if ( ! $faqs ) {
	return;
}

$faq_schema = array(
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

<section class="mails-faq-section">
	<div class="mails-faq-container">
		<div class="mails-faq-header reveal-up">
			<div class="mails-faq-icon"><h2 class="mails-faq-title"><?php echo esc_html( mailsextract_service_value( 'faq_title', 'Frequently Asked Questions (FAQs)' ) ); ?></h2></div>
			<p class="mails-faq-subtitle"><?php echo esc_html( mailsextract_service_value( 'faq_intro', 'Everything you need to know about this service.' ) ); ?></p>
		</div>
		<div class="mails-faq-list">
			<?php foreach ( $faqs as $index => $faq ) : ?>
				<div class="mails-faq-item<?php echo 0 === $index ? ' mails-active' : ''; ?> reveal-up">
					<button class="mails-faq-question" type="button" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>">
						<span><?php echo esc_html( $faq['question'] ); ?></span><span class="mails-faq-button" aria-hidden="true">+</span>
					</button>
					<div class="mails-faq-answer"><p><?php echo nl2br( esc_html( $faq['answer'] ) ); ?></p></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<script type="application/ld+json">
	<?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
