<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'the_author', function( $author ) {
	return 'Editorial Team';
});

get_header();

global $wp_query;

$em_query       = get_search_query();
$em_total       = (int) $wp_query->found_posts;
$em_post_type   = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
$em_terms       = array_filter( preg_split( '/\s+/', trim( $em_query ) ) );

/**
 * Wrap search terms in <mark> inside already-escaped text.
 */
if ( ! function_exists( 'em_search_highlight' ) ) {
	function em_search_highlight( $text, $terms ) {
		$text = esc_html( $text );
		foreach ( $terms as $term ) {
			if ( mb_strlen( $term ) < 2 ) {
				continue;
			}
			$pattern = '/(' . preg_quote( esc_html( $term ), '/' ) . ')/iu';
			$text    = preg_replace( $pattern, '<mark>$1</mark>', $text );
		}
		return $text;
	}
}

if ( ! function_exists( 'em_search_reading_time' ) ) {
	function em_search_reading_time( $post_id ) {
		$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
		return max( 1, (int) ceil( $words / 200 ) );
	}
}

if ( ! function_exists( 'em_search_card' ) ) {
	/**
	 * Render one result card. $featured gives the wide editorial layout.
	 */
	function em_search_card( $terms = array(), $featured = false, $heading = 'h2' ) {
		$cats    = get_the_category();
		$classes = array( 'em-card' );
		if ( $featured ) {
			$classes[] = 'em-card--featured';
		}
		$title   = $terms ? em_search_highlight( get_the_title(), $terms ) : esc_html( get_the_title() );
		$excerpt = wp_trim_words( get_the_excerpt(), $featured ? 42 : 24, '…' );
		$excerpt = $terms ? em_search_highlight( $excerpt, $terms ) : esc_html( $excerpt );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
			<div class="em-card__media<?php echo has_post_thumbnail() ? '' : ' em-card__media--empty'; ?>" aria-hidden="true">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( $featured ? 'large' : 'medium_large', array( 'loading' => $featured ? 'eager' : 'lazy', 'alt' => '' ) ); ?>
				<?php else : ?>
					<span class="em-card__placeholder">
						<svg viewBox="0 0 24 24"><path d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Zm0 2v.5l8 5 8-5V7H4Zm16 2.85-7.47 4.67a1 1 0 0 1-1.06 0L4 9.85V17h16V9.85Z"/></svg>
					</span>
				<?php endif; ?>
				<?php if ( $featured ) : ?>
					<span class="em-card__badge">Top match</span>
				<?php endif; ?>
			</div>

			<div class="em-card__body">
				<div class="em-card__top">
					<?php if ( ! empty( $cats ) ) : ?>
						<a class="em-card__cat" href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a>
					<?php elseif ( 'post' !== get_post_type() ) : ?>
						<span class="em-card__cat"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
					<?php endif; ?>
					<span class="em-card__read">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20Zm0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm0 2a1 1 0 0 1 1 1v4.59l2.7 2.7a1 1 0 0 1-1.4 1.42l-3-3A1 1 0 0 1 11 12V7a1 1 0 0 1 1-1Z"/></svg>
						<?php echo esc_html( em_search_reading_time( get_the_ID() ) ); ?> min read
					</span>
				</div>

				<<?php echo tag_escape( $heading ); ?> class="em-card__title">
					<a href="<?php the_permalink(); ?>"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></a>
				</<?php echo tag_escape( $heading ); ?>>

				<p class="em-card__excerpt"><?php echo $excerpt; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></p>

				<div class="em-card__meta">
					<span class="em-card__date">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 2a1 1 0 0 1 1 1v1h6V3a1 1 0 1 1 2 0v1h1a3 3 0 0 1 3 3v11a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h1V3a1 1 0 0 1 1-1Zm11 8H5v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8ZM6 6a1 1 0 0 0-1 1v1h14V7a1 1 0 0 0-1-1H6Z"/></svg>
						<?php echo esc_html( get_the_date( 'j M Y' ) ); ?>
					</span>
					<span class="em-card__more">
						Read article
						<span class="em-card__arrow">
							<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.3 5.3a1 1 0 0 1 1.4 0l6 6a1 1 0 0 1 0 1.4l-6 6a1 1 0 0 1-1.4-1.4l4.3-4.3H4a1 1 0 1 1 0-2h13.6l-4.3-4.3a1 1 0 0 1 0-1.4Z"/></svg>
						</span>
					</span>
				</div>
			</div>
		</article>
		<?php
	}
}

