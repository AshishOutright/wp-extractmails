<?php
/*
Template Name: about-us
*/
get_header()
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@waline/client@3/dist/waline.css">

<script type="module">
  import { init } from "https://cdn.jsdelivr.net/npm/@waline/client@3/dist/waline.js";

  init({
    el: "#waline",
    serverURL: "YOUR_SERVER_URL",
  });
</script>

 <style>
  /* @keyframes spin-slow {
  from {
    transform: translateX(-50%) rotate(0deg);
  }
  to {
    transform: translateX(-50%) rotate(360deg);
  }
}

.animate-spin-slow {
  animation: spin-slow 40s linear infinite;
} */



  @keyframes slideUpFade {
    0% {
      opacity: 0;
      transform: translateY(130px);
    }

    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-slide-up {
    animation: slideUpFade 1.2s ease-out forwards;
  }
</style>








<section
    class="relative h-[590px] overflow-hidden"
    style="background-image: url('https://www.mailsextract.com/wp-content/uploads/2026/08/colleagues-giving-fist-bump-1-2.png'); background-size: cover; background-position: center;"
>

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/30"></div>

    <!-- Content -->
    <div class="relative z-10 flex h-full flex-col items-center justify-center px-4 text-center pt-24">

        <!-- About Us Badge -->
        <span
            class="mb-6 inline-flex items-center rounded-full bg-[#ff4d22] px-6 py-2 text-sm font-medium text-white animate-slide-up">
            About Us
        </span>

        <!-- Heading -->
        <h1
            class="max-w-[1200px] text-4xl font-bold leading-tight text-white md:text-6xl animate-slide-up ">
            Simplifying Email Automation for
            <br>
            Modern Businesses
        </h1>

        <!-- Description -->
        <p
            class="mt-6 max-w-3xl text-lg leading-relaxed text-white/90 md:text-xl animate-slide-up ">
            Automate email data extraction, streamline workflows, and connect to your business systems,
            transforming emails into structured records without manual effort.
        </p>

        <!-- Buttons -->
        <div class="mt-10 flex flex-wrap items-center justify-center gap-4 animate-slide-up">

            <a href="#"
                class="rounded-full bg-white px-8 py-3 text-base font-semibold text-black shadow-lg transition hover:scale-105">
                Start Parsing Free
            </a>

            <a href="#"
                class="rounded-full border border-white px-8 py-3 text-base font-medium text-white transition hover:bg-white hover:text-black">
                Talk to Sales
            </a>

        </div>

    </div>

</section>



<section class="bg-[#f3f3f3] py-16 px-2 md:px-12">
    <div class="max-w-[1600px] mx-auto">

        <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-4 items-center">

            <!-- LEFT SIDE -->
            <div class="px-2">

                <!-- Badge -->
                <div
                    class="inline-flex items-center px-12 py-1 rounded-full bg-[#bde7f7] text-[#1a1a1a] text-[17px] font-semibold shadow-xl reveal-up ">
                    Our Mission
                </div>

                <!-- Heading -->
                <h2 class="mt-6 text-4xl lg:text-6xl font-bold leading-tight text-[#111] reveal-up ">
                    Smarter Email Automation.
                    <span class="block text-[#ff4d22]">
                        Eliminating Manual Work
                    </span>
                </h2>

                <!-- Content Box -->
                <div
                    class="mt-10 bg-[#ff4d22] rounded-[24px] p-8 lg:p-10 shadow-lg max-w-[1200px] reveal-up ">

                    <p class="text-white leading-8 text-lg">
                        MailsExtract empowers organizations to automate
                        email-driven workflows, eliminating repetitive tasks,
                        and saving organizational time. Manual email processing
                        creates bottlenecks, which increase costs and limit
                        productivity.
                    </p>

                    <p class="text-white leading-8 text-lg mt-8">
                        By connecting inboxes with business systems,
                        MailsExtract helps teams operate efficiently and make
                        faster and more informed decisions.
                    </p>

                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="relative mx-auto">

                <!-- Connector Lines -->
             
<div class="hidden lg:block absolute inset-0 pointer-events-none  reveal-up">

  
<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">

    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>

</div>

<style>
.wave {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80px;
    height: 80px;
    border: 4px solid #2bb6ff;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: radarWave 4s linear infinite;
}

/* Delays create continuous waves */
.wave:nth-child(1) {
    animation-delay: 0s;
}

.wave:nth-child(2) {
    animation-delay: 1s;
}

.wave:nth-child(3) {
    animation-delay: 2s;
}

.wave:nth-child(4) {
    animation-delay: 3s;
}

@keyframes radarWave {
    0% {
        width: 80px;
        height: 80px;
        opacity: 1;
    }

    100% {
        width: 400px;
        height: 400px;
        opacity: 0;
    }
}
</style>




</div>



                <!-- Grid -->
                <div class="relative z-10 grid grid-cols-2 gap-2 w-full">

    <!-- LEFT COLUMN -->
    <div class="flex flex-col gap-2 w-full">

        <!-- Top Small -->
        <div class="relative h-[250px] rounded-[24px] overflow-hidden ">

            <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Mask-group-7.png"
                class="w-full h-full object-cover">

            <!-- overlay -->
           

            <div class="absolute top-5 left-12 text-white">

                <div class="flex items-start">
                    <span class="counter text-6xl font-bold"
                        data-target="545">0</span>

                    <span class="text-[#ff4d22] text-5xl font-bold">
                        +
                    </span>
                </div>

                <p class="text-[23px]">Annual Partners</p>

            </div>

        </div>

        <!-- Bottom Large -->
        <div class="h-[250px] rounded-[24px] overflow-hidden ">

            <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Mask-group-9.png"
                class="w-full h-full object-cover">

        </div>

    </div>


    <!-- RIGHT COLUMN -->
    <div class="flex flex-col gap-2 pt-0">

        <!-- Top Large -->
        <div class="h-[320px] rounded-[24px] overflow-hidden shadow-xl">

            <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Mask-group-8.png"
                class="w-full h-full object-cover">

        </div>

        <!-- Bottom Small -->
        <div class="relative h-[180px] rounded-[24px] overflow-hidden ">

            <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Mask-group-10.png"
                class="w-full h-full object-cover">

          

            <div
                class="absolute inset-0 flex flex-col items-center justify-center text-white">

                <div class="flex items-start">

                    <span class="counter text-5xl font-bold"
                        data-target="48">0</span>

                    <span class="text-[#ff4d22] text-4xl font-bold">
                        +
                    </span>

                </div>

                <p>Step Setup</p>

            </div>

        </div>

    </div>

</div>

                </div>

            </div>

        </div>

    </div>
</section>



<script>
  
document.addEventListener("DOMContentLoaded", () => {

    const counters = document.querySelectorAll(".counter");

    const observer = new IntersectionObserver((entries) => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {

                const counter = entry.target;
                const target = +counter.dataset.target;

                let current = 0;
                const duration = 2000;
                const increment = target / (duration / 16);

                const updateCounter = () => {

                    current += increment;

                    if (current < target) {

                        counter.textContent = Math.floor(current);

                        requestAnimationFrame(updateCounter);

                    } else {

                        counter.textContent = target;

                    }
                };

                updateCounter();

                observer.unobserve(counter);
            }
        });

    }, {
        threshold: 0.5
    });

    counters.forEach(counter => observer.observe(counter));

});
</script>


