<?php
/**
 * Plugin Name: Auto Relevant Blog
 * Description: Suggests relevant blog URLs via GPT & RSS, opens in a popup. Category-aware; uses Azure OpenAI Chat Completions (hardcoded).
 * Version: 1.4
 * Author: kjl
 */

if (!defined('ABSPATH')) exit;

/**
 * -----------------------------
 * CONFIG (hardcoded)
 * -----------------------------
 * Replace values here if needed.
 */
$ARB_API_KEY = '5f155d5a75f54c17bfa9b947a8e000df';
$ARB_API_URL = 'https://outright-openai-1.openai.azure.com/openai/deployments/chatgpt-40/chat/completions?api-version=2024-02-15-preview';
$ARB_FEEDS = [
    'https://www.outrightcrm.com/feed/',
    'https://www.outrightsystems.com/feed/'
];
/**
 * End Config
 * -----------------------------
 */

/* ------------------------------
   Enqueue scripts & styles
   ------------------------------ */
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        $base = plugin_dir_url(__FILE__);
        // enqueue user's popup.js & css — make sure popup.js exists in plugin folder
        wp_enqueue_script('auto-relevant-js', $base . 'popup.js', ['jquery'], filemtime(plugin_dir_path(__FILE__) . 'popup.js'), true);
        wp_enqueue_style('auto-relevant-css', $base . 'popup.css', [], filemtime(plugin_dir_path(__FILE__) . 'popup.css'));

        $post_id = get_the_ID();
        if (!$post_id && isset($_GET['post'])) $post_id = intval($_GET['post']);

        wp_localize_script('auto-relevant-js', 'ARB', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('arb_nonce'),
            'post_id'  => $post_id,
        ]);
    }
});

/* ------------------------------
   Add Floating Button + Modal
   ------------------------------ */
add_action('admin_footer-post.php', 'arb_add_popup_button');
add_action('admin_footer-post-new.php', 'arb_add_popup_button');
function arb_add_popup_button() {
    // keep markup identical to your earlier structure so popup.js works unchanged
    ?>
    <button id="showRelevantUrls" class="arb-floating-btn" style="position:fixed; right:18px; bottom:80px; z-index:99999; padding:5px 7px; border-radius:23px; background: #650aef; color:#fff; border:none; width: 43px;
    height: 41px;
">
        📎 
    </button>

    <div id="arbModal" class="arb-modal" style="display:none; position:fixed; inset:0; z-index:99998; background:rgba(0,0,0,0.6);">
        <div class="arb-modal-content" style="width:920px; max-width:96%; margin:40px auto; background:#fff; border-radius:12px; padding:18px; position:relative;">
            <span class="arb-close" style="position:absolute; right:14px; top:10px; cursor:pointer; font-size:24px;">&times;</span>
           

<script>
document.querySelector('.arb-close').addEventListener('click', function () {
    // If the close button is inside a specific container, hide that container
    this.parentElement.style.display = 'none';
});
</script>


            <div id="arbContentAnalysis" class="arb-content-analysis">
                <h3>Content Analysis</h3>
                <div class="analysis-grid">
                    <div class="analysis-item">
                        <h4>Word Count</h4>
                        <div id="arbWordCount" class="analysis-value">-</div>
                    </div>
                    <div class="analysis-item">
                        <h4>Readability</h4>
                        <div id="arbReadability" class="analysis-value">-</div>
                    </div>
                    <div class="analysis-item">
                        <h4>SEO Score</h4>
                        <div id="arbSeoScore" class="analysis-value">-</div>
                    </div>
                    <div class="analysis-item">
                        <h4>Key Topics</h4>
                        <div id="arbKeyTopics" class="analysis-value">-</div>
                    </div>
                </div>
                <!-- <div class="analysis-recommendations">
                    <h4>SEO Recommendations</h4>
                    <ul id="arbSeoRecommendations"></ul>
                </div> -->
                <div class="analysis-item" style="margin-top:15px;">
                    <h4>SEO Keywords</h4>
                    <ul id="arbSeoKeywords" style="list-style:none; padding:0;"></ul>
                </div>
            </div>
            <div class="analysis-item">
    <h4>Human Tone</h4>
    <div id="arbHumanTone" class="analysis-value">-</div>
</div>


            <div id="arbContent" style="margin-top:16px;"><p>Loading relevant blogs...</p></div>

            <div style="margin-top:12px; display:flex; gap:8px; align-items:center;">
                <button id="arbSaveAnchors" class="arb-save-btn button button-primary" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">💾 Save Anchors</button>
                <span id="arbStatus" style="margin-left:8px;"></span>
            </div>
        </div>
    </div>
    
    <?php
}

