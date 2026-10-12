<?php
/*
Template Name: Extract URLs from Text
*/
get_header();

?>


<?php
$page = get_page_by_path('tools-template');

if ($page) {
    // Handle Gutenberg blocks correctly
    $content = apply_filters('the_content', $page->post_content);
    echo do_blocks($content);
} else {
    echo '<p>Tools Template page not found.</p>';
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extract Domains From Text</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
       .header_tool {
    background: linear-gradient(135deg, #0d0d0d 0%, #1a1a1a 100%);
    color: white;
     padding-top: 7rem;     /* Top */
    padding-right: 3rem;   /* Right */
    padding-bottom: 5rem;  /* Bottom */
    padding-left: 3rem;  
    margin-bottom: 2rem;

    /* animation part */
    background-size: 300% 300%;
    animation: gradientShift 8s ease infinite;
}

/* Gradient shifting animation */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
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
            margin-bottom: 2rem;
        }
        
        .section-header_tool {
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
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
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
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
            color: white;
        }
        
        .action-buttons {
            padding: 0.9rem 0.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            gap: 1rem;
        }
        
        .action-btn {
            flex: 1;
            padding: 0.75rem 1rem;
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
            cursor: pointer;
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
        .clear-btn { color: #dc3545; }
        
        .file-input {
            display: none;
        }
        
        .url-list {
            max-height: 400px;
            overflow-y: auto;
            padding: 1rem;
            background-color: #f8f9fa;
        }
        
        .url-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
            word-break: break-all;
        }
        
        .url-item:last-child {
            margin-bottom: 0;
        }
        
        .url-link {
            color: #0066cc;
            text-decoration: none;
            flex: 1;
        }
        
        .url-link:hover {
            text-decoration: underline;
            color: #0052a3;
        }
        
        .copy-single-btn {
            background: none;
            border: none;
            color: #6c757d;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin-left: 0.5rem;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .copy-single-btn:hover {
            background-color: #e9ecef;
            color: #495057;
        }
        
        .stats-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .processing {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .alert {
            margin: 1rem 1.5rem;
            border-radius: 8px;
        }
        
        .no-urls-message {
            text-align: center;
            color: #6c757d;
            padding: 2rem;
            font-style: italic;
        }
        
        .filter-section {
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .filter-btn {
            padding: 0.25rem 0.75rem;
            margin: 0.25rem;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 20px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        
        .drag-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(76, 175, 80, 0.9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border-radius: 12px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .drag-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        @media (max-width: 768px) {
            .action-buttons, .stats-container {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .action-btn {
                flex: none;
                width: 100%;
            }
            
            .stat-item {
                margin: 0.25rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="header_tool">
        <div class="main-container">
            <h1 class="mb-1">Extract Domains From  <span class="star-rating">⭐⭐⭐⭐</span></h1>
            <p class="mb-0 opacity-75">The tool can be used to extract domains from text and PDF files. <a href="#" class="text-white">More.</a></p>
        </div>
    </div>

    <div class="main-container">
        <div class="row g-4">
            <!-- Input Section -->
            <div class="col-lg-6">
                <div class="input-section" id="inputSection">
                    <div class="drag-overlay" id="dragOverlay">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                        <p>Drop your file here to upload</p>
                    </div>
                    <div class="section-header_tool d-flex justify-content-between align-items-center">
                        <span>Input</span>
                        <button type="button" class="load-file-btn" onclick="document.getElementById('fileInput').click()">
                            <i class="fas fa-upload me-2"></i>Load file
                        </button>
                        <input type="file" id="fileInput" class="file-input" accept=".txt,.pdf,.docx" onchange="handleFileUpload(this)">

                    </div>
                    <div class="textarea-container">
                        <textarea 
                            class="form-control" 
                            id="inputText" 
                            rows="15" 
                            placeholder="Please visit our website at https://www.example.com for more information.

You can also check out:
- Our blog: https://blog.example.com
- Support page: https://support.example.com/help
- GitHub repository: https://github.com/username/project

For questions, email us at contact@example.com or visit ftp://files.example.com/downloads/"
                            oninput="updateCharCount('inputText', 'inputCharCount')"
                        ></textarea>
                        <span class="char-count" id="inputCharCount">0 char(s)</span>
                    </div>
                    <div class="p-3">
                        <button type="button" class="submit-btn" onclick="extractDomains()">
                            <i class="fas fa-search me-2"></i>Extract Domains
                        </button>
                    </div>
                </div>
            </div>

            <!-- Output Section -->
            <div class="col-lg-6">
                <div class="output-section">
                    <div class="section-header_tool">
                        Output
                    </div>
                    
                    <!-- Statistics -->
                    <div class="stats-container" id="statsContainer" style="display: none;">
                        <div class="stat-item">
                            <div class="stat-number" id="totalDomains">0</div>
                            <div class="stat-label">Domains Found</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" id="uniqueDomains">0</div>
                            <div class="stat-label">Unique Domains</div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section" id="filterSection" style="display: none;">
                        <div class="mb-2">
                            <strong>Filter by type:</strong>
                        </div>
                        <div>
                            <button class="filter-btn active" data-filter="all" onclick="filterDomains('all')">All</button>
                            <button class="filter-btn" data-filter="http" onclick="filterDomains('http')">HTTP/HTTPS</button>
                            <button class="filter-btn" data-filter="ftp" onclick="filterDomains('ftp')">FTP</button>
                            <button class="filter-btn" data-filter="email" onclick="filterDomains('email')">Email</button>
                            <button class="filter-btn" data-filter="other" onclick="filterDomains('other')">Other</button>
                        </div>
                    </div>
                    
                    <!-- Domain List -->
                    <div class="url-list" id="domainList">
                        <div class="no-urls-message">
                            No domains found. Please enter text or upload a file to extract domains.
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons" id="actionButtons" style="display: none;">
                        <button type="button" class="action-btn copy-btn" onclick="copyAllDomains()">
                            <i class="fas fa-copy"></i>Copy all Domains
                        </button>
                        <button type="button" class="action-btn download-btn" onclick="downloadDomains()">
                            <i class="fas fa-download"></i>Download Domains
                        </button>
                        <!-- <button type="button" class="action-btn share-btn" onclick="copyShareableLink()">
                            <i class="fas fa-share"></i>Share results
                        </button> -->
                        <button type="button" class="action-btn clear-btn" onclick="clearResults()">
                            <i class="fas fa-trash"></i>Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>

    <script>
        // Set the worker source for PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let extractedDomains = [];
        let filteredDomains = [];

        function updateCharCount(textareaId, countId) {
            const textarea = document.getElementById(textareaId);
            const count = document.getElementById(countId);
            count.textContent = `${textarea.value.length} char(s)`;
        }

        function extractDomains() {
            const inputText = document.getElementById('inputText').value;
            
            if (!inputText.trim()) {
                showAlert('Please enter some text to process or upload a file.', 'warning');
                return;
            }

            // Show processing state
            const submitBtn = document.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Extracting Domains...';
            submitBtn.disabled = true;

            // Use PHP backend for processing
            fetch('extract_domains.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ text: inputText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    extractedDomains = data.domains;
                    filteredDomains = extractedDomains;
                    displayDomains();
                    updateStats(data.stats);
                    showAlert(`Found ${data.stats.totalDomains} domains in the text!`, 'success');
                } else {
                    showAlert('Error processing text: ' + data.error, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Fallback to client-side processing
                extractedDomains = extractDomainsClientSide(inputText);
                filteredDomains = extractedDomains;
                displayDomains();
                const stats = calculateStats(extractedDomains);
                updateStats(stats);
                showAlert(`Found ${stats.totalDomains} domains in the text!`, 'success');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function extractDomainsClientSide(text) {
    const urlRegex = /(?:(?:https?|ftp):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[A-Z0-9+&@#\/%=~_|$])/gi;
    const emailRegex = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/g;
    
    const domains = [];
    const seenDomains = new Set(); // Track seen domains to avoid duplicates
    let match;
    
    // Extract domains from URLs
    while ((match = urlRegex.exec(text)) !== null) {
        const domain = extractDomain(match[0]);
        if (domain && !seenDomains.has(domain)) {
            seenDomains.add(domain);
            domains.push({
                domain: domain,
                type: getDomainType(domain, match[0])
            });
        }
    }
    
    // Extract domains from emails
    while ((match = emailRegex.exec(text)) !== null) {
        const domain = match[0].split('@')[1];
        if (domain && !seenDomains.has(domain)) {
            seenDomains.add(domain);
            domains.push({
                domain: domain,
                type: 'email'
            });
        }
    }
    
    return domains;
}

        function extractDomain(url) {
            try {
                let domain = url;
                if (url.includes('@') && !url.includes('://')) {
                    return url.split('@')[1];
                }
                if (!url.includes('://')) {
                    domain = 'http://' + url;
                }
                const urlObj = new URL(domain);
                return urlObj.hostname.replace(/^www\./, '');
            } catch (e) {
                // If URL parsing fails, try to extract domain manually
                const match = url.match(/^(?:https?:\/\/)?(?:www\.)?([^\/:]+)/i);
                return match ? match[1] : null;
            }
        }

        function getDomainType(domain, originalUrl = '') {
            if (originalUrl.includes('@') && !originalUrl.includes('://')) return 'email';
            if (originalUrl.startsWith('ftp://') || originalUrl.startsWith('ftp.')) return 'ftp';
            if (originalUrl.startsWith('http://') || originalUrl.startsWith('https://') || originalUrl.startsWith('www.')) return 'http';
            return 'other';
        }

        function calculateStats(domains) {
            const uniqueDomains = [...new Set(domains.map(item => item.domain))];
            
            return {
                totalDomains: domains.length,
                uniqueDomains: uniqueDomains.length
            };
        }

        function updateStats(stats) {
            document.getElementById('totalDomains').textContent = stats.totalDomains;
            document.getElementById('uniqueDomains').textContent = stats.uniqueDomains;
            
            if (stats.totalDomains > 0) {
                document.getElementById('statsContainer').style.display = 'flex';
                document.getElementById('filterSection').style.display = 'block';
                document.getElementById('actionButtons').style.display = 'flex';
            }
        }

        function displayDomains() {
            const domainList = document.getElementById('domainList');
            
            if (filteredDomains.length === 0) {
                domainList.innerHTML = '<div class="no-urls-message">No domains found matching the current filter.</div>';
                return;
            }
            
            let html = '';
            filteredDomains.forEach((item, index) => {
                const icon = getDomainIcon(item.type);
                html += `
                    <div class="url-item" data-type="${item.type}">
                        <i class="${icon} me-2" style="color: ${getDomainColor(item.type)};"></i>
                        <span class="url-link">${item.domain}</span>
                        <button class="copy-single-btn" onclick="copySingleDomain('${item.domain}')" title="Copy this domain">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `;
            });
            
            domainList.innerHTML = html;
        }

        function getDomainIcon(type) {
            switch (type) {
                case 'http': return 'fas fa-globe';
                case 'ftp': return 'fas fa-server';
                case 'email': return 'fas fa-envelope';
                default: return 'fas fa-link';
            }
        }

        function getDomainColor(type) {
            switch (type) {
                case 'http': return '#0066cc';
                case 'ftp': return '#ff6b35';
                case 'email': return '#28a745';
                default: return '#6f42c1';
            }
        }

        function filterDomains(type) {
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-filter="${type}"]`).classList.add('active');
            
            // Filter domains
            if (type === 'all') {
                filteredDomains = extractedDomains;
            } else {
                filteredDomains = extractedDomains.filter(item => item.type === type);
            }
            
            displayDomains();
        }

        function handleFileUpload(input) {
            const file = input.files[0];
            if (!file) return;

            const fileType = file.type;
            const fileName = file.name;

            if (fileType === 'text/plain') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('inputText').value = e.target.result;
                    updateCharCount('inputText', 'inputCharCount');
                    showAlert(`Text file "${fileName}" loaded successfully!`, 'success');
                };
                reader.readAsText(file);
            } else if (fileType === 'application/pdf') {
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
                            showAlert(`PDF file "${fileName}" loaded and text extracted successfully!`, 'success');
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

                } else if (
    fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
    file.name.endsWith('.docx')
) {
    const reader = new FileReader();
    reader.onload = function(event) {
        const arrayBuffer = event.target.result;

        mammoth.extractRawText({ arrayBuffer: arrayBuffer })
            .then(function(result) {
                document.getElementById('inputText').value = result.value;
                updateCharCount('inputText', 'inputCharCount');
                showAlert(`DOCX file "${fileName}" loaded and text extracted successfully!`, 'success');
            })
            .catch(function(err) {
                console.error("Error extracting DOCX:", err);
                showAlert('Error reading DOCX file. Please try a different one.', 'danger');
            });
    };
    reader.readAsArrayBuffer(file);

                
            } else {
                showAlert('Please select a valid text (.txt) or PDF (.pdf) file.', 'danger');
            }
        }

        function copyAllDomains() {
            if (filteredDomains.length === 0) {
                showAlert('No domains to copy!', 'warning');
                return;
            }

            const domainText = filteredDomains.map(item => item.domain).join('\n');
            
            navigator.clipboard.writeText(domainText).then(function() {
                showAlert(`${filteredDomains.length} domains copied to clipboard!`, 'success');
            }).catch(function(err) {
                console.error('Error copying domains: ', err);
                showAlert('Error copying domains to clipboard!', 'danger');
            });
        }

        function copySingleDomain(domain) {
            navigator.clipboard.writeText(domain).then(function() {
                showAlert('Domain copied to clipboard!', 'success');
            }).catch(function(err) {
                console.error('Error copying domain: ', err);
                showAlert('Error copying domain!', 'danger');
            });
        }

        function downloadDomains() {
            if (filteredDomains.length === 0) {
                showAlert('No domains to download!', 'warning');
                return;
            }

            let content = `Extracted Domains (${filteredDomains.length} total)\n`;
            content += `Generated on: ${new Date().toLocaleString()}\n`;
            content += '='.repeat(50) + '\n\n';

            const groupedDomains = {};
            filteredDomains.forEach(item => {
                if (!groupedDomains[item.type]) {
                    groupedDomains[item.type] = [];
                }
                groupedDomains[item.type].push(item.domain);
            });

            Object.keys(groupedDomains).forEach(type => {
                content += `${type.toUpperCase()} Domains:\n`;
                groupedDomains[type].forEach(domain => {
                    content += `- ${domain}\n`;
                });
                content += '\n';
            });

            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `extracted_domains_${new Date().getTime()}.txt`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showAlert('Domains downloaded successfully!', 'success');
        }

        function clearResults() {
            extractedDomains = [];
            filteredDomains = [];
            document.getElementById('domainList').innerHTML = '<div class="no-urls-message">No domains found. Please enter text or upload a file to extract domains.</div>';
            document.getElementById('statsContainer').style.display = 'none';
            document.getElementById('filterSection').style.display = 'none';
            document.getElementById('actionButtons').style.display = 'none';
            
            // Reset filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('[data-filter="all"]').classList.add('active');
            
            showAlert('Results cleared!', 'info');
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

            // Auto-dismiss success and info alerts
            if (type === 'success' || type === 'info') {
                setTimeout(() => {
                    const alertElement = bootstrap.Alert.getOrCreateInstance(alert);
                    alertElement.close();
                }, 3000);
            }
        }

        // Drag and drop functionality
        function setupDragAndDrop() {
            const inputSection = document.getElementById('inputSection');
            const dragOverlay = document.getElementById('dragOverlay');
            
            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                inputSection.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });
            
            // Highlight drop area when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                inputSection.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                inputSection.addEventListener(eventName, unhighlight, false);
            });
            
            // Handle dropped files
            inputSection.addEventListener('drop', handleDrop, false);
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            function highlight() {
                dragOverlay.classList.add('active');
            }
            
            function unhighlight() {
                dragOverlay.classList.remove('active');
            }
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length) {
                    // Create a fake file input element to reuse the existing handler
                    const fileInput = document.getElementById('fileInput');
                    fileInput.files = files;
                    handleFileUpload(fileInput);
                }
                
                unhighlight();
            }
        }

        // Load domains from URL parameter if present AND process default input
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sharedDomains = urlParams.get('domains');
            
            // Set placeholder text
            const placeholderText = `Please visit our website at https://www.example.com for more information.

        You can also check out:
        - Our blog: https://blog.example.com


        For questions, email us at contact@example.com or visit ftp://files.example.com/downloads/`;
            
            document.getElementById('inputText').value = placeholderText;
            updateCharCount('inputText', 'inputCharCount');
            
            // If shared domains are provided in URL parameter, use them
            if (sharedDomains) {
                const domains = decodeURIComponent(sharedDomains).split('\n');
                extractedDomains = domains.map(domain => ({
                    domain: domain.trim(),
                    type: getDomainType(domain.trim())
                }));
                filteredDomains = extractedDomains;
                displayDomains();
                updateStats(calculateStats(extractedDomains));
            } else {
                // Extract domains from the default input text immediately
                extractedDomains = extractDomainsClientSide(placeholderText);
                filteredDomains = extractedDomains;
                displayDomains();
                const stats = calculateStats(extractedDomains);
                updateStats(stats);
            }
            
            // Set up drag and drop
            setupDragAndDrop();
        });
    </script>

    <?php get_template_part('template-parts/hero'); ?>
</body>
</html>

<?php get_footer() ?>