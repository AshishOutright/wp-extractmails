<?php
/**
 * Template Name: Email Deliverability Services 
 * Template Post Type: page
 * 
 * ExtractMails - Email Deliverability Designed for Higher Inbox Success
 * Advanced Deliverability, Automated DNS Alignment, Sender Reputation & Warmup Engines
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

// Enqueue Google Fonts (DM Sans, JetBrains Mono) into wp_head when running within WordPress
if (function_exists('add_action')) {
    add_action('wp_head', function() {
        ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,300..800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
        <title>Email Deliverability - Service Page | ExtractMails</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,300..800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <?php
}
?>

<!-- Tailwind CSS CDN & Custom Config -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,300..800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: {
            navy: '#071C50',
            secondaryNavy: '#142F68',
            navyCard: '#0E245C',
            electric: '#2563EB',
            highlight: '#3B82F6',
            icy: '#F6FAFDP0',
            icyBg: '#F6FAFD',
            paleDetail: '#EAF3FF',
            lightBorder: '#D5EAFD',
            textLightMain: '#12213F',
            textLightSecondary: '#52627B',
            textDarkMain: '#FFFFFF',
            textDarkSecondary: '#CDDAF4'
          }
        },
        fontFamily: {
          sans: ['"DM Sans"', 'sans-serif'],
          mono: ['"JetBrains Mono"', 'monospace']
        },
        borderRadius: {
          '2xl': '20px',
          '3xl': '28px',
          '4xl': '32px'
        }
      }
    }
  };
</script>

<style>
  /* Base Style Normalization & Scoped Layout Overrides */
  .extractmails-deliverability-page {
    font-family: 'DM Sans', sans-serif;
    overflow-x: hidden;
    color: #12213F;
    background-color: #071C50;
    width: 100%;
  }
  .glass-navy {
    background: rgba(20, 47, 104, 0.65);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(79, 138, 255, 0.22);
  }
  .glass-navy-subtle {
    background: rgba(14, 36, 92, 0.6);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(147, 197, 253, 0.18);
  }
  .glass-light {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid #D5EAFD;
  }
  .glow-blue {
    box-shadow: 0 0 50px -10px rgba(37, 99, 235, 0.35);
  }
  .glow-blue-lg {
    box-shadow: 0 0 100px -20px rgba(37, 99, 235, 0.45);
  }
  details summary::-webkit-details-marker {
    display: none;
  }
  /* Astra / WordPress Fixed Navbar clearance */
  .deliverability-hero-pad {
    padding-top: 130px !important;
  }
  @media (max-width: 768px) {
    .deliverability-hero-pad {
      padding-top: 110px !important;
    }
  }
</style>