<style>
    .swiper-wrapper-container {
    overflow: visible;
}

.swiper {
    overflow: hidden;
    padding: 20px 10px!important; /* space for hover animation */
}
.swiper {
    overflow: visible !important;
     overflow-x: hidden!important;
}
</style>



<!-- Swiper CSS -->
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<section class="drives-section">
    <div class="container">

        <h2 class="section-title reveal-up">What Drives Us?</h2>

        <div class="swiper drives-slider  reveal-up">

            <div class="swiper-wrapper">

                <!-- Card 1 -->
                <div class="swiper-slide">
                    <div class="card-wrap">
                        <div class="card-bg"></div>

                        <div class="drive-card">
                            <div class="icon">
                              <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Group-98-2.png" alt="">
                            </div>

                            <h3>Efficient Automation</h3>

                            <p>
                                Every capability designed to reduce repetitive
                                tasks and eliminate bottlenecks.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="swiper-slide">
                    <div class="card-wrap">
                        <div class="card-bg"></div>

                        <div class="drive-card">
                            <div class="icon">
                               <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Group-98-2.png" alt="">
                            </div>

                            <h3>Customer Success</h3>

                            <p>
                                Solutions aimed at solving real business
                                challenges.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="swiper-slide">
                    <div class="card-wrap">
                        <div class="card-bg"></div>

                        <div class="drive-card">
                            <div class="icon">
                               <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Group-98-2.png" alt="">
                            </div>

                            <h3>Unmatched Accuracy</h3>

                            <p>
                                Built to extract and structure information
                                with enhanced precision.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="swiper-slide">
                    <div class="card-wrap">
                        <div class="card-bg"></div>

                        <div class="drive-card">
                            <div class="icon">
                             <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Group-98-2.png" alt="">
                            </div>

                            <h3>Continuous Innovation</h3>

                            <p>
                                We constantly evolve to provide smarter,
                                faster solutions.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="slider-nav">
            <div class="swiper-button-prev custom-prev"><img src="https://www.mailsextract.com/wp-content/uploads/2026/06/arrow_back-2.png" alt=""></div>
            <div class="swiper-button-next custom-next"><img src="https://www.mailsextract.com/wp-content/uploads/2026/06/arrow_back-3.png" alt=""></div>
        </div>

    </div>

   <div class="circle bottom-left">
    <span class="ring ring-2"></span>
    <span class="ring ring-3"></span>
    <span class="ring ring-4"></span>
