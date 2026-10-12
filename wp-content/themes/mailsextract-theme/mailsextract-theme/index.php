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









<style>
@keyframes float {
  0%,100%{
    transform:translateY(0px);
  }
  50%{
    transform:translateY(-12px);
  }
}

@keyframes floatSlow {
  0%,100%{
    transform:translateY(0px);
  }
  50%{
    transform:translateY(-20px);
  }
}

.float{
  animation:float 4s ease-in-out infinite;
}

.float-slow{
  animation:floatSlow 6s ease-in-out infinite;
}

.hero-image{
  animation:floatSlow 8s ease-in-out infinite;
}
</style>

</head>



<section class="max-w-[1600px] mx-auto px-6 lg:px-10 py-24">

  <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-0 items-center">

    <!-- LEFT CONTENT -->
    <div >

      <h1 class="text-2xl lg:text-7xl font-semibold leading-tight text-black mt-8">
        Insights on Email
        <br>
        Automation and
        <span class="text-[#f45d2d]">
          Work flow
        </span>
   
        <span class="text-[#f45d2d]">
          Optimization
        </span>
      </h1>

      <p class="mt-8 text-gray-700 text-lg leading-relaxed max-w-xl">
        Stay ahead with blogs, practical guides, industry trends,
        and best practices for email parsing, data extraction,
        CRM automation, and workflow optimization.

        Our resources help you work smarter with the latest
        articles, whether you're looking to automate repetitive
        tasks, improve operational efficiency, or unlock hidden
        value in your inbox.
      </p>

      <button
        class="mt-10 bg-black text-white px-8 py-4 rounded-full font-medium shadow-lg hover:scale-105 transition duration-300"
      >
        Explore Articles
      </button>

    </div>

    <!-- RIGHT SIDE -->
    <div class="relative flex justify-center">

     <div class="relative w-full max-w-[650px] h-[500px]">

  <!-- Main Background Card -->
  <div
    class="absolute inset-0  rounded-[40px] overflow-hidden mt-12"
  >

    <!-- Person Image -->
    <img
      src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-170.png"
      alt=""
      class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[100%] w-[100%] object-contain z-10 "
    />

  </div>

  <!-- CRM Badge -->
  <div
    class="absolute left-4 top-36  flex items-center gap-3 z-20 float"
  >
   <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-171.png" alt="">
  </div>

  <!-- IoT Badge -->
  <div
    class="absolute right-4 top-20   flex items-center gap-3 z-20 float-slow"
  >
    <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-174-1.png" alt="">
  </div>

  <!-- AI Badge -->
  <div
    class="absolute right-4 bottom-20   flex items-center gap-3 z-20 float"
  >
    <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-173.png" alt="">
  </div>

</div>
    </div>

  </div>

</section>








<style>
  
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-container {
    animation: scaleIn .8s ease forwards;
}

.animate-header {
    opacity: 0;
    animation: fadeUp .8s ease forwards;
}

.animate-featured {
    opacity: 0;
    animation: fadeLeft 1s ease .3s forwards;
}

.animate-card {
    opacity: 0;
}
</style>





<section class="max-w-[1600px] mx-auto md:px-6 px-2 py-0">

<div class="bg-[#ff4b22] rounded-[40px] p-0 lg:p-14 shadow-xl animate-container">



    <!-- Header -->
    <div class="relative pb-10 animate-header">

    <div class="grid lg:grid-cols-2 gap-10 items-start p-4">
        <h2 class="text-white text-4xl lg:text-5xl font-bold leading-tight">
            Popular Resources and <br>
            Expert Insights
        </h2>

        <p class="text-white/95 text-lg leading-relaxed">
             Explore the most valuable content and stay informed with
            featured articles on email parsing, automation technologies,
            operational efficiency, and digital transformations. Learn
            from real-world use cases, best practices, and actionable
            solutions that drive business growth.
        </p>
    </div>

    <!-- Custom Border -->
    <div class="absolute bottom-0 left-0 w-full h-[2px]">
        <div
            class="w-full h-full rounded-full"
            style="
                background: linear-gradient(
                    90deg,
                    rgba(255,255,255,0.05) 0%,
                    rgba(255,255,255,0.12) 20%,
                    rgba(255,255,255,0.55) 50%,
                    rgba(255,255,255,0.12) 80%,
                    rgba(255,255,255,0.05) 100%
                );
            "
        ></div>
    </div>