/* ------------------------------
   AJAX: Fetch relevant URLs + analysis (uses Azure OpenAI)
   ------------------------------ */
add_action('wp_ajax_fetch_relevant_urls', 'arb_fetch_relevant_urls');
function arb_fetch_relevant_urls() {
    // Access global config
    global $ARB_API_KEY, $ARB_API_URL, $ARB_FEEDS;

    check_ajax_referer('arb_nonce', 'nonce');
    
    $post_id = intval($_POST['post_id'] ?? 0);
    if (!$post_id) wp_send_json_error('Missing post ID');

    $post = get_post($post_id);
    if (!$post) wp_send_json_error('Post not found');

    $content = trim(strip_tags($post->post_content));
    
    $title = get_the_title($post_id);

    // 1) fetch external posts list (with categories if available)
    $external_posts = arb_fetch_external_blog_data($ARB_FEEDS);

    // 2) try to match category-wise: get current post categories
    $post_cats = wp_get_post_terms($post_id, 'category', ['fields' => 'names']);
    $post_cats_lower = array_map('strtolower', $post_cats);

    // build candidate list: prefer matching categories, but include others as fallback
    $candidates = [];
    foreach ($external_posts as $ext) {
        $ext_cats = array_map('strtolower', array_map('trim', (array)($ext['categories'] ?? [])));
        $priority = 0;
        if (!empty($post_cats_lower) && count(array_intersect($post_cats_lower, $ext_cats)) > 0) $priority = 1;
        $candidates[] = array_merge($ext, ['priority' => $priority]);
    }
    // sort by priority desc
    usort($candidates, function($a,$b){ return ($b['priority'] <=> $a['priority']); });
    $candidates = array_slice($candidates, 0, 12); // limit

    // fetch excerpts / main content for each candidate
    foreach ($candidates as &$c) {
        $c['excerpt_text'] = arb_fetch_url_content_excerpt($c['link'], 1200);
    }
    unset($c);

    // 3) run local analysis
    $analysis = arb_analyze_blog_content($post->post_content);
    $human_tone = arb_detect_human_tone($title, $post->post_content);

    // 4) build prompt for GPT — enforce strict JSON output
    $candidate_payload = [];
    foreach ($candidates as $c) {
        $candidate_payload[] = [
            'title' => mb_substr($c['title'] ?? '', 0, 300),
            'url' => $c['link'] ?? '',
            'categories' => $c['categories'] ?? [],
            'excerpt' => mb_substr($c['excerpt_text'] ?? '', 0, 1000)
        ];
    }

    $system = "You are an SEO assistant. Given a blog post and a list of candidate external blog posts, return STRICT JSON ONLY with this shape:\n\n".
              "{\n  \"matches\": [ { \"title\":\"...\", \"url\":\"...\", \"anchor_text\":\"...\", \"anchor_exists\": true|false, \"relevance\": 0-100, \"note\":\"...\" } ],\n  \"seo_keywords\": [ \"keyword phrase 1\", \"keyword phrase 2\", ... ]\n}\n\nRules:\n- Return up to 6 matches sorted by relevance.\n- anchor_text should be a short phrase (1-4 words) preferably present verbatim in the current post. If not present, still provide a natural short anchor and set anchor_exists=false.\n- relevance is an integer 0-100.\n- The seo_keywords array should contain up to 12 suggested SEO keyword phrases relevant to the current post (2-4 words each).\n- DO NOT return any commentary, only JSON.";

    $user = "Current post title:\n" . $title . "\n\nCurrent post content (truncated to 3500 chars):\n" . arb_truncate_for_prompt($content, 3500) . "\n\nCandidates (title | url | categories | excerpt):\n" . json_encode($candidate_payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $messages = [
        ['role' => 'system', 'content' => $system],
        ['role' => 'user', 'content' => $user]
    ];

    $gpt_raw = arb_call_chatgpt($messages, 900);
    $gpt_ok = $gpt_raw['ok'] ?? false;
    $gpt_content = $gpt_raw['content'] ?? '';

    $matches = [];
    $seo_keywords = [];

    if ($gpt_ok && !empty($gpt_content)) {
        $parsed = arb_extract_json_blob($gpt_content);
        if (!empty($parsed) && (isset($parsed['matches']) || isset($parsed[0]))) {
            // compat: support both object and array top-level
            if (isset($parsed['matches'])) {
                $matches = $parsed['matches'];
                $seo_keywords = $parsed['seo_keywords'] ?? [];
            } elseif (is_array($parsed) && isset($parsed[0]) && isset($parsed[0]['matches'])) {
                // sometimes the assistant nests results
                $matches = $parsed[0]['matches'];
                $seo_keywords = $parsed[0]['seo_keywords'] ?? [];
            } else {
                // fallback to trying to map if parsed is matches array directly
                if (array_key_exists('title', $parsed[0] ?? [])) {
                    $matches = $parsed;
                }
            }
        }
    }

    // fallback: if GPT failed, use simple matching
    if (empty($matches)) {
        $fallback = [];
        foreach ($candidates as $c) {
            $w = arb_find_matching_word_in_content($post->post_content, $c['title']);
            if ($w !== 'Not found') {
                $fallback[] = [
                    'title' => $c['title'],
                    'url' => $c['link'],
                    'anchor_text' => $w,
                    'anchor_exists' => true,
                    'relevance' => 55,
                    'note' => 'Fallback exact match'
                ];
            }
            if (count($fallback) >= 6) break;
        }
        $matches = $fallback;
        // simple SEO keywords from local analysis
        $seo_keywords = array_slice($analysis['key_topics'] ?? [], 0, 12);
    }

    // Get saved custom anchors from post meta
    $saved_custom_anchors = get_post_meta($post_id, '_arb_custom_anchors', true);
    if (!is_array($saved_custom_anchors)) {
        $saved_custom_anchors = [];
    }

    // 5) Build HTML output used by popup.js — keep markup consistent with your JS expectations
    ob_start();

    // insert a small script to populate analysis numbers
    ?>
    <script>
    jQuery(document).ready(function($){
        $("#arbWordCount").text("<?php echo esc_js($analysis['word_count']); ?>");
        $("#arbReadability").text("<?php echo esc_js($analysis['readability']); ?>");
        $("#arbSeoScore").text("<?php echo esc_js($analysis['seo_score']); ?>/10");
        $("#arbKeyTopics").html("<?php echo esc_js(implode(', ', $analysis['key_topics'])); ?>");
        $("#arbHumanTone").text("<?php echo esc_js($human_tone); ?>");

        var recList = $("#arbSeoRecommendations");
        recList.empty();
        <?php foreach ($analysis['seo_recommendations'] as $rec) : ?>
            recList.append("<li><?php echo esc_js($rec); ?></li>");
        <?php endforeach; ?>

        // populate SEO keywords (as tags)
        var kw = $('#arbSeoKeywords');
        kw.empty();
        <?php foreach ($seo_keywords as $k): ?>
            kw.append('<li><?php echo esc_js($k); ?></li>');
        <?php endforeach; ?>

        // Handle custom anchor addition
        $('#addCustomAnchor').on('click', function() {
            var anchorText = $('#customAnchorText').val().trim();
            var anchorUrl = $('#customAnchorUrl').val().trim();
            var errorEl = $('#customAnchorError');
            
            // Reset error
            errorEl.hide().text('');
            
            // Validation
            if (!anchorText) {
                errorEl.text('Please enter anchor text').show();
                return;
            }
            
            if (!anchorUrl) {
                errorEl.text('Please enter target URL').show();
                return;
            }
            
            // Check if URL is valid
            if (!/^https?:\/\//i.test(anchorUrl)) {
                errorEl.text('Please enter a valid URL starting with http:// or https://').show();
                return;
            }
            
            // Check if anchor exists in content (simplified check)
            var postContent = '<?php echo esc_js(wp_strip_all_tags($post->post_content)); ?>';
            if (postContent.indexOf(anchorText) === -1) {
                if (!confirm('This text was not found in your content. Are you sure you want to add it anyway?')) {
                    return;
                }
            }
            
            // Add to custom anchors list
          var customAnchorItem = $(
    '<div class="anchor-suggestion-item" data-word="' + anchorText + '" style="margin-bottom:12px;padding:8px;border-radius:6px;border:1px solid #d1e5ff;background:#f0f7ff;">' +
        '<div class="external-blog-title" style="font-weight:600;">' +
            '<a href="' + anchorUrl + '" target="_blank" rel="noopener noreferrer">Custom Link: ' + anchorText + '</a>' +
        '</div>' +
        '<div class="match-details" style="margin-top:6px; display:flex; gap:10px; align-items:center;">' +
            '<div style="flex:1;">' +
                '<span class="match-type" style="font-size:12px;color:#666;">Anchor Text:</span><br>' +
                '<input type="text" class="anchor-text" value="' + anchorText + '" style="width:100%; padding:6px; box-sizing:border-box; margin-top:6px;">' +
                '<input type="hidden" class="anchor-url" value="' + anchorUrl + '">' +
            '</div>' +
            '<div style="width:180px; font-size:12px; color:#666;">' +
                'Exists in content: <strong>' + (postContent.indexOf(anchorText) !== -1 ? 'Yes' : 'No') + '</strong><br>' +
                'Relevance: <strong>Custom</strong><br>' +
                '<span style="color:#999;">Manually added link</span>' +
            '</div>' +
        '</div>' +
        '<div class="custom-anchor-actions" style="margin-top:8px; text-align:right;">' +
            /* '<button class="button button-link delete-custom-anchor" style="color:#cc0000;">Delete</button>' + */
        '</div>' +
    '</div>'
);

            
            // Insert at the beginning of the container
            $('.anchor-suggestions-container').prepend(customAnchorItem);
            
            // Clear inputs
            $('#customAnchorText').val('');
            $('#customAnchorUrl').val('');
            
            // Handle delete button
            customAnchorItem.find('.delete-custom-anchor').on('click', function() {
                $(this).closest('.anchor-suggestion-item').remove();
            });
            
            // Save to database via AJAX
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_custom_anchor',
                    post_id: <?php echo $post_id; ?>,
                    anchor_text: anchorText,
                    anchor_url: anchorUrl,
                    nonce: '<?php echo wp_create_nonce('arb_nonce'); ?>'
                },
                success: function(response) {
                    if (!response.success) {
                        console.error('Error saving custom anchor:', response.data);
                    }
                }
            });
        });
        
        // Handle delete for existing custom anchors
        $('.delete-custom-anchor').on('click', function() {
            var item = $(this).closest('.anchor-suggestion-item');
            var anchorText = item.find('.anchor-text').val();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_custom_anchor',
                    post_id: <?php echo $post_id; ?>,
                    anchor_text: anchorText,
                    nonce: '<?php echo wp_create_nonce('arb_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        item.remove();
                    } else {
                        console.error('Error deleting custom anchor:', response.data);
                    }
                }
            });
        });
    });
    </script>
    <?php

    echo '<div class="anchor-suggestions-container">';

    // Display saved custom anchors first
    foreach ($saved_custom_anchors as $custom) {
        $anchor_exists = strpos($post->post_content, $custom['anchor_text']) !== false;
        ?>
        <div class="anchor-suggestion-item" data-word="<?php echo esc_attr($custom['anchor_text']); ?>" style="margin-bottom:12px;padding:8px;border-radius:6px;border:1px solid #d1e5ff;background:#f0f7ff;">
            <div class="external-blog-title" style="font-weight:600;">
                <a href="<?php echo esc_url($custom['url']); ?>" target="_blank" rel="noopener noreferrer">Custom Link: <?php echo esc_html($custom['anchor_text']); ?></a>
            </div>
            <div class="match-details" style="margin-top:6px; display:flex; gap:10px; align-items:center;">
                <div style="flex:1;">
                    <span class="match-type" style="font-size:12px;color:#666;">Anchor Text:</span><br>
                    <input type="text" class="anchor-text" value="<?php echo esc_attr($custom['anchor_text']); ?>" style="width:100%; padding:6px; box-sizing:border-box; margin-top:6px;">
                    <input type="hidden" class="anchor-url" value="<?php echo esc_url($custom['url']); ?>">
                </div>
                <div style="width:180px; font-size:12px; color:#666;">
                    Exists in content: <strong><?php echo $anchor_exists ? 'Yes' : 'No'; ?></strong><br>
                    Relevance: <strong>Custom</strong><br>
                    <span style="color:#999;">Manually added link</span>
                </div>
            </div>
            <!-- <div class="custom-anchor-actions" style="margin-top:8px; text-align:right;">
                <button class="button button-link delete-custom-anchor" style="color:#cc0000;">Delete</button>
            </div> -->
        </div>
        <?php
    }

    $found = 0;
    foreach ($matches as $m) {
        $anchor = isset($m['anchor_text']) ? $m['anchor_text'] : ($m['anchor'] ?? '');
        $anchor_exists = isset($m['anchor_exists']) && $m['anchor_exists'] ? true : false;
        $url = esc_url($m['url'] ?? ($m['link'] ?? ''));
        $title = esc_html($m['title'] ?? '');
        $note = esc_html($m['note'] ?? '');
        $category = esc_html($m['category'] ?? 'Uncategorized');

        if (empty($url) || empty($title)) continue;
        $found++;
        ?>
        <div class="anchor-suggestion-item" data-word="<?php echo esc_attr($anchor); ?>" style="margin-bottom:12px;padding:8px;border-radius:6px;border:1px solid #eee;">
            <div class="external-blog-title" style="font-weight:600;">
                <a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer"><?php echo $title; ?></a>
            </div>
            <div class="external-blog-category" style="font-size:12px;color:#888;margin-top:2px;">
                Category: <strong><?php echo $category; ?></strong>
            </div>

            <div class="match-details" style="margin-top:6px; display:flex; gap:10px; align-items:center;">
                <div style="flex:1;">
                    <span class="match-type" style="font-size:12px;color:#666;">Matched phrase:</span><br>
                    <input type="text" class="anchor-text" value="<?php echo esc_attr($anchor); ?>" style="width:100%; padding:6px; box-sizing:border-box; margin-top:6px;">
                    <input type="hidden" class="anchor-url" value="<?php echo $url; ?>">
                </div>
                <div style="width:180px; font-size:12px; color:#666;">
                    Exists in content: <strong><?php echo $anchor_exists ? 'Yes' : 'No'; ?></strong><br>
                    Relevance: <strong><?php echo intval($m['relevance'] ?? 0); ?></strong><br>
                    <span style="color:#999;"><?php echo $note; ?></span>
                </div>
            </div>
        </div>
        <?php
    }

    if ($found === 0 && empty($saved_custom_anchors)) {
        echo '<p>No relevant matches found for this content.</p>';
    }

    // Add custom anchor input section
    echo '<div class="custom-anchor-section" style="margin-top:20px; padding:15px; background:#f9f9f9; border-radius:6px;">';
    echo '<h4 style="margin-top:0;">Add Custom Anchor Link</h4>';
    echo '<div style="display:flex; gap:10px; align-items:center;">';
    echo '<div style="flex:1;">';
    echo '<label style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Anchor Text (should exist in your content)</label>';
    echo '<input type="text" id="customAnchorText" placeholder="Enter anchor text" style="width:100%; padding:8px; box-sizing:border-box;">';
    echo '</div>';
    echo '<div style="flex:1;">';
    echo '<label style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Target URL</label>';
    echo '<input type="text" id="customAnchorUrl" placeholder="https://example.com" style="width:100%; padding:8px; box-sizing:border-box;">';
    echo '</div>';
    echo '<div style="flex:0 0 auto;">';
    echo '<button id="addCustomAnchor" class="button button-primary" style="margin-top:16px;">Add Link</button>';
    echo '</div>';
    echo '</div>';
    echo '<div id="customAnchorError" style="color:#cc0000; font-size:12px; margin-top:8px; display:none;"></div>';
    echo '</div>'; // end custom-anchor-section

    echo '</div>'; // end anchor-suggestions-container

    $html = ob_get_clean();

    wp_send_json_success($html);
}

