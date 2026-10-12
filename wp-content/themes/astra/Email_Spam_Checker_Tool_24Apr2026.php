<?php
/*
Template Name: Reverse Email Lookup Tool  */
add_action(
    'wp_head',
    static function () {
        ?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap');

  .extractmail-spam-editor {
    min-height: 560px;
    outline: none;
    white-space: pre-wrap;
    word-break: break-word;
  }

  .extractmail-spam-editor.is-empty::before {
    content: attr(data-placeholder);
    color: #94a3b8;
  }

  .extractmail-spam-tag {
    display: inline;
    padding: 0 2px;
    border-radius: 4px;
    box-decoration-break: clone;
    -webkit-box-decoration-break: clone;
  }

  .extractmail-spam-tag.urgency {
    background: #ffd8e1;
  }

  .extractmail-spam-tag.shady {
    background: #ffc3e1;
  }

  .extractmail-spam-tag.overpromise {
    background: #ffd39c;
  }

  .extractmail-spam-tag.unnatural {
    background: #e7defe;
  }

  .extractmail-faq-content {
    transition: max-height 0.3s ease;
  }

  .extractmail-glass-card {
    backdrop-filter: blur(14px);
  }

  #extractmail-spam-checker-section {
    scroll-margin-top: 110px;
  }

  .extractmail-try-now-link,
  .extractmail-try-now-link:hover,
  .extractmail-try-now-link:focus,
  .extractmail-try-now-link:active {
    background: linear-gradient(to right, #1d4ed8, #4ade80) !important;
    color: #ffffff !important;
    opacity: 1 !important;
    text-decoration: none !important;
  }
</style>
<?php
    },
    1
);

get_header();
?>

<section class="py-12 md:py-10">
  <div class="max-w-[1500px] mx-auto px-4 md:px-6">

    <!-- HERO -->
    <div class="grid md:grid-cols-2 gap-8 md:gap-4 items-center">

      <!-- LEFT -->
      <div>

        <!-- Badge -->

        <span
          class="inline-block  py-1 text-[14px] md:text-[18px] lg:text-[22px] text-[#8eb7f3] rounded-full mt-12 lg:mt-4 md:mb-8">

          <a href="/" class="hover:underline">Home</a> /
          <a href="/tools" class="hover:underline">Tools</a> /
          <a href="/tools/reverse-email-lookup" class="hover:underline">Email Spam Checker Tool </a>

        </span>


        <!-- Heading -->
        <h1 class="text-[28px] sm:text-[34px] md:text-[42px] lg:text-[49px] leading-snug md:leading-tight font-semibold 
        bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
        bg-clip-text text-transparent mb-4 md:mb-6">
          Email Spam Checker Tool 
        </h1>

        <!-- Description -->
        <p
          class="text-gray-600 text-[14px] sm:text-[16px] md:text-[18px] lg:text-[20px] leading-relaxed max-w-xl md:mb-8">
          Check the spam score of your email instantly, with the Email Spam Checker tool, which identifies spam triggers
          and helps improve deliverability. 
        </p>

        <a href="#extractmail-spam-checker-section" class="extractmail-try-now-link inline-block px-6 md:px-12 py-1 text-[14px] md:text-[18px] lg:text-[22px] text-white rounded-full mt-12 lg:mt-4 md:mb-8
          bg-gradient-to-r from-blue-700 to-green-400">
          Try Now
        </a>

      </div>

      <!-- RIGHT IMAGE -->
      <div class="flex justify-center md:justify-end mt-6 md:mt-6">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-62-3.png" alt="Hero"
          class="w-full max-w-[300px] sm:max-w-[400px] md:max-w-[480px] lg:max-w-[520px] object-contain">
      </div>

    </div>

    <!-- SECOND SECTION -->


  </div>
</section>

<main id="extractmail-spam-checker-section" class=" ">
  <section class="mx-auto max-w-7xl">

    <div
      class="mx-auto mt-10 grid max-w-6xl overflow-hidden rounded-[18px] border border-line bg-white shadow-panel lg:grid-cols-[1.95fr_minmax(320px,1fr)]">
      <div class="border-b border-line bg-white p-7 lg:border-b-0 lg:border-r">
        <div id="extractmail-spam-editor" class="extractmail-spam-editor text-[18px] leading-[1.72] text-slate-900"
          contenteditable="true" spellcheck="false"
          data-placeholder="Paste your email here to check it for risky spam words."></div>
      </div>

      <div class="bg-surface p-7">
        <div class="space-y-2">
          <div class="flex items-baseline justify-between gap-4 text-[18px] leading-7 text-slate-500">
            <span>Overall score:</span>
            <strong id="extractmail-spam-status" class="text-[18px] font-extrabold text-rose-600">Poor</strong>
          </div>
          <div class="flex items-baseline justify-between gap-4 text-[18px] leading-7 text-slate-500">
            <span>Words:</span>
            <strong id="extractmail-spam-words" class="text-[18px] font-extrabold text-ink">0</strong>
          </div>
          <div class="flex items-baseline justify-between gap-4 text-[18px] leading-7 text-slate-500">
            <span>Read time:</span>
            <strong id="extractmail-spam-read-time" class="text-[18px] font-extrabold text-ink">a few seconds</strong>
          </div>
        </div>

        <div class="mt-8 space-y-3">
          <div id="extractmail-spam-card-urgency"
            class="rounded-xl border-l-[3px] border-[#ff7a8f] bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <div class="flex items-center gap-3">
                  <span class="text-lg">🚨</span>
                  <span class="text-[17px] font-extrabold text-ink">Urgency</span>
                </div>
              </div>
              <div id="extractmail-spam-count-urgency" class="text-[17px] font-extrabold text-ink">0</div>
            </div>
          </div>

          <div id="extractmail-spam-card-shady"
            class="rounded-xl border-l-[3px] border-[#f472b6] bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <div class="flex items-center gap-3">
                  <span class="text-lg">🚫</span>
                  <span class="text-[17px] font-extrabold text-ink">Shady</span>
                </div>
              </div>
              <div id="extractmail-spam-count-shady" class="text-[17px] font-extrabold text-ink">0</div>
            </div>
          </div>

          <div id="extractmail-spam-card-overpromise"
            class="rounded-xl border-l-[3px] border-[#ffb347] bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <div class="flex items-center gap-3">
                  <span class="text-lg">🤩</span>
                  <span class="text-[17px] font-extrabold text-ink">Overpromise</span>
                </div>
              </div>
              <div id="extractmail-spam-count-overpromise" class="text-[17px] font-extrabold text-ink">0</div>
            </div>
          </div>

          <div id="extractmail-spam-card-unnatural"
            class="rounded-xl border-l-[3px] border-[#c4b5fd] bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <div class="flex items-center gap-3">
                  <span class="text-lg">💬</span>
                  <span class="text-[17px] font-extrabold text-ink">Unnatural</span>
                </div>
              </div>
              <div id="extractmail-spam-count-unnatural" class="text-[17px] font-extrabold text-ink">0</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

