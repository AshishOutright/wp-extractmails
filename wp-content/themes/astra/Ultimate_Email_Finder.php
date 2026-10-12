<?php
    /* Template Name:  Email Finder */





set_time_limit(900);

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

if (!$isAjax) {
    if (function_exists('addhead')) {
        addhead([
            '<meta name="description" content="Extract emails from any website or domain with our free email extraction tool.">',
            '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
            '<link rel="preconnect" href="https://fonts.googleapis.com">',
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">',
            '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">',
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">'
        ]);
    }

    get_header();
}

// -------------------- Helper Functions --------------------

function fetchHtml($url) {
    $context = stream_context_create([
        "http" => [
            "timeout" => 10,
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
        ]
    ]);
    return @file_get_contents($url, false, $context);
}

function extractEmailsFromUrl($url) {
    $html = fetchHtml($url);
    if (!$html) return [];

   $pattern = '/[a-z0-9._%+\-]+@[a-z0-9\-]+\.(com|org|net|edu|gov|mil|info|biz|io|co|us|uk|de|ca|fr|au|in|jp|nl|ru|ch|se|no|es|it|me|tv)/i';

    preg_match_all($pattern, $html, $matches);

    return array_unique($matches[0]);
}

function getLinksFromHtml($html, $domain) {
    $links = [];
    if (preg_match_all('/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']+)["\']/i', $html, $matches)) {
        foreach ($matches[1] as $link) {
            $parsed = parse_url($link);

            if (!isset($parsed["host"])) {
                $link = rtrim("https://" . $domain, "/") . "/" . ltrim($link, "/");
            }

            if (strpos($link, $domain) !== false) {
                $links[] = strtok($link, "#");
            }
        }
    }
    return array_unique($links);
}

function buildSitemapXml($urls) {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($urls as $url) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . htmlspecialchars($url) . '</loc>';
        $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';
    }

    $sitemap .= '</urlset>';
    return $sitemap;
}

