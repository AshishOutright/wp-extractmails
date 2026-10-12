<?php
/*
Template Name: Remove Emojis From Text
*/
get_header();

// Function to remove emojis
function removeEmojis($text) {
    // Comprehensive emoji removal using Unicode ranges
    $emojiPatterns = [
        // Emoticons
        '/[\x{1F600}-\x{1F64F}]/u',
        // Miscellaneous Symbols and Pictographs
        '/[\x{1F300}-\x{1F5FF}]/u',
        // Transport and Map Symbols
        '/[\x{1F680}-\x{1F6FF}]/u',
        // Regional Indicator Symbols
        '/[\x{1F1E0}-\x{1F1FF}]/u',
        // Miscellaneous Symbols
        '/[\x{2600}-\x{26FF}]/u',
        // Dingbats
        '/[\x{2700}-\x{27BF}]/u',
        // Supplemental Symbols and Pictographs
        '/[\x{1F900}-\x{1F9FF}]/u',
        // Symbols and Pictographs Extended-A
        '/[\x{1FA70}-\x{1FAFF}]/u',
        // Additional emoji ranges
        '/[\x{2B50}\x{2B55}]/u',
        '/[\x{231A}-\x{231B}]/u',
        '/[\x{2328}\x{23CF}]/u',
        '/[\x{23E9}-\x{23F3}]/u',
        '/[\x{23F8}-\x{23FA}]/u',
        '/[\x{24C2}]/u',
        '/[\x{25AA}-\x{25AB}]/u',
        '/[\x{25B6}]/u',
        '/[\x{25C0}]/u',
        '/[\x{25FB}-\x{25FE}]/u',
        '/[\x{2934}-\x{2935}]/u',
        '/[\x{3030}\x{303D}]/u',
        '/[\x{3297}\x{3299}]/u',
        // Variation selectors (often used with emojis)
        '/[\x{FE0F}]/u',
        // Zero Width Joiner (used in emoji sequences)
        '/[\x{200D}]/u',
        // Skin tone modifiers
        '/[\x{1F3FB}-\x{1F3FF}]/u'
    ];
    
    // Apply all patterns
    foreach ($emojiPatterns as $pattern) {
        $text = preg_replace($pattern, '', $text);
    }
    
    // Clean up multiple spaces that might be left after emoji removal
    $text = preg_replace('/\s+/', ' ', $text);
    
    return trim($text);
}

