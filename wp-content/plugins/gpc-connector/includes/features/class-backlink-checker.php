<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST endpoint that checks whether a public blog page contains a link to a
 * URL with the given anchor text. Replaces the "Backlink Checker API" plugin.
 *
 * POST /wp-json/backlink-checker/v1/check
 * {"backlinkurl": "...", "anchor_text": "...", "blog_url": "..."}
 * Responds with the JSON boolean true or false.
 */
final class GPC_Connector_Backlink_Checker
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('backlink-checker/v1', '/check', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'check'],
            'permission_callback' => [$this, 'authorize'],
            'args'                => [
                'backlinkurl' => ['required' => true, 'type' => 'string'],
                'anchor_text' => ['required' => true, 'type' => 'string'],
                'blog_url'    => ['required' => true, 'type' => 'string'],
            ],
        ]);
    }

    public function authorize(WP_REST_Request $request)
    {
        if (!GPC_Connector_Rest::key_matches('backlink_checker', $request->get_header('x-api-key'))) {
            return new WP_Error('backlink_checker_unauthorized', 'A valid X-API-Key header is required.', ['status' => 401]);
        }

        return true;
    }

    public function check(WP_REST_Request $request)
    {
        $backlink_url = trim($request->get_param('backlinkurl'));
        $anchor_text  = $this->normalize_text($request->get_param('anchor_text'));
        $blog_url     = trim($request->get_param('blog_url'));

        if (!$this->is_http_url($backlink_url) || !wp_http_validate_url($blog_url) || $anchor_text === '') {
            return new WP_Error('backlink_checker_invalid_input', 'Provide valid HTTP(S) URLs and nonempty anchor_text.', ['status' => 400]);
        }

        $response = wp_safe_remote_get($blog_url, [
            'timeout'             => 12,
            'redirection'         => 3,
            'limit_response_size' => 2 * MB_IN_BYTES,
            'headers'             => ['Accept' => 'text/html'],
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('backlink_checker_fetch_failed', 'Could not fetch the blog page.', ['status' => 502]);
        }

        if (wp_remote_retrieve_response_code($response) !== 200) {
            return new WP_Error('backlink_checker_fetch_failed', 'The blog page did not return HTTP 200.', ['status' => 502]);
        }

        $content_type = wp_remote_retrieve_header($response, 'content-type');
        if (is_string($content_type) && $content_type !== '' && !preg_match('~^(text/html|application/xhtml\+xml)(;|$)~i', $content_type)) {
            return new WP_Error('backlink_checker_not_html', 'The blog URL did not return HTML.', ['status' => 502]);
        }

        $html = wp_remote_retrieve_body($response);
        if (strlen($html) >= 2 * MB_IN_BYTES) {
            return new WP_Error('backlink_checker_page_too_large', 'The blog page exceeds the 2 MB scan limit.', ['status' => 502]);
        }

        if (!class_exists('DOMDocument')) {
            return new WP_Error('backlink_checker_dom_missing', 'The PHP DOM extension is required.', ['status' => 500]);
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded   = $document->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return new WP_Error('backlink_checker_invalid_html', 'The blog page could not be parsed.', ['status' => 502]);
        }

        foreach ($document->getElementsByTagName('a') as $link) {
            if (!$link->hasAttribute('href') || $this->normalize_text($link->textContent) !== $anchor_text) {
                continue;
            }

            $href = WP_Http::make_absolute_url(trim($link->getAttribute('href')), $blog_url);
            if ($this->urls_match($href, $backlink_url)) {
                return new WP_REST_Response(true, 200);
            }
        }

        return new WP_REST_Response(false, 200);
    }

    private function is_http_url($url)
    {
        $parts = wp_parse_url($url);

        return is_array($parts)
            && isset($parts['scheme'], $parts['host'])
            && in_array(strtolower($parts['scheme']), ['http', 'https'], true)
            && $parts['host'] !== ''
            && !isset($parts['user'])
            && !isset($parts['pass'])
            && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    private function normalize_text($text)
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $text));
    }

    /**
     * Scheme, host, port, path and query must match. Host case, a trailing
     * slash and the fragment are ignored.
     */
    private function urls_match($first, $second)
    {
        if (!$this->is_http_url($first) || !$this->is_http_url($second)) {
            return false;
        }

        $a = wp_parse_url($first);
        $b = wp_parse_url($second);

        return strtolower($a['scheme']) === strtolower($b['scheme'])
            && strtolower($a['host']) === strtolower($b['host'])
            && ($a['port'] ?? null) === ($b['port'] ?? null)
            && untrailingslashit($a['path'] ?? '/') === untrailingslashit($b['path'] ?? '/')
            && ($a['query'] ?? '') === ($b['query'] ?? '');
    }
}