<section class="bg-[#f4f4f4] py-20 px-4">
  <div class="max-w-[1200px] mx-auto text-center">

    <!-- Main Heading -->
    <h1 class="text-[28px] md:text-[38px] font-medium text-gray-900 leading-snug mb-6">
      Improve Deliverability and Analyze Content, Links, and Setup of Your Email 
    </h1>

    <!-- Sub Text -->
    <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed mb-14">
      Analyze your email content and improve deliverability before sending the email, with the Email Spam Checker tool
      of ExtractMails, that helps in detecting spam triggers and land your email in the inbox of the recipient every
      time. 
    </p>

    <!-- Section Heading -->
    <h2 class="text-[24px] md:text-[37px] font-medium text-gray-900 mb-5">
      What is Email Spam Checker 
    </h2>

    <!-- Section Text -->
    <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed">
      The Email Spam Checker is a tool that analyzes the content of your email, subject line, and technical setup
      to determine whether the email could be flagged as spam. The Email Spam Checker identifies various possible
      risk elements from your email, which include spam-triggering words, authentication issues, or poor formatting, and
      helps optimize your email message better before sending. 
    </p>

  </div>
</section>


<section class=" py-10">
  <div class="max-w-[1400px] mx-auto px-6 text-center">

    <!-- Heading -->
    <h2
      class="text-[36px] font-semibold  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent mb-4 leading-snug">
      Key Features of Email <br> Spam Checker 
    </h2>

    <p class="text-gray-600 text-[14px] md:text-[15px] leading-relaxed mb-10">
      The Email Spam Checker by ExtractMails is designed to offer a complete and pre-send analysis of your email. The
      tool identifies all possible risks in email content, subject line, and technical setup. The tool helps to fix
      these issues and ensures that your email lands in the inbox of your recipient rather than in their spam folder. 
    </p>

    <!-- Grid -->
    <div class="grid md:grid-cols-4">

      <!-- ITEM 1 -->
      <div class="px-6 text-center relative">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/instant-messaging-1.png"
          class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Instant Spam Score Analyses 
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The Email Spam Checker by ExtractMails offers a real-time spam score as soon as you enter the content of your
          email. The spam score indicated how likely your email is to be flagged by the spam filters.  
        </p>

        <!-- Divider -->
        <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
      </div>

      <!-- ITEM 2 -->
      <div class="px-6 text-center relative">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/email-7-1.png" class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Advanced Spam Trigger Detection 
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The Email Spam Checker completely scans the subject line and body of your email for various common words and
          phrases that can be flagged by the spam filters. 
        </p>

        <!-- Divider -->
        <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
      </div>

      <!-- ITEM 3 -->
      <div class="px-6 text-center relative">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/insight-1.png" class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Content Quality Insight 
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The spam checker tool also evaluates the overall quality of your email content, along with checking for
          clarity, tone, structure, and balance between links & texts.  
        </p>

        <!-- Divider -->
        <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
      </div>

      <!-- ITEM 4 -->
      <div class="px-6 text-center">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/checkmark-1.png" class="w-20 mx-auto mb-6">

        <h3 class="font-bold text-[18px] text-gray-900 mb-3">
          Email Authentication Verification 
        </h3>

        <p class="text-gray-600 text-[15px] leading-relaxed">
          The Email Spam Checker tool verifies various key protocols, like SPF, DKIM, and DMARC policies, to ensure
          these are correctly configured with email providers and help build the sender's domain trust. 
        </p>
      </div>

    </div>

  </div>
</section>


