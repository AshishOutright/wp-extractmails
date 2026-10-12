<?php
session_start();
if (isset($_REQUEST['test']) && $_REQUEST['test'] == 1) {
    echo '<hr/>Session Data';
    echo '<pre>';
    print_r( $_SESSION );
    echo '</pre>';
    echo '</hr>';
    session_destroy();
    die('testing');
}
if (isset($_GET['saas_type']) ) {
   
    $_SESSION['saas_type'] = $_GET['saas_type'];
}


// ────────────────────────────────────────────────
//   CONFIGURATION - ADD YOUR KEYS HERE
// ────────────────────────────────────────────────

define('GOOGLE_CLIENT_ID',     '842559391916-i2hq0thd3hnsj90mr3mvrr4i4mtqoou3.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI',  'https://www.extractmails.com/google.php');

define('MICROSOFT_CLIENT_ID',     '51c4a942-4d08-438f-880c-3befb48041f3');
define('MICROSOFT_CLIENT_SECRET', 'KW88Q~dwNmj4GGjtSUdyOBHK9EWQEexHH7jTyaMC');
define('MICROSOFT_REDIRECT_URI',  'https://www.extractmails.com/google.php');

define('COMMON_AUTHORITY', 'https://login.microsoftonline.com/common');
define('REDIRECT_URI', 'https://www.extractmails.com/google.php');

// ────────────────────────────────────────────────
//   ENDPOINTS
// ────────────────────────────────────────────────

define('GOOGLE_AUTH_URL',    'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL',   'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL','https://www.googleapis.com/oauth2/v2/userinfo');

define('MS_AUTH_URL',     COMMON_AUTHORITY . '/oauth2/v2.0/authorize');
define('MS_TOKEN_URL',    COMMON_AUTHORITY . '/oauth2/v2.0/token');
define('MS_USERINFO_URL', 'https://graph.microsoft.com/oidc/userinfo');

