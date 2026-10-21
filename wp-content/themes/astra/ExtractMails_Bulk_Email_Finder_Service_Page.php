<?php
/*
Template Name: Bulk Email Finder
*/

get_header();
$theme_uri = get_stylesheet_directory_uri();
$bulkhero_url = $theme_uri . '/bulkhero.png';
?>

  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Font DM Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"DM Sans"', 'sans-serif'],
          },
          colors: {
            'navy-main': '#071C50',
            'navy-dark': '#041030',
            'navy-secondary': '#0A2762',
            'blue-primary': '#2563EB',
            'blue-bright': '#3B82F6',
            'blue-soft': '#EFF6FF',
            'blue-border': '#D5EAFD',
            'bg-alt': '#F6FAFD',
            'text-primary': '#0B1F4A',
            'text-secondary': '#536784',
            'text-dark-sub': '#D8E7FF',
          },
          boxShadow: {
            'glass': '0 8px 32px 0 rgba(7, 28, 80, 0.06)',
            'glass-hover': '0 16px 40px 0 rgba(37, 99, 235, 0.12)',
            'dark-glass': '0 12px 40px 0 rgba(0, 0, 0, 0.25)',
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'DM Sans', sans-serif;
      color: #0B1F4A;
      background-color: #FFFFFF;
      overflow-x: clip;
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.90);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border: 1px solid #D5EAFD;
    }
    .dark-glass-card {
      background: rgba(10, 39, 98, 0.65);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(213, 234, 253, 0.22);
    }
    .hero-gradient {
      background: radial-gradient(circle at 75% 30%, #153c8b 0%, #0A2762 45%, #071C50 100%);
    }
    .cta-gradient {
      background: linear-gradient(135deg, #071C50 0%, #0A2762 50%, #153e94 100%);
    }
    details summary::-webkit-details-marker {
      display: none;
    }
    @media (min-width: 1024px) {
      .editorial-sticky-art {
        position: -webkit-sticky;
        position: sticky;
        top: 115px;
        align-self: flex-start;
      }
    }
      /* === HERO CINEMATIC CHOREOGRAPHY === */
    @keyframes heroPillDrop {
      0% {
        opacity: 0;
        transform: translateY(-16px) scale(0.94);
        filter: blur(8px);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0px);
      }
    }

    @keyframes heroHeadlineReveal {
      0% {
        opacity: 0;
        transform: translateY(32px) scale(0.97);
        filter: blur(12px);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0px);
      }
    }

    @keyframes heroTextReveal {
      0% {
        opacity: 0;
        transform: translateY(22px);
        filter: blur(6px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
        filter: blur(0px);
      }
    }

    @keyframes heroBtnSpring {
      0% {
        opacity: 0;
        transform: translateY(24px) scale(0.95);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes heroVisualDock {
      0% {
        opacity: 0;
        transform: perspective(1200px) translateY(42px) scale(0.95) rotateX(4deg);
        filter: blur(14px);
      }
      100% {
        opacity: 1;
        transform: perspective(1200px) translateY(0) scale(1) rotateX(0deg);
        filter: blur(0px);
      }
    }

    @keyframes cardSheenSweep {
      0% { left: -140%; opacity: 0; }
      25% { opacity: 0.85; }
      65% { opacity: 0.85; }
      100% { left: 180%; opacity: 0; }
    }

    .hero-pill-badge {
      animation: heroPillDrop 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.08s both;
      will-change: transform, opacity, filter;
    }

    .hero-main-title {
      animation: heroHeadlineReveal 1.05s cubic-bezier(0.16, 1, 0.3, 1) 0.18s both;
      will-change: transform, opacity, filter;
    }

    .hero-sub-text {
      animation: heroTextReveal 0.95s cubic-bezier(0.16, 1, 0.3, 1) 0.30s both;
      will-change: transform, opacity, filter;
    }

    .hero-cta-group,
    .hero-proof-bar {
      animation: heroBtnSpring 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.44s both;
    }

    .hero-visual-container {
      animation: heroVisualDock 1.25s cubic-bezier(0.16, 1, 0.3, 1) 0.22s both;
      will-change: transform, opacity, filter;
      position: relative;
    }

    .hero-preview-card {
      position: relative;
      overflow: hidden;
    }

    .hero-preview-card::after {
      content: "";
      position: absolute;
      top: 0;
      left: -140%;
      width: 70%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent 0%,
        rgba(255, 255, 255, 0.15) 20%,
        rgba(255, 255, 255, 0.6) 50%,
        rgba(108, 181, 255, 0.4) 75%,
        transparent 100%
      );
      transform: skewX(-24deg);
      pointer-events: none;
      z-index: 30;
      animation: cardSheenSweep 1.8s cubic-bezier(0.16, 1, 0.3, 1) 0.45s both;
    }

    /* === MULTI-DIRECTIONAL CINEMATIC SCROLL REVEAL SYSTEM === */
    .reveal-ready {
      opacity: 0 !important;
      will-change: transform, opacity, filter;
      transition: opacity 0.88s cubic-bezier(0.16, 1, 0.3, 1),
                  transform 0.88s cubic-bezier(0.16, 1, 0.3, 1),
                  filter 0.88s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Direction 1: Upward Lift & Focal De-blur */
    .reveal-ready.reveal-up {
      transform: translateY(42px) scale(0.97) !important;
      filter: blur(8px) !important;
    }

    /* Direction 2: Left Visual / 3D Art Entrance */
    .reveal-ready.reveal-left {
      transform: translateX(-48px) scale(0.96) !important;
      filter: blur(10px) !important;
    }

    /* Direction 3: Right Narrative / Content Card Entrance */
    .reveal-ready.reveal-right {
      transform: translateX(48px) scale(0.96) !important;
      filter: blur(10px) !important;
    }

    /* Direction 4: Scale Pop Entrance for Grid & Feature Cards */
    .reveal-ready.reveal-scale {
      transform: translateY(28px) scale(0.93) !important;
      filter: blur(8px) !important;
    }

    /* Resting State: Full Reset (Preserves Sticky Positioning on Editorial Artwork) */
    .reveal-ready.is-visible {
      opacity: 1 !important;
      transform: none !important;
      filter: none !important;
    }

    /* Interactive Cursor Spotlight on Cards */
    .glass-card,
    .dark-glass-card,
    details {
      position: relative;
      overflow: hidden;
      transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.32s ease, border-color 0.32s ease !important;
    }

    .glass-card::before,
    .dark-glass-card::before,
    details::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: radial-gradient(
        380px circle at var(--spot-x, -999px) var(--spot-y, -999px),
        rgba(32, 91, 232, 0.14),
        transparent 70%
      );
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.35s ease;
      z-index: 10;
    }

    .glass-card:hover::before,
    .dark-glass-card:hover::before,
    details:hover::before {
      opacity: 1;
    }

    @media (prefers-reduced-motion: reduce) {
      .hero-pill-badge,
      .hero-main-title,
      .hero-sub-text,
      .hero-cta-group,
      .hero-proof-bar,
      .hero-visual-container,
      .hero-preview-card::after,
      .reveal-ready,
      .reveal-ready.reveal-up,
      .reveal-ready.reveal-left,
      .reveal-ready.reveal-right,
      .reveal-ready.reveal-scale {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
        filter: none !important;
        transition: none !important;
      }
    }

  </style>

  <!-- ========================================================= -->
  <!-- 2. HERO SECTION -->
  <!-- ========================================================= -->
  <section class="relative hero-gradient text-white pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden">
    <!-- Ambient radial glow -->
    <div class="absolute top-1/4 right-10 w-[550px] h-[550px] bg-blue-bright/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 w-[400px] h-[400px] bg-navy-secondary/50 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
        
        <!-- Left Column: Copy (46%) -->
        <div class="lg:col-span-6 xl:col-span-6 space-y-6 text-center lg:text-left">
          <div class="hero-pill-badge inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full dark-glass-card text-[13px] font-semibold text-text-dark-sub">
            <span class="w-2 h-2 rounded-full bg-blue-bright animate-pulse"></span>
            Professional Email Discovery
          </div>

          <h1 class="hero-main-title text-4xl sm:text-5xl lg:text-[60px] font-bold tracking-tight leading-[1.12] text-white">
            Bulk Email Finder — <br class="hidden sm:inline" />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 via-blue-200 to-white">
              Find professional email addresses faster
            </span>
          </h1>

          <p class="hero-sub-text text-lg sm:text-[19px] leading-[1.65] text-text-dark-sub max-w-xl mx-auto lg:mx-0">
            Turn your list of contacts into organized professional email addresses ready for outreach and communication.
          </p>

          <!-- Buttons -->
          <div class="hero-cta-group flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-3">
            <a href="#upload" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 rounded-xl bg-blue-primary hover:bg-blue-600 text-white font-semibold text-base shadow-lg shadow-blue-primary/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
              <svg class="w-5 h-5 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              Upload Contact List
            </a>
            <a href="#how-it-works" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 rounded-xl dark-glass-card hover:bg-white/10 text-white font-semibold text-base transition-all hover:border-white/40">
              See How It Works
            </a>
          </div>

          <!-- Proof stats bar -->
          <div class="hero-proof-bar pt-6 border-t border-white/15 flex items-center justify-center lg:justify-start gap-8 text-sm text-text-dark-sub">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-bright" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              <span>SMTP-Verified Results</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-bright" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              <span>CSV & Excel Ready</span>
            </div>
          </div>
        </div>

        <!-- Right Column: 3D Composition & Interactive Glass Preview (54%) -->
        <div class="hero-visual-container lg:col-span-6 xl:col-span-6 relative flex flex-col items-center justify-center">
          <!-- Main 3D Artwork cutout -->
          <div class="relative w-full max-w-[480px] lg:max-w-[520px] aspect-square flex items-center justify-center">
            <img src="<?php echo $bulkhero_url; ?>" onerror="this.onerror=null;this.src='https://www.extractmails.com/wp-content/uploads/2024/12/bulkhero.png';" alt="3D cobalt blue open envelope with translucent acrylic contact cards" class="w-full h-full object-contain filter drop-shadow-2xl transition-transform hover:scale-105 duration-500 pointer-events-none" />

            <!-- Floating Glass Results Card overlay -->
            <div class="hero-preview-card absolute bottom-2 left-2 right-2 sm:left-4 sm:right-auto sm:w-[350px] dark-glass-card p-4.5 rounded-2xl shadow-dark-glass border border-white/20 text-white backdrop-blur-xl">
              <div class="flex items-center justify-between pb-3 border-b border-white/10 text-xs">
                <span class="font-semibold text-text-dark-sub flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                  Example data
                </span>
                <span class="text-[11px] text-blue-200 bg-blue-500/30 px-2 py-0.5 rounded-md">Live Preview</span>
              </div>
              <div class="mt-3 space-y-2.5 text-xs">
                <div class="flex items-center justify-between bg-white/5 p-2 rounded-lg">
                  <div>
                    <p class="font-medium text-white">Sarah Jenkins</p>
                    <p class="text-[11px] text-text-dark-sub">acmecorp.example</p>
                  </div>
                  <span class="text-[11px] font-mono text-emerald-300 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-500/30">sarah.j@acmecorp.example</span>
                </div>
                <div class="flex items-center justify-between bg-white/5 p-2 rounded-lg">
                  <div>
                    <p class="font-medium text-white">David Chen</p>
                    <p class="text-[11px] text-text-dark-sub">vertextech.example</p>
                  </div>
                  <span class="text-[11px] font-mono text-emerald-300 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-500/30">d.chen@vertextech.example</span>
                </div>
              </div>
            </div>

            <!-- Mini floating tag top right -->
            <div class="absolute top-6 right-2 sm:right-6 dark-glass-card px-3.5 py-2 rounded-xl text-xs font-semibold text-blue-100 flex items-center gap-2 shadow-lg">
              <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
              <span>10,000+ List Capacity</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 3. WHAT IS A BULK EMAIL FINDER? -->
  <!-- ========================================================= -->
  <section class="py-24 bg-white relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
        
        <!-- Copy Column (7 cols on desktop to preserve long readable text) -->
        <div class="lg:col-span-7 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider">
            Understanding the Tool
          </div>
          
          <h2 class="text-3xl sm:text-4xl lg:text-[42px] font-bold text-text-primary tracking-tight leading-[1.2]">
            What Is a Bulk Email Finder?
          </h2>

          <div class="space-y-4 text-base sm:text-[17px] leading-[1.7] text-text-secondary">
            <p>
              A bulk email finder is a tool used to find email addresses for a large list of people or organizations. There’s no need to search for emails manually. Now users can simply upload a a file containing the names of the people, companies, and domains, and the tool can identify possible email addresses associated with those contacts.
            </p>
            <p>
              The Bulk Email Finder uses publicly available data, domain patterns, and various online sources to find potential email addresses. This powerful tool also verifies each email via SMTP to make sure that email is successfully delivered to the recipient while reducing bounce rates and improving campaign performance. Our tool is designed to handle the volume with ease whether you need 100 or 10,000 contacts.
            </p>
            <p>
              A bulk email finder is especially useful for someone who works with large contact lists or needs to quickly coordinate with potential leads, partners, or industry professionals.
            </p>
          </div>

          <div class="pt-4 flex flex-wrap gap-4 text-sm font-medium text-text-primary">
            <div class="flex items-center gap-2 bg-bg-alt px-3.5 py-2 rounded-xl border border-blue-border">
              <svg class="w-4 h-4 text-blue-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
              <span>Automated Domain Pattern Analysis</span>
            </div>
            <div class="flex items-center gap-2 bg-bg-alt px-3.5 py-2 rounded-xl border border-blue-border">
              <svg class="w-4 h-4 text-blue-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
              <span>Real-Time SMTP Verification</span>
            </div>
          </div>
        </div>

        <!-- Image Column with glass badge overlay (5 cols) -->
        <div class="lg:col-span-5 relative">
          <div class="relative rounded-3xl overflow-hidden border border-blue-border shadow-glass group">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuB2AhwAiMGzLHkKuJi3fKqABwv-XWZt_KHyrJkQ-_12hoGnqEMGItv3eS864nQO7rjHjenEkQ8dnQAC-UKQicTLiRZNMEU4NqVRLHwe3owInl5srZL-JRs2aWMqHeaWhnKTgIE7jFyyEasYopR05GxWDCwYpWnXNaFggjR8S4oug3Lb5R20OSYDRUnDHziO2x3VlxfqHmzaDXcEuMbAyJ4YB5pmRdvY_dzaEX2hvC4bA4KCHI4IhWw" alt="Colleagues reviewing structured partner outreach on tablet in bright minimalist office" class="w-full h-[420px] object-cover transition-transform duration-700 group-hover:scale-105" />
            <div class="absolute inset-0 bg-gradient-to-t from-navy-main/70 via-transparent to-transparent"></div>
            
            <!-- Restrained glass contact-list overlay outside/at bottom edge -->
            <div class="absolute bottom-5 left-5 right-5 glass-card p-4 rounded-2xl shadow-lg border border-white/60">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-primary/10 text-blue-primary flex items-center justify-center font-bold">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <h4 class="text-sm font-bold text-text-primary">Structured Lead Pipeline</h4>
                  <p class="text-xs text-text-secondary">Instant conversion of prospect names into validated mailboxes</p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 4. WHY BUSINESSES USE A BULK EMAIL FINDER? -->
  <!-- ========================================================= -->
  <section class="py-24 bg-bg-alt relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider mb-3">
          Business Impact
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight">
          Why Businesses Use a Bulk Email Finder?
        </h2>
      </div>

      <!-- 3 Spacious Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Card 1 -->
        <div class="glass-card p-8 rounded-3xl transition-all duration-300 hover:shadow-glass-hover hover:-translate-y-1 relative group">
          <div class="w-14 h-14 rounded-2xl bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center mb-6 group-hover:bg-blue-primary group-hover:text-white transition-colors">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-text-primary mb-3.5">
            Faster Contact Discovery
          </h3>
          <p class="text-base leading-[1.65] text-text-secondary">
            It takes time and effort to find email addresses manually. Bulk email finder automates this process to collect contact information faster.
          </p>
        </div>

        <!-- Card 2 -->
        <div class="glass-card p-8 rounded-3xl transition-all duration-300 hover:shadow-glass-hover hover:-translate-y-1 relative group">
          <div class="w-14 h-14 rounded-2xl bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center mb-6 group-hover:bg-blue-primary group-hover:text-white transition-colors">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-text-primary mb-3.5">
            Organized Outreach
          </h3>
          <p class="text-base leading-[1.65] text-text-secondary">
            With structured contact data, businesses can manage outreach campaigns, communication, and relationship building more effectively.
          </p>
        </div>

        <!-- Card 3 -->
        <div class="glass-card p-8 rounded-3xl transition-all duration-300 hover:shadow-glass-hover hover:-translate-y-1 relative group">
          <div class="w-14 h-14 rounded-2xl bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center mb-6 group-hover:bg-blue-primary group-hover:text-white transition-colors">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-text-primary mb-3.5">
            Scalable Lead Building
          </h3>
          <p class="text-base leading-[1.65] text-text-secondary">
            Teams can expand their lead generation efforts by identifying contact information for multiple prospects at once.
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 5. KEY FEATURES OF BULK EMAIL FINDER -->
  <!-- ========================================================= -->
  <section id="features" class="py-24 bg-white relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider mb-3">
          Core Capabilities
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight">
          Key Features of Bulk Email Finder
        </h2>
      </div>

      <!-- Asymmetric Bento Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Large Feature Card: Bulk List Upload (8 cols) -->
        <div class="lg:col-span-8 glass-card p-8 sm:p-10 rounded-3xl flex flex-col justify-between hover:shadow-glass-hover transition-all">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-primary text-white flex items-center justify-center mb-6 shadow-sm">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
            </div>
            <h3 class="text-2xl sm:text-3xl font-bold text-text-primary mb-3">
              Bulk List Upload
            </h3>
            <p class="text-base sm:text-lg leading-[1.65] text-text-secondary max-w-2xl">
              Upload spreadsheets containing names, company domains, or organizations and process multiple contacts at once.
            </p>
          </div>

          <!-- Spreadsheet Upload UI Preview -->
          <div class="mt-8 pt-6 border-t border-blue-border/70">
            <div id="upload" class="bg-bg-alt rounded-2xl p-5 border border-dashed border-blue-primary/40 flex flex-col sm:flex-row items-center justify-between gap-4">
              <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-white border border-blue-border text-blue-primary flex items-center justify-center font-bold text-xs shadow-sm">
                  CSV
                </div>
                <div>
                  <p class="text-sm font-semibold text-text-primary">leads_q3_outreach.csv</p>
                  <p class="text-xs text-text-secondary">Ready to map columns: [Full Name, Company, Domain]</p>
                </div>
              </div>
              <span class="px-4 py-2 rounded-xl bg-blue-soft border border-blue-border text-xs font-semibold text-blue-primary">
                Auto-mapping enabled
              </span>
            </div>
          </div>
        </div>

        <!-- Supporting Feature Card 1: Email Validation (4 cols) -->
        <div class="lg:col-span-4 glass-card p-8 rounded-3xl flex flex-col justify-between hover:shadow-glass-hover transition-all">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center mb-6">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-text-primary mb-3">
              Email Validation
            </h3>
            <p class="text-base leading-[1.65] text-text-secondary">
              This tool also includes validation checks to verify whether an email address is active or deliverable.
            </p>
          </div>
          <!-- Validation state UI preview -->
          <div class="mt-8 pt-4 border-t border-blue-border/70 flex items-center justify-between text-xs font-medium">
            <span class="text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">✓ SMTP Deliverable</span>
            <span class="text-text-secondary">Bounce guard active</span>
          </div>
        </div>

        <!-- Supporting Feature Card 2: Export Options (6 cols) -->
        <div class="lg:col-span-6 glass-card p-8 rounded-3xl flex flex-col justify-between hover:shadow-glass-hover transition-all">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center mb-6">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-text-primary mb-3">
              Export Options
            </h3>
            <p class="text-base leading-[1.65] text-text-secondary">
              Results can usually be downloaded or integrated into other tools such as CRM platforms, marketing software, or spreadsheets.
            </p>
          </div>
          <!-- Export controls preview -->
          <div class="mt-8 pt-4 border-t border-blue-border/70 flex flex-wrap gap-2 text-xs font-semibold text-text-primary">
            <span class="bg-bg-alt px-3 py-1.5 rounded-lg border border-blue-border">Download CSV</span>
            <span class="bg-bg-alt px-3 py-1.5 rounded-lg border border-blue-border">Excel (.xlsx)</span>
            <span class="bg-bg-alt px-3 py-1.5 rounded-lg border border-blue-border">Webhook / CRM Sync</span>
          </div>
        </div>

        <!-- Supporting Feature Card 3: Contact Enrichment (6 cols) -->
        <div class="lg:col-span-6 glass-card p-8 rounded-3xl flex flex-col justify-between hover:shadow-glass-hover transition-all">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center mb-6">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-text-primary mb-3">
              Contact Enrichment
            </h3>
            <p class="text-base leading-[1.65] text-text-secondary">
              Along with contacts, it also provides context such as professional roles, organizations, or other publicly available details.
            </p>
          </div>
          <!-- Role & company record UI preview -->
          <div class="mt-8 pt-4 border-t border-blue-border/70 flex items-center justify-between text-xs text-text-secondary">
            <span>Enriched fields: Role, Seniority, Verified Domain</span>
            <span class="text-blue-primary font-semibold">100% Contextual</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 6. HOW A BULK EMAIL FINDER WORKS -->
  <!-- ========================================================= -->
  <section id="how-it-works" class="py-24 bg-bg-alt relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider mb-3">
          Workflow Journey
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight">
          How a Bulk Email Finder Works
        </h2>
      </div>

      <!-- 4-step Horizontal Sequence on desktop, stacked on mobile -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
        
        <!-- Step 1 -->
        <div class="glass-card p-7 rounded-3xl relative flex flex-col justify-between hover:shadow-glass-hover transition-all group">
          <div>
            <div class="flex items-center justify-between mb-5">
              <span class="w-10 h-10 rounded-xl bg-navy-main text-white font-bold text-sm flex items-center justify-center shadow-sm">
                01
              </span>
              <svg class="w-6 h-6 text-blue-bright" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary mb-3">
              Upload Your Contact List
            </h3>
            <p class="text-sm leading-[1.65] text-text-secondary">
              Upload a spreadsheet containing information such as names, company names, or domains.
            </p>
          </div>
          <div class="mt-6 pt-3 border-t border-blue-border text-xs text-text-primary font-medium">
            Format: CSV, XLS, XLSX
          </div>
        </div>

        <!-- Step 2 -->
        <div class="glass-card p-7 rounded-3xl relative flex flex-col justify-between hover:shadow-glass-hover transition-all group">
          <div>
            <div class="flex items-center justify-between mb-5">
              <span class="w-10 h-10 rounded-xl bg-navy-main text-white font-bold text-sm flex items-center justify-center shadow-sm">
                02
              </span>
              <svg class="w-6 h-6 text-blue-bright" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary mb-3">
              Analyze and Generate Emails
            </h3>
            <p class="text-sm leading-[1.65] text-text-secondary">
              The system analyzes company email patterns and other signals to generate possible professional email addresses.
            </p>
          </div>
          <div class="mt-6 pt-3 border-t border-blue-border text-xs text-text-primary font-medium">
            AI pattern match engine
          </div>
        </div>

        <!-- Step 3 -->
        <div class="glass-card p-7 rounded-3xl relative flex flex-col justify-between hover:shadow-glass-hover transition-all group">
          <div>
            <div class="flex items-center justify-between mb-5">
              <span class="w-10 h-10 rounded-xl bg-navy-main text-white font-bold text-sm flex items-center justify-center shadow-sm">
                03
              </span>
              <svg class="w-6 h-6 text-blue-bright" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary mb-3">
              Validate the Results
            </h3>
            <p class="text-sm leading-[1.65] text-text-secondary">
              Every email is SMTP‑verified to establish whether email addresses are valid or deliverable.
            </p>
          </div>
          <div class="mt-6 pt-3 border-t border-blue-border text-xs text-text-primary font-medium">
            Direct mail server handshake
          </div>
        </div>

        <!-- Step 4 -->
        <div class="glass-card p-7 rounded-3xl relative flex flex-col justify-between hover:shadow-glass-hover transition-all group">
          <div>
            <div class="flex items-center justify-between mb-5">
              <span class="w-10 h-10 rounded-xl bg-navy-main text-white font-bold text-sm flex items-center justify-center shadow-sm">
                04
              </span>
              <svg class="w-6 h-6 text-blue-bright" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary mb-3">
              Export Your Contact Data
            </h3>
            <p class="text-sm leading-[1.65] text-text-secondary">
              Download the results or export them to other tools used for communication, marketing, or lead management.
            </p>
          </div>
          <div class="mt-6 pt-3 border-t border-blue-border text-xs text-text-primary font-medium">
            Single-click export package
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 7. WHO CAN USE A BULK EMAIL FINDER? -->
  <!-- ========================================================= -->
  <section class="py-24 bg-white relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto text-center space-y-4 mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider">
          Diverse Roles
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight">
          Who Can Use a Bulk Email Finder?
        </h2>
        <p class="text-base sm:text-lg text-text-secondary leading-[1.65]">
          Bulk email finder tools are useful for many professionals who need to communicate with new contacts.
        </p>
        <p class="text-sm font-semibold text-text-primary">
          Common users include:
        </p>
      </div>

      <!-- 10 Audience Groups in a neat grid of compact frosted tiles -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 sm:gap-5 mb-12">
        
        <!-- 1 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Sales teams</span>
        </div>

        <!-- 2 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Marketing professionals</span>
        </div>

        <!-- 3 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Recruiters</span>
        </div>

        <!-- 4 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Business development teams</span>
        </div>

        <!-- 5 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Researchers</span>
        </div>

        <!-- 6 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Journalists</span>
        </div>

        <!-- 7 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Agencies</span>
        </div>

        <!-- 8 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Event organizers</span>
        </div>

        <!-- 9 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Partnership managers</span>
        </div>

        <!-- 10 -->
        <div class="glass-card p-5 rounded-2xl text-center hover:shadow-glass-hover hover:border-blue-primary/40 transition-all group">
          <div class="w-10 h-10 mx-auto rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
          <span class="text-sm font-bold text-text-primary">Entrepreneurs</span>
        </div>

      </div>

      <!-- Preserved closing sentence -->
      <div class="text-center">
        <p class="inline-block px-6 py-3 rounded-full bg-blue-soft border border-blue-border text-sm sm:text-base font-medium text-navy-secondary">
          Anyone who needs to connect with multiple people across organizations can benefit from organized email discovery.
        </p>
      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 8. BENEFITS OF USING A BULK EMAIL FINDER -->
  <!-- ========================================================= -->
  <section class="py-24 bg-bg-alt relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider mb-3">
          Key Advantages
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight">
          Benefits of Using a Bulk Email Finder
        </h2>
      </div>

      <!-- 3 col x 2 row clean arrangement of lightweight benefit blocks -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <!-- Benefit 1 -->
        <div class="p-6 rounded-2xl bg-white border border-blue-border/80 shadow-sm hover:shadow-glass transition-all">
          <div class="flex items-center gap-3.5 mb-3.5">
            <div class="w-9 h-9 rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary">Save Research Time</h3>
          </div>
          <p class="text-sm sm:text-base leading-[1.65] text-text-secondary">
            Automates email discovery and reduce manual research.
          </p>
        </div>

        <!-- Benefit 2 -->
        <div class="p-6 rounded-2xl bg-white border border-blue-border/80 shadow-sm hover:shadow-glass transition-all">
          <div class="flex items-center gap-3.5 mb-3.5">
            <div class="w-9 h-9 rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary">Streamline Outreach</h3>
          </div>
          <p class="text-sm sm:text-base leading-[1.65] text-text-secondary">
            Prepare contact lists quickly for outreach and communication.
          </p>
        </div>

        <!-- Benefit 3 -->
        <div class="p-6 rounded-2xl bg-white border border-blue-border/80 shadow-sm hover:shadow-glass transition-all">
          <div class="flex items-center gap-3.5 mb-3.5">
            <div class="w-9 h-9 rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary">Better Contact Organization</h3>
          </div>
          <p class="text-sm sm:text-base leading-[1.65] text-text-secondary">
            Keep prospect data structured and easy to manage.
          </p>
        </div>

        <!-- Benefit 4 -->
        <div class="p-6 rounded-2xl bg-white border border-blue-border/80 shadow-sm hover:shadow-glass transition-all">
          <div class="flex items-center gap-3.5 mb-3.5">
            <div class="w-9 h-9 rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary">Team Collaboration</h3>
          </div>
          <p class="text-sm sm:text-base leading-[1.65] text-text-secondary">
            Share and manage contact lists across teams.
          </p>
        </div>

        <!-- Benefit 5 -->
        <div class="p-6 rounded-2xl bg-white border border-blue-border/80 shadow-sm hover:shadow-glass transition-all">
          <div class="flex items-center gap-3.5 mb-3.5">
            <div class="w-9 h-9 rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary">Simplified Lead Generation</h3>
          </div>
          <p class="text-sm sm:text-base leading-[1.65] text-text-secondary">
            Discover potential contacts for outreach and prospecting.
          </p>
        </div>

        <!-- Benefit 6 -->
        <div class="p-6 rounded-2xl bg-white border border-blue-border/80 shadow-sm hover:shadow-glass transition-all">
          <div class="flex items-center gap-3.5 mb-3.5">
            <div class="w-9 h-9 rounded-xl bg-blue-soft text-blue-primary flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-text-primary">Workflow Integration</h3>
          </div>
          <p class="text-sm sm:text-base leading-[1.65] text-text-secondary">
            Export data easily to CRM and outreach tools.
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 9. USE CASES -->
  <!-- ========================================================= -->
  <section id="use-cases" class="py-24 bg-white relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider mb-3">
          Real-World Applications
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight">
          Use Cases
        </h2>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Editorial Split: Ultra-Photorealistic Collaboration Image (5 cols) -->
        <div class="lg:col-span-5 relative editorial-sticky-art">
          <div class="rounded-3xl overflow-hidden border border-blue-border shadow-glass relative group">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTTGTrIVyVr4NSdhQYrPnd0IcKPvZ1nUyFlGQV4lqDhPThWAPdwD2GMgLjgL4uhvjA-YTfTOwTbbopsEQjWyxQIJEDxXsqyJhI4XpL73TufbmXocYgiHKpI32RT13vAanTEnRbiQmPOAXHefax9munteP4Mw7wCwoj1H9MHQSknl__fdz7U_QPDVNUQzhBsYDnShQOp5KVbieEPrPFJJoFp5M1EU8rmOJ0UT3JiMn1eTVYV_bI1BQ" alt="Diverse business professionals in bright glass office collaborating over contact lists" class="w-full h-[520px] object-cover transition-transform duration-700 group-hover:scale-105" />
            <div class="absolute inset-0 bg-gradient-to-t from-navy-main/80 via-navy-main/20 to-transparent"></div>
            
            <div class="absolute bottom-6 left-6 right-6 text-white">
              <span class="text-xs font-bold tracking-wider uppercase text-blue-200">High-Velocity Operations</span>
              <h3 class="text-xl font-bold mt-1 text-white">Scale team outreach without manual lookup</h3>
            </div>
          </div>
        </div>

        <!-- 5 Structured Use-Case Rows (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
          
          <!-- Row 1: Sales Prospecting -->
          <div class="p-6 rounded-2xl bg-bg-alt border border-blue-border/80 hover:border-blue-primary/40 hover:bg-white hover:shadow-sm transition-all flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-primary mb-1">Sales Prospecting</h3>
              <p class="text-sm sm:text-base leading-[1.6] text-text-secondary">
                Identify relevant contacts within organizations for outreach and relationship building.
              </p>
            </div>
          </div>

          <!-- Row 2: Partnership Development -->
          <div class="p-6 rounded-2xl bg-bg-alt border border-blue-border/80 hover:border-blue-primary/40 hover:bg-white hover:shadow-sm transition-all flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-primary mb-1">Partnership Development</h3>
              <p class="text-sm sm:text-base leading-[1.6] text-text-secondary">
                Find decision makers at companies that may be suitable for collaborations.
              </p>
            </div>
          </div>

          <!-- Row 3: Recruitment Outreach -->
          <div class="p-6 rounded-2xl bg-bg-alt border border-blue-border/80 hover:border-blue-primary/40 hover:bg-white hover:shadow-sm transition-all flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-primary mb-1">Recruitment Outreach</h3>
              <p class="text-sm sm:text-base leading-[1.6] text-text-secondary">
                Connect with potential candidates or professionals in specific industries.
              </p>
            </div>
          </div>

          <!-- Row 4: Media and PR -->
          <div class="p-6 rounded-2xl bg-bg-alt border border-blue-border/80 hover:border-blue-primary/40 hover:bg-white hover:shadow-sm transition-all flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-primary mb-1">Media and PR</h3>
              <p class="text-sm sm:text-base leading-[1.6] text-text-secondary">
                Locate contacts for journalists, writers, and media professionals.
              </p>
            </div>
          </div>

          <!-- Row 5: Research and Networking -->
          <div class="p-6 rounded-2xl bg-bg-alt border border-blue-border/80 hover:border-blue-primary/40 hover:bg-white hover:shadow-sm transition-all flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-primary mb-1">Research and Networking</h3>
              <p class="text-sm sm:text-base leading-[1.6] text-text-secondary">
                Reach out to experts, academics, or professionals for insights or collaboration.
              </p>
            </div>
          </div>

        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 10. WHY USE EXTRACTMAILS FOR BULK EMAIL FINDING? -->
  <!-- ========================================================= -->
  <section class="py-24 bg-bg-alt relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Distinctive pale-blue glass panel enclosure -->
      <div class="rounded-3xl bg-blue-soft/70 border border-blue-border p-8 sm:p-12 lg:p-16 relative shadow-glass">
        
        <div class="text-center max-w-2xl mx-auto mb-14">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-white border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider mb-3">
            Platform Benefits
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight">
            Why Use ExtractMails for Bulk Email Finding?
          </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
          
          <!-- Smaller 3D contact-and-envelope illustration (4 cols) -->
          <div class="lg:col-span-4 flex justify-center editorial-sticky-art">
            <div class="relative w-64 h-64 sm:w-80 sm:h-80 flex items-center justify-center">
              <img src="<?php echo $bulkhero_url; ?>" onerror="this.onerror=null;this.src='https://www.extractmails.com/wp-content/uploads/2024/12/bulkhero.png';" alt="ExtractMails 3D contact data engine" class="w-full h-full object-contain filter drop-shadow-xl" />
            </div>
          </div>

          <!-- 5 Compact Feature Rows with generous text spacing (8 cols) -->
          <div class="lg:col-span-8 space-y-5">
            
            <!-- 1 -->
            <div class="bg-white/90 backdrop-blur-sm p-5 rounded-2xl border border-blue-border flex items-start gap-4">
              <div class="w-6 h-6 rounded-full bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-primary">Automated Email Discovery</h3>
                <p class="text-sm sm:text-base leading-[1.65] text-text-secondary mt-1">
                  ExtractMails helps simplify the process of locating professional email addresses from large lists.
                </p>
              </div>
            </div>

            <!-- 2 -->
            <div class="bg-white/90 backdrop-blur-sm p-5 rounded-2xl border border-blue-border flex items-start gap-4">
              <div class="w-6 h-6 rounded-full bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-primary">Structured Contact Processing</h3>
                <p class="text-sm sm:text-base leading-[1.65] text-text-secondary mt-1">
                  Upload and process multiple contacts in a single workflow.
                </p>
              </div>
            </div>

            <!-- 3 -->
            <div class="bg-white/90 backdrop-blur-sm p-5 rounded-2xl border border-blue-border flex items-start gap-4">
              <div class="w-6 h-6 rounded-full bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-primary">Organized Data Output</h3>
                <p class="text-sm sm:text-base leading-[1.65] text-text-secondary mt-1">
                  Download results in formats that are easy to manage and integrate with existing tools.
                </p>
              </div>
            </div>

            <!-- 4 -->
            <div class="bg-white/90 backdrop-blur-sm p-5 rounded-2xl border border-blue-border flex items-start gap-4">
              <div class="w-6 h-6 rounded-full bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-primary">Flexible Workflow</h3>
                <p class="text-sm sm:text-base leading-[1.65] text-text-secondary mt-1">
                  Use the tool for outreach preparation, research, recruiting, or prospecting depending on your needs.
                </p>
              </div>
            </div>

            <!-- 5 -->
            <div class="bg-white/90 backdrop-blur-sm p-5 rounded-2xl border border-blue-border flex items-start gap-4">
              <div class="w-6 h-6 rounded-full bg-blue-primary/10 text-blue-primary flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-primary">Simple User Experience</h3>
                <p class="text-sm sm:text-base leading-[1.65] text-text-secondary mt-1">
                  The interface is designed to make bulk email discovery straightforward and easy to manage.
                </p>
              </div>
            </div>

          </div>

        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 11. FREQUENTLY ASKED QUESTIONS (ALL 7 PRESERVED) -->
  <!-- ========================================================= -->
  <section id="faq" class="py-24 bg-white relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- Heading Column (4 cols) -->
        <div class="lg:col-span-4 space-y-4">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-blue-soft border border-blue-border text-xs font-bold text-blue-primary uppercase tracking-wider">
            Common Questions
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold text-text-primary tracking-tight leading-[1.2]">
            Frequently Asked Questions
          </h2>
          <p class="text-base text-text-secondary leading-[1.65]">
            Everything you need to know about processing bulk contact lists and discovering professional email addresses.
          </p>
        </div>

        <!-- Accordion Column (8 cols) -->
        <div class="lg:col-span-8 space-y-4">
          
          <!-- Q1 (Initial Open) -->
          <details open class="group p-6 rounded-2xl bg-bg-alt border border-blue-border transition-all open:bg-white open:shadow-glass">
            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
              <h3 class="text-lg sm:text-xl font-bold text-navy-main group-hover:text-blue-primary transition-colors pr-4">
                What information is needed to find emails?
              </h3>
              <div class="w-8 h-8 rounded-full bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center shrink-0 group-open:rotate-180 transition-transform duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </summary>
            <div class="mt-4 pt-4 border-t border-blue-border/70 text-base leading-[1.65] text-text-secondary">
              <p>Most bulk email finder tools work best when provided with names, company names, or domains. Additional information may improve matching accuracy.</p>
            </div>
          </details>

          <!-- Q2 -->
          <details class="group p-6 rounded-2xl bg-bg-alt border border-blue-border transition-all open:bg-white open:shadow-glass">
            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
              <h3 class="text-lg sm:text-xl font-bold text-navy-main group-hover:text-blue-primary transition-colors pr-4">
                What file types can I upload?
              </h3>
              <div class="w-8 h-8 rounded-full bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center shrink-0 group-open:rotate-180 transition-transform duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </summary>
            <div class="mt-4 pt-4 border-t border-blue-border/70 text-base leading-[1.65] text-text-secondary">
              <p>Many tools support common spreadsheet formats such as CSV or Excel files.</p>
            </div>
          </details>

          <!-- Q3 -->
          <details class="group p-6 rounded-2xl bg-bg-alt border border-blue-border transition-all open:bg-white open:shadow-glass">
            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
              <h3 class="text-lg sm:text-xl font-bold text-navy-main group-hover:text-blue-primary transition-colors pr-4">
                Can bulk email finders verify email addresses?
              </h3>
              <div class="w-8 h-8 rounded-full bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center shrink-0 group-open:rotate-180 transition-transform duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </summary>
            <div class="mt-4 pt-4 border-t border-blue-border/70 text-base leading-[1.65] text-text-secondary">
              <p>Some tools include validation checks that analyze whether an email appears deliverable or associated with a domain.</p>
            </div>
          </details>

          <!-- Q4 -->
          <details class="group p-6 rounded-2xl bg-bg-alt border border-blue-border transition-all open:bg-white open:shadow-glass">
            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
              <h3 class="text-lg sm:text-xl font-bold text-navy-main group-hover:text-blue-primary transition-colors pr-4">
                Can I export the results?
              </h3>
              <div class="w-8 h-8 rounded-full bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center shrink-0 group-open:rotate-180 transition-transform duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </summary>
            <div class="mt-4 pt-4 border-t border-blue-border/70 text-base leading-[1.65] text-text-secondary">
              <p>Yes. Most platforms allow exporting results, so they can be used in outreach tools, CRM systems, or spreadsheets.</p>
            </div>
          </details>

          <!-- Q5 -->
          <details class="group p-6 rounded-2xl bg-bg-alt border border-blue-border transition-all open:bg-white open:shadow-glass">
            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
              <h3 class="text-lg sm:text-xl font-bold text-navy-main group-hover:text-blue-primary transition-colors pr-4">
                Who typically uses bulk email finder tools?
              </h3>
              <div class="w-8 h-8 rounded-full bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center shrink-0 group-open:rotate-180 transition-transform duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </summary>
            <div class="mt-4 pt-4 border-t border-blue-border/70 text-base leading-[1.65] text-text-secondary">
              <p>Professionals involved in sales, marketing, recruiting, partnerships, and research often use bulk email discovery to organize communication with new contacts.</p>
            </div>
          </details>

          <!-- Q6 -->
          <details class="group p-6 rounded-2xl bg-bg-alt border border-blue-border transition-all open:bg-white open:shadow-glass">
            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
              <h3 class="text-lg sm:text-xl font-bold text-navy-main group-hover:text-blue-primary transition-colors pr-4">
                Can I upload a large contact list?
              </h3>
              <div class="w-8 h-8 rounded-full bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center shrink-0 group-open:rotate-180 transition-transform duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </summary>
            <div class="mt-4 pt-4 border-t border-blue-border/70 text-base leading-[1.65] text-text-secondary">
              <p>Yes. Bulk email finder tools are designed to process multiple contacts at once. Upload a spreadsheet containing names, company names, or domains to begin the discovery process.</p>
            </div>
          </details>

          <!-- Q7 -->
          <details class="group p-6 rounded-2xl bg-bg-alt border border-blue-border transition-all open:bg-white open:shadow-glass">
            <summary class="flex items-center justify-between cursor-pointer list-none select-none">
              <h3 class="text-lg sm:text-xl font-bold text-navy-main group-hover:text-blue-primary transition-colors pr-4">
                Is the bulk email finder useful for outreach campaigns?
              </h3>
              <div class="w-8 h-8 rounded-full bg-blue-soft border border-blue-border text-blue-primary flex items-center justify-center shrink-0 group-open:rotate-180 transition-transform duration-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </summary>
            <div class="mt-4 pt-4 border-t border-blue-border/70 text-base leading-[1.65] text-text-secondary">
              <p>Yes. The tool helps prepare contact lists that can be used for communication, outreach campaigns, networking, or research.</p>
            </div>
          </details>

        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 12. FINAL CTA SECTION -->
  <!-- ========================================================= -->
  <section class="py-20 bg-white relative">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Wide rounded navy panel on light surrounding background -->
      <div class="rounded-3xl cta-gradient text-white p-10 sm:p-14 lg:p-16 relative overflow-hidden shadow-2xl border border-white/20">
        
        <!-- Subtle ambient radial lights -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-bright/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-navy-main/80 rounded-full blur-3xl pointer-events-none"></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
          
          <!-- Copy (8 cols) -->
          <div class="lg:col-span-8 space-y-6 text-center lg:text-left">
            <h2 class="text-3xl sm:text-4xl lg:text-[46px] font-bold text-white tracking-tight leading-[1.15]">
              Find Emails Faster with Bulk Email Discovery
            </h2>

            <p class="text-lg sm:text-[19px] leading-[1.65] text-text-dark-sub max-w-2xl mx-auto lg:mx-0">
              Upload your contact list and simplify the process of discovering professional email addresses for outreach, research, and networking.
            </p>

            <p class="text-base sm:text-lg font-semibold text-blue-200">
              Start exploring bulk email discovery today.
            </p>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
              <a href="#upload" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl bg-blue-primary hover:bg-blue-600 text-white font-bold text-base shadow-lg shadow-blue-primary/40 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-5 h-5 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload Contact List
              </a>
            </div>
          </div>

          <!-- Restrained 3D cutout near outer edge (4 cols) -->
          <div class="lg:col-span-4 hidden lg:flex items-center justify-center">
            <div class="w-64 h-64 relative flex items-center justify-center">
              <img src="<?php echo $bulkhero_url; ?>" onerror="this.onerror=null;this.src='https://www.extractmails.com/wp-content/uploads/2024/12/bulkhero.png';" alt="ExtractMails fast bulk discovery" class="w-full h-full object-contain filter drop-shadow-2xl" />
            </div>
          </div>

        </div>

      </div>
    </div>
  </section>



<script>
  // === MULTI-DIRECTIONAL CINEMATIC SCROLL REVEAL & SPOTLIGHT SYSTEM ===
  (()=>{
    'use strict';
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const pointer = matchMedia('(hover: hover) and (pointer: fine)');

    // 1. Dynamic Cursor Spotlight Tracking
    document.addEventListener('pointermove', event => {
      if (motion.matches || !pointer.matches) return;
      const card = event.target.closest('.glass-card, .dark-glass-card, details');
      if (!card) return;
      const rect = card.getBoundingClientRect();
      const spotX = (event.clientX - rect.left).toFixed(1) + 'px';
      const spotY = (event.clientY - rect.top).toFixed(1) + 'px';
      card.style.setProperty('--spot-x', spotX);
      card.style.setProperty('--spot-y', spotY);
    }, { passive: true });

    if (motion.matches) return;
    if (!('IntersectionObserver' in window)) return;

    // 2. Selectors Mapping
    const upSelectors = [
      'section:not(:first-of-type) .text-center',
      '#features .text-center',
      '#how-it-works .text-center',
      '#faq .lg\\:col-span-5',
      'section:last-of-type .lg\\:col-span-8'
    ];

    const leftSelectors = [
      'section:nth-of-type(2) .lg\\:col-span-5',
      '#use-cases .lg\\:col-span-5',
      'section:nth-of-type(9) .lg\\:col-span-5'
    ];

    const rightSelectors = [
      'section:nth-of-type(2) .lg\\:col-span-7',
      '#use-cases .lg\\:col-span-7',
      'section:nth-of-type(9) .lg\\:col-span-7',
      'section:last-of-type .lg\\:col-span-4'
    ];

    const scaleSelectors = [
      'section:nth-of-type(3) .grid > div',
      '#features .grid > div',
      '#how-it-works .grid > div',
      'section:nth-of-type(6) .grid > div',
      'section:nth-of-type(7) .grid > div',
      '#faq details'
    ];

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      rootMargin: '0px 0px -45px 0px',
      threshold: 0.08
    });

    function register(selectors, dirClass) {
      const els = document.querySelectorAll(selectors.join(', '));
      els.forEach(el => {
        el.classList.add('reveal-ready', dirClass);
        observer.observe(el);
      });
    }

    register(upSelectors, 'reveal-up');
    register(leftSelectors, 'reveal-left');
    register(rightSelectors, 'reveal-right');
    register(scaleSelectors, 'reveal-scale');

    // Stagger delays for child grids
    const gridContainers = document.querySelectorAll(
      'section:nth-of-type(3) .grid, #features .grid, #how-it-works .grid, section:nth-of-type(6) .grid, section:nth-of-type(7) .grid, #faq .space-y-4'
    );
    gridContainers.forEach(container => {
      Array.from(container.children).forEach((child, idx) => {
        child.style.transitionDelay = ((idx % 6) * 0.09).toFixed(2) + 's';
      });
    });

    // Safety Viewport Check for elements above the fold
    requestAnimationFrame(() => {
      const vh = window.innerHeight;
      document.querySelectorAll('.reveal-ready:not(.is-visible)').forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < vh - 40 && rect.bottom > 0) {
          el.classList.add('is-visible');
        }
      });
    });
  })();
</script>


<?php get_footer(); ?>