<section class="relative overflow-hidden max-w-[1500px] mx-auto my-10 py-16 rounded-[4]">
  <!-- Background Gradient -->
  <!-- Background Gradient -->


  <!-- Rectangles -->

  <div class="absolute w-[687px] h-[272px] left-[24px] top-[425px] bg-[#b1c3ff] blur-[147px]"></div>



  <div class="absolute w-[372px] h-[175px] left-[225px] top-[237px] bg-[#b1c3ff] blur-[147px]"></div>


  <div
    class="absolute w-[715.56px] h-[161.15px] left-[658.32px] top-[390.31px] bg-[#98b0ff] blur-[147px] origin-top-left rotate-[-18.579deg]">
  </div>

  <div
    class="absolute w-[260.74px] h-[54.57px] left-[969px] top-[-8.93px] bg-[#98b0ff]/40 blur-[47.7px] origin-top-left rotate-[-18.579deg]">
  </div>

  <div
    class="absolute w-[315.56px] h-[161.15px] left-[998.32px] top-[1190.31px] bg-[#98b0ff] blur-[97px] origin-top-left rotate-[-18.579deg]">
  </div>

  <div
    class="absolute w-[560.74px] h-[184.57px] left-[0px] top-[1108.93px] bg-[#98b0ff] blur-[87.7px] origin-top-left rotate-[-18.579deg]">
  </div>




  <div
    class="absolute w-[525.08px] h-[82.15px] left-[985.53px] top-[666.46px] bg-[#00c979] blur-[107px] origin-top-left rotate-[-9.961deg]">
  </div>



  <div
    class="absolute w-[153.89px] h-[55.97px] left-[-56px] top-[847.02px] bg-[#00c979] blur-[81px] origin-top-left rotate-[-9.961deg]">
  </div>

  <div
    class="absolute w-[519.35px] h-[94.17px] left-[-219.02px] top-[-118.97px] bg-[#00c979] blur-[147px] origin-top-left rotate-[48.581deg]">
  </div>



  <div
    class="absolute w-[525.08px] h-[82.15px] left-[985.53px] top-[1566.46px] bg-[#98b0ff] blur-[107px] origin-top-left rotate-[-9.961deg]">
  </div>

  <div
    class="absolute w-[153.89px] h-[55.97px] left-[96px] top-[1547.02px] bg-[#00c979] blur-[81px] origin-top-left rotate-[-9.961deg]">
  </div>

  <!-- Container -->
  <div class="relative max-w-6xl mx-auto px-6">

    <!-- Heading -->

    <!-- Top Cards -->




    <!-- Steps Section -->
    <div class="mt-0 text-center max-w-3xl mx-auto">
      <h3 class="text-[26px] md:text-[30px] font-semibold text-gray-800 leading-tight mb-8">
        Why Use Email Spam Checker 
      </h3>

      <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed mb-8">
        Even a well-written and well-formatted email fails to deliver results, as it might never reach the inbox. The
        spam filters analyze every piece of content, from the subject line to your domain reputation. The Email Spam
        Checker tool from ExtractMails offers proper checks to your email, preventing it from getting flagged. 
      </p>

    </div>

    <!-- Steps Grid -->

    <div class="max-w-5xl mx-auto space-y-10">

      <!-- Card 1 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[1.5fr_0.5fr] items-center gap-8">

        <!-- Text -->
        <div class="">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Improves Email Deliverability 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            A major challenge in email communication is ensuring that the email reaches the recipient's inbox. The Email
            Spam Checker tool analyzes the complete email against all common filtering criteria and helps
            in eliminating any potential risks.  

          </p>
          <p class="text-gray-700 text-sm mb-2">
            The tool helps in: 
          </p>

          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>Increasing the inbox placement rates.</li>
            <li>Reducing bounce rates. </li>

          </ul>
        </div>

        <!-- Image -->
        <div class="">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-20-2026-03_56_59-PM-1.png"
            alt="Unknown Sender" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>
      </div>


      <!-- Card 2 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[0.5fr_1.5fr] items-center gap-8">


        <div class="flex-1">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-20-2026-03_58_00-PM-1.png"
            alt="Verify Leads" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>

        <!-- Text -->
        <div class="flex-1">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Detect Content Issues 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            In an email marketing campaign, even a small detail can easily trigger spam filters. The Email Spam Checker
            tool by ExtractMails helps in detecting the tiniest mistakes, like excessive punctuation, misleading subject
            lines, or promotional language.  
          </p>

          <p class="text-gray-700 text-sm mb-2">
            The tool helps you: 
          </p>

          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>Identify problematic phrases or words.</li>
            <li>Improves the tone and overall readability.</li>
            <li>Create more natural and human-like emails.</li>
          </ul>
        </div>

        <!-- Image -->

      </div>






      <!-- Card 1 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[1.5fr_0.5fr] items-center gap-8">

        <!-- Text -->
        <div class="">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Protect Your Sender Reputation 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            The tool plays a crucial role in improving email deliverability. By increasing the deliverability of your
            email, the Email Spam Checker tool helps in building a positive reputation for your domain or IP.  
          </p>

          <p class="text-gray-700 text-sm mb-2">
            The tool also helps you in: 

          </p>

          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>Maintaining a positive sending history</li>
            <li>Avoid getting your email flagged or blocked.</li>
            <li>Building long-term trust with the email provider.</li>
          </ul>
        </div>

        <!-- Image -->
        <div class="">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-20-2026-03_59_00-PM-1.png"
            alt="Unknown Sender" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>
      </div>


      <!-- Card 2 -->
      <div class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[0.5fr_1.5fr] items-center gap-8">


        <div class="flex-1">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-20-2026-04_00_58-PM-1.png"
            alt="Verify Leads" class="rounded-2xl w-full max-w-sm mx-auto">
        </div>

        <!-- Text -->
        <div class="flex-1">
          <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
            Offer Optimized Campaign Performance 
          </h3>

          <p class="text-gray-600 text-sm leading-relaxed mb-4">
            The Email Spam Market helps email marketers thoroughly check and optimize the email before sending, ensuring
            an improved engagement rate. 
          </p>

          <p class="text-gray-700 text-sm mb-2">
            The tool helps in ensuring: 

          </p>

          <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
            <li>A higher open rate. </li>
            <li>An increased click-through rate.</li>
            <li>Higher conversions and responses. </li>
          </ul>
        </div>

        <!-- Image -->

      </div>

    </div>



  </div>
</section>


