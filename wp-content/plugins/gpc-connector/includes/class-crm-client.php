<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Talks to the CRMs through their smart_gateway entry point
 * (index.php?entryPoint=smart_gateway). Replaces get_set_blog() and
 * outright_do_curl() from /web/html/global_utilsfinal.php, which used the
 * older get_post_all entry point.
 */
final class GPC_Connector_CRM_Client
{
    const TIMEOUT = 15;

    /**
     * CRMs that should receive $module, as name => endpoint, or a WP_Error
     * when nothing can be sent.
     */
    public static function targets($module)
    {
        if (GPC_Connector_Settings::gateway_secret() === '') {
            return self::missing_secret_error();
        }

        $endpoints = GPC_Connector_Settings::crm_endpoints_for($module);
        if (!$endpoints) {
            return new WP_Error('gpc_no_crm', "No enabled CRM is set to receive {$module}.");
        }

        return $endpoints;
    }

    /**
     * Creates the record, or updates the newest one whose $match_field equals
     * $match_value. Returns ['operation' => 'create'|'update', 'id' => string]
     * or a WP_Error.
     */
    public static function upsert($endpoint, $module, $match_field, $match_value, array $data)
    {
        $existing_id = self::find_id($endpoint, $module, $match_field, $match_value);
        if (is_wp_error($existing_id)) {
            return $existing_id;
        }

        $data[$match_field] = $match_value;
        $payload = [
            'module' => $module,
            'data'   => $data,
        ];

        if ($existing_id !== '') {
            $payload['action'] = 'update';
            $payload['id']     = $existing_id;
        } else {
            $payload['action'] = 'create';
        }

        $body = self::request($endpoint, $payload);
        if (is_wp_error($body)) {
            return $body;
        }

        return [
            'operation' => $payload['action'],
            'id'        => (string) ($body['id'] ?? $existing_id),
        ];
    }

    /**
     * ID of the newest record whose $field equals $value, '' when there is
     * none, or a WP_Error when the CRM could not be asked.
     */
    public static function find_id($endpoint, $module, $field, $value)
    {
        $body = self::request($endpoint, [
            'action'    => 'fetch',
            'module'    => $module,
            'fields'    => [$field],
            'filters'   => [$field => $value],
            'order_by'  => 'date_entered',
            'order_dir' => 'DESC',
            'page'      => 1,
            'per_page'  => 1,
        ]);

        if (is_wp_error($body)) {
            return $body;
        }

        $record = $body['records'][0] ?? null;

        // fetch skips filters on fields the module doesn't have and would hand
        // back the newest record instead, so confirm it really is a match
        // before overwriting it.
        if (
            !is_array($record) ||
            empty($record['id']) ||
            !isset($record[$field]) ||
            strcasecmp(trim((string) $record[$field]), trim((string) $value)) !== 0
        ) {
            return '';
        }

        return (string) $record['id'];
    }

    /**
     * Sends one signed request. Returns the decoded body on success, or a
     * WP_Error. Network failures use the code gpc_gateway_unreachable so
     * callers can stop retrying a CRM that is down.
     */
    public static function request($endpoint, array $payload)
    {
        $secret = GPC_Connector_Settings::gateway_secret();
        if ($secret === '') {
            return self::missing_secret_error();
        }

        // smart_gateway reads order_by in its constructor, so every call sends it.
        $payload += ['order_by' => ''];

        $response = wp_remote_post($endpoint, [
            'timeout' => self::TIMEOUT,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'X-Api-Token'  => self::token($secret),
            ],
            'body'    => wp_json_encode($payload),
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('gpc_gateway_unreachable', $response->get_error_message());
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        $raw    = (string) wp_remote_retrieve_body($response);
        $body   = json_decode($raw, true);

        // smart_gateway answers 200 even when nothing was written, so the body
        // decides success, not the status code.
        if (is_array($body) && !empty($body['success'])) {
            return $body;
        }

        $message = '';
        if (is_array($body)) {
            foreach (['error', 'message'] as $key) {
                if (!empty($body[$key]) && is_string($body[$key])) {
                    $message = $body[$key];
                    break;
                }
            }
        }

        if ($message === '') {
            $message = $raw === ''
                ? "Empty response (HTTP {$status})."
                : "Unexpected response (HTTP {$status}): " . mb_substr(trim(wp_strip_all_tags($raw)), 0, 200);
        }

        return new WP_Error('gpc_gateway_error', $message, ['status' => $status]);
    }

    /**
     * Reads one record from every module on every enabled CRM so the settings
     * page can show whether the URL, secret and module names are right.
     */
    public static function test_connections()
    {
        $results = [];

        foreach (GPC_Connector_Settings::all()['crms'] as $crm) {
            if (empty($crm['enabled'])) {
                continue;
            }

            foreach ((array) $crm['modules'] as $module) {
                $body = GPC_Connector_Settings::is_valid_endpoint($crm['endpoint'])
                    ? self::request($crm['endpoint'], [
                        'action'   => 'fetch',
                        'module'   => $module,
                        'fields'   => ['name'],
                        'page'     => 1,
                        'per_page' => 1,
                    ])
                    : new WP_Error('gpc_invalid_endpoint', 'The smart_gateway URL is not a valid HTTP(S) URL.');

                $results[] = [
                    'crm'     => $crm['name'],
                    'module'  => $module,
                    'ok'      => !is_wp_error($body),
                    'message' => is_wp_error($body)
                        ? $body->get_error_message()
                        : sprintf('Connected. %d record(s) in this module.', (int) ($body['total'] ?? 0)),
                ];
            }
        }

        return $results;
    }

    /**
     * The X-Api-Token the CRM front end sends: base64("<json>||<signature>"),
     * where the signature is the hex HMAC-SHA256 of the JSON keyed with the
     * shared SmartGateway secret.
     */
    private static function token($secret)
    {
        $json = wp_json_encode([
            'ts'     => time(),
            'source' => GPC_Connector_Settings::gateway_source(),
        ]);

        return base64_encode($json . '||' . hash_hmac('sha256', $json, $secret));
    }

    private static function missing_secret_error()
    {
        return new WP_Error(
            'gpc_gateway_secret_missing',
            'The smart_gateway signing secret is not set (Settings → GPC Connector).'
        );
    }
}
