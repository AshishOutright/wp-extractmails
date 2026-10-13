


<?php
/* Template Name: contac t*/
?>
<?php


get_header();


?>





<!-- brevo -->
 <?php

function add_contact_form_to_brevo($email, $first_name = '', $last_name = '', $phone = '', $message = '') {
    $api_key = "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";
    $list_id = 57;
    $api_url = "https://api.brevo.com/v3/contacts";

    $payload = [
        'email' => $email,
        'attributes' => [
            'FIRSTNAME' => $first_name,
            'LASTNAME'  => $last_name,
            'PHONE'     => $phone,
            'MESSAGE'   => $message,
            'SOURCE'    => 'ExtractMails-Contact',
            'SUBMIT_DATE' => date('Y-m-d H:i:s')
        ],
        'listIds' => [$list_id],
        'updateEnabled' => true
    ];

    $json_payload = json_encode($payload);

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $api_key
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log('Brevo Contact Response (' . $http_code . '): ' . $response);

    return ($http_code == 201 || $http_code == 204);
}

?>
<!-- end -->

<!-- for moosend  -->
<!-- for moosend  -->
<?php
function add_contact_form_to_moosend($email, $first_name = '', $last_name = '') {
    $moosend_api_key = "a9a77b24-523e-470d-b136-a0a241b1d794"; 
    $moosend_list_id = "edabcc1d-a84b-4a09-ba44-04077a09d4a4";

    // $api_url = "https://api.moosend.com/v3/subscribers/$moosend_list_id/subscribe.json?apikey=$moosend_api_key";
        $api_url = "https://api.moosend.com/v3/subscribers/$moosend_list_id/subscribe.json?apikey=$moosend_api_key&update_if_exists=true";

    // Payload
   $moosend_payload = [
      "Name" => trim($first_name . " " . $last_name) . " (Extractmails)",
   
    "Email" => $email,
    "HasExternalDoubleOptIn" => false,
    "CustomFields" => (object) [
        "resource" => "Extractmails"
    ]
];


    $json_payload = json_encode($moosend_payload);

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Debug logs (optional, remove in production)
    error_log("Moosend Payload: " . $json_payload);
    error_log("Moosend Response ($http_code): " . $response);

    // Return true if request succeeded
    return in_array($http_code, [200, 201]);
}
?>




<!-- end -->



<?php
// Load WordPress configuration to access database credentials
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php');

// Create a database connection using WordPress constants
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

<?php

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $business_email = trim($_POST["business_email"]);
    $phone = trim($_POST["phone"]);
    $message = trim($_POST["message"]);
}
    
    // Handle checkbox value for 'agree' (convert to integer: 1 for checked, 0 for unchecked)
    $agree = isset($_POST['agree']) ? 1 : 0;

    $file_name = "";

    // Verify reCAPTCHA
    $recaptcha_secret = '6LfkcMwqAAAAALdTDpd-tMiKFU6UL088rWoWLQ-s'; // Your secret key
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Verify the reCAPTCHA with Google
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        // Set error message if reCAPTCHA fails
        $error_message = '';
    } else {
        // Handle file upload
        if (!empty($_FILES["file_upload"]["name"])) {
            $target_dir = "uploads/";

            // Create uploads folder if it doesn't exist
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = basename($_FILES["file_upload"]["name"]);
            $target_file = $target_dir . $file_name;

            if (!move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file)) {
                $error_message = "Error uploading file.";
            }
        }

        // Insert data into the contact_form table
        if (empty($error_message)) {
            $stmt = $conn->prepare("INSERT INTO contact_form (first_name, last_name, business_email, phone, message, file_upload, agree) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $first_name, $last_name, $business_email, $phone, $message, $file_name, $agree);

            if ($stmt->execute()) {
                $success_message = "Thank you for contacting us! We will get back to you soon.";
                add_contact_form_to_brevo($business_email, $first_name, $last_name, $phone, $message);
                // for moosend
                add_contact_form_to_moosend($business_email, $first_name, $last_name, "OutrightSystems");


     
     // **Send email notification to Admin**
$to = "sales@outrightcrm.com,websites@outrightcrm.com"; // Admin email
$subject = "New Contact Form Submission from Extractmails";

// Email body including name, email, phone, and message
$email_message = "
You have received a new message from the contact form:

Name: $first_name $last_name
Email: $business_email
Phone: $phone

Message:
$message

Agree to Terms: " . ($agree ? "Yes" : "No") . "
";

// Email headers
$headers = [
    "From: " . get_option('admin_email'), // Use WordPress default admin email
    "Reply-To: $business_email",
    "Content-Type: text/plain; charset=UTF-8"
];

// Send email to Admin
if (wp_mail($to, $subject, $email_message, $headers)) {
    // **✅ Send Auto-Reply Email to User**
    $user_subject = "Thank You for Contacting Us - Extractmails";
    
    $user_message = "
    Hi $first_name,

    Thank you for reaching out to Extractmails! We have received your message and will get back to you as soon as possible.

    Here’s a copy of your message:
    ------------------------------
    Name: $first_name $last_name
    Email: $business_email
    Phone: $phone
    Message:
    $message
    ------------------------------

    If this was not you, please ignore this email.

    Best Regards,  
    Extractmails Team
    ";

    $user_headers = [
        "From: Extractmails <no-reply@extractmails.com>",
        "Reply-To: support@extractmails.com",
        "Content-Type: text/plain; charset=UTF-8"
    ];

    // Send auto-reply to user
    if (wp_mail($business_email, $user_subject, $user_message, $user_headers)) {
        echo "Admin and user emails sent successfully.";
    } else {
        echo "Admin email sent, but auto-reply failed.";
    }
   // for rightee
                        get_set_blog('out_r_website_contact_form', [], ['module' => 'out_r_website_contact_form' , 'first_name_c' => $first_name, 'business_email_c'  => $business_email,  'message_c'=> $message_text, 'phone_c'=> $phone, 'resource_c'=>'Extractmails'], 0);

                        // end
} else {
    echo "Mail sending failed.";
}

// Debugging: Log the email content for troubleshooting
error_log(print_r($email_message, true));
       }
    }
    $conn->close();
}