</div>

<div class="circle top-right">
    <span class="ring ring-2"></span>
    <span class="ring ring-3"></span>
    <span class="ring ring-4"></span>
</div>

</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    new Swiper(".drives-slider", {
        slidesPerView: 3,
        spaceBetween: 40,
        loop: true,
        speed: 700,

        navigation: {
            nextEl: ".custom-next",
            prevEl: ".custom-prev",
        },

        breakpoints: {
            0: {
                slidesPerView: 1
            },
            768: {
                slidesPerView: 2
            },
            1024: {
                slidesPerView: 3
            }
        }
    });
</script>



<style>
    .drives-section {
    position: relative;
    background: #edf5f8;
    padding: 80px 20px;
    overflow: hidden;
}

.container {
    max-width: 1200px;
    margin: auto;
}

.section-title {
    text-align: center;
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 60px;
}

/* CARD */
.card-wrap {
    position: relative;
    width: 100%;
    height: 250px;
}

/* RED CARD */
.card-bg {
    position: absolute;
    inset: 0;
    background: #ff4d22;
    border-radius: 16px;
    transform: rotate(-4deg);
    transition: all .45s ease;
}

/* WHITE CARD */
.drive-card {
    position: absolute;
    inset: 0;
    background: #fff;
    border-radius: 16px;
    padding: 35px 25px;
    text-align: center;

    display: flex;
    flex-direction: column;
    align-items: center;

    box-shadow: 0 8px 18px rgba(0,0,0,.12);

    transition: all .45s ease;
}

/* HOVER EFFECT */
.card-wrap:hover .drive-card {
    transform: translate(-10px,-10px);
}

.card-wrap:hover .card-bg {
    transform: rotate(0deg) translate(10px,10px);
}

.icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: #ff4d22;

    color: white;
    font-size: 24px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 20px;
}

.drive-card h3 {
    font-size: 22px;
    margin-bottom: 18px;
}

.drive-card p {
    color: #555;
    line-height: 1.7;
}

/* NAVIGATION */
.slider-nav {
    display: flex;
    justify-content: center;
    gap: 18px;
    margin-top: 45px;
}

.custom-prev,
.custom-next {
    position: static !important;

    width: 52px !important;
    height: 52px !important;

    border-radius: 50%;
    background: #000;

    color: white !important;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 22px;
    cursor: pointer;

    box-shadow: 0 5px 15px rgba(0,0,0,.2);

    transition: .3s;
}

.custom-prev:hover,
.custom-next:hover {
    transform: translateY(-4px);
}

.swiper-button-prev::after,
.swiper-button-next::after {
    display: none;
}
.circle {
    position: absolute;
    width: 290px;
    height: 290px;
}

/* Base ring styles */
.circle::before,
.circle .ring {
    content: "";
    position: absolute;
    inset: 0;
    border: 3px solid #00b8ff;
    border-radius: 50%;
    opacity: 0;
}