// Add AJAX handlers for saving/deleting custom anchors
add_action('wp_ajax_save_custom_anchor', 'arb_save_custom_anchor');
function arb_save_custom_anchor() {
    check_ajax_referer('arb_nonce', 'nonce');
    
    $post_id = intval($_POST['post_id'] ?? 0);
    $anchor_text = sanitize_text_field($_POST['anchor_text'] ?? '');
    $anchor_url = esc_url_raw($_POST['anchor_url'] ?? '');
    
    if (!$post_id || !$anchor_text || !$anchor_url) {
        wp_send_json_error('Missing required fields');
    }
    
    $saved_anchors = get_post_meta($post_id, '_arb_custom_anchors', true);
    if (!is_array($saved_anchors)) {
        $saved_anchors = [];
    }
    
    // Check if this anchor already exists
    $exists = false;
    foreach ($saved_anchors as &$anchor) {
        if ($anchor['anchor_text'] === $anchor_text) {
            $anchor['url'] = $anchor_url;
            $exists = true;
            break;
        }
    }
    
    if (!$exists) {
        $saved_anchors[] = [
            'anchor_text' => $anchor_text,
            'url' => $anchor_url
        ];
    }
    
    update_post_meta($post_id, '_arb_custom_anchors', $saved_anchors);
    wp_send_json_success();
}

