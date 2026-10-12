<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helpers shared by several features.
 */
final class GPC_Connector_Util
{
    /**
     * Resolves a post URL to its ID, retrying with and without a trailing slash.
     */
    public static function url_to_post_id($url)
    {
        $post_id = url_to_postid($url);
        if ($post_id) {
            return (int) $post_id;
        }

        $post_id = url_to_postid(untrailingslashit($url));
        if ($post_id) {
            return (int) $post_id;
        }

        return (int) url_to_postid(trailingslashit($url));
    }

    /**
     * Like url_to_post_id(), but also understands ?p=123 style preview and
     * default-permalink URLs.
     */
    public static function resolve_post_id_from_url($url)
    {
        $parsed = wp_parse_url($url);

        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $qs);
            if (!empty($qs['p'])) {
                return (int) $qs['p'];
            }
        }

        return self::url_to_post_id($url);
    }

    /**
     * Normalizes a link URL for comparison: lowercase scheme and host, no www,
     * no trailing slash and no fragment.
     */
    public static function normalize_link_url($url)
    {
        $url   = trim(html_entity_decode((string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $parts = wp_parse_url($url);

        if (!is_array($parts) || empty($parts['host'])) {
            return untrailingslashit(strtolower($url));
        }

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host   = strtolower(preg_replace('/^www\./i', '', $parts['host']));
        $path   = isset($parts['path']) ? untrailingslashit($parts['path']) : '';
        $query  = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $scheme . '://' . $host . $path . $query;
    }

    /**
     * This site's host without www. CRM records use it to tell sites apart.
     */
    public static function site_name()
    {
        $site_host = wp_parse_url(home_url(), PHP_URL_HOST);

        if (!empty($site_host)) {
            return preg_replace('/^www\./', '', strtolower($site_host));
        }

        $site_name = sanitize_text_field(get_bloginfo('name'));
        return !empty($site_name) ? $site_name : 'wordpress-site';
    }
}
