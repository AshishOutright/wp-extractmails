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

define('CLIENT_ID', '51c4a942-4d08-438f-880c-3befb48041f3');
define('CLIENT_SECRET', 'KW88Q~dwNmj4GGjtSUdyOBHK9EWQEexHH7jTyaMC');
define('TENANT_ID', 'common'); // or your tenant id

define('REDIRECT_URI', 'https://www.guestpostcrm.com/rightee/microsoft.php');

define('AUTH_URL', 'https://login.microsoftonline.com/' . TENANT_ID . '/oauth2/v2.0/authorize');
define('TOKEN_URL', 'https://login.microsoftonline.com/' . TENANT_ID . '/oauth2/v2.0/token');
define('USERINFO_URL', 'https://graph.microsoft.com/v1.0/me');

define('API_KEY', 'FldBjAIfoBo2UTcBAezvTOQg9');
define('API_URL', 'https://crm.outrightsystems.org/index.php?entryPoint=get_post_all&action_type=post_data');
define('LEADS_CHECK_URL', 'https://crm.outrightsystems.org/index.php?entryPoint=get_post_all&action_type=get_data');

function makeCurlRequest($url, $options = [])
{
    $ch = curl_init($url);

    $defaults = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ];

    curl_setopt_array($ch, $options + $defaults);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    if ($response === false) {
        throw new Exception($error);
    }

    return [
        'response' => $response,
        'code' => $httpCode
    ];
}

function displayError($msg)
{
    die("<h3 style='color:red;text-align:center'>{$msg}</h3>");
}

/*
|--------------------------------------------------------------------------
| Already Authorized
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['microsoft_consent_completed']) && !isset($_GET['code'])) {
    header(
        "Location: https://appstage.guestpostcrm.com/?email=" .
        urlencode($_SESSION['consent_email'])
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| Step 1 - Login
|--------------------------------------------------------------------------
*/

if (!isset($_GET['code'])) {

    $state = bin2hex(random_bytes(16));

    $_SESSION['oauth_state'] = $state;

    $params = [
        'client_id' => CLIENT_ID,
        'response_type' => 'code',
        'redirect_uri' => REDIRECT_URI,
        'response_mode' => 'query',
        'scope' => implode(' ', [
            'offline_access',
            'User.Read',
            'Mail.ReadWrite',
            'Mail.Send'
        ]),
        'state' => $state
    ];

    header("Location: " . AUTH_URL . "?" . http_build_query($params));
    exit;
}

/*
|--------------------------------------------------------------------------
| Error Check
|--------------------------------------------------------------------------
*/

if (isset($_GET['error'])) {
    displayError($_GET['error_description'] ?? $_GET['error']);
}

if (
    !isset($_GET['state']) ||
    $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')
) {
    displayError('Invalid State');
}

/*
|--------------------------------------------------------------------------
| Step 2 - Get Token
|--------------------------------------------------------------------------
*/

$tokenResponse = makeCurlRequest(TOKEN_URL, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'code' => $_GET['code'],
        'redirect_uri' => REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded'
    ]
]);

$tokenData = json_decode($tokenResponse['response'], true);

if (empty($tokenData['access_token'])) {
    displayError('Unable to get access token');
}

$accessToken = $tokenData['access_token'];
$refreshToken = $tokenData['refresh_token'] ?? '';
$subscriptionId = $tokenData['subscription_id'] ?? '';
/*
|--------------------------------------------------------------------------
| Step 3 - Get User
|--------------------------------------------------------------------------
*/

$userResponse = makeCurlRequest(USERINFO_URL, [
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$accessToken}"
    ]
]);

$user = json_decode($userResponse['response'], true);

$userEmail = $user['mail']
    ?? $user['userPrincipalName']
    ?? '';

$userName = $user['displayName'] ?? '';

if (!$userEmail) {
    displayError('Email not found');
}

/*
|--------------------------------------------------------------------------
| Check Existing CRM Record
|--------------------------------------------------------------------------
*/

$checkPayload = [
    "module" => "outr_inbound_settings",
    "where" => [
        "user_email_address" => $userEmail,
        "deleted" => 0
    ],
    "limit" => 1
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
$existingId = $existingData[0]['id'] ?? '';

/*
|--------------------------------------------------------------------------
| Save CRM
|--------------------------------------------------------------------------
*/

$payload = [
    "parent_bean" => [
        "module" => "outr_inbound_settings",
        "id" => $existingId,
        "name" => $userName,
        "user_email_address" => $userEmail,
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "client_id" => CLIENT_ID,
        "secret_id" => CLIENT_SECRET,
        "active_status" => 1,
        "use_type" => "use_outlook",
        "description" => "Microsoft Consent on " . date('Y-m-d H:i:s'),
        "flag" => 1, 
        "outlook_user_id" => $subscriptionId,

    ]
];

$save = makeCurlRequest(API_URL, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        "X-Api-Key: " . API_KEY,
        "Content-Type: application/json"
    ]
]);

if ($save['code'] >= 200 && $save['code'] < 300) {

    $_SESSION['microsoft_consent_completed'] = true;
    $_SESSION['consent_email'] = $userEmail;

    header(
        "Location: https://appstage.guestpostcrm.com/?email=" .
        urlencode($userEmail)
    );
    exit;
}

displayError('Failed to save Microsoft account');

?>