if ( ! function_exists( 'em_search_form' ) ) {
	function em_search_form( $query, $post_type, $variant = 'hero' ) {
		?>
		<form role="search" method="get" class="em-search-form em-search-form--<?php echo esc_attr( $variant ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="em-search-input-<?php echo esc_attr( $variant ); ?>">Search for:</label>
			<svg class="em-search-form__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.5 3a7.5 7.5 0 0 1 5.96 12.06l4.24 4.24a1 1 0 0 1-1.42 1.42l-4.24-4.24A7.5 7.5 0 1 1 10.5 3Zm0 2a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Z"/></svg>
			<input id="em-search-input-<?php echo esc_attr( $variant ); ?>" type="search" name="s" value="<?php echo esc_attr( $query ); ?>" placeholder="Search articles, guides and tools…" autocomplete="off">
			<?php if ( $post_type ) : ?>
				<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
			<?php endif; ?>
			<button type="submit">Search</button>
		</form>
		<?php
	}
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

<style>
	/* ---------- Break out of Astra / header containers on the search page ---------- */
	body.search #content {
		padding-top: 0 !important;
		margin-top: 0 !important;
		background: #F6FAFD;
	}
	body.search #content > .ast-container,
	body.search .site-content > .ast-container {
		max-width: 100% !important;
		width: 100% !important;
		padding: 0 !important;
		margin: 0 !important;
		display: block !important;
	}
	body.search .ast-archive-description,
	body.search .ast-breadcrumbs-wrapper {
		display: none !important;
	}

	/* ---------- Page ---------- */
	.em-search {
		--em-navy: #071C50;
		--em-navy-2: #05289B;
		--em-blue: #0048C2;
		--em-sky: #6CB5FF;
		--em-ink: #0B1B38;
		--em-muted: #5A6785;
		--em-line: #D5EAFD;
		--em-bg: #F6FAFD;
		width: 100%;
		background: var(--em-bg);
		color: var(--em-ink);
		font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		-webkit-font-smoothing: antialiased;
		overflow-x: hidden;
	}
	.em-search *,
	.em-search *::before,
	.em-search *::after {
		box-sizing: border-box;
	}
	/* Explicit family (not inherit) so site-wide font overrides can't swap in a serif */
	.em-search,
	.em-search h1,
	.em-search h2,
	.em-search h3,
	.em-search p,
	.em-search a,
	.em-search button,
	.em-search input,
	.em-search span,
	.em-search q,
	.em-search mark,
	.em-search strong {
		font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
	}
	.em-search__wrap {
		max-width: 1280px;
		margin: 0 auto;
		padding: 0 24px;
	}

	/* ---------- Hero ---------- */
	.em-search__hero {
		position: relative;
		overflow: hidden;
		background: var(--em-navy);
		color: #fff;
		padding: 150px 0 72px;
	}
	body.admin-bar .em-search__hero {
		padding-top: 182px;
	}
	.em-search__hero::before,
	.em-search__hero::after {
		content: "";
		position: absolute;
		border-radius: 50%;
		pointer-events: none;
		filter: blur(120px);
	}
	.em-search__hero::before {
		width: 420px; height: 420px;
		top: -160px; left: -120px;
		background: var(--em-navy-2);
		opacity: .6;
	}
	.em-search__hero::after {
		width: 460px; height: 460px;
		bottom: -220px; right: -80px;
		background: #0A7CF8;
		opacity: .28;
	}
	.em-search__hero .em-search__wrap {
		position: relative;
		z-index: 1;
	}
	.em-search__crumbs {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 8px;
		margin: 0 0 20px;
		font-size: 14px;
		color: #CDDAF4;
	}
	.em-search__crumbs a {
		color: #CDDAF4;
		text-decoration: none;
	}
	.em-search__crumbs a:hover {
		color: #fff;
	}
	.em-search__crumbs span[aria-hidden] {
		opacity: .5;
	}
	.em-search__eyebrow {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 6px 14px;
		border-radius: 999px;
		background: rgba(5, 40, 155, .5);
		border: 1px solid rgba(108, 181, 255, .3);
		color: var(--em-sky);
		font-size: 12px;
		font-weight: 700;
		letter-spacing: .08em;
		text-transform: uppercase;
	}
	.em-search__eyebrow::before {
		content: "";
		width: 8px; height: 8px;
		border-radius: 50%;
		background: var(--em-sky);
	}
	.em-search__title {
		margin: 18px 0 10px;
		font-size: clamp(32px, 4.5vw, 52px);
		line-height: 1.1;
		font-weight: 700;
		letter-spacing: -0.025em;
		color: #fff;
		overflow-wrap: anywhere;
	}
	.em-search__title q {
		color: var(--em-sky);
		quotes: "\201C" "\201D";
	}
	.em-search__count {
		margin: 0 0 32px;
		font-size: 17px;
		color: #CDDAF4;
	}
	.em-search__count strong {
		color: #fff;
		font-weight: 600;
	}

	/* ---------- Search form ---------- */
	.em-search-form {
		position: relative;
		display: flex;
		align-items: center;
		gap: 8px;
		max-width: 680px;
		padding: 6px;
		border-radius: 16px;
		background: #fff;
		box-shadow: 0 18px 40px rgba(2, 12, 40, .35);
	}
	.em-search-form--empty {
		margin: 28px auto 0;
		border: 1px solid var(--em-line);
		box-shadow: 0 10px 30px rgba(7, 28, 80, .08);
	}
	.em-search-form__icon {
		flex: none;
		width: 20px; height: 20px;
		margin-left: 12px;
		fill: #8A96B3;
	}
	.em-search-form input[type="search"] {
		flex: 1;
		min-width: 0;
		height: 48px;
		padding: 0 8px;
		border: 0 !important;
		background: transparent !important;
		box-shadow: none !important;
		outline: none;
		color: var(--em-ink);
		font-size: 16px;
	}
	.em-search-form input[type="search"]::-webkit-search-cancel-button {
		cursor: pointer;
	}
	.em-search-form button {
		flex: none;
		height: 48px;
		padding: 0 26px;
		border: 0;
		border-radius: 12px;
		background: var(--em-blue);
		color: #fff;
		font-size: 15px;
		font-weight: 600;
		cursor: pointer;
		transition: background .2s ease;
	}
	.em-search-form button:hover,
	.em-search-form button:focus-visible {
		background: var(--em-navy-2);
	}

	/* ---------- Results ---------- */
	.em-search__results {
		padding: 64px 0 96px;
	}
	.em-search__grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 32px;
	}
	/* Astra adds .ast-article-post (big padding, margins, widths) via post_class() — neutralise it */
	.em-search .em-card {
		position: relative;
		display: flex;
		flex-direction: column;
		width: auto !important;
		max-width: none !important;
		float: none !important;
		margin: 0 !important;
		padding: 10px !important;
		background: #fff;
		border: 1px solid rgba(7, 28, 80, .07);
		border-radius: 24px;
		box-shadow: 0 1px 2px rgba(7, 28, 80, .04), 0 10px 30px -14px rgba(7, 28, 80, .14);
		transition: transform .4s cubic-bezier(.2, .7, .2, 1), box-shadow .4s ease, border-color .4s ease;
	}
	.em-search .em-card:hover,
	.em-search .em-card:focus-within {
		transform: translateY(-6px);
		border-color: rgba(0, 72, 194, .16);
		box-shadow: 0 2px 4px rgba(7, 28, 80, .04), 0 32px 60px -24px rgba(5, 40, 155, .32);
	}

	/* Image shown at its natural ratio: never cropped, never letterboxed */
	.em-card__media {
		position: relative;
		overflow: hidden;
		border-radius: 16px;
		background: #EEF4FC;
		isolation: isolate;
	}
	.em-card__media img {
		display: block;
		width: 100%;
		height: auto;
		max-width: none;
	}
	.em-card__media::after {
		content: "";
		position: absolute;
		inset: 0;
		border-radius: inherit;
		box-shadow: inset 0 0 0 1px rgba(7, 28, 80, .06);
		background: linear-gradient(180deg, rgba(7, 28, 80, 0) 55%, rgba(7, 28, 80, .22) 100%);
		opacity: .0;
		transition: opacity .4s ease;
		pointer-events: none;
	}
	.em-card:hover .em-card__media::after {
		opacity: 1;
	}
	.em-card__media--empty {
		aspect-ratio: 16 / 9;
		background: linear-gradient(135deg, var(--em-navy) 0%, var(--em-navy-2) 60%, #0A7CF8 100%);
	}
	.em-card__placeholder {
		position: absolute;
		inset: 0;
		display: grid;
		place-items: center;
	}
	.em-card__placeholder svg {
		width: 56px; height: 56px;
		fill: rgba(255, 255, 255, .55);
	}
	.em-card__badge {
		position: absolute;
		top: 14px;
		left: 14px;
		z-index: 1;
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 6px 12px;
		border-radius: 999px;
		background: rgba(7, 28, 80, .72);
		-webkit-backdrop-filter: blur(8px);
		backdrop-filter: blur(8px);
		color: #fff;
		font-size: 12px;
		font-weight: 600;
		letter-spacing: .02em;
	}
	.em-card__badge::before {
		content: "";
		width: 6px; height: 6px;
		border-radius: 50%;
		background: var(--em-sky);
		box-shadow: 0 0 0 3px rgba(108, 181, 255, .3);
	}

	.em-card__body {
		display: flex;
		flex-direction: column;
		flex: 1;
		padding: 20px 14px 10px;
	}
	.em-card__top {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 8px 12px;
		margin-bottom: 14px;
	}
	.em-card__cat {
		position: relative;
		z-index: 2;
		padding: 5px 11px;
		border-radius: 999px;
		background: #EEF4FF;
		border: 1px solid #DCE7FD;
		color: var(--em-blue);
		font-size: 11px;
		font-weight: 700;
		letter-spacing: .07em;
		line-height: 1.2;
		text-transform: uppercase;
		text-decoration: none;
		transition: background .2s ease, color .2s ease, border-color .2s ease;
	}
	.em-card__cat:hover {
		background: var(--em-blue);
		border-color: var(--em-blue);
		color: #fff;
	}
	.em-card__read,
	.em-card__date {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		color: #7A86A3;
		font-size: 13px;
		font-weight: 500;
	}
	.em-card__read svg,
	.em-card__date svg {
		width: 15px; height: 15px;
		fill: #A3AEC6;
	}
	.em-card__title {
		margin: 0 0 10px;
		color: var(--em-ink);
		font-size: 21px;
		line-height: 1.3;
		font-weight: 700;
		letter-spacing: -0.02em;
		overflow-wrap: anywhere;
	}
	.em-card__title a {
		color: inherit;
		text-decoration: none;
		transition: color .2s ease;
	}
	.em-card:hover .em-card__title a {
		color: var(--em-blue);
	}
	/* Whole card is clickable via the title link */
	.em-card__title a::after {
		content: "";
		position: absolute;
		inset: 0;
		z-index: 1;
	}
	.em-card__title a:focus-visible {
		outline: none;
	}
	.em-card:focus-within {
		outline: 2px solid var(--em-blue);
		outline-offset: 2px;
	}
	.em-card__excerpt {
		margin: 0 0 22px;
		color: var(--em-muted);
		font-size: 15px;
		line-height: 1.65;
		display: -webkit-box;
		-webkit-line-clamp: 3;
		-webkit-box-orient: vertical;
		overflow: hidden;
	}
	.em-search mark {
		background: #FFF1B8;
		color: inherit;
		padding: 0 2px;
		border-radius: 3px;
	}
	.em-card__meta {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		margin-top: auto;
		padding-top: 16px;
		border-top: 1px solid #EEF2F8;
	}
	.em-card__more {
		display: inline-flex;
		align-items: center;
		gap: 10px;
		color: var(--em-ink);
		font-size: 14px;
		font-weight: 600;
		white-space: nowrap;
		transition: color .2s ease;
	}
	.em-card__arrow {
		display: inline-grid;
		place-items: center;
		width: 34px; height: 34px;
		border-radius: 50%;
		background: #EEF4FF;
		color: var(--em-blue);
		transition: background .3s ease, color .3s ease, transform .3s ease;
	}
	.em-card__arrow svg {
		width: 16px; height: 16px;
		fill: currentColor;
		transition: transform .3s ease;
	}
	.em-card:hover .em-card__more {
		color: var(--em-blue);
	}
	.em-card:hover .em-card__arrow {
		background: var(--em-blue);
		color: #fff;
		box-shadow: 0 8px 18px -6px rgba(0, 72, 194, .55);
	}
	.em-card:hover .em-card__arrow svg {
		transform: translateX(2px) rotate(-45deg);
	}

	/* ---------- Featured (top match) card ---------- */
	@media (min-width: 900px) {
		.em-search .em-card--featured {
			grid-column: 1 / -1;
			flex-direction: row;
			align-items: center;
			gap: 8px;
			padding: 12px !important;
		}
		.em-card--featured .em-card__media {
			flex: 0 0 56%;
			border-radius: 18px;
		}
		.em-card--featured .em-card__body {
			align-self: stretch;
			justify-content: center;
			padding: 28px 36px 24px;
		}
		.em-card--featured .em-card__title {
			font-size: clamp(26px, 2.4vw, 34px);
			line-height: 1.2;
			letter-spacing: -0.025em;
			margin-bottom: 14px;
		}
		.em-card--featured .em-card__excerpt {
			font-size: 16.5px;
			-webkit-line-clamp: 4;
			margin-bottom: 28px;
		}
		.em-card--featured .em-card__meta {
			margin-top: 0;
		}
	}

	/* ---------- Pagination ---------- */
	.em-search__pagination {
		margin-top: 56px;
	}
	.em-search__pagination ul {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 8px;
		margin: 0;
		padding: 0;
		list-style: none;
	}
	.em-search__pagination .page-numbers {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		min-width: 44px;
		height: 44px;
		padding: 0 14px;
		border-radius: 12px;
		border: 1px solid var(--em-line);
		background: #fff;
		color: var(--em-ink);
		font-size: 15px;
		font-weight: 600;
		text-decoration: none;
	}
	.em-search__pagination a.page-numbers:hover {
		border-color: var(--em-blue);
		color: var(--em-blue);
	}
	.em-search__pagination .page-numbers.current {
		background: var(--em-blue);
		border-color: var(--em-blue);
		color: #fff;
	}
	.em-search__pagination .page-numbers.dots {
		border: 0;
		background: transparent;
	}

	/* ---------- Empty state ---------- */
	.em-search__empty {
		max-width: 720px;
		margin: 0 auto;
		padding: 56px 32px;
		text-align: center;
		background: #fff;
		border: 1px solid var(--em-line);
		border-radius: 24px;
	}
	.em-search__empty-icon {
		display: inline-grid;
		place-items: center;
		width: 72px; height: 72px;
		border-radius: 20px;
		background: #E8F1FE;
	}
	.em-search__empty-icon svg {
		width: 34px; height: 34px;
		fill: var(--em-blue);
	}
	.em-search__empty h2 {
		margin: 20px 0 8px;
		font-size: 28px;
		font-weight: 700;
		color: var(--em-ink);
	}
	.em-search__empty p {
		margin: 0;
		color: var(--em-muted);
		font-size: 16px;
		line-height: 1.6;
	}
	.em-search__tips {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		gap: 10px;
		margin-top: 24px;
	}
	.em-search__tips a {
		padding: 8px 16px;
		border-radius: 999px;
		border: 1px solid var(--em-line);
		color: var(--em-ink);
		font-size: 14px;
		font-weight: 500;
		text-decoration: none;
	}
	.em-search__tips a:hover {
		border-color: var(--em-blue);
		color: var(--em-blue);
	}
	.em-search__section-title {
		margin: 64px 0 24px;
		font-size: 26px;
		font-weight: 700;
		letter-spacing: -0.015em;
		color: var(--em-ink);
	}

	/* ---------- Responsive ---------- */
	@media (max-width: 1024px) {
		.em-search__grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 24px;
		}
		.em-card--featured {
			grid-column: 1 / -1;
		}
	}
	@media (max-width: 768px) {
		.em-search__wrap {
			padding: 0 16px;
		}
		.em-search__hero {
			padding: 120px 0 48px;
		}
		body.admin-bar .em-search__hero {
			padding-top: 166px;
		}
		.em-search__results {
			padding: 40px 0 64px;
		}
	}
	@media (max-width: 640px) {
		.em-search__grid {
			grid-template-columns: 1fr;
		}
		.em-search-form button {
			padding: 0 18px;
		}
		.em-search__empty {
			padding: 40px 20px;
		}
	}
	@media (prefers-reduced-motion: reduce) {
		.em-search .em-card,
		.em-card__media::after,
		.em-card__arrow,
		.em-card__arrow svg {
			transition: none;
		}
		.em-search .em-card:hover,
		.em-search .em-card:focus-within {
			transform: none;
		}
	}
