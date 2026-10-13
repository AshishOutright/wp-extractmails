<?php
/**
 * The header for Astra Theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!DOCTYPE html>
<?php astra_html_before(); ?>
<html <?php language_attributes(); ?>>
<head>
<?php astra_head_top(); ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-MSDWPEKZ0X"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-MSDWPEKZ0X');
</script>
 <!-- 27f8f40e874ebd5bc8b9c9fda7e030ae9cab02cc -->

<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if ( apply_filters( 'astra_header_profile_gmpg_link', true ) ) {
	?>
	<link rel="profile" href="https://gmpg.org/xfn/11"> 
	<?php
} 
?>
<?php wp_head(); ?>
<?php astra_head_bottom(); ?>


</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>





<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <style>
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes slideInFromTop { from { transform:translateY(-10px); opacity:0; } to { transform:translateY(0); opacity:1; } }
        .animate-in { animation-duration:200ms; }
        .fade-in { animation-name:fadeIn; }
        .slide-in-from-top { animation-name:slideInFromTop; }
        .slide-in-from-top-2 { animation-name:slideInFromTop; animation-duration:150ms; }


    .header-site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    margin: 0 auto;
    width: 100%;
    max-width: 1600px;
    z-index: 100000;
    background: transparent ;
    border-radius: 42px;
    transition: background 0.3s ease;
}

.header-site-header.scrolled {
    background: rgba(161, 161, 161, 0.2) ;
}

    </style>
    <?php wp_head(); ?>
</head>


<style>
 .feature-pill {
  position: relative;
  padding: 6px 40px;
  font-size: 18px;
  font-weight: 400;
  color: #000;
  background: transparent;
  border-radius: 999px;
  cursor: pointer;
  z-index: 1;
}

/* Custom broken border */
.feature-pill::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 999px;
  padding: 1px; /* border thickness */
  background:
    linear-gradient(
      135deg,
      transparent 0%,
      rgba(255,255,255,0.9) 22%,
      rgba(255,255,255,0.9) 72%,
      transparent 88%
    );
  -webkit-mask:
    linear-gradient(#000 0 0) content-box,
    linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
          mask-composite: exclude;
  z-index: -1;
}

/* Soft glass shadow */
.feature-pill {
  box-shadow:
    0 1px 0 rgba(255, 255, 255, 0.9),
    0 8px 20px rgba(0, 0, 0, 0.03);
  transition: all 0.25s ease;
}

</style>
<header class="fixed top-4 left-0 right-0 z-50">

  <div class="max-w-[1600px] mx-auto px-6">

    <!-- Glass Navbar -->
    <div class="flex items-center px-6 py-3 
                rounded-2xl border border-white/40 
                bg-white/20 backdrop-blur-md
                shadow-[0_2px_4px_rgba(0,0,0,0.08)]">

      <!-- Logo -->
      <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center">
        <img src="https://www.extractmails.com/wp-content/uploads/2024/12/mailextracter-final-domain-logo-188x44.png"
             class="h-8 w-auto"
             alt="Logo">
      </a>

      <!-- Menu (SHIFTED LEFT) -->
      <div class="hidden md:flex items-center gap-16 ml-20 text-[15px] font-medium text-gray-700">



       <div class="relative group">
          <a href="<?php echo home_url('/tools-template/'); ?>" class="flex items-center gap-1 hover:text-black transition">
           Tools
            <svg class="w-3 h-3 transition group-hover:rotate-180"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>

          <!-- Dropdown -->
          <!-- <div class="absolute left-1/2 -translate-x-1/2 top-full pt-4
                      opacity-0 invisible group-hover:opacity-100 group-hover:visible
                      transition duration-200">

            <div class="w-[260px] bg-white rounded-xl shadow-lg p-3 space-y-2">

              <a href="/blog" class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-sm">
                Blog
              </a>

              <a href="/tech-insights" class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-sm">
                Tech Insights
              </a>

            </div>
          </div> -->
        </div>




        <!-- <a href="<?php echo home_url('/features/'); ?>" class="hover:text-black transition"></a> -->



        <a href="/pricing-page" class="hover:text-black transition">Pricing</a>

        <!-- Blog Dropdown -->
        <div class="relative group">
          <a href="<?php echo home_url('/blog/'); ?>" class="flex items-center gap-1 hover:text-black transition">
            Blog
            <svg class="w-3 h-3 transition group-hover:rotate-180"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>

          <!-- Dropdown -->
          <!-- <div class="absolute left-1/2 -translate-x-1/2 top-full pt-4
                      opacity-0 invisible group-hover:opacity-100 group-hover:visible
                      transition duration-200">

            <div class="w-[260px] bg-white rounded-xl shadow-lg p-3 space-y-2">

              <a href="/blog" class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-sm">
                Blog
              </a>

              <a href="/tech-insights" class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-sm">
                Tech Insights
              </a>

            </div>
          </div> -->
        </div>



         <div class="relative group">
          <a href="<?php echo home_url('/#/'); ?>" class="flex items-center gap-1 hover:text-black transition">
           Services
            <svg class="w-3 h-3 transition group-hover:rotate-180"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </a>

          <!-- Dropdown -->
        

  <!-- DROPDOWN -->
 <!-- DROPDOWN -->
