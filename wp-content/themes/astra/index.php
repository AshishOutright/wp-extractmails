<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<!-- Fonts from blog.html -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ==========================================================================
   CSS DIRECTLY FROM blog.html (Scoped to .index-view so Astra Header/Footer are untouched)
   ========================================================================== */
:root {
  --navy: #0B1745;
  --navy-secondary: #14285F;
  --blue: #2563EB;
  --blue-bright: #3B82F6;
  --background: #FFFFFF;
  --background-alt: #F4F7FC;
  --border: #DCE5F2;
  --text: #18233F;
  --text-muted: #64748B;
  --text-inverse: #FFFFFF;
  --blue-hover: #1d4ed8;
  --focus: rgba(59, 130, 246, 0.25);
  --shadow: 0 8px 28px rgba(11, 23, 69, 0.05);
  --shadow-hover: 0 16px 36px rgba(11, 23, 69, 0.10);
  --radius: 16px;
  --radius-sm: 8px;
  --width: 1600px;
  --font: 'DM Sans', 'Inter', 'Segoe UI', Arial, sans-serif;
}

.index-view {
  font-family: var(--font);
  color: var(--text);
  background: var(--background);
  padding-top: 90px;
  overflow-x: clip;
  -webkit-font-smoothing: antialiased;
}

.index-view *, .index-view *::before, .index-view *::after {
  box-sizing: border-box;
}

.index-view .container {
  width: min(var(--width), calc(100% - 48px));
  margin-inline: auto;
}

.index-view h1, .index-view h2, .index-view h3, .index-view p {
  margin: 0;
}

.index-view h1, .index-view h2, .index-view h3 {
  color: var(--navy);
  line-height: 1.2;
  letter-spacing: -.035em;
}

.index-view a {
  color: var(--blue);
  text-decoration: none;
  transition: color .2s;
}

.index-view a:hover {
  color: var(--navy);
}

.index-view button, .index-view select, .index-view input {
  font: inherit;
  transition: background-color .2s, border-color .2s, box-shadow .2s, color .2s;
}

.index-view button {
  cursor: pointer;
}

.index-view .button {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  gap: 9px;
  min-height: 46px;
  padding: 11px 24px;
  border: 1px solid var(--blue);
  border-radius: var(--radius-sm);
  font-weight: 650;
  font-size: .875rem;
  line-height: 1.3;
}

.index-view .button-primary {
  background: var(--blue);
  color: var(--text-inverse) !important;
}

.index-view .button-primary:hover {
  background: var(--blue-hover);
  color: var(--text-inverse) !important;
  border-color: var(--blue-hover);
}

/* Hero Section from blog.html */
.index-view .hero {
  position: relative;
  overflow: hidden;
  padding: 48px 54px 35px;
  border: 1px solid var(--navy-secondary);
  background: linear-gradient(114deg, var(--navy) 0%, var(--navy-secondary) 100%);
  border-radius: 20px;
  color: var(--text-inverse);
}

.index-view .hero-top {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 330px;
  gap: 45px;
  align-items: center;
}

.index-view .hero h1 {
  max-width: 690px;
  color: var(--text-inverse);
  font-size: clamp(2rem, 3.4vw, 3rem);
  line-height: 1.15;
  font-weight: 700;
  letter-spacing: -.045em;
}

.index-view .hero-description {
  margin-top: 19px;
  color: var(--border);
  font-size: .9375rem;
  line-height: 1.75;
  max-width: 560px;
}

.index-view .hero-art {
  overflow: hidden;
  background: var(--background-alt);
  border: 1px solid rgb(220 229 242 / 30%);
  border-radius: 15px;
  box-shadow: 0 14px 35px rgb(11 23 69 / 14%);
}

.index-view .hero-art img {
  width: 100%;
  height: auto;
  aspect-ratio: 101 / 65;
  object-fit: contain;
  display: block;
}

/* Search Form from blog.html */
.index-view .search-form {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-top: 35px;
  padding: 6px 6px 6px 20px;
  min-height: 60px;
  border-radius: 11px;
  background: var(--background);
  border: 1px solid var(--border);
  box-shadow: 0 8px 20px rgb(11 23 69 / 8%);
}

