<?php
/**
 * The template for displaying 404 pages (not found).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

<section class="min-h-[70vh] flex items-center mt-10 pt-10">
    <div class="max-w-7xl mx-auto px-4 w-full">
        <div class="flex flex-col lg:flex-row items-center">

            <!-- LEFT CONTENT -->
            <div class="w-full lg:w-1/2 text-center lg:text-left">

                <h1 class="text-4xl font-bold mb-4 text-[#0f71b7]">
                    Page Not Found!
                </h1>

                <p class="text-lg text-gray-500 mb-4">
                    Uh-oh! The page you’re trying to reach doesn’t
                    <br>
                    exist. Let’s head back to the homepage.
                </p>

                <a href="<?php echo home_url(); ?>"
                   class="inline-block px-4 py-2 text-white bg-[#7b3be9] hover:bg-blue-600 transition rounded">
                    Go to Home
                </a>

            </div>

            <!-- RIGHT IMAGE -->
            <div class="w-full lg:w-1/2 text-center mt-6 lg:mt-0">
                <img src="https://www.extractmails.com/wp-content/uploads/2026/02/error-404-img-1.png"
                     class="mx-auto max-w-full"
                     alt="404 error">
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>