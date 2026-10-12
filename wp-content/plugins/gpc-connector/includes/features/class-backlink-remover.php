<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST endpoint the CRM calls to unlink a tracked backlink from a post when
 * it marks the backlink removed. Replaces the "Rightee Backlink Remover" plugin.
 */
final class GPC_Connector_Backlink_Remover
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('rightee/v1', '/remove-backlink', [
            'methods'             => 'POST',
            'callback'            => [$this, 'remove_backlink_from_post'],
            'permission_callback' => [$this, 'check_key'],
        ]);
    }

    public function check_key(WP_REST_Request $request)
    {
        if (GPC_Connector_Settings::api_key('backlink_remover') === '') {
            return new WP_Error('api_key_not_configured', 'Backlink removal API key is not configured.', ['status' => 500]);
        }

        if (!GPC_Connector_Rest::key_matches('backlink_remover', $request->get_header('X-API-Key'))) {
            return new WP_Error('forbidden', 'Invalid API Key.', ['status' => 403]);
        }

        return true;
    }

    /**
     * Accepts both the CRM field names (post_id_c, backlink_url_c, …) and plain
     * ones. Unwraps the first matching link, or all of them with remove_all.
     */
    public function remove_backlink_from_post(WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        if (!is_array($params)) {
            $params = [];
        }

        $post_id      = absint($params['post_id_c'] ?? $params['post_id'] ?? $params['wp_post_id'] ?? 0);
        $source_url   = esc_url_raw($params['source_url_c'] ?? $params['source_url'] ?? $params['gp_doc_url_c'] ?? $params['wp_post_url'] ?? '');
        $backlink_url = esc_url_raw($params['backlink_url_c'] ?? $params['backlink_url'] ?? $params['target_url_c'] ?? $params['target_url'] ?? '');
        $anchor_text  = sanitize_text_field($params['anchor_text_c'] ?? $params['anchor_text'] ?? '');
        $remove_all   = !empty($params['remove_all']);

        if (empty($backlink_url) || !filter_var($backlink_url, FILTER_VALIDATE_URL)) {
            return new WP_Error('invalid_backlink_url', 'A valid backlink_url_c/backlink_url is required.', ['status' => 400]);
        }

        if (!$post_id && !empty($source_url)) {
            $post_id = GPC_Connector_Util::url_to_post_id($source_url);
        }

        if (!$post_id) {
            return new WP_Error('post_not_found', 'post_id_c or a valid source_url_c is required.', ['status' => 404]);
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'post') {
            return new WP_Error('post_not_found', 'The target post could not be found.', ['status' => 404]);
        }

        $result = $this->remove_backlink_from_content($post->post_content, $backlink_url, $anchor_text, $remove_all);
        if ($result['removed_count'] < 1) {
            return new WP_Error('backlink_not_found', 'The backlink was not found in the post content.', [
                'status'       => 404,
                'post_id'      => $post_id,
                'post_url'     => get_permalink($post_id),
                'backlink_url' => $backlink_url,
            ]);
        }

        $update_result = wp_update_post([
            'ID'           => $post_id,
            'post_content' => $result['content'],
        ], true);

        if (is_wp_error($update_result)) {
            return new WP_Error('update_failed', 'Failed to update the post content.', ['status' => 500]);
        }

        return rest_ensure_response([
            'success'       => true,
            'post_id'       => $post_id,
            'post_url'      => get_permalink($post_id),
            'backlink_url'  => $backlink_url,
            'anchor_text'   => $anchor_text,
            'removed_count' => $result['removed_count'],
            'message'       => 'Backlink removed successfully.',
        ]);
    }

    /**
     * Replaces matching <a> tags with their inner HTML. When no tag matches,
     * falls back to deleting the URL where it appears as plain text.
     */
    private function remove_backlink_from_content($content, $backlink_url, $anchor_text = '', $remove_all = false)
    {
        $removed_count           = 0;
        $normalized_backlink_url = GPC_Connector_Util::normalize_link_url($backlink_url);
        $expected_anchor_text    = $this->normalize_text_for_compare($anchor_text);

        $new_content = preg_replace_callback('/<a\b([^>]*)>(.*?)<\/a>/is', function ($matches) use ($normalized_backlink_url, $expected_anchor_text, $remove_all, &$removed_count) {
            if (!$remove_all && $removed_count > 0) {
                return $matches[0];
            }

            $href = $this->extract_href_from_attrs($matches[1]);
            if (empty($href) || GPC_Connector_Util::normalize_link_url($href) !== $normalized_backlink_url) {
                return $matches[0];
            }

            // "No anchor text" is the placeholder the backlink sync stores for bare URLs.
            $actual_anchor_text = $this->normalize_text_for_compare($matches[2]);
            if (!empty($expected_anchor_text) && $expected_anchor_text !== 'no anchor text' && $actual_anchor_text !== $expected_anchor_text) {
                return $matches[0];
            }

            $removed_count++;
            return $matches[2];
        }, $content);

        if ($removed_count < 1) {
            $plain_result   = $this->remove_plain_backlink_url($new_content, $backlink_url, $remove_all);
            $new_content    = $plain_result['content'];
            $removed_count += $plain_result['removed_count'];
        }

        return [
            'content'       => $new_content,
            'removed_count' => $removed_count,
        ];
    }

    private function remove_plain_backlink_url($content, $backlink_url, $remove_all = false)
    {
        $removed_count = 0;
        $variants      = array_values(array_unique([
            $backlink_url,
            esc_url($backlink_url),
            esc_url_raw($backlink_url),
            esc_attr($backlink_url),
            untrailingslashit($backlink_url),
            trailingslashit($backlink_url),
        ]));

        foreach ($variants as $variant) {
            if ($variant === '') {
                continue;
            }

            if ($remove_all) {
                $count = substr_count($content, $variant);
                if ($count > 0) {
                    $content        = str_replace($variant, '', $content);
                    $removed_count += $count;
                }
            } else {
                $position = strpos($content, $variant);
                if ($position !== false) {
                    $content = substr_replace($content, '', $position, strlen($variant));
                    $removed_count++;
                    break;
                }
            }
        }

        return [
            'content'       => $content,
            'removed_count' => $removed_count,
        ];
    }

    private function extract_href_from_attrs($attrs)
    {
        if (preg_match('/\bhref\s*=\s*(["\'])(.*?)\1/i', $attrs, $matches)) {
            return html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8');
        }

        if (preg_match('/\bhref\s*=\s*([^\s>]+)/i', $attrs, $matches)) {
            return html_entity_decode(trim($matches[1], "\"'"), ENT_QUOTES, 'UTF-8');
        }

        return '';
    }

    private function normalize_text_for_compare($text)
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES, 'UTF-8');
        $text = wp_strip_all_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