// end
// Check if the form is submitted

$message = ''; // Initialize the message variable
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify reCAPTCHA
    $recaptcha_secret = '6LfkcMwqAAAAALdTDpd-tMiKFU6UL088rWoWLQ-s'; // Your secret key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify the reCAPTCHA with Google
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        echo "<p class='error'></p>";
    } else {
        // reCAPTCHA was successful, process the form data here
        // Your form processing logic here (e.g., send email, save to database)
        echo "<p class='success'>Thank you for contacting us! We will get back to you soon.</p>";
    }
}
?>








  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <meta http-equiv="Content-Security-Policy" content="script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/;">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

 


<?php
function enqueue_recaptcha_script() {
    wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_recaptcha_script');

?>

<div class="contact-main">
  <h1 style="color:#fff;">Contact Us</h1>

 
</div>


<!-- sec1 -->

<!-- Info Cards Section -->
 <div class=" bg-[#f3f4f6] max-w-[1800px] mx-auto py-10  mb-2 mt-10">


 <div class="text-center mb-12">
    <button class="border px-6 py-1 rounded-full text-sm mb-4">
      Contact Us
    </button>
    <h2 class="text-3xl font-semibold text-gray-800">
      Get in touch, let us know how can we help
    </h2>
  </div>

  <!-- Info Cards -->
  <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto mb-10">

    <div class="bg-white rounded-2xl shadow-md p-6 text-center">
      <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center  rounded-full"><img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-20-10.png" alt=""></div>
      <h4 class="font-semibold text-blue-600">Email Address</h4>
      <p class="text-sm text-gray-600">sales@extractmails.com</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 text-center">
      <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center bg-blue-100 rounded-full"><img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-20-11.png" alt=""></div>
      <h4 class="font-semibold text-blue-600">Make A Call</h4>
      <p class="text-sm text-gray-600">+91 7678127593</p>
      <p class="text-sm text-gray-600">+1 (218)-277-7555</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 text-center">
      <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center bg-blue-100 rounded-full"><img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-20-12.png" alt=""></div>
      <h4 class="font-semibold text-blue-600">Working Hours</h4>
      <p class="text-sm text-gray-600">Monday - Friday</p>
      <p class="text-sm text-gray-600">09:30 AM - 06:30 PM</p>
    </div>
  </div>

  <!-- Address -->
  <div class="max-w-3xl mx-auto mb-6">
    <div class="bg-white rounded-2xl shadow-md p-6 text-center">
      <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center bg-blue-100 rounded-full"><img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-73-1.png" alt=""></div>
      <h4 class="font-semibold text-blue-600 mb-2">Office Address</h4>
      <p class="text-sm text-gray-600">
        <strong>India</strong> H-51, Second Floor, Sector 63, Noida<br>
        <strong>USA</strong> 5560 Progress Rd Indianapolis, IN<br>
        <strong>UAE</strong> Ras Al Khaimah
      </p>
    </div>
  </div>


 </div>




<!-- Contact Form + Map Section (UI Updated Only) -->

<div class="bg-[#f3f4f6] py-16 px-4 max-w-[1800px] mx-auto">
  <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-start">

    <!-- LEFT SIDE FORM -->
    <div class="contact-form">

      <h3 class="text-2xl font-semibold text-gray-800 mb-2">
        Start The Conversation
      </h3>
      <p class="text-gray-500 text-sm mb-6">
        Get support, clarity, and see how WP-1Click simplifies secure WordPress setup with AI-powered automation.
      </p>

      <?php if ($message): ?>
        <div class="mb-4 text-red-500">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <!-- FORM (UNCHANGED BACKEND) -->
      <form action="#" method="post" enctype="multipart/form-data" id="contact-form" class="space-y-4">

        <div class="grid md:grid-cols-2 gap-4">
          <input type="text" name="first_name" placeholder="First Name" required
            class="w-full px-5 py-3 rounded-full bg-white shadow-md outline-none">

          <input type="text" name="last_name" placeholder="Last Name"
            class="w-full px-5 py-3 rounded-full bg-white shadow-md outline-none">
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <input type="email" name="business_email" placeholder="Business Email" required
            class="w-full px-5 py-3 rounded-full bg-white shadow-md outline-none">

          <input type="tel" name="phone" placeholder="Phone Number" required
            class="w-full px-5 py-3 rounded-full bg-white shadow-md outline-none">
        </div>

        <input type="file" name="file_upload" accept=".doc,.docx,.pdf"
          class="w-full text-sm">

        <textarea name="message" rows="5" placeholder="Type your message..."
          class="w-full px-5 py-4 rounded-2xl bg-white shadow-md outline-none"></textarea>

        <!-- Terms + reCAPTCHA -->
        <div class="form-check recaptcha-container flex flex-col md:flex-row md:items-center md:justify-between gap-4">

          <div class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="terms" id="terms" required>
            <label for="terms">
              I accept the
              <a href="https://www.outrightcrm.com/privacy-and-policy/" target="_blank" class="text-blue-600 underline">
                Terms & Conditions
              </a>
            </label>
          </div>

          <div class="g-recaptcha recaptcha-box"
            data-sitekey="6LfkcMwqAAAAAKf5xLmHvcrd3dx2SosCmfGIYp1k"></div>

        </div>

        <!-- Button -->
        <button type="submit"
          class="mt-3 bg-[#4f46e5] text-white px-6 py-2 rounded-full text-sm shadow-md hover:scale-105 transition">
          Send Message
        </button>

        <!-- Messages -->
        <?php if ($error_message): ?>
          <div class="text-red-500 mt-2">
            <?php echo $error_message; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
  	 <div class="text-green-600 mt-2">
   	  <?php echo $success_message; ?>
  	 </div>
  	<?php endif; ?>
      </form>
    </div>

    <!-- RIGHT SIDE MAP -->
    <div class="rounded-2xl overflow-hidden shadow-xl">
      <iframe
        class="w-full h-[580px]"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28280.21595328599!2d77.3710215!3d28.5680074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390c8c47f857c1a5%3A0x74fef37b30b9c5cf!2sA%20Block%2C%20Sector%2063%2C%20Noida%2C%20Uttar%20Pradesh%20201207!5e0!3m2!1sen!2sin!4v1675429923090!5m2!1sen!2sin"
        loading="lazy">
      </iframe>
    </div>

  </div>
</div>




<script>
document.getElementById('contact-form').addEventListener('submit', function (e) {
    // Check if Terms & Conditions checkbox is checked
    var termsAccepted = document.getElementById('terms').checked;
    // Check if reCAPTCHA is completed
    var recaptchaResponse = grecaptcha.getResponse();
    
    if (!termsAccepted) {
        alert('You must accept the Terms & Conditions.');
        e.preventDefault(); // Prevent form submission
        return false;
    }

    if (recaptchaResponse.length == 0) {
        alert('Please complete the reCAPTCHA.');
        e.preventDefault(); // Prevent form submission
        return false;
    }
});
</script>



<?php get_footer(); ?>
