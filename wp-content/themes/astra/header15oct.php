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

if (! defined('ABSPATH')) {
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

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-MSDWPEKZ0X');
  </script>
  <!-- 27f8f40e874ebd5bc8b9c9fda7e030ae9cab02cc -->

  
  <meta
    charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
if (apply_filters('astra_header_profile_gmpg_link', true)) {
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







  <style>
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes slideInFromTop {
      from {
        transform: translateY(-10px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .animate-in {
      animation-duration: 200ms;
    }

    .fade-in {
      animation-name: fadeIn;
    }

    .slide-in-from-top {
      animation-name: slideInFromTop;
    }

    .slide-in-from-top-2 {
      animation-name: slideInFromTop;
      animation-duration: 150ms;
    }


    header,
    header.fixed,
    body > header {
      background: transparent !important;
      border: none !important;
      box-shadow: none !important;
      backdrop-filter: none !important;
      -webkit-backdrop-filter: none !important;
      border-radius: 0 !important;
    }

    /* Clearance for inner dropdown/tool pages so fixed header does not overlap content */
    body:not(.home) main,
    body:not(.home) .extractmail-page,
    body:not(.home) #content {
      padding-top: 105px !important;
    }

    .header-site-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      margin: 0 auto;
      width: 100%;
      max-width: 1600px;
      z-index: 100000;
      background: transparent !important;
      border: none !important;
      box-shadow: none !important;
      transition: background 0.3s ease;
    }

    .header-site-header.scrolled {
      background: transparent !important;
    }

    /* ExtractMails SaaS Header System from ExtractMails-Website-Updated */
    .glass-header-nav {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-height: 64px;
      padding: 8px 24px;
      background: linear-gradient(125deg, rgba(7, 28, 80, 0.94), rgba(7, 28, 80, 0.90));
      border: 1px solid rgba(213, 234, 253, 0.25);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 12px 32px rgba(2, 11, 43, 0.22);
      border-radius: 16px;
      transition: all 0.3s ease;
    }

    .glass-header-nav.scrolled {
      background: linear-gradient(125deg, rgba(7, 28, 80, 0.98), rgba(7, 28, 80, 0.96));
      border-color: rgba(213, 234, 253, 0.35);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 16px 36px rgba(2, 11, 43, 0.35);
    }

    .glass-header-logo {
      display: inline-flex;
      align-items: center;
      background: transparent;
      padding: 0;
      border: none;
      box-shadow: none;
      transition: transform 0.2s ease, opacity 0.2s ease;
      flex-shrink: 0;
      z-index: 2;
    }

    .glass-header-logo:hover {
      transform: translateY(-1px);
      opacity: 0.85;
    }

    .glass-header-logo img {
      height: 64px !important;
      width: auto !important;
      display: block;
      object-fit: contain;
      margin: -8px 0;
    }

    /* Dead-centered menu according to Image 2 */
    .glass-nav-center {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      font-size: 15px;
      font-weight: 500;
    }

    @media (min-width: 901px) {
      .glass-nav-center {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
      }
    }

    .glass-nav-link {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      color: #D5EAFD !important;
      font-size: 15px;
      font-weight: 500;
      text-decoration: none !important;
      padding: 6px 8px;
      border-radius: 8px;
      transition: color 0.2s ease, background 0.2s ease;
    }

    .glass-nav-link:hover,
    .glass-nav-link:focus {
      color: #ffffff !important;
      background: rgba(255, 255, 255, 0.06);
    }

    .glass-nav-link svg {
      width: 14px;
      height: 14px;
      color: #D5EAFD;
      transition: transform 0.2s ease, color 0.2s ease;
    }

    .glass-nav-link:hover svg {
      color: #ffffff;
    }

    /* Right side actions according to Image 2 */
    .glass-header-actions {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-left: auto;
      flex-shrink: 0;
      z-index: 2;
    }

    /* Sign Up: Solid blue pill button */
    .glass-signup-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 40px;
      padding: 8px 24px;
      background: #0048C2;
      border: 1px solid #0048C2;
      border-radius: 999px;
      font-size: 14px;
      font-weight: 600;
      color: #ffffff !important;
      text-decoration: none !important;
      box-shadow: 0 4px 14px rgba(0, 72, 194, 0.35);
      transition: all 0.2s ease;
      white-space: nowrap;
      cursor: pointer;
    }

    .glass-signup-btn:hover {
      background: #05289B;
      border-color: #05289B;
      color: #ffffff !important;
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(0, 72, 194, 0.5);
    }

    /* Login to App: Translucent/bordered pill button */
    .glass-login-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 40px;
      padding: 8px 22px;
      border: 1px solid rgba(255, 255, 255, 0.45);
      border-radius: 999px;
      font-size: 14px;
      font-weight: 600;
      background: transparent;
      color: #ffffff !important;
      text-decoration: none !important;
      transition: all 0.2s ease;
      white-space: nowrap;
      cursor: pointer;
    }

    .glass-login-btn:hover {
      background: rgba(255, 255, 255, 0.14);
      border-color: rgba(255, 255, 255, 0.85);
      color: #ffffff !important;
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
    }

    /* Mobile toggle: STRICTLY HIDDEN ON DESKTOP */
    .glass-mobile-btn {
      display: none !important;
      align-items: center;
      justify-content: center;
      padding: 8px;
      border-radius: 9px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #ffffff;
      cursor: pointer;
      margin-left: auto;
      transition: background 0.2s ease;
    }

    .glass-mobile-btn:hover {
      background: rgba(255, 255, 255, 0.18);
    }

    @media (max-width: 900px) {
      .glass-nav-center {
        display: none !important;
      }
      .glass-header-actions {
        display: none !important;
      }
      .glass-mobile-btn {
        display: inline-flex !important;
      }
    }

    .glass-mobile-menu {
      background: linear-gradient(125deg, rgba(7, 28, 80, 0.98), rgba(7, 28, 80, 0.95));
      border: 1px solid rgba(213, 234, 253, 0.25);
      border-radius: 16px;
      padding: 18px;
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
    }

    .glass-mobile-menu a {
      color: #D5EAFD;
      text-decoration: none;
      font-size: 15px;
      font-weight: 500;
      padding: 8px 4px;
      transition: color 0.2s ease;
    }

    .glass-mobile-menu a:hover {
      color: #ffffff;
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
      padding: 1px;
      background:
        linear-gradient(135deg,
          transparent 0%,
          rgba(255, 255, 255, 0.9) 22%,
          rgba(255, 255, 255, 0.9) 72%,
          transparent 88%);
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

    /* Header Dropdowns (Pixel-perfect match to Image 2) */
    .tools-mega-menu {
      position: relative;
      width: 530px !important;
      max-width: 95vw !important;
      border-radius: 28px !important;
      padding: 24px 26px !important;
      overflow: hidden !important;
      background: #f8faff !important;
      border: 1px solid rgba(255, 255, 255, 0.95) !important;
      box-shadow: 0 20px 60px rgba(0, 15, 60, 0.14), 0 2px 10px rgba(0, 0, 0, 0.04) !important;
    }

    .services-mega-menu {
      position: relative;
      width: 610px !important;
      max-width: 95vw !important;
      border-radius: 28px !important;
      padding: 24px 26px !important;
      overflow: hidden !important;
      background: #f8faff !important;
      border: 1px solid rgba(255, 255, 255, 0.95) !important;
      box-shadow: 0 20px 60px rgba(0, 15, 60, 0.14), 0 2px 10px rgba(0, 0, 0, 0.04) !important;
    }

    .tools-mega-grid {
      position: relative;
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      gap: 20px !important;
      align-items: center !important;
      z-index: 2 !important;
    }

    .tools-mega-col {
      display: flex !important;
      flex-direction: column !important;
      gap: 16px !important;
    }

    .tools-mega-col.has-divider {
      border-right: 1px solid rgba(0, 0, 0, 0.08) !important;
      padding-right: 20px !important;
    }

    .tools-mega-item {
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
      text-decoration: none !important;
      transition: transform 0.2s ease, opacity 0.2s ease !important;
      cursor: pointer !important;
    }

    .tools-mega-item:hover {
      transform: translateX(3px) !important;
    }

    .tools-mega-icon {
      width: 44px !important;
      height: 44px !important;
      border-radius: 12px !important;
      background: linear-gradient(135deg, #383CFF 0%, #00FF55 100%) !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      flex-shrink: 0 !important;
      box-shadow: 0 4px 12px rgba(56, 60, 255, 0.22) !important;
    }

    .tools-mega-icon img {
      width: 22px !important;
      height: 22px !important;
      object-fit: contain !important;
      display: block !important;
    }

    .tools-mega-text {
      font-size: 15px !important;
      font-weight: 500 !important;
      color: #1e293b !important;
      white-space: nowrap !important;
      line-height: 1.35 !important;
      transition: color 0.2s ease !important;
    }

    .tools-mega-item:hover .tools-mega-text {
      color: #383CFF !important;
    }

    .tools-glow-green {
      position: absolute;
      top: -80px;
      left: -80px;
      width: 260px;
      height: 260px;
      background: #00FF55;
      opacity: 0.25;
      filter: blur(80px);
      border-radius: 50%;
      pointer-events: none;
    }

    .tools-glow-blue {
      position: absolute;
      bottom: -80px;
      right: -80px;
      width: 280px;
      height: 280px;
      background: #383CFF;
      opacity: 0.25;
      filter: blur(80px);
      border-radius: 50%;
      pointer-events: none;
    }

    .tools-glow-center {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 220px;
      height: 220px;
      background: linear-gradient(135deg, #00FF55, #383CFF);
      opacity: 0.10;
      filter: blur(70px);
      border-radius: 50%;
      pointer-events: none;
    }
  </style>
  <header class="fixed top-4 left-0 right-0 z-50">

    <div class="max-w-[1600px] mx-auto px-6">

      <!-- Glass Navbar (ExtractMails SaaS Theme) -->
      <div class="glass-header-nav flex items-center px-6 py-3">

        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>"
          class="glass-header-logo" aria-label="ExtractMails">
          <img src="https://www.extractmails.com/wp-content/uploads/2024/12/ExtractMails-Logo-2.png"
            alt="ExtractMails">
        </a>

        <!-- Menu (CENTERED ACCORDING TO IMAGE 2) -->
        <nav class="glass-nav-center" aria-label="Main navigation">

          <div class="relative group">
            <a href="<?php echo home_url('/tools/'); ?>"
              class="glass-nav-link">
              Tools
              <svg class="w-3.5 h-3.5 transition group-hover:rotate-180" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>

            </a>


            <!-- DROPDOWN (Matches Image 2) -->
            <div class="absolute left-1/2 -translate-x-1/2 top-full pt-4
            opacity-0 invisible group-hover:opacity-100 group-hover:visible
            transition-all duration-200 z-50">

              <!-- MAIN BOX -->
              <div class="tools-mega-menu">

                <!-- GREEN GLOW CIRCLE -->
                <div class="tools-glow-green"></div>

                <!-- BLUE GLOW CIRCLE -->
                <div class="tools-glow-blue"></div>

                <!-- EXTRA CENTER MIX GLOW -->
                <div class="tools-glow-center"></div>

                <!-- CONTENT -->
                <div class="tools-mega-grid">

                  <!-- LEFT COLUMN -->
                  <div class="tools-mega-col has-divider">

                    <a href="<?php echo home_url('/tools/reverse-email-lookup/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/email-8-1-3.png" alt="Reverse Email Lookup">
                      </div>
                      <span class="tools-mega-text">
                        Reverse Email Lookup
                      </span>
                    </a>

                    <a href="<?php echo home_url('/tools/gmail-generator/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/gmail-1-1.png" alt="Gmail Generator">
                      </div>
                      <span class="tools-mega-text">
                        Gmail Generator
                      </span>
                    </a>

                  </div>

                  <!-- RIGHT COLUMN -->
                  <div class="tools-mega-col">

                    <a href="<?php echo home_url('/tools/email-spam-checker/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/message-1.png" alt="Email Spam Checker">
                      </div>
                      <span class="tools-mega-text">
                        Email Spam Checker
                      </span>
                    </a>

                    <a href="<?php echo home_url('/tools/ai-email-writer/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ai-3-1.png" alt="AI Email Writer">
                      </div>
                      <span class="tools-mega-text">
                        AI Email Writer
                      </span>
                    </a>

                  </div>

                </div>
              </div>
            </div>


          </div>




          <!-- <a href="<?php echo home_url('/features/'); ?>"
          class="hover:text-black transition"></a> -->


          <!-- Pricing -->
          <div class="relative group">
            <a href="<?php echo home_url('/pricing-page/'); ?>"
              class="glass-nav-link">
              Pricing
            </a>
          </div>

          <!-- Blog -->
          <div class="relative group">
            <a href="<?php echo home_url('/blog/'); ?>"
              class="glass-nav-link">
              Blog
            </a>
          </div>

          <!-- Services -->
          <div class="relative group">
            <a href="<?php echo home_url('/#/'); ?>"
              class="glass-nav-link">
              Services
              <svg class="w-3.5 h-3.5 transition group-hover:rotate-180" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </a>

            <!-- Dropdown -->


            <!-- DROPDOWN (Matches Image 2) -->
            <div class="absolute left-1/2 -translate-x-1/2 top-full pt-4
            opacity-0 invisible group-hover:opacity-100 group-hover:visible
            transition-all duration-200 z-50">

              <!-- MAIN BOX -->
              <div class="services-mega-menu">

                <!-- GREEN GLOW CIRCLE -->
                <div class="tools-glow-green"></div>

                <!-- BLUE GLOW CIRCLE -->
                <div class="tools-glow-blue"></div>

                <!-- EXTRA CENTER MIX GLOW -->
                <div class="tools-glow-center"></div>

                <!-- CONTENT -->
                <div class="tools-mega-grid">

                  <!-- LEFT COLUMN -->
                  <div class="tools-mega-col has-divider">

                    <a href="<?php echo home_url('/email-scraper/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/scraping-2.png" alt="Email Scraper">
                      </div>
                      <span class="tools-mega-text">
                        Email Scraper
                      </span>
                    </a>

                    <a href="<?php echo home_url('/bulk-email-finder/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/emails-2.png" alt="Bulk Email Finder">
                      </div>
                      <span class="tools-mega-text">
                        Bulk Email Finder
                      </span>
                    </a>

                    <a href="<?php echo home_url('/website-email-extraction-services/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/email-website-1-1.png" alt="Expert Email Extraction">
                      </div>
                      <span class="tools-mega-text">
                        Expert Email Extraction
                      </span>
                    </a>

                    <a href="<?php echo home_url('/email-marketing-automation/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/settings-1.png" alt="Email Marketing Automation">
                      </div>
                      <span class="tools-mega-text">
                        Email Marketing Automation
                      </span>
                    </a>

                  </div>

                  <!-- RIGHT COLUMN -->
                  <div class="tools-mega-col">

                    <a href="<?php echo home_url('/email-extractor/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/folder-1.png" alt="Email Extractor">
                      </div>
                      <span class="tools-mega-text">
                        Email Extractor
                      </span>
                    </a>

                    <a href="<?php echo home_url('/lead-generation-services/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/list-1.png" alt="Lead Generation">
                      </div>
                      <span class="tools-mega-text">
                        Lead Generation
                      </span>
                    </a>

                    <a href="<?php echo home_url('/email-deliverability/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/deliverable-1.png" alt="Email Deliverability">
                      </div>
                      <span class="tools-mega-text">
                        Email Deliverability
                      </span>
                    </a>

                    <a href="<?php echo home_url('/cold-outreach/'); ?>"
                      class="tools-mega-item">
                      <div class="tools-mega-icon">
                        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/deliverable-1.png" alt="Cold Outreach">
                      </div>
                      <span class="tools-mega-text">
                        Cold Outreach
                      </span>
                    </a>

                  </div>

                </div>
              </div>
            </div>

          </div>





        </nav>

        <!-- Right Action Buttons (According to Image 2) -->
        <div class="glass-header-actions">
          <!-- Sign Up -->
          <a href="https://www.extractmails.com/google.php" class="glass-signup-btn">
            Sign Up
          </a>

          <!-- Login to App -->
          <a href="https://app.extractmails.com/" class="glass-login-btn">
            Login to App
          </a>
        </div>

        <!-- Mobile Toggle (strictly hidden on desktop) -->
        <button id="mobileToggle" class="glass-mobile-btn" aria-label="Toggle navigation">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

      </div>

      <!-- Mobile Menu -->
      <div id="mobileMenu" class="hidden mt-3 glass-mobile-menu">
        <div class="flex flex-col gap-2 text-sm">
          <a href="<?php echo home_url('/tools/'); ?>">Tools</a>
          <a href="<?php echo home_url('/pricing-page/'); ?>">Pricing</a>
          <a href="<?php echo home_url('/blog/'); ?>">Blog</a>
          <a href="<?php echo home_url('/#/'); ?>">Services</a>
          <div style="display:flex; flex-direction:column; gap:8px; padding-top:12px; margin-top:6px; border-top:1px solid rgba(255,255,255,0.15);">
            <a href="https://www.extractmails.com/google.php" class="glass-signup-btn" style="text-align:center;">Sign Up</a>
            <a href="https://app.extractmails.com/" class="glass-login-btn" style="text-align:center;">Login to App</a>
          </div>
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

      mobileProductsToggle?.addEventListener('click', () => toggleElement(mobileProductsMenu,
        mobileProductsChevron));

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
          mobileMenuIcon.innerHTML =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
        });
      });

      // Close mobile menu on resize
      window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
          mobileMenu.classList.add('hidden');
          mobileMenuIcon.innerHTML =
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
        }
      });
    });



    document.getElementById('mobileToggle').addEventListener('click', () => {
      document.getElementById('mobileMenu').classList.toggle('hidden');
    });
  </script>



  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const header = document.querySelector(".header-site-header");

      window.addEventListener("scroll", function() {
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
  <div id="loginModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm">

    <!-- Modal Box -->
    <div id="modalBox" class="relative w-[95%] max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden
                scale-95 opacity-0 transition-all duration-300">

      <!-- CLOSE BUTTON -->
      <button id="closeModal" class="absolute right-4 top-4 text-gray-500 hover:text-black text-3xl leading-none">
        &times;
      </button>

      <div class="grid md:grid-cols-2">

        <!-- LEFT IMAGE -->
        <div class="hidden md:block ">
          <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/BCC.png" class="w-full h-full object-cover"
            alt="WP-1Click ">
        </div>

        <!-- RIGHT CONTENT -->
        <div class="p-8 text-center flex flex-col justify-center">

          <img src="https://www.extractmails.com/wp-content/uploads/2024/12/mailextracter-final-domain-logo-188x44.png"
            alt="Logo" class="mx-auto mb-6 w-[200px]" alt="WP-1Click logo ">

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

            <img src="https://www.wp-1click.com/wp-content/uploads/2026/02/search-2-1.png" alt="Logo" width="20"
              alt="WP-1Click Continue with Google">
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
    document.addEventListener("DOMContentLoaded", function() {

      const modal = document.getElementById("loginModal");
      const modalBox = document.getElementById("modalBox");
      const closeBtn = document.getElementById("closeModal");

      // OPEN MODAL (when login clicked)
      const loginBtn = document.getElementById("openLoginModal");
      const mobileLoginBtn = document.getElementById("openMobileLoginModal");

      loginBtn?.addEventListener("click", function(e) {
        e.preventDefault();
        openModal();
      });

      mobileLoginBtn?.addEventListener("click", function(e) {
        e.preventDefault();
        openModal();
      });

      function openModal() {
        modal.classList.remove("hidden");
        modal.classList.add("flex");

        setTimeout(() => {
          modalBox.classList.remove("scale-95", "opacity-0");
          modalBox.classList.add("scale-100", "opacity-100");
        }, 10);

        document.body.style.overflow = "hidden";
      }

      function closeModal() {
        modalBox.classList.add("scale-95", "opacity-0");
        modalBox.classList.remove("scale-100", "opacity-100");

        setTimeout(() => {
          modal.classList.add("hidden");
          modal.classList.remove("flex");
          document.body.style.overflow = "auto";
        }, 250);
      }

      // close button
      closeBtn.addEventListener("click", closeModal);

      // outside click
      modal.addEventListener("click", function(e) {
        if (e.target === modal) closeModal();
      });

      // ESC key
      document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") closeModal();
      });

    });
  </script>