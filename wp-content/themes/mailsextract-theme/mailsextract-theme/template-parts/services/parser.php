<?php
/**
 * Introduction section displayed immediately after the service hero.
 *
 * @package MailsExtract
 */

$fallback          = get_template_directory_uri() . '/assets/images/default-featured-image.png';
$section_title     = mailsextract_service_value( 'parser_title', 'Smarter email workflows with AI' );
$left_image        = mailsextract_service_value( 'parser_left_image', $fallback );
$right_image       = mailsextract_service_value( 'parser_image', $fallback );
$second_paragraph  = mailsextract_service_value( 'parser_text_2', 'The platform helps organizations automate customer outreach, follow-ups, notifications, and other email-driven business processes.' );
?>

<section class="w-full overflow-hidden bg-white px-3 py-10 text-[#111] max-[600px]:px-4">
	<div class="mx-auto grid min-h-[500px] w-[min(1480px,100%)] grid-cols-[minmax(0,1.08fr)_minmax(390px,.92fr)] items-center gap-[10px] max-[980px]:grid-cols-1 max-[980px]:gap-3">
		<div class="py-[14px] opacity-0 [animation:mxSectionReveal_.85s_ease-out_.15s_forwards] motion-reduce:animate-none motion-reduce:opacity-100 max-[1180px]:mx-auto">
			<span class="mb-[17px] inline-flex min-h-7 items-center rounded-full bg-[#b8f1ff] px-[26px] py-[5px] text-[17px] font-bold leading-none text-[#09131c] shadow-[0_3px_5px_rgba(0,0,0,.18)]"><?php echo mailsextract_service_inline_html( mailsextract_service_value( 'parser_badge', 'What is Email Automation?' ) ); ?></span>
			<h2 class="mb-5 mt-2 max-w-[690px] text-[clamp(27px,2.65vw,45px)] font-[540] leading-[1.06] tracking-[-.45px] text-[#090909] max-[600px]:text-[27px]"><?php echo mailsextract_service_inline_html( $section_title ); ?></h2>
			<p class="mb-[9px] max-w-[778px] text-[21px] leading-[1.32] text-[#171717] max-[600px]:text-[15px]"><?php echo mailsextract_service_inline_html( mailsextract_service_value( 'parser_text', 'MailsExtract creates intelligent workflows that process incoming emails automatically, saving time and helping teams deliver accurate results without repetitive manual work.' ), true ); ?></p>
			<?php if ( $second_paragraph ) : ?>
				<p class="mb-[9px] max-w-[778px] text-[21px] leading-[1.32] text-[#171717] max-[600px]:text-[15px]"><?php echo mailsextract_service_inline_html( $second_paragraph, true ); ?></p>
			<?php endif; ?>
			<?php if ( $left_image ) : ?>
				<div class="mt-7 max-w-[700px]">
					<img class="block max-h-[170px] w-[70%] object-contain object-left max-[600px]:w-full" src="<?php echo esc_url( $left_image ); ?>" alt="">
				</div>
			<?php endif; ?>
		</div>

		<div class="relative rounded-[27px] opacity-0 [animation:mxSectionReveal_.85s_ease-out_.15s_forwards] motion-reduce:animate-none motion-reduce:opacity-100 max-[980px]:mx-auto max-[980px]:max-w-[650px]">
			<img class="relative z-[1] block max-h-[625px] w-full object-contain object-bottom max-[600px]:max-h-[460px]" src="<?php echo esc_url( $right_image ); ?>" alt="<?php echo esc_attr( $section_title ); ?>" width="540" height="525">
		  <!-- Floating AI icon -->
			<span
				class="pointer-events-none absolute left-[6%] top-[35%] z-[4] w-[clamp(45px,7vw,80px)] max-[600px]:left-[3%]"
				aria-hidden="true"
			>
			<img class="block h-auto w-full" src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-327.png" alt="">
               
            </span>

            <!-- Floating gear icon -->
			<span
				class="pointer-events-none absolute right-[7%] top-[59%] z-[4] w-[clamp(45px,7vw,80px)] max-[600px]:right-[3%]"
				aria-hidden="true"
			>
			   <img class="block h-auto w-full" src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-328.png" alt="">
            </span>
		
		</div>
	</div>
</section>