/* Animation */
@keyframes ripple {
    0% {
        transform: scale(0);
        opacity: 1;
    }

    70% {
        opacity: 0.5;
    }

    100% {
        transform: scale(1);
        opacity: 0;
    }
}

/* Ring 1 */
.circle::before {
    animation: ripple 4s linear infinite;
}

/* Ring 2 */
.circle .ring-2 {
    animation: ripple 4s linear 1s infinite;
}

/* Ring 3 */
.circle .ring-3 {
    animation: ripple 4s linear 2s infinite;
}

/* Ring 4 */
.circle .ring-4 {
    animation: ripple 4s linear 3s infinite;
}

/* Positioning */
.top-right {
    top: -15px;
    right: -135px;
}

.bottom-left {
    bottom: -135px;
    left: -135px;
}
</style>





<section class="bg-[#f4f4f4] py-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">

        <!-- Heading -->
        <div class="text-center mb-20  reveal-up">
            <h2 class="text-4xl font-bold text-black">
                Our Journey Towards
            </h2>

            <h3 class="text-4xl font-bold text-[#ff5a2c] mt-2">
                Smarter Email Automation
            </h3>
        </div>

        <div class="relative">

            <!-- Center Line -->
            <div class="absolute left-1/2 top-20 bottom-0 hidden lg:block">
             
<div
    class="h-[90%] w-[3px]"
    style="
        background: repeating-linear-gradient(
            to bottom,
            #d1d5db 0px,
            #d1d5db 10px,
            transparent 10px,
            transparent 20px
        );
    ">