add_action('wp_ajax_delete_custom_anchor', 'arb_delete_custom_anchor');
function arb_delete_custom_anchor() {
    check_ajax_referer('arb_nonce', 'nonce');
    
    $post_id = intval($_POST['post_id'] ?? 0);
    $anchor_text = sanitize_text_field($_POST['anchor_text'] ?? '');
    
    if (!$post_id || !$anchor_text) {
        wp_send_json_error('Missing required fields');
    }
    
    $saved_anchors = get_post_meta($post_id, '_arb_custom_anchors', true);
    if (!is_array($saved_anchors)) {
        wp_send_json_success();
    }
    
    // Filter out the anchor to be deleted
    $updated_anchors = array_filter($saved_anchors, function($anchor) use ($anchor_text) {
        return $anchor['anchor_text'] !== $anchor_text;
    });
    
    update_post_meta($post_id, '_arb_custom_anchors', array_values($updated_anchors));
    wp_send_json_success();
}
/* ------------------------------
   Save anchors to post (unchanged logic)
   ------------------------------ */

add_action('wp_ajax_save_anchors_to_post', 'arb_save_anchors_to_post');
function arb_save_anchors_to_post() {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized');
    }
    check_ajax_referer('arb_nonce', 'nonce');

    try {
        $post_id = intval($_POST['post_id'] ?? 0);
        $suggestions = $_POST['suggestions'] ?? [];

        if (!$post_id || empty($suggestions)) {
            throw new Exception('Invalid data');
        }

        $post = get_post($post_id);
        if (!$post) throw new Exception('Post not found');

        $content = $post->post_content;
        $replace_count = 0;
        $skipped_words = [];

        foreach ($suggestions as $suggestion) {
            $word = sanitize_text_field($suggestion['word'] ?? '');
            if (strtolower($word) === 'not found' || empty($word)) continue;

            foreach ($suggestion['urls'] as $entry) {
                $url = esc_url_raw($entry['url'] ?? '');
                $anchor = sanitize_text_field($entry['text'] ?? $word);
                if ($url && $anchor) {
                    $patterns = [
                        '/(?<!["\'>\/])\b(' . preg_quote($word, '/') . ')\b(?!["\'<])(?!\s*<\/a>)/iu',
                        '/(?<!["\'>\/])(' . preg_quote($word, '/') . ')(?=[,.!?]?)(?!["\'<])(?!\s*<\/a>)/iu',
                        '/(?<!\w)(' . preg_quote($word, '/') . ')(?!\w)/iu'
                    ];
                    $matched = false;
                    foreach ($patterns as $pattern) {
                        if (preg_match($pattern, $content) && !preg_match('/<a\b[^>]*>\s*' . preg_quote($word, '/') . '\s*<\/a>/iu', $content)) {
                            $anchor_tag = '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . $anchor . '</a>';
                            $content = preg_replace($pattern, $anchor_tag, $content, 1, $count);
                            if ($count > 0) {
                                $replace_count += $count;
                                $matched = true;
                                break;
                            }
                        }
                    }
                    if (!$matched) $skipped_words[] = $word;
                }
            }
        }

        if ($replace_count > 0) {
            $updated = wp_update_post(['ID' => $post_id, 'post_content' => $content]);
            if ($updated) {
                wp_send_json_success(['message' => "$replace_count links added successfully", 'content' => $content]);
            } else {
                throw new Exception('Failed to update post content');
            }
        } else {
            $message = 'No matching words found to link';
            if (!empty($skipped_words)) $message .= ' (Skipped: ' . implode(', ', array_unique($skipped_words)) . ')';
            throw new Exception($message);
        }
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }

}

