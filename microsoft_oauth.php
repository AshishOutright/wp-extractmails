<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

/**
 * Microsoft OAuth Callback Handler
 * 
 * This file should be placed at the root of your WordPress installation as microsoft_oauth.php
 */

// Your Microsoft OAuth credentials
$client_id = 'f8344f13-2784-41c2-93d9-d7c8ad3b4be3';
$client_secret = 'ieE8Q~zwfUXmcYWAKV1LyJfyYcP5YyRyC7dUnceQ';
$redirect_uri = 'https://www.extractmails.com/microsoft_oauth.php';

// Get the authorization code
$code = isset($_GET['code']) ? $_GET['code'] : '';

if (empty($code)) {
    echo "Error: No authorization code received.";
    exit;
}

// Exchange the authorization code for an access token
$token_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

$token_data = array(
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'code' => $code,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
);

// Use cURL directly for more control
$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded'
));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$token_response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Debug information via Google Chat
sendToGoogleChat(var_export(json_decode($token_response, true), true));

if ($http_code != 200) {
    echo "Error: Failed to get access token. HTTP Code: " . $http_code;
    exit;
}

$token_data = json_decode($token_response, true);

if (!isset($token_data['access_token'])) {
    echo "Error: Failed to get access token: " . (isset($token_data['error_description']) 
         ? $token_data['error_description'] : 'Unknown error');
    exit;
}

$access_token = $token_data['access_token'];

// Use the access token to get user information
$graph_url = 'https://graph.microsoft.com/v1.0/me';

$ch = curl_init($graph_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$graph_response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    echo "Error: Failed to get user data. HTTP Code: " . $http_code;
    exit;
}

$user_data = json_decode($graph_response, true);

// Store the user data in the session for later use
session_start();
$_SESSION['microsoft_oauth_user_data'] = $user_data;

// Now redirect to a WordPress page that will handle the login
// At the end of microsoft_oauth.php, instead of using sessions:
$encoded_data = base64_encode(json_encode($user_data));
header('Location: ' . 'https://www.extractmails.com/microsoft-callback/?user_data=' . urlencode($encoded_data));

exit;

/**
 * Send debug information to Google Chat
 */
function sendToGoogleChat($messageText) {
    $webhookUrl = 'https://chat.googleapis.com/v1/spaces/AAAA1btsUiY/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=yRGA6GisbTMw2nxLM-Yq94rCX_IqGkOP1fKzzk_-1d8';
    $postData = json_encode([
        'text' => $messageText
    ]);

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'response' => $response
    ];
}