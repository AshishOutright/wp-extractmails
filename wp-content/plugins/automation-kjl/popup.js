jQuery(document).ready(function ($) {
    if (typeof ARB === 'undefined') {
        console.error('ARB object not found. Ensure wp_localize_script() is set.');
        return;
    }

    // Open modal and fetch relevant blog URLs
    $('#showRelevantUrls').on('click', function (e) {
        e.preventDefault();

        $('#arbWordCount, #arbReadability, #arbSeoScore, #arbKeyTopics').text('...');
        $('#arbSeoRecommendations, #arbSeoKeywords').empty();
        $('#arbContentAnalysis').addClass('loading');

        $('#arbModal').fadeIn();
        $('#arbContent').html('<div class="arb-loading"><div class="arb-spinner"></div><p>Analyzing content and finding relevant blogs...</p></div>');

        $.post(ARB.ajax_url, {
            action: 'fetch_relevant_urls',
            nonce: ARB.nonce,
            post_id: ARB.post_id
        })
        .done(function (response) {
            $('#arbContentAnalysis').removeClass('loading');
            if (response.success) {
                $('#arbContent').html(response.data);
                initKeywordTags();
            } else {
                $('#arbContent').html('<div class="arb-error"><p>❌ Error fetching blog URLs.</p><p>' + (response.data || 'Unknown error') + '</p></div>');
                console.error(response.data);
            }
        })
        .fail(function (xhr) {
            $('#arbContentAnalysis').removeClass('loading');
            $('#arbContent').html('<div class="arb-error"><p>❌ Request failed. Please try again.</p><p>' + xhr.statusText + '</p></div>');
            console.error('Request failed:', xhr.statusText);
        });
    });

    // Close modal
    $(document).on('click', '.arb-close, #arbModal', function (e) {
        if ($(e.target).is('#arbModal') || $(e.target).hasClass('arb-close')) {
            $('#arbModal').fadeOut();
        }
    });

    // Prevent modal content clicks from closing
    $('.arb-modal-content').on('click', function (e) {
        e.stopPropagation();
    });

    // Save anchors
    $('#arbSaveAnchors').on('click', function (e) {
        e.preventDefault();

        const $button = $(this);
        const postId = $button.data('post-id');
        const suggestions = [];

        $('.anchor-suggestion-item').each(function () {
            const $item = $(this);
            const word = String($item.data('word') || '').trim();
            const url = $item.find('.anchor-url').val();
            const anchorText = $item.find('.anchor-text').val();

            if (word && url && anchorText && word.toLowerCase() !== 'not found') {
                suggestions.push({
                    word: word,
                    urls: [{ url: url, text: anchorText }]
                });
            }
        });

        if (!suggestions.length) {
            alert('No valid anchors to save. Please check:\n1. Matching words\n2. Not already linked\n3. All fields filled');
            return;
        }

        $button.html('<span class="arb-saving-spinner"></span> Saving...').prop('disabled', true);

        $.post(ARB.ajax_url, {
            action: 'save_anchors_to_post',
            nonce: ARB.nonce,
            post_id: postId,
            suggestions: suggestions
        })
        .done(function (response) {
            if (response.success) {
                $button.text('✅ Saved!').addClass('success');
                setTimeout(function () {
                    $('#arbModal').fadeOut();
                    $button.html('💾 Save Anchors').removeClass('success').prop('disabled', false);

                    if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
                        wp.data.dispatch('core/editor').editPost({ content: response.data.content });
                        if (wp.autosave && wp.autosave.server) {
                            wp.autosave.server.triggerSave();
                        }
                    }
                }, 1500);
            } else {
                handleSaveError($button, response.data);
            }
        })
        .fail(function (xhr) {
            handleSaveError($button, xhr.statusText);
        });
    });

    function handleSaveError($button, message) {
        $button.text('❌ Error!already added ').addClass('error').prop('disabled', false);
        showAlertModal('Error saving anchors', message || 'Please try again.');
        setTimeout(function () {
            $button.html('💾 Save Anchors').removeClass('error');
        }, 2000);
    }

    function initKeywordTags() {
        $('#arbSeoKeywords li').on('click', function () {
            const keyword = $(this).text().trim();
            const $input = $('.anchor-suggestion-item:first .anchor-text');

            if ($input.length) {
                $input.val(keyword).trigger('change');
                $('html, body').animate({ scrollTop: $input.offset().top - 100 }, 300);
            } else {
                alert('No anchor fields available to insert keyword.');
            }
        });
    }

    function showAlertModal(title, message) {
        const $alert = $(`
            <div class="arb-alert-modal">
                <div class="arb-alert-content">
                    <h3>${title}</h3>
                    <p>${message}</p>
                    <button class="arb-alert-close">OK</button>
                </div>
            </div>
        `);
        $('body').append($alert);
        $alert.fadeIn();
        $alert.on('click', function (e) {
            if ($(e.target).is('.arb-alert-modal, .arb-alert-close')) {
                $alert.fadeOut(function () { $(this).remove(); });
            }
        });
    }
});




// Handle delete for existing custom anchors
$('.delete-custom-anchor').on('click', function() {
    var item = $(this).closest('.anchor-suggestion-item');
    var anchorText = item.find('.anchor-text').val();
    var anchorUrl = item.find('.anchor-url').val();
    
    if (confirm('Are you sure you want to remove this anchor link from your post?')) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'remove_anchor_from_post',
                post_id: ARB.post_id,
                anchor_text: anchorText,
                anchor_url: anchorUrl,
                nonce: ARB.nonce
            },
            success: function(response) {
                if (response.success) {
                    item.remove();
                    
                    // Optional: Update the editor content if in block editor
                    if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
                        wp.data.dispatch('core/editor').editPost({ content: response.data.content });
                    }
                    
                    showAlertModal('Success', 'Anchor link removed successfully');
                } else {
                    console.error('Error deleting anchor:', response.data);
                    showAlertModal('Error', 'Failed to remove anchor: ' + response.data);
                }
            },
            error: function(xhr) {
                console.error('Request failed:', xhr.statusText);
                showAlertModal('Error', 'Request failed: ' + xhr.statusText);
            }
        });
    }
});



