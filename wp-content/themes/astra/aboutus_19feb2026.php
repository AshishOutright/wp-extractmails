<?php
/*
Template Name: about-us
*/
get_header()
?>


  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" ></script>

<link rel="stylesheet"
      href="https://unpkg.com/aos@2.3.4/dist/aos.css"
      media="print"
      onload="this.media='all'">

  


    

<!-- HERO SECTION -->
<section
  class="max-w-[1800px] m-auto relative mt-0 pt-20
         md:min-h-[560px] flex items-center justify-center
         overflow-hidden ">



<div class="blog-glow-left"></div>
<div class="blog-glow-right"></div>

<style>
    .blog-glow-left{
    position:absolute;
    top:-160px;
    left:160px;
    width:820px;
    height:220px;
    pointer-events:none;
    z-index:-1;
    filter:blur(100px);
    background: radial-gradient(circle at center,
        rgba(226,82,16,0.55) 0%,
        rgba(224, 84, 8, 0.35) 35%,
        rgba(226, 7, 7, 0) 70%);
}



.blog-glow-right{
    position:absolute;
    top:-160px;
    right:160px;
    width:820px;
    height:220px;
    pointer-events:none;
    z-index:-1;
    filter:blur(100px);
    background: radial-gradient(circle at center,
        rgba(26, 5, 216, 0.55) 0%,
        rgba(5, 155, 249, 0.35) 35%,
        rgba(247, 12, 12, 0) 70%);
}


</style>


      
<section class=" py-0 px-4 text-center">

  <!-- Title -->
  <h2 class="text-[44px] font-semibold text-black mb-8">
    About Us
  </h2>

  <!-- Card -->
  <div class="max-w-[1250px] mx-auto bg-[#e8e0ff] rounded-[32px] px-10 py-10 md:px-16 md:py-12 text-left">

    <p class="text-[16px] leading-[1.7] text-gray-900 mb-6">
      WP-1Click is built to simplify the way WordPress websites are launched.
      We help businesses, creators, and agencies start with a secure,
      high-performance foundation instead of relying on risky default settings.
    </p>

    <p class="text-[16px] leading-[1.7] text-gray-900 mb-6">
      Our platform automates WordPress configuration using security-first
      practices and performance optimization from day one. No manual setup,
      no unnecessary plugins, and no overlooked vulnerabilities.
    </p>

    <p class="text-[16px] leading-[1.7] text-gray-900">
      We focus on building clean, scalable, and reliable WordPress environments
      that are ready to grow. With WP-1Click, launching a website becomes
      faster, safer, and smarter.
    </p>

  </div>

</section>


 
</section>


