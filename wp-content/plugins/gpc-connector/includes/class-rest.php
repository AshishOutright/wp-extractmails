<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * CORS and API key checks shared by the REST features.
 */
final class GPC_Connector_Rest
{
    /**
     * Namespaces served by this plugin. They are the same ones the old plugins
     * used, so the CRM keeps calling the same URLs.
     */
    const NAMESPACES = [
        'my-api/v1',
        'rightee/v1',
        'backlink-checker/v1',
        'blog-link-extractor/v2',
    ];

    const ALLOWED_HEADERS = 'Content-Type, Authorization, X-WP-Nonce, X-API-Key, X-BLE-API-Key, X-GPC-User-Email';

    public static function init()
    {
        // Runs after WordPress's own rest_send_cors_headers (priority 10) so
        // these headers replace its values for our routes.
        add_filter('rest_pre_serve_request', [__CLASS__, 'send_cors_headers'], 20, 3);
    }

    /**
     * The CRM calls these endpoints from the browser, so they allow any origin.
     * This covers the OPTIONS preflight too, which WordPress answers itself.
     */
    public static function send_cors_headers($served, $result, $request)
    {
        $route = ltrim((string) $request->get_route(), '/');

        foreach (self::NAMESPACES as $namespace) {
            if (strpos($route, $namespace . '/') === 0) {
                header_remove('Access-Control-Allow-Credentials');
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
                header('Access-Control-Allow-Headers: ' . self::ALLOWED_HEADERS);
                header('Access-Control-Max-Age: 86400');
                break;
            }
        }

        return $served;
    }

    /**
     * Whether $provided matches the inbound API key saved for $key_name.
     * An empty saved key never matches.
     */
    public static function key_matches($key_name, $provided)
    {
        $expected = GPC_Connector_Settings::api_key($key_name);

        return $expected !== ''
            && is_string($provided)
            && $provided !== ''
            && hash_equals($expected, $provided);
    }
}
