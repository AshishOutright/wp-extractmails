<?php
/**
 * Plugin Name: Auto Relevant Blog
 * Description: Suggests relevant blog URLs via GPT & RSS, opens in a popup.
 * Version: 1.1
 * Author: Outright Systems
 */

// ✅ Enqueue Scripts + Styles
add_action('admin_enqueue_scripts', function ($hook) {
    if (in_array($hook, ['post-new.php', 'post.php'])) {
        $base = plugin_dir_url(__FILE__);
        wp_enqueue_script('auto-relevant-js', $base . 'popup.js', ['jquery'], null, true);
        wp_enqueue_style('auto-relevant-css', $base . 'popup.css');
        wp_localize_script('auto-relevant-js', 'ARB', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('arb_nonce'),
            'post_id'  => get_the_ID(),
        ]);
    }
});

// ✅ Add Floating Button + Modal to Admin Footer
add_action('admin_footer-post.php', 'arb_add_popup_button');
add_action('admin_footer-post-new.php', 'arb_add_popup_button');

function arb_add_popup_button() {
    ?>
    <button id="showRelevantUrls" class="arb-floating-btn">📎Relevant Blog</button>

    <div id="arbModal" class="arb-modal">
        <div class="arb-modal-content">
            <span class="arb-close">&times;</span>
            <div id="arbContent"><p>Loading relevant blogs...</p></div>

            <!-- ✅ Save Button -->
            <!-- <button id="arbSaveAnchors" class="arb-save-btn">💾 Save Anchors</button> -->
            <button id="arbSaveAnchors" class="arb-save-btn" data-post-id="<?php echo get_the_ID(); ?>">💾 Save Anchors</button>

        </div>
    </div>
    <?php
}


