<?php
/*
Plugin Name: Chatbot Plugin aa
Description: Tawk.to-like chatbot plugin for WordPress with WP-1Click integration and real-time functionality
Version: 1.5
Author: Editorial Team
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CHATBOT_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Get client IP address more reliably
function chatbot_get_client_ip() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return sanitize_text_field($ip);
}

// Enqueue scripts and styles
function chatbot_enqueue_scripts() {
    wp_enqueue_style('chatbot-style', plugins_url('chatbot-style.css', __FILE__));
    wp_enqueue_script('chatbot-script', plugins_url('chatbot-script.js', __FILE__), array('jquery'), '1.5', true);
    
    // Create a unique identifier for the visitor
    $visitor_id = isset($_COOKIE['chatbot_visitor_id']) ? sanitize_text_field($_COOKIE['chatbot_visitor_id']) : uniqid('visitor_', true);
    
    // Set cookie if not already set
    if (!isset($_COOKIE['chatbot_visitor_id'])) {
        setcookie('chatbot_visitor_id', $visitor_id, time() + (86400 * 30), '/'); // 30 days
    }
    
    // Localize script with additional data
    wp_localize_script('chatbot-script', 'chatbot_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('chatbot_public_nonce'),
        'current_user_ip' => chatbot_get_client_ip(),
        'visitor_id' => $visitor_id,
        'is_user_logged_in' => is_user_logged_in(),
        'use_websocket' => get_option('chatbot_use_websocket', 0),
        'websocket_url' => get_option('chatbot_websocket_url', 'wss://your-websocket-server.com'),
        'polling_interval' => 3000 // 3 seconds
    ));
}
add_action('wp_enqueue_scripts', 'chatbot_enqueue_scripts');

// Add chatbot to footer
function add_chatbot_to_footer() {
    echo chatbot_display();
}
add_action('wp_footer', 'add_chatbot_to_footer');

function chatbot_display() {
    ob_start();
    ?>
    <div id="chatbot-widget">
        <!-- Chat Toggle Button -->
        <div id="chatbot-launcher">
            <div class="launcher-icon">
                <img id="chat-icon" src="https://www.mailsextract.com/wp-content/uploads/2025/05/image-removebg-preview-1.png" alt="Chat Icon" class="img-fluid" width="34" height="34">
                <svg id="close-icon" viewBox="0 0 24 24" width="24" height="24" style="display: none;">
                    <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </div>
            <div class="launcher-notification" id="notification-badge" style="display: none;">1</div>
        </div>

        <!-- Chat Window -->
        <div id="chatbot-window" style="display: none;">
            <div class="chat-header">
                <div class="header-content">
                    <div class="avatar">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V7H9V21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21V9H21Z"/>
                        </svg>
                    </div>
                    <div class="header-info">
                        <h4>Customer Support</h4>
                        <span class="status">● Online</span>
                    </div>
                </div>
                <button class="minimize-btn">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M19 13H5v-2h14v2z"/>
                    </svg>
                </button>
            </div>

            <div id="chat-messages" class="chat-messages">
                <div class="message bot-message">
                    <div class="message-avatar">
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V7H9V21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21V9H21Z"/>
                        </svg>
                    </div>
                    <div class="message-content">
                        <div class="message-text">Hi there! 👋 Welcome to Wp-1click! How can I help you today?</div>
                        <div class="message-time"><?php echo date('H:i'); ?></div>
                    </div>
                </div>
            </div>

            <div class="chat-input-container">
                <div class="input-wrapper">
                    <textarea
                        id="chat-input"
                        placeholder="Type your message..."
                        rows="1"
                        maxlength="1000"
                    ></textarea>
                    <button id="send-button" disabled>
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </button>
                </div>
                <div class="powered-by">
                    Powered by ExtractMails AI
                </div>
            </div>
        </div>
    </div>

    <style>
    #chatbot-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    #chatbot-launcher {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        color: white;
    }

    #chatbot-launcher:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .launcher-icon svg {
        transition: all 0.3s ease;
    }

    .launcher-notification {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4757;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    #chatbot-window {
        width: 380px;
        height: 600px;
        max-height: 80vh;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        margin-bottom: 10px;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .header-info h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .status {
        font-size: 12px;
        opacity: 0.9;
        color: #4ade80;
    }

    .minimize-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 8px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .minimize-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .chat-messages {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .message {
        display: flex;
        gap: 8px;
        max-width: 85%;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .bot-message {
        align-self: flex-start;
    }

    .user-message {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 32px;
        height: 32px;
        background: #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .user-message .message-avatar {
        background: #10b981;
    }

    .message-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .message-text {
        background: white;
        padding: 12px 16px;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        word-wrap: break-word;
        line-height: 1.4;
    }

    .user-message .message-text {
        background: #667eea;
        color: white;
    }

    .message-time {
        font-size: 11px;
        color: #64748b;
        margin-left: 8px;
    }

    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 16px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        background: #94a3b8;
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-10px); }
    }

    .chat-input-container {
        background: white;
        border-top: 1px solid #e2e8f0;
        padding: 16px;
    }

    .input-wrapper {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        background: #f1f5f9;
        border-radius: 24px;
        padding: 8px 12px;
        transition: all 0.2s;
    }

    .input-wrapper:focus-within {
        background: #e2e8f0;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
    }

    #chat-input {
        flex: 1;
        background: none;
        border: none;
        outline: none;
        resize: none;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.4;
        max-height: 100px;
        min-height: 20px;
    }

    #chat-input::placeholder {
        color: #94a3b8;
    }

    #send-button {
        width: 32px;
        height: 32px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    #send-button:hover:not(:disabled) {
        background: #5a67d8;
        transform: scale(1.1);
    }

    #send-button:disabled {
        background: #cbd5e0;
        cursor: not-allowed;
    }

    .powered-by {
        text-align: center;
        margin-top: 8px;
        font-size: 11px;
        color: #94a3b8;
    }

    @media (max-width: 480px) {
        #chatbot-window {
            width: calc(100vw - 40px);
            height: calc(100vh - 100px);
            max-height: none;
        }
    }

    .chat-messages::-webkit-scrollbar {
        width: 4px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 2px;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        let chatbotOpen = false;
        let messageHistory = [];
        let checkingForResponses = false;
        let websocket = null;
        let pollingInterval = null;
        
        // Initialize real-time communication
        function initRealtimeCommunication() {
            if (chatbot_ajax.use_websocket) {
                initWebSocket();
            } else {
                startPolling();
            }
        }
        
        // Initialize WebSocket connection
        function initWebSocket() {
            try {
                websocket = new WebSocket(chatbot_ajax.websocket_url);
                
                websocket.onopen = function() {
                    console.log('WebSocket connection established');
                    authenticateWebSocket();
                };
                
                websocket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    handleIncomingMessage(data);
                };
                
                websocket.onclose = function() {
                    console.log('WebSocket disconnected, falling back to polling');
                    startPolling();
                };
                
                websocket.onerror = function(error) {
                    console.error('WebSocket error:', error);
                    startPolling();
                };
            } catch (e) {
                console.error('WebSocket initialization failed:', e);
                startPolling();
            }
        }
        
        // Authenticate WebSocket connection
        function authenticateWebSocket() {
            if (websocket && websocket.readyState === WebSocket.OPEN) {
                websocket.send(JSON.stringify({
                    action: 'auth',
                    user_ip: chatbot_ajax.current_user_ip,
                    visitor_id: chatbot_ajax.visitor_id,
                    is_user_logged_in: chatbot_ajax.is_user_logged_in,
                    nonce: chatbot_ajax.nonce
                }));
            }
        }
        
        // Start polling for new messages
        function startPolling() {
            if (pollingInterval) clearInterval(pollingInterval);
            
            pollingInterval = setInterval(function() {
                checkForNewResponses();
            }, chatbot_ajax.polling_interval);
        }
        
        // Check for new responses from server
        function checkForNewResponses() {
            $.post(chatbot_ajax.ajax_url, {
                action: 'chatbot_check_responses',
                user_ip: chatbot_ajax.current_user_ip,
                visitor_id: chatbot_ajax.visitor_id,
                is_user_logged_in: chatbot_ajax.is_user_logged_in,
                nonce: chatbot_ajax.nonce
            }, function(response) {
                if (response.success && response.data.messages.length > 0) {
                    response.data.messages.forEach(message => {
                        handleIncomingMessage(message);
                    });
                }
            });
        }
        
        // Handle incoming messages (from WebSocket or polling)
        function handleIncomingMessage(message) {
            if (message.type === 'chat_message' && !message.is_user) {
                addMessage(message.content, false);
                messageHistory.push({ role: 'assistant', content: message.content });
                
                // Play notification sound if chat is closed
                if (!chatbotOpen) {
                    playNotificationSound();
                    showNotificationBadge();
                }
            }
        }
        
        // Play notification sound
        function playNotificationSound() {
            const audio = new Audio('<?php echo plugins_url('notification.mp3', __FILE__); ?>');
            audio.play().catch(e => console.log('Audio play failed:', e));
        }
        
        // Show notification badge
        function showNotificationBadge() {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.style.display = 'flex';
                badge.textContent = parseInt(badge.textContent || '0') + 1;
            }
        }
        
        // Toggle chatbot visibility
        window.toggleChatbot = function() {
            const chatWindow = document.getElementById('chatbot-window');
            const chatIcon = document.getElementById('chat-icon');
            const closeIcon = document.getElementById('close-icon');
            const notification = document.getElementById('notification-badge');
           
            chatbotOpen = !chatbotOpen;
           
            if (chatbotOpen) {
                chatWindow.style.display = 'flex';
                chatIcon.style.display = 'none';
                closeIcon.style.display = 'block';
                notification.style.display = 'none';
                document.getElementById('chat-input').focus();
                
                // Initialize real-time communication when chat is opened
                initRealtimeCommunication();
            } else {
                chatWindow.style.display = 'none';
                chatIcon.style.display = 'block';
                closeIcon.style.display = 'none';
                
                // Clean up real-time communication when chat is closed
                if (websocket) {
                    websocket.close();
                    websocket = null;
                }
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            }
        };

        // Make sure the toggle function is globally accessible
        if (typeof window.toggleChatbot !== 'function') {
            window.toggleChatbot = toggleChatbot;
        }

        // Add click event to launcher
        $('#chatbot-launcher').on('click', function() {
            toggleChatbot();
        });

        // Add click event to minimize button
        $('.minimize-btn').on('click', function() {
            toggleChatbot();
        });

        function addMessage(text, isUser = false, showTime = true) {
            const messagesContainer = document.getElementById('chat-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
           
            const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
           
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="${isUser ? 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z' : 'M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V7H9V21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21V9H21Z'}"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="message-text">${escapeHtml(text)}</div>
                    ${showTime ? `<div class="message-time">${currentTime}</div>` : ''}
                </div>
            `;
           
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function addTypingIndicator() {
            const messagesContainer = document.getElementById('chat-messages');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot-message';
            typingDiv.id = 'typing-indicator';
           
            typingDiv.innerHTML = `
                <div class="message-avatar">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V7H9V21C9 22.1 9.9 23 11 23H13C14.1 23 15 22.1 15 21V9H21Z"/>
                    </svg>
                </div>
                <div class="message-content">
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            `;
           
            messagesContainer.appendChild(typingDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function removeTypingIndicator() {
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
           
            if (!message) return;
           
            addMessage(message, true);
            messageHistory.push({ role: 'user', content: message });
           
            input.value = '';
            adjustTextareaHeight();
            updateSendButton();
           
            addTypingIndicator();
           
            // Send message via WebSocket if available, otherwise use AJAX
            if (websocket && websocket.readyState === WebSocket.OPEN) {
                websocket.send(JSON.stringify({
                    action: 'send_message',
                    message: message,
                    history: messageHistory,
                    user_ip: chatbot_ajax.current_user_ip,
                    visitor_id: chatbot_ajax.visitor_id,
                    is_user_logged_in: chatbot_ajax.is_user_logged_in,
                    nonce: chatbot_ajax.nonce
                }));
                
                // Set timeout in case WebSocket fails
                setTimeout(function() {
                    if (document.getElementById('typing-indicator')) {
                        sendMessageViaAjax(message);
                    }
                }, 5000);
            } else {
                sendMessageViaAjax(message);
            }
        }
        
        function sendMessageViaAjax(message) {
            $.post(chatbot_ajax.ajax_url, {
                action: 'chatbot_response',
                message: message,
                history: JSON.stringify(messageHistory),
                user_ip: chatbot_ajax.current_user_ip,
                visitor_id: chatbot_ajax.visitor_id,
                is_user_logged_in: chatbot_ajax.is_user_logged_in,
                nonce: chatbot_ajax.nonce
            }, function(response) {
                removeTypingIndicator();
               
                if (response.success) {
                    addMessage(response.data.reply, false);
                    messageHistory.push({ role: 'assistant', content: response.data.reply });
                } else {
                    console.error('Chatbot error:', response);
                    // Use the fallback response from the server if available
                    const fallback = response.data && response.data.fallback ? 
                        response.data.fallback : 
                        'I apologize, but I\'m having trouble processing your request right now. Please try again in a moment.';
                    addMessage(fallback, false);
                }
            }).fail(function(xhr, status, error) {
                removeTypingIndicator();
                console.error('Chatbot AJAX error:', status, error);
                addMessage('I\'m experiencing some technical difficulties. Please try again later or contact our support team directly.', false);
            });
        }

        function adjustTextareaHeight() {
            const textarea = document.getElementById('chat-input');
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
        }

        function updateSendButton() {
            const input = document.getElementById('chat-input');
            const sendButton = document.getElementById('send-button');
            sendButton.disabled = !input.value.trim();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event listeners
        $('#chat-input').on('input', function() {
            adjustTextareaHeight();
            updateSendButton();
        });

        $('#chat-input').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Send button click event
        $('#send-button').on('click', function() {
            sendMessage();
        });

        // Initialize
        updateSendButton();
    });
    </script>
    <?php
    return ob_get_clean();
}

// AJAX handler for chatbot responses
add_action('wp_ajax_chatbot_response', 'handle_chatbot_response');
add_action('wp_ajax_nopriv_chatbot_response', 'handle_chatbot_response');

function handle_chatbot_response() {
    // Verify nonce for public users
    if (!wp_verify_nonce($_POST['nonce'], 'chatbot_public_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce', 'fallback' => 'Session verification failed. Please refresh the page and try again.'));
        return;
    }
    
    $user_message = sanitize_text_field($_POST['message']);
    $history = json_decode(stripslashes($_POST['history']), true);
    $user_ip = chatbot_get_client_ip();
    $visitor_id = isset($_POST['visitor_id']) ? sanitize_text_field($_POST['visitor_id']) : '';
    $is_user_logged_in = isset($_POST['is_user_logged_in']) ? (bool)$_POST['is_user_logged_in'] : false;
    
    if (empty($user_message)) {
        wp_send_json_error(array('message' => 'Empty message', 'fallback' => 'Please type a message before sending.'));
        return;
    }
    
    // Store the conversation in database
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    $wpdb->insert(
        $table_name,
        array(
            'user_ip' => $user_ip,
            'visitor_id' => $visitor_id,
            'message' => $user_message,
            'status' => 'pending',
            'created_at' => current_time('mysql')
        )
    );
    
    $conversation_id = $wpdb->insert_id;
    
    // Check if we should respond with AI or wait for human
    $use_ai = get_option('chatbot_use_ai', 1);
    
    if ($use_ai) {
        // Check for WordPress actions first
        $wp_action = detect_wordpress_action($user_message);
        
        // Try to get intelligent response first
        $ai_response = get_ai_response($user_message, $history);
        
        if ($ai_response) {
            // Update the conversation with AI response
            $wpdb->update(
                $table_name,
                array(
                    'response' => $ai_response,
                    'is_ai_response' => 1,
                    'status' => 'responded',
                    'responded_at' => current_time('mysql')
                ),
                array('id' => $conversation_id)
            );
            
            wp_send_json_success([
                'reply' => $ai_response,
                'wp_action' => $wp_action ? $wp_action : null
            ]);
            return;
        }
        
        // If AI fails, use enhanced fallback system
        $fallback_response = get_fallback_response($user_message);
        
        // Update with fallback response
        $wpdb->update(
            $table_name,
            array(
                'response' => $fallback_response,
                'is_ai_response' => 1,
                'status' => 'responded',
                'responded_at' => current_time('mysql')
            ),
            array('id' => $conversation_id)
        );
        
        wp_send_json_success([
            'reply' => $fallback_response,
            'fallback' => true,
            'wp_action' => $wp_action ? $wp_action : null
        ]);
    } else {
        // Notify admin about new message waiting for response
        notify_admin_new_message($conversation_id, $user_message);
        
        wp_send_json_success([
            'reply' => 'Thank you for your message! Our support team has been notified and will respond shortly.'
        ]);
    }
}

// AJAX handler for checking new responses
add_action('wp_ajax_chatbot_check_responses', 'handle_chatbot_check_responses');
add_action('wp_ajax_nopriv_chatbot_check_responses', 'handle_chatbot_check_responses');

function handle_chatbot_check_responses() {
    // Verify public nonce
    if (!wp_verify_nonce($_POST['nonce'], 'chatbot_public_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    $user_ip = chatbot_get_client_ip();
    $visitor_id = isset($_POST['visitor_id']) ? sanitize_text_field($_POST['visitor_id']) : '';
    $is_user_logged_in = isset($_POST['is_user_logged_in']) ? (bool)$_POST['is_user_logged_in'] : false;
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    // Get unread responses for this user
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE 
        (user_ip = %s OR visitor_id = %s)
        AND status = 'responded' 
        AND is_ai_response = 0 
        AND is_read = 0 
        ORDER BY responded_at ASC",
        $user_ip,
        $visitor_id
    );
    
    $responses = $wpdb->get_results($query);
    
    if ($responses) {
        // Mark responses as read
        $ids = array_map(function($r) { return $r->id; }, $responses);
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET is_read = 1 WHERE id IN (" . implode(',', array_fill(0, count($ids), '%d')) . ")",
                $ids
            )
        );
        
        wp_send_json_success([
            'messages' => array_map(function($response) {
                return [
                    'type' => 'chat_message',
                    'content' => $response->response,
                    'timestamp' => $response->responded_at,
                    'is_user' => false
                ];
            }, $responses)
        ]);
    } else {
        wp_send_json_success(['messages' => []]);
    }
}

// Notify admin about new message
function notify_admin_new_message($conversation_id, $message) {
    $admin_email = get_option('admin_email');
    $subject = 'New Chatbot Message Waiting for Response';
    $message = "You have a new message from the chatbot:\n\n";
    $message .= "Message: $message\n";
    $message .= "View and respond: " . admin_url('admin.php?page=chatbot-conversations&id=' . $conversation_id);
    
    wp_mail($admin_email, $subject, $message);
    
    // Also send browser notification if possible
    add_action('admin_footer', function() use ($message) {
        ?>
        <script>
        if (Notification.permission === 'granted') {
            new Notification('New Chatbot Message', {
                body: '<?php echo esc_js(wp_trim_words($message, 20)); ?>',
                icon: '<?php echo plugins_url('chatbot-icon.png', __FILE__); ?>'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    new Notification('New Chatbot Message', {
                        body: '<?php echo esc_js(wp_trim_words($message, 20)); ?>',
                        icon: '<?php echo plugins_url('chatbot-icon.png', __FILE__); ?>'
                    });
                }
            });
        }
        </script>
        <?php
    });
}

// Detect WordPress-related actions
function detect_wordpress_action($message) {
    $lower_message = strtolower(trim($message));
   
    $actions = [
        'install wordpress' => ['type' => 'install_wordpress'],
        'create site' => ['type' => 'install_wordpress'],
        'new site' => ['type' => 'install_wordpress'],
        'manage sites' => ['type' => 'manage_sites'],
        'my sites' => ['type' => 'manage_sites'],
        'site list' => ['type' => 'manage_sites'],
        'backup site' => ['type' => 'backup_site'],
        'create backup' => ['type' => 'backup_site'],
        'site backup' => ['type' => 'backup_site'],
        'site health' => ['type' => 'site_health'],
        'check health' => ['type' => 'site_health'],
        'site status' => ['type' => 'site_health']
    ];
   
    foreach ($actions as $keyword => $action) {
        if (strpos($lower_message, $keyword) !== false) {
            return $action;
        }
    }
   
    return null;
}

function get_ai_response($user_message, $history) {
    // Get API credentials from WordPress options
    $api_key = get_option('chatbot_api_key', '');
    $api_url = get_option('chatbot_api_url', '');
   
    if (empty($api_key) || empty($api_url)) {
        error_log('Chatbot API: Missing API key or URL');
        return false;
    }
   
    // Enhanced system prompt with WordPress integration
    $system_prompt = 'You are a helpful customer service assistant for WP-1Click Websites. Your tone should always be friendly, professional, and solution-oriented. Offer step-by-step help and encourage users, especially beginners, with clear and easy-to-follow language.';
   
    // Prepare messages for API
    $messages = [
        [
            'role' => 'system',
            'content' => $system_prompt
        ]
    ];
   
    // Add conversation history (limit to last 10 messages)
    if ($history && is_array($history)) {
        $recent_history = array_slice($history, -10);
        $messages = array_merge($messages, $recent_history);
    }
   
    // Add current user message
    $messages[] = [
        'role' => 'user',
        'content' => $user_message
    ];
   
    try {
        $response = wp_remote_post($api_url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'api-key' => $api_key
            ],
            'body' => json_encode([
                'messages' => $messages,
                'max_tokens' => 300,
                'temperature' => 0.7,
                'presence_penalty' => 0.1,
                'frequency_penalty' => 0.1
            ]),
            'timeout' => 30
        ]);
       
        if (is_wp_error($response)) {
            error_log('Chatbot API Error: ' . $response->get_error_message());
            return false;
        }
       
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
       
        if ($response_code !== 200) {
            error_log('Chatbot API Error - Response Code: ' . $response_code);
            error_log('Chatbot API Error - Response Body: ' . $response_body);
            return false;
        }
       
        if (!$data || isset($data['error']) || !isset($data['choices'][0]['message']['content'])) {
            error_log('Chatbot API Error - Invalid response format');
            return false;
        }
       
        $reply = trim($data['choices'][0]['message']['content']);
       
        if (empty($reply)) {
            error_log('Chatbot API Error - Empty response');
            return false;
        }
       
        return $reply;
       
    } catch (Exception $e) {
        error_log('Chatbot API Exception: ' . $e->getMessage());
        return false;
    }
}

function get_fallback_response($user_message) {
    $lower_message = strtolower(trim($user_message));
   
    // Context-aware responses
    $responses = [
        // Greetings
        'hello' => 'Hello! 👋 How can I help you today?',
        'hi' => 'Hi there! 👋 What can I do for you?',
        'hey' => 'Hey! 👋 How can I assist you?',
       
        // Help and guidance
        'help' => 'I\'d be happy to help! What do you need assistance with?',
       
        // Thanks and goodbye
        'thank' => 'You\'re very welcome! 😊 Is there anything else I can help with?',
        'bye' => 'Goodbye! 👋 Have a great day!',
       
        // Default fallback
        'default' => 'Thanks for your message! Our team will get back to you shortly.'
    ];
   
    // Keyword-based response matching
    foreach ($responses as $keyword => $response) {
        if (strpos($lower_message, $keyword) !== false) {
            return $response;
        }
    }
   
    return $responses['default'];
}

// Add admin menu
add_action('admin_menu', 'chatbot_admin_menu');

function chatbot_admin_menu() {
    add_menu_page(
        'Chatbot Conversations',
        'Chatbot',
        'manage_chatbot_conversations',
        'chatbot-conversations',
        'chatbot_conversations_page',
        'dashicons-format-chat',
        25
    );
    
    add_submenu_page(
        'chatbot-conversations',
        'Settings',
        'Settings',
        'manage_options',
        'chatbot-settings',
        'chatbot_settings_page'
    );
}

function chatbot_conversations_page() {
    if (!current_user_can('manage_chatbot_conversations')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    // Handle response submission
    if (isset($_POST['send_response']) && isset($_POST['conversation_id']) && isset($_POST['response'])) {
        $conversation_id = intval($_POST['conversation_id']);
        $response = sanitize_textarea_field($_POST['response']);
        
        $wpdb->update(
            $table_name,
            array(
                'response' => $response,
                'is_ai_response' => 0,
                'status' => 'responded',
                'responded_at' => current_time('mysql')
            ),
            array('id' => $conversation_id)
        );
        
        echo '<div class="notice notice-success"><p>Response sent!</p></div>';
    }
    
    // Handle status filter
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $where = ($status_filter != 'all') ? " WHERE status = '$status_filter'" : '';
    
    // Get conversations
    $conversations = $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY created_at DESC");
    
    // Get counts for each status
    $all_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $pending_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
    $responded_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'responded'");
    ?>
    <div class="wrap">
        <h1>Chatbot Conversations</h1>
        
        <div class="chatbot-admin-container">
            <div class="conversation-filters">
                <ul class="subsubsub">
                    <li><a href="?page=chatbot-conversations&status=all" class="<?php echo $status_filter == 'all' ? 'current' : ''; ?>">All <span class="count">(<?php echo $all_count; ?>)</span></a> |</li>
                    <li><a href="?page=chatbot-conversations&status=pending" class="<?php echo $status_filter == 'pending' ? 'current' : ''; ?>">Pending <span class="count">(<?php echo $pending_count; ?>)</span></a> |</li>
                    <li><a href="?page=chatbot-conversations&status=responded" class="<?php echo $status_filter == 'responded' ? 'current' : ''; ?>">Responded <span class="count">(<?php echo $responded_count; ?>)</span></a></li>
                </ul>
            </div>
            
            <div class="conversation-list">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IP Address</th>
                            <th>Visitor ID</th>
                            <th>Message</th>
                            <th>Response</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conversations as $conv): ?>
                        <tr>
                            <td><?php echo $conv->id; ?></td>
                            <td><?php echo esc_html($conv->user_ip); ?></td>
                            <td><?php echo esc_html($conv->visitor_id); ?></td>
                            <td><?php echo wp_trim_words($conv->message, 10); ?></td>
                            <td><?php echo $conv->response ? wp_trim_words($conv->response, 10) : '—'; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $conv->status; ?>">
                                    <?php echo ucfirst($conv->status); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y g:i a', strtotime($conv->created_at)); ?></td>
                            <td>
                                <a href="#conversation-<?php echo $conv->id; ?>" class="button view-conversation">View/Reply</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="conversation-detail">
                <?php if (!empty($conversations)): ?>
                    <?php $first_conv = reset($conversations); ?>
                    <div id="conversation-<?php echo $first_conv->id; ?>" class="conversation-panel">
                        <h2>Conversation #<?php echo $first_conv->id; ?></h2>
                        
                        <div class="message-history">
                            <div class="message user-message">
                                <div class="message-content">
                                    <div class="message-text"><?php echo wpautop(esc_textarea($first_conv->message)); ?></div>
                                    <div class="message-time"><?php echo date('M j, Y g:i a', strtotime($first_conv->created_at)); ?></div>
                                </div>
                            </div>
                            
                            <?php if ($first_conv->response): ?>
                            <div class="message admin-message">
                                <div class="message-content">
                                    <div class="message-text"><?php echo wpautop(esc_textarea($first_conv->response)); ?></div>
                                    <div class="message-time"><?php echo date('M j, Y g:i a', strtotime($first_conv->responded_at)); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <form method="post" class="response-form">
                            <input type="hidden" name="conversation_id" value="<?php echo $first_conv->id; ?>">
                            <textarea name="response" rows="5" placeholder="Type your response here..." required><?php echo $first_conv->response; ?></textarea>
                            <div class="response-actions">
                                <button type="submit" name="send_response" class="button button-primary"><?php echo $first_conv->response ? 'Update Response' : 'Send Response'; ?></button>
                                <button type="button" class="button button-secondary use-ai-response">Use AI Response</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="no-conversations">
                        <p>No conversations found matching your criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .chatbot-admin-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }
        
        .conversation-filters {
            background: #fff;
            padding: 10px 20px;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        
        .conversation-list {
            flex: 1;
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        
        .conversation-detail {
            flex: 1;
            background: #fff;
            padding: 20px;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        
        .message-history {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        
        .message {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 4px;
            max-width: 80%;
        }
        
        .user-message {
            background: #f3f3f3;
            margin-right: auto;
        }
        
        .admin-message {
            background: #e3f2fd;
            margin-left: auto;
        }
        
        .message-time {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending {
            background: #ff9800;
            color: white;
        }
        
        .status-responded {
            background: #4caf50;
            color: white;
        }
        
        .response-form textarea {
            width: 100%;
            margin-bottom: 10px;
            min-height: 100px;
        }
        
        .response-actions {
            display: flex;
            gap: 10px;
        }
        
        .no-conversations {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('.view-conversation').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                $('.conversation-panel').hide();
                $(target).show();
            });
            
            $('.use-ai-response').on('click', function() {
                var message = $(this).closest('.conversation-panel').find('.user-message .message-text').text();
                var textarea = $(this).closest('form').find('textarea');
                
                textarea.val('Generating AI response...').prop('disabled', true);
                
                $.post(ajaxurl, {
                    action: 'chatbot_response',
                    message: message,
                    history: JSON.stringify([{role: 'user', content: message}]),
                    nonce: '<?php echo wp_create_nonce('chatbot_nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        textarea.val(response.data.reply);
                    } else {
                        textarea.val('Error generating AI response');
                    }
                    textarea.prop('disabled', false);
                });
            });
        });
        </script>
    </div>
    <?php
}

function chatbot_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('chatbot_api_key', sanitize_text_field($_POST['api_key']));
        update_option('chatbot_api_url', sanitize_text_field($_POST['api_url']));
        update_option('chatbot_use_ai', isset($_POST['use_ai']) ? 1 : 0);
        update_option('chatbot_use_websocket', isset($_POST['use_websocket']) ? 1 : 0);
        update_option('chatbot_websocket_url', sanitize_text_field($_POST['websocket_url']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
   
    $api_key = get_option('chatbot_api_key', '');
    $api_url = get_option('chatbot_api_url', '');
    $use_ai = get_option('chatbot_use_ai', 1);
    $use_websocket = get_option('chatbot_use_websocket', 0);
    $websocket_url = get_option('chatbot_websocket_url', 'wss://your-websocket-server.com');
    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <input type="password" name="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" />
                        <p class="description">Enter your Azure OpenAI API key</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">API URL</th>
                    <td>
                        <input type="url" name="api_url" value="<?php echo esc_attr($api_url); ?>" class="regular-text" />
                        <p class="description">Enter your Azure OpenAI endpoint URL</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Response Mode</th>
                    <td>
                        <label>
                            <input type="checkbox" name="use_ai" value="1" <?php checked($use_ai, 1); ?> />
                            Use AI Responses (uncheck to require human responses)
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Real-Time Communication</th>
                    <td>
                        <label>
                            <input type="checkbox" name="use_websocket" value="1" <?php checked($use_websocket, 1); ?> />
                            Use WebSocket for real-time communication (recommended)
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">WebSocket URL</th>
                    <td>
                        <input type="url" name="websocket_url" value="<?php echo esc_attr($websocket_url); ?>" class="regular-text" />
                        <p class="description">Enter your WebSocket server URL (e.g., wss://your-server.com)</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Add shortcode support
add_shortcode('chatbot_ui', 'chatbot_display');

// Add activation hook to set default options
register_activation_hook(__FILE__, 'chatbot_activation');

function chatbot_activation() {
    // Set default options if they don't exist
    if (!get_option('chatbot_api_key')) {
        add_option('chatbot_api_key', '');
    }
    if (!get_option('chatbot_api_url')) {
        add_option('chatbot_api_url', 'https://api.openai.com/v1/chat/completions'); // Default OpenAI endpoint
    }
    if (!get_option('chatbot_use_ai')) {
        add_option('chatbot_use_ai', 1);
    }
    if (!get_option('chatbot_use_websocket')) {
        add_option('chatbot_use_websocket', 0);
    }
    if (!get_option('chatbot_websocket_url')) {
        add_option('chatbot_websocket_url', 'wss://your-websocket-server.com');
    }
    
    // Create database table for conversations
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_ip varchar(45) NOT NULL,
        visitor_id varchar(100) NOT NULL,
        user_name varchar(100) DEFAULT '',
        user_email varchar(100) DEFAULT '',
        message text NOT NULL,
        response text DEFAULT '',
        is_ai_response tinyint(1) DEFAULT 1,
        is_read tinyint(1) DEFAULT 0,
        status varchar(20) DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        responded_at datetime NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Add capability for admins to manage chats
    $role = get_role('administrator');
    $role->add_cap('manage_chatbot_conversations');
}

// Add admin notification badge
add_action('admin_head', 'chatbot_admin_menu_notification');

function chatbot_admin_menu_notification() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    $pending_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
    
    if ($pending_count > 0) {
        ?>
        <style>
        #adminmenu .toplevel_page_chatbot-conversations .wp-menu-name:after {
            content: '<?php echo $pending_count; ?>';
            display: inline-block;
            background: #d63638;
            color: #fff;
            font-size: 9px;
            line-height: 17px;
            font-weight: 600;
            border-radius: 10px;
            padding: 0 5px;
            margin-left: 5px;
            vertical-align: top;
        }
        </style>
        <?php
    }
}