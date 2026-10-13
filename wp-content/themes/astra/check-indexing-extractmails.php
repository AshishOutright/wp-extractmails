<?php

// ======================================================
// 1. CONFIGURATION
// ======================================================
$alert_email = 'promotion@outrightcrm.com'; // <--- UPDATE THIS
$from_name   = 'Indexing Alerts';
$from_email  = 'notification.outright@gmail.com';
$keyPath     = '/web/html/extractmails.com/wp-content/themes/astra/keys/search-consol-api-466709-09bbacc9afe7.json';

/*
 * Define your sites here.
 * Key = The actual domain
 * Value = The exact Google Search Console property name
 * (Usually 'sc-domain:example.com' or 'https://example.com/')
 */
$target_sites = [
    'extractmails.com' => 'sc-domain:extractmails.com',
];

$wp_page_batch_size          = 20;  // WordPress REST API max is 100.
$max_urls_per_site_per_run  = 20;  // Keep this a multiple of $wp_page_batch_size.
$max_urls_per_site_per_day  = 2000; // Keep below Google's 2,000/day/site URL Inspection quota.
$inline_email_limit         = 50;   // If issues are <= this, show all rows in the email.
$email_preview_limit        = 25;   // If CSV is attached, show only this many rows in the email.
$scan_state_file            = __DIR__ . '/indexing-scan-state.json';

/*
 * Automatic recrawl notifications for unindexed pages.
 *
 * Google does not provide a public API for the normal "Request indexing" button
 * for standard pages. The script therefore uses two legitimate automation paths:
 * 1) IndexNow submits the exact unindexed URLs to participating search engines.
 * 2) Google Search Console sitemap submission asks Google to recrawl the sitemap.
 */
$auto_reindex_unindexed_pages    = true;
$indexnow_enabled                = true;
$indexnow_key                    = 'a7f2d4c91e6b4a8fb5c3d90712e4ab60';
$indexnow_endpoint               = 'https://api.indexnow.org/indexnow';
$indexnow_key_file_path          = dirname(__DIR__, 3) . '/' . $indexnow_key . '.txt';
$google_sitemap_resubmit_enabled = true;
$sitemap_candidates_by_domain    = [
    'extractmails.com' => [
        'https://extractmails.com/wp-sitemap.xml',
        'https://extractmails.com/sitemap_index.xml',
        'https://extractmails.com/sitemap.xml',
    ],
];

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);
set_time_limit(0);

require_once '/web/html/vendor/vendor/autoload.php';

// ======================================================
// 2. SETUP HTTP CLIENTS
// ======================================================
$client = new Google_Client();
$client->setAuthConfig($keyPath);
$client->setScopes(['https://www.googleapis.com/auth/webmasters']);
$token        = $client->fetchAccessTokenWithAssertion();
$access_token = $token['access_token'] ?? '';

$gscClient = new GuzzleHttp\Client([
    'headers' => [
        'Authorization' => 'Bearer ' . $access_token,
        'Accept'        => 'application/json',
    ],
    'timeout' => 30,
]);

$wpClient = new GuzzleHttp\Client([
    'verify'  => false,
    'timeout' => 30,
]);

// ======================================================
// 3. HELPERS
// ======================================================
function default_scan_state()
{
    return [
        'scan_started_at' => date('c'),
        'total_checked'   => 0,
        'unindexed_pages' => [],
        'sites'           => [],
    ];
}

function load_scan_state($state_file)
{
    if (! file_exists($state_file)) {
        return default_scan_state();
    }

    $decoded = json_decode(file_get_contents($state_file), true);

    if (! is_array($decoded) || ! isset($decoded['sites'])) {
        return default_scan_state();
    }

    $decoded['total_checked']   = (int) ($decoded['total_checked'] ?? 0);
    $decoded['unindexed_pages'] = is_array($decoded['unindexed_pages'] ?? null) ? $decoded['unindexed_pages'] : [];

    return $decoded;
}