<section class=" py-4 px-4 flex justify-center">

  <!-- Main Rounded Container -->
  <div class="relative max-w-[1050px] w-full bg-[#ffff] border border-gray-300 rounded-[42px] px-12 py-10 overflow-hidden">

    <!-- ===== TOP RIGHT FLOATING GLOBE (VISION IMAGE) ===== -->
    <img
      src="https://www.wp-1click.com/wp-content/uploads/2026/02/20260211_1310_Image-Generation_simple_compose_01kh5t85f9fpbrhtmhg2nkcgv8-1.png"
      alt="Vision Globe"
      class="hidden md:block absolute right-0 top-0 w-[360px] drop-shadow-[0_35px_35px_rgba(0,0,0,0.18)]"
    >

    <!-- ===== BOTTOM LEFT TELESCOPE GLOBE ===== -->
    <img
      src="https://www.wp-1click.com/wp-content/uploads/2026/02/20260211_1258_Image-Generation_simple_compose_01kh5sjbcaepgb3w096e9f9vad-1.png"
      alt="Mission"
      class="hidden md:block absolute left-0 bottom-0 w-[420px]"
    >

    <!-- GRID CONTENT -->
    <div class="grid grid-cols-12 gap-y-16 gap-x-6 relative z-10">

  <!-- ================= ROW 1 : MISSION ================= -->
  <!-- Wide Left Column -->
  <div class="col-span-12 md:col-span-8">

    <h3 class="text-[30px] font-semibold text-gray-800 mb-4">
      Our Mission
    </h3>

    <p class="text-[16.5px] leading-relaxed text-gray-700 max-w-[820px] mb-4">
      Our mission is to redefine WordPress deployment by making security
      and performance the foundation — not an afterthought. Through
      intelligent automation, we eliminate setup errors and ensure every
      website launches optimized, stable, and protected.
    </p>

  </div>

  <!-- Empty space for top-right globe -->
  <div class="hidden md:block md:col-span-4"></div>



  <!-- ================= ROW 2 : VISION ================= -->
  <!-- Empty space for telescope -->
  <div class="hidden md:block md:col-span-4"></div>

  <!-- Wide Right Column -->
  <div class="col-span-12 md:col-span-8">

    <h3 class="text-[30px] font-semibold text-gray-800 mb-4 flex md:justify-end ">
      Our Vision
    </h3>

    <p class="text-[16.5px] leading-relaxed text-gray-700 max-w-[720px]">
      Our vision is to become the trusted standard for secure,
      performance-optimized WordPress deployment. We aim for a WordPress
      ecosystem where every website is built strong, protected by
      default, and ready to scale with confidence.
    </p>

  </div>

</div>


    <!-- MOBILE IMAGES -->
    <div class="md:hidden mt-10 space-y-0 text-center">

      <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/20260211_1310_Image-Generation_simple_compose_01kh5t85f9fpbrhtmhg2nkcgv8-1.png" class="w-[280px] mx-auto">
      <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/20260211_1258_Image-Generation_simple_compose_01kh5sjbcaepgb3w096e9f9vad-1.png" class="w-[200px] mx-auto">

    </div>

  </div>

</section>





<section class=" py-8 px-4">

  <!-- Heading -->
  <h2 class="text-center text-[36px] font-semibold text-gray-900 mb-14">
    Why Choose Us?
  </h2>

  <!-- Cards Wrapper -->
  <div class="max-w-[1100px] mx-auto grid md:grid-cols-3 gap-10">

    <!-- CARD 1 -->
    <div class="bg-[#e8e0ff] rounded-[26px] p-8 text-center">

      <!-- Icon Circle -->
      <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-white flex items-center justify-center border-2 border-yellow-400">
        <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/process-1-1.png" class="w-13 h-13">
      </div>

      <h3 class="text-[24px] font-semibold text-gray-800 mb-3">
        Security First
      </h3>

      <p class="text-[18px] leading-relaxed text-gray-700">
        Every website launches with secure configurations applied by default.
      </p>

    </div>


    <!-- CARD 2 -->
    <div class="bg-[#ffe5da] rounded-[26px] p-8 text-center">

      <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-white flex items-center justify-center border-2 border-green-400">
        <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/good-feedback-1.png" class="w-13 h-13">
      </div>

      <h3 class="text-[24px] font-semibold text-gray-800 mb-3">
        Performance
      </h3>

      <p class="text-[18px] leading-relaxed text-gray-700">
        Every website is built lightweight, fast, and optimized for long-term scalability.
      </p>

    </div>


    <!-- CARD 3 -->
    <div class="bg-[#e8e0ff] rounded-[26px] p-8 text-center">

      <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-white flex items-center justify-center border-2 border-yellow-400">
        <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/process-1-1.png" class="w-13 h-13">
      </div>

      <h3 class="text-[24px] font-semibold text-gray-800 mb-3">
        Smart Automation
      </h3>

      <p class="text-[18px] leading-relaxed text-gray-700">
        AI-powered system delivers consistent WordPress deployments in minutes.
      </p>

    </div>

  </div>

</section>












<?php get_footer() ?>
