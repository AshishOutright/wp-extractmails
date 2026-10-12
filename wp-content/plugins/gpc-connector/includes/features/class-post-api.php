<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST endpoints the CRM uses to create draft posts, insert backlinks and
 * look up post and link status. Replaces the "My Post API" plugin; routes,
 * headers and responses are unchanged.
 */
final class GPC_Connector_Post_API
{
    const REST_NAMESPACE = 'my-api/v1';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        // One POST route; the "type" field picks the action.
        // type=post (or omitted) → create post, type=li → insert backlink.
        register_rest_route(self::REST_NAMESPACE, '/create-post', [
            'methods'             => 'POST',
            'callback'            => [$this, 'router'],
            'permission_callback' => [$this, 'check_key'],
        ]);

        // GET ?url=<encoded url> or ?post_id=<int>
        register_rest_route(self::REST_NAMESPACE, '/post-status', [
            'methods'             => 'GET',
            'callback'            => [$this, 'post_status'],
            'permission_callback' => [$this, 'check_key'],
        ]);

        // GET ?post_id=<int>&backlink_url=<encoded url>&anchor_text=<optional>
        register_rest_route(self::REST_NAMESPACE, '/link-status', [
            'methods'             => 'GET',
            'callback'            => [$this, 'link_status'],
            'permission_callback' => [$this, 'check_key'],
        ]);