/* ------------------------------
   Fetch external blog data with categories (uses WP fetch_feed)
   ------------------------------ */
function arb_fetch_external_blog_data($feed_urls = []) {
    include_once ABSPATH . WPINC . '/feed.php';
    if (empty($feed_urls)) {
        global $ARB_FEEDS;
        $feed_urls = $ARB_FEEDS;
    }
    $all = [];
    foreach ($feed_urls as $url) {
        $rss = fetch_feed($url);
        if (is_wp_error($rss)) continue;
        $max = $rss->get_item_quantity(15);
        $items = $rss->get_items(0, $max);
        foreach ($items as $it) {
            // categories extraction (SimplePie)
            $cats = [];
            $spcats = $it->get_categories();
            if (!empty($spcats) && is_array($spcats)) {
                foreach ($spcats as $c) {
                    $cats[] = $c->get_label();
                }
            }
            $all[] = [
                'title' => $it->get_title(),
                'link' => $it->get_link(),
                'description' => $it->get_description(),
                'categories' => $cats,
            ];
        }
    }
    return $all;
}

/* ------------------------------
   Fetch remote page & extract main content excerpt
   ------------------------------ */
function arb_fetch_url_content_excerpt($url, $max_chars = 1000) {
    $resp = wp_remote_get($url, ['timeout' => 12, 'headers' => ['Accept' => 'text/html']]);
    if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) return '';
    $body = wp_remote_retrieve_body($resp);
    $main = arb_extract_main_content_from_html($body);
    $main = trim(preg_replace('/\s+/', ' ', strip_tags($main)));
    if (mb_strlen($main) > $max_chars) $main = mb_substr($main, 0, $max_chars) . '...';
    return $main;
}

