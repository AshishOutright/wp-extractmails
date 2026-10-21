<?php
/**
 * Template Name: Email Automation Landing Page
 * Template Post Type: page
 * 
 * ExtractMails — Deliverability Intelligence & Lead Extraction Platform
 * High-Velocity Email Automation, Structured Data Routing & Workflow Ingestion
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
        <title>ExtractMails — Deliverability Intelligence &amp; Lead Extraction Platform</title>
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
tailwind.config = { "darkMode": "class", "theme": { "extend": { "colors": { "inverse-on-surface": "#edf0ff", "surface-container-low": "#f1f3ff", "on-tertiary-container": "#abc6ff", "outline-variant": "#c3c6d6", "on-tertiary": "#ffffff", "secondary": "#4c5c92", "primary-fixed": "#dbe1ff", "on-primary-fixed": "#00174b", "on-surface-variant": "#434654", "secondary-fixed": "#dbe1ff", "on-secondary-fixed-variant": "#334479", "surface-variant": "#d9e2ff", "outline": "#737685", "primary-fixed-dim": "#b4c5ff", "background": "#faf9ff", "surface-bright": "#faf9ff", "tertiary-fixed-dim": "#abc7ff", "on-background": "#0b1b38", "on-tertiary-fixed-variant": "#004590", "error": "#ba1a1a", "inverse-surface": "#22304f", "on-primary": "#ffffff", "error-container": "#ffdad6", "surface-container": "#e9edff", "on-secondary": "#ffffff", "on-primary-fixed-variant": "#003ea8", "tertiary-fixed": "#d7e2ff", "secondary-fixed-dim": "#b5c4ff", "surface-tint": "#1e55ce", "tertiary": "#003a7b", "on-surface": "#0b1b38", "surface-dim": "#ccdaff", "primary": "#003490", "primary-container": "#0048c2", "on-tertiary-fixed": "#001b3f", "surface-container-lowest": "#ffffff", "secondary-container": "#b1c2ff", "surface": "#faf9ff", "on-primary-container": "#b4c4ff", "tertiary-container": "#0050a6", "on-secondary-container": "#3e4e84", "on-error": "#ffffff", "surface-container-high": "#e1e8ff", "on-error-container": "#93000a", "on-secondary-fixed": "#01174b", "surface-container-highest": "#d9e2ff", "inverse-primary": "#b4c5ff" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "gutter-sm": "1rem", "gutter": "1.5rem", "space-lg": "1.5rem", "margin-xl": "7.5rem", "space-sm": "0.5rem", "space-2xl": "4rem", "margin-md": "3rem", "margin-lg": "5rem", "gutter-lg": "2rem", "space-md": "1rem", "space-xs": "0.25rem", "margin": "1.5rem", "space-xl": "2.5rem" }, "fontFamily": { "headline-lg": ["DM Sans"], "display-lg": ["DM Sans"], "label-sm": ["DM Sans"], "display-xl": ["DM Sans"], "headline-md": ["DM Sans"], "display-lg-mobile": ["DM Sans"], "headline-lg-mobile": ["DM Sans"], "body-xl": ["DM Sans"], "display-xl-mobile": ["DM Sans"], "headline-sm": ["DM Sans"], "body-md": ["DM Sans"], "code-sm": ["DM Sans"], "body-lg": ["DM Sans"], "label-md": ["DM Sans"] }, "fontSize": { "headline-lg": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "600" }], "display-lg": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.025em", "fontWeight": "700" }], "label-sm": ["11px", { "lineHeight": "14px", "letterSpacing": "0.06em", "fontWeight": "700" }], "display-xl": ["64px", { "lineHeight": "72px", "letterSpacing": "-0.03em", "fontWeight": "700" }], "headline-md": ["28px", { "lineHeight": "36px", "letterSpacing": "-0.015em", "fontWeight": "600" }], "display-lg-mobile": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }], "headline-lg-mobile": ["26px", { "lineHeight": "34px", "letterSpacing": "-0.015em", "fontWeight": "600" }], "body-xl": ["18px", { "lineHeight": "28px", "letterSpacing": "-0.005em", "fontWeight": "400" }], "display-xl-mobile": ["40px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" }], "headline-sm": ["22px", { "lineHeight": "30px", "letterSpacing": "-0.01em", "fontWeight": "600" }], "body-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0em", "fontWeight": "400" }], "code-sm": ["12px", { "lineHeight": "16px", "letterSpacing": "0em", "fontWeight": "500" }], "body-lg": ["16px", { "lineHeight": "24px", "letterSpacing": "0em", "fontWeight": "400" }], "label-md": ["13px", { "lineHeight": "18px", "letterSpacing": "0.01em", "fontWeight": "600" }] } } } };
</script>

<style>
  /* ========================================================= */
  /* 1. CRITICAL: MATERIAL SYMBOLS OUTLINED ICON DEFINITION   */
  /* ========================================================= */
  .email-automation-page .material-symbols-outlined {
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
    -webkit-font-feature-settings: 'liga' 1 !important;
    font-feature-settings: 'liga' 1 !important;
    -webkit-font-smoothing: antialiased;
    vertical-align: middle;
  }

  /* ========================================================= */
  /* 2. TYPOGRAPHY ENFORCEMENT AGAINST ASTRA SERIF OVERRIDES   */
  /* ========================================================= */
  .email-automation-page,
  .email-automation-page h1,
  .email-automation-page h2,
  .email-automation-page h3,
  .email-automation-page h4,
  .email-automation-page h5,
  .email-automation-page h6,
  .email-automation-page p,
  .email-automation-page a,
  .email-automation-page button,
  .email-automation-page input,
  .email-automation-page select,
  .email-automation-page textarea,
  .email-automation-page label,
  .email-automation-page span:not(.material-symbols-outlined) {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
  }

  .email-automation-page .font-code-sm,
  .email-automation-page code,
  .email-automation-page pre {
    font-family: 'JetBrains Mono', monospace !important;
  }

  /* ========================================================= */
  /* 3. ASTRA FIXED HEADER & WP ADMIN-BAR CLEARANCE           */
  /* ========================================================= */
  /* Navy hero starts at the top of the page, behind the fixed floating header */
  body:has(.email-automation-page) #content,
  body:not(.home) .email-automation-page main {
    padding-top: 0 !important;
    margin-top: 0 !important;
  }

  .email-automation-page .hero-clearance {
    padding-top: 120px !important;
  }

  @media (max-width: 768px) {
    .email-automation-page .hero-clearance {
      padding-top: 100px !important;
    }
  }

  body.admin-bar .email-automation-page .hero-clearance {
    padding-top: 152px !important;
  }

  @media (max-width: 768px) {
    body.admin-bar .email-automation-page .hero-clearance {
      padding-top: 136px !important;
    }
  }

  /* Scoped layout & container resets */
  .email-automation-page {
    color: #0b1b38;
    background-color: #faf9ff;
    width: 100%;
    overflow-x: hidden;
  }

  .email-automation-page main {
    margin-top: 0 !important;
    padding-top: 0 !important;
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .email-automation-page ::-webkit-scrollbar {
    display: none;
  }
</style>

<div class="email-automation-page bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen">
<main class="w-full bg-surface">
            <div class="flex flex-col w-full">
                <!-- SECTION 1 — HERO: EMAILS BECOME BUSINESS ACTIONS -->
                <section
                    class="relative w-full overflow-hidden bg-[#071C50] text-[#FFFFFF] hero-clearance pb-24 md:pb-28">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <!-- Ambient glow gradients -->
                    <div
                        class="pointer-events-none absolute -top-40 -left-40 w-96 h-96 bg-[#05289B] opacity-50 blur-[120px] rounded-full">
                    </div>
                    <div
                        class="pointer-events-none absolute top-1/2 -right-20 w-[420px] h-[420px] bg-[#0A7CF8] opacity-25 blur-[140px] rounded-full">
                    </div>
                    <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
                        <!-- Left Column -->
                        <div class="lg:col-span-6 flex flex-col items-start gap-space-md">
                            <div
                                class="inline-flex items-center gap-space-xs px-3.5 py-1.5 rounded-full bg-[#05289B]/50 border border-[#6CB5FF]/30 backdrop-blur-md">
                                <span class="w-2 h-2 rounded-full bg-[#6CB5FF] animate-pulse"></span>
                                <span class="font-label-sm text-label-sm uppercase tracking-widest text-[#6CB5FF]">Email
                                    Automation</span>
                            </div>
                            <h1
                                class="font-display-lg lg:font-display-xl text-display-lg lg:text-display-xl text-[#FFFFFF] tracking-tight">
                                Turn Incoming Emails into Automated Business Actions
                            </h1>
                            <p class="font-body-xl text-body-xl text-[#CDDAF4] max-w-xl">
                                Automatically process incoming emails, extract important information, and send relevant
                                data to the CRM, webhook, or connected business systems.
                            </p>
                            <div
                                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md pt-space-sm w-full sm:w-auto">
                                <a class="h-12 px-8 bg-primary-container hover:bg-[#05289B] text-on-primary font-label-md text-label-md rounded-xl flex items-center justify-center gap-2 transition-all shadow-[0_10px_28px_rgba(0,72,194,0.45)]" href="<?php echo esc_url(home_url('/pricing/')); ?>"><span>Get Started</span>
                                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                </a>
                                <span
                                    class="font-code-sm text-code-sm text-[#CDDAF4]/80 flex items-center gap-2 self-center">
                                    <span class="material-symbols-outlined text-[#6CB5FF] text-[18px]">verified</span>
                                    Enterprise-grade automation • No coding required
                                </span>
                            </div>
                        </div>
                        <!-- Right Column: Visual Sculpture + Interactive Workflow Card -->
                        <div class="lg:col-span-6 relative flex flex-col gap-space-md">
                            <div
                                class="relative w-full rounded-2xl overflow-hidden border border-[#6CB5FF]/20 shadow-2xl bg-[#05289B]/20 backdrop-blur-md">
                                <img class="w-full h-[320px] sm:h-[380px] object-cover"
                                    data-alt="Futuristic 3D mechanical rule-gate router diagram on light background with blue neon glowing light data cables, translucent cyber letter envelope feeding into a central ceramic circular aperture that forks out data records and webhook payloads, precise technical glassmorphism aesthetic."
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuD3FZuf3osfDRAHWj-ucktG5wa_2FN_mfqHkVJ-h7p1G3sPOkdWl8cQaaVMMwz47bW9lXbIGPFPU1RPbGi31UdieoJe3HviQjJ9PJlGDoJOUyA541SYBbgSj_sPpD0JQCgiLdXDXQuhsjQvBxNRAVGvSKXsOzXBpM-IdUK2AJvF9Br0MY6fXrAYKuarq_lT5P2U2JLmwt5pnOWCvoHJD0PgAj61TejAdMIDb3-QQt8BrE3vmBDD6r-NHQ" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-[#071C50] via-transparent to-transparent opacity-60">
                                </div>
                            </div>
                            <!-- Glass card floating over/below image -->
                            <div
                                class="w-full rounded-xl bg-[#071C50]/85 border border-[#6CB5FF]/25 backdrop-blur-xl p-space-md shadow-xl">
                                <div
                                    class="flex items-center justify-between pb-space-xs border-b border-[#6CB5FF]/15 mb-space-sm">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="material-symbols-outlined text-[#6CB5FF] text-[18px]">account_tree</span>
                                        <span
                                            class="font-label-sm text-label-sm uppercase tracking-wider text-[#CDDAF4]">Example
                                            workflow</span>
                                    </div>
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-[#0A7CF8]/20 text-[#6CB5FF] font-code-sm text-code-sm border border-[#0A7CF8]/30">Live
                                        Filter</span>
                                </div>
                                <div class="space-y-space-xs">
                                    <div
                                        class="flex items-center justify-between p-2.5 rounded-lg bg-[#05289B]/40 border border-[#6CB5FF]/15">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="material-symbols-outlined text-[#6CB5FF] text-[18px]">mail</span>
                                            <span class="font-body-md text-body-md text-[#FFFFFF] font-medium">New
                                                Customer Demo Request - acme.corp</span>
                                        </div>
                                        <span class="font-code-sm text-code-sm text-[#CDDAF4]">0.14s</span>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#0A7CF8]/10 text-[#6CB5FF] font-code-sm text-code-sm border border-[#0A7CF8]/20">
                                        <span class="material-symbols-outlined text-[16px]">tune</span>
                                        <span>Condition: Body contains 'Demo'</span>
                                    </div>
                                    <div
                                        class="flex items-center justify-between p-2.5 rounded-lg bg-surface-container-lowest/10 border border-[#6CB5FF]/20 text-[#FFFFFF]">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="material-symbols-outlined text-primary-fixed text-[18px]">sync_alt</span>
                                            <span
                                                class="font-body-md text-body-md font-medium text-primary-fixed">Handoff
                                                → CRM Record Created &amp; Webhook Dispatched</span>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-emerald-400 text-[18px]">check_circle</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 2 — MANUAL EMAIL MANAGEMENT CHALLENGES -->
                <section
                    class="relative w-full bg-[#FFFFFF] py-24 md:py-28 border-b border-[#D5EAFD]">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mx-auto text-center flex flex-col items-center gap-space-xs mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-primary font-label-sm text-label-sm uppercase tracking-wider">
                            Operational Bottlenecks
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            The Challenges of Managing Business Emails Manually
                        </h2>
                        <p class="font-body-lg text-body-lg text-[#52627B]">
                            Businesses usually receive leads, customer enquiries, booking confirmations, order
                            notifications, and operational updates through emails. Manually reviewing messages,
                            identifying important information, and transferring data to other systems can slow down
                            business processes significantly and create unnecessary administrative work.
                        </p>
                    </div>
                    <!-- 7 Challenges Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
                        <!-- 1 -->
                        <div
                            class="flex flex-col gap-space-xs p-space-lg rounded-2xl bg-[#F6FAFD]/80 border border-[#D5EAFD] hover:border-primary-container/40 transition-all hover:shadow-md group">
                            <div
                                class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-[#FFFFFF] transition-colors">
                                <span class="material-symbols-outlined text-[22px]">timer_off</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-[#12213F] pt-space-xs">Time-Consuming
                                Email Management</h3>
                            <p class="font-body-md text-body-md text-[#52627B]">Employees spend valuable time opening
                                and reviewing individual emails to find relevant information.</p>
                        </div>
                        <!-- 2 -->
                        <div
                            class="flex flex-col gap-space-xs p-space-lg rounded-2xl bg-[#F6FAFD]/80 border border-[#D5EAFD] hover:border-primary-container/40 transition-all hover:shadow-md group">
                            <div
                                class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-[#FFFFFF] transition-colors">
                                <span class="material-symbols-outlined text-[22px]">content_copy</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-[#12213F] pt-space-xs">Repetitive Manual
                                Tasks</h3>
                            <p class="font-body-md text-body-md text-[#52627B]">Copying details from emails into
                                spreadsheets, CRMs, and other business applications consumes time.</p>
                        </div>
                        <!-- 3 -->
                        <div
                            class="flex flex-col gap-space-xs p-space-lg rounded-2xl bg-[#F6FAFD]/80 border border-[#D5EAFD] hover:border-primary-container/40 transition-all hover:shadow-md group">
                            <div
                                class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-[#FFFFFF] transition-colors">
                                <span class="material-symbols-outlined text-[22px]">schedule</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-[#12213F] pt-space-xs">Delayed Lead
                                Follow-Ups</h3>
                            <p class="font-body-md text-body-md text-[#52627B]">Manually processing incoming enquiries
                                can delay the transfer of lead information to sales teams.</p>
                        </div>
                        <!-- 4 -->
                        <div
                            class="flex flex-col gap-space-xs p-space-lg rounded-2xl bg-[#F6FAFD]/80 border border-[#D5EAFD] hover:border-primary-container/40 transition-all hover:shadow-md group">
                            <div
                                class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-[#FFFFFF] transition-colors">
                                <span class="material-symbols-outlined text-[22px]">rule</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-[#12213F] pt-space-xs">Inconsistent Data
                                Handling</h3>
                            <p class="font-body-md text-body-md text-[#52627B]">Manual data entry can introduce errors,
                                missing information, and inconsistent business records.</p>
                        </div>
                        <!-- 5 -->
                        <div
                            class="flex flex-col gap-space-xs p-space-lg rounded-2xl bg-[#F6FAFD]/80 border border-[#D5EAFD] hover:border-primary-container/40 transition-all hover:shadow-md group">
                            <div
                                class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-[#FFFFFF] transition-colors">
                                <span class="material-symbols-outlined text-[22px]">link_off</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-[#12213F] pt-space-xs">Disconnected
                                Business Applications</h3>
                            <p class="font-body-md text-body-md text-[#52627B]">Important information can remain in
                                email inboxes instead of reaching out the systems that need it.</p>
                        </div>
                        <!-- 6 -->
                        <div
                            class="flex flex-col gap-space-xs p-space-lg rounded-2xl bg-[#F6FAFD]/80 border border-[#D5EAFD] hover:border-primary-container/40 transition-all hover:shadow-md group">
                            <div
                                class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-[#FFFFFF] transition-colors">
                                <span class="material-symbols-outlined text-[22px]">hourglass_empty</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-[#12213F] pt-space-xs">Slow Operational
                                Workflows</h3>
                            <p class="font-body-md text-body-md text-[#52627B]">Teams may need to process order
                                notifications, booking confirmations, and customer requests individually.</p>
                        </div>
                        <!-- 7 (Spans full width on desktop or balanced 3-col last card) -->
                        <div
                            class="md:col-span-2 lg:col-span-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-space-md p-space-lg rounded-2xl bg-gradient-to-r from-[#F6FAFD] via-surface-container to-[#F6FAFD] border border-[#D5EAFD] hover:border-primary-container/40 transition-all hover:shadow-md">
                            <div class="flex items-center gap-space-md">
                                <div
                                    class="w-12 h-12 rounded-xl bg-primary-container text-[#FFFFFF] flex items-center justify-center shadow-[0_4px_16px_rgba(0,72,194,0.3)]">
                                    <span class="material-symbols-outlined text-[24px]">trending_down</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Limited Process
                                        Efficiency</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-0.5">Repetitive email-related
                                        tasks prevent employees from focusing on customer service and other higher-value
                                        activities.</p>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md border border-[#D5EAFD] whitespace-nowrap">High
                                Operational Friction</span>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 3 — SIMPLIFY EMAIL AUTOMATION -->
                <section
                    class="relative w-full bg-[#F6FAFD] py-24 md:py-28 border-b border-[#D5EAFD]">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm text-label-sm uppercase tracking-wider mb-space-xs">
                            Streamlined Integration
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            Simplify Email Automation with ExtractMails
                        </h2>
                        <p class="font-body-lg text-body-lg text-[#52627B] mt-space-xs">
                            Email Automation helps organizations reduce repetitive tasks by connecting incoming messages
                            with data extraction, connected applications, and predefined workflow actions. Email
                            Automation Services of ExtractMails help streamline email-based processes and reduce the
                            manual effort that is involved in:
                        </p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
                        <!-- Left side image -->
                        <div class="lg:col-span-5 relative">
                            <div
                                class="rounded-3xl overflow-hidden shadow-md border border-[#D5EAFD] bg-[#FFFFFF] sticky top-24">
                                <img class="w-full h-[480px] object-cover"
                                    data-alt="High quality corporate business operations team and analysts collaborating over incoming automated lead pipelines on a modern laptop screen in a bright, sunlit glass-walled tech office in downtown, photorealistic natural lighting, corporate modern."
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYHlpuUagebGwG4vEca0jgMkRbr4VMJ2EEP7sozxhbehJLks2G22fYIAKM5SpqJxexoStL2ltY7h8UuCgG6y-xgmvkjkoYvI0fDafW9w3iZqqG9-PmDV5tUfX27fMsPLvSctF30_qEKFUU9DzczYRdAjpjO-l5QXJLzL607HJpBcunyJcLou-NY6Xtku_zegXbALaBis4617jexiMEnAO2c8gUXE4SVNfOLiRpGGpIWqLxR3-IPuOiWQ" />
                                <div
                                    class="p-space-md bg-[#FFFFFF] border-t border-[#D5EAFD] flex items-center justify-between">
                                    <div>
                                        <span class="font-label-md text-label-md text-[#12213F] block">Operations
                                            Intelligence Unit</span>
                                        <span class="font-code-sm text-code-sm text-[#52627B]">Real-time queue
                                            verification</span>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-label-sm text-label-sm">Active
                                        Sync</span>
                                </div>
                            </div>
                        </div>
                        <!-- Right side: 6 clear action rows -->
                        <div class="lg:col-span-7 flex flex-col gap-space-sm">
                            <!-- 1 -->
                            <div
                                class="flex items-start gap-space-md p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-[#0A7CF8]/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">mark_email_read</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Reviewing Incoming
                                        Emails</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Reduce the need to inspect
                                        every message manually to identify useful business information.</p>
                                </div>
                            </div>
                            <!-- 2 -->
                            <div
                                class="flex items-start gap-space-md p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-[#0A7CF8]/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">data_object</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Extracting Important
                                        Details</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Capture relevant customer
                                        names, contact information, lead enquiries, and order-related information.</p>
                                </div>
                            </div>
                            <!-- 3 -->
                            <div
                                class="flex items-start gap-space-md p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-[#0A7CF8]/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">move_to_inbox</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Transferring Email Data
                                    </h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Reduce repetitive copying
                                        and pasting between email messages, spreadsheets, CRMs, and other supported
                                        systems.</p>
                                </div>
                            </div>
                            <!-- 4 -->
                            <div
                                class="flex items-start gap-space-md p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-[#0A7CF8]/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">cloud_sync</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Updating Business
                                        Records</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Streamline the transfer of
                                        relevant information into connected business applications.</p>
                                </div>
                            </div>
                            <!-- 5 -->
                            <div
                                class="flex items-start gap-space-md p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-[#0A7CF8]/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">webhook</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Routing Information to
                                        Webhooks</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Send parsed email data to
                                        designated webhook endpoints for further processing.</p>
                                </div>
                            </div>
                            <!-- 6 -->
                            <div
                                class="flex items-start gap-space-md p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-[#0A7CF8]/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">bolt</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Triggering Workflow
                                        Actions</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Configure business
                                        workflows that run when incoming emails match specified rules and conditions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 4 — FROM EMAIL TO AUTOMATED ACTION -->
                <section
                    class="relative w-full bg-[#FFFFFF] py-24 md:py-28 border-b border-[#D5EAFD]">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm text-label-sm uppercase tracking-wider mb-space-xs">
                            System Architecture
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            From Incoming Emails to Automated Business Actions
                        </h2>
                        <p class="font-body-lg text-body-lg text-[#52627B] mt-space-xs">
                            Email Automation Service of ExtractMails helps businesses connect incoming emails with
                            structured data extraction and automated business actions through configured workflows.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">
                        <!-- Left Column: Steps 01 to 05 -->
                        <div class="lg:col-span-6 flex flex-col gap-space-lg relative">
                            <div
                                class="absolute left-5 top-6 bottom-6 w-0.5 bg-gradient-to-b from-[#0048C2] via-[#0A7CF8] to-[#D5EAFD]">
                            </div>
                            <!-- 01 -->
                            <div class="relative flex items-start gap-space-md pl-2 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-primary-container text-on-primary font-headline-sm text-headline-sm flex items-center justify-center shrink-0 z-10 shadow-md">
                                    01
                                </div>
                                <div class="pt-1">
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Incoming Emails are
                                        Received</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">The solution brings
                                        incoming email messages into the workflow through a supported email setup.</p>
                                </div>
                            </div>
                            <!-- 02 -->
                            <div class="relative flex items-start gap-space-md pl-2 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-primary-container text-on-primary font-headline-sm text-headline-sm flex items-center justify-center shrink-0 z-10 shadow-md">
                                    02
                                </div>
                                <div class="pt-1">
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Parse Email Content
                                    </h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">It identifies relevant
                                        information from incoming messages and passes it into the workflow through the
                                        supported email setup.</p>
                                </div>
                            </div>
                            <!-- 03 -->
                            <div class="relative flex items-start gap-space-md pl-2 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-primary-container text-on-primary font-headline-sm text-headline-sm flex items-center justify-center shrink-0 z-10 shadow-md">
                                    03
                                </div>
                                <div class="pt-1">
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Extract and Structure
                                        Data</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Automation organizes
                                        relevant information into structured fields that can be transferred to connected
                                        business systems.</p>
                                </div>
                            </div>
                            <!-- 04 -->
                            <div class="relative flex items-start gap-space-md pl-2 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-primary-container text-on-primary font-headline-sm text-headline-sm flex items-center justify-center shrink-0 z-10 shadow-md">
                                    04
                                </div>
                                <div class="pt-1">
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Send Information to
                                        Connected Systems</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Delivers all the parsed
                                        email data to a designated webhook, CRM, or other supported business application
                                        for further processing.</p>
                                </div>
                            </div>
                            <!-- 05 -->
                            <div class="relative flex items-start gap-space-md pl-2 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-[#0A7CF8] text-[#FFFFFF] font-headline-sm text-headline-sm flex items-center justify-center shrink-0 z-10 shadow-md">
                                    05
                                </div>
                                <div class="pt-1">
                                    <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Trigger Automated
                                        Workflow Actions</h3>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Configure rules and
                                        conditions to initiate subsequent actions when incoming emails meet the
                                        specified criteria, helping reduce repetitive manual work.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Right Column: Interactive Mock UI Pipeline Preview -->
                        <div
                            class="lg:col-span-6 bg-[#071C50] text-[#FFFFFF] rounded-2xl p-space-lg border border-[#6CB5FF]/20 shadow-xl font-code-sm">
                            <div
                                class="flex items-center justify-between pb-space-sm border-b border-[#6CB5FF]/15 mb-space-md">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                                    <span class="font-label-md text-label-md text-[#FFFFFF]">Active Parser</span>
                                </div>
                                <span
                                    class="font-code-sm text-code-sm text-[#CDDAF4]">inbound@extractmails-pipeline.io</span>
                            </div>
                            <!-- Box 1 -->
                            <div class="p-3.5 rounded-xl bg-[#05289B]/50 border border-[#6CB5FF]/20 mb-space-sm">
                                <div
                                    class="flex items-center justify-between text-[#6CB5FF] text-[11px] uppercase tracking-wider mb-1">
                                    <span>Incoming Email</span>
                                    <span>Just now</span>
                                </div>
                                <p class="text-[#FFFFFF] font-semibold text-body-md font-body-md">New Enterprise Inbound
                                    Inquiry</p>
                                <p class="text-[#CDDAF4] text-xs">From: sarah.j@acmetech.io</p>
                            </div>
                            <!-- Arrow connector -->
                            <div class="flex justify-center py-1">
                                <span class="material-symbols-outlined text-[#6CB5FF]">south</span>
                            </div>
                            <!-- Box 2 JSON -->
                            <div
                                class="p-3.5 rounded-xl bg-[#031131] border border-[#6CB5FF]/20 mb-space-sm font-mono text-[12px] leading-relaxed text-[#CDDAF4]">
                                <div class="text-[#6CB5FF] text-[11px] uppercase tracking-wider mb-1 font-sans">
                                    Structured JSON Output</div>
                                <div>{</div>
                                <div class="pl-4"><span class="text-secondary-fixed">"customer_name"</span>: <span
                                        class="text-emerald-300">"Sarah Jenkins"</span>,</div>
                                <div class="pl-4"><span class="text-secondary-fixed">"work_email"</span>: <span
                                        class="text-emerald-300">"sarah.j@acmetech.io"</span>,</div>
                                <div class="pl-4"><span class="text-secondary-fixed">"seats_requested"</span>: <span
                                        class="text-amber-300">"250"</span>,</div>
                                <div class="pl-4"><span class="text-secondary-fixed">"enquiry_type"</span>: <span
                                        class="text-emerald-300">"Enterprise Tier"</span></div>
                                <div>}</div>
                            </div>
                            <!-- Arrow connector -->
                            <div class="flex justify-center py-1">
                                <span class="material-symbols-outlined text-[#6CB5FF]">south</span>
                            </div>
                            <!-- Box 3 -->
                            <div class="p-3 rounded-xl bg-[#0A7CF8]/15 border border-[#0A7CF8]/30 mb-space-sm">
                                <div class="text-[#6CB5FF] text-[11px] uppercase tracking-wider mb-1">Configured
                                    Condition</div>
                                <p class="text-[#FFFFFF] font-medium text-xs font-mono">IF enquiry_type == 'Enterprise
                                    Tier'</p>
                            </div>
                            <!-- Box 4 -->
                            <div
                                class="p-3.5 rounded-xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-200 text-xs flex flex-col gap-1.5">
                                <div
                                    class="text-emerald-400 text-[11px] uppercase tracking-wider font-sans font-semibold">
                                    Automated Actions</div>
                                <div class="flex items-center gap-1.5 font-medium">
                                    <span
                                        class="material-symbols-outlined text-emerald-400 text-[16px]">check_circle</span>
                                    <span>Create Hubspot Contact &amp; Deal ($48,000)</span>
                                </div>
                                <div class="flex items-center gap-1.5 font-medium">
                                    <span
                                        class="material-symbols-outlined text-emerald-400 text-[16px]">check_circle</span>
                                    <span>Send Webhook to Slack #sales-leads</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 5 — AUTOMATED WORKFLOW FEATURES -->
                <section
                    class="relative w-full bg-[#F0F6FD] py-24 md:py-28 border-b border-[#D5EAFD]">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mx-auto text-center flex flex-col items-center gap-space-xs mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm text-label-sm uppercase tracking-wider">
                            Full Suite Capabilities
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            Essential Features for Automated Email Workflows
                        </h2>
                        <p class="font-body-lg text-body-lg text-[#52627B]">
                            Email Automation Service of ExtractMails combines email parsing, structured data extraction,
                            system integration, and rule-based workflows to enable businesses to simplify email
                            processing and connect incoming messages with business operations.
                        </p>
                    </div>
                    <!-- 6 Feature Cards in 3x2 Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
                        <!-- 1 -->
                        <div
                            class="p-space-lg rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div
                                    class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-sm">
                                    <span class="material-symbols-outlined text-[26px]">forward_to_inbox</span>
                                </div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F] mb-2">Automated Email
                                    Parsing</h3>
                                <p class="font-body-md text-body-md text-[#52627B]">The solution processes incoming
                                    emails to identify information required for business workflows.</p>
                            </div>
                            <div
                                class="pt-space-md border-t border-[#D5EAFD]/60 mt-space-md flex items-center text-primary font-label-md text-label-md gap-1">
                                <span>Real-time ingestion</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </div>
                        </div>
                        <!-- 2 -->
                        <div
                            class="p-space-lg rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div
                                    class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-sm">
                                    <span class="material-symbols-outlined text-[26px]">view_quilt</span>
                                </div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F] mb-2">Structured Email Data
                                    Extraction</h3>
                                <p class="font-body-md text-body-md text-[#52627B]">Easily capture relevant details,
                                    such as names, contact information, lead enquiries, customer requests, and order
                                    information.</p>
                            </div>
                            <div
                                class="pt-space-md border-t border-[#D5EAFD]/60 mt-space-md flex items-center text-primary font-label-md text-label-md gap-1">
                                <span>Schema-free mapping</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </div>
                        </div>
                        <!-- 3 -->
                        <div
                            class="p-space-lg rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div
                                    class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-sm">
                                    <span class="material-symbols-outlined text-[26px]">hub</span>
                                </div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F] mb-2">Business System
                                    Integration</h3>
                                <p class="font-body-md text-body-md text-[#52627B]">Send parsed email data directly to a
                                    designated webhook, CRM platform, and other connected business systems.</p>
                            </div>
                            <div
                                class="pt-space-md border-t border-[#D5EAFD]/60 mt-space-md flex items-center text-primary font-label-md text-label-md gap-1">
                                <span>REST &amp; Webhook ready</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </div>
                        </div>
                        <!-- 4 -->
                        <div
                            class="p-space-lg rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div
                                    class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-sm">
                                    <span class="material-symbols-outlined text-[26px]">rule_settings</span>
                                </div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F] mb-2">Rule-Based Workflow
                                    Automation</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mb-space-sm">Configure automated
                                    actions that run when incoming emails match specified conditions.</p>
                                <div
                                    class="px-2.5 py-1.5 rounded-lg bg-[#F6FAFD] border border-[#D5EAFD] text-[#0048C2] font-code-sm text-code-sm">
                                    IF subject == 'Urgent' THEN POST /webhook
                                </div>
                            </div>
                            <div
                                class="pt-space-md border-t border-[#D5EAFD]/60 mt-space-md flex items-center text-primary font-label-md text-label-md gap-1">
                                <span>Conditional logic</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </div>
                        </div>
                        <!-- 5 -->
                        <div
                            class="p-space-lg rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div
                                    class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-sm">
                                    <span class="material-symbols-outlined text-[26px]">person_search</span>
                                </div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F] mb-2">Automated Lead Capture
                                </h3>
                                <p class="font-body-md text-body-md text-[#52627B]">Extract lead details from incoming
                                    enquiry emails and pass structured information to sales systems.</p>
                            </div>
                            <div
                                class="pt-space-md border-t border-[#D5EAFD]/60 mt-space-md flex items-center text-primary font-label-md text-label-md gap-1">
                                <span>Zero lead latency</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </div>
                        </div>
                        <!-- 6 -->
                        <div
                            class="p-space-lg rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div
                                    class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-primary mb-space-sm">
                                    <span class="material-symbols-outlined text-[26px]">speed</span>
                                </div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F] mb-2">Streamlined Email
                                    Operations</h3>
                                <p class="font-body-md text-body-md text-[#52627B]">Connect incoming messages with data
                                    extraction and downstream business actions to simplify repetitive tasks.</p>
                            </div>
                            <div
                                class="pt-space-md border-t border-[#D5EAFD]/60 mt-space-md flex items-center text-primary font-label-md text-label-md gap-1">
                                <span>Continuous throughput</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 6 — PRACTICAL APPLICATIONS -->
                <section
                    class="relative w-full bg-[#FFFFFF] py-24 md:py-28 border-b border-[#D5EAFD]">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm text-label-sm uppercase tracking-wider mb-space-xs">
                            Use Case
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            Practical Applications of Email Workflow Automation
                        </h2>
                        <p class="font-body-lg text-body-lg text-[#52627B] mt-space-xs">
                            Businesses use email for most of their daily communication and for exchanging information
                            across sales, customer support, and daily operations. Email Automation Services of
                            ExtractMails help automate the processing of incoming messages and transfer relevant
                            information into the appropriate business workflows.
                        </p>
                    </div>
                    <!-- 2-column spacious editorial rows -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-space-xl gap-y-space-lg">
                        <!-- 1 -->
                        <div class="flex items-start gap-space-md pb-space-md border-b border-[#D5EAFD]">
                            <div
                                class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-[22px]">contact_mail</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Lead Enquiry Automation
                                </h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Extract contact details and
                                    enquiry information from incoming lead emails, then pass the data to a CRM or sales
                                    workflow.</p>
                            </div>
                        </div>
                        <!-- 2 -->
                        <div class="flex items-start gap-space-md pb-space-md border-b border-[#D5EAFD]">
                            <div
                                class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-[22px]">support_agent</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Customer Enquiry Processing
                                </h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Capture relevant details from
                                    customer messages and route the information to the appropriate business system.</p>
                            </div>
                        </div>
                        <!-- 3 -->
                        <div class="flex items-start gap-space-md pb-space-md border-b border-[#D5EAFD]">
                            <div
                                class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-[22px]">event_available</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Booking Confirmation
                                    Automation</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Collect relevant booking
                                    information from incoming confirmation emails and make it available to connected
                                    systems.</p>
                            </div>
                        </div>
                        <!-- 4 -->
                        <div class="flex items-start gap-space-md pb-space-md border-b border-[#D5EAFD]">
                            <div
                                class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Order Notification
                                    Processing</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Extract order-related details
                                    from incoming notifications and transfer them to downstream business processes.</p>
                            </div>
                        </div>
                        <!-- 5 -->
                        <div class="flex items-start gap-space-md pb-space-md border-b border-[#D5EAFD]">
                            <div
                                class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-[22px]">cable</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Webhook-Based Automation
                                </h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Send parsed email information
                                    to connected applications through designated webhook endpoints.</p>
                            </div>
                        </div>
                        <!-- 6 -->
                        <div class="flex items-start gap-space-md pb-space-md border-b border-[#D5EAFD]">
                            <div
                                class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-[22px]">lan</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Operational Workflow
                                    Automation</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Configure workflows that
                                    respond to incoming emails matching specified conditions, such as notifying a team
                                    or updating a record.</p>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 7 — TEAMS AND AUDIENCES -->
                <section
                    class="relative w-full bg-[#F6FAFD] py-24 md:py-28 border-b border-[#D5EAFD]">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm text-label-sm uppercase tracking-wider mb-space-xs">
                            Who Should Use an Email Parser?
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            Designed for Businesses That Process Emails Every Day
                        </h2>
                        <p class="font-body-lg text-body-lg text-[#52627B] mt-space-xs">
                            Email Automation Service of ExtractMails is designed for businesses and teams that receive
                            information through emails and need to reduce manual processing, improve data handling, and
                            streamline their workflows.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
                        <!-- Left Column: 6 Audience Panels -->
                        <div class="lg:col-span-7 flex flex-col gap-space-sm">
                            <!-- 1 -->
                            <div
                                class="p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-primary-container/40 transition-all">
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Sales and Lead Generation
                                    Teams</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Efficiently capture incoming
                                    lead details and transfer structured information to sales systems.</p>
                            </div>
                            <!-- 2 -->
                            <div
                                class="p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-primary-container/40 transition-all">
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Sales Operations Teams</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Reduce repetitive lead
                                    processing and streamline information flow into the CRM and business applications.
                                </p>
                            </div>
                            <!-- 3 -->
                            <div
                                class="p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-primary-container/40 transition-all">
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Customer Support Teams</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Extract all the relevant
                                    details from customer enquiries and transfer them into appropriate workflows.</p>
                            </div>
                            <!-- 4 -->
                            <div
                                class="p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-primary-container/40 transition-all">
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Operations Teams</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">The solution simplifies order
                                    notification processing, booking confirmations, and other operational emails.</p>
                            </div>
                            <!-- 5 -->
                            <div
                                class="p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-primary-container/40 transition-all">
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Developers and Automation
                                    Builders</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Connect email-based information
                                    with webhook endpoints, applications, and automated processes.</p>
                            </div>
                            <!-- 6 -->
                            <div
                                class="p-space-md rounded-2xl bg-[#FFFFFF] border border-[#D5EAFD] shadow-sm hover:border-primary-container/40 transition-all">
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Agencies and Growing
                                    Businesses</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Reduce repetitive email
                                    handling as enquiry volumes increase and business processes become more complex.</p>
                            </div>
                        </div>
                        <!-- Right Column: Sticky Photography -->
                        <div class="lg:col-span-5 sticky top-24">
                            <div class="rounded-3xl overflow-hidden shadow-md border border-[#D5EAFD] bg-[#FFFFFF]">
                                <img class="w-full h-[520px] object-cover"
                                    data-alt="A diverse professional team of three enterprise colleagues reviewing data automation workflows and business communication charts on an office desktop screen in a bright contemporary tech space, soft daylighting, high clarity."
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBLRxcO0lJJzUUrd9U0b_j0ISmNhX4Usm5Ihhouo8kfon3y0B3ZCwjB3qtr0Vs3CPDJ6uuUwiAtSe3CaTBaV2YQMPCX4mzbbli0Mxd15Ml7yBTEmIEMANpP0iCq7dy4QE6GRmeZI_mKXKXBPi7toeN1qy0TNKifzo6IQnc3xa4n7npfIU9-TsSUMvOEPd5qlEhe3BhOyt7L5o663lwkpT4CTV6K2BFniHpYJ41sWLkEfBuQ2yk8Y3YGLQ" />
                                <div class="p-space-md bg-[#FFFFFF] border-t border-[#D5EAFD]">
                                    <div class="flex items-center gap-2 text-primary font-label-md text-label-md">
                                        <span class="material-symbols-outlined text-[18px]">groups</span>
                                        <span>Collaborative Execution</span>
                                    </div>
                                    <p class="font-body-md text-body-md text-[#52627B] mt-1">Empowering cross-functional
                                        revenue and ops units with standardized email intake.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 8 — WHY CHOOSE EXTRACTMAILS -->
                <section
                    class="relative w-full bg-[#FFFFFF] py-24 md:py-28 border-b border-[#D5EAFD]">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm text-label-sm uppercase tracking-wider mb-space-xs">
                            Why choose ExtractMails
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            Simplify Business Email Processing with ExtractMails
                        </h2>
                        <p class="font-body-lg text-body-lg text-[#52627B] mt-space-xs">
                            Email Automation Services from ExtractMails enable businesses to move beyond manually
                            reviewing incoming emails and transferring information between applications. The solution
                            combines email parsing, structured data extraction, integrations, and rule-based workflows.
                            It also helps streamline email-based processes.
                        </p>
                    </div>
                    <!-- 6 Generously Spaced Editorial Rows with Check Markers -->
                    <div class="flex flex-col divide-y divide-[#D5EAFD]">
                        <!-- 1 -->
                        <div class="flex items-start gap-space-md py-space-md">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-container/10 text-primary flex items-center justify-center shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Automate Incoming Email
                                    Processing</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Reduce the need to review each
                                    email manually to identify relevant information.</p>
                            </div>
                        </div>
                        <!-- 2 -->
                        <div class="flex items-start gap-space-md py-space-md">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-container/10 text-primary flex items-center justify-center shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Extract Useful Business
                                    Information</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Capture details from incoming
                                    enquiries, contact information, lead messages, and order-related emails.</p>
                            </div>
                        </div>
                        <!-- 3 -->
                        <div class="flex items-start gap-space-md py-space-md">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-container/10 text-primary flex items-center justify-center shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Connect Email with Business
                                    Applications</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Send parsed information to
                                    support CRMs, webhooks, and other connected systems.</p>
                            </div>
                        </div>
                        <!-- 4 -->
                        <div class="flex items-start gap-space-md py-space-md">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-container/10 text-primary flex items-center justify-center shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Configure Rule-Based
                                    Actions</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Set up workflows that respond
                                    when incoming messages meet specified conditions.</p>
                            </div>
                        </div>
                        <!-- 5 -->
                        <div class="flex items-start gap-space-md py-space-md">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-container/10 text-primary flex items-center justify-center shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Reduce Repetitive
                                    Administrative Work</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Minimize manual copying, data
                                    transfer, and routine email-processing tasks.</p>
                            </div>
                        </div>
                        <!-- 6 -->
                        <div class="flex items-start gap-space-md py-space-md">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-container/10 text-primary flex items-center justify-center shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-[#12213F]">Support More Efficient
                                    Workflows</h3>
                                <p class="font-body-md text-body-md text-[#52627B] mt-1">Help teams spend less time
                                    handling email data and more time on business priorities.</p>
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 9 — FINAL CTA -->
                <section
                    class="relative w-full overflow-hidden bg-[#071C50] text-[#FFFFFF] py-24 md:py-28 text-center">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <!-- Radial blue glow behind card -->
                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                        <div class="w-[500px] h-[500px] bg-[#05289B] opacity-40 blur-[130px] rounded-full"></div>
                    </div>
                    <div class="relative max-w-3xl mx-auto flex flex-col items-center gap-space-md">
                        <div
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#05289B]/60 border border-[#6CB5FF]/30 font-label-sm text-label-sm text-[#6CB5FF] uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[16px]">rocket_launch</span>
                            Get Started Today
                        </div>
                        <h2
                            class="font-display-lg lg:font-display-xl text-display-lg lg:text-display-xl text-[#FFFFFF] tracking-tight">
                            Simplify Email Processing with ExtractMails
                        </h2>
                        <p class="font-body-xl text-body-xl text-[#CDDAF4] max-w-2xl">
                            Stop spending valuable time manually reviewing emails, copying information, and updating
                            business systems. Automate email processing, extract important information, and connect
                            incoming messages with the workflows your business depends on.
                        </p>
                        <div class="pt-space-sm">
                            <a class="h-14 px-9 bg-primary-container hover:bg-[#05289B] text-on-primary font-headline-sm text-headline-sm rounded-xl inline-flex items-center justify-center gap-3 transition-all shadow-[0_12px_32px_rgba(0,72,194,0.5)]" href="<?php echo esc_url(home_url('/pricing/')); ?>"><span>Start Email Automation Today</span>
                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
                </section>
                <!-- SECTION 10 — FAQ -->
                <section
                    class="relative w-full bg-[#FFFFFF] py-24 md:py-28">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="max-w-3xl mx-auto text-center mb-space-xl">
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container/10 text-primary font-label-sm text-label-sm uppercase tracking-wider mb-space-xs">
                            Common Questions
                        </div>
                        <h2
                            class="font-headline-lg lg:font-display-lg text-headline-lg lg:text-display-lg text-[#12213F] tracking-tight">
                            Frequently Asked Questions
                        </h2>
                    </div>
                    <!-- Accordion container -->
                    <div class="max-w-3xl mx-auto flex flex-col gap-space-sm" id="faq-accordion">
                        <!-- FAQ 1 (Expanded by default) -->
                        <div
                            class="rounded-2xl border border-[#D5EAFD] bg-[#F6FAFD]/60 overflow-hidden transition-all faq-item">
                            <button
                                class="w-full p-space-md text-left flex items-center justify-between gap-space-md faq-trigger">
                                <span class="font-headline-sm text-headline-sm text-[#12213F]">How does ExtractMails
                                    automate email processing?</span>
                                <span
                                    class="material-symbols-outlined text-primary transition-transform duration-200 faq-icon rotate-180">expand_more</span>
                            </button>
                            <div class="px-space-md pb-space-md font-body-md text-body-md text-[#52627B] faq-body">
                                ExtractMails helps automate incoming email processing by parsing messages, extracting
                                relevant information, structuring the data, and transferring it to supported business
                                systems or webhook endpoints.
                            </div>
                        </div>
                        <!-- FAQ 2 -->
                        <div
                            class="rounded-2xl border border-[#D5EAFD] bg-[#F6FAFD]/60 overflow-hidden transition-all faq-item">
                            <button
                                class="w-full p-space-md text-left flex items-center justify-between gap-space-md faq-trigger">
                                <span class="font-headline-sm text-headline-sm text-[#12213F]">Can I trigger workflows
                                    based on incoming emails?</span>
                                <span
                                    class="material-symbols-outlined text-primary transition-transform duration-200 faq-icon">expand_more</span>
                            </button>
                            <div
                                class="px-space-md pb-space-md font-body-md text-body-md text-[#52627B] faq-body hidden">
                                Yes. ExtractMails supports rule-based workflows that run when incoming emails match
                                specified conditions, enabling configured follow-up actions.
                            </div>
                        </div>
                        <!-- FAQ 3 -->
                        <div
                            class="rounded-2xl border border-[#D5EAFD] bg-[#F6FAFD]/60 overflow-hidden transition-all faq-item">
                            <button
                                class="w-full p-space-md text-left flex items-center justify-between gap-space-md faq-trigger">
                                <span class="font-headline-sm text-headline-sm text-[#12213F]">What types of business
                                    emails can be processed?</span>
                                <span
                                    class="material-symbols-outlined text-primary transition-transform duration-200 faq-icon">expand_more</span>
                            </button>
                            <div
                                class="px-space-md pb-space-md font-body-md text-body-md text-[#52627B] faq-body hidden">
                                Potential use cases include lead enquiries, customer requests, booking confirmations,
                                order notifications, and other operational emails containing relevant business
                                information.
                            </div>
                        </div>
                        <!-- FAQ 4 -->
                        <div
                            class="rounded-2xl border border-[#D5EAFD] bg-[#F6FAFD]/60 overflow-hidden transition-all faq-item">
                            <button
                                class="w-full p-space-md text-left flex items-center justify-between gap-space-md faq-trigger">
                                <span class="font-headline-sm text-headline-sm text-[#12213F]">Does ExtractMails support
                                    webhook-based email automation?</span>
                                <span
                                    class="material-symbols-outlined text-primary transition-transform duration-200 faq-icon">expand_more</span>
                            </button>
                            <div
                                class="px-space-md pb-space-md font-body-md text-body-md text-[#52627B] faq-body hidden">
                                Yes. ExtractMails can send parsed email data to a designated webhook URL so connected
                                applications can receive and process the information for further business actions.
                            </div>
                        </div>
                    </div>
                </div>
                </section>
            </div>
</main>
</div>

<!-- Scoped FAQ Accordion Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordion = document.getElementById('faq-accordion');
    if (!accordion) return;

    const triggers = accordion.querySelectorAll('.faq-trigger');
    triggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = trigger.closest('.faq-item');
            if (!parent) return;
            const body = parent.querySelector('.faq-body');
            const icon = trigger.querySelector('.faq-icon');
            if (!body) return;

            const isHidden = body.classList.contains('hidden');

            // Close siblings
            accordion.querySelectorAll('.faq-body').forEach(b => b.classList.add('hidden'));
            accordion.querySelectorAll('.faq-icon').forEach(i => i.classList.remove('rotate-180'));

            // Toggle current
            if (isHidden) {
                body.classList.remove('hidden');
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