add_action('wp_ajax_save_anchors_to_post', function () {
    check_ajax_referer('arb_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $new_content_html = wp_unslash($_POST['content']); // user-edited HTML (with anchors)

    // 🔹 Get original post content
    $original_post = get_post($post_id);
    if (!$original_post) {
        wp_send_json_error('Post not found.');
    }

    $original_content = $original_post->post_content;

    // 🔹 Load both contents into DOMDocument
    libxml_use_internal_errors(true);
    $original_dom = new DOMDocument();
    $anchor_dom = new DOMDocument();

    $original_dom->loadHTML(mb_convert_encoding($original_content, 'HTML-ENTITIES', 'UTF-8'));
    $anchor_dom->loadHTML(mb_convert_encoding($new_content_html, 'HTML-ENTITIES', 'UTF-8'));

    // 🔹 Collect anchor words and hrefs from anchor DOM
    $anchors = $anchor_dom->getElementsByTagName('a');
    $replace_map = [];

    foreach ($anchors as $a) {
        $word = trim($a->textContent);
        $href = $a->getAttribute('href');
        if (!empty($word) && !empty($href)) {
            $replace_map[$word] = $href;
        }
    }

    // 🔹 Replace only matching words in original content (first occurrence)
    $body = $original_dom->getElementsByTagName('body')->item(0);
    replace_words_with_anchors($body, $replace_map, $original_dom);

    $final_content = inner_html($body);

    // 🔹 Update post
    wp_update_post([
        'ID' => $post_id,
        'post_content' => $final_content
    ]);

    wp_send_json_success('Anchors saved into post content.');
});

function replace_words_with_anchors(DOMNode $node, $replace_map, $dom) {
    foreach ($node->childNodes as $child) {
        if ($child->nodeType === XML_TEXT_NODE) {
            $text = $child->nodeValue;
            foreach ($replace_map as $word => $href) {
                if (stripos($text, $word) !== false) {
                    $parts = explode($word, $text, 2); // only first occurrence
                    if (count($parts) === 2) {
                        $before = $parts[0];
                        $after = $parts[1];

                        $a = $dom->createElement('a', $word);
                        $a->setAttribute('href', $href);
                        $a->setAttribute('target', '_blank');

                        $fragment = $dom->createDocumentFragment();
                        $fragment->appendChild($dom->createTextNode($before));
                        $fragment->appendChild($a);
                        $fragment->appendChild($dom->createTextNode($after));

                        $node->replaceChild($fragment, $child);
                    }
                    break; // move to next child node after one replacement
                }
            }
        } elseif ($child->hasChildNodes()) {
            replace_words_with_anchors($child, $replace_map, $dom);
        }
    }
}

function inner_html(DOMNode $node) {
    $innerHTML = '';
    foreach ($node->childNodes as $child) {
        $innerHTML .= $node->ownerDocument->saveHTML($child);
    }
    return $innerHTML;
}


// ✅ AJAX: Fetch Blog Links
add_action('wp_ajax_fetch_relevant_urls', function () {
    check_ajax_referer('arb_nonce', 'nonce');
    $post_id = intval($_POST['post_id']);
    $post = get_post($post_id);
    if (!$post) wp_send_json_error('Invalid post ID');

    $content = $post->post_content;
    $assigned_categories = wp_get_post_categories($post_id, ['fields' => 'names']);

    $categories = empty($assigned_categories) || in_array('uncategorized', array_map('strtolower', $assigned_categories))
        ? extract_categories_from_content_gpt($content)
        : array_map('strtolower', $assigned_categories);

    $externalPosts = fetch_external_blog_data();
    $grouped_by_category = [];

  foreach ($externalPosts as $item) {
    $matched = false;

    foreach ($item['categories'] as $external_cat) {
        foreach ($categories as $local_cat) {
            similar_text(strtolower($external_cat), strtolower($local_cat), $percent);
            if ($percent > 60) {
                $grouped_by_category[$local_cat][] = $item;
                $matched = true;
                // ✅ Keep checking for other matching categories
            }
        }
    }

    if (!$matched) {
        $grouped_by_category['uncategorized'][] = $item;
    }
}


    ob_start();
   foreach ($grouped_by_category as $category => $posts) {
    echo '<h3>' . esc_html(ucwords($category)) . '</h3><ul>';
    foreach ($posts as $item) {
        $link_hash = md5($item['link']);
        $suggested_anchor = find_relevant_anchor_from_content($content, $item['title'], $item['link']);

        echo '<li>';
        echo '<a href="' . esc_url($item['link']) . '" target="_blank">' . esc_html($item['title']) . '</a>';
        echo '<br><label>Anchor Text:</label> ';
        echo '<input type="text" class="arb-anchor-text" style="outline: none; border: none;"  data-link="' . esc_attr($item['link']) . '" data-hash="' . esc_attr($link_hash) . '" value="' . esc_attr($suggested_anchor) . '" />';
       
        echo '</li>';
    }
    echo '</ul>';
}


      
    wp_send_json_success(ob_get_clean());
});
function find_relevant_anchor_from_content($blog_content, $external_title, $external_url) {
    $apiKey = '5f155d5a75f54c17bfa9b947a8e000df';
    $apiUrl = 'https://outright-openai-1.openai.azure.com/openai/deployments/chatgpt-40/chat/completions?api-version=2024-02-15-preview';

    $prompt = <<<EOT
You are helping a writer link to external blog posts by finding relevant phrases in the current blog content.

Goal: Suggest a short anchor text (1-4 words) that matches the topic of the external blog post.

Instructions:
- Use ONLY the post content: "$blog_content"
- External post title: "$external_title"
- External blog categories are: AI, Technical, Business, Email, Social Media, SEO

Return a matching short phrase from the post content that relates to the external blog title/topic. If no relevant match is found in the content, return exactly this text: "Not found".

Return only the phrase.
EOT;

    $body = json_encode([
        'messages' => [
            ['role' => 'system', 'content' => 'You are a smart assistant that finds anchor text in content.'],
            ['role' => 'user', 'content' => $prompt],
        ],
        'temperature' => 0.3,
        'max_tokens' => 40,
    ]);

    $response = wp_remote_post($apiUrl, [
        'headers' => [
            'Content-Type' => 'application/json',
            'api-key' => $apiKey,
        ],
        'body' => $body,
        'timeout' => 60,
    ]);

    if (is_wp_error($response)) return $external_title;

    $data = json_decode(wp_remote_retrieve_body($response), true);
    $anchor = trim($data['choices'][0]['message']['content'] ?? '');

    return $anchor ?: 'Not found';
}

// ✅ Get External Blogs
function fetch_external_blog_data() {
    include_once ABSPATH . WPINC . '/feed.php';
    $sources = [
        'https://www.outrightcrm.com/feed/',
        'https://www.outrightsystems.org/feed/',
    ];

    $items = [];
    foreach ($sources as $source) {
        $rss = fetch_feed($source);
        if (!is_wp_error($rss)) {
            foreach ($rss->get_items(0, $rss->get_item_quantity(10)) as $item) {
                $items[] = [
                    'title' => $item->get_title(),
                    'link'  => $item->get_permalink(),
                    'categories' => array_map('strtolower', wp_list_pluck($item->get_categories(), 'term')),
                ];
            }
        }
    }
    return $items;
}

// ✅ GPT Category Extraction
function extract_categories_from_content_gpt($content) {
    $apiKey = '5f155d5a75f54c17bfa9b947a8e000df';
    $apiUrl = 'https://outright-openai-1.openai.azure.com/openai/deployments/chatgpt-40/chat/completions?api-version=2024-02-15-preview';

    $prompt = "Read the blog content and extract 3–5 relevant topic categories (like CRM, Migration, Data Security, WhatsApp Integration):\n\n$content";

    $body = json_encode([
        'messages' => [
            ['role' => 'system', 'content' => 'You are a smart assistant that extracts blog topics.'],
            ['role' => 'user', 'content' => $prompt],
        ],
        'temperature' => 0.4,
        'max_tokens' => 300,
    ]);

    $response = wp_remote_post($apiUrl, [
        'headers' => [
            'Content-Type' => 'application/json',
            'api-key' => $apiKey,
        ],
        'body' => $body,
        'timeout' => 60,
    ]);

    if (is_wp_error($response)) return [];

    $data = json_decode(wp_remote_retrieve_body($response), true);
    $text = trim($data['choices'][0]['message']['content'] ?? '');
    $lines = array_filter(array_map('trim', explode("\n", $text)));
    return array_map('strtolower', $lines);
}

function arb_save_anchors_and_replace_in_content($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Only run for posts
    if (get_post_type($post_id) !== 'post') return;

    if (!empty($_POST['relevant_urls'])) {
        $grouped = json_decode(stripslashes($_POST['relevant_urls']), true);
        $urls = [];

        foreach ($grouped as $items) {
            foreach ($items as $item) {
                $urls[] = $item['link'];
            }
        }

        $stored_urls = [];
        $anchor_replaced = false;

        foreach ($urls as $url) {
            $anchor_key = 'anchor_for_' . md5($url);
            $anchor_val = sanitize_text_field($_POST[$anchor_key] ?? '');

            if (!empty($anchor_val)) {
                update_post_meta($post_id, $anchor_key, $anchor_val);
                $stored_urls[] = [
                    'url' => esc_url_raw($url),
                    'anchor' => $anchor_val,
                ];
            }
        }

        update_post_meta($post_id, 'relevant_urls', json_encode($stored_urls));

        // Replace anchor text with links in post content
        $content = get_post_field('post_content', $post_id);
        foreach ($stored_urls as $item) {
            $anchor = $item['anchor'];
            $url = $item['url'];

            if (stripos($content, $anchor) !== false) {
                $linked = '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($anchor) . '</a>';
                $content = preg_replace('/\b' . preg_quote($anchor, '/') . '\b/i', $linked, $content, 1);
                $anchor_replaced = true;
            }
        }

        if ($anchor_replaced) {
            remove_action('save_post', 'arb_save_anchors_and_replace_in_content');
            wp_update_post([
                'ID' => $post_id,
                'post_content' => $content,
            ]);
            add_action('save_post', 'arb_save_anchors_and_replace_in_content');
        }
    }
}



// new



function display_relevant_urls_meta_box($post) {
    $post_content = apply_filters('the_content', $post->post_content);
    $external_title = get_post_meta($post->ID, '_external_title', true);
    $external_url = get_post_meta($post->ID, '_external_url', true);

    ?>
    <div id="anchor-suggestion-box">
        <button type="button" class="button button-primary" id="find-anchor-btn">Find Relevant Anchor</button>
        <div id="anchor-result" style="margin-top:10px;"></div>
        <input type="hidden" id="relevant-anchor" name="relevant_anchor" value="">
    </div>
    <script>
        jQuery(document).ready(function ($) {
            $('#find-anchor-btn').on('click', function () {
                $('#anchor-result').html('Checking for relevant anchor...');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'find_relevant_anchor',
                        content: <?php echo json_encode($post_content); ?>,
                        title: <?php echo json_encode($external_title); ?>,
                        url: <?php echo json_encode($external_url); ?>,
                    },
                    success: function (response) {
                        if (response === 'Not found') {
                            $('#anchor-result').html('<strong>No relevant anchor found.</strong>');
                            $('#relevant-anchor').val('');
                        } else {
                            const anchorHTML = `<p><strong>Suggested anchor:</strong> "${response}"</p>
                            <button type="button" class="button" id="save-anchor-btn">Save Link</button>`;
                            $('#anchor-result').html(anchorHTML);
                            $('#relevant-anchor').val(response);
                        }
                    },
                    error: function () {
                        $('#anchor-result').html('Error finding anchor.');
                    }
                });
            });

            // Save anchor and update meta field
            $(document).on('click', '#save-anchor-btn', function () {
                const anchorText = $('#relevant-anchor').val();
                if (!anchorText) return;

                const externalUrl = <?php echo json_encode($external_url); ?>;
                const postId = <?php echo $post->ID; ?>;

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'save_anchor_to_meta',
                        anchor: anchorText,
                        url: externalUrl,
                        post_id: postId
                    },
                    success: function (response) {
                        $('#anchor-result').append('<p style="color:green;">Anchor link saved successfully.</p>');
                    },
                    error: function () {
                        $('#anchor-result').append('<p style="color:red;">Error saving anchor link.</p>');
                    }
                });
            });
        });
    </script>
    <?php
}


add_action('wp_ajax_save_anchor_text_meta', function () {
    check_ajax_referer('arb_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $link = esc_url_raw($_POST['link']);
    $anchor = sanitize_text_field($_POST['anchor']);

    if (!$post_id || empty($link)) {
        wp_send_json_error('Invalid data');
    }

    // Save as post meta - key: arb_anchor_[md5(link)]
    $meta_key = 'arb_anchor_' . md5($link);
    update_post_meta($post_id, $meta_key, $anchor);

    wp_send_json_success('Saved');
});
