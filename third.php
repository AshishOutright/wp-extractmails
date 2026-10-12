
<?php

session_start();
if($_REQUEST['test'] == 1){
    echo '<hr/>Session Data';
    echo '<pre>';
    print_r( $_SESSION );
    echo '</pre>';
    echo '</hr>';
    session_destroy();
    die('testing');
}
define('CLIENT_ID', '842559391916-i2hq0thd3hnsj90mr3mvrr4i4mtqoou3.apps.googleusercontent.com');
define('CLIENT_SECRET', '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');
define('REDIRECT_URI', 'https://www.guestpostcrm.com/rightee/third.php');
define('SCOPE',
    'https://www.googleapis.com/auth/userinfo.email ' .
    'https://www.googleapis.com/auth/userinfo.profile ' .
    'https://www.googleapis.com/auth/gmail.send ' .
    'https://www.googleapis.com/auth/gmail.modify ' .
    'https://www.googleapis.com/auth/pubsub'
);

define('API_KEY', 'FldBjAIfoBo2UTcBAezvTOQg9');
define('API_URL', 'https://crm.outrightsystems.org/index.php?entryPoint=get_post_all&action_type=post_data');
define('LEADS_CHECK_URL', 'https://crm.outrightsystems.org/index.php?entryPoint=get_post_all&action_type=get_data');

define('AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

function makeCurlRequest($url, $options = [])
{
    $ch = curl_init($url);
    $default = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
    ];
    curl_setopt_array($ch, $options + $default);
    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($response === false) {
        throw new Exception("cURL Error: " . $err);
    }
    return ['response' => $response, 'code' => $http];
}

function displayError($msg)
{
    http_response_code(400);
    echo "<h3 style='font-family:sans-serif;color:red;text-align:center;margin-top:100px;'>{$msg}</h3>";
    exit;
}

// === CHECK IF CONSENT ALREADY GIVEN ===
if (isset($_SESSION['gmail_consent_completed']) && !isset($_GET['code'])) {
    // User already gave consent, redirect to app directly
    header("Location: https://appstage.guestpostcrm.com/" . "?email=" . urlencode($_SESSION['consent_email']));
    exit;
}

// === STEP 1: Redirect to Google Consent ===
if (!isset($_GET['code'])) {
    $state = bin2hex(random_bytes(12));
    $_SESSION['oauth_state'] = $state;

    $authParams = http_build_query([
        'client_id' => CLIENT_ID,
        'redirect_uri' => REDIRECT_URI,
        'response_type' => 'code',
        'scope' => SCOPE,
        'access_type' => 'offline',
        'prompt' => 'consent',
        'state' => $state
    ]);

    header("Location: " . AUTH_URL . "?" . $authParams);
    exit;
}

// === STEP 2: Handle OAuth Callback ===
if (isset($_GET['error'])) {
    displayError("Google Authorization Error: " . htmlspecialchars($_GET['error_description'] ?? 'Unknown'));
}
// if(empty($_REQUEST['force'])){
// if (!isset($_GET['state']) || $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')) {
//     displayError("Invalid or missing state (possible CSRF attack)");
// }
// }


$code = $_GET['code'];

// === STEP 3: Exchange Code for Tokens ===
$tokenRequest = [
    'code' => $code,
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,
    'redirect_uri' => REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$tokenResult = makeCurlRequest(TOKEN_URL, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($tokenRequest),
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
]);
$tokenData = json_decode($tokenResult['response'], true);
if (empty($tokenData['access_token'])) {
    displayError("Failed to obtain access token.");
}

$accessToken = $tokenData['access_token'];
$refreshToken = $tokenData['refresh_token'] ?? '';

// === STEP 4: Get User Info ===
$userResult = makeCurlRequest(USERINFO_URL, [
    CURLOPT_HTTPHEADER => ["Authorization: Bearer {$accessToken}"]
]);
$userinfo = json_decode($userResult['response'], true);
if (empty($userinfo['email'])) {
    displayError("Could not retrieve email from Google.");
}
$userEmail = $userinfo['email'];
$userName = $userinfo['name'] ?? 'Unknown User';

// === STEP 5: Check if User Exists in CRM ===

    $checkPayload = [
    "module" => "outr_inbound_settings",
    "where"  => [
        "user_email_address" => $userEmail,
        "deleted" => 0
    ],
    "limit"  => 1
];
 
 
$checkResult = makeCurlRequest(LEADS_CHECK_URL, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($checkPayload),
    CURLOPT_HTTPHEADER => [
        "X-Api-Key: " . API_KEY,
        "Content-Type: application/json"
    ]
]);
$existingData = json_decode($checkResult['response'], true);
$existingId = $existingData[0]['id'] ?? null;

// === STEP 6: Save or Update CRM Record ===
$payload = [
    "parent_bean" => [
        "module" => "outr_inbound_settings",
        "id" => $existingId,
        "name" => $userName,
        "user_email_address" => $userEmail,
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "client_id_gmail" => CLIENT_ID,
        "secret_id" => CLIENT_SECRET,
        "active_status" => 1,
        "use_type" => "consent",
        "description" => "User gave Gmail consent on " . date('Y-m-d H:i:s'),
        "topic_name" => "projects/rightee-452204/topics/trynow",
        "flag" => 1
    ]
];

//  $domain = explode('@', $userEmail);
//    $db = $domain[0]. '_gp_crm';
// $apiUrl = "https://trynow.guestpostcrm.com/index.php?entryPoint=get_post_all&action_type=post_data&db_name=" .$db;

// // 10 sec dealy to wait for crm to create db and tables
//    sleep(10);
// $save = makeCurlRequest($apiUrl, [
//     CURLOPT_POST => true,
//     CURLOPT_POSTFIELDS => json_encode($payload),
//     CURLOPT_HTTPHEADER => [
//         "X-Api-Key: " . API_KEY,
//         "Content-Type: application/json"
//     ]
// ]);


$save = makeCurlRequest(API_URL, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        "X-Api-Key: " . API_KEY,
        "Content-Type: application/json"
    ]
]);


if ($save['code'] >= 200 && $save['code'] < 300) {

    $_SESSION['gmail_consent_completed'] = true;
    $_SESSION['consent_time'] = time();
    $_SESSION['consent_email'] = $userEmail;

    // Fire-and-forget server-side hit
    // $ch = curl_init('https://crm.outrightsystems.org/index.php?entryPoint=cron_signup');
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_TIMEOUT, 2); // don't slow redirect
    // curl_exec($ch);
    // curl_close($ch);

    // Redirect user
    header("Location: https://appstage.guestpostcrm.com/"."?email=" . urlencode($userEmail));
    exit;
}
 else {
    displayError("Failed to save user consent data to CRM.");
}
?>