function arb_extract_main_content_from_html($html) {
    if (empty($html)) return '';
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $selectors = [
        "//article",
        "//*[contains(@class,'entry-content')]",
        "//*[contains(@class,'post-content')]",
        "//*[contains(@id,'content')]",
        "//main",
        "//*[contains(@class,'content')]"
    ];
    foreach ($selectors as $sel) {
        $nodes = $xpath->query($sel);
        if ($nodes && $nodes->length > 0) {
            $text = trim($nodes->item(0)->textContent);
            if (!empty($text)) return $text;
        }
    }
    $ps = $xpath->query("//p");
    if ($ps && $ps->length > 0) {
        $collected = [];
        for ($i=0; $i < min(6, $ps->length); $i++) {
            $t = trim($ps->item($i)->textContent);
            if (mb_strlen($t) > 40) $collected[] = $t;
        }
        if (!empty($collected)) return implode("\n\n", $collected);
    }
    $plain = trim(strip_tags($html));
    if (mb_strlen($plain) > 1500) return mb_substr($plain, 0, 1500) . '...';
    return $plain;
}

/* ------------------------------
   ChatGPT / Azure call helper
   ------------------------------ */
function arb_call_chatgpt($messages, $max_tokens = 700) {
    global $ARB_API_KEY, $ARB_API_URL;

    if (empty($ARB_API_KEY) || empty($ARB_API_URL)) {
        return ['ok' => false, 'error' => 'API config missing'];
    }

    $body = [
        'messages' => $messages,
        'max_tokens' => $max_tokens,
        'temperature' => 0.0
    ];

    $args = [
        'headers' => [
            'Content-Type' => 'application/json',
            'api-key' => $ARB_API_KEY
        ],
        'body' => wp_json_encode($body),
        'timeout' => 40
    ];

    $resp = wp_remote_post($ARB_API_URL, $args);
    if (is_wp_error($resp)) return ['ok' => false, 'error' => $resp->get_error_message()];

    $code = wp_remote_retrieve_response_code($resp);
    $raw = wp_remote_retrieve_body($resp);
    if ($code < 200 || $code >= 300) return ['ok' => false, 'error' => "HTTP $code: $raw"];

    $json = json_decode($raw, true);
    if (!$json) return ['ok' => false, 'error' => 'Invalid JSON from API: ' . substr($raw, 0, 400)];

    // pick assistant content
    $content = '';
    if (!empty($json['choices'][0]['message']['content'])) {
        $content = $json['choices'][0]['message']['content'];
    } elseif (!empty($json['choices'][0]['text'])) {
        $content = $json['choices'][0]['text'];
    } else {
        $content = wp_json_encode($json);
    }

    return ['ok' => true, 'content' => $content, 'raw' => $json];
}

