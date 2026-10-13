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


<section class="max-w-[1600px] mx-auto px-6 pt-20">

  <!-- MAIN CONTAINER -->
 <div class="relative rounded-[32px] overflow-hidden 
           
             relative w-full py-2 px-2 md:py-12  md:px-12">



            
<div class="absolute w-[315.56px] h-[161.15px] left-[1358.32px] top-[230.31px] bg-[#98b0ff] blur-[107px] origin-top-left rotate-[-18.579deg]"></div>

<div class="absolute w-[515.56px] h-[161.15px] left-[158.32px] top-[390.31px] bg-[#98b0ff] blur-[107px] origin-top-left rotate-[-18.579deg]"></div>



<!-- <div class="absolute w-[525.08px] h-[82.15px] left-[185.53px] top-[766.46px] bg-[#00c979] blur-[47px] origin-top-left rotate-[-9.961deg]"></div>

<div class="absolute w-[525.08px] h-[74.61px] left-[218px] top-[1658.83px] bg-[#00c979] blur-[47px] origin-top-left rotate-[-9.961deg]"></div>

<div class="absolute w-[153.89px] h-[55.97px] left-[-56px] top-[847.02px] bg-[#00c979] blur-[81px] origin-top-left rotate-[-9.961deg]"></div> -->

<div class="absolute w-[519.35px] h-[94.17px] left-[-219.02px] top-[-118.97px] bg-[#00c979] blur-[147px] origin-top-left rotate-[48.581deg]"></div>



<div class="absolute w-[315.56px] h-[161.15px] left-[758.32px] top-[0.31px] bg-[#00c979] blur-[147px] origin-top-left rotate-[-18.579deg]"></div>
<div class="absolute w-[315.56px] h-[161.15px] left-[1058.32px] top-[440.31px] bg-[#00c979] blur-[157px] origin-top-left rotate-[-18.579deg]"></div>
    <!-- LEFT CONTENT -->
       <div class="relative flex flex-col md:flex-row items-center">

      <!-- LEFT CONTENT -->
      <div class="w-full md:w-1/2">

        <h1 class="text-[32px] md:text-[48px] font-semibold text-[#0f172a] leading-tight">
          Email Insights, Guides & <br> Automation Tips
        </h1>

        <p class="mt-5 text-[16px] text-gray-600 max-w-[520px] leading-relaxed">
          Explore expert blogs on email extraction, marketing, Gmail management, and
          automation to streamline your workflow and grow your business.
        </p>

      </div>

      <!-- RIGHT IMAGE -->
      <div class="w-full md:w-1/2 flex justify-center md:justify-end mt-10 md:mt-0">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Mar-31-2026-01_32_30-PM-1.png"
             class="w-[320px] md:w-[620px] object-contain"
             alt="Blog Illustration">
      </div>

    </div>

    <!-- SEARCH BAR (FULL WIDTH LIKE IMAGE) -->
    <div class="relative flex justify-center max-w-[1100px] m-auto">

     <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
      class="flex items-center w-full bg-white border border-gray-200 rounded-full px-6 py-3 shadow-sm">

      <!-- Icon -->
     <img src="https://www.extractmails.com/wp-content/uploads/2026/04/search-1-1.png" alt="">

      <!-- Input -->
      <input type="search" name="s"  class="searchbox" 
  placeholder="Search the blog..."
  class="flex-1 bg-transparent outline-none border-none ring-0 focus:outline-none focus:ring-0 focus:border-none text-sm text-gray-600">

      <!-- Button -->
      <button type="submit"
        class="ml-3 bg-[#1a237e]  text-white text-sm font-medium px-5 py-1.5 rounded-full transition">
        Search
      </button>
    </form>

    </div>

  </div>

  </div>
</section>





<style>
  .searchbox:focus{outline: none; border: none; box-shadow: none; }
  .searchbox{outline: none; border: none !important; box-shadow: none !important; background: transparent;}
</style>





<section class="max-w-[1500px] mx-auto px-6 pt-10 pb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-10">

  <!-- LEFT SIDE -->
