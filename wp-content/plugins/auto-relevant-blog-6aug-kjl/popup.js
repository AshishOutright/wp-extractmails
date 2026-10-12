
jQuery(document).ready(function ($) {
    // Open modal and fetch relevant blog URLs
    $('#showRelevantUrls').on('click', function () {
        $('#arbModal').fadeIn(); // Show modal
        $('#arbContent').html('<p>Loading relevant blogs...</p>'); // Show loading text

        // Send AJAX request to PHP
        $.post(ARB.ajax_url, {
            action: 'fetch_relevant_urls',
            nonce: ARB.nonce,
            post_id: ARB.post_id // <-- ✅ passed from PHP via wp_localize_script
        }, function (response) {
            if (response.success) {
                $('#arbContent').html(response.data); // Replace content with blog URLs
            } else {
                $('#arbContent').html('<p>Error fetching blog URLs.</p>');
                console.error(response.data);
            }
        });
    });

    // Close modal when clicking the close icon
    $(document).on('click', '.arb-close', function () {
        $('#arbModal').fadeOut();
    });

    // Optional: Close modal if clicking outside modal content
    $(window).on('click', function (e) {
        if ($(e.target).is('#arbModal')) {
            $('#arbModal').fadeOut();
        }
    });
});




// save

// jQuery(document).on('click', '.arb-save-anchor', function () {
//     const $btn = jQuery(this);
//     const $input = $btn.closest('.arb-anchor-group').find('.arb-anchor-input');
//     const anchorText = $input.val();
//     const postId = $btn.data('post-id');
//     const linkUrl = $btn.data('url');

//     if (!anchorText) {
//         alert('Please enter anchor text.');
//         return;
//     }

//     $btn.text('Saving...').prop('disabled', true);

//     jQuery.ajax({
//         url: ajaxurl,
//         type: 'POST',
//         data: {
//             action: 'arb_save_anchor_text',
//             post_id: postId,
//             link_url: linkUrl,
//             anchor_text: anchorText,
//         },
//         success: function (res) {
//             if (res.success) {
//                 $btn.text('Saved!').addClass('btn-success');
//             } else {
//                 $btn.text('Failed').prop('disabled', false).addClass('btn-danger');
//             }
//         },
//         error: function () {
//             $btn.text('Error').prop('disabled', false).addClass('btn-danger');
//         }
//     });
// });

function convertSelectionToAnchor(href) {
    const selection = window.getSelection();
    if (selection.rangeCount > 0) {
        const range = selection.getRangeAt(0);
        const selectedText = selection.toString();

        if (selectedText.length > 0) {
            const anchor = document.createElement("a");
            anchor.href = href || "#";
            anchor.textContent = selectedText;
            anchor.target = "_blank";

            range.deleteContents();
            range.insertNode(anchor);
        }
    }
}
// jQuery(document).on('click', '#arbSaveAnchors', function () {
//     const $btn = jQuery(this);
//     const content = jQuery('#arbContent').html(); // Get modified content
//     const postId = $btn.data('post-id'); // Set this in PHP when rendering

//     $btn.text('Saving...').prop('disabled', true);

//     jQuery.ajax({
//         url: ajaxurl,
//         method: 'POST',
//         data: {
//             action: 'wp_ajax_save_anchors_to_post',
//             content: content,
//             post_id: postId,
//             nonce: arb_nonce
//         },
//         success: function (res) {
//             alert('Anchors saved successfully!');
//             $btn.text('Saved').prop('disabled', false);
//         },
//         error: function () {
//             alert('Error saving anchors');
//             $btn.text('Save Anchors').prop('disabled', false);
//         }
//     });
// });



jQuery(document).on('click', '#arbSaveAnchors', function () {
    const $btn = jQuery(this);
    const content = jQuery('#arbContent').html();

    $btn.text('Saving...').prop('disabled', true);

    jQuery.ajax({
        url: ARB.ajax_url, // ✅ updated from arbData to ARB
        method: 'POST',
        data: {
            action: 'save_anchors_to_post',
            content: content,
            post_id: ARB.post_id, // ✅ updated
            nonce: ARB.nonce       // ✅ updated
        },
        success: function (res) {
            if (res.success) {
                alert('Anchors saved successfully!');
            } else {
                alert('Error: ' + res.data);
            }
            $btn.text('💾 Save Anchors').prop('disabled', false);
        },
        error: function (xhr, status, error) {
            alert('AJAX error: ' + error);
            $btn.text('💾 Save Anchors').prop('disabled', false);
        }
    });
});