.index-view .search-form:focus-within {
  border-color: var(--blue-bright);
  box-shadow: 0 0 0 4px var(--focus);
}

.index-view .search-form .search-icon {
  width: 21px;
  height: 21px;
  flex: none;
  object-fit: contain;
}

.index-view .search-form input {
  min-width: 0;
  width: 100%;
  flex: 1;
  color: var(--text);
  background: var(--background);
  border: 0 !important;
  outline: none !important;
  box-shadow: none !important;
  padding: 8px 0;
  font-size: .9375rem;
}

.index-view .search-form input::placeholder {
  color: var(--text-muted);
  opacity: 1;
}

.index-view .search-form .button {
  min-width: 135px;
  align-self: stretch;
  border-radius: 7px;
  background: var(--blue);
  border-color: var(--blue);
}

.index-view .search-form .button:hover {
  background: var(--blue-hover);
  border-color: var(--blue-hover);
}

/* Blogs Section from blog.html */
.index-view .blogs-section {
  padding-top: 44px;
  padding-bottom: 54px;
}

.index-view .section-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

.index-view .section-heading h2 {
  font-size: 1.9rem;
  font-weight: 650;
  color: var(--navy);
}

.index-view .stats-pill-wrapper {
  padding: 2px;
  border-radius: 9999px;
  background: linear-gradient(90deg, #3b5cff 0%, #00c979 100%);
  display: inline-flex;
}

.index-view .stats-pill-inner {
  display: flex;
  align-items: center;
  background: #ffffff;
  border-radius: 9999px;
  padding: 6px 6px;
}

.index-view .stats-pill-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2px 20px;
  border-right: 1px solid #e5e7eb;
}

.index-view .stats-pill-item:last-child {
  border-right: 0;
}

.index-view .stats-pill-num {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0b1745;
  line-height: 1.15;
}

.index-view .stats-pill-label {
  font-size: 0.8125rem;
  font-weight: 500;
  color: #383cff;
  margin-top: 3px;
  line-height: 1.2;
  white-space: nowrap;
}

/* Filter Toolbar from blog.html */
.index-view .filter-toolbar {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 18px;
  margin: 23px 0 14px;
}

.index-view .category-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.index-view .category-button {
  padding: 8px 17px;
  min-height: 40px;
  border: 1px solid var(--border);
  border-radius: 7px;
  color: var(--text-muted);
  font-size: .875rem;
  font-weight: 550;
  background: var(--background);
  text-decoration: none !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all .2s;
}

.index-view .category-button:hover {
  color: var(--blue) !important;
  border-color: var(--blue);
  background: var(--background-alt);
}

.index-view .category-button.active,
.index-view .category-button[aria-pressed="true"] {
  background: var(--navy) !important;
  color: var(--text-inverse) !important;
  border-color: var(--navy);
}

.index-view .select-wrap {
  display: flex;
  align-items: center;
  position: relative;
}

.index-view .select-wrap select {
  appearance: none;
  cursor: pointer;
  padding: 10px 40px 10px 17px;
  border: 1px solid var(--border);
  border-radius: 7px;
  background: var(--background);
  color: var(--navy);
  font-size: .875rem;
  min-height: 41px;
  max-width: 100%;
}

.index-view .select-wrap svg {
  position: absolute;
  right: 13px;
  width: 15px;
  height: 15px;
  pointer-events: none;
  color: var(--blue);
}

/* Blog Grid & Article Cards from blog.html */
.index-view .blog-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 26px;
}

.index-view .article-card {
  min-width: 0;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  background: var(--background);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
}

.index-view .article-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
  border-color: color-mix(in srgb, var(--blue) 30%, var(--border));
}

.index-view .card-link {
  display: flex;
  flex-direction: column;
  height: 100%;
  color: var(--text);
  text-decoration: none !important;
}

.index-view .card-image {
  overflow: hidden;
  background: var(--background-alt);
  border-bottom: 1px solid var(--border);
}

.index-view .card-image img {
  width: 100%;
  height: auto;
  aspect-ratio: 65 / 37;
  object-fit: cover;
  display: block;
}