<div class="absolute left-1/2 -translate-x-1/2 top-full pt-6
            opacity-0 invisible group-hover:opacity-100 group-hover:visible
            transition-all duration-300 z-50">

  <!-- MAIN BOX -->
  <div class="relative w-[600px] rounded-[30px] p-6 overflow-hidden
              bg-[#f9faff] shadow-[0_25px_70px_rgba(0,0,0,0.15)]">

    <!-- GREEN GLOW CIRCLE -->
    <div class="absolute -top-24 -left-24 w-[280px] h-[280px]
                bg-[#00FF55] opacity-25 blur-[120px] rounded-full"></div>

    <!-- BLUE GLOW CIRCLE -->
    <div class="absolute -bottom-24 -right-24 w-[300px] h-[300px]
                bg-[#383CFF] opacity-25 blur-[120px] rounded-full"></div>

    <!-- EXTRA CENTER MIX GLOW -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                w-[250px] h-[250px]
                bg-gradient-to-r from-[#00FF55] to-[#383CFF]
                opacity-10 blur-[100px] rounded-full"></div>

    <!-- CONTENT -->
    <div class="relative grid grid-cols-2 gap-6 items-start">

      <!-- LEFT COLUMN -->
      <div class="space-y-4 pr-6 border-r border-gray-200">

        <a href="https://www.extractmails.com/email-scraper/" 
           class="flex items-center gap-3 group/item hover:translate-x-1 transition">
          <div class="w-10 h-10 rounded-lg 
                      bg-gradient-to-br from-[#383CFF] to-[#00FF55] 
                      flex items-center justify-center shadow-md">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/scraping-2.png" class="w-5 h-5">
          </div>
          <span class="text-[15px] font-medium text-gray-800 
                       transition group-hover/item:text-[#383CFF]">
            Email Scraper
          </span>
        </a>

        <a href="https://www.extractmails.com/bulk-email-finder/" 
           class="flex items-center gap-3 group/item hover:translate-x-1 transition">
          <div class="w-10 h-10 rounded-lg 
                      bg-gradient-to-br from-[#383CFF] to-[#00FF55] 
                      flex items-center justify-center shadow-md">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/emails-2.png" class="w-5 h-5">
          </div>
          <span class="text-[15px] font-medium text-gray-800 
                       transition group-hover/item:text-[#383CFF]">
            Bulk Email Finder
          </span>
        </a>

        <a href="https://www.extractmails.com/website-email-extraction-services/" 
           class="flex items-center gap-3 group/item hover:translate-x-1 transition">
          <div class="w-10 h-10 rounded-lg 
                      bg-gradient-to-br from-[#383CFF] to-[#00FF55] 
                      flex items-center justify-center shadow-md">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/email-website-1-1.png" class="w-5 h-5">
          </div>
          <span class="text-[15px] font-medium text-gray-800 
                       transition group-hover/item:text-[#383CFF]">
            Expert Email Extraction
          </span>
        </a>

      </div>

      <!-- RIGHT COLUMN -->
      <div class="space-y-4 pl-6">

        <a href="https://www.extractmails.com/email-extractor/" 
           class="flex items-center gap-3 group/item hover:translate-x-1 transition">
          <div class="w-10 h-10 rounded-lg 
                      bg-gradient-to-br from-[#383CFF] to-[#00FF55] 
                      flex items-center justify-center shadow-md">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/folder-1.png" class="w-5 h-5">
          </div>
          <span class="text-[15px] font-medium text-gray-800 
                       transition group-hover/item:text-[#383CFF]">
            Email Extractor
          </span>
        </a>

        <a href="https://www.extractmails.com/lead-generation-services/" 
           class="flex items-center gap-3 group/item hover:translate-x-1 transition">
          <div class="w-10 h-10 rounded-lg 
                      bg-gradient-to-br from-[#383CFF] to-[#00FF55] 
                      flex items-center justify-center shadow-md">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/list-1.png" class="w-5 h-5">
          </div>
          <span class="text-[15px] font-medium text-gray-800 
                       transition group-hover/item:text-[#383CFF]">
            Lead Generation
          </span>
        </a>

      </div>

    </div>
  </div>
</div>

        </div>



        <!-- <a href="/plugins" class="hover:text-black transition">Our Plugins</a> -->

      </div>

      <!-- Push Right Side -->
      <div class="ml-auto hidden md:flex items-center gap-6">

        <!-- Login -->
        <a href="#"
   id="openLoginModal"
   class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-black transition">

          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2"
                  d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5m0 0l-5-5m5 5H3"/>
          </svg>

          Login
        </a>


        




      </div>

      <!-- Mobile Toggle -->
      <button id="mobileToggle"
              class="md:hidden ml-auto p-2 rounded-lg bg-white/40 backdrop-blur border border-white/40">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu"
         class="hidden mt-3 rounded-xl p-4
                bg-white/30 backdrop-blur-md border border-white/40">

      <div class="flex flex-col gap-3 text-sm text-gray-700">

        <a href="/features" class="hover:text-black">Features</a>
        <a href="/pricing" class="hover:text-black">Pricing</a>
        <a href="/blog" class="hover:text-black">Blog</a>
        <a href="/tech-insights" class="hover:text-black">Tech Insights</a>
        <a href="/plugins" class="hover:text-black">Our Plugins</a>

        <a href="<?php echo wp_login_url(); ?>" class="pt-2 border-t">
          Login
        </a>

      </div>
    </div>

  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle functions
    function toggleElement(element, chevron = null) {
        const isHidden = element.classList.contains('hidden');
        element.classList.toggle('hidden', !isHidden);
        element.classList.toggle('block', isHidden);
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    // Products dropdown
    const productsToggle = document.getElementById('products-toggle');
    const productsMenu = document.getElementById('products-menu');
    const productsChevron = document.getElementById('products-chevron');
    
    productsToggle?.addEventListener('click', () => toggleElement(productsMenu, productsChevron));

    // Mobile products dropdown
    const mobileProductsToggle = document.getElementById('mobile-products-toggle');
    const mobileProductsMenu = document.getElementById('mobile-products-menu');
    const mobileProductsChevron = document.getElementById('mobile-products-chevron');
    
    mobileProductsToggle?.addEventListener('click', () => toggleElement(mobileProductsMenu, mobileProductsChevron));

    // Mobile menu
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuIcon = document.getElementById('mobile-menu-icon');
    
    mobileMenuToggle?.addEventListener('click', () => {
        const isHidden = mobileMenu.classList.contains('hidden');
        toggleElement(mobileMenu);
        mobileMenuIcon.innerHTML = isHidden ? 
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#products-dropdown') && productsMenu?.classList.contains('block')) {
            toggleElement(productsMenu, productsChevron);
        }
    });

    // Close mobile menu when link clicked
    mobileMenu?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            mobileMenuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
        });
    });

    // Close mobile menu on resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            mobileMenu.classList.add('hidden');
            mobileMenuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
        }
    });
});



document.getElementById('mobileToggle').addEventListener('click', () => {
  document.getElementById('mobileMenu').classList.toggle('hidden');
});


</script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector(".header-site-header");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 10) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });
});


