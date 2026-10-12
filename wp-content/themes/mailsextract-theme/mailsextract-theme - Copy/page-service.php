<?php
/**
 * Template Name: Service Page
 * Template Post Type: page
 *
 * Shared controller for every service page.
 *
 * @package MailsExtract
 */

get_header();
?>

<main class="mx-service">
	<?php
	get_template_part( 'template-parts/services/hero' );
	get_template_part( 'template-parts/services/parser' );
	get_template_part( 'template-parts/services/why-businesses' );
	get_template_part( 'template-parts/services/how-it-extracts' );
	get_template_part( 'template-parts/services/all-the-steps' );
	get_template_part( 'template-parts/services/why-business' );
	get_template_part( 'template-parts/services/faq' );
	?>
</main>

<?php get_footer(); ?>
