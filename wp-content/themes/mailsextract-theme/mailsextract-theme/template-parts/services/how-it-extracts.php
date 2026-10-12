<?php
/**
 * How_It_Extracts four-card section.
 *
 * @package MailsExtract
 */

$defaults = array(
	array( 'Real-Time Email Processing', 'MailsExtract offers faster, reliable, and scalable data extraction with real-time processing.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/image-156.png' ),
	array( 'AI-Powered Email Recognition', 'The platform identifies customer names, order numbers, invoice details, and tracking IDs automatically.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/image-157.png' ),
	array( 'Document Extraction', 'MailsExtract helps businesses extract structured data from PDF invoices, Excel files, Word documents, purchase orders, shipping labels, receipts, and more.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/Untitled-July-22-2026-at-13.05.53-1.png' ),
	array( 'Workflow Automation', 'MailsExtract can automatically send extracted information to CRMs, ERPs, databases, Google Sheets, APIs, Webhooks, and other systems.', 'https://www.mailsextract.com/wp-content/uploads/2026/07/Untitled-July-22-2026-at-13.44.12-1.png' ),
);
$cards = array();
$card_padding = array( 'px-12', 'px-12', 'px-5', 'px-0' );
$icon_width   = array( 'w-[94px]', 'w-[103px]', 'w-[104px]', 'w-[104px]' );
foreach ( $defaults as $index => $default ) {
	$number  = $index + 1;
	$cards[] = array(
		'title' => mailsextract_service_value( 'how_extracts_' . $number . '_title', $default[0] ),
		'text'  => mailsextract_service_value( 'how_extracts_' . $number . '_text', $default[1] ),
		'image' => mailsextract_service_value( 'how_extracts_' . $number . '_image', $default[2] ),
	);
}
?>

<section class="relative isolate mx-auto mt-10 min-h-[780px] max-w-[1640px] overflow-hidden bg-[#eaf9fd] px-4 py-8 font-['DM_Sans'] text-[#101010] sm:px-8 lg:px-[60px]">
	<div class="pointer-events-none absolute -right-[162px] top-2 -z-0 hidden sm:block"><div class="ripple-circle"><span class="ring"></span><span class="ring"></span><span class="ring"></span><span class="ring"></span></div></div>
	<div class="pointer-events-none absolute -bottom-[105px] -left-[105px] -z-0 hidden sm:block"><div class="ripple-circle"><span class="ring"></span><span class="ring"></span><span class="ring"></span><span class="ring"></span></div></div>

	<div class="relative z-10 mx-auto max-w-[1270px]">
		<header class="text-center">
			<span class="reveal-up inline-flex min-h-[30px] min-w-[220px] items-center justify-center rounded-full border border-[#ff806b] bg-white px-5 py-1 text-[18px] font-semibold leading-none text-[#ff4d37] shadow-[0_2px_4px_rgba(0,0,0,.12)]"><?php echo esc_html( mailsextract_service_value( 'how_extracts_badge', 'How It Extracts?' ) ); ?></span>
			<h2 class="reveal-up mx-auto mt-6 max-w-[790px] text-[27px] font-medium leading-[1.08] tracking-[-.45px] text-black md:text-[43px]"><?php echo esc_html( mailsextract_service_value( 'how_extracts_title', 'Powerful Features That Automate Email Processing' ) ); ?></h2>
			<p class="reveal-up mx-auto mt-6 max-w-[750px] text-[13px] leading-[1.35] text-[#202020] sm:text-[19px]"><?php echo esc_html( mailsextract_service_value( 'how_extracts_text', 'The AI-powered Email Data Extraction of MailsExtract combines intelligent recognition, document processing, workflow automation, and enterprise integration to automate every step of email processing.' ) ); ?></p>
		</header>

		<div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 lg:gap-[20px]">
			<?php foreach ( $cards as $index => $card ) : ?>
				<article class="reveal-up float-card group flex min-h-[232px] flex-col items-center rounded-[10px] bg-white <?php echo esc_attr( $card_padding[ $index ] ); ?> pb-5 pt-[30px] text-center shadow-[4px_5px_2px_rgba(80,88,91,.32)] transition-all duration-500 ease-out hover:-translate-y-4 hover:scale-[1.04] hover:shadow-[0_20px_40px_rgba(0,185,245,.25)]">
					<img class="mb-2 h-[78px] <?php echo esc_attr( $icon_width[ $index ] ); ?> object-contain transition-all duration-500 group-hover:scale-110 <?php echo 0 === $index || 2 === $index ? 'group-hover:-rotate-6' : 'group-hover:rotate-6'; ?>" src="<?php echo esc_url( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" width="104" height="78">
					<h3 class="text-[24px] font-bold leading-[1.2] text-[#111] transition-colors duration-300 group-hover:text-[#00b9f5]"><?php echo esc_html( $card['title'] ); ?></h3>
					<p class="mb-5 mt-4 max-w-[232px] text-[17px] leading-[1.25] text-[#1f1f1f] transition-colors duration-300 group-hover:text-[#444]"><?php echo esc_html( $card['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
