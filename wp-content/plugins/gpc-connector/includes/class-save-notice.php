<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The popup shown after saving or publishing a post. It reports what the
 * backlink sync and the published blog sync sent to the CRM.
 */
final class GPC_Connector_Save_Notice
{
    public static function init()
    {
        add_action('admin_notices', [__CLASS__, 'render']);
    }

    /**
     * Stores one section ('backlinks' or 'published') for the popup on the
     * next admin page load.
     */
    public static function add($section, array $data)
    {
        $key    = self::key();
        $notice = get_transient($key);
        $notice = is_array($notice) ? $notice : [];

        $notice[$section] = $data;
        set_transient($key, $notice, 30);
    }

    private static function key()
    {
        return 'gpc_connector_notice_' . get_current_user_id();
    }

    public static function render()
    {
        $key    = self::key();
        $notice = get_transient($key);

        if (!is_array($notice) || !$notice) {
            return;
        }

        // Show it once.
        delete_transient($key);

        $backlinks  = $notice['backlinks'] ?? null;
        $published  = $notice['published'] ?? null;
        $post_title = $published['post_title'] ?? ($backlinks['post_title'] ?? '');

        if ($published) {
            $action = !empty($published['is_new_publish']) ? 'published' : 'updated';
        } else {
            $action = !empty($backlinks['is_update']) ? 'updated' : 'saved';
        }

        // Each line is [css modifier, text].
        $lines = [];

        if ($backlinks) {
            if ($backlinks['processed'] > 0 && empty($backlinks['errors'])) {
                $lines[] = ['ok', "{$backlinks['processed']} new link(s) sent to backlink system."];
            } elseif ($backlinks['processed'] > 0) {
                $lines[] = ['error', "{$backlinks['synced']} of {$backlinks['processed']} new link(s) sent to backlink system."];
                foreach ($backlinks['errors'] as $error) {
                    $lines[] = ['error', $error];
                }
            }

            if ($backlinks['excluded'] > 0) {
                $lines[] = ['warn', "{$backlinks['excluded']} new link(s) excluded from blacklisted domains."];
            }
        }

        if ($published) {
            if (empty($published['errors'])) {
                $lines[] = ['ok', 'Blog details successfully sent to published blogs system.'];
            } else {
                foreach ($published['errors'] as $error) {
                    $lines[] = ['error', 'Failed to send blog details: ' . $error];
                }
            }
        }

        $failed = in_array('error', array_column($lines, 0), true);
        ?>
<div id="gpc-save-popup" class="gpc-save-popup">
    <div class="gpc-save-popup__content">
        <span class="gpc-save-popup__close" data-gpc-close>&times;</span>
        <div class="gpc-save-popup__icon <?php echo $failed ? 'is-failed' : ''; ?>"><?php echo $failed ? '⚠' : '✓'; ?></div>
        <h3><?php echo $failed ? 'Warning!' : 'Success!'; ?></h3>
        <p>
            Your blog post "<strong><?php echo esc_html($post_title); ?></strong>"
            has been <?php echo esc_html($action); ?> successfully!
        </p>
        <?php foreach ($lines as $line) : ?>
        <p class="gpc-save-popup__line is-<?php echo esc_attr($line[0]); ?>"><small><?php echo esc_html($line[1]); ?></small></p>
        <?php endforeach; ?>
        <button type="button" class="gpc-save-popup__button" data-gpc-close>OK</button>
    </div>
</div>

<style>
    .gpc-save-popup {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100000;
    }

    .gpc-save-popup__content {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        position: relative;
        max-width: 450px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        animation: gpcSavePopupIn 0.3s ease-in-out;
    }

    @keyframes gpcSavePopupIn {
        from {
            opacity: 0;
            transform: scale(0.7);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .gpc-save-popup__close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #999;
    }

    .gpc-save-popup__close:hover {
        color: #333;
    }

    .gpc-save-popup__icon {
        font-size: 48px;
        color: #4CAF50;
        margin-bottom: 15px;
    }

    .gpc-save-popup__icon.is-failed {
        color: #ff9800;
    }

    .gpc-save-popup__content h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 24px;
    }

    .gpc-save-popup__content p {
        margin: 0 0 12px 0;
        color: #666;
        line-height: 1.5;
    }

    .gpc-save-popup__line {
        font-weight: bold;
    }

    .gpc-save-popup__line.is-ok {
        color: #4CAF50 !important;
    }

    .gpc-save-popup__line.is-warn {
        color: #ff9800 !important;
    }

    .gpc-save-popup__line.is-error {
        color: #f44336 !important;
    }

    .gpc-save-popup__button {
        margin-top: 8px;
        background: #0073aa;
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .gpc-save-popup__button:hover {
        background: #005177;
    }
</style>

<script>
    (function() {
        var popup = document.getElementById('gpc-save-popup');
        if (!popup) {
            return;
        }

        function close() {
            popup.style.display = 'none';
        }

        popup.addEventListener('click', function(event) {
            if (event.target === popup || event.target.hasAttribute('data-gpc-close')) {
                close();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                close();
            }
        });

        setTimeout(close, <?php echo $failed ? 12000 : 6000; ?>);
    })();
</script>
<?php
    }
}
