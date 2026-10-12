<?php
/**
 * WordPress Header with Tailwind CSS
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
 
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-E0GX3ZP2MP"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
 
  gtag('config', 'G-E0GX3ZP2MP');
</script>
 <!-- google-site-verification link -->
<meta name="google-site-verification" content="zkVK3j02IYx1DhzotbN-8amCneKT4-7o0tuGpgfq5r8" />
<!-- end -->


<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if ( apply_filters( 'astra_header_profile_gmpg_link', true ) ) {
	?>
	<link rel="profile" href="https://gmpg.org/xfn/11"> 
	<?php
} 
?>
    <?php wp_head(); ?>
</head>


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




@layer base {
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: 'Cormorant Garamond', serif;
    }

    p {
        font-family: 'Manrope', sans-serif;
    }
}




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

/* Reference-style framed navigation */
#mainHeader {
    padding: 0;
}

#mainHeader .header-outer-shell {
    position: relative;
}

#mainHeader .header-outer-shell::before {
    content: "";
    position: absolute;
    inset: -9px;
    z-index: 0;
    pointer-events: none;
    background: rgba(255, 255, 255, 0.3);
    border: 2px solid #fff;
    border-radius: 30px;
    box-shadow: 0 5px 2px rgba(55, 55, 55, .14);
}

#mainHeader .header-inner {
    position: relative;
    z-index: 1;
}



/* Desktop only */
@media (min-width: 768px) {

    /* Main header */
    #mainHeader {
        max-width: 1280px;
        margin: 0 auto;
        transition: max-width 0.9s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Header inner wrapper */
    #mainHeader .header-inner {
        transition: all 0.9s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Left section (logo + nav) */
    #mainHeader .header-inner > div:first-child {
        transition: gap 0.9s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Navigation elements */
    #mainHeader .desktop-hide-on-scroll {
        opacity: 1;
        transform: translateX(0);

        transition:
            opacity 0.25s ease,
            transform 0.35s ease;

        /* Show AFTER header expands */
        transition-delay: 0.3s;
    }

    /* Collapsed header */
    #mainHeader.header-collapsed {
        max-width: 220px;
        margin: 0 auto;
    }

    /* Center everything */
   

    /* Remove gap so logo is perfectly centered */
   

    /* Hide menu immediately */
   
 /* Smooth movement */
.left-section,
.right-section {
    transition: all 0.8s cubic-bezier(.4,0,.2,1);
}

/* Menu items */
.desktop-hide-on-scroll {
    transition:
        opacity .6s ease,
        transform .6s ease;
}

/* Header collapsed state */
#mainHeader.header-collapsed .desktop-hide-on-scroll {
    opacity: 0;
    transform: translateY(-10px) scale(.95);
    pointer-events: none;
}

/* Left side moves toward center */
#mainHeader.header-collapsed .left-section {
    transform: translateX(140px);
}

/* Right side moves toward center */
#mainHeader.header-collapsed .right-section {
    transform: translateX(-140px);
}

/* Shrink header */
#mainHeader.header-collapsed {
    max-width: 210px;
}


.left-section a img{
    transition: all .8s cubic-bezier(.4,0,.2,1);
}

}
</style>
<!-- ================= HEADER ================= -->