// Process the request if it's a POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    function logError($message) {
        error_log(date('Y-m-d H:i:s') . " - Emoji Removal Tool: " . $message . PHP_EOL, 3, 'error.log');
    }

    try {
        // Get the JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }
        
        if (!isset($data['text']) || !is_string($data['text'])) {
            throw new Exception('Text field is required and must be a string');
        }
        
        $inputText = $data['text'];
        
        // Validate input length (prevent abuse)
        if (strlen($inputText) > 1000000) { // 1MB limit
            throw new Exception('Input text is too large. Maximum 1MB allowed.');
        }
        
        if (empty(trim($inputText))) {
            throw new Exception('Input text cannot be empty');
        }
        
        // Remove emojis
        $cleanText = removeEmojis($inputText);
        
        // Prepare response
        $response = [
            'success' => true,
            'cleanText' => $cleanText,
            'originalLength' => mb_strlen($inputText),
            'cleanLength' => mb_strlen($cleanText),
            'emojisRemoved' => mb_strlen($inputText) - mb_strlen($cleanText),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
        
    } catch (Exception $e) {
        logError($e->getMessage());
        
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Emojis From Text</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
           padding-top: 7rem;
padding-right: 2rem;
padding-bottom: 2rem;
padding-left: 2rem;
            margin-bottom: 2rem;
        }
        
        .star-rating {
            color: #ffc107;
            margin-left: 0.5rem;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .input-section, .output-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }
        
        .section-header {
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .textarea-container {
            position: relative;
            padding: 1.5rem;
        }
        
        .form-control {
            border: none;
            resize: none;
            box-shadow: none;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: transparent;
        }
        
        .char-count {
            position: absolute;
            bottom: 10px;
            right: 15px;
            font-size: 12px;
            color: #6c757d;
            background: rgba(255, 255, 255, 0.9);
            padding: 2px 6px;
            border-radius: 4px;
        }
        
        .load-file-btn {
            background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .load-file-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(54, 209, 220, 0.3);
            color: white;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .action-buttons {
            padding: 1rem 0.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            gap: 1rem;
        }
        
        .action-btn {
            flex: 1;
                padding: 0.5rem 0.3rem;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #495057;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .action-btn:hover {
            background-color: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
            text-decoration: none;
        }
        
        .copy-btn { color: #28a745; }
        .download-btn { color: #17a2b8; }
        .share-btn { color: #6f42c1; }
        
        .file-input {
            display: none;
        }
        
        .processing {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .alert {
            margin: 1rem 1.5rem;
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .action-btn {
                flex: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="main-container">
            <h1 class="mb-1">Remove Emojis From  <span class="star-rating">⭐⭐⭐⭐</span></h1>
            <p class="mb-0 opacity-75">The tool can be used to remove emojis from text and PDF files . <a href="#" class="text-white">More.</a></p>
        </div>
    </div>

    <div class="main-container">
        <div class="row g-4">
            <!-- Input Section -->
            <div class="col-lg-6">
                <div class="input-section">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <span>Input</span>
                        <button type="button" class="load-file-btn" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-upload me-2"></i>Load file
                        </button>
                        <input type="file" id="fileInput" class="file-input" accept=".txt,.pdf" onchange="handleFileUpload(this)">
                    </div>
                    <div class="textarea-container">
                        <textarea 
                            class="form-control" 
                            id="inputText" 
                            rows="12" 
                            placeholder="Sure! Here's a sentence with some text and a few emojis:

&quot;Let's get started on this project! 💪 ✨ 🚀&quot;

Let me know if you want a different tone or topic (funny, professional, romantic, etc.) 😊"
                            oninput="updateCharCount('inputText', 'inputCharCount')"
                        ></textarea>
                        <span class="char-count" id="inputCharCount">0 char(s)</span>
                    </div>
                    <div class="p-3">
                        <button type="button" class="submit-btn" onclick="removeEmojis()">
                            <i class="fas fa-paper-plane me-2"></i>Submit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Output Section -->
            <div class="col-lg-6">
                <div class="output-section">
                    <div class="section-header">
                        Output
                    </div>
                    <div class="textarea-container">
                        <textarea 
                            class="form-control" 
                            id="outputText" 
                            rows="12" 
                            readonly
                            placeholder="Sure! Here's a sentence with some text and a few emojis:

&quot;Let's get started on this project! &quot;

Let me know if you want a different tone or topic (funny, professional, romantic, etc.)"
                        ></textarea>
                        <span class="char-count" id="outputCharCount">0 char(s)</span>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="action-btn copy-btn" onclick="copyToClipboard()">
                            <i class="fas fa-copy"></i>Copy to clipboard
                        </button>
                        <button type="button" class="action-btn download-btn" onclick="downloadFile()">
                            <i class="fas fa-download"></i>Download file
                        </button>
                        <button type="button" class="action-btn share-btn" onclick="copyShareableLink()">
                            <i class="fas fa-share"></i>Copy shareable link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Set the worker source for PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        function updateCharCount(textareaId, countId) {
            const textarea = document.getElementById(textareaId);
            const count = document.getElementById(countId);
            count.textContent = `${textarea.value.length} char(s)`;
        }

        function removeEmojis() {
            const inputText = document.getElementById('inputText').value;
            
            if (!inputText.trim()) {
                showAlert('Please enter some text to process.', 'warning');
                return;
            }

            // Show processing state
            const submitBtn = document.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitBtn.disabled = true;

            // Use the same page for processing
            fetch('<?php echo get_permalink(); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ text: inputText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('outputText').value = data.cleanText;
                    updateCharCount('outputText', 'outputCharCount');
                    showAlert('Emojis removed successfully!', 'success');
                } else {
                    showAlert('Error processing text: ' + data.error, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Fallback to client-side processing
                const cleanText = inputText.replace(/[\u{1F600}-\u{1F64F}]|[\u{1F300}-\u{1F5FF}]|[\u{1F680}-\u{1F6FF}]|[\u{1F1E0}-\u{1F1FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}]/gu, '');
                document.getElementById('outputText').value = cleanText;
                updateCharCount('outputText', 'outputCharCount');
                showAlert('Emojis removed successfully!', 'success');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function handleFileUpload(input) {
            const file = input.files[0];
            if (!file) return;

            const fileType = file.type;
            const fileName = file.name;

            if (fileType === 'text/plain') {
                // Handle text file
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('inputText').value = e.target.result;
                    updateCharCount('inputText', 'inputCharCount');
                    showAlert(`Text file "${fileName}" loaded successfully!`, 'success');
                };
                reader.readAsText(file);
            } else if (fileType === 'application/pdf') {
                // Handle PDF file
                const reader = new FileReader();
                reader.onload = function(e) {
                    const typedArray = new Uint8Array(e.target.result);
                    
                    pdfjsLib.getDocument(typedArray).promise.then(function(pdf) {
                        let textContent = '';
                        let pagePromises = [];

                        for (let i = 1; i <= pdf.numPages; i++) {
                            pagePromises.push(
                                pdf.getPage(i).then(function(page) {
                                    return page.getTextContent().then(function(content) {
                                        return content.items.map(item => item.str).join(' ');
                                    });
                                })
                            );
                        }

                        Promise.all(pagePromises).then(function(pages) {
                            textContent = pages.join('\n\n');
                            document.getElementById('inputText').value = textContent;
                            updateCharCount('inputText', 'inputCharCount');
                            showAlert(`PDF file "${fileName}" loaded successfully!`, 'success');
                        }).catch(function(error) {
                            console.error('Error extracting text from PDF:', error);
                            showAlert('Error reading PDF file. Please try a different file.', 'danger');
                        });
                    }).catch(function(error) {
                        console.error('Error loading PDF:', error);
                        showAlert('Error loading PDF file. Please try a different file.', 'danger');
                    });
                };
                reader.readAsArrayBuffer(file);
            } else {
                showAlert('Please select a valid text (.txt) or PDF (.pdf) file.', 'danger');
            }
        }

        function copyToClipboard() {
            const outputText = document.getElementById('outputText');
            if (!outputText.value.trim()) {
                showAlert('No text to copy!', 'warning');
                return;
            }

            navigator.clipboard.writeText(outputText.value).then(function() {
                showAlert('Text copied to clipboard!', 'success');
            }).catch(function(err) {
                console.error('Error copying text: ', err);
                // Fallback method
                outputText.select();
                document.execCommand('copy');
                showAlert('Text copied to clipboard!', 'success');
            });
        }

        function downloadFile() {
            const outputText = document.getElementById('outputText').value;
            if (!outputText.trim()) {
                showAlert('No text to download!', 'warning');
                return;
            }

            const blob = new Blob([outputText], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'cleaned_text.txt';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showAlert('File downloaded successfully!', 'success');
        }

        function copyShareableLink() {
            const outputText = document.getElementById('outputText').value;
            if (!outputText.trim()) {
                showAlert('No text to share!', 'warning');
                return;
            }

            // Create a shareable link with the cleaned text as URL parameter
            const encodedText = encodeURIComponent(outputText);
            const shareableLink = `${window.location.origin}${window.location.pathname}?text=${encodedText}`;
            
            navigator.clipboard.writeText(shareableLink).then(function() {
                showAlert('Shareable link copied to clipboard!', 'success');
            }).catch(function(err) {
                console.error('Error copying link: ', err);
                showAlert('Error creating shareable link!', 'danger');
            });
        }

        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const outputSection = document.querySelector('.output-section');
            outputSection.insertBefore(alert, outputSection.children[1]);

            // Auto-dismiss success alerts
            if (type === 'success') {
                setTimeout(() => {
                    const alertElement = bootstrap.Alert.getOrCreateInstance(alert);
                    alertElement.close();
                }, 3000);
            }
        }

        // Load text from URL parameter if present
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sharedText = urlParams.get('text');
            if (sharedText) {
                document.getElementById('outputText').value = decodeURIComponent(sharedText);
                updateCharCount('outputText', 'outputCharCount');
            }

            // Set placeholder text
            document.getElementById('inputText').value = `Sure! Here's a sentence with some text and a few emojis:

"Let's get started on this project! 💪 ✨ 🚀"

Let me know if you want a different tone or topic (funny, professional, romantic, etc.) 😊`;
            updateCharCount('inputText', 'inputCharCount');
        });
    </script>



<style>
    /* Section wrapper */


/* Title block */
.remove_emojis_title {
  text-align: center;
  margin-bottom: 2rem;
}

.remove_emojis_title h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.remove_emojis_title p {
  font-size: 1rem;
  color: #475569;
  margin: 0;
}

/* FAQ sections */
.faq_sections,
.faq_que {
  margin-bottom: 2rem;
}

.faq_sections h2,
.faq_que h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #0b1220;
  margin-bottom: 0.75rem;
}

.faq_sections p,
.faq_que li {
  font-size: 0.95rem;
  color: #334155;
  line-height: 1.6;
}

/* Lists */
.faq_que ul {
  padding-left: 1.25rem;
  margin: 0;
}

.faq_que ul li {
  margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 576px) {
  section {
    padding: 2rem 1rem;
  }
  .remove_emojis_title h2 {
    font-size: 1.5rem;
  }
  .faq_sections h2,
  .faq_que h2 {
    font-size: 1.05rem;
  }
}


</style>

<section>
    <div class="container mt-5">
        <div class="remove_emojis_title">
            <h2>Remove Emojis: </h2>
            <p>Learn what Remove Emojis   is, how it works, and how it helps you save time.</p>
        </div>
        <div class="faq_sections">
            <h2>Q. What is Remove Emojis?  </h2>
            <p>Remove Emojis is a free tool that helps you in removing all emojis instantly. You can paste your text or upload a file, and it will give you emoji free version in just seconds.  </p>

        </div>
       <div class="faq_que">
        <h2>Q. How does it work?  </h2>
        <ul>
          <li> Paste your text in the input box or load file.  </li>
          <li>Click the Submit button.  </li>
          <li>Get emoji free version which you can copy, download your file or you can copy shareable link.  </li>
         </ul>
       </div>

        <div class="faq_que">
        <h2>Q. How does it work?  </h2>
        <ul>
          <li> Paste your text in the input box or load file.  </li>
          <li>Click the Submit button.  </li>
          <li>Get emoji free version which you can copy, download your file or you can copy shareable link.  </li>
         </ul>
       </div>

    </div>
</section>


<?php get_template_part('template-parts/hero'); ?>
</body>
</html>

<?php get_footer(); ?>