/* ------------------------------
   Utilities
   ------------------------------ */
function arb_truncate_for_prompt($text, $max_chars = 3000) {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
    if (mb_strlen($text) <= $max_chars) return $text;
    $tr = mb_substr($text, 0, $max_chars);
    $pos = mb_strrpos($tr, ' ');
    if ($pos !== false) $tr = mb_substr($tr, 0, $pos);
    return $tr . '...';
}

function arb_extract_json_blob($text) {
    $text = trim($text);
    // look for first { ... } block
    $start = strpos($text, '{');
    $end = strrpos($text, '}');
    if ($start !== false && $end !== false && $end > $start) {
        $candidate = substr($text, $start, $end - $start + 1);
        $decoded = json_decode($candidate, true);
        if (json_last_error() === JSON_ERROR_NONE) return $decoded;
    }
    // look for [ ... ]
    $start = strpos($text, '[');
    $end = strrpos($text, ']');
    if ($start !== false && $end !== false && $end > $start) {
        $candidate = substr($text, $start, $end - $start + 1);
        $decoded = json_decode($candidate, true);
        if (json_last_error() === JSON_ERROR_NONE) return $decoded;
    }
    // try whole text
    $decoded = json_decode($text, true);
    if (json_last_error() === JSON_ERROR_NONE) return $decoded;
    return [];
}