.index-view .card-content {
  padding: 23px 23px 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.index-view .card-category {
  align-self: flex-start;
  margin-bottom: 11px;
  color: var(--blue);
  font-size: .75rem;
  font-weight: 700;
  letter-spacing: .045em;
  line-height: 1.4;
}

.index-view .card-content h3 {
  font-size: 1.1875rem;
  font-weight: 650;
  letter-spacing: -.028em;
  line-height: 1.35;
  color: var(--navy);
  transition: color .2s;
}

.index-view .card-link:hover h3 {
  color: var(--blue);
}

.index-view .card-description {
  margin-top: 11px;
  margin-bottom: 20px;
  font-size: .875rem;
  color: var(--text-muted);
  line-height: 1.65;
}

.index-view .card-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-top: auto;
  padding-top: 15px;
  border-top: 1px solid var(--border);
}

.index-view .author-mini {
  display: flex;
  align-items: center;
  gap: 9px;
  color: var(--text-muted);
  font-size: .75rem;
}

.index-view .author-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
}

.index-view .card-read {
  font-size: .75rem;
  color: var(--text-muted);
  white-space: nowrap;
}

/* Pagination from blog.html */
.index-view .pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 7px;
  margin-top: 40px;
}

.index-view .pagination ul {
  display: flex;
  align-items: center;
  gap: 7px;
  list-style: none;
  padding: 0;
  margin: 0;
}

.index-view .pagination li {
  list-style: none;
}

.index-view .pagination a,
.index-view .pagination span {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  min-height: 40px;
  padding: 7px 10px;
  color: var(--text-muted);
  background: var(--background);
  border: 1px solid var(--border);
  border-radius: 7px;
  font-size: .875rem;
  text-decoration: none;
  transition: all .2s;
}

.index-view .pagination a:hover {
  color: var(--blue);
  border-color: var(--blue);
}

.index-view .pagination .current {
  color: var(--text-inverse) !important;
  background: var(--blue) !important;
  border-color: var(--blue);
  box-shadow: 0 4px 10px rgb(37 99 235 / 16%);
}

.index-view .pagination .prev,
.index-view .pagination .next {
  padding-inline: 15px;
}

/* Popular Section from blog.html */
.index-view .popular-section {
  padding: 45px 0 60px;
  background: var(--background-alt);
  border-top: 1px solid var(--border);
  margin-top: 50px;
}

.index-view .popular-section .section-heading {
  margin-bottom: 29px;
  align-items: flex-end;
}

.index-view .popular-section .section-heading p {
  color: var(--text);
  font-size: .875rem;
  max-width: 360px;
  text-align: right;
}

.index-view .popular-card .date-badge {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(4px);
  color: var(--text);
  font-size: 0.75rem;
  padding: 4px 10px;
  border-radius: 9999px;
  border: 1px solid var(--border);
  font-weight: 550;
}

/* Responsive Rules */
@media (max-width: 1200px) {
  .index-view { padding-top: 86px; }
  .index-view .hero { padding: 40px 38px 32px; }
  .index-view .hero-top { grid-template-columns: minmax(0, 1fr) 300px; gap: 32px; }
  .index-view .hero h1 { font-size: 2.5rem; }
  .index-view .blog-grid { gap: 22px; }
}

@media (max-width: 991px) {
  .index-view { padding-top: 82px; }
  .index-view .container { width: calc(100% - 36px); }
  .index-view .hero { padding: 34px 28px 28px; }
  .index-view .hero-top { grid-template-columns: 1fr; gap: 26px; }
  .index-view .hero-art { max-width: 280px; justify-self: center; margin-inline: auto; }
  .index-view .hero h1 { font-size: 2.1rem; }
  .index-view .hero-description { font-size: .875rem; margin-top: 14px; }
  .index-view .search-form { margin-top: 26px; }
  .index-view .section-heading { flex-direction: column; align-items: flex-start; gap: 16px; }
  .index-view .stats-pill-wrapper { width: auto; max-width: 100%; border-radius: 9999px; }
  .index-view .stats-pill-inner { padding: 6px 12px; }
  .index-view .stats-pill-item { padding: 2px 14px; }
  .index-view .stats-pill-num { font-size: 1.1rem; }
  .index-view .stats-pill-label { font-size: 0.78rem; }
  .index-view .blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
  .index-view .popular-section .blog-grid .article-card:last-child { grid-column: 1 / -1; max-width: calc(50% - 10px); }
}