</style>

<div class="em-search">

	<!-- HERO -->
	<section class="em-search__hero">
		<div class="em-search__wrap">
			<nav class="em-search__crumbs" aria-label="Breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a>
				<span aria-hidden="true">/</span>
				<span>Search</span>
			</nav>

			<span class="em-search__eyebrow">Search results</span>

			<h1 class="em-search__title">
				<?php if ( $em_query ) : ?>
					Results for <q><?php echo esc_html( $em_query ); ?></q>
				<?php else : ?>
					Search the ExtractMails blog
				<?php endif; ?>
			</h1>

			<p class="em-search__count">
				<?php if ( $em_query ) : ?>
					<?php if ( $em_total > 0 ) : ?>
						<strong><?php echo esc_html( number_format_i18n( $em_total ) ); ?></strong>
						<?php echo esc_html( _n( 'article matches your search', 'articles match your search', $em_total ) ); ?>
					<?php else : ?>
						No articles matched your search.
					<?php endif; ?>
				<?php else : ?>
					Find guides on email extraction, deliverability and automation.
				<?php endif; ?>
			</p>

			<?php em_search_form( $em_query, $em_post_type, 'hero' ); ?>
		</div>
	</section>

	<!-- RESULTS -->
	<section class="em-search__results">
		<div class="em-search__wrap">

			<?php if ( have_posts() ) : ?>

				<div class="em-search__grid">
					<?php
					// Lead with a wide "top match" card on page 1 when there are enough results to fill the rows below it.
					$em_has_featured = ! is_paged() && $wp_query->post_count > 3;
					while ( have_posts() ) :
						the_post();
						em_search_card( $em_terms, $em_has_featured && 0 === $wp_query->current_post );
					endwhile;
					?>
				</div>

				<?php
				$em_pagination = paginate_links(
					array(
						'type'      => 'list',
						'mid_size'  => 1,
						'prev_text' => '&larr; Prev',
						'next_text' => 'Next &rarr;',
					)
				);
				if ( $em_pagination ) :
					?>
					<nav class="em-search__pagination" aria-label="Search results pages">
						<?php echo $em_pagination; // phpcs:ignore WordPress.Security.EscapeOutput -- core output. ?>
					</nav>
				<?php endif; ?>

			<?php else : ?>

				<div class="em-search__empty">
					<span class="em-search__empty-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24"><path d="M10.5 3a7.5 7.5 0 0 1 5.96 12.06l4.24 4.24a1 1 0 0 1-1.42 1.42l-4.24-4.24A7.5 7.5 0 1 1 10.5 3Zm0 2a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Z"/></svg>
					</span>
					<h2>Nothing found<?php echo $em_query ? ' for &ldquo;' . esc_html( $em_query ) . '&rdquo;' : ''; ?></h2>
					<p>Try a shorter or more general keyword, check the spelling, or browse one of these topics.</p>

					<?php em_search_form( $em_query, $em_post_type, 'empty' ); ?>

					<div class="em-search__tips">
						<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">All articles</a>
						<a href="<?php echo esc_url( home_url( '/email-data-extraction/' ) ); ?>">Email Data Extraction</a>
						<a href="<?php echo esc_url( home_url( '/email-automation/' ) ); ?>">Email Automation</a>
						<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">Pricing</a>
					</div>
				</div>

				<?php
				$em_recent = new WP_Query(
					array(
						'post_type'           => 'post',
						'posts_per_page'      => 3,
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);
				if ( $em_recent->have_posts() ) :
					?>
					<h2 class="em-search__section-title">Latest from the blog</h2>
					<div class="em-search__grid">
						<?php
						while ( $em_recent->have_posts() ) :
							$em_recent->the_post();
							em_search_card( array(), false, 'h3' );
						endwhile;
						?>
					</div>
					<?php
					wp_reset_postdata();
				endif;
				?>

			<?php endif; ?>

		</div>
	</section>
</div>

<?php get_footer(); ?>