<header id="mainHeader" class="fixed top-4 left-0 right-0 z-50">

    <div class="header-outer-shell max-w-[1280px] mx-auto px-0">

        <!-- NAVBAR -->
       <div class="header-inner bg-white rounded-[18px]
            shadow-[0_4px_20px_rgba(0,0,0,0.08)]
            h-[64px] px-6 flex items-center justify-between">

            <!-- LEFT -->
            <div class="flex items-center gap-14">

                <!-- LOGO -->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="shrink-0">
                    <img src="https://www.mailsextract.com/wp-content/uploads/2024/images/mailextracter.png"
                         alt="MailsExtract"
                         class="h-9 w-auto">
                </a>

                <!-- DESKTOP MENU -->
                <nav class="desktop-menu desktop-hide-on-scroll hidden md:flex items-center gap-9">

                    <!-- HOME -->
                    

                    <!-- TOOLS -->
                    <a href="<?php echo esc_url(home_url('/email-finder')); ?>" class="hover:text-black transition">
                        Tools
                    </a>


                       <a href="<?php echo esc_url(home_url('/about-us')); ?>" class="hover:text-black transition">
                        About Us
                    </a>


                    <!-- SERVICES -->
                  

               <div class="relative group">

    <!-- Blog Menu -->
    <a href="<?php echo esc_url(home_url('/blog/')); ?>"
       class="flex items-center gap-1 hover:text-blue-600 transition">

       Services
        <svg class="w-3 h-3"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M19 9l-7 7-7-7"/>
        </svg>

    </a>

    <!-- Mega Menu -->
    <div class="absolute left-1/2 -translate-x-1/2 top-full pt-7
                opacity-0 invisible
                group-hover:opacity-100
                group-hover:visible
                transition-all duration-300 z-50">

        <div class="w-[720px] bg-white rounded-3xl shadow-2xl border border-gray-200 p-6">

            <!-- Heading -->
          <h2 class="text-3xl font-semibold text-center mb-6 mb-6 ">
    Services
</h2>
<!-- <div class=" mb-6 border-t-2 border-blue-300"></div> -->

            <div class="grid grid-cols-2 gap-8 mb-6">

                <!-- Left -->
                <div class="space-y-1 border-r-2 border-blue-300 pr-2">

                  <a href="https://www.mailsextract.com/email-parser/"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/parsing-3-4.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
       Email Parser
    </span>

</a>

                    <a href="https://www.mailsextract.com/email-data-extraction/"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/data-1-8.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
       Email Data Extraction 
    </span>

</a>
                   <a href="#"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/json-file-1.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
       Email to JSON Services 
    </span>

</a>

                    <a href="#"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/deep-learning-1.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
        Email Automation 
    </span>

</a>

                   <a href="#"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/campaign-1-1.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
        Email Marketing Services
    </span>

</a>
                </div>

                <!-- Right -->
                <div class="space-y-4">

                    <a href="#"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/lead-generation-2.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
        Lead Enrichment 
    </span>

</a>

                    <a href="#"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/verify-1.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
        Email Verification 
    </span>

</a>

                   <a href="#"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/integration-9-1.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
       Webhook Integration
    </span>