<div class="md:w-[60%]">
  <h2 class="text-4xl mb-6 bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">
    Our Blogs
  </h2>

  <div class="flex flex-wrap items-center gap-3">

    <!-- ALL -->
    <a href="<?php echo home_url('/blog'); ?>" 
       class="px-5 py-2 rounded-[14px] text-sm font-medium bg-[#1a237e] text-white">
      All
    </a>

    <a href="<?php echo get_category_link(get_category_by_slug('Email')); ?>" 
       class="px-5 py-2 rounded-[14px] text-sm font-medium border border-[#1a237e] text-[#1a237e] hover:bg-[#1a237e] hover:text-white transition">
      Email
    </a>

    <!-- SEO -->
    <a href="<?php echo get_category_link(get_category_by_slug('Email Extraction Tools')); ?>" 
       class="px-5 py-2 rounded-[14px] text-sm font-medium border border-[#1a237e] text-[#1a237e] hover:bg-[#1a237e] hover:text-white transition">
      Email Extraction Tools
    </a>

    

    <!-- IoT -->
    <a href="<?php echo get_category_link(get_category_by_slug('Business')); ?>" 
       class="px-5 py-2 rounded-[14px] text-sm font-medium border border-[#1a237e] text-[#1a237e] hover:bg-[#1a237e] hover:text-white transition">
      Business
    </a>

    <!-- AI Tools -->
    <!-- <a href="<?php echo get_category_link(get_category_by_slug('ai-tools')); ?>" 
       class="px-5 py-2 rounded-[14px] text-sm font-medium border border-[#1a237e] text-[#1a237e] hover:bg-[#1a237e] hover:text-white transition">
      AI Tools
    </a> -->

    <!-- AI -->
    <a href="<?php echo get_category_link(get_category_by_slug('ai')); ?>" 
       class="px-5 py-2 rounded-[14px] text-sm font-medium border border-[#1a237e] text-[#1a237e] hover:bg-[#1a237e] hover:text-white transition">
      AI
    </a>

  </div>
</div>


  <!-- RIGHT SIDE -->
  <div class="md:w-[55%] flex flex-col items-end gap-4 ">

    <!-- Search Bar -->
   


    
        <div class="p-[2px] rounded-[36px] 
            bg-gradient-to-r from-[#3b5cff] to-[#00c979]">

  <!-- INNER CONTENT -->
  <div class="grid grid-cols-2 md:grid-cols-4 text-center 
              bg-white px-6 py-2  rounded-[34px]">

          <!-- ITEM -->
          <div class=" px-4 md:px-4 md:border-r border-gray-200">
            <p class="text-[14px] md:text-[18px] font-semibold text-[#1e2a78]">
              <?php echo wp_count_posts()->publish; ?>
            </p>
            <p class="text-[16px] text-gray-600 mt-1  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">Total Blogs</p>
          </div>

          <!-- ITEM -->
          <div class="md:border-r border-gray-200 ">
            <p class="text-[14px] md:text-[18px] font-semibold text-[#1e2a78]">
              <?php
              $week = new WP_Query([
                'date_query' => [['after' => '7 days ago']],
                'post_type' => 'post',
                'post_status' => 'publish',
                'fields' => 'ids'
              ]);
              echo $week->found_posts;
              wp_reset_postdata();
              ?>
            </p>
            <p class="text-[16px] text-gray-600 mt-1  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">Last 7 Days</p>
          </div>

          <!-- ITEM -->
          <div class="md:border-r border-gray-200">
            <p class="text-[14px] md:text-[18px] font-semibold text-[#1e2a78]">
              <?php
              $year = new WP_Query([
                'date_query' => [['after' => date('Y-01-01')]],
                'post_type' => 'post',
                'post_status' => 'publish',
                'fields' => 'ids'
              ]);
              echo $year->found_posts;
              wp_reset_postdata();
              ?>
            </p>
            <p class="text-[16px] text-gray-600 mt-1  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">This Year</p>
          </div>

          <!-- ITEM -->
          <div>
            <p class="text-[14px] md:text-[18px] font-semibold text-[#1e2a78]">
              <?php
              $last_year = date('Y') - 1;
              $last = new WP_Query([
                'date_query' => [[
                  'after' => "$last_year-01-01",
                  'before' => "$last_year-12-31",
                  'inclusive' => true
                ]],
                'post_type' => 'post',
                'post_status' => 'publish',
                'fields' => 'ids'
              ]);
              echo $last->found_posts;
              wp_reset_postdata();
              ?>
            </p>
            <p class="text-[16px] text-gray-600 mt-1  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">Last Year</p>
          </div>

        </div>
         </div>

     



       <style>
        /* Style the dropdown like the image */
#category-dropdown {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;

  background: linear-gradient(180deg, #0e107b, #2d31dc)!important;
  color: #fff;
  font-weight: 600;

  border: none;
  border-radius: 50px;
  padding: 6px 45px 10px 20px;

  cursor: pointer;
  outline: none;

  /* FIXED SVG (encoded) */
  background-image: url("data:image/svg+xml,%3Csvg fill='white' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5 7l5 5 5-5z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 15px center;
  background-size: 16px;
}

#category-dropdown::-ms-expand {
  display: none;
}

/* Custom arrow */
#category-dropdown {
  background-image: url("data:image/svg+xml;utf8,<svg fill='white' height='20' viewBox='0 0 20 20' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M5 7l5 5 5-5z'/></svg>");
  background-repeat: no-repeat;
  background-position: right 15px center;
  background-size: 16px;
}

