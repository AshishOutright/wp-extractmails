<?php
/*
Template Name: guestpostcrm
*/
get_header()
?>
<!-- ================= HERO SECTION ================= -->
<!-- HERO SECTION -->


<style>






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


  input {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    -webkit-appearance: none;
}

input:focus {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
}
</style>



<section class="guest-crm-hero relative overflow-hidden bg-[#fffffe] max-w-[1800px] mx-auto rounded-b-[40px] lg:px-10 ">

  <!-- EXACT STYLE GRADIENT BLOBS -->
  <div class="absolute inset-0 overflow-hidden pointer-events-none">

    <!-- TOP LEFT LARGE -->
    <div class="absolute 
                -top-[80px] left-[11%]
                w-[350px] h-[200px]
                bg-[#fff6ba]
                blur-[50px]
                opacity-90
                rounded-full">
    </div>

    <!-- TOP CENTER -->
    <div class="absolute
                -top-[220px] left-[32%]
                w-[700px] h-[380px]
                bg-[#fff0a8]
                blur-[150px]
                opacity-80
                rounded-full">
    </div>

    <div class="absolute 
                -top-[80px] right-[11%]
                w-[350px] h-[200px]
                bg-[#fff6ba]
                blur-[50px]
                opacity-90
                rounded-full">
    </div>







    <!-- LEFT SIDE -->
    <!-- <div class="absolute
                top-[180px] -left-[180px]
                w-[380px] h-[620px]
                bg-[#fff7c9]
                blur-[140px]
                opacity-90
                rounded-full">
    </div> -->

    <!-- BOTTOM LEFT -->
    <div class="absolute
                -bottom-[120px] -left-[16%]
                w-[780px] h-[420px]
                bg-[#fff5c5]
                blur-[150px]
                opacity-90
                rounded-full">
    </div>


    <div class="absolute
                -bottom-[120px] left-[16%]
                w-[780px] h-[420px]
                bg-[#fff5c5]
                blur-[100px]
                opacity-90
                rounded-full">
    </div>

  

    <!-- RIGHT BOTTOM -->
    <div class="absolute
                -bottom-[10px] right-[4%]
                w-[300px] h-[220px]
                bg-[#fff5c7]
                blur-[150px]
                opacity-90
                rounded-full">
    </div>

  </div>

  <!-- CONTENT -->
  <div class="guest-crm-content relative z-10 max-w-[1550px] mx-auto px-6 pt-40 pb-36">

    <div class="guest-crm-grid grid lg:grid-cols-[1.1fr_0.9fr] items-center gap-0">

      <!-- LEFT CONTENT -->
      <div class="guest-crm-copy">

        <!-- HEADING -->
      <h1
  class="guest-crm-title
         text-[clamp(2.125rem,5vw,5rem)]
         leading-[1.05]
         font-semibold
         font-cormorant
         tracking-[-0.02em]
         text-black
         animate-slide-up"
>
  Automatically <br />
  Converts Emails into <br />

  <span class="text-[#ff5b2e]">
    Actionable CRM Data
  </span>
</h1>

        <!-- SUBTEXT -->
        <p class="max-w-[810px]  guest-crm-description mt-10 mb-16 text-[24px] text-[#282827]  font-Regular font-manrope animate-slide-up ">
          Stop manually copying information from emails. MailsExtract lets you automatically extract information from incoming emails and syncs them directly to your CRM. 
        </p>

        <!-- INPUT -->
        <div class="guest-crm-form mt-10 mb-10 flex items-center
                    bg-white
                    rounded-full
                    shadow-[0_10px_30px_rgba(0,0,0,0.08)]
                    border border-white/80
                    overflow-hidden
                    max-w-[720px]
                    h-[58px]">

          <input
  type="email"
  placeholder="Enter Your Mail.."
  class="flex-1 h-full px-7 bg-transparent text-[15px] text-gray-700
         border-none outline-none ring-0
         focus:outline-none focus:ring-0 focus:border-transparent
         focus:shadow-none"
/>

          <button class="mr-2 group relative overflow-hidden
                         h-[38px]
                         px-8
                         rounded-full
                         bg-black
                         text-white
                         text-sm
                         font-medium
                         hover:bg-[#111] transition
                        ">


                         <span
  class="absolute inset-0 bg-[#FD4821] text-[#ffff]
  scale-x-0 origin-left
  transition-transform duration-500
  group-hover:scale-x-100"
></span>
<span class="relative z-10">
            Get Started
            </span>
          </button>
        </div>

      </div>

      <!-- RIGHT VISUAL -->
      <!-- RIGHT VISUAL -->
<!-- RIGHT VISUAL -->
<!-- RIGHT VISUAL -->
<div class="guest-crm-visual relative flex justify-center lg:justify-end">

    <div class="flip-container">

        <img 
            id="flipImage"
            src="https://www.mailsextract.com/wp-content/uploads/2026/05/Group-76-3.png"
            class="flip-image"
        >

    </div>

</div>

<style>

.flip-container{
    position: relative;

    width: 500px;
    height: 500px;

    perspective: 2500px;
}

.flip-image{
    position: absolute;

    width: 100%;
    height: 100%;

    top: 0;
    left: 0;

    object-fit: contain;

    border-radius: 20px;

    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;

    transition: transform 0.8s ease-in-out;
}

</style>

<script>

const images = [
    "https://www.mailsextract.com/wp-content/uploads/2026/05/Group-76-3.png",
    "https://www.mailsextract.com/wp-content/uploads/2026/05/Group-65-3.png",
    "https://www.mailsextract.com/wp-content/uploads/2026/05/Group-66-9.png"
];

let index = 0;

const flipImage = document.getElementById("flipImage");

function rotateImages(){

    /* FLIP TO HALF */
    flipImage.style.transform = "rotateY(90deg)";

    setTimeout(() => {

        /* CHANGE IMAGE WHEN HIDDEN */
        index = (index + 1) % images.length;

        flipImage.src = images[index];

        /* RESET WITHOUT ANIMATION */
        flipImage.style.transition = "none";
        flipImage.style.transform = "rotateY(-90deg)";

        /* FORCE REFRESH */
        flipImage.offsetHeight;

        /* SHOW NEW IMAGE SMOOTHLY */
        flipImage.style.transition = "transform 0.8s ease-in-out";
        flipImage.style.transform = "rotateY(0deg)";

    }, 800);
}

setInterval(rotateImages, 3000);

</script>
  </div>

  <!-- EXACT BOTTOM BRAND SECTION -->
  <div class="guest-crm-logos absolute bottom-[-20px] left-1/2 -translate-x-1/2 z-20 w-full flex justify-center px-6">

    <!-- MAIN CONTAINER -->
    <div class="logo-slider">

        <!-- LEFT BLUR -->
        <div class="blur-left"></div>

        <!-- RIGHT BLUR -->
        <div class="blur-right"></div>

        <!-- SLIDER TRACK -->
        <div class="logo-track">

            <!-- SET 1 -->

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-15-1.png">
            </div>

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-15-1.png">
            </div>

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-17.png">
            </div>

            <div class="logo-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Twilio-logo-red.svg">
            </div>

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-15-1.png">
            </div>

            <!-- DUPLICATE FOR INFINITE LOOP -->

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-15-1.png">
            </div>

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-15-1.png">
            </div>

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-17.png">
            </div>

            <div class="logo-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/7e/Twilio-logo-red.svg">
            </div>

            <div class="logo-item">
                <img src="https://www.mailsextract.com/wp-content/uploads/2026/05/image-15-1.png">
            </div>

        </div>

    </div>

</div>

<style>

.logo-slider{
    position: relative;

    overflow: hidden;

    width: 100%;
    max-width: 1200px;

    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(14px);

    border-radius: 32px;

    padding: 45px 0;

    box-shadow: 0 15px 40px rgba(0,0,0,0.06);
}

/* TRACK */

.logo-track{
    display: flex;
    align-items: center;
    gap: 80px;

    width: max-content;

    animation: scrollLogos 25s linear infinite;
}

/* PAUSE ON HOVER */

.logo-slider:hover .logo-track{
    animation-play-state: paused;
}

/* ITEMS */

.logo-item{
    flex-shrink: 0;

    transition: transform 0.4s ease;
}

.logo-item img{
    height: 42px;

    opacity: 1;

    transition: all 0.4s ease;
}

/* SCALE EFFECT */

.logo-item:hover{
    transform: scale(1.15);
}

.logo-item:hover img{
    opacity: 1;
}

/* SMOOTH INFINITE SLIDE */

@keyframes scrollLogos{

    0%{
        transform: translateX(0);
    }

    100%{
        transform: translateX(-50%);
    }
}

/* LEFT BLUR */

.blur-left {
    position: absolute;
    left: 0;
    top: 0;

    width: 250px; /* Increased from 120px */
    height: 100%;

    z-index: 10;

    background: linear-gradient(
        to right,
        rgba(255,255,255,1) 0%,
        rgba(255,255,255,0.95) 20%,
        rgba(255,255,255,0.7) 45%,
        rgba(255,255,255,0.3) 70%,
        rgba(255,255,255,0) 100%
    );
}

/* RIGHT BLUR */
.blur-right {
    position: absolute;
    right: 0;
    top: 0;

    width: 220px; /* Increased from 120px */
    height: 100%;

    z-index: 10;

    background: linear-gradient(
        to left,
        rgba(255,255,255,1) 0%,
        rgba(255,255,255,0.95) 15%,
        rgba(255,255,255,0.75) 40%,
        rgba(255,255,255,0.35) 70%,
        rgba(255,255,255,0) 100%
    );
}





/* Responsive behavior for this hero only. Desktop UI remains unchanged. */
@media (max-width: 1023px) {
    .guest-crm-content {
        padding: 100px 32px 155px;
    }

    .guest-crm-grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 28px;
    }

    .guest-crm-copy {
        max-width: 760px;
        margin-inline: auto;
        text-align: center;
    }

    .guest-crm-title {
        font-size: 56px;
        line-height: 1.06;
        
    }

    .guest-crm-description {
        max-width: 680px;
        margin: 30px auto 36px;
    }

    .guest-crm-form {
        margin: 0 auto 18px;
    }

    .guest-crm-visual {
        justify-content: center;
    }

    .flip-container {
        width: 390px;
        height: 390px;
    }

    .guest-crm-logos {
        padding-inline: 28px;
    }

    .logo-slider {
        max-width: 820px;
        padding-block: 30px;
    }

    .logo-track {
        gap: 58px;
    }
}

@media (max-width: 767px) {
    .guest-crm-hero {
        border-radius: 0 0 24px 24px;
    }

    .guest-crm-content {
        padding: 92px 18px 122px;
    }

    .guest-crm-grid {
        gap: 18px;
    }

    .guest-crm-title {
        font-size: clamp(32px, 9.2vw, 44px);
        line-height: 1.08;
        letter-spacing: .5px;
        overflow-wrap: anywhere;
    }

    .guest-crm-description {
        margin: 24px auto 28px;
        font-size: 16px;
        line-height: 1.7;
    }

    .guest-crm-form {
        width: 100%;
        height: 50px;
        margin: 0 auto 8px;
    }

    .guest-crm-form input {
        min-width: 0;
        padding-inline: 18px;
        font-size: 14px;
    }

    .guest-crm-form button {
        flex: 0 0 auto;
        height: 38px;
        padding-inline: 20px;
        margin-right: 6px;
        white-space: nowrap;
    }

    .flip-container {
        width: min(100%, 330px);
        height: auto;
        aspect-ratio: 1;
    }

    .guest-crm-logos {
        bottom: -12px;
        padding-inline: 14px;
    }

    .logo-slider {
        padding-block: 22px;
        border-radius: 22px;
    }

    .logo-track {
        gap: 42px;
    }

    .logo-item img {
        width: auto;
        max-width: 112px;
        height: 27px;
    }

    .blur-left,
    .blur-right {
        width: 55px;
    }
}

@media (max-width: 380px) {
    .guest-crm-content {
        padding-inline: 14px;
    }

    .guest-crm-title {
        font-size: 30px;
    }

    .guest-crm-form input {
        padding-inline: 14px 6px;
    }

    .guest-crm-form button {
        padding-inline: 15px;
        font-size: 12px;
    }

    .flip-container {
        width: min(100%, 285px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .guest-crm-hero .animate-slide-up,
    .guest-crm-hero .logo-track,
    .guest-crm-hero .flip-image {
        animation: none;
        transition: none;
    }
}

</style>

</section>






<!-- Tailwind CSS CDN -->


<section class="stats-solutions-section max-w-[1700px] mx-auto px-2 lg:px-[2%] py-20 overflow-hidden">
  
  <!-- Header -->
  <div class="stats-solutions-header flex justify-between gap-0 mb-16 reveal-up">
    
    <div>
  <h2 class=" max-w-[800px] font-semibold font-cormorant text-black leading-[1.1]
             text-[clamp(2.025rem,5vw,3.7rem)]">
    Accurate Email Data That Is  Trusted by Businesses 
</h2>
    </div>

    <div>
      <p class="max-w-[790px]  font-Regular font-manrope text-[24px] leading-[1.3] text-[#222] max-md:text-[18px]">
        MailsExtract transforms incoming emails into structured business data with enhanced accuracy. MailsExtract helps organizations automate data capture, eliminate manual entry, and allows seamless email workflow connections with multiple tools.  
      </p>
    </div>

  </div>

  <!-- Cards -->
  <div class="stats-solutions-grid grid grid-cols-[1.5fr_1fr_1fr_1fr] gap-5 max-xl:grid-cols-2 max-md:grid-cols-1">

    <!-- Card 1 -->
    <div
  class="stat-card
         bg-[url('https://www.mailsextract.com/wp-content/uploads/2026/07/Frame-184-1.png')]
         bg-cover
         bg-center
         bg-no-repeat
         text-white"
         
>
      
      <div class="stats-card-top flex">
        <div class="stats-card-icon "><img src="https://www.mailsextract.com/wp-content/uploads/2026/05/Group-80-5.png" alt=""></div>
         <h3 class="text-[72px] text-white leading-none font-semibold mt-2  ">
          <span class="counter font-[Poppins] " data-target="48">0</span>+
        </h3>
         <span class="text-[24px] leading-[1.0] font-medium mt-4">
          Email <br> Processes
        </span>
      </div>

      <div class="relative z-10">
       

       
      </div>

      <p class="text-[18px] leading-[1.6] max-w-[90%] relative z-10">
       Extract critical information for businesses from emails, invoices, orders, bookings, and customer communication.   
      </p>

    </div>

    <!-- Card 2 -->
    <div class="stat-card  bg-[url('https://www.mailsextract.com/wp-content/uploads/2026/07/Frame-187.png')]
         bg-cover
         bg-center
         bg-no-repeat
         text-white"
         
>
       <div class="stats-card-top flex">
      <div class="stats-card-icon"><img src="https://www.mailsextract.com/wp-content/uploads/2026/05/Group-77-4.png" alt=""></div>
       <h3 class="text-[72px] text-white leading-none font-semibold m-2">
          <span class="counter font-[Poppins]" data-target="99.8">0</span>%
        </h3>
</div>
      <div class="relative z-10">
       

        <span class="text-[22px] font-medium">
          Parsing Accuracy
        </span>
      </div>

      <p class="text-[18px] leading-[1.6] max-w-[90%] relative z-10">
        Extract precise email data with unmatched reliable automation.
      </p>

    </div>

    <!-- Card 3 -->
    <div class="stat-card  bg-[url('https://www.mailsextract.com/wp-content/uploads/2026/07/Frame-185.png')]
         bg-cover
         bg-center
         bg-no-repeat
         text-white"
         
>
       <div class="stats-card-top flex">
      <div class="stats-card-icon"><img src="https://www.mailsextract.com/wp-content/uploads/2026/05/Group-79-5.png" alt=""></div>
      <h3 class="text-[72px] text-white leading-none font-semibold m-2">
          <span class="counter font-[Poppins] " data-target="25">0</span>k+
        </h3>
</div>
      <div class="relative z-10">
        

        <span class="text-[22px] font-medium">
          Business Worldwide
        </span>
      </div>

      <p class="text-[18px] leading-[1.6] max-w-[90%] relative z-10">
        Trusted globally for seamless email extraction and integration.
      </p>

    </div>

    <!-- Card 4 -->
    <div class="stat-card  bg-[url('https://www.mailsextract.com/wp-content/uploads/2026/07/Frame-186.png')]
         bg-cover
         bg-center
         bg-no-repeat
         text-black"
         
>
       <div class="stats-card-top flex">
      <div class="stats-card-icon"><img src="https://www.mailsextract.com/wp-content/uploads/2026/05/Group-78-3.png" alt=""></div>
       <h3 class="text-[72px] leading-none font-semibold m-2">
          <span class="counter  font-[Poppins]" data-target="120">0</span>+
        </h3>
</div>
      <div class="relative z-10">
       

        <span class="text-[22px]   font-medium">
          App Integrations
        </span>
      </div>

      <p class="text-[18px] leading-[1.6] max-w-[90%] relative z-10">
        Connect effortlessly with CRM, marketing, and productivity platforms.
      </p>

    </div>

  </div>
</section>

<style>
  .stat-card {
    position: relative;
    padding: 28px;
    border-radius: 22px;
    overflow: hidden;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: 0.5s ease;
    animation: fadeUp 1s ease forwards;
    opacity: 0;
  }


  .stat-card {
  background-size: 100%;
  transition: background-size 0.5s ease, transform 0.5s ease;
}

.stat-card:hover {
  background-size: 110%;
  transform: translateY(-20px) scale(1.22);
}

  .stat-card:nth-child(1) {
    animation-delay: 0.1s;
  }

  .stat-card:nth-child(2) {
    animation-delay: 0.3s;
  }

  .stat-card:nth-child(3) {
    animation-delay: 0.5s;
  }

  .stat-card:nth-child(4) {
    animation-delay: 0.7s;
  }

  .stat-card:hover {
    transform: translateY(-10px) scale(1.02);
  }

  .stat-card::before {
    content: "";
    position: absolute;
    width: 220%;
    height: 220%;
    top: -60%;
    left: -60%;
    background: linear-gradient(
      120deg,
      transparent,
      rgba(255, 255, 255, 0.25),
      transparent
    );
    transform: rotate(25deg);
    animation: shine 6s linear infinite;
  }

  .icon {
    width: 42px;
    height: 42px;
    border-radius: 999px;
    background: #ff5a36;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    position: relative;
    z-index: 10;
  }

  @keyframes shine {
    0% {
      transform: translateX(-100%) rotate(25deg);
    }

    100% {
      transform: translateX(100%) rotate(25deg);
    }
  }

  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(40px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Responsive behavior for this statistics section only.
     The desktop layout and visual styling above remain unchanged. */
  @media (max-width: 1100px) {
    .stats-solutions-header {
      gap: 40px;
    }

    .stats-solutions-header > div:first-child,
    .stats-solutions-header > div:last-child {
      flex: 1 1 0;
      min-width: 0;
    }

    .stats-solutions-header br {
      display: none;
    }
  }

  @media (max-width: 900px) {
    .stats-solutions-section {
      padding-inline: 5%;
      padding-block: 64px;
    }

    .stats-solutions-header {
      flex-direction: column;
      gap: 22px;
      margin-bottom: 42px;
    }

    .stats-solutions-header > div:last-child p {
      max-width: 720px;
    }
  }

  @media (max-width: 767px) {
    .stats-solutions-section {
      padding: 48px 18px;
    }

    .stats-solutions-header {
      gap: 18px;
      margin-bottom: 30px;
    }

    .stats-solutions-header h2 {
      font-size: clamp(28px, 8vw, 36px);
      line-height: 1.15;
    }

    .stats-solutions-header p {
      font-size: 16px;
      line-height: 1.65;
    }

    .stats-solutions-grid {
      gap: 16px;
    }

    .stats-solutions-section .stat-card {
      min-height: 260px;
      padding: 22px;
      border-radius: 18px;
    }

    .stats-card-top {
      align-items: center;
      gap: 10px;
      min-width: 0;
    }

    .stats-card-icon {
      flex: 0 1 auto;
      min-width: 0;
    }

    .stats-card-icon img {
      display: block;
      width: auto;
      max-width: 100%;
      max-height: 90px;
      object-fit: contain;
    }

    .stats-card-top h3 {
      flex: 0 0 auto;
      margin-bottom: 0;
      font-size: clamp(48px, 15vw, 64px);
    }

    .stats-card-top > span,
    .stats-solutions-section .stat-card > div > span {
      font-size: 19px;
      line-height: 1.3;
    }

    .stats-solutions-section .stat-card > p {
      max-width: 100%;
      font-size: 16px;
      line-height: 1.55;
    }

    .stats-solutions-section .stat-card:hover {
      transform: none;
    }
  }

  @media (max-width: 380px) {
    .stats-solutions-section {
      padding-inline: 14px;
    }

    .stats-solutions-section .stat-card {
      min-height: 240px;
      padding: 19px;
    }

    .stats-card-icon img {
      max-height: 76px;
    }

    .stats-card-top h3 {
      font-size: 46px;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .stats-solutions-section .stat-card,
    .stats-solutions-section .stat-card::before {
      animation: none;
      opacity: 1;
    }
  }
</style>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const counters = document.querySelectorAll(".counter");

    const observer = new IntersectionObserver((entries) => {

        entries.forEach(entry => {

            if (entry.isIntersecting) {

                const counter = entry.target;
                const target = parseFloat(counter.dataset.target);

                let current = 0;
                const duration = 2000;
                const increment = target / (duration / 16);

                const updateCounter = () => {

                    current += increment;

                    if (current < target) {

                        if (target % 1 !== 0) {
                            counter.textContent = current.toFixed(1);
                        } else {
                            counter.textContent = Math.floor(current);
                        }

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



<section class="email-features-section relative overflow-hidden bg-[#ebfaff] py-16">

  <!-- BACKGROUND BLUR -->
  <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-200/30 blur-3xl rounded-full"></div>
  <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-cyan-200/30 blur-3xl rounded-full"></div>

  <div class="email-features-container relative z-10 max-w-7xl mx-auto px-6">

    <!-- HEADING -->
    <div class="email-features-heading text-center max-w-5xl mx-auto mb-24 reveal-up">

      <h2 class="text-4xl md:text-5xl lg:text-5xl font-bold leading-tight text-black font-cormorant">
      Everything You Need to Automate Email <br> Data Extraction 
      </h2>

      <p class="mt-6 text-gray-700 text-lg leading-relaxed font-manrope">
      Automatically extract, organize, and route valuable information from every email. MailsExtract gives businesses tools, from AI-powered parsing to real-time integration, to eliminate manual data entry and streamline workflows. 
      </p>

    </div>

    <!-- GRID -->
    <div class="email-features-grid grid md:grid-cols-2 gap-x-24 gap-y-28">

      <!-- CARD -->
      <div class="email-feature-item group relative reveal-up">

        <!-- GLOW -->
        <div class="absolute inset-0 bg-cyan-300/20 blur-3xl rounded-[40px] opacity-0 group-hover:opacity-100 transition duration-700"></div>

        <!-- ICON -->
        <div class="feature-icon">

          <img 
            src="https://www.mailsextract.com/wp-content/uploads/2026/05/Group-82-1.png"
            alt=""
          >

        </div>

        <!-- CARD BODY -->
        <div class="feature-card">

          <!-- LIGHT -->
         

          <!-- BORDER -->
          <div class="absolute top-0 left-0 w-full h-full rounded-[28px]
                      border border-white/40"></div>

          <!-- SHINE -->
          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-700">
            <div class="absolute -left-40 top-0 w-32 h-full
                        bg-white/40 blur-2xl rotate-12
                        group-hover:left-[120%]
                        transition-all duration-1000"></div>
          </div>

          <!-- CONTENT -->
          <div class="relative z-20">

            <h3 class="text-3xl font-bold text-black mb-5 leading-snug">
              Smart Field Recognition and AI Extraction 
            </h3>

            <p class="text-gray-700 leading-relaxed text-[17px]">
             Automatically identify and extract key business details from emails and attachments without any complex rules. It can extract information like customer details, order numbers, invoice data, booking information, tracking IDs, and payment references.  
            </p>

          </div>
        </div>
      </div>

      <!-- CARD -->
      <div class="email-feature-item group relative reveal-up">

        <div class="absolute inset-0 bg-cyan-300/20 blur-3xl rounded-[40px] opacity-0 group-hover:opacity-100 transition duration-700"></div>

        <!-- ICON -->
        <div class="feature-icon">

          <img 
            src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-316-1.png"
            alt=""
          >

        </div>

        <!-- CARD BODY -->
        <div class="feature-card">

       

         

          <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-700">
            <div class="absolute -left-40 top-0 w-32 h-full
                        bg-white/40 blur-2xl rotate-12
                        group-hover:left-[120%]
                        transition-all duration-1000"></div>
          </div>

          <div class="relative z-20">

            <h3 class="text-3xl font-bold text-black mb-5">
              Bulk and Real-Time Email Processing 
            </h3>

            <p class="text-gray-700 leading-relaxed text-[17px]">
              MailsExtract can process thousands of emails simultaneously, with enterprise-grade performance. It offers real-time email monitoring, with automated workflow execution and faster operational response times.  
            </p>

          </div>
        </div>
      </div>

      <!-- CARD -->
      <div class="email-feature-item group relative reveal-up">

        <div class="absolute inset-0 bg-cyan-300/20 blur-3xl rounded-[40px] opacity-0 group-hover:opacity-100 transition duration-700"></div>

        <!-- ICON -->
        <div class="feature-icon">

          <img 
            src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-316.png"
            alt=""
          >

        </div>

        <!-- CARD BODY -->
        <div class="feature-card">

         

          <div class="absolute top-0 left-0 w-full h-full rounded-[28px]
                      border border-white/40"></div>

          <div class="relative z-20">

            <h3 class="text-3xl font-bold text-black mb-5">
              Seamless Integration and Workflow Automation 
            </h3>

            <p class="text-gray-700 leading-relaxed text-[17px]">
             The platform can seamlessly connect extracted email data directly to the tools that businesses already use. The tool can be integrated with CRM platforms, Google Sheets, databases, ERP systems, and REST APIs, among others.  
            </p>

          </div>
        </div>
      </div>

      <!-- CARD -->
      <div class="email-feature-item group relative reveal-up">

        <div class="absolute inset-0 bg-cyan-300/20 blur-3xl rounded-[40px] opacity-0 group-hover:opacity-100 transition duration-700"></div>

        <!-- ICON -->
        <div class="feature-icon">

          <img 
            src="https://www.mailsextract.com/wp-content/uploads/2026/07/Group-317.png"
            alt=""
          >

        </div>

        <!-- CARD BODY -->
        <div class="feature-card ">

         

          <div class="absolute top-0 left-0 w-full h-full rounded-[28px]
                      border border-white/40"></div>

          <div class="relative z-20">

            <h3 class="text-3xl font-bold text-black mb-5">
              Data Mapping and Custom Parsing 
            </h3>

            <p class="text-gray-700 leading-relaxed text-[17px]">
              MailsExtract offers full control of how the email data is captured and processed. With the tool, the user can customize field mapping, data transformations, conditional logic, validation rules, and extract workflows. 
            </p>

          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<style>

/* CARD */

.feature-card{
    position: relative;
min-height: 315px;
    margin-left: 45px;

    z-index: 20;

    background:transparent;

    backdrop-filter: blur(20px);

    border: 2px solid rgba(255,255,255,0.7);

    border-radius: 28px;

    padding: 42px;

    overflow: hidden;

    box-shadow: 0 20px 50px rgba(104,156,187,0.25);

    transition: all 0.5s ease;
}

.group:hover .feature-card{
    transform: translateY(-14px);

    box-shadow: 0 30px 80px rgba(80,140,180,0.35);
}

/* ICON */

.feature-icon{
    position: absolute;

    top: -35px;
    left: -10px;

    width: 90px;
    height: 90px;

    z-index: 5; /* LESS THAN CARD */

    animation: floatIcon 4s ease-in-out infinite;

    transition: all 0.5s ease;
}

.feature-icon img{
    width: 100%;
    height: 100%;

    object-fit: contain;

    /* filter: drop-shadow(0 20px 35px rgba(255,77,35,0.25)); */
}

/* HOVER */

.group:hover .feature-icon{
    transform: translateY(-8px) rotate(4deg) scale(1.05);
}

/* FLOAT */

@keyframes floatIcon{

    0%{
        transform: translateY(0px);
    }

    50%{
        transform: translateY(-8px);
    }

    100%{
        transform: translateY(0px);
    }
}

/* Responsive behavior scoped to the email-features section.
   Existing desktop styles above remain unchanged. */
@media (max-width: 1023px) {
    .email-features-section {
        padding-block: 56px;
    }

    .email-features-heading {
        margin-bottom: 72px;
    }

    .email-features-heading h2 {
        font-size: 42px;
    }

    .email-features-grid {
        column-gap: 34px;
        row-gap: 76px;
    }

    .email-features-section .feature-card {
        margin-left: 30px;
        padding: 34px 30px;
    }

    .email-features-section .feature-card h3 {
        font-size: 26px;
    }

    .email-features-section .feature-icon {
        width: 80px;
        height: 80px;
    }
}

@media (max-width: 767px) {
    .email-features-section {
        padding-block: 50px;
    }

    .email-features-container {
        padding-inline: 18px;
    }

    .email-features-heading {
        margin-bottom: 52px;
    }

    .email-features-heading h2 {
        font-size: clamp(30px, 8.5vw, 38px);
        line-height: 1.15;
    }

    .email-features-heading h2 br {
        display: none;
    }

    .email-features-heading p {
        margin-top: 18px;
        font-size: 16px;
        line-height: 1.65;
    }

    .email-features-grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 54px;
    }

    .email-feature-item {
        padding-top: 34px;
    }

    .email-features-section .feature-icon {
        top: 0;
        left: 18px;
        width: 70px;
        height: 70px;
        z-index: 30;
    }

    .email-features-section .feature-card {
        min-height: 0;
        margin-left: 0;
        padding: 54px 22px 26px;
        border-radius: 22px;
    }

    .email-features-section .feature-card h3 {
        margin-bottom: 14px;
        font-size: clamp(22px, 6.5vw, 27px);
        line-height: 1.25;
    }

    .email-features-section .feature-card p {
        font-size: 16px;
        line-height: 1.65;
    }

    .email-features-section .group:hover .feature-card,
    .email-features-section .group:hover .feature-icon {
        transform: none;
    }
}

@media (max-width: 380px) {
    .email-features-container {
        padding-inline: 14px;
    }

    .email-features-heading h2 {
        font-size: 29px;
    }

    .email-features-section .feature-card {
        padding: 50px 18px 23px;
    }

    .email-features-section .feature-icon {
        left: 14px;
        width: 64px;
        height: 64px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .email-features-section .feature-icon {
        animation: none;
    }

    .email-features-section .feature-card,
    .email-features-section .feature-icon {
        transition: none;
    }
}

</style>


















<section class="workflow-section relative overflow-hidden bg-[#ffffff] py-[4%] px-[5%]">

  <div class="workflow-layout max-w-[1450px] mx-auto flex flex-wrap items-center justify-between gap-y-20">

    <!-- LEFT SIDE -->
    <div class="workflow-content relative w-[60%] max-xl:w-full pl-[4%] overflow-hidden">

      <!-- BOTTOM LINE -->
      <div class="absolute 
                  left-0 
                  bottom-0 
                  h-[3px] 
                  w-full 
                  z-50
                  bg-gradient-to-r 
                  from-[#ff5a2f] 
                  via-[#ff7b5c] 
                  to-transparent">
      </div>

      <!-- LEFT LINE -->
      <div class="absolute 
                  left-0 
                  top-0 
                  w-[3px] 
                  h-full 
                  z-50
                  bg-gradient-to-b 
                  from-[#ffb29f]/40 
                  via-[#ff5a2f] 
                  to-[#ff5a2f]">
      </div>

      <!-- HEADING -->
      <h2 class="workflow-title text-[4vw]
                 leading-[0.99]
                 tracking-[-0.2vw]
                 font-semibold
                 text-black
                 max-xl:text-[44px]
                 max-md:text-[37px] reveal-up ">

        Extracting Emails 
        <br>

        <span class="text-[#ff5a2f]">
         Within Minutes 
        </span>

      </h2>

      <!-- DESC -->
      <p class="workflow-description mt-[4%]
                text-[1.3vw]
                leading-[1.1]
                text-[#1d1d1d]
                max-w-[90%]
                max-xl:text-[16px]
                max-md:text-[16px] reveal-up ">

      MailsExtract turns incoming emails into structured business data in just four simple steps. I do not require coding, a complex setup, or cause any disruption to your existing workflows.  

      </p>

      <!-- LINE -->
      <div class="workflow-divider w-[92%]
                  h-[1px]
                  bg-[#d8d8d8]
                  my-[3%]">
      </div>

      <!-- STEPS WRAPPER -->
      <div class="left-steps-wrapper reveal-up">

        <!-- STEP 1 -->
        <div class="step-card active-step flex items-start gap-[1.4vw]">

          <div class="min-w-[58px]
                      h-[58px]
                      rounded-full
                      bg-[#10b7ff]
                      text-white
                      font-bold
                      text-[18px]
                      flex items-center justify-center
                     ">

            01

          </div>

          <div>

            <h3 class="text-[2vw]
                       leading-none
                       font-semibold
                       text-black
                       mb-[3%]
                       max-xl:text-[34px]
                       max-md:text-[28px]">

              Create Your Account

            </h3>

            <p class="text-[1.15vw]
                      leading-[1.8]
                      text-[#444]
                      max-w-[90%]
                      max-xl:text-[19px]
                      max-md:text-[16px]">

              Sign up in seconds and get instant access to your
              MailSExtract dashboard. No credit card required.

            </p>

          </div>

        </div>

        <!-- STEP 2 -->
        <div class="step-card next-step flex items-start gap-[1.4vw]  ">

          <div class="min-w-[58px]
                      h-[58px]
                      rounded-full
                      bg-[#8edfff]
                      text-white
                      font-bold
                      text-[18px]
                      flex items-center justify-center">

            02

          </div>

          <div>

            <h3 class="text-[2vw]
                       leading-none
                       font-semibold
                       text-black
                       mb-[3%]
                       max-xl:text-[34px]
                       max-md:text-[28px]">

              Forward Your Emails

            </h3>

            <p class="text-[1.15vw]
                      text-[#444]
                      max-w-[90%]
                      max-xl:text-[19px]
                      max-md:text-[16px]">

              Send emails to your unique MailSExtract address
              or enable auto forwarding.

            </p>

          </div>

        </div>

        <!-- STEP 3 -->
        <div class="step-card third-step flex items-start gap-[1.4vw]">

          <div class="min-w-[58px]
                      h-[58px]
                      rounded-full
                      bg-[#ffb08c]
                      text-white
                      font-bold
                      text-[18px]
                      flex items-center justify-center">

            03

          </div>

          <div>

            <h3 class="text-[2vw]
                       leading-none
                       font-semibold
                       text-black
                       mb-[3%]
                       max-xl:text-[34px]
                       max-md:text-[28px]">

              AI Extracts Data

            </h3>

            <p class="text-[1.15vw]
                      text-[#444]
                      max-w-[90%]
                      max-xl:text-[19px]
                      max-md:text-[16px]">

              AI instantly extracts and structures your
              incoming email data automatically.

            </p>

          </div>

        </div>

        <!-- STEP 4 -->
        <div class="step-card flex items-start gap-[1.4vw]">

          <div class="min-w-[58px]
                      h-[58px]
                      rounded-full
                      bg-[#ffd37c]
                      text-white
                      font-bold
                      text-[18px]
                      flex items-center justify-center">

            04

          </div>

          <div>

            <h3 class="text-[2vw]
                       leading-none
                       font-semibold
                       text-black
                       mb-[3%]
                       max-xl:text-[34px]
                       max-md:text-[28px]">

              Export Anywhere

            </h3>

            <p class="text-[1.15vw]
                      text-[#444]
                      max-w-[90%]
                      max-xl:text-[19px]
                      max-md:text-[16px]">

              Push clean structured data directly into your CRM
              and workflow tools.

            </p>

          </div>

        </div>

      </div>

      <!-- LEFT BOTTOM CIRCLE -->
      <div class="absolute bottom-[-120px] left-[-100px]  ">

       <div class="mini-orbit-wrapper">

  <!-- BACKGROUND -->
  <div class="absolute inset-0 rounded-full bg-[#ebfaff]"></div>

  <!-- ROTATING LAYER -->
  <div class="mini-orbit-rotate">

    <!-- DASH BORDER -->
    <div class="absolute inset-0 rounded-full border-2 border-dashed border-black"></div>

    <!-- CIRCLE 1 -->
    <div class="mini-orbit-circle mini-circle1"></div>

    <!-- CIRCLE 2 -->
    <div class="mini-orbit-circle mini-circle2"></div>

    <!-- CIRCLE 3 -->
    <div class="mini-orbit-circle mini-circle3"></div>

    <!-- CIRCLE 4 -->
    <div class="mini-orbit-circle mini-circle4"></div>

  </div>

</div>

      </div>

    </div>



    <style>
      /* ========================================= */
/* MINI ORBIT WRAPPER */
/* ========================================= */

.mini-orbit-wrapper{

    position: relative;

    width: 300px;
    height: 300px;

}

/* ========================================= */
/* ROTATION */
/* ========================================= */

.mini-orbit-rotate{

    position: absolute;

    inset: 0;

    animation:
    miniOrbitRotate 14s linear infinite;

}

/* ========================================= */
/* ANTICLOCKWISE ROTATION */
/* ========================================= */

@keyframes miniOrbitRotate{

    0%{
        transform: rotate(0deg);
    }

    100%{
        transform: rotate(-360deg);
    }

}

/* ========================================= */
/* SMALL CIRCLES */
/* ========================================= */

.mini-orbit-circle{

    position: absolute;

    width: 38px;
    height: 38px;

    border-radius: 999px;

    background: #ff5a2f;

    box-shadow:
    0 15px 40px rgba(255,90,47,0.35);

}

/* ========================================= */
/* PERFECT EQUAL SPACING */
/* ========================================= */

/* TOP */

.mini-circle1{

    top: -19px;
    left: 50%;

    transform: translateX(-50%);

}

/* RIGHT */

.mini-circle2{

    right: -19px;
    top: 50%;

    transform: translateY(-50%);

}

/* BOTTOM */

.mini-circle3{

    bottom: -19px;
    left: 50%;

    transform: translateX(-50%);

}

/* LEFT */

.mini-circle4{

    left: -19px;
    top: 50%;

    transform: translateY(-50%);

}
    </style>

    <!-- RIGHT SIDE -->
    <div class="workflow-visual relative w-[40%] max-xl:w-full flex justify-end reveal-up">

      <div class="orbit-wrapper relative w-[42vw] h-[42vw]
                  max-w-[530px]
                  max-h-[530px]
                  min-w-[200px]
                  min-h-[200px]
                  rounded-full
                  bg-[#ebfaff]">

        <!-- ROTATING -->
        <div class="orbit-rotate absolute inset-0">

          <!-- DASH -->
          <svg class="absolute inset-0 w-full h-full">
  <circle
    cx="50%"
    cy="50%"
    r="49.8%"
    fill="none"
    stroke="black"
    stroke-width="3"
    stroke-dasharray="14 14"
  />
</svg>

          <!-- 01 -->
          <div class="orbit-number orbit1 active-orbit">
            01
          </div>

          <!-- 02 -->
          <div class="orbit-number orbit2">
            02
          </div>

          <!-- 03 -->
          <div class="orbit-number orbit3">
            03
          </div>

          <!-- 04 -->
          <div class="orbit-number orbit4">
            04
          </div>

          <!-- ARROWS -->
          <div class="arrow arrow1"></div>
          <div class="arrow arrow2"></div>
          <div class="arrow arrow3"></div>
          <div class="arrow arrow4"></div>

        </div>

        <!-- CENTER IMAGE -->
       <!-- CENTER IMAGE -->
<div class="absolute inset-0 flex items-center justify-center z-30">

  <div class="center-image-wrapper bg-[#ffff] rounded-full">

    <img 
      src="https://www.mailsextract.com/wp-content/uploads/2026/06/New-Project-1.jpg"
      class="center-image active-center-image p-2"
    >

    <img 
      src="https://www.mailsextract.com/wp-content/uploads/2026/05/Ellipse-440.png"
      class="center-image p-2"
    >

    <img 
      src="https://www.mailsextract.com/wp-content/uploads/2026/05/Adobe-Express-file-1.png"
      class="center-image p-2"
    >

    <img 
      src="https://www.mailsextract.com/wp-content/uploads/2026/05/Ellipse-440.png"
      class="center-image p-2"
    >

  </div>

</div>

      </div>

    </div>

  </div>

</section>

<style>
/* ========================================= */
/* LEFT STACK */
/* ========================================= */



/* ========================================= */
/* CENTER IMAGE ANIMATION */
/* ========================================= */

.center-image-wrapper{

    position: relative;

    width: 42%;
    height: 42%;

}

/* IMAGE */

.center-image{

    position: absolute;

    inset: 0;

    width: 100%;
    height: 100%;

    object-fit: cover;

    border-radius: 999px;

    opacity: 1;
    visibility: hidden;
    transform: scale(.04);

    transition:
    transform 1.2s cubic-bezier(.22,1,.36,1),
    visibility 0s linear 1.2s;

}

/* ACTIVE IMAGE */

.active-center-image{
    visibility: visible;
    transform: scale(1);
    transition-delay: 0s;

}

/* EXIT ANIMATION */

.hide-center-image{
    opacity: 1;
    visibility: visible;
    transform: scale(.04);
    transition:
    transform 1.2s cubic-bezier(.22,1,.36,1),
    visibility 0s linear 1.2s;
}







.left-steps-wrapper{
    position: relative;
    min-height: 400px;
    width: 100%;
    overflow: hidden;
}

/* ========================================= */
/* STEP CARD */
/* ========================================= */

.step-card{
    position: absolute;
    left: 0;
    width: 92%;
    transition:
    opacity 1.8s cubic-bezier(.22,1,.36,1),
    transform 1.8s cubic-bezier(.22,1,.36,1),
    filter 1.8s cubic-bezier(.22,1,.36,1);
    opacity: 0;
    filter: blur(8px);
    transform:
    translateY(180px)
    scale(.92);
    will-change: transform, opacity, filter;
}

/* ========================================= */
/* ACTIVE */
/* ========================================= */

.active-step{
    opacity: 1 !important;
    filter: blur(0px);
    transform:
    translateY(0px)
    scale(1);
    z-index: 30;
}

/* ========================================= */
/* NEXT */
/* ========================================= */

.next-step{
    opacity: .48 !important;
    filter: blur(0px);
    transform:
    translateY(140px)
    scale(.96);
    margin-left:160px;
    margin-top: 90px;
    z-index: 20;
}

/* ========================================= */
/* THIRD */
/* ========================================= */

/* .third-step{

    opacity: .12 !important;
margin-left:200px;
    filter: blur(7px);

    transform:
    translateY(240px)
    scale(.88);

    z-index: 10;

} */

/* ========================================= */
/* ORBIT ROTATION */
/* ========================================= */


.orbit-rotate{
    transform-origin: center center;
    transition:
    transform 2.2s cubic-bezier(.77,0,.18,1);
    will-change: transform;
}



@keyframes orbitRotate{
    0%{
        transform: rotate(0deg);
    }
    100%{
        transform: rotate(360deg);
    }
}

/* ========================================= */
/* ORBIT NUMBER */
/* ========================================= */

/* ========================================= */
/* KEEP NUMBER STRAIGHT */
/* ========================================= */

/* ========================================= */
/* ACTIVE ORBIT */
/* ========================================= */
.active-orbit{
    background: #10b7ff;
    transform: scale(1.12);
    box-shadow:
    0 20px 60px rgba(16,183,255,.45);
}

/* ========================================= */
/* ORBIT POSITION */
/* ========================================= */
.orbit-number{
    position: absolute;
    width: 95px;
    height: 95px;
    border-radius: 999px;
    background: #ff5a2f;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: 700;
    box-shadow: 0 10px 10px rgba(255,90,47,0.35);
    z-index: 20;
    transition:
    transform 2.2s cubic-bezier(.77,0,.18,1),
    background 1.6s ease,
    opacity 1.6s ease,
    box-shadow 1.6s ease;
    will-change: transform;
}

/* PERFECT CENTER ALIGNMENT */

.orbit1{
    left: 0;
    top: 50%;
    margin-left: -47.5px;
    margin-top: -47.5px;
}

.orbit2{
    left: 50%;
    bottom: 0;
    margin-left: -47.5px;
    margin-bottom: -47.5px;
}

.orbit3{
    right: 0;
    top: 50%;
    margin-right: -47.5px;
    margin-top: -47.5px;
}

.orbit4{
    left: 50%;
    top: 0;
    margin-left: -47.5px;
    margin-top: -47.5px;
}

/* ========================================= */
/* ARROWS */
/* ========================================= */

.arrow{
    position: absolute;
    width: 0;
    height: 0;
    border-top: 12px solid transparent;
    border-bottom: 12px solid transparent;
    border-left: 18px solid black;

}

.arrow1{
    top: 8.4%;
    left: 18%;
    transform: rotate(-45deg);
}

.arrow2{
    top: 8.4%;
    right: 18%;
    transform: rotate(45deg);
}

.arrow3{
    bottom: 8.4%;
    right: 18%;
    transform: rotate(135deg);
}

.arrow4{
    bottom: 8.4%;
    left: 18%;
    transform: rotate(-135deg);
}

/* ========================================= */
/* RESPONSIVE */
/* ========================================= */

@media(max-width:1024px){

    .orbit-number{
        width: 75px;
        height: 75px;
        font-size: 24px;
    }

    .orbit1 {
        margin-left: -37.5px;
        margin-top: -37.5px;
    }

    .orbit2 {
        margin-left: -37.5px;
        margin-bottom: -37.5px;
    }

    .orbit3 {
        margin-right: -37.5px;
        margin-top: -37.5px;
    }

    .orbit4 {
        margin-left: -37.5px;
        margin-top: -37.5px;
    }
}

@media(max-width:768px){

    .orbit-number{
        width: 65px;
        height: 65px;
        font-size: 20px;
    }

    .orbit1 {
        margin-left: -32.5px;
        margin-top: -32.5px;
    }

    .orbit2 {
        margin-left: -32.5px;
        margin-bottom: -32.5px;
    }

    .orbit3 {
        margin-right: -32.5px;
        margin-top: -32.5px;
    }

    .orbit4 {
        margin-left: -32.5px;
        margin-top: -32.5px;
    }

    .arrow{
        display: none;
    }

}

/* Responsive behavior scoped to this workflow section.
   All desktop declarations above remain unchanged. */
@media (max-width: 1279px) {
    .workflow-section {
        padding: 64px 5%;
    }

    .workflow-layout {
        flex-direction: column;
        align-items: stretch;
        gap: 54px;
    }

    .workflow-content {
        width: 100%;
        padding-left: 36px;
    }

    .workflow-title {
        max-width: 850px;
    }

    .workflow-description {
        max-width: 850px;
        line-height: 1.55;
    }

    .workflow-visual {
        width: 100%;
        justify-content: center;
        padding: 40px 0;
    }

    .orbit-wrapper {
        width: min(58vw, 500px);
        height: min(58vw, 500px);
    }
}

@media (max-width: 767px) {
    .workflow-section {
        padding: 48px 18px;
    }

    .workflow-layout {
        gap: 28px;
    }

    .workflow-content {
        padding: 4px 0 0 18px;
    }

    .workflow-title {
        font-size: clamp(31px, 9vw, 40px);
        line-height: 1.08;
        letter-spacing: -.8px;
    }

    .workflow-description {
        max-width: 100%;
        margin-top: 22px;
        padding-right: 10px;
        font-size: 16px;
        line-height: 1.6;
    }

    .workflow-divider {
        width: calc(100% - 10px);
        margin-block: 24px;
    }

    .left-steps-wrapper {
        min-height: 330px;
    }

    .workflow-section .step-card {
        width: calc(100% - 8px);
        gap: 14px;
    }

    .workflow-section .step-card > div:first-child {
        min-width: 48px;
        width: 48px;
        height: 48px;
        font-size: 16px;
    }

    .workflow-section .step-card > div:last-child {
        min-width: 0;
    }

    .workflow-section .step-card h3 {
        margin-bottom: 10px;
        font-size: clamp(22px, 6.4vw, 27px);
        line-height: 1.15;
    }

    .workflow-section .step-card p {
        max-width: 100%;
        font-size: 15px;
        line-height: 1.55;
    }

    .workflow-section .next-step {
        margin-top: 66px;
        margin-left: 24px;
        transform: translateY(112px) scale(.95);
    }

    .mini-orbit-wrapper {
        width: 180px;
        height: 180px;
    }

    .mini-orbit-circle {
        width: 26px;
        height: 26px;
    }

    .mini-circle1 { top: -13px; }
    .mini-circle2 { right: -13px; }
    .mini-circle3 { bottom: -13px; }
    .mini-circle4 { left: -13px; }

    .workflow-visual {
        padding: 34px 36px;
    }

    .orbit-wrapper {
        width: min(72vw, 340px);
        height: min(72vw, 340px);
        min-width: 0;
        min-height: 0;
    }

    .orbit-number {
        width: 56px;
        height: 56px;
        font-size: 18px;
    }

    .orbit1 {
        margin-left: -28px;
        margin-top: -28px;
    }

    .orbit2 {
        margin-left: -28px;
        margin-bottom: -28px;
    }

    .orbit3 {
        margin-right: -28px;
        margin-top: -28px;
    }

    .orbit4 {
        margin-left: -28px;
        margin-top: -28px;
    }
}

@media (max-width: 380px) {
    .workflow-section {
        padding-inline: 14px;
    }

    .workflow-content {
        padding-left: 14px;
    }

    .workflow-title {
        font-size: 29px;
    }

    .workflow-section .next-step {
        margin-left: 8px;
    }

    .workflow-visual {
        padding-inline: 31px;
    }

    .orbit-wrapper {
        width: min(70vw, 280px);
        height: min(70vw, 280px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .workflow-section .mini-orbit-rotate {
        animation: none;
    }

    .workflow-section .step-card,
    .workflow-section .orbit-rotate,
    .workflow-section .orbit-number,
    .workflow-section .center-image {
        transition-duration: .01ms;
    }
}

</style>

<script>

const steps = document.querySelectorAll('.step-card');
const orbitNumbers = document.querySelectorAll('.orbit-number');
const orbitRotate = document.querySelector('.orbit-rotate');

const centerImages =
document.querySelectorAll('.center-image');

/* ========================================= */
/* IMAGE LIST */
/* ========================================= */

const imageSources = [

"https://www.mailsextract.com/wp-content/uploads/2026/06/New-Project-1.jpg",

"https://www.mailsextract.com/wp-content/uploads/2026/06/ChatGPT-Image-Jun-18-2026-01_17_29-PM-1.jpg",

"https://www.mailsextract.com/wp-content/uploads/2026/06/New-Project.jpg",

"https://www.mailsextract.com/wp-content/uploads/2026/06/ChatGPT-Image-Jun-18-2026-01_20_38-PM-1.jpg"

];

/* ========================================= */
/* VARIABLES */
/* ========================================= */

let activeIndex = 0;
let rotation = 0;

/* ========================================= */
/* UPDATE STEPS */
/* ========================================= */

function updateSteps(){

    /* REMOVE */

    steps.forEach((step)=>{

        step.classList.remove(
            'active-step',
            'next-step',
            'third-step'
        );

    });

    orbitNumbers.forEach((orbit)=>{

        orbit.classList.remove('active-orbit');

    });

    centerImages.forEach((image)=>{

        image.classList.remove(
            'active-center-image',
            'hide-center-image'
        );

    });

    /* ACTIVE STEP */

    steps[activeIndex]
    .classList.add('active-step');

    /* ACTIVE ORBIT */

    orbitNumbers[activeIndex]
    .classList.add('active-orbit');

    /* NEXT STEP */

    steps[(activeIndex + 1) % steps.length]
    .classList.add('next-step');

    /* THIRD STEP */

    steps[(activeIndex + 2) % steps.length]
    .classList.add('third-step');

    /* IMAGE */

    centerImages[activeIndex].src =
    imageSources[activeIndex];

    centerImages[activeIndex]
    .classList.add('active-center-image');

}

/* ========================================= */
/* ROTATE STEP */
/* ========================================= */

function rotateStep(){

    /* SHOW CURRENT */

    updateSteps();

    /* STAY PERFECT 3 SEC */

    setTimeout(()=>{

        /* HIDE IMAGE */

        centerImages[activeIndex]
        .classList.remove('active-center-image');

        centerImages[activeIndex]
        .classList.add('hide-center-image');

        /* ROTATE */

        rotation += 90;

        orbitRotate.style.transform =
        `rotate(${rotation}deg)`;

        /* KEEP NUMBER STRAIGHT */

        orbitNumbers.forEach((orbit)=>{

            orbit.style.transform =
            `rotate(${-rotation}deg)`;

        });

        /* NEXT */

        activeIndex++;

        if(activeIndex >= steps.length){

            activeIndex = 0;

        }

    },4000);

}

/* ========================================= */
/* INITIAL */
/* ========================================= */

updateSteps();

rotateStep();

/* ========================================= */
/* LOOP */
/* ========================================= */

setInterval(()=>{

    rotateStep();

},5200);

</script>













<div class="pricing-section bg-[#EBFAFF] min-h-screen flex items-center justify-center px-6 py-20">

  <section class="pricing-inner max-w-6xl w-full mx-auto">

    <!-- Heading -->
    <div class="pricing-heading text-center mb-16 reveal-up">
      <h2 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
       Powerful Automation with
        <br />
        Transparent Pricing
      </h2>

      <p class="text-gray-600 mt-5 max-w-3xl mx-auto text-sm md:text-base leading-relaxed">
        From processing a few hundred emails to managing enterprise-scale workflows, MailsExtract
        offers flexible plans that are designed to grow with your business.
      </p>
    </div>

    <!-- Pricing Cards -->
    <div class="pricing-grid grid grid-cols-1 md:grid-cols-3 gap-8 items-end reveal-up">

      <!-- Card 1 -->
      <div class="pricing-card pricing-card-side bg-white rounded-[28px] shadow-lg p-8 border border-gray-100">

        <h3 class="font-semibold text-gray-900 text-lg mb-5">
          Agency
        </h3>

        <div class="border-t border-gray-200 pt-5">

          <div class="flex items-start">
            <span class="text-5xl font-bold text-black">$35.99</span>
          </div>

          <p class="text-gray-500 text-sm mt-2">
            user per month
          </p>

          <button class="  group relative overflow-hidden w-full mt-5 bg-black text-white py-3 rounded-lg font-medium transition duration-300 hover:opacity-90">

           <span
  class="absolute inset-0 bg-[#fd4821] text-[#ffff]
  scale-x-0 origin-left
  transition-transform duration-500
  group-hover:scale-x-100"
></span>
            <span class="relative z-10">
            Get Started
            </span>
          </button>


         

          <div class="mt-8">

            <p class="font-medium text-gray-900 mb-5">
              Features included
            </p>

            <ul class="space-y-4 text-gray-700 text-sm">

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                10GB Storage
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Unlimited Domain
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                1 Databases
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                3 Email Inboxes
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Basic AI Parsing
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Email Support
              </li>

            </ul>

          </div>

        </div>

      </div>

      <!-- Middle Featured Card -->
      <div class="pricing-card pricing-card-featured relative bg-white rounded-[28px] shadow-2xl px-8 py-14 border-2 border-[#FD4821] scale-105 z-10">

        <!-- Badge -->
        <div class="pricing-popular-badge absolute -top-5 left-1/2 -translate-x-1/2 bg-[#FD4821] text-white px-8 py-2 rounded-full text-sm font-semibold shadow-lg">
          Most Popular
        </div>

        <h3 class="font-semibold text-gray-900 text-lg mb-5">
          Agency
        </h3>

        <div class="border-t border-gray-200 pt-5">

          <div class="flex items-start">
            <span class="text-5xl font-bold text-black">$65.99</span>
          </div>

          <p class="text-gray-500 text-sm mt-2">
            user per month
          </p>

          <button class="group relative overflow-hidden w-full mt-5 bg-[#FD4821] text-white py-3 rounded-lg font-medium transition duration-300 hover:bg-orange-600">
             <span
  class="absolute inset-0 bg-[#000000] text-[#ffff]
  scale-x-0 origin-left
  transition-transform duration-500
  group-hover:scale-x-100"
></span>
            <span class="relative z-10">
            Get Started
            </span>
          </button>

          <div class="mt-8">

            <p class="font-medium text-gray-900 mb-5">
              Features included
            </p>

            <ul class="space-y-4 text-gray-700 text-sm">

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                100GB Storage
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Unlimited Domain
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                3 Databases
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                10 Email Inboxes
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Advanced AI Parsing
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Priority Support
              </li>

            </ul>

          </div>

        </div>

      </div>

      <!-- Card 3 -->
      <div class="pricing-card pricing-card-side bg-white rounded-[28px] shadow-lg p-8 border border-gray-100">

        <h3 class="font-semibold text-gray-900 text-lg mb-5">
          Agency
        </h3>

        <div class="border-t border-gray-200 pt-5">

          <div class="flex items-start">
            <span class="text-5xl font-bold text-black">$95.99</span>
          </div>

          <p class="text-gray-500 text-sm mt-2">
            user per month
          </p>

          <button class=" group relative overflow-hidden w-full mt-5 bg-black text-white py-3 rounded-lg font-medium transition duration-300 hover:opacity-90">
             <span
  class="absolute inset-0 bg-[#FD4821] text-[#ffff]
  scale-x-0 origin-left
  transition-transform duration-500
  group-hover:scale-x-100"
></span>
          <span class="relative z-10">
            Get Started
            </span>
          </button>

          <div class="mt-8">

            <p class="font-medium text-gray-900 mb-5">
              Features included
            </p>

            <ul class="space-y-4 text-gray-700 text-sm">

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Unlimited Storage
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Unlimited Domain
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Unlimited Databases
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Unlimited Inboxes
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Custom AI Models
              </li>

              <li class="flex items-center gap-3">
                <span class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold">
                  ✓
                </span>
                Dedicated Manager
              </li>

            </ul>

          </div>

        </div>

      </div>

    </div>

  </section>

 </div>


<style>
/* Reference-matched pricing layout. Desktop type sizes remain controlled
   exclusively by the existing Tailwind classes in the markup. */
.pricing-section {
  min-height: 0;
  padding: 60px 44px 54px;
  background: #ebfaff;
}

.pricing-inner {
  max-width: 1100px;
}

.pricing-heading {
  margin-bottom: 70px;
  animation: pricingHeadingIn .75s cubic-bezier(.22,1,.36,1) both;
}

.pricing-heading p {
  max-width: 700px;
  color: #171717;
}

.pricing-grid {
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 40px;
}

.pricing-card {
  position: relative;
  overflow: hidden;
  opacity: 0;
  color: #050505;
  transition:
    transform .45s cubic-bezier(.22,1,.36,1),
    box-shadow .45s ease;
  animation: pricingCardIn .8s cubic-bezier(.22,1,.36,1) forwards;
}

.pricing-card:nth-child(1) { animation-delay: .12s; }
.pricing-card:nth-child(2) { animation-delay: .25s; }
.pricing-card:nth-child(3) { animation-delay: .38s; }

.pricing-card-side {
  min-height: 400px;
  padding: 30px 40px 34px;
  border: 0;
  border-radius: 22px;
  box-shadow: 0 10px 18px rgba(69,174,210,.15);
}

.pricing-card-featured {
  min-height: 445px;
  padding: 48px 38px 34px;
  border: 2px solid #ff4829;
  border-radius: 22px;
  box-shadow: 0 12px 24px rgba(69,174,210,.18);
  transform: none;
}

.pricing-popular-badge {
  top: -2px;
  right: -2px;
  left: -2px;
  width: auto;
  height: 32px;
  padding: 0;
  border-radius: 20px 20px 0 0;
  box-shadow: none;
  transform: none;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 400;
  animation: pricingBadgeGlow 2.8s ease-in-out infinite;
}

.pricing-card > div.border-t {
  padding-top: 10px;
  border-color: #d0d0d0;
}

.pricing-card h3 {
  margin-bottom: 12px;
}

.pricing-card p.text-gray-500 {
  margin-top: 4px;
}

.pricing-card button {
  height: 34px;
  margin-top: 14px;
  padding-block: 0;
  border-radius: 6px;
  box-shadow: 0 5px 12px rgba(0,0,0,.08);
  transition: transform .3s ease, box-shadow .3s ease;
}

.pricing-card div.mt-8 {
  margin-top: 14px;
}

.pricing-card div.mt-8 > p {
  margin-bottom: 10px;
}

.pricing-card ul {
  display: grid;
  gap: 8px;
}

.pricing-card ul > li + li {
  margin-top: 0 !important;
}

.pricing-card ul > li > span {
  width: 15px;
  height: 15px;
  flex: 0 0 15px;
}

.pricing-card button::after {
  content: "";
  position: absolute;
  top: -40%;
  bottom: -40%;
  left: -28%;
  width: 20%;
  z-index: 2;
  pointer-events: none;
  background: rgba(255,255,255,.42);
  filter: blur(5px);
  transform: translateX(-220%) skewX(-18deg);
  transition: transform .7s cubic-bezier(.22,1,.36,1);
}

.pricing-card button:hover::after {
  transform: translateX(750%) skewX(-18deg);
}

.pricing-card button:hover {
  transform: translateY(-2px);
  box-shadow: 0 9px 18px rgba(0,0,0,.15);
}

.pricing-card ul li {
  transition: transform .25s ease, color .25s ease;
}

.pricing-card ul li:hover {
  transform: translateX(4px);
  color: #050505;
}

.pricing-card ul li > span {
  animation: pricingCheckIn .45s cubic-bezier(.2,1.5,.4,1) both;
}

.pricing-card ul li:nth-child(2) > span { animation-delay: .06s; }
.pricing-card ul li:nth-child(3) > span { animation-delay: .12s; }
.pricing-card ul li:nth-child(4) > span { animation-delay: .18s; }
.pricing-card ul li:nth-child(5) > span { animation-delay: .24s; }
.pricing-card ul li:nth-child(6) > span { animation-delay: .30s; }

.pricing-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 36px rgba(69,174,210,.24);
}

.pricing-card-featured:hover {
  transform: translateY(-10px);
}

@keyframes pricingHeadingIn {
  from { opacity: 0; transform: translateY(24px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes pricingCardIn {
  from { opacity: 0; translate: 0 42px; scale: .97; }
  to { opacity: 1; translate: 0 0; scale: 1; }
}

@keyframes pricingBadgeGlow {
  0%, 100% { box-shadow: 0 0 0 rgba(253,72,33,0); }
  50% { box-shadow: 0 7px 20px rgba(253,72,33,.25); }
}

@keyframes pricingCheckIn {
  from { opacity: 0; transform: scale(.25) rotate(-35deg); }
  to { opacity: 1; transform: scale(1) rotate(0); }
}

@media (max-width: 900px) {
  .pricing-section {
    padding: 58px 24px;
  }

  .pricing-inner {
    max-width: 620px;
  }

  .pricing-heading {
    margin-bottom: 48px;
  }

  .pricing-grid {
    grid-template-columns: minmax(0, 1fr);
    gap: 28px;
    max-width: 410px;
    margin-inline: auto;
  }

  .pricing-card-side,
  .pricing-card-featured {
    min-height: 0;
  }
}

@media (max-width: 600px) {
  .pricing-section {
    padding: 48px 16px;
  }

  .pricing-heading h2 {
    font-size: clamp(30px, 9vw, 38px);
  }

  .pricing-heading p {
    font-size: 15px;
    line-height: 1.6;
  }

  .pricing-card-side,
  .pricing-card-featured {
    padding-inline: 26px;
  }

  .pricing-card:hover,
  .pricing-card-featured:hover {
    transform: none;
  }
}

@media (prefers-reduced-motion: reduce) {
  .pricing-heading,
  .pricing-card,
  .pricing-popular-badge,
  .pricing-card ul li > span {
    opacity: 1;
    animation: none;
  }

  .pricing-card,
  .pricing-card button,
  .pricing-card ul li {
    transition: none;
  }
}
</style>









<section class="bg-[#ffff] py-14">

    <div class="max-w-[1400px] mx-auto px-6 reveal-up">

        <!-- Top Heading -->
        <div class="flex items-center justify-between mb-12">

            <h2 class="text-4xl md:text-5xl font-bold text-black">
                See All Blogs
            </h2>

            <a href="<?php echo site_url('/blog'); ?>"
               class="bg-black text-white px-8 py-4 rounded-full text-sm font-medium hover:bg-gray-900 transition duration-300">
                View All Articles
            </a>

        </div>

        <?php
        $latest_posts = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post_status'    => 'publish'
        ));
        ?>

        <!-- Blog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">

            <?php if ($latest_posts->have_posts()) : ?>
                <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>

                    <article class="group">

                        <!-- Image -->
                        <a href="<?php the_permalink(); ?>" class="block overflow-hidden rounded-[28px]">

                            <?php if (has_post_thumbnail()) : ?>

                                <img src="<?php the_post_thumbnail_url('large'); ?>"
                                     alt="<?php the_title(); ?>"
                                     class="w-full h-[230px] object-cover transition duration-500 group-hover:scale-105">

                            <?php else : ?>

                                <div class="w-full h-[230px] bg-gray-300 flex items-center justify-center text-gray-600 text-lg">
                                    No Image
                                </div>

                            <?php endif; ?>

                        </a>

                        <!-- Content -->
                        <div class="pt-6">

                            <!-- Title -->
                            <h3 class="text-[30px] leading-[1.15] font-semibold text-black">

                                <a href="<?php the_permalink(); ?>"
                                   class="hover:text-gray-700 transition duration-300">

                                    <?php echo wp_trim_words(get_the_title(), 10, '...'); ?>

                                </a>

                            </h3>

                            <!-- Excerpt -->
                            <p class="text-gray-600 text-[15px] leading-7 mt-4">

                                <?php echo wp_trim_words(get_the_excerpt(), 24, '...'); ?>

                            </p>

                        </div>

                    </article>

                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

        </div>

    </div>

</section>






<style>
  .nav-glass-btn {
       position: absolute;
    z-index: 99999999;
    width: 110px;
    height: 110px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    color: #fff;
    font-size: 48px;
    font-weight: 700;
    overflow: hidden;
    transition: transform .25s ease;
    background: transparent;
    border: none;
    padding: 0;
  }

  .nav-glass-btn canvas.btn-gl {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
    opacity: 0;
  }

  /* Top white shine arc — over everything */
  /* .nav-glass-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: radial-gradient(
      ellipse 80% 50% at 50% -5%,
      rgba(255,255,255,0.82) 0%,
      rgba(255,255,255,0.38) 40%,
      transparent 68%
    );
    pointer-events: none;
    z-index: 10;
  } */

  .nav-glass-btn::before {
    content: "";
    position: absolute;
    inset: 1px;
    border-radius: 50%;
    background: radial-gradient(ellipse 76% 36% at 50% 0%, rgba(255,255,255,.34), transparent 72%);
    pointer-events: none;
    z-index: 9;
  }

  /* Bottom purple glow */
  .nav-glass-btn::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: radial-gradient(
      ellipse 60% 38% at 50% 108%,
      rgba(255,255,255,.78) 0%,
      rgba(255,246,249,.32) 48%,
      transparent 72%
    );
    pointer-events: none;
    z-index: 10;
  }

  /* Bright rim border via box-shadow on a wrapper */
  .nav-glass-btn .btn-rim {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,0.58);
    box-shadow:
      0 12px 28px rgba(0,0,0,0.22),
      0 5px 12px rgba(0,0,0,0.14),
      inset 0 2px 0 rgba(255,255,255,0.88),
      inset 0 -1px 0 rgba(255,255,255,0.22);
    pointer-events: none;
    z-index: 11;
  }

  .nav-glass-btn img {
    position: relative;
    z-index: 12;
    width: 115px;
    height: 115px;
    object-fit: contain;
  }

  .prev-btn { left: -62px; bottom: 21px; }
  .next-btn { right: -62px; bottom: 21px; }

  #prevBtn:hover, #nextBtn:hover { transform: scale(1.07); }
  #prevBtn:focus, #nextBtn:focus { outline: none; }
  #prevBtn:active, #nextBtn:active { transform: scale(0.96); }

  #sliderImage, #sliderTitle, #sliderDesc { transition: all 0.5s ease; }
  .slide-out { opacity: 0; transform: translateX(-40px); }
  .slide-in  { opacity: 1; transform: translateX(0); }
  .text-out  { opacity: 0; transform: translateY(20px); }
  .text-in   { opacity: 1; transform: translateY(0); }

  /* About slider: tablet layout. Desktop values above remain unchanged. */
  @media (max-width: 1024px) {
    .about-slider-container { padding-inline: 28px; }
    .about-slider-title { font-size: 2.5rem; margin-bottom: 2rem; }
    .about-slider-bg {
      top: 200px;
      height: calc(100% - 200px);
      border-radius: 26px;
    }
    .about-slider-image {
      height: 400px;
      margin-bottom: 72px;
      border-radius: 24px;
    }
    .about-slider-card-wrap {
      margin-top: -36px;
      margin-inline: 20px;
    }
    .nav-glass-btn {
      width: 90px;
      height: 90px;
      top: 355px;
      bottom: auto;
    }
    .nav-glass-btn canvas.btn-gl { width: 90px; height: 90px; }
    .nav-glass-btn img { width: 88px; height: 88px; }
    .prev-btn { left: 8px; }
    .next-btn { right: 8px; }
  }

  /* Phone layout: buttons stay inside the viewport and overlap the
     image/orange boundary without covering the content card. */
  @media (max-width: 767px) {
    .about-slider-section { padding-bottom: 8px; }
    .about-slider-container { padding-inline: 12px; }
    .about-slider-title {
      font-size: clamp(1.8rem, 8vw, 2.2rem);
      line-height: 1.15;
      margin-bottom: 24px;
    }
    .about-slider-bg {
      top: 110px;
      bottom: 0;
      height: auto;
      border-radius: 20px;
    }
    .about-slider-image {
      height: clamp(210px, 55vw, 360px);
      margin-bottom: 52px;
      border-radius: 18px;
      object-position: center;
    }
    .about-slider-card-wrap {
      margin: -16px 8px 24px;
    }
    .about-slider-card {
      padding: 22px 18px;
      border-radius: 16px;
    }
    .about-slider-card #sliderTitle {
      font-size: clamp(1.35rem, 6vw, 1.75rem);
      line-height: 1.22;
      margin-bottom: 14px;
    }
    .about-slider-card #sliderDesc {
      font-size: 1rem;
      line-height: 1.7;
    }
    .nav-glass-btn {
      width: 72px;
      height: 72px;
      top: calc(clamp(210px, 55vw, 360px) - 36px);
      bottom: auto;
    }
    .nav-glass-btn canvas.btn-gl { width: 72px; height: 72px; }
    .nav-glass-btn img { width: 70px; height: 70px; }
    .nav-glass-btn .btn-rim {
      box-shadow:
        0 10px 24px rgba(0,0,0,.32),
        0 4px 10px rgba(0,0,0,.18),
        inset 0 1px 0 rgba(255,255,255,.82);
    }
    .prev-btn { left: 8px; }
    .next-btn { right: 8px; }
    #prevBtn:hover, #nextBtn:hover { transform: none; }
    #prevBtn:active, #nextBtn:active { transform: scale(.94); }
  }

  @media (max-width: 380px) {
    .about-slider-container { padding-inline: 8px; }
    .about-slider-card-wrap { margin-inline: 5px; }
    .about-slider-card { padding: 20px 15px; }
    .nav-glass-btn { width: 66px; height: 66px; }
    .nav-glass-btn canvas.btn-gl { width: 66px; height: 66px; }
    .nav-glass-btn img { width: 64px; height: 64px; }
  }
</style>


<section class="about-slider-section py-0 overflow-hidden">
  <div class="about-slider-container max-w-[1900px] mx-auto px-4 reveal-up reveal-up">

    <h2 class="about-slider-title text-5xl font-bold text-center mb-10">About Us</h2>

    <div class="about-slider-stage relative">

      <div class="about-slider-bg absolute left-0 right-0 top-64 h-[730px] bg-[#ff4d1f] rounded-[32px] max-w-[1900px] z-0"></div>

      <div class="relative z-20 flex justify-center">
        <div class="about-slider-media relative w-full max-w-5xl">

          <img
            id="sliderImage"
            src="https://www.mailsextract.com/wp-content/uploads/2026/06/Rectangle-985.png"
            class="about-slider-image slide-in w-full h-[520px] object-cover rounded-[28px] shadow-xl mb-24"
            alt=""
          >

          <button id="prevBtn" class="nav-glass-btn prev-btn !bg-transparent hover:bg-gray-900">
            <canvas class="btn-gl" id="glPrev" width="110" height="110"></canvas>
            <div class="btn-rim"></div>
            <img src="https://www.extractmails.com/wp-content/uploads/2026/06/up-left-arrow-2.png" alt="">
          </button>

          <button id="nextBtn" class="nav-glass-btn next-btn !bg-transparent hover:bg-gray-900">
            <canvas class="btn-gl" id="glNext" width="110" height="110"></canvas>
            <div class="btn-rim"></div>
            <img src="https://www.extractmails.com/wp-content/uploads/2026/06/up-left-arrow-1.png" alt="">
          </button>

        </div>
      </div>

      <div class="about-slider-card-wrap relative z-0 max-w-7xl mx-auto -mt-12 mb-12">
        <div class="about-slider-card bg-white rounded-2xl shadow-xl p-8 md:p-10">
          <h3 id="sliderTitle" class="slide-in text-3xl md:text-4xl font-bold mb-5">
            Simplifying Business Automation
          </h3>
          <p id="sliderDesc" class="slide-in text-gray-600 leading-8 text-lg">
            MailsExtract firs is an intelligent email parsing and automation
            platform that transforms incoming emails into actionable
            business data. Extract orders, invoices, customer requests,
            reservations and more from emails automatically.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
/* ── SLIDER LOGIC ── */
const slides = [
  {
    image: "https://www.mailsextract.com/wp-content/uploads/2026/06/Rectangle-986-1.png",
    title: "Simplifying Business Automation",
    desc: "MailsExtract tes1 is an intelligent email parsing and automation platform that transforms incoming emails into actionable business data."
  },
  {
    image: "https://www.mailsextract.com/wp-content/uploads/2026/06/Rectangle-985.png",
    title: "Automated Workflow Management",
    desc: "MailsExtract test2 is an intelligent email parsing and automation platform that transforms incoming emails into actionable business data."
  },
  {
    image: "https://www.mailsextract.com/wp-content/uploads/2026/06/Rectangle-986-1.png",
    title: "AI-Powered Data Extraction",
    desc: "MailsExtract test3 is an intelligent email parsing and automation platform that transforms incoming emails into actionable business data."
  }
];
let currentSlide = 0;
const sliderImg = document.getElementById("sliderImage");
const sliderTitle = document.getElementById("sliderTitle");
const sliderDesc  = document.getElementById("sliderDesc");

function updateSlide() {
  sliderImg.classList.remove("slide-in");   sliderImg.classList.add("slide-out");
  sliderTitle.classList.remove("text-in");  sliderTitle.classList.add("text-out");
  sliderDesc.classList.remove("text-in");   sliderDesc.classList.add("text-out");
  setTimeout(() => {
    sliderImg.src          = slides[currentSlide].image;
    sliderTitle.textContent = slides[currentSlide].title;
    sliderDesc.textContent  = slides[currentSlide].desc;
    sliderImg.classList.remove("slide-out");
    sliderTitle.classList.remove("text-out");
    sliderDesc.classList.remove("text-out");
    setTimeout(() => {
      sliderImg.classList.add("slide-in");
      sliderTitle.classList.add("text-in");
      sliderDesc.classList.add("text-in");
    }, 50);
  }, 400);
}
document.getElementById("nextBtn").addEventListener("click", () => {
  currentSlide = (currentSlide + 1) % slides.length;
  updateSlide();
});
document.getElementById("prevBtn").addEventListener("click", () => {
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  updateSlide();
});

/* ── WEBGL GLASS SHADER ON EACH BUTTON CANVAS ── */
const FRAG = `
  precision mediump float;
  uniform vec2  uRes;
  uniform vec2  uCenter;
  uniform float uRadius;
  uniform float uTime;
  uniform sampler2D uTex;
  uniform vec2  uTexRes;

  vec2 coverUv(vec2 uv) {
    float ca = uRes.x / uRes.y;
    float ia = uTexRes.x / uTexRes.y;
    vec2 s = ca > ia ? vec2(1.0, ia/ca) : vec2(ca/ia, 1.0);
    return (uv - 0.5) * s + 0.5;
  }

  void main() {
    vec2 fc    = gl_FragCoord.xy;
    vec2 uv    = fc / uRes;
    vec2 d     = fc - uCenter;
    float dist = length(d);
    float r    = uRadius;

    /* outside circle = discard (transparent) */
    if (dist > r) { gl_FragColor = vec4(0.0); return; }

    float norm  = dist / r;               /* 0=center 1=edge */
    float edge  = 1.0 - norm;

    /* ── lens distortion: shrink UVs toward center ── */
    float lensStr = 0.18 * (1.0 - norm * norm);
    vec2 lensUv   = uv + normalize(d + 0.001) * (-lensStr * norm) / uRes * r;

    /* ── 9×9 blur ── */
    vec4 acc   = vec4(0.0);
    float tot  = 0.0;
    for (float x = -4.0; x <= 4.0; x += 1.0) {
      for (float y = -4.0; y <= 4.0; y += 1.0) {
        vec2 off = vec2(x, y) * 1.3 / uRes;
        acc += texture2D(uTex, coverUv(lensUv + off));
        tot += 1.0;
      }
    }
    acc /= tot;

    /* ── lighting: bright top, fade bottom ── */
    float dy       = (fc.y - uCenter.y) / r;   /* -1 bottom … +1 top (y-up) */
    float topLight  = clamp((dy + 1.0) * 0.5, 0.0, 1.0);
    float rim       = smoothstep(0.82, 1.0, norm) * 0.55;

    vec4 lit = acc + vec4(topLight * edge * 0.38) + vec4(rim * 0.45);
    lit = clamp(lit, 0.0, 1.0);

    /* ── alpha: solid inside, soft anti-alias at rim ── */
    float alpha = smoothstep(1.0, 0.96, norm);

    gl_FragColor = vec4(lit.rgb, alpha * 0.92);
  }
`;

const VERT = `
  attribute vec2 aPos;
  void main() { gl_Position = vec4(aPos, 0.0, 1.0); }
`;

function makeGlassBtn(canvasEl) {
  const gl = canvasEl.getContext("webgl", { alpha: true, premultipliedAlpha: false });
  if (!gl) return null;

  function compile(type, src) {
    const s = gl.createShader(type);
    gl.shaderSource(s, src);
    gl.compileShader(s);
    return s;
  }
  const prog = gl.createProgram();
  gl.attachShader(prog, compile(gl.VERTEX_SHADER, VERT));
  gl.attachShader(prog, compile(gl.FRAGMENT_SHADER, FRAG));
  gl.linkProgram(prog);
  gl.useProgram(prog);

  const buf = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, buf);
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1,-1, 1,-1, -1,1, 1,1]), gl.STATIC_DRAW);
  const aPos = gl.getAttribLocation(prog, "aPos");
  gl.enableVertexAttribArray(aPos);
  gl.vertexAttribPointer(aPos, 2, gl.FLOAT, false, 0, 0);

  const uRes    = gl.getUniformLocation(prog, "uRes");
  const uCenter = gl.getUniformLocation(prog, "uCenter");
  const uRadius = gl.getUniformLocation(prog, "uRadius");
  const uTime   = gl.getUniformLocation(prog, "uTime");
  const uTex    = gl.getUniformLocation(prog, "uTex");
  const uTexRes = gl.getUniformLocation(prog, "uTexRes");

  const tex = gl.createTexture();
  gl.bindTexture(gl.TEXTURE_2D, tex);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);

  let texW = 1, texH = 1;

  function uploadTex(source, w, h) {
    texW = w; texH = h;
    gl.bindTexture(gl.TEXTURE_2D, tex);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
    gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, source);
  }

  function render(t) {
    const W = canvasEl.width, H = canvasEl.height;
    gl.viewport(0, 0, W, H);
    gl.clearColor(0,0,0,0);
    gl.clear(gl.COLOR_BUFFER_BIT);
    gl.enable(gl.BLEND);
    gl.blendFunc(gl.SRC_ALPHA, gl.ONE_MINUS_SRC_ALPHA);

    gl.uniform2f(uRes, W, H);
    gl.uniform2f(uCenter, W / 2, H / 2);
    gl.uniform1f(uRadius, W / 2 - 1);
    gl.uniform1f(uTime, t * 0.001);
    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture(gl.TEXTURE_2D, tex);
    gl.uniform1i(uTex, 0);
    gl.uniform2f(uTexRes, texW, texH);
    gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
    requestAnimationFrame(render);
  }

  return { uploadTex, render };
}

/* ── Grab a screenshot of the page area behind each button ── */
function captureBackground(btnEl, glObj) {
  const rect = btnEl.getBoundingClientRect();

  /* Build a canvas from the visible DOM using html2canvas-like approach:
     Instead, we capture via a temporary offscreen canvas drawn from
     the slider image + orange bg color, which is what's visually behind. */

  const offW = 220, offH = 220;
  const off  = document.createElement("canvas");
  off.width  = offW;
  off.height = offH;
  const ctx  = off.getContext("2d");

  /* Fill with the orange background color */
  ctx.fillStyle = "#ff4d1f";
  ctx.fillRect(0, 0, offW, offH);

  /* Draw the slider image if it's behind this button */
  const img = document.getElementById("sliderImage");
  if (img && img.complete) {
    const iRect = img.getBoundingClientRect();
    const sx = rect.left - iRect.left;
    const sy = rect.top  - iRect.top;
    try {
      ctx.drawImage(img,
        Math.max(0, sx), Math.max(0, sy),
        rect.width, rect.height,
        0, 0, offW, offH
      );
    } catch(e) {}
  }

  glObj.uploadTex(off, offW, offH);
}

/* ── Init both button WebGL canvases ── */
window.addEventListener("load", () => {
  const prevCanvas = document.getElementById("glPrev");
  const nextCanvas = document.getElementById("glNext");
  const prevBtn    = document.getElementById("prevBtn");
  const nextBtn    = document.getElementById("nextBtn");

  const glPrev = makeGlassBtn(prevCanvas);
  const glNext = makeGlassBtn(nextCanvas);

  if (glPrev) {
    captureBackground(prevBtn, glPrev);
    glPrev.render(0);
  }
  if (glNext) {
    captureBackground(nextBtn, glNext);
    glNext.render(0);
  }

  /* Re-capture when slide changes */
  const origUpdate = window.updateSlide;
  document.getElementById("nextBtn").addEventListener("click", () => {
    setTimeout(() => {
      if (glPrev) captureBackground(prevBtn, glPrev);
      if (glNext) captureBackground(nextBtn, glNext);
    }, 420);
  });
  document.getElementById("prevBtn").addEventListener("click", () => {
    setTimeout(() => {
      if (glPrev) captureBackground(prevBtn, glPrev);
      if (glNext) captureBackground(nextBtn, glNext);
    }, 420);
  });
});
</script>








<style>
  /* Ripple Container */
.ripple-circle {
    position: relative;
    width: 210px;
    height: 210px;
}

/* Rings */
.ripple-circle .ring {
    position: absolute;
    inset: 0;

    border: 4px solid #FD4821 !important;
    border-radius: 50%;

    background: none !important;
    box-shadow: none !important;
    filter: none !important;
    outline: none !important;

    opacity: 0;
    animation: ripple 4s linear infinite;
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

/* Delays */
.ripple-circle .ring:nth-child(1) {
    animation-delay: 0s;
}

.ripple-circle .ring:nth-child(2) {
    animation-delay: 1s;
}

.ripple-circle .ring:nth-child(3) {
    animation-delay: 2s;
}

.ripple-circle .ring:nth-child(4) {
    animation-delay: 3s;
}
</style>





<section class="bg-[#fff] pt-16 px-6">
    <div class="max-w-[1500px] mx-auto">
        
        <div class="grid lg:grid-cols-2 gap-16 items-center reveal-up">

            <!-- LEFT IMAGE -->
            <div class="relative flex justify-center">

                <!-- Decorative Rings -->
               <!-- Left Circle -->
<div class="absolute -left-2 top-28 hidden md:block">
    <div class="ripple-circle">
        <span class="ring"></span>
        <span class="ring"></span>
        <span class="ring"></span>
        <span class="ring"></span>
    </div>
</div>

<!-- Right Circle -->
<div class="absolute right-0 top-16 hidden md:block">
    <div class="ripple-circle">
        <span class="ring"></span>
        <span class="ring"></span>
        <span class="ring"></span>
        <span class="ring"></span>
    </div>
</div>

<!-- Bottom Circle -->
<div class="absolute -bottom-8 left-1/2 -translate-x-1/2 translate-y-8 hidden md:block">
    <div class="ripple-circle">
        <span class="ring"></span>
        <span class="ring"></span>
        <span class="ring"></span>
        <span class="ring"></span>
    </div>
</div>

                <!-- Main Image -->
                <div class="relative z-10">
                    <img
                        src="https://www.mailsextract.com/wp-content/uploads/2026/07/What-We-Do-1-1.png"
                        alt="Mail automation"
                        class="w-full max-w-[520px] "
                    />
                </div>

            </div>

            <!-- RIGHT CONTENT -->
            <div class="max-w-[620px]">

                <h2 class="text-[42px] md:text-[52px] font-bold text-[#151515] leading-tight mb-6">
                    What We Do
                </h2>

                <div class="space-y-5 text-[#3e3e3e]">

                    <div>
                        <h3 class="font-bold text-[20px] mb-2">
                            MailsExtract Transforms Emails into Useful Business Data.
                        </h3>

                        <p class="text-[17px] leading-[1.8]">
                            Businesses receive critical information through email every day,
                            including customer inquiries, invoices, orders, bookings,
                            shipments, and more. Processing such information manually is
                            error-prone, time-consuming, and difficult to scale.
                        </p>
                    </div>

                    <p class="text-[17px] leading-[1.8]">
                        MailsExtract helps organizations to efficiently automate the email
                        data extraction process, just by converting unstructured emails and
                        attachments into structured and usable information. MailsExtract
                        automatically captures key data points and delivers them where they
                        are needed, like CRM platforms, databases, spreadsheets, ERPs, and
                        other business tools.
                    </p>

                    <p class="text-[17px] leading-[1.8]">
                        We help businesses with automating email processing, accelerating
                        business workflows, integrating systems seamlessly, reducing manual
                        data entry, improving data accuracy, and scale operations without
                        additional overhead.
                    </p>

                </div>

            </div>

        </div>

    </div>
</section>









<section class="bg-[#fff] pt-16 px-6">
    <div class="max-w-[1500px] mx-auto">

        <div class="grid lg:grid-cols-2 gap-16 items-center reveal-up">

            <!-- LEFT CONTENT -->
            <div class="max-w-[620px]">

                <h2 class="text-[42px] md:text-[52px] font-bold text-[#151515] leading-tight mb-6">
                    Who We Are
                </h2>

                <div class="space-y-5 text-[#3e3e3e]">

                    <div>
                        <h3 class="font-bold text-[20px] mb-2">
                            MailsExtract Transforms Emails into Useful Business Data.
                        </h3>

                        <p class="text-[17px] leading-[1.8]">
                            Businesses receive critical information through email every day,
                            including customer inquiries, invoices, orders, bookings,
                            shipments, and more. Processing such information manually is
                            error-prone, time-consuming, and difficult to scale.
                        </p>
                    </div>

                    <p class="text-[17px] leading-[1.8]">
                        MailsExtract helps organizations to efficiently automate the email
                        data extraction process, just by converting unstructured emails and
                        attachments into structured and usable information. MailsExtract
                        automatically captures key data points and delivers them where they
                        are needed, like CRM platforms, databases, spreadsheets, ERPs, and
                        other business tools.
                    </p>

                    <p class="text-[17px] leading-[1.8]">
                        We help businesses with automating email processing, accelerating
                        business workflows, integrating systems seamlessly, reducing manual
                        data entry, improving data accuracy, and scaling operations without
                        additional overhead.
                    </p>

                </div>

            </div>

            <!-- RIGHT IMAGE -->
            <div class="relative flex justify-center">

                <!-- Left Circle -->
                <div class="absolute -left-2 top-28 hidden md:block">
                    <div class="ripple-circle">
                        <span class="ring"></span>
                        <span class="ring"></span>
                        <span class="ring"></span>
                        <span class="ring"></span>
                    </div>
                </div>

                <!-- Right Circle -->
                <div class="absolute right-0 top-16 hidden md:block">
                    <div class="ripple-circle">
                        <span class="ring"></span>
                        <span class="ring"></span>
                        <span class="ring"></span>
                        <span class="ring"></span>
                    </div>
                </div>

                <!-- Bottom Circle -->
                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 translate-y-8 hidden md:block">
                    <div class="ripple-circle">
                        <span class="ring"></span>
                        <span class="ring"></span>
                        <span class="ring"></span>
                        <span class="ring"></span>
                    </div>
                </div>

                <!-- Main Image -->
                <div class="relative z-10">
                    <img
                        src="https://www.mailsextract.com/wp-content/uploads/2026/07/Who-we-are-2-1.png"
                        alt="Mail automation"
                        class="w-full max-w-[520px]"
                    />
                </div>

            </div>

        </div>

    </div>
</section>









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