function save_scan_state($state_file, $state)
{
    file_put_contents($state_file, json_encode($state, JSON_PRETTY_PRINT));
}

function ensure_site_state(&$state, $domain)
{
    $today = date('Y-m-d');

    if (! isset($state['sites'][ $domain ])) {
        $state['sites'][ $domain ] = [
            'next_page'          => 1,
            'next_offset'        => 0,
            'total_items'        => null,
            'total_pages'        => null,
            'complete'           => false,
            'checked_today'      => 0,
            'checked_today_date' => $today,
            'total_checked'      => 0,
            'issue_count'        => 0,
            'unindexed_count'    => 0,
            'other_issue_count'  => 0,
            'reindex_submitted_count' => 0,
            'reindex_failed_count'    => 0,
        ];
    }

    if (! isset($state['sites'][ $domain ]['unindexed_count'])) {
        $state['sites'][ $domain ]['unindexed_count'] = 0;
    }

    if (! isset($state['sites'][ $domain ]['other_issue_count'])) {
        $state['sites'][ $domain ]['other_issue_count'] = 0;
    }

    if (! isset($state['sites'][ $domain ]['reindex_submitted_count'])) {
        $state['sites'][ $domain ]['reindex_submitted_count'] = 0;
    }

    if (! isset($state['sites'][ $domain ]['reindex_failed_count'])) {
        $state['sites'][ $domain ]['reindex_failed_count'] = 0;
    }

    if (! isset($state['sites'][ $domain ]['next_offset'])) {
        $next_page = max(1, (int) ($state['sites'][ $domain ]['next_page'] ?? 1));
        $state['sites'][ $domain ]['next_offset'] = ($next_page - 1) * 100;
    }

    if (($state['sites'][ $domain ]['checked_today_date'] ?? '') !== $today) {
        $state['sites'][ $domain ]['checked_today']      = 0;
        $state['sites'][ $domain ]['checked_today_date'] = $today;
    }
}

function all_sites_complete($state, $target_sites)
{
    foreach ($target_sites as $domain => $gsc_property) {
        if (empty($state['sites'][ $domain ]['complete'])) {
            return false;
        }
    }

    return true;
}