/* ------------------------------
   Local analysis & matching (kept)
   ------------------------------ */
function arb_analyze_blog_content($content) {
    $clean = strip_tags($content);
    $word_count = str_word_count($clean);
    $sentence_count = preg_match_all('/[.!?]+/', $clean);
    $readability = ($sentence_count > 0 && $word_count > 0)
        ? round(min(100, max(0, 206.835 - (1.015 * ($word_count / $sentence_count)))), 1)
        : 0;

    $seo_score = 5;
    if ($word_count > 300) $seo_score += 1;
    if (preg_match_all('/<h[1-6][^>]*>/i', $content)) $seo_score += 1;
    if (preg_match_all('/<img[^>]*alt=["\'][^"\']+["\'][^>]*>/i', $content)) $seo_score += 1;
    if (preg_match_all('/<a[^>]*href=["\']' . preg_quote(home_url(), '/') . '[^"\']*["\'][^>]*>/i', $content)) $seo_score += 1;

    $key_topics = ['General'];
    if ($word_count > 100) {
        $words = preg_split('/\s+/', preg_replace('/[^a-zA-Z0-9\s]/', '', strip_tags($content)));
        $filtered = array_filter($words, function($w){ return strlen($w) > 5; });
        $unique = array_values(array_unique($filtered));
        $key_topics = array_slice($unique, 0, 12);
    }

    $recommendations = [];
    if ($word_count < 300) $recommendations[] = 'Increase word count';
    if (!preg_match('/<h[1-6][^>]*>/i', $content)) $recommendations[] = 'Add headings';

    return [
        'word_count' => $word_count,
        'readability' => $readability,
        'seo_score' => min(10, $seo_score),
        'key_topics' => $key_topics,
        'seo_recommendations' => $recommendations
    ];
}

function arb_find_matching_word_in_content($content, $title) {
    $clean_content = preg_replace('/[^a-zA-Z0-9\s]/', '', strip_tags($content));
    $title_words = array_filter(preg_split('/\s+/', $title), function($w){ return strlen($w) > 3; });
    $content_words = array_unique(array_filter(preg_split('/\s+/', $clean_content), function($w){ return strlen($w) > 3; }));

    $best = '';
    $high = 0;
    foreach ($title_words as $t) {
        foreach ($content_words as $c) {
            similar_text(strtolower($t), strtolower($c), $percent);
            if ($percent > $high && $percent > 60) {
                $high = $percent;
                $best = $c;
            }
        }
    }
    return $best ?: 'Not found';
}




// for humane tone

function arb_detect_human_tone($title, $content) {
    global $ARB_API_KEY, $ARB_API_URL;

    if (empty($content)) return 'Unknown';

    $messages = [
        [
            'role' => 'system',
            'content' => 'You are a tone analysis assistant. Analyze the tone of the given text and respond ONLY in the following format: "Tone: {tone_name}, Score: {1-10}". The score is based on how human-like, natural, and emotionally engaging the text sounds (1 = robotic, 10 = highly human and natural). No extra commentary or explanation.'
        ],
        [
            'role' => 'user',
            'content' => "Blog Title: {$title}\n\nBlog Content (truncated):\n" . arb_truncate_for_prompt($content, 2000)
        ]
    ];

    $resp = arb_call_chatgpt($messages, 50);
    if ($resp['ok'] && !empty($resp['content'])) {
        return trim($resp['content']);
    }
    return 'Unknown';
}



