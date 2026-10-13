<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header(); 


?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">












<section class="relative pt-20 md:pt-20 overflow-hidden lg:overflow-visible">




 




<style>

html{
  scroll-behavior: smooth;
}
   






/* TOP COLOR → BOTTOM WHITE FADE (MAIN EFFECT) */


/* COMMON LIGHT STYLE */



.entry-content > *{
    max-width: 850px;
    margin-left: auto;
    margin-right: auto;
}


/* ===== Fix Gutenberg Image Gap (Tailwind Conflict) ===== */

/* remove Tailwind figure margins */
figure.wp-block-image{
    margin:0 !important;
}

/* control spacing manually */
.wp-block-image{
    margin-top: 25px !important;
    margin-bottom: 25px !important;
}

/* remove invisible space under image */
.wp-block-image img{
    display:block;
    width:100%;
    height:auto;
}

/* remove empty caption space */
.wp-block-image figcaption{
    margin-top:10px;
}
.wp-block-image figcaption:empty{
    display:none !important;
}

/* VERY IMPORTANT — WordPress content container fix */
.entry-content > figure{
    max-width: 100% !important;
}


/* Your custom image style */
.post-image{
    margin:30px 0;
}

.post-image img{
    width:100%;
    height:auto;
    display:block;
    border-radius:14px;
}



/* ===== REMOVE GUTENBERG BLOCK GAP AFTER IMAGES ===== */

/* stop WordPress block spacing system */
.entry-content .wp-block-image{
    margin-bottom:0 !important;
}

/* Astra adds spacing to every direct child */
.entry-content > *{
    margin-bottom:18px;
}

/* remove figure default spacing */
.entry-content figure{
    margin:0 !important;
    padding:0 !important;
}

/* critical fix */
.entry-content .wp-block-image + *{
    margin-top:0 !important;
}


/* ===== Fix long words in Blog Title ===== */
.entry-title{
    overflow-wrap:anywhere;
    word-break:break-word;
    line-height:1.25;
}


.entry-content table th, .entry-content table td, .single-post table th, .single-post table td, .post-content table th, .post-content table td, .ast-container table th, .ast-container table td {
      border: 1px solid #000 !important;
}


</style>




   

<div class="max-w-[1650px] mx-auto px-2.5 mb-2 mt-12">


    <!-- MAIN HERO CARD -->
    <div class="bg-[#ffffff] rounded-[32px] px-2 py-6 mb-py-6 md:px-6  border-2">

       <div class="grid md:grid-cols-[0.7fr_1.3fr] gap-10 items-center">

            <!-- LEFT CONTENT -->
            <div class=>

                <?php
$categories = get_the_category();
?>

<div class="text-sm text-blue-500 space-x-1 mb-2">
    
    <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:underline">
        Home
    </a>

    <span>/</span>

    <a href="<?php echo esc_url(home_url('/blog')); ?>" class="hover:underline">
        Blog
    </a>

    <?php if (!empty($categories)) : ?>
        <span>/</span>

        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="hover:underline">
            <?php echo esc_html($categories[0]->name); ?>
        </a>
    <?php endif; ?>

</div>

                <!-- Title -->
                <h1 class="text-1xl md:text-[34px] font-semibold text-gray-800 leading-tight mb-5 entry-title">
                    <?php the_title(); ?>
                </h1>

                <!-- Description -->
                <?php
                $meta_description = get_post_meta(get_the_ID(), 'rank_math_description', true);
                if (!empty($meta_description)) {
                    echo '<p class="text-gray-600 text-[15px] leading-relaxed mb-6 max-w-xl">'
                        . esc_html($meta_description) .
                        '</p>';
                }
                ?>
                

                <!-- Author + Share -->
                <div class="flex flex-wrap items-center justify-between gap-4  mt-4 md:mt-20">

                    <!-- Author -->
                    <div class="flex items-center gap-4 mb-2 md:mb-10">

                        <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/Generic-avatar-2.png"
                             alt="Author"
                             class="w-10 h-10 rounded-full object-cover">

                        <div class="text-[19px] text-gray-600">
                            <span class="font-medium text-gray-800">Jonathan William</span>
                            <span class="mx-2">•</span>
                            <span><?php echo get_the_modified_date('j M Y'); ?></span>
                        </div>

                    </div>

                    <!-- Share Button -->
                    <a href="#"
   onclick="copyURL(event)"
   class="flex items-center gap-2 bg-blue-100 text-blue-600 text-sm font-medium px-4 py-2 rounded-full hover:bg-blue-200 transition">

    <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/share-1.png" alt="">
    Share on media
