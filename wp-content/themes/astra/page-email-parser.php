<?php
/**
 * Template Name: Email Parser Landing Page
 * Template Post Type: page
 * 
 * ExtractMails - Intelligent Email Parser & Data Extraction Landing Page
 * High-Throughput Email Intelligence & Automated Business Ingestion
 */

// Safe fallback for home_url if executed standalone
if (!function_exists('home_url')) {
    function home_url($path = '/') {
        return $path;
    }
}
if (!function_exists('esc_url')) {
    function esc_url($url) {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}

// Enqueue Google Fonts (DM Sans, JetBrains Mono, Material Symbols) into wp_head when running within WordPress
if (function_exists('add_action')) {
    add_action('wp_head', function() {
        ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
        <?php
    }, 1);
}

// Load WordPress Theme Header
if (function_exists('get_header')) {
    get_header();
} else {
    // Standalone HTML5 Document Fallback
    ?>
    <!DOCTYPE html>
    <html class="scroll-smooth" lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ExtractMails - High-Throughput Email Intelligence &amp; Data Extraction</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <?php
}
?>

<!-- Dependencies & Tailwind Configuration -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "tertiary-fixed-dim": "#abc7ff",
        "on-secondary-fixed": "#001158",
        "outline-variant": "#c3c6d6",
        "primary-container": "#0048c2",
        "surface-container-lowest": "#ffffff",
        "on-tertiary-fixed": "#001b3f",
        "tertiary": "#003a7b",
        "secondary-container": "#778eff",
        "surface-container": "#e9edff",
        "primary": "#003490",
        "on-error": "#ffffff",
        "secondary": "#3d54c3",
        "on-surface-variant": "#434654",
        "surface-dim": "#ccdaff",
        "surface-variant": "#d9e2ff",
        "on-tertiary": "#ffffff",
        "error-container": "#ffdad6",
        "surface-tint": "#1e55ce",
        "on-secondary": "#ffffff",
        "inverse-primary": "#b4c5ff",
        "surface": "#faf9ff",
        "inverse-on-surface": "#edf0ff",
        "on-error-container": "#93000a",
        "secondary-fixed-dim": "#b9c3ff",
        "on-secondary-fixed-variant": "#203aaa",
        "surface-container-high": "#e1e8ff",
        "on-surface": "#0b1b38",
        "on-secondary-container": "#002087",
        "on-primary-fixed": "#00174b",
        "error": "#ba1a1a",
        "on-tertiary-container": "#abc6ff",
        "surface-container-low": "#f1f3ff",
        "primary-fixed-dim": "#b4c5ff",
        "primary-fixed": "#dbe1ff",
        "surface-container-highest": "#d9e2ff",
        "outline": "#737685",
        "surface-bright": "#faf9ff",
        "tertiary-container": "#0050a6",
        "on-primary-fixed-variant": "#003ea8",
        "on-tertiary-fixed-variant": "#004590",
        "inverse-surface": "#22304f",
        "on-primary": "#ffffff",
        "on-primary-container": "#b4c4ff",
        "secondary-fixed": "#dee1ff",
        "on-background": "#0b1b38",
        "background": "#faf9ff",
        "tertiary-fixed": "#d7e2ff"
      },
      borderRadius: {
        "DEFAULT": "0.25rem",
        "lg": "0.5rem",
        "xl": "0.75rem",
        "full": "9999px"
      },
      spacing: {
        "margin": "1.5rem",
        "gutter-desktop": "2rem",
        "margin-desktop": "3rem",
        "space-sm": "0.5rem",
        "space-xs": "0.25rem",
        "gutter": "1.5rem",
        "space-lg": "1.5rem",
        "space-md": "1rem",
        "space-xl": "2.5rem"
      },
      fontFamily: {
        "label-sm": ["DM Sans", "sans-serif"],
        "headline-lg-mobile": ["DM Sans", "sans-serif"],
        "headline-md": ["DM Sans", "sans-serif"],
        "body-sm": ["DM Sans", "sans-serif"],
        "body-md": ["DM Sans", "sans-serif"],
        "headline-md-mobile": ["DM Sans", "sans-serif"],
        "display-xl-mobile": ["DM Sans", "sans-serif"],
        "label-md": ["DM Sans", "sans-serif"],
        "title-md": ["DM Sans", "sans-serif"],
        "headline-lg": ["DM Sans", "sans-serif"],
        "body-lg": ["DM Sans", "sans-serif"],
        "title-lg": ["DM Sans", "sans-serif"],
        "display-xl": ["DM Sans", "sans-serif"]
      },
      fontSize: {
        "label-sm": ["11px", { lineHeight: "16px", letterSpacing: "0.04em", fontWeight: "600" }],
        "headline-lg-mobile": ["32px", { lineHeight: "40px", letterSpacing: "-0.02em", fontWeight: "700" }],
        "headline-md": ["32px", { lineHeight: "40px", letterSpacing: "-0.02em", fontWeight: "600" }],
        "body-sm": ["13px", { lineHeight: "20px", letterSpacing: "0.005em", fontWeight: "400" }],
        "body-md": ["15px", { lineHeight: "24px", letterSpacing: "0em", fontWeight: "400" }],
        "headline-md-mobile": ["26px", { lineHeight: "34px", letterSpacing: "-0.015em", fontWeight: "600" }],
        "display-xl-mobile": ["40px", { lineHeight: "48px", letterSpacing: "-0.025em", fontWeight: "700" }],
        "label-md": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "500" }],
        "title-md": ["18px", { lineHeight: "26px", letterSpacing: "-0.01em", fontWeight: "600" }],
        "headline-lg": ["44px", { lineHeight: "52px", letterSpacing: "-0.025em", fontWeight: "700" }],
        "body-lg": ["18px", { lineHeight: "28px", letterSpacing: "-0.005em", fontWeight: "400" }],
        "title-lg": ["22px", { lineHeight: "30px", letterSpacing: "-0.015em", fontWeight: "600" }],
        "display-xl": ["64px", { lineHeight: "72px", letterSpacing: "-0.03em", fontWeight: "700" }]
      }
    }
  }
};
</script>