<section class="py-6">

  <!-- TOP SECTION -->
  <div class="max-w-[1500px] mx-auto bg-white px-6 py-2 text-center rounded-lg">

    <h2
      class="text-[44px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent mb-8">
      How Email Spam Checker Works 
    </h2>

    <p class="text-gray-600 max-w-3xl mx-auto text-sm md:text-base leading-relaxed">
      The Email Spam Checker tool analyzes multiple layers of an email, which include email content, subject line, and
      technical setup, to detect any potential spam risk. The process works similarly to how the email service provider
      analyzes the email for detecting spam. 
      The process of the toll involves the following steps: 
    </p>



    <!-- STEPS -->
    <div class="relative mt-12">

      <div class="absolute top-1/2 left-0 w-full border-t-2 border-dashed border-gray-800 -translate-y-1/2"></div>

      <div class="relative flex justify-evenly items-center">

        <!-- Step 1 -->
        <div class="step flex flex-col items-center cursor-pointer mt-3 " data-step="1">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            1
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png" class="arrow w-4 h-4 mt-1">
        </div>

        <!-- Step 2 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="2">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            2
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <!-- Step 3 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="3">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            3
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <!-- Step 4 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="4">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            4
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <!-- Step 5 -->
        <div class="step flex flex-col items-center cursor-pointer" data-step="5">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            5
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>
        <div class="step flex flex-col items-center cursor-pointer" data-step="6">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            6
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

        <div class="step flex flex-col items-center cursor-pointer" data-step="7">
          <div
            class="w-10 h-10 flex items-center justify-center bg-gradient-to-b from-[#030458] to-[#383CFF] text-white rounded-full z-10">
            7
          </div>
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Vector-3-2.png"
            class="arrow w-4 h-4 mt-1 hidden">
        </div>

      </div>
    </div>

  </div>

  <!-- BOTTOM CONTENT -->
  <div class="max-w-[1300px] mx-auto px-6 mt-10">

    <!-- Step 1 -->

    <div class="step-content" data-content="1">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Analysis of the Content:
        </h3>
        <p class="px-4">
          The Email Spam Checker analyzes the various parts of the content, which include the subject line, body text,
          and technical setup, to detect any spam triggers. The tool checks for promotional phrases, suspicious or
          misleading wordings, and urgency-driven language, among other key triggers.
        </p>
        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>

    </div>

    <!-- Step 2 -->
    <div class="step-content hidden" data-content="2">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Spam Score Calculation:
        </h3>
        <p class="px-4">
          The tool assigns a specific score based on the analysis. The score reflects the likelihood of the email being
          flagged as spam.
        </p>
        <ul style="list-style-type: disc; padding-left: 20px;">
          <li>Low Score = Safe for Inbox </li>
          <li>Medium Score = Need basic Improvement </li>
          <li>High Score = Likely to land in Spam </li>
        </ul>
        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>

    <!-- Step 3 -->
    <div class="step-content hidden" data-content="3">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Link and Domain Evaluation:
        </h3>
        <p class="px-4">
          The tool checks all the links and domains that are included in your email. The tool analyzes:
        </p>
        <ul style="list-style-type: disc; padding-left: 20px;">
          <li>Link reputation </li>
          <li>Redirect behavior. </li>
          <li>Domain trustworthiness </li>
        </ul>
        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>

    <!-- Step 4 -->
    <div class="step-content hidden" data-content="4">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Authentication Verification:
        </h3>
        <p class="px-4">
          The system verifies your email authentication protocol, which includes SPF, DKIM, and DMARC. The proper
          authentication ensures that your email is trusted by various email providers, like Gmail, Yahoo, and Outlook,
          among others.
        </p>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>

    <!-- Step 5 -->
    <div class="step-content hidden" data-content="5">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Blacklist & Reputation Check:
        </h3>
        <p class="px-4">
          The Email Spam Checker tool by ExtractMails scans your domain and IP against the known spam blacklists. If the
          domain or IP is flagged, then it is more likely that your email will be blocked.
        </p>
        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>


    <div class="step-content hidden" data-content="6">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Inbox Placement Testing:
        </h3>
        <p class="px-4">
          The tool also checks some advanced spam checkers by simulating sending emails across different providers, like
          Gmail and Outlook. This helps in understanding the real-world performance of the email before sending.
        </p>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>



    <div class="step-content hidden" data-content="7">
      <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
        <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
        <h3 class="text-[29px] font-semibold text-[#1f2a7a] px-4 mb-4">
          Generate Detailed Report:
        </h3>
        <p class="px-4">
          After analyzing inbox placement, the tool provides a complete report of the email, which includes spam score,
          all the identified issues, and actionable recommendations.
        </p>

        <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
      </div>
    </div>





  </div>

</section>


<section class=" py-10">
  <div class="max-w-[1300px] mx-auto px-4">

    <!-- Title -->
    <h2 class="text-center text-[32px] md:text-[40px] lg:text-[45px] font-semibold 
                  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
                  bg-clip-text text-transparent mb-12">
      Other Tools
    </h2>

    <!-- Card -->
    <div class="bg-white border border-gray-300 rounded-xl p-8">

      <!-- Grid -->
      <div class="grid md:grid-cols-2 gap-8 relative">

        <!-- Vertical Divider -->
        <div class="hidden md:block absolute left-1/2 top-0 h-full w-px bg-gray-300"></div>

        <!-- Left Column -->
        <div class="space-y-8 pr-4 md:pr-8">

          <!-- Item 1 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              Gmail Generator
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Create custom or temporary Gmail addresses, which are ideal for testing and quick usage with the
              <a href="#" class="text-indigo-600 hover:underline">
                Gmail Generator tool ›
              </a>
            </p>
          </div>

          <!-- Item 2 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              AI Email Writer
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Generate personalized and professional emails instantly with the
              <a href="#" class="text-indigo-600 hover:underline">
                AI-Email Writer ›
              </a>
            </p>
          </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-8 pl-4 md:pl-8">

          <!-- Item 3 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              Reverse Email Lookup
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Find the details of any email address and convert email into actionable insights with the
              <a href="#" class="text-indigo-600 hover:underline">
                Reverse Email Lookup tool ›
              </a>
            </p>
          </div>

          <!-- Item 4 -->
          <div>
            <h3 class="font-semibold text-gray-800 mb-2">
              Email Checker
            </h3>
            <p class="text-sm text-gray-600 leading-relaxed">
              Verify the validity of an email, along with its format and activity with
              <a href="#" class="text-indigo-600 hover:underline">
                Email Checker ›
              </a>
            </p>
          </div>

        </div>

      </div>

    </div>
  </div>
</section>


<section class="max-w-[1400px] mx-auto py-0 px-6">

  <!-- Heading -->
  <h2 class="text-center text-[32px] md:text-[40px] lg:text-[45px] font-semibold 
  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
  bg-clip-text text-transparent mb-12">
    Frequently Asked Questions
  </h2>

  <div class="space-y-6">

    <!-- ITEM -->
    <div class="faq-item cursor-pointer">

      <!-- GRADIENT BORDER WRAPPER -->
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">

        <!-- INNER CARD -->
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">

            <!-- GRADIENT TEXT -->
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              How does the Email Spam Checker work?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <!-- CONTENT -->
          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              The Email Spam Checker tool of ExtractMails analyzes the content of the email, along with authentication
              settings and sender reputation, to provide a score that predicts the likelihood of the email reaching the
              inbox or spam folder.
            </p>
          </div>

        </div>
      </div>
    </div>

    <!-- COPY SAME STRUCTURE FOR ALL ITEMS -->

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Does the Email Spam Checker tool also check the authentication of an email?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the Email Spam Checker tool of ExtractMails also checks the authentication of an email, along with
              detecting spam triggers that can be flagged.
            </p>
          </div>

        </div>
      </div>
    </div>



    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Is my data in the Email Spam Checker secure?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the Email Spam Checker tool of ExtractMails is completely secure. The tool generally deletes the
              content within 24 hours, to ensure safety.
            </p>
          </div>

        </div>
      </div>
    </div>





    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              How can the Email Spam Checker tool help in a marketing campaign?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              The Email Spam Checker offers significant help in marketing campaigns. It helps in analyzing the content
              of the email, along with analyzing the subject line and technical authentications. The tool also helps in
              predicting whether the email will land in the spam folder, increasing the deliverability rate and ROI of
              the campaign.
            </p>
          </div>

        </div>
      </div>
    </div>

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Is the Email Spam Checker tool user-friendly?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the Email Spam Checker tool of ExtractMails is highly user-friendly. It is designed to offer instant
              and actionable insights on email deliverability.
            </p>
          </div>

        </div>
      </div>
    </div>


    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Can an Email Spam Checker improve open rates?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, the Email Spam Checker tool can significantly improve the open rate, as it ensures that the message
              lands in the inbox, rather than in the spam folder.
            </p>
          </div>

        </div>
      </div>
    </div>

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              What are common spam trigger words in emails?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              In an email, the most common spam trigger words involve a high-pressure urgency tone, along with financial
              promises and exaggerated claims. Some of the most common trigger words include "100% free", "guaranteed",
              "hidden charges", "no catch", and "winner", among others.
            </p>
          </div>

        </div>
      </div>
    </div>


    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Does email authentication affect spam filtering?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[16px] text-gray-500">
              Yes, email authentication affects spam filtering. Email authentication directly affects email
              deliverability, as a properly configured authentication protocol verifies the legitimacy of the vender.
            </p>
          </div>

        </div>
      </div>
    </div>


  </div>