</a>

<script>
function copyURL(event) {
    event.preventDefault(); // stop link jumping

    const url = window.location.href;

    navigator.clipboard.writeText(url).then(() => {
        alert("Link copied!");
    }).catch(err => {
        console.error("Failed to copy: ", err);
    });
}
</script>

                </div>

            </div>

            <!-- RIGHT IMAGE -->
            <div class="relative">

                <?php if (has_post_thumbnail()) : ?>
                <div class="rounded-[28px] overflow-hidden shadow-xl">
                  <?php the_post_thumbnail('full', ['class' => 'w-full h-[200px] sm:h-[250px] md:h-[400px] lg:h-[500px] object-cover']); ?>
                </div>
                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

</div>















<style>

/* GLOBAL SMOOTH UI (Bootstrap replacement) */
a,
button,
input,
textarea,
select {
  transition: all 0.25s ease;
}

/* hover links */
.hover-link{
  transition: color .25s ease, background .25s ease, transform .15s ease;
}

.hover-link:hover{
  transform: translateX(3px);
}

/* inputs focus smooth */
input:focus, textarea:focus{
  outline: none;
  transition: box-shadow .2s ease, border-color .2s ease;
  box-shadow: 0 0 0 3px rgba(59,130,246,.18);
}

/* images smooth */
img{
  transition: transform .3s ease, opacity .3s ease;
}

/* card hover smooth */
.toc-container{
  transition: box-shadow .25s ease, transform .25s ease;
}

/* .toc-container:hover{
  transform: translateY(-2px);
  box-shadow: 0 20px 50px rgba(0,0,0,.08);
} */
.tocright_side {
    background-color: white;
    border-radius: 25px; /* adjust as needed */
    /* box-shadow: 0 0 30px #edebf4 */
}



.hero_container{
    max-width: 1650px;
    margin:auto;
}

.post-content p{
    font-size:18px;
    color:black!important;
}

.post-content h2{
    font-size:34px;
    color:black!important;
}


td, th {
      padding: .7em 1em;
    border-width: 0 1px 1px 0;
    color: black;
}
</style>




<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">


<!-- Blog Content and TOC -->
<div class="hero_container p-3">

  <!-- GRID -->
  <div class="grid grid-cols-12 gap-4">

    <!-- LEFT TOC -->
    <div class="hidden lg:block lg:col-span-3 ">

      <div class="sticky top-[87px]">
        <div class="toc-container custom-toc tocright_side p-3 border-2">
          <div class="toc-header m-2">
            <strong class="text-3xl font-bold center p-3">Contents</strong>
          </div>

          <ul class="toc-list">
            <?php
            $content = apply_filters('the_content', get_the_content());

            preg_match_all('/<h[2].*?>(.*?)<\/h[2]>/i', $content, $matches);
            foreach ($matches[1] as $index => $title) {
              $section_id = 'section-' . ($index + 1);
              echo '<li>
        <a href="javascript:void(0)"
           data-target="' . esc_attr($section_id) . '"
           class="block text-black hover-link toc-link">'
           . esc_html(strip_tags($title)) .
        '</a>
      </li>';
            }

            $updated_content = preg_replace_callback(
              '/(<h[2])(.*?>)(.*?)(<\/h[2]>)/i',
              function ($heading_matches) {
                static $counter = 0;
                $counter++;
                $id = 'section-' . $counter;
                return $heading_matches[1] . ' id="' . esc_attr($id) . '"' . $heading_matches[2] . $heading_matches[3] . $heading_matches[4];
              },
              $content
            );
            ?>
          </ul>
        </div>
      </div>

    </div>


    <!-- MAIN CONTENT -->
    <div class="col-span-12 lg:col-span-7  tocright_side p-5 border-2">

      <div class="post-content custom-blog-content custom-padding">


        <div class="content-body  blog-content">
          
          <?php echo $updated_content; ?>
        </div>

      </div>

    </div>

    <style>
      /* ===== BLOG CONTENT TYPOGRAPHY FIX ===== */

.blog-content a{
    color:#2563eb;              /* blue link */
    text-decoration: underline; /* show underline */
    font-weight:500;
    transition: all .25s ease;
}

.blog-content a:hover{
    color:#1e40af;
    text-decoration: underline;
}

