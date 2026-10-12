<?php
// Hide warnings and notices in production
// ini_set('display_errors', 0);
// error_reporting(E_ERROR | E_PARSE);

require '/web/html/vendor/vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include '/web/html/global_utilsfinal.php';

$downloadLink = '';
$customerData = [];
$step = '';
$conversionError = '';
// Define recent customers (hardcoded as per your requirement)
$recentCustomers = [
    'Mike Hanson',
    'Jacqueline Olson',
    'John Milleli',
    'Sue Nelson',
    'Rick Pasin',
    'Rhonda Lind',
    'Tony Tanios',
    'Nick Hoffman'
];
// Handle new customer case
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_customer'])) {
    $customerData = ['is_new' => true];
    $step = 'generate';
}
// PDF Conversion Function
function convertDocxToPdf($docxFilePath, $apiSecret) {
    $url = "https://v2.convertapi.com/convert/docx/to/pdf?Secret=$apiSecret";
    
    // Read the DOCX file content
    $fileContent = file_get_contents($docxFilePath);
    $fileName = basename($docxFilePath);
    
    // Create a temporary file for cURL
    $tempFile = tmpfile();
    fwrite($tempFile, $fileContent);
    $metaData = stream_get_meta_data($tempFile);
    $tempFilePath = $metaData['uri'];
    
    $postData = [
        'File' => new CURLFile($tempFilePath, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', $fileName),
        'StoreFile' => 'true'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 second timeout
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        fclose($tempFile);
        return ['error' => 'cURL Error: ' . $error];
    }
    
    curl_close($ch);
    fclose($tempFile);
    
    if ($httpCode !== 200) {
        return ['error' => 'HTTP Error: ' . $httpCode . ' - ' . $response];
    }
    
    $responseData = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid JSON response from ConvertAPI'];
    }
    
    return $responseData;
}

