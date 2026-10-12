// chatbot-admin.js
jQuery(document).ready(function($) {
    let pollingInterval = null;
    let lastUpdateTime = 0;
    let notificationSound = new Audio(chatbot_admin_vars.notification_sound);
    let currentPendingCount = 0;
    
    // Initialize real-time updates
    function initRealTimeUpdates() {
        if (pollingInterval) clearInterval(pollingInterval);
        
        // Function to check for updates
        const checkUpdates = () => {
            $.ajax({
                url: chatbot_admin_vars.ajaxurl,
                method: 'POST',
                data: {
                    action: 'chatbot_check_new_messages',
                    nonce: chatbot_admin_vars.nonce,
                    last_update: lastUpdateTime,
                    current_conversation_id: $('.conversation-panel').data('conversation-id') || 0
                },
                success: function(response) {
                    if (response.success) {
                        lastUpdateTime = response.data.timestamp;
                        
                        // Play sound for any new messages (when pending count is > 0)
                        if (response.data.pending_count > 0) {
                            notificationSound.play().catch(e => console.log('Audio play failed:', e));
                        }
                        
                        // Update pending count
                        currentPendingCount = response.data.pending_count;
                        updatePendingCountBadge(response.data.pending_count);
                        
                        // Update current conversation if open
                        if (response.data.current_conversation_updates) {
                            updateCurrentConversation(response.data.current_conversation_updates);
                        }
                        
                        // Update conversation list
                        if (response.data.new_conversations) {
                            updateConversationList(response.data.new_conversations);
                        }
                    }
                }
            });
        };
        
        // Immediate check to get initial count and existing messages
        $.ajax({
            url: chatbot_admin_vars.ajaxurl,
            method: 'POST',
            data: {
                action: 'chatbot_check_new_messages',
                nonce: chatbot_admin_vars.nonce,
                last_update: 0
            },
            success: function(response) {
                if (response.success) {
                    currentPendingCount = response.data.pending_count;
                    updatePendingCountBadge(currentPendingCount);
                }
            }
        });
        
        // Start regular polling
        pollingInterval = setInterval(checkUpdates, chatbot_admin_vars.polling_interval);
    }

    // Function to update current conversation
    function updateCurrentConversation(messages) {
        const $history = $('.message-history');
        
        messages.forEach(msg => {
            // Check if message already exists
            if (!$(`[data-message-id="${msg.id}"]`).length) {
                const messageClass = msg.response ? 'admin-message' : 'user-message';
                const messageHtml = `
                    <div class="message ${messageClass}" data-message-id="${msg.id}">
                        <div class="message-content">
                            <div class="message-text">${msg.response || msg.message}</div>
                            <div class="message-time">${msg.created_at}</div>
                        </div>
                    </div>
                `;
                
                $history.append(messageHtml);
                
                // Scroll to bottom
                $history.parent().scrollTop($history[0].scrollHeight);
            }
        });
    }
        
    // Update the pending count badge in admin menu
    function updatePendingCountBadge(count) {
        const badge = $('#adminmenu .toplevel_page_chatbot-conversations .wp-menu-name:after');
        
        if (count > 0) {
            badge.text(count).show();
        } else {
            badge.hide();
        }
    }
    
    // Handle new messages (show desktop notifications)
    function handleNewMessages(messages) {
        if (messages.length === 0) return;
        
        // Check if browser notifications are supported
        if (!("Notification" in window)) {
            return;
        }
        
        // Check if notifications are permitted
        if (Notification.permission === "granted") {
            showNotifications(messages);
        } 
        // Otherwise, ask for permission
        else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(function(permission) {
                if (permission === "granted") {
                    showNotifications(messages);
                }
            });
        }
    }
    
    // Show desktop notifications
    function showNotifications(messages) {
        messages.forEach(message => {
            new Notification('New Chat Message', {
                body: message.message,
                icon: chatbot_admin_vars.notification_icon || ''
            });
        });
    }
    
    // Update conversation list via AJAX
    function updateConversationList() {
        const currentFilters = {
            status: $('#status-filter').val(),
            visitor: $('#visitor-filter').val(),
            search: $('#search-input').val(),
            paged: $('.tablenav-pages .current-page').val() || 1
        };
        
        $.ajax({
            url: window.location.href,
            method: 'GET',
            data: currentFilters,
            success: function(response) {
                const $response = $(response);
                const newTable = $response.find('.conversation-list-container').html();
                
                // Only update if content changed
                if (newTable !== $('.conversation-list-container').html()) {
                    $('.conversation-list-container').html(newTable);
                    
                    // Reattach event handlers
                    attachEventHandlers();
                    
                    // Show update notification
                    showUpdateNotification('Conversation list updated');
                }
            }
        });
    }
    
    // Show a temporary update notification
    function showUpdateNotification(message) {
        const $notice = $('<div class="notice notice-success is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);
        
        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            $notice.fadeOut(500, () => $notice.remove());
        }, 3000);
    }
    
    // Attach event handlers to dynamic elements
    function attachEventHandlers() {
        // Bulk select all checkbox
        $('#cb-select-all').off('change').on('change', function() {
            $('input[name="selected_conversations[]"]').prop('checked', $(this).prop('checked'));
        });
        
        // AI response generation
        $('.use-ai-response').off('click').on('click', function() {
            const $panel = $(this).closest('.conversation-panel');
            const message = $panel.find('.user-message .message-text').text();
            const $textarea = $panel.find('textarea');
            
            $textarea.val('Generating AI response...').prop('disabled', true);
            
            $.ajax({
                url: chatbot_admin_vars.ajaxurl,
                method: 'POST',
                data: {
                    action: 'chatbot_generate_ai_response',
                    message: message,
                    nonce: chatbot_admin_vars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $textarea.val(response.data.reply);
                    } else {
                        $textarea.val('Error generating AI response');
                    }
                    $textarea.prop('disabled', false);
                }
            });
        });
        
        // Handle reply sending - reset last update time to get fresh notifications
        $(document).on('click', '.send-reply', function() {
            setTimeout(() => {
                lastUpdateTime = 0; // Reset to get fresh data after reply
            }, 1000);
        });
    }
    
    // Initialize everything
    initRealTimeUpdates();
    attachEventHandlers();
    
    // Clean up on page unload
    $(window).on('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
});