//for this mobile service page

const mobileServicesToggle = document.getElementById('mobileServicesToggle');
const mobileServicesMenu = document.getElementById('mobileServicesMenu');

mobileServicesToggle?.addEventListener('click', () => {
  mobileServicesMenu.classList.toggle('hidden');
});
</script>










<!-- ================= LOGIN POPUP (TAILWIND MODAL) ================= -->

<!-- Overlay -->
<div id="loginModal"
     class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">

    <!-- Modal Box -->
    <div id="modalBox"
         class="relative w-[95%] max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden
                scale-95 opacity-0 transition-all duration-300">

        <!-- CLOSE BUTTON -->
        <button id="closeModal"
                class="absolute right-4 top-4 text-gray-500 hover:text-black text-3xl leading-none">
            &times;
        </button>

        <div class="grid md:grid-cols-2">

            <!-- LEFT IMAGE -->
            <div class="hidden md:block ">
                <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/BCC.png"
                     class="w-full h-full object-cover" alt="WP-1Click ">
            </div>

            <!-- RIGHT CONTENT -->
            <div class="p-8 text-center flex flex-col justify-center">

                <img src="https://www.extractmails.com/wp-content/uploads/2024/12/mailextracter-final-domain-logo-188x44.png"
                     class="mx-auto mb-6 w-[200px]" alt="WP-1Click logo ">

                <!-- separator -->
                <div class="flex items-center my-6">
                    <div class="flex-1 border-t"></div>
                    <span class="px-4 text-sm text-gray-500">continue with</span>
                    <div class="flex-1 border-t"></div>
                </div>

                <!-- Microsoft Login -->
                <div class="mb-4 flex items-center justify-center gap-3 border rounded-full py-3 hover:bg-gray-50 transition">
                    <?php Microsoft_login_logout_button(); ?>
                </div>

                <!-- Google Login -->
                <a href="<?php echo google_oauth_url(); ?>"
                   class="flex items-center justify-center gap-3 border rounded-full py-3 hover:bg-gray-50 transition">

                    <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/search-2-1.png" width="20" alt="WP-1Click Continue with Google">
                    Continue with Google
                </a>

                <!-- Terms -->
                <p class="text-xs text-gray-500 mt-6">
                    By proceeding, you agree to our <br>
                    <a href="https://www.extractmails.com/terms-and-conditions/" target="_blank" class="text-blue-600">
                        Terms of Use <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    and <br>
                    <a href="https://www.extractmails.com/privacy-and-policy/" target="_blank" class="text-blue-600">
                        Privacy Policy <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </p>

            </div>
        </div>
    </div>
</div>





<script>
document.addEventListener("DOMContentLoaded", function(){

    const modal = document.getElementById("loginModal");
    const modalBox = document.getElementById("modalBox");
    const closeBtn = document.getElementById("closeModal");

    // OPEN MODAL (when login clicked)
  const loginBtn = document.getElementById("openLoginModal");

loginBtn?.addEventListener("click", function(e){
    e.preventDefault();
    openModal();
});

    function openModal(){
        modal.classList.remove("hidden");
        modal.classList.add("flex");

        setTimeout(()=>{
            modalBox.classList.remove("scale-95","opacity-0");
            modalBox.classList.add("scale-100","opacity-100");
        },10);

        document.body.style.overflow="hidden";
    }

    function closeModal(){
        modalBox.classList.add("scale-95","opacity-0");
        modalBox.classList.remove("scale-100","opacity-100");

        setTimeout(()=>{
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow="auto";
        },250);
    }

    // close button
    closeBtn.addEventListener("click", closeModal);

    // outside click
    modal.addEventListener("click", function(e){
        if(e.target === modal) closeModal();
    });

    // ESC key
    document.addEventListener("keydown", function(e){
        if(e.key === "Escape") closeModal();
    });

});
</script>