// -------------------- Handle AJAX Request --------------------
$isAjax = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) &&
    strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $isAjax) {
    header("Content-Type: application/json");

    $inputType = $_POST["inputType"] ?? "domain";
    $inputValue = trim($_POST["inputValue"] ?? "");
    
    if (empty($inputValue)) {
        echo json_encode(["status" => "error", "message" => "❌ Please enter a valid domain or URL."]);
        exit;
    }

    $emailsByUrl = [];
    $links = [];

    if ($inputType === "domain") {
        // Process domain
        $domain = preg_replace('#^https?://#', '', rtrim($inputValue, '/'));
        $baseUrl = "https://" . $domain;
        $html = fetchHtml($baseUrl);

        if (!$html) {
            echo json_encode(["status" => "error", "message" => "❌ Unable to fetch homepage."]);
            exit;
        }

        $links = getLinksFromHtml($html, $domain);
        array_unshift($links, $baseUrl);
        $links = array_unique($links);

        $sitemapContent = buildSitemapXml($links);
        file_put_contents("sitemap.xml", $sitemapContent);

        foreach ($links as $link) {
            $emails = extractEmailsFromUrl($link);
            if (!empty($emails)) {
                $emailsByUrl[] = [
                    "url" => $link,
                    "emails" => array_values($emails)
                ];
            }
        }
    } else {
        // Process single URL
        $url = $inputValue;
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        
        $emails = extractEmailsFromUrl($url);
        if (!empty($emails)) {
            $emailsByUrl[] = [
                "url" => $url,
                "emails" => array_values($emails)
            ];
        }
        $links = [$url];
    }

    echo json_encode([
        "status" => "success",
        "message" => "✅ Email extraction successful!",
        "urls" => $links,
        "emails" => $emailsByUrl,
        "downloadUrl" => ($inputType === "domain") ? "sitemap.xml" : null
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Extractor - Find Emails by Domain or URL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }
        .nav-tabs .nav-link {
            border: none;
            padding: 12px 40px;
            font-weight: 500;
            color: #6c757d;
            border-radius: 8px 8px 0 0;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: #fff;
            border-bottom: 3px solid #0d6efd;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        .result-table {
            border-radius: 10px;
            overflow: hidden;
        }
        .result-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .search-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
        }

        .card-title{
            font-size:40px;
            font-weight:700;
        }


        .nav-tabs {
    display: flex;
    justify-content: space-between;
    gap: 10px; /* optional spacing between tabs */
}

.nav-tabs .nav-item {
    flex: 1;
    text-align: center;
}

        
    </style>
</head>
<body>
<div class="container mt-4 pt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class=" ">
                <div class=" pt-5  ">
                    <h1 class="card-title mb-4 text-center">Ultimate Email Finder</h1>
                    <p class="text-center text-muted mb-5">Quickly discover  email addresses from any website, domain, or bulk list.</p>
                    
                 
                    
                   <div class="tab-content search-section  card-body shadow-lg card mt-5" id="myTabContent">

                      <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="domain-tab" data-bs-toggle="tab" data-bs-target="#domain" type="button" role="tab" aria-controls="domain" aria-selected="true">By Domain</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="url-tab" data-bs-toggle="tab" data-bs-target="#url" type="button" role="tab" aria-controls="url" aria-selected="false">By URL</button>
                        </li>

                        <li class="nav-item" role="presentation">
                      <button class="nav-link" id="bulk-tab" data-bs-toggle="tab" data-bs-target="#bulk" type="button" role="tab" aria-controls="bulk" aria-selected="false">Bulk Domain</button>
                      </li>

                    </ul>
    <!-- Domain Form -->
    <div class="tab-pane fade show active" id="domain" role="tabpanel" aria-labelledby="domain-tab">
        <form id="domainForm">
            <input type="hidden" name="inputType" value="domain">
            <div class="row g-2 align-items-center">
                <div class="col-md-9">
                    <label for="domainInput" class="form-label">Enter Domain</label>
                    <input type="text" name="inputValue" id="domainInput" class="form-control form-control-lg" placeholder="   example.com" required>
                </div>
                <div class="col-md-3 d-grid">
                    <label class="form-label invisible">Search</label>
                    <button type="submit" class="btn btn-primary btn-lg">Search</button>
                </div>
            </div>
        </form>
    </div>

    <!-- URL Form -->
    <div class="tab-pane fade" id="url" role="tabpanel" aria-labelledby="url-tab">
        <form id="urlForm">
            <input type="hidden" name="inputType" value="url">
            <div class="row g-2 align-items-center">
                <div class="col-md-9">
                    <label for="urlInput" class="form-label">Enter URL</label>
                    <input type="text" name="inputValue" id="urlInput" class="form-control form-control-lg" placeholder="  https://www.example.com/contact" required>
                </div>
                <div class="col-md-3 d-grid">
                    <label class="form-label invisible">Search</label>
                    <button type="submit" class="btn btn-primary btn-lg">Search</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Domain Form -->
<div class="tab-pane fade" id="bulk" role="tabpanel" aria-labelledby="bulk-tab">
    <form id="bulkForm">
        <input type="hidden" name="inputType" value="bulk">
        <div class="mb-3">
            <label for="bulkInput" class="form-label">Enter up to 5 domains (one per line)</label>
            <textarea name="inputValue" id="bulkInput" class="form-control form-control-lg" rows="5" placeholder="example.com&#10;another.com" required></textarea>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Search</button>
        </div>
    </form>
</div>

</div>

                   <div class="mt-4" id="response"></div>

<div class="result-section shadow-lg card mt-5 p-3" style="display:none;">

    <h4 class="mb-4">Results</h4>

    <button class="download-btn btn btn-warning" style="display:none;">
   Download CSV
</button>

    <div class="table-responsive">
        <table class="table table-hover result-table">
            <thead>
               <tr>
  <th>Name</th>
  <th>Email</th>
  <th>Source</th>
  <th>Action</th>
</tr>

            </thead>
            <tbody id="emailTableBody">
            </tbody>
        </table>
    </div>

</div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    // Handle form submissions
   $('#domainForm, #urlForm, #bulkForm').on('submit', function (e) {
    e.preventDefault();
    $('#response').html('<div class="alert alert-info text-center">⏳ Processing...</div>');
    $('.result-section').hide();

    const form = $(this);
    const inputType = form.find('input[name="inputType"]').val();

    if (inputType === 'bulk') {
        const domains = form.find('textarea[name="inputValue"]').val().trim().split('\n').filter(Boolean);
        
        if (domains.length > 5) {
            $('#response').html('<div class="alert alert-warning text-center">⚠️ You can enter a maximum of 5 domains at a time.</div>');
            return;
        }

        // Process each domain one-by-one
        let allRequests = domains.map(domain => {
            return $.ajax({
                url: '',
                type: 'POST',
                data: {
                    inputType: 'domain',
                    inputValue: domain.trim()
                },
                dataType: 'json',
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });
        });

        Promise.all(allRequests).then(responses => {
            const tableBody = $('#emailTableBody');
            tableBody.empty();
            let seenEmails = new Set();
            let foundAny = false;

            responses.forEach(response => {
                if (response.status === 'success') {
                    response.emails.forEach(item => {
                        item.emails.forEach(email => {
                            const blacklist = [
                                'sendmail@example.com',
                                'example@example.com',
                                'abc@gmail.com',
                                'xyz@gmail.com',
                            ];

                            if (
                                !seenEmails.has(email) &&
                                !blacklist.includes(email.toLowerCase())
                            ) {
                                seenEmails.add(email);
                                foundAny = true;

                                const namePart = email.split('@')[0];
                                const name = namePart.split('.').map(part =>
                                    part.charAt(0).toUpperCase() + part.slice(1)
                                ).join(' ');
tableBody.append(
    '<tr>' +
        '<td>' + name + '</td>' +
        '<td>' + email + '</td>' +
        '<td><a href="' + item.url + '" target="_blank">' + item.url + '</a></td>' +
        '<td>' +
            '<button class="btn btn-sm btn-primary add-btn" ' +
            'data-name="' + name + '" ' +
            'data-email="' + email + '" ' +
            'data-source="' + item.url + '">' +
            'Add' +
            '</button>' +
        '</td>' +
    '</tr>'
);

                            }
                        });
                    });
                }
            });

            if (!foundAny) {
                tableBody.append('<tr><td colspan="3" class="text-center">No emails found.</td></tr>');
            }

            $('.result-section').show();
            $('#response').html('<div class="alert alert-success text-center">✅ Bulk email extraction complete!</div>');
        }).catch(() => {
            $('#response').html('<div class="alert alert-danger">❌ Error occurred during bulk processing.</div>');
        });

        return;
    }

    // For domain or url form (original functionality)
    $.ajax({
        url: '',
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        headers: { "X-Requested-With": "XMLHttpRequest" },
       success: function (response) {
    const tableBody = $('#emailTableBody');
    tableBody.empty();
    let seenEmails = new Set();
    let foundAny = false;

    if (response.status === 'success') {
        response.emails.forEach(item => {
            item.emails.forEach(email => {
                const blacklist = [
                    'sendmail@example.com',
                    'example@example.com',
                    'abc@gmail.com',
                    'xyz@gmail.com',
                ];

                if (
                    !seenEmails.has(email) &&
                    !blacklist.includes(email.toLowerCase())
                ) {
                    seenEmails.add(email);
                    foundAny = true;

                    const namePart = email.split('@')[0];
                    const name = namePart.split('.').map(part =>
                        part.charAt(0).toUpperCase() + part.slice(1)
                    ).join(' ');

                   tableBody.append(
    '<tr>' +
        '<td>' + name + '</td>' +
        '<td>' + email + '</td>' +
        '<td><a href="' + item.url + '" target="_blank">' + item.url + '</a></td>' +
        '<td>' +
            '<button class="btn btn-sm btn-primary add-btn" ' +
            'data-name="' + name + '" ' +
            'data-email="' + email + '" ' +
            'data-source="' + item.url + '">' +
            'Add' +
            '</button>' +
        '</td>' +
    '</tr>'
);

                }
            });
        });

        if (!foundAny) {
            tableBody.append('<tr><td colspan="3" class="text-center">No emails found.</td></tr>');
        }

        $('.result-section').show();
        $('#response').html('<div class="alert alert-success text-center">✅ Email extraction complete!</div>');

    } else {
        $('#response').html('<div class="alert alert-warning text-center">' + response.message + '</div>');
    }
},

        error: function () {
            $('#response').html('<div class="alert alert-danger">❌ An error occurred while processing your request.</div>');
        }
    });
});

});
</script>