function downloadFileFromUrl($url, $localPath) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch) || $httpCode !== 200) {
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    return file_put_contents($localPath, $data) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? '';

    // STEP 1: Search by name
    if ($step === 'search') {
        $name = $_POST['name'] ?? '';
        if ($name) {
            $results = get_set_blog('outr_invoice_cusotmers', ['name' => $name], [], 1);
            if (!empty($results)) {
                $customerData = $results[0]; // take first match
            }
        }
    }

    // STEP 2: Generate and convert to PDF
    if ($step === 'generate') {
    //     $data = [
    //         '{{invoice_date}}'    => $_POST['invoice_date'] ?? date('d-M-Y'),
    //         '{{due_date}}'        => $_POST['due_date'] ?? date('d-M-Y', strtotime('+7 days')),
    //         '{{invoice_num}}'     => $_POST['invoice_num'] ?? 'INV-XXXX',
    //         '{{contact_name}}'    => $_POST['contact_name'] ?? 'N/A',
    //         '{{company_name}}'    => $_POST['company_name'] ?? 'N/A',
    //         '{{Address}}'         => $_POST['address'] ?? 'N/A',
    //         '{{country}}'         => $_POST['country'] ?? 'N/A',
    //         '{{Project_type}}'    => $_POST['project_type'] ?? 'CRM Integration',
    //          '{{frequency}}'       => $_POST['frequency_c'] ?? 'N/A',
    // '{{first_amount}}'    => $_POST['first_amount_c'] ?? '0',
    // '{{second_amount}}'   => $_POST['second_amount_c'] ?? '0',
    // '{{last_amount}}'     => $_POST['last_amount_c'] ?? '0'
    //     ];

    // Calculate total amount
    // Calculate total amount safely
// $first_amount  = isset($_POST['first_amount_c'])  ? (float)$_POST['first_amount_c']  : 0;
// $second_amount = isset($_POST['second_amount_c']) ? (float)$_POST['second_amount_c'] : 0;
// $last_amount   = isset($_POST['last_amount_c'])   ? (float)$_POST['last_amount_c']   : 0;
// $total_amount  = $first_amount + $second_amount + $last_amount;

$first_amount  = isset($_POST['first_amount_c'])  ? (float) $_POST['first_amount_c']  : 0;
$second_amount = isset($_POST['second_amount_c']) ? (float) $_POST['second_amount_c'] : 0;
$last_amount   = isset($_POST['last_amount_c'])   ? (float) $_POST['last_amount_c']   : 0;

$total_amount  = $first_amount + $second_amount + $last_amount;



$data = [
    '{{invoice_date}}'   => $_POST['invoice_date'] ?? date('d-M-Y'),
    '{{due_date}}'       => $_POST['due_date'] ?? date('d-M-Y', strtotime('+7 days')),
    '{{invoice_num}}'    => $_POST['invoice_num'] ?? 'INV-XXXX',
    '{{contact_name}}'   => $_POST['contact_name'] ?? 'N/A',
    '{{company_name}}'   => $_POST['company_name'] ?? 'N/A',
    '{{Address}}'        => $_POST['address'] ?? 'N/A',
    '{{country}}'        => $_POST['country'] ?? 'N/A',
    '{{Project_type}}'   => $_POST['project_type'] ?? 'Remote CRM development & Remote Content creation',
    '{{frequency}}'      => $_POST['frequency_c'] ?? 'N/A',

    '{{first_amount}}'   => number_format($first_amount, 2),
    '{{second_amount}}'  => number_format($second_amount, 2),
    '{{last_amount}}'    => number_format($last_amount, 2),
    '{{total_amount}}'   => number_format($total_amount, 2),
];





        try {
            // Step 1: Generate DOCX
            // $template = new TemplateProcessor('/web/html/outrightcrm.com/templates/document13kjl-final.docx');
            
            $template = new TemplateProcessor('/web/html/outrightcrm.com/templates/document-final-done-bykjl.docx');
            foreach ($data as $key => $value) {
                $template->setValue($key, $value);
            }

            // Create directories if they don't exist
            $generatedDocsDir = __DIR__ . '/generated_docs/';
            if (!is_dir($generatedDocsDir)) {
                mkdir($generatedDocsDir, 0755, true);
            }

            // Save DOCX file with unique name
            $baseFilename = 'Invoice_' . preg_replace('/\s+/', '_', $_POST['contact_name']) . '_' . time();
            $docxFilename = $baseFilename . '.docx';
            $docxPath = $generatedDocsDir . $docxFilename;
            $template->saveAs($docxPath);

            // Step 2: Convert DOCX to PDF
            // $apiSecret = 'ovwYoJoGmoZiiDpkakOhxgVGjnAgGARx'; // Your ConvertAPI secret
            $apiSecret = 'q7W4OBOLRhpg0vbSACudFCfgTxu0cYcr'; // Your ConvertAPI secret
            $conversionResponse = convertDocxToPdf($docxPath, $apiSecret);

            if (isset($conversionResponse['error'])) {
                $conversionError = $conversionResponse['error'];
                // Fallback to DOCX download if conversion fails
                $downloadLink = 'download.php?file=' . urlencode($docxPath) . '&type=docx';
            } else if (isset($conversionResponse['Files'][0]['Url'])) {
                // Step 3: Download PDF from ConvertAPI and save locally
                $pdfUrl = $conversionResponse['Files'][0]['Url'];
                $pdfFilename = $baseFilename . '.pdf';
                $pdfPath = $generatedDocsDir . $pdfFilename;

                if (downloadFileFromUrl($pdfUrl, $pdfPath)) {
                    // Success - provide PDF download
                    $downloadLink = 'download.php?file=' . urlencode($pdfPath) . '&type=pdf';
                    
                    // Optional: Clean up the temporary DOCX file
                    if (file_exists($docxPath)) {
                        unlink($docxPath);
                    }
                } else {
                    $conversionError = 'Failed to download converted PDF file.';
                    // Fallback to DOCX
                    $downloadLink = 'download.php?file=' . urlencode($docxPath) . '&type=docx';
                }
            } else {
                $conversionError = 'Unexpected response from ConvertAPI.';
                // Fallback to DOCX
                $downloadLink = 'download.php?file=' . urlencode($docxPath) . '&type=docx';
            }

        } catch (Exception $e) {
            $conversionError = 'Error generating invoice: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator Pro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(180deg); }
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .progress-bar {
            background: rgba(255, 255, 255, 0.2);
            height: 4px;
            margin-top: 30px;
            border-radius: 2px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .progress-fill {
            height: 100%;
            background: white;
            border-radius: 2px;
            transition: width 0.5s ease;
        }

        .content {
            padding: 40px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            gap: 20px;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
        }

        .step.active {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
            transform: scale(1.1);
        }

        .step.completed {
            background: #10b981;
            color: white;
        }

        .step.inactive {
            background: #e5e7eb;
            color: #9ca3af;
        }

        .step-connector {
            width: 60px;
            height: 2px;
            background: #e5e7eb;
            transition: all 0.3s ease;
        }

        .step-connector.completed {
            background: #10b981;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.9em;
        }

        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        input[type="text"]:focus, input[type="date"]:focus {
            outline: none;
            border-color: #4facfe;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            transform: translateY(-2px);
        }

        .search-container {
            position: relative;
        }

        .search-input {
            padding-left: 50px !important;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
        }

        .btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-generate {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            width: 100%;
            justify-content: center;
            font-size: 18px;
            padding: 18px;
        }

        .btn-download {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            font-size: 18px;
            padding: 18px 40px;
            margin: 0 auto;
            display: flex;
            animation: pulse 2s infinite;
        }

        .btn-download.pdf {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        .btn-download.docx {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(139, 92, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
        }

        .success-message {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            animation: successSlide 0.6s ease-out;
        }

        .warning-message {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            animation: successSlide 0.6s ease-out;
        }

        @keyframes successSlide {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .success-icon {
            font-size: 3em;
            margin-bottom: 15px;
            animation: checkmark 0.6s ease-out 0.3s both;
        }

        @keyframes checkmark {
            from {
                opacity: 0;
                transform: scale(0);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #4facfe;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.7);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2em;
            color: #4facfe;
            margin-bottom: 10px;
        }

        .back-btn {
            background: #6b7280;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: #4b5563;
            box-shadow: 0 5px 15px rgba(107, 114, 128, 0.3);
        }
        /* New styles for additional buttons and modal */
.button-group {
    display: flex;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn-new-customer {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.btn-video {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.8);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border-radius: 15px;
    width: 80%;
    max-width: 800px;
    box-shadow: 0 5px 30px rgba(0,0,0,0.3);
    animation: slideInModal 0.4s ease;
}

@keyframes slideInModal {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.close-modal {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.close-modal:hover {
    color: #000;
}


/* Recent customers dropdown styles */
.recent-customers-dropdown {
    width: 100%;
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 16px;
    background: #f9fafb;
    transition: all 0.3s ease;
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
}

.recent-customers-dropdown:focus {
    outline: none;
    border-color: #4facfe;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
}

.recent-customers-dropdown option {
    padding: 10px;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://www.outrightcrm.com/wp-content/uploads/2024/11/cropped-cropped-OutRightcrm-web-1.png" alt="">
            <h1><i class="fas fa-file-invoice"></i> Invoice Generator Pro</h1>
            <p>Create professional PDF invoices in seconds</p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= empty($customerData) && empty($downloadLink) ? '33%' : (!empty($customerData) && empty($downloadLink) ? '66%' : '100%') ?>"></div>
            </div>
        </div>

        <div class="content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?= empty($customerData) && empty($downloadLink) ? 'active' : 'completed' ?>">
                    <i class="fas fa-search"></i>
                </div>
                <div class="step-connector <?= !empty($customerData) || !empty($downloadLink) ? 'completed' : '' ?>"></div>
                <div class="step <?= !empty($customerData) && empty($downloadLink) ? 'active' : (!empty($downloadLink) ? 'completed' : 'inactive') ?>">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="step-connector <?= !empty($downloadLink) ? 'completed' : '' ?>"></div>
                <div class="step <?= !empty($downloadLink) ? 'active' : 'inactive' ?>">
                    <i class="fas fa-file-pdf"></i>
                </div>
            </div>

           <!-- STEP 1: Search form -->
<?php if (empty($customerData) && empty($downloadLink)): ?>
    <div class="form-container">
        <h2 style="text-align: center; color: #374151; margin-bottom: 30px;">
            <i class="fas fa-search" style="color: #4facfe;"></i> Find Customer
        </h2>
        
        <form method="post" onsubmit="showLoading()">
            <input type="hidden" name="step" value="search">
            <div class="form-group">
                <label for="name">Customer Name</label>
                <div class="search-container">
                    <i class="fas fa-user search-icon"></i>
                    <input type="text" id="name" name="name" class="search-input" placeholder="Enter customer name to search..." required>
                </div>
                <!-- Add this after the search input field but before the example list -->
<div class="form-group">
    <label for="recent-customers"><i class="fas fa-history"></i> Repeated Customers</label>
    <select id="recent-customers" class="recent-customers-dropdown" onchange="fillFromDropdown(this.value)">
        <option value="">Select a repeated customer</option>
        <?php foreach ($recentCustomers as $customer): ?>
            <option value="<?= htmlspecialchars($customer) ?>">
                <?= htmlspecialchars($customer) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                <!-- Example customers -->
                <!-- <div class="example-list">
                    <span onclick="fillCustomer('Customer 81599')">Customer 81599</span>
                    <span onclick="fillCustomer('Customer 88531')">Customer 88531</span>
                    <span onclick="fillCustomer('Customer 59297')">Customer 59297</span>
                </div> -->
            </div>
            
            <!-- <button type="submit" class="btn">
                <i class="fas fa-search"></i> Search Customer
            </button> -->
        </form>
<!-- Add this after the search button but before the feature grid -->
<div class="button-group">
    <button type="button" class="btn btn-new-customer" onclick="proceedAsNewCustomer()">
        <i class="fas fa-user-plus"></i> New Customer
    </button>
    
    <button type="button" class="btn btn-video" onclick="openVideoModal()">
        <i class="fas fa-video"></i> Watch Tutorial
    </button>
</div>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-file-pdf"></i></div>
                <h4>PDF Output</h4>
                <p>Professional PDF invoices ready to send</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h4>Lightning Fast</h4>
                <p>Generate invoices in seconds</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h4>Secure & Reliable</h4>
                <p>Your data is safe with us</p>
            </div>
        </div>
    </div>

    <script>
        function fillCustomer(name) {
            document.getElementById('name').value = name;
        }
    </script>

    <style>
        .example-list {
            margin-top: 8px;
        }
        .example-list span {
            cursor: pointer;
            background: #f3f4f6;
            padding: 5px 10px;
            border-radius: 12px;
            margin-right: 5px;
            font-size: 14px;
            color: #374151;
            display: inline-block;
        }
        .example-list span:hover {
            background: #4facfe;
            color: #fff;
        }
    </style>
<?php endif; ?>


            <!-- STEP 2: Editable form -->
            <?php if (!empty($customerData) && empty($downloadLink)): ?>
                <div class="form-container">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                        <h2 style="color: #374151;">
                            <i class="fas fa-edit" style="color: #10b981;"></i> Invoice Details
                        </h2>
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="step" value="search">
                            <button type="submit" class="btn back-btn">
                                <i class="fas fa-arrow-left"></i> Back to Search
                            </button>
                        </form>
                    </div>
                    
                    <form method="post" onsubmit="showLoading()">
                        <input type="hidden" name="step" value="generate">

                        <!-- <div class="form-row">
                            <div class="form-group">
                                <label for="invoice_date"><i class="fas fa-calendar"></i> Invoice Date</label>
                                <input type="date" id="invoice_date" name="invoice_date" value="<?
                                //= date('Y-m-d') ?>">
                            </div>
                            <div class="form-group">
                                <label for="due_date"><i class="fas fa-calendar-check"></i> Due Date</label>
                                <input type="date" id="due_date" name="due_date" value="<?
                                //= date('Y-m-d', strtotime('+7 days')) ?>">
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label for="invoice_num"><i class="fas fa-hashtag"></i> Invoice Number</label>
                            <input type="text" id="invoice_num" name="invoice_num" value="<?= htmlspecialchars($customerData['invoice_number_c'] ?? 'INV-' . date('Ymd') . '-' . rand(1000,9999)) ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_name"><i class="fas fa-user"></i> Contact Name</label>
                                <input type="text" id="contact_name" name="contact_name" value="<?= htmlspecialchars($customerData['name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="company_name"><i class="fas fa-building"></i> Company Name</label>
                                <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($customerData['company_name_c'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
                                <input type="text" id="address" name="address" value="<?= htmlspecialchars($customerData['address'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="country"><i class="fas fa-globe"></i> Country</label>
                                <input type="text" id="country" name="country" value="<?= htmlspecialchars($customerData['country_c'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="project_type"><i class="fas fa-project-diagram"></i> Project Type</label>
                            <input type="text" id="project_type" name="project_type" value="CRM Integration">
                        </div>




                        
                        <!-- add new kjl -->
       <div class="form-group">
    <label for="frequency_c">Frequency</label>
    <select id="frequency_c" name="frequency_c" class="form-control">
        <?php
        // $options = [
        //     'Weekly' => 'Weekly',
        //     'Monthly' => 'Monthly',
        //     'Annual' => 'Annual',
        //     'Random' => 'Random',
        //     'Not fix' => 'Not fix'
        // ];
        $options = [
    'April' => 'April',
    'May' => 'May',
    'June' => 'June',
    'July' => 'July',
    'August' => 'August',
    'September' => 'September',
    'October' => 'October',
    'November' => 'November',
    'December' => 'December',
    'January' => 'January',
    'February' => 'February',
    'March' => 'March'
];


        $selected = $customerData['frequency_c'] ?? '';

        foreach ($options as $value => $label) {
            $isSelected = ($selected === $value) ? 'selected' : '';
            echo "<option value=\"$value\" $isSelected>$label</option>";
        }
        ?>
    </select>
</div>



                            <div class="form-group">
                                   <label for="first_amount_c">First Amount</label>
                                 <input type="number" id="first_amount_c" name="first_amount_c" class="form-control" 
                                  value="<?= htmlspecialchars($customerData['first_amount_c'] ?? '') ?>">
                           </div>

                           <div class="form-group">
                                  <label for="second_amount_c">Second Amount</label>
                                  <input type="number" id="second_amount_c" name="second_amount_c" class="form-control" 
                                  value="<?= htmlspecialchars($customerData['second_amount_c'] ?? '') ?>">
                            </div>

                       <div class="form-group">
                               <label for="last_amount_c">Last Amount</label>
                          <input type="number" id="last_amount_c" name="last_amount_c" class="form-control" 
                            value="<?= htmlspecialchars($customerData['last_amount_c'] ?? '') ?>">
                         </div>



                         <style>
                            /* Container spacing */
.form-group {
    margin-bottom: 20px;
}

/* Labels */
.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #333;
}

/* Input fields */
.form-control {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #fff;
    transition: border-color 0.3s, box-shadow 0.3s;
}

/* Input focus effect */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    outline: none;
}

/* Dropdown select */
select.form-control {
    appearance: none;
    background: #fff url("data:image/svg+xml;utf8,<svg fill='black' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>") no-repeat right 12px center;
    background-size: 16px;
    padding-right: 35px;
    cursor: pointer;
}

/* Number input fields */
input[type="number"].form-control {
    -moz-appearance: textfield; /* hide arrows in Firefox */
}

input[type="number"].form-control::-webkit-outer-spin-button,
input[type="number"].form-control::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

                         </style>
                        <!-- end -->


                        <button type="submit" class="btn btn-generate">
                            <i class="fas fa-file-pdf"></i> Generate PDF Invoice
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- STEP 3: Download link -->
            <?php if ($downloadLink): ?>
                <?php if ($conversionError): ?>
                    <div class="warning-message">
                        <div style="font-size: 1.5em; margin-bottom: 10px;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>PDF conversion failed</h3>
                        <p style="margin: 10px 0; font-size: 0.9em;"><?= htmlspecialchars($conversionError) ?></p>
                        <p><strong>Your invoice has been generated as a DOCX file instead.</strong></p>
                    </div>
                <?php else: ?>
                    <div class="success-message">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 style="margin-bottom: 10px;">PDF Invoice Generated Successfully!</h2>
                        <p>Your professional PDF invoice is ready for download</p>
                    </div>
                <?php endif; ?>

                <div style="text-align: center;">
                    <?php 
                    $fileType = strpos($downloadLink, 'type=pdf') !== false ? 'pdf' : 'docx';
                    $iconClass = $fileType === 'pdf' ? 'fas fa-file-pdf' : 'fas fa-file-word';
                    $buttonText = $fileType === 'pdf' ? 'Download PDF Invoice' : 'Download DOCX Invoice';
                    ?>
                    
                    <a href="<?= $downloadLink ?>" target="_blank" class="btn btn-download <?= $fileType ?>">
                        <i class="<?= $iconClass ?>"></i> <?= $buttonText ?>
                    </a>
                    
                    <div style="margin-top: 30px;">
                        <form method="get" style="display: inline;">
                            <button type="submit" class="btn back-btn">
                                <i class="fas fa-plus"></i> Create Another Invoice
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Loading indicator -->
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing your request...</p>
                <?php if (!empty($customerData) && empty($downloadLink)): ?>
                    <p style="margin-top: 10px; font-size: 0.9em; opacity: 0.8;">Converting to PDF...</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        // Auto-format dates
        document.addEventListener('DOMContentLoaded', function() {
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.addEventListener('change', function() {
                    this.style.transform = 'scale(1.02)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            // Add smooth animations to form inputs
            const inputs = document.querySelectorAll('input[type="text"]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.style.borderColor = '#10b981';
                        this.style.backgroundColor = '#f0fdf4';
                    } else {
                        this.style.borderColor = '#e5e7eb';
                        this.style.backgroundColor = '#f9fafb';
                    }
                });
            });
        });


        function proceedAsNewCustomer() {
    // Create a hidden form to submit and proceed to the next step
    const form = document.createElement('form');
    form.method = 'post';
    form.action = '';
    
    const stepInput = document.createElement('input');
    stepInput.type = 'hidden';
    stepInput.name = 'new_customer';
    stepInput.value = 'true';
    
    form.appendChild(stepInput);
    document.body.appendChild(form);
    
    showLoading();
    form.submit();
}

function openVideoModal() {
    document.getElementById('videoModal').style.display = 'block';
}

function closeVideoModal() {
    document.getElementById('videoModal').style.display = 'none';
}

// Close modal when clicking outside the content
window.onclick = function(event) {
    const modal = document.getElementById('videoModal');
    if (event.target === modal) {
        closeVideoModal();
    }
}
function fillFromDropdown(customerName) {
    if (customerName) {
        document.getElementById('name').value = customerName;
        document.querySelector('button[type="submit"]').focus();
    }
}
    </script>
    <!-- Video Modal -->
<div id="videoModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeVideoModal()">&times;</span>
        <h2 style="text-align: center; margin-bottom: 20px;">
            <i class="fas fa-video"></i> Invoice Generator Tutorial
        </h2>
        <div class="video-container" style="text-align:center; padding:40px; background:#f9fafb; border:1px solid #ddd; border-radius:12px; max-width:500px; margin:auto; box-shadow:0 4px 10px rgba(0,0,0,0.08);">
  <!-- Decorative SVG Icon -->
  <div style="margin-bottom:20px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="none" viewBox="0 0 24 24" stroke="#0078d7" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" 
        d="M15.91 11.17a1.2 1.2 0 010 1.66l-5.1 5.1a1.2 1.2 0 01-2.05-.84V7.91a1.2 1.2 0 012.05-.84l5.1 5.1z" />
    </svg>
  </div>

  <!-- Title -->
  <h2 style="font-size:22px; margin-bottom:10px; color:#333; font-family:sans-serif;">Watch the Video</h2>
  <p style="font-size:16px; color:#666; margin-bottom:20px; font-family:sans-serif;">
    Click below to open the video in a new tab and watch it seamlessly.
  </p>

  <!-- Button -->
  <a href="https://outrightsers-my.sharepoint.com/:v:/g/personal/kamal_outrightsystems_org/EQJqw236VLZKihoFWvjIYiMB_-9hoK8UkP7nriZjLhEBYA?e=BKu8zE" 
     target="_blank" rel="noopener noreferrer" 
     style="display:inline-flex; align-items:center; gap:10px; padding:12px 20px; background:#0078d7; color:#fff; text-decoration:none; border-radius:30px; font-weight:bold; font-family:sans-serif; box-shadow:0 2px 5px rgba(0,0,0,0.2); transition:background 0.3s;">
    <!-- Play Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" viewBox="0 0 24 24">
      <path d="M8 5v14l11-7z"/>
    </svg>
    Open Video
  </a>
</div>

    </div>
</div>

<!-- Video Thumbnail (floating button) -->
<div class="video-thumbnail" onclick="openVideoModal()">
    <i class="fas fa-video" style="color: white; font-size: 20px;"></i>
</div>



<script>
function fillFromDropdown(customerName) {
    if (customerName) {
        // Fill the Customer Name input
        document.getElementById('name').value = customerName;

        // Find the closest form (the search form)
        const form = document.getElementById('name').closest('form');
        if (form) {
            // Show the loading spinner if you have it
            if (typeof showLoading === 'function') {
                showLoading();
            }

            // Submit the form automatically
            form.submit();
        }
    }
}
</script>





<!-- by kjl -->

</body>
</html>