#category-dropdown option {
  color: #000;
}

#category-dropdown option:first-child {
  color: #fff;
}

/* Remove default arrow in IE */
#category-dropdown::-ms-expand {
  display: none;
}
      </style>

    <!-- Category Dropdown Button -->
   <div class="relative inline-block">

  <form action="<?php echo esc_url(home_url('/')); ?>" method="get">
    <?php
      wp_dropdown_categories(array(
        'show_option_all' => 'All Categories',
        'orderby'         => 'name',
        'order'           => 'ASC',
        'hide_empty'      => 0,
        'taxonomy'        => 'category',
        'value_field'     => 'term_id',
        'hierarchical'    => true,
        'name'            => 'cat',
        'id'              => 'category-dropdown',
        'class'           => 'appearance-none rounded-full shadow-sm border-0 bg-gradient-to-r from-[#1a237e] to-[#3b5cff] text-white px-6 py-2 pr-10',
      ));
    ?>
  </form>

  <!-- CUSTOM ICON -->
  <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M6 9l6 6 6-6"></path>
    </svg>
  </div>

</div>

      <script>
  document.getElementById('category-dropdown').addEventListener('change', function () {
    this.form.submit();
  });
</script>

  </div>

</section>


<!-- ================= BLOG GRID ================= -->


 <!-- ================= BLOG GRID ================= -->
<section class="max-w-[1500px] mx-auto px-4 ">

<div id="articlesGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>

<article class="transition duration-300">

    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('medium_large', ['class' => 'rounded-2xl w-full h-60 object-cover']); ?>
        </a>
    <?php endif; ?>

    <div class="p-5">

        <h3 class="font-semibold text-[25px] mb-2 leading-snug">
            <a href="<?php the_permalink(); ?>" class="text-gray-900 hover:text-blue-600 transition">
                <?php echo wp_trim_words(get_the_title(), 4, '...'); ?>
            </a>
        </h3>

        <p class="text-gray-500 text-sm mb-4">
            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
        </p>

        <div class="flex items-center gap-2 text-gray-500 text-sm">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Generic-avatar-3.png" class="w-6 h-6 rounded-full">
            <span>Editorial Team</span>
            <span>•</span>
            <span><?php echo get_the_date('j M Y'); ?></span>
        </div>

    </div>

</article>

<?php endwhile; ?>
<?php else : ?>
<p>No posts found.</p>
<?php endif; ?>

</div>

