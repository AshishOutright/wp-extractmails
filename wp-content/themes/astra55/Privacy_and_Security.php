<?php
    /* Template Name: Privacy_and_Security */
?>
<?php get_header(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Email Extraction for Your Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0j60pDi2pqfgh1rJGGX58JlF0z6Qj3/KGRf09eYEmX9ykkJ8" crossorigin="anonymous" defer></script>


    <style>
        .body_pricing {
            background-color:black;
            background-image: url('https://dev.outrightcrm.in/dev/finalextractmailsdev.com/wp-content/uploads/2025/01/Line-12.png');
            background-position: center center;
    background-size: cover;
    opacity: 1;
    transition: background 0.3s, border-radius 0.3s, opacity 0.3s;
        }
   
      

        /* faq_sactions---------------------------------------------------start */
        .faq-container {
            max-width: 600px;
            margin: auto;
        }
        .faq-item {
            border-bottom: 1px solid #ddd;
            
        }
        .faq-question {
            width: 100%;
            text-align: left;
             background: none;
            padding: 15px;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            outline: none;
            color:white;
            
        }

        .faq-question:hover {
    background: none !important;
    color: white !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.faq-question {
    background: transparent !important;
}

.faq-question:hover,
.faq-question:focus,
.faq-question:active {
    background: transparent !important;
    box-shadow: none !important;
}
        .faq-answer {
            display: none;
            padding: 15px;
            background: #fff;
            font-size: 16px;
            transition: max-height 0.9s ease-out, padding 0.4s ease-out;
        }
        .faq-icon {
            font-size: 20px;
            font-weight: bold;
        }


 /* faq_sactions---------------------------------------------------end */

 /* how_we_can_help_sections----------------------------------------------start */
 .main-container {
            max-width: 1140px;
            margin: 0 auto;
        }
        
        .heading {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .heading h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: #333;
            margin-top: 88px;
            margin-bottom: 20px;
        }
        
        .heading p {
            max-width: 800px;
            margin: 0 auto;
            color: #666;
            font-size: 1.1rem;
        }
        
        .cards-row {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 40px 0;
            flex-wrap: wrap;
        }
        
        .feature-card {
            width: 100%;
            max-width: 540px;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .blue-card {
            background-color: #1a237e;
        }
        
        .orange-card {
            background-color: #ff5722;
        }
        
        .circle-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .feature-card h2 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        
        .btn-action {
            background-color: white;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
            margin-top: 10px;
        }
        
        .btn-action:hover {
            opacity: 0.9;
        }
        
        .footer-text {
            text-align: center;
            color: #666;
            margin-top: 40px;
            font-size: 0.95rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .feature-card {
                max-width: 430px;
            }
        }
        
        @media (max-width: 768px) {
            .heading h1 {
                font-size: 2.2rem;
                margin-top: 28px;
            }
            
            .cards-row {
                gap: 20px;
            }
            
            .feature-card {
                padding: 25px;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding: 0px 0px;
            }
            
            .heading h1 {
                font-size: 1.8rem;
            }
            
            .heading p {
                font-size: 1rem;
            }
            
            .feature-card h2 {
                font-size: 1.5rem;
            }
        }

/* how_we_can_help_sections----------------------------------------------end */
<style>
        .body_privacy{
            background-color: #f8f9fa;
        }
        .icon-box {
            font-size: 2rem;
            color: #007bff;
        }
        .card {
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class=' d-flex justify-content-center align-items-center '>
    <div class="container py-5 m-5  ">
        <h2 class="text-center mb-4">Privacy & Security</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-3 text-center shadow-sm">
                    <i class="fas fa-lock icon-box"></i>
                    <h5 class="mt-3">Local Processing</h5>
                    <p>Ensures data confidentiality by handling processing on local devices.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center shadow-sm">
                    <i class="fas fa-cloud-slash icon-box"></i>
                    <h5 class="mt-3">No Cloud Storage</h5>
                    <p>Extracted emails are never stored in the cloud, maintaining full privacy.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center shadow-sm">
                    <i class="fas fa-shield-alt icon-box"></i>
                    <h5 class="mt-3">Encrypted Data</h5>
                    <p>All extracted email data is securely encrypted to prevent unauthorized access.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center shadow-sm">
                    <i class="fas fa-gavel icon-box"></i>
                    <h5 class="mt-3">Regulatory Compliance</h5>
                    <p>Complies with international data protection regulations for user safety.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center shadow-sm">
                    <i class="fas fa-user-secret icon-box"></i>
                    <h5 class="mt-3">Optional Anonymization</h5>
                    <p>Allows users to anonymize extracted email data for added privacy.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center shadow-sm">
                    <i class="fas fa-user-lock icon-box"></i>
                    <h5 class="mt-3">User Control</h5>
                    <p>Users have full control over their data and can manage their privacy settings.</p>
                </div>
            </div>
        </div>
    </div>
    </div>

<div class=" container-fluid body_pricing  d-flex justify-content-center ">

   <div class=" container m-lg-5 p-lg-5"> 
   <h2 class="text-center text-white">Frequently Asked Questions</h2>
    
    <div class="faq-item ">
        <button class="faq-question">
        Will my email information be safe while using  extractmails.com?  
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">Of course, We prioritize data protection and use protocols to guarantee the privacy of your data. 
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
        Does extractmails.com allow for bulk email extraction? 
 
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">Yes, we offer bulk extraction features to manage multiple emails at once.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
        Which software works with extractmails.com?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">Our service is compatible with all leading applications, such as CRM systems, spreadsheets, and various business tools, to enhance workflow automation. </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
        Is extractmails.com free to use?  
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">We provide a variety of plans, including both free and paid options.
        </div>
    </div>

    <div class="faq-item">
        <button class="faq-question">
        Where can I get help if I get into any problem on extractmails.com?
            <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">For any kind of support, you can contact us via email or by visiting our Contact Us page.
        </div>
    </div>
</div>
</div>

<script>
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const answer = button.nextElementSibling;
            const icon = button.querySelector('.faq-icon');

            if (answer.style.display === "block") {
                answer.style.display = "none";
                icon.textContent = "+";
            } else {
                document.querySelectorAll('.faq-answer').forEach(a => a.style.display = "none");
                document.querySelectorAll('.faq-icon').forEach(i => i.textContent = "+");
                
                answer.style.display = "block";
                icon.textContent = "-";
            }
        });
    });
