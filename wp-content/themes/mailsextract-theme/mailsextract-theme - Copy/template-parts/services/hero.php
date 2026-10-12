<?php
/**
 * Service page hero.
 *
 * @package MailsExtract
 */

$fallback   = get_template_directory_uri() . '/assets/images/default-featured-image.png';
$hero_image = mailsextract_service_value( 'hero_image', get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: $fallback );
$cta_text   = mailsextract_service_value( 'cta_text', 'Start Parsing Free' );
$cta_url    = mailsextract_service_value( 'cta_url', home_url( '/contact-us/' ) );
?>

<section class="relative isolate mx-auto mt-14 min-h-[720px] w-[min(1550px,calc(100%_-_40px))] overflow-hidden rounded-[19px] bg-[#2097f2] bg-[url('https://www.mailsextract.com/wp-content/uploads/2026/07/Frame-279.png')] bg-cover bg-center bg-no-repeat text-white max-[560px]:mt-8 max-[560px]:w-[calc(100%_-_20px)]">
	<img class="pointer-events-none absolute -left-8 -top-12 z-[1] h-auto w-[200px] select-none [animation:mxTopLeftReveal_1s_ease-out_both] motion-reduce:animate-none" src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-316-2.png" alt="" aria-hidden="true">
	<img class="pointer-events-none absolute -bottom-16 -right-6 z-[1] h-auto w-[200px] select-none [animation:mxBottomRightReveal_1s_ease-out_both] motion-reduce:animate-none" src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-317-1.png" alt="" aria-hidden="true">

	<div class="relative z-[2] mx-auto grid min-h-[680px] w-[min(1400px,calc(100%_-_48px))] grid-cols-2 items-center max-[900px]:grid-cols-1 max-[900px]:pt-6 max-[560px]:w-[calc(100%_-_30px)]">
		<div class="py-[62px] pb-[22px] max-[900px]:pb-2.5">
		<h1 class="mb-8 max-w-[775px] text-[clamp(32px,3.8vw,48px)] font-[550] leading-[1.18] tracking-[-.5px] text-white max-[560px]:text-[30px]">
	<?php echo mailsextract_service_inline_html( mailsextract_service_value( 'hero_title', get_the_title() ) ); ?>
</h1>
			<p class="mb-12 max-w-[705px] text-[23px] leading-[1.38] tracking-[1.4px] text-white/99 max-[560px]:text-[17px] max-[560px]:tracking-[.4px]">
				<?php echo mailsextract_service_inline_html( mailsextract_service_value( 'hero_description', get_the_excerpt() ?: 'Turn incoming emails into useful, structured business data automatically.' ), true ); ?>
			</p>

			<a class="inline-flex transition-transform hover:-translate-y-0.5" href="<?php echo esc_url( $cta_url ); ?>" aria-label="<?php echo esc_attr( $cta_text ); ?>">
				<img src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-320.png" alt="<?php echo esc_attr( $cta_text ); ?>" class="h-auto w-[230px]">
			</a>

			<div class="mt-[68px] grid min-h-28 w-[min(625px,100%)] grid-cols-[116px_repeat(3,1fr)] items-center rounded-[17px] bg-white px-5 py-3.5 text-[#1f2937] shadow-[0_9px_24px_rgba(0,58,111,.2)] max-[560px]:mt-11 max-[560px]:grid-cols-3 max-[560px]:px-2" aria-label="Product statistics">
				<div class="h-[88px] w-[112px] overflow-hidden rounded-[7px] max-[560px]:hidden" aria-hidden="true">
					<img class="h-full w-full object-contain" src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-326.png" alt="">
				</div>

				<div class="flex min-h-[68px] flex-col items-center justify-center px-3 py-[5px] text-center max-[560px]:px-1.5">
					<strong class="mx-stat-value text-3xl font-bold leading-none text-[#ff503f] max-[560px]:text-2xl" data-count="1000" data-suffix="+">0+</strong>
					<span class="mt-[11px] text-[17px] font-semibold leading-[1.15] max-[560px]:text-[13px]">Active<br>Users</span>
				</div>
				<div class="flex min-h-[68px] flex-col items-center justify-center border-l border-[#e1e5e9] px-3 py-[5px] text-center max-[560px]:px-1.5">
					<strong class="mx-stat-value text-3xl font-bold leading-none text-[#ff503f] max-[560px]:text-2xl" data-count="99" data-suffix="%">0%</strong>
					<span class="mt-[11px] text-[17px] font-semibold leading-[1.15] max-[560px]:text-[13px]">Accuracy</span>
				</div>
				<div class="flex min-h-[68px] flex-col items-center justify-center border-l border-[#e1e5e9] px-3 py-[5px] text-center max-[560px]:px-1.5">
					<strong class="mx-stat-value text-3xl font-bold leading-none text-[#ff503f] max-[560px]:text-2xl" data-count="10" data-suffix="+">0+</strong>
					<span class="mt-[11px] text-[17px] font-semibold leading-[1.15] max-[560px]:text-[13px]">App<br>Integrations</span>
				</div>
			</div>
		</div>

		<div class="ml-[2%] w-full translate-y-1 max-[900px]:mx-auto max-[900px]:mb-6 max-[900px]:mt-2.5 max-[900px]:w-[min(600px,70%)] max-[560px]:w-[90%]">
			<img class="block h-[620px] w-[690px] max-w-full object-contain [animation:mxHeroImageFromBottom_.9s_ease-out_both] motion-reduce:animate-none max-[900px]:h-auto max-[900px]:max-h-[520px]" src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="690" height="620">
		</div>
	</div>
</section>
