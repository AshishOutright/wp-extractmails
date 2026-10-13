


<?php
/* Template Name: contact-page.php */
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
    $recaptcha_response = $_POST['g-recaptcha-response'];

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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <meta http-equiv="Content-Security-Policy" content="script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/;">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <style>


    body, html {
        margin-top:500px;
      height: 100%;
      margin: 0;
    }

   
    .ast-container{
        display: contents!important;
    }

    /* Contact Main Section Styling */
    .contact-main {
      background: url('https://www.extractmails.com/wp-content/uploads/2025/02/pp.webp') no-repeat center center;
      background-size: cover; /* Cover entire section */
      height: 38vh; /* Full viewport height */
      /* height: 80vh; */
      display: flex;
      flex-direction: column;
      justify-content: center; /* Vertically center the content */
      align-items: center; /* Horizontally center the content */
      padding: 0 15px;
      color: white; /* Ensure text is visible */
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Improve readability */
    }

    .container {
      max-width: 1200px;
      width: 100%;
    }

    .card {
        background: rgb(2,0,36);
        background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,64,121,1) 35%, rgba(0,212,255,1) 100%);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }

    .card:hover {
      transform: translateY(-10px) scale(1.05);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .card-body {
      padding: 20px;
    }

    .card h5 {
      font-weight: bold;
    }



.contact-us {
  padding: 40px;
  background-color: #fff;
  border-radius: 8px;
  /* margin-top: 40px; */
  margin-top: 65px;
  /* box-shadow: 0px 10px 30px rgba(136, 136, 255, 0.2); */
  /* transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; */
  text-align: center; /* Center the text inside the section */
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.contact-us h2 {
  font-size: 2em;
  font-family: 'Arial', sans-serif;
  color: #333;
  margin-bottom: 10px;
  
}

.contact-us p {
  font-size: 1.2em;
  font-family: 'Arial', sans-serif;
  color: #555;
}

.contact-us a {
  color: #007bff;
  text-decoration: none;
}

.contact-us a:hover {
  text-decoration: underline;
}

/* Optional: Adding hover effect to the section */

.info-card {
        text-align: center;
        background: #fff;
        padding: 11px;
        border-radius: 15px;
        box-shadow: 0px 10px 30px rgba(136, 136, 255, 0.2);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        height: 387px;
    }
    
    /* 3D Hover Effect */
    .info-card:hover {
        transform: translateY(-10px) scale(1.05);
        box-shadow: 0px 15px 40px rgba(136, 136, 255, 0.4);
    }

    .icon-box {
        width: 70px;
        height: 70px;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(136, 136, 255, 0.1);
        transition: background 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    /* Icon Hover Animation */
    .info-card:hover .icon-box {
        background: rgba(136, 136, 255, 0.3);
        transform: rotate(10deg);
    }

    .icon-box i {
        font-size: 28px;
        color: #6C63FF;
        transition: color 0.3s ease-in-out;
    }

    .info-card:hover .icon-box i {
        color: #4b47ff;
    }

    .info-card h5 {
        font-weight: bold;
    }

    .info-card p {
        color: #777;
    }

    /* end */
/* Make reCAPTCHA smaller and align it to the right */
.g-recaptcha {
    transform: scale(0.75); /* Scale down the size */
    transform-origin: 0 0; /* Set scaling origin to top-left */
    position: absolute; /* Position it to the right */
    right: 0; /* Align to the right side */
    top: 10px; /* Adjust vertical position if needed */
}

/* Optional: Make sure the parent container has enough space */


.contact-form form {
    text-align: center;
    background: #ebebfd;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0px 10px 30px rgba(136, 136, 255, 0.2);
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
 
}

/* 3D Hover Effect for Form */
/* .contact-form form:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0px 15px 40px rgba(136, 136, 255, 0.4);
} */

.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 15px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.contact-form button {
    width: 100%;
    padding: 15px;
    background-color: #6C63FF;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

/* Button Hover Effect */
.contact-form button:hover {
    background-color: #4b47ff;
}

/* Style for row and columns */
.contact-form .row {
    margin: 0 -10px; /* Adjust for proper spacing */
}

.contact-form .col-md-6 {
    padding: 0 10px; /* Add spacing between columns */
}

/* Ensure inputs inside .col-md-6 take full width */
.contact-form .col-md-6 input {
    width: 100%;
}

/* Ensure responsive behavior: stack input fields on smaller screens */
@media (max-width: 767px) {
    .contact-form .col-md-6 {
        width: 100%; /* Stack inputs vertically on small screens */
        padding: 0; /* Remove horizontal padding */
    }
}
/* trems & con*/

/* Styling the form check (checkbox) */
.form-check {
    /* margin-top: 15px;
    font-size: 14px; */
    display: flex!important;
    align-items: center;
    height: 62px;
}

/* Styling the checkbox */
.form-check input[type="checkbox"] {
    margin-right: 10px;
    width: 20px;
    height: 20px;
    border: 2px solid #333;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Adding a checkmark when the checkbox is checked */
.form-check input[type="checkbox"]:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-check input[type="checkbox"]:checked::after {
    font-size: 16px;
    color: white;
    position: absolute;
    left: 5px;
    top: 3px;
}

/* Styling the label text */
.form-check label {
    font-size: 14px;
    color: #333;
    cursor: pointer;
}

/* Styling the anchor link */
.form-check a {
    color: #007bff;
    text-decoration: none;
}

.form-check a:hover {
    text-decoration: underline;
}

/* Adding spacing for better alignment */
button[type="submit"] {
    margin-top: 20px;
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}



    /* end */

    .google-map {
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  text-align: center;
  margin-top: 40px;
  box-shadow: 0px 10px 30px rgba(136, 136, 255, 0.2); /* Applying box-shadow */
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; /* Adding transition */
}

.google-map h2 {
  font-size: 2em;
  color: #333;
  margin-bottom: 10px;
}

.google-map p {
  font-size: 1.2em;
  color: #555;
  margin-bottom: 20px;
}

.map-container {
  max-width: 100%;
  overflow: hidden;
  position: relative;
}

.map-container iframe {
  width: 100%;
  height: 400px;
  border: none;
}

/* Optional: Adding hover effect to the map section */
.google-map:hover {
  transform: scale(1.05); /* Slight zoom effect on hover */
  box-shadow: 0px 15px 40px rgba(136, 136, 255, 0.4); /* Darker shadow on hover */
}



/* media Query */
/* For tablets and smaller devices */
@media (max-width: 992px) {
    .contact-main {
        height: 30vh; /* Adjust the height */
        text-align: center;
    }

    .info-card {
        padding: 20px;
        height: auto; /* Allow content to determine height */
    }

    .g-recaptcha {
        transform: scale(0.85); /* Adjust reCAPTCHA size */
    }
}

/* For mobile devices */
@media (max-width: 767px) {
    .contact-main {
        height: 25vh; /* Reduce height */
        padding: 20px;
    }

    .container {
        padding: 10px;
    }

    .info-card {
        margin-bottom: 20px; /* Add space between cards */
        padding: 15px;
    }

    .contact-form form {
        padding: 20px;
    }

    .contact-form input,
    .contact-form textarea {
        font-size: 14px;
        padding: 10px;
    }

    .contact-form button {
        font-size: 14px;
        padding: 10px;
    }

    .form-check {
        display: block; /* Stack elements */
        height: auto;
        text-align: left;
    }

    .g-recaptcha {
        transform: scale(0.7); /* Reduce size further */
    }

    .google-map iframe {
        height: 250px; /* Adjust map height */
    }
}


/* For extra small devices */
@media (max-width: 480px) {
    .contact-main {
        height: 20vh; /* Further reduce height */
    }

    .info-card {
        padding: 10px;
        font-size: 14px;
    }

    .contact-form form {
        padding: 15px;
    }

    .contact-form input,
    .contact-form textarea {
        font-size: 12px;
        padding: 8px;
    }

    .contact-form button {
        font-size: 12px;
        padding: 8px;
    }

    .g-recaptcha {
        transform: scale(0.6); /* Scale down further */
    }
}


  </style>


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
<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="info-card">
                <div class="icon-box">
                    <i class="fas fa-envelope"></i>
                </div>
                <h5>Email Address</h5>
                <p> sales@outrightcrm.com</p>
            </div>
        </div>
        <div class="col-md-3">
    <div class="info-card">
        <div class="icon-box">
            <i class="fas fa-phone"></i>
        </div>
        <h5>Make A Call</h5>
        <p>0120-423-9837</p>
        <p>(+1) 218-277-7555</p>
        <!-- WhatsApp Icon Link -->
        <a href="https://api.whatsapp.com/send?phone=919005468298" target="_blank">
            <img src="https://www.mailsextract.com/wp-content/uploads/2025/07/whatsapp-2-1.png" alt="WhatsApp" style="width: 40px; height: 40px;"/>
        </a>
    </div>
</div>

        <div class="col-md-3">
            <div class="info-card">
                <div class="icon-box">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h5>Office Location</h5>
                <p><strong>India</strong>  H-51, Second Floor, Sector 63, Noida,
Uttar Pradesh 201301</p>
<p><strong>USA</strong> 5560 Progress Rd Indianapolis, IN 46241, United States</p>

<p><strong>UAE</strong> Al Nakheel Area, P.O. Box No. 10055, Ras Al Khaimah, United Arab Emirates.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <div class="icon-box">
                    <i class="fas fa-clock"></i>
                </div>
                <h5>Working Hours</h5>
                <p>Monday - Friday<br>09:30 AM - 06:30 PM</p>
            </div>
        </div>
    </div>
</div>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Custom Styles -->
<style>
    
</style>

<!-- add new  -->
<section class="contact-us">
<h2 style="font-weight: bold; color: #5b24cb;">Contact Us</h2>

  <p>We'd love to hear from you! Please reach out with any questions or comments.</p>
  <p>Email us at: <a href="mailto:example@example.com" style="color:#5b24cb">sales@outrightcrm.com</a></p>
</section>
<style>
 



</style>

<!-- thrd -->

<!-- Contact Form Section -->
<!-- Contact Form Section -->

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="contact-form">
                <?php if ($message): ?>
                    <div class="alert-container">
                        <?php echo $message; // Display the success or error message ?>
                    </div>
                <?php endif; ?>
                
                <form action="#" method="post" enctype="multipart/form-data" id="contact-form">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="last_name" placeholder="Last Name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="email" name="business_email" placeholder="Business Email" required>
                        </div>
                        <div class="col-md-6">
                            <input type="tel" name="phone" placeholder="Phone Number" required>
                        </div>
                    </div>
                    <input type="file" name="file_upload" accept=".doc,.docx,.pdf">
                    <textarea name="message" rows="5" placeholder="Your Message"></textarea>

                    <!-- Terms & Conditions Checkbox -->
                    <div class="form-check recaptcha-container">
                        <input type="checkbox" name="terms" id="terms" required>
                        <label for="terms">
                            I accept the <a href="https://www.outrightcrm.com/privacy-and-policy/" target="_blank">Terms & Conditions</a>
                        </label>
                    

                    <!-- reCAPTCHA Section -->
                    
                        <div class="g-recaptcha recaptcha-box" data-sitekey="6LfkcMwqAAAAAKf5xLmHvcrd3dx2SosCmfGIYp1k" required></div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Send Message</button>

                      <!-- Display error message if reCAPTCHA fails -->
                      <?php if ($error_message): ?>
                        <div class="alert alert-danger mt-3">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display success message after form submission -->
                    <?php if ($success_message): ?>
                        <div class="alert alert-success mt-3">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                </form>
            </div>
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

<style>


.recaptcha-container {
    display: flex;
    align-items: center;
    gap: 15px; /* Adjust space between checkbox and reCAPTCHA */
    flex-wrap: wrap;
}

/* For Mobile: Stack elements */
@media (max-width: 767px) {
    .recaptcha-container {
        flex-direction: column;
        align-items: flex-start; /* Aligns left on small screens */
    }
}


</style>


<!-- add -->
<section class="google-map">
  <h2 style="color:#5b24cb">Our Location</h2>
  <p>Find us at:</p>
  <p>Noida Sector 63, H-51, 2nd Floor</p>
  <!-- Embed Google Map -->
  <div class="map-container">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28280.21595328599!2d77.3710215!3d28.5680074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390c8c47f857c1a5%3A0x74fef37b30b9c5cf!2sA%20Block%2C%20Sector%2063%2C%20Noida%2C%20Uttar%20Pradesh%20201207!5e0!3m2!1sen!2sin!4v1675429923090!5m2!1sen!2sin"
    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</section>
<style>

 
</style>

 


<?php get_footer(); ?>