<div class="extractmails-deliverability-page antialiased selection:bg-blue-600 selection:text-white">
<!-- ========================================================= -->
  <!-- 1. DARK HERO — THE INBOX GATEWAY (Includes Sticky Floating Nav) -->
  <!-- ========================================================= -->
  <header class="relative bg-[#071C50] text-white pt-28 md:pt-36 deliverability-hero-pad pb-24 md:pb-32 overflow-hidden border-b border-[#142F68]/60">
    <!-- Ambient Blue Glow Gradients -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[550px] bg-gradient-to-b from-[#2563EB]/20 via-[#142F68]/20 to-transparent blur-3xl pointer-events-none -z-0"></div>
    <div class="absolute right-0 top-1/4 w-[500px] h-[500px] bg-[#3B82F6]/15 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

      <!-- FLOATING NAV BAR -->
      

      <!-- HERO TWO-COLUMN CONTENT -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center pt-2">
        <!-- Text Column (Left) -->
        <div class="lg:col-span-7 flex flex-col items-start pr-0 lg:pr-6">
          <!-- Small Eyebrow -->
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-navy text-xs font-semibold tracking-wide text-blue-300 uppercase mb-6">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            ExtractMails / Email Deliverability
          </div>

          <!-- Exact H1 Headline -->
          <h1 class="text-4xl sm:text-5xl lg:text-[58px] font-bold text-white tracking-tight leading-[1.12] mb-6">
            Email Deliverability designed for a higher inbox success
          </h1>

          <!-- Exact Supporting Paragraph -->
          <p class="text-lg sm:text-xl text-[#CDDAF4] leading-relaxed mb-9 max-w-2xl font-normal">
            Offering improved inbox placement and reduced bounce rates, the Email Deliverability of ExtractMails is optimized with advanced deliverability tools, which are designed to scale up your outreach performance.
          </p>

          <!-- Buttons -->
          <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
            <a href="#cta" class="w-full sm:w-auto text-center px-7 py-3.5 rounded-full bg-[#2563EB] hover:bg-[#1d4ed8] text-white font-semibold text-[16px] shadow-xl shadow-blue-600/40 transition-all hover:-translate-y-0.5">
              Improve Deliverability
            </a>
            <a href="#process" class="w-full sm:w-auto text-center px-7 py-3.5 rounded-full border border-white/60 hover:border-white text-white font-semibold text-[16px] glass-navy hover:bg-white/10 transition-all">
              See How It Works
            </a>
          </div>

          <!-- Floating Badges / Security Check Chips below buttons -->
          <div class="mt-10 pt-8 border-t border-white/10 flex flex-wrap items-center gap-6 text-xs text-[#CDDAF4]/80">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
              <span class="">Automated DNS Alignment</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
              <span class="">Proactive Spam Trap Shield</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
              <span class="">Dedicated Warmup Engines</span>
            </div>
          </div>
        </div>

        <!-- 3D Inbox Gateway Column (Right) -->
        <div class="lg:col-span-5 relative flex justify-center items-center">
          <!-- Ambient spotlight glow -->
          <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/30 to-cyan-400/20 rounded-full blur-2xl transform scale-90 pointer-events-none"></div>

          <!-- Glass Frame Wrapper -->
          <div class="relative w-full max-w-[480px] p-4 glass-navy rounded-3xl border border-blue-400/30 shadow-2xl">
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-b from-[#091e57] to-[#05143a] p-2 flex items-center justify-center">
              <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0KEmxiR7VRjgaj8iPqfUj1yCqaDXANW9RcsUmyhdwMMzs2OEPqvlNLJQyAoKxQg-pkA-ZLu0LqRZt3DaWE-xR1uAkrqTjKMLbFTWnF3yTbyz8jXkYSyLu1xlDbo0ysnJIMHTera9oCCe8RTa0Hb_hkUgHkA8qEBHWBUjS7Q3Pg-T-Am6no67c3titGZrJ92ha-42l2OjXS-21PL8u2JygiuOecvnHVbosWjLGM-8MbO2rv1mNScTo" alt="3D render of futuristic cobalt-blue inbox gateway receiver with authentication arch and message tile" class="w-full h-auto object-contain drop-shadow-[0_20px_40px_rgba(0,0,0,0.6)] transform hover:scale-[1.02] transition-transform duration-500">

              <!-- Separate editable SPF / DKIM / DMARC floating labels -->
              <div class="absolute top-4 left-4 glass-navy px-3 py-1.5 rounded-full border border-blue-300/40 text-[11px] font-mono font-medium text-blue-200 flex items-center gap-1.5 shadow-lg">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                SPF: v=spf1 include:_spf.extractmails.com ~all
              </div>

              <div class="absolute top-16 right-4 glass-navy px-3 py-1.5 rounded-full border border-blue-300/40 text-[11px] font-mono font-medium text-emerald-300 flex items-center gap-1.5 shadow-lg">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                DKIM: 2048-bit Verified
              </div>

              <div class="absolute bottom-5 left-6 glass-navy px-3.5 py-1.5 rounded-full border border-blue-300/40 text-[11px] font-mono font-medium text-white flex items-center gap-1.5 shadow-lg">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                DMARC: p=reject strict
              </div>

              <div class="absolute bottom-5 right-6 glass-navy px-3 py-1 rounded-md border border-white/20 text-[10px] text-blue-200/90 font-mono">
                GATEWAY SECURE
              </div>
            </div>

            <!-- Caption / Visual Stage note -->
            <div class="mt-3 px-2 flex justify-between items-center text-xs text-[#CDDAF4]/70">
              <span class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                Inbox Placement Gateway
              </span>
              <span class="">Hardware-grade Authentication</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ========================================================= -->
  <!-- 2. LIGHT INTRODUCTION -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-white border-b border-[#D5EAFD]" id="introduction">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        <!-- Copy Column (Left) -->
        <div class="lg:col-span-6 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF3FF] text-xs font-bold text-[#2563EB] uppercase tracking-wider">
            Overview &amp; Definition
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold text-[#12213F] tracking-tight leading-tight">
            What is Email Deliverability?
          </h2>
          <!-- Exact introductory paragraph 1 -->
          <p class="text-lg text-[#52627B] leading-relaxed font-normal">
            Email deliverability is the ability to deliver emails directly to subscribers' inboxes, without getting bounced, marked as spam, or lost in the junk folder. It is not just about sending emails, but ensuring that they reach where they are intended to be.
          </p>
          <!-- Exact introductory paragraph 2 -->
          <p class="text-lg text-[#52627B] leading-relaxed font-normal">
            Poor email deliverability results in wasted marketing effort, reduced open rates, lost potential leads, and damaged sender reputation. A professional email deliverability service analyzes, optimizes, and protects your sending domain to ensure maximum deliverability success.
          </p>

          <!-- Core Deliverability Metric Callouts -->
          <div class="grid grid-cols-3 gap-4 pt-4">
            <div class="p-4 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD]">
              <div class="text-2xl font-bold text-[#12213F]">99.4%</div>
              <div class="text-xs font-medium text-[#52627B] mt-1">Average Inbox Placement</div>
            </div>
            <div class="p-4 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD]">
              <div class="text-2xl font-bold text-[#2563EB]">&lt;0.5%</div>
              <div class="text-xs font-medium text-[#52627B] mt-1">Target Bounce Benchmark</div>
            </div>
            <div class="p-4 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD]">
              <div class="text-2xl font-bold text-emerald-600">Zero</div>
              <div class="text-xs font-medium text-[#52627B] mt-1">Blacklist Exposure</div>
            </div>
          </div>
        </div>

        <!-- Photograph Column (Right) -->
        <div class="lg:col-span-6 relative">
          <div class="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-[#D5EAFD] group">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBkuyI7fEn6vD_lMyjyiP8KRgZUueIRmgfTb8Jfy-oOxF4ghspKazBPFZYX4updwjdJKL3n8weCqiL4hTmNGTi0EulaqFix_f34Js5_7dv9YrcZPJGLms5ajUST07xlh2tPT6r0As-vWBT0VnaqEjzdFpgD-ucPGf7eLEM1EYypBI4RmJgzAW421fHttPZNP3bX6fJ4qsf24WuwAToZzB-1lNachAhDGE1HVPa_cj1TIAuNrcNLZgO0" alt="Enterprise email operations engineers collaborating on sender domain DNS analytics" class="w-full h-auto object-cover group-hover:scale-[1.02] transition-transform duration-500">
            
            <!-- Glass overlay containing neutral authentication preview -->
            <div class="absolute bottom-5 left-5 right-5 glass-light p-4 rounded-2xl shadow-xl border border-white/80">
              <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#D5EAFD]">
                <div class="flex items-center gap-2">
                  <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                  <span class="text-xs font-bold text-[#12213F]">DNS Configuration Status</span>
                </div>
                <span class="text-[10px] font-mono uppercase bg-[#EAF3FF] text-[#2563EB] px-2 py-0.5 rounded font-medium">Demonstration Interface</span>
              </div>
              <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-emerald-50 text-emerald-800 rounded py-1 px-2 font-mono font-medium border border-emerald-200">
                  SPF Valid
                </div>
                <div class="bg-emerald-50 text-emerald-800 rounded py-1 px-2 font-mono font-medium border border-emerald-200">
                  DKIM Aligned
                </div>
                <div class="bg-emerald-50 text-emerald-800 rounded py-1 px-2 font-mono font-medium border border-emerald-200">
                  DMARC Enforced
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 3. DARK REAL-WORLD APPLICATIONS -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#071C50] text-white relative border-b border-[#142F68]/70" id="applications">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-navy text-xs font-semibold tracking-wider text-blue-300 uppercase mb-4">
          Practical Use Cases
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-4">
          Real-World Application of Email Deliverability
        </h2>
        <p class="text-lg text-[#CDDAF4]">
          Tailored authentication protocols and adaptive sending cadences for your critical communication channels.
        </p>
      </div>

      <!-- 2x2 Dark Glass Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Card 1 -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 hover:border-blue-400/40 transition-all hover:-translate-y-1 group">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#2563EB] to-[#3B82F6] flex items-center justify-center mb-6 shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white mb-3">Marketing Campaign</h3>
          <p class="text-[17px] text-[#CDDAF4] leading-relaxed">
            Protect massive newsletter and promotional email dispatch from reaching spam boxes, ensuring high open and click-through rates across all recipient providers.
          </p>
        </div>

        <!-- Card 2 -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 hover:border-blue-400/40 transition-all hover:-translate-y-1 group">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#2563EB] to-[#3B82F6] flex items-center justify-center mb-6 shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white mb-3">Cold Email Outreach</h3>
          <p class="text-[17px] text-[#CDDAF4] leading-relaxed">
            Maintain high domain reputation when sending cold emails at scale, preventing your primary business domain from being blocklisted by internet service providers.
          </p>
        </div>

        <!-- Card 3 -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 hover:border-blue-400/40 transition-all hover:-translate-y-1 group">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#2563EB] to-[#3B82F6] flex items-center justify-center mb-6 shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white mb-3">SaaS Onboarding Emails</h3>
          <p class="text-[17px] text-[#CDDAF4] leading-relaxed">
            Guarantee mission-critical activation links, verification codes, and onboarding sequences land in the user's primary inbox in real time without delay.
          </p>
        </div>

        <!-- Card 4 -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 hover:border-blue-400/40 transition-all hover:-translate-y-1 group">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#2563EB] to-[#3B82F6] flex items-center justify-center mb-6 shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.033 8.033 0 01-15.357-2m15.357 2H15"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white mb-3">Re-engagement Campaigns</h3>
          <p class="text-[17px] text-[#CDDAF4] leading-relaxed">
            Safely re-connect with dormant subscribers through segmented warming patterns, keeping reputation healthy even when scrubbing inactive contacts.
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 4. LIGHT PROCESS -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#F6FAFD] border-b border-[#D5EAFD]" id="process">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF3FF] text-xs font-bold text-[#2563EB] uppercase tracking-wider mb-4">
          End-to-End Methodology
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-[#12213F] tracking-tight mb-4">
          How Does Email Deliverability Service Work
        </h2>
        <p class="text-lg text-[#52627B]">
          A four-stage systematic infrastructure alignment to turn cold or degraded domains into high-reputation senders.
        </p>
      </div>

      <!-- 4 Numbered Steps -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
        
        <!-- Step 1 -->
        <div class="glass-light p-7 rounded-3xl relative flex flex-col justify-between hover:border-blue-400 transition-all hover:shadow-xl">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-[#EAF3FF] text-[#2563EB] font-bold text-xl flex items-center justify-center mb-6 border border-blue-200 shadow-sm">
              01
            </div>
            <h3 class="text-xl font-bold text-[#12213F] mb-3">Setup and Authentication</h3>
            <p class="text-[15px] text-[#52627B] leading-relaxed">
              Configure DNS records including SPF, DKIM, DMARC, and BIMI to prove authenticity to inbox service providers and protect your domain against spoofing.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-[#D5EAFD] text-xs font-semibold text-[#2563EB] flex items-center gap-1">
            <span class="">DNS Validation</span> →
          </div>
        </div>

        <!-- Step 2 -->
        <div class="glass-light p-7 rounded-3xl relative flex flex-col justify-between hover:border-blue-400 transition-all hover:shadow-xl">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-[#EAF3FF] text-[#2563EB] font-bold text-xl flex items-center justify-center mb-6 border border-blue-200 shadow-sm">
              02
            </div>
            <h3 class="text-xl font-bold text-[#12213F] mb-3">Optimization</h3>
            <p class="text-[15px] text-[#52627B] leading-relaxed">
              Clean and verify subscriber lists, optimize email headers and HTML content, and establish gradual IP and domain warming schedules to warm up sending volume.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-[#D5EAFD] text-xs font-semibold text-[#2563EB] flex items-center gap-1">
            <span class="">Volume Warmup</span> →
          </div>
        </div>

        <!-- Step 3 -->
        <div class="glass-light p-7 rounded-3xl relative flex flex-col justify-between hover:border-blue-400 transition-all hover:shadow-xl">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-[#EAF3FF] text-[#2563EB] font-bold text-xl flex items-center justify-center mb-6 border border-blue-200 shadow-sm">
              03
            </div>
            <h3 class="text-xl font-bold text-[#12213F] mb-3">Monitor and Protect</h3>
            <p class="text-[15px] text-[#52627B] leading-relaxed">
              Track sender reputation, monitor IP and domain blocklists, analyze bounce codes in real time, and detect spam trap hits before they hurt placement.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-[#D5EAFD] text-xs font-semibold text-[#2563EB] flex items-center gap-1">
            <span class="">Real-time Telemetry</span> →
          </div>
        </div>

        <!-- Step 4 -->
        <div class="glass-light p-7 rounded-3xl relative flex flex-col justify-between hover:border-blue-400 transition-all hover:shadow-xl">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-[#EAF3FF] text-[#2563EB] font-bold text-xl flex items-center justify-center mb-6 border border-blue-200 shadow-sm">
              04
            </div>
            <h3 class="text-xl font-bold text-[#12213F] mb-3">Improve and Scale</h3>
            <p class="text-[15px] text-[#52627B] leading-relaxed">
              Receive ongoing actionable insights, fine-tune sending cadences, maintain pristine reputation, and confidently scale email volume without inbox drops.
            </p>
          </div>
          <div class="mt-6 pt-4 border-t border-[#D5EAFD] text-xs font-semibold text-[#2563EB] flex items-center gap-1">
            <span class="">Enterprise Scale</span> →
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 5. DARK SERVICE FEATURES -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#071C50] text-white border-b border-[#142F68]/70" id="features">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-navy text-xs font-semibold tracking-wider text-blue-300 uppercase mb-4">
          Core Capabilities
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-4">
          What Email Deliverability Service Offers
        </h2>
        <p class="text-lg text-[#CDDAF4]">
          Comprehensive tools engineered to maximize every dimension of your domain's sending health.
        </p>
      </div>

      <!-- Bento Grid for 6 Features -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Feature 1: Wider Panel (Inbox Placement) -->
        <div class="lg:col-span-2 glass-navy p-8 rounded-3xl border border-blue-400/20 relative flex flex-col justify-between overflow-hidden">
          <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-semibold uppercase mb-4">
              Featured Solution
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Optimized Inbox Placement</h3>
            <p class="text-[17px] text-[#CDDAF4] leading-relaxed max-w-xl">
              Advanced algorithms and routing rules verify recipient addresses, align message contents with ISP filters, and guarantee your campaigns bypass spam filters to land directly in primary folders.
            </p>
          </div>

          <!-- Compact illustrative inbox demonstration preview -->
          <div class="mt-8 pt-6 border-t border-white/10 relative z-10">
            <div class="glass-navy-subtle p-4 rounded-2xl border border-white/10">
              <div class="flex items-center justify-between text-xs text-[#CDDAF4] mb-3 font-mono">
                <span class="">INBOX TELEMETRY (SAMPLE PREVIEW)</span>
                <span class="text-emerald-400 font-semibold">100% INBOX LANDING</span>
              </div>
              <div class="space-y-2">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/5 text-sm">
                  <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="font-medium text-white">Google Workspace / Gmail</span>
                  </div>
                  <span class="text-xs text-blue-300 font-mono">Primary Tab ✓</span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/5 border border-white/5 text-sm">
                  <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span class="font-medium text-white">Microsoft 365 / Outlook</span>
                  </div>
                  <span class="text-xs text-blue-300 font-mono">Focused Inbox ✓</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Feature 2: Bounce & Spam Management -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-600/30 text-blue-300 flex items-center justify-center mb-6 border border-blue-400/30">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Bounce &amp; Spam Management</h3>
            <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
              Automated bounce classification filters out hard and soft bounces, detects spam traps instantly, and cleans recipient lists to safeguard your sender score.
            </p>
          </div>
          <div class="mt-6 text-xs text-blue-300 font-mono flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
            Auto Suppression Active
          </div>
        </div>

        <!-- Feature 3: Email Authentication -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-600/30 text-blue-300 flex items-center justify-center mb-6 border border-blue-400/30">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Email Authentication</h3>
            <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
              Complete setup and continuous validation for SPF, DKIM, and DMARC enforcement to satisfy strict modern requirements from major email providers.
            </p>
          </div>
          <div class="mt-6 flex gap-2">
            <span class="px-2.5 py-1 rounded bg-blue-900/60 border border-blue-400/30 text-xs font-mono text-blue-200">SPF</span>
            <span class="px-2.5 py-1 rounded bg-blue-900/60 border border-blue-400/30 text-xs font-mono text-blue-200">DKIM</span>
            <span class="px-2.5 py-1 rounded bg-blue-900/60 border border-blue-400/30 text-xs font-mono text-blue-200">DMARC</span>
          </div>
        </div>

        <!-- Feature 4: IP and Domain Monitoring -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-600/30 text-blue-300 flex items-center justify-center mb-6 border border-blue-400/30">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">IP and Domain Monitoring</h3>
            <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
              24/7 scanning across 100+ global RBLs and blacklist databases with instant alerts, reputation history logs, and automated remediation recommendations.
            </p>
          </div>
          <div class="mt-6 text-xs text-emerald-400 font-mono">
            ● 100+ RBLs Monitored 24/7
          </div>
        </div>

        <!-- Feature 5: Supports Integration -->
        <div class="glass-navy p-8 rounded-3xl border border-blue-400/20 flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-2xl bg-blue-600/30 text-blue-300 flex items-center justify-center mb-6 border border-blue-400/30">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">Supports Integration</h3>
            <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
              Seamlessly connects with your existing CRMs, cold email software, custom mail servers, and API pipelines via robust webhook and SMTP bridges.
            </p>
          </div>
          <!-- Generic connected workflow nodes -->
          <div class="mt-6 flex items-center gap-2 text-xs font-mono text-[#CDDAF4]">
            <span class="px-2 py-1 rounded bg-white/10">App</span>
            <span class="">→</span>
            <span class="px-2 py-1 rounded bg-blue-600/40 text-blue-200">Relay</span>
            <span class="">→</span>
            <span class="px-2 py-1 rounded bg-white/10">Inbox</span>
          </div>
        </div>

        <!-- Feature 6: Reporting & Analytics (Spans 3 cols or 1 col) -->
        <div class="lg:col-span-3 glass-navy p-8 rounded-3xl border border-blue-400/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div class="max-w-2xl">
            <h3 class="text-2xl font-bold text-white mb-2">Reporting &amp; Analytics</h3>
            <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
              Granular analytics dashboard detailing placement rates per provider, bounce reasons, spam complaints, and domain reputation changes over time.
            </p>
          </div>
          <div class="flex items-center gap-4 text-xs font-mono text-blue-200 bg-white/5 px-4 py-3 rounded-2xl border border-white/10 shrink-0">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Deliverability: Optimal</span>
            <span class="text-white/20">|</span>
            <span class="">Spam Score: 0.01%</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 6. LIGHT AUDIENCE SECTION -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-white border-b border-[#D5EAFD]" id="audience">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        <!-- Introductory Column (Left) -->
        <div class="lg:col-span-5 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF3FF] text-xs font-bold text-[#2563EB] uppercase tracking-wider">
            Who Can Benefit
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold text-[#12213F] tracking-tight leading-tight">
            Built for any team relying on critical email delivery
          </h2>
          <!-- Exact introductory sentence -->
          <p class="text-lg text-[#52627B] leading-relaxed">
            Whether you are running high-volume marketing broadcasts, targeted sales sequences, or transactional notifications, ExtractMails provides the deliverability safeguards you need.
          </p>
          <div class="pt-2">
            <a href="#cta" class="inline-flex items-center gap-2 text-[#2563EB] font-bold text-base hover:gap-3 transition-all">
              <span class="">Find the right deliverability plan</span>
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
          </div>
        </div>

        <!-- 8 Compact Audience Tiles (Right) -->
        <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-4">
          
          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">Sales Teams</h4>
            <p class="text-xs text-[#52627B] mt-1">High cold response</p>
          </div>

          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">Marketers</h4>
            <p class="text-xs text-[#52627B] mt-1">Newsletter success</p>
          </div>

          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">SaaS Companies</h4>
            <p class="text-xs text-[#52627B] mt-1">Instant onboarding</p>
          </div>

          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">E-Commerce Businesses</h4>
            <p class="text-xs text-[#52627B] mt-1">Transactional order alerts</p>
          </div>

          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">Agencies</h4>
            <p class="text-xs text-[#52627B] mt-1">Multi-client domains</p>
          </div>

          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">Recruiters</h4>
            <p class="text-xs text-[#52627B] mt-1">Direct talent contact</p>
          </div>

          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">Startups</h4>
            <p class="text-xs text-[#52627B] mt-1">Rapid scale-up</p>
          </div>

          <div class="p-5 rounded-2xl bg-[#F6FAFD] border border-[#D5EAFD] hover:border-blue-400 transition-all hover:-translate-y-1 group">
            <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h4 class="font-bold text-[#12213F] text-base">Enterprises</h4>
            <p class="text-xs text-[#52627B] mt-1">SLA guarantee</p>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 7. DARK REASONS TO CHOOSE EXTRACTMAILS -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#071C50] text-white border-b border-[#142F68]/70" id="why-choose">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        
        <!-- Copy Column (Left) -->
        <div class="lg:col-span-7 space-y-8">
          <div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-navy text-xs font-semibold tracking-wider text-blue-300 uppercase mb-4">
              The ExtractMails Advantage
            </div>
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-4">
              Why Choose Email Deliverability of ExtractMails
            </h2>
            <p class="text-lg text-[#CDDAF4]">
              Engineered with enterprise security rigor, smart feedback loops, and deployment flexibility.
            </p>
          </div>

          <div class="space-y-6">
            <!-- Reason 1 -->
            <div class="glass-navy p-6 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 border border-blue-400/30">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path></svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white mb-1.5">High Inbox Placement</h3>
                <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
                  Proven routing and authentication techniques that maximize inbox delivery while strictly avoiding spam filters.
                </p>
              </div>
            </div>

            <!-- Reason 2 -->
            <div class="glass-navy p-6 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 border border-blue-400/30">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white mb-1.5">Smart Reputation Monitoring</h3>
                <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
                  Continuous tracking of domain and IP reputation to catch deliverability issues before they impact outreach campaigns.
                </p>
              </div>
            </div>

            <!-- Reason 3 -->
            <div class="glass-navy p-6 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 border border-blue-400/30">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white mb-1.5">Compliance-first Approach</h3>
                <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
                  Fully adheres to CAN-SPAM, GDPR, and anti-spam protocols to keep your email operations completely compliant.
                </p>
              </div>
            </div>

            <!-- Reason 4 -->
            <div class="glass-navy p-6 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
              <div class="w-10 h-10 rounded-xl bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 border border-blue-400/30">
                <svg class="w-5 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-white mb-1.5">Cloud or On-premises Hosting</h3>
                <p class="text-[16px] text-[#CDDAF4] leading-relaxed">
                  Available as both managed cloud service and self-hosted setup to meet your infrastructure and data privacy requirements.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- 3D Reputation Monitoring Sculpture Column (Right) -->
        <div class="lg:col-span-5 relative flex justify-center items-center">
          <!-- Ambient spotlight glow -->
          <div class="absolute inset-0 bg-blue-600/20 rounded-full blur-3xl transform scale-90 pointer-events-none"></div>

          <!-- Glass Frame Wrapper -->
          <div class="relative w-full max-w-[460px] p-4 glass-navy rounded-3xl border border-blue-400/30 shadow-2xl">
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-b from-[#091e57] to-[#05143a] p-3 flex items-center justify-center">
              <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYszAqiz0Ihtr3Jo7yJOzOnIH2xSrHhvLEQQkR2S4aPyQo8ny4_1TZ7V5I67PMY-7i4ifGqJYVd1-8-SZ1_Rqizais1KZZTBd30lU5LsXfofGZrd1Bq38Y283LrrGqbIAaoHqOpw2cn9jpJy7IqcWxxXhdvNjqECxVnVucBe2aceYq33aGMNprzVArB3x34uPiZN0UU3JLrEN_H93-PPAeBoxLx1XF1TcHOTSsfPZdTfXz3tHmAu29" alt="3D render of futuristic reputation monitoring circular gauge sculpture with white porcelain domain node" class="w-full h-auto object-contain drop-shadow-[0_20px_40px_rgba(0,0,0,0.6)] transform hover:scale-[1.02] transition-transform duration-500">

              <!-- Overlay physical indicator labels -->
              <div class="absolute top-4 left-4 glass-navy px-3 py-1.5 rounded-full border border-blue-300/40 text-[11px] font-mono text-blue-200">
                SENSOR A: FEEDBACK LOOP ACTIVE
              </div>
              <div class="absolute bottom-4 right-4 glass-navy px-3 py-1.5 rounded-full border border-blue-300/40 text-[11px] font-mono text-emerald-300">
                DOMAIN HEALTH: STABLE
              </div>
            </div>

            <div class="mt-3 px-2 flex justify-between items-center text-xs text-[#CDDAF4]/70">
              <span class="">Domain Reputation Core</span>
              <span class="">Hardware-grade Observation</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 8. LIGHT KEY BENEFITS -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#F6FAFD] border-b border-[#D5EAFD]" id="benefits">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF3FF] text-xs font-bold text-[#2563EB] uppercase tracking-wider mb-4">
          Strategic Value
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold text-[#12213F] tracking-tight mb-4">
          Key Benefits of Professional Email Deliverability
        </h2>
        <p class="text-lg text-[#52627B]">
          Deliverability is the foundation that amplifies every email interaction and revenue stream.
        </p>
      </div>

      <!-- Five Benefit Rows -->
      <div class="max-w-4xl mx-auto space-y-4">
        
        <!-- Benefit 1 -->
        <div class="glass-light p-6 rounded-2xl flex items-center gap-5 border border-[#D5EAFD] hover:border-blue-400 transition-all hover:shadow-md">
          <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
          </div>
          <div class="text-[17px] font-medium text-[#12213F]">
            Higher email delivery rates, ensuring emails reach the recipient's main inbox.
          </div>
        </div>

        <!-- Benefit 2 -->
        <div class="glass-light p-6 rounded-2xl flex items-center gap-5 border border-[#D5EAFD] hover:border-blue-400 transition-all hover:shadow-md">
          <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
          </div>
          <div class="text-[17px] font-medium text-[#12213F]">
            Reduced bounce and spam complaint rates to protect your sender credibility.
          </div>
        </div>

        <!-- Benefit 3 -->
        <div class="glass-light p-6 rounded-2xl flex items-center gap-5 border border-[#D5EAFD] hover:border-blue-400 transition-all hover:shadow-md">
          <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
          </div>
          <div class="text-[17px] font-medium text-[#12213F]">
            Increased open, click, and engagement rates across all marketing and sales emails.
          </div>
        </div>

        <!-- Benefit 4 -->
        <div class="glass-light p-6 rounded-2xl flex items-center gap-5 border border-[#D5EAFD] hover:border-blue-400 transition-all hover:shadow-md">
          <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
          </div>
          <div class="text-[17px] font-medium text-[#12213F]">
            Long-term protection of domain and IP reputation across major mail providers.
          </div>
        </div>

        <!-- Benefit 5 -->
        <div class="glass-light p-6 rounded-2xl flex items-center gap-5 border border-[#D5EAFD] hover:border-blue-400 transition-all hover:shadow-md">
          <div class="w-10 h-10 rounded-xl bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
          </div>
          <div class="text-[17px] font-medium text-[#12213F]">
            Maximized ROI on your outreach, email marketing, and communication efforts.
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 9. DARK COMPARISON SECTION -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#071C50] text-white border-b border-[#142F68]/70" id="comparison">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        
        <!-- Left Column -->
        <div class="lg:col-span-5 space-y-6">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-navy text-xs font-semibold tracking-wider text-blue-300 uppercase">
            Comparative Analysis
          </div>
          <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white leading-tight">
            Compared to Other Free or Manual Tools
          </h2>
          <!-- Exact introductory sentence -->
          <p class="text-lg text-[#CDDAF4] leading-relaxed">
            Free online tools only offer basic DNS lookups or one-time checks. ExtractMails delivers a complete, continuous deliverability infrastructure built for teams that cannot afford deliverability failures.
          </p>

          <!-- Restrained visual showing separate monitoring tasks becoming one organized workflow -->
          <div class="glass-navy p-6 rounded-2xl border border-blue-400/20 space-y-4">
            <div class="text-xs font-mono text-blue-300 uppercase tracking-wider">Unified Orchestration</div>
            <div class="flex items-center justify-between text-xs font-mono text-[#CDDAF4]">
              <span class="px-2.5 py-1.5 rounded bg-red-900/30 text-red-300 border border-red-500/30">Manual Checks (Fragmented)</span>
              <span class="">➔</span>
              <span class="px-2.5 py-1.5 rounded bg-emerald-900/40 text-emerald-300 border border-emerald-500/40">ExtractMails Auto-Shield</span>
            </div>
            <p class="text-xs text-[#CDDAF4]/70">
              Replaces scattered test scripts, third-party blocklist lookups, and manual DNS editing with unified automated deliverability intelligence.
            </p>
          </div>
        </div>

        <!-- Right Column: 5 Aligned Advantage Rows -->
        <div class="lg:col-span-7 space-y-4">
          
          <!-- Advantage 1 -->
          <div class="glass-navy p-5 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 mt-0.5 border border-blue-400/30">
              <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
              <div class="text-lg font-bold text-white mb-1">Continuous Real-Time Protection</div>
              <div class="text-[16px] text-[#CDDAF4] leading-relaxed">
                Ongoing monitoring instead of snapshot-in-time checks, alerting you immediately to any reputation degradation.
              </div>
            </div>
          </div>

          <!-- Advantage 2 -->
          <div class="glass-navy p-5 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 mt-0.5 border border-blue-400/30">
              <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
              <div class="text-lg font-bold text-white mb-1">Actionable Remediation</div>
              <div class="text-[16px] text-[#CDDAF4] leading-relaxed">
                Specific, step-by-step guidance to resolve issues rather than vague diagnostic scores with no clear fix.
              </div>
            </div>
          </div>

          <!-- Advantage 3 -->
          <div class="glass-navy p-5 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 mt-0.5 border border-blue-400/30">
              <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
              <div class="text-lg font-bold text-white mb-1">Full Protocol Suite</div>
              <div class="text-[16px] text-[#CDDAF4] leading-relaxed">
                Deep SPF, DKIM, DMARC, and BIMI implementation covering every modern mail security standard in one place.
              </div>
            </div>
          </div>

          <!-- Advantage 4 -->
          <div class="glass-navy p-5 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 mt-0.5 border border-blue-400/30">
              <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
              <div class="text-lg font-bold text-white mb-1">Scale-Ready Architecture</div>
              <div class="text-[16px] text-[#CDDAF4] leading-relaxed">
                Engineered for teams sending thousands or millions of emails monthly without performance bottlenecks.
              </div>
            </div>
          </div>

          <!-- Advantage 5 -->
          <div class="glass-navy p-5 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-blue-600/30 text-blue-300 flex items-center justify-center shrink-0 mt-0.5 border border-blue-400/30">
              <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
              <div class="text-lg font-bold text-white mb-1">Dedicated Deliverability Support</div>
              <div class="text-[16px] text-[#CDDAF4] leading-relaxed">
                Direct access to email infrastructure specialists who understand ISP relations and sender compliance.
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 10. LIGHT SECURITY AND RELIABILITY -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-white border-b border-[#D5EAFD]" id="security">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
        
        <!-- Copy Column (Left) -->
        <div class="lg:col-span-6 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF3FF] text-xs font-bold text-[#2563EB] uppercase tracking-wider">
            Infrastructure &amp; Compliance
          </div>
          <!-- Exact Heading -->
          <h2 class="text-3xl sm:text-4xl font-bold text-[#12213F] tracking-tight leading-tight">
            Send Securely. Reach Reliably.
          </h2>
          <!-- Exact Supplied Introduction -->
          <p class="text-lg text-[#52627B] leading-relaxed">
            Enterprise-grade infrastructure built to safeguard your sending domains, protect recipient data, and ensure uncompromised message delivery.
          </p>

          <!-- 5 Security Points -->
          <div class="space-y-4 pt-2">
            
            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0 mt-1 font-bold text-xs">
                ✓
              </div>
              <div>
                <span class="font-bold text-[#12213F] text-base">End-to-End Encryption:</span>
                <span class="text-[#52627B] text-base ml-1">TLS 1.3 enforced across all mail transit channels to secure in-flight email data.</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0 mt-1 font-bold text-xs">
                ✓
              </div>
              <div>
                <span class="font-bold text-[#12213F] text-base">Strict Privacy Compliance:</span>
                <span class="text-[#52627B] text-base ml-1">Full compliance with GDPR, CCPA, and international data protection standards.</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0 mt-1 font-bold text-xs">
                ✓
              </div>
              <div>
                <span class="font-bold text-[#12213F] text-base">Isolated IP Pools:</span>
                <span class="text-[#52627B] text-base ml-1">Dedicated IP options ensuring bad sender neighbors never harm your domain score.</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0 mt-1 font-bold text-xs">
                ✓
              </div>
              <div>
                <span class="font-bold text-[#12213F] text-base">Automated Spam Trap Detection:</span>
                <span class="text-[#52627B] text-base ml-1">Early warning detection protecting sending lists from toxic trap addresses.</span>
              </div>
            </div>

            <div class="flex items-start gap-3.5">
              <div class="w-6 h-6 rounded-full bg-[#EAF3FF] text-[#2563EB] flex items-center justify-center shrink-0 mt-1 font-bold text-xs">
                ✓
              </div>
              <div>
                <span class="font-bold text-[#12213F] text-base">99.9% Service Availability SLA:</span>
                <span class="text-[#52627B] text-base ml-1">High-availability redundant delivery nodes guaranteeing continuous uptime.</span>
              </div>
            </div>

          </div>
        </div>

        <!-- Photograph Column (Right): Data center aisle -->
        <div class="lg:col-span-6 relative">
          <div class="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-[#D5EAFD] group">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCLKl2KpoVkeCgs4UOjEvpijbUTnBmb9BQGFPWnYYjip5eISDuicbeo-QmtF4ZeABlaqfAX9ImuA8XXHkniUxc-hRNgwnuzSAUKFUsXfNhrrKN3clFdEEQSxXMUKINolZNqlMsnTGdlkxxjMgGQoOGsi6_XTXgl1vRzpwVFNAOZwneGo3W6F4xXVhws8eO4xNLh-p_WH87tBHe-n4BeczMZgWq6io6pOC6KtXKb3jgWc6KRf9SBT8EL" alt="Enterprise data center server room aisle illustrating secure infrastructure" class="w-full h-auto object-cover group-hover:scale-[1.02] transition-transform duration-500">
            
            <!-- Glass overlay identifying illustrative infrastructure -->
            <div class="absolute bottom-5 left-5 right-5 glass-light p-4 rounded-2xl shadow-xl border border-white/80">
              <div class="flex items-center justify-between text-xs text-[#12213F]">
                <div class="flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                  <span class="font-bold">Illustrative Infrastructure</span>
                </div>
                <span class="font-mono text-[11px] text-[#52627B]">Global Redundancy</span>
              </div>
              <p class="text-xs text-[#52627B] mt-1.5">
                Multi-region fault-tolerant server infrastructure powering ExtractMails email delivery protocols.
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 11. DARK FAQ -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#071C50] text-white border-b border-[#142F68]/70" id="faq">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-navy text-xs font-semibold tracking-wider text-blue-300 uppercase mb-4">
          Got Questions?
        </div>
        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-4">
          Frequently Asked Questions
        </h2>
        <p class="text-lg text-[#CDDAF4]">
          Everything you need to know about setting up and scaling professional email deliverability with ExtractMails.
        </p>
      </div>

      <!-- FAQ Accordion (880px wide) -->
      <div class="max-w-[880px] mx-auto space-y-4">
        
        <!-- FAQ 1 (Expanded by default) -->
        <details class="glass-navy rounded-2xl border border-blue-400/20 group overflow-hidden" open="">
          <summary class="p-6 cursor-pointer flex items-center justify-between text-left text-lg font-bold text-white select-none">
            <span class="">What causes emails to go to spam?</span>
            <span class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center text-blue-300 group-open:rotate-45 transition-transform duration-300 shrink-0 text-xl font-light">
              +
            </span>
          </summary>
          <div class="px-6 pb-6 text-[16px] text-[#CDDAF4] leading-relaxed border-t border-white/5 pt-4">
            Emails end up in spam primarily due to missing or misconfigured authentication records (SPF, DKIM, DMARC), poor sender reputation, sudden spikes in sending volume, high bounce rates, spam complaints from recipients, and trigger words or suspicious formatting within the email content.
          </div>
        </details>

        <!-- FAQ 2 -->
        <details class="glass-navy rounded-2xl border border-blue-400/20 group overflow-hidden">
          <summary class="p-6 cursor-pointer flex items-center justify-between text-left text-lg font-bold text-white select-none">
            <span class="">How long does it take to improve email deliverability?</span>
            <span class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center text-blue-300 group-open:rotate-45 transition-transform duration-300 shrink-0 text-xl font-light">
              +
            </span>
          </summary>
          <div class="px-6 pb-6 text-[16px] text-[#CDDAF4] leading-relaxed border-t border-white/5 pt-4">
            Initial authentication fixes (DNS setup) take effect within 24 to 48 hours. Meaningful sender reputation repair and domain warming typically show measurable improvements within 2 to 4 weeks of consistent, optimized sending practices.
          </div>
        </details>

        <!-- FAQ 3 -->
        <details class="glass-navy rounded-2xl border border-blue-400/20 group overflow-hidden">
          <summary class="p-6 cursor-pointer flex items-center justify-between text-left text-lg font-bold text-white select-none">
            <span class="">What is the difference between SPF, DKIM, and DMARC?</span>
            <span class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center text-blue-300 group-open:rotate-45 transition-transform duration-300 shrink-0 text-xl font-light">
              +
            </span>
          </summary>
          <div class="px-6 pb-6 text-[16px] text-[#CDDAF4] leading-relaxed border-t border-white/5 pt-4">
            SPF specifies which mail servers are authorized to send email on behalf of your domain. DKIM adds a cryptographic digital signature to verify that emails were not altered in transit. DMARC uses both SPF and DKIM to tell recipient servers what to do (monitor, quarantine, or reject) if authentication fails.
          </div>
        </details>

        <!-- FAQ 4 -->
        <details class="glass-navy rounded-2xl border border-blue-400/20 group overflow-hidden">
          <summary class="p-6 cursor-pointer flex items-center justify-between text-left text-lg font-bold text-white select-none">
            <span class="">Can I use ExtractMails Deliverability with my existing email tool?</span>
            <span class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center text-blue-300 group-open:rotate-45 transition-transform duration-300 shrink-0 text-xl font-light">
              +
            </span>
          </summary>
          <div class="px-6 pb-6 text-[16px] text-[#CDDAF4] leading-relaxed border-t border-white/5 pt-4">
            Yes. ExtractMails delivers standalone deliverability services, SMTP relays, and API integrations that work seamlessly with major email service providers, cold outreach platforms, and marketing automation tools.
          </div>
        </details>

        <!-- FAQ 5 -->
        <details class="glass-navy rounded-2xl border border-blue-400/20 group overflow-hidden">
          <summary class="p-6 cursor-pointer flex items-center justify-between text-left text-lg font-bold text-white select-none">
            <span class="">Do I need a dedicated IP address?</span>
            <span class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center text-blue-300 group-open:rotate-45 transition-transform duration-300 shrink-0 text-xl font-light">
              +
            </span>
          </summary>
          <div class="px-6 pb-6 text-[16px] text-[#CDDAF4] leading-relaxed border-t border-white/5 pt-4">
            A dedicated IP is recommended if you send more than 100,000 emails per month, as it gives you complete control over your sender reputation. For lower volumes, our carefully managed high-reputation shared IP pools are typically more effective and cost-efficient.
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 12. LIGHT FINAL CTA -->
  <!-- ========================================================= -->
  <section class="py-24 md:py-28 bg-[#F6FAFD] border-b border-[#D5EAFD]" id="cta">
    <div class="max-w-[1240px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto glass-light p-10 md:p-14 rounded-4xl border-2 border-[#D5EAFD] text-center shadow-xl">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#EAF3FF] text-xs font-bold text-[#2563EB] uppercase tracking-wider mb-5">
          Get Started Today
        </div>
        <!-- Exact Heading -->
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-[#12213F] tracking-tight mb-8">
          Start Improving Your Email Delivery
        </h2>
        
        <!-- Two CTA Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-4">
          <a href="<?php echo esc_url(home_url('/pricing/')); ?>" class="px-8 py-4 rounded-full bg-[#2563EB] hover:bg-[#1d4ed8] text-white font-semibold text-base shadow-xl shadow-blue-600/30 transition-all hover:-translate-y-0.5">Improve Deliverability
          </a>
          <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="px-8 py-4 rounded-full border border-[#D5EAFD] hover:border-blue-400 text-[#12213F] font-semibold text-base bg-white hover:bg-slate-50 transition-all">Talk to Sales
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================================================= -->
  <!-- 13. DARK FOOTER -->
  <!-- ========================================================= -->
</div>

<?php
// Load WordPress Theme Footer
if (function_exists('get_footer')) {
    get_footer();
} else {
    // Standalone Closing Tags Fallback
    ?>
    </body>
    </html>
    <?php
}
?>