</a>

                   <a href="#"
   class="flex items-center gap-4 px-2 py-1 rounded-xl transition">

    <img src="https://www.mailsextract.com/wp-content/uploads/2026/08/data-cleaning-1.png"
         class="w-6 h-6">

    <span class="text-[20px] px-4 py-2 rounded-xl
                 transition
                 hover:bg-[#00A1FF]
                 hover:text-white">
       Data Cleansing Services
    </span>

</a>

                </div>

            </div>

        </div>

    </div>

</div>

                    <!-- PRICING -->
                 
                    <!-- BLOG -->
                    <div class="relative group">

                        <a href="<?php echo esc_url(home_url('/blog/')); ?>"
                           class="flex items-center gap-1 hover:text-black transition">
                            Blog

                            <svg class="w-3 h-3 mt-[1px]"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-width="2"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>

                        <!-- DROPDOWN -->
                        <div class="absolute top-full left-0 pt-5 opacity-0 invisible
                                    group-hover:opacity-100 group-hover:visible
                                    transition duration-200">

                            <div class="w-[220px] bg-white rounded-2xl shadow-xl p-3 border border-gray-100">

                                <a href="/blog"
                                   class="block px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                                    Blog
                                </a>

                                <a href="/tech-insights"
                                   class="block px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                                    Tech Insights
                                </a>

                            </div>
                        </div>
                    </div>

                    <!-- CONTACT -->
                    <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="hover:text-black transition">
                        Contact Us
                    </a>

                </nav>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center">
                

                <!-- LOGIN BUTTON -->
               <!-- <a href="#"
   id="openLoginModal"
   class="login-button desktop-hide-on-scroll hidden md:flex items-center justify-center
                          bg-black text-white
                          h-[42px] px-9 rounded-full
                          hover:bg-[#111] transition">

                    Login
                </a> -->

                 <?php if (is_user_logged_in()) : ?>
        <!-- If user is logged in, show Logout button -->
        <a href="<?php echo wp_logout_url(home_url()); ?>" class="login-button desktop-hide-on-scroll hidden md:flex items-center justify-center
                          bg-black text-white
                          h-[42px] px-9 rounded-full
                          hover:bg-[#111] transition"> Logout</a>
    <?php else : ?>
        <!-- If user is NOT logged in, show Login button -->
        <a href="#" class="login-button desktop-hide-on-scroll hidden md:flex items-center justify-center
                          bg-black text-white
                          h-[42px] px-9 rounded-full
                          hover:bg-[#111] transition">Login</a>
    <?php endif; ?>

                <!-- MOBILE TOGGLE -->
                <button id="mobileToggle"
                        class="md:hidden flex items-center justify-center w-11 h-11 rounded-xl border border-gray-200">

                    <svg class="w-6 h-6"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

            </div>

        </div>

        <!-- MOBILE MENU -->
        <div id="mobileMenu"
             class="hidden mt-3 bg-white rounded-2xl shadow-xl p-5">

            <div class="flex flex-col gap-4">

             

                <a href="/features" class="text-sm font-medium text-gray-800">
                    Features
                </a>

                <a href="/blog" class="text-sm font-medium text-gray-800">
                    Blog
                </a>

                <a href="/pricing" class="text-sm font-medium text-gray-800">
                    Pricing
                </a>

                <a href="/contact-us" class="text-sm font-medium text-gray-800">
                    Contact Us
                </a>

                <a href="#"
                   class="mt-2 bg-black text-white text-center py-3 rounded-full text-sm font-medium">
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





<script>
document.addEventListener("DOMContentLoaded", function () {

    if (window.innerWidth < 768) return;

    const header = document.getElementById("mainHeader");

    function checkScroll() {
        if (window.scrollY > 120) {
            header.classList.add("header-collapsed");
        } else {
            header.classList.remove("header-collapsed");
        }
    }

    checkScroll();
    window.addEventListener("scroll", checkScroll);

    // Expand on hover
    header.addEventListener("mouseenter", () => {
        if (window.scrollY > 120) {
            header.classList.remove("header-collapsed");
        }
    });

    // Collapse again when leaving
    header.addEventListener("mouseleave", () => {
        if (window.scrollY > 120) {
            header.classList.add("header-collapsed");
        }
    });

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

                <img src="https://www.mailsextract.com/wp-content/uploads/2024/images/mailextracter.png"
                     class="mx-auto mb-6 w-[200px]" alt="WP-1Click logo ">

                <!-- separator -->
                <div class="flex items-center my-6">
                    <div class="flex-1 border-t"></div>
                    <span class="px-4 text-sm text-gray-500">continue with</span>
                    <div class="flex-1 border-t"></div>
                </div>

                <!-- Microsoft Login -->
                <div class="mb-4">
                    <?php //echo mailsextract_microsoft_login_button( 'text-center' ); ?>
                </div>

                <!-- Google Login -->
                <!-- <a href="<?php// echo google_oauth_url(); ?>"
                   class="flex items-center justify-center gap-3 border rounded-full py-3 hover:bg-gray-50 transition">

                    <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/search-2-1.png" width="20" alt="WP-1Click Continue with Google">
                    Continue with Google
                </a> -->

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
    document.addEventListener("click", function(e){
        const link = e.target.closest("a");
        if(!link) return;

        const text = link.textContent.toLowerCase();

        if(text.includes("login") || text.includes("my account")){
            e.preventDefault();
            openModal();
        }
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




