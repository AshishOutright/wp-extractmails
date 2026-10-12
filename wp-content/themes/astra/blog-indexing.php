<?php
/**
 * Template Name: Web Indexing Status
 */

// ======================================================
// HIDE WARNINGS
// ======================================================

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);


// ======================================================
// REQUIRED FILES
// ======================================================

require_once '/web/html/vendor/vendor/autoload.php';

ob_start();
require_once '/web/html/extractmails.com/global_utilsfinal.php';
ob_end_clean();


// ======================================================
// GOOGLE CLIENT
// ======================================================

$keyPath = get_template_directory() . '/keys/search-consol-api-466709-09bbacc9afe7.json';

$client = new Google_Client();

$client->setAuthConfig($keyPath);

$client->setScopes([
    'https://www.googleapis.com/auth/webmasters.readonly'
]);

$token = $client->fetchAccessTokenWithAssertion();

$access_token = $token['access_token'];


// ======================================================
// HTTP CLIENT
// ======================================================

$httpClient = new GuzzleHttp\Client([

    'headers' => [

        'Authorization' => 'Bearer ' . $access_token,
        'Accept'        => 'application/json'
    ]
]);


// ======================================================
// FUNCTION : CHECK INDEX STATUS
// ======================================================

function check_url_indexing_gsc($url, $httpClient)
{
    try {

        $response = $httpClient->post(
            'https://searchconsole.googleapis.com/v1/urlInspection/index:inspect',
            [

                'json' => [

                    'inspectionUrl' => $url,
                    'siteUrl'       => 'sc-domain:extractmails.com'
                ]
            ]
        );

        $result = json_decode($response->getBody(), true);

        $coverage = strtolower(
            $result['inspectionResult']['indexStatusResult']['coverageState'] ?? ''
        );

        if (strpos($coverage, 'indexed') !== false) {

            return 'indexed';
        }

        return 'not_indexed';

    } catch (Exception $e) {

        return 'error';
    }
}


// ======================================================
// FETCH BLOGS FROM CRM
// ======================================================

// $records = get_set_blog(
//     'outr_published_blogs',
//     [],
//     [
//         'order_by' => 'date_entered DESC',
//         'limit'    => 50
//     ],
//     1
// );

// ======================================================
// FETCH LAST 3 DAYS BLOGS FROM CRM
// ======================================================

$three_days_ago = date('Y-m-d H:i:s', strtotime('-4 days'));

$records = get_set_blog(
    'outr_published_blogs',
    [
        'date_entered >=' => $three_days_ago
    ],
    [
        'order_by' => 'date_entered DESC',
        'limit'    => 50
    ],
    1
);

// ======================================================
// TABLE DESIGN
// ======================================================

echo "
<style>

table.crm-table{
    width:100%;
    border-collapse:collapse;
    font-family:Arial;
    background:#fff;
}

table.crm-table th{
    background:#f5f5f5;
    padding:12px;
    border:1px solid #ddd;
    text-align:left;
}

table.crm-table td{
    padding:12px;
    border:1px solid #ddd;
}

table.crm-table tr:nth-child(even){
    background:#fafafa;
}

</style>
";


// ======================================================
// TABLE START
// ======================================================

echo "
<table class='crm-table'>

<tr>
    <th>Blog Title</th>
    <th>Blog URL</th>
    <th>Index Status</th>
    <th>indexing_c</th>
</tr>
";


// ======================================================
// LOOP BLOGS
// ======================================================

if (!empty($records) && is_array($records)) {

    foreach ($records as $row) {

        $id = $row['id'] ?? '';

        $title = esc_html($row['name'] ?? 'N/A');

        $url = trim(
            $row['blog_url'] ??
            ($row['url'] ?? '')
        );

        // ONLY EXTRACTMAILS BLOGS
        if (
            empty($url) ||
            strpos($url, 'https://www.extractmails.com/') !== 0
        ) {
            continue;
        }

        // CHECK INDEX STATUS
        $status = check_url_indexing_gsc($url, $httpClient);

        // DEFAULT
        $indexing_c = 0;

        if ($status == 'indexed') {

            $status_html = "
            <span style='color:green;font-weight:bold;'>
                ✅ Indexed
            </span>
            ";

            $indexing_c = 0;

            // UPDATE CRM
            get_set_blog(
                'outr_published_blogs',
                [
                    'id'         => $id,
                    'indexing_c' => 0
                ],
                'update'
            );

        } elseif ($status == 'not_indexed') {

            $status_html = "
            <span style='color:red;font-weight:bold;'>
                ❌ Not Indexed
            </span>
            ";

            $indexing_c = 1;

            // UPDATE CRM
            get_set_blog(
                'outr_published_blogs',
                [
                    'id'         => $id,
                    'indexing_c' => 1
                ],
                'update'
            );

        } else {

            $status_html = "
            <span style='color:orange;font-weight:bold;'>
                ⚠️ Error
            </span>
            ";

            $indexing_c = '-';
        }

        // GOOGLE SEARCH URL
        $google_site_url = 'https://www.google.com/search?q=site:' . rawurlencode($url);

        echo "
        <tr>

            <td>{$title}</td>

            <td>
                <a href='{$google_site_url}'
                   target='_blank'
                   style='font-family:monospace;'>

                    site:{$url}

                </a>
            </td>

            <td>{$status_html}</td>

            <td>{$indexing_c}</td>

        </tr>
        ";
    }
}

echo "</table>";
?>