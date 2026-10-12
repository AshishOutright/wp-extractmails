<?php
/**
 * Plugin Name: GPC Connector
 * Description: One plugin for every GuestPostCRM integration on this site. Syncs backlinks and published blogs to the CRM through smart_gateway, and serves the REST endpoints the CRM calls to create posts and insert, check, extract and remove backlinks.
 * Version: 1.0.0
 * Author: Outright Systems
 * License: GPL-2.0-or-later
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Text Domain: gpc-connector
 */

if (!defined('ABSPATH')) {
    exit;
}

define('GPC_CONNECTOR_VERSION', '1.0.0');
define('GPC_CONNECTOR_FILE', __FILE__);
define('GPC_CONNECTOR_DIR', plugin_dir_path(__FILE__));

require_once GPC_CONNECTOR_DIR . 'includes/class-util.php';
require_once GPC_CONNECTOR_DIR . 'includes/class-settings.php';
require_once GPC_CONNECTOR_DIR . 'includes/class-crm-client.php';
require_once GPC_CONNECTOR_DIR . 'includes/class-rest.php';
require_once GPC_CONNECTOR_DIR . 'includes/class-save-notice.php';
require_once GPC_CONNECTOR_DIR . 'includes/features/class-backlink-sync.php';
require_once GPC_CONNECTOR_DIR . 'includes/features/class-published-blog-sync.php';
require_once GPC_CONNECTOR_DIR . 'includes/features/class-post-api.php';
require_once GPC_CONNECTOR_DIR . 'includes/features/class-backlink-remover.php';
require_once GPC_CONNECTOR_DIR . 'includes/features/class-backlink-checker.php';
require_once GPC_CONNECTOR_DIR . 'includes/features/class-link-extractor.php';
require_once GPC_CONNECTOR_DIR . 'includes/features/class-backlink-scanner.php';

final class GPC_Connector
{
    /**
     * Every feature bundled here, keyed by the id used in settings. Each one
     * replaces a plugin that used to be installed on its own.
     */
    public static function features()
    {
        return [
            'backlink_sync' => [
                'class'       => 'GPC_Connector_Backlink_Sync',
                'label'       => 'Backlink sync',
                'description' => 'When a post is saved, sends the links added since its published version to the CRM module outr_seo_backlinksmain.',
                'replaces'    => 'Rightee Backlink Tracker',
            ],
            'published_blog_sync' => [
                'class'       => 'GPC_Connector_Published_Blog_Sync',
                'label'       => 'Published blog sync',
                'description' => 'When a post is published, or updated while published, sends its details to the CRM module outr_published_blogs.',
                'replaces'    => 'Published Blog Notification',
            ],
            'post_api' => [
                'class'       => 'GPC_Connector_Post_API',
                'label'       => 'Post API',
                'description' => 'REST endpoints under my-api/v1 for creating draft posts, inserting backlinks, and checking post and link status.',
                'replaces'    => 'My Post API',
            ],
            'backlink_remover' => [
                'class'       => 'GPC_Connector_Backlink_Remover',
                'label'       => 'Backlink remover',
                'description' => 'REST endpoint rightee/v1/remove-backlink that unlinks a tracked backlink from a post.',
                'replaces'    => 'Rightee Backlink Remover',
            ],
            'backlink_checker' => [
                'class'       => 'GPC_Connector_Backlink_Checker',
                'label'       => 'Backlink checker',
                'description' => 'REST endpoint backlink-checker/v1/check that checks whether a public page links to a URL with the given anchor text.',
                'replaces'    => 'Backlink Checker API',
            ],
            'link_extractor' => [
                'class'       => 'GPC_Connector_Link_Extractor',
                'label'       => 'Blog link extractor',
                'description' => 'REST endpoint blog-link-extractor/v2/links that lists the links inside the article body of a public URL.',
                'replaces'    => 'Blog Link Extractor API',
            ],
            'backlink_scanner' => [
                'class'       => 'GPC_Connector_Backlink_Scanner',
                'label'       => 'Backlink scanner',
                'description' => 'Tools → Backlink Scanner and the /backlinks/ report for finding and removing outbound links on this site.',
                'replaces'    => 'Backlink Scanner',
            ],
        ];
    }

    public static function boot()
    {
        GPC_Connector_Settings::init();
        GPC_Connector_Rest::init();

        foreach (self::features() as $key => $feature) {
            if (GPC_Connector_Settings::feature_enabled($key)) {
                new $feature['class']();
            }
        }

        if (
            GPC_Connector_Settings::feature_enabled('backlink_sync') ||
            GPC_Connector_Settings::feature_enabled('published_blog_sync')
        ) {
            GPC_Connector_Save_Notice::init();
        }

        add_action('init', [__CLASS__, 'maybe_flush_rewrite_rules'], 99);
    }

    public static function activate()
    {
        // The scanner table is created even when the feature is off so that
        // switching it on later needs no extra step. dbDelta keeps existing data.
        GPC_Connector_Backlink_Scanner::create_table();

        if (GPC_Connector_Settings::feature_enabled('backlink_scanner')) {
            GPC_Connector_Backlink_Scanner::add_rewrite_rule();
        }

        flush_rewrite_rules();
    }

    public static function deactivate()
    {
        global $wp_rewrite, $wpdb;

        // The rule is still registered during this request, so drop it before
        // flushing or the flush would keep it.
        unset($wp_rewrite->extra_rules_top[GPC_Connector_Backlink_Scanner::REWRITE_REGEX]);
        flush_rewrite_rules();

        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '\\_transient\\_gpc\\_connector\\_notice\\_%'
                OR option_name LIKE '\\_transient\\_timeout\\_gpc\\_connector\\_notice\\_%'"
        );
    }

    /**
     * Settings flag this after the scanner is switched on or off, because that
     * adds or removes the /backlinks/ rewrite rule.
     */
    public static function maybe_flush_rewrite_rules()
    {
        if (get_option('gpc_connector_flush_rewrite')) {
            delete_option('gpc_connector_flush_rewrite');
            flush_rewrite_rules();
        }
    }

    public static function log($message)
    {
        error_log('[GPC Connector] ' . $message);
    }
}

register_activation_hook(__FILE__, ['GPC_Connector', 'activate']);
register_deactivation_hook(__FILE__, ['GPC_Connector', 'deactivate']);
add_action('plugins_loaded', ['GPC_Connector', 'boot']);