</div>

    <?php
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'post_status'    => 'publish'
    ];

    $query = new WP_Query($args);

    if($query->have_posts()) :

        $query->the_post();
    ?>

    <!-- Content -->
    <div class="grid lg:grid-cols-[1.1fr_.9fr] gap-8 mt-10">

        <!-- Featured Blog -->
        <a href="<?php the_permalink(); ?>"
           class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:-translate-y-2 transition duration-300  animate-header">

            <div class="relative">

                <?php if(has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail(
                        'large',
                        [
                            'class' =>
                            'w-full h-[320px] object-cover group-hover:scale-105 transition duration-500'
                        ]
                    ); ?>
                <?php endif; ?>

                <span class="absolute top-4 left-4 bg-white px-3 py-1 rounded-full text-xs font-medium">
                    AI & Automation
                </span>

            </div>

            <div class="p-6">

                <h3 class="text-3xl font-semibold text-gray-900 mb-4 leading-snug">
                    <?php the_title(); ?>
                </h3>

                <p class="text-gray-500 leading-relaxed mb-6">
                    <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                </p>

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <img
                            src="https://www.mailsextract.com/wp-content/uploads/2026/06/Generic-avatar-4.png"
                            class="w-10 h-10 rounded-full"
                        >

                        <span class="text-sm text-gray-700">
                            <?php the_author(); ?>
                        </span>

                    </div>

                    <span class="text-sm text-blue-500">
                        <?php echo get_the_date('d-m-Y'); ?>
                    </span>

                </div>

            </div>

        </a>

        <!-- Side Blogs -->
        <div class="space-y-5">

            <?php
            while($query->have_posts()) :
                $query->the_post();
            ?>

           <a href="<?php the_permalink(); ?>"
   class="group bg-white rounded-2xl shadow-lg p-3 flex flex-col md:flex-row gap-4 hover:translate-x-2 transition duration-300">

                <div class="w-full md:w-[140px] shrink-0 overflow-hidden rounded-xl">

                    <?php if(has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail(
                            'medium',
                            [
                                'class' =>
                                'w-full h-[90px] object-cover group-hover:scale-110 transition duration-500'
                            ]
                        ); ?>
                    <?php endif; ?>

                </div>

                <div class="flex-1">

                    <h4 class="font-semibold text-xl text-gray-900 mb-2 leading-snug">
                        <?php echo wp_trim_words(get_the_title(), 10, '...'); ?>
                    </h4>

                    <p class="text-sm text-gray-500 leading-relaxed">
                        <?php echo wp_trim_words(get_the_excerpt(), 12, '...'); ?>
                    </p>

                </div>

            </a>

            <?php endwhile; ?>

        </div>

    </div>

    <?php
        wp_reset_postdata();
    endif;
    ?>

</div>

</section>






<section class="max-w-[1600px] mx-auto px-2 py-16">

    <!-- Heading -->
    <h2 class="text-center md:text-[56px] text-[25px] font-bold leading-none">
        Explore Our
        <span class="text-[#ff4b22]">Blogs</span>
    </h2>

    <!-- Search -->
    <div class="flex justify-center mt-8">

        <form
            role="search"
            method="get"
            action="<?php echo esc_url(home_url('/')); ?>"
            class="w-full max-w-[880px]"
        >

            <div class="relative">

                <div
                    class="bg-white rounded-full border border-[#ff4b22]
                           shadow-lg p-2 flex items-center"
                >

                    <!-- Search Icon -->
                    <div
                        class="w-12 h-12 rounded-full bg-[#ff4b22]
                               flex items-center justify-center shrink-0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5 text-white"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <input
                        type="search"
                        name="s"
                        placeholder="Search blog by category..."
                        class="flex-1 px-5 text-lg border-0 outline-none focus:ring-0"
                    >

                    <button
                        type="submit"
                        class="bg-black text-white
                               px-10 py-3 rounded-full
                               font-semibold text-sm"
                    >
                        Search
                    </button>

                </div>

            </div>

        </form>

    </div>

    <!-- Bottom Row -->
    <div class="md:flex justify-between items-center mt-12">

        <!-- Stats -->
        <div
            class="bg-black text-white rounded-full
                   md:px-6 px-2 py-3 flex items-center md:gap-6 gap-2
                   shadow-lg"
        >

            <span class="text-sm">
                Blogs:
                <?php echo wp_count_posts()->publish; ?>
            </span>

            <span class="w-px h-5 bg-white/20"></span>

            <span class="text-sm">
                Last 7 Days:
                <?php
                $week = new WP_Query([
                    'date_query' => [['after' => '7 days ago']],
                    'post_type' => 'post',
                    'fields' => 'ids'
                ]);
                echo $week->found_posts;
                wp_reset_postdata();
                ?>
            </span>

            <span class="w-px h-5 bg-white/20"></span>

            <span class="text-sm">
                Last 30 Days:
                <?php
                $month = new WP_Query([
                    'date_query' => [['after' => '30 days ago']],
                    'post_type' => 'post',
                    'fields' => 'ids'
                ]);
                echo $month->found_posts;
                wp_reset_postdata();
                ?>
            </span>

             <span class="w-px h-5 bg-white/20"></span>

            <span class="text-sm">
                This Year:
               
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
            </span>

        </div>

        <!-- Category Dropdown -->
       <div class="relative w-[290px]">

    <!-- Toggle Button -->
    <button id="categoryToggle"
        type="button"
        class="w-full bg-black text-white rounded-full px-5 py-3 mt-2
               flex items-center justify-between
               shadow-lg">

        <div class="flex items-center gap-3">

            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1v-4zm10-10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zm0 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4"/>
            </svg>

            <span class="font-medium">
                All Categories
            </span>

        </div>

        <!-- Arrow -->
        <svg id="categoryArrow"
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5 transition duration-300"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">

            <path stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"/>
        </svg>

    </button>

    <!-- Dropdown -->
    <div id="categoryMenu"
        class="hidden absolute right-0 top-full mt-1
               w-[280px] bg-[#000000] rounded-2xl
               shadow-2xl p-6 z-50">

        <div class="relative pl-8">

            <!-- Line -->
            <div class="absolute left-[5px] top-0 w-[2px] h-full
                bg-gradient-to-b
                from-[#FF4B22]
                via-[#FF5252]
                to-transparent">
            </div>

            <!-- Dot -->
            <div class="absolute left-0 top-0 w-3 h-3 rounded-full bg-[#FF4B22]"></div>

            <div class="space-y-5">

                <!-- All Posts -->
                <a href="<?php echo home_url('/blog'); ?>"
                    class="block text-[18px] font-medium text-white hover:text-blue-600">
                    All Categories
                </a>

                <?php
                $categories = get_categories([
                    'hide_empty' => false
                ]);

                foreach ($categories as $category) :
                ?>

                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
                        class="block text-[18px] font-medium text-white hover:text-blue-600 transition">

                        <?php echo esc_html($category->name); ?>

                        <span class="text-gray-400 text-sm">
                            (<?php echo $category->count; ?>)
                        </span>

                    </a>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

</div>

<script>

const toggleBtn = document.getElementById('categoryToggle');
const menu = document.getElementById('categoryMenu');
const arrow = document.getElementById('categoryArrow');

toggleBtn.addEventListener('click', () => {

    menu.classList.toggle('hidden');

    arrow.classList.toggle('rotate-180');
});

document.addEventListener('click', (e) => {

    if (!toggleBtn.contains(e.target) &&
        !menu.contains(e.target)) {

        menu.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
});
</script>


    </div>

</section>



<style>
  #category-dropdown,
.searchbox,
input[type="search"] {
    border: none !important;
    box-shadow: none !important;
}

#categoryToggle {
    background-color: black!important;
}


#categoryToggle:hover{
  background-color:black;
}
</style>



<!-- ================= BLOG GRID ================= -->


 <!-- ================= BLOG GRID ================= -->
<section class="max-w-[1600px] mx-auto px-4 ">

<?php
/* ===== PAGINATION FIX ===== */
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

/* ===== WORDPRESS QUERY ===== */
$args = array(
    'post_type'      => 'post',
    'posts_per_page' => 6,
    'paged'          => $paged,
    'post_status'    => 'publish'
);

$query = new WP_Query($args);
?>

<div id="articlesGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">

<?php if($query->have_posts()): ?>
<?php while($query->have_posts()): $query->the_post(); ?>

<article class="transition duration-300">

    <?php if(has_post_thumbnail()): ?>
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('medium_large',['class'=>'rounded-2xl w-full h-60 object-cover']); ?>
        </a>
    <?php endif; ?>

    <div class="p-5">

        <h3 class="font-semibold text-[25px] mb-2 leading-snug">
            <a href="<?php the_permalink(); ?>" class="text-gray-900 hover:text-blue-600 transition">
                <?php echo wp_trim_words(get_the_title() , 4,'...'); ?>
            </a>
        </h3>

        <p class="text-gray-500 text-sm mb-4">
            <?php echo wp_trim_words(get_the_excerpt(),15,'...'); ?>
        </p>

        <div class="flex items-center gap-2 text-gray-500 text-sm">
            <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Generic-avatar-4.png" class="w-7 h-7 rounded-full">
            <span>Editorial Team</span>
            <span>•</span>
            <span><?php echo get_the_date('j M Y'); ?></span>
        </div>

    </div>

</article>

<?php endwhile; ?>
<?php else: ?>
<p>No posts found.</p>
<?php endif; ?>

<?php wp_reset_postdata(); ?>

</div>


<!-- ================= REAL WORDPRESS PAGINATION ================= -->
<div class="outright-pagination flex justify-center mt-14">

<?php
echo paginate_links(array(
    'total'      => $query->max_num_pages,
    'current'    => $paged,
    'mid_size'   => 2,
    'prev_text'  => '‹Previous',
    'next_text'  => 'Next›',
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
    padding:7px 14px;
    background:#fff;
    border-radius:14px;
    box-shadow: 0 5px 6px rgba(0, 0, 0, 0.12);
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
    background:#FF4B22;
    color:#fff;
    width:28px;
    height:28px;
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










  

</section>













<?php get_footer() ?>