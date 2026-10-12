<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Keeps all configuration in one option and renders
 * Settings → GPC Connector.
 */
final class GPC_Connector_Settings
{
    public const OPTION = 'gpc_connector_settings';
    public const PAGE   = 'gpc-connector';

    /** Names of the standalone plugins this one replaces. */
    public const LEGACY_PLUGINS = [
        'Backlink Checker API',
        'Backlink Scanner',
        'Blog Link Extractor API',
        'My Post API',
        'Rightee Backlink Tracker',
        'Published Blog Notification',
        'Rightee Backlink Remover',
    ];

    private static $cache = null;

    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_init', [__CLASS__, 'register']);
        add_action('admin_notices', [__CLASS__, 'legacy_plugins_notice']);
        add_filter('plugin_action_links_' . plugin_basename(GPC_CONNECTOR_FILE), [__CLASS__, 'action_links']);
    }

    public static function defaults()
    {
        return [
            'features'       => array_fill_keys(array_keys(GPC_Connector::features()), 1),
            // Secret shared with smart_gateway. The CRM front end has it as VITE_SMARTGATEWAY_SECRET_KEY.
            'gateway_secret' => '',
            'gateway_source' => 'claude-mcp',
            // The same CRMs and routing global_utilsfinal.php used: backlinks go to
            // every enabled CRM, published blogs only to the Outright CRM.
            'crms'           => [
                [
                    'name'     => 'outright',
                    'endpoint' => 'https://crm.outrightsystems.org/index.php?entryPoint=smart_gateway',
                    'enabled'  => 1,
                    'modules'  => ['outr_seo_backlinksmain', 'outr_published_blogs'],
                ],
                [
                    'name'     => 'sales',
                    'endpoint' => 'https://sales.guestpostcrm.com/index.php?entryPoint=smart_gateway',
                    'enabled'  => 1,
                    'modules'  => ['outr_seo_backlinksmain'],
                ],
                [
                    'name'     => 'kartikey',
                    'endpoint' => 'https://kartikey.guestpostcrm.com/index.php?entryPoint=smart_gateway',
                    'enabled'  => 0,
                    'modules'  => ['outr_seo_backlinksmain'],
                ],
                [
                    'name'     => 'testcrm',
                    'endpoint' => 'https://testcrm.guestpostcrm.com/index.php?entryPoint=smart_gateway',
                    'enabled'  => 1,
                    'modules'  => ['outr_seo_backlinksmain'],
                ],
                [
                    'name'     => 'anshik',
                    'endpoint' => 'https://anshik.guestpostcrm.com/index.php?entryPoint=smart_gateway',
                    'enabled'  => 0,
                    'modules'  => ['outr_seo_backlinksmain'],
                ],
            ],
            // The keys the CRM already sends to the old plugins, so existing calls keep working.
            'api_keys'       => [
                'post_api'         => 'YOUR_SECRET_EXTRACT_HERE',
                'backlink_remover' => 'rightee_7f3b9c92a1d84e6db91a42f0',
                'backlink_checker' => '0bb4a8eb4f236cdc76ea2cc856d467d5',
                'link_extractor'   => '9lEMZM1wd9vBtoYDxpoYQRdTDPmwCBOClDiCxj06',
            ],
        ];
    }

    /** CRM modules a CRM row can be routed, as module => label. */
    public static function modules()
    {
        return [
            GPC_Connector_Backlink_Sync::MODULE       => 'Backlinks',
            GPC_Connector_Published_Blog_Sync::MODULE => 'Published blogs',
        ];
    }

    /** Where each inbound key is used, for the settings page. */
    public static function api_key_fields()
    {
        return [
            'post_api'         => [
                'label'  => 'Post API',
                'header' => 'X-API-Key',
                'routes' => ['my-api/v1/create-post', 'my-api/v1/post-status', 'my-api/v1/link-status', 'my-api/v1/anchor-occurrences'],
            ],
            'backlink_remover' => [
                'label'  => 'Backlink remover',
                'header' => 'X-API-Key',
                'routes' => ['rightee/v1/remove-backlink'],
            ],
            'backlink_checker' => [
                'label'  => 'Backlink checker',
                'header' => 'X-API-Key',
                'routes' => ['backlink-checker/v1/check'],
            ],
            'link_extractor'   => [
                'label'  => 'Blog link extractor',
                'header' => 'X-BLE-API-Key (or the api_key query parameter)',
                'routes' => ['blog-link-extractor/v2/links'],
            ],
        ];
    }

    public static function all()
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $saved    = get_option(self::OPTION, []);
        $saved    = is_array($saved) ? $saved : [];
        $defaults = self::defaults();

        self::$cache = [
            'features'       => array_merge($defaults['features'], (array) ($saved['features'] ?? [])),
            'gateway_secret' => (string) ($saved['gateway_secret'] ?? $defaults['gateway_secret']),
            'gateway_source' => (string) ($saved['gateway_source'] ?? $defaults['gateway_source']),
            'crms'           => isset($saved['crms']) && is_array($saved['crms']) ? $saved['crms'] : $defaults['crms'],
            'api_keys'       => array_merge($defaults['api_keys'], (array) ($saved['api_keys'] ?? [])),
        ];

        return self::$cache;
    }

    public static function feature_enabled($key)
    {
        return !empty(self::all()['features'][$key]);
    }

    public static function api_key($key)
    {
        $keys = self::all()['api_keys'];
        return isset($keys[$key]) ? (string) $keys[$key] : '';
    }

    public static function gateway_secret()
    {
        if (defined('GPC_CONNECTOR_GATEWAY_SECRET')) {
            return (string) GPC_CONNECTOR_GATEWAY_SECRET;
        }

        return self::all()['gateway_secret'];
    }

    public static function gateway_source()
    {
        return self::all()['gateway_source'];
    }

    /**
     * Enabled CRMs routed to receive $module, as name => smart_gateway URL.
     */
    public static function crm_endpoints_for($module)
    {
        $endpoints = [];

        foreach (self::all()['crms'] as $crm) {
            if (empty($crm['enabled']) || !in_array($module, (array) ($crm['modules'] ?? []), true)) {
                continue;
            }

            if (!self::is_valid_endpoint($crm['endpoint'] ?? '')) {
                GPC_Connector::log("Skipping invalid CRM endpoint '{$crm['name']}'.");
                continue;
            }

            $endpoints[$crm['name']] = $crm['endpoint'];
        }

        return $endpoints;
    }

    public static function is_valid_endpoint($url)
    {
        $url    = trim((string) $url);
        $host   = (string) wp_parse_url($url, PHP_URL_HOST);
        $scheme = strtolower((string) wp_parse_url($url, PHP_URL_SCHEME));

        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && $host !== ''
            && strpos($host, '.') !== 0
            && in_array($scheme, ['http', 'https'], true);
    }

    public static function add_menu()
    {
        add_options_page(
            'GPC Connector',
            'GPC Connector',
            'manage_options',
            self::PAGE,
            [__CLASS__, 'render_page']
        );
    }

    public static function register()
    {
        register_setting(self::OPTION, self::OPTION, [
            'type'              => 'array',
            'sanitize_callback' => [__CLASS__, 'sanitize'],
            'default'           => [],
        ]);
    }

    public static function action_links($links)
    {
        array_unshift($links, '<a href="' . esc_url(admin_url('options-general.php?page=' . self::PAGE)) . '">Settings</a>');
        return $links;
    }

    public static function sanitize($input)
    {
        $input    = is_array($input) ? $input : [];
        $current  = self::all();
        $defaults = self::defaults();
        $clean    = [
            'features' => [],
            'crms'     => [],
            'api_keys' => [],
        ];

        foreach (array_keys(GPC_Connector::features()) as $key) {
            $clean['features'][$key] = empty($input['features'][$key]) ? 0 : 1;
        }

        // Password fields are never pre-filled, so a blank value keeps the saved secret.
        $secret = trim((string) ($input['gateway_secret'] ?? ''));
        $clean['gateway_secret'] = $secret !== '' ? $secret : $current['gateway_secret'];

        $source = sanitize_text_field($input['gateway_source'] ?? '');
        $clean['gateway_source'] = $source !== '' ? $source : $defaults['gateway_source'];

        foreach ((array) ($input['crms'] ?? []) as $row) {
            $endpoint = esc_url_raw(trim((string) ($row['endpoint'] ?? '')));
            if ($endpoint === '') {
                continue;
            }

            // Names identify CRMs in logs and results, so keep them unique.
            $base = sanitize_key($row['name'] ?? '');
            $base = $base !== '' ? $base : 'crm';
            $name = $base;
            for ($n = 2; in_array($name, array_column($clean['crms'], 'name'), true); $n++) {
                $name = $base . '_' . $n;
            }

            $clean['crms'][] = [
                'name'     => $name,
                'endpoint' => $endpoint,
                'enabled'  => empty($row['enabled']) ? 0 : 1,
                'modules'  => array_values(array_intersect(array_keys(self::modules()), (array) ($row['modules'] ?? []))),
            ];
        }

        foreach (array_keys($defaults['api_keys']) as $key) {
            $clean['api_keys'][$key] = sanitize_text_field($input['api_keys'][$key] ?? '');
        }

        if (!empty($current['features']['backlink_scanner']) !== !empty($clean['features']['backlink_scanner'])) {
            update_option('gpc_connector_flush_rewrite', 1);
        }

        return $clean;
    }

    public static function active_legacy_plugins()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $active = [];
        foreach (get_plugins() as $file => $data) {
            if (in_array($data['Name'], self::LEGACY_PLUGINS, true) && is_plugin_active($file)) {
                $active[$file] = $data['Name'];
            }
        }

        return $active;
    }

    /**
     * Running an old plugin next to this one syncs every post twice, so point
     * that out on the Plugins screen and on the settings page.
     */
    public static function legacy_plugins_notice()
    {
        if (!current_user_can('activate_plugins') || !function_exists('get_current_screen')) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, ['plugins', 'settings_page_' . self::PAGE], true)) {
            return;
        }

        $active = self::active_legacy_plugins();
        if (!$active) {
            return;
        }
        ?>