<style>
/* Email Finder Section */


/* Header */
.emila_content-header {
  text-align: center;
  margin-bottom: 2rem;
}

.emila_content-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.emila_content-subtitle {
  font-size: 1rem;
  color: #475569;
  margin: 0;
}

/* Content blocks */
.emila_content-block {
  margin-bottom: 2rem;
}

.emila_content-block h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #0b1220;
  margin-bottom: 0.75rem;
}

.emila_content-block p,
.emila_content-block li {
  font-size: 0.95rem;
  color: #334155;
  line-height: 1.6;
}

/* Lists */
.emila_content-block ul,
.emila_content-block ol {
  padding-left: 1.25rem;
  margin: 0;
}

.emila_content-block li {
  margin-bottom: 0.5rem;
}

/* Responsive */
@media (max-width: 576px) {
  .emila_content-section {
    padding: 2rem 1rem;
  }
  .emila_content-title {
    font-size: 1.5rem;
  }
  .emila_content-block h4 {
    font-size: 1.05rem;
  }
}

</style>



<section class="emila_content-section  container mt-5">
<div class="emila_content-header">
<h2 class="emila_content-title">Email Finder</h2>
<p class="emila_content-subtitle">Learn what Email Finder is, how it works, and how it helps you save time.</p>
</div>