        // GET ?url=<post url>&anchor_text=<phrase>
        register_rest_route(self::REST_NAMESPACE, '/anchor-occurrences', [
            'methods'             => 'GET',
            'callback'            => [$this, 'anchor_occurrences'],
            'permission_callback' => [$this, 'check_key'],
        ]);
    }

    public function check_key(WP_REST_Request $request)
    {
        if (!GPC_Connector_Rest::key_matches('post_api', $request->get_header('X-API-Key'))) {
            return new WP_Error('forbidden', 'Invalid API Key', ['status' => 403]);
        }

        return true;
    }

    /**
     * The WordPress user behind a CRM request, identified by the GPC user's
     * email, checked for the capability the action needs.
     */
    private function get_authorized_gpc_user(WP_REST_Request $request, $action = 'create', $post_id = 0)
    {
        $params = $request->get_json_params();
        if (!is_array($params)) {
            $params = [];
        }

        $email = sanitize_email(
            $request->get_header('X-GPC-User-Email')
            ?: $request->get_param('gpc_user_email')
            ?: $request->get_param('current_email')
            ?: ($params['gpc_user_email'] ?? '')
            ?: ($params['current_email'] ?? '')
        );

        if (empty($email) || !is_email($email)) {
            return new WP_Error(
                'gpc_user_missing',
                'GPC user email is required for WordPress publishing authorization.',
                ['status' => 401]
            );
        }

        $user = get_user_by('email', $email);
        if (!$user) {
            return new WP_Error(
                'gpc_user_not_found',
                'This GPC user is not a WordPress user on this website.',
                ['status' => 403]
            );
        }

        if ($action === 'create' && !user_can($user, 'publish_posts')) {
            return new WP_Error(
                'gpc_user_not_authorized',
                'The user is not authorized to publish.',
                ['status' => 403]
            );
        }

        if ($action === 'edit' && (!$post_id || !user_can($user, 'edit_post', $post_id))) {
            return new WP_Error(
                'gpc_user_not_authorized',
                'This WordPress user is not authorized to edit this post from GPC.',
                ['status' => 403]
            );
        }

        return $user;
    }

    public function router(WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        $type   = strtolower(trim($params['type'] ?? 'post'));

        if ($type === 'li') {
            return $this->insert_backlink($request);
        }

        return $this->create_post($request);
    }

    /**
     * Creates a draft post authored by the GPC user.
     */
    private function create_post(WP_REST_Request $request)
    {
        $params = $request->get_json_params();

        $gpc_user = $this->get_authorized_gpc_user($request, 'create');
        if (is_wp_error($gpc_user)) {
            return $gpc_user;
        }

        $title   = sanitize_text_field($params['title'] ?? '');
        $content = wp_kses_post($params['content'] ?? '');

        if (empty($title) || empty($content)) {
            return new WP_Error('missing_fields', 'Title and content are required', ['status' => 400]);
        }

        $post_id = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'draft',
            'post_author'  => (int) $gpc_user->ID,
            'post_type'    => 'post',
        ]);

        if (is_wp_error($post_id)) {
            return new WP_Error('post_failed', 'Failed to create post', ['status' => 500]);
        }

        return rest_ensure_response([
            'success' => true,
            'post_id' => $post_id,
            'url'     => get_preview_post_link($post_id),
        ]);
    }

    /**
     * Links an existing phrase in a blog post (type = li).
     *
     * Expected payload:
     * {
     *   "type"        : "li",
     *   "anchor_text" : "exact phrase inside the blog post",
     *   "backlink_url": "https://external-site.com/page",
     *   "target_url"  : "https://yourblog.com/some-post/",
     *   "occurrences" : [0, 2]   // OPTIONAL — zero-based indices of the
     *                            // unlinked occurrences to link. If omitted,
     *                            // the FIRST unlinked occurrence is linked.
     * }
     */
    private function insert_backlink(WP_REST_Request $request)
    {
        $params = $request->get_json_params();

        $anchor_text  = trim($params['anchor_text'] ?? '');
        $backlink_url = trim($params['backlink_url'] ?? '');
        $target_url   = trim($params['target_url'] ?? '');
        $occurrences  = $params['occurrences'] ?? null;

        if (empty($anchor_text)) {
            return new WP_Error('missing_anchor_text', 'anchor_text is required.', ['status' => 400]);
        }

        if (empty($backlink_url) || !filter_var($backlink_url, FILTER_VALIDATE_URL)) {
            return new WP_Error('invalid_backlink_url', 'A valid backlink_url is required.', ['status' => 400]);
        }

        if (empty($target_url)) {
            return new WP_Error('missing_target_url', 'target_url is required.', ['status' => 400]);
        }

        $post_id = GPC_Connector_Util::url_to_post_id($target_url);

        if (!$post_id) {
            return new WP_Error(
                'target_url_not_found',
                'No target URL found matching: ' . esc_url($target_url),
                ['status' => 404]
            );
        }

        $post = get_post($post_id);

        if (!$post) {
            return new WP_Error('post_not_found', 'Post could not be loaded for the given target URL.', ['status' => 404]);
        }

        if ($post->post_type !== 'post') {
            return new WP_Error(
                'not_a_blog_post',
                'The target URL does not point to a blog post (post_type must be "post").',
                ['status' => 422]
            );
        }

        $gpc_user = $this->get_authorized_gpc_user($request, 'edit', $post_id);
        if (is_wp_error($gpc_user)) {
            return $gpc_user;
        }

        $original_content = $post->post_content;
        $all_hits         = $this->find_anchor_positions($original_content, $anchor_text);

        if (empty($all_hits)) {
            return new WP_Error(
                'anchor_text_not_found',
                'The anchor text "' . esc_html($anchor_text) . '" was not found in the post content.',
                ['status' => 404]
            );
        }

        // Only unlinked occurrences can be linked; indices count those alone.
        $unlinked = array_values(array_filter($all_hits, function ($hit) {
            return !$hit['already_linked'];
        }));

        if (empty($unlinked)) {
            return new WP_Error(
                'already_linked',
                'Every occurrence of the anchor text is already wrapped in a hyperlink.',
                ['status' => 409]
            );
        }

        if (is_array($occurrences) && !empty($occurrences)) {
            $wanted = [];
            foreach ($occurrences as $idx) {
                $idx = (int) $idx;
                if (isset($unlinked[$idx])) {
                    $wanted[$idx] = $unlinked[$idx];
                }
            }

            if (empty($wanted)) {
                return new WP_Error(
                    'invalid_occurrences',
                    'None of the requested occurrence indices match an unlinked anchor.',
                    ['status' => 400]
                );
            }
        } else {
            $wanted = [0 => $unlinked[0]];
        }

        $link_html = '<a href="' . esc_url($backlink_url) . '" target="_blank" rel="noopener">'
            . esc_html($anchor_text) . '</a>';

        // Replace right to left so earlier offsets stay valid.
        usort($wanted, function ($a, $b) {
            return $b['pos'] - $a['pos'];
        });

        $new_content = $original_content;
        $anchor_len  = strlen($anchor_text);
        foreach ($wanted as $hit) {
            $new_content = substr_replace($new_content, $link_html, $hit['pos'], $anchor_len);
        }

        $update_result = wp_update_post([
            'ID'           => $post_id,
            'post_content' => $new_content,
        ], true);

        if (is_wp_error($update_result)) {
            return new WP_Error('update_failed', 'Failed to update the post content.', ['status' => 500]);
        }

        return rest_ensure_response([
            'success'        => true,
            'post_id'        => $post_id,
            'post_url'       => get_permalink($post_id),
            'anchor_text'    => $anchor_text,
            'backlink_url'   => $backlink_url,
            'inserted_count' => count($wanted),
            'message'        => 'Backlink successfully inserted (' . count($wanted) . ' occurrence(s)).',
        ]);
    }

    /**
     * Every occurrence of anchor_text in the post at url, with the paragraph
     * around it and whether it is already linked. "index" counts unlinked
     * occurrences only and is what insert_backlink's "occurrences" expects.
     */
    public function anchor_occurrences(WP_REST_Request $request)
    {
        $url         = trim((string) $request->get_param('url'));
        $anchor_text = trim((string) $request->get_param('anchor_text'));

        if ($url === '') {
            return new WP_Error('missing_url', 'url query parameter is required.', ['status' => 400]);
        }

        if ($anchor_text === '') {
            return new WP_Error('missing_anchor_text', 'anchor_text query parameter is required.', ['status' => 400]);
        }

        $post_id = GPC_Connector_Util::url_to_post_id($url);
        if (!$post_id) {
            return new WP_Error('target_url_not_found', 'No post matched the supplied URL.', ['status' => 404]);
        }

        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('post_not_found', 'Post could not be loaded.', ['status' => 404]);
        }

        $gpc_user = $this->get_authorized_gpc_user($request, 'edit', $post_id);
        if (is_wp_error($gpc_user)) {
            return $gpc_user;
        }

        $content          = $post->post_content;
        $hits             = $this->find_anchor_positions($content, $anchor_text);
        $unlinked_counter = 0;
        $occurrences      = [];

        foreach ($hits as $i => $hit) {
            $paragraph = $this->extract_paragraph_around($content, $hit['pos'], strlen($anchor_text));

            $occurrences[] = [
                'index'            => $hit['already_linked'] ? null : $unlinked_counter,
                'absolute_index'   => $i,
                'already_linked'   => $hit['already_linked'],
                'paragraph_html'   => $paragraph['html'],
                'paragraph_marked' => $paragraph['marked'],
                'paragraph_text'   => $paragraph['text'],
            ];

            if (!$hit['already_linked']) {
                $unlinked_counter++;
            }
        }

        return rest_ensure_response([
            'success'     => true,
            'post_id'     => $post_id,
            'post_url'    => get_permalink($post_id),
            'title'       => $post->post_title,
            'anchor_text' => $anchor_text,
            'total'       => count($occurrences),
            'unlinked'    => $unlinked_counter,
            'occurrences' => $occurrences,
        ]);
    }

    /**
     * Current status of the post at url or post_id, so the CRM can show
     * Live, Trashed, Draft and so on. Always 200 with success: true; use
     * "exists" and "status" instead of the HTTP status.
     */
    public function post_status(WP_REST_Request $request)
    {
        $url     = trim((string) $request->get_param('url'));
        $post_id = (int) $request->get_param('post_id');

        if (!$post_id && $url !== '') {
            $post_id = GPC_Connector_Util::resolve_post_id_from_url($url);
        }

        $post = $post_id ? get_post($post_id) : null;

        if (!$post) {
            return rest_ensure_response([
                'success'   => true,
                'exists'    => false,
                'status'    => null,
                'post_id'   => $post_id,
                'permalink' => null,
                'title'     => null,
                'url'       => $url,
            ]);
        }

        return rest_ensure_response([
            'success'   => true,
            'exists'    => true,
            'status'    => $post->post_status,
            'post_id'   => $post_id,
            'permalink' => $this->permalink_for($post),
            'title'     => $post->post_title,
            'url'       => $url,
        ]);
    }

    /**
     * Whether the post exists, is published, and still contains the backlink.
     * post_id is preferred; url is accepted as a fallback.
     */
    public function link_status(WP_REST_Request $request)
    {
        $post_id      = (int) $request->get_param('post_id');
        $url          = trim((string) $request->get_param('url'));
        $backlink_url = trim((string) $request->get_param('backlink_url'));
        $anchor_text  = trim((string) $request->get_param('anchor_text'));

        if ($backlink_url === '' || !filter_var($backlink_url, FILTER_VALIDATE_URL)) {
            return new WP_Error(
                'invalid_backlink_url',
                'A valid backlink_url query parameter is required.',
                ['status' => 400]
            );
        }

        if (!$post_id && $url !== '') {
            $post_id = GPC_Connector_Util::resolve_post_id_from_url($url);
        }

        $post = $post_id ? get_post($post_id) : null;

        if (!$post) {
            return rest_ensure_response([
                'success'      => true,
                'exists'       => false,
                'status'       => null,
                'post_id'      => $post_id,
                'permalink'    => null,
                'title'        => null,
                'link_found'   => false,
                'link_live'    => false,
                'matched_link' => null,
            ]);
        }

        $matched_link = $this->find_backlink_in_content($post->post_content, $backlink_url, $anchor_text);
        $link_found   = $matched_link !== null;

        return rest_ensure_response([
            'success'      => true,
            'exists'       => true,
            'status'       => $post->post_status,
            'post_id'      => $post_id,
            'permalink'    => $this->permalink_for($post),
            'title'        => $post->post_title,
            'link_found'   => $link_found,
            'link_live'    => ($post->post_status === 'publish' && $link_found),
            'matched_link' => $matched_link,
        ]);
    }

    /** The public permalink for published posts, the preview link otherwise. */
    private function permalink_for($post)
    {
        return $post->post_status === 'publish'
            ? get_permalink($post->ID)
            : get_preview_post_link($post->ID);
    }

    private function find_backlink_in_content($content, $backlink_url, $anchor_text = '')
    {
        if ($content === '') {
            return null;
        }

        $expected_url = GPC_Connector_Util::normalize_link_url($backlink_url);

        if (!preg_match_all('/<a\b[^>]*href\s*=\s*([\'"])(.*?)\1[^>]*>(.*?)<\/a>/is', $content, $matches, PREG_SET_ORDER)) {
            return null;
        }

        foreach ($matches as $match) {
            $href = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (GPC_Connector_Util::normalize_link_url($href) !== $expected_url) {
                continue;
            }

            $link_text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($match[3])));
            if ($anchor_text !== '' && strcasecmp($link_text, $anchor_text) !== 0) {
                continue;
            }

            return [
                'href'        => esc_url_raw($href),
                'anchor_text' => $link_text,
            ];
        }

        return null;
    }

    /**
     * Byte offsets of every case-sensitive occurrence of $anchor_text, each
     * flagged with whether it sits inside an existing <a>…</a>.
     *
     * Returns [['pos' => int, 'already_linked' => bool], ...] ordered by pos.
     */
    private function find_anchor_positions($content, $anchor_text)
    {
        $hits = [];
        if ($anchor_text === '' || $content === '') {
            return $hits;
        }

        $len    = strlen($anchor_text);
        $offset = 0;
        while (($pos = strpos($content, $anchor_text, $offset)) !== false) {
            $hits[] = [
                'pos'            => $pos,
                'already_linked' => $this->position_is_inside_link($content, $pos),
            ];
            $offset = $pos + $len;
        }

        return $hits;
    }

    /**
     * Whether $pos falls inside an open <a …> tag, judged by whether the last
     * <a before it comes after the last </a>.
     */
    private function position_is_inside_link($content, $pos)
    {
        $haystack = substr($content, 0, $pos);

        $last_open = -1;
        if (preg_match_all('/<a\b[^>]*>/i', $haystack, $opens, PREG_OFFSET_CAPTURE)) {
            $last_open = end($opens[0])[1];
        }

        $last_close = -1;
        if (preg_match_all('/<\/a\s*>/i', $haystack, $closes, PREG_OFFSET_CAPTURE)) {
            $last_close = end($closes[0])[1];
        }

        return $last_open > $last_close;
    }

    /**
     * The block (<p>, <div>, <li>, <h1>-<h6>, <blockquote>) around the
     * occurrence at $pos, for previews in the CRM. Falls back to about 200
     * characters either side when no block tags are found.
     *
     * Returns ['html' => raw block, 'marked' => block with the anchor in
     * <mark>, 'text' => block without tags].
     */
    private function extract_paragraph_around($content, $pos, $len)
    {
        $block_tags   = 'p|div|li|h[1-6]|blockquote';
        $before_chunk = substr($content, 0, $pos);
        $after_chunk  = substr($content, $pos);

        if (preg_match_all('/<(?:' . $block_tags . ')\b[^>]*>/i', $before_chunk, $opens, PREG_OFFSET_CAPTURE)) {
            $start = end($opens[0])[1];
        } else {
            $start = max(0, $pos - 200);
        }

        if (preg_match('/<\/(?:' . $block_tags . ')>/i', $after_chunk, $m, PREG_OFFSET_CAPTURE)) {
            $end_offset = $pos + $m[0][1] + strlen($m[0][0]);
        } else {
            $end_offset = min(strlen($content), $pos + $len + 200);
        }

        $paragraph_html = substr($content, $start, $end_offset - $start);

        // $pos is relative to the whole content, so shift it into the slice.
        $relative = $pos - $start;
        $anchor   = substr($content, $pos, $len);
        if ($relative >= 0 && $relative + $len <= strlen($paragraph_html)) {
            $marked = substr($paragraph_html, 0, $relative)
                . '<mark>' . $anchor . '</mark>'
                . substr($paragraph_html, $relative + $len);
        } else {
            $marked = $paragraph_html;
        }

        return [
            'html'   => $paragraph_html,
            'marked' => $marked,
            'text'   => trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($paragraph_html))),
        ];
    }
}
