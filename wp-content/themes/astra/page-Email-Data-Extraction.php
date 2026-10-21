<?php
/**
 * Template Name: Email Parser Landing Page (V2 High-Contrast Navy)
 * Template Post Type: page
 * 
 * ExtractMails - Automate Email Data Extraction with Intelligent Email Parser
 * High-Contrast Alternating Navy & White Layout with Live Telemetry
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
        <title>Automate Email Data Extraction with an Intelligent Email Parser | ExtractMails</title>
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
        "inverse-on-surface": "#eff0ff",
        "primary-container": "#2563eb",
        "surface-variant": "#dbe1ff",
        "on-error-container": "#93000a",
        "tertiary-container": "#0f69dc",
        "on-surface": "#01174b",
        "inverse-primary": "#b4c5ff",
        "secondary-fixed": "#dae2ff",
        "on-primary-fixed-variant": "#003ea8",
        "primary-fixed": "#dbe1ff",
        "on-primary-fixed": "#00174b",
        "tertiary-fixed": "#d8e2ff",
        "on-surface-variant": "#434655",
        "error-container": "#ffdad6",
        "secondary-fixed-dim": "#b2c5ff",
        "on-tertiary-fixed-variant": "#004395",
        "secondary-container": "#a6bdff",
        "outline-variant": "#c3c6d7",
        "on-tertiary": "#ffffff",
        "surface-container": "#ebedff",
        "on-secondary-fixed-variant": "#2c447e",
        "on-tertiary-fixed": "#001a42",
        "on-background": "#01174b",
        "on-secondary": "#ffffff",
        "outline": "#737686",
        "inverse-surface": "#1b2d61",
        "on-tertiary-container": "#edf0ff",
        "surface-container-lowest": "#ffffff",
        "on-secondary-container": "#334b85",
        "on-secondary-fixed": "#001847",
        "primary": "#004ac6",
        "surface": "#faf8ff",
        "surface-container-low": "#f3f3ff",
        "tertiary-fixed-dim": "#adc6ff",
        "tertiary": "#0051b1",
        "surface-container-highest": "#dbe1ff",
        "error": "#ba1a1a",
        "surface-container-high": "#e3e7ff",
        "on-primary": "#ffffff",
        "background": "#faf8ff",
        "surface-dim": "#d0d9ff",
        "surface-tint": "#0053db",
        "secondary": "#455c98",
        "on-error": "#ffffff",
        "primary-fixed-dim": "#b4c5ff",
        "on-primary-container": "#eeefff",
        "surface-bright": "#faf8ff"
      },
      borderRadius: {
        "DEFAULT": "0.25rem",
        "lg": "0.5rem",
        "xl": "0.75rem",
        "full": "9999px"
      },
      spacing: {
        "margin": "2rem",
        "gutter": "1.5rem",
        "space-2xl": "4rem",
        "space-xl": "2.5rem",
        "gutter-mobile": "1rem",
        "space-3xl": "6rem",
        "space-xs": "0.5rem",
        "space-lg": "1.5rem",
        "margin-mobile": "1rem",
        "space-sm": "0.75rem",
        "space-md": "1rem"
      },
      fontFamily: {
        "body-lg": ["DM Sans", "sans-serif"],
        "headline-lg": ["DM Sans", "sans-serif"],
        "label-sm": ["DM Sans", "sans-serif"],
        "label-md": ["DM Sans", "sans-serif"],
        "body-md": ["DM Sans", "sans-serif"],
        "headline-sm": ["DM Sans", "sans-serif"],
        "mono-data": ["JetBrains Mono", "monospace"],
        "display-lg-mobile": ["DM Sans", "sans-serif"],
        "headline-xl-mobile": ["DM Sans", "sans-serif"],
        "headline-xl": ["DM Sans", "sans-serif"],
        "body-sm": ["DM Sans", "sans-serif"],
        "headline-md": ["DM Sans", "sans-serif"],
        "headline-lg-mobile": ["DM Sans", "sans-serif"],
        "display-lg": ["DM Sans", "sans-serif"]
      },
      fontSize: {
        "body-lg": ["18px", { lineHeight: "28px", letterSpacing: "0em", fontWeight: "400" }],
        "headline-lg": ["32px", { lineHeight: "40px", letterSpacing: "-0.015em", fontWeight: "700" }],
        "label-sm": ["12px", { lineHeight: "16px", letterSpacing: "0.03em", fontWeight: "600" }],
        "label-md": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "600" }],
        "body-md": ["16px", { lineHeight: "24px", letterSpacing: "0em", fontWeight: "400" }],
        "headline-sm": ["20px", { lineHeight: "28px", letterSpacing: "-0.005em", fontWeight: "600" }],
        "mono-data": ["13px", { lineHeight: "18px", letterSpacing: "0em", fontWeight: "500" }],
        "display-lg-mobile": ["38px", { lineHeight: "46px", letterSpacing: "-0.02em", fontWeight: "700" }],
        "headline-xl-mobile": ["30px", { lineHeight: "38px", letterSpacing: "-0.015em", fontWeight: "700" }],
        "headline-xl": ["40px", { lineHeight: "48px", letterSpacing: "-0.02em", fontWeight: "700" }],
        "body-sm": ["14px", { lineHeight: "20px", letterSpacing: "0.005em", fontWeight: "400" }],
        "headline-md": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "600" }],
        "headline-lg-mobile": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "700" }],
        "display-lg": ["56px", { lineHeight: "64px", letterSpacing: "-0.03em", fontWeight: "700" }]
      }
    }
  }
};
</script>

<style>
  /* ========================================================= */
  /* 1. CRITICAL: MATERIAL SYMBOLS OUTLINED ICON DEFINITION   */
  /* ========================================================= */
  .email-parser-v2 .material-symbols-outlined {
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
  .email-parser-v2,
  .email-parser-v2 h1,
  .email-parser-v2 h2,
  .email-parser-v2 h3,
  .email-parser-v2 h4,
  .email-parser-v2 h5,
  .email-parser-v2 h6,
  .email-parser-v2 p,
  .email-parser-v2 a,
  .email-parser-v2 button,
  .email-parser-v2 input {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
  }

  .email-parser-v2 .font-mono-data,
  .email-parser-v2 code,
  .email-parser-v2 pre {
    font-family: 'JetBrains Mono', monospace !important;
  }

  /* Cross-browser reliable gradient text */
  .email-parser-v2 .gradient-text-blue {
    background: linear-gradient(to right, #60A5FA, #3B82F6, #93C5FD);
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
  }

  /* ========================================================= */
  /* 3. ASTRA FIXED HEADER & WP ADMIN-BAR CLEARANCE           */
  /* ========================================================= */
  .email-parser-v2 .hero-clearance {
    padding-top: 140px !important;
  }

  @media (max-width: 768px) {
    .email-parser-v2 .hero-clearance {
      padding-top: 110px !important;
    }
  }

  body.admin-bar .email-parser-v2 .hero-clearance {
    padding-top: 172px !important;
  }

  @media (max-width: 768px) {
    body.admin-bar .email-parser-v2 .hero-clearance {
      padding-top: 146px !important;
    }
  }

  /* Scoped layout resets */
  .email-parser-v2 {
    color: #01174b;
    background-color: #faf8ff;
    width: 100%;
    overflow-x: hidden;
  }

  .email-parser-v2 main {
    margin-top: 0 !important;
    padding-top: 0 !important;
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .email-parser-v2 ::-webkit-scrollbar {
    display: none;
  }
</style>

<div class="email-parser-v2 bg-surface font-body-md text-on-surface antialiased">
<main class="w-full bg-surface">
<div class="flex flex-col w-full">
<!-- ========================================================================= -->
<!-- 1. HERO SECTION (Dark Navy: #071C50 / #142F68 via on-surface/inverse-surface) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#071C50] text-[#FFFFFF] hero-clearance pb-space-3xl relative overflow-hidden">
<!-- Ambient Blue Radiant Glow -->
<div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-primary-container/20 blur-[120px] pointer-events-none"></div>
<div class="absolute top-1/2 -right-32 w-[520px] h-[520px] rounded-full bg-tertiary-container/25 blur-[140px] pointer-events-none"></div>
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin relative z-10">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<!-- Left Column: Copy & Actions -->
<div class="lg:col-span-6 flex flex-col items-start space-y-space-md">
<!-- Eyebrow Pill -->
<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#142F68]/70 backdrop-blur-md shadow-sm">
<span class="w-2 h-2 rounded-full bg-[#3B82F6] animate-pulse"></span>
<span class="font-mono-data text-mono-data text-[#DCE9FF] tracking-wide font-medium">ExtractMails / Email Parser</span>
</div>
<!-- Hero Headline -->
<h1 class="font-display-lg text-headline-xl lg:text-display-lg font-bold tracking-tight text-[#FFFFFF] leading-tight">
            Automate Email Data Extraction with an <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#60A5FA] via-[#3B82F6] to-[#93C5FD] gradient-text-blue">Intelligent Email Parser</span>
</h1>
<!-- Body Copy -->
<p class="font-body-lg text-body-lg text-[#DCE9FF]/85 max-w-xl leading-relaxed">
            Automatically parse email content, capture important details, and send the extracted information directly to the CRM, webhook, or business workflow, without repetitive manual data entry with the Email Parser service of ExtractMails.
          </p>
<!-- CTAs & Trust Badges -->
<div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md w-full sm:w-auto">
<a class="inline-flex items-center justify-center px-space-lg py-3.5 rounded-lg bg-primary-container text-on-primary font-label-md text-label-md font-semibold shadow-[0_0_28px_rgba(37,99,235,0.45)] hover:bg-[#3B82F6] transition-all transform hover:-translate-y-0.5" href="<?php echo esc_url(home_url('/pricing/')); ?>"><span class="">Get Started</span>
<span class="material-symbols-outlined ml-2 text-[18px]">arrow_forward</span>
</a>
<div class="flex items-center gap-2 text-[#DCE9FF]/75 font-body-sm text-body-sm px-2">
<span class="material-symbols-outlined text-[#10B981] text-[18px]">check_circle</span>
<span class="">Free trial • No credit card required</span>
</div>
</div>
<!-- Live Extraction Stats Bar -->
<div class="pt-space-md grid grid-cols-3 gap-space-md w-full max-w-lg">
<div class="p-3 rounded-lg bg-[#142F68]/40 backdrop-blur-md">
<div class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF]">99.8%</div>
<div class="font-label-sm text-label-sm text-[#DCE9FF]/70">Field Accuracy</div>
</div>
<div class="p-3 rounded-lg bg-[#142F68]/40 backdrop-blur-md">
<div class="font-headline-sm text-headline-sm font-bold text-[#60A5FA]">&lt; 350ms</div>
<div class="font-label-sm text-label-sm text-[#DCE9FF]/70">Parse Latency</div>
</div>
<div class="p-3 rounded-lg bg-[#142F68]/40 backdrop-blur-md">
<div class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF]">250M+</div>
<div class="font-label-sm text-label-sm text-[#DCE9FF]/70">Processed Mo.</div>
</div>
</div>
</div>
<!-- Right Column: Interactive Pipeline Graphic -->
<div class="lg:col-span-6 relative mt-space-lg lg:mt-0">
<div class="relative w-full rounded-2xl bg-gradient-to-b from-[#142F68]/80 to-[#071C50]/90 p-space-md lg:p-space-lg backdrop-blur-2xl shadow-[0_24px_64px_rgba(0,0,0,0.5)]">
<!-- Window Bar -->
<div class="flex items-center justify-between pb-space-sm mb-space-md border-b border-surface-variant/10">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-[#EF4444]/80 inline-block"></span>
<span class="w-3 h-3 rounded-full bg-[#F59E0B]/80 inline-block"></span>
<span class="w-3 h-3 rounded-full bg-[#10B981]/80 inline-block"></span>
<span class="ml-2 font-mono-data text-label-sm text-[#DCE9FF]/60">runtime://extractmails.pipeline.v3</span>
</div>
<div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#10B981]/15 text-[#10B981] font-mono-data text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-[#10B981] animate-ping"></span>
                Parsing Active
              </div>
</div>
<div class="space-y-space-md">
<!-- Step 1: Raw Inbound Email Envelope -->
<div class="rounded-xl p-3.5 bg-[#071C50]/80 shadow-inner">
<div class="flex items-center justify-between mb-2">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[#60A5FA] text-[20px]">mail</span>
<span class="font-mono-data text-label-sm text-[#DCE9FF] font-semibold">Inbound Email Received</span>
</div>
<span class="font-mono-data text-label-sm text-[#DCE9FF]/50">just now</span>
</div>
<div class="space-y-1 text-label-sm font-mono-data text-[#DCE9FF]/80">
<div class=""><span class="text-[#DCE9FF]/50">From:</span> alex@northstar.example</div>
<div class=""><span class="text-[#DCE9FF]/50">Subject:</span> Product Enquiry — Enterprise Integration</div>
<div class="p-2 rounded bg-[#142F68]/50 text-body-sm font-body-sm text-[#DCE9FF] italic mt-1.5">
                    “Hi, I'm Alex Morgan from Northstar. I'd like to discuss your services and enterprise SLA packages for Q3.”
                  </div>
</div>
</div>
<!-- Pipeline Connector Line -->
<div class="flex items-center justify-center -my-1">
<div class="h-6 w-0.5 bg-gradient-to-b from-[#3B82F6] to-transparent"></div>
</div>
<!-- Step 2: Intelligent Extraction Schema Node -->
<div class="rounded-xl p-4 bg-[#142F68]/70 shadow-lg relative overflow-hidden">
<div class="absolute -right-8 -top-8 w-28 h-28 bg-[#3B82F6]/20 rounded-full blur-xl"></div>
<div class="flex items-center justify-between mb-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[#60A5FA] text-[20px]">account_tree</span>
<span class="font-label-md text-label-md font-bold text-[#FFFFFF]">Extracted Structured Schema</span>
</div>
<span class="px-2 py-0.5 rounded text-[11px] font-mono-data bg-[#2563EB] text-[#FFFFFF]">JSON Validated</span>
</div>
<div class="grid grid-cols-2 gap-2 text-label-sm font-mono-data">
<div class="p-2 rounded bg-[#071C50]/60">
<span class="text-[#60A5FA]">contact_name:</span>
<span class="text-[#FFFFFF] ml-1 font-semibold">"Alex Morgan"</span>
</div>
<div class="p-2 rounded bg-[#071C50]/60">
<span class="text-[#60A5FA]">company:</span>
<span class="text-[#FFFFFF] ml-1 font-semibold">"Northstar"</span>
</div>
<div class="p-2 rounded bg-[#071C50]/60">
<span class="text-[#60A5FA]">email:</span>
<span class="text-[#FFFFFF] ml-1">"alex@northstar.example"</span>
</div>
<div class="p-2 rounded bg-[#071C50]/60">
<span class="text-[#60A5FA]">intent:</span>
<span class="text-[#10B981] ml-1 font-semibold">"Product Enquiry"</span>
</div>
</div>
</div>
<!-- Pipeline Connector Line -->
<div class="flex items-center justify-center -my-1">
<div class="h-6 w-0.5 bg-gradient-to-b from-[#3B82F6] to-[#10B981]"></div>
</div>
<!-- Step 3: Dispatch Destinations -->
<div class="grid grid-cols-3 gap-2">
<div class="p-2.5 rounded-lg bg-[#071C50]/90 text-center">
<span class="material-symbols-outlined text-[#60A5FA] text-[20px] mb-1">hub</span>
<div class="font-label-sm text-label-sm font-bold text-[#FFFFFF]">CRM Sync</div>
<div class="font-mono-data text-[11px] text-[#10B981]">Lead Created</div>
</div>
<div class="p-2.5 rounded-lg bg-[#071C50]/90 text-center">
<span class="material-symbols-outlined text-[#60A5FA] text-[20px] mb-1">webhook</span>
<div class="font-label-sm text-label-sm font-bold text-[#FFFFFF]">Webhook</div>
<div class="font-mono-data text-[11px] text-[#10B981]">200 OK • 82ms</div>
</div>
<div class="p-2.5 rounded-lg bg-[#071C50]/90 text-center">
<span class="material-symbols-outlined text-[#60A5FA] text-[20px] mb-1">bolt</span>
<div class="font-label-sm text-label-sm font-bold text-[#FFFFFF]">Workflow</div>
<div class="font-mono-data text-[11px] text-[#10B981]">Slack Alerted</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 2. THE HIDDEN COST (White: #FFFFFF / #F6FAFD) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#FFFFFF] text-[#01174B] py-space-3xl relative">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container text-primary font-mono-data text-label-sm font-semibold mb-space-xs">
<span class="material-symbols-outlined text-[16px]">warning</span>
          Operational Friction
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#01174B]">
          The Hidden Cost of Processing Emails Manually
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-xs leading-relaxed">
          Processing business emails manually consumes valuable operational time, leads to costly data-entry errors, and delays response times to important customer enquiries.
        </p>
</div>
<!-- Asymmetrical Split Editorial Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
<!-- Left: Realistic Business Image with Data Overlay -->
<div class="lg:col-span-5 relative">
<div class="rounded-2xl overflow-hidden shadow-xl bg-surface-container relative">
<img class="w-full h-[520px] object-cover" data-alt="Modern professional woman working at executive office desk with dual monitors analyzing corporate operational email inquiries in high-rise tech headquarters with warm ambient lighting and sleek minimalist design" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBDg3McsIO4FnCVdzv_E31TAzBp-Nrm9fHBzjUTMsQ929NeUo8mwA-pONJKmNTjyCwPlHwj95wBEROZbx4_kYTSmcOB7tpHswNGdpbvdr07czrBVRBFscEcO9FdEI4AERQuznhuO08tmO_WePGZGG5Lsc8R4NQ5jI5BzmKhzwRwh0Q_SBTZgbxHAh3XI_9jELeCqX_ZfliCT63ojnnMjkgzcH3umfoLLIpuzJYcKv1xRa-_ofAU2zSHQg">
<!-- Visual Glass Floating Card Over Image -->
<div class="absolute bottom-4 inset-x-4 p-4 rounded-xl bg-on-surface/85 backdrop-blur-md text-surface-container-lowest shadow-2xl">
<div class="flex items-center justify-between pb-2 mb-2 border-b border-surface-variant/20">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-error text-[18px]">hourglass_empty</span>
<span class="font-label-md text-label-md font-semibold text-[#FFFFFF]">Manual Inbox Bottleneck</span>
</div>
<span class="font-mono-data text-label-sm text-[#FFDAD6]">3.4 hrs / rep / day</span>
</div>
<p class="font-body-sm text-body-sm text-primary-fixed-dim/80">
                Unstructured conversational threads create latency, missed escalations, and unrecoverable lead attrition.
              </p>
</div>
</div>
</div>
<!-- Right: 7 Elegant Numbered Issue Rows -->
<div class="lg:col-span-7 flex flex-col space-y-3">
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">
<span class="font-mono-data text-label-md font-bold text-primary-container px-2.5 py-1 rounded-md bg-surface-container-highest">01</span>
<div class="flex-1 min-w-0">
<h4 class="font-headline-sm text-headline-sm font-semibold text-[#01174B]">Time-Consuming Email Review</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Manually reviewing every incoming message drains hours from your team's day that should be spent closing deals.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">
<span class="font-mono-data text-label-md font-bold text-primary-container px-2.5 py-1 rounded-md bg-surface-container-highest">02</span>
<div class="flex-1 min-w-0">
<h4 class="font-headline-sm text-headline-sm font-semibold text-[#01174B]">Difficulty Finding Key Details</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Critical customer information gets lost in unstructured, conversational email bodies, notes, and signatures.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">
<span class="font-mono-data text-label-md font-bold text-primary-container px-2.5 py-1 rounded-md bg-surface-container-highest">03</span>
<div class="flex-1 min-w-0">
<h4 class="font-headline-sm text-headline-sm font-semibold text-[#01174B]">Repetitive Data Entry</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Copying names, order numbers, and inquiry details into multiple databases by hand introduces severe typo rates.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">
<span class="font-mono-data text-label-md font-bold text-primary-container px-2.5 py-1 rounded-md bg-surface-container-highest">04</span>
<div class="flex-1 min-w-0">
<h4 class="font-headline-sm text-headline-sm font-semibold text-[#01174B]">Delayed Lead Follow-Up</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Slow manual review means high-intent leads wait hours or days for initial responses, slashing conversion by up to 70%.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">
<span class="font-mono-data text-label-md font-bold text-primary-container px-2.5 py-1 rounded-md bg-surface-container-highest">05</span>
<div class="flex-1 min-w-0">
<h4 class="font-headline-sm text-headline-sm font-semibold text-[#01174B]">Inconsistent Data Handling</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Different team members format, record, and categorize incoming information differently, muddying operational BI.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">
<span class="font-mono-data text-label-md font-bold text-primary-container px-2.5 py-1 rounded-md bg-surface-container-highest">06</span>
<div class="flex-1 min-w-0">
<h4 class="font-headline-sm text-headline-sm font-semibold text-[#01174B]">Disconnected Business Workflows</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Unstructured emails leave downstream tools like CRMs, ERPs, and ticket desks starved of real-time operational sync.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">
<span class="font-mono-data text-label-md font-bold text-primary-container px-2.5 py-1 rounded-md bg-surface-container-highest">07</span>
<div class="flex-1 min-w-0">
<h4 class="font-headline-sm text-headline-sm font-semibold text-[#01174B]">Repetitive Administrative Tasks</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Routine sorting, tagging, and manual forwarding consume headcount that should be dedicated to strategic pipeline growth.</p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 3. STOP COPYING DATA MANUALLY (Dark Navy: #071C50 / #142F68) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#071C50] text-[#FFFFFF] py-space-3xl relative">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="text-center max-w-3xl mx-auto mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#142F68] text-[#60A5FA] font-mono-data text-label-sm font-medium mb-space-xs">
<span class="material-symbols-outlined text-[16px]">transform</span>
          Automation Transformation
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#FFFFFF]">
          Stop Copying Data from Emails Manually
        </h2>
<p class="font-body-lg text-body-lg text-[#DCE9FF]/80 mt-space-xs leading-relaxed">
          Break free from tedious manual workflows. See how ExtractMails automates the entire ingestion pipeline from inbox directly to database.
        </p>
</div>
<!-- Before & After Comparison -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter mb-space-2xl">
<!-- Before: Manual Workflow -->
<div class="rounded-2xl p-space-lg bg-[#142F68]/30 backdrop-blur-md relative overflow-hidden">
<div class="flex items-center justify-between pb-space-sm mb-space-md border-b border-surface-variant/10">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-error text-[22px]">cancel</span>
<span class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF]">Manual Email Handling</span>
</div>
<span class="px-2.5 py-1 rounded text-label-sm font-mono-data bg-error/15 text-[#FFDAD6]">High Friction</span>
</div>
<div class="space-y-4">
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/60">
<span class="w-7 h-7 rounded-full bg-error/20 text-error flex items-center justify-center font-mono-data text-label-sm font-bold">1</span>
<div class="font-body-md text-body-md text-[#DCE9FF]/80">Incoming Email sits idle in congested team inbox</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/60">
<span class="w-7 h-7 rounded-full bg-error/20 text-error flex items-center justify-center font-mono-data text-label-sm font-bold">2</span>
<div class="font-body-md text-body-md text-[#DCE9FF]/80">Rep manually reads text, hunting for customer specs</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/60">
<span class="w-7 h-7 rounded-full bg-error/20 text-error flex items-center justify-center font-mono-data text-label-sm font-bold">3</span>
<div class="font-body-md text-body-md text-[#DCE9FF]/80">Manually copies snippets across browser tabs</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/60">
<span class="w-7 h-7 rounded-full bg-error/20 text-error flex items-center justify-center font-mono-data text-label-sm font-bold">4</span>
<div class="font-body-md text-body-md text-[#DCE9FF]/80">Pastes into CRM • High error rate, 45+ mins delay</div>
</div>
</div>
</div>
<!-- After: Automated Pipeline -->
<div class="rounded-2xl p-space-lg bg-[#142F68]/85 backdrop-blur-xl relative overflow-hidden shadow-[0_16px_48px_rgba(0,74,198,0.25)]">
<div class="absolute -right-12 -bottom-12 w-48 h-48 bg-primary-container/20 rounded-full blur-2xl pointer-events-none"></div>
<div class="flex items-center justify-between pb-space-sm mb-space-md border-b border-surface-variant/20">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[#10B981] text-[22px]">check_circle</span>
<span class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF]">ExtractMails Automated Pipeline</span>
</div>
<span class="px-2.5 py-1 rounded text-label-sm font-mono-data bg-[#10B981]/20 text-[#10B981] font-semibold">Real-Time Sync</span>
</div>
<div class="space-y-4">
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/80">
<span class="w-7 h-7 rounded-full bg-[#10B981]/20 text-[#10B981] flex items-center justify-center font-mono-data text-label-sm font-bold">1</span>
<div class="font-body-md text-body-md text-[#FFFFFF]">Inbound webhook or forwarding catches email instantly</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/80">
<span class="w-7 h-7 rounded-full bg-[#10B981]/20 text-[#10B981] flex items-center justify-center font-mono-data text-label-sm font-bold">2</span>
<div class="font-body-md text-body-md text-[#FFFFFF]">Neural parsing model detects entities, keys, and values</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/80">
<span class="w-7 h-7 rounded-full bg-[#10B981]/20 text-[#10B981] flex items-center justify-center font-mono-data text-label-sm font-bold">3</span>
<div class="font-body-md text-body-md text-[#FFFFFF]">Structured JSON schema constructed in sub-second time</div>
</div>
<div class="flex items-center gap-3 p-3 rounded-lg bg-[#071C50]/80">
<span class="w-7 h-7 rounded-full bg-[#10B981]/20 text-[#10B981] flex items-center justify-center font-mono-data text-label-sm font-bold">4</span>
<div class="font-body-md text-body-md text-[#FFFFFF]">Direct dispatch to CRM • Instant execution, 100% field accuracy</div>
</div>
</div>
</div>
</div>
<!-- 3x2 Grid of Inefficiencies Solved -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
<div class="p-5 rounded-xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-[#2563EB]/25 text-[#60A5FA] flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">drafts</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Opening Emails One by One</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">Eliminates tedious inbox triage by automatically ingesting every incoming business email continuously.</p>
</div>
<div class="p-5 rounded-xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-[#2563EB]/25 text-[#60A5FA] flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">manage_search</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Searching for Important Details</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">Pinpoints vital customer names, order IDs, booking dates, and key requirements without human eye-scanning.</p>
</div>
<div class="p-5 rounded-xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-[#2563EB]/25 text-[#60A5FA] flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">content_paste_off</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Copying and Pasting Data</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">Replaces error-prone manual copy-pasting with automated schema-driven field extraction and schema typing.</p>
</div>
<div class="p-5 rounded-xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-[#2563EB]/25 text-[#60A5FA] flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">sync_alt</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Updating CRM Records</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">Automatically matches or provisions new leads, contacts, and deals inside your CRM repository instantly.</p>
</div>
<div class="p-5 rounded-xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-[#2563EB]/25 text-[#60A5FA] flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">fork_right</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Routing Email Information</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">Dispatches parsed payloads to the exact department, agent queue, or webhook endpoint with custom routing.</p>
</div>
<div class="p-5 rounded-xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-[#2563EB]/25 text-[#60A5FA] flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-[20px]">autorenew</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Repeating Routine Tasks</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">Runs continuous automated background syncs so your operations scale effortlessly with zero incremental labor.</p>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 4. FIVE STEPS TO AUTOMATED EMAIL PARSING (White: #FFFFFF / #F6FAFD) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#FFFFFF] text-[#01174B] py-space-3xl relative">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container text-primary font-mono-data text-label-sm font-semibold mb-space-xs">
<span class="material-symbols-outlined text-[16px]">linear_scale</span>
          Streamlined Process
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#01174B]">
          Five Steps to Automated Email Parsing
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-xs leading-relaxed">
          A streamlined, frictionless 5-stage pipeline designed for modern enterprise workflows and high-scale outbound architectures.
        </p>
</div>
<!-- Process Pipeline Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-space-md items-stretch">
<!-- Step 1 -->
<div class="p-space-md rounded-xl bg-surface-container-low flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-mono-data text-headline-sm font-bold text-primary-container">01</span>
<div class="w-8 h-8 rounded-md bg-surface-container-highest flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[18px]">inbox</span>
</div>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Receive Incoming Emails</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              Transfers incoming email messages into the workflow through the supported email forwarding or IMAP setup.
            </p>
</div>
<div class="pt-4 mt-4 border-t border-surface-variant/30 font-mono-data text-label-sm text-primary">
            auto-forwarding • webhook
          </div>
</div>
<!-- Step 2 -->
<div class="p-space-md rounded-xl bg-surface-container-low flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-mono-data text-headline-sm font-bold text-primary-container">02</span>
<div class="w-8 h-8 rounded-md bg-surface-container-highest flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[18px]">document_scanner</span>
</div>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Parse Email Content</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              ExtractMails reads incoming emails and identifies relevant info, including contact details, enquiries, and order items.
            </p>
</div>
<div class="pt-4 mt-4 border-t border-surface-variant/30 font-mono-data text-label-sm text-primary">
            regex • AI extraction
          </div>
</div>
<!-- Step 3: Prominent Highlighted Center Card -->
<div class="p-space-md rounded-xl bg-[#071C50] text-[#FFFFFF] flex flex-col justify-between shadow-xl relative overflow-hidden lg:-translate-y-2">
<div class="absolute -top-10 -right-10 w-24 h-24 bg-[#3B82F6]/30 rounded-full blur-xl pointer-events-none"></div>
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-mono-data text-headline-sm font-bold text-[#60A5FA]">03</span>
<div class="w-8 h-8 rounded-md bg-primary-container flex items-center justify-center text-on-primary">
<span class="material-symbols-outlined text-[18px]">data_object</span>
</div>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-2">Extract Structured Data</h3>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/85 leading-relaxed">
              All relevant information is normalized into structured JSON fields that are readily ingested by business databases.
            </p>
<!-- Mini Schema UI Preview -->
<div class="mt-3 p-2 rounded bg-[#142F68] font-mono-data text-[11px] text-[#93C5FD]">
<div class="">{</div>
<div class="pl-2 text-[#FFFFFF]">"lead": true,</div>
<div class="pl-2 text-[#10B981]">"score": 98</div>
<div class="">}</div>
</div>
</div>
<div class="pt-3 mt-3 border-t border-surface-variant/20 font-mono-data text-label-sm text-[#60A5FA]">
            Strict Typing • Verified
          </div>
</div>
<!-- Step 4 -->
<div class="p-space-md rounded-xl bg-surface-container-low flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-mono-data text-headline-sm font-bold text-primary-container">04</span>
<div class="w-8 h-8 rounded-md bg-surface-container-highest flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[18px]">send_and_archive</span>
</div>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Send to Multiple Systems</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              Delivers parsed information to a designated business application, such as a webhook, CRM, or data warehouse.
            </p>
</div>
<div class="pt-4 mt-4 border-t border-surface-variant/30 font-mono-data text-label-sm text-primary">
            HubSpot • Salesforce • API
          </div>
</div>
<!-- Step 5 -->
<div class="p-space-md rounded-xl bg-surface-container-low flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-mono-data text-headline-sm font-bold text-primary-container">05</span>
<div class="w-8 h-8 rounded-md bg-surface-container-highest flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[18px]">smart_toy</span>
</div>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Automate the Next Step</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              Set up downstream workflows that reply, tag, alert Slack, or assign account executives according to match conditions.
            </p>
</div>
<div class="pt-4 mt-4 border-t border-surface-variant/30 font-mono-data text-label-sm text-primary">
            Rules • Zero Delay
          </div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 5. POWERFUL FEATURES (Dark Navy: #071C50 / #142F68) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#071C50] text-[#FFFFFF] py-space-3xl relative">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#142F68] text-[#60A5FA] font-mono-data text-label-sm font-medium mb-space-xs">
<span class="material-symbols-outlined text-[16px]">tune</span>
          Platform Capabilities
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#FFFFFF]">
          Powerful Features Built for Business Automation
        </h2>
<p class="font-body-lg text-body-lg text-[#DCE9FF]/80 mt-space-xs leading-relaxed">
          Engineered with enterprise-grade reliability, SOC-2 security principles, and ultra-flexible integration protocols.
        </p>
</div>
<!-- Bento Grid (7 Features) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-space-md">
<!-- Large Card 1: Incoming Email Parsing (8 cols) -->
<div class="lg:col-span-8 p-space-lg rounded-2xl bg-[#142F68]/60 backdrop-blur-md relative overflow-hidden flex flex-col justify-between">
<div class="relative z-10">
<div class="flex items-center justify-between mb-space-md">
<div class="w-12 h-12 rounded-xl bg-primary-container/20 text-[#60A5FA] flex items-center justify-center">
<span class="material-symbols-outlined text-[24px]">mark_email_read</span>
</div>
<span class="font-mono-data text-label-sm text-[#10B981] px-2.5 py-1 rounded-full bg-[#10B981]/15">Deep Inspection</span>
</div>
<h3 class="font-headline-md text-headline-md font-bold text-[#FFFFFF] mb-2">Incoming Email Parsing</h3>
<p class="font-body-md text-body-md text-[#DCE9FF]/85 max-w-xl leading-relaxed">
              Deep inspection of email headers, multipart body text, nested HTML tables, and attached documents (PDF, CSV, XLSX) with sub-second parsing precision.
            </p>
</div>
<!-- Visual Decorative Wave Line -->
<div class="mt-space-md pt-space-sm border-t border-surface-variant/10 flex items-center gap-3 font-mono-data text-label-sm text-[#DCE9FF]/70">
<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#3B82F6]"></span> Headers</span>
<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#60A5FA]"></span> Body Text</span>
<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#93C5FD]"></span> HTML Tables</span>
<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#10B981]"></span> Attachments</span>
</div>
</div>
<!-- Medium Card 2: Structured Data Extraction (4 cols) -->
<div class="lg:col-span-4 p-space-lg rounded-2xl bg-[#142F68]/60 backdrop-blur-md relative overflow-hidden flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-xl bg-primary-container/20 text-[#60A5FA] flex items-center justify-center mb-space-md">
<span class="material-symbols-outlined text-[24px]">schema</span>
</div>
<h3 class="font-headline-md text-headline-md font-bold text-[#FFFFFF] mb-2">Structured Extraction</h3>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/85 leading-relaxed">
              Transforms chaotic conversational paragraphs into clean, strictly typed JSON objects and array keys ready for computational downstream sync.
            </p>
</div>
<div class="mt-space-md pt-2 font-mono-data text-label-sm text-[#60A5FA]">
            Strict Typing Guaranteed →
          </div>
</div>
<!-- Medium Card 3: Webhook Integration (4 cols) -->
<div class="lg:col-span-4 p-space-md rounded-2xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-primary-container/20 text-[#60A5FA] flex items-center justify-center mb-space-sm">
<span class="material-symbols-outlined text-[20px]">webhook</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Webhook Integration</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">
            Real-time HTTPS POST callbacks with HMAC signature verification, cryptographic security, and automated exponential-backoff retries.
          </p>
</div>
<!-- Medium Card 4: CRM and Business System Integration (4 cols) -->
<div class="lg:col-span-4 p-space-md rounded-2xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-primary-container/20 text-[#60A5FA] flex items-center justify-center mb-space-sm">
<span class="material-symbols-outlined text-[20px]">sync</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">CRM System Integration</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">
            Native two-way synchronization with Salesforce, HubSpot, Zoho, and custom SQL/NoSQL storage environments.
          </p>
</div>
<!-- Medium Card 5: Rule-Based Email Workflows (4 cols) -->
<div class="lg:col-span-4 p-space-md rounded-2xl bg-[#142F68]/50 backdrop-blur-md">
<div class="w-10 h-10 rounded-lg bg-primary-container/20 text-[#60A5FA] flex items-center justify-center mb-space-sm">
<span class="material-symbols-outlined text-[20px]">alt_route</span>
</div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Rule-Based Workflows</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">
            Apply expressive conditional logic: “If subject contains ‘Urgent Quote’, route to Tier-1 AE on Slack instantly.”
          </p>
</div>
<!-- Supporting Card 6: Automated Lead Capture (6 cols) -->
<div class="lg:col-span-6 p-space-md rounded-2xl bg-[#142F68]/40 backdrop-blur-md flex items-start gap-4">
<div class="w-10 h-10 rounded-lg bg-[#10B981]/20 text-[#10B981] flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">person_add</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Automated Lead Capture</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">
              Never drop high-intent inbound opportunities. Incoming leads are captured, normalized, and deposited in sales sequences within milliseconds.
            </p>
</div>
</div>
<!-- Supporting Card 7: Streamlined Operations (6 cols) -->
<div class="lg:col-span-6 p-space-md rounded-2xl bg-[#142F68]/40 backdrop-blur-md flex items-start gap-4">
<div class="w-10 h-10 rounded-lg bg-[#3B82F6]/20 text-[#60A5FA] flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">query_stats</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Streamlined Email Operations</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80 leading-relaxed">
              Centralized audit logs, granular parsing run-history, payload replay capabilities, and automated exception diagnostics.
            </p>
</div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 6. USE CASES (White: #FFFFFF / #F6FAFD) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#FFFFFF] text-[#01174B] py-space-3xl relative">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container text-primary font-mono-data text-label-sm font-semibold mb-space-xs">
<span class="material-symbols-outlined text-[16px]">business_center</span>
          Proven Workflows
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#01174B]">
          One Email Parser for Multiple Business Processes
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-xs leading-relaxed">
          From sales development to supply chain operations, automate high-frequency data ingestion across critical corporate functions.
        </p>
</div>
<!-- 6 Distinct Editorial Use-Case Cards (2-column layout) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
<!-- Use Case 1 -->
<div class="p-space-lg rounded-2xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="w-10 h-10 rounded-lg bg-surface-container-highest text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">contacts</span>
</span>
<span class="font-mono-data text-label-sm text-on-surface-variant">Sales • Inbound</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Lead Enquiry Parsing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Converts inbound contact forms, RFPs, and direct emails into qualified CRM lead records within seconds, complete with company budget and scope parameters.
            </p>
</div>
<div class="mt-4 pt-3 border-t border-surface-variant/30 flex items-center justify-between font-mono-data text-label-sm text-primary">
<span class="">Extracts: Contact • Scope • Budget</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</div>
<!-- Use Case 2 -->
<div class="p-space-lg rounded-2xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="w-10 h-10 rounded-lg bg-surface-container-highest text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">support_agent</span>
</span>
<span class="font-mono-data text-label-sm text-on-surface-variant">Support • Triage</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Customer Enquiry Processing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Extracts support ticket parameters, urgency tags, account tiers, and customer metadata to feed Zendesk, Jira Service Desk, or custom queues automatically.
            </p>
</div>
<div class="mt-4 pt-3 border-t border-surface-variant/30 flex items-center justify-between font-mono-data text-primary text-label-sm">
<span class="">Extracts: Account ID • Issue • Urgency</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</div>
<!-- Use Case 3 -->
<div class="p-space-lg rounded-2xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="w-10 h-10 rounded-lg bg-surface-container-highest text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">calendar_month</span>
</span>
<span class="font-mono-data text-label-sm text-on-surface-variant">Hospitality • Events</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Booking Confirmation Parsing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Captures reservation confirmation numbers, guest names, check-in schedules, and venue preferences automatically from booking platforms and aggregators.
            </p>
</div>
<div class="mt-4 pt-3 border-t border-surface-variant/30 flex items-center justify-between font-mono-data text-primary text-label-sm">
<span class="">Extracts: Dates • Guest • Venue ID</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</div>
<!-- Use Case 4 -->
<div class="p-space-lg rounded-2xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="w-10 h-10 rounded-lg bg-surface-container-highest text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">receipt_long</span>
</span>
<span class="font-mono-data text-label-sm text-on-surface-variant">E-Commerce • Logistics</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Order Notification Parsing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Parses e-commerce store receipts and B2B vendor purchase notifications, line item tables, SKU breakdowns, and delivery tracking destinations effortlessly.
            </p>
</div>
<div class="mt-4 pt-3 border-t border-surface-variant/30 flex items-center justify-between font-mono-data text-primary text-label-sm">
<span class="">Extracts: SKUs • Billing Totals • Shipping</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</div>
<!-- Use Case 5 -->
<div class="p-space-lg rounded-2xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="w-10 h-10 rounded-lg bg-surface-container-highest text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">notifications_active</span>
</span>
<span class="font-mono-data text-label-sm text-on-surface-variant">Alerts • Incident Mgmt</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Automated Email Notifications</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Triggers instantaneous Slack channels, SMS dispatch, or internal team alerts when threshold events, critical errors, or high-value contracts arrive.
            </p>
</div>
<div class="mt-4 pt-3 border-t border-surface-variant/30 flex items-center justify-between font-mono-data text-primary text-label-sm">
<span class="">Extracts: Severity • Timestamp • Trace</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</div>
<!-- Use Case 6 -->
<div class="p-space-lg rounded-2xl bg-surface-container-low hover:bg-surface-container transition-all flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-sm">
<span class="w-10 h-10 rounded-lg bg-surface-container-highest text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">local_shipping</span>
</span>
<span class="font-mono-data text-label-sm text-on-surface-variant">Supply Chain • ERP</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-[#01174B] mb-2">Operational Email Processing</h3>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Handles repetitive freight logistics updates, bills of lading, customs statuses, and recurring invoice attachments without manual employee touch.
            </p>
</div>
<div class="mt-4 pt-3 border-t border-surface-variant/30 flex items-center justify-between font-mono-data text-primary text-label-sm">
<span class="">Extracts: Tracking • Customs Code • Route</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 7. WHO SHOULD USE AN EMAIL PARSER (Dark Navy: #071C50 / #142F68) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#071C50] text-[#FFFFFF] py-space-3xl relative">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#142F68] text-[#60A5FA] font-mono-data text-label-sm font-medium mb-space-xs">
<span class="material-symbols-outlined text-[16px]">groups</span>
          Tailored Personas
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#FFFFFF]">
          Built for Teams That Rely on Email
        </h2>
<p class="font-body-lg text-body-lg text-[#DCE9FF]/80 mt-space-xs leading-relaxed">
          Whether scaling sales outreach or orchestrating complex supply chains, automated email parsing empowers every modern business unit.
        </p>
</div>
<!-- Split Layout: Photography Left + 6 Translucent Audience Cards Right -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<!-- Left: Team Collaboration Photography -->
<div class="lg:col-span-5 relative">
<div class="rounded-2xl overflow-hidden shadow-2xl relative">
<img class="w-full h-[580px] object-cover" data-alt="Modern tech company executive team and operations directors collaborating around wooden conference table with laptops reviewing automated cloud data workflows with city skyline view" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQA3o6mHufTbkZSK-sfm7oLfdsL-6Ns6sXnyNxzNGUS2yBT0SeU8nS4hT7GKY6aCt4-lWerp5OpOV0Oggiu8WW4-KLu529nncF9vmxoalFIOGekqXjEgvH_ddTUlIDr4_cYw0dzssBv15QzPUFp6IKJ8MGSNKexsLXy0EKdIcCJNQKPRGPmYfaTgTiB1kbgJJs_vU67-JBhKGmhHMoMnOSKHGU2Ftza0LZ_N7m4E4DJ6knURjP_O1Ltg">
<div class="absolute inset-0 bg-gradient-to-t from-[#071C50] via-transparent to-transparent opacity-80"></div>
<div class="absolute bottom-6 inset-x-6 p-4 rounded-xl bg-[#142F68]/85 backdrop-blur-md">
<div class="font-headline-sm text-headline-sm font-bold text-[#FFFFFF] mb-1">Cross-Functional Agility</div>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/80">Eliminate data silos between customer-facing reps and operational databases.</p>
</div>
</div>
</div>
<!-- Right: 6 Dark Glass Audience Cards -->
<div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="p-space-md rounded-xl bg-[#142F68]/55 backdrop-blur-md">
<div class="w-8 h-8 rounded-lg bg-[#3B82F6]/25 text-[#60A5FA] flex items-center justify-center mb-2.5">
<span class="material-symbols-outlined text-[18px]">trending_up</span>
</div>
<h4 class="font-label-md text-label-md font-bold text-[#FFFFFF] mb-1">Sales &amp; Lead Gen Teams</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/75 leading-relaxed">Respond faster to inbound prospects and ensure every conversion opportunity enters pipeline automatically.</p>
</div>
<div class="p-space-md rounded-xl bg-[#142F68]/55 backdrop-blur-md">
<div class="w-8 h-8 rounded-lg bg-[#3B82F6]/25 text-[#60A5FA] flex items-center justify-center mb-2.5">
<span class="material-symbols-outlined text-[18px]">hub</span>
</div>
<h4 class="font-label-md text-label-md font-bold text-[#FFFFFF] mb-1">Sales Operations Teams</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/75 leading-relaxed">Maintain spotless CRM hygiene without enforcing tedious manual logging chores on outbound account executives.</p>
</div>
<div class="p-space-md rounded-xl bg-[#142F68]/55 backdrop-blur-md">
<div class="w-8 h-8 rounded-lg bg-[#3B82F6]/25 text-[#60A5FA] flex items-center justify-center mb-2.5">
<span class="material-symbols-outlined text-[18px]">headset_mic</span>
</div>
<h4 class="font-label-md text-label-md font-bold text-[#FFFFFF] mb-1">Customer Support Teams</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/75 leading-relaxed">Accelerate ticket triage by immediately dispatching incoming user requests with pre-filled context parameters.</p>
</div>
<div class="p-space-md rounded-xl bg-[#142F68]/55 backdrop-blur-md">
<div class="w-8 h-8 rounded-lg bg-[#3B82F6]/25 text-[#60A5FA] flex items-center justify-center mb-2.5">
<span class="material-symbols-outlined text-[18px]">precision_manufacturing</span>
</div>
<h4 class="font-label-md text-label-md font-bold text-[#FFFFFF] mb-1">Operations Teams</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/75 leading-relaxed">Eliminate repetitive data transfer bottlenecks across suppliers, procurement requests, and fulfillment pipelines.</p>
</div>
<div class="p-space-md rounded-xl bg-[#142F68]/55 backdrop-blur-md">
<div class="w-8 h-8 rounded-lg bg-[#3B82F6]/25 text-[#60A5FA] flex items-center justify-center mb-2.5">
<span class="material-symbols-outlined text-[18px]">code</span>
</div>
<h4 class="font-label-md text-label-md font-bold text-[#FFFFFF] mb-1">Developers &amp; Builders</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/75 leading-relaxed">Integrate email data triggers directly into microservices via predictable, schema-validated HTTPS JSON payloads.</p>
</div>
<div class="p-space-md rounded-xl bg-[#142F68]/55 backdrop-blur-md">
<div class="w-8 h-8 rounded-lg bg-[#3B82F6]/25 text-[#60A5FA] flex items-center justify-center mb-2.5">
<span class="material-symbols-outlined text-[18px]">corporate_fare</span>
</div>
<h4 class="font-label-md text-label-md font-bold text-[#FFFFFF] mb-1">Agencies &amp; Growth Firms</h4>
<p class="font-body-sm text-body-sm text-[#DCE9FF]/75 leading-relaxed">Scale multi-client intake funnels and report ingestion without ballooning headcount or operational overhead.</p>
</div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 8. WHY CHOOSE EXTRACTMAILS (White: #FFFFFF / #F6FAFD) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#FFFFFF] text-[#01174B] py-space-3xl relative">
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="max-w-3xl mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container text-primary font-mono-data text-label-sm font-semibold mb-space-xs">
<span class="material-symbols-outlined text-[16px]">verified</span>
          Why ExtractMails
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#01174B]">
          Email Parsing Designed Around Automation
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-xs leading-relaxed">
          Purpose-built for mission-critical business automation with unmatched reliability, schema integrity, and parse speed.
        </p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<!-- Left: 5 Advantage Rows -->
<div class="lg:col-span-6 space-y-space-md">
<div class="flex items-start gap-3.5">
<div class="w-8 h-8 rounded-full bg-primary-container/15 text-primary-container flex-shrink-0 flex items-center justify-center mt-1">
<span class="material-symbols-outlined text-[20px]">check</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#01174B]">Focus on Incoming Emails</h4>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Specialization in deep inbound email processing rather than generic, noisy web scraping or spam software.</p>
</div>
</div>
<div class="flex items-start gap-3.5">
<div class="w-8 h-8 rounded-full bg-primary-container/15 text-primary-container flex-shrink-0 flex items-center justify-center mt-1">
<span class="material-symbols-outlined text-[20px]">check</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#01174B]">Extract Relevant Information</h4>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Intelligent entity recognition that isolates vital business values while cleanly filtering disclaimers and signatures.</p>
</div>
</div>
<div class="flex items-start gap-3.5">
<div class="w-8 h-8 rounded-full bg-primary-container/15 text-primary-container flex-shrink-0 flex items-center justify-center mt-1">
<span class="material-symbols-outlined text-[20px]">check</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#01174B]">Connect With Webhooks and CRMs</h4>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Out-of-the-box compatibility with your existing corporate stack, automation engines, and custom databases.</p>
</div>
</div>
<div class="flex items-start gap-3.5">
<div class="w-8 h-8 rounded-full bg-primary-container/15 text-primary-container flex-shrink-0 flex items-center justify-center mt-1">
<span class="material-symbols-outlined text-[20px]">check</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#01174B]">Automate Repetitive Tasks</h4>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Eliminates tens of hours of human manual typing, tab toggling, and data sorting every business week.</p>
</div>
</div>
<div class="flex items-start gap-3.5">
<div class="w-8 h-8 rounded-full bg-primary-container/15 text-primary-container flex-shrink-0 flex items-center justify-center mt-1">
<span class="material-symbols-outlined text-[20px]">check</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-[#01174B]">Reduce Manual Data Handling</h4>
<p class="font-body-md text-body-md text-on-surface-variant mt-0.5">Minimizes human transcription errors to nearly zero and guarantees uniform database field formats.</p>
</div>
</div>
</div>
<!-- Right: Layered UI Architecture Visualization -->
<div class="lg:col-span-6 relative">
<div class="p-space-lg rounded-2xl bg-surface-container-low shadow-xl relative overflow-hidden">
<div class="flex items-center justify-between pb-3 mb-4 border-b border-surface-variant/20">
<div class="font-mono-data text-label-sm font-bold text-primary">REAL-TIME INGESTION RUNTIME</div>
<div class="flex items-center gap-1.5 font-mono-data text-label-sm text-[#10B981]">
<span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                ONLINE • 99.99% Uptime
              </div>
</div>
<!-- Visualization Layers -->
<div class="space-y-3 font-mono-data text-label-sm">
<!-- Raw Layer -->
<div class="p-3 rounded-lg bg-surface-container-highest">
<div class="text-on-surface-variant text-[11px] mb-1 font-semibold uppercase">1. Raw Stream Tokenizer</div>
<div class="text-[#01174B] truncate">Received RFC-822 message (24.8KB) from mail-out.corp.example</div>
</div>
<!-- Parsing Engine Layer -->
<div class="p-3 rounded-lg bg-primary text-on-primary shadow-md">
<div class="text-primary-fixed text-[11px] mb-1 font-semibold uppercase">2. Entity Isolation Engine</div>
<div class="text-surface-container-lowest font-medium truncate">✓ 8 keys mapped • 100% confidence • 0 missing attributes</div>
</div>
<!-- Structured Payload Layer -->
<div class="p-3 rounded-lg bg-surface-container-highest">
<div class="text-on-surface-variant text-[11px] mb-1 font-semibold uppercase">3. Dispatched JSON Object</div>
<div class="text-primary-container truncate font-semibold">{ "event": "order_processed", "val": 4820.00, "status": "synced" }</div>
</div>
<!-- Downstream Target Layer -->
<div class="grid grid-cols-2 gap-2 pt-1">
<div class="p-2.5 rounded bg-surface-container text-center font-bold text-on-surface">
                  CRM Synced: &lt;140ms
                </div>
<div class="p-2.5 rounded bg-surface-container text-center font-bold text-[#10B981]">
                  Webhook: 200 OK
                </div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 9. FINAL CTA (Dark Navy: #071C50 / #142F68) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#071C50] text-[#FFFFFF] py-space-3xl relative overflow-hidden">
<!-- Ambient Blue Radiant Glow -->
<div class="absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-primary-container/20 to-transparent pointer-events-none"></div>
<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin relative z-10">
<div class="rounded-3xl bg-gradient-to-r from-[#142F68] to-[#071C50] p-space-xl lg:p-space-2xl shadow-[0_24px_72px_rgba(0,0,0,0.6)]">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<div class="lg:col-span-8 flex flex-col items-start space-y-space-md">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#071C50] text-[#60A5FA] font-mono-data text-label-sm font-semibold">
<span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
              Instant Setup in Under 5 Minutes
            </div>
<h2 class="font-headline-xl text-headline-xl lg:text-display-lg font-bold tracking-tight text-[#FFFFFF] leading-tight">
              Turn Your Incoming Emails into Actionable Data
            </h2>
<p class="font-body-lg text-body-lg text-[#DCE9FF]/90 max-w-2xl leading-relaxed">
              Stop manually processing email and transferring information between different business systems. The Email Parser Service of ExtractMails helps automate email processing, simplify data handling, and keep your business workflows moving without delays.
            </p>
<div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md">
<a class="inline-flex items-center justify-center px-space-xl py-4 rounded-xl bg-primary-container text-on-primary font-headline-sm text-headline-sm font-semibold shadow-[0_0_36px_rgba(37,99,235,0.5)] hover:bg-[#3B82F6] transition-all transform hover:-translate-y-0.5" data-path="sign-up" href="#">
<span class="">Start Email Parsing Now</span>
<span class="material-symbols-outlined ml-2 text-[22px]">rocket_launch</span>
</a>
<span class="text-[#DCE9FF]/70 font-body-sm text-body-sm text-center sm:text-left">
                No contract required • Cancel anytime
              </span>
</div>
</div>
<!-- Decorative Floating Node Hub Graphic -->
<div class="lg:col-span-4 flex items-center justify-center mt-space-lg lg:mt-0">
<div class="w-48 h-48 lg:w-56 lg:h-56 rounded-full bg-gradient-to-tr from-primary-container/30 to-[#60A5FA]/30 flex items-center justify-center relative shadow-[0_0_60px_rgba(37,99,235,0.3)]">
<div class="w-36 h-36 lg:w-44 lg:h-44 rounded-full bg-[#071C50] flex flex-col items-center justify-center text-center p-4">
<span class="material-symbols-outlined text-[#60A5FA] text-[48px] animate-bounce">mark_email_read</span>
<span class="font-mono-data text-label-sm font-bold text-[#FFFFFF] mt-2">PARSER ACTIVE</span>
<span class="font-mono-data text-[11px] text-[#10B981]">0 errors reported</span>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- ========================================================================= -->
<!-- 10. FREQUENTLY ASKED QUESTIONS (White: #FFFFFF / #F6FAFD) -->
<!-- ========================================================================= -->
<section class="w-full bg-[#FFFFFF] text-[#01174B] py-space-3xl relative">
<div class="max-w-4xl mx-auto px-margin-mobile lg:px-margin">
<!-- Section Header -->
<div class="text-center mb-space-2xl">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-container text-primary font-mono-data text-label-sm font-semibold mb-space-xs">
<span class="material-symbols-outlined text-[16px]">help_outline</span>
          Answers &amp; Technical Clarifications
        </div>
<h2 class="font-headline-xl text-headline-xl font-bold tracking-tight text-[#01174B]">
          Frequently Asked Questions
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-xs max-w-xl mx-auto">
          Everything you need to know about setting up and automating your email data extraction pipelines.
        </p>
</div>
<!-- Centered Accordion List -->
<div class="space-y-space-md" id="faq-container">
<!-- FAQ 1 (Open by default) -->
<div class="rounded-xl bg-surface-container-low transition-all">
<button aria-expanded="true" class="faq-toggle w-full px-space-lg py-5 flex items-center justify-between text-left" type="button">
<span class="font-headline-sm text-headline-sm font-bold text-[#01174B]">
              What information can ExtractMails extract from incoming emails?
            </span>
<span class="faq-icon material-symbols-outlined text-primary transition-transform duration-200 rotate-180">
              expand_more
            </span>
</button>
<div class="faq-content px-space-lg pb-space-lg">
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              ExtractMails can extract virtually any business data present in incoming emails, including sender names, contact emails, phone numbers, company names, order IDs, booking references, pricing, dates, message summaries, custom parameters, and even structured data within tables and email attachments.
            </p>
</div>
</div>
<!-- FAQ 2 -->
<div class="rounded-xl bg-surface-container-low transition-all">
<button aria-expanded="false" class="faq-toggle w-full px-space-lg py-5 flex items-center justify-between text-left" type="button">
<span class="font-headline-sm text-headline-sm font-bold text-[#01174B]">
              Can I automate email data extraction with ExtractMails?
            </span>
<span class="faq-icon material-symbols-outlined text-primary transition-transform duration-200">
              expand_more
            </span>
</button>
<div class="faq-content hidden px-space-lg pb-space-lg">
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Yes, ExtractMails is built specifically for end-to-end automation. Once you set up your parsing rules, any email forwarded or delivered to your parsing address is processed automatically in real time without human intervention.
            </p>
</div>
</div>
<!-- FAQ 3 -->
<div class="rounded-xl bg-surface-container-low transition-all">
<button aria-expanded="false" class="faq-toggle w-full px-space-lg py-5 flex items-center justify-between text-left" type="button">
<span class="font-headline-sm text-headline-sm font-bold text-[#01174B]">
              Does ExtractMails support webhook integration?
            </span>
<span class="faq-icon material-symbols-outlined text-primary transition-transform duration-200">
              expand_more
            </span>
</button>
<div class="faq-content hidden px-space-lg pb-space-lg">
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Absolutely. ExtractMails supports standard HTTPS webhooks, allowing you to instantly broadcast JSON payloads of extracted data to any endpoint, custom backend, Zapier, Make, or internal API immediately upon parsing.
            </p>
</div>
</div>
<!-- FAQ 4 -->
<div class="rounded-xl bg-surface-container-low transition-all">
<button aria-expanded="false" class="faq-toggle w-full px-space-lg py-5 flex items-center justify-between text-left" type="button">
<span class="font-headline-sm text-headline-sm font-bold text-[#01174B]">
              Can I create automated workflows based on incoming emails?
            </span>
<span class="faq-icon material-symbols-outlined text-primary transition-transform duration-200">
              expand_more
            </span>
</button>
<div class="faq-content hidden px-space-lg pb-space-lg">
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
              Yes. You can define rule-based conditions and logic (such as filtering by subject, sender domain, or parsed values) to automatically trigger downstream workflows, update CRM records, send alerts, or route tasks to specific team queues.
            </p>
</div>
</div>
</div>
</div>
</section>
</div>
</main>
</div>

<!-- Accessible Interactive FAQ Accordion Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggles = document.querySelectorAll('.email-parser-v2 .faq-toggle');
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