<!-- ================= PAGINATION ================= -->
<div class="outright-pagination flex justify-center mt-14">

<?php
echo paginate_links(array(
    'total'      => $GLOBALS['wp_query']->max_num_pages,
    'current'    => max(1, get_query_var('paged')),
    'mid_size'   => 2,
    'prev_text'  => '‹',
    'next_text'  => '›',
    'type'       => 'list',
));
?>

</div>

</section>


<style>
  /* ===== OUTRIGHT PAGINATION DESIGN ===== */

.outright-pagination{
    display:flex;
    justify-content:center;
    margin-top:60px;
}

.outright-pagination ul{
    display:flex;
    align-items:center;
    gap:14px;
    padding:14px 22px;
    background:#fff;
    border-radius:40px;
    box-shadow:0 8px 18px rgba(0,0,0,0.12);
    list-style:none;
    margin:0;
}

.outright-pagination li{
    list-style:none;
}

.outright-pagination a,
.outright-pagination span{
    text-decoration:none;
    color:#111;
    font-weight:500;
    font-size:16px;
    padding:8px 12px;
    border-radius:50%;
    transition:all .25s ease;
}

.outright-pagination a:hover{
    background:#f3f4f6;
}

.outright-pagination .current{
    background:#000;
    color:#fff;
    width:38px;
    height:38px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    font-weight:600;
}

.outright-pagination .prev,
.outright-pagination .next{
    border-radius:20px;
    padding:8px 14px;
}

</style>








<!-- ================= CATEGORY BUTTON JS ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

  const buttons = document.querySelectorAll(".category-btn");

  buttons.forEach(button => {
    button.addEventListener("click", function () {

      buttons.forEach(btn => btn.classList.remove("active"));
      this.classList.add("active");

      const link = this.getAttribute("data-link");
      if(link){
        window.location.href = link;
      }
    });
  });

});
</script>




<section class="max-w-[1500px] mx-auto px-6 py-10 ">

  <!-- Header -->
  <div class="flex justify-between items-start mb-10 border-t py-10">
    <h2 class="text-3xl font-semibold text-[#2d3dbf]">Popular Blogs</h2>

    <p class="text-sm text-gray-600 max-w-md text-right">
      Our Popular blog post is live—packed with fresh insight and inspiration.
      Take a quick read and join the conversation!
    </p>
  </div>

  <!-- Blog Grid -->
  <div class="grid md:grid-cols-3 gap-8">

    <?php
    $args = array(
      'post_type' => 'post',
      'posts_per_page' => 3,
      'post_status' => 'publish'
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
      while ($query->have_posts()) : $query->the_post();
    ?>

    <!-- Card -->
    <div class="group">

      <!-- Thumbnail -->
      <div class="relative overflow-hidden rounded-2xl mb-4">
        <a href="<?php the_permalink(); ?>">
          <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('large', ['class' => 'w-full h-[220px] object-cover group-hover:scale-105 transition duration-300']); ?>
          <?php endif; ?>
        </a>

        <!-- Date Badge -->
        <span class="absolute bottom-3 right-3 bg-white/80 text-xs px-3 py-1 rounded-full text-gray-600">
          <?php echo get_the_date('d M, Y'); ?>
        </span>
      </div>

      <!-- Title -->
      <h3 class="text-lg font-semibold text-gray-800 leading-snug mb-2">
        <a href="<?php the_permalink(); ?>" class="hover:text-[#2d3dbf] transition">
          <?php echo wp_trim_words(get_the_title(), 5 , '...'); ?>
        </a>
      </h3>

      <!-- Excerpt -->
      <p class="text-sm text-gray-500 mb-4">
        <?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?>
      </p>

      <!-- Author -->
      <div class="flex items-center gap-2">
       <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Generic-avatar-3.png" alt="">
        <span class="text-sm text-gray-700">
          <?php the_author(); ?>
        </span>
      </div>

    </div>

    <?php
      endwhile;
      wp_reset_postdata();
    endif;
    ?>

  </div>
  

</section>













<?php get_footer() ?>