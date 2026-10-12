<?php
/**
 * Why businesses choose this service section.
 *
 * @package MailsExtract
 */

$why_title = mailsextract_service_value( 'why_title', 'Smarter Email Extraction. Faster Business Automation.' );
$why_image = mailsextract_service_value( 'why_image', 'https://www.mailsextract.com/wp-content/uploads/2026/08/What-We-Do-1-1.png' );
$benefits  = array_filter(
	array(
		mailsextract_service_value( 'benefit_1', 'AI-Powered Email Recognition' ),
		mailsextract_service_value( 'benefit_2', 'Real-Time Data Extraction' ),
		mailsextract_service_value( 'benefit_3', 'Automated Email Processing' ),
		mailsextract_service_value( 'benefit_4', 'No-Code Configuration' ),
	)
);
?>

<section class="bg-white px-6 pt-16">
	<div class="mx-auto max-w-[1500px]">
		<div class="reveal-up grid items-center gap-16 lg:grid-cols-2">
			<div class="relative flex justify-center">
				<div class="absolute -left-2 top-28 hidden md:block"><div class="smart-ripple-circle"><span class="smart-ring"></span><span class="smart-ring"></span><span class="smart-ring"></span><span class="smart-ring"></span></div></div>
				<div class="absolute right-0 top-16 hidden md:block"><div class="smart-ripple-circle"><span class="smart-ring"></span><span class="smart-ring"></span><span class="smart-ring"></span><span class="smart-ring"></span></div></div>
				<div class="absolute -bottom-8 left-1/2 hidden -translate-x-1/2 translate-y-8 md:block"><div class="smart-ripple-circle"><span class="smart-ring"></span><span class="smart-ring"></span><span class="smart-ring"></span><span class="smart-ring"></span></div></div>
				<div class="relative z-10"><img src="<?php echo esc_url( $why_image ); ?>" alt="<?php echo esc_attr( $why_title ); ?>" class="w-full max-w-[520px]"></div>
			</div>

			<div class="max-w-[620px]">
				<span class="reveal-up inline-flex items-center rounded-full bg-[#bfefff] px-6 py-2 text-[15px] font-medium text-[#111]"><?php echo esc_html( mailsextract_service_value( 'why_badge', 'Why Teams Choose Us?' ) ); ?></span>
				<h2 class="reveal-up mt-6 text-[32px] font-[550] leading-[1.15] text-[#151515] md:text-[45px]"><?php echo esc_html( $why_title ); ?></h2>
				<p class="reveal-up mt-6 text-[18px] leading-[1.75] text-[#333]"><?php echo nl2br( esc_html( mailsextract_service_value( 'why_text', 'Businesses rely heavily on emails. MailsExtract transforms incoming emails into structured business records that power sales, finance, logistics, customer support, and operations.' ) ) ); ?></p>
				<div class="reveal-up mt-10 grid grid-cols-1 gap-x-12 gap-y-8 sm:grid-cols-2">
					<?php foreach ( $benefits as $benefit ) : ?>
						<div class="flex items-start gap-3"><span class="mt-2 h-3 w-3 shrink-0 rounded-full bg-[#2196f3]"></span><p class="text-[18px] leading-[1.45] text-[#222]"><?php echo esc_html( $benefit ); ?></p></div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
