<?php
/**
 * Why_Businesses icon carousel.
 *
 * @package MailsExtract
 */

$defaults = array(
	array( 'Customer Onboarding', 'Send onboarding emails, setup instructions, tutorials, account verification, and welcome sequences to new customers automatically.', 'https://www.mailsextract.com/wp-content/uploads/2026/08/Frame-275-4.png' ),
	array( 'Lead Nurturing', 'Automatically send welcome emails, educational content, product updates, and follow-ups to guide prospects through the business sales funnel.', 'https://www.mailsextract.com/wp-content/uploads/2026/08/Frame-274-4.png' ),
	array( 'Customer Onboarding', 'Send onboarding emails, setup instructions, tutorials, account verification, and welcome sequences to new customers automatically.', 'https://www.mailsextract.com/wp-content/uploads/2026/08/Frame-272-3.png' ),
	array( 'Lead Nurturing', 'Automatically send welcome emails, educational content, product updates, and follow-ups to guide prospects through the business sales funnel.', 'https://www.mailsextract.com/wp-content/uploads/2026/08/Frame-273-4.png' ),
	array( 'Marketing Campaign Automation', 'Launch personalized automated bulk email campaigns to promote products, events, newsletters, seasonal offers, and announcements.', 'https://www.mailsextract.com/wp-content/uploads/2026/08/Frame-276-4.png' ),
	array( 'Sales Follow-Ups', 'Automatically send follow-up emails, reminders, and proposal updates so opportunities are not missed.', 'https://www.mailsextract.com/wp-content/uploads/2026/08/Frame-275-4.png' ),
	array( 'Customer Engagement', 'Keep customers engaged through personalized newsletters, product recommendations, loyalty campaigns, and feedback requests.', 'https://www.mailsextract.com/wp-content/uploads/2026/08/Frame-274-4.png' ),
);
$items = array();
foreach ( $defaults as $index => $default ) {
	$number  = $index + 1;
	$items[] = array(
		'title'       => mailsextract_service_value( 'why_businesses_' . $number . '_title', $default[0] ),
		'description' => mailsextract_service_value( 'why_businesses_' . $number . '_text', $default[1] ),
		'image'       => mailsextract_service_value( 'why_businesses_' . $number . '_image', $default[2] ),
	);
}
?>

<section class="why-businesses-carousel overflow-hidden bg-white [font-family:'DM_Sans',sans-serif] text-[#111]" aria-labelledby="why-businesses-title" data-services="<?php echo esc_attr( wp_json_encode( $items ) ); ?>">
	<div class="mx-auto max-w-[1180px] px-4 pt-4 text-center sm:px-6 sm:pt-4">
		<div class="relative inline-block border-y-2 border-dashed border-black px-5 py-3">
			<span class="absolute -left-1.5 -top-1.5 h-3 w-3 bg-[#ff4b32]"></span><span class="absolute -right-1.5 -top-1.5 h-3 w-3 bg-[#ff4b32]"></span><span class="absolute -bottom-1.5 -left-1.5 h-3 w-3 bg-[#ff4b32]"></span><span class="absolute -bottom-1.5 -right-1.5 h-3 w-3 bg-[#ff4b32]"></span>
			<span class="absolute bottom-2 left-0 top-2 border-l-2 border-dashed border-black"></span><span class="absolute bottom-2 right-0 top-2 border-r-2 border-dashed border-black"></span>
			<span class="text-xl font-[500] leading-none text-[#ff4b32]"><?php echo esc_html( mailsextract_service_value( 'why_businesses_badge', 'How It Extracts?' ) ); ?></span>
		</div>
		<h2 id="why-businesses-title" class="reveal-up mx-auto mt-6 max-w-[560px] text-[27px] font-[500] leading-[1.05] text-black md:text-[42px]"><?php echo esc_html( mailsextract_service_value( 'why_businesses_title', 'Automate Every Customer Interaction' ) ); ?></h2>
		<p class="reveal-up mx-auto mt-5 max-w-[930px] text-[17px] leading-[1.45] text-[#252525] md:text-[20px]"><?php echo esc_html( mailsextract_service_value( 'why_businesses_text', 'MailsExtract helps businesses strengthen customer relationships, improve response times, and eliminate repetitive communication tasks.' ) ); ?></p>
	</div>
	<div class="reveal-up relative mx-auto mt-0 min-h-[440px] max-w-[1640px] sm:min-h-[465px]">
		<div class="absolute bottom-0 h-[505px] w-full overflow-hidden"><svg class="pointer-events-none absolute inset-0 h-full w-full" viewBox="0 0 1000 285" preserveAspectRatio="none" aria-hidden="true"><path d="M-10 30 Q500 200 1010 30 L1010 285 L-10 285 Z" fill="#f3f0e6"/><path d="M-10 30 Q500 200 1010 30" fill="none" stroke="#8d98a4" stroke-width="1" stroke-dasharray="6 7"/></svg></div>
		<div class="parser-slider-track relative mx-auto h-[285px] max-w-[1880px] overflow-hidden" aria-label="Service categories">
			<?php $colors = array( 'from-[#ffd54b] to-[#ffb70b]', 'from-[#ff826f] to-[#ff543b]', 'from-[#45d5ff] to-[#17b8ed]', 'from-[#ff826f] to-[#ff543b]', 'from-[#ffd54b] to-[#ffb70b]', 'from-[#ff826f] to-[#ff543b]', 'from-[#ff826f] to-[#ff543b]' ); ?>
			<?php foreach ( $items as $index => $item ) : ?><button type="button" class="parser-slide absolute grid size-[58px] place-items-center rounded-full bg-gradient-to-b <?php echo esc_attr( $colors[ $index ] ); ?> text-white shadow-[0_7px_13px_rgba(255,84,59,.22)] transition-[left,top,transform,opacity] duration-500 ease-out focus:outline-none focus-visible:ring-4 focus-visible:ring-[#129bf3]/30 sm:size-[102px]" data-slide="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( $item['title'] ); ?>"><img src="<?php echo esc_url( $item['image'] ); ?>" alt=""></button><?php endforeach; ?>
		</div>
		<div class="reveal-up absolute inset-x-4 top-[262px] mx-auto max-w-[780px] sm:top-[275px]"><div class="relative min-h-[126px] rounded-[22px] bg-white px-12 py-7 text-center shadow-[0_8px_22px_rgba(28,40,49,.06)] sm:min-h-[145px] sm:px-20 sm:py-8">
			<button type="button" class="parser-slider-prev absolute left-0 top-1/2 z-10 size-20 -translate-x-1/2 -translate-y-1/2 !border-0 !bg-transparent !p-0 !shadow-none !outline-none" aria-label="Previous service"><img class="block size-full object-contain" src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-319-1.png" alt="" aria-hidden="true"></button>
			<h3 class="parser-slide-title text-[14px] font-bold text-[#171717] md:text-[26px]"></h3><p class="parser-slide-description mx-auto mt-4 max-w-[610px] text-[11px] leading-[1.45] text-[#333] md:text-[18px]"></p>
			<button type="button" class="parser-slider-next absolute right-0 top-1/2 z-10 size-20 translate-x-1/2 -translate-y-1/2 !border-0 !bg-transparent !p-0 !shadow-none !outline-none" aria-label="Next service"><img class="block size-full object-contain" src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-318-1.png" alt="" aria-hidden="true"></button>
		</div></div>
	</div>
</section>