</script>

   <!-- Frequently Asked Questions_end----------------------------------------------------------------- -->

   <!-- /* how_we_can_help_sections----------------------------------------------start */ -->

    <div class="container ">
        <div class="heading">
            <h1>How can we help you?</h1>
            <p>If you need to extract emails from websites or documents, Extractmails can help you quickly collect and organize contacts for your marketing campaigns.</p>
        </div>
        
        <div class="cards-row">
            <!-- Extract Emails Card -->
            <div class="feature-card blue-card">
                <div>
                    <div class="circle-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </div>
                    <h2>Extract Emails Now</h2>
                    <p>Extract emails from any URL or uploaded document in seconds. Our tool finds all hidden email addresses. 100% accuracy guaranteed.</p>
                </div>
                <a href="#" class="btn-action">Start Extraction ?</a>
            </div>
            
            <!-- Bulk Email Finder -->
            <div class="feature-card orange-card">
                <div>
                    <div class="circle-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h2>Bulk Email Finder</h2>
                    <p>Process multiple websites at once with our advanced bulk extraction tool. Over 10,000+ businesses trust Extractmails for lead generation and marketing campaigns.</p>
                </div>
                <a href="#" class="btn-action">Try Bulk Finder ?</a>
            </div>
        </div>
        
        <div class="footer-text mb-5">
            Extractmails - The #1 Email Extraction Tool for Marketers and Sales Teams
        </div>
    </div>

    </div>
    </div>

    <!-- Bootstrap & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add hover effect for cards
        $(document).ready(function() {
            // Make sure all elements are fully loaded
            setTimeout(function() {
                $('.feature-card').css('opacity', '1');
            }, 100);
        });
    </script>




       
         
       
       
</body>
</html>

<?php get_footer(); ?>