<div class="emila_content-block">
<h4>Q. What is Email Finder?</h4>
<p> Email Finder is a free-to-use tool that helps you find email addresses from domains, URLs, and bulk domains. Instead of spending time manually searching, the tool does it for you. Just enter the domain or URL and get results in seconds.</p>
</div>


<div class="emila_content-block">
<h4>Q. How does it work?</h4>
<ol>
<li>Enter the URL or Domain in the search bar.</li>
<li>If you have multiple domains, use bulk search (up to 5 domains per search).</li>
<li>Hit the <strong>Search</strong> button and get instant results.</li>
</ol>
</div>


<div class="emila_content-block">
<h4>Q. How does Email Finder help?</h4>
<ul>
<li>Saves your time by automating email search.</li>
<li>Works with single or multiple domains.</li>
<li>Provides organized search results that are easy to use.</li>
</ul>
</div>

</div>

</section>


<?php get_template_part('template-parts/hero'); ?>



</body>
</html>



<!-- 
<script>
$(document).on('click', '.add-btn', function () {

    const btn = $(this);
    const name = btn.data('name');
    const email = btn.data('email');
    const source = btn.data('source');

    console.log("Sending data to Google Sheet:");
    console.log("Name:", name);
    console.log("Email:", email);
    console.log("Source:", source);

    // Disable button while saving
    btn.prop('disabled', true).text('Saving...');

    // Build URL
    const url =
    `https://script.google.com/macros/s/AKfycbwitXMZY2mX6-e7AW-iaFraR1vdgW5VAKz3oVR-6hdtqKjfJAzrQ9CZasIULoGozZKb/exec` +
    `?name=${encodeURIComponent(name)}` +
    `&email=${encodeURIComponent(email)}` +
    `&source=${encodeURIComponent(source)}`;
    
    // Create hidden iframe
    const iframe = document.createElement('iframe');
    iframe.style.display = "none";
    iframe.src = url;

    // Remove iframe after it loads
    iframe.onload = () => {
        document.body.removeChild(iframe);
    };

    // Trigger request silently
    document.body.appendChild(iframe);

    // Update UI
    btn.text('Added')
       .removeClass('btn-primary')
       .addClass('btn-success')
       .prop('disabled', true);
});
</script> -->






<script>
let csvData = "Name,Email,Source\n";  // CSV headers

$(document).on('click', '.add-btn', function () {

    const btn = $(this);
    const name = btn.data('name');
    const email = btn.data('email');
    const source = btn.data('source');

    console.log("Adding to CSV:");
    console.log("Name:", name);
    console.log("Email:", email);
    console.log("Source:", source);

    // Disable button
    btn.prop('disabled', true)
       .text('Saving...')
       .removeClass('btn-primary')
       .addClass('btn-success');

    // Append row to CSV memory
    csvData += `${name},${email},${source}\n`;

    // Update UI
    btn.text('Added');

    // Show Download button
    $('.download-btn').show();
});


// ON DOWNLOAD CLICK
$(document).on('click', '.download-btn', function () {

    // Convert CSV to File
    const blob = new Blob([csvData], { type: 'text/csv' });

    // Download link
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "leads_data.csv";

    // Trigger download
    link.click();
});
</script>







<?php get_footer(); ?>