/* paragraphs spacing */
.blog-content p{
    margin-bottom:18px;
    line-height:1.8;
    font-size:18px;
    color:#1f2937;
}

/* headings */
.blog-content h2,
.blog-content h3,
.blog-content h4{
    margin-top:35px;
    margin-bottom:15px;
    font-weight:700;
    line-height:1.3;
}
.blog-content h3{
  font-size:27px;
}

/* lists */
.blog-content ul{
    list-style: disc;
    padding-left:22px;
    margin-bottom:20px;
    color:black;
}

.blog-content ol{
    list-style: decimal;
    padding-left:22px;
    margin-bottom:20px;
    color:black;
}

/* blockquote */
.blog-content blockquote{
    border-left:4px solid #7c3aed;
    padding:12px 18px;
    background:#f5f3ff;
    margin:20px 0;
    font-style:italic;
}


/* headings */
.blog-content h1,
.blog-content h2,
.blog-content h3,
.blog-content h4,
.blog-content h5,
.blog-content h6{
    overflow-wrap: anywhere;
}

/* for technical content & error codes */
.blog-content code,
.blog-content pre{
    white-space: pre-wrap;
    word-break: break-word;
    overflow-x: auto;
}


/* Fix long words / URLs / error codes inside blog content */
.blog-content p,
.blog-content a,
.blog-content span,
.blog-content li,
.blog-content strong,
.blog-content em {
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}


.entry-content h2.has-ast-global-color-6-background-color,
.entry-content h3.has-ast-global-color-6-background-color {
    background-color: var(--ast-global-color-6) !important;
}


.blog-content h2.has-ast-global-color-6-background-color,
.blog-content h3.has-ast-global-color-6-background-color {
    background-color: var(--ast-global-color-6);
}
.has-ast-global-color-6-background-color {
    background: var(--ast-global-color-6) !important;
}



    </style>


    <!-- RIGHT SIDEBAR -->
    <div class="hidden lg:block lg:col-span-2">

      <div class="mt-0"></div>

      <div class="sticky top-[65px]  ">
        <div class="toc-container custom-toc p-0">

          <div class=" ">

           

            <!-- Popular Topics -->
           

            <aside class="hidden lg:block sticky top-38 self-start">

              

             

            <style>
              .searchbox:focus{outline: none; border: none; box-shadow: none; }
              .searchbox{outline: none; border: none !important; box-shadow: none !important; background: transparent;}
            </style>


               <!-- Search Box -->
           
 <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
      class="flex items-center w-full max-w-md bg-white border border-gray-200 rounded-full px-1 py-1 shadow-sm">

      <!-- Icon -->
     <img src="https://www.extractmails.com/wp-content/uploads/2026/04/search-1-1.png" alt="">

      <!-- Input -->
      <input type="search" name="s"  class="searchbox" 
  placeholder="Search the blog..."
  class="flex-1 bg-transparent outline-none border-none ring-0 focus:outline-none focus:ring-0 focus:border-none text-sm text-gray-600">

      <!-- Button -->
      <button type="submit"
        class="ml-0 bg-[linear-gradient(180deg,rgba(3,4,88,1)_0%,rgba(56,60,255,1)_100%)] hover:bg-[#0d1b5e] text-white text-sm font-medium px-5 py-1 rounded-full transition">
        Search
      </button>
    </form>


            <div class="toc-container custom-toc p-5 rounded-2xl tocright_side border-2  entry-title mt-2">

    <h3 class="text-center text-xl font-semibold mb-4">
        <span class="text-indigo-500">Latest Blogs</span>
    </h3>

   

    <?php
    // current post ID (to exclude it)
    $current_post_id = get_the_ID();

    // Fetch latest posts
    $latest_posts = new WP_Query(array(
        'post_type'           => 'post',
        'posts_per_page'      => 4,
        'post_status'         => 'publish',
        'post__not_in'        => array($current_post_id), // do not show current post
        'ignore_sticky_posts' => true
    ));

    if ($latest_posts->have_posts()) :
        while ($latest_posts->have_posts()) : $latest_posts->the_post();
    ?>

        <div class="latest-blog-item py-3 border-b border-gray-300 last:border-0">

            <a href="<?php the_permalink(); ?>" class="block hover:text-blue-600 transition">

                <h4 class="text-[15px] leading-snug font-medium text-gray-700">
                    <?php the_title(); ?>
                </h4>

            </a>

        </div>

    <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>No recent posts found.</p>';
    endif;
    ?>

    <!-- Search box -->
    




     <!-- Social Icons -->
              <div class="flex py-4 px-2 gap-2">

                <div class="p-1">
                  <a href="https://www.facebook.com/people/ExtractMails/61577511276950/" target="_blank">
                    <img src="https://outrightcrm.com/wp-content/tools_image/facebook%20(1)%201.png">
                  </a>
                </div>

                <div class="p-1">
                  <a href="https://www.instagram.com/outrightstore/" target="_blank">
                    <img src="https://outrightcrm.com/wp-content/tools_image/social%201.png">
                  </a>
                </div>

                <div class="p-1">
                  <a href="https://x.com/MailsExtract" target="_blank">
                    <img src="https://outrightcrm.com/wp-content/tools_image/twitter%20(2)%201.png">
                  </a>
                </div>

                <div class="p-1">
                  <a href="https://api.whatsapp.com/send?phone=917275894867" target="_blank">
                    <img src="https://outrightcrm.com/wp-content/tools_image/whatsapp%202.png">
                  </a>
                </div>

                <div class="p-1">
                  <a href="https://www.youtube.com/@OutrightSystems" target="_blank">
                    <img src="https://outrightcrm.com/wp-content/tools_image/youtube%20(2)%201.png">
                  </a>
                </div>

              </div>







            </aside>

          </div>
        </div>
      </div>

    </div>

  </div>
