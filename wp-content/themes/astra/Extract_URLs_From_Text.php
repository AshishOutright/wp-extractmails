


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
    <title>Extract URLs From Text</title>
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
            <h1 class="mb-1">Extract URLs From  <span class="star-rating">⭐⭐⭐⭐</span></h1>
            <p class="mb-0 opacity-75">The tool can be used to extract URLs from text and PDF files. <a href="#" class="text-white">More.</a></p>
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
                        <button type="button" class="submit-btn" onclick="extractUrls()">
                            <i class="fas fa-search me-2"></i>Extract URLs
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
                            <div class="stat-number" id="totalUrls">0</div>
                            <div class="stat-label">URLs Found</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" id="uniqueUrls">0</div>
                            <div class="stat-label">Unique URLs</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number" id="totalDomains">0</div>
                            <div class="stat-label">Domains</div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section" id="filterSection" style="display: none;">
                        <div class="mb-2">
                            <strong>Filter by type:</strong>
                        </div>
                        <div>
                            <button class="filter-btn active" data-filter="all" onclick="filterUrls('all')">All</button>
                            <button class="filter-btn" data-filter="http" onclick="filterUrls('http')">HTTP/HTTPS</button>
                            <button class="filter-btn" data-filter="ftp" onclick="filterUrls('ftp')">FTP</button>
                            <button class="filter-btn" data-filter="email" onclick="filterUrls('email')">Email</button>
                            <button class="filter-btn" data-filter="other" onclick="filterUrls('other')">Other</button>
                        </div>
                    </div>
                    
                    <!-- URL List -->
                    <div class="url-list" id="urlList">
                        <div class="no-urls-message">
                            No URLs found. Please enter text or upload a file to extract URLs.
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons" id="actionButtons" style="display: none;">
                        <button type="button" class="action-btn copy-btn" onclick="copyAllUrls()">
                            <i class="fas fa-copy"></i>Copy all URLs
                        </button>
                        <button type="button" class="action-btn download-btn" onclick="downloadUrls()">
                            <i class="fas fa-download"></i>Download URLs
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

        let extractedUrls = [];
        let filteredUrls = [];

        function updateCharCount(textareaId, countId) {
            const textarea = document.getElementById(textareaId);
            const count = document.getElementById(countId);
            count.textContent = `${textarea.value.length} char(s)`;
        }

        function extractUrls() {
            const inputText = document.getElementById('inputText').value;
            
            if (!inputText.trim()) {
                showAlert('Please enter some text to process or upload a file.', 'warning');
                return;
            }

            // Show processing state
            const submitBtn = document.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Extracting URLs...';
            submitBtn.disabled = true;

            // Use PHP backend for processing
            fetch('extract_urls.php', {
                method: 'POST',
                header_tools: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ text: inputText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    extractedUrls = data.urls;
                    filteredUrls = extractedUrls;
                    displayUrls();
                    updateStats(data.stats);
                    showAlert(`Found ${data.stats.totalUrls} URLs in the text!`, 'success');
                } else {
                    showAlert('Error processing text: ' + data.error, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Fallback to client-side processing
                extractedUrls = extractUrlsClientSide(inputText);
                filteredUrls = extractedUrls;
                displayUrls();
                const stats = calculateStats(extractedUrls);
                updateStats(stats);
                showAlert(`Found ${stats.totalUrls} URLs in the text!`, 'success');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function extractUrlsClientSide(text) {
            const urlRegex = /(?:(?:https?|ftp):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[A-Z0-9+&@#\/%=~_|$])/gi;
            const emailRegex = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/g;
            
            const urls = [];
            let match;
            
            // Extract URLs
            while ((match = urlRegex.exec(text)) !== null) {
                urls.push({
                    url: match[0],
                    type: getUrlType(match[0]),
                    domain: extractDomain(match[0])
                });
            }
            
            // Extract emails
            while ((match = emailRegex.exec(text)) !== null) {
                urls.push({
                    url: match[0],
                    type: 'email',
                    domain: match[0].split('@')[1]
                });
            }
            
            return urls;
        }

        function getUrlType(url) {
            if (url.includes('@') && !url.includes('://')) return 'email';
            if (url.startsWith('ftp://') || url.startsWith('ftp.')) return 'ftp';
            if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('www.')) return 'http';
            return 'other';
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
                return urlObj.hostname;
            } catch (e) {
                return url.split('/')[0].replace(/^(https?:\/\/|ftp:\/\/|www\.)/i, '');
            }
        }

        function calculateStats(urls) {
            const uniqueUrls = [...new Set(urls.map(item => item.url))];
            const uniqueDomains = [...new Set(urls.map(item => item.domain))];
            
            return {
                totalUrls: urls.length,
                uniqueUrls: uniqueUrls.length,
                totalDomains: uniqueDomains.length
            };
        }

        function updateStats(stats) {
            document.getElementById('totalUrls').textContent = stats.totalUrls;
            document.getElementById('uniqueUrls').textContent = stats.uniqueUrls;
            document.getElementById('totalDomains').textContent = stats.totalDomains;
            
            if (stats.totalUrls > 0) {
                document.getElementById('statsContainer').style.display = 'flex';
                document.getElementById('filterSection').style.display = 'block';
                document.getElementById('actionButtons').style.display = 'flex';
            }
        }

        function displayUrls() {
            const urlList = document.getElementById('urlList');
            
            if (filteredUrls.length === 0) {
                urlList.innerHTML = '<div class="no-urls-message">No URLs found matching the current filter.</div>';
                return;
            }
            
            let html = '';
            filteredUrls.forEach((item, index) => {
                const icon = getUrlIcon(item.type);
                html += `
                    <div class="url-item" data-type="${item.type}">
                        <i class="${icon} me-2" style="color: ${getUrlColor(item.type)};"></i>
                        <a href="${item.url}" target="_blank" class="url-link">${item.url}</a>
                        <button class="copy-single-btn" onclick="copySingleUrl('${item.url}')" title="Copy this URL">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `;
            });
            
            urlList.innerHTML = html;
        }

        function getUrlIcon(type) {
            switch (type) {
                case 'http': return 'fas fa-globe';
                case 'ftp': return 'fas fa-server';
                case 'email': return 'fas fa-envelope';
                default: return 'fas fa-link';
            }
        }

        function getUrlColor(type) {
            switch (type) {
                case 'http': return '#0066cc';
                case 'ftp': return '#ff6b35';
                case 'email': return '#28a745';
                default: return '#6f42c1';
            }
        }

        function filterUrls(type) {
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-filter="${type}"]`).classList.add('active');
            
            // Filter URLs
            if (type === 'all') {
                filteredUrls = extractedUrls;
            } else {
                filteredUrls = extractedUrls.filter(item => item.type === type);
            }
            
            displayUrls();
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

        function copyAllUrls() {
            if (filteredUrls.length === 0) {
                showAlert('No URLs to copy!', 'warning');
                return;
            }

            const urlText = filteredUrls.map(item => item.url).join('\n');
            
            navigator.clipboard.writeText(urlText).then(function() {
                showAlert(`${filteredUrls.length} URLs copied to clipboard!`, 'success');
            }).catch(function(err) {
                console.error('Error copying URLs: ', err);
                showAlert('Error copying URLs to clipboard!', 'danger');
            });
        }

        function copySingleUrl(url) {
            navigator.clipboard.writeText(url).then(function() {
                showAlert('URL copied to clipboard!', 'success');
            }).catch(function(err) {
                console.error('Error copying URL: ', err);
                showAlert('Error copying URL!', 'danger');
            });
        }

        function downloadUrls() {
            if (filteredUrls.length === 0) {
                showAlert('No URLs to download!', 'warning');
                return;
            }

            let content = `Extracted URLs (${filteredUrls.length} total)\n`;
            content += `Generated on: ${new Date().toLocaleString()}\n`;
            content += '='.repeat(50) + '\n\n';

            const groupedUrls = {};
            filteredUrls.forEach(item => {
                if (!groupedUrls[item.type]) {
                    groupedUrls[item.type] = [];
                }
                groupedUrls[item.type].push(item.url);
            });

            Object.keys(groupedUrls).forEach(type => {
                content += `${type.toUpperCase()} URLs:\n`;
                groupedUrls[type].forEach(url => {
                    content += `- ${url}\n`;
                });
                content += '\n';
            });

            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `extracted_urls_${new Date().getTime()}.txt`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showAlert('URLs downloaded successfully!', 'success');
        }

        // function copyShareableLink() {
        //     if (filteredUrls.length === 0) {
        //         showAlert('No URLs to share!', 'warning');
        //         return;
        //     }

        //     const urlText = encodeURIComponent(filteredUrls.map(item => item.url).join('\n'));
        //     const shareableLink = `${window.location.origin}${window.location.pathname}?urls=${urlText}`;
            
        //     navigator.clipboard.writeText(shareableLink).then(function() {
        //         showAlert('Shareable link copied to clipboard!', 'success');
        //     }).catch(function(err) {
        //         console.error('Error copying link: ', err);
        //         showAlert('Error creating shareable link!', 'danger');
        //     });
        // }

        function clearResults() {
            extractedUrls = [];
            filteredUrls = [];
            document.getElementById('urlList').innerHTML = '<div class="no-urls-message">No URLs found. Please enter text or upload a file to extract URLs.</div>';
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

       // ... (previous JavaScript code remains the same until the end)
// ... (previous JavaScript code remains the same until the end)
// ... (previous JavaScript code remains the same until the end)

// Load URLs from URL parameter if present AND process default input
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const sharedUrls = urlParams.get('urls');
    
    // Set placeholder text
    const placeholderText = `Please visit our website at https://www.example.com for more information.

You can also check out:
- Our blog: https://blog.example.com


For questions, email us at contact@example.com or visit ftp://files.example.com/downloads/`;
    
    document.getElementById('inputText').value = placeholderText;
    updateCharCount('inputText', 'inputCharCount');
    
    // If shared URLs are provided in URL parameter, use them
    if (sharedUrls) {
        const urls = decodeURIComponent(sharedUrls).split('\n');
        extractedUrls = urls.map(url => ({
            url: url.trim(),
            type: getUrlType(url.trim()),
            domain: extractDomain(url.trim())
        }));
        filteredUrls = extractedUrls;
        displayUrls();
        updateStats(calculateStats(extractedUrls));
    } else {
        // Extract URLs from the default input text immediately
        extractedUrls = extractUrlsClientSide(placeholderText);
        filteredUrls = extractedUrls;
        displayUrls();
        const stats = calculateStats(extractedUrls);
        updateStats(stats);
    }
    
    // Set up drag and drop
    setupDragAndDrop();
});
    </script>

    <style>

        /* Extract URLs Section */


/* Title block */
.titel_extract {
  text-align: center;
  margin-bottom: 2rem;
}

.titel_extract h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.titel_extract p {
  font-size: 1rem;
  color: #475569;
  margin: 0;
}

/* FAQ blocks */
.faq_extract,
section > .container > div {
  margin-bottom: 2rem;
}

.faq_extract h2,
section h2 {
  font-size: 1.2rem;
  font-weight: 600;
  color: #0b1220;
  margin-bottom: 0.75rem;
}

.faq_extract p,
section p,
section li {
  font-size: 0.95rem;
  color: #334155;
  line-height: 1.6;
}

section ul {
  padding-left: 1.25rem;
  margin: 0;
}

section ul li {
  margin-bottom: 0.5rem;
}

/* Responsive tweaks */
@media (max-width: 576px) {
  section {
    padding: 2rem 1rem;
  }
  .titel_extract h2 {
    font-size: 1.5rem;
  }
  .faq_extract h2,
  section h2 {
    font-size: 1.05rem;
  }
}





    </style>
    <section>
        <div class="container mt-5">
            <div class="titel_extract">
                <h2>Extract URLs: </h2>
                <p>Learn what Extract URLs  is, how it works, and how it helps you save time.</p>
            </div>
            <div class="faq_extract">
                <h2>Q. What is Extract URLs?  </h2>
                <p>Extract URLs is a free tool that helps you find all links from text or any uploaded file in seconds. Just paste text or upload your file and get all links in it instantly.  </p>
            </div>
            <div>
                <h2>Q. How does Extract URLs work?  </h2>
                <ul>
                    <li>Paste your text in the input box or upload your file</li>
                    <li>Click the Extract URLs button.</li>
                    <li>View all the URLs, presented neatly, filtered by type (HTML, HTTPS, Email, FTP, and others), and unique URLs.  </li>
                    <li>Copy all URLs or download the file.  </li>
                </ul>
            </div>

             <div>
                <h2>Q. How does Extract URLs help?   </h2>
                <ul>
                    <li>Saves time by detecting links in large texts and files. </li>
                    <li>Categorizes results by type for better clarity.  </li>
                    <li>Multiple export options: copy or download.    </li>
                    
                </ul>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/hero'); ?>
</body>
</html>







<?Php get_footer() ?>