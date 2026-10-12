<?php
/**
 * All_The_Steps alternating process section.
 *
 * @package MailsExtract
 */

$defaults = array(
	array( 'AI Understands the Email', 'The artificial intelligence analyzes incoming emails and attachments while recognizing business context instead of relying only on fixed templates.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/Intersect.png' ),
	array( 'Intelligent Data Extraction', 'The platform extracts names, contact details, invoices, purchase orders, tracking numbers, and booking information automatically.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/Intersect-1.png' ),
	array( 'Smart Data Transformation', 'Users can apply custom field mapping, validation rules, Regex conditions, formatting, and standardize extracted data before delivery.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/Intersect-2.png' ),
	array( 'Instant Workflow Delivery', 'The platform pushes structured data into CRM platforms, ERP systems, spreadsheets, databases, APIs, and helpdesk software automatically.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/Intersect-3.png' ),
);
$steps = array();
foreach ( $defaults as $index => $default ) {
	$number  = $index + 1;
	$steps[] = array(
		'title' => mailsextract_service_value( 'all_steps_' . $number . '_title', $default[0] ),
		'text'  => mailsextract_service_value( 'all_steps_' . $number . '_text', $default[1] ),
		'image' => mailsextract_service_value( 'all_steps_' . $number . '_image', $default[2] ),
	);
}
?>

<section class="email-intelligence" aria-labelledby="email-intelligence-title">
	<div class="email-intelligence__container">
		<header class="email-intelligence__header">
			<div class="relative inline-block border-y-2 border-dashed border-black px-5 py-2">
				<span class="absolute -left-1.5 -top-1.5 h-3 w-3 bg-[#ff4b32]"></span><span class="absolute -right-1.5 -top-1.5 h-3 w-3 bg-[#ff4b32]"></span><span class="absolute -bottom-1.5 -left-1.5 h-3 w-3 bg-[#ff4b32]"></span><span class="absolute -bottom-1.5 -right-1.5 h-3 w-3 bg-[#ff4b32]"></span>
				<span class="absolute bottom-2 left-0 top-2 border-l-2 border-dashed border-black"></span><span class="absolute bottom-2 right-0 top-2 border-r-2 border-dashed border-black"></span>
				<span class="text-[19px] font-[500] leading-none text-[#ff4b32]"><?php echo esc_html( mailsextract_service_value( 'all_steps_badge', 'How Email Data Extraction Works' ) ); ?></span>
			</div>
			<h2 id="email-intelligence-title" class="email-intelligence__title reveal-up"><?php echo esc_html( mailsextract_service_value( 'all_steps_title', 'Intelligent Steps That Automate Every Incoming Email' ) ); ?></h2>
			<p class="email-intelligence__intro reveal-up"><?php echo esc_html( mailsextract_service_value( 'all_steps_text', 'Email Data Extraction by MailsExtract combines artificial intelligence with business rules and workflow automation to extract valuable information from every incoming message.' ) ); ?></p>
		</header>

		<div class="email-intelligence__list">
			<?php foreach ( $steps as $index => $step ) : ?>
				<article class="email-intelligence__row<?php echo 1 === $index % 2 ? ' email-intelligence__row--reverse' : ''; ?>">
					<?php if ( 0 === $index % 2 ) : ?>
						<div class="email-intelligence__number" aria-hidden="true"><img src="<?php echo esc_url( $step['image'] ); ?>" alt=""></div>
					<?php endif; ?>
					<div class="email-intelligence__content"><h3 class="email-intelligence__heading"><?php echo esc_html( $step['title'] ); ?></h3><p class="email-intelligence__description"><?php echo esc_html( $step['text'] ); ?></p></div>
					<?php if ( 1 === $index % 2 ) : ?>
						<div class="email-intelligence__number" aria-hidden="true"><img src="<?php echo esc_url( $step['image'] ); ?>" alt=""></div>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>