</div>


<style>

.toc-list .list-group-item {
  border: none !important; /* ✅ Removes the bottom border */
  padding: 0;              /* Optional: removes extra padding */
  background: transparent; /* Keeps background clean */
}
 .toc-list a {
  font-size:15px;
  display: block;
  width: 100%;
  padding: 6px;
  border-radius: 10px;
  color: #000;
  text-decoration: none;
  transition: all 0.3s ease; /* Smooth transition */
}

.toc-list a:hover {
  background: rgba(26, 86, 219, 0.1); /* subtle hover effect */
  color: #1a56db;
}
.toc-list a.active {
  
 background:

    /* warm glow (bottom-left) — must be FIRST */
    radial-gradient(60% 80% at 8% 92%,
        rgb(71, 32, 15) 0%,
        rgb(71, 32, 15) 18%,
        rgba(150, 60, 20, 0.45) 45%,
        rgba(0,0,0,0) 90%
    ),

    /* purple glow (top-right) */
    radial-gradient(75% 90% at 92% 8%,
        #2f1873 0%,
        #2f1873 30%,
        #14082c 55%,
        #05000a 72%,
        #000 90%
    ),

    /* base */
    #000;


   
  color:white;
 
  
}









</style>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const sections =
        document.querySelectorAll('.content-body h2, .content-body h3');

    const tocLinks =
        document.querySelectorAll('.toc-list a');

    /*
    =========================================
    TOC CLICK SCROLL
    =========================================
    */

    tocLinks.forEach((link) => {

        link.addEventListener('click', function () {

            const targetId = this.dataset.target;

            const targetElement =
                document.getElementById(targetId);

            if (targetElement) {

                const offset = 120;

                const top =
                    targetElement.getBoundingClientRect().top +
                    window.pageYOffset -
                    offset;

                window.scrollTo({
                    top: top,
                    behavior: 'smooth'
                });
            }
        });
    });

    /*
    =========================================
    ACTIVE LINK ON SCROLL
    =========================================
    */

    function setActiveLink() {

        let currentSectionId = "";

        sections.forEach((section) => {

            const sectionTop =
                section.getBoundingClientRect().top +
                window.scrollY -
                140;

            if (window.scrollY >= sectionTop) {
                currentSectionId = section.id;
            }
        });

        tocLinks.forEach((link) => {

            link.classList.remove('active');

            if (link.dataset.target === currentSectionId) {
                link.classList.add('active');
            }
        });
    }

    window.addEventListener("scroll", setActiveLink);

    setActiveLink();
});
</script>



</section>

</section>
<!-- =========================================
     ACF FAQ SECTION START
========================================= -->

<?php

// Get FAQ title from ACF
$faq_title = get_field('faq_title');

// Create an empty FAQ array
$faq_items = [];


/*
|--------------------------------------------------------------------------
| GET FAQ QUESTION AND ANSWER
|--------------------------------------------------------------------------
|
| Check FAQ fields from 1 to 10
|
*/

