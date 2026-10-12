<?php
/*
Template Name: Gmail Generator Tool  */

add_action(
    'wp_head',
    static function () {
        ?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap');

    #extractmail-gmail-generator-section {
        scroll-margin-top: 110px;
    }

    .extractmail-try-now-link,
    .extractmail-try-now-link:hover,
    .extractmail-try-now-link:focus,
    .extractmail-try-now-link:active {
        background: linear-gradient(to right, #1d4ed8, #4ade80) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        opacity: 1 !important;
        outline: none;
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
                    <a href="/tools/reverse-email-lookup" class="hover:underline">Gmail Generator Tool </a>

                </span>


                <!-- Heading -->
                <h1 class="text-[28px] sm:text-[34px] md:text-[42px] lg:text-[49px] leading-snug md:leading-tight font-semibold 
                            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
                            bg-clip-text text-transparent mb-4 md:mb-6">
                    Gmail Generator Tool 
                </h1>

                <!-- Description -->
                <p
                    class="text-gray-600 text-[14px] sm:text-[16px] md:text-[18px] lg:text-[20px] leading-relaxed max-w-xl md:mb-8">
                    Create multiple variations of your Gmail account with the free Gmail Generator tool by ExtractMails,
                    which uses dots and aliases to manage emails smarter. 
                </p>

                <button type="button" class="extractmail-try-now-link inline-block px-6 md:px-12 py-1 text-[14px] md:text-[18px] lg:text-[22px] text-white rounded-full mt-12 lg:mt-4 md:mb-8
                            bg-gradient-to-r from-blue-700 to-green-400">
                    Try Now
                </button>

            </div>

            <!-- RIGHT IMAGE -->
            <div class="flex justify-center md:justify-end mt-6 md:mt-0">
                <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-62-4.png" alt="Hero"
                    class="w-full max-w-[300px] sm:max-w-[400px] md:max-w-[480px] lg:max-w-[520px] object-contain">
            </div>

        </div>

        <!-- SECOND SECTION -->


    </div>
</section>

<main id="extractmail-gmail-generator-section" class="mb-14 bg-white font-body text-ink">
    <section class="mx-auto max-w-7xl px-4 sm:px-6">

        <div class="mx-auto mt-14 max-w-6xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="p-6 sm:p-8">
                <form id="gmail-generator-form" class="space-y-4">
                    <label for="gmail-address" class="block text-sm font-semibold text-slate-600">
                        Enter your email address:
                    </label>
                    <div class="flex items-stretch"
                        style="display:flex;align-items:stretch;overflow:hidden;border:1px solid #cbd5e1;border-radius:6px;background:#ffffff;">
                        <input id="gmail-address" name="gmail_address" type="email" placeholder="name@example.com"
                            class="block w-full"
                            style="display:block;width:100%;height:46px;padding:0 16px;border:0;border-radius:0;background:#ffffff;color:#14224d;font-size:18px;line-height:46px;box-shadow:none;outline:none;-webkit-appearance:none;appearance:none;"
                            required />
                        <button type="submit" class="inline-flex items-center justify-center"
                            style="display:inline-flex;align-items:center;justify-content:center;min-width:210px;height:46px;padding:0 24px;border:0;border-left:1px solid #1d4ed8;border-radius:0;background:#2563eb;color:#ffffff;font-size:16px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;cursor:pointer;">
                            Generate
                        </button>
                    </div>
                </form>
                <div id="generator-error"
                    class="mt-4 hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                </div>
            </div>

            <div class="border-t border-slate-200 bg-panel px-6 py-6 sm:px-8 sm:py-8">
                <p class="text-lg text-slate-600">
                    Number of possible aliases:
                    <strong id="alias-count" class="font-bold text-ink">0</strong>
                </p>

                <div id="results-list" class="mt-5 whitespace-pre-line text-[22px] leading-10 text-ink">Enter an email
                    address and click Generate.</div>
            </div>
        </div>
    </section>
</main>


<section class="bg-[#f4f4f4] py-20 px-4">
    <div class="max-w-[1200px] mx-auto text-center">

        <!-- Main Heading -->
        <h1 class="text-[28px] md:text-[38px] font-medium text-gray-900 leading-snug mb-6">
            Instantly Generate Unlimited Gmail Variations for Testing & Productivity 
        </h1>

        <!-- Sub Text -->
        <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed mb-14">
            Create multiple variations of your Gmail account in seconds using the Gmail Generator tool of ExtractMails.
            Ideal for testing, signups, filtering, and productivity workflow: the Gmail Generator tool helps in creating
            unique Gmail formats, without creating a new account. 
        </p>

        <!-- Section Heading -->
        <h2 class="text-[24px] md:text-[37px] font-medium text-gray-900 mb-5">
            What is Gmail Generator? 
        </h2>

        <!-- Section Text -->
        <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed">
            Gmail Generator is a smart tool that creates multiple variations of a single Gmail address. The tool uses
            Gmail-supported formatting techniques, which include using dots and aliases. Although these variations route
            the emails to the primary inbox, it appears as unique addresses to the websites and online service pages. 
            Creating a variation of email address allows users to manage multiple sign-ups, test email workflows, and
            organize communications effectively. Gmail Generator is a simple yet powerful solution, especially for
            marketers, developers, and everyday users. 
             
        </p>

    </div>
</section>


<section class=" py-10">
    <div class="max-w-[1400px] mx-auto px-6 text-center">

        <!-- Heading -->
        <h2
            class="text-[36px] font-semibold  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent mb-4 leading-snug">
            Key Features of Gmail <br> Generator 
        </h2>

        <p class="text-gray-600 text-[14px] md:text-[15px] leading-relaxed mb-10">
            The Gmail Generator tool of ExtractMails offers fast and highly practical email addresses, using the
            Gmail-supported variation techniques. The tool helps the user unlock the full potential of Gmail's flexible
            email formatting systems. 
        </p>

        <!-- Grid -->
        <div class="grid md:grid-cols-4">

            <!-- ITEM 1 -->
            <div class="px-6 text-center relative">
                <img src="https://www.extractmails.com/wp-content/uploads/2026/04/instant-messaging-1.png"
                    class="w-20 mx-auto mb-6">

                <h3 class="font-bold text-[18px] text-gray-900 mb-3">
                    Instant Email Variations
                </h3>

                <p class="text-gray-600 text-[15px] leading-relaxed">
                    Gmail Generator can generate hundreds of Gmail variations in seconds with just one input.  
                </p>

                <!-- Divider -->
                <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
            </div>

            <!-- ITEM 2 -->
            <div class="px-6 text-center relative">
                <img src="https://www.extractmails.com/wp-content/uploads/2026/04/inventory-4.png"
                    class="w-20 mx-auto mb-6">

                <h3 class="font-bold text-[18px] text-gray-900 mb-3">
                    Bulk Generation
                </h3>

                <p class="text-gray-600 text-[15px] leading-relaxed">
                    The tool can generate multiple combinations at once for large-scale testing or account creation
                    workflows.  
                </p>

                <!-- Divider -->
                <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
            </div>

            <!-- ITEM 3 -->
            <div class="px-6 text-center relative">
                <img src="https://www.extractmails.com/wp-content/uploads/2026/04/letter-1.png"
                    class="w-20 mx-auto mb-6">

                <h3 class="font-bold text-[18px] text-gray-900 mb-3">
                    Unique Yet Valid Emails
                </h3>

                <p class="text-gray-600 text-[15px] leading-relaxed">
                    All emails generated by the Gmail Generator are valid under Gmail rules and will be delivered to the
                    same inbox.   
                </p>

                <!-- Divider -->
                <div class="hidden md:block absolute right-0 top-1/2 -translate-y-1/2 h-[90%] w-px bg-gray-300"></div>
            </div>

            <!-- ITEM 4 -->
            <div class="px-6 text-center">
                <img src="https://www.extractmails.com/wp-content/uploads/2026/04/puzzle-1.png"
                    class="w-20 mx-auto mb-6">

                <h3 class="font-bold text-[18px] text-gray-900 mb-3">
                    Universal Compatibility 
                </h3>

                <p class="text-gray-600 text-[15px] leading-relaxed">
                    The email generated using the Gmail Generator tool works across all platforms, browsers, and
                    devices, without the need for any installation.  
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
                Why Use Gmail Generator? 
            </h3>

            <p class="text-gray-600 text-[14px] md:text-[17px] leading-relaxed mb-8">
                Manually managing multiple variations of emails is a time-consuming and tedious task. The Gmail
                Generator helps simplify the process of generating variations of email addresses by creating thousands
                of valid email variations from a single Gmail account. 
            </p>

        </div>

        <!-- Steps Grid -->

        <div class="max-w-5xl mx-auto space-y-10">

            <!-- Card 1 -->
            <div
                class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[1.5fr_0.5fr] items-center gap-8">

                <!-- Text -->
                <div class="">
                    <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
                        Saves Time and Eliminates Repetition
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        Creating multiple variations of Gmail accounts requires various verification steps, which the
                        Gmail Generator tool eliminates. The Gmail Generator tool can generate hundreds of valid email
                        addresses with a single primary account, instantly.  
                    </p>

                    <p class="text-gray-700 text-sm mb-2">
                        With the Gmail Generator tool, you can: 
                    </p>

                    <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
                        <li>Generate hundreds and thousands of valid Gmail email addresses instantly.  </li>
                        <li>Avoid repeated sign-ups and verification. </li>

                    </ul>
                </div>

                <!-- Image -->
                <div class="">
                    <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-17-2026-12_36_10-PM-1.png"
                        alt="Unknown Sender" class="rounded-2xl w-full max-w-sm mx-auto">
                </div>
            </div>


            <!-- Card 2 -->
            <div
                class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[0.5fr_1.5fr] items-center gap-8">


                <div class="flex-1">
                    <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-17-2026-12_39_12-PM-1.png"
                        alt="Verify Leads" class="rounded-2xl w-full max-w-sm mx-auto">
                </div>

                <!-- Text -->
                <div class="flex-1">
                    <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
                        Ideal for Testing and Development
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        The Gmail Generator tool helps generate hundreds of variations of Gmail accounts, which are
                        essential for testing the user's flow for the QA tester, product teams, and developers.  
                    </p>

                    <p class="text-gray-700 text-sm mb-2">
                        With the tool, you can:  
                    </p>

                    <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
                        <li>Test signup and login system.</li>
                        <li>Validate email triggers.</li>
                        <li>Automate workflow and simulate multiple user accounts.</li>
                    </ul>
                </div>

                <!-- Image -->

            </div>






            <!-- Card 1 -->
            <div
                class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[1.5fr_0.5fr] items-center gap-8">

                <!-- Text -->
                <div class="">
                    <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
                        Organize Your Inbox More Efficiently 
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        The plus (+) addressing features of Gmail allow users to label incoming emails automatically.
                        The Gmail Generator tool helps in generating hundreds of free Gmail account aliases, using the
                        "+" trick, to better organize your inbox.  
                    </p>

                    <p class="text-gray-700 text-sm mb-2">
                        The plus technique of Gmail generation helps in: 
                    </p>

                    <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
                        <li>Reduce clutter from your inbox.</li>
                        <li>Analyze the credibility of the domain address.</li>
                        <li>Stay organized and find relevant emails faster.</li>
                    </ul>
                </div>

                <!-- Image -->
                <div class="">
                    <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-17-2026-12_49_20-PM-1.png"
                        alt="Unknown Sender" class="rounded-2xl w-full max-w-sm mx-auto">
                </div>
            </div>


            <!-- Card 2 -->
            <div
                class="bg-white backdrop-blur-md rounded-3xl p-8 md:p-10 grid md:grid-cols-[0.5fr_1.5fr] items-center gap-8">


                <div class="flex-1">
                    <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-17-2026-12_51_07-PM-1.png"
                        alt="Verify Leads" class="rounded-2xl w-full max-w-sm mx-auto">
                </div>

                <!-- Text -->
                <div class="flex-1">
                    <h3 class="text-lg md:text-xl font-semibold text-indigo-700 mb-3">
                        Track Sources and Avoid Spam  
                    </h3>

                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        With the help of the Gmail Generator, users can generate aliases for different platforms. It
                        allows users to track the origin of email and spam.  
                    </p>

                    <p class="text-gray-700 text-sm mb-2">
                        The user can:  
                    </p>

                    <ul class="list-disc pl-5 text-gray-600 text-sm space-y-1">
                        <li>Identify the source of data sharing and leaks.</li>
                        <li>Easily unsubscribe from the unwanted services.</li>

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
            How Gmail Generator Works? 
        </h2>

        <p class="text-gray-600 max-w-3xl mx-auto text-sm md:text-base leading-relaxed">
            How Gmail Generator Works? 
            The Gmail Generator tool is designed to create email variations on how Gmail naturally handles email
            addresses. Gmail ignores the dots in the username and supports any aliases using the "+" symbol. This allows
            for generating multiple variations of the same steps.  
            The process of generating a variation of a Gmail address using the Gmail Generator tool is easy and simple.
        </p>

        <p class="text-gray-500 mt-4 text-sm">
            The process involves:
        </p>

        <!-- STEPS -->


        <!-- BOTTOM CONTENT -->
        <div class="max-w-[1300px] mx-auto px-6 mt-10">

            <!-- Step 1 -->


            <div class="relative rounded-2xl p-8 pt-10 bg-[#daf8ec]">
                <div class="absolute top-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
                <div class="grid md:grid-cols-2   gap-6">
                    <div class="text-start px-0 md:px-4">
                        <h3 class="text-[29px] font-semibold text-[#1f2a7a]  mb-4">
                            For Dot Variation 
                        </h3>
                        <ul class="list-disc px-0 md:px-4">
                            <li>Enter the Gmail account address. </li>
                            <li>Click on Generate.  </li>
                            <li>Copy the Preferred email address from the generated list. </li>
                        </ul>
                    </div>
                    <div class="text-start px-0 md:px-4">
                        <h3 class="text-[29px] font-semibold text-[#1f2a7a]  mb-4">
                            For Plus Aliases
                        </h3>
                        <ul class="list-disc px-0 md:px-4">
                            <li>Enter the Gmail account address.  </li>
                            <li>Select the preferred preset from Quick Presets. (Optional) .  </li>
                            <li>Click on Generate. </li>
                            <li>Copy the Preferred email address from the generated list. </li>
                        </ul>
                    </div>

                </div>

                <div class="absolute bottom-3 left-0 w-full h-2 bg-white rounded-t-2xl"></div>
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
                            Email Spam Checker
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Analyze any emails for spam triggers and improve your deliverability instantly with
                            <a href="https://www.extractmails.com/tools/email-spam-checker/" class="text-indigo-600 hover:underline">
                                Email Spam Checker ›
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
                            <a href="https://www.extractmails.com/tools/ai-email-writer/" class="text-indigo-600 hover:underline">
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
                            Gmail Generator
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Create custom or temporary Gmail addresses, which are ideal for testing and quick usage with
                            the
                            <a href="https://www.extractmails.com/tools/gmail-generator/" class="text-indigo-600 hover:underline">
                                Gmail Generator tool ›
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
                            Is Gmail Generator safe to use?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <!-- CONTENT -->
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            If the Gmail Generator tool uses "The Dot Trick" or "The Plus Trick", it is safe to use, as
                            it does not violate Google's Terms of Service. On the other hand, if the tool generates new
                            accounts using bots, it is not safe to use.
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
                            Do generated emails create new Gmail accounts?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            Emails generated using the Gmail Generator tool of ExtractMails do not automatically create
                            new and independent Gmail accounts. The tool generally creates aliases that can be traced
                            back to the single primary inbox.
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
                            Can I receive emails on all generated addresses?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            Yes, you can receive emails on all types of generated or alternative email addresses. Email
                            addresses generated by the Gmail Generator tool of ExtractMails create alias addresses that
                            flow into a single primary inbox.
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
                            Is this method supported by Gmail officially?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            The Gmail Generator tool of ExtractMails uses "The Plus Trick" or "The Dot Trick", which is
                            officially supported by Gmail as a method of creating "Aliases".
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
                            Can I use these emails for multiple signups?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            Yes, you can use one email address to sign up for multiple websites, using the "+" or "."
                            techniques.

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
                            What is the difference between dots and plus aliases?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            In Gmail, both the "." and "+" variations are used to create email variations, which deliver
                            emails to the primary address. The primary difference between the two variations is that the
                            "." variation cannot be used to create filters, whereas the "+" variation can be used to
                            create filters.
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
                            Is there a limit to how many variations I can generate?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            There is no limit to the number of variations you can generate from a single address using
                            the "." trick. Although the Gmail Generator tool of ExtractMails offers 256 variations in
                            its basic version.
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
                            Do websites detect Gmail variations as the same email?
                        </h3>

                        <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
                    </div>

                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <p class="mt-4 text-[14px] text-gray-500">
                            Yes, almost all the websites and online services detect the variations of Gmail, especially
                            "." and "+" aliases as the same email address.
                        </p>
                    </div>

                </div>
            </div>
        </div>


    </div>

</section>


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
        const form = document.getElementById('gmail-generator-form');
        const addressInput = document.getElementById('gmail-address');
        const errorBox = document.getElementById('generator-error');
        const aliasCount = document.getElementById('alias-count');
        const resultsList = document.getElementById('results-list');
        const tryNowLink = document.querySelector('.extractmail-try-now-link');
        const generatorSection = document.getElementById('extractmail-gmail-generator-section');
        let latestResults = [];

        if (tryNowLink && generatorSection) {
            tryNowLink.addEventListener('click', function(event) {
                const header = document.querySelector('.site-header, #masthead, header');
                const headerHeight = header ? header.getBoundingClientRect().height : 0;
                const scrollGap = Math.max(headerHeight + 28, 110);
                const targetTop = generatorSection.getBoundingClientRect().top + window.pageYOffset - scrollGap;

                event.preventDefault();
                window.scrollTo({
                    top: Math.max(targetTop, 0),
                    behavior: 'smooth'
                });
            });
        }

        function normalizeAddress(value) {
            return value.toLowerCase().trim();
        }

        function parseAddress(value) {
            const normalized = normalizeAddress(value);
            const match = normalized.match(/^([^@\s]+)@([^@\s]+)$/);

            if (!match) {
                return null;
            }

            return {
                baseLocal: match[1].replace(/\./g, ''),
                domain: match[2]
            };
        }

        function generateDotVariants(baseLocal, domain, limit) {
            const chars = baseLocal.split('');
            const results = [];

            function build(index, current) {
                if (results.length >= limit) {
                    return;
                }

                if (index === chars.length - 1) {
                    results.push(current + chars[index] + '@' + domain);
                    return;
                }

                build(index + 1, current + chars[index]);

                if (results.length >= limit) {
                    return;
                }

                build(index + 1, current + chars[index] + '.');
            }

            if (!chars.length) {
                return results;
            }

            build(0, '');

            return Array.from(new Set(results)).slice(0, limit);
        }

        function showError(message) {
            errorBox.textContent = message;
            errorBox.classList.remove('hidden');
        }

        function clearError() {
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
        }

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const parsed = parseAddress(addressInput.value);

            if (!parsed) {
                showError('Please enter a valid email address.');
                return;
            }

            clearError();
            latestResults = generateDotVariants(parsed.baseLocal, parsed.domain, 256);
            aliasCount.textContent = String(latestResults.length);
            resultsList.textContent = latestResults.join('\n');
        });
    });
</script>


<style id="faq-style">
    .faq-content {
        transition: all 0.3s ease;
    }
</style>


<?php get_footer() ?>
