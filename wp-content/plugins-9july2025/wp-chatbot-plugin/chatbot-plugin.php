<?php
/*
Plugin Name: Chatbot Plugin
Description: Tawk.to-like chatbot plugin for WordPress with REST API and WebSocket integration
Version: 1.6
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
    wp_enqueue_script('chatbot-script', plugins_url('chatbot-script.js', __FILE__), array('jquery'), '1.6', true);
    
    // Create a unique identifier for the visitor
    $visitor_id = isset($_COOKIE['chatbot_visitor_id']) ? sanitize_text_field($_COOKIE['chatbot_visitor_id']) : uniqid('visitor_', true);
    
    // Set cookie if not already set
    if (!isset($_COOKIE['chatbot_visitor_id'])) {
        setcookie('chatbot_visitor_id', $visitor_id, time() + (86400 * 30), '/'); // 30 days
    }
    
    // Localize script with additional data
    wp_localize_script('chatbot-script', 'chatbot_data', array(
        'rest_url' => rest_url('chatbot/v1'),
        'nonce' => wp_create_nonce('wp_rest'),
        'current_user_id' => get_current_user_id(),
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
                        <div class="message-text">Hi there! 👋 Welcome to our support chat! How can I help you today?</div>
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
                    Powered by Our Support Team
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
            if (chatbot_data.use_websocket) {
                initWebSocket();
            } else {
                startPolling();
            }
        }
        
        // Initialize WebSocket connection
        function initWebSocket() {
            try {
                websocket = new WebSocket(chatbot_data.websocket_url);
                
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
                    user_id: chatbot_data.current_user_id,
                    user_ip: chatbot_data.current_user_ip,
                    visitor_id: chatbot_data.visitor_id,
                    is_user_logged_in: chatbot_data.is_user_logged_in,
                    nonce: chatbot_data.nonce
                }));
            }
        }
        
        // Start polling for new messages
        function startPolling() {
            if (pollingInterval) clearInterval(pollingInterval);
            
            pollingInterval = setInterval(function() {
                checkForNewResponses();
            }, chatbot_data.polling_interval);
        }
        
        // Check for new responses from server
        function checkForNewResponses() {
            $.ajax({
                url: chatbot_data.rest_url + '/check_responses',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', chatbot_data.nonce);
                },
                data: {
                    user_id: chatbot_data.current_user_id,
                    user_ip: chatbot_data.current_user_ip,
                    visitor_id: chatbot_data.visitor_id
                },
                success: function(response) {
                    if (response.success && response.data.messages.length > 0) {
                        response.data.messages.forEach(message => {
                            handleIncomingMessage(message);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to check for new responses:', error);
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
           
            // Send message via WebSocket if available, otherwise use REST API
            if (websocket && websocket.readyState === WebSocket.OPEN) {
                websocket.send(JSON.stringify({
                    action: 'send_message',
                    message: message,
                    history: messageHistory,
                    user_id: chatbot_data.current_user_id,
                    user_ip: chatbot_data.current_user_ip,
                    visitor_id: chatbot_data.visitor_id,
                    is_user_logged_in: chatbot_data.is_user_logged_in
                }));
                
                // Set timeout in case WebSocket fails
                setTimeout(function() {
                    if (document.getElementById('typing-indicator')) {
                        sendMessageViaRest(message);
                    }
                }, 5000);
            } else {
                sendMessageViaRest(message);
            }
        }
        
        function sendMessageViaRest(message) {
            $.ajax({
                url: chatbot_data.rest_url + '/send_message',
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', chatbot_data.nonce);
                },
                data: {
                    message: message,
                    history: JSON.stringify(messageHistory),
                    user_id: chatbot_data.current_user_id,
                    user_ip: chatbot_data.current_user_ip,
                    visitor_id: chatbot_data.visitor_id,
                    is_user_logged_in: chatbot_data.is_user_logged_in
                },
                success: function(response) {
                    removeTypingIndicator();
                   
                    if (response.success) {
                        addMessage(response.data.reply, false);
                        messageHistory.push({ role: 'assistant', content: response.data.reply });
                    } else {
                        addMessage('I apologize, but I\'m having trouble processing your request right now. Please try again in a moment.', false);
                    }
                },
                error: function(xhr, status, error) {
                    removeTypingIndicator();
                    addMessage('I\'m experiencing some technical difficulties. Please try again later or contact our support team directly.', false);
                    console.error('Failed to send message:', error);
                }
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

// Register REST API endpoints
add_action('rest_api_init', 'register_chatbot_rest_routes');

function register_chatbot_rest_routes() {
    register_rest_route('chatbot/v1', '/send_message', array(
        'methods' => 'POST',
        'callback' => 'handle_chatbot_rest_response',
        'permission_callback' => function() {
            return current_user_can('read') || true; // Allow for guests
        }
    ));
    
    register_rest_route('chatbot/v1', '/check_responses', array(
        'methods' => 'GET',
        'callback' => 'handle_chatbot_check_responses',
        'permission_callback' => function() {
            return current_user_can('read') || true; // Allow for guests
        }
    ));
}

// REST handler for chatbot responses
function handle_chatbot_rest_response(WP_REST_Request $request) {
    $parameters = $request->get_params();
    
    $user_message = sanitize_text_field($parameters['message']);
    $history = json_decode(stripslashes($parameters['history']), true);
    $user_id = isset($parameters['user_id']) ? intval($parameters['user_id']) : 0;
    $user_ip = isset($parameters['user_ip']) ? sanitize_text_field($parameters['user_ip']) : '';
    $visitor_id = isset($parameters['visitor_id']) ? sanitize_text_field($parameters['visitor_id']) : '';
    $is_user_logged_in = isset($parameters['is_user_logged_in']) ? (bool)$parameters['is_user_logged_in'] : false;
    
    if (empty($user_message)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Empty message'
        ), 400);
    }
    
    // Store the conversation in database
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'user_ip' => $user_ip,
            'visitor_id' => $visitor_id,
            'message' => $user_message,
            'status' => 'pending',
            'created_at' => current_time('mysql')
        )
    );
    
    $conversation_id = $wpdb->insert_id;
    
    // Check if we should respond with AI or wait for human
    $use_ai = get_option('chatbot_use_ai', 1); // Default to AI
    
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
            
            $response_data = ['reply' => $ai_response];
            if ($wp_action) {
                $response_data['wp_action'] = $wp_action;
            }
            return new WP_REST_Response(array(
                'success' => true,
                'data' => $response_data
            ));
        }
        
        // If AI fails, use enhanced fallback system
        $fallback_response = get_fallback_response($user_message);
        $response_data = ['reply' => $fallback_response];
        if ($wp_action) {
            $response_data['wp_action'] = $wp_action;
        }
        
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
        
        return new WP_REST_Response(array(
            'success' => true,
            'data' => $response_data
        ));
    } else {
        // Notify admin about new message waiting for response
        notify_admin_new_message($conversation_id, $user_message);
        
        return new WP_REST_Response(array(
            'success' => true,
            'data' => [
                'reply' => 'Thank you for your message! Our support team has been notified and will respond shortly.'
            ]
        ));
    }
}

// REST handler for checking new responses
function handle_chatbot_check_responses(WP_REST_Request $request) {
    $parameters = $request->get_params();
    
    $user_id = isset($parameters['user_id']) ? intval($parameters['user_id']) : 0;
    $user_ip = isset($parameters['user_ip']) ? sanitize_text_field($parameters['user_ip']) : '';
    $visitor_id = isset($parameters['visitor_id']) ? sanitize_text_field($parameters['visitor_id']) : '';
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    // Get unread responses for this user (using either user_id, user_ip, or visitor_id)
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE 
        (user_id = %d OR user_ip = %s OR visitor_id = %s)
        AND status = 'responded' 
        AND is_ai_response = 0 
        AND is_read = 0 
        ORDER BY responded_at ASC",
        $user_id, $user_ip, $visitor_id
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
        
        return new WP_REST_Response(array(
            'success' => true,
            'data' => [
                'messages' => array_map(function($response) {
                    return [
                        'type' => 'chat_message',
                        'content' => $response->response,
                        'timestamp' => $response->responded_at,
                        'is_user' => false
                    ];
                }, $responses)
            ]
        ));
    } else {
        return new WP_REST_Response(array(
            'success' => true,
            'data' => ['messages' => []]
        ));
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
   
    foreach ($actions as $trigger => $action) {
        if (strpos($lower_message, $trigger) !== false) {
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
        return false;
    }
   
    // Enhanced system prompt with WordPress integration
    $system_prompt = 'You are a helpful customer service assistant. Your tone should always be friendly, professional, and solution-oriented. Offer step-by-step help and encourage users, especially beginners, with clear and easy-to-follow language.';
   
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
    
    // Handle bulk actions
    if (isset($_POST['bulk_action']) && isset($_POST['selected_conversations'])) {
        $action = sanitize_text_field($_POST['bulk_action']);
        $selected = array_map('intval', $_POST['selected_conversations']);
        
        if (!empty($selected)) {
            if ($action === 'delete') {
                $wpdb->query("DELETE FROM $table_name WHERE id IN (" . implode(',', $selected) . ")");
                echo '<div class="notice notice-success"><p>Selected conversations deleted successfully.</p></div>';
            } elseif ($action === 'mark_responded') {
                $wpdb->query("UPDATE $table_name SET status = 'responded', responded_at = '" . current_time('mysql') . "' WHERE id IN (" . implode(',', $selected) . ")");
                echo '<div class="notice notice-success"><p>Selected conversations marked as responded.</p></div>';
            } elseif ($action === 'mark_pending') {
                $wpdb->query("UPDATE $table_name SET status = 'pending', responded_at = NULL WHERE id IN (" . implode(',', $selected) . ")");
                echo '<div class="notice notice-success"><p>Selected conversations marked as pending.</p></div>';
            }
        }
    }
    
    // Handle single delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($table_name, array('id' => $id));
        echo '<div class="notice notice-success"><p>Conversation deleted successfully.</p></div>';
    }
    
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
    
    // Handle filters
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $visitor_filter = isset($_GET['visitor']) ? sanitize_text_field($_GET['visitor']) : '';
    
    // Build WHERE clause
    $where = array();
    $params = array();
    
    if ($status_filter != 'all') {
        $where[] = "status = %s";
        $params[] = $status_filter;
    }
    
    if (!empty($search_query)) {
        $where[] = "(message LIKE %s OR response LIKE %s)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }
    
    if (!empty($visitor_filter)) {
        $where[] = "visitor_id = %s";
        $params[] = $visitor_filter;
    }
    
    $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    // Pagination
    $items_per_page = 20;
    $total_items = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name $where_sql",
        $params
    ));
    $total_pages = ceil($total_items / $items_per_page);
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;
    
    // Get conversations
    $conversations = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d",
        array_merge($params, array($items_per_page, $offset))
    ));
    
    // Get visitor IDs for filter
    $visitors = $wpdb->get_results(
        "SELECT DISTINCT visitor_id FROM $table_name ORDER BY created_at DESC LIMIT 100"
    );
    
    // Get counts for each status
    $all_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $pending_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
    $responded_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'responded'");
    
    // Get conversation to display (first one or selected one)
    $display_conv = null;
    if (isset($_GET['id'])) {
        $display_conv = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            intval($_GET['id'])
        ));
    } elseif (!empty($conversations)) {
        $display_conv = reset($conversations);
    }
    ?>
    <div class="wrap">
        <h1>Chatbot Conversations</h1>
        
        <div class="chatbot-admin-container">
            <div class="conversation-filters">
                <form method="get" action="">
                    <input type="hidden" name="page" value="chatbot-conversations">
                    
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status-filter">Status:</label>
                            <select id="status-filter" name="status" class="postform">
                                <option value="all" <?php selected($status_filter, 'all'); ?>>All (<?php echo $all_count; ?>)</option>
                                <option value="pending" <?php selected($status_filter, 'pending'); ?>>Pending (<?php echo $pending_count; ?>)</option>
                                <option value="responded" <?php selected($status_filter, 'responded'); ?>>Responded (<?php echo $responded_count; ?>)</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="visitor-filter">Visitor:</label>
                            <select id="visitor-filter" name="visitor" class="postform">
                                <option value="">All Visitors</option>
                                <?php foreach ($visitors as $visitor): ?>
                                    <option value="<?php echo esc_attr($visitor->visitor_id); ?>" <?php selected($visitor_filter, $visitor->visitor_id); ?>>
                                        <?php echo esc_html(substr($visitor->visitor_id, 0, 8) . '...'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="search-input">Search:</label>
                            <input type="search" id="search-input" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="Search messages...">
                        </div>
                        
                        <div class="filter-group">
                            <input type="submit" class="button" value="Filter">
                            <?php if ($status_filter != 'all' || !empty($search_query) || !empty($visitor_filter)): ?>
                                <a href="?page=chatbot-conversations" class="button">Reset</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="conversation-list-container">
                <form method="post" action="">
                    <div class="tablenav top">
                        <div class="alignleft actions bulkactions">
                            <select name="bulk_action" class="bulk-action-select">
                                <option value="">Bulk Actions</option>
                                <option value="delete">Delete</option>
                                <option value="mark_responded">Mark as Responded</option>
                                <option value="mark_pending">Mark as Pending</option>
                            </select>
                            <input type="submit" class="button action" value="Apply">
                        </div>
                        
                        <div class="tablenav-pages">
                            <?php if ($total_pages > 1): ?>
                                <span class="displaying-num"><?php printf('%s items', $total_items); ?></span>
                                <span class="pagination-links">
                                    <?php
                                    $page_links = paginate_links(array(
                                        'base' => add_query_arg('paged', '%#%'),
                                        'format' => '',
                                        'prev_text' => '&laquo;',
                                        'next_text' => '&raquo;',
                                        'total' => $total_pages,
                                        'current' => $current_page
                                    ));
                                    echo $page_links;
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <td class="manage-column column-cb check-column">
                                    <input type="checkbox" id="cb-select-all">
                                </td>
                                <th>Visitor ID</th>
                                <th>User</th>
                                <th>Message Preview</th>
                                <th>Response Preview</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($conversations)): ?>
                                <tr>
                                    <td colspan="8" class="no-conversations">No conversations found matching your criteria.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($conversations as $conv): ?>
                                <tr <?php if ($display_conv && $display_conv->id == $conv->id) echo 'class="active"'; ?>>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="selected_conversations[]" value="<?php echo $conv->id; ?>">
                                    </th>
                                    <td>
                                        <a href="?page=chatbot-conversations&visitor=<?php echo urlencode($conv->visitor_id); ?>">
                                            <?php echo esc_html(substr($conv->visitor_id, 0, 8) . '...'); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($conv->user_id) {
                                            $user = get_user_by('id', $conv->user_id);
                                            echo $user ? $user->display_name : 'Guest';
                                        } else {
                                            echo 'Guest ('. $conv->user_ip .')';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo wp_trim_words($conv->message, 10); ?></td>
                                    <td><?php echo $conv->response ? wp_trim_words($conv->response, 10) : '—'; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $conv->status; ?>">
                                            <?php echo ucfirst($conv->status); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y g:i a', strtotime($conv->created_at)); ?></td>
                                    <td>
                                        <a href="?page=chatbot-conversations&id=<?php echo $conv->id; ?>" class="button view-conversation">View</a>
                                        <a href="?page=chatbot-conversations&action=delete&id=<?php echo $conv->id; ?>" class="button delete-conversation" onclick="return confirm('Are you sure you want to delete this conversation?');">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>
            
            <?php if ($display_conv): ?>
                <div class="conversation-detail">
                    <div id="conversation-<?php echo $display_conv->id; ?>" class="conversation-panel">
                        <div class="conversation-header">
                            <h2>Conversation with <?php echo esc_html($display_conv->visitor_id); ?></h2>
                            <div class="conversation-meta">
                                <span class="meta-item">
                                    <strong>User:</strong> 
                                    <?php 
                                    if ($display_conv->user_id) {
                                        $user = get_user_by('id', $display_conv->user_id);
                                        echo $user ? $user->display_name : 'Guest';
                                    } else {
                                        echo 'Guest';
                                    }
                                    ?>
                                </span>
                                <span class="meta-item">
                                    <strong>IP:</strong> <?php echo esc_html($display_conv->user_ip); ?>
                                </span>
                                <span class="meta-item">
                                    <strong>Status:</strong> 
                                    <span class="status-badge status-<?php echo $display_conv->status; ?>">
                                        <?php echo ucfirst($display_conv->status); ?>
                                    </span>
                                </span>
                                <span class="meta-item">
                                    <strong>Started:</strong> <?php echo date('M j, Y g:i a', strtotime($display_conv->created_at)); ?>
                                </span>
                                <?php if ($display_conv->responded_at): ?>
                                    <span class="meta-item">
                                        <strong>Responded:</strong> <?php echo date('M j, Y g:i a', strtotime($display_conv->responded_at)); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="message-history">
                            <div class="message user-message">
                                <div class="message-content">
                                    <div class="message-text"><?php echo wpautop(esc_textarea($display_conv->message)); ?></div>
                                    <div class="message-time"><?php echo date('M j, Y g:i a', strtotime($display_conv->created_at)); ?></div>
                                </div>
                            </div>
                            
                            <?php if ($display_conv->response): ?>
                            <div class="message admin-message">
                                <div class="message-content">
                                    <div class="message-text"><?php echo wpautop(esc_textarea($display_conv->response)); ?></div>
                                    <div class="message-time"><?php echo date('M j, Y g:i a', strtotime($display_conv->responded_at)); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <form method="post" class="response-form">
                            <input type="hidden" name="conversation_id" value="<?php echo $display_conv->id; ?>">
                            <textarea name="response" rows="5" placeholder="Type your response here..." required><?php echo $display_conv->response; ?></textarea>
                            <div class="response-actions">
                                <button type="submit" name="send_response" class="button button-primary"><?php echo $display_conv->response ? 'Update Response' : 'Send Response'; ?></button>
                                <button type="button" class="button button-secondary use-ai-response">Use AI Response</button>
                                <a href="?page=chatbot-conversations&action=delete&id=<?php echo $display_conv->id; ?>" class="button button-link-delete" onclick="return confirm('Are you sure you want to delete this conversation?');">Delete Conversation</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
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
            padding: 20px;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .filter-group {
    box-sizing: border-box;
    position: relative;
    float: left;
    margin: 0 1% 0 0;
    padding: 20px 17px 13px;
    width: 31%;
    background: #fff;
    border: 1px solid #dcdcde;
    box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
}
        .filter-group label {
            font-weight: 500;
        }
        
        .conversation-list-container {
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
        
        tr.active {
            background-color: #f0f6fc !important;
        }
        
        .conversation-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .conversation-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
            font-size: 13px;
            color: #646970;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
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
            align-items: center;
        }
        
        .button-link-delete {
            color: #b32d2e;
            text-decoration: none;
        }
        
        .button-link-delete:hover {
            color: #8a2424;
        }
        
        .no-conversations {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .bulk-action-select {
            margin-right: 5px;
        }
        
        @media screen and (min-width: 1200px) {
            .chatbot-admin-container {
                flex-direction: row;
                flex-wrap: wrap;
            }
            
            .conversation-list-container {
                flex: 0 0 100%;
            }
            
            .conversation-detail {
                flex: 1;
                margin-top: 20px;
            }
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Bulk select all checkbox
            $('#cb-select-all').on('change', function() {
                $('input[name="selected_conversations[]"]').prop('checked', $(this).prop('checked'));
            });
            
            // AI response generation
            $('.use-ai-response').on('click', function() {
                var message = $(this).closest('.conversation-panel').find('.user-message .message-text').text();
                var textarea = $(this).closest('form').find('textarea');
                
                textarea.val('Generating AI response...').prop('disabled', true);
                
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'chatbot_response',
                        message: message,
                        history: JSON.stringify([{role: 'user', content: message}]),
                        nonce: '<?php echo wp_create_nonce('chatbot_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            textarea.val(response.data.reply);
                        } else {
                            textarea.val('Error generating AI response');
                        }
                        textarea.prop('disabled', false);
                    }
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
        add_option('chatbot_api_url', '');
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
        user_id varchar(100) NOT NULL,
        user_ip varchar(45) NOT NULL,
        visitor_id varchar(100) NOT NULL,
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