for ($i = 1; $i <= 10; $i++) {

    $question = get_field("faq_question{$i}");

    $answer = get_field("faq_answer{$i}");


    /*
    |--------------------------------------------------------------------------
    | ADD ONLY COMPLETE FAQS
    |--------------------------------------------------------------------------
    */

    if (!empty($question) && !empty($answer)) {

        $faq_items[] = [

            'question' => $question,

            'answer' => $answer

        ];

    }

}


/*
|--------------------------------------------------------------------------
| DISPLAY FAQ SECTION
|--------------------------------------------------------------------------
|
| Show FAQ only when the current post
| has at least one question and answer.
|
*/

if (!empty($faq_items)) :
?>

<section class="post-acf-faq-section">

```
<div class="post-acf-faq-container">


    <!-- FAQ TITLE -->

    <h2 class="post-acf-faq-title">

        <?php

        echo !empty($faq_title)

            ? esc_html($faq_title)

            : 'Frequently Asked Questions';

        ?>

    </h2>


    <!-- FAQ LIST -->

    <div class="post-acf-faq-list">


        <?php foreach ($faq_items as $index => $faq) : ?>


            <div class="post-acf-faq-item">


                <!-- FAQ QUESTION -->

                <button

                    class="post-acf-faq-question"

                    type="button"

                    aria-expanded="false"

                    aria-controls="post-faq-answer-<?php echo esc_attr($index); ?>"

                >


                    <!-- FAQ QUESTION IN H3 -->

                    <h3 class="post-acf-faq-question-title">

                        <?php

                        echo esc_html(

                            $faq['question']

                        );

                        ?>

                    </h3>


                    <!-- PLUS ICON -->

                    <span

                        class="post-acf-faq-icon"

                        aria-hidden="true"

                    >

                        +

                    </span>


                </button>


                <!-- FAQ ANSWER -->

                <div

                    class="post-acf-faq-answer"

                    id="post-faq-answer-<?php echo esc_attr($index); ?>"

                >


                    <div class="post-acf-faq-answer-content">


                        <?php

                        echo wp_kses_post(

                            wpautop(

                                $faq['answer']

                            )

                        );

                        ?>


                    </div>


                </div>


            </div>


        <?php endforeach; ?>


    </div>


</div>
```

</section>

<?php endif; ?>

<!-- =========================================
     ACF FAQ SECTION END
========================================= -->

<!-- =========================================
     FAQ SCHEMA START
========================================= -->

<?php

/*
|--------------------------------------------------------------------------
| CREATE FAQ SCHEMA
|--------------------------------------------------------------------------
|
| Schema is generated only when
| the current post has FAQ data.
|
*/

if (!empty($faq_items)) :


    $schema_data = [

        "@context" => "https://schema.org",

        "@type" => "FAQPage",

        "mainEntity" => []

    ];


    /*
    |--------------------------------------------------------------------------
    | ADD EACH FAQ TO THE SCHEMA
    |--------------------------------------------------------------------------
    */

    foreach ($faq_items as $item) {


        $schema_data["mainEntity"][] = [

            "@type" => "Question",

            "name" => wp_strip_all_tags(

                $item['question']

            ),

            "acceptedAnswer" => [

                "@type" => "Answer",

                "text" => wp_strip_all_tags(

                    $item['answer']

                )

            ]

        ];


    }

?>

<script type="application/ld+json">

<?php

echo wp_json_encode(

    $schema_data,

    JSON_PRETTY_PRINT

    |

    JSON_UNESCAPED_UNICODE

    |

    JSON_UNESCAPED_SLASHES

);

?>

</script>

<?php endif; ?>

<!-- =========================================
     FAQ SCHEMA END
========================================= -->

<!-- =========================================
     FAQ CSS
========================================= -->

<style>


/* FAQ SECTION */

.post-acf-faq-section {

    width: 100%;

    padding: 70px 20px;

    margin-top: 40px;

    background: #ffffff;

}


/* FAQ CONTAINER */

.post-acf-faq-container {

    width: 100%;

    max-width: 1100px;

    margin: 0 auto;

}


/* FAQ MAIN HEADING */

.post-acf-faq-title {

    margin: 0 0 35px;

    text-align: center;

    font-size: 36px;

    font-weight: 700;

    line-height: 1.3;

    color: #030458;

}


/* FAQ LIST */

.post-acf-faq-list {

    display: flex;

    flex-direction: column;

    gap: 15px;

}


/* FAQ ITEM */

.post-acf-faq-item {

    overflow: hidden;

    border: 1px solid #d9def5;

    border-radius: 12px;

    background: #ffffff;

    transition:

        border-color 0.3s ease,

        box-shadow 0.3s ease;

}


/* ACTIVE FAQ */

.post-acf-faq-item.active {

    border-color: #383cff;

    box-shadow:

        0 8px 25px

        rgba(56, 60, 255, 0.10);

}


/* FAQ QUESTION BUTTON */

.post-acf-faq-question {

    width: 100%;

    padding: 20px 24px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    border: none;

    cursor: pointer;

    text-align: left;

    background: #f5f7ff;

    transition:

        background 0.3s ease;

}


/* QUESTION H3 */

.post-acf-faq-question-title {

    margin: 0 !important;

    padding: 0;

    font-size: 19px;

    font-weight: 600;

    line-height: 1.5;

    color: #111827;

}


/* QUESTION HOVER */

.post-acf-faq-question:hover {

    background: #e9edff;

}


/* PLUS ICON */

.post-acf-faq-icon {

    width: 34px;

    height: 34px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 50%;

    color: #ffffff;

    background: #383cff;

    font-size: 25px;

    font-weight: 400;

    line-height: 1;

    transition:

        transform 0.3s ease;

}


/* ROTATE + TO × */

.post-acf-faq-item.active

.post-acf-faq-icon {

    transform:

        rotate(45deg);

}


/* FAQ ANSWER */

.post-acf-faq-answer {

    display: none;

    padding:

        0 24px 24px;

    background: #ffffff;

}


/* SHOW ACTIVE ANSWER */

.post-acf-faq-item.active

.post-acf-faq-answer {

    display: block;

}


/* ANSWER CONTENT */

.post-acf-faq-answer-content {

    padding-top: 18px;

    color: #374151;

    font-size: 17px;

    line-height: 1.8;

}


/* REMOVE LAST PARAGRAPH GAP */

.post-acf-faq-answer-content

p:last-child {

    margin-bottom: 0;

}


/* MOBILE */

@media screen and (max-width: 767px) {


    .post-acf-faq-section {

        padding:

            50px 15px;

    }


    .post-acf-faq-title {

        font-size: 27px;

        margin-bottom: 25px;

    }


    .post-acf-faq-question {

        padding:

            16px;

    }


    .post-acf-faq-question-title {

        font-size: 16px;

    }


    .post-acf-faq-icon {

        width: 30px;

        height: 30px;

        font-size: 22px;

    }


    .post-acf-faq-answer {

        padding:

            0 16px 18px;

    }


    .post-acf-faq-answer-content {

        font-size: 15px;

    }

}


</style>

<!-- =========================================
     FAQ JAVASCRIPT
========================================= -->

<script>


document.addEventListener(

    'DOMContentLoaded',

    function () {


        const faqSection =

            document.querySelector(

                '.post-acf-faq-section'

            );


        // Stop if this post has no FAQ

        if (!faqSection) {

            return;

        }


        const faqQuestions =

            faqSection.querySelectorAll(

                '.post-acf-faq-question'

            );


        faqQuestions.forEach(

            function (question) {


                question.addEventListener(

                    'click',

                    function () {


                        const currentItem =

                            this.closest(

                                '.post-acf-faq-item'

                            );


                        const isOpen =

                            currentItem

                            .classList

                            .contains(

                                'active'

                            );


                        /*
                        CLOSE ALL FAQ ITEMS
                        */

                        faqSection

                        .querySelectorAll(

                            '.post-acf-faq-item'

                        )

                        .forEach(

                            function (item) {


                                item

                                .classList

                                .remove(

                                    'active'

                                );


                                const button =

                                    item.querySelector(

                                        '.post-acf-faq-question'

                                    );


                                if (button) {


                                    button.setAttribute(

                                        'aria-expanded',

                                        'false'

                                    );


                                }


                            }

                        );


                        /*
                        OPEN CLICKED FAQ
                        */

                        if (!isOpen) {


                            currentItem

                            .classList

                            .add(

                                'active'

                            );


                            this.setAttribute(

                                'aria-expanded',

                                'true'

                            );


                        }


                    }

                );


            }

        );


    }

);


</script>

<?php get_footer(); ?>


<?php get_footer(); ?>
