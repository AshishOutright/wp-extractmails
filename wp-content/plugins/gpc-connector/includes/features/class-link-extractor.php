<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST endpoint that lists the links inside the main article content of any
 * public URL. The page is fetched fresh on every request. Replaces the
 * "Blog Link Extractor API" plugin.
 *
 * GET /wp-json/blog-link-extractor/v2/links?url=<encoded url>
 */
final class GPC_Connector_Link_Extractor
{
    const REST_NAMESPACE = 'blog-link-extractor/v2';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route(self::REST_NAMESPACE, '/links', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'extract_links'],
            'permission_callback' => [$this, 'permission_check'],
            'args'                => [
                'url' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'esc_url_raw',
                    'validate_callback' => function ($value) {
                        return filter_var($value, FILTER_VALIDATE_URL) !== false;
                    },
                ],
            ],
        ]);
    }

    public function permission_check(WP_REST_Request $request)
    {
        $provided_key = (string) $request->get_header('X-BLE-API-Key');

        if ($provided_key === '') {
            $provided_key = isset($_GET['api_key'])
                ? sanitize_text_field(wp_unslash($_GET['api_key']))
                : '';
        }

        return GPC_Connector_Rest::key_matches('link_extractor', $provided_key);
    }

    public function extract_links(WP_REST_Request $request)
    {
        $url = trim((string) $request->get_param('url'));

        if (!$this->is_safe_public_url($url)) {
            return new WP_Error(
                'ble_invalid_url',
                'The URL must be a valid public HTTP/HTTPS URL. Private/local IPs are blocked.',
                ['status' => 400]
            );
        }

        $response = wp_safe_remote_get($url, [
            'timeout'             => 20,
            'redirection'         => 5,
            'limit_response_size' => 10 * 1024 * 1024,
            'headers'             => [
                'Accept'     => 'text/html,application/xhtml+xml',
                'User-Agent' => 'Blog-Link-Extractor/2.0; ' . home_url('/'),
            ],
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('ble_fetch_failed', $response->get_error_message(), ['status' => 502]);
        }

        $status       = (int) wp_remote_retrieve_response_code($response);
        $content_type = (string) wp_remote_retrieve_header($response, 'content-type');
        $body         = (string) wp_remote_retrieve_body($response);

        if ($status < 200 || $status >= 400) {
            return new WP_Error('ble_remote_http_error', 'Target URL returned HTTP ' . $status . '.', ['status' => 502]);
        }

        if (
            $content_type !== '' &&
            stripos($content_type, 'text/html') === false &&
            stripos($content_type, 'application/xhtml+xml') === false
        ) {
            return new WP_Error('ble_not_html', 'Target URL did not return an HTML document.', ['status' => 415]);
        }

        if ($body === '') {
            return new WP_Error('ble_empty_response', 'Target URL returned an empty response.', ['status' => 502]);
        }

        $final_url = $this->get_final_url($response, $url);
        $result    = $this->parse_article_links($body, $final_url);

        nocache_headers();
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        return new WP_REST_Response([
            'success'          => true,
            'requested_url'    => $url,
            'final_url'        => $final_url,
            'content_selector' => $result['selector'],
            'fetched_at_utc'   => gmdate('c'),
            'total_links'      => count($result['links']),
            'links'            => $result['links'],
        ], 200);
    }

    /**
     * The WordPress HTTP API follows redirects without always exposing the
     * final URL, so use a transport's x-final-url header when there is one.
     */
    private function get_final_url($response, $fallback)
    {
        $location = wp_remote_retrieve_header($response, 'x-final-url');
        return $location ? esc_url_raw($location) : $fallback;
    }

    private function parse_article_links($html, $source_url)
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        if (!$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING | LIBXML_NOERROR)) {
            libxml_clear_errors();
            return ['selector' => 'none', 'links' => []];
        }

        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Drop areas that are almost never part of the article body. The list
        // is broad on purpose so menus, footers, sidebars, widgets and
        // comments don't leak into the result.
        $remove_classes = [
            'sidebar', 'widget', 'widgets', 'navigation', 'navbar', 'menu', 'comments', 'comment',
            'related-posts', 'related', 'share', 'social', 'breadcrumb', 'breadcrumbs', 'author-box',
            'newsletter', 'subscription', 'popup', 'modal', 'advertisement', 'ads',
        ];

        $remove_xpath = ['//header', '//footer', '//nav', '//aside', '//form', '//script', '//style', '//noscript', '//template', '//svg'];
        foreach ($remove_classes as $class) {
            $remove_xpath[] = $this->class_xpath('*', $class);
        }

        $remove_nodes = $xpath->query(implode(' | ', $remove_xpath));

        if ($remove_nodes) {
            // Copy first; removing while iterating a live DOMNodeList skips nodes.
            $nodes = [];
            foreach ($remove_nodes as $node) {
                $nodes[] = $node;
            }
            foreach ($nodes as $node) {
                if ($node->parentNode) {
                    $node->parentNode->removeChild($node);
                }
            }
        }

        // Article containers, most specific first.
        $selectors = [
            $this->class_xpath('article', 'entry-content'),
            $this->class_xpath('article', 'post-content'),
            $this->class_xpath('article', 'article-content'),
            $this->class_xpath('*', 'entry-content'),
            $this->class_xpath('*', 'post-content'),
            $this->class_xpath('*', 'article-content'),
            $this->class_xpath('*', 'single-post-content'),
            $this->class_xpath('*', 'blog-content'),
            $this->class_xpath('*', 'post-body'),
            $this->class_xpath('*', 'article-body'),
            $this->class_xpath('*', 'wp-block-post-content'),
            '//main',
            '//article',
        ];

        $container     = null;
        $selector_used = 'none';

        foreach ($selectors as $selector) {
            $nodes = $xpath->query($selector);

            if (!$nodes || $nodes->length === 0) {
                continue;
            }

            // Pick the candidate with the most text.
            $best        = null;
            $best_length = 0;

            foreach ($nodes as $node) {
                $text_length = mb_strlen(trim(preg_replace('/\s+/u', ' ', $node->textContent)));

                if ($text_length > $best_length) {
                    $best        = $node;
                    $best_length = $text_length;
                }
            }

            if ($best && $best_length >= 100) {
                $container     = $best;
                $selector_used = $selector;
                break;
            }
        }

        // No recognizable article wrapper: score the body's children by text
        // length and link count, favouring content/article/post names.
        if (!$container) {
            $body_nodes = $xpath->query('//body/*');
            $best       = null;
            $best_score = 0;

            if ($body_nodes) {
                foreach ($body_nodes as $node) {
                    $text_length = mb_strlen(trim(preg_replace('/\s+/u', ' ', $node->textContent)));

                    if ($text_length < 150) {
                        continue;
                    }

                    $links = $xpath->query('.//a', $node);
                    $score = $text_length + (($links ? $links->length : 0) * 50);

                    $class = strtolower((string) $node->getAttribute('class'));
                    $id    = strtolower((string) $node->getAttribute('id'));

                    foreach (['content', 'article', 'post'] as $hint) {
                        if (strpos($class, $hint) !== false || strpos($id, $hint) !== false) {
                            $score += 10000;
                            break;
                        }
                    }

                    if ($score > $best_score) {
                        $best_score = $score;
                        $best       = $node;
                    }
                }
            }

            if ($best) {
                $container     = $best;
                $selector_used = 'content-density-fallback';
            }
        }

        if (!$container) {
            return ['selector' => 'none', 'links' => []];
        }

        $links   = [];
        $anchors = $xpath->query('.//a', $container);

        if (!$anchors) {
            return ['selector' => $selector_used, 'links' => []];
        }

        foreach ($anchors as $anchor) {
            $href = trim((string) $anchor->getAttribute('href'));

            if ($href === '' || strpos($href, '#') === 0 || preg_match('/^(javascript:|mailto:|tel:|sms:|data:|blob:)/i', $href)) {
                continue;
            }

            $target_url = $this->resolve_url($href, $source_url);
            if (!$target_url) {
                continue;
            }

            // Skip empty and icon-only links.
            $anchor_text = trim(preg_replace('/\s+/u', ' ', $anchor->textContent));
            if ($anchor_text === '') {
                continue;
            }

            $links[] = [
                'anchor_text' => $anchor_text,
                'target_url'  => $target_url,
                'source_url'  => $source_url,
                'rel'         => trim((string) $anchor->getAttribute('rel')),
            ];
        }

        return ['selector' => $selector_used, 'links' => $links];
    }

    /** XPath for $tag elements that have $class as one of their classes. */
    private function class_xpath($tag, $class)
    {
        return '//' . $tag . '[contains(concat(" ", normalize-space(@class), " "), " ' . $class . ' ")]';
    }

    private function resolve_url($href, $base_url)
    {
        $href = html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (strpos($href, '//') === 0) {
            return esc_url_raw(wp_parse_url($base_url, PHP_URL_SCHEME) . ':' . $href);
        }

        if (preg_match('#^https?://#i', $href)) {
            return esc_url_raw($href);
        }

        $base = wp_parse_url($base_url);

        if (!$base || empty($base['scheme']) || empty($base['host'])) {
            return false;
        }

        $port      = isset($base['port']) ? ':' . $base['port'] : '';
        $base_path = $base['path'] ?? '/';

        // Root-relative, or relative to the base URL's directory.
        $path = strpos($href, '/') === 0 ? $href : preg_replace('#/[^/]*$#', '/', $base_path) . $href;

        $fragment     = '';
        $fragment_pos = strpos($path, '#');
        if ($fragment_pos !== false) {
            $fragment = substr($path, $fragment_pos);
            $path     = substr($path, 0, $fragment_pos);
        }

        $query     = '';
        $query_pos = strpos($path, '?');
        if ($query_pos !== false) {
            $query = substr($path, $query_pos);
            $path  = substr($path, 0, $query_pos);
        }

        $normalized = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($normalized);
                continue;
            }

            $normalized[] = $part;
        }

        return esc_url_raw(
            $base['scheme'] . '://' . $base['host'] . $port . '/' . implode('/', $normalized) . $query . $fragment
        );
    }

    /**
     * Blocks localhost and private or reserved IPs, including hostnames that
     * resolve to them, so the endpoint can't be used to probe internal hosts.
     */
    private function is_safe_public_url($url)
    {
        $parts = wp_parse_url($url);

        if (
            !$parts ||
            empty($parts['scheme']) ||
            empty($parts['host']) ||
            !in_array(strtolower($parts['scheme']), ['http', 'https'], true)
        ) {
            return false;
        }

        $host = strtolower(trim($parts['host'], '[]'));

        if ($host === 'localhost' || $host === 'localhost.localdomain') {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->is_public_ip($host);
        }

        if (function_exists('dns_get_record')) {
            $records = @dns_get_record($host, DNS_A | DNS_AAAA);

            if (is_array($records)) {
                foreach ($records as $record) {
                    $ip = !empty($record['ip']) ? $record['ip'] : ($record['ipv6'] ?? '');

                    if ($ip && !$this->is_public_ip($ip)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function is_public_ip($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