</div>


            </div>

            <!-- ITEM 01 -->
            <div class="grid lg:grid-cols-2 gap-16 items-center relative mb-28 timeline-item">

                <!-- Number -->
                <div
                    class="absolute hidden lg:flex left-1/2 -translate-x-1/2 items-center justify-center w-16 h-16 rounded-full border-4 border-[#ff5a2c] bg-white z-20">
                    <span class="text-3xl font-bold">01</span>
                </div>

                <!-- Content -->
                <div class="lg:pr-28 text-right animate-left">

                    <h4 class="text-2xl font-bold mb-5">
                        The Problem We Discovered
                    </h4>

                    <p class="text-gray-600 leading-8">
                        Businesses were spending countless hours manually copying information from
                        emails to CRMs, spreadsheets, and other business applications.
                        Every single information, like customer inquiries, orders, invoices,
                        and booking details, required repetitive data entry.
                    </p>
                </div>

                <!-- Image -->
                <div class="relative animate-right">

                    <div
                        class="w-52 h-52 bg-[#ff5a2c] rounded-full flex items-center justify-center mx-auto">

                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/issue-1-1.png"
                            class="w-28 transition duration-500 hover:scale-110"
                            alt="">
                    </div>

                    <!-- Connector -->
                  

                </div>

            </div>


            <!-- ITEM 02 -->
            <div class="grid lg:grid-cols-2 gap-16 items-center relative mb-28 timeline-item">

                <div
                    class="absolute hidden lg:flex left-1/2 -translate-x-1/2 items-center justify-center w-16 h-16 rounded-full border-4 border-[#ff5a2c] bg-white z-20">
                    <span class="text-3xl font-bold">02</span>
                </div>

                <!-- Image -->
                <div class="relative order-2 lg:order-1 animate-left">

                    <div
                        class="w-52 h-52 bg-[#ff5a2c] rounded-full flex items-center justify-center mx-auto">

                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/solution-1.png"
                            class="w-28 transition duration-500 hover:scale-110"
                            alt="">
                    </div>

                   

                </div>

                <!-- Content -->
                <div class="order-1 lg:order-2 lg:pl-28 animate-right">

                    <h4 class="text-2xl font-bold mb-5">
                        Building the First Solution
                    </h4>

                    <p class="text-gray-600 leading-8 mb-12">
                        We determined to eliminate manual email processing.
                        Our team developed the first version of MailExtract.
                        It offered a simple goal: automatically extract important
                        information from emails.
                    </p>

                     <div
                        class="hidden  lg:block absolute bottom-0  w-[80%] h-[3px] " style="
        background: repeating-linear-gradient(
            to right,
            #d1d5db 0px,
            #d1d5db 10px,
            transparent 10px,
            transparent 30px
        );
    ">
                    </div>

                </div>

            </div>


            <!-- ITEM 03 -->
            <div class="grid lg:grid-cols-2 gap-16 items-center relative mb-28 timeline-item">

                <div
                    class="absolute hidden lg:flex left-1/2 -translate-x-1/2 items-center justify-center w-16 h-16 rounded-full border-4 border-[#ff5a2c] bg-white z-20">
                    <span class="text-3xl font-bold">03</span>
                </div>

                <!-- Content -->
                <div class="lg:pr-28 text-right animate-left">

                    <h4 class="text-2xl font-bold mb-5">
                        Expanding Automation and Integrations
                    </h4>

                    <p class="text-gray-600 leading-8 mb-12">
                        As the needs of clients evolved, we enhanced our platform
                        significantly with intelligent field mapping, attachment processing,
                        custom parsing rules, APIs, and CRM integrations.
                    </p>

                     <div
                        class="hidden  lg:block absolute bottom-0  w-[80%] h-[3px] " style="
        background: repeating-linear-gradient(
            to right,
            #d1d5db 0px,
            #d1d5db 10px,
            transparent 10px,
            transparent 30px
        );
    ">
                    </div>

                </div>

                <!-- Image -->
                <div class="relative animate-right">

                    <div
                        class="w-52 h-52 bg-[#ff5a2c] rounded-full flex items-center justify-center mx-auto">

                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/scalable-1.png"
                            class="w-28 transition duration-500 hover:scale-110"
                            alt="">
                    </div>

                   

                </div>

            </div>


            <!-- ITEM 04 -->
            <div class="grid lg:grid-cols-2 gap-16 items-center relative timeline-item">

                <div
                    class="absolute hidden lg:flex left-1/2 -translate-x-1/2 items-center justify-center w-16 h-16 rounded-full border-4 border-[#ff5a2c] bg-white z-20">
                    <span class="text-3xl font-bold">04</span>
                </div>

                <!-- Image -->
                <div class="relative order-2 lg:order-1 animate-left">

                    <div
                        class="w-52 h-52 bg-[#ff5a2c] rounded-full flex items-center justify-center mx-auto">

                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/empower-1-1.png"
                            class="w-28 transition duration-500 hover:scale-110"
                            alt="">
                    </div>

                  

                </div>

                <!-- Content -->
                <div class="order-1 lg:order-2 lg:pl-28 animate-right">

                    <h4 class="text-2xl font-bold mb-5">
                        Empowering Businesses Through Automation
                    </h4>

                    <p class="text-gray-600 leading-8 mb-12">
                        Presently, MailExtract is helping organizations globally
                        convert emails into structured business data. It automates
                        workflows and improves accuracy and scalability.
                    </p>

                    <div
                        class="hidden  lg:block absolute bottom-0  w-[80%] h-[3px] " style="
        background: repeating-linear-gradient(
            to right,
            #d1d5db 0px,
            #d1d5db 10px,
            transparent 10px,
            transparent 30px
        );
    ">
                    </div>

                </div>

            </div>

        </div>
    </div>
</section>

<style>
    .animate-left,
    .animate-right {
        opacity: 0;
        transition: all 0.8s ease;
    }

    .animate-left {
        transform: translateX(-80px);
    }

    .animate-right {
        transform: translateX(80px);
    }

    .show .animate-left,
    .show .animate-right {
        opacity: 1;
        transform: translateX(0);
    }

    .timeline-item {
        transition: 0.5s;
    }

    .timeline-item:hover img {
        transform: scale(1.1) rotate(5deg);
    }
</style>


<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {

            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }

        });
    }, {
        threshold: 0.25
    });

    document.querySelectorAll('.timeline-item').forEach(item => {
        observer.observe(item);
    });
</script>





<style>
.reveal-up {
    opacity: 0;
    transform: translateY(60px);
    transition: all 0.99s ease;
}

.reveal-up.active {
    opacity: 1;
    transform: translateY(0);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const elements = document.querySelectorAll(".reveal-up");

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {

            if (entry.isIntersecting) {
                entry.target.classList.add("active");
            }

        });
    }, {
        threshold: 0.15
    });

    elements.forEach(el => observer.observe(el));
});
</script>




<?php get_footer() ?>