@media (max-width: 640px) {
  .index-view { padding-top: 76px; }
  .index-view .container { width: calc(100% - 24px); }
  .index-view .hero { padding: 26px 18px 20px; border-radius: 16px; }
  .index-view .hero-top { grid-template-columns: 1fr; gap: 20px; }
  .index-view .hero h1 { font-size: clamp(1.65rem, 6.5vw, 2rem); max-width: 100%; }
  .index-view .hero-description { font-size: .875rem; line-height: 1.65; }
  .index-view .hero-art { width: 100%; max-width: 210px; justify-self: center; margin-inline: auto; border-radius: 10px; }
  .index-view .search-form { padding-left: 12px; gap: 8px; min-height: 52px; margin-top: 22px; }
  .index-view .search-form .button { min-width: 78px; padding-inline: 14px; font-size: .8125rem; }
  .index-view .search-form input { font-size: .875rem; }
  .index-view .blogs-section { padding-top: 32px; padding-bottom: 38px; }
  .index-view .section-heading { flex-direction: column; align-items: flex-start; gap: 14px; }
  .index-view .section-heading h2 { font-size: 1.55rem; }
  .index-view .stats-pill-wrapper { width: 100%; border-radius: 16px; }
  .index-view .stats-pill-inner { width: 100%; border-radius: 14px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px 0; padding: 10px 6px; }
  .index-view .stats-pill-item { border-right: none; padding: 4px; }
  .index-view .stats-pill-item:nth-child(1),
  .index-view .stats-pill-item:nth-child(3) { border-right: 1px solid #e5e7eb; }
  .index-view .filter-toolbar { gap: 14px; margin-top: 18px; }
  .index-view .category-list { gap: 6px; }
  .index-view .category-button { font-size: .8125rem; padding: 7px 11px; }
  .index-view .select-wrap { width: 100%; }
  .index-view .select-wrap select { width: 100%; min-height: 38px; font-size: .8125rem; padding-top: 8px; padding-bottom: 8px; }
  .index-view .blog-grid { grid-template-columns: 1fr; gap: 20px; }
  .index-view .card-content { padding: 18px; }
  .index-view .popular-section { padding: 30px 0 36px; }
  .index-view .popular-section .section-heading { flex-direction: column; align-items: flex-start; gap: 10px; margin-bottom: 20px; }
  .index-view .popular-section .section-heading p { text-align: left; max-width: 100%; }
  .index-view .popular-section .blog-grid .article-card:last-child { grid-column: auto; max-width: none; }
}
</style>

<div class="index-view">
  <div class="container">

    <!-- ================= HERO SECTION (blog.html layout) ================= -->
    <section class="hero" aria-labelledby="index-title">
      <div class="hero-top">
        <div>
          <h1 id="index-title">Email Insights, Guides &amp;<br class="desktop-break"> Automation Tips</h1>
          <p class="hero-description">Explore expert blogs on email extraction, marketing, Gmail management, and automation to streamline your workflow and grow your business.</p>
        </div>
        <div class="hero-art">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Mar-31-2026-01_32_30-PM-1.png" alt="Blog Illustration">
        </div>
      </div>

      <!-- Search Form -->
      <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/search-1-1.png" alt="" class="search-icon">
        <input type="search" name="s" placeholder="Search the blog..." value="<?php echo get_search_query(); ?>">
        <input type="hidden" name="post_type" value="post">
        <button class="button button-primary" type="submit">Search</button>
      </form>
    </section>

    <!-- ================= BLOGS SECTION (blog.html layout) ================= -->
    <section class="blogs-section" aria-labelledby="blogs-heading">
      
      <!-- Section Heading with Stats Pill (matching Image 1) -->
      <div class="section-heading">
        <h2 id="blogs-heading">Our Blogs</h2>
        <div class="stats-pill-wrapper" aria-label="Blog statistics">
          <div class="stats-pill-inner">
            <!-- ITEM: Total Blogs -->
            <div class="stats-pill-item">
              <span class="stats-pill-num">
                <?php
                  $post_count = wp_count_posts();
                  echo esc_html($post_count->publish ?? 0);
                ?>
              </span>
              <span class="stats-pill-label">Total Blogs</span>
            </div>

            <!-- ITEM: Last 7 Days -->
            <div class="stats-pill-item">
              <span class="stats-pill-num">
                <?php
                  $week_count = new WP_Query([
                      'date_query' => [
                          ['after' => date('Y-m-d', strtotime('-7 days'))]
                      ],
                      'post_type' => 'post',
                      'post_status' => 'publish',
                      'fields' => 'ids',
                      'posts_per_page' => -1
                  ]);
                  echo esc_html($week_count->found_posts);
                  wp_reset_postdata();
                ?>
              </span>
              <span class="stats-pill-label">Last 7 Days</span>
            </div>

            <!-- ITEM: This Year -->
            <div class="stats-pill-item">
              <span class="stats-pill-num">
                <?php
                  $this_year_count = new WP_Query([
                      'date_query' => [
                          ['after' => date('Y-01-01')]
                      ],
                      'post_type' => 'post',
                      'post_status' => 'publish',
                      'fields' => 'ids',
                      'posts_per_page' => -1
                  ]);
                  echo esc_html($this_year_count->found_posts);
                  wp_reset_postdata();
                ?>
              </span>
              <span class="stats-pill-label">This Year</span>
            </div>

            <!-- ITEM: Last Year -->
            <div class="stats-pill-item">
              <span class="stats-pill-num">
                <?php
                  $last_year = date('Y') - 1;
                  $last_year_count = new WP_Query([
                      'date_query' => [
                          [
                              'after'     => "$last_year-01-01",
                              'before'    => "$last_year-12-31",
                              'inclusive' => true
                          ]
                      ],
                      'post_type' => 'post',
                      'post_status' => 'publish',
                      'fields' => 'ids',
                      'posts_per_page' => -1
                  ]);
                  echo esc_html($last_year_count->found_posts);
                  wp_reset_postdata();
                ?>
              </span>
              <span class="stats-pill-label">Last Year</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Toolbar -->
      <div class="filter-toolbar">
        <div class="category-list">
          <?php $is_all_active = (is_home() && !is_category()); ?>
          <a href="<?php echo home_url('/blog'); ?>" class="category-button <?php echo $is_all_active ? 'active' : ''; ?>">All</a>

          <?php $cat_email = get_category_by_slug('Email') ?: get_category_by_slug('email'); ?>
          <?php if ($cat_email) : ?>
            <a href="<?php echo get_category_link($cat_email); ?>" class="category-button <?php echo is_category($cat_email->term_id) ? 'active' : ''; ?>">Email</a>
          <?php endif; ?>

          <?php $cat_tools = get_category_by_slug('Email Extraction Tools') ?: get_category_by_slug('email-extraction-tools'); ?>
          <?php if ($cat_tools) : ?>
            <a href="<?php echo get_category_link($cat_tools); ?>" class="category-button <?php echo is_category($cat_tools->term_id) ? 'active' : ''; ?>">Email Extraction Tools</a>
          <?php endif; ?>

          <?php $cat_business = get_category_by_slug('Business') ?: get_category_by_slug('business'); ?>
          <?php if ($cat_business) : ?>
            <a href="<?php echo get_category_link($cat_business); ?>" class="category-button <?php echo is_category($cat_business->term_id) ? 'active' : ''; ?>">Business</a>
          <?php endif; ?>

          <?php $cat_ai = get_category_by_slug('ai') ?: get_category_by_slug('AI'); ?>
          <?php if ($cat_ai) : ?>
            <a href="<?php echo get_category_link($cat_ai); ?>" class="category-button <?php echo is_category($cat_ai->term_id) ? 'active' : ''; ?>">AI</a>
          <?php endif; ?>
        </div>

        <!-- Category Dropdown Select -->
        <div class="select-wrap">
          <form action="<?php echo esc_url(home_url('/')); ?>" method="get" id="category-filter-form">
            <?php
              wp_dropdown_categories(array(
                'show_option_all' => 'All categories',
                'orderby'         => 'name',
                'order'           => 'ASC',
                'hide_empty'      => 0,
                'taxonomy'        => 'category',
                'value_field'     => 'term_id',
                'hierarchical'    => true,
                'name'            => 'cat',
                'id'              => 'category-dropdown',
                'selected'        => get_queried_object_id(),
              ));
            ?>
          </form>
          <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"></path></svg>
        </div>
      </div>

      <script>
        document.getElementById('category-dropdown')?.addEventListener('change', function () {
          this.form.submit();
        });
      </script>

      <!-- ================= BLOG GRID ================= -->
      <div class="blog-grid" id="articlesGrid">
        <?php if (have_posts()) : ?>
          <?php while (have_posts()) : the_post(); ?>
            <article class="article-card">
              <a class="card-link" href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()) : ?>
                  <div class="card-image">
                    <?php the_post_thumbnail('medium_large'); ?>
                  </div>
                <?php endif; ?>
                <div class="card-content">
                  <?php
                    $post_cats = get_the_category();
                    if (!empty($post_cats)) :
                  ?>
                    <span class="card-category"><?php echo esc_html($post_cats[0]->name); ?></span>
                  <?php endif; ?>

                  <h3><?php echo wp_trim_words(get_the_title(), 7, '...'); ?></h3>
                  <p class="card-description"><?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?></p>
                  
                  <div class="card-meta">
                    <span class="author-mini">
                      <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Generic-avatar-3.png" class="author-avatar" alt="Author avatar">
                      <span>Editorial Team</span>
                    </span>
                    <span class="card-read"><?php echo get_the_date('j M Y'); ?></span>
                  </div>
                </div>
              </a>
            </article>
          <?php endwhile; ?>
        <?php else : ?>
          <p style="grid-column: 1 / -1; padding: 40px; text-align: center; color: var(--text-muted);">No posts found.</p>
        <?php endif; ?>
      </div>

      <!-- ================= PAGINATION ================= -->
      <nav class="pagination" aria-label="Blog pagination">
        <?php
        echo paginate_links(array(
            'total'      => $GLOBALS['wp_query']->max_num_pages,
            'current'    => max(1, get_query_var('paged')),
            'mid_size'   => 2,
            'prev_text'  => '‹ Previous',
            'next_text'  => 'Next ›',
            'type'       => 'list',
        ));
        ?>
      </nav>

    </section>
  </div>

  <!-- ================= POPULAR SECTION (blog.html layout) ================= -->
  <section class="popular-section" aria-labelledby="popular-heading">
    <div class="container">
      <div class="section-heading">
        <h2 id="popular-heading">Popular Blogs</h2>
        <p>Our most popular insights on email marketing, automation, and business communication.</p>
      </div>

      <div class="blog-grid" id="popular-grid">
        <?php
        $args = array(
          'post_type'      => 'post',
          'posts_per_page' => 3,
          'post_status'    => 'publish'
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) :
          while ($query->have_posts()) : $query->the_post();
        ?>
          <article class="article-card popular-card">
            <a class="card-link" href="<?php the_permalink(); ?>">
              <?php if (has_post_thumbnail()) : ?>
                <div class="card-image" style="position: relative;">
                  <?php the_post_thumbnail('large'); ?>
                  <span class="date-badge"><?php echo get_the_date('d M, Y'); ?></span>
                </div>
              <?php endif; ?>
              <div class="card-content">
                <h3><?php echo wp_trim_words(get_the_title(), 6, '...'); ?></h3>
                <p class="card-description"><?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?></p>
                <div class="card-meta">
                  <span class="author-mini">
                    <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Generic-avatar-3.png" class="author-avatar" alt="Author avatar">
                    <span><?php the_author(); ?></span>
                  </span>
                </div>
              </div>
            </a>
          </article>
        <?php
          endwhile;
          wp_reset_postdata();
        endif;
        ?>
      </div>
    </div>
  </section>

</div><!-- .index-view -->

<?php get_footer(); ?>