<style>
  /* ========================================================= */
  /* 1. CRITICAL: MATERIAL SYMBOLS OUTLINED ICON DEFINITION   */
  /* ========================================================= */
  .email-parser-page .material-symbols-outlined {
    font-family: 'Material Symbols Outlined' !important;
    font-weight: normal;
    font-style: normal;
    font-size: 24px;
    line-height: 1;
    letter-spacing: normal;
    text-transform: none;
    display: inline-block;
    white-space: nowrap;
    word-wrap: normal;
    direction: ltr;
    -webkit-font-feature-settings: 'liga';
    -webkit-font-smoothing: antialiased;
    vertical-align: middle;
  }

  /* ========================================================= */
  /* 2. TYPOGRAPHY ENFORCEMENT AGAINST ASTRA SERIF OVERRIDES   */
  /* ========================================================= */
  .email-parser-page,
  .email-parser-page h1,
  .email-parser-page h2,
  .email-parser-page h3,
  .email-parser-page h4,
  .email-parser-page h5,
  .email-parser-page h6,
  .email-parser-page p,
  .email-parser-page a,
  .email-parser-page button,
  .email-parser-page input {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
  }

  .email-parser-page .font-mono,
  .email-parser-page code,
  .email-parser-page pre {
    font-family: 'JetBrains Mono', monospace !important;
  }

  /* Cross-browser reliable gradient text */
  .email-parser-page .gradient-text-accent {
    background: linear-gradient(to right, #abc7ff, #d9e2ff, #edf0ff);
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
  }

  /* ========================================================= */
  /* 3. ASTRA FIXED HEADER & WP ADMIN-BAR CLEARANCE           */
  /* ========================================================= */
  .email-parser-page .hero-clearance {
    padding-top: 140px !important;
  }

  @media (max-width: 768px) {
    .email-parser-page .hero-clearance {
      padding-top: 110px !important;
    }
  }

  body.admin-bar .email-parser-page .hero-clearance {
    padding-top: 172px !important;
  }

  @media (max-width: 768px) {
    body.admin-bar .email-parser-page .hero-clearance {
      padding-top: 146px !important;
    }
  }

  /* Scoped layout resets */
  .email-parser-page {
    color: #0b1b38;
    background-color: #faf9ff;
    width: 100%;
    overflow-x: hidden;
  }

  .email-parser-page main {
    margin-top: 0 !important;
    padding-top: 0 !important;
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .email-parser-page ::-webkit-scrollbar {
    display: none;
  }
</style>

<div class="email-parser-page bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen flex flex-col justify-between selection:bg-primary-container selection:text-on-primary">
<main class="w-full bg-surface flex-grow">
<div class="flex flex-col w-full">
<!-- 1. HERO SECTION (Contained Navy Canvas #071C50) -->
<section class="relative w-full bg-on-primary-fixed overflow-hidden hero-clearance pb-24 md:pb-32 text-inverse-on-surface">
<!-- Ambient Background Glass Radiance -->
<div class="absolute -top-32 -left-32 w-96 h-96 bg-primary-container/20 rounded-full blur-[128px] pointer-events-none"></div>
<div class="absolute top-1/2 right-[-10%] w-[540px] h-[540px] bg-secondary-container/15 rounded-full blur-[140px] pointer-events-none"></div>
<div class="max-w-[1240px] mx-auto px-margin relative z-10">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
<!-- Left: Copy & CTA -->
<div class="lg:col-span-6 flex flex-col items-start space-y-space-lg">
<div class="inline-flex items-center gap-space-sm px-space-md py-1.5 rounded-full bg-primary-container/30 border border-tertiary-fixed-dim/30 backdrop-blur-md shadow-sm">
<span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim animate-pulse"></span>
<span class="font-label-sm text-label-sm uppercase tracking-wider text-tertiary-fixed-dim">Email Data Extraction</span>
</div>
<h1 class="font-display-xl text-display-xl text-inverse-on-surface font-bold tracking-tight">
            Transform Incoming Emails into <span class="bg-gradient-to-r from-tertiary-fixed-dim via-surface-variant to-inverse-on-surface bg-clip-text text-transparent gradient-text-accent">Business-Ready Data</span>
</h1>
<p class="font-body-lg text-body-lg text-inverse-on-surface/80 max-w-xl leading-relaxed">
            Extract important information automatically from incoming emails, convert unstructured email content into structured data, and transfer it directly to the CRM, webhook, or other connected business systems.
          </p>
<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md pt-space-xs w-full sm:w-auto">
<a class="inline-flex items-center justify-center h-12 px-space-xl rounded-xl bg-primary-container text-on-primary font-label-md text-label-md shadow-[0_12px_28px_-6px_rgba(0,72,194,0.65)] hover:bg-primary transition-all duration-300 hover:-translate-y-0.5 group" href="<?php echo esc_url(home_url('/pricing/')); ?>"><span class="">Get Started</span>
<span class="material-symbols-outlined ml-2 text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
</a>
<div class="flex items-center gap-3 px-space-md py-2 text-inverse-on-surface/70 font-body-sm text-body-sm">
<span class="material-symbols-outlined text-tertiary-fixed-dim text-[18px]">verified</span>
<span class="">Zero-code setup • 99.8% precision parsing</span>
</div>
</div>
</div>
<!-- Right: Glass Extraction Visual Sculpture -->
<div class="lg:col-span-6 relative">
<!-- Glass Core Showcase Frame -->
<div class="relative rounded-3xl p-6 sm:p-8 bg-inverse-surface/85 backdrop-blur-2xl shadow-[0_30px_70px_-15px_rgba(2,10,31,0.7)] border border-tertiary-fixed-dim/20 overflow-hidden">
<!-- Decorative Subtle Grid & Specular Edge -->
<div class="absolute inset-0 bg-[radial-gradient(#abc7ff_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>
<div class="absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-tertiary-fixed-dim/40 to-transparent"></div>
<!-- Prism Architecture Diagram -->
<div class="relative z-10 flex flex-col gap-6">
<!-- Source Node: Raw MIME Mail -->
<div class="flex items-center justify-between p-4 rounded-2xl bg-surface-container-lowest/10 border border-tertiary-fixed-dim/20 backdrop-blur-xl shadow-md">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-primary-container/60 flex items-center justify-center text-on-primary">
<span class="material-symbols-outlined text-[20px]">mark_email_unread</span>
</div>
<div>
<div class="font-label-sm text-label-sm text-tertiary-fixed-dim uppercase">Inbound Trigger</div>
<div class="font-title-md text-title-md text-inverse-on-surface font-semibold">New Order Confirmation / Customer Enquiry</div>
</div>
</div>
<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-tertiary-fixed-dim/20 text-tertiary-fixed-dim border border-tertiary-fixed-dim/30">MIME raw</span>
</div>
<!-- Refraction Core (Prism Beam) -->
<div class="relative py-2 flex items-center justify-center">
<div class="h-10 w-[2px] bg-gradient-to-b from-primary-container via-tertiary-fixed-dim to-secondary-container"></div>
<div class="absolute px-3 py-1 rounded-full bg-primary-container text-on-primary font-label-sm text-label-sm shadow-[0_0_15px_rgba(171,199,255,0.6)] flex items-center gap-1.5">
<span class="material-symbols-outlined text-[14px]">auto_fix_high</span>
<span class="">Neural Tokenizer Core</span>
</div>
</div>
<!-- Structured Output Field Badges -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
<div class="p-3 rounded-xl bg-surface-container-lowest/5 border border-tertiary-fixed-dim/20 backdrop-blur-md">
<div class="font-label-sm text-label-sm text-tertiary-fixed-dim/70">Customer</div>
<div class="font-body-md text-body-md text-inverse-on-surface font-medium truncate">Sarah Jenkins</div>
</div>
<div class="p-3 rounded-xl bg-surface-container-lowest/5 border border-tertiary-fixed-dim/20 backdrop-blur-md">
<div class="font-label-sm text-label-sm text-tertiary-fixed-dim/70">Amount</div>
<div class="font-body-md text-body-md text-inverse-on-surface font-semibold text-tertiary-fixed-dim">$4,850.00</div>
</div>
<div class="p-3 rounded-xl bg-surface-container-lowest/5 border border-tertiary-fixed-dim/20 backdrop-blur-md">
<div class="font-label-sm text-label-sm text-tertiary-fixed-dim/70">Status</div>
<div class="flex items-center gap-1.5 font-body-md text-body-md text-emerald-300 font-medium">
<span class="w-2 h-2 rounded-full bg-emerald-400"></span> Verified
                  </div>
</div>
</div>
<!-- Synchronized Destination Pipeline -->
<div class="grid grid-cols-2 gap-3 pt-2">
<div class="flex items-center gap-2.5 p-3 rounded-xl bg-primary-container/20 border border-primary-container/40">
<span class="material-symbols-outlined text-tertiary-fixed-dim text-[20px]">hub</span>
<div>
<div class="font-label-sm text-label-sm text-tertiary-fixed-dim">Target Record</div>
<div class="font-body-sm text-body-sm text-inverse-on-surface font-medium">HubSpot CRM</div>
</div>
</div>
<div class="flex items-center gap-2.5 p-3 rounded-xl bg-primary-container/20 border border-primary-container/40">
<span class="material-symbols-outlined text-tertiary-fixed-dim text-[20px]">webhook</span>
<div>
<div class="font-label-sm text-label-sm text-tertiary-fixed-dim">Target Payload</div>
<div class="font-body-sm text-body-sm text-inverse-on-surface font-medium">Custom Webhook</div>
</div>
</div>
</div>
<!-- Glass Card: Example Extraction Inspector -->
<div class="mt-2 rounded-2xl p-4 bg-surface-container-lowest/10 border border-tertiary-fixed-dim/20 shadow-inner">
<div class="flex items-center justify-between pb-3 border-b border-tertiary-fixed-dim/15">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-tertiary-fixed-dim text-[16px]">data_object</span>
<span class="font-label-sm text-label-sm uppercase tracking-wider text-tertiary-fixed-dim font-bold">Example extraction</span>
</div>
<span class="font-label-sm text-label-sm text-inverse-on-surface/60 font-mono">latency: 184ms</span>
</div>
<div class="grid grid-cols-2 gap-y-2.5 gap-x-4 pt-3 font-body-sm text-body-sm">
<div>
<span class="text-inverse-on-surface/60 text-xs block">Customer Name:</span>
<span class="text-inverse-on-surface font-medium">Sarah Jenkins</span>
</div>
<div>
<span class="text-inverse-on-surface/60 text-xs block">Email Address:</span>
<span class="text-inverse-on-surface font-medium truncate">s.jenkins@enterprise.io</span>
</div>
<div>
<span class="text-inverse-on-surface/60 text-xs block">Enquiry Type:</span>
<span class="text-inverse-on-surface font-medium">Enterprise Annual License</span>
</div>
<div>
<span class="text-inverse-on-surface/60 text-xs block">Order Reference:</span>
<span class="text-inverse-on-surface font-mono text-tertiary-fixed-dim">ORD-2024-8891</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- 2. MANUAL EXTRACTION CHALLENGES (Light Pure Canvas) -->
<section class="w-full bg-surface py-24 md:py-28 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Inbox Inefficiencies</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Manual Extraction Challenges</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
          Unstructured emails trap crucial sales, operational, and customer transactions inside cluttered inboxes. Without automated intelligence, companies bleed productive operational hours into manual copy-pasting, exposing workflows to administrative drag and missed opportunities.
        </p>
</div>
<!-- 7 Challenges Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<!-- 1 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30 flex flex-col justify-between group">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-[24px]">hourglass_empty</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">Time-Consuming Data Collection</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Teams waste dozens of hours each week combing through inbox threads to copy key transaction values, leaving revenue-critical tasks unattended.
            </p>
</div>
<div class="mt-6 pt-4 border-t border-surface-container-high flex items-center text-primary font-label-md text-label-md">
<span class="">Severe Operations Bottleneck</span>
</div>
</div>
<!-- 2 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30 flex flex-col justify-between group">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-[24px]">find_in_page</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">Difficulty Extracting Key Details</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Freeform customer inquiries and erratic order formats bury phone numbers, line items, and addresses beneath deep message chains.
            </p>
</div>
<div class="mt-6 pt-4 border-t border-surface-container-high flex items-center text-primary font-label-md text-label-md">
<span class="">Inconsistent Field Formats</span>
</div>
</div>
<!-- 3 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30 flex flex-col justify-between group">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-[24px]">content_paste_go</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">Repetitive Copying and Pasting</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Human re-entry causes costly typographical mistakes, faulty order numbers, and misspelled recipient emails that derail downstream workflows.
            </p>
</div>
<div class="mt-6 pt-4 border-t border-surface-container-high flex items-center text-primary font-label-md text-label-md">
<span class="">High Risk of Human Error</span>
</div>
</div>
<!-- 4 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30 flex flex-col justify-between group">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-[24px]">timer_off</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">Delayed Lead Processing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Inbound prospects sit idle for hours or days before being assigned to sales reps, causing lead decay and forfeited enterprise deals.
            </p>
</div>
<div class="mt-6 pt-4 border-t border-surface-container-high flex items-center text-primary font-label-md text-label-md">
<span class="">Compromised Conversion Rate</span>
</div>
</div>
<!-- 5 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30 flex flex-col justify-between group">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-[24px]">rule</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">Inconsistent Business Records</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Different team members record email data with conflicting schemas, leading to polluted CRM schemas and compromised reporting accuracy.
            </p>
</div>
<div class="mt-6 pt-4 border-t border-surface-container-high flex items-center text-primary font-label-md text-label-md">
<span class="">Fragmented Single-Source Truth</span>
</div>
</div>
<!-- 6 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all duration-300 border border-outline-variant/30 flex flex-col justify-between group">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-[24px]">sync_problem</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">Disconnected Business Systems</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Email data stays quarantined from ERPs, analytics platforms, and payment systems, crippling cross-departmental coordination.
            </p>
</div>
<div class="mt-6 pt-4 border-t border-surface-container-high flex items-center text-primary font-label-md text-label-md">
<span class="">Siloed Operational Stack</span>
</div>
</div>
<!-- 7 (Span Full on bottom) -->
<div class="p-8 rounded-3xl bg-gradient-to-r from-surface-container-lowest to-surface-container-low shadow-sm md:col-span-2 lg:col-span-3 border border-outline-variant/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
<div class="flex items-start gap-4">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">work_history</span>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-1">Repetitive Administrative Work</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl leading-relaxed">
                Talented engineers and operational leaders get pulled into low-leverage clerical tasks simply to keep systems synchronized manually.
              </p>
</div>
</div>
<a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-surface-container text-primary font-label-md text-label-md hover:bg-primary-container hover:text-on-primary transition-colors" href="#">
<span class="">Eliminate manual entry</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</a>
</div>
</div>
</div>
</section>
<!-- 3. AUTOMATIC EMAIL DATA EXTRACTION (Off-White #F6FAFD Glass Contrast) -->
<section class="w-full bg-surface-container-low py-24 md:py-28 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Automated Intelligence</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Extract Email Data Automatically with ExtractMails</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
          ExtractMails functions as an autonomous data orchestration bridge between your inboxes and business stack. By deciphering message syntax in real time, our engine converts chaotic incoming communications into verified, clean database entries instantly.
        </p>
</div>
<!-- Split Composition: Photo Card & 6 Action Rows -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
<!-- Left Column: Business Photography -->
<div class="lg:col-span-5 relative">
<div class="rounded-3xl overflow-hidden shadow-2xl border border-surface-container-highest/60 bg-surface-container-lowest p-2">
<img class="w-full h-[520px] object-cover rounded-2xl" data-alt="High-resolution photography of an operations professional reviewing automated data flow diagrams on an ultrawide desktop monitor within an airy modern office with blue accent lighting, symbolizing effortless data pipelines and structured email analysis" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDvhtZG82dWrox74Pa_3r8gtR6q0QO7707dCDhwTi2GQHPIr7M_XdloXKjadMsHF96rTElQbjoSsQiD-pQnetLg16Z1bpCdjH5if8brn8B0e2BRoahkWv2PqkA98rE4opJceVNQTV54xPtNdUp2tnxdfgSL_iueb2XqYCxUD2C0JNGOWNEFm2q4LZ01E97fU-M1KHY7n8EOxEtTMDfFF8FquviWE04Xi4_CQvm3KQjIt6KXa6t15-i0">
</div>
<!-- Floating Status Badge -->
<div class="absolute -bottom-6 -right-4 bg-surface-container-lowest/95 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-outline-variant/30 flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">bolt</span>
</div>
<div>
<div class="font-label-sm text-label-sm text-primary font-bold">Latency Benchmark</div>
<div class="font-title-md text-title-md text-on-surface font-bold">Sub-200ms Execution</div>
</div>
</div>
</div>
<!-- Right Column: 6 Action Rows -->
<div class="lg:col-span-7 flex flex-col gap-4">
<!-- Action Row 1 -->
<div class="p-5 rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center flex-shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[20px]">inbox</span>
</div>
<div class="flex-grow">
<div class="flex items-center justify-between">
<h4 class="font-title-md text-title-md text-on-surface font-semibold">1. Reviewing Incoming Emails</h4>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-primary">Autonomous</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Monitors designated IMAP, Google Workspace, or Outlook mailboxes, instantaneously detecting new inbound alerts, attachments, and confirmations.
              </p>
</div>
</div>
<!-- Action Row 2 -->
<div class="p-5 rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center flex-shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[20px]">filter_alt</span>
</div>
<div class="flex-grow">
<div class="flex items-center justify-between">
<h4 class="font-title-md text-title-md text-on-surface font-semibold">2. Finding Relevant Details</h4>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-primary">Regex &amp; LLM</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Isolates phone numbers, SKU identifiers, monetary sums, customer requirements, and postal codes with precision heuristic pattern matching.
              </p>
</div>
</div>
<!-- Action Row 3 -->
<div class="p-5 rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center flex-shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[20px]">transform</span>
</div>
<div class="flex-grow">
<div class="flex items-center justify-between">
<h4 class="font-title-md text-title-md text-on-surface font-semibold">3. Transferring Data Between Applications</h4>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-primary">Real-time</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Transforms heterogeneous message strings into canonical JSON or tabular structures formatted to comply with target database schemas.
              </p>
</div>
</div>
<!-- Action Row 4 -->
<div class="p-5 rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center flex-shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[20px]">contact_page</span>
</div>
<div class="flex-grow">
<div class="flex items-center justify-between">
<h4 class="font-title-md text-title-md text-on-surface font-semibold">4. Updating CRM Records</h4>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-primary">Bi-directional</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Appends extracted lead qualifications and customer metadata directly into Salesforce, HubSpot, or Pipedrive without manual friction.
              </p>
</div>
</div>
<!-- Action Row 5 -->
<div class="p-5 rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center flex-shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[20px]">cable</span>
</div>
<div class="flex-grow">
<div class="flex items-center justify-between">
<h4 class="font-title-md text-title-md text-on-surface font-semibold">5. Sending Extracted Data to Webhooks</h4>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-primary">HTTP POST</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Dispatches custom HTTP webhooks with cryptographic signature verification to your proprietary backend microservices instantly.
              </p>
</div>
</div>
<!-- Action Row 6 -->
<div class="p-5 rounded-2xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center flex-shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[20px]">published_with_changes</span>
</div>
<div class="flex-grow">
<div class="flex items-center justify-between">
<h4 class="font-title-md text-title-md text-on-surface font-semibold">6. Automating Repetitive Workflows</h4>
<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-fixed text-primary">Continuous</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Triggers conditional notifications, invoicing pipelines, and order dispatch sequences without any human intervention required.
              </p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- 4. FIVE-STEP WORKFLOW (Pure Canvas with Dark Interactive Terminal) -->
<section class="w-full bg-surface py-24 md:py-28 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Precision Architecture</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">How It Works: Five-Step Email Data Extraction</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
          Follow how raw, unstructured incoming emails are systematically parsed, standardized, and distributed across your cloud ecosystem in milliseconds.
        </p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">
<!-- Left: 5 Step Sequence -->
<div class="lg:col-span-6 flex flex-col space-y-8">
<!-- Step 1 -->
<div class="flex gap-5">
<div class="flex flex-col items-center">
<div class="w-10 h-10 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center text-sm shadow-md">1</div>
<div class="w-0.5 h-full bg-surface-container-highest mt-2"></div>
</div>
<div class="pb-6">
<h3 class="font-title-lg text-title-lg text-on-surface font-bold">Receive Incoming Emails</h3>
<p class="font-body-md text-body-md text-on-surface-variant mt-2 leading-relaxed">
                ExtractMails establishes listening rules via direct forwarding addresses or native mailbox API syncing, capturing incoming messages in real time.
              </p>
</div>
</div>
<!-- Step 2 -->
<div class="flex gap-5">
<div class="flex flex-col items-center">
<div class="w-10 h-10 rounded-full bg-primary text-on-primary font-bold flex items-center justify-center text-sm shadow-md">2</div>
<div class="w-0.5 h-full bg-surface-container-highest mt-2"></div>
</div>
<div class="pb-6">
<h3 class="font-title-lg text-title-lg text-on-surface font-bold">Parse Email Content</h3>
<p class="font-body-md text-body-md text-on-surface-variant mt-2 leading-relaxed">
                The extraction engine decodes MIME bodies, HTML structures, and attached files (PDFs, spreadsheets, and invoices) into tokenized text blocks.
              </p>
</div>
</div>
<!-- Step 3 -->
<div class="flex gap-5">
<div class="flex flex-col items-center">
<div class="w-10 h-10 rounded-full bg-secondary text-on-secondary font-bold flex items-center justify-center text-sm shadow-md">3</div>
<div class="w-0.5 h-full bg-surface-container-highest mt-2"></div>
</div>
<div class="pb-6">
<h3 class="font-title-lg text-title-lg text-on-surface font-bold">Extract and Structure Data</h3>
<p class="font-body-md text-body-md text-on-surface-variant mt-2 leading-relaxed">
                Rule-based parsing templates and contextual pattern matching extract custom key-value pairs, normalizing date stamps, currencies, and names.
              </p>
</div>
</div>
<!-- Step 4 -->
<div class="flex gap-5">
<div class="flex flex-col items-center">
<div class="w-10 h-10 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center text-sm shadow-md">4</div>
<div class="w-0.5 h-full bg-surface-container-highest mt-2"></div>
</div>
<div class="pb-6">
<h3 class="font-title-lg text-title-lg text-on-surface font-bold">Send Data to Connected Systems</h3>
<p class="font-body-md text-body-md text-on-surface-variant mt-2 leading-relaxed">
                Synchronously transmit structured payloads via native CRM connectors, RESTful endpoints, or Zapier/Make webhooks without manual human intervention.
              </p>
</div>
</div>
<!-- Step 5 -->
<div class="flex gap-5">
<div class="flex flex-col items-center">
<div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container font-bold flex items-center justify-center text-sm shadow-md">5</div>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-bold">Automate Business Actions</h3>
<p class="font-body-md text-body-md text-on-surface-variant mt-2 leading-relaxed">
                Connected platforms trigger instant email confirmations, automated sales representative routing, invoice generation, or analytics dashboard refreshes.
              </p>
</div>
</div>
</div>
<!-- Right: Contained Navy Demonstration Panel -->
<div class="lg:col-span-6 sticky top-28">
<div class="rounded-3xl p-6 sm:p-7 bg-on-primary-fixed text-inverse-on-surface border border-tertiary-fixed-dim/20 shadow-2xl backdrop-blur-2xl">
<!-- Console Header -->
<div class="flex items-center justify-between pb-4 border-b border-surface-container-highest/15">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
<span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
<span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
<span class="ml-2 font-label-sm text-label-sm text-tertiary-fixed-dim uppercase tracking-wider font-semibold">Example workflow</span>
</div>
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
<span class="font-label-sm text-label-sm text-emerald-300">Live Engine Ready</span>
</div>
</div>
<!-- Visual Flow Stages Inside Terminal -->
<div class="py-5 space-y-4">
<!-- Raw MIME Stage -->
<div class="p-3.5 rounded-xl bg-surface-container-lowest/5 border border-tertiary-fixed-dim/15">
<div class="flex items-center justify-between font-label-sm text-label-sm text-tertiary-fixed-dim mb-1">
<span class="">STEP 1 • INBOUND RAW STRING</span>
<span class="">MIME / UTF-8</span>
</div>
<p class="font-mono text-xs text-inverse-on-surface/80 bg-surface-container-lowest/5 p-2 rounded">
                  "Hi team, please process an order for Acme Corp. Contact: Alex Stone (alex@acme.org). Item: 50x Pro Tier ($12,500 total). PO: #AC-9921."
                </p>
</div>
<!-- Pipeline Transition Icon -->
<div class="flex items-center justify-center py-0.5">
<div class="w-6 h-6 rounded-full bg-primary-container/40 flex items-center justify-center text-tertiary-fixed-dim">
<span class="material-symbols-outlined text-[16px]">arrow_downward</span>
</div>
</div>
<!-- Parsed JSON Key-Values -->
<div class="p-3.5 rounded-xl bg-surface-container-lowest/5 border border-tertiary-fixed-dim/15 font-mono text-xs text-inverse-on-surface/90">
<div class="flex items-center justify-between font-label-sm text-label-sm text-tertiary-fixed-dim font-sans mb-1">
<span class="">STEP 3 • PARSED JSON PAYLOAD</span>
<span class="text-emerald-300 font-mono">200 OK</span>
</div>
<div class="p-2.5 rounded bg-on-secondary-fixed/50 space-y-1">
<div class="">{</div>
<div class="pl-4"><span class="text-tertiary-fixed-dim">"organization"</span>: <span class="text-amber-200">"Acme Corp"</span>,</div>
<div class="pl-4"><span class="text-tertiary-fixed-dim">"contact_name"</span>: <span class="text-amber-200">"Alex Stone"</span>,</div>
<div class="pl-4"><span class="text-tertiary-fixed-dim">"contact_email"</span>: <span class="text-amber-200">"alex@acme.org"</span>,</div>
<div class="pl-4"><span class="text-tertiary-fixed-dim">"total_value"</span>: <span class="text-emerald-300">12500.00</span>,</div>
<div class="pl-4"><span class="text-tertiary-fixed-dim">"po_reference"</span>: <span class="text-amber-200">"AC-9921"</span></div>
<div class="">}</div>
</div>
</div>
<!-- Pipeline Transition Icon -->
<div class="flex items-center justify-center py-0.5">
<div class="w-6 h-6 rounded-full bg-primary-container/40 flex items-center justify-center text-tertiary-fixed-dim">
<span class="material-symbols-outlined text-[16px]">arrow_downward</span>
</div>
</div>
<!-- Multi-destination Synced -->
<div class="p-3.5 rounded-xl bg-primary-container/20 border border-primary-container/40 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-tertiary-fixed-dim">check_circle</span>
<div>
<div class="font-title-md text-title-md text-inverse-on-surface font-semibold text-sm">Dispatched Targets</div>
<div class="font-body-sm text-body-sm text-inverse-on-surface/70 text-xs">HubSpot CRM • PostgreSQL Database • Slack Alert</div>
</div>
</div>
<span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Synced</span>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- 5. FEATURES FOR EMAIL DATA PROCESSING (Pale Icy-Blue Canvas) -->
<section class="w-full bg-surface-container py-24 md:py-28 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Enterprise Engine</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Features Built for High-Volume Email Processing</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
          Scale email-driven data intake with purpose-built tools engineered for exceptional throughput, zero data truncation, and seamless schema adaptability.
        </p>
</div>
<!-- Feature Grid: 1 Hero Feature + 6 Regular Features -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<!-- Hero Feature Card (Span 2) -->
<div class="md:col-span-2 lg:col-span-2 p-8 sm:p-10 rounded-3xl bg-surface-container-lowest shadow-md border border-outline-variant/30 flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-6">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[26px]">smart_toy</span>
</div>
<span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-surface-container text-primary">Core Capability</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface font-bold mb-3">1. Automated Email Parsing</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl leading-relaxed">
              ExtractMails deploys intelligent multi-layer parsers that intuitively recognize repetitive document patterns, parsing plain text bodies, table structures, and complex attachments without manual human supervision.
            </p>
</div>
<div class="mt-8 p-4 rounded-2xl bg-surface-container-low border border-outline-variant/20 flex flex-wrap items-center gap-4 text-xs font-mono text-on-surface-variant">
<span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-primary"></span> HTML Elements</span>
<span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-primary"></span> PDF Invoices</span>
<span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-primary"></span> CSV Tables</span>
<span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-primary"></span> Header Metadata</span>
</div>
</div>
<!-- Feature 2 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-md border border-outline-variant/30 flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">schema</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-bold mb-3">2. Structured Data Extraction</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Convert erratic email sentences into uniform, validated JSON, CSV, or XML records ready for direct data warehouse loading.
            </p>
</div>
<div class="mt-6 flex items-center gap-2 text-primary font-label-md text-label-md">
<span class="">JSON, CSV &amp; XML Output</span>
</div>
</div>
<!-- Feature 3 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-md border border-outline-variant/30 flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">webhook</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-bold mb-3">3. Webhook Integration</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Dispatch parsed data packets directly to any URL endpoint in sub-second timeframes with automatic exponential backoff retries.
            </p>
</div>
<div class="mt-6 flex items-center gap-2 text-primary font-label-md text-label-md">
<span class="">Instant HTTP POST</span>
</div>
</div>
<!-- Feature 4 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-md border border-outline-variant/30 flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">sync_alt</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-bold mb-3">4. CRM and Business System Integration</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Pre-built integrations allow seamless sync with HubSpot, Salesforce, Zoho, Google Sheets, Airtable, and custom REST APIs.
            </p>
</div>
<div class="mt-6 flex items-center gap-2 text-primary font-label-md text-label-md">
<span class="">Direct Ecosystem Connectors</span>
</div>
</div>
<!-- Feature 5 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-md border border-outline-variant/30 flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">alt_route</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-bold mb-3">5. Rule-Based Workflow Automation</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Design flexible logic trees based on subject lines, sender domains, deal sizes, or parsed keywords to direct outcomes dynamically.
            </p>
</div>
<div class="mt-6 flex items-center gap-2 text-primary font-label-md text-label-md">
<span class="">Conditional Logic Engine</span>
</div>
</div>
<!-- Feature 6 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-md border border-outline-variant/30 flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">person_add</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-bold mb-3">6. Automated Lead Data Capture</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Instantly extract form fills from lead portals, trade show lists, or website inquiry forms and assign them to your sales pipeline.
            </p>
</div>
<div class="mt-6 flex items-center gap-2 text-primary font-label-md text-label-md">
<span class="">Zero Lead Leakage</span>
</div>
</div>
<!-- Feature 7 -->
<div class="md:col-span-2 lg:col-span-2 p-8 rounded-3xl bg-surface-container-lowest shadow-md border border-outline-variant/30 flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-2xl bg-surface-container-high text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">speed</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-bold mb-3">7. Streamlined Email Processing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Eliminate backlogs forever. Our parallelized architecture handles burst volumes of thousands of incoming emails an hour with strict throughput reliability.
            </p>
</div>
<div class="mt-6 flex items-center gap-2 text-primary font-label-md text-label-md">
<span class="">High-Throughput Parallel Processing</span>
</div>
</div>
</div>
</div>
</section>
<!-- 6. BUSINESS USE CASES (Light Pure Canvas) -->
<section class="w-full bg-surface py-24 md:py-28 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Use Case</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Simplify Everyday Business Processes with Email Data Extraction</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
          From e-commerce order routing to enterprise client onboarding, explore how ExtractMails automates repetitive daily operations across mission-critical workflows.
        </p>
</div>
<!-- 6 Use Cases Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<!-- 1 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all border border-outline-variant/30">
<div class="w-12 h-12 rounded-2xl bg-primary-container/10 text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">contacts</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">1. Lead Data Extraction</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            Automatically capture prospect contact info, company name, requirements, and UTM parameters from inbound lead notifications and route them to sales reps.
          </p>
</div>
<!-- 2 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all border border-outline-variant/30">
<div class="w-12 h-12 rounded-2xl bg-primary-container/10 text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">support_agent</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">2. Customer Enquiry Processing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            Parse support requests and inquiries to extract ticket category, urgency indicators, and customer IDs to dispatch tickets directly to Zendesk or Jira.
          </p>
</div>
<!-- 3 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all border border-outline-variant/30">
<div class="w-12 h-12 rounded-2xl bg-primary-container/10 text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">calendar_month</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">3. Booking Data Extraction</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            Consolidate reservation confirmations, flight bookings, and calendar invitations by extracting dates, attendee counts, and venue information seamlessly.
          </p>
</div>
<!-- 4 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all border border-outline-variant/30">
<div class="w-12 h-12 rounded-2xl bg-primary-container/10 text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">shopping_bag</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">4. Order Information Extraction</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            Extract PO numbers, line items, quantities, shipping addresses, and billing amounts directly from vendor notifications into your ERP or fulfillment stack.
          </p>
</div>
<!-- 5 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all border border-outline-variant/30">
<div class="w-12 h-12 rounded-2xl bg-primary-container/10 text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">account_tree</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">5. Email-Based Workflow Automation</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            Connect legacy software applications that only communicate via automated notification emails to modern cloud services via automated parsing webhooks.
          </p>
</div>
<!-- 6 -->
<div class="p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-xl transition-all border border-outline-variant/30">
<div class="w-12 h-12 rounded-2xl bg-primary-container/10 text-primary flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-[24px]">precision_manufacturing</span>
</div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-3">6. Operational Email Data Processing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            Parse automated server telemetry, system health alerts, and partner status reports into clean datasets for real-time operations dashboards.
          </p>
</div>
</div>
</div>
</section>
<!-- 7. TEAMS AND AUDIENCES (Off-White Split Canvas) -->
<section class="w-full bg-surface-container-low py-24 md:py-28 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Who Should Use an Email Parser?</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Designed for Teams That Process Business Emails</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
          Regardless of your industry or operational maturity, ExtractMails provides the precision intelligence required to eliminate inbox overhead and scale throughput effortlessly.
        </p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
<!-- Left Column: Team Photograph -->
<div class="lg:col-span-5 relative">
<div class="rounded-3xl overflow-hidden shadow-2xl border border-surface-container-highest/60 bg-surface-container-lowest p-2">
<img class="w-full h-[520px] object-cover rounded-2xl" data-alt="A modern, diverse technology and operations team collaborating in an open-plan office around a polished wooden table, analyzing data automation workflows on a laptop and sharing structured email reports with calm focus" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2BdqtFRcWA4my9XpIFowHJYqVzaKQlo4NOAXejKtj3dk2Uy-QU8uy_E5pRuekp9fdQl1mKCWzKKcgyCzPrnjzgUXNmrHgiJrnO46twpN1CN3oLJCckhqlWZjuDhtIrSq5qzXUwrpK1BnxOyitYj4-2SFfT3kF30HU8leSFmVI1hrOaaCG2DIRcXM9qFjSRv0jhwF1lPV535JaaIH4uAW-GlV1IdZH3-jg694KHoDskF0XlQO8BIP6">
</div>
<div class="absolute -bottom-6 -left-4 bg-surface-container-lowest/95 backdrop-blur-xl p-4 rounded-2xl shadow-xl border border-outline-variant/30 flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-primary text-on-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">groups</span>
</div>
<div>
<div class="font-label-sm text-label-sm text-primary font-bold">Collaborative Scaling</div>
<div class="font-title-md text-title-md text-on-surface font-bold">Multi-Seat Role Access</div>
</div>
</div>
</div>
<!-- Right Column: 6 Audience Panels -->
<div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- 1 -->
<div class="p-6 rounded-2xl bg-surface-container-lowest shadow-sm border border-outline-variant/30">
<h4 class="font-title-md text-title-md text-on-surface font-semibold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">trending_up</span>
<span class="">Sales and Lead Gen</span>
</h4>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Never lose a warm prospect. Parse inbound portal leads in seconds and trigger immediate speed-to-lead outbound follow-ups.
            </p>
</div>
<!-- 2 -->
<div class="p-6 rounded-2xl bg-surface-container-lowest shadow-sm border border-outline-variant/30">
<h4 class="font-title-md text-title-md text-on-surface font-semibold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">analytics</span>
<span class="">Sales Operations</span>
</h4>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Enforce strict CRM schema hygiene and eliminate manual data entry across multi-tiered account executive pipelines.
            </p>
</div>
<!-- 3 -->
<div class="p-6 rounded-2xl bg-surface-container-lowest shadow-sm border border-outline-variant/30">
<h4 class="font-title-md text-title-md text-on-surface font-semibold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">headset_mic</span>
<span class="">Customer Support</span>
</h4>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Route complex customer cases automatically to appropriate tier-2 specialists with all account history pre-populated.
            </p>
</div>
<!-- 4 -->
<div class="p-6 rounded-2xl bg-surface-container-lowest shadow-sm border border-outline-variant/30">
<h4 class="font-title-md text-title-md text-on-surface font-semibold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">tune</span>
<span class="">Operations Teams</span>
</h4>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Automate invoice processing, shipping tracking aggregation, and supply chain confirmations into central databases.
            </p>
</div>
<!-- 5 -->
<div class="p-6 rounded-2xl bg-surface-container-lowest shadow-sm border border-outline-variant/30">
<h4 class="font-title-md text-title-md text-on-surface font-semibold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">code</span>
<span class="">Developers &amp; Builders</span>
</h4>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Skip maintaining fragile in-house regex scrapers. Consume clean webhook payloads with validated JSON payloads.
            </p>
</div>
<!-- 6 -->
<div class="p-6 rounded-2xl bg-surface-container-lowest shadow-sm border border-outline-variant/30">
<h4 class="font-title-md text-title-md text-on-surface font-semibold mb-2 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">rocket_launch</span>
<span class="">Agencies &amp; SMBs</span>
</h4>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Deliver enterprise-grade automation to multiple client accounts without needing dedicated engineering overhead.
            </p>
</div>
</div>
</div>
</div>
</section>
<!-- 8. WHY CHOOSE EXTRACTMAILS (Main White Canvas) -->
<section class="w-full bg-surface py-24 md:py-28 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Why Choose ExtractMails</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">A Smarter Way to Extract, Structure, and Transfer Email Data</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
          Engineered for operational excellence, ExtractMails provides the precision parsing reliability required by modern fast-growing businesses.
        </p>
</div>
<!-- 6 Reason Rows -->
<div class="space-y-4">
<!-- 1 -->
<div class="p-6 sm:p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
<div class="flex items-start gap-5">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">done_all</span>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-1">1. Automate Incoming Email Processing</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">
                Transform inboxes from chaotic communication repositories into automated, high-velocity ingestion engines that never sleep.
              </p>
</div>
</div>
<span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary-fixed text-primary whitespace-nowrap">Full Automation</span>
</div>
<!-- 2 -->
<div class="p-6 sm:p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
<div class="flex items-start gap-5">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">verified</span>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-1">2. Extract Relevant Business Information</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">
                Zero noise. Pinpoint exact dates, invoice line items, pricing tiers, and client identifiers while discarding useless disclaimers and email signatures.
              </p>
</div>
</div>
<span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary-fixed text-primary whitespace-nowrap">High Precision</span>
</div>
<!-- 3 -->
<div class="p-6 sm:p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
<div class="flex items-start gap-5">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">hub</span>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-1">3. Connect Email Data with Business Systems</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">
                Eliminate data silos by bridging isolated customer communications directly into CRMs, relational databases, analytics sheets, and internal tools.
              </p>
</div>
</div>
<span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary-fixed text-primary whitespace-nowrap">Unified Stack</span>
</div>
<!-- 4 -->
<div class="p-6 sm:p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
<div class="flex items-start gap-5">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">alt_route</span>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-1">4. Create Rule-Based Workflows</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">
                Design multi-branch routing logic that categorizes incoming transactions and triggers tailored downstream actions based on custom criteria.
              </p>
</div>
</div>
<span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary-fixed text-primary whitespace-nowrap">Custom Logic</span>
</div>
<!-- 5 -->
<div class="p-6 sm:p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
<div class="flex items-start gap-5">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">format_image_left</span>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-1">5. Reduce Manual Data Handling</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">
                Safeguard accuracy and free human talent from tedious copy-paste marathons, cutting operational processing costs by upwards of 80%.
              </p>
</div>
</div>
<span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary-fixed text-primary whitespace-nowrap">Zero Fatigue</span>
</div>
<!-- 6 -->
<div class="p-6 sm:p-8 rounded-3xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all border border-outline-variant/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
<div class="flex items-start gap-5">
<div class="w-12 h-12 rounded-2xl bg-primary-container text-on-primary flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">speed</span>
</div>
<div>
<h3 class="font-title-lg text-title-lg text-on-surface font-semibold mb-1">6. Support More Efficient Operations</h3>
<p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">
                Accelerate customer turnaround times, fulfill orders with lower latency, and empower leadership with real-time operational transparency.
              </p>
</div>
</div>
<span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary-fixed text-primary whitespace-nowrap">Instant Sync</span>
</div>
</div>
</div>
</section>
<!-- 9. FINAL CTA (Contained Navy Glass Card in Pure Canvas) -->
<section class="w-full bg-surface py-20 relative">
<div class="max-w-[1240px] mx-auto px-margin">
<div class="rounded-3xl p-10 sm:p-16 bg-on-primary-fixed text-inverse-on-surface relative overflow-hidden shadow-2xl border border-tertiary-fixed-dim/20">
<!-- Ambient Radial Glows -->
<div class="absolute -top-24 -right-24 w-80 h-80 bg-primary-container/30 rounded-full blur-[100px] pointer-events-none"></div>
<div class="absolute -bottom-24 -left-24 w-80 h-80 bg-secondary-container/20 rounded-full blur-[100px] pointer-events-none"></div>
<div class="relative z-10 max-w-2xl mx-auto text-center space-y-6">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container-lowest/10 border border-tertiary-fixed-dim/20 text-tertiary-fixed-dim font-label-sm text-label-sm">
<span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim animate-pulse"></span>
<span class="">Immediate Integration</span>
</div>
<h2 class="font-display-xl text-display-xl text-inverse-on-surface font-bold tracking-tight">
            Start Automating Email Data Extraction Today
          </h2>
<p class="font-body-lg text-body-lg text-inverse-on-surface/80 leading-relaxed">
            Stop spending valuable time manually extracting information from emails and transferring it between business applications.
          </p>
<div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
<a class="inline-flex items-center justify-center h-14 px-8 rounded-xl bg-primary-container text-on-primary font-label-md text-label-md font-semibold shadow-[0_12px_28px_-6px_rgba(0,72,194,0.7)] hover:bg-primary transition-all duration-300 hover:-translate-y-0.5 group" href="#">
<span class="">Start Data Extraction Today</span>
<span class="material-symbols-outlined ml-2 text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
</a>
</div>
<div class="pt-2 text-xs text-inverse-on-surface/60 font-mono">
            No credit card required • Free tier includes 250 extractions/month
          </div>
</div>
</div>
</div>
</section>
<!-- 10. FREQUENTLY ASKED QUESTIONS (880px Centered Accordion) -->
<section class="w-full bg-surface-container-low py-24 md:py-28 relative">
<div class="max-w-[880px] mx-auto px-margin">
<!-- Section Header -->
<div class="text-center mb-16 space-y-4">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Frequently Asked Questions</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Got Questions? We Have Answers</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">
          Everything you need to know about setting up, securing, and scaling your email data extraction pipeline.
        </p>
</div>
<!-- Accordion Container -->
<div class="space-y-4" id="faq-accordion">
<!-- FAQ 1 (Expanded by Default) -->
<div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/30 overflow-hidden transition-all duration-200">
<button class="faq-toggle w-full p-6 text-left flex items-center justify-between gap-4 font-title-lg text-title-lg text-on-surface font-semibold" type="button">
<span class="">What types of emails can ExtractMails parse?</span>
<span class="material-symbols-outlined text-primary text-[24px] transform transition-transform duration-200 rotate-180">expand_more</span>
</button>
<div class="faq-content px-6 pb-6 pt-0 text-on-surface-variant font-body-md text-body-md leading-relaxed">
            ExtractMails can parse plain text emails, HTML formatted messages, and email attachments including PDFs, spreadsheets, and CSVs. Custom extraction rules and intelligent field detection extract relevant fields accurately regardless of format variation.
          </div>
</div>
<!-- FAQ 2 -->
<div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/30 overflow-hidden transition-all duration-200">
<button class="faq-toggle w-full p-6 text-left flex items-center justify-between gap-4 font-title-lg text-title-lg text-on-surface font-semibold" type="button">
<span class="">How does data transfer to our CRM or database work?</span>
<span class="material-symbols-outlined text-primary text-[24px] transform transition-transform duration-200">expand_more</span>
</button>
<div class="faq-content hidden px-6 pb-6 pt-0 text-on-surface-variant font-body-md text-body-md leading-relaxed">
            Extracted data is routed in real time via native webhooks, direct API endpoints, and direct integrations with popular CRMs like Salesforce, HubSpot, and Zoho, ensuring instant synchronization without manual re-entry.
          </div>
</div>
<!-- FAQ 3 -->
<div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/30 overflow-hidden transition-all duration-200">
<button class="faq-toggle w-full p-6 text-left flex items-center justify-between gap-4 font-title-lg text-title-lg text-on-surface font-semibold" type="button">
<span class="">Is technical or coding expertise required to build extraction rules?</span>
<span class="material-symbols-outlined text-primary text-[24px] transform transition-transform duration-200">expand_more</span>
</button>
<div class="faq-content hidden px-6 pb-6 pt-0 text-on-surface-variant font-body-md text-body-md leading-relaxed">
            No coding is required. Our intuitive visual rule builder allows any operations or sales professional to define parsing templates, select target fields, and set up automated routing rules within minutes.
          </div>
</div>
<!-- FAQ 4 -->
<div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/30 overflow-hidden transition-all duration-200">
<button class="faq-toggle w-full p-6 text-left flex items-center justify-between gap-4 font-title-lg text-title-lg text-on-surface font-semibold" type="button">
<span class="">How does ExtractMails handle confidential and sensitive email data?</span>
<span class="material-symbols-outlined text-primary text-[24px] transform transition-transform duration-200">expand_more</span>
</button>
<div class="faq-content hidden px-6 pb-6 pt-0 text-on-surface-variant font-body-md text-body-md leading-relaxed">
            We prioritize enterprise-grade data protection with end-to-end encryption in transit (TLS 1.3) and at rest (AES-256), strict access controls, and zero permanent storage of parsed payload contents beyond your specified retention policy.
          </div>
</div>
</div>
</div>
</section>
<!-- Interactive Accordion Logic -->

</div>
</main>
</div>

<!-- Accessible Interactive FAQ Accordion Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggles = document.querySelectorAll('.email-parser-page .faq-toggle');
  toggles.forEach(function(button) {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const isExpanded = button.getAttribute('aria-expanded') === 'true';
      const content = button.nextElementSibling;
      const icon = button.querySelector('.faq-icon');

      if (isExpanded) {
        button.setAttribute('aria-expanded', 'false');
        content.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
      } else {
        button.setAttribute('aria-expanded', 'true');
        content.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
      }
    });
  });
});
</script>

<?php
// Load WordPress Footer or Close HTML5 Document
if (function_exists('get_footer')) {
    get_footer();
} else {
    ?>
    </body>
    </html>
    <?php
}
?>