define('GOOGLE_SCOPE', 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');
define('MS_SCOPE', 'openid profile email');

// ────────────────────────────────────────────────
//   HELPERS
// ────────────────────────────────────────────────

function generateCSRFToken() {
    if (empty($_SESSION['oauth_state'])) {
        $_SESSION['oauth_state'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['oauth_state'];
}
 function getRealIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
function verifyCSRFToken($token) {
    return isset($_SESSION['oauth_state']) && hash_equals($_SESSION['oauth_state'], $token);
}

function makeCurlRequest($url, $options = []) {
    $ch = curl_init($url);
    $defaultOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ];
    curl_setopt_array($ch, $options + $defaultOptions);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($response === false) {
        throw new Exception("cURL Error: " . $error);
    }
    return ['response' => $response, 'http_code' => $httpCode];
}

function displayError($message, $details = '') {
    http_response_code(400);
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - ExtractMails</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; text-align: center; }
        .error-box { background: #fee; border: 2px solid #c33; border-radius: 8px; padding: 20px; }
        .error-icon { font-size: 48px; color: #c33; }
        .error-title { color: #c33; margin: 10px 0; }
        .back-link { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-icon">❌</div>
        <h2 class="error-title">Error</h2>
        <p>{$message}</p>
        {$details}
        <a href="{$_SERVER['PHP_SELF']}" class="back-link">Try Again</a>
    </div>
</body>
</html>
HTML;
    exit;
}

// ────────────────────────────────────────────────
//   AJAX PROCESSING ENDPOINT
//   Called by JS after overlay is shown.
//   Returns JSON: { redirect: "url" } or { error: "msg" }
// ────────────────────────────────────────────────

if (isset($_GET['ajax_process']) && isset($_GET['code'])) {
    header('Content-Type: application/json');

    try {
        $code  = $_GET['code'];
        $state = $_GET['state'] ?? '';

        if (!verifyCSRFToken($state)) {
            echo json_encode(['error' => 'Invalid state parameter.']);
            exit;
        }

        // ── Try Google token exchange ──
        $provider     = 'google';
        $tokenRequest = [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code'
        ];
        $result    = makeCurlRequest(GOOGLE_TOKEN_URL, [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => http_build_query($tokenRequest),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);
        $tokenData = json_decode($result['response'], true);

        // ── Fallback: Microsoft ──
        if (empty($tokenData['access_token'])) {
            $provider     = 'microsoft';
            $tokenRequest = [
                'code'          => $code,
                'client_id'     => MICROSOFT_CLIENT_ID,
                'client_secret' => MICROSOFT_CLIENT_SECRET,
                'redirect_uri'  => MICROSOFT_REDIRECT_URI,
                'grant_type'    => 'authorization_code',
                'scope'         => MS_SCOPE
            ];
            $result    = makeCurlRequest(MS_TOKEN_URL, [
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => http_build_query($tokenRequest),
                CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
            ]);
            $tokenData = json_decode($result['response'], true);

            if (empty($tokenData['access_token'])) {
                $errorMsg = $tokenData['error_description'] ?? 'Failed to obtain access token';
                echo json_encode(['error' => $errorMsg]);
                exit;
            }
        }

        $accessToken = $tokenData['access_token'];

        // ── Fetch user info ──
        $userinfoUrl = ($provider === 'google') ? GOOGLE_USERINFO_URL : MS_USERINFO_URL;
        $result      = makeCurlRequest($userinfoUrl, [
            CURLOPT_HTTPHEADER => ["Authorization: Bearer {$accessToken}"]
        ]);
        $userinfo = json_decode($result['response'], true);

        if ($provider === 'google') {
            $email   = filter_var($userinfo['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $name    = $userinfo['name'] ?? 'Unknown User';
            $picture = $userinfo['picture'] ?? '';
        } else {
            $email   = filter_var($userinfo['email'] ?? $userinfo['preferred_username'] ?? '', FILTER_VALIDATE_EMAIL);
            $name    = $userinfo['name'] ?? (($userinfo['given_name'] ?? '') . ' ' . ($userinfo['family_name'] ?? ''));
            $picture = $userinfo['picture'] ?? '';
        }

        if (!$email) {
            echo json_encode(['error' => 'Could not retrieve a valid email.']);
            exit;
        }

        // ── Store session ──
        $_SESSION['provider']              = $provider;
        $_SESSION['provider_email']        = $email;
        $_SESSION['provider_name']         = $name;
        $_SESSION['provider_picture']      = $picture;
        $_SESSION['basic_login_completed'] = true;
        $_SESSION['login_time']            = time();

        // ── Hit CRM entry point ──
       $url = "https://crm.outrightsystems.org/index.php?entryPoint=trynow"
    . "&email=" . urlencode($email)
    . "&name=" . urlencode($name)
    . "&provider=" . urlencode($provider)
    . "&ip=" . urlencode(getRealIP())
    . "&saas_type=ExtractMails";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_FOLLOWLOCATION => true,
]);

$response = curl_exec($ch);
if ($response === false) {
    error_log('cURL Error: ' . curl_error($ch));
}

curl_close($ch);

        // ── Build redirect URL ──
        $params = ['name' => $name, 'email' => $email];
        if ($provider === 'google') {
            $params['google_login'] = '1';
        } else {
            $params['microsoft_login'] = '1';
        }
        $redirectUrl = 'https://app.extractmails.com/auth/callback/?' . http_build_query($params);

        echo json_encode(['redirect' => $redirectUrl]);
        exit;

    } catch (Exception $e) {
        error_log("OAuth AJAX Error: " . $e->getMessage());
        echo json_encode(['error' => 'An unexpected error occurred. Please try again.']);
        exit;
    }
}

// ────────────────────────────────────────────────
//   LOGIC
// ────────────────────────────────────────────────

try {
    // Logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: " . REDIRECT_URI);
        exit;
    }

    // Already fully logged in → go to app
    if (isset($_SESSION['basic_login_completed']) && isset($_SESSION['gmail_consent_completed'])) {
        header("Location: https://app.extractmails.com/auth/callback/?email=" . urlencode($_SESSION['provider_email']));
        exit;
    }

    // Basic login done → redirect to signup
    if (isset($_SESSION['basic_login_completed']) &&
        isset($_SESSION['provider_email']) &&
        !isset($_GET['code']) &&
        !isset($_GET['error'])) {

        $sessionAge = time() - ($_SESSION['login_time'] ?? 0);
        if ($sessionAge < 3600) {
            $params = ['name' => $_SESSION['provider_name'], 'email' => $_SESSION['provider_email']];
            if ($_SESSION['provider'] === 'google') {
                $params['google_login'] = '1';
            } else if ($_SESSION['provider'] === 'microsoft') {
                $params['microsoft_login'] = '1';
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => "https://crm.outrightsystems.org/index.php?entryPoint=trynow&email=" . urlencode($_SESSION['provider_email'])."&name=" . urlencode($_SESSION['provider_name'])."&provider=" . urlencode($_SESSION['provider'])."&ip=" . urlencode(getRealIP()) . "&saas_type=ExtractMails",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_FOLLOWLOCATION => true
            ]);
            curl_exec($ch);
            curl_close($ch);

            header("Location: " . 'https://app.extractmails.com/auth/callback/?' . http_build_query($params));
            exit;
        }
    }

    // ─── STEP 1: Show login page ───
    if (!isset($_GET['code']) && !isset($_GET['error'])) {
        
        $state = generateCSRFToken();

        $googleParams = http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => GOOGLE_SCOPE,
            'access_type'   => 'online',
            'state'         => $state
        ]);
        $googleAuthUrl = GOOGLE_AUTH_URL . '?' . $googleParams;

        $msParams = http_build_query([
            'client_id'     => MICROSOFT_CLIENT_ID,
            'redirect_uri'  => MICROSOFT_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => MS_SCOPE,
            'state'         => $state
        ]);
        $msAuthUrl = MS_AUTH_URL . '?' . $msParams;

        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ExtractMails</title>
</head>
<body>
    <main class="auth-shell">
        <section class="signup-box" aria-label="ExtractMails account connection">
            <div class="brand-row">
                <img class="logo" src="https://www.extractmails.com/wp-content/uploads/2024/12/mailextracter-final-domain-logo-188x44.png" alt="ExtractMails Logo">
                <span class="secure-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Secure connection
                </span>
            </div>

            <div class="intro-row">
                <div>
                    <p class="eyebrow">AI email workspace</p>
                    <h1>Welcome to ExtractMails</h1>
                    <p class="lead">Connect your account to manage outreach emails, detect publisher replies, generate AI-powered responses, and track offers, deals, and orders — all from one place.</p>
                </div>
                <img class="top-gif" src="https://i.gifer.com/7efs.gif" alt="Email automation animation">
            </div>

            <div class="content-grid">
                <div class="consent-panel">
                    <p class="section-title">You are allowing ExtractMails to</p>
                    <ul class="consent-list">
                        <li><span>&#10003;</span>Read outreach conversations</li>
                        <li><span>&#10003;</span>Detect publisher replies</li>
                        <li><span>&#10003;</span>Organize email threads automatically</li>
                        <li><span>&#10003;</span>Track Offers, Deals, And Orders </li>
                        <li><span>&#10003;</span>Generate AI-powered replies &amp; summaries instantly</li>
                    </ul>
                    <p class="security-note">Your credentials remain secure, and access can be revoked anytime.</p>
                </div>

                <div class="action-panel">
                    <a href="{$googleAuthUrl}" class="auth-btn google-btn">
                        <span class="google-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 18 18">
                                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                                <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.039l3.007-2.332z"/>
                                <path fill="#EA4335" d="M9 3.582c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.29C4.672 5.163 6.656 3.582 9 3.582z"/>
                            </svg>
                        </span>
                        Connect with Google
                    </a>

                    <div class="divider"><span>or</span></div>

                    <a href="{$msAuthUrl}" class="auth-btn ms-btn">
                        <span class="ms-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24">
                                <path fill="#f25022" d="M2 2h10v10H2z"/>
                                <path fill="#7fba00" d="M12 2h10v10H12z"/>
                                <path fill="#00a4ef" d="M2 12h10v10H2z"/>
                                <path fill="#ffb900" d="M12 12h10v10H12z"/>
                            </svg>
                        </span>
                        Connect with Microsoft
                    </a>

                    <div class="info-box">
                        <strong>Privacy first.</strong> We request access only to support outreach automation, reply detection, summaries, and order tracking inside ExtractMails.
                    </div>
                </div>
            </div>
        </section>
    </main>

<style>
    * { box-sizing: border-box; }
    body {
        min-height: 100vh; margin: 0;
        background: radial-gradient(circle at top left, rgba(25, 118, 210, 0.14), transparent 32%), #eef3f8;
        color: #172033; font-family: Arial, Helvetica, sans-serif;
    }
    .auth-shell { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 18px; }
    .signup-box { width: min(980px, 100%); max-height: calc(100vh - 36px); overflow: auto; background: #ffffff; border: 1px solid #dce5ef; border-radius: 8px; box-shadow: 0 22px 55px rgba(31, 45, 61, 0.14); padding: clamp(18px, 3vw, 30px); }
    .brand-row, .intro-row, .content-grid, .auth-btn, .divider { display: flex; align-items: center; }
    .brand-row { justify-content: space-between; gap: 14px; margin-bottom: 18px; }
    .logo { width: 164px; max-width: 54vw; display: block; }
    .secure-pill { flex: 0 0 auto; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #bfd4c8; color: #1f6b42; background: #edf8f1; border-radius: 999px; padding: 6px 12px; font-size: 13px; font-weight: 600; line-height: 1; }
    .secure-pill svg { flex-shrink: 0; }
    .intro-row { justify-content: space-between; gap: 22px; padding-bottom: 20px; border-bottom: 1px solid #edf1f5; }
    .eyebrow { margin: 0 0 6px; color: #1c6fd1; font-size: 13px; font-weight: 800; text-transform: uppercase; }
    h1 { margin: 0; color: #111827; font-size: clamp(28px, 4vw, 44px); line-height: 1.06; }
    .lead { max-width: 620px; margin: 10px 0 0; color: #4d5b6c; font-size: 16px; line-height: 1.45; }
    .top-gif { width: clamp(94px, 15vw, 138px); flex: 0 0 auto; }
    .content-grid { align-items: stretch; gap: 22px; margin-top: 22px; }
    .consent-panel, .action-panel { flex: 1 1 0; min-width: 0; }
    .section-title { margin: 0 0 12px; color: #273244; font-size: 15px; font-weight: 800; }
    .consent-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 9px; }
    .consent-list li { display: flex; align-items: flex-start; gap: 10px; color: #263244; font-size: 15px; line-height: 1.35; }
    .consent-list span { width: 22px; height: 22px; flex: 0 0 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: #eaf6ef; color: #15803d; font-weight: 800; font-size: 13px; }
    .security-note { margin: 16px 0 0; color: #556273; font-size: 14px; line-height: 1.45; }
    .action-panel { border-left: 1px solid #edf1f5; padding-left: 22px; display: flex; flex-direction: column; justify-content: center; }
    .auth-btn { width: 100%; min-height: 52px; justify-content: center; gap: 12px; border-radius: 8px; border: 1px solid #d8e1ec; color: #152033 !important; background: #ffffff; font-size: 15px; font-weight: 800; text-decoration: none !important; box-shadow: 0 8px 20px rgba(26, 38, 52, 0.08); transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease; }
    .auth-btn:hover { transform: translateY(-2px); border-color: #8fb4e8; box-shadow: 0 14px 28px rgba(26, 38, 52, 0.13); }
    .google-icon, .ms-icon { width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; background: #f8fafc; border: 1px solid #e6edf5; }
    .divider { gap: 12px; margin: 14px 0; color: #7a8795; font-size: 13px; font-weight: 700; }
    .divider::before, .divider::after { content: ""; height: 1px; flex: 1; background: #e1e8f0; }
    .info-box { margin-top: 18px; background: #f6f9fc; border: 1px solid #e2eaf3; border-left: 4px solid #1c6fd1; border-radius: 8px; padding: 12px 14px; color: #465569; font-size: 13px; line-height: 1.45; }
    .info-box strong { color: #172033; }
    @media (max-width: 760px) {
        .auth-shell { align-items: flex-start; padding: 10px; }
        .signup-box { max-height: none; padding: 16px; }
        .intro-row { align-items: flex-start; gap: 12px; }
        .top-gif { width: 80px; }
        .content-grid { flex-direction: column; gap: 16px; margin-top: 16px; }
        .action-panel { border-left: 0; border-top: 1px solid #edf1f5; padding-left: 0; padding-top: 16px; }
        .lead { font-size: 14px; }
        .consent-list li { font-size: 14px; }
    }
    @media (max-width: 430px) {
        .brand-row { align-items: flex-start; flex-direction: column; margin-bottom: 12px; }
        .intro-row { flex-direction: column; padding-bottom: 14px; }
        .top-gif { display: none; }
        h1 { font-size: 27px; }
        .auth-btn { min-height: 48px; font-size: 14px; }
    }
</style>
</body>
</html>
HTML;
        exit;
    }

    // ─── Handle OAuth Errors ───
    if (isset($_GET['error'])) {
        $errorDescription = $_GET['error_description'] ?? 'Unknown error occurred';
        displayError('Authentication Failed', '<p style="color: #666; font-size: 14px;">' . htmlspecialchars($errorDescription) . '</p>');
    }

    if (!isset($_GET['code'])) {
        displayError('Missing authorization code');
    }

    // ─── Has ?code= → Show overlay page, JS will do the AJAX call ───
    $code  = htmlspecialchars($_GET['code']);
    $state = htmlspecialchars($_GET['state'] ?? '');

    // Build the AJAX URL (same page, with ajax_process flag)
    $ajaxUrl = REDIRECT_URI . '?ajax_process=1&code=' . urlencode($_GET['code']) . '&state=' . urlencode($_GET['state'] ?? '');

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signing you in – ExtractMails</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue: #3b82f6; --blue-dark: #1d4ed8;
            --green: #22c55e; --green-dark: #15803d;
            --bg: #eef2f7; --card: #ffffff;
            --text: #0f172a; --muted: #64748b; --border: #e2e8f0;
        }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        body::before {
            content: "";
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 20%, rgba(59,130,246,0.08) 0%, transparent 70%),
                radial-gradient(ellipse 50% 60% at 80% 80%, rgba(34,197,94,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .card {
            background: var(--card);
            border-radius: 24px;
            padding: 48px 44px;
            max-width: 460px; width: 100%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 20px 60px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
            position: relative;
            text-align: center;
            animation: cardIn 0.5s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .card::before {
            content: "";
            position: absolute; top: 0; left: 0; right: 0; height: 4px;
            border-radius: 24px 24px 0 0;
            background: linear-gradient(90deg, #3b82f6 0%, #22c55e 100%);
        }
        .logo { height: 30px; margin-bottom: 32px; }

        /* Ring spinner */
        .ring-wrap { width: 90px; height: 90px; margin: 0 auto 28px; position: relative; }
        .ring-wrap svg { width: 100%; height: 100%; animation: spin 1.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .ring-icon {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .ring-icon svg { width: 30px; height: 30px; color: var(--blue); }

        .title { font-size: 21px; font-weight: 800; color: var(--text); margin-bottom: 8px; }
        .sub   { font-size: 14px; color: var(--muted); margin-bottom: 30px; line-height: 1.6; }

        /* Steps */
        .steps { display: flex; flex-direction: column; gap: 10px; margin-bottom: 28px; text-align: left; }
        .step {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 10px;
            background: #f8fafc; border: 1px solid var(--border);
            font-size: 13.5px; font-weight: 500; color: var(--muted);
            transition: all 0.35s ease;
        }
        .step.active { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
        .step.done   { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .step-dot {
            width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
            background: var(--border);
            display: flex; align-items: center; justify-content: center;
            transition: background 0.3s;
        }
        .step.active .step-dot { background: #bfdbfe; }
        .step.done   .step-dot { background: #bbf7d0; }
        .step-dot svg { width: 13px; height: 13px; }

        /* Dots */
        .dots { display: flex; justify-content: center; gap: 6px; }
        .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--blue);
            animation: pulse 1.4s ease-in-out infinite;
        }
        .dot:nth-child(2) { animation-delay: 0.2s; background: #60a5fa; }
        .dot:nth-child(3) { animation-delay: 0.4s; background: var(--green); }
        @keyframes pulse {
            0%,80%,100% { transform: scale(0.6); opacity: 0.4; }
            40%          { transform: scale(1);   opacity: 1; }
        }

        /* Error state */
        .error-box { display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin-top: 20px; color: #b91c1c; font-size: 14px; line-height: 1.5; }
        .retry-btn { display: inline-block; margin-top: 12px; padding: 8px 20px; background: #ef4444; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 700; }
    </style>
</head>
<body>
<div class="card">
    <img class="logo" src="https://www.extractmails.com/wp-content/uploads/2024/12/mailextracter-final-domain-logo-188x44.png" alt="ExtractMails">

    <div class="ring-wrap">
        <svg viewBox="0 0 90 90" fill="none">
            <circle cx="45" cy="45" r="39" stroke="#e2e8f0" stroke-width="6"/>
            <path d="M45 6 A39 39 0 0 1 84 45" stroke="url(#g)" stroke-width="6" stroke-linecap="round"/>
            <defs>
                <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#3b82f6"/>
                    <stop offset="100%" stop-color="#22c55e"/>
                </linearGradient>
            </defs>
        </svg>
        <div class="ring-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="2" y="4" width="20" height="16" rx="3"/>
                <polyline points="2,4 12,13 22,4"/>
            </svg>
        </div>
    </div>

    <div class="title">Signing you in…</div>
    <div class="sub">Verifying your account and setting up your workspace.</div>

    <div class="steps">
        <div class="step active" id="s1">
            <span class="step-dot">
                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="7" cy="7" r="5"/><polyline points="7,4 7,7 9,9"/>
                </svg>
            </span>
            Verifying OAuth token
        </div>
        <div class="step" id="s2">
            <span class="step-dot">
                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M7 2a5 5 0 1 1 0 10A5 5 0 0 1 7 2z"/><line x1="7" y1="5" x2="7" y2="9"/>
                </svg>
            </span>
            Fetching your profile
        </div>
        <div class="step" id="s3">
            <span class="step-dot">
                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polyline points="2 7 5 10 12 4"/>
                </svg>
            </span>
            Setting up your workspace
        </div>
    </div>

    <div class="dots">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>

    <div class="error-box" id="errorBox">
        <strong>Something went wrong.</strong><br>
        <span id="errorMsg"></span>
        <br>
        <a href="{$_SERVER['PHP_SELF']}" class="retry-btn">Try Again</a>
    </div>
</div>

<script>
(function () {
    var steps  = [document.getElementById('s1'), document.getElementById('s2'), document.getElementById('s3')];
    var errorBox = document.getElementById('errorBox');
    var errorMsg = document.getElementById('errorMsg');

    function activate(i) {
        steps.forEach(function(s, idx) {
            s.classList.remove('active', 'done');
            if (idx < i)  s.classList.add('done');
            if (idx === i) s.classList.add('active');
        });
    }

    function showError(msg) {
        steps.forEach(function(s) { s.classList.remove('active'); });
        errorBox.style.display = 'block';
        errorMsg.textContent = msg;
    }

    // Step 1 already active on load — start fetch after short delay for visual feedback
    setTimeout(function () {
        activate(1); // move to step 2

        fetch('{$ajaxUrl}', { method: 'GET', credentials: 'same-origin' })
            .then(function (res) {
                if (!res.ok) throw new Error('Server returned ' + res.status);
                return res.json();
            })
            .then(function (data) {
                if (data.error) {
                    showError(data.error);
                    return;
                }
                activate(2); // step 3 — workspace setup
                setTimeout(function () {
                    steps.forEach(function(s) { s.classList.remove('active'); s.classList.add('done'); });
                    window.location.href = data.redirect;
                }, 700);
            })
            .catch(function (err) {
                showError('Connection error. Please check your network and try again.');
            });
    }, 600);
})();
</script>
</body>
</html>
HTML;
    exit;

} catch (Exception $e) {
    error_log("OAuth Error: " . $e->getMessage());
    displayError('An unexpected error occurred. Please try again later.');
}
?>
