<?php
/*
Template Name: contact us
*/





global $wpdb;
$table = 'contact_form';   // your table name

$error_msg = '';

/* ================= FORM PROCESS ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {

    // Security nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'contact_nonce_action')) {
        $error_msg = 'Security verification failed. Please refresh the page.';
    } else {

        $name    = sanitize_text_field($_POST['fullname'] ?? '');
        $email   = sanitize_email($_POST['email'] ?? '');
        $phone   = sanitize_text_field($_POST['phone'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');

        // Validation
        if (empty($name) || empty($email) || empty($phone)) {
            $error_msg = 'Please fill all required fields.';
        }

        /* ===== DATABASE INSERT ===== */
        if (empty($error_msg)) {

            $inserted = $wpdb->insert(
                $table,
                [
                    'username' => $name,
                    'email'    => $email,
                    'phone'    => $phone,
                    'message'  => $message
                ],
                ['%s','%s','%s','%s']
            );

            if ($inserted) {
                // redirect with success flag (IMPORTANT FIX)
                wp_safe_redirect( add_query_arg('sent', 'true', get_permalink()) );
                exit;
            } else {
                $error_msg = 'Database error: ' . $wpdb->last_error;
            }
        }
    }
}

get_header();
?>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SUCCESS POPUP -->
<?php if(isset($_GET['sent']) && $_GET['sent'] == 'true'): ?>
<script>
document.addEventListener("DOMContentLoaded", function(){
    Swal.fire({
        icon: 'success',
        title: 'Thank You!',
        text: 'Your message has been submitted successfully. We will contact you shortly.',
        confirmButtonColor: '#6937ff'
    });

    // Remove ?sent=true from URL after popup
    if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('sent');
        window.history.replaceState({}, document.title, url.pathname);
    }
});
</script>
<?php endif; ?>

<!-- ERROR POPUP -->
<?php if($error_msg): ?>
<script>
document.addEventListener("DOMContentLoaded", function(){
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?php echo esc_js($error_msg); ?>'
    });
});
</script>
<?php endif; ?>





<div class="relative mx-auto max-w-[1550px] overflow-hidden rounded-[32px] bg-[#ff4b22] py-20 mt-32">

    <!-- Top Right Rings -->

<div class="absolute top-4 right-4">

    <!-- 192px -->
   
<!-- Top Right Rings -->

<!-- 240px -->
<div class="absolute top-[-120px] right-[-120px] h-60 w-60 rounded-full border-4 border-[#ffb6a3]"></div>
<!-- 208px -->
<div class="absolute top-[-104px] right-[-104px] h-52 w-52 rounded-full border-4 border-[#ffb6a3]"></div>
<!-- 176px -->
<div class="absolute top-[-88px] right-[-88px] h-44 w-44 rounded-full border-4 border-[#ffb6a3]"></div>

<!-- 144px -->
<div class="absolute top-[-72px] right-[-72px] h-36 w-36 rounded-full border-4 border-[#ffb6a3]"></div>
</div>



    <!-- Bottom Left Rings -->
    
<!-- Bottom Left Rings -->
<div class="absolute -bottom-4 -left-2">

    <!-- 1 -->

<!-- 1 -->
<div class="absolute bottom-[-92px] left-[-92px] h-60 w-60 rounded-full border-4 border-[#ffb6a3]"></div>

<!-- 2 -->
<div class="absolute bottom-[-76px] left-[-76px] h-52 w-52 rounded-full border-4 border-[#ffb6a3]"></div>

<!-- 3 -->
<div class="absolute bottom-[-60px] left-[-60px] h-44 w-44 rounded-full border-4 border-[#ffb6a3]"></div>

<!-- 4 -->
<div class="absolute bottom-[-44px] left-[-44px] h-36 w-36 rounded-full border-4 border-[#ffb6a3]"></div>


</div>



    <!-- Content -->
    <div class="relative z-10 flex flex-col items-center justify-center text-center">

        <button
            class="mb-5 rounded-2xl bg-white px-10 py-3 text-3xl font-medium text-[#ff4b22] shadow-lg hover:bg-black">
            Contact Us
        </button>

        <h2 class="text-5xl font-semibold text-white mt-4">
            Get in Touch
        </h2>

    </div>

</div>






<section class="bg-[#fff] py-10">
    <div class="max-w-[1550px] mx-auto px-4">

        <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-0 items-center">

            <!-- Left Content -->
            <div>

                <h2 class="text-[48px] font-bold text-[#333] leading-tight">
                    Let's Talk!
                </h2>

                <p class="mt-4 text-[20px] text-[#444] leading-relaxed max-w-[1150px]">
                    Get in touch and discover how MailsExtract can efficiently
                    simplify your workflows, eliminate manual data entry,
                    and transform how your business handles email data.
                </p>

                <form class="mt-10 space-y-4">

                    <input
                        type="text"
                        placeholder="Name"
                        class="w-full h-[68px] px-10 rounded-full border border-[#d8d8d8] bg-[#fff] placeholder:text-[#9b9b9b]"
                    >

                    <input
                        type="email"
                        placeholder="Email"
                        class="w-full h-[68px] px-10 rounded-full border border-[#d8d8d8] bg-transparent focus:outline-none focus:ring-0 placeholder:text-[#9b9b9b]"
                    >

                    <input
                        type="text"
                        placeholder="Phone Number"
                        class="w-full h-[68px] px-10 rounded-full border border-[#d8d8d8] bg-transparent focus:outline-none focus:ring-0 placeholder:text-[#9b9b9b]"
                    >

                    <textarea
                        placeholder="Type your message..."
                        rows="7"
                        class="w-full px-10 py-6 rounded-[30px] border border-[#d8d8d8] bg-transparent resize-none focus:outline-none focus:ring-0 placeholder:text-[#9b9b9b]"
                    ></textarea>

                    <button
                        type="submit"
                        class="mt-4 bg-black text-white px-8 py-3 rounded-full text-lg font-medium hover:opacity-90 transition"
                    >
                        Send Message
                    </button>

                </form>

            </div>

            <!-- Right Image -->
            <div class="flex justify-center lg:justify-end">

               

                    <!-- Top Rings -->
                   
                    <!-- Your Image -->
                    <img
                        src="http://www.guestpostcrm.com/wp-content/uploads/2026/06/Frame-164.png"
                        alt="Contact"
                        class="relative z-10 w-full max-w-[500px] object-cover"
                    >

          

            </div>

        </div>

      
     
<div class="mt-6 h-px w-full bg-gradient-to-r from-transparent via-gray-400 to-transparent"></div>



    </div>
</section>




<section class="py-8 bg-white">
    <div class="max-w-[1550px] mx-auto px-4">

        <div class="grid grid-cols-1 lg:grid-cols-[0.8fr_1.2fr] gap-12 items-start">

            <!-- Left Map -->
            <div>
                <div class="overflow-hidden rounded-[24px] shadow-lg">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18..."
                        width="100%"
                        height="450"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
            </div>

            <!-- Right Contact Info -->
            <div class="space-y-0">

                <!-- Email -->
                <div class="flex gap-5 pb-8">
                   <div
                        class="flex h-14 w-14 ">
                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-165.png" alt="">
                    </div>

                    <div>
                        <h3 class="text-[20px] font-semibold text-[#333]">
                            Email Address
                        </h3>

                        <a href="mailto:sales@outrightcrm.com"
                            class="mt-1 block text-[18px] text-blue-600 hover:underline">
                            sales@outrightcrm.com
                        </a>
                    </div>
                </div>

               <!-- <div class=" h-px w-full bg-gradient-to-r from-gray-200 via-gray-600 via-gray-200 to-transparent"></div> -->
                <div
  class="h-px w-full"
  style="background: linear-gradient(to right, #e5e7eb 0%, #4b5563 40%, #e5e7eb 80%, transparent 100%);"
></div>

                <!-- Call -->
                <div class="flex gap-5 py-8">
                   <div
                        class="flex h-14 w-14 ">
                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-166.png" alt="">
                    </div>

                    <div>
                        <h3 class="text-[20px] font-semibold text-[#333]">
                            Make A Call
                        </h3>

                        <p class="mt-1 text-[18px] text-[#555]">
                            0120-423-9837, (+1) 218-277-7555
                        </p>
                    </div>
                </div>

               <div
  class="h-px w-full"
  style="background: linear-gradient(to right, #e5e7eb 0%, #4b5563 40%, #e5e7eb 80%, transparent 100%);"
></div>

                <!-- Working Hours -->
                <div class="flex gap-5 py-8">
                   <div
                        class="flex h-14 w-14 ">
                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-167.png" alt="">
                    </div>

                    <div>
                        <h3 class="text-[20px] font-semibold text-[#333]">
                            Working Hours
                        </h3>

                        <p class="mt-1 text-[18px] text-[#555]">
                            Monday - Friday (09:30 AM - 06:30 PM)
                        </p>
                    </div>
                </div>

              <div
  class="h-px w-full"
  style="background: linear-gradient(to right, #e5e7eb 0%, #4b5563 40%, #e5e7eb 80%, transparent 100%);"
></div>

                <!-- Address -->
                <div class="flex gap-5 pt-8">
                    <div
                        class="flex h-14 w-14 ">
                        <img src="https://www.mailsextract.com/wp-content/uploads/2026/06/Frame-168.png" alt="">
                    </div>

                    <div>
                        <h3 class="text-[20px] font-semibold text-[#333]">
                            Office Address
                        </h3>

                        <p class="mt-2 text-[18px] text-[#444]">
                            <strong>H-51</strong>, Second Floor, Sector 63,
                            Noida, Uttar Pradesh 201301
                        </p>

                        <p class="mt-3 text-[18px] text-[#444]">
                            <strong>USA</strong> 5560 Progress Rd Indianapolis,
                            IN 46241, United States
                        </p>

                        <p class="mt-3 text-[18px] text-[#444]">
                            <strong>UAE</strong> Al Nakheel Area,
                            P.O. Box No. 10055, Ras Al Khaimah,
                            United Arab Emirates.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>





































<style>
    /* ===== Remove Astra default form styling ===== */

form input[type="text"],
form input[type="email"],
form input[type="tel"],
form input[type="url"],
form input[type="password"]{

    height:54px !important;
    padding:0 28px !important;
    border-radius:9999px !important;   /* perfect pill */
   
    box-shadow:none !important;
}

/* Focus style */
form input:focus{
    outline:none !important;
    box-shadow:0 0 0 2px #8b5cf6 !important;
}

</style>




















<?php get_footer(); ?>
