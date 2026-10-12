
<?php
/*
Template Name: Home-page
*/

// POST handler.
function ee_fetch_url($url)
{
    $body = '';
    $resp = wp_remote_get($url, [
        'timeout'    => 15,
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'sslverify'  => false,
        'headers'    => [
            'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Encoding' => 'gzip, deflate',
            'Connection'      => 'keep-alive',
        ],
    ]);

    if (! is_wp_error($resp)) {
        $code = wp_remote_retrieve_response_code($resp);
        if ($code >= 200 && $code < 400) {
            $body = wp_remote_retrieve_body($resp);
        }
    }

    if (empty($body) && function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/122 Safari/537.36',
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
            ],
        ]);
        $curl_body = curl_exec($ch);
        $curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curl_body && $curl_code >= 200 && $curl_code < 400) {
            $body = $curl_body;
        }
    }

    return $body;
}

function ee_extract_emails($body)
{
    $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $body = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($m) {
        return mb_chr(hexdec($m[1]), 'UTF-8');
    }, $body);

    preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $body, $m);
    return array_map('strtolower', $m[0]);
}

function ee_extract_links($body, $base_url)
{
    $parsed = parse_url($base_url);
    $scheme = isset($parsed['scheme']) ? $parsed['scheme'] : 'https';
    $host   = isset($parsed['host']) ? $parsed['host'] : '';
    $origin = $scheme . '://' . $host;

    $links = [];

    if (preg_match_all('/href=["\']([^"\'#]+)/i', $body, $matches)) {
        foreach ($matches[1] as $link) {
            $link = trim($link);

            if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|webp|ico|pdf|zip|mp4|mp3|woff|woff2|ttf|eot|xml|rss)(\?|$)/i', $link)) {
                continue;
            }

            if (preg_match('/^(mailto:|tel:|javascript:|#)/i', $link)) {
                continue;
            }

            if (strpos($link, '//') === 0) {
                $link = $scheme . ':' . $link;
            } elseif (strpos($link, '/') === 0) {
                $link = $origin . $link;
            } elseif (! preg_match('#^https?://#i', $link)) {
                $link = rtrim($base_url, '/') . '/' . $link;
            }

            $link_parsed = parse_url($link);
            $link_host   = isset($link_parsed['host']) ? $link_parsed['host'] : '';

            if (strcasecmp($link_host, $host) === 0) {
                $links[] = $link;
            }
        }
    }

    return array_unique($links);
}

function ee_filter_emails($emails)
{
    $bad_ext = ['.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp', '.css', '.js', '.woff', '.woff2', '.ttf', '.eot', '.ico', '.mp4', '.mp3', '.pdf'];

    return array_values(array_filter($emails, function ($e) use ($bad_ext) {
        foreach ($bad_ext as $ext) {
            if (substr($e, -strlen($ext)) === $ext) {
                return false;
            }
        }

        $parts = explode('@', $e);
        if (! isset($parts[1]) || strpos($parts[1], '.') === false) {
            return false;
        }

        if (in_array($e, ['email@example.com', 'user@example.com', 'test@test.com', 'name@example.com'], true)) {
            return false;
        }

        return true;
    }));
}

if (isset($_POST['ee_nonce']) && wp_verify_nonce($_POST['ee_nonce'], 'ee_extract')) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    $raw_url   = isset($_POST['url']) ? trim(wp_strip_all_tags($_POST['url'])) : '';
    $max_pages = 500;

    if (empty($raw_url)) {
        echo wp_json_encode(['ok' => false, 'msg' => 'Please enter a URL.']);
        exit;
    }

    if (! preg_match('#^https?://#i', $raw_url)) {
        $raw_url = 'https://' . $raw_url;
    }

    if (! filter_var($raw_url, FILTER_VALIDATE_URL)) {
        echo wp_json_encode(['ok' => false, 'msg' => 'That does not look like a valid URL. Try: https://example.com']);
        exit;
    }

    $body = ee_fetch_url($raw_url);
    if (empty($body)) {
        echo wp_json_encode(['ok' => false, 'msg' => 'Could not fetch that URL. The site may be blocking automated requests (Cloudflare, login wall, etc.).']);
        exit;
    }

    $all_emails = ee_extract_emails($body);
    $crawled    = [$raw_url => true];
    $pages_done = 1;

    $queue = ee_extract_links($body, $raw_url);
    shuffle($queue);

    foreach ($queue as $link) {
        if ($pages_done >= $max_pages) {
            break;
        }

        if (isset($crawled[$link])) {
            continue;
        }

        $crawled[$link] = true;
        $page_body       = ee_fetch_url($link);

        if (! empty($page_body)) {
            $all_emails = array_merge($all_emails, ee_extract_emails($page_body));
            $pages_done++;
        }
    }

    $emails = array_values(array_unique($all_emails));
    $emails = ee_filter_emails($emails);

    echo wp_json_encode([
        'ok'            => true,
        'emails'        => $emails,
        'count'         => count($emails),
        'pages_scanned' => $pages_done,
    ]);
    exit;
}

get_header();
$nonce           = wp_create_nonce('ee_extract');
$app_padding_top = is_admin_bar_showing() ? 'calc(5rem + 32px)' : '5rem';
?>




<section class=" max-w-[1900px] mx-auto relative overflow-hidden py-40 px-4">

  <!-- MAIN GRADIENT BACKGROUND -->
 <!-- OVAL / RADIAL GRADIENT BACKGROUND -->
<!-- CUSTOM BACKGROUND (FROM YOUR CSS DESIGN) -->
<div class="absolute inset-0 pointer-events-none overflow-hidden">

  <!-- TOP LEFT BLUE -->
  <div class="absolute left-10 md:left-20 top-[-80px] md:top-[-100px] 
              w-[60vw] md:w-[500px] lg:w-[850px] 
              h-[250px] md:h-[320px] lg:h-[390px] 
              bg-[#b1b2ff] blur-[90px] md:blur-[105px] rounded-full">
  </div>

  <!-- TOP RIGHT BLUE -->
  <div class="absolute right-0 top-[-80px] md:top-[-100px] 
              w-[60vw] md:w-[500px] lg:w-[850px] 
              h-[220px] md:h-[260px] lg:h-[300px] 
              bg-[#b1b2ff] blur-[90px] md:blur-[105px] rounded-full">
  </div>

  <!-- LEFT SMALL -->
  <div class="absolute left-[-120px] md:left-[-180px] top-[60px] md:top-[80px] 
              w-[140px] md:w-[200px] lg:w-[240px] 
              h-[180px] md:h-[240px] lg:h-[300px] 
              bg-[#b1b2ff] blur-[60px] md:blur-[75px] rounded-full">
  </div>

  <!-- BOTTOM LEFT GREEN -->
  <div class="absolute left-[-60px] md:left-[-80px] bottom-[-40px] md:bottom-[-50px] 
              w-[65vw] md:w-[600px] lg:w-[890px] 
              h-[240px] md:h-[300px] lg:h-[360px] 
              bg-[#dfffea] blur-[90px] md:blur-[105px] rounded-full">
  </div>

  <!-- BOTTOM RIGHT GREEN -->
  <div class="absolute right-0 bottom-[-80px] md:bottom-[-100px] 
              w-[65vw] md:w-[600px] lg:w-[880px] 
              h-[260px] md:h-[340px] lg:h-[420px] 
              bg-[#dfffea] blur-[90px] md:blur-[105px] rounded-full rotate-[-10deg]">
  </div>

</div>

  <!-- SOFT RADIAL BLOBS -->
  



  <!-- FLOATING ICONS -->
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-66-3.png" alt="icons"
       class="absolute top-20 left-[45%] w-20 -rotate-[30deg]  hidden lg:block">

  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-66-3.png" alt="icons"
       class="absolute bottom-20  lg:left-[15%] md:left-20 w-20 rotate-[30deg]  hidden lg:block">

  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-66-3.png" alt="icons"
       class="absolute bottom-24 left-[45%] w-24 rotate-[30deg] hidden lg:block " >

  <!-- CONTENT -->
  <div class="relative max-w-[1400px] mx-auto px-0 grid md:grid-cols-[1.2fr_0.8fr] gap-4 items-center">

    <!-- LEFT SIDE -->
   <div class="relative inline-block">

  <!-- WHITE OVAL GRADIENT BACKGROUND -->
  <!-- <div class="absolute top-6 left-1/2 -translate-x-1/2 
              w-[820px] h-[320px] 
              bg-gradient-to-b from-white/100 to-white/70 
              blur-[62px] rounded-full shadow-md">
  </div> -->

  <!-- CONTENT -->
  <div class="relative z-10 ">

    <!-- USERS BADGE -->
    <div class="flex items-center  gap-0 mb-4">
      <div class="flex -space-x-3">
        <img class="w-9 h-9 rounded-full border-2 border-white"
             src="https://www.wp-1click.com/wp-content/uploads/2026/01/Frame-10.png" alt="icons">
        <img class="w-9 h-9 rounded-full border-2 border-white"
             src="https://www.wp-1click.com/wp-content/uploads/2026/01/Frame-11.png" alt="icons">
        <img class="w-9 h-9 rounded-full border-2 border-white"
             src="https://www.wp-1click.com/wp-content/uploads/2026/01/Frame-10.png" alt="icons">
      </div>

      <!-- <span class="text-xs font-medium px-3 py-1 rounded-full 
            bg-white shadow">
        HUNDRED PLUS USERS
      </span> -->
      <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-29-3.png" alt="" class="w-[200px] h-[30px] ">
    </div>

    <!-- HEADING -->
    <h1 class="text-3xl md:text-4xl font-bold leading-tight text-gray-900">
  

  <span class=" bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent inline-flex items-center gap-2">
    
    Powerful & Smart  Email Extraction
    
    <!-- INLINE ICON -->
   
  </span>

  <br> Platform <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-35-3.png"
         alt="icons"
         class="w-20  mt-8 object-contain inline-block align-middle">
</h1>

    <!-- SUBTEXT -->
    <p class="mt-6 text-gray-600 text-lg max-w-xl">
      An AI-powered tool that lets you extract unstructured data from emails effortlessly and convert it into structured insights in your database directly.  
    </p>

    <!-- BUTTONS -->
    <div class="mt-8 flex  gap-4">
    <a href="#"
   onclick="scrollToSection(event)"
   class="text-[13px] md:text-[20px] justify-center content-center px-5 md:px-8 py-2 md:py-3 bg-gradient-to-r from-[#090a6b] to-[#5a6bff] text-white rounded-lg shadow hover:text-white">
  Start Extracting Emails Now-Free
</a>

     
    </div>

  </div>
</div>

    <!-- RIGHT IMAGE -->
    <div class="relative">

      <div class="  max-w-[720px] text-end">
        <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-50-1.png" alt="Home"
             class="w-full ">
      </div>

    </div>

  </div>
</section>





<section>
  <div class="max-w-[1430px] mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">

    <div>
      <h2 class=" text-[34px] font-semibold   bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent py-8  reveal-up">Worldwide Teams Trust ExtractMails </h2>
      <p class="text-[17px] font-base text-justify reveal-up">
       Professionals across various industries rely on ExtractMails to convert unstructured data into structured insights in their preferred database. It caters to diverse teams such as recruitment, marketing, sales, software development, and customer support. Our platform is purpose-built to cater to real-world workflows and deliver reliable results consistently.  

Teams rely on ExtractMails because it minimizes manual work, enhances accuracy of data, and improves overall productivity. Irrespective of whether you are a freelancer handling
numerous leads or an evolving business growing your outreach, ExtractMails provides you with an exact solution as per your needs.  

We can help you provide a smooth user experience powered by reliable performance and robust technology.  
      </p>

    </div>
    <div>
      <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Mar-30-2026-03_46_58-PM-1.png" alt="Teams">
    </div>
  </div>
</section>



<style>
  html {
  scroll-behavior: smooth;
}
</style>










<div   id="contact-form" id="ee-app" class="relative overflow-hidden max-w-[1400px] mx-auto overflow-hidden  text-slate-900  reveal-up "
    >


    
<div class="absolute w-[687px] h-[272px] left-[24px] top-[425px] bg-[#b1c3ff] blur-[147px]"></div>



<div class="absolute w-[372px] h-[175px] left-[225px] top-[237px] bg-[#b1c3ff] blur-[147px]"></div>

<div class="absolute w-[715.56px] h-[161.15px] left-[658.32px] top-[390.31px] bg-[#98b0ff] blur-[147px] origin-top-left rotate-[-18.579deg]"></div>



<div class="absolute w-[260.74px] h-[54.57px] left-[969px] top-[-8.93px] bg-[#98b0ff]/40 blur-[47.7px] origin-top-left rotate-[-18.579deg]"></div>

<div class="absolute w-[525.08px] h-[82.15px] left-[985.53px] top-[666.46px] bg-[#00c979] blur-[107px] origin-top-left rotate-[-9.961deg]"></div>



<div class="absolute w-[153.89px] h-[55.97px] left-[-56px] top-[847.02px] bg-[#00c979] blur-[81px] origin-top-left rotate-[-9.961deg]"></div>

<div class="absolute w-[519.35px] h-[94.17px] left-[-219.02px] top-[-118.97px] bg-[#00c979] blur-[147px] origin-top-left rotate-[48.581deg]"></div>


    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap"
        rel="stylesheet" />

    <main  id="extractmail-gmail-generator-section" class="mb-14 bg-white font-body text-ink   ">

        <section  class="mx-auto max-w-[1440px] px-4 sm:px-6 relative" >
            <div id="ee-card"
                class="mx-auto mt-14 w-full max-w-[1296px] overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="p-6 sm:p-8">
                    <form  id="ee-form" class="space-y-4">
                        <label id="ee-label" for="ee-url" class="block text-sm font-semibold text-slate-600">
                            Enter website URL:
                        </label>

                        <style>
                            @media (min-width: 640px) {

                                #ee-app #ee-url,
                                #ee-app #ee-btn {
                                    border: none !important;
                                    border-radius: 0 !important;
                                }
                            }
                        </style>
                        <div id="ee-row" 
                            class="flex flex-col sm:flex-row sm:items-stretch gap-3 sm:gap-0 sm:overflow-hidden sm:rounded-md sm:border sm:border-slate-300 sm:bg-white sm:focus-within:border-blue-500 sm:focus-within:ring-1 sm:focus-within:ring-blue-500">
                            <input id="ee-url" type="url" placeholder="https://example.com" autocomplete="off"
                                spellcheck="false"
                                class="block h-[48px] sm:h-[46px] w-full flex-1 rounded-md border border-slate-300 bg-white px-4 text-[18px] text-[#14224d] placeholder-slate-400 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:focus:ring-0"
                                style="appearance: none;" />
                            <button  id="ee-btn" type="submit"
                                class="inline-flex h-[48px] sm:h-[46px] items-center justify-center gap-2 rounded-md bg-[#2563eb] px-6 text-[16px] font-bold uppercase tracking-[0.16em] text-white transition hover:bg-blue-700 sm:min-w-[210px]"
                                style="cursor:pointer; outline: none; border: none;">
                                <i data-lucide="search" id="ee-btn-icon" class="h-4 w-4"></i>
                                <span id="ee-btn-label">Extract</span>
                            </button>
                        </div>
                    </form>

                    <div id="ee-error"
                        class="hidden mt-4 items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
                        role="alert">
                        <i data-lucide="alert-circle" class="mt-0.5 h-4 w-4 shrink-0 text-rose-500"></i>
                        <span id="ee-error-msg"></span>
                    </div>
                </div>

                <div class="border-t border-slate-200 bg-panel px-6 py-6 sm:px-8 sm:py-8">
                    <div id="ee-loading" class="hidden">
                        <div class="flex items-center gap-3 text-sm font-medium text-slate-700">
                            <span class="relative flex h-3 w-3 shrink-0">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex h-3 w-3 rounded-full bg-blue-500"></span>
                            </span>
                            Scanning domain pages for email addresses...
                        </div>
                        <div id="ee-scan-progress" class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-500">
                            Crawling page 1...</div>
                        <div class="mt-5 space-y-3">
                            <div class="h-12 animate-pulse rounded-lg bg-slate-100"></div>
                            <div class="h-12 animate-pulse rounded-lg bg-slate-100"></div>
                            <div class="h-12 animate-pulse rounded-lg bg-slate-100"></div>
                        </div>
                    </div>

                    <div id="ee-results" class="hidden">
                        <div id="ee-stats" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div id="ee-status" class="flex flex-wrap items-center gap-2 text-[18px] text-slate-700">
                                <i data-lucide="check-circle-2" class="h-5 w-5 shrink-0 text-emerald-600"></i>
                                <span class="font-semibold text-slate-800">Extraction complete</span>
                                <span class="text-slate-300">:</span>
                                <span id="ee-count-text" class="font-semibold text-slate-900"></span>
                            </div>
                            <div id="ee-actions" class="flex flex-wrap gap-3">
                                <button
                                    class="ee-action-btn inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.12em] text-slate-600 transition duration-200 hover:border-slate-300 hover:text-slate-900"
                                    onclick="eeExportCSV()">
                                    <i data-lucide="download" class="h-4 w-4"></i>
                                    CSV
                                </button>
                                <button
                                    class="ee-action-btn inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold uppercase tracking-[0.12em] text-slate-600 transition duration-200 hover:border-slate-300 hover:text-slate-900"
                                    id="ee-copyall-btn" onclick="eeCopyAll()">
                                    <i data-lucide="copy" class="h-4 w-4"></i>
                                    Copy All
                                </button>
                            </div>
                        </div>

                        <div id="ee-pages-scanned" class="hidden mt-4 text-sm font-medium text-slate-500"></div>

                        <div id="ee-empty" class="hidden pt-8 text-left">
                            <p class="text-[18px] font-medium leading-9 text-slate-700 sm:text-[20px]">
                                Enter a website URL and click Extract.
                            </p>
                            <p class="mt-3 text-[18px] leading-9 text-slate-500 sm:text-[20px]">
                                Results from the scanned domain pages will appear here.
                            </p>
                        </div>

                        <div id="ee-list"
                            class="hidden mt-6 max-h-[420px] overflow-y-auto rounded-xl border border-slate-200 bg-white">
                        </div>
                    </div>

                    <div id="ee-idle" class="pt-8 text-left">
                        <p class="text-[18px] font-medium leading-9 text-slate-700 sm:text-[20px]">
                            Enter a website URL and click Extract.
                        </p>
                        <p class="mt-3 text-[18px] leading-9 text-slate-500 sm:text-[20px]">
                            Results from the scanned domain pages will appear here.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
 </div>







<script>
  function scrollToSection(e) {
    e.preventDefault(); // stops URL change

    const target = document.getElementById("contact-form"); // your section ID
    if (target) {
      target.scrollIntoView({ behavior: "smooth" });
    }
  }
</script>


























<div class="max-w-[1430px] w-full grid md:grid-cols-2 gap-0 p-6 mx-auto mt-4">

  <!-- LEFT CARD -->
  <div class=" reveal-up ">

    <!-- Floating Icons -->
    
    <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-10-1.png" alt="Export">
  </div>


  <!-- RIGHT CARD -->
  <div class="relative bg-[#efeff1] rounded-2xl p-8 flex flex-col justify-center shadow-inner border border-[#e2e2e2] reveal-up">
<div class="absolute bg-white w-[783px] h-[9px] left-[-15px] top-[12px] hidden lg:block"></div>
    <h2 class="text-[37px] font-semibold text-[#1f2937] text-center">
      Extract from Anywhere.<br>
      Export Anywhere
    </h2>

    <p class="text-gray-700 text-[16px] mt-4 leading-relaxed text-center px-4 text-justify">
      ExtractMails supports all email clients including Gmail, Outlook, Yahoo, or any IMAP-based inbox. 
      It can also help you map the data in the preferred format, including CSV, JSON, Excel, Docs, etc. 
      It is an excellent tool if you want to extract data from emails and store it in your CRM database. 
      Your CRM database can be open source such as SuiteCRM, SugarCRM or any mainstream platform such as Salesforce. 
      You can also create custom mapping rules to structure your data exactly how you want to.
    </p>
<div class="absolute bg-white w-[783px] h-[9px] left-[-15px] bottom-[12px] hidden lg:block"></div>
  </div>

</div>



















<section class="py-4">
  <div class="max-w-6xl mx-auto px-6 text-center">

    <!-- Heading -->
    <h2 class="text-[32px] md:text-[40px] lg:text-[45px]  font-semibold  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent leading-snug reveal-up">
      Purpose-built for Smooth <br>
      <span class=" bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">Email Extraction</span>
    </h2>

    <!-- Subheading -->
    <p class="mt-4 text-[#6b7280] text-[15px] max-w-[1200px] mx-auto leading-relaxed reveal-up">
      All the features you need to extract, structure, and manage email data.
      Ensure efficient and accurate email extraction at scale.
    </p>

    <!-- Features -->
    <div class="mt-16 flex flex-col md:flex-row items-center justify-between relative reveal-up">

      <!-- Vertical Divider (1) -->
      <div class="hidden md:block absolute left-1/3 top-6 h-20 w-px bg-gray-300"></div>

      <!-- Vertical Divider (2) -->
      <div class="hidden md:block absolute left-2/3 top-6 h-20 w-px bg-gray-300"></div>

      <!-- Feature 1 -->
      <div class="w-full md:w-1/3 px-6 text-center">
        <div class="flex justify-center mb-4">
         <div class="w-26 h-26 ">
           <img src="https://www.extractmails.com/wp-content/uploads/2026/04/contact-mail-1.png" alt="Support">
          </div>
        </div>
        <h3 class="font-semibold text-[#111827] text-[16px]">
          Universal Email Support
        </h3>
        <p class="text-[#6b7280] text-[14px] mt-2 leading-relaxed">
          Connect Gmail, Outlook, or any IMAP email account
          without complex setup.
        </p>
      </div>

      <!-- Feature 2 -->
      <div class="w-full md:w-1/3 px-6 text-center mt-10 md:mt-0">
        <div class="flex justify-center mb-4">
         <div class="w-26 h-26 ">
           <img src="https://www.extractmails.com/wp-content/uploads/2026/04/export-1.png" alt="Export">
          </div>
        </div>
        <h3 class="font-semibold text-[#111827] text-[16px]">
          Flexible Data Export
        </h3>
        <p class="text-[#6b7280] text-[14px] mt-2 leading-relaxed">
          Export extracted data into CSV, Excel, JSON, or Docs —
          ready to use instantly.
        </p>
      </div>

      <!-- Feature 3 -->
      <div class="w-full md:w-1/3 px-6 text-center mt-10 md:mt-0">
        <div class="flex justify-center mb-4">
          <div class="w-26 h-26 ">
           <img src="https://www.extractmails.com/wp-content/uploads/2026/04/server-1.png" alt="Ready">
          </div>
        </div>
        <h3 class="font-semibold text-[#111827] text-[16px]">
          Database Ready
        </h3>
        <p class="text-[#6b7280] text-[14px] mt-2 leading-relaxed">
          Seamlessly push structured data into your internal
          systems or workflows.
        </p>
      </div>

    </div>

    <!-- Button -->
    <div class="mt-14">
      <a href="#"
       class=" mt-6 px-10 py-2 rounded-full text-white text-sm font-medium 
          bg-gradient-to-r from-blue-600 to-green-400
          transform transition duration-300 ease-in-out
          hover:scale-110 hover:-translate-y-1 hover:shadow-xl
          hover:text-white focus:text-white active:text-white">
        Contact Us
      </a>
    </div>

  </div>
</section>









<section class="py-10">
  <div class="max-w-[1400px] mx-auto px-6 text-center">

    <!-- Heading -->
    <h2 class="text-[32px] md:text-[40px] lg:text-[45px]  font-semibold  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent leading-snug reveal-up">
      Smart Email Extraction– Total <br>
      <span class=" bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">Automation With 0% Manual Work</span>
    </h2>

    <!-- Subtext -->
    <p class="mt-5 text-[#6b7280] text-[14px] max-w-[1400px] mx-auto leading-relaxed reveal-up">
      Extract, structure, and manage email data automatically, converting unstructured messages into actionable and clean insights.
      Easily process high volume of emails with greater precision, minimize manual work, and get ready to use data.
      Trigger your workflows and tools instantly and automatically.
    </p>

    <!-- Cards -->
    <div class="mt-16 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 reveal-up">

      <!-- CARD -->
      <div class="text-center">
        <div class="mx-auto w-32 h-32 ">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-28-2.png" alt="icons" class="w-32 h-32">
        </div>
        <h3 class="mt-4 font-semibold text-[#111827] text-[15px]">
          Multi-Field Extraction
        </h3>
        <p class="text-[#6b7280] text-[13px] mt-2 leading-relaxed px-2">
          Extract key data automatically from: To, From, BCC, CC, Body, Subject, and Attachments.
        </p>
      </div>

      <!-- CARD -->
      <div class="text-center">
        <div class="mx-auto w-32 h-32 ">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-57-4.png" alt="icons" alt="icons" class="w-32 h-32">
        </div>
        <h3 class="mt-4 font-semibold text-[#111827] text-[15px]">
          Smart Email Parsing
        </h3>
        <p class="text-[#6b7280] text-[13px] mt-2 leading-relaxed px-2">
          Transform unstructured emails into structured data and insights.
        </p>
      </div>

      <!-- CARD -->
      <div class="text-center">
        <div class="mx-auto w-32 h-32 ">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-58-4.png" alt="icons" class="w-32 h-32">
        </div>
        <h3 class="mt-4 font-semibold text-[#111827] text-[15px]">
          Bulk Email Processing
        </h3>
        <p class="text-[#6b7280] text-[13px] mt-2 leading-relaxed px-2">
          Process a top volume of emails immediately and accurately with minimum manual effort.
        </p>
      </div>

      <!-- CARD -->
      <div class="text-center">
         <div class="mx-auto w-32 h-32 ">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-59-2.png" alt="icons" class="w-32 h-32">
        </div>
        <h3 class="mt-4 font-semibold text-[#111827] text-[15px]">
          Flexible Data Export
        </h3>
        <p class="text-[#6b7280] text-[13px] mt-2 leading-relaxed px-2">
          Export data to CSV, Excel, JSON, Doc, or any preferable formats directly.
        </p>
      </div>

      <!-- CARD -->
      <div class="text-center">
         <div class="mx-auto w-32 h-32 ">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-60-3.png" alt="icons" class="w-32 h-32">
        </div>
        <h3 class="mt-4 font-semibold text-[#111827] text-[15px]">
          Workflow Automation
        </h3>
        <p class="text-[#6b7280] text-[13px] mt-2 leading-relaxed px-2">
          Ensure total automation of email extraction, get usable and structured data in your database directly.
        </p>
      </div>

    </div>

  </div>
</section>







<section class="relative overflow-hidden max-w-[1400px] mx-auto my-12 py-16 rounded-[4]">
  <!-- Background Gradient -->
  <!-- Background Gradient -->


<!-- Rectangles -->

<div class="absolute w-[687px] h-[272px] left-[24px] top-[425px] bg-[#b1c3ff] blur-[147px]"></div>

<div class="absolute w-[548px] h-[387px] left-[749px] top-[1136px] bg-[#b1c3ff] blur-[147px]"></div>

<div class="absolute w-[548px] h-[444px] left-[24px] top-[1098px] bg-[#b1c3ff] blur-[147px]"></div>

<div class="absolute w-[372px] h-[175px] left-[225px] top-[237px] bg-[#b1c3ff] blur-[147px]"></div>

<div class="absolute w-[715.56px] h-[161.15px] left-[658.32px] top-[390.31px] bg-[#98b0ff] blur-[147px] origin-top-left rotate-[-18.579deg]"></div>

<div class="absolute w-[256.53px] h-[161.15px] left-[1002px] top-[1724.73px] bg-[#98b0ff] blur-[147px] origin-top-left rotate-[-18.579deg]"></div>

<div class="absolute w-[260.74px] h-[54.57px] left-[969px] top-[-8.93px] bg-[#98b0ff]/40 blur-[47.7px] origin-top-left rotate-[-18.579deg]"></div>

<div class="absolute w-[525.08px] h-[82.15px] left-[1085.53px] top-[766.46px] bg-[#00c979] blur-[147px] origin-top-left rotate-[-9.961deg]"></div>

<div class="absolute w-[525.08px] h-[74.61px] left-[218px] top-[1658.83px] bg-[#00c979] blur-[147px] origin-top-left rotate-[-9.961deg]"></div>

<div class="absolute w-[153.89px] h-[55.97px] left-[-56px] top-[847.02px] bg-[#00c979] blur-[81px] origin-top-left rotate-[-9.961deg]"></div>

<div class="absolute w-[519.35px] h-[94.17px] left-[-219.02px] top-[-118.97px] bg-[#00c979] blur-[147px] origin-top-left rotate-[48.581deg]"></div>

  <!-- Container -->
  <div class="relative max-w-6xl mx-auto px-6">

    <!-- Heading -->
    <div class="text-center max-w-3xl mx-auto">
      <h2 class="text-[30px] md:text-[36px] font-semibold text-gray-800 leading-tight tracking-[-0.3px] reveal-up">
        Email Extraction Tools Built for <br class="hidden md:block"> Speed and Accuracy
      </h2>
      <p class="mt-4 text-[14px] md:text-[15px] text-gray-700 leading-relaxed reveal-up">
        Irrespective of whether you want to extract structured data from emails or find email addresses
        from websites, we have the right tools for you.
      </p>
    </div>

    <!-- Top Cards -->
    <div class="grid md:grid-cols-2 gap-8 mt-14 reveal-up">
      
      <!-- Card 1 -->
      <div class="bg-white backdrop-blur-xl rounded-[28px] p-10 text-center shadow-[0_10px_30px_rgba(0,0,0,0.05)]">
        <div class="w-16 h-16 mx-auto  items-center justify-center text-xl text-gray-600">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-2-2.png" alt="icon">
        </div>
        <h3 class="mt-6 text-[28px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">
  Extract Emails Now
</h3>
        <p class="mt-3 text-[14.5px] text-gray-700 leading-relaxed">
         Extract important data from incoming emails automatically, including recipients, subject, sender, body, and attachments. Backed by AI, this tool enables you to automate workflows such as data entry, lead creation, and email processing.  
        </p>
        <button class=" mt-6 px-20 py-2 rounded-full text-white text-sm font-medium 
          bg-gradient-to-r from-blue-600 to-green-400
          transform transition duration-300 ease-in-out
          hover:scale-110 hover:-translate-y-1 hover:shadow-xl
          hover:text-white focus:text-white active:text-white">
          Start Extraction
        </button>
      </div>

      <!-- Card 2 -->
      <div class="   bg-white backdrop-blur-xl rounded-[28px] p-10 text-center shadow-[0_10px_30px_rgba(0,0,0,0.05)]">
       <div class="w-16 h-16 mx-auto  items-center justify-center text-xl text-gray-600">
          <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-51-1.png" alt="icon">
        </div>
        <h3 class="mt-6 text-[28px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">
          Bulk Email Finder
        </h3>
        <p class="mt-3 text-[14.5px] text-gray-700 leading-relaxed">
         Provide a URL or domain and immediately find email addresses publicly available on the website. This is perfect for outreach, lead generation, and developing targeted contact lists.  
        </p>
        <button class=" mt-10 px-20 py-2 rounded-full text-white text-sm font-medium 
          bg-gradient-to-r from-blue-600 to-green-400
          transform transition duration-300 ease-in-out
          hover:scale-110 hover:-translate-y-1 hover:shadow-xl
          hover:text-white focus:text-white active:text-white">
          Try Bulk Finder
        </button>
      </div>

    </div>

    <!-- Steps Section -->
    <div class="mt-20 text-center max-w-3xl mx-auto">
      <h3 class="text-[26px] md:text-[30px] font-semibold text-gray-800 leading-tight reveal-up">
        Automate Your Email <br class="hidden md:block"> Extraction in 4 Simple Steps
      </h3>
      <p class="mt-4 text-[14px] text-gray-500 leading-relaxed reveal-up">
        Using ExtractMails is extremely simple and ideal for beginners with no technical background.
        You can activate our tool in your business workflow in four simple steps.
      </p>
    </div>

    <!-- Steps Grid -->
    <div class="grid md:grid-cols-2 gap-8 mt-14  reveal-up">

      <!-- Step 1 -->
      <div class="bg-white  backdrop-blur-xl rounded-[22px] p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 items-center justify-center ">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-20.png" alt="ExtractMails">
          </div>
          <span class="text-[15px] text-gray-600">Step 1</span>
        </div>
        <h4 class="mt-4 text-[24px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_95.23%)] bg-clip-text text-transparent [-webkit-background-clip:text] [-webkit-text-fill-color:transparent]">
  Sign Up at ExtractMails:
</h4>
        <p class="mt-2 text-[15px] text-gray-700 leading-relaxed">
          Sign up at ExtractMails and get a tailored and intuitive dashboard where you can handle all activities of email extraction.  
        </p>
      </div>

      <!-- Step 2 -->
    <div class=" bg-white backdrop-blur-xl rounded-[22px] p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 items-center justify-center ">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-20.png" alt="ExtractMails">
          </div>
          <span class="text-[15px] text-gray-600">Step 2</span>
        </div>
        <h4 class="mt-4 text-[24px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_95.23%)] bg-clip-text text-transparent [-webkit-background-clip:text] [-webkit-text-fill-color:transparent]">
         Connect ExtractMails: 
        </h4>
        <p class="mt-2 text-[14px] text-gray-600 leading-relaxed">
          Connect ExtractMails to your email account using API integration. The system will then activate and begin analyzing all incoming emails, extracting crucial data from the fields.  
        </p>
      </div>

      <!-- Step 3 -->
      <div class=" bg-white  backdrop-blur-xl rounded-[22px] p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
        <div class="flex items-center gap-3">
         <div class="w-10 h-10 items-center justify-center ">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-20.png" alt="ExtractMails">
          </div>
          <span class="text-[15px] text-gray-600">Step 3</span>
        </div>
        <h4 class="mt-4 text-[24px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_95.23%)] bg-clip-text text-transparent [-webkit-background-clip:text] [-webkit-text-fill-color:transparent]">
          Create Mapping Rules: 
        </h4>
        <p class="mt-2 text-[14px] text-gray-600 leading-relaxed">
          Personalize how you want the data to be extracted through custom mapping rules. This will make sure that the information will be structured exactly how you want it to be.  
        </p>
      </div>

      <!-- Step 4 -->
      <div class=" bg-white  backdrop-blur-xl rounded-[22px] p-6 shadow-[0_10px_25px_rgba(0,0,0,0.05)]">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 items-center justify-center ">
            <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Frame-20.png" alt="ExtractMails">
          </div>
          <span class="text-[15px] text-gray-600">Step 4</span>
        </div>
        <h4 class="mt-4 text-[24px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_95.23%)] bg-clip-text text-transparent [-webkit-background-clip:text] [-webkit-text-fill-color:transparent]">
          Connect and Export: 
        </h4>
        <p class="mt-2 text-[14px] text-gray-600 leading-relaxed">
          Incorporate your preferred platforms and automatically export the extracted data. Update systems, create records, and keep your database updated and accurate.  
        </p>
      </div>

    </div>

  </div>
</section>







<section class="max-w-[1400px] mx-auto px-3 py-0">
  <div class="grid md:grid-cols-[.9fr_1.1fr] gap-12 items-center">

    <!-- LEFT IMAGE -->
    <div class="relative p-4">
      <div class="rounded-[20px] overflow-hidden  reveal-up ">
        <img 
          src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Mar-30-2026-04_33_43-PM-1-1.png" 
          alt="Why Choose ExtractMails"
          class="w-full h-full object-cover "
        />
      </div>
    </div>

    <!-- RIGHT CONTENT -->
    <div>
      <h2 class="text-[28px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent reveal-up">
        Why Choose ExtractMails?
      </h2>

      <p class="mt-4 text-[16px] text-gray-500 leading-relaxed reveal-up">
        A right email extractor solution can make a world of difference to how you approach 
        email campaigns, client relationships, and bulk orders. ExtractMails stands apart 
        because of its efficiency, reliability, and usability.
      </p>

      <div class="mt-6 space-y-4 reveal-up">

        <div >
          <h3 class="text-[17px] font-semibold text-[#0f172a]">
            Save Effort and Time:
          </h3>
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
            Automate routine tasks and remove manual entry of data. You can then use your 
            saved time on strategic activities that elevate your growth.
          </p>
        </div>

        <div>
          <h3 class="text-[17px] font-semibold text-[#0f172a]">
            Expertise:
          </h3>
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
            With years of experience in data processing and email solutions, we have a clear 
            and in-depth understanding of the kind of email extraction-related challenges 
            that businesses face.
          </p>
        </div>

        <div>
          <h3 class="text-[17px] font-semibold text-[#0f172a]">
            Reliability:
          </h3>
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
            Our platform is developed to ensure continuous performance, making sure that 
            operations occur smoothly without any disruptions or inaccuracies.
          </p>
        </div>

        <div>
          <h3 class="text-[17px] font-semibold text-[#0f172a]">
            Responsive Support:
          </h3>
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
            We are responsive to our clients and try to resolve their issues on priority. 
            We are available via call or email on all business days.
          </p>
        </div>

      </div>
    </div>

  </div>
</section>





<section class="max-w-[1400px] mx-auto px-3 py-0">
  <div class="grid md:grid-cols-[1.1fr_.9fr] gap-12 items-center">

    

    <!-- RIGHT CONTENT -->
    <div>
      <h2 class="text-[28px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent reveal-up">
        What We Do 
      </h2>

      <p class="mt-4 text-[16px] text-gray-500 leading-relaxed reveal-up">
       ExtractMails offers a wide range of email-related services that are focused on automation, extraction, and business communication enhancement. The core function of the platform is smart email extraction, which allows users to automatically capture and organize important data from the email, without manual intervention. 
      </p>

      <div class="mt-6 space-y-4 reveal-up">

        <div>
          
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
           ExtractMails offers multiple email-related services, such as email parsing, bulk email extraction, and integration with CRM tools and spreadsheets. The platform also supports multiple features, such as intelligent filtering, prioritizing and sorting extracted data based on user-defined criteria, and high-performance processing for handling large-scale data efficiently. 
          </p>
        </div>

        <div>
          
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
           The platform also provides various complementary services, like email security, email hosting, email marketing, and email archiving. The main goal of ExtractMails is to create an all-in-one ecosystem that manages business email operations efficiently. 
          </p>
        </div>

        <div>
          
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
           ExtractMails helps organizations improve their productivity by automating repetitive tasks, reducing errors, and streamlining overall communication processes. 
          </p>
        </div>
      </div>
    </div>



    <!-- LEFT IMAGE -->
    <div class="relative p-4">
      <div class="rounded-[20px] overflow-hidden  reveal-up ">
        <img 
          src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Apr-14-2026-04_29_49-PM-1.png" 
          alt="Why Choose ExtractMails"
          class="w-full h-full object-cover "
        />
      </div>
    </div>




  </div>
</section>



<section class="max-w-[1400px] mx-auto px-3 py-0">
  <div class="grid md:grid-cols-[.9fr_1.1fr] gap-12 items-center">

    <!-- LEFT IMAGE -->
    <div class="relative p-4">
      <div class="rounded-[20px] overflow-hidden  reveal-up ">
        <img 
          src="https://www.extractmails.com/wp-content/uploads/2026/04/20260414_1630_Image-Generation_simple_compose_01kp5tc8gaewtsj2m89xr2q6by-1.png" 
          alt="Why Choose ExtractMails"
          class="w-full h-full object-cover "
        />
      </div>
    </div>

    <!-- RIGHT CONTENT -->
    <div>
      <h2 class="text-[28px] font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent reveal-up ">
       Who We Are 
      </h2>

      <p class="mt-4 text-[16px] text-gray-500 leading-relaxed reveal-up">
      ExtractMails is a technology-driven company focused on delivering email automation and data extraction solutions to businesses worldwide. The company aims to empower organizations by providing efficient, secure, and user-friendly email services that boost productivity and business growth. 
      </p>

      <div class="mt-6 space-y-4 reveal-up">

        <div>
          
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
          ExtractMails is built around email marketing and automation, offering a specialized solution that meets every need. Our team emphasizes reliability, innovation, and superior customer support. The platform also ensures that users can depend on their tools for consistent performance. ExtractMails also focuses on data privacy and security, which is among the most critical aspects in handling sensitive communication data. 
          </p>
        </div>

        <div>
          
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
           ExtractMails positions itself as a reliable partner for businesses that aim to automate email workflow, along with ensuring improved data handling and optimized communication systems.
          </p>
        </div>

        <div>
          
          <p class="text-[16px] text-gray-500 leading-relaxed mt-1">
          The platform aims to boost its capabilities in evolving email technology and marketing space, with continuous improvements and technology integration. 
          </p>
        </div>

       

      </div>
    </div>

  </div>
</section>





<section class="relative py-20 overflow-hidden max-w-[1600px] mx-auto">

  <!-- Background Gradient Shapes -->
   
    
  <div class="absolute left-[-153px] top-[450px] w-[740px] h-[50.69px] bg-[#00c979]/80 blur-[197.85px] origin-top-left rotate-[-2.707deg]"></div>

  <div class="absolute left-[679.2px] top-[450px] w-[740px] h-[50.66px] bg-[#2629c6]/60 blur-[197.85px] origin-top-left rotate-[-2.707deg]"></div>

  <div class="relative max-w-[1300px] mx-auto  px-6 text-center">

    <!-- Heading -->
    <h2 class="text-[32px] md:text-[40px] lg:text-[45px]  font-semibold leading-tight  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent reveal-up">
  Choose a Plan That <br> Fits Your Needs
</h2>
    <p class="mt-4 text-[14px] text-gray-500 reveal-up">
      We offer flexible pricing plans designed to meet the needs of individuals, startups, and large organizations.
    </p>

    <!-- Toggle -->
    <!-- <div class="flex items-center justify-center gap-4 mt-6">
      <span class="text-sm text-gray-600">Monthly</span>
      <button id="toggle" class="w-16 h-6 bg-[linear-gradient(93.93deg,rgba(4,5,89,1)_0%,rgba(55,59,251,1)_100%)] rounded-[10px] relative">
        <span id="dot" class="absolute left-1 top-1 w-6 h-4 bg-white rounded-full transition-all"></span>
      </button>
      <span class="text-sm text-gray-600">Yearly</span>
    </div> -->

    <!-- Cards -->
    <div class="grid md:grid-cols-3 gap-16 mt-14 reveal-up">

      <!-- Basic -->
      <div class="bg-[#ffffff] rounded-[28px] p-8 shadow-[0px_0px_0.6px_8px_rgb(246,253,251)]">
        <h3 class="text-[22px] font-semibold text-[#1e293b]">Trial</h3>

        <div class="border-t border-b py-6 my-6">
          <span id="basicPrice" class="text-[40px] font-bold">Free</span>
          <span class="text-sm text-gray-500"> 100 credits</span>
        </div>

        <ul class="text-left space-y-3 text-sm text-gray-600">
          <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="plan" 
       class="w-4 h-4">
  <span>100 Credits</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="plan" 
       class="w-4 h-4">
  <span>Limited Usage</span>
</li>

  <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="plan" 
       class="w-4 h-4">
  <span>Limited Email Extraction</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="" 
       class="w-4 h-4">
  <span>No CRM integrations</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="" 
       class="w-4 h-4">
  <span>No Full automation</span>
</li>
        </ul>

        <a href="https://www.extractmails.com/contact-page/" 
   class="mt-10 inline-block px-6 py-2 rounded-full text-white text-sm font-medium 
          bg-gradient-to-r from-blue-600 to-green-400
          transform transition duration-300 ease-in-out
          hover:scale-110 hover:-translate-y-1 hover:shadow-xl
          hover:text-white focus:text-white active:text-white">
  Contact Us
</a>
      </div>

      <!-- Standard -->
      <div class="bg-white  rounded-[28px] p-8 border-2 border-green-200 shadow-[0px_0px_0.6px_8px_rgb(246,253,251)] relative">
        <span class="absolute top-4 right-4 text-xs bg-gradient-to-r from-blue-700 to-green-400 text-white px-3 py-1 rounded-full">
          Most Popular
        </span>

        <h3 class="text-[22px] font-semibold text-[#1e293b]">Pay As You Go</h3>

        <div class="border-t border-b py-6 my-6">
          <span id="standardPrice" class="text-[40px] font-bold">$80</span>
          <span class="text-sm text-gray-500"> 250 credits</span>
        </div>

        <ul class="text-left space-y-3 text-sm text-gray-600">
          <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="Plan" 
       class="w-4 h-4">
  <span>250 Credits</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="Plan" 
       class="w-4 h-4">
  <span>Unlimited Email Extraction</span>
</li>

  <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="Plan" 
       class="w-4 h-4">
  <span>Unlimited Usage</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="" 
       class="w-4 h-4">
  <span>CRM integrations</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="Plan" 
       class="w-4 h-4">
  <span>Full automation </span>
</li>
        </ul>

        <a href="https://www.extractmails.com/contact-page/" 
   class="mt-10 inline-block px-6 py-2 rounded-full text-white text-sm font-medium 
          bg-gradient-to-r from-blue-600 to-green-400
          transform transition duration-300 ease-in-out
          hover:scale-110 hover:-translate-y-1 hover:shadow-xl
          hover:text-white focus:text-white active:text-white">
  Contact Us
</a>
      </div>

      <!-- Premium -->
      <div class="bg-white  rounded-[28px] p-8 shadow-[0px_0px_0.6px_8px_rgb(246,248,255)]">
        <h3 class="text-[22px] font-semibold text-[#1e293b]">Enterprise Plan</h3>

        <div class="border-t border-b py-6 my-6">
          <span id="premiumPrice" class="text-[35px] font-bold">Let's Talk</span>
          <span class="text-sm text-gray-500"> Custom credits</span>
        </div>

        <ul class="text-left space-y-3 text-sm text-gray-600">
          <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="Plan" 
       class="w-4 h-4">
  <span>Custom Credits</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="" 
       class="w-4 h-4">
  <span> Unlimited Email Extraction</span>
</li>

  <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="Plan" 
       class="w-4 h-4">
  <span> Unlimited Usage</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="" 
       class="w-4 h-4">
  <span>CRM integrations</span>
</li>

   <li class="flex items-center gap-2">
  <img src="https://www.extractmails.com/wp-content/uploads/2026/04/Group-8-1.png" 
       alt="Plan" 
       class="w-4 h-4">
  <span>Full automation</span>
</li>

  
        </ul>

        <a href="https://www.extractmails.com/contact-page/" 
   class="mt-10 inline-block px-6 py-2 rounded-full text-white text-sm font-medium 
          bg-gradient-to-r from-blue-600 to-green-400
          transform transition duration-300 ease-in-out
          hover:scale-110 hover:-translate-y-1 hover:shadow-xl
          hover:text-white focus:text-white active:text-white">
  Contact Us
</a>
      </div>

    </div>
  </div>

  <!-- Script -->
  <script>
    const toggle = document.getElementById('toggle');
    const dot = document.getElementById('dot');

    let yearly = false;

    toggle.addEventListener('click', () => {
      yearly = !yearly;

      dot.classList.toggle('translate-x-8');

      document.getElementById('basicPrice').innerText = yearly ? '$699.99' : '$69.99';
      document.getElementById('standardPrice').innerText = yearly ? '$899.99' : '$89.99';
      document.getElementById('premiumPrice').innerText = yearly ? '$999.99' : '$99.99';
    });
  </script>
</section>








































<section class="max-w-[1400px] mx-auto px-6 py-16">

  <!-- Header -->
  <div class="flex items-center justify-between mb-10 reveal-up">
    <h2 class="text-[32px] md:text-[40px] lg:text-[45px]  font-semibold bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] bg-clip-text text-transparent">
      Recently Posted <span class="text-[#4f46e5]">Blogs</span>
    </h2>

 <a href="<?php echo site_url('/blog'); ?>"
   class="px-6 py-2 rounded-full text-white text-sm font-medium 
          bg-gradient-to-r from-blue-600 to-green-400
          transform transition duration-300 ease-in-out
          hover:scale-110 hover:-translate-y-1 hover:shadow-xl
          hover:text-white focus:text-white active:text-white">
  See All Blogs
</a>
  </div>

  <!-- Blog Grid -->
  <div class="grid md:grid-cols-3 gap-8 reveal-up">

    <?php
    $args = array(
      'post_type' => 'post',
      'posts_per_page' => 3
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
      while ($query->have_posts()) : $query->the_post();
    ?>

    <!-- Clickable Card -->
    <a href="<?php the_permalink(); ?>" class="block group">

      <div class="transition duration-300">

        <!-- Image -->
        <div class="rounded-[20px] overflow-hidden shadow-md">
          <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('large'); ?>" 
                 alt="<?php the_title(); ?>"
                 class="w-full h-[220px] object-cover 
                        group-hover:scale-105 transition duration-300">
          <?php else: ?>
            <img src="https://via.placeholder.com/400x250"
                 class="w-full h-[220px] object-cover">
          <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="mt-4">
          <h3 class="text-[18px] font-semibold text-[#111827] leading-snug 
                     group-hover:text-blue-600 transition">
            <?php the_title(); ?>
          </h3>

          <p class="text-[14px] text-gray-600 mt-3 leading-relaxed">
            <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
          </p>
        </div>

      </div>

    </a>

    <?php
      endwhile;
      wp_reset_postdata();
    endif;
    ?>

  </div>

</section>






<section class="max-w-[1400px] mx-auto py-0 px-6">

  <!-- Heading -->
  <h2 class="text-center text-[32px] md:text-[40px] lg:text-[45px] font-semibold 
  bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
  bg-clip-text text-transparent mb-12  reveal-up">
    Frequently Asked Questions
  </h2>

  <div class="space-y-6">

    <!-- ITEM -->
    <div class="faq-item cursor-pointer">

      <!-- GRADIENT BORDER WRAPPER -->
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">

        <!-- INNER CARD -->
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)] reveal-up">

          <div class="flex justify-between items-center ">
            
            <!-- GRADIENT TEXT -->
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Does ExtractMails supports all email clients?  
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <!-- CONTENT -->
          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[14px] text-gray-500">
             ExtractMails operates with all major clients including Outlook, Gmail, Yahoo, etc. You do not need any complex setup to connect with your account. 
            </p>
          </div>

        </div>
      </div>
    </div>

    <!-- COPY SAME STRUCTURE FOR ALL ITEMS -->

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
             In how many formats can I export the extracted data?
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[14px] text-gray-500">
              You can export the extracted data from emails in a variety of formats such as Excel, CSV, JSON, or Docs as per your preference. The data is extracted with custom mapping rules and is ready to use upon extraction. 
            </p>
          </div>

        </div>
      </div>
    </div>

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
              Can ExtractMails incorporate with my CRM?  
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[14px] text-gray-500">
             Yes, ExtractMails provides support for both open-source CRMs such as SugarCRM and SuiteCRM and can also combine with commercial platforms such as Salesforce. You have the option to push structured data into your preferred internal systems or database directly.  
            </p>
          </div>

        </div>
      </div>
    </div>

    <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
               If I don’t have a technical background, can I still use ExtractMails? 
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[14px] text-gray-500">
              Yes, the platform is purpose-built to be friendly for beginners. You can follow the four-step process to sign up and launch ExtractMails into your workflows. It has an innovative dashboard that lets you handle all your email extraction controls without any coding.
            </p>
          </div>

        </div>
      </div>
    </div>




     <div class="faq-item cursor-pointer">
      <div class="p-[2px] rounded-[20px] bg-gradient-to-r from-black to-blue-600">
        <div class="bg-white rounded-[18px] px-6 py-5 shadow-[0_8px_20px_rgba(59,59,255,0.15)]">

          <div class="flex justify-between items-center">
            <h3 class="text-[16px] md:text-[22px] font-semibold 
            bg-[linear-gradient(271deg,#383CFF_1.04%,#030458_65.23%)] 
            bg-clip-text text-transparent">
               What kind of data can ExtractMails extract from emails?  
            </h3>

            <span class="text-[26px] font-light faq-icon transition-transform duration-300">+</span>
          </div>

          <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
            <p class="mt-4 text-[14px] text-gray-500">
               ExtractMails can easily extract data from different fields such as To, From, CC, BCC, Subject, Body, and Attachments. Via custom mapping rules, you can also structure the data however you want it. 
            </p>
          </div>

        </div>
      </div>
    </div>

  </div>

</section>







<script>
document.querySelectorAll('.faq-item').forEach(item => {
  item.addEventListener('click', () => {

    const content = item.querySelector('.faq-content');
    const icon = item.querySelector('.faq-icon');

    // Close others
    document.querySelectorAll('.faq-item').forEach(i => {
      if (i !== item) {
        const c = i.querySelector('.faq-content');
        c.style.maxHeight = null;
        i.querySelector('.faq-icon').textContent = '+';
      }
    });

    // Toggle current
    if (content.style.maxHeight) {
      content.style.maxHeight = null;
      icon.textContent = '+';
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
      icon.textContent = '-';
    }

  });
});
</script>



<style id="faq-style">
.faq-content {
  transition: all 0.3s ease;
}
</style>






















<section class="max-w-[1400px] mx-auto py-0 px-4 pt-16">

  <div class="relative overflow-hidden rounded-[40px] md:rounded-[58px] 
              bg-[linear-gradient(95.92deg,#030458_0%,#383CFF_100%)] 
              min-h-[500px] md:h-[471px] 
              px-6 md:px-16 py-10 md:py-14 
              flex flex-col md:flex-row items-center">

    <!-- STRIPES (same as yours) -->
    <div class="absolute w-[18%] h-[140%] left-[8%] top-[-20%] rotate-[18deg] 
                bg-[linear-gradient(180.41deg,#030458_0%,#383CFF_100%)]"></div>

    <div class="absolute w-[18%] h-[140%] left-[27%] top-[-20%] rotate-[18deg] 
                bg-[#02034a]/60"></div>

    <div class="absolute w-[18%] h-[140%] left-[33%] top-[-20%] rotate-[18deg] 
                bg-[linear-gradient(180.41deg,#030458_0%,#383CFF_100%)]"></div>

    <div class="absolute w-[7%] h-[140%] left-[88%] top-[-20%] rotate-[18deg] 
                bg-[linear-gradient(180.41deg,#030458_0%,#383CFF_100%)]"></div>

    <!-- GLOW -->
    <div class="absolute right-[-120px] top-[-100px] w-[400px] h-[400px] 
                bg-blue-400/20 blur-[120px] rounded-full"></div>

    <!-- OVERLAY -->
    <div class="absolute inset-0 
                bg-[linear-gradient(120deg,transparent_40%,rgba(255,255,255,0.08))]"></div>

    <!-- RIGHT IMAGE (TOP IN MOBILE) -->
    <div class="relative z-10 w-full md:w-1/2 flex justify-center md:justify-end order-1 md:order-2 mb-6 md:mb-0 reveal-up">
      <img src="https://www.extractmails.com/wp-content/uploads/2026/04/ChatGPT-Image-Mar-30-2026-11_07_25-AM-1.png"
           class="h-[260px] md:h-[500px] object-contain"
           alt="">
    </div>

    <!-- LEFT CONTENT -->
    <div class="relative z-10 w-full md:w-1/2 text-white text-center md:text-left order-2 md:order-1">

      <h2 class="text-[26px] md:text-[38px] text-white/80 font-semibold leading-[1.3] reveal-up">
        Convert Your Emails into a Goldmine of Insights
      </h2>

      <p class="mt-4 md:mt-5 text-[14px] md:text-[16px] text-white/80 leading-relaxed reveal-up">
        Any data can become useful in closing deals. Prevent it from being lost in the inbox.
        Try ExtractMails Today! Fully automate your email extraction. No manual effort needed.
      </p>

      <!-- BUTTON -->
      <div class="mt-6 md:mt-8 flex justify-center md:justify-start reveal-up">
        <a href="#"
           class="inline-block px-6 md:px-12 py-3 rounded-full text-white text-sm font-medium
                  bg-gradient-to-r from-indigo-700 to-green-400
                  hover:scale-105 transition duration-300">
          Start Now
        </a>
      </div>

    </div>

  </div>

</section>







    
    <script>
        const EE_NONCE = <?php echo wp_json_encode($nonce); ?> ;
        const EE_URL = <?php echo wp_json_encode(get_permalink()); ?> ;
        let _emails = [];
        let _pagesScanned = 0;

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            document.getElementById('ee-form').addEventListener('submit', e => {
                e.preventDefault();
                eeExtract();
            });
        });

        function showEl(id, displayClass = 'flex') {
            const el = document.getElementById(id);
            el.classList.remove('hidden', 'block', 'flex', 'inline-flex', 'grid');
            el.classList.add(displayClass);
        }

        function hideEl(id) {
            const el = document.getElementById(id);
            el.classList.remove('block', 'flex', 'inline-flex', 'grid');
            el.classList.add('hidden');
        }

        function esc(s) {
            return s
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function setLoading(on) {
            const btn = document.getElementById('ee-btn');
            const label = document.getElementById('ee-btn-label');
            const icon = document.getElementById('ee-btn-icon');

            hideEl('ee-error');
            hideEl('ee-results');

            if (on) {
                hideEl('ee-idle');
                btn.disabled = true;
                label.textContent = 'Extracting...';
                icon.setAttribute('data-lucide', 'loader-2');
                icon.className = 'h-5 w-5 animate-spin';
                showEl('ee-loading', 'block');
            } else {
                btn.disabled = false;
                label.textContent = 'Extract';
                icon.setAttribute('data-lucide', 'search');
                icon.className = 'h-5 w-5';
                hideEl('ee-loading');
            }

            lucide.createIcons();
        }

        function showError(msg) {
            document.getElementById('ee-error-msg').textContent = msg;
            showEl('ee-idle', 'block');
            showEl('ee-error', 'flex');
        }

        async function eeExtract() {
            const urlEl = document.getElementById('ee-url');
            const url = urlEl.value.trim();

            if (!url) {
                urlEl.focus();
                return;
            }

            setLoading(true);

            const fd = new FormData();
            fd.append('ee_nonce', EE_NONCE);
            fd.append('url', url);

            try {
                const resp = await fetch(EE_URL, {
                    method: 'POST',
                    body: fd
                });

                if (!resp.ok) {
                    throw new Error(`Server error HTTP ${resp.status}`);
                }

                let data;

                try {
                    data = await resp.json();
                } catch (e) {
                    throw new Error('Unexpected server response - check PHP error logs.');
                }

                setLoading(false);

                if (!data.ok) {
                    showError(data.msg || 'Unknown error.');
                    return;
                }

                _emails = data.emails || [];
                _pagesScanned = data.pages_scanned || 1;
                renderResults(_emails, _pagesScanned);
            } catch (err) {
                setLoading(false);
                showError('Request failed: ' + err.message);
            }
        }

        function renderResults(emails, pagesScanned) {
            hideEl('ee-idle');
            showEl('ee-results', 'block');

            const n = emails.length;
            const pagesEl = document.getElementById('ee-pages-scanned');
            const listEl = document.getElementById('ee-list');

            document.getElementById('ee-count-text').textContent = n === 1 ? '1 email found' : `${n} emails found`;

            if (pagesScanned && pagesScanned > 0) {
                pagesEl.textContent = `Pages scanned: ${pagesScanned} across the domain`;
                showEl('ee-pages-scanned', 'block');
            } else {
                hideEl('ee-pages-scanned');
            }

            listEl.innerHTML = '';

            if (!n) {
                showEl('ee-empty', 'flex');
                hideEl('ee-list');
                return;
            }

            hideEl('ee-empty');
            showEl('ee-list', 'block');

            emails.forEach(email => {
                const row = document.createElement('div');
                row.className =
                    'flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-4 transition hover:bg-slate-50 last:border-b-0 sm:px-6';
                row.innerHTML = `
                <div class="flex min-w-0 items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="h-4 w-4 shrink-0 text-sky-600">
                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                    <span class="truncate text-base font-medium text-slate-800 select-all">${esc(email)}</span>
                </div>
                <button
                    class="ee-copy-btn inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-transparent text-slate-400 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-200"
                    title="Copy"
                    onclick="eeCopyOne('${esc(email)}', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="h-4 w-4">
                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
                    </svg>
                </button>`;
                listEl.appendChild(row);
            });

            lucide.createIcons();
        }

        const CHECK_SVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
        stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
        <path d="M20 6 9 17l-5-5"></path>
    </svg>`;
        const COPY_SVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
        <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
    </svg>`;

        function eeCopyOne(email, btn) {
            const done = () => {
                btn.innerHTML = CHECK_SVG;
                btn.style.color = '#16a34a';
                setTimeout(() => {
                    btn.innerHTML = COPY_SVG;
                    btn.style.color = '';
                }, 2000);
            };

            if (navigator.clipboard) {
                navigator.clipboard.writeText(email).then(done).catch(done);
            } else {
                const ta = document.createElement('textarea');
                ta.value = email;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                done();
            }
        }

        function eeCopyAll() {
            if (!_emails.length) {
                return;
            }

            const btn = document.getElementById('ee-copyall-btn');
            const orig = btn.innerHTML;
            const done = () => {
                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                style="display:inline;margin-right:4px"><path d="M20 6 9 17l-5-5"></path></svg>Copied!`;
                btn.style.color = '#16a34a';
                btn.style.borderColor = '#bbf7d0';
                setTimeout(() => {
                    btn.innerHTML = orig;
                    btn.style.color = '';
                    btn.style.borderColor = '';
                    lucide.createIcons();
                }, 2200);
            };

            if (navigator.clipboard) {
                navigator.clipboard.writeText(_emails.join('\n')).then(done).catch(done);
            } else {
                done();
            }
        }

        function eeExportCSV() {
            if (!_emails.length) {
                return;
            }

            const blob = new Blob(['Email Address\n' + _emails.join('\n')], {
                type: 'text/csv;charset=utf-8;'
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'extracted-emails.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>









<style>
.reveal-up {
    opacity: 0;
    transform: translateY(60px);
    transition: all 0.99s ease;
}

.reveal-up.active {
    opacity: 1;
    transform: translateY(0);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const elements = document.querySelectorAll(".reveal-up");

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {

            if (entry.isIntersecting) {
                entry.target.classList.add("active");
            }

        });
    }, {
        threshold: 0.15
    });

    elements.forEach(el => observer.observe(el));
});
</script>




<?php get_footer() ?>