<div class="notice notice-warning">
    <p><strong>GPC Connector</strong> replaces these plugins, which are still active. Deactivate them so posts
        are not synced twice:</p>
    <ul style="list-style: disc; margin-left: 20px;">
        <?php foreach ($active as $name) : ?>
        <li><?php echo esc_html($name); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
    }

    public static function render_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $test_results = null;
        if (isset($_POST['gpc_connector_test'])) {
            check_admin_referer('gpc_connector_test');
            $test_results = GPC_Connector_CRM_Client::test_connections();
        }

        $settings = self::all();
        $field    = self::OPTION;
        $crms     = array_values($settings['crms']);

        // Two blank rows for adding CRMs. Rows saved without a URL are dropped.
        $crms[] = ['name' => '', 'endpoint' => '', 'enabled' => 1, 'modules' => []];
        $crms[] = ['name' => '', 'endpoint' => '', 'enabled' => 1, 'modules' => []];
        ?>
<div class="wrap">
    <h1>GPC Connector</h1>

    <form method="post" action="options.php">
        <?php settings_fields(self::OPTION); ?>

        <h2>Features</h2>
        <p>Each feature replaces one of the old standalone plugins. Turn off anything this site does not use.</p>
        <table class="form-table" role="presentation">
            <?php foreach (GPC_Connector::features() as $key => $feature) : ?>
            <tr>
                <th scope="row">
                    <?php echo esc_html($feature['label']); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox"
                            name="<?php echo esc_attr("{$field}[features][{$key}]"); ?>"
                            value="1"
                            <?php checked(!empty($settings['features'][$key])); ?>
                        />
                        Enabled
                    </label>
                    <p class="description">
                        <?php echo esc_html($feature['description']); ?>
                        Replaces
                        <em><?php echo esc_html($feature['replaces']); ?></em>.
                    </p>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>CRM connection</h2>
        <p>
            Backlinks and published blogs are written through each CRM's <code>smart_gateway</code> entry point.
            Every request carries an <code>X-Api-Token</code> header signed with the SmartGateway secret, which is
            the value of <code>VITE_SMARTGATEWAY_SECRET_KEY</code> in the CRM front end.
        </p>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="gpc-gateway-secret">Signing secret</label></th>
                <td>
                    <?php if (defined('GPC_CONNECTOR_GATEWAY_SECRET')) : ?>
                    <p>Set in <code>wp-config.php</code> with <code>GPC_CONNECTOR_GATEWAY_SECRET</code>.</p>
                    <?php else : ?>
                    <input type="password" id="gpc-gateway-secret" class="regular-text" autocomplete="new-password"
                        name="<?php echo esc_attr("{$field}[gateway_secret]"); ?>"
                        value=""
                        placeholder="<?php echo $settings['gateway_secret'] !== '' ? esc_attr('Saved. Leave blank to keep it.') : ''; ?>" />
                    <p class="description">
                        <?php echo $settings['gateway_secret'] === '' ? '<strong>Not set. Nothing is sent to the CRM until it is.</strong> ' : ''; ?>
                        You can also define <code>GPC_CONNECTOR_GATEWAY_SECRET</code> in <code>wp-config.php</code>.
                    </p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="gpc-gateway-source">Token source</label></th>
                <td>
                    <input type="text" id="gpc-gateway-source" class="regular-text"
                        name="<?php echo esc_attr("{$field}[gateway_source]"); ?>"
                        value="<?php echo esc_attr($settings['gateway_source']); ?>" />
                    <p class="description">The <code>source</code> value inside the signed token.</p>
                </td>
            </tr>
        </table>

        <table class="widefat striped" style="max-width: 1100px;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>smart_gateway URL</th>
                    <th>Enabled</th>
                    <?php foreach (self::modules() as $label) : ?>
                    <th><?php echo esc_html($label); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($crms as $i => $crm) : ?>
                <?php $row = "{$field}[crms][{$i}]"; ?>
                <tr>
                    <td>
                        <input type="text"
                            name="<?php echo esc_attr("{$row}[name]"); ?>"
                            value="<?php echo esc_attr($crm['name']); ?>" />
                    </td>
                    <td>
                        <input type="url" class="large-text"
                            name="<?php echo esc_attr("{$row}[endpoint]"); ?>"
                            value="<?php echo esc_attr($crm['endpoint']); ?>"
                            placeholder="https://crm.example.com/index.php?entryPoint=smart_gateway" />
                    </td>
                    <td>
                        <input type="checkbox"
                            name="<?php echo esc_attr("{$row}[enabled]"); ?>"
                            value="1"
                            <?php checked(!empty($crm['enabled'])); ?>
                        />
                    </td>
                    <?php foreach (self::modules() as $module => $label) : ?>
                    <td>
                        <input type="checkbox"
                            name="<?php echo esc_attr("{$row}[modules][]"); ?>"
                            value="<?php echo esc_attr($module); ?>"
                            <?php checked(in_array($module, (array) $crm['modules'], true)); ?>
                        />
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="description">Tick the modules each CRM should receive. Clear a URL to remove that CRM.</p>

        <h2>Inbound API keys</h2>
        <p>
            Keys the CRM sends when it calls this site. The defaults are the keys the old plugins used, so existing
            CRM calls keep working.
        </p>
        <table class="form-table" role="presentation">
            <?php foreach (self::api_key_fields() as $key => $info) : ?>
            <tr>
                <th scope="row">
                    <label
                        for="gpc-key-<?php echo esc_attr($key); ?>"><?php echo esc_html($info['label']); ?></label>
                </th>
                <td>
                    <input type="text"
                        id="gpc-key-<?php echo esc_attr($key); ?>"
                        class="regular-text code"
                        name="<?php echo esc_attr("{$field}[api_keys][{$key}]"); ?>"
                        value="<?php echo esc_attr($settings['api_keys'][$key]); ?>" />
                    <p class="description">
                        Sent in
                        <code><?php echo esc_html($info['header']); ?></code>
                        to:
                        <?php foreach ($info['routes'] as $route) : ?>
                        <br /><code><?php echo esc_html(rest_url($route)); ?></code>
                        <?php endforeach; ?>
                    </p>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php submit_button(); ?>
    </form>

    <h2>Test CRM connection</h2>
    <p>Reads one record from each routed module on every enabled CRM, using the saved settings.</p>
    <form method="post">
        <?php wp_nonce_field('gpc_connector_test'); ?>
        <?php submit_button('Run test', 'secondary', 'gpc_connector_test', false); ?>
    </form>

    <?php if ($test_results !== null) : ?>
    <?php if (!$test_results) : ?>
    <p>No enabled CRM has a module ticked.</p>
    <?php else : ?>
    <table class="widefat striped" style="max-width: 1100px; margin-top: 12px;">
        <thead>
            <tr>
                <th>CRM</th>
                <th>Module</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($test_results as $result) : ?>
            <tr>
                <td><?php echo esc_html($result['crm']); ?>
                </td>
                <td><code><?php echo esc_html($result['module']); ?></code>
                </td>
                <td
                    style="color: <?php echo $result['ok'] ? '#008a20' : '#d63638'; ?>;">
                    <?php echo $result['ok'] ? '✓ ' : '✗ '; ?><?php echo esc_html($result['message']); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php
    }
}
?>