</section>


<!-- JS -->
<script>
  const steps = document.querySelectorAll('.step');
  const contents = document.querySelectorAll('.step-content');

  steps.forEach(step => {
    step.addEventListener('click', () => {
      const id = step.dataset.step;

      // remove mt-3 from all steps
      steps.forEach(s => s.classList.remove('mt-3'));

      // add mt-3 only to active step
      step.classList.add('mt-3');

      // hide all contents
      contents.forEach(c => c.classList.add('hidden'));

      // show selected content
      document.querySelector(`[data-content="${id}"]`).classList.remove('hidden');

      // hide all arrows
      document.querySelectorAll('.arrow').forEach(a => a.classList.add('hidden'));

      // show active arrow
      step.querySelector('.arrow').classList.remove('hidden');
    });
  });
</script>


<script>
  document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', () => {

      const content = item.querySelector('.faq-content');
      const icon = item.querySelector('.faq-icon');

      // Close others
      document.querySelectorAll('.faq-item').forEach(i => {
        if (i !== item) {
          const c = i.querySelector('.faq-content');
          c.style.maxHeight = null;
          i.querySelector('.faq-icon').textContent = '+';
        }
      });

      // Toggle current
      if (content.style.maxHeight) {
        content.style.maxHeight = null;
        icon.textContent = '+';
      } else {
        content.style.maxHeight = content.scrollHeight + "px";
        icon.textContent = '-';
      }

    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('extractmail-spam-editor');
    const statusNode = document.getElementById('extractmail-spam-status');
    const wordsNode = document.getElementById('extractmail-spam-words');
    const readTimeNode = document.getElementById('extractmail-spam-read-time');
    const countNodes = {
      urgency: document.getElementById('extractmail-spam-count-urgency'),
      shady: document.getElementById('extractmail-spam-count-shady'),
      overpromise: document.getElementById('extractmail-spam-count-overpromise'),
      unnatural: document.getElementById('extractmail-spam-count-unnatural')
    };
    const cardNodes = {
      urgency: document.getElementById('extractmail-spam-card-urgency'),
      shady: document.getElementById('extractmail-spam-card-shady'),
      overpromise: document.getElementById('extractmail-spam-card-overpromise'),
      unnatural: document.getElementById('extractmail-spam-card-unnatural')
    };
    const tryNowLink = document.querySelector('.extractmail-try-now-link');
    const checkerSection = document.getElementById('extractmail-spam-checker-section');

    if (tryNowLink && checkerSection) {
      tryNowLink.addEventListener('click', function(event) {
        const header = document.querySelector('.site-header, #masthead, header');
        const headerHeight = header ? header.getBoundingClientRect().height : 0;
        const scrollGap = Math.max(headerHeight + 28, 110);
        const targetTop = checkerSection.getBoundingClientRect().top + window.pageYOffset - scrollGap;

        event.preventDefault();
        window.scrollTo({
          top: Math.max(targetTop, 0),
          behavior: 'smooth'
        });
      });
    }

    if (
      !editor ||
      !statusNode ||
      !wordsNode ||
      !readTimeNode ||
      !countNodes.urgency ||
      !countNodes.shady ||
      !countNodes.overpromise ||
      !countNodes.unnatural ||
      !cardNodes.urgency ||
      !cardNodes.shady ||
      !cardNodes.overpromise ||
      !cardNodes.unnatural
    ) {
      return;
    }

    const SAMPLE_TEXT = [
      'Hello Sarah,',
      '',
      'Limited time offer: claim your free consultation today and get instant access to our special deal.',
      '',
      'Our investment plan promises amazing results, risk-free returns, and financial freedom for new members.',
      '',
      'Click here now to confirm your account details.',
      '',
      'Best regards,',
      'John Doe'
    ].join('\n');

    const STATUS_CLASSES = {
      Poor: 'text-[18px] font-extrabold text-rose-600',
      Fair: 'text-[18px] font-extrabold text-orange-600',
      Good: 'text-[18px] font-extrabold text-brand',
      Great: 'text-[18px] font-extrabold text-emerald-600'
    };

    const CATEGORY_META = {
      urgency: {
        weight: 8
      },
      shady: {
        weight: 9
      },
      overpromise: {
        weight: 8
      },
      unnatural: {
        weight: 6
      }
    };

    const SOURCE_TRIGGERS = {
      money: [
        '$$$', '\u20ac\u20ac\u20ac', '\u00a3\u00a3\u00a3', '50% off', 'a few bob', 'accept cash cards',
        'accept credit cards',
        'affordable', 'affordable deal', 'avoid bankruptcy', 'bad credit', 'bank', 'bankruptcy', 'bargain',
        'billing',
        'billing address', 'billion', 'billion dollars', 'billionaire', 'card accepted', 'cards accepted',
        'cash', 'cash bonus',
        'cash out', 'cash-out', 'cashcashcash', 'casino', 'cents on the dollar', 'check',
        'check or money order',
        'claim your discount', 'cost', 'costs', 'credit', 'credit bureaus', 'credit card', 'credit card offers',
        'credit or debit', 'deal', 'debt', 'discount', 'dollars', 'double your', 'double your wealth', 'earn',
        'earn $',
        'earn cash', 'earn extra income', 'earn from home', 'earn monthly', 'earn per month', 'earn per week',
        'earn per year',
        'easy income', 'easy terms', 'f r e e', 'for free', 'for just $', 'free access', 'free consultation',
        'free gift',
        'free hosting', 'free info', 'free investment', 'free membership', 'free money', 'free preview',
        'free quote',
        'free trial', 'full refund', 'get out of debt', 'giveaway', 'guaranteed deposit', 'increase revenue',
        'increase sales/traffic',
        'instant earnings', 'instant income', 'insurance', 'investment', 'investment advice', 'loans', 'make $',
        'money-back guarantee', 'mortgage', 'mortgage rates', 'offer', 'one hundred percent free', 'only $',
        'price',
        'price protection', 'profits', 'quote', 'refinance', 'save $', 'save big money', 'subject to credit',
        'us dollars',
        'why pay more?', 'your income'
      ],
      scam: [
        '100% guaranteed', 'access now', 'act fast', 'amazing deal', 'apply now', 'as seen on', 'best deal',
        'big profit',
        'can\'t miss', 'click below', 'click here', 'deal ending soon', 'don\'t delete', 'double your money',
        'exclusive deal',
        'fantastic offer', 'free membership', 'get it now', 'great news', 'guaranteed results',
        'important information',
        'increase sales', 'instant savings', 'limited time', 'must read', 'new customers only', 'no catch',
        'no cost',
        'no credit check', 'no obligation', 'no strings attached', 'once in a lifetime', 'only available here',
        'order now',
        'potential earnings', 'pure profit', 'risk-free', 'special invitation', 'special offer',
        'this won\'t last', 'urgent',
        'will not believe'
      ],
      marketing: [
        '#1', '100% free', '100% off', '100% satisfied', 'additional income', 'amazed', 'amazing',
        'amazing deal',
        'amazing offer', 'amazing stuff', 'be amazed', 'be surprised', 'be your own boss', 'best bargain',
        'best deal',
        'best offer', 'best price', 'best rates', 'big bucks', 'bonus', 'can\'t live without',
        'consolidate debt',
        'double your cash', 'double your income', 'drastically reduced', 'earn extra cash', 'earn money',
        'expect to earn',
        'extra', 'extra cash', 'extra income', 'fantastic', 'fantastic deal', 'fantastic offer', 'fast cash',
        'financial freedom',
        'free priority mail', 'get paid', 'incredible deal', 'join millions', 'lowest price', 'make money',
        'million dollars',
        'money-back guarantee', 'prize', 'promise', 'pure profit', 'risk-free', 'satisfaction guaranteed',
        'save up to',
        'special promotion', 'the best', 'thousands', 'unbeatable offer', 'unbelievable', 'unlimited',
        'wonderful',
        'you will not believe your eyes'
      ],
      urgencySource: [
        'access', 'access now', 'act', 'act immediately', 'act now', 'act now!', 'action', 'action required',
        'apply here',
        'apply now', 'apply now!', 'apply online', 'become a member', 'before it\'s too late', 'being a member',
        'buy',
        'buy direct', 'buy now', 'buy today', 'call', 'call free', 'call free/now', 'call me', 'call now',
        'call now!',
        'can we have a minute of your time?', 'cancel now', 'cancellation required', 'claim now', 'click',
        'click below',
        'click here', 'click me to download', 'click now', 'click this link', 'click to get', 'click to remove',
        'contact us immediately', 'deal ending soon', 'do it now', 'do it today', 'don\'t delete',
        'don\'t hesitate',
        'don\'t waste time', 'exclusive deal', 'expire', 'expires today', 'final call', 'for instant access',
        'for only',
        'for you', 'friday before [holiday]', 'get it away', 'get it now', 'get now', 'get paid', 'get started',
        'get started now', 'great offer', 'hurry up', 'immediately', 'info you requested',
        'information you requested',
        'instant', 'limited time', 'new customers only', 'now', 'now only', 'offer expires', 'once in lifetime',
        'only',
        'order now', 'order today', 'please read', 'purchase now', 'sign up free', 'sign up free today',
        'supplies are limited',
        'take action', 'take action now', 'this won\'t last', 'time limited', 'today', 'top urgent', 'trial',
        'urgent',
        'what are you waiting for?', 'while supplies last', 'you are a winner'
      ],
      health: [
        '100% natural', 'all natural', 'best price', 'certified organic', 'clinical trial', 'cure for',
        'diet pill',
        'doctor recommended', 'double blind study', 'fat burner', 'fast weight loss', 'free consultation',
        'get slim',
        'guaranteed weight loss', 'hair growth', 'lose weight fast', 'medical breakthrough', 'miracle cure',
        'money-back guarantee', 'natural remedy', 'no prescription needed', 'online pharmacy',
        'over-the-counter',
        'pain relief', 'prescription drugs', 'reverse aging', 'safe and effective', 'scientifically proven',
        'secret formula',
        'weight loss', 'youthful skin'
      ],
      tech: [
        'access your account', 'account update', 'action required', 'activate now', 'antivirus',
        'change password',
        'click to verify', 'confirm your details', 'confidential information', 'cyber monday', 'data breach',
        'download now',
        'final notice', 'free antivirus', 'free trial', 'important update', 'immediate action required',
        'improve security',
        'install now', 'last warning', 'log in now', 'new login detected', 'online account', 'password reset',
        'payment details needed', 'phishing alert', 'secure payment', 'security breach', 'security update',
        'update account',
        'verify identity', 'warning message'
      ],
      gambling: [
        'adult content', 'bet now', 'big win', 'blackjack', 'casino bonus', 'cash out now', 'click to win',
        'double your money', 'exclusive access', 'free chips', 'free spins', 'gamble online', 'hot deal',
        'instant winnings',
        'jackpot', 'live dealer', 'lottery winner', 'lucky chance', 'online betting', 'online casino',
        'online gaming',
        'poker tournament', 'risk-free bet', 'slots jackpot', 'spin to win', 'try for free', 'vip offer',
        'winner announced', 'winning numbers', 'xxx'
      ],
      unnaturalSource: [
        'dear friend', 'don\'t delete', 'f r e e', 'can\'t miss', 'will not believe',
        'what are you waiting for?'
      ]
    };

    const TRIGGERS = {
      urgency: Array.from(new Set(SOURCE_TRIGGERS.urgencySource)),
      shady: Array.from(new Set([].concat(SOURCE_TRIGGERS.money, SOURCE_TRIGGERS.scam, SOURCE_TRIGGERS.tech,
        SOURCE_TRIGGERS.gambling))),
      overpromise: Array.from(new Set([].concat(SOURCE_TRIGGERS.marketing, SOURCE_TRIGGERS.health))),
      unnatural: Array.from(new Set(SOURCE_TRIGGERS.unnaturalSource))
    };

    const PATTERN_TRIGGERS = {
      unnatural: [{
          label: 'ALL CAPS',
          regex: /\b[A-Z]{4,}\b/g
        },
        {
          label: '!!!',
          regex: /!{2,}/g
        },
        {
          label: '$$$',
          regex: /(?:\$|\u20ac|\u00a3){3,}/g
        },
        {
          label: 'stacked links',
          regex: /https?:\/\/[^\s]+/g
        }
      ]
    };

    function escapeHtml(value) {
      return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function escapeRegExp(value) {
      return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function normalizeText(text) {
      return text
        .replace(/\r\n?/g, '\n')
        .replace(/\u00a0/g, ' ')
        .replace(/[\u2018\u2019]/g, '\'')
        .replace(/[\u201c\u201d]/g, '"')
        .replace(/[\u2013\u2014]/g, '-');
    }

    const TOKEN_STOP_WORDS = new Set([
      'able', 'about', 'after', 'again', 'against', 'almost', 'also', 'among', 'and', 'any', 'are', 'because',
      'been', 'before', 'being', 'between', 'both', 'but', 'call', 'can', 'come', 'does', 'each', 'even',
      'every', 'from', 'have', 'into', 'just', 'many', 'more', 'most', 'much', 'must', 'need', 'not', 'now',
      'off', 'only', 'onto', 'other', 'our', 'out', 'over', 'same', 'some', 'such', 'than', 'that', 'the',
      'their', 'them', 'then', 'there', 'these', 'they', 'this', 'those', 'through', 'today', 'under', 'until',
      'very', 'what', 'when', 'where', 'which', 'while', 'will', 'with', 'would', 'your'
    ]);

    function buildExactPhraseRegex(phrase) {
      const normalizedPhrase = normalizeText(phrase).trim();
      let pattern = escapeRegExp(normalizedPhrase).replace(/\s+/g, '\\s+');

      if (/^[A-Za-z0-9]/.test(normalizedPhrase)) {
        pattern = '\\b' + pattern;
      }

      if (/[A-Za-z0-9]$/.test(normalizedPhrase)) {
        pattern = pattern + '\\b';
      }

      return new RegExp(pattern, 'gi');
    }

    function tokenizePhrase(phrase) {
      const normalizedPhrase = normalizeText(phrase).toLowerCase();
      return normalizedPhrase.match(/[a-z0-9]+/g) || [];
    }

    function buildPhraseRegexes(phrase) {
      const normalizedPhrase = normalizeText(phrase).trim();
      const regexes = [];
      const seen = new Set();

      function pushRegex(regex) {
        const key = regex.source + '::' + regex.flags;

        if (!seen.has(key)) {
          seen.add(key);
          regexes.push(regex);
        }
      }

      pushRegex(buildExactPhraseRegex(normalizedPhrase));

      const tokens = tokenizePhrase(normalizedPhrase);

      if (tokens.length > 1) {
        pushRegex(new RegExp('\\b' + tokens.map(escapeRegExp).join('[\\s\\W_]+') + '\\b', 'gi'));
      }

      return regexes;
    }

    function buildTokenRegex(token) {
      return new RegExp('\\b' + escapeRegExp(token) + '\\b', 'gi');
    }

    function buildBucketMatchers(bucket) {
      const matchers = [];
      const seen = new Set();
      const phrases = TRIGGERS[bucket].slice().sort(function(a, b) {
        return b.length - a.length;
      });

      function pushMatcher(regex) {
        const key = regex.source + '::' + regex.flags;

        if (!seen.has(key)) {
          seen.add(key);
          matchers.push(regex);
        }
      }

      phrases.forEach(function(phrase) {
        buildPhraseRegexes(phrase).forEach(function(regex) {
          pushMatcher(regex);
        });

        const tokens = Array.from(new Set(tokenizePhrase(phrase).filter(function(token) {
          return token.length >= 4 && !TOKEN_STOP_WORDS.has(token);
        })));

        if (tokens.length > 1) {
          tokens.forEach(function(token) {
            pushMatcher(buildTokenRegex(token));
          });
        }
      });

      return matchers;
    }

    const COMPILED_TRIGGERS = Object.keys(TRIGGERS).reduce(function(compiled, bucket) {
      compiled[bucket] = buildBucketMatchers(bucket);
      return compiled;
    }, {});

    function collectMatches(text) {
      const matches = [];

      Object.keys(COMPILED_TRIGGERS).forEach(function(bucket) {
        COMPILED_TRIGGERS[bucket].forEach(function(compiledRegex) {
          const regex = new RegExp(compiledRegex.source, compiledRegex.flags);
          let match;

          while ((match = regex.exec(text)) !== null) {
            matches.push({
              start: match.index,
              end: match.index + match[0].length,
              bucket: bucket,
              label: match[0]
            });

            if (match.index === regex.lastIndex) {
              regex.lastIndex += 1;
            }
          }
        });
      });

      Object.keys(PATTERN_TRIGGERS).forEach(function(bucket) {
        PATTERN_TRIGGERS[bucket].forEach(function(rule) {
          const regex = new RegExp(rule.regex.source, rule.regex.flags);
          let match;

          while ((match = regex.exec(text)) !== null) {
            matches.push({
              start: match.index,
              end: match.index + match[0].length,
              bucket: bucket,
              label: rule.label
            });

            if (match.index === regex.lastIndex) {
              regex.lastIndex += 1;
            }
          }
        });
      });

      matches.sort(function(a, b) {
        if (a.start !== b.start) {
          return a.start - b.start;
        }

        return (b.end - b.start) - (a.end - a.start);
      });

      const filtered = [];
      let cursor = -1;

      matches.forEach(function(item) {
        if (item.start >= cursor) {
          filtered.push(item);
          cursor = item.end;
        }
      });

      return filtered;
    }

    function buildHighlightedHtml(text, matches) {
      if (!text) {
        return '';
      }

      let html = '';
      let cursor = 0;

      matches.forEach(function(item) {
        if (cursor < item.start) {
          html += escapeHtml(text.slice(cursor, item.start));
        }

        html += '<span class="extractmail-spam-tag ' + item.bucket + '">' + escapeHtml(text.slice(item.start,
          item.end)) + '</span>';
        cursor = item.end;
      });

      if (cursor < text.length) {
        html += escapeHtml(text.slice(cursor));
      }

      return html.replace(/\n/g, '<br>');
    }

    function getWordCount(text) {
      const matches = text.trim().match(/\b[\w'-]+\b/g);
      return matches ? matches.length : 0;
    }

    function getReadTimeText(words) {
      if (words <= 35) {
        return 'a few seconds';
      }

      const minutes = Math.max(1, Math.ceil(words / 180));
      return minutes === 1 ? '1 minute' : minutes + ' minutes';
    }

    function getStatus(score) {
      if (score >= 55) {
        return 'Poor';
      }

      if (score >= 35) {
        return 'Fair';
      }

      if (score >= 18) {
        return 'Good';
      }

      return 'Great';
    }

    function getCaretOffset(element) {
      const selection = window.getSelection();
      if (!selection || !selection.rangeCount || !element.contains(selection.anchorNode)) {
        return 0;
      }

      const range = selection.getRangeAt(0).cloneRange();
      range.selectNodeContents(element);
      range.setEnd(selection.anchorNode, selection.anchorOffset);
      return range.toString().length;
    }

    function setCaretOffset(element, offset) {
      const selection = window.getSelection();
      if (!selection) {
        return;
      }

      const range = document.createRange();
      let chars = 0;
      let found = false;

      function walk(node) {
        if (found) {
          return;
        }

        if (node.nodeType === Node.TEXT_NODE) {
          const nextChars = chars + node.textContent.length;
          if (offset <= nextChars) {
            range.setStart(node, Math.max(0, offset - chars));
            range.collapse(true);
            found = true;
          }
          chars = nextChars;
          return;
        }

        Array.prototype.forEach.call(node.childNodes, walk);
      }

      walk(element);

      if (!found) {
        range.selectNodeContents(element);
        range.collapse(false);
      }

      selection.removeAllRanges();
      selection.addRange(range);
    }

    function readEditorNodeText(node) {
      if (!node) {
        return '';
      }

      if (node.nodeType === Node.TEXT_NODE) {
        return node.textContent || '';
      }

      if (node.nodeType !== Node.ELEMENT_NODE) {
        return '';
      }

      if (node.nodeName === 'BR') {
        return '\n';
      }

      const blockNodes = {
        DIV: true,
        P: true,
        LI: true,
        UL: true,
        OL: true,
        PRE: true,
        BLOCKQUOTE: true
      };

      let output = '';
      Array.prototype.forEach.call(node.childNodes, function(childNode, index) {
        const childText = readEditorNodeText(childNode);
        output += childText;

        if (
          childNode &&
          childNode.nodeType === Node.ELEMENT_NODE &&
          blockNodes[childNode.nodeName] &&
          index < node.childNodes.length - 1 &&
          !output.endsWith('\n')
        ) {
          output += '\n';
        }
      });

      if (blockNodes[node.nodeName] && node !== editor && output && !output.endsWith('\n')) {
        output += '\n';
      }

      return output;
    }

    function getEditorText() {
      return normalizeText(readEditorNodeText(editor));
    }

    function render(text, preserveCaret) {
      const safeText = normalizeText(text);
      const caretOffset = preserveCaret ? getCaretOffset(editor) : 0;
      const matches = collectMatches(safeText);
      const grouped = {
        urgency: [],
        shady: [],
        overpromise: [],
        unnatural: []
      };
      let score = 0;

      matches.forEach(function(item) {
        grouped[item.bucket].push(item.label);
        score += CATEGORY_META[item.bucket].weight;
      });

      score = Math.min(100, score);
      editor.innerHTML = safeText ? buildHighlightedHtml(safeText, matches) : '';
      editor.classList.toggle('is-empty', !safeText.trim());

      if (preserveCaret) {
        setCaretOffset(editor, caretOffset);
      }

      const wordCount = getWordCount(safeText);
      const status = getStatus(score);

      statusNode.textContent = status;
      statusNode.className = STATUS_CLASSES[status];
      wordsNode.textContent = String(wordCount);
      readTimeNode.textContent = getReadTimeText(wordCount);

      Object.keys(grouped).forEach(function(bucket) {
        const count = grouped[bucket].length;
        countNodes[bucket].textContent = String(count);
        cardNodes[bucket].classList.toggle('hidden', count === 0);
      });
    }

    editor.addEventListener('input', function() {
      render(getEditorText(), true);
    });

    editor.addEventListener('paste', function(event) {
      event.preventDefault();
      const text = normalizeText((event.clipboardData || window.clipboardData).getData('text'));

      if (document.queryCommandSupported && document.queryCommandSupported('insertText')) {
        document.execCommand('insertText', false, text);
      } else {
        const selection = window.getSelection();
        if (selection && selection.rangeCount) {
          selection.deleteFromDocument();
          selection.getRangeAt(0).insertNode(document.createTextNode(text));
        } else {
          editor.textContent += text;
        }
        render(getEditorText(), true);
      }
    });

    editor.addEventListener('keydown', function(event) {
      if (event.key === 'Tab') {
        event.preventDefault();
        if (document.queryCommandSupported && document.queryCommandSupported('insertText')) {
          document.execCommand('insertText', false, '    ');
        }
      }
    });

    editor.textContent = SAMPLE_TEXT;
    render(SAMPLE_TEXT, false);
  });
</script>


<style id="faq-style">
  .faq-content {
    transition: all 0.3s ease;
  }
</style>


<?php get_footer() ?>
