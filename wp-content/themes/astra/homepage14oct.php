<?php
/**
 * Template Name: Home-page
 * 
 * ExtractMails SaaS Homepage Template
 * Fully responsive, modern SaaS presentation matching ExtractMails-Complete-Responsive-Website
 */

// === EXTRACTMAILS OAUTH & CRM AUTHENTICATION ENGINE ===
if (!defined('EXTRACTMAILS_GOOGLE_CLIENT_ID')) {
    define('EXTRACTMAILS_GOOGLE_CLIENT_ID', '842559391916-i2hq0thd3hnsj90mr3mvrr4i4mtqoou3.apps.googleusercontent.com');
}
if (!defined('EXTRACTMAILS_MICROSOFT_CLIENT_ID')) {
    define('EXTRACTMAILS_MICROSOFT_CLIENT_ID', '51c4a942-4d08-438f-880c-3befb48041f3');
}

// 1. One-Time Code (OTC) Exchange Endpoint (Backend-mediated session redemption)
if (isset($_GET['oauth_exchange'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: https://app.extractmails.com');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $otc = sanitize_text_field($_GET['otc'] ?? $_POST['otc'] ?? '');
    if (empty($otc)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing one-time authorization code.']);
        exit;
    }

    $session_data = get_transient('em_otc_' . $otc);
    if ($session_data) {
        // Enforce single-use: invalidate code immediately
        delete_transient('em_otc_' . $otc);

        // Set HttpOnly, Secure session cookie for the app domain
        $cookie_domain = (strpos($_SERVER['HTTP_HOST'], 'extractmails.com') !== false) ? '.extractmails.com' : '';
        setcookie('em_auth_session', json_encode($session_data), [
            'expires' => time() + (12 * 3600),
            'path' => '/',
            'domain' => $cookie_domain,
            'secure' => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        http_response_code(200);
        echo json_encode(['success' => true, 'session' => $session_data]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired authorization code.']);
    }
    exit;
}

// 2. OAuth Initiation Handler (?oauth_login=google | ?oauth_login=microsoft)
if (isset($_GET['oauth_login'])) {
    $provider = strtolower(sanitize_text_field($_GET['oauth_login']));
    $state = bin2hex(random_bytes(24));
    
    // Store CSRF state token in HttpOnly cookie
    setcookie('em_oauth_state', $state, [
        'expires' => time() + 600,
        'path' => '/',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    setcookie('em_oauth_provider', $provider, [
        'expires' => time() + 600,
        'path' => '/',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    $callback_url = home_url('/?oauth_callback=1');

    if ($provider === 'google') {
        $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id' => EXTRACTMAILS_GOOGLE_CLIENT_ID,
            'redirect_uri' => $callback_url,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $state,
        ]);
        wp_redirect($auth_url);
        exit;
    } elseif ($provider === 'microsoft') {
        $auth_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query([
            'client_id' => EXTRACTMAILS_MICROSOFT_CLIENT_ID,
            'redirect_uri' => $callback_url,
            'response_type' => 'code',
            'scope' => 'openid profile email User.Read',
            'response_mode' => 'query',
            'prompt' => 'select_account',
            'state' => $state,
        ]);
        wp_redirect($auth_url);
        exit;
    }
}

// 3. OAuth Callback Handler (?oauth_callback=1 or code & state present)
if (isset($_GET['oauth_callback']) || (isset($_GET['code']) && isset($_GET['state']))) {
    // Check for user cancellation or provider error
    if (!empty($_GET['error'])) {
        wp_redirect(add_query_arg('auth_notice', 'cancelled', home_url('/')));
        exit;
    }

    $state = sanitize_text_field($_GET['state'] ?? '');
    $cookie_state = $_COOKIE['em_oauth_state'] ?? '';
    $provider = $_COOKIE['em_oauth_provider'] ?? 'google';

    // Verify CSRF state token
    if (empty($state) || empty($cookie_state) || !hash_equals($cookie_state, $state)) {
        wp_redirect(add_query_arg('auth_error', 'invalid_state', home_url('/')));
        exit;
    }

    // Clean up CSRF state cookie
    setcookie('em_oauth_state', '', time() - 3600, '/');
    setcookie('em_oauth_provider', '', time() - 3600, '/');

    $code = sanitize_text_field($_GET['code'] ?? '');
    $callback_url = home_url('/?oauth_callback=1');
    $user_email = '';
    $user_name = '';
    $user_id = '';

    if ($provider === 'google') {
        // Exchange code with Google
        $token_response = wp_remote_post('https://oauth2.googleapis.com/token', [
            'body' => [
                'code' => $code,
                'client_id' => EXTRACTMAILS_GOOGLE_CLIENT_ID,
                'redirect_uri' => $callback_url,
                'grant_type' => 'authorization_code',
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($token_response)) {
            wp_redirect(add_query_arg('auth_error', 'token_failed', home_url('/')));
            exit;
        }

        $token_data = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_data['access_token'] ?? '';

        if (!$access_token) {
            wp_redirect(add_query_arg('auth_error', 'token_missing', home_url('/')));
            exit;
        }

        // Fetch User Info
        $profile_response = wp_remote_get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'headers' => ['Authorization' => 'Bearer ' . $access_token],
            'timeout' => 10,
        ]);
        $profile_data = json_decode(wp_remote_retrieve_body($profile_response), true);
        $user_email = strtolower(trim($profile_data['email'] ?? ''));
        $user_name = $profile_data['name'] ?? '';
        $user_id = $profile_data['sub'] ?? '';

    } elseif ($provider === 'microsoft') {
        // Exchange code with Microsoft
        $token_response = wp_remote_post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
            'body' => [
                'client_id' => EXTRACTMAILS_MICROSOFT_CLIENT_ID,
                'code' => $code,
                'redirect_uri' => $callback_url,
                'grant_type' => 'authorization_code',
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($token_response)) {
            wp_redirect(add_query_arg('auth_error', 'token_failed', home_url('/')));
            exit;
        }

        $token_data = json_decode(wp_remote_retrieve_body($token_response), true);
        $access_token = $token_data['access_token'] ?? '';

        if (!$access_token) {
            wp_redirect(add_query_arg('auth_error', 'token_missing', home_url('/')));
            exit;
        }

        // Fetch User Info from Microsoft Graph
        $profile_response = wp_remote_get('https://graph.microsoft.com/v1.0/me', [
            'headers' => ['Authorization' => 'Bearer ' . $access_token],
            'timeout' => 10,
        ]);
        $profile_data = json_decode(wp_remote_retrieve_body($profile_response), true);
        $user_email = strtolower(trim($profile_data['mail'] ?? $profile_data['userPrincipalName'] ?? ''));
        $user_name = $profile_data['displayName'] ?? '';
        $user_id = $profile_data['id'] ?? '';
    }

    if (empty($user_email)) {
        wp_redirect(add_query_arg('auth_error', 'email_missing', home_url('/')));
        exit;
    }

    // 4. Reconcile / Provision User with SuiteCRM Backend API
    $crm_url = 'https://crm.outrightsystems.org/index.php?entryPoint=get_domain&email=' . urlencode($user_email) . '&saas_type=ExtractMails';
    $crm_response = wp_remote_get($crm_url, ['timeout' => 15]);
    $crm_data = [];

    if (!is_wp_error($crm_response)) {
        $crm_data = json_decode(wp_remote_retrieve_body($crm_response), true);
    }

    // Construct the authenticated user session payload
    $session_payload = [
        'success' => true,
        'domain' => $crm_data['domain'] ?? 'crm.outrightsystems.org',
        'email' => $user_email,
        'current_user' => $user_email,
        'name' => $user_name,
        'user_id' => $crm_data['user_id'] ?? $user_id,
        'auth_provider' => $provider,
        'authenticated_at' => time(),
    ];

    // 5. Store session payload under a short-lived, single-use One-Time Code (OTC)
    $otc = bin2hex(random_bytes(32));
    set_transient('em_otc_' . $otc, $session_payload, 60); // Expires in 60 seconds

    // 6. Set userSession cookie across .extractmails.com for seamless SPA integration
    $cookie_domain = (strpos($_SERVER['HTTP_HOST'], 'extractmails.com') !== false) ? '.extractmails.com' : '';
    setcookie('userSession', json_encode($session_payload), [
        'expires' => time() + (12 * 3600),
        'path' => '/',
        'domain' => $cookie_domain,
        'secure' => is_ssl(),
        'httponly' => false,
        'samesite' => 'Lax'
    ]);

    // Also set Secure, HttpOnly cookie
    setcookie('em_auth_session', json_encode($session_payload), [
        'expires' => time() + (12 * 3600),
        'path' => '/',
        'domain' => $cookie_domain,
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    // 7. Secure Redirect to the App via One-Time Code Exchange
    $app_target = 'https://app.extractmails.com/auth/exchange?otc=' . $otc;
    wp_redirect($app_target);
    exit;
}

get_header();
$theme_uri = get_stylesheet_directory_uri();
?>

<style>
/* Reset and Scope Isolation for Astra Theme */
.homepage-glow {
  width: 100%;
  overflow: visible !important;
  overflow-x: clip !important;
}

/* Sticky Left Image & Scrollable Right Content for Editorial Sections */
@media screen and (min-width: 861px) {
  .problem-layout,
  .workflow-layout,
  .about-editorial,
  .work-editorial {
    display: grid !important;
    grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.15fr) !important;
    gap: 48px !important;
    align-items: start !important;
  }

  .problem-art,
  .workflow-art,
  .about-art,
  .work-art {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 105px !important;
    align-self: start !important;
    z-index: 5 !important;
    width: 100% !important;
  }

  .problem-art img,
  .workflow-art img,
  .about-art img,
  .work-art img {
    width: 100% !important;
    height: auto !important;
    max-height: calc(100vh - 150px) !important;
    object-fit: cover !important;
    display: block !important;
    border-radius: var(--radius, 16px) !important;
  }
}

/* Remove outer blue header container so only the floating glass navbar displays */
header,
header.fixed,
body > header,
header:not(.detail-topbar),
.site-header,
.responsive-site > header:not(.detail-topbar) {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
  border-radius: 0 !important;
  padding: 0 !important;
  margin: 0 !important;
  width: 100% !important;
  max-width: 100% !important;
}

/* Ensure fixed header does not overlap hero content (Matching Image 2) */
#home.hero.reference-hero {
  padding-top: 140px !important;
  padding-bottom: 72px !important;
}

#home.reference-hero::before {
  background-image: linear-gradient(90deg, rgba(7, 28, 80, 0.25), transparent 75%),
                    url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') !important;
  background-position: center center !important;
  background-size: cover !important;
  background-repeat: no-repeat !important;
  background-color: #071C50 !important;
}

@media (max-width: 1000px) {
  #home.hero.reference-hero {
    padding-top: 130px !important;
  }
}

@media (max-width: 760px) {
  #home.hero.reference-hero {
    padding-top: 110px !important;
  }
  #home.reference-hero::before {
    background-image: linear-gradient(180deg, rgba(7, 28, 80, 0.35), rgba(7, 28, 80, 0.2)),
                      url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') !important;
    background-position: center center !important;
    background-size: cover !important;
    background-repeat: no-repeat !important;
    background-color: #071C50 !important;
  }
}

/* Bundled SaaS Design System */
/* === styles.css === */
:root{--navy:#071c50;--blue:#205be8;--blue-hover:#1749ce;--ink:#132b50;--muted:#52647f;--pale:#f4f7fc;--border:#dce4f0;--dark-border:#ffffff25;--radius:16px;--shadow:0 8px 24px #071c5008;--space:24px;color-scheme:light}
*{box-sizing:border-box}html{scroll-behavior:smooth;scroll-padding-top:104px}body{margin:0;background:var(--navy);color:#fff;font:400 16px/1.65 "Segoe UI",Inter,Arial,sans-serif;overflow-x:clip}a{color:inherit;text-decoration:none}button,input{font:inherit}button,a,summary{-webkit-tap-highlight-color:transparent}img{max-width:100%}p{margin:0 0 20px;line-height:1.75;color:#d8e2f4}h1,h2,h3{margin:0;font-weight:700}h1{font-size:clamp(36px,3.8vw,52px);line-height:1.12;letter-spacing:-1.8px}h2{font-size:clamp(30px,3vw,40px);line-height:1.2;letter-spacing:-1.1px}h3{font-size:20px;line-height:1.35;letter-spacing:-.3px}figure{margin:0}a:focus-visible,button:focus-visible,summary:focus-visible,input:focus-visible{outline:3px solid #82aaff;outline-offset:4px}a,button,summary{touch-action:manipulation}.skip-link{position:fixed;top:8px;left:16px;z-index:100;padding:12px 20px;color:var(--ink);background:#fff;border-radius:8px;transform:translateY(-150%)}.skip-link:focus{transform:none}
main{max-width:1280px;margin:auto;padding:0 40px}.standalone-header{position:sticky;top:12px;z-index:20;max-width:1200px;margin:12px auto 0;min-height:76px;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:24px;background:#071c50f5;border:1px solid var(--dark-border);border-radius:14px;box-shadow:0 6px 20px #00000010}.brand{display:flex;align-items:center;gap:10px;flex-shrink:0;font-size:22px;font-weight:700;letter-spacing:-.6px}.brand-logo{width:40px;height:40px;background:#fff;border-radius:9px;padding:2px;object-fit:contain}nav{display:flex;align-items:center;gap:8px;font-size:14px;font-weight:500}nav a{padding:10px 12px;border-radius:8px;color:#d8e2f4;transition:background .15s,color .15s}nav a:hover,nav a[aria-current]{background:#ffffff0c;color:#fff}.btn{display:inline-flex;min-height:48px;align-items:center;justify-content:center;padding:12px 22px;border:1px solid transparent;border-radius:9px;background:var(--blue);color:#fff;font-size:14px;font-weight:600;line-height:1.4;white-space:normal;text-align:center;box-shadow:none;transition:background .15s,border-color .15s}.btn:hover{background:var(--blue-hover)}.header-cta{background:#ffffff08;border-color:#ffffff40;min-height:44px;padding:10px 18px}.header-cta:hover{background:#ffffff14;border-color:#ffffff70}.menu-toggle{display:none}
.hero{position:relative;isolation:isolate;display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);align-items:center;gap:48px;min-height:620px;padding:72px 0 80px}.hero>div{min-width:0}.hero:before{content:"";position:absolute;z-index:-1;inset:0 auto 0 50%;width:100vw;transform:translateX(-50%);background:radial-gradient(ellipse at 78% 45%,#205be818,transparent 65%),var(--navy);pointer-events:none}.hero h1{margin:22px 0;color:#fff}.hero p{font-size:17px;max-width:58ch;margin-bottom:24px}.pill{display:inline-flex;border:1px solid #ffffff30;border-radius:6px;padding:7px 10px;color:#d8e2f4;font-size:12px;font-weight:500;letter-spacing:1px}.hero-note{display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;color:#bccde8;font-size:13px}.hero-note span+span{padding-left:12px;border-left:1px solid #ffffff30}.hero-motion{position:relative;width:100%;aspect-ratio:16/9;border:1px solid #ffffff30;border-radius:var(--radius);overflow:hidden;background:var(--navy);box-shadow:0 16px 40px #020b2b30}.hero-motion iframe{position:absolute;inset:0;width:100%;height:100%;display:block;border:0;background:var(--navy)}
.section{position:relative;isolation:isolate;padding:80px 0}.section:before{content:"";position:absolute;z-index:-1;inset:0 auto 0 50%;width:100vw;transform:translateX(-50%);background:var(--navy)}.section.tone-light{color:var(--ink)}.section.tone-light:before{background:var(--pale)}.section.tone-light p{color:var(--muted)}.sectiontop{max-width:780px;margin-bottom:36px}.eyebrow{display:block;font-size:12px;font-weight:600;letter-spacing:1.4px;color:#b6cbee;margin-bottom:10px}.kicker{display:block;color:#b6cbee;font-size:14px;font-weight:500;margin-bottom:8px}.section.tone-light .eyebrow,.section.tone-light .kicker{color:#2854a7}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.card{padding:28px;border:1px solid var(--border);border-radius:var(--radius);background:#fff;color:var(--ink);min-width:0;box-shadow:none}.card h3{margin:18px 0 12px}.card p,.section.tone-light .card p{font-size:16px;margin:0;color:var(--muted)}.icon3d{display:block;width:56px;height:56px;flex-shrink:0;background:#fff url('https://www.extractmails.com/wp-content/uploads/2024/12/icons-3d.webp');background-size:400% 300%;background-position:calc(var(--ix)*100%/3) calc(var(--iy)*100%/2);border-radius:10px}.bento{grid-template-columns:repeat(6,minmax(0,1fr))}.bento .card{grid-column:span 2}.bento .campaign-card{grid-column:span 3;display:flex;flex-direction:column;overflow:hidden}.bento .campaign-card h3{font-size:24px;letter-spacing:-.5px}.campaign-visual{margin:24px -28px -28px;background:var(--navy)}.campaign-visual img{display:block;width:100%;height:270px;object-fit:cover}.bento .campaign-card:first-child{background:var(--navy);color:#fff;border-color:#ffffff25}.bento .campaign-card:first-child p{color:#d8e2f4}
.problem-layout,.premium-split,.work-editorial,.workflow-layout,.about-editorial{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.1fr);gap:40px;align-items:start}.problem-art,.premium-art,.work-art,.workflow-art,.about-art,.premium-team{border-radius:var(--radius);overflow:hidden;background:var(--navy);border:1px solid #ffffff20;box-shadow:none}.problem-art img,.work-art img,.workflow-art img,.about-art img{display:block;width:100%;height:380px;object-fit:cover}.problem-art figcaption,.about-art figcaption{padding:24px;color:white;border-top:1px solid var(--dark-border)}.problem-art figcaption span,.about-art-label{font-size:12px;letter-spacing:1.4px;color:#b6cbee}.problem-art strong,.about-art strong{display:block;font-size:28px;line-height:1.2;letter-spacing:-.7px;margin-top:10px}.problem-stories,.work-chapters{display:grid;gap:20px}.problem-chapter{display:grid;grid-template-columns:28px minmax(0,1fr);gap:16px;padding:24px;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}.chapter-number,.work-number,.step-number{font-size:13px;color:#2854a7;font-weight:600}.chapter-number{padding-top:3px}.problem-chapter h3{margin-bottom:12px}.problem-chapter p{margin:0;font-size:16px}.problem-chapter:last-child{border-color:#adc3ee;background:#edf3ff}.premium-split{align-items:center}.premium-art img{width:100%;height:auto;display:block}.premium-copy p{max-width:62ch}.fields{display:flex;flex-wrap:wrap;gap:8px;margin-top:20px}.fields span{font-size:14px;line-height:1.4;padding:8px 11px;border-radius:6px;border:1px solid #ffffff30;color:#d8e2f4;background:#ffffff04}
.use-tabs{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:24px}.use-tab{cursor:pointer;padding:11px 15px;min-height:44px;border:1px solid #ffffff35;border-radius:8px;background:#ffffff05;color:#e0e9f8;font-size:14px;font-weight:500;transition:background .15s}.use-tab:hover{background:#ffffff12}.use-tab[aria-selected=true]{background:var(--blue);border-color:var(--blue);color:#fff}.case-stage{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:28px;padding:24px;border-radius:var(--radius);background:#fff;color:var(--ink);border:1px solid var(--border);align-items:center}.case-panel{padding:12px;border:0;background:none}.case-panel[hidden]{display:none}.case-panel h3{font-size:24px;margin:20px 0 12px}.case-panel p{color:var(--muted);margin:0;font-size:16px}.case-preview{position:relative;border-radius:12px;overflow:hidden;background:var(--navy);min-width:0}.case-preview img{display:block;width:100%;height:360px;object-fit:cover}.preview-caption{position:absolute;bottom:16px;left:16px;right:16px;padding:10px 12px;background:#071c50ec;color:#e0e9f8;border:1px solid #ffffff30;border-radius:8px;font-size:13px}
.workflow-art figcaption{padding:20px 24px;border-top:1px solid var(--dark-border);font-size:16px;color:#e0e9f8}.steps{display:grid;gap:16px;padding-left:20px;border-left:1px solid #bacbea}.steps .card{display:grid;grid-template-columns:56px minmax(0,1fr);gap:4px 16px;padding:24px;position:relative}.steps .icon3d{grid-row:1/3;align-self:center}.steps h3{margin:0 0 8px;padding-right:28px;font-size:19px;grid-column:2}.steps p{grid-column:2}.step-number{position:absolute;right:18px;top:24px}.steps .card:before{content:"";position:absolute;left:-25px;top:30px;width:8px;height:8px;border-radius:50%;background:var(--blue);box-shadow:0 0 0 5px var(--pale)}.team-editorial{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:40px;align-items:center;margin-bottom:36px}.team-editorial .sectiontop{margin:0}.premium-team img{display:block;width:100%;height:340px;object-fit:cover}#teams .grid{grid-template-columns:repeat(6,minmax(0,1fr))}#teams .card{grid-column:span 2}#teams .card:last-child{grid-column:span 6;display:grid;grid-template-columns:56px minmax(180px,.6fr) minmax(0,1fr);gap:24px;align-items:center}#teams .card:last-child h3{margin:0}
#section-7 .grid{grid-template-columns:repeat(2,minmax(0,1fr))}#section-7 .card:first-child{grid-column:1/-1;display:grid;grid-template-columns:56px minmax(0,1fr);column-gap:24px;background:#edf3ff;border-color:#c5d5f2}#section-7 .card:first-child .icon3d{grid-row:1/3}#section-7 .card:first-child h3{margin:0 0 12px;font-size:24px}.about-stories{display:grid;grid-template-columns:1fr;gap:0}.about-story{padding:24px 0;border-bottom:1px solid var(--dark-border)}.about-story:first-child{padding-top:0}.about-story:last-child{border-bottom:0;padding-bottom:0}.about-story-top{display:flex;align-items:center;gap:12px;margin-bottom:12px}.about-story h3{margin:0}.about-number{display:grid;place-items:center;min-width:32px;height:32px;background:#ffffff08;border:1px solid var(--dark-border);border-radius:7px;font-size:13px;color:#b6cbee}.about-story p{font-size:16px;margin:0}.work-art img{height:480px}.work-art figcaption{display:flex;flex-wrap:wrap;gap:8px;padding:20px;border-top:1px solid var(--dark-border)}.work-art figcaption span{font-size:13px;padding:6px 10px;color:#d8e2f4;border:1px solid var(--dark-border);border-radius:6px}.work-chapters article{position:relative;padding:0 0 24px 44px;border-bottom:1px solid var(--border)}.work-chapters article:last-child{padding-bottom:0;border-bottom:0}.work-number{position:absolute;left:0;top:4px}.work-chapters h3{margin-bottom:12px}.work-chapters p{margin:0;font-size:16px}
#faq .sectiontop{margin-bottom:32px}.faqs{max-width:900px;display:grid;gap:12px}details{border:1px solid var(--dark-border);border-radius:12px;padding:0 24px;background:#ffffff03}summary{min-height:64px;cursor:pointer;font-size:17px;font-weight:600;padding:18px 0;line-height:1.5}details[open]{background:#ffffff06}details p{font-size:16px;margin:0 0 24px;padding-right:16px;max-width:75ch}.closing-cta{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:0 40px;margin:32px 0 72px;border:1px solid var(--dark-border);border-radius:var(--radius);overflow:hidden;background:#0c2557;padding-right:40px}.cta-art{grid-row:span 2;align-self:stretch;overflow:hidden}.cta-art img{display:block;width:100%;height:100%;min-height:330px;object-fit:cover}.closing-cta>div{padding-top:40px}.closing-cta h2{font-size:32px;max-width:22ch}.closing-cta p{margin:16px 0 24px;font-size:16px}.closing-cta>.btn{justify-self:start;align-self:start;margin-bottom:40px}
.premium-footer{max-width:1200px;margin:auto;padding:48px 0 28px;border-top:1px solid var(--dark-border);display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:40px;color:#bccde8}.footer-intro p{font-size:16px;margin:16px 0 0}.footer-links{display:flex;flex-direction:column;align-items:start;gap:4px;font-size:14px}.footer-links span{color:#fff;font-weight:600;margin-bottom:12px}.footer-links a{padding:8px 0;min-height:40px}.footer-links a:hover{color:#fff;text-decoration:underline;text-underline-offset:4px}.footer-bottom{grid-column:1/-1;display:flex;justify-content:space-between;gap:20px;padding-top:24px;border-top:1px solid var(--dark-border);font-size:13px}.footer-bottom a{padding:4px 0}/* === WHOLE-HOMEPAGE MULTI-DIRECTIONAL CINEMATIC SCROLL REVEAL SYSTEM === */
.reveal-ready {
  opacity: 0 !important;
  will-change: transform, opacity, filter;
  transition: opacity 0.9s cubic-bezier(0.16, 1, 0.3, 1),
              transform 0.9s cubic-bezier(0.16, 1, 0.3, 1),
              filter 0.9s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

/* Direction 1: Upward Lift & Focal De-blur (Headings, Subtitles, Stage panels) */
.reveal-ready.reveal-up {
  transform: translateY(42px) scale(0.97) !important;
  filter: blur(8px) !important;
}

/* Direction 2: Left 3D Artwork Entrance (Smooth Inward Perspective Slide) */
.reveal-ready.reveal-left {
  transform: translateX(-48px) scale(0.96) !important;
  filter: blur(10px) !important;
}

/* Direction 3: Right Content Card Entrance (Smooth Inward Perspective Slide) */
.reveal-ready.reveal-right {
  transform: translateX(48px) scale(0.96) !important;
  filter: blur(10px) !important;
}

/* Direction 4: Scale & Pop Entrance for Grid Cards, Bento & Telemetry */
.reveal-ready.reveal-scale {
  transform: translateY(28px) scale(0.93) !important;
  filter: blur(8px) !important;
}

/* Common Resting State: 100% Sharp Focus & Full Reset (Preserves Sticky Positioning) */
.reveal-ready.is-visible {
  opacity: 1 !important;
  transform: none !important;
  filter: none !important;
}

@media (prefers-reduced-motion: reduce) {
  .reveal-ready,
  .reveal-ready.reveal-up,
  .reveal-ready.reveal-left,
  .reveal-ready.reveal-right,
  .reveal-ready.reveal-scale {
    opacity: 1 !important;
    transform: none !important;
    filter: none !important;
    transition: none !important;
  }
}
@media(min-width:1101px){.problem-art,.work-art,.workflow-art,.about-art{position:sticky;top:112px}}
@media(max-width:1280px){.standalone-header,.premium-footer{margin-left:40px;margin-right:40px}}
@media(max-width:1100px){.hero{grid-template-columns:1fr;min-height:0;gap:36px;padding:56px 0 64px}.hero>div:first-child{max-width:760px}.hero h1{max-width:720px;font-size:48px}.hero p{max-width:680px}.hero-motion{max-width:900px}.problem-layout,.work-editorial,.workflow-layout,.about-editorial{gap:28px}.problem-art img,.workflow-art img,.about-art img{height:330px}.bento .campaign-card h3{font-size:22px}nav{gap:0}nav a{padding:10px}.brand{font-size:20px}.standalone-header{gap:16px}.header-cta{padding:10px 14px}#teams .card:last-child{grid-template-columns:56px minmax(0,1fr)}#teams .card:last-child p{grid-column:2}.steps .card{padding:20px;gap:4px 12px}.step-number{top:20px;right:16px}}
@media(max-width:900px){main{padding:0 28px}.standalone-header,.premium-footer{margin-left:28px;margin-right:28px}.section{padding:64px 0}.problem-layout,.premium-split,.work-editorial,.workflow-layout,.about-editorial,.team-editorial{grid-template-columns:1fr;gap:28px}.problem-art img,.work-art img,.workflow-art img,.about-art img{height:auto;aspect-ratio:3/2;object-fit:cover}.problem-art,.work-art,.workflow-art,.about-art{max-width:680px}.about-stories{grid-template-columns:1fr 1fr;gap:24px}.about-story,.about-story:first-child{padding:0 0 24px}.about-story:last-child{border-bottom:1px solid var(--dark-border)}.premium-team img{height:auto;aspect-ratio:3/2}.grid{grid-template-columns:repeat(2,minmax(0,1fr))}#teams .grid{grid-template-columns:repeat(2,minmax(0,1fr))}#teams .card{grid-column:auto}#teams .card:last-child{grid-column:1/-1}.case-stage{grid-template-columns:1fr;gap:24px}.case-panel{padding:4px}.case-preview img{height:auto;aspect-ratio:3/2}.closing-cta{gap:0 28px;padding-right:28px}.closing-cta h2{font-size:28px}.premium-footer{gap:28px}}
@media(max-width:760px){header{flex-wrap:wrap;min-height:68px;padding:10px 16px;gap:12px;top:8px}.brand{font-size:20px}.brand-logo{width:38px;height:38px}.menu-toggle{display:inline-flex;align-items:center;justify-content:center;min-width:64px;min-height:44px;margin-left:auto;border:1px solid #ffffff35;border-radius:8px;color:white;background:#ffffff06;font-size:14px;cursor:pointer}.header-cta{display:none}.standalone-header nav{display:none;width:100%;align-items:stretch;flex-direction:column;gap:4px;border-top:1px solid var(--dark-border);padding-top:8px}.standalone-header nav.open{display:flex}.standalone-header nav a{min-height:44px;padding:10px 12px}.bento{grid-template-columns:repeat(2,minmax(0,1fr))}.bento .card,.bento .campaign-card{grid-column:auto}.bento .card:last-child{grid-column:1/-1}.campaign-visual img{height:220px}.about-stories{grid-template-columns:1fr}.closing-cta{grid-template-columns:1fr;padding:0;gap:0}.cta-art{grid-row:auto}.cta-art img{height:auto;min-height:0;max-height:330px}.closing-cta>div{padding:28px 28px 0}.closing-cta h2{max-width:28ch}.closing-cta>.btn{margin:0 28px 28px}.premium-footer{grid-template-columns:1fr 1fr}.footer-intro{grid-column:1/-1}}
@media(max-width:600px){main{padding:0 20px}.standalone-header,.premium-footer{margin-left:20px;margin-right:20px}.standalone-header{margin-top:8px}.hero{padding:40px 0 48px;gap:28px}.hero h1{font-size:clamp(34px,8.5vw,42px);letter-spacing:-1.1px}.hero p{font-size:16px}.hero-note{font-size:12px;gap:8px}.hero-note span+span{padding-left:8px}.hero .btn{min-width:180px}.section{padding:48px 0}.sectiontop{margin-bottom:28px}h2{font-size:30px;letter-spacing:-.8px}.grid,.bento,#teams .grid,#section-7 .grid{grid-template-columns:1fr}.bento .card,.bento .campaign-card,.bento .card:last-child,#teams .card,#teams .card:last-child{grid-column:auto}.card{padding:24px}.campaign-visual{margin:24px -24px -24px}.campaign-visual img{height:auto;aspect-ratio:3/2}.problem-chapter{padding:22px;gap:12px;grid-template-columns:24px minmax(0,1fr)}.problem-art strong,.about-art strong{font-size:26px}.case-stage{padding:20px}.use-tab{padding:11px 13px}.preview-caption{position:static;border:0;border-radius:0;font-size:13px;padding:12px 16px}.steps{padding-left:16px}.steps .card{grid-template-columns:48px minmax(0,1fr);padding:20px 16px;gap:4px 12px}.steps .icon3d{width:48px;height:48px}.steps .card:before{left:-21px}.steps h3{font-size:18px;padding-right:24px}.step-number{right:12px;font-size:12px}#teams .card:last-child{display:block}#teams .card:last-child h3{margin:18px 0 12px}#section-7 .card:first-child{display:block}#section-7 .card:first-child h3{margin:18px 0 12px;font-size:22px}.work-chapters article{padding-left:36px}.work-art figcaption{padding:16px}.faqs{gap:12px}details{padding:0 20px}summary{font-size:16px}details p{padding-right:0}.closing-cta{margin:16px 0 48px}.premium-footer{padding-top:36px;gap:28px 20px}.footer-bottom{flex-wrap:wrap}.footer-links a{min-height:44px}}
@media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}.reveal-ready{opacity:1;transform:none;transition:none}*,*:before,*:after{animation:none!important;transition:none!important}}

/* Original-copy-only layouts */
.problem-chapter{display:block}.problem-chapter p{margin:0}.work-chapters article{padding-left:0}.problem-art img,.about-art img{height:auto;aspect-ratio:3/2}.work-art img{height:auto;aspect-ratio:3/2}.workflow-art img{height:auto;aspect-ratio:3/2}.hero h1{margin-top:0}.premium-footer{margin-top:24px}

/* Sculpted surfaces: one restrained depth treatment across the site. */
:root{--surface-shadow:0 2px 2px #071c5005,0 12px 28px #071c500a;--art-shadow:0 2px 0 #153979,0 8px 0 -3px #0b285d,0 22px 45px #030e2b22}.standalone-header{background:linear-gradient(160deg,#102859,#071c50 75%);border-color:#ffffff30;box-shadow:inset 0 1px 0 #ffffff0d,0 8px 24px #00000018}
.btn{background:linear-gradient(180deg,#2b68f0,#205be8);border-color:#4d7ff1;box-shadow:inset 0 1px 0 #ffffff24,0 3px 0 #1643b8,0 8px 16px #071c5014;transition:background .16s,box-shadow .16s,transform .16s}.btn:hover{background:linear-gradient(180deg,#205be8,#1749ce);transform:translateY(-1px);box-shadow:inset 0 1px 0 #ffffff24,0 4px 0 #133ea9,0 10px 18px #071c5020}.btn:active{transform:translateY(2px);box-shadow:inset 0 1px 0 #ffffff12,0 1px 0 #1643b8}.header-cta{background:#ffffff06;border-color:#ffffff35;box-shadow:inset 0 1px 0 #ffffff0a}.header-cta:hover{background:#ffffff12;box-shadow:none;transform:none}
.hero{gap:56px}.hero:before{background:radial-gradient(ellipse at 75% 40%,#205be826,transparent 65%),var(--navy)}.hero-motion{border-color:#86a7ed66;box-shadow:inset 0 1px 0 #ffffff30,0 3px 0 #133674,0 9px 0 -3px #0b285d,0 24px 50px #020b2b40;transform:perspective(1800px) rotateY(-3deg);isolation:isolate}.hero-motion:after{content:"";position:absolute;inset:0;pointer-events:none;border-radius:inherit;box-shadow:inset 0 1px 0 #ffffff40,inset 1px 0 0 #ffffff12}
.section.tone-light:before{background:linear-gradient(145deg,#f8faff,#f1f5fc)}
.card,.problem-chapter,.case-stage{border-color:#d5dfef;box-shadow:inset 0 1px 0 #fff,var(--surface-shadow);background:linear-gradient(155deg,#fff 70%,#f8faff)}.icon3d{width:64px;height:64px;box-shadow:inset 0 0 0 1px #071c5008,0 3px 0 #e0e7f3,0 8px 16px #071c5010;border-radius:12px}.steps .icon3d{width:56px;height:56px}.bento .campaign-card:first-child{background:linear-gradient(150deg,#143879,#071c50);border-color:#5375b55c;box-shadow:inset 0 1px 0 #ffffff18,0 12px 28px #071c5010}.campaign-visual{border-top:1px solid #ffffff16}.campaign-visual img{height:300px}.problem-art,.premium-art,.work-art,.workflow-art,.about-art,.premium-team{border:1px solid #8ea9dc45;box-shadow:var(--art-shadow)}.premium-split,.team-editorial{gap:48px}.case-stage{padding:28px}.case-preview{border:1px solid #9bb1d744;box-shadow:0 3px 0 #122d5b,0 12px 25px #071c5015}.use-tab[aria-selected=true]{box-shadow:inset 0 1px 0 #ffffff24,0 2px 0 #1643b8}.fields span{background:#ffffff06;box-shadow:inset 0 1px 0 #ffffff08}.problem-chapter:last-child,#section-7 .card:first-child{background:linear-gradient(135deg,#edf3ff,#f7faff);border-color:#b8cdf3}.about-art{align-self:start}.work-chapters article{padding-bottom:28px}.work-chapters article:last-child{padding-bottom:0}.about-story{padding-top:28px;padding-bottom:28px}details{box-shadow:inset 0 1px 0 #ffffff06;background:linear-gradient(150deg,#ffffff04,#ffffff01)}details[open]{border-color:#8ba8dd66;background:#ffffff06}
@media(hover:hover) and (prefers-reduced-motion:no-preference){.card{transition:transform .18s,box-shadow .18s}.card:hover{transform:translateY(-3px);box-shadow:inset 0 1px 0 #fff,0 16px 30px #071c5010}.bento .campaign-card:first-child:hover{box-shadow:inset 0 1px 0 #ffffff18,0 16px 30px #071c5020}}
@media(max-width:1100px){.hero{gap:36px}.hero-motion{transform:none}.campaign-visual img{height:270px}}
@media(max-width:900px){.premium-split,.team-editorial{gap:28px}}
@media(max-width:600px){.hero{gap:28px}.icon3d{width:56px;height:56px}.steps .icon3d{width:48px;height:48px}.case-stage{padding:20px}.campaign-visual img{height:auto}.hero-motion{box-shadow:0 3px 0 #133674,0 7px 0 -3px #0b285d,0 16px 32px #020b2b30}.problem-art,.premium-art,.work-art,.workflow-art,.about-art,.premium-team{box-shadow:0 2px 0 #153979,0 6px 0 -3px #0b285d,0 16px 32px #030e2b18}}
@media(prefers-reduced-motion:reduce){.hero-motion{transform:none}.btn:hover,.card:hover{transform:none}}

/* Glass surfaces over restrained email-to-data backgrounds. */
.hero:before{background:radial-gradient(ellipse at 76% 45%,#205be832,transparent 65%),var(--navy)}
.hero:after{content:"";position:absolute;z-index:-1;top:0;bottom:0;left:50%;width:100vw;transform:translateX(-50%);background:url('https://www.extractmails.com/wp-content/uploads/2024/12/data-routes.svg') center/1100px auto no-repeat;opacity:.2;pointer-events:none;mask-image:linear-gradient(90deg,#000,transparent 38%,transparent 60%,#000)}
.hero-motion{background:#0b2358aa;border-color:#b8ceff60;backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px)}
.section:before{background:radial-gradient(ellipse at 90% 70%,#205be818,transparent 65%),var(--navy)}
.section.tone-light:before{background:radial-gradient(ellipse at 12% 20%,#205be809,transparent 50%),linear-gradient(145deg,#f8faff,#f1f5fc)}
#section-2:after,#use-cases:after,#about:after{content:"";position:absolute;z-index:-1;inset:0 auto 0 50%;width:100vw;transform:translateX(-50%);background:url('https://www.extractmails.com/wp-content/uploads/2024/12/data-routes.svg') right center/1200px auto no-repeat;opacity:.14;pointer-events:none;mask-image:linear-gradient(90deg,transparent 55%,#000)}
#section-1:after{content:"";position:absolute;z-index:-1;top:20px;right:-10vw;width:360px;height:260px;background:url('https://www.extractmails.com/wp-content/uploads/2024/12/email-data.webp') center/cover no-repeat;opacity:.07;pointer-events:none;mask-image:radial-gradient(ellipse,#000,transparent 72%)}
.card,.problem-chapter,.case-stage{background:#ffffffcf;border-color:#c8d5e9;backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);box-shadow:inset 0 1px 0 #fff,0 8px 24px #071c5009}
.bento .campaign-card:first-child{background:linear-gradient(150deg,#143879db,#071c50db);border-color:#b8ceff40;box-shadow:inset 0 1px 0 #ffffff20,0 12px 28px #071c5010}
.problem-chapter:last-child,#section-7 .card:first-child{background:#edf3ffcf;border-color:#adc4eb}.about-story{background:#ffffff04;padding-left:24px;padding-right:24px;border:1px solid #ffffff20;border-radius:12px;margin-bottom:16px;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);box-shadow:inset 0 1px 0 #ffffff08}.about-story:first-child{padding-top:24px}.about-story:last-child{padding-bottom:24px;border-bottom:1px solid #ffffff20;margin-bottom:0}.fields span,.use-tab{backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px)}details{background:#ffffff05;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border-color:#ffffff28}
@supports not (backdrop-filter:blur(1px)){.card,.problem-chapter,.case-stage{background:#fff}.about-story{background:#102859}}
@media(max-width:900px){.about-stories{gap:16px}.about-story{margin-bottom:0}.about-story:first-child{padding-top:24px}.about-story:last-child{border-bottom:1px solid #ffffff20}.hero:after{opacity:.12}#section-1:after{right:-20px;width:240px}}
@media(max-width:600px){.about-story{padding:22px}.about-story:first-child{padding-top:22px}.about-story:last-child{padding-bottom:22px}.hero:after{background-size:800px auto;mask-image:linear-gradient(transparent,#000 60%);opacity:.09}#section-2:after,#use-cases:after,#about:after{opacity:.08}.card,.problem-chapter,.case-stage{backdrop-filter:none;-webkit-backdrop-filter:none;background:#fffffff0}}

/* User-supplied luminous motion, composed behind the hero. */
.hero-video-background{position:absolute;z-index:-2;top:-100px;bottom:0;left:50%;width:100vw;transform:translateX(-50%);overflow:hidden;background:#071c50;pointer-events:none}.hero-video-background video{display:block;width:100%;height:100%;object-fit:cover;object-position:50% 55%;opacity:.68;filter:saturate(.9)}
.hero:before{top:-100px;background:linear-gradient(90deg,#071c50f5 0%,#071c50d9 30%,#071c5073 65%,#071c5052),linear-gradient(0deg,#071c50,transparent 36%,#071c504d)}.hero:after{display:none}.hero{padding-bottom:96px}.hero>div:first-of-type{max-width:none}.hero h1{max-width:13ch}.hero p{color:#e3ebfa}.hero-motion{background:#071c50b8;border-color:#bed7ff66;box-shadow:0 3px 0 #133674,0 16px 45px #020b2b40}
.background-motion-toggle{position:absolute;bottom:24px;right:0;display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:10px 14px;border:1px solid #ffffff40;border-radius:8px;background:#071c50b8;color:#e4edfc;font-size:13px;cursor:pointer;backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px)}.background-motion-toggle:hover{background:#153879}
#section-2:before,#about:before{background:linear-gradient(90deg,#071c50f7 5%,#071c50bb 58%,#071c50dd),radial-gradient(ellipse at 82% 20%,#709dff73,transparent 45%),linear-gradient(100deg,#071c50 32%,#1649ce 55%,#3977ff 63%,#1649ce 68%,#071c50 85%)}
#use-cases:before{background:linear-gradient(100deg,#071c50f5 15%,#071c50b8 62%,#071c50de),radial-gradient(ellipse at 65% 80%,#709dff66,transparent 48%),linear-gradient(80deg,#071c50 40%,#1649ce 55%,#3977ff 60%,#1749ce 67%,#071c50 82%)}
@media(max-width:1100px){.hero h1{max-width:20ch}.hero-video-background video{object-position:65% 50%}.hero:before{background:linear-gradient(90deg,#071c50e8,#071c5099),linear-gradient(0deg,#071c50,transparent 45%)}.hero{padding-bottom:88px}}
@media(max-width:600px){.hero-video-background{top:-84px}.hero:before{top:-84px;background:linear-gradient(90deg,#071c50e8,#071c50ad),linear-gradient(0deg,#071c50,transparent 50%)}.hero-video-background video{opacity:.55}.hero{padding-bottom:84px}.background-motion-toggle{bottom:20px;font-size:12px}.hero h1{max-width:18ch}}
@media(prefers-reduced-motion:reduce){.hero-video-background video{display:none}.hero-video-background{background:#071c50 url('https://www.extractmails.com/wp-content/uploads/2024/12/hero-gradient-poster.jpg') center/cover no-repeat}.hero-video-background video{opacity:.4}}

.hero>.hero-video-background{max-width:none}.hero>div:nth-of-type(2){min-width:0}@media(max-width:1100px){.hero>div:nth-of-type(2){max-width:760px}}


/* Lower-section effects retained. */
#section-2 .sectiontop{max-width:800px;text-align:center;margin:0 auto 48px}
#section-2 h2{font-weight:600;font-size:clamp(32px,3.4vw,44px);max-width:26ch;margin:auto}
#section-2 .premium-split{position:relative;gap:48px;align-items:center;padding:28px;border-radius:22px;border:1px solid #c0d6ff30;background:#071c5070;backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);box-shadow:inset 0 1px 0 #ffffff12,0 20px 40px #020b2b20}
#section-2:before{background:linear-gradient(180deg,#071c50,#0a2355 70%,#071c50)}
#section-2:after{left:50%;top:100px;bottom:auto;width:110vw;height:450px;background:radial-gradient(ellipse at 14% 70%,#709dff88,transparent 27%),radial-gradient(ellipse at 58% 50%,#205be8bb,transparent 42%),radial-gradient(ellipse at 88% 35%,#709dff80,transparent 20%);transform:translateX(-50%) rotate(-7deg);filter:blur(36px);opacity:.65;mask-image:linear-gradient(transparent,#000 40%,transparent)}
#section-2 .premium-art{box-shadow:none;border-color:#9bbdff44}
.fields span{background:#ffffff09;border-color:#ffffff30;border-radius:8px}
@media(max-width:900px){#section-2 .premium-split{padding:24px;gap:28px}
#section-2 .sectiontop{margin-bottom:32px}}
@media(max-width:600px){#section-2 .premium-split{padding:18px;gap:24px;border-radius:16px}
#section-2 h2{font-size:30px}
#section-2:after{opacity:.4}}

/* Palette used as light and accents, rather than solid section fills. */
:root{--blue:#0048C2;--blue-hover:#05289B;--pale:#F6FAFD;--electric:#0A7CF8;--sky:#6CB5FF;--ice:#D5EAFD;--border:#D4D4D580}
body{background:#071c50}.section:before{background:#071c50}.section.tone-light:before{background:#F6FAFD}.standalone-header{background:#071c50de;border-color:#D5EAFD30}.btn{background:linear-gradient(150deg,#0048C2,#0A7CF8);border-color:#6CB5FF60;box-shadow:inset 0 1px 0 #D5EAFD30,0 3px 0 #05289B,0 8px 18px #05289B18}.btn:hover{background:linear-gradient(150deg,#05289B,#0048C2)}.header-cta{background:#D5EAFD08;border-color:#D5EAFD40;box-shadow:none}.header-cta:hover{background:#D5EAFD14}
.hero-video-background video{opacity:.55}.hero:before{background:linear-gradient(90deg,#071c50f2 5%,#071c50c9 35%,#071c5066 72%,#071c5080),linear-gradient(0deg,#071c50,transparent 45%)}.hero-motion{border-color:#6CB5FF55;box-shadow:0 3px 0 #05289B80,0 24px 48px #020b2b30}
#section-2:before{background:radial-gradient(ellipse at 88% 65%,#0A7CF818,transparent 48%),#071c50}#section-2:after{opacity:.32;background:radial-gradient(ellipse at 18% 65%,#6CB5FF66,transparent 26%),radial-gradient(ellipse at 68% 45%,#0A7CF870,transparent 35%);filter:blur(42px)}#section-2 .premium-split{background:#D5EAFD04;border-color:#D5EAFD24;box-shadow:inset 0 1px 0 #D5EAFD10,0 16px 40px #0000000a}
#use-cases:before{background:radial-gradient(ellipse at 85% 75%,#0048C218,transparent 50%),#071c50}#about:before{background:radial-gradient(ellipse at 8% 45%,#0A7CF812,transparent 45%),#071c50}#use-cases:after,#about:after{opacity:.1}
.card,.problem-chapter,.case-stage{background:#ffffffec;border-color:#D4D4D580;box-shadow:inset 0 1px 0 #fff,0 8px 24px #05289B08}.problem-chapter:last-child,#section-7 .card:first-child{background:linear-gradient(135deg,#D5EAFD70,#F6FAFD);border-color:#6CB5FF55}.bento .campaign-card:first-child{background:linear-gradient(150deg,#0f2c62,#071c50);border-color:#6CB5FF35}.icon3d{box-shadow:inset 0 0 0 1px #D4D4D540,0 3px 0 #D5EAFD80,0 8px 16px #05289B09}.fields span{background:#D5EAFD06;border-color:#D5EAFD30}.use-tab[aria-selected=true]{background:#0048C2;border-color:#6CB5FF60;box-shadow:inset 0 1px 0 #D5EAFD20}.use-tab:not([aria-selected=true]){background:#D5EAFD04}.chapter-number,.work-number,.step-number{color:#0048C2}.section.tone-light .kicker{color:#0048C2}.problem-art,.premium-art,.work-art,.workflow-art,.about-art,.premium-team{border-color:#6CB5FF35;box-shadow:0 2px 0 #05289B55,0 16px 32px #020b2b18}.about-story{background:#D5EAFD04;border-color:#D5EAFD20}a:focus-visible,button:focus-visible,summary:focus-visible,input:focus-visible{outline-color:#6CB5FF}
@media(max-width:600px){.hero-video-background video{opacity:.45}.hero:before{background:linear-gradient(90deg,#071c50e8,#071c50ad),linear-gradient(0deg,#071c50,transparent 50%)}.card,.problem-chapter,.case-stage{background:#fff}#section-2:after{opacity:.2}}

/* Palette accents confined to the existing dark sections. */
.section.tone-dark:before{background:radial-gradient(ellipse at 85% 60%,#0048C21C,transparent 55%),#071c50}
#section-2:before{background:radial-gradient(ellipse at 82% 60%,#0A7CF826,transparent 50%),linear-gradient(125deg,#071c50 45%,#05289B24)}
#use-cases:before{background:radial-gradient(ellipse at 90% 80%,#0048C22B,transparent 50%),#071c50}
#teams:before{background:radial-gradient(ellipse at 78% 12%,#0A7CF81C,transparent 46%),#071c50}
#about:before{background:radial-gradient(ellipse at 8% 45%,#0A7CF81E,transparent 45%),#071c50}
#faq:before{background:radial-gradient(ellipse at 88% 100%,#0048C21F,transparent 50%),#071c50}
.section.tone-dark .kicker{color:#6CB5FF}.section.tone-dark .fields span{background:#D5EAFD08;border-color:#6CB5FF35;color:#D5EAFD}.section.tone-dark .about-story{background:linear-gradient(145deg,#D5EAFD07,#D5EAFD02);border-color:#6CB5FF30}.section.tone-dark details{border-color:#D5EAFD30;background:#D5EAFD04}

/* Brand-matched integration destinations */
#integrations{background:#fff;isolation:isolate;position:relative;color:#05289B}#integrations::before{content:"";position:absolute;inset:0;z-index:-1;pointer-events:none;background:radial-gradient(ellipse at 90% 15%,#D5EAFD99,transparent 52%)}#integrations .sectiontop{max-width:780px}#integrations h2{color:#05289B}#integrations .kicker{color:#0048C2}.integrations-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}.integration-card{padding:28px;border:1px solid #D5EAFD;border-radius:18px;background:linear-gradient(135deg,#fff,#F6FAFDdd);box-shadow:0 8px 24px #05289B08;transition:transform .2s,border-color .2s,box-shadow .2s;min-width:0}.integration-card:hover{transform:translateY(-4px);border-color:#6CB5FF;box-shadow:0 14px 32px #05289B10}.integration-card-head{display:flex;align-items:center;gap:14px;margin-bottom:24px}.integration-card h3{font-size:1.15rem;margin:0;color:#05289B}.integration-icon{display:grid;place-items:center;width:48px;height:48px;flex-shrink:0;color:#0048C2;border:1px solid #D5EAFD;border-radius:12px;background:linear-gradient(145deg,#fff,#D5EAFD)}.integration-icon svg{width:25px;height:25px}.integration-route{margin:0!important;padding:12px 14px;background:#D5EAFD55;border-radius:8px;font-size:.9rem!important;color:#05289B!important;overflow-wrap:anywhere;line-height:1.6}.integration-tags{margin:14px 0 22px!important;font-size:.875rem!important;color:#0048C2!important;line-height:1.6}.integration-detail{border-top:1px solid #D5EAFD;padding-top:16px}.integration-detail summary{cursor:pointer;color:#0048C2;font-weight:600;font-size:.9rem;min-height:44px;display:flex;align-items:center;justify-content:space-between;list-style:none}.integration-detail summary::after{content:"+";font-size:1.3rem}.integration-detail[open] summary::after{content:"−"}.integration-detail summary:focus-visible{outline:2px solid #0A7CF8;outline-offset:5px;border-radius:4px}.integration-detail p{color:#05289B!important;font-size:.95rem!important;margin:8px 0 0!important;line-height:1.7}@media(max-width:1000px){.integrations-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.integrations-grid{grid-template-columns:1fr;gap:16px}.integration-card{padding:22px}}@media(prefers-reduced-motion:reduce){.integration-card{transition:none}.integration-card:hover{transform:none}}

/* Compact integration showcase immediately below the hero */
#integrations.integrations-section{width:100vw;max-width:none;margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);padding:56px max(24px,calc((100vw - 1200px)/2));background:#fff;border-radius:0;border-top:1px solid #D5EAFD;border-bottom:1px solid #D5EAFD;overflow:hidden;box-shadow:none;transform:none}#integrations::before{background:radial-gradient(ellipse at 50% 100%,#D5EAFD70,transparent 65%)}#integrations .sectiontop{margin:0 auto 32px;max-width:740px;text-align:center;display:block}#integrations .kicker{display:block;font-size:.875rem;font-weight:600;letter-spacing:.06em;margin-bottom:10px}#integrations h2{font-size:clamp(24px,2.5vw,32px);line-height:1.3;letter-spacing:-.7px}#integrations .integration-subtitle{color:#52647f;font-size:1rem;margin:12px auto 0;max-width:600px}#integrations .integrations-grid{grid-template-columns:repeat(5,minmax(0,1fr));gap:16px}#integrations .integration-card{padding:24px 16px;text-align:center;border-radius:14px;background:#ffffffd9;box-shadow:0 4px 16px #05289B04;backdrop-filter:blur(8px)}#integrations .integration-card-head{flex-direction:column;gap:14px;margin:0}#integrations .integration-icon{width:52px;height:52px;border-radius:14px}#integrations .integration-icon svg{width:28px;height:28px}#integrations .integration-card h3{font-size:1rem;line-height:1.4}#integrations .integration-route{background:none;border-radius:0;padding:0;margin:10px 0 0!important;font-size:.875rem!important;color:#52647f!important;line-height:1.5}#integrations .integration-card:hover{box-shadow:0 10px 24px #05289B0c;transform:translateY(-3px)}@media(max-width:900px){#integrations .integrations-grid{grid-template-columns:repeat(6,minmax(0,1fr))}#integrations .integration-card{grid-column:span 2}#integrations .integration-card:nth-child(4){grid-column:2/span 2}}@media(max-width:600px){#integrations.integrations-section{padding:40px 20px}#integrations .sectiontop{margin-bottom:24px}#integrations .integrations-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}#integrations .integration-card{grid-column:auto;padding:20px 12px}#integrations .integration-card:nth-child(4){grid-column:auto}#integrations .integration-card:last-child{grid-column:1/-1;max-width:none}#integrations .integration-card:last-child .integration-card-head{flex-direction:row;justify-content:center}#integrations .integration-card:last-child .integration-route{margin-top:8px!important}}@media(prefers-reduced-motion:reduce){#integrations .integration-card:hover{transform:none}}

/* Uploaded white brand symbol */
.brand-logo{background:transparent;padding:0}

/* Original integration brand assets */
#integrations .integration-icon:has(.integration-brand-logo){width:116px;height:58px;border:0;background:none;border-radius:0}#integrations .integration-brand-logo{display:block;max-width:116px;max-height:58px;width:auto;height:auto;object-fit:contain;filter:none}#integrations .integration-card-head{min-height:94px}#integrations .integration-icon:has(img[alt="Google Sheets"]){width:52px}#integrations img[alt="Google Sheets"]{height:44px;width:auto}

/* Center the integration heading and all destination cards */
#integrations .sectiontop{text-align:center;margin-inline:auto}
#integrations .integrations-grid{display:flex;flex-wrap:wrap;justify-content:center;gap:16px;max-width:1040px;margin-inline:auto}
#integrations .integration-card{flex:0 1 calc((100% - 48px)/4);text-align:center}
#integrations .integration-card-head{align-items:center;justify-content:center}
@media(max-width:900px){#integrations .integration-card{flex-basis:calc((100% - 16px)/2)}}
@media(max-width:600px){#integrations .integrations-grid{gap:12px}#integrations .integration-card{flex-basis:calc((100% - 12px)/2)}#integrations .integration-card:last-child .integration-card-head{flex-direction:column}}

/* Account navigation */
.header-account{display:flex;align-items:center;gap:10px;flex-shrink:0}.header-account .btn{min-height:44px;padding:10px 18px;border-radius:999px}.header-account .account-login{background:transparent;border:1px solid #ffffff60;color:#fff}.header-account .account-login:hover{background:#ffffff10;border-color:#6CB5FF}
@media(max-width:1000px){header{gap:12px;padding-inline:16px}nav a{padding-inline:8px}.header-account .btn{padding-inline:14px}}
@media(max-width:600px){header{flex-wrap:wrap;gap:10px}.header-account{order:2;margin-left:auto;gap:8px}.header-account .btn{font-size:13px;padding:10px 12px}.standalone-header .menu-toggle{order:3}.standalone-header nav{order:4}}

/* Refined navigation with supplied envelope-E symbol */
header{min-height:80px;gap:24px;padding:14px 24px}.standalone-header .brand-logo{width:42px;height:46px;object-fit:contain;border-radius:0}#main-nav{gap:24px;align-items:center}.nav-dropdown{position:relative;border:0;background:none;padding:0;color:#fff}.nav-dropdown summary,.nav-pending{display:flex;align-items:center;gap:8px;padding:10px 8px;font-size:14px;line-height:24px;font-weight:500;color:#D5EAFD;list-style:none;border-radius:8px;white-space:nowrap}.nav-dropdown summary{cursor:pointer}.nav-dropdown summary::-webkit-details-marker{display:none}.nav-dropdown summary:hover,.nav-dropdown[open]>summary{color:white;background:#ffffff0c}.nav-dropdown summary span{font-size:16px;transition:transform .15s}.nav-dropdown[open] summary span{transform:rotate(180deg)}.nav-pending{cursor:default}.nav-dropdown-menu{position:absolute;top:calc(100% + 10px);left:0;min-width:205px;padding:8px;background:#fff;border:1px solid #D5EAFD;border-radius:12px;box-shadow:0 12px 30px #00000020}.nav-dropdown-menu a{display:block;color:#132B50;padding:12px;font-size:14px;border-radius:6px}.nav-dropdown-menu a:hover{background:#F6FAFD;color:#0048C2}.standalone-header .header-account .account-signup{background:#0048C2}.standalone-header .header-account .account-signup:hover{background:#05289B}
@media(max-width:1000px){header{gap:14px;padding-inline:18px}#main-nav{gap:12px}}
@media(max-width:900px){header{flex-wrap:wrap}#main-nav{width:100%;justify-content:center}.nav-dropdown-menu{top:100%}.header-account{margin-left:auto}}
@media(max-width:600px){header{padding:12px 14px;gap:8px}.standalone-header .brand-logo{width:34px;height:38px}#main-nav{justify-content:flex-start;flex-wrap:wrap;gap:8px}.nav-dropdown-menu{position:static;min-width:160px;margin-top:8px}.nav-dropdown[open]{flex-basis:100%}.nav-pending,.nav-dropdown summary{font-size:14px}.header-account .btn{padding-inline:10px}}

/* Uploaded workflow video and reference mega menus */
.hero-motion video{display:block;width:100%;height:100%;object-fit:contain;background:#071C50}
#main-nav .nav-dropdown{position:static}.standalone-header{position:sticky}.nav-dropdown-menu.mega-menu{left:auto;right:24px;top:calc(100% + 8px);width:min(600px,calc(100vw - 48px));min-width:0;padding:20px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 24px;border-radius:20px;border:1px solid #D5EAFD;background:linear-gradient(110deg,#F6FAFD,#D5EAFD);box-shadow:0 16px 36px #071C501C;color:#132B50}.mega-item{display:flex;align-items:center;gap:14px;padding:8px 6px;min-height:62px;line-height:1.45;font-size:15px;font-weight:500;cursor:default}.mega-item:nth-child(even){border-left:1px solid #05289B15;padding-left:24px}.mega-icon{width:44px;height:44px;flex-shrink:0;display:grid;place-items:center;color:white;border-radius:10px;background:linear-gradient(135deg,#0048C2,#0A7CF8);box-shadow:0 3px 8px #05289B18}.mega-icon svg{width:24px;height:24px}
@media(max-width:900px){.nav-dropdown-menu.mega-menu{right:18px;width:min(600px,calc(100vw - 36px))}}
@media(max-width:600px){.nav-dropdown-menu.mega-menu{position:static;width:100%;padding:12px;grid-template-columns:1fr;gap:4px;border-radius:14px;margin-top:8px}.mega-item{font-size:14px;min-height:56px}.mega-item:nth-child(even){padding-left:6px;border-left:0}.mega-icon{width:38px;height:38px}.mega-icon svg{width:22px;height:22px}}

.nav-dropdown:not([open])>.mega-menu{display:none}

#main-nav>.nav-blog-link{padding:10px 8px;color:#D5EAFD;font-weight:500;font-size:14px}#main-nav>.nav-blog-link:hover{color:white;background:#ffffff0c}
/* === reference-hero.css === */
#home.hero.reference-hero{display:grid;grid-template-columns:minmax(0,.95fr) minmax(0,1.25fr);gap:48px;align-items:center;min-height:640px;padding:64px 0 72px;perspective:none;isolation:isolate}#home.reference-hero::before{display:block;position:absolute;inset:0 auto 0 50%;width:100vw;transform:translateX(-50%);z-index:-1;background:radial-gradient(ellipse at 91% 35%,#6CB5FF70,transparent 55%),linear-gradient(120deg,#FFFFFF,#F6FAFD 55%,#D5EAFD)}#home.reference-hero::after{display:none}.reference-hero-copy{min-width:0}.reference-hero-symbol{display:block;width:44px;height:48px;object-fit:contain;margin-bottom:24px;filter:brightness(0) saturate(100%) invert(17%) sepia(90%) saturate(4000%) hue-rotate(214deg)}#home.reference-hero h1{font-size:clamp(34px,3.6vw,50px);line-height:1.13;letter-spacing:-1.5px;color:#071C50;margin:0 0 24px}#home.reference-hero h1 span{color:#0048C2}#home.reference-hero p{color:#52647F;font-size:16px;line-height:1.75;margin:0;max-width:55ch}.reference-signin{display:grid;gap:12px;margin-top:28px;max-width:430px}.provider-button{display:flex;align-items:center;justify-content:flex-start;gap:18px;width:100%;min-height:60px;padding:14px 22px;border-radius:12px;font:600 16px/1.4 "Segoe UI",Arial,sans-serif;border:1px solid #D5EAFD;box-shadow:inset 0 1px 0 #FFFFFFa0,0 8px 24px #05289B0c;opacity:1;cursor:default}.google-provider{background:#fff;color:#132B50}.microsoft-provider{background:linear-gradient(135deg,#0048C2,#0A7CF8);color:#fff;border-color:#0A7CF8}.provider-letter{font-size:27px;font-weight:700;color:#0048C2;width:24px;text-align:center}.provider-button svg{flex-shrink:0}#home.reference-hero .provider-note{font-size:12px;color:#52647F;margin-top:10px;line-height:1.5}.reference-account-links{display:flex;gap:24px;margin-top:18px;font-size:14px;font-weight:600}.reference-account-links a{color:#0048C2;padding-block:6px}.reference-account-links a:hover{text-decoration:underline}.reference-hero-visual{position:relative;min-width:0;padding:14px;border:1px solid #FFFFFFd0;background:linear-gradient(135deg,#ffffff90,#D5EAFD65);border-radius:24px;backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);box-shadow:inset 0 1px 0 #fff,0 24px 60px #0048C225}.reference-hero-visual .hero-motion{border-radius:16px;aspect-ratio:16/9;border-color:#D5EAFD;transform:none;box-shadow:0 10px 28px #05289B20}.reference-hero-visual .hero-motion:hover{transform:none}.reference-hero-visual video{width:100%;height:100%;object-fit:contain;display:block}@media(max-width:1000px){#home.hero.reference-hero{gap:28px;padding-block:48px}#home.reference-hero h1{font-size:38px}}@media(max-width:760px){#home.hero.reference-hero{grid-template-columns:1fr;gap:32px;min-height:0;padding-block:40px}#home.reference-hero h1{font-size:clamp(32px,6vw,42px);max-width:20ch}.reference-signin{max-width:none}.reference-hero-visual{padding:10px;border-radius:18px}.reference-hero-symbol{margin-bottom:20px}.provider-button{min-height:56px;font-size:15px}}@media(prefers-reduced-motion:reduce){.reference-hero-visual *{transition:none!important;transform:none!important}}
/* Supplied envelope wavescape */
#home.reference-hero::before{background:linear-gradient(90deg,#071C5045,transparent 75%),url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') center center/cover no-repeat,#071C50}
#home.reference-hero h1{color:#FFFFFF}
#home.reference-hero h1 span{color:#6CB5FF}
#home.reference-hero p{color:#D5EAFD}
.reference-hero-visual{background:linear-gradient(135deg,#ffffff18,#D5EAFD0a);border-color:#6CB5FF60;box-shadow:inset 0 1px 0 #ffffff40,0 24px 60px #020b2b45}
@media(max-width:760px){#home.reference-hero::before{background:linear-gradient(180deg,#071C5060,#071C5030),url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') 65% center/cover no-repeat,#071C50}}

/* Continue the supplied background through the header */
body{background:#071C50}
.provider-google-logo{display:block;width:24px;height:24px;object-fit:contain;flex-shrink:0}
#home.hero.reference-hero{grid-template-columns:minmax(0,1.05fr) minmax(0,1.25fr);gap:40px}
#home.reference-hero h1{font-size:clamp(34px,3.6vw,48px);letter-spacing:-1.4px;text-wrap:balance}
#home.reference-hero .provider-button{min-height:60px}
@media(max-width:760px){#home.hero.reference-hero{grid-template-columns:1fr;gap:32px}#home.reference-hero h1{font-size:clamp(32px,6vw,42px)}}

/* A continuous header/hero background with a transparent glass navbar. */
body{background:#071C50}
body>header::before{display:none}
#main-nav .nav-dropdown summary,#main-nav>.nav-blog-link,#main-nav .nav-pending{background:transparent;color:#F5F8FF}
#main-nav .nav-dropdown summary{gap:7px;padding:10px 12px;line-height:24px}
#main-nav .nav-dropdown summary span{display:inline-flex;align-items:center;line-height:1;transform:translateY(-1px)}
#main-nav .nav-dropdown[open] summary span{transform:translateY(-1px) rotate(180deg)}
#main-nav .nav-dropdown summary:hover,#main-nav .nav-dropdown[open]>summary,#main-nav>.nav-blog-link:hover{background:#ffffff0c}
body>header .header-account .account-login{background:#ffffff06;border-color:#D5EAFD55;box-shadow:inset 0 1px 0 #ffffff18}
body{background:#071C50}
body>header::before{display:none}
#home.reference-hero::before{background:linear-gradient(90deg,#071C5020,transparent 75%),url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') center center/cover no-repeat,#071C50}
@media(max-width:760px){#home.reference-hero::before{background:linear-gradient(180deg,#071C5030,#071C5030),url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') center center/cover no-repeat,#071C50}}
/* === home-glow.css === */
/* Homepage lighting derives from the supplied blue-wave hero artwork. */
.homepage-glow{--glow-navy:#071C50;--glow-blue:#0048C2;--glow-electric:#0A7CF8;--glow-sky:#6CB5FF;--glow-ice:#D5EAFD;--glow-panel:linear-gradient(135deg,#132E67ec,#071C50f5 75%);--glow-panel-shadow:inset 0 1px 0 #D5EAFD55,inset 0 -1px 0 #6CB5FF18,0 18px 42px #020b2b40,0 0 36px #0A7CF824}
/* The waves shine at the perimeter; headings retain a quiet navy background. */
.homepage-glow .section.tone-dark::before,
.homepage-glow #section-2::before,.homepage-glow #use-cases::before,
.homepage-glow #teams::before,.homepage-glow #about::before,.homepage-glow #faq::before{
  background:radial-gradient(ellipse at 50% 5%,#071C50ed,transparent 68%),
    linear-gradient(180deg,#071C50dc 0%,#071C509c 38%,#071C5040 85%,#071C5080),
    url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') center bottom/cover no-repeat,#071C50;
  pointer-events:none;
}
/* Remove older decorative overlays competing with the supplied wave artwork. */
.homepage-glow .section.tone-dark::after,.homepage-glow #section-2::after,
.homepage-glow #use-cases::after,.homepage-glow #about::after{display:none}
.homepage-glow .section.tone-dark h2{color:#FFFFFF}
.homepage-glow .section.tone-dark p{color:#D5EAFD}
.homepage-glow .section.tone-dark .eyebrow,.homepage-glow .section.tone-dark .kicker{color:#6CB5FF}
/* Darker glass interiors separate readable copy from the luminous backdrop. */
.homepage-glow #section-2 .premium-split,.homepage-glow .section.tone-dark .about-story,.homepage-glow #faq details{
  border-color:#6CB5FF70;border-top-color:#D5EAFD8c;
  background:var(--glow-panel);box-shadow:var(--glow-panel-shadow);
}
.homepage-glow #section-2 .premium-split{backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px)}
.homepage-glow #section-2 .premium-copy p,.homepage-glow .section.tone-dark .about-story p,.homepage-glow #faq details p{color:#D5EAFD}
.homepage-glow #faq summary{color:#F5F8FF}
.homepage-glow #faq details[open]{border-color:#6CB5FFa6;border-top-color:#D5EAFD;box-shadow:var(--glow-panel-shadow),0 0 24px #0A7CF818}
.homepage-glow .section.tone-dark .fields span{background:#0D2B64;color:#F5F8FF;border-color:#6CB5FF66;box-shadow:inset 0 1px 0 #D5EAFD20;backdrop-filter:none;-webkit-backdrop-filter:none}
/* Keep white sections bright: blue appears at the perimeter and on controls. */
.homepage-glow .section.tone-light::before{background:radial-gradient(ellipse at 94% 6%,#6CB5FF12,transparent 48%),#F6FAFD}
.homepage-glow #integrations::before{background:radial-gradient(ellipse at 50% 100%,#6CB5FF28,transparent 65%)}
.homepage-glow .section.tone-light .card,.homepage-glow .problem-chapter,.homepage-glow #use-cases .case-panel,.homepage-glow #integrations .integration-card{border-color:#6CB5FF48;box-shadow:inset 0 1px 0 #FFFFFF,0 8px 24px #05289B08,0 0 20px #0A7CF805}
.homepage-glow .section.tone-dark .card{border-color:#6CB5FF70;border-top-color:#D5EAFD80;box-shadow:var(--glow-panel-shadow)}
.homepage-glow .card:hover,.homepage-glow .problem-chapter:hover,.homepage-glow #integrations .integration-card:hover{border-color:#6CB5FF9c;box-shadow:inset 0 1px 0 #FFFFFF40,0 14px 32px #05289B16,0 0 26px #0A7CF816}
.homepage-glow .section.tone-light .bento .campaign-card:first-child{background:radial-gradient(ellipse at 85% 0%,#0A7CF829,transparent 60%),linear-gradient(150deg,#0F2C62,#071C50);color:#FFFFFF;box-shadow:inset 0 1px 0 #D5EAFD26,0 12px 30px #05289B18}
.homepage-glow .section.tone-light .bento .campaign-card:first-child h3{color:#FFFFFF}
.homepage-glow .section.tone-light .bento .campaign-card:first-child p{color:#D5EAFD}
.homepage-glow #use-cases .card.case-panel{background:#FFFFFF;color:#132B50}
.homepage-glow #use-cases .card.case-panel h3{color:#132B50}
.homepage-glow #use-cases .card.case-panel p{color:#52647F}
/* Blue accents carry the same light direction through the workflow and CTA. */
.homepage-glow .btn:not(.account-login),.homepage-glow .use-tab[aria-selected=true]{background:linear-gradient(180deg,#0048C2,#05289B);color:#FFFFFF;border-color:#6CB5FF85;border-top-color:#D5EAFDb3;box-shadow:inset 0 1px 0 #D5EAFD65,0 4px 18px #0A7CF835}
.homepage-glow .btn:not(.account-login):hover{background:linear-gradient(180deg,#0048C2,#05289B);box-shadow:inset 0 1px 0 #D5EAFD80,0 6px 24px #0A7CF848}
.homepage-glow .use-tab:not([aria-selected=true]){background:#071C50e6;border-color:#6CB5FF66;color:#F5F8FF}
.homepage-glow .use-tab:focus-visible{outline-color:#6CB5FF}
.homepage-glow .steps{border-left-color:#6CB5FF70}
.homepage-glow .steps .card::before{background:#0A7CF8;box-shadow:0 0 0 5px #D5EAFD,0 0 16px #0A7CF840}
.homepage-glow .problem-art,.homepage-glow .premium-art,.homepage-glow .work-art,.homepage-glow .workflow-art,.homepage-glow .about-art,.homepage-glow .premium-team,.homepage-glow #section-2 .premium-art{border-color:#6CB5FF80;border-top-color:#D5EAFD99;box-shadow:0 2px 0 #05289B66,0 16px 36px #020b2b30,0 0 32px #0A7CF82b}
.homepage-glow #home .reference-hero-visual{border-color:#6CB5FF85;border-top-color:#D5EAFD99;box-shadow:inset 0 1px 0 #D5EAFD65,0 22px 48px #020b2b45,0 0 40px #0A7CF82b}
.homepage-glow #main-nav .mega-menu{background:linear-gradient(125deg,#FFFFFFfa,#D5EAFDfa);border-color:#6CB5FF80;box-shadow:inset 0 1px 0 #FFFFFF,0 14px 36px #020b2b26,0 0 28px #0A7CF813}
.homepage-glow .mega-icon{background:linear-gradient(135deg,#0048C2,#0A7CF8);box-shadow:inset 0 1px 0 #D5EAFD75,0 4px 14px #0A7CF81f}
.homepage-glow .reference-footer{background:radial-gradient(ellipse at 5% 0%,#6CB5FF25,transparent 60%),#F6FAFD}
.homepage-glow .reference-footer-panel{border-color:#6CB5FF35;box-shadow:inset 0 1px 0 #FFFFFF,0 12px 32px #05289B0a,0 0 26px #0A7CF807}
.homepage-glow .reference-footer-column h2{background:linear-gradient(110deg,#05289B,#0048C2);box-shadow:inset 0 1px 0 #D5EAFD50,0 4px 14px #0A7CF818}
/* The supplied light video keeps a matching light surface while loading. */
.homepage-glow #home .hero-motion,.homepage-glow #hero-workflow-video{background:#F5F8FF}
@media(hover:hover) and (pointer:fine){.homepage-glow .section.tone-dark .card:hover{box-shadow:var(--glow-panel-shadow),0 0 26px #0A7CF824}}
@media(max-width:600px){
  .homepage-glow .section.tone-dark::before,.homepage-glow #section-2::before,
  .homepage-glow #use-cases::before,.homepage-glow #teams::before,
  .homepage-glow #about::before,.homepage-glow #faq::before{
    background:radial-gradient(ellipse at 95% 90%,#0A7CF845,transparent 55%),
      linear-gradient(180deg,#071C50f2 15%,#071C5070 85%,#071C50a6),
      url('https://www.extractmails.com/wp-content/uploads/2024/12/bg-hmpage.jpg') center bottom/auto 600px no-repeat,#071C50;
  }
  .homepage-glow #section-2 .premium-split,.homepage-glow .section.tone-dark .about-story,.homepage-glow #faq details{
    backdrop-filter:none;-webkit-backdrop-filter:none;background:linear-gradient(135deg,#132E67,#071C50);
    box-shadow:inset 0 1px 0 #D5EAFD55,0 10px 24px #020b2b30,0 0 22px #0A7CF81c;
  }
  .homepage-glow .card:hover{box-shadow:inset 0 1px 0 #D5EAFD30,0 8px 20px #05289B10}
}
@supports not (backdrop-filter:blur(22px)){
  .homepage-glow #section-2 .premium-split,.homepage-glow .section.tone-dark .about-story,.homepage-glow #faq details{background:#102859}
}
/* === premium.css === */
/* Shared polish layer: brand colors and page structure are preserved. */
:root{--premium-blue:#0048C2;--premium-sky:#6CB5FF;--premium-ice:#D5EAFD;--premium-navy:#071C50;--premium-shadow:0 12px 32px #05289B0c;--premium-highlight:inset 0 1px 0 #ffffff90}
.site-header{background:linear-gradient(125deg,#071C50ee,#071C50e8);border-color:#D5EAFD30;backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);box-shadow:inset 0 1px 0 #ffffff16,0 12px 32px #020b2b18}
.nav-dropdown-menu.mega-menu,.site-header .mega-menu{background:linear-gradient(125deg,#FFFFFFf2,#D5EAFDe8);backdrop-filter:blur(26px);-webkit-backdrop-filter:blur(26px);border-color:#ffffffc0;box-shadow:inset 0 1px 0 #fff,0 18px 40px #071C5020;animation:premium-menu-in .18s ease-out}
@keyframes premium-menu-in{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.mega-icon{border:1px solid #ffffff40;box-shadow:inset 0 1px 0 #ffffff80,inset 0 -1px 0 #05289B40,0 5px 12px #05289B1a}
body:not(:has(.index-view)) .hero{perspective:1200px}.hero-motion{isolation:isolate;overflow:hidden;border-color:#72A0FF60;box-shadow:0 1px 0 #ffffff45 inset,0 16px 40px #020b2b30,0 0 44px #0048C212;transform:perspective(1400px) rotateY(-2deg);transition:transform .28s ease,border-color .28s ease}.hero-motion:hover{transform:perspective(1400px) rotateY(0);border-color:#6CB5FF90}.hero-motion video{border-radius:inherit}
/* Reflections stay on the surfaces rather than obscuring readable copy. */
.btn,.button-primary,.submit{position:relative;isolation:isolate;overflow:hidden;border-top-color:#ffffff50;background-image:linear-gradient(180deg,#ffffff14,transparent 58%);box-shadow:inset 0 1px 0 #ffffff35,0 6px 16px #0048C220;transition:transform .18s ease,background-color .18s ease,box-shadow .18s ease}
.btn::after,.button-primary::after,.submit::after{content:"";position:absolute;z-index:0;inset:-70% auto -70% -55%;width:30%;background:linear-gradient(90deg,transparent,#ffffff25,transparent);transform:skewX(-22deg);pointer-events:none}
.btn:hover,.button-primary:hover,.submit:hover{transform:translateY(-2px);box-shadow:inset 0 1px 0 #ffffff45,0 9px 22px #0048C22b}
.btn:hover::after,.button-primary:hover::after,.submit:hover::after{animation:premium-shine .65s ease-out}
@keyframes premium-shine{to{left:130%}}
.btn:active,.button-primary:active,.submit:active{transform:translateY(0)}
.header-account .account-login,.site-header .account-login{background-image:linear-gradient(180deg,#ffffff0e,transparent);box-shadow:inset 0 1px 0 #ffffff20}
/* Light sections retain their white surfaces. */
.section.tone-light .card,#integrations .integration-card{background:linear-gradient(145deg,#ffffffed,#F6FAFDe6);border-color:#D5EAFD;backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);box-shadow:var(--premium-highlight),var(--premium-shadow);transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease}
.section.tone-dark .card{background:linear-gradient(145deg,#D5EAFD0c,#D5EAFD04);border-color:#6CB5FF30;backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px);box-shadow:inset 0 1px 0 #ffffff12,0 12px 28px #020b2b18}
.card,.integration-card,.article-card{transform-style:preserve-3d;will-change:auto}
.premium-tilt{transform:perspective(1100px) rotateX(var(--tilt-x,0deg)) rotateY(var(--tilt-y,0deg)) translateY(var(--tilt-lift,0px))!important}
.card:hover,.integration-card:hover,.article-card:hover{border-color:#6CB5FF90;box-shadow:inset 0 1px 0 #ffffff90,0 18px 38px #05289B12}
.section.tone-dark .card{color:#F5F8FF}.section.tone-dark .card h3{color:#F5F8FF}.section.tone-dark .card p{color:#D5EAFD}.section.tone-dark .card:hover{box-shadow:inset 0 1px 0 #ffffff20,0 16px 32px #020b2b26}
.section.tone-dark details{background:linear-gradient(135deg,#D5EAFD08,#D5EAFD03);border-color:#D5EAFD24;box-shadow:inset 0 1px 0 #ffffff0d}
.campaign-visual,.about-art,.work-art{border-radius:16px;box-shadow:0 14px 32px #071C5012;overflow:hidden}
/* Blog: depth without reducing reading contrast. */
.index-view .hero{box-shadow:inset 0 1px 0 #ffffff25,0 16px 40px #071C5015}.hero-art{border-color:#ffffff50;box-shadow:inset 0 1px 0 #fff,0 12px 28px #071C5025}.search-form{box-shadow:inset 0 1px 0 #fff,0 10px 26px #071C5012}.article-card{background:linear-gradient(145deg,#fff,#F6FAFD);box-shadow:inset 0 1px 0 #fff,0 10px 26px #071C5008}.toc-box{background:#fffffff0;backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);box-shadow:inset 0 1px 0 #fff,0 10px 26px #071C5008}.article-callout{box-shadow:inset 0 1px 0 #ffffff20,0 8px 24px #071C5010}
/* Account forms remain opaque where users read and enter data. */
.brand-panel{box-shadow:inset -1px 0 0 #6CB5FF24}.form-panel{background:linear-gradient(135deg,#fff,#F6FAFD)}.form-wrap{padding:32px;border:1px solid #D5EAFD;border-radius:18px;background:#ffffffee;box-shadow:inset 0 1px 0 #fff,0 12px 36px #05289B0c}input{box-shadow:inset 0 1px 2px #071C5004}input:focus{box-shadow:0 0 0 3px #6CB5FF20}
.reference-footer-panel{background:linear-gradient(135deg,#ffffffed,#fffffff7);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-color:#ffffff;box-shadow:inset 0 1px 0 #fff,0 16px 40px #05289B08}.reference-footer-column h2{box-shadow:inset 0 1px 0 #ffffff40,0 4px 12px #0048C212}.reference-footer-column:last-child .reference-footer-items>a{transition:background .18s,border-color .18s,transform .18s}.reference-footer-column:last-child .reference-footer-items>a:hover{transform:translateY(-1px)}
@supports not (backdrop-filter:blur(20px)){.site-header{background:#071C50}.nav-dropdown-menu.mega-menu,.site-header .mega-menu,.reference-footer-panel,.toc-box{background:#fff}.section.tone-light .card,#integrations .integration-card{background:#fff}.section.tone-dark .card{background:#0c255e}}
@media(max-width:900px){.hero-motion{transform:none}.form-wrap{padding:26px}.nav-dropdown-menu.mega-menu,.site-header .mega-menu{backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}}
@media(max-width:600px){.hero-motion,.premium-tilt{transform:none!important}.card,.integration-card,.article-card{backdrop-filter:none;-webkit-backdrop-filter:none}.form-wrap{padding:24px 20px}.reference-footer-panel{backdrop-filter:none;-webkit-backdrop-filter:none}.btn:hover,.submit:hover{transform:none}}
@media(prefers-reduced-motion:reduce){.hero-motion,.premium-tilt,.btn,.button-primary,.submit,.card,.integration-card,.article-card{transform:none!important;transition:none!important}.nav-dropdown-menu.mega-menu,.site-header .mega-menu,.btn::after,.button-primary::after,.submit::after{animation:none!important}.btn::after,.button-primary::after,.submit::after{display:none}}

/* Centered FAQ composition */
#faq .sectiontop{max-width:860px;margin:0 auto 36px;text-align:center}
#faq .faqs{width:100%;max-width:860px;margin-inline:auto}
#faq details summary{justify-content:center;text-align:center;position:relative;padding-inline:52px}
#faq details summary::after{position:absolute;right:20px}
#faq details p{text-align:center;max-width:70ch;margin-inline:auto}
@media(max-width:600px){#faq .sectiontop{margin-bottom:24px}#faq details summary{padding-inline:40px}#faq details summary::after{right:14px}}

/* Original hero: explicit readable foregrounds */
#home.hero h1{color:#FFFFFF;opacity:1}
#home.hero p{color:#D5EAFD;opacity:1}
#home.hero .btn{color:#FFFFFF;background-color:#0048C2}

/* Text contrast follows each card's own background. */
.section.tone-light .card{color:#132B50}
.section.tone-light .card h3{color:#132B50}
.section.tone-light .card p{color:#52647F}
.section.tone-light .bento .campaign-card:first-child{background:linear-gradient(150deg,#0F2C62,#071C50);color:#FFFFFF}
.section.tone-light .bento .campaign-card:first-child h3{color:#FFFFFF}
.section.tone-light .bento .campaign-card:first-child p{color:#D5EAFD}
.section.tone-dark .card{background:linear-gradient(145deg,#102859,#071C50);color:#F5F8FF}
.section.tone-dark .card h3{color:#F5F8FF}
.section.tone-dark .card p{color:#D5EAFD}
#use-cases .card.case-panel{background:#FFFFFF;color:#132B50}
#use-cases .card.case-panel h3{color:#132B50}
#use-cases .card.case-panel p{color:#52647F}
.section .case-stage,.section .problem-chapter{background:#FFFFFF;color:#132B50}
.section .case-stage h3,.section .problem-chapter h3{color:#132B50}
.section .case-stage p,.section .problem-chapter p{color:#52647F}

/* Center mega menus independently of the selected nav item. */
body>header .nav-dropdown-menu.mega-menu,.site-header .nav-dropdown-menu.mega-menu{left:50%;right:auto;transform:translateX(-50%);max-width:calc(100vw - 40px);animation:centered-menu-in .18s ease-out}
@keyframes centered-menu-in{from{opacity:0;transform:translate(-50%,-6px)}to{opacity:1;transform:translate(-50%,0)}}
@media(max-width:760px){body>header .nav-dropdown-menu.mega-menu,.site-header .nav-dropdown-menu.mega-menu{position:static;left:auto;right:auto;transform:none;width:100%;max-width:100%;animation:none}}
@media(prefers-reduced-motion:reduce){body>header .nav-dropdown-menu.mega-menu,.site-header .nav-dropdown-menu.mega-menu{animation:none}}
/* === responsive.css === */
/* Responsive corrections shared by the existing pages. */
.responsive-site {
  --page-gutter: clamp(16px, 3vw, 40px);
  max-width: 100%;
  overflow-x: clip;
}
html { scroll-padding-top: calc(var(--site-header-height, 88px) + 24px); }
.responsive-site > header:not(.detail-topbar) {
  width: min(1200px, calc(100% - var(--page-gutter) * 2));
  margin-inline: auto;
}
.homepage-glow > main { padding-inline: var(--page-gutter); }
.responsive-site :is(.hero > div, .sectiontop, .premium-copy, .problem-stories,
  .work-chapters, .steps, .about-stories, .case-panel, .form-panel, .form-wrap,
  .card-content, .article-sidebar, .section-heading > *, .filter-toolbar > *) {
  min-width: 0;
  max-width: 100%;
}
.responsive-site :is(p, h1, h2, h3, .fields span, .mega-item > span:last-child,
  .reference-footer-items > *, .status) { overflow-wrap: anywhere; }
.responsive-site :is(img, video, iframe) { max-width: 100%; }
.responsive-site :is(.mega-menu, .fields span, .use-tab, .provider-button) { min-width: 0; }
.responsive-site :is(.provider-button, .btn, .button, .submit) { max-width: 100%; }
.responsive-site .mega-item > span:last-child { min-width: 0; }
.responsive-site #integrations .integration-icon:has(.integration-brand-logo) { max-width: 100%; }
.responsive-site #integrations .integration-brand-logo { max-width: 100%; }
.responsive-site .fields span { max-width: 100%; }
.responsive-site .reference-footer-items a { min-height: 44px; display: flex; align-items: center; }
.responsive-site .reference-footer-column:last-child .reference-footer-items > a { justify-content: center; }

/* Explicit spans prevent implicit extra columns between desktop and tablet. */
@media screen and (max-width:1100px) {
  .homepage-glow .grid.bento { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .homepage-glow .bento :is(.card, .campaign-card) { grid-column: auto; }
  .homepage-glow .bento .card:last-child { grid-column: 1 / -1; }
  .homepage-glow .campaign-visual img { height: auto; aspect-ratio: 3 / 2; }
}

@media screen and (max-width:1024px) {
  .responsive-site > header:not(.detail-topbar) {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    top: 8px;
    padding: 12px 16px;
    min-height: 70px;
  }
  .responsive-site > header .brand { grid-column: 1; grid-row: 1; }
  .responsive-site > header .brand-logo { width: 34px; height: 38px; }
  .responsive-site > header .header-account {
    grid-column: 2; grid-row: 1; margin: 0; justify-content: flex-end; gap: 8px;
  }
  .responsive-site > header .header-account .btn {
    min-height: 44px; padding: 10px 12px; font-size: 13px; white-space: nowrap;
  }
  .responsive-site > header .menu-toggle {
    grid-column: 3; grid-row: 1; display: inline-flex;
    align-items: center; justify-content: center; min-width: 52px; min-height: 44px;
    margin: 0; padding: 8px 10px; border: 1px solid #D5EAFD60;
    border-radius: 8px; color: #F5F8FF; background: #071C50a6;
    font-size: 13px; line-height: 1.4; cursor: pointer;
  }
  .responsive-site > header :is(#main-nav, #blog-main-nav) {
    grid-column: 1 / -1; grid-row: 2; display: none;
    width: 100%; min-width: 0; max-width: 100%; margin: 0;
    flex-direction: column; flex-wrap: nowrap; align-items: stretch;
    justify-content: flex-start; gap: 4px; padding: 12px 0 0;
    border-top: 1px solid #D5EAFD40;
    max-height: calc(100svh - var(--site-header-height, 88px) - 28px);
    overflow-y: auto; overscroll-behavior: contain;
  }
  .responsive-site > header :is(#main-nav.open, #blog-main-nav.is-open) { display: flex; }
  .responsive-site > header :is(#main-nav, #blog-main-nav) > *,
  .responsive-site > header :is(#main-nav, #blog-main-nav) .nav-dropdown {
    width: 100%; min-width: 0; flex-basis: auto; margin: 0;
  }
  .responsive-site > header :is(#main-nav, #blog-main-nav) :is(summary, .nav-pending, .nav-blog-link) {
    min-height: 44px; padding: 10px 12px; justify-content: space-between;
    font-size: 14px; line-height: 24px;
  }
  .responsive-site > header .nav-dropdown-menu.mega-menu {
    position: static; left: auto; right: auto; top: auto; transform: none;
    width: 100%; min-width: 0; max-width: 100%; margin: 8px 0;
    padding: 12px; grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 4px 12px; border-radius: 12px; animation: none;
  }
  .responsive-site > header .mega-item { min-height: 56px; gap: 12px; padding: 8px 6px; }
  .responsive-site > header .mega-item:nth-child(even) { padding-left: 12px; }
  .responsive-site > header .mega-icon { width: 38px; height: 38px; }
  .responsive-site > header .mega-icon svg { width: 22px; height: 22px; }

  .homepage-glow #home.hero.reference-hero {
    grid-template-columns: minmax(0, 1fr); min-height: 0; gap: 32px;
    padding-block: 48px 56px;
  }
  .homepage-glow #home .reference-hero-copy { max-width: 700px; }
  .homepage-glow #home.reference-hero h1 { font-size: clamp(32px, 4.4vw, 46px); max-width: 25ch; }
  .homepage-glow #home .reference-hero-visual { width: 100%; max-width: 880px; }
  .homepage-glow #home .hero-motion { max-width: none; width: 100%; aspect-ratio: 16 / 9; }
  .homepage-glow :is(.premium-split, .team-editorial, .case-stage) {
    grid-template-columns: minmax(0, 1fr); gap: 28px;
  }
  .homepage-glow :is(.premium-team, .case-preview) img {
    height: auto; aspect-ratio: 3 / 2;
  }
  .homepage-glow .about-stories { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
  .homepage-glow #teams .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .homepage-glow #teams .card { grid-column: auto; }
  .homepage-glow #teams .card:last-child { grid-column: 1 / -1; }

  .responsive-site .blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .responsive-site .card-meta { flex-wrap: wrap; gap: 8px; }
  .responsive-site .section-heading { flex-wrap: wrap; }
  .responsive-site .article-layout { grid-template-columns: minmax(0, 1fr); gap: 28px; }
  .responsive-site .article-sidebar { position: static; grid-row: 1; }
  .responsive-site .toc-nav { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
}

@media screen and (max-width:900px) {
  .responsive-site .auth-layout { grid-template-columns: minmax(0, 1fr); }
  .responsive-site .brand-panel { position: relative; padding: 24px; }
  .responsive-site .brand-panel .back { position: absolute; top: 34px; right: 24px; }
  .responsive-site .brand-content { margin: 0; max-width: none; padding: 28px 0 8px; }
  .responsive-site .brand-content h2 { font-size: 28px; margin-bottom: 12px; }
  .responsive-site .brand-content :is(p, .workflow) { display: none; }
  .responsive-site .form-panel { padding: 32px 24px 48px; }
}

@media screen and (max-width: 860px) {
  .homepage-glow :is(.problem-layout, .workflow-layout, .about-editorial, .work-editorial) {
    grid-template-columns: minmax(0, 1fr) !important;
    gap: 28px !important;
  }
  .homepage-glow :is(.problem-art, .work-art, .workflow-art, .about-art) {
    position: static !important;
    width: 100% !important;
  }
}

@media screen and (max-width:640px) {
  .responsive-site > header:not(.detail-topbar) { gap: 8px; padding: 10px 12px; }
  .responsive-site > header .header-account .btn { padding-inline: 10px; }
  .responsive-site > header .nav-dropdown-menu.mega-menu { grid-template-columns: minmax(0, 1fr); }
  .responsive-site > header .mega-item:nth-child(even) { padding-left: 6px; border-left: 0; }
  .homepage-glow .section { padding-block: 48px; }
  .homepage-glow .section h2, .homepage-glow #section-2 h2 {
    font-size: clamp(26px, 6vw, 32px); line-height: 1.25; letter-spacing: -.7px;
  }
  .homepage-glow #home.hero.reference-hero { padding-block: 32px 40px; gap: 28px; }
  .homepage-glow #home.reference-hero h1 {
    font-size: clamp(30px, 7.8vw, 40px); line-height: 1.16; letter-spacing: -.9px; max-width: 22ch;
  }
  .homepage-glow #home.reference-hero p { font-size: 16px; line-height: 1.7; }
  .homepage-glow .reference-signin { max-width: none; margin-top: 24px; }
  .homepage-glow .reference-hero-visual { padding: 8px; border-radius: 16px; }
  .homepage-glow #home .provider-button { min-height: 56px; gap: 14px; padding-inline: 18px; }
  .homepage-glow :is(.grid, .grid.bento, #teams .grid, #section-7 .grid) { grid-template-columns: minmax(0, 1fr); }
  .homepage-glow .bento :is(.card, .campaign-card),
  .homepage-glow #teams :is(.card, .card:last-child) { grid-column: auto; }
  .homepage-glow #teams .card:last-child { display: block; }
  .homepage-glow #teams .card:last-child h3 { margin: 18px 0 12px; }
  .homepage-glow #section-7 .card:first-child { display: block; }
  .homepage-glow #section-7 .card:first-child h3 { margin: 18px 0 12px; }
  .homepage-glow .about-stories { grid-template-columns: minmax(0, 1fr); }
  .homepage-glow .work-chapters article { padding-left: 0; }
  .homepage-glow .case-stage { padding: 16px; }
  .homepage-glow .case-preview img { height: auto; aspect-ratio: 3 / 2; }
  .homepage-glow .preview-caption { position: static; border-radius: 0; margin: 0; padding: 12px 16px; }
  .homepage-glow .use-tab { max-width: 100%; min-height: 44px; }
  .homepage-glow .steps .card { grid-template-columns: 40px minmax(0, 1fr); column-gap: 12px; padding: 20px 16px; }
  .homepage-glow .steps .icon3d { width: 40px; height: 40px; }
  .homepage-glow #faq details { padding-inline: 16px; }
  .homepage-glow #faq details summary { padding-inline: 30px; }
  .homepage-glow #faq details summary::after { right: 2px; }
  .homepage-glow #faq details p { padding-right: 0; }

  .responsive-site .blog-grid { grid-template-columns: minmax(0, 1fr); }
  .responsive-site .popular-section .blog-grid .article-card:last-child { grid-column: auto; max-width: none; }
  .responsive-site .index-view .hero-top { grid-template-columns: minmax(0, 1fr); }
  .responsive-site .index-view .hero-art { width: min(100%, 260px); justify-self: end; }
  .responsive-site .category-button, .responsive-site .page-button,
  .responsive-site .select-wrap select, .responsive-site .clear-search { min-height: 44px; }
  .responsive-site :is(.search-form input, .select-wrap select, .form-panel input) { font-size: 16px; }
  .responsive-site .clear-search { width: 44px; }
  .responsive-site .toc-nav { grid-template-columns: minmax(0, 1fr); }
  .responsive-site .detail-topbar .container { flex-wrap: wrap; padding-block: 12px; }
  .responsive-site .form-panel { padding: 24px 16px 40px; }
  .responsive-site .form-wrap { padding: 24px 20px; }
  .responsive-site .form-wrap h1 { font-size: clamp(26px, 7vw, 32px); }
  .responsive-site .reference-footer-legal > * { overflow-wrap: anywhere; }
}

@media screen and (max-width:420px) {
  .responsive-site .search-form { display: grid; grid-template-columns: minmax(0, 1fr) 44px; gap: 8px; padding: 8px 12px; }
  .responsive-site .search-form > svg { display: none; }
  .responsive-site .search-form input { grid-column: 1; grid-row: 1; min-height: 44px; }
  .responsive-site .search-form .clear-search { grid-column: 2; grid-row: 1; }
  .responsive-site .search-form .button { grid-column: 1 / -1; grid-row: 2; min-height: 44px; width: 100%; }
}

@media screen and (max-width:360px) {
  .responsive-site > header .header-account { grid-column: 1 / -1; grid-row: 2; }
  .responsive-site > header .header-account .btn { flex: 1; }
  .responsive-site > header :is(#main-nav, #blog-main-nav) { grid-row: 3; }
  .homepage-glow .card { padding: 20px; }
  .homepage-glow .campaign-visual { margin: 20px -20px -20px; }
  .homepage-glow #integrations .integration-card { flex-basis: 100%; }
}
/* === consent.css === */
/* Consent copy follows the supplied reference and each page's own contrast. */
.responsive-site .consent-note {
  margin: 16px 0 0;
  color: #52647F;
  font-size: 13px;
  line-height: 1.65;
  text-align: center;
  overflow-wrap: anywhere;
}
.responsive-site .consent-policy { color: #0048C2; font-weight: 600; }
.homepage-glow #home.reference-hero .consent-note {
  margin: 4px 0 0;
  max-width: none;
  color: #D5EAFD;
  font-size: 13px;
  line-height: 1.65;
}
.homepage-glow #home.reference-hero .consent-policy { color: #6CB5FF; }



/* === PREMIUM HIGH-TECH HERO ANIMATIONS & LIGHTING === */

/* Choreographed Hero Entrance Keyframes (Left Copy Sequence) */
@keyframes heroPillDrop {
  0% {
    opacity: 0;
    transform: translateY(-16px) scale(0.94);
    filter: blur(8px);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0px);
  }
}

@keyframes heroHeadlineReveal {
  0% {
    opacity: 0;
    transform: translateY(32px) scale(0.97);
    filter: blur(12px);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0px);
  }
}

@keyframes heroTextReveal {
  0% {
    opacity: 0;
    transform: translateY(22px);
    filter: blur(6px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0px);
  }
}

@keyframes heroBtnSpring {
  0% {
    opacity: 0;
    transform: translateY(24px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes heroFadeOnly {
  0% {
    opacity: 0;
    transform: translateY(14px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Use-Case Tab Crossfade Entrance */
@keyframes panelSlideFade {
  0% {
    opacity: 0;
    transform: translateY(14px);
    filter: blur(5px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0px);
  }
}

/* 1. Cinematic Hero Visual Reveal Physics (Hyper-smooth ease & perspective entrance) */
@keyframes cinematicHeroReveal {
  0% {
    opacity: 0;
    transform: perspective(1200px) translateY(42px) scale(0.95) rotateX(4deg);
    filter: blur(14px);
  }
  100% {
    opacity: 1;
    transform: perspective(1200px) translateY(0) scale(1) rotateX(0deg);
    filter: blur(0px);
  }
}

/* 1b. Specular Lens Flare / Sheen Sweep on Reveal */
@keyframes cinematicSheenSweep {
  0% {
    left: -130%;
    opacity: 0;
  }
  25% {
    opacity: 0.95;
  }
  65% {
    opacity: 0.95;
  }
  100% {
    left: 170%;
    opacity: 0;
  }
}

/* 2. Pulsing Ambient Neon Aura behind the Card (Soft Glow - Stationary) */
@keyframes heroAuraBreath {
  0% {
    opacity: 0.5;
    filter: blur(28px);
  }
  50% {
    opacity: 0.85;
    filter: blur(40px);
  }
  100% {
    opacity: 0.5;
    filter: blur(28px);
  }
}

/* 3. Universal High-Speed Laser Border Beam (100% cross-browser, no broken masks) */
@keyframes heroBeamRotate {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* 4. Vivid Shimmer Sweep for "Business Automation" Heading */
@keyframes heroTextShimmerSweep {
  0% {
    background-position: 150% center;
  }
  100% {
    background-position: -50% center;
  }
}

/* 5. Telemetry Badges Cinematic Docking Entrance (Stationary Resting State) */
@keyframes cinematicChipDockTop {
  0% {
    opacity: 0;
    transform: translateY(-16px) scale(0.92);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes cinematicChipDockBottom {
  0% {
    opacity: 0;
    transform: translateY(16px) scale(0.92);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* 6. Active Live Status Radar Pulse Ping */
@keyframes radarDotPing {
  0% {
    transform: scale(0.85);
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.8), 0 0 10px #10B981;
  }
  70% {
    transform: scale(1.1);
    box-shadow: 0 0 0 10px rgba(16, 185, 129, 0), 0 0 14px #10B981;
  }
  100% {
    transform: scale(0.85);
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0), 0 0 10px #10B981;
  }
}

/* 7. Pill Badge Shimmer */
@keyframes badgePillShine {
  0%, 65% {
    left: -120%;
  }
  100% {
    left: 180%;
  }
}

/* === APPLIED COMPONENT STYLES === */

/* Top Pill Badge */
.hero-pill-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 16px;
  border-radius: 999px;
  background: rgba(10, 124, 248, 0.18);
  border: 1px solid rgba(108, 181, 255, 0.45);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  color: #BAE0FF;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 20px;
  box-shadow: 0 4px 18px rgba(10, 124, 248, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.3);
  position: relative;
  overflow: hidden;
}

.hero-pill-badge::after {
  content: "";
  position: absolute;
  top: 0;
  left: -120%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
  transform: skewX(-20deg);
  animation: badgePillShine 4s ease-in-out infinite !important;
}

.hero-pill-badge .badge-pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #6CB5FF;
  box-shadow: 0 0 10px #6CB5FF;
  animation: radarDotPing 2.2s ease-in-out infinite !important;
}

/* Heading Shimmer Text */
#home.reference-hero h1 span.shimmer-text {
  display: inline-block !important;
  background: linear-gradient(
    90deg,
    #6CB5FF 0%,
    #6CB5FF 25%,
    #D5EAFD 38%,
    #FFFFFF 50%,
    #D5EAFD 62%,
    #6CB5FF 75%,
    #6CB5FF 100%
  ) !important;
  background-size: 200% auto !important;
  -webkit-background-clip: text !important;
  background-clip: text !important;
  -webkit-text-fill-color: transparent !important;
  color: transparent !important;
  animation: heroTextShimmerSweep 3.2s linear infinite !important;
  filter: drop-shadow(0 2px 16px rgba(10, 124, 248, 0.5));
}

/* Main Hero Visual Card Container (Anchored & Stationary - Does Not Float) */
#home.hero.reference-hero {
  align-items: center !important;
}

.homepage-glow #home .reference-hero-visual {
  position: relative !important;
  top: auto !important;
  align-self: center !important;
  z-index: 10 !important;
  isolation: isolate;
  overflow: visible !important;
  border-radius: 26px !important;
  padding: 3px !important;
  background: transparent !important;
  border: none !important;
  box-shadow: 0 24px 60px rgba(2, 11, 43, 0.6) !important;
  animation: cinematicHeroReveal 1.35s cubic-bezier(0.16, 1, 0.3, 1) both !important;
  will-change: transform, opacity, filter;
}

/* Ambient Pulsing Aura Behind Card */
.homepage-glow #home .reference-hero-visual::before {
  content: "";
  position: absolute;
  inset: -18px;
  border-radius: 40px;
  background: radial-gradient(ellipse at 50% 50%, rgba(10, 124, 248, 0.6) 0%, rgba(108, 181, 255, 0.35) 45%, transparent 72%);
  filter: blur(32px);
  z-index: -1;
  pointer-events: none;
  animation: heroAuraBreath 5s ease-in-out infinite !important;
}

/* High-tech Border Beam Frame */
.hero-border-beam-track {
  position: absolute;
  inset: 0;
  border-radius: 26px;
  overflow: hidden;
  pointer-events: none;
  z-index: 1;
}

.hero-border-beam-glow {
  position: absolute;
  top: -100%;
  left: -100%;
  width: 300%;
  height: 300%;
  background: conic-gradient(from 0deg, transparent 0deg, transparent 65%, #0A7CF8 78%, #6CB5FF 88%, #FFFFFF 96%, #0A7CF8 100%);
  animation: heroBeamRotate 4s linear infinite !important;
}

/* Inner Wrapper - Stationary Video Container with Cinematic Specular Sheen */
.homepage-glow #home .hero-inner-tilt {
  position: relative;
  z-index: 2;
  border-radius: 23px;
  overflow: hidden;
  background: #071C50;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35);
  transform: none !important;
  transition: none !important;
}

.homepage-glow #home .hero-inner-tilt::after {
  content: "";
  position: absolute;
  top: 0;
  left: -130%;
  width: 75%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.08) 20%,
    rgba(255, 255, 255, 0.45) 50%,
    rgba(108, 181, 255, 0.35) 75%,
    transparent 100%
  );
  transform: skewX(-24deg);
  pointer-events: none;
  z-index: 5;
  animation: cinematicSheenSweep 1.8s cubic-bezier(0.16, 1, 0.3, 1) 0.25s both !important;
}

.homepage-glow #home .hero-inner-tilt video {
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 23px;
  object-fit: contain;
  background: #071C50;
}

/* Floating Telemetry Badges */
.hero-floating-chip {
  position: absolute;
  z-index: 10;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 999px;
  background: rgba(7, 28, 80, 0.88);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(213, 234, 253, 0.45);
  box-shadow: 0 12px 32px rgba(2, 11, 43, 0.65), 0 0 20px rgba(10, 124, 248, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.35);
  color: #F6FAFD;
  font-size: 12px;
  font-weight: 600;
  pointer-events: none;
  letter-spacing: 0.02em;
  white-space: nowrap;
}

.chip-top {
  top: -18px;
  left: 20px;
  animation: cinematicChipDockTop 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.35s both !important;
}

.chip-bottom {
  bottom: -18px;
  right: 20px;
  animation: cinematicChipDockBottom 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.55s both !important;
}

.chip-status-pulse {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #10B981;
  display: inline-block;
  animation: radarDotPing 2s ease-in-out infinite !important;
}

.chip-badge {
  padding: 2px 8px;
  border-radius: 999px;
  background: rgba(16, 185, 129, 0.22);
  color: #34D399;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid rgba(52, 211, 153, 0.35);
}

.chip-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: rgba(10, 124, 248, 0.35);
  color: #6CB5FF;
  box-shadow: 0 0 10px rgba(10, 124, 248, 0.4);
}

/* Provider buttons hover shimmer */
.homepage-glow #home .provider-button {
  position: relative;
  overflow: hidden;
  cursor: pointer !important;
  transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s ease, border-color 0.28s ease !important;
}

.homepage-glow #home .provider-button:hover {
  transform: translateY(-3px) scale(1.02);
}

.homepage-glow #home .google-provider:hover {
  box-shadow: 0 12px 28px rgba(2, 11, 43, 0.22), 0 0 24px rgba(255, 255, 255, 0.45) !important;
  border-color: #FFFFFF !important;
}

.homepage-glow #home .microsoft-provider:hover {
  box-shadow: 0 12px 32px rgba(10, 124, 248, 0.45), 0 0 28px rgba(108, 181, 255, 0.35) !important;
  border-color: #A5D8FF !important;
}

.homepage-glow #home .provider-button::after {
  content: "";
  position: absolute;
  top: 0;
  left: -130%;
  width: 70%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.38), transparent);
  transform: skewX(-20deg);
  pointer-events: none;
  transition: left 0.8s ease;
}

.homepage-glow #home .provider-button:hover::after {
  left: 170%;
}

@media (max-width: 760px) {
  .hero-floating-chip { display: none; }
  .homepage-glow #home .reference-hero-visual { animation: none !important; }
}


.homepage-glow #home .provider-button {
  text-decoration: none !important;
  cursor: pointer !important;
}
.homepage-glow #home .google-provider {
  color: #132B50 !important;
}
.homepage-glow #home .microsoft-provider {
  color: #FFFFFF !important;
}

/* Reusable Unified Single-Block Editorial Card */
.editorial-single-wrap {
  display: block !important;
  width: 100%;
}

.editorial-unified-card {
  background: #FFFFFF !important;
  border: 1px solid rgba(108, 181, 255, 0.35) !important;
  border-radius: 20px !important;
  padding: clamp(28px, 4vw, 42px) !important;
  box-shadow: 0 10px 30px rgba(5, 40, 155, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04), inset 0 1px 0 #FFFFFF !important;
  transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
}

.editorial-unified-card:hover {
  border-color: rgba(108, 181, 255, 0.7) !important;
  box-shadow: 0 16px 40px rgba(5, 40, 155, 0.1), inset 0 1px 0 #FFFFFF !important;
}

.editorial-unified-card p {
  margin: 0 !important;
  font-size: clamp(16px, 1.15vw, 18px) !important;
  line-height: 1.85 !important;
  color: #334155 !important;
  letter-spacing: -0.01em !important;
}

.section.tone-dark .editorial-unified-card {
  background: rgba(11, 35, 88, 0.85) !important;
  border-color: rgba(108, 181, 255, 0.25) !important;
  box-shadow: 0 16px 40px rgba(2, 11, 43, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
  backdrop-filter: blur(16px) !important;
  -webkit-backdrop-filter: blur(16px) !important;
}

.section.tone-dark .editorial-unified-card p {
  color: #D5EAFD !important;
}

/* Choreographed Hero Entrance Elements */
#home.reference-hero .hero-pill-badge {
  animation: heroPillDrop 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.08s both !important;
  will-change: transform, opacity, filter;
}

#home.reference-hero h1 {
  animation: heroHeadlineReveal 1.05s cubic-bezier(0.16, 1, 0.3, 1) 0.18s both !important;
  will-change: transform, opacity, filter;
}

#home.reference-hero p:not(.consent-note) {
  animation: heroTextReveal 0.95s cubic-bezier(0.16, 1, 0.3, 1) 0.32s both !important;
  will-change: transform, opacity, filter;
}

.homepage-glow #home .google-provider {
  animation: heroBtnSpring 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.46s both !important;
}

.homepage-glow #home .microsoft-provider {
  animation: heroBtnSpring 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.58s both !important;
}

.homepage-glow #home.reference-hero .consent-note {
  animation: heroFadeOnly 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.72s both !important;
}

/* Interactive Cursor Spotlight Sheen on Cards */
.card,
.integration-card,
.editorial-unified-card {
  position: relative;
}

.card::after,
.integration-card::after,
.editorial-unified-card::after {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  background: radial-gradient(
    380px circle at var(--spot-x, -999px) var(--spot-y, -999px),
    rgba(108, 181, 255, 0.16),
    transparent 70%
  );
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.35s ease;
  z-index: 2;
}

.card:hover::after,
.integration-card:hover::after,
.editorial-unified-card:hover::after {
  opacity: 1;
}

/* Use-Case Tab Crossfade */
.case-panel {
  transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1), transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.case-panel.tab-entering {
  animation: panelSlideFade 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Interactive Step Card Hover Illumination */
.homepage-glow .steps .card {
  transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.32s ease, box-shadow 0.32s ease !important;
}
.homepage-glow .steps .card:hover {
  transform: translateX(6px) !important;
  border-color: #6CB5FF !important;
  box-shadow: 0 12px 30px rgba(5, 40, 155, 0.12), inset 0 1px 0 #FFFFFF !important;
}
.homepage-glow .steps .card::before {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease !important;
}
.homepage-glow .steps .card:hover::before {
  transform: scale(1.35) !important;
  box-shadow: 0 0 0 6px #D5EAFD, 0 0 20px #0A7CF8 !important;
}

/* Editorial 3D Artwork Depth */
.problem-art,
.premium-art,
.about-art,
.work-art,
.workflow-art {
  overflow: hidden !important;
}

.problem-art img,
.premium-art img,
.about-art img,
.work-art img,
.workflow-art img {
  transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1), filter 0.65s ease !important;
}

.problem-art:hover img,
.premium-art:hover img,
.about-art:hover img,
.work-art:hover img,
.workflow-art:hover img {
  transform: scale(1.028) !important;
}

/* Reduced Motion Safety Override */
@media (prefers-reduced-motion: reduce) {
  .hero-pill-badge,
  #home.reference-hero h1,
  #home.reference-hero p,
  .homepage-glow #home .google-provider,
  .homepage-glow #home .microsoft-provider,
  .homepage-glow #home.reference-hero .consent-note,
  .homepage-glow #home .reference-hero-visual {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
    filter: none !important;
  }
  .case-panel.tab-entering {
    animation: none !important;
  }
  .steps .card:hover {
    transform: none !important;
  }
  .steps .card:hover::before {
    transform: none !important;
  }
  .problem-art:hover img,
  .premium-art:hover img,
  .about-art:hover img,
  .work-art:hover img,
  .workflow-art:hover img {
    transform: none !important;
  }
}

</style>

<div class="homepage-glow responsive-site">
<main id="main-content"><section class="hero reference-hero" id="home"><div class="reference-hero-copy">
<div class="hero-pill-badge"><span class="badge-pulse-dot"></span><span>AI-Powered Ingestion &amp; Parsing Engine</span></div>
<h1>Intelligent Email Parser: Built for <span class="shimmer-text">Business Automation</span></h1><p>ExtractMails intelligently reads and parses emails, extracts the information you need, and turns unstructured email content into structured business data that can power your CRM, database, and workflows.</p><div class="reference-signin"><a href="https://www.extractmails.com/google.php" class="provider-button google-provider" aria-label="Continue with Google">
<img src="https://www.extractmails.com/wp-content/uploads/2024/12/google-signin-logo.png" width="24" height="24" alt="" class="provider-google-logo">Continue with Google</a><a href="https://www.extractmails.com/google.php" class="provider-button microsoft-provider" aria-label="Continue with Microsoft"><svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path fill="#f25022" d="M1 1h10v10H1z"/><path fill="#7fba00" d="M13 1h10v10H13z"/><path fill="#00a4ef" d="M1 13h10v10H1z"/><path fill="#ffb900" d="M13 13h10v10H13z"/></svg>Continue with Microsoft</a><p class="consent-note">By clicking Continue to sign up or sign in, you agree to the ExtractMails <a href="https://www.extractmails.com/terms-and-conditions/" class="consent-policy">User Agreement</a>, <a href="https://www.extractmails.com/privacy-and-policy/" class="consent-policy">Privacy Policy</a>, and <span class="consent-policy">Cookie Policy</span>.</p></div></div><div class="reference-hero-visual">
<div class="hero-border-beam-track" aria-hidden="true">
  <div class="hero-border-beam-glow"></div>
</div>
<div class="hero-floating-chip chip-top" aria-hidden="true">
  <span class="chip-status-pulse"></span>
  <span class="chip-title">Live Ingestion Active</span>
  <span class="chip-badge">99.9%</span>
</div>
<div class="hero-floating-chip chip-bottom" aria-hidden="true">
  <span class="chip-icon">
    <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1.414 1.414 0 01-1.414 0z" clip-rule="evenodd"/></svg>
  </span>
  <span class="chip-title">Automated CRM Sync</span>
</div>
<div class="hero-motion hero-inner-tilt"><video id="hero-workflow-video" autoplay muted loop playsinline controls preload="metadata" poster="https://www.extractmails.com/wp-content/uploads/2024/12/workflow-light-poster.jpg" aria-label="ExtractMails email extraction and CRM synchronization workflow demonstration"><source src="https://www.extractmails.com/wp-content/uploads/2024/12/ExtractMails_Workflow_Light_v6_45s.mp4" type="video/mp4">Your browser does not support video playback.</video></div></div></section><section class="section integrations-section" id="integrations"><div class="sectiontop"><span class="kicker">Integrations</span><h2>Connect your email data to the tools you use.</h2><p class="integration-subtitle">Send extracted data directly to the systems your team already uses.</p></div><div class="integrations-grid"><article class="integration-card"><div class="integration-card-head"><span class="integration-icon"><img class="integration-brand-logo" src="https://www.extractmails.com/wp-content/uploads/2024/12/sheets-logo.svg" alt="Google Sheets" loading="lazy"></span><h3>Google Sheets</h3></div><p class="integration-route">Spreadsheets & reports</p></article><article class="integration-card"><div class="integration-card-head"><span class="integration-icon"><img class="integration-brand-logo" src="https://www.extractmails.com/wp-content/uploads/2024/12/zoho-crm-logo.png" alt="Zoho CRM" loading="lazy"></span><h3>Zoho CRM</h3></div><p class="integration-route">Sales pipeline</p></article><article class="integration-card"><div class="integration-card-head"><span class="integration-icon"><img class="integration-brand-logo" src="https://www.extractmails.com/wp-content/uploads/2024/12/salesforce-logo.jpg" alt="Salesforce" loading="lazy"></span><h3>Salesforce</h3></div><p class="integration-route">Customer accounts & leads</p></article><article class="integration-card"><div class="integration-card-head"><span class="integration-icon"><img class="integration-brand-logo" src="https://www.extractmails.com/wp-content/uploads/2024/12/hubspot-logo.png" alt="HubSpot" loading="lazy"></span><h3>HubSpot</h3></div><p class="integration-route">Sales Leads</p></article></div></section><section class="section tone-light" id="section-1"><div class="sectiontop"><h2>Don&#x27;t Let Valuable Business Data Stay Trapped in Emails</h2></div><div class="problem-layout"><figure class="problem-art"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/email-data.webp" width="1536" height="1024" loading="lazy" alt="Blue 3D envelope releasing structured business data"></figure><div class="editorial-single-wrap"><article class="editorial-unified-card"><p>Businesses receive thousands of emails every day containing critical information such as orders, invoices, customer inquiries, shipping updates, booking confirmations, leads, and receipts. However, this valuable data remains trapped inside unstructured email messages, forcing teams to manually open emails one by one, copy and paste details into spreadsheets, and update CRM records daily. ExtractMails eliminates this repetitive manual bottleneck through intelligent email parsing &mdash; automatically reading incoming email content, identifying key fields, and transforming raw unstructured messages into clean, structured business data ready for your database and workflows.</p></article></div></div></section><section class="section tone-dark" id="section-2"><div class="sectiontop"><span class="kicker">What Is ExtractMails?</span><h2>Email Parser That Understands Your Business Data</h2></div><div class="premium-split"><figure class="premium-art"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/email-data.webp" width="1536" height="1024" loading="lazy" alt="Sculptural blue envelope transforming paper messages into structured glass data cards"></figure><div class="premium-copy"><p>ExtractMails is an AI-powered intelligent email parser that reads and analyzes email content to identify information that the business requires. ExtractMails treats information as a source of valuable business data, instead of treating emails as messages that need to be manually processed.</p><p>It can help businesses extract a wide range of information, such as:</p><div class="fields"><span>Customer names</span><span>Email addresses</span><span>Phone numbers</span><span>Order numbers</span><span>Invoice numbers</span><span>Product information</span><span>Quantities</span><span>Prices and totals</span><span>Dates</span><span>Addresses</span><span>Tracking information</span><span>Lead details</span><span>Booking information</span><span>Other custom business fields</span></div></div></div></section><section class="section tone-light" id="features"><div class="sectiontop"><span class="kicker">Key Features</span><h2>Turn Email Parsing into an Automated Workflow</h2></div><div class="grid bento"><article class="card campaign-card"><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:0"></span><h3>Intelligent Email Parsing</h3><p>Automatically read and analyze email content to identify relevant information.</p><figure class="campaign-visual"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/email-data.webp" width="1536" height="1024" loading="lazy" alt="Email content transforming into structured business data"></figure></article><article class="card campaign-card"><span class="icon3d" aria-hidden="true" style="--ix:1;--iy:0"></span><h3>Email Data Extraction</h3><p>Extract specific fields from unstructured email messages and turn them into structured data.</p><figure class="campaign-visual"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/team-data.webp" width="1536" height="1024" loading="lazy" alt="Professionals working with extracted email data"></figure></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:2;--iy:0"></span><h3>Custom Data Fields</h3><p>Define the information your business needs—from customer details and order numbers to invoice amounts, tracking IDs, and more.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:3;--iy:0"></span><h3>Email Body Parsing</h3><p>Analyze the content of email messages and identify relevant information from the message body.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:1"></span><h3>Email Header Parsing</h3><p>Capture useful email metadata such as sender, recipient, subject, date, and other available header information.</p></article></div></section><section class="section tone-dark" id="use-cases"><div class="sectiontop"><h2>Automate Data Extraction Across Your Business</h2></div><div class="use-tabs" role="tablist" aria-label="Email parsing use cases"><button class="use-tab" role="tab" id="case-tab-0" aria-controls="case-panel-0" aria-selected="true" tabindex="0" data-case="0">Invoice Emails</button><button class="use-tab" role="tab" id="case-tab-1" aria-controls="case-panel-1" aria-selected="false" tabindex="-1" data-case="1">Order Confirmations</button><button class="use-tab" role="tab" id="case-tab-2" aria-controls="case-panel-2" aria-selected="false" tabindex="-1" data-case="2">Lead Notifications</button><button class="use-tab" role="tab" id="case-tab-3" aria-controls="case-panel-3" aria-selected="false" tabindex="-1" data-case="3">Shipping Notifications</button><button class="use-tab" role="tab" id="case-tab-4" aria-controls="case-panel-4" aria-selected="false" tabindex="-1" data-case="4">Customer Support Emails</button><button class="use-tab" role="tab" id="case-tab-5" aria-controls="case-panel-5" aria-selected="false" tabindex="-1" data-case="5">Booking Confirmations</button><button class="use-tab" role="tab" id="case-tab-6" aria-controls="case-panel-6" aria-selected="false" tabindex="-1" data-case="6">Receipts</button><button class="use-tab" role="tab" id="case-tab-7" aria-controls="case-panel-7" aria-selected="false" tabindex="-1" data-case="7">Purchase Orders</button></div><div class="case-stage"><article class="card case-panel" role="tabpanel" id="case-panel-0" aria-labelledby="case-tab-0"><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:1"></span><h3>Invoice Emails</h3><p>Extract invoice numbers, vendor information, invoice dates, amounts, taxes, and payment details.</p></article><article class="card case-panel" role="tabpanel" id="case-panel-1" aria-labelledby="case-tab-1" hidden><span class="icon3d" aria-hidden="true" style="--ix:1;--iy:1"></span><h3>Order Confirmations</h3><p>Capture customer information, order IDs, products, quantities, prices, totals, and delivery details.</p></article><article class="card case-panel" role="tabpanel" id="case-panel-2" aria-labelledby="case-tab-2" hidden><span class="icon3d" aria-hidden="true" style="--ix:2;--iy:1"></span><h3>Lead Notifications</h3><p>Turn lead emails and form notifications into structured prospect records for your sales team.</p></article><article class="card case-panel" role="tabpanel" id="case-panel-3" aria-labelledby="case-tab-3" hidden><span class="icon3d" aria-hidden="true" style="--ix:3;--iy:1"></span><h3>Shipping Notifications</h3><p>Extract tracking numbers, carriers, delivery dates, shipment information, and addresses.</p></article><article class="card case-panel" role="tabpanel" id="case-panel-4" aria-labelledby="case-tab-4" hidden><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:2"></span><h3>Customer Support Emails</h3><p>Identify customer information, ticket numbers, issues, requests, and other relevant details.</p></article><article class="card case-panel" role="tabpanel" id="case-panel-5" aria-labelledby="case-tab-5" hidden><span class="icon3d" aria-hidden="true" style="--ix:1;--iy:2"></span><h3>Booking Confirmations</h3><p>Parse reservation information, customer details, booking IDs, dates, locations, and other available information.</p></article><article class="card case-panel" role="tabpanel" id="case-panel-6" aria-labelledby="case-tab-6" hidden><span class="icon3d" aria-hidden="true" style="--ix:2;--iy:2"></span><h3>Receipts</h3><p>Extract merchant information, transaction dates, amounts, receipt numbers, and purchase details.</p></article><article class="card case-panel" role="tabpanel" id="case-panel-7" aria-labelledby="case-tab-7" hidden><span class="icon3d" aria-hidden="true" style="--ix:3;--iy:2"></span><h3>Purchase Orders</h3><p>Capture PO numbers, supplier information, products, quantities, prices, and delivery information.</p></article><figure class="case-preview"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/automation-3d.webp" width="1536" height="1024" alt="Email messages transformed into organized business records" loading="lazy"></figure></div></section><section class="section tone-light" id="how"><div class="sectiontop"><span class="kicker">How It Works</span><h2>How ExtractMails Parses Emails</h2></div><div class="workflow-layout"><figure class="workflow-art"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/data-hub-3d.webp" width="1536" height="1024" loading="lazy" alt="3D email hub connected to business data panels"></figure><div class="steps"><article class="card"><span class="step-number">01</span><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:0"></span><h3>Connect Your Email Workflow</h3><p>Bring the emails you want ExtractMails to process into your parsing workflow.</p></article><article class="card"><span class="step-number">02</span><span class="icon3d" aria-hidden="true" style="--ix:1;--iy:0"></span><h3>Define the Data You Need</h3><p>Specify the information you want to capture from your emails.</p></article><article class="card"><span class="step-number">03</span><span class="icon3d" aria-hidden="true" style="--ix:2;--iy:0"></span><h3>ExtractMails Parses the Email</h3><p>The intelligent email parser reads the message and identifies the relevant information.</p></article><article class="card"><span class="step-number">04</span><span class="icon3d" aria-hidden="true" style="--ix:3;--iy:0"></span><h3>Get Structured Data</h3><p>The extracted information is organized into clearly defined fields.</p></article><article class="card"><span class="step-number">05</span><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:1"></span><h3>Send It to Your Workflow</h3><p>Use the structured data in your CRM, database, application, or automated business process.</p></article></div></div></section><section class="section tone-dark" id="teams"><div class="team-editorial"><div class="sectiontop"><span class="kicker">Who Is It For?</span><h2>Built for Teams That Work with Email Data</h2></div><figure class="premium-team"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/team-data.webp" width="1536" height="1024" loading="lazy" alt="Two professionals reviewing structured email data in a blue contemporary workspace"></figure></div><div class="grid "><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:2;--iy:1"></span><h3>Sales Teams</h3><p>Automatically turn lead notifications and customer emails into usable prospect information.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:3;--iy:1"></span><h3>Operations Teams</h3><p>Process orders, notifications, confirmations, and operational emails without repetitive data entry.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:2"></span><h3>Finance Teams</h3><p>Extract useful information from invoices, receipts, payment notifications, and financial emails.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:1;--iy:2"></span><h3>E-commerce Businesses</h3><p>Parse order confirmations, shipping notifications, returns, and customer information.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:2;--iy:2"></span><h3>Customer Support Teams</h3><p>Structure information from support requests, ticket notifications, and customer communications.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:3;--iy:2"></span><h3>Logistics Teams</h3><p>Extract tracking, shipment, delivery, and order information from incoming emails.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:0"></span><h3>Developers &amp; Automation Teams</h3><p>Use structured email data as an input for applications, databases, APIs, and automated workflows.</p></article></div></section><section class="section tone-light" id="section-7"><div class="sectiontop"><h2>Why Choose ExtractMails?</h2></div><div class="grid "><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:2"></span><h3>Turn Emails into Data Your Business Can Use</h3><p>ExtractMails is an intelligent email parser that helps transform unstructured email content into structured business data. It makes it easier to connect email information with the systems and workflows that the business already relies on.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:1;--iy:2"></span><h3>Intelligent Email Parsing</h3><p>ExtractMails intelligently analyzes email content to identify the information that matters to your business, helping you move beyond manual email processing.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:2;--iy:2"></span><h3>Extract the Data You Actually Need</h3><p>Capture specific information from emails, whether it&#x27;s customer details, order numbers, invoice information, tracking numbers, dates, addresses, or other business fields.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:3;--iy:2"></span><h3>Built for Business Workflows</h3><p>Apart from email parsing, ExtractMails is also designed to turn parsed information into structured data that can become part of a CRM, database, or broader business workflow of the business.</p></article><article class="card"><span class="icon3d" aria-hidden="true" style="--ix:0;--iy:0"></span><h3>From Email Extraction to Email Parsing</h3><p>ExtractMails builds on its experience with email extractor and email scraping while expanding into intelligent email parsing.</p></article></div></section><section class="section tone-dark" id="about"><div class="sectiontop"><h2>Who We Are</h2></div><div class="about-editorial"><figure class="about-art"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/data-hub-3d.webp" width="1536" height="1024" loading="lazy" alt="Glowing email hub connecting structured business data"></figure><div class="editorial-single-wrap"><article class="editorial-unified-card"><p>ExtractMails is a technology-driven platform that focuses on helping intelligently process and manage email data. The email parser of ExtractMails transforms unstructured email content into structured business data, helping organizations reduce manual data entry and improve the process of managing information. ExtractMails is designed around intelligent email data extraction and email parsing software, helping businesses identify and capture the information that matters most. From customer details and order information to invoices, leads, shipping updates, and other business communications, ExtractMails helps turn emails into usable and structured data. We believe that valuable business information shouldn&#x27;t remain trapped inside inboxes. ExtractMails is built on our experience in email extraction and email scraping, while also expanding the way businesses can work with email data. As email-driven business processes continue to evolve, ExtractMails remains focused on developing smarter, more efficient ways to process email information and help businesses turn everyday messages into meaningful business data.</p></article></div></div></section><section class="section tone-light" id="section-9"><div class="sectiontop"><h2>What We Do</h2></div><div class="work-editorial"><figure class="work-art"><img src="https://www.extractmails.com/wp-content/uploads/2024/12/automation-3d.webp" width="1536" height="1024" alt="3D portal converting email messages into structured records" loading="lazy"></figure><div class="work-chapters"><article><p>ExtractMails offers an intelligent email processing solution that is designed to help businesses automate data handling and improve the way they work with information received through email. The core functionality of the platform is intelligent email parsing, which reads and analyzes email content to identify information and transforms every unstructured message into structured business data.</p></article><article><p>ExtractMails enables businesses to parse emails, extract specific data fields, and organize information for future use. From customer details and order information to invoices, leads, shipping updates, and other business communications, ExtractMails helps capture valuable information without relying on repetitive manual data entry.</p></article><article><p>The platform also builds its existing email extraction and email scraping capabilities, which help users to collect relevant email information when required. By combining email parsing with email data extraction, ExtractMails offers a broader approach to discovering, processing, and organizing email-related information.</p></article></div></div></section><section class="section tone-dark" id="faq"><div class="sectiontop"><h2>Frequently Asked Questions</h2></div><div class="faqs"><details><summary>What is email parsing?</summary><p>Email parsing is the process of analyzing an email&#x27;s content and extracting useful information such as customer details, order numbers, invoice information, dates, addresses, and other business data.</p></details><details><summary>Can ExtractMails automatically extract data from emails?</summary><p>Yes. The purpose of ExtractMails&#x27; email parsing functionality is to automate the process of identifying and extracting required information from email messages.</p></details><details><summary>Can an email parser send data to a CRM?</summary><p>An email parser can be used as part of a workflow that sends structured email data to CRM systems. ExtractMails is designed around this email-to-business-workflow model.</p></details><details><summary>Can I extract custom fields from emails?</summary><p>Yes, you can extract custom field data from emails using ExtractMails. Custom data fields allow an email parsing workflow to focus on the information that is relevant to a particular business process.</p></details></div></section>
<?php if (!empty($_GET['auth_notice']) || !empty($_GET['auth_error'])) : 
    $is_error = !empty($_GET['auth_error']);
    $msg = 'Authentication was cancelled.';
    if ($is_error) {
        $err = sanitize_text_field($_GET['auth_error']);
        if ($err === 'invalid_state') $msg = 'Security verification failed or session expired. Please try again.';
        elseif ($err === 'token_failed' || $err === 'token_missing') $msg = 'Could not verify credentials with provider. Please try again.';
        elseif ($err === 'email_missing') $msg = 'No verified email address returned from your account.';
        else $msg = 'Authentication failed. Please try again.';
    }
?>
<div id="authToast" style="position:fixed; bottom:28px; right:28px; z-index:999999; background:<?php echo $is_error ? 'rgba(220,38,38,0.95)' : 'rgba(10,124,248,0.95)'; ?>; color:#ffffff; padding:14px 22px; border-radius:12px; font-size:14px; font-weight:600; box-shadow:0 12px 36px rgba(2,11,43,0.45); backdrop-filter:blur(16px); display:flex; align-items:center; gap:14px; border:1px solid rgba(255,255,255,0.25);">
    <span><?php echo esc_html($msg); ?></span>
    <button type="button" onclick="document.getElementById('authToast').remove()" style="background:none; border:none; color:#ffffff; font-size:20px; cursor:pointer; line-height:1; padding:0 4px;">&times;</button>
</div>
<?php endif; ?>
</main>
</div>

<script>
// === premium.js (TILT + DYNAMIC SPECULAR SPOTLIGHT) ===
(()=>{'use strict';
const motion=matchMedia('(prefers-reduced-motion: reduce)');
const pointer=matchMedia('(hover: hover) and (pointer: fine)');
let active=null,frame=0;
function reset(){
  if(active){
    active.classList.remove('premium-tilt');
    active.style.removeProperty('--tilt-x');
    active.style.removeProperty('--tilt-y');
    active.style.removeProperty('--tilt-lift');
    active.style.removeProperty('--spot-x');
    active.style.removeProperty('--spot-y');
    active=null;
  }
  if(frame){cancelAnimationFrame(frame);frame=0;}
}
document.addEventListener('pointermove',event=>{
  if(motion.matches||!pointer.matches)return;
  const card=event.target.closest('.section .card,#integrations .integration-card,.editorial-unified-card,.article-card');
  if(!card){reset();return;}
  if(active!==card){
    reset();
    active=card;
    active.classList.add('premium-tilt');
  }
  const rect=card.getBoundingClientRect();
  const x=Math.max(-1,Math.min(1,(event.clientX-rect.left)/rect.width*2-1));
  const y=Math.max(-1,Math.min(1,(event.clientY-rect.top)/rect.height*2-1));
  const spotX=(event.clientX-rect.left).toFixed(1)+'px';
  const spotY=(event.clientY-rect.top).toFixed(1)+'px';
  if(frame)cancelAnimationFrame(frame);
  frame=requestAnimationFrame(()=>{
    frame=0;
    if(active!==card)return;
    card.style.setProperty('--tilt-x',(-y*2).toFixed(2)+'deg');
    card.style.setProperty('--tilt-y',(x*2).toFixed(2)+'deg');
    card.style.setProperty('--tilt-lift','-3px');
    card.style.setProperty('--spot-x',spotX);
    card.style.setProperty('--spot-y',spotY);
  });
},{passive:true});
document.addEventListener('pointerleave',reset);
window.addEventListener('blur',reset);
motion.addEventListener('change',reset);
pointer.addEventListener('change',reset);
})();

// Tabs Selection Logic with Crossfade
const tabs = [...document.querySelectorAll('.use-tab')];
function selectCase(i) {
  tabs.forEach((tab, n) => {
    tab.setAttribute('aria-selected', String(n === i));
    tab.tabIndex = n === i ? 0 : -1;
    const panel = document.getElementById('case-panel-' + n);
    if (panel) {
      if (n === i) {
        panel.hidden = false;
        panel.classList.remove('tab-entering');
        void panel.offsetWidth; // Force reflow for clean re-trigger
        panel.classList.add('tab-entering');
      } else {
        panel.hidden = true;
        panel.classList.remove('tab-entering');
      }
    }
  });
}
tabs.forEach((tab, i) => {
  tab.addEventListener('click', () => selectCase(i));
  tab.addEventListener('keydown', e => {
    let n = i;
    if (e.key === 'ArrowRight') n = (i + 1) % tabs.length;
    else if (e.key === 'ArrowLeft') n = (i - 1 + tabs.length) % tabs.length;
    else if (e.key === 'Home') n = 0;
    else if (e.key === 'End') n = tabs.length - 1;
    else return;
    e.preventDefault();
    selectCase(n);
    tabs[n].focus();
  });
});

// === WHOLE-HOMEPAGE MULTI-DIRECTIONAL CINEMATIC SCROLL REVEAL OBSERVER ===
(()=>{
  'use strict';
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (!('IntersectionObserver' in window)) return;

  const upSelectors = [
    '.sectiontop',
    '#use-cases .use-tabs',
    '#use-cases .case-stage',
    '.closing-cta'
  ];

  const leftSelectors = [
    '#section-1 .problem-art',
    '#section-2 .premium-art',
    '#how .workflow-art',
    '#teams .premium-team',
    '#about .about-art',
    '#section-9 .work-art'
  ];

  const rightSelectors = [
    '#section-1 .editorial-unified-card',
    '#section-2 .premium-copy',
    '#about .editorial-unified-card',
    '#section-9 .work-chapters article'
  ];

  const scaleSelectors = [
    '#integrations .integration-card',
    '#features .card',
    '#how .steps .card',
    '#teams .grid .card',
    '#section-7 .card',
    '#faq .faqs details',
    '#section-2 .fields span'
  ];

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, {
    rootMargin: '0px 0px -50px 0px',
    threshold: 0.08
  });

  function registerTargets(selectorList, dirClass) {
    const selectorStr = selectorList.join(', ');
    const elements = document.querySelectorAll(selectorStr);
    elements.forEach(el => {
      el.classList.add('reveal-ready', dirClass);
      observer.observe(el);
    });
  }

  registerTargets(upSelectors, 'reveal-up');
  registerTargets(leftSelectors, 'reveal-left');
  registerTargets(rightSelectors, 'reveal-right');
  registerTargets(scaleSelectors, 'reveal-scale');

  // Stagger cascading transitions for grid & list items
  const staggeredContainers = document.querySelectorAll(
    '.integrations-grid, #features .bento, #how .steps, #teams .grid, #section-7 .grid, #faq .faqs, #section-9 .work-chapters'
  );
  staggeredContainers.forEach(container => {
    Array.from(container.children).forEach((child, index) => {
      child.style.transitionDelay = ((index % 6) * 0.09).toFixed(2) + 's';
    });
  });

  const fieldsContainer = document.querySelector('#section-2 .fields');
  if (fieldsContainer) {
    Array.from(fieldsContainer.children).forEach((pill, index) => {
      pill.style.transitionDelay = ((index % 7) * 0.04).toFixed(2) + 's';
    });
  }

  // Safety Viewport Check: Elements already above the fold or intersecting trigger smoothly immediately
  requestAnimationFrame(() => {
    const vh = window.innerHeight;
    document.querySelectorAll('.reveal-ready:not(.is-visible)').forEach(el => {
      const rect = el.getBoundingClientRect();
      if (rect.top < vh - 40 && rect.bottom > 0) {
        el.classList.add('is-visible');
      }
    });
  });
})();

// Active Nav Link Observer
if ('IntersectionObserver' in window) {
  const navLinks = [...document.querySelectorAll('.glass-nav-center a, #main-nav a')];
  const sectionObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      const link = navLinks.find(a => a.hash === '#' + entry.target.id);
      if (link) {
        if (entry.isIntersecting) link.setAttribute('aria-current', 'location');
        else link.removeAttribute('aria-current');
      }
    });
  }, { rootMargin: '-15% 0px -60% 0px', threshold: 0 });
  navLinks.forEach(a => {
    const target = a.hash ? document.querySelector(a.hash) : null;
    if (target) sectionObserver.observe(target);
  });
}

// Workflow Video Reduced Motion Sync
const workflowVideo = document.getElementById('hero-workflow-video');
const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
function syncWorkflowMotion() {
  if (workflowVideo && motionPreference.matches) {
    workflowVideo.autoplay = false;
    workflowVideo.pause();
  }
}
syncWorkflowMotion();
motionPreference.addEventListener('change', syncWorkflowMotion);



</script>

<?php get_footer(); ?>