function clean_page_title($title)
{
    if (is_array($title)) {
        $title = $title['rendered'] ?? 'Untitled';
    }

    $title = html_entity_decode((string) $title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $title = trim(strip_tags($title));

    return $title !== '' ? $title : 'Untitled';
}

function add_indexing_issue(&$state, $domain, $title, $url, $reason, $type = 'unindexed')
{
    $type = $type === 'other' ? 'other' : 'unindexed';

    $issue = [
        'domain'     => $domain,
        'title'      => clean_page_title($title),
        'url'        => $url,
        'type'       => $type,
        'reason'     => $reason,
        'checked_at' => date('c'),
    ];
    $issue['id'] = sha1($domain . '|' . $url . '|' . $issue['checked_at'] . '|' . count($state['unindexed_pages']));

    if ($type === 'unindexed') {
        $issue['reindex_status']       = 'pending';
        $issue['reindex_method']       = '';
        $issue['reindex_message']      = 'Waiting for automatic recrawl notification.';
        $issue['reindex_submitted_at'] = '';
    } else {
        $issue['reindex_status']       = 'not_applicable';
        $issue['reindex_method']       = '';
        $issue['reindex_message']      = 'Automatic recrawl notification is only sent for unindexed pages.';
        $issue['reindex_submitted_at'] = '';
    }

    $state['unindexed_pages'][] = $issue;
    $state['sites'][ $domain ]['issue_count'] = (int) ($state['sites'][ $domain ]['issue_count'] ?? 0) + 1;

    if ($type === 'unindexed') {
        $state['sites'][ $domain ]['unindexed_count'] = (int) ($state['sites'][ $domain ]['unindexed_count'] ?? 0) + 1;
    } else {
        $state['sites'][ $domain ]['other_issue_count'] = (int) ($state['sites'][ $domain ]['other_issue_count'] ?? 0) + 1;
    }

    return $issue;
}

function mark_issue_reindex_result(&$state, &$issue, $status, $method, $message)
{
    $issue['reindex_status']       = $status;
    $issue['reindex_method']       = $method;
    $issue['reindex_message']      = $message;
    $issue['reindex_submitted_at'] = date('c');

    foreach ($state['unindexed_pages'] as $index => $stored_issue) {
        if (($stored_issue['id'] ?? '') === ($issue['id'] ?? '')) {
            $state['unindexed_pages'][ $index ] = $issue;
            break;
        }
    }

    $domain = $issue['domain'] ?? '';

    if ($domain !== '' && isset($state['sites'][ $domain ])) {
        if ($status === 'submitted') {
            $state['sites'][ $domain ]['reindex_submitted_count'] = (int) ($state['sites'][ $domain ]['reindex_submitted_count'] ?? 0) + 1;
        } elseif ($status === 'failed') {
            $state['sites'][ $domain ]['reindex_failed_count'] = (int) ($state['sites'][ $domain ]['reindex_failed_count'] ?? 0) + 1;
        }
    }
}

function ensure_indexnow_key_file($key_file_path, $indexnow_key)
{
    if ($indexnow_key === '') {
        return [
            'success' => false,
            'message' => 'IndexNow key is empty.',
        ];
    }

    if (file_exists($key_file_path)) {
        $existing_key = trim((string) file_get_contents($key_file_path));

        if ($existing_key === $indexnow_key) {
            return [
                'success' => true,
                'message' => 'IndexNow key file already exists.',
            ];
        }
    }

    $directory = dirname($key_file_path);

    if (! is_dir($directory) || ! is_writable($directory)) {
        return [
            'success' => false,
            'message' => 'IndexNow key file could not be written to ' . $directory . '.',
        ];
    }

    $written = @file_put_contents($key_file_path, $indexnow_key);

    if ($written === false) {
        return [
            'success' => false,
            'message' => 'IndexNow key file write failed.',
        ];
    }

    return [
        'success' => true,
        'message' => 'IndexNow key file created.',
    ];
}

function submit_indexnow_urls($domain, $urls, $indexnow_key, $indexnow_endpoint, $indexnow_key_file_path, $httpClient)
{
    $urls = array_values(array_unique(array_filter($urls)));

    if (empty($urls)) {
        return [
            'success' => false,
            'method'  => 'IndexNow',
            'message' => 'No URLs to submit to IndexNow.',
        ];
    }

    $key_file_result = ensure_indexnow_key_file($indexnow_key_file_path, $indexnow_key);

    if (! $key_file_result['success']) {
        return [
            'success' => false,
            'method'  => 'IndexNow',
            'message' => $key_file_result['message'],
        ];
    }

    try {
        $response = $httpClient->post($indexnow_endpoint, [
            'http_errors' => false,
            'json' => [
                'host'        => $domain,
                'key'         => $indexnow_key,
                'keyLocation' => 'https://' . $domain . '/' . basename($indexnow_key_file_path),
                'urlList'     => $urls,
            ],
        ]);

        $status_code = (int) $response->getStatusCode();

        if ($status_code === 200 || $status_code === 202) {
            return [
                'success' => true,
                'method'  => 'IndexNow',
                'message' => 'IndexNow accepted ' . count($urls) . ' URL(s).',
            ];
        }

        return [
            'success' => false,
            'method'  => 'IndexNow',
            'message' => 'IndexNow returned HTTP ' . $status_code . '.',
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'method'  => 'IndexNow',
            'message' => 'IndexNow error: ' . $e->getMessage(),
        ];
    }
}

function get_sitemap_candidates($domain, $sitemap_candidates_by_domain)
{
    if (! empty($sitemap_candidates_by_domain[ $domain ]) && is_array($sitemap_candidates_by_domain[ $domain ])) {
        return $sitemap_candidates_by_domain[ $domain ];
    }

    return [
        'https://' . $domain . '/wp-sitemap.xml',
        'https://' . $domain . '/sitemap_index.xml',
        'https://' . $domain . '/sitemap.xml',
    ];
}

function find_accessible_sitemap($domain, $sitemap_candidates_by_domain, $httpClient)
{
    foreach (get_sitemap_candidates($domain, $sitemap_candidates_by_domain) as $sitemap_url) {
        try {
            $response = $httpClient->request('GET', $sitemap_url, [
                'http_errors' => false,
                'timeout'     => 20,
            ]);

            $status_code = (int) $response->getStatusCode();

            if ($status_code >= 200 && $status_code < 400) {
                return $sitemap_url;
            }
        } catch (Exception $e) {
            continue;
        }
    }

    return '';
}

function submit_google_sitemap_gsc($domain, $siteUrl, $gscClient, $wpClient, $sitemap_candidates_by_domain)
{
    $sitemap_url = find_accessible_sitemap($domain, $sitemap_candidates_by_domain, $wpClient);

    if ($sitemap_url === '') {
        return [
            'success' => false,
            'method'  => 'Google sitemap',
            'message' => 'No accessible sitemap was found for Google Search Console submission.',
        ];
    }

    try {
        $endpoint = 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($siteUrl) . '/sitemaps/' . rawurlencode($sitemap_url);
        $response = $gscClient->put($endpoint, [
            'http_errors' => false,
        ]);

        $status_code = (int) $response->getStatusCode();

        if ($status_code >= 200 && $status_code < 300) {
            return [
                'success' => true,
                'method'  => 'Google sitemap',
                'message' => 'Submitted sitemap to Google Search Console: ' . $sitemap_url,
            ];
        }

        return [
            'success' => false,
            'method'  => 'Google sitemap',
            'message' => 'Google Search Console sitemap submission returned HTTP ' . $status_code . '.',
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'method'  => 'Google sitemap',
            'message' => 'Google sitemap submission error: ' . $e->getMessage(),
        ];
    }
}

function auto_reindex_unindexed_issues(
    &$run_issues,
    &$state,
    $target_sites,
    $enabled,
    $indexnow_enabled,
    $indexnow_key,
    $indexnow_endpoint,
    $indexnow_key_file_path,
    $google_sitemap_resubmit_enabled,
    $sitemap_candidates_by_domain,
    $gscClient,
    $wpClient
) {
    if (! $enabled) {
        foreach ($run_issues as $index => $issue) {
            if (($issue['type'] ?? 'unindexed') === 'unindexed') {
                mark_issue_reindex_result($state, $run_issues[ $index ], 'skipped', '', 'Automatic recrawl notification is disabled.');
            }
        }

        return;
    }

    $urls_by_domain = [];

    foreach ($run_issues as $issue) {
        if (($issue['type'] ?? 'unindexed') !== 'unindexed') {
            continue;
        }

        $domain = $issue['domain'] ?? '';
        $url    = $issue['url'] ?? '';

        if ($domain !== '' && $url !== '') {
            $urls_by_domain[ $domain ][] = $url;
        }
    }

    foreach ($urls_by_domain as $domain => $urls) {
        $successful_methods = [];
        $failed_messages    = [];

        if ($indexnow_enabled) {
            $indexnow_result = submit_indexnow_urls($domain, $urls, $indexnow_key, $indexnow_endpoint, $indexnow_key_file_path, $wpClient);

            if ($indexnow_result['success']) {
                $successful_methods[] = $indexnow_result['method'];
            } else {
                $failed_messages[] = $indexnow_result['message'];
            }
        }

        if ($google_sitemap_resubmit_enabled && isset($target_sites[ $domain ])) {
            $sitemap_result = submit_google_sitemap_gsc($domain, $target_sites[ $domain ], $gscClient, $wpClient, $sitemap_candidates_by_domain);

            if ($sitemap_result['success']) {
                $successful_methods[] = $sitemap_result['method'];
            } else {
                $failed_messages[] = $sitemap_result['message'];
            }
        }

        if (! empty($successful_methods)) {
            $status  = 'submitted';
            $method  = implode(' + ', $successful_methods);
            $message = 'Automatic recrawl notification sent. ' . implode(' ', $failed_messages);
        } else {
            $status  = 'failed';
            $method  = 'IndexNow / Google sitemap';
            $message = ! empty($failed_messages) ? implode(' ', $failed_messages) : 'No automatic recrawl method was enabled.';
        }

        foreach ($run_issues as $index => $issue) {
            if (($issue['type'] ?? 'unindexed') === 'unindexed' && ($issue['domain'] ?? '') === $domain) {
                mark_issue_reindex_result($state, $run_issues[ $index ], $status, $method, trim($message));
            }
        }
    }
}

function check_url_indexing_gsc($url, $siteUrl, $gscClient)
{
    try {
        $response = $gscClient->post(
            'https://searchconsole.googleapis.com/v1/urlInspection/index:inspect',
            [
                'json' => [
                    'inspectionUrl' => $url,
                    'siteUrl'       => $siteUrl,
                ],
            ]
        );

        $result = json_decode($response->getBody(), true);
        $index  = $result['inspectionResult']['indexStatusResult'] ?? [];

        $verdict  = strtoupper($index['verdict'] ?? '');
        $coverage = $index['coverageState'] ?? 'Unknown Reason';

        if ($verdict === 'PASS') {
            return [
                'status'         => 'indexed',
                'coverage_state' => $coverage,
            ];
        }

        return [
            'status'         => 'not_indexed',
            'coverage_state' => $coverage,
        ];
    } catch (Exception $e) {
        return [
            'status'         => 'error',
            'coverage_state' => 'API Error: ' . $e->getMessage(),
        ];
    }
}

function fetch_wp_pages_batch($domain, $offset, $per_page, $wpClient)
{
    $rest_url = sprintf(
        'https://%s/wp-json/wp/v2/pages?per_page=%d&offset=%d&_fields=link,title',
        $domain,
        $per_page,
        $offset
    );

    $response    = $wpClient->request('GET', $rest_url);
    $pages       = json_decode($response->getBody(), true);
    $total_items = (int) $response->getHeaderLine('X-WP-Total');
    $total_pages = (int) $response->getHeaderLine('X-WP-TotalPages');

    return [
        'pages'       => is_array($pages) ? $pages : [],
        'total_items' => max(0, $total_items),
        'total_pages' => max(1, $total_pages),
    ];
}

function build_issue_rows_html($issues, $limit = null)
{
    $rows_html = '';
    $count     = 0;

    foreach ($issues as $issue) {
        if ($limit !== null && $count >= $limit) {
            break;
        }

        $clean_title  = esc_html($issue['title'] ?? 'Untitled');
        $clean_url    = esc_url($issue['url'] ?? '');
        $clean_type   = esc_html(($issue['type'] ?? 'unindexed') === 'other' ? 'Other Issue' : 'Unindexed');
        $clean_reason = esc_html($issue['reason'] ?? 'Unknown Reason');
        $clean_domain = esc_html($issue['domain'] ?? '');
        $clean_reindex_status  = esc_html(ucwords(str_replace('_', ' ', $issue['reindex_status'] ?? 'pending')));
        $clean_reindex_method  = esc_html($issue['reindex_method'] ?? '');
        $clean_reindex_message = esc_html($issue['reindex_message'] ?? '');
        $reindex_color = '#5f6368';

        if (($issue['reindex_status'] ?? '') === 'submitted') {
            $reindex_color = '#188038';
        } elseif (($issue['reindex_status'] ?? '') === 'failed') {
            $reindex_color = '#c5221f';
        }

        $rows_html .= "
        <tr>
            <td style='padding:10px;border:1px solid #ddd;font-weight:bold;'>{$clean_domain}</td>
            <td style='padding:10px;border:1px solid #ddd;'>{$clean_title}</td>
            <td style='padding:10px;border:1px solid #ddd;'><a href='{$clean_url}'>{$clean_url}</a></td>
            <td style='padding:10px;border:1px solid #ddd;'>{$clean_type}</td>
            <td style='padding:10px;border:1px solid #ddd;color:#c5221f;'>{$clean_reason}</td>
            <td style='padding:10px;border:1px solid #ddd;color:{$reindex_color};'><b>{$clean_reindex_status}</b><br>{$clean_reindex_method}<br>{$clean_reindex_message}</td>
        </tr>";

        $count++;
    }

    return $rows_html;
}

function build_site_summary_html($state)
{
    $rows_html = '';

    foreach ($state['sites'] as $domain => $site_state) {
        $clean_domain = esc_html($domain);
        $checked      = (int) ($site_state['total_checked'] ?? 0);
        $unindexed    = (int) ($site_state['unindexed_count'] ?? 0);
        $other_issues = (int) ($site_state['other_issue_count'] ?? 0);
        $reindex_submitted = (int) ($site_state['reindex_submitted_count'] ?? 0);
        $reindex_failed    = (int) ($site_state['reindex_failed_count'] ?? 0);

        $rows_html .= "
        <tr>
            <td style='padding:8px;border:1px solid #ddd;font-weight:bold;'>{$clean_domain}</td>
            <td style='padding:8px;border:1px solid #ddd;'>{$checked}</td>
            <td style='padding:8px;border:1px solid #ddd;color:#c5221f;'>{$unindexed}</td>
            <td style='padding:8px;border:1px solid #ddd;color:#b06000;'>{$other_issues}</td>
            <td style='padding:8px;border:1px solid #ddd;color:#188038;'>{$reindex_submitted}</td>
            <td style='padding:8px;border:1px solid #ddd;color:#c5221f;'>{$reindex_failed}</td>
        </tr>";
    }

    return $rows_html;
}

function create_issues_csv($issues)
{
    $tmp_path = tempnam(sys_get_temp_dir(), 'gsc_unindexed_');
    $csv_path = $tmp_path . '.csv';
    rename($tmp_path, $csv_path);

    $handle = fopen($csv_path, 'w');
    fputcsv($handle, [ 'Domain', 'Page Title', 'URL', 'Issue Type', 'Reason', 'Checked At', 'Reindex Status', 'Reindex Method', 'Reindex Message', 'Reindex Submitted At' ]);

    foreach ($issues as $issue) {
        fputcsv($handle, [
            $issue['domain'] ?? '',
            $issue['title'] ?? '',
            $issue['url'] ?? '',
            ($issue['type'] ?? 'unindexed') === 'other' ? 'Other Issue' : 'Unindexed',
            $issue['reason'] ?? '',
            $issue['checked_at'] ?? '',
            $issue['reindex_status'] ?? '',
            $issue['reindex_method'] ?? '',
            $issue['reindex_message'] ?? '',
            $issue['reindex_submitted_at'] ?? '',
        ]);
    }

    fclose($handle);

    return $csv_path;
}

// ======================================================
// 4. FETCH, PROCESS, AND EMAIL EVERY BATCH UNTIL COMPLETE
// ======================================================
require_once '/web/html/wp-1click.com/wp-load.php';

$state        = load_scan_state($scan_state_file);
$batch_number = 0;

while (! all_sites_complete($state, $target_sites)) {
    $batch_number++;
    $run_checked = 0;
    $run_issues  = [];

    foreach ($target_sites as $domain => $gsc_property) {
        ensure_site_state($state, $domain);

        if (! empty($state['sites'][ $domain ]['complete'])) {
            continue;
        }

        $site_processed_this_run = 0;

        while ($site_processed_this_run < $max_urls_per_site_per_run) {
            ensure_site_state($state, $domain);

            $site_state       = &$state['sites'][ $domain ];
            $remaining_today  = $max_urls_per_site_per_day - (int) $site_state['checked_today'];
            $remaining_run    = $max_urls_per_site_per_run - $site_processed_this_run;
            $request_batch_size = min($wp_page_batch_size, $remaining_today, $remaining_run);

            if ($request_batch_size <= 0) {
                break;
            }

            try {
                $batch = fetch_wp_pages_batch($domain, (int) $site_state['next_offset'], $request_batch_size, $wpClient);
            } catch (Exception $e) {
                $run_issues[] = add_indexing_issue(
                    $state,
                    $domain,
                    'REST API Connection Failed',
                    "https://{$domain}",
                    'Could not fetch pages from this site: ' . $e->getMessage(),
                    'other'
                );

                $site_state['complete'] = true;
                break;
            }

            $site_state['total_items'] = $batch['total_items'];
            $site_state['total_pages'] = $batch['total_pages'];

            if (empty($batch['pages'])) {
                $site_state['complete'] = true;
                break;
            }

            foreach ($batch['pages'] as $page) {
                $page_url   = $page['link'] ?? '';
                $page_title = $page['title'] ?? 'Untitled';

                if (empty($page_url)) {
                    continue;
                }

                $state['total_checked']++;
                $site_state['total_checked']++;
                $site_state['checked_today']++;
                $site_processed_this_run++;
                $run_checked++;

                $result = check_url_indexing_gsc($page_url, $gsc_property, $gscClient);

                if ($result['status'] === 'not_indexed' || $result['status'] === 'error') {
                    $issue_type = $result['status'] === 'error' ? 'other' : 'unindexed';
                    $run_issues[] = add_indexing_issue($state, $domain, $page_title, $page_url, $result['coverage_state'], $issue_type);
                }
            }

            $site_state['next_offset'] = (int) $site_state['next_offset'] + count($batch['pages']);
            $site_state['next_page']   = (int) floor($site_state['next_offset'] / max(1, $wp_page_batch_size)) + 1;

            if ((int) $site_state['next_offset'] >= (int) $site_state['total_items']) {
                $site_state['complete'] = true;
                break;
            }
        }
    }

    auto_reindex_unindexed_issues(
        $run_issues,
        $state,
        $target_sites,
        $auto_reindex_unindexed_pages,
        $indexnow_enabled,
        $indexnow_key,
        $indexnow_endpoint,
        $indexnow_key_file_path,
        $google_sitemap_resubmit_enabled,
        $sitemap_candidates_by_domain,
        $gscClient,
        $wpClient
    );

    save_scan_state($scan_state_file, $state);
    $scan_complete = all_sites_complete($state, $target_sites);

    if ($run_checked === 0 && count($run_issues) === 0 && ! $scan_complete) {
        echo "No URLs processed. Daily quota or per-run limit may be reached. Scan will resume later.\n";
        exit;
    }

    $unindexed_pages = $run_issues;
    $issue_count     = count($run_issues);
    $run_unindexed_count = count(array_filter($run_issues, function ($issue) {
        return ($issue['type'] ?? 'unindexed') === 'unindexed';
    }));
    $run_other_issue_count = $issue_count - $run_unindexed_count;
    $run_reindex_submitted_count = count(array_filter($run_issues, function ($issue) {
        return ($issue['reindex_status'] ?? '') === 'submitted';
    }));
    $run_reindex_failed_count = count(array_filter($run_issues, function ($issue) {
        return ($issue['reindex_status'] ?? '') === 'failed';
    }));
    $total_checked   = (int) $state['total_checked'];

    $send_csv       = $issue_count > $inline_email_limit;
    $display_limit  = $send_csv ? $email_preview_limit : null;
    $rows_html      = $issue_count > 0 ? build_issue_rows_html($unindexed_pages, $display_limit) : '';
    $summary_html   = build_site_summary_html($state);
    $attachments    = [];
    $csv_attachment = null;

    if ($send_csv) {
        $csv_attachment = create_issues_csv($unindexed_pages);
        $attachments[]  = $csv_attachment;
    }

    $subject_prefix = $scan_complete ? 'EXTRACTMAILS - Final Page Indexing Batch Report' : 'EXTRACTMAILS - Page Indexing Batch Report';
    $subject = $issue_count > 0
        ? "{$subject_prefix} #{$batch_number}: {$run_checked} Checked, {$run_unindexed_count} Unindexed, {$run_reindex_submitted_count} Reindex Requests, {$run_other_issue_count} Other Issues"
        : "{$subject_prefix} #{$batch_number}: {$run_checked} Checked, 0 Issues";

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        "From: {$from_name} <{$from_email}>",
        "Reply-To: {$from_name} <{$from_email}>",
    ];

    if ($issue_count > 0) {
        $preview_note = $send_csv
            ? "<p>The full list is attached as CSV. Showing the first <b>{$email_preview_limit}</b> issues below.</p>"
            : "<p>Showing all detected indexing issues below.</p>";

        $issues_section = "
            {$preview_note}
            <table style='width:100%; border-collapse:collapse; font-size:13px; text-align:left;'>
                <tr style='background:#f1f3f4;'>
                    <th style='padding:10px;border:1px solid #ddd;'>Site</th>
                    <th style='padding:10px;border:1px solid #ddd;'>Page Title</th>
                    <th style='padding:10px;border:1px solid #ddd;'>URL</th>
                    <th style='padding:10px;border:1px solid #ddd;'>Issue Type</th>
                    <th style='padding:10px;border:1px solid #ddd;'>Reason</th>
                    <th style='padding:10px;border:1px solid #ddd;'>Reindex Request</th>
                </tr>
                {$rows_html}
            </table>";
    } else {
        $issues_section = "<p>No unindexed pages or API errors were found in this batch.</p>";
    }

    $completion_note = $scan_complete
        ? '<p><b>Status:</b> This was the final batch. Full scan is complete.</p>'
        : '<p><b>Status:</b> This batch is complete. The script is continuing with the next batch.</p>';

    $email_body = "
    <div style='font-family: Arial, sans-serif;'>
        <h2>EXTRACTMAILS - Page Indexing Report</h2>
        <p>This batch checked <b>{$run_checked}</b> pages and found <b>{$run_unindexed_count}</b> unindexed pages and <b>{$run_other_issue_count}</b> other issues.</p>
        <p>Automatic recrawl notifications submitted for <b>{$run_reindex_submitted_count}</b> unindexed pages. Failed notifications: <b>{$run_reindex_failed_count}</b>.</p>
        <p>Total checked in this scan cycle so far: <b>{$total_checked}</b>.</p>
        {$completion_note}
        <h3>Site Summary</h3>
        <table style='width:100%; border-collapse:collapse; font-size:13px; text-align:left; margin-bottom:18px;'>
            <tr style='background:#f1f3f4;'>
                <th style='padding:8px;border:1px solid #ddd;'>Site</th>
                <th style='padding:8px;border:1px solid #ddd;'>Checked</th>
                <th style='padding:8px;border:1px solid #ddd;'>Unindexed</th>
                <th style='padding:8px;border:1px solid #ddd;'>Other Issues</th>
                <th style='padding:8px;border:1px solid #ddd;'>Reindex Sent</th>
                <th style='padding:8px;border:1px solid #ddd;'>Reindex Failed</th>
            </tr>
            {$summary_html}
        </table>
        {$issues_section}
    </div>";

    $sent = wp_mail($alert_email, $subject, $email_body, $headers, $attachments);

    if ($csv_attachment && file_exists($csv_attachment)) {
        unlink($csv_attachment);
    }

    if (! $sent) {
        echo "Batch {$batch_number} completed, but wp_mail() failed. State file kept so the scan can continue next run.\n";
        exit;
    }

    echo "Batch {$batch_number} completed. Checked {$run_checked} URLs. Report email sent successfully.\n";

    if ($scan_complete) {
        if (file_exists($scan_state_file)) {
            unlink($scan_state_file);
        }

        echo "Full scan completed.\n";
        exit;
    }
}
