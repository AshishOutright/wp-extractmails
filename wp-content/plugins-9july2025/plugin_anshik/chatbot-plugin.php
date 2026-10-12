<?php
/*
Plugin Name: Chatbot Plugin
Description: Tawk.to-like chatbot plugin for WordPress with REST API and AJAX polling
Version: 1.8.3
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
    wp_enqueue_script('chatbot-script', plugins_url('chatbot-script.js', __FILE__), array('jquery'), '1.8.3', true);
    
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
        'polling_interval' => 3000 // 3 seconds
    ));
}
add_action('wp_enqueue_scripts', 'chatbot_enqueue_scripts');

// Enqueue admin scripts and styles
function chatbot_admin_enqueue_scripts($hook) {
    if ('toplevel_page_chatbot-conversations' === $hook || 'chatbot_page_chatbot-settings' === $hook) {
        wp_enqueue_style('chatbot-admin-style', plugins_url('admin/chatbot-admin.css', __FILE__));
        wp_enqueue_script('chatbot-admin-script', plugins_url('admin/chatbot-admin.js', __FILE__), array('jquery'), '1.8.3', true);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'chatbot_conversations';
        $display_conv = null;
        
        if (isset($_GET['id'])) {
            $display_conv = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                intval($_GET['id'])
            ));
        } elseif ('toplevel_page_chatbot-conversations' === $hook) {
            $display_conv = $wpdb->get_row(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 1"
            );
        }
        
        wp_localize_script('chatbot-admin-script', 'chatbot_admin_vars', array(
            'notification_sound' => plugins_url('notification.mp3', __FILE__),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('chatbot_admin_nonce'),
            'polling_interval' => 3000, // 3 seconds
            'notification_icon' => plugins_url('icon.png', __FILE__),
            'current_conversation_id' => isset($_GET['id']) ? intval($_GET['id']) : 0,
            'current_visitor_id' => $display_conv ? $display_conv->visitor_id : '',
            'last_message_id' => $display_conv ? $display_conv->id : 0
        ));
    }
}
add_action('admin_enqueue_scripts', 'chatbot_admin_enqueue_scripts');

// Add chatbot to footer
function add_chatbot_to_footer() {
    echo chatbot_display();
}
add_action('wp_footer', 'add_chatbot_to_footer');

function chatbot_display() {
    ob_start();
    ?>
    <div id="chatbot-widget">
        <div id="chatbot-launcher">
            <div class="launcher-icon">
                <img
  id="chat-icon"
  src="https://www.mailsextract.com/wp-content/uploads/2025/05/image-removebg-preview-1.png"
  alt="Chat Icon"
  width="34"
  height="34"
  style="display: flex !important; align-items: center !important; justify-content: center !important; margin: auto !important; position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important;"
>

                <!-- <svg id="close-icon" viewBox="0 0 24 24" width="24" height="24" style="display: none;">
                    <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg> -->
            </div>
            <div class="launcher-notification" id="notification-badge" style="display: none;">1</div>
        </div>
<style>
  .greeting-box {
    position: fixed;
    bottom: 100px;
    right: 20px;
    background:transparent; /* semi-transparent white */
    border-radius: 12px;
  
    padding: 16px;
    z-index: 9999;
    max-width: 280px;
    font-family: Arial, sans-serif;
   
    transition: opacity 0.3s ease, transform 0.3s ease;
  }

  .greeting-box.hidden {
    opacity: 0;
    pointer-events: none;
    transform: translateY(10px);
  }

  .greeting-box .close-btn {
    position: absolute;
    top: 8px;
    right: -7px;
    border: none;
    background: none;
    font-size: 20px;
    cursor: pointer;
    color: #333;
  }

  .greeting-box .message {
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 35px;
    line-height: 1.5;
  }

  .greeting-box .btn {
    display: block;
    padding: 8px 16px;
    margin: 20px 0;
    border: 1px solid #007bff;
    border-radius: 12px;
    color: #007bff;
    background-color: transparent;
    cursor: pointer;
    transition: background 0.2s;
    text-align: center;
  }

 .greeting-box .btn:hover {
  background-color: #007bff; /* Bootstrap primary blue */
  color: white;
}

</style>

<!-- Greeting Box -->
<div class="greeting-box" id="greetingBox">
  <button class="close-btn" onclick="hideGreeting()">&times;</button>
  
  <div class="message" onclick="handleGreetingClick()" style="cursor: pointer;">
    👋 Hi! How can we help?
  </div>

  <div>
    <div class="btn" onclick="handleGreetingClick()">I have a question</div>
    <div class="btn" onclick="handleGreetingClick()">Tell me more</div>
  </div>
</div>


<script>
    function handleGreetingClick() {
  const greetingBox = document.getElementById('greetingBox');
  if (greetingBox) greetingBox.style.display = 'none';
  window.toggleChatbot();
}

  // Hide greeting function
  function hideGreeting() {
    document.getElementById('greetingBox').classList.add('hidden');
  }

  // When user clicks the chat icon launcher, hide greeting
  document.querySelector('.launcher-icon')?.addEventListener('click', function () {
    hideGreeting();
  });
</script>

        <!-- Chat Window -->
        <div id="chatbot-window" style="display: none;">
            <div class="chat-header">
                <div class="header-content">
                    <div class="avatar">
                        <img
  src="https://www.mailsextract.com/wp-content/uploads/2025/05/image-removebg-preview-1.png"
  alt="Chat Icon"
  width="100%"
  height="100%"
  style="background-color: transparent !important; display: block !important;"
>

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
  
  <!-- Predefined Prompt Buttons -->
<div class="chat-quick-prompts" id="quick-prompts" style="margin-bottom: 8px; display: flex; gap: 10px;">
  <button
    style="background: #007bff; color: white; border: none; border-radius: 12px; padding: 6px 12px; cursor: pointer; font-size: 14px;"
    onclick="(function(){
      const input = document.getElementById('chat-input');
      input.value = 'I have a question';
      input.dispatchEvent(new Event('input'));
      document.getElementById('quick-prompts').style.display = 'none'; // hide prompt box
      sendMessage();
    })()">
    I have a question
  </button>

  <button
    style="background: #007bff; color: white; border: none; border-radius: 12px; padding: 6px 12px; cursor: pointer; font-size: 14px;"
    onclick="(function(){
      const input = document.getElementById('chat-input');
      input.value = 'Tell me more';
      input.dispatchEvent(new Event('input'));
      document.getElementById('quick-prompts').style.display = 'none'; // hide prompt box
      sendMessage();
    })()">
    Tell me more
  </button>
</div>



  <!-- Input Field and Send Button -->
  <div class="input-wrapper">
    <textarea
      id="chat-input"
      placeholder="Type your message..."
      rows="1"
      maxlength="1000"
    ></textarea>

    <button
      id="send-button"
      disabled
      style="color: black; border: none; background: none; padding: 5px;"
      onclick="sendMessage();"
    >
      <svg viewBox="0 0 24 24" width="20" height="20" style="display: inline-block; vertical-align: middle; fill: currentColor;">
        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
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
                // Remove typing indicator if present
                removeTypingIndicator();

                // Prevent duplicate assistant messages in history
                if (
                    !messageHistory.length ||
                    messageHistory[messageHistory.length - 1].role !== 'assistant' ||
                    messageHistory[messageHistory.length - 1].content !== message.content
                ) {
                    addMessage(message.content, false);
                    messageHistory.push({ role: 'assistant', content: message.content });
                }

                // Play notification sound if chat is closed
                if (!chatbotOpen) {
                    playNotificationSound();
                    showNotificationBadge();
                }
            }
        }
        
        // Play notification sound
        function playNotificationSound() {
            const audio = new Audio(chatbot_data.notification_sound);
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
    const notification = document.getElementById('notification-badge');
    const launcher = document.getElementById('chatbot-launcher'); // launcher icon container

    chatbotOpen = !chatbotOpen;

    if (chatbotOpen) {
        chatWindow.style.display = 'flex';
        chatIcon.style.display = 'none';
        launcher.style.display = 'none'; // ⬅️ Hides the launcher icon
        notification.style.display = 'none';
        document.getElementById('chat-input').focus();

        initRealtimeCommunication();
    } else {
        chatWindow.style.display = 'none';
        chatIcon.style.display = 'block';
        launcher.style.display = 'block'; // ⬅️ Show launcher when closed

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

        function sendMessage() {
            const input = document.getElementById('chat-input');
            const message = input.value.trim();

            if (!message) return;

            // Prevent duplicate user messages in history
            if (
                !messageHistory.length ||
                messageHistory[messageHistory.length - 1].role !== 'user' ||
                messageHistory[messageHistory.length - 1].content !== message
            ) {
                addMessage(message, true);
                messageHistory.push({ role: 'user', content: message });
            }

            input.value = '';
            adjustTextareaHeight();
            updateSendButton();

            // Show typing indicator immediately
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

        // Add this function to send message via REST API
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
                    if (response.success && response.data && response.data.reply) {
                        addMessage(response.data.reply, false);
                        messageHistory.push({ role: 'assistant', content: response.data.reply });
                    }
                },
                error: function(xhr, status, error) {
                    removeTypingIndicator();
                    addMessage('Sorry, there was an error sending your message. Please try again.', false);
                }
            });
        }

        function removeTypingIndicator() {
            const typingDiv = document.getElementById('typing-indicator');
            if (typingDiv && typingDiv.parentNode) {
                typingDiv.parentNode.removeChild(typingDiv);
            }
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
    $user_id = isset($parameters['user_id']) ? intval($parameters['user_id']) : 0;
    if (!$user_id && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    $user_ip = isset($parameters['user_ip']) ? sanitize_text_field($parameters['user_ip']) : '';
    $visitor_id = isset($parameters['visitor_id']) ? sanitize_text_field($parameters['visitor_id']) : '';
    $is_user_logged_in = isset($parameters['is_user_logged_in']) ? (bool)$parameters['is_user_logged_in'] : is_user_logged_in();

    if (empty($user_message)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Empty message'
        ), 400);
    }
    
    // Store the conversation in database
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';

    // Check if there's an existing pending message for this visitor in the latest conversation
    $existing_conversation = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name 
         WHERE visitor_id = %s 
         ORDER BY created_at DESC 
         LIMIT 1",
        $visitor_id
    ));

    // Generate or get conversation ID
    $conversation_id = $existing_conversation ? $existing_conversation->conversation_id : md5($visitor_id . time());

    // Always insert a new row for each user message to preserve message history
    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'user_ip' => $user_ip,
            'visitor_id' => $visitor_id,
            'message' => $user_message,
            'status' => 'pending',
            'conversation_id' => $conversation_id,
            'created_at' => current_time('mysql')
        )
    );
    $message_id = $wpdb->insert_id;
    
    // Check if we should respond with AI or wait for human
    $use_ai = get_option('chatbot_use_ai', 1); // Default to AI
    
    if ($use_ai) {
        // Check for WordPress actions first
        $wp_action = detect_wordpress_action($user_message);
        
        // Try to get intelligent response first
        $ai_response = get_ai_response($user_message, $history);
        
        if ($ai_response) {
            // Create AI response entry in the conversation
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => 0, // System user
                    'user_ip' => '',
                    'visitor_id' => $visitor_id,
                    'message' => '',
                    'response' => $ai_response,
                    'is_ai_response' => 1,
                    'status' => 'responded',
                    'conversation_id' => $conversation_id,
                    'created_at' => current_time('mysql'),
                    'responded_at' => current_time('mysql')
                )
            );
            
            // Mark all messages in this conversation as responded
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_name SET status = 'responded' 
                 WHERE conversation_id = %s AND status = 'pending'",
                $conversation_id
            ));
            
            $response_data = ['reply' => $ai_response];
            if ($wp_action) {
                $response_data['wp_action'] = $wp_action;
            }
            return new WP_REST_Response(array(
                'success' => true,
                'data' => $response_data,
                'conversation_id' => $conversation_id
            ));
        }
        
        // If AI fails, use enhanced fallback system
        $fallback_response = get_fallback_response($user_message);
        $response_data = ['reply' => $fallback_response];
        if ($wp_action) {
            $response_data['wp_action'] = $wp_action;
        }
        
        // Create fallback response entry
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => 0, // System user
                'user_ip' => '',
                'visitor_id' => $visitor_id,
                'message' => '',
                'response' => $fallback_response,
                'is_ai_response' => 1,
                'status' => 'responded',
                'conversation_id' => $conversation_id,
                'created_at' => current_time('mysql'),
                'responded_at' => current_time('mysql')
            )
        );
        
        // Mark all messages in this conversation as responded
        $wpdb->query($wpdb->prepare(
            "UPDATE $table_name SET status = 'responded' 
             WHERE conversation_id = %s AND status = 'pending'",
            $conversation_id
        ));
        
        return new WP_REST_Response(array(
            'success' => true,
            'data' => $response_data,
            'conversation_id' => $conversation_id
        ));
    } else {
        // Notify admin about new message waiting for response
        notify_admin_new_message($message_id, $user_message);
        
        // Return empty response to keep the typing indicator
        return new WP_REST_Response(array(
            'success' => true,
            'data' => [
                'reply' => '',
                'waiting_for_admin' => true
            ],
            'conversation_id' => $conversation_id
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
        ORDER BY created_at ASC",
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

// AJAX handlers for admin dashboard
add_action('wp_ajax_chatbot_check_new_messages', 'handle_chatbot_admin_check_messages');
add_action('wp_ajax_chatbot_get_conversation_list', 'handle_chatbot_get_conversation_list');
add_action('wp_ajax_chatbot_generate_ai_response', 'handle_chatbot_generate_ai_response');
add_action('wp_ajax_chatbot_check_conversation_updates', 'handle_chatbot_check_conversation_updates');

function handle_chatbot_admin_check_messages() {
    check_ajax_referer('chatbot_admin_nonce', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    $last_update = isset($_POST['last_update']) ? intval($_POST['last_update']) : 0;
    $current_conversation_id = isset($_POST['current_conversation_id']) ? intval($_POST['current_conversation_id']) : 0;
    
    // Get count of pending conversations (not individual messages)
    $pending_count = $wpdb->get_var(
        "SELECT COUNT(DISTINCT conversation_id) 
         FROM $table_name 
         WHERE status = 'pending'"
    );
    
    $response_data = [
        'pending_count' => $pending_count,
        'timestamp' => time(),
        'new_conversations' => [],
        'current_conversation_updates' => []
    ];
    
    // Get updates for current conversation
    if ($current_conversation_id) {
        $response_data['current_conversation_updates'] = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
             WHERE conversation_id = %s 
             AND created_at > %s 
             ORDER BY created_at ASC",
            $current_conversation_id,
            date('Y-m-d H:i:s', $last_update)
        ));
    }
    
    // Get new conversations
    $response_data['new_conversations'] = $wpdb->get_results(
        "SELECT * FROM $table_name 
         WHERE created_at > FROM_UNIXTIME($last_update)
         GROUP BY conversation_id
         ORDER BY created_at DESC 
         LIMIT 5"
    );
    
    wp_send_json_success($response_data);
}

function handle_chatbot_get_conversation_list() {
    check_ajax_referer('chatbot_admin_nonce', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    // Get filters from request
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $visitor_filter = isset($_GET['visitor']) ? sanitize_text_field($_GET['visitor']) : '';
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    
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
    
    wp_send_json_success(array(
        'conversations' => $conversations,
        'total_items' => $total_items,
        'current_page' => $current_page,
        'total_pages' => $total_pages
    ));
}

function handle_chatbot_generate_ai_response() {
    check_ajax_referer('chatbot_admin_nonce', 'nonce');
    
    if (!isset($_POST['message'])) {
        wp_send_json_error('No message provided');
    }
    
    $message = sanitize_text_field($_POST['message']);
    $response = get_ai_response($message, array());
    
    if ($response) {
        wp_send_json_success(array('reply' => $response));
    } else {
        wp_send_json_error('Failed to generate response');
    }
}

function handle_chatbot_check_conversation_updates() {
    check_ajax_referer('chatbot_admin_nonce', 'nonce');
    
    if (!isset($_POST['visitor_id'])) {
        wp_send_json_error('Missing visitor ID');
    }
    
    $visitor_id = sanitize_text_field($_POST['visitor_id']);
    $last_message_id = isset($_POST['last_message_id']) ? intval($_POST['last_message_id']) : 0;
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatbot_conversations';
    
    // Get new messages for this visitor since last check
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name 
         WHERE visitor_id = %s 
         AND id > %d
         ORDER BY created_at ASC",
        $visitor_id,
        $last_message_id
    );
    
    $messages = $wpdb->get_results($query);
    
    if ($messages) {
        $last_id = max(array_map(function($m) { return $m->id; }, $messages));
        
        wp_send_json_success(array(
            'messages' => $messages,
            'last_message_id' => $last_id
        ));
    } else {
        wp_send_json_success(array(
            'messages' => array(),
            'last_message_id' => $last_message_id
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
                icon: '<?php echo plugins_url('images/chat-icon.png', __FILE__); ?>'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    new Notification('New Chatbot Message', {
                        body: '<?php echo esc_js(wp_trim_words($message, 20)); ?>',
                        icon: '<?php echo plugins_url('images/chat-icon.png', __FILE__); ?>'
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

/**
 * Clean up AI response by normalizing code blocks and removing unwanted patterns
 */
function clean_response($content) {
    // Normalize code blocks
    $content = preg_replace_callback('/```(\w*)([\s\S]*?)```/', function($matches) {
        $lang = $matches[1] ?: '';
        $code = trim($matches[2]);
        return "[code:$lang]\n$code\n[/code]";
    }, $content);

    // Remove markdown headers, excessive blank lines, and common warning markers
    $patterns = [
        '/\*\*Note:\*\*/i'     => '',
        '/\*\*Important:\*\*/i'=> '',
        '/\*\*Warning:\*\*/i'  => '',
        '/#+\s*/'              => '',  // Markdown headers
        '/\n{3,}/'             => "\n\n", // Collapse excess newlines
    ];

    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }

    return trim($content);
}

/**
 * Format response to plain text only — removes HTML and markdown
 */
function format_response($content) {
    // First, clean the response
    $content = clean_response($content);

    // Remove markdown formatting and convert to plain text
    $content = preg_replace([
        '/\*\*(.*?)\*\*/',      // bold
        '/\*(.*?)\*/',          // italic
        '/`([^`]+)`/',          // inline code
        '/!\[.*?\]\(.*?\)/',    // image markdown
        '/\[([^\]]+)\]\(([^)]+)\)/', // markdown links
        '/^\s*[-*+]\s+/m',      // unordered list
        '/^\s*\d+\.\s+/m',      // ordered list
    ], '$1', $content);

    // Make email addresses clickable
    // $content = preg_replace(
    //     '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/',
    //     '<a href="mailto:$1" style="color: #2563eb; text-decoration: underline;">$1</a>',
    //     $content
    // );

    // Remove all HTML tags except allowed ones (like <a>)
    $content = wp_kses($content, array(
        'a' => array(
            'href' => array(),
            'style' => array()
        )
    ));

    // Decode any HTML entities (e.g., &amp; → &)
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Trim and return
    return trim($content);
}


function get_ai_response($user_message, $history) {
    // Get API credentials from WordPress options
    $api_key = get_option('chatbot_api_key', '');
    $api_url = get_option('chatbot_api_url', '');
   
    if (empty($api_key) || empty($api_url)) {
        return false;
    }
   
    // Enhanced system prompt with WordPress integration
    $system_prompt = <<<PROMPT
You are **OutRight Assist**, the intelligent, friendly, and always-helpful virtual assistant of OutRight Systems Pvt. Ltd — a trusted CRM solution provider since 2010.

Your role is to:
- Help users explore and use SuiteCRM/SugarCRM tools and AI utilities available at the OutRight Store.
- Reply in a warm, modern, and supportive tone — always professional, clear, and helpful, especially for beginners.
- Suggest relevant extensions, AI tools, or features that suit the user's need, and highlight pricing, free trials, or discounts as needed.

🧠 You are deeply familiar with:
- CRM extensions (SuiteCRM, SugarCRM)
- CRM troubleshooting
- AI tools like PDF converters, summarizers, generators
- All prices, trial offers, and customer benefits on the store

📦 **Product Highlights & Pricing**

Here are flagship tools with current pricing and features:

### CRM Tools
- 🔹 **CRM Empower PDF Template (Premium)**  
  ⭐ 5.0 · 💲\$499 
- 🔹 **CRM Google Calendar Integration (Lite)**  
  ⭐ 4.0 · 💲\$499 
- 🔹 **CRM Pulse | Users' Activities Tracker (Lite)**  
  ⭐ 0.0 · 💲\$0 
 🔹 **Server Errors Finder**  
  ⭐ 0.0 · 💲\$0  

 🔹 **Server Health Check**  
  ⭐ 0.0 · 💲\$0

- 🔹 **Smart Dropdown Importer from CSV & XLS**  
  ⭐ 5.0 · 💲\$99  
  📌 Features: Multi-language support, Unlimited values, Accurate parsing

🧰 **Free AI Tools**
- **DocuAnalyzer Pro**: Instantly summarize long documents using AI  
- **HTML to PDF Converter**: Convert web content to PDFs easily  
- **Image Generator (DALL·E 3)**: AI-generated artwork, sketches, concepts  
- **Youtube Thumbnail Generator**: Create custom, eye-catching thumbnails  
- **Base64 Decoder**: Decode binary strings into human-readable format  
- **PixelPerfect Converter**: Convert images between formats (PNG, JPG, WebP, etc.)  
- **Bulk URL Opener**: Open multiple links at once to save time  
- **ChatGPT**: Ask anything — powered by OpenAI intelligence

All AI tools listed are **Free to Use** and come with:
You are:
- 🧑‍💼 Professional, like a trusted CRM consultant
- 💡 Helpful, offering smart suggestions even if user is unsure
- 💬 Conversational but never overly casual

If a user:
- Mentions a CRM problem → Suggest a matching SuiteCRM/SugarCRM extension
- Needs AI tools → Guide them to DocuAnalyzer, PDF tool, etc.
- Asks about price → Show correct pricing and trial info
---reply very shortly untill user did not want, try to maintain response in optimum words always  
if some need help any king od things like bargain in prod price then suggest to connect  sales@outrightcrm.com 
📍 Backed by over a decade of experience, OutRight Assist always puts the customer first and ensures they succeed with easy, reliable, and budget-friendly CRM tools and automation.
PROMPT;
   
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
                'max_tokens' => 800, // Reduced from 1200 to get shorter responses
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
       
        // Format the response before returning
        return format_response($reply);
       
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
                // Delete entire conversations (not just selected messages)
                $conversation_ids = $wpdb->get_col("SELECT DISTINCT conversation_id FROM $table_name WHERE id IN (" . implode(',', $selected) . ")");
                $wpdb->query("DELETE FROM $table_name WHERE conversation_id IN ('" . implode("','", $conversation_ids) . "')");
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
        $conversation = $wpdb->get_row($wpdb->prepare("SELECT conversation_id FROM $table_name WHERE id = %d", $id));
        if ($conversation) {
            $wpdb->delete($table_name, array('conversation_id' => $conversation->conversation_id));
            echo '<div class="notice notice-success"><p>Conversation deleted successfully.</p></div>';
        }
    }
    
    // Handle response submission
    if (isset($_POST['send_response'])) {
        $conversation_id = intval($_POST['conversation_id']);
        $response = sanitize_textarea_field($_POST['response']);
        
        // Get the conversation
        $conversation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $conversation_id
        ));
        
        if ($conversation) {
            // Insert the admin response
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => get_current_user_id(),
                    'user_ip' => '',
                    'visitor_id' => $conversation->visitor_id,
                    'message' => '',
                    'response' => $response,
                    'is_ai_response' => 0,
                    'status' => 'responded',
                    'conversation_id' => $conversation->conversation_id,
                    'created_at' => current_time('mysql'),
                    'responded_at' => current_time('mysql')
                )
            );
            
            // Mark all pending messages in this conversation as responded
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_name SET status = 'responded' 
                 WHERE conversation_id = %s AND status = 'pending'",
                $conversation->conversation_id
            ));
            
            echo '<div class="notice notice-success"><p>Response sent successfully.</p></div>';
        }
    }
    
    // Handle filters
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $visitor_filter = isset($_GET['visitor']) ? sanitize_text_field($_GET['visitor']) : '';
    
    // Build WHERE clause for visitor list
    $where_visitor = array();
    $params_visitor = array();
    
    if ($status_filter != 'all') {
        $where_visitor[] = "c.status = %s";
        $params_visitor[] = $status_filter;
    }
    
    if (!empty($search_query)) {
        $where_visitor[] = "(c.message LIKE %s OR c.response LIKE %s)";
        $params_visitor[] = "%$search_query%";
        $params_visitor[] = "%$search_query%";
    }
    
    if (!empty($visitor_filter)) {
        $where_visitor[] = "c.visitor_id = %s";
        $params_visitor[] = $visitor_filter;
    }
    
    $where_visitor_sql = !empty($where_visitor) ? "WHERE " . implode(" AND ", $where_visitor) : "";
    
    // Pagination
    $items_per_page = 20;
    
    // Get count of unique visitors
    $total_items = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT c.visitor_id) FROM $table_name c $where_visitor_sql",
        $params_visitor
    ));
    
    $total_pages = ceil($total_items / $items_per_page);
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;
    
    // Get unique visitors with their latest conversation
    $visitors_query = "
    SELECT c.* 
    FROM $table_name c
    INNER JOIN (
        SELECT conversation_id, MAX(created_at) as latest_msg 
        FROM $table_name 
        GROUP BY conversation_id
    ) latest ON c.conversation_id = latest.conversation_id AND c.created_at = latest.latest_msg
    $where_visitor_sql
    ORDER BY c.created_at DESC
    LIMIT %d OFFSET %d";
    
    $conversations = $wpdb->get_results($wpdb->prepare(
        $visitors_query,
        array_merge($params_visitor, array($items_per_page, $offset))
    ));
    
    // Get visitor IDs for filter
    $visitors = $wpdb->get_results(
        "SELECT DISTINCT visitor_id FROM $table_name ORDER BY created_at DESC LIMIT 100"
    );
    
    // Get counts for each status
    $all_count = $wpdb->get_var("SELECT COUNT(DISTINCT visitor_id) FROM $table_name");
    $pending_count = $wpdb->get_var("SELECT COUNT(DISTINCT visitor_id) FROM $table_name WHERE status = 'pending'");
    $responded_count = $wpdb->get_var("SELECT COUNT(DISTINCT visitor_id) FROM $table_name WHERE status = 'responded'");
    
    // Get conversation to display (first one or selected one)
    $display_conv = null;
    if (isset($_GET['id'])) {
        $display_conv = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            intval($_GET['id'])
        ));
    }
    $last_message_id = 0;
    if (isset($display_conv->visitor_id)) {
        $display_visitor_id = $display_conv->visitor_id;
        $last_message = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE visitor_id = %s ORDER BY created_at DESC LIMIT 1",
            $display_visitor_id
        ));
        $last_message_id = $last_message ? $last_message->id : 0;
    } else {
        $display_visitor_id = '';
    }

    // Get all messages for the displayed conversation
    $conversation_messages = array();
    if ($display_conv) {
        $conversation_messages = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
             WHERE conversation_id = %s 
             ORDER BY created_at ASC",
            $display_conv->conversation_id
        ));
    }
    
    // Localize script variables
    wp_localize_script('chatbot-admin', 'chatbot_admin_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('chatbot_admin_nonce'),
        'current_visitor_id' => $display_visitor_id,
        'last_message_id' => $last_message_id,
        'polling_interval' => 3000, // 3 seconds
    ));
    ?>
    <div class="wrap">
        <h1>Chatbot Conversations</h1>
        
        <div class="chatbot-admin-container">
            <div class="conversation-list-wrapper">
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
                                <label for="search-input" style="margin-right: 8px; font-weight: 500;">Search:</label>
                                <input
                                    type="search"
                                    id="search-input"
                                    name="s"
                                    value="<?php echo esc_attr($search_query); ?>"
                                    placeholder="Search messages..."
                                    style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; width: 100%; max-width: 250px; box-sizing: border-box;"
                                >
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
                                    <th>Status</th>
                                    <!-- <th>Last Message</th> -->
                                    <th>Date</th>
                                    <th>Messages</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($conversations)): ?>
                                    <tr>
                                        <td colspan="6" class="no-conversations">No conversations found matching your criteria.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($conversations as $conv): 
                                        // Get message count for this visitor
                                        $message_count = $wpdb->get_var($wpdb->prepare(
                                            "SELECT COUNT(*) FROM $table_name WHERE visitor_id = %s",
                                            $conv->visitor_id
                                        ));
                                    ?>
                                    <tr class="conversation-row <?php if ($display_visitor_id && $display_visitor_id == $conv->visitor_id) echo 'active'; ?>" data-conversation-id="<?php echo $conv->id; ?>">
                                        <th scope="row" class="check-column">
                                            <input type="checkbox" name="selected_conversations[]" value="<?php echo $conv->id; ?>">
                                        </th>
                                        <td class="visitor-cell">
                                            <div class="visitor-avatar"><?php echo strtoupper(substr($conv->visitor_id, 0, 1)); ?></div>
                                            <div class="visitor-info">
                                                <div class="visitor-id"><?php echo esc_html(substr($conv->visitor_id, 0, 8) . '...'); ?></div>
                                                <div class="visitor-ip"><?php echo esc_html($conv->user_ip); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $conv->status; ?>">
                                                <?php echo ucfirst($conv->status); ?>
                                            </span>
                                        </td>
                                        <!-- <td><?php //echo wp_trim_words($conv->message, 10); ?></td> -->
                                        <td><?php echo date('M j, Y g:i a', strtotime($conv->created_at)); ?></td>
                                        <td><?php echo $message_count; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            
            <div class="conversation-detail-wrapper">
                <?php if ($display_conv): ?>
                    <div id="conversation-<?php echo $display_conv->id; ?>" class="conversation-panel" data-visitor-id="<?php echo $display_conv->visitor_id; ?>">
                        <div class="conversation-header">
                            <div class="visitor-info">
                                <div class="visitor-avatar"><?php echo strtoupper(substr($display_conv->visitor_id, 0, 1)); ?></div>
                                <div>
                                    <h2><?php echo esc_html(substr($display_conv->visitor_id, 0, 8) . '...'); ?></h2>
                                    <div class="conversation-meta">
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
                                            <strong>Messages:</strong> 
                                            <?php echo $wpdb->get_var($wpdb->prepare(
                                                "SELECT COUNT(*) FROM $table_name WHERE visitor_id = %s",
                                                $display_conv->visitor_id
                                            )); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="conversation-actions">
                                <button class="button button-secondary use-ai-response">Generate AI Response</button>
                                <a href="?page=chatbot-conversations&action=delete&id=<?php echo $display_conv->id; ?>" class="button button-link-delete" onclick="return confirm('Are you sure you want to delete this conversation?');">Delete</a>
                            </div>
                        </div>
                        
                        <div class="message-history-container">
                            <div class="message-history">
                                <?php foreach ($conversation_messages as $msg): ?>
                                    <div class="message <?php echo $msg->response ? 'admin-message' : 'user-message'; ?>" data-message-id="<?php echo $msg->id; ?>">
                                        <div class="message-content">
                                            <div class="message-text"><?php echo wpautop(esc_textarea($msg->response ? $msg->response : $msg->message)); ?></div>
                                            <div class="message-time"><?php echo date('M j, Y g:i a', strtotime($msg->response ? $msg->responded_at : $msg->created_at)); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if ($display_conv->status === 'pending'): ?>
                                <div class="message bot-message">
                                    <div class="message-content">
                                        <!-- <div class="typing-indicator">
                                            <div class="typing-dot"></div>
                                            <div class="typing-dot"></div>
                                            <div class="typing-dot"></div>
                                        </div> -->
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <form method="post" class="response-form">
    <input type="hidden" name="conversation_id" value="<?php echo $display_conv->id; ?>">
    <div class="response-input">
        
        <!-- Emoji button -->
        <button type="button" class="emoji-button" onclick="toggleEmojiPicker(event)">
            😊
        </button>

        <!-- Hidden emoji picker -->
        <div class="emoji-picker" id="emojiPicker" style="display:none;">
            <span onclick="insertEmoji('😊')">😊</span>
            <span onclick="insertEmoji('😂')">😂</span>
            <span onclick="insertEmoji('👍')">👍</span>
            <span onclick="insertEmoji('❤️')">❤️</span>
            <span onclick="insertEmoji('🔥')">🔥</span>
            <span onclick="insertEmoji('😢')">😢</span>
            <!-- Add more as needed -->
        </div>

        <textarea name="response" id="responseTextarea" rows="3" placeholder="Type your response here..." required></textarea>

        <button type="submit" name="send_response" class="button button-primary send-button">
            <svg viewBox="0 0 24 24" width="20" height="20">
                <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
        </button>
    </div>
</form>

                    </div>
                <?php else: ?>
                    <div class="no-conversation-selected">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" width="48" height="48">
                                <path fill="#ccc" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                            </svg>
                            <h3>No conversation selected</h3>
                            <p>Select a conversation from the list to view and respond to messages.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script>
function toggleEmojiPicker(e) {
    e.preventDefault();
    const picker = document.getElementById('emojiPicker');
    picker.style.display = picker.style.display === 'block' ? 'none' : 'block';
}

function insertEmoji(emoji) {
    const textarea = document.getElementById('responseTextarea');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + emoji + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
    textarea.focus();

    document.getElementById('emojiPicker').style.display = 'none';
}
</script>
<style>

       :root {
  --primary: #4f8cff;
  --primary-dark: #3866c2;
  --accent: linear-gradient(90deg, #4f8cff 0%, #67e8f9 100%);
  --bg: #f4f7fa;
  --white: #fff;
  --border: #e3e8ee;
  --shadow: 0 2px 12px rgba(30, 42, 80, 0.08);
  --radius: 14px;
  --font: 'Inter', 'Segoe UI', Arial, sans-serif;
}

/* GENERAL */
body {
  background: var(--bg);
  font-family: var(--font);
  color: #222;
  margin: 0;
}

.chatbot-admin-container {
  display: flex;
  gap: 28px;
  margin: 32px auto 0 auto;
  max-width: 1400px;
  min-height: 80vh;
  padding: 0 16px;
}

/* CONVERSATION LIST */
.conversation-list-wrapper {
  flex: 0 0 420px;
  background: var(--white);
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  border-radius: var(--radius);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.conversation-filters {
  padding: 20px 18px 12px 18px;
  border-bottom: 1px solid var(--border);
  background: var(--bg);
}

.filter-row {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: center;
}

.filter-group {
  position: relative;
  margin: 0 1% 0 0;
  padding: 8px 12px;
  width: 47%;
  display: flex;
  flex-direction: row;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

.filter-group label {
  font-weight: 600;
  font-size: 13px;
  color: #555;
}

.conversation-list-container {
  flex: 1;
  overflow-y: auto;
  background: var(--white);
}

.conversation-row {
  cursor: pointer;
  transition: background 0.18s, box-shadow 0.18s;
  border-bottom: 1px solid var(--border);
  padding: 16px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.conversation-row:hover {
  background: #f0f6ff;
  box-shadow: 0 2px 8px rgba(79, 140, 255, 0.04);
}

.conversation-row.active {
  background: linear-gradient(90deg, #e3f0ff 0%, #f0faff 100%);
  border-left: 4px solid var(--primary);
}

/* VISITOR CELL */
.visitor-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.visitor-avatar {
  width: 38px;
  height: 38px;
  background: var(--accent);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 17px;
  box-shadow: 0 2px 8px rgba(79, 140, 255, 0.12);
}

.visitor-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 0;
}

.visitor-id {
  font-weight: 600;
  font-size: 15px;
  color: #222;
}

.visitor-ip {
  font-size: 11px;
  color: #888;
}

/* CONVERSATION DETAIL */
.conversation-detail-wrapper {
  flex: 1;
  background: var(--white);
  border: 1px solid var(--border);
  box-shadow: var(--shadow);
  border-radius: var(--radius);
  display: flex;
  flex-direction: column;
  position: relative;
  min-width: 0;
  height: calc(100vh - 120px);
  overflow: hidden;
}

.conversation-header {
  padding: 18px 24px;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--bg);
  border-top-left-radius: var(--radius);
  border-top-right-radius: var(--radius);
}

.conversation-actions {
  display: flex;
  gap: 10px;
}

/* MESSAGE HISTORY */
.message-history-container {
    flex: 1;
    overflow-y: auto;
    padding: 26px 32px 20px 32px;
    background: #f7faff;
    height: calc(69vh - 300px);
}

.message-history {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

/* MESSAGES */
.message {
  max-width: 75%;
  padding: 0;
  border-radius: 18px;
  position: relative;
  opacity: 0;
  transform: translateY(10px);
  transition: opacity 0.3s, transform 0.3s;
  box-shadow: 0 2px 8px rgba(30, 42, 80, 0.06);
}

.message.visible {
  opacity: 1;
  transform: translateY(0);
}

.user-message {
  align-self: flex-start;
  background: var(--white);
  border: 1px solid #e5e5ea;
  margin-right: 20%;
  border-top-left-radius: 0;
}

.admin-message {
  align-self: flex-end;
  background: linear-gradient(90deg, #dbeafe 0%, #c7ffd8 100%);
  margin-left: 20%;
  border-top-right-radius: 0;
}

.bot-message {
  align-self: flex-start;
  background: #f0f6ff;
  border: 1px solid #e5e5ea;
  margin-right: 20%;
  border-top-left-radius: 0;
}

.message-content {
  padding: 12px 18px;
}

.message-text {
  word-wrap: break-word;
  line-height: 1.6;
  font-size: 15px;
  color: #222;
}

.message-time {
  font-size: 11px;
  color: #888;
  text-align: right;
  margin-top: 6px;
}

/* BADGES */
.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.02em;
}

.status-pending {
  background: #ffc107;
  color: #fff;
}

.status-responded {
  background: #4caf50;
  color: #fff;
}

/* RESPONSE FORM */
.response-form {
  padding: 18px 24px;
  border-top: 1px solid var(--border);
  position: sticky;
  bottom: 0;
  background: var(--white);
  z-index: 10;
  border-bottom-left-radius: var(--radius);
  border-bottom-right-radius: var(--radius);
}

.response-input {
  display: flex;
  gap: 10px;
  align-items: flex-end;
}

.response-input textarea {
  flex: 1;
  min-height: 80px;
  padding: 12px 16px;
  border: 1px solid var(--border);
  border-radius: 10px;
  font-size: 15px;
  resize: none;
  background: #f7faff;
  transition: border 0.2s;
}

.response-input textarea:focus {
  border: 1.5px solid var(--primary);
  outline: none;
  background: #fff;
}

.send-button {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  box-shadow: 0 1px 4px rgba(79, 140, 255, 0.15);
  transition: background 0.2s, box-shadow 0.2s;
}

.send-button:hover {
  background: var(--primary-dark);
  box-shadow: 0 2px 8px rgba(79, 140, 255, 0.25);
}

/* EMOJI PICKER */
.emoji-button {
  background: none;
  border: none;
  font-size: 22px;
  cursor: pointer;
  transition: color 0.2s;
  color: #888;
  margin-right: 4px;
}

.emoji-button:hover {
  color: var(--primary);
}

.emoji-picker {
  position: absolute;
  bottom: 70px;
  background: var(--white);
  border: 1px solid var(--border);
  padding: 8px 10px;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(30,42,80,0.12);
  z-index: 999;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.emoji-picker span {
  cursor: pointer;
  font-size: 22px;
  margin: 4px;
  transition: transform 0.15s;
}

.emoji-picker span:hover {
  transform: scale(1.2);
}

/* EMPTY/NO STATE */
.no-conversation-selected {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 50px;
}
.conversation-panel{
    height:100% !important;
}

.empty-state {
  text-align: center;
  color: #888;
}

.empty-state svg {
  margin-bottom: 18px;
}

.empty-state h3 {
  margin: 0 0 12px 0;
  font-size: 20px;
  font-weight: 600;
  color: #222;
}

.empty-state p {
  margin: 0;
  font-size: 15px;
  color: #666;
}

/* TYPING INDICATOR */
.typing-indicator {
  display: flex;
  padding: 10px 0 0 12px;
  align-items: center;
  gap: 3px;
}

.typing-dot {
  width: 9px;
  height: 9px;
  background-color: #b6d0ff;
  border-radius: 50%;
  margin: 0 2px;
  animation: typingAnimation 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) { animation-delay: 0s; }
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingAnimation {
  0%, 60%, 100% { transform: translateY(0); }
  30% { transform: translateY(-6px); }
}

/* RESPONSIVE */
@media screen and (max-width: 1200px) {
  .chatbot-admin-container { flex-direction: column; }
  .conversation-list-wrapper { flex: 0 0 auto; }
  .conversation-detail-wrapper { min-width: 0; }
}

@media (max-width: 900px) {
  .conversation-list-wrapper { width: 100%; }
  .message-history-container { padding: 14px 8px; }
  .conversation-header, .response-form { padding: 12px 8px; }
}

@media (max-width: 600px) {
  .chatbot-admin-container { padding: 0; }
  .conversation-list-wrapper, .conversation-detail-wrapper {
    border-radius: 0;
    box-shadow: none;
    border: none;
  }
}
</style>
        
        <script>
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
        jQuery(document).ready(function($) {
            // Make all existing messages visible with animation
            $('.message').each(function(index) {
                var $message = $(this);
                setTimeout(function() {
                    $message.addClass('visible');
                }, index * 100);
            });
            
            // Clickable conversation rows - now links to the visitor's latest message
            $(document).on('click', '.conversation-row', function() {
                const conversationId = $(this).data('conversation-id');
                window.location.href = '?page=chatbot-conversations&id=' + conversationId;
            });

            // AI response generator
            $(document).on('click', '.use-ai-response', function() {
                const conversationId = $(this).closest('.conversation-panel').attr('id').replace('conversation-', '');
                const message = $('.message.user-message:last .message-text').text();

                $(this).prop('disabled', true).text('Generating...');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'chatbot_generate_ai_response',
                        message: message,
                        nonce: chatbot_admin_vars.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $('textarea[name="response"]').val(response.data.reply).trigger('input');
                        } else {
                            alert('Failed to generate AI response');
                        }
                    },
                    error: function() {
                        alert('Error generating AI response');
                    },
                    complete: function() {
                        $('.use-ai-response').prop('disabled', false).text('Generate AI Response');
                    }
                });
            });

            // Auto-resize textarea
            $('textarea[name="response"]').on('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            }).trigger('input');

            // Send on Enter (submit form via button) — allow Shift+Enter for newlines
            $('textarea[name="response"]').on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    $(this).closest('form').find('.send-button').trigger('click');
                }
            });

            // Real-time conversation updates
            if (chatbot_admin_vars.current_visitor_id) {
                let lastMessageId = chatbot_admin_vars.last_message_id;
                let isPolling = true;
                let isRequestInProgress = false;
                
                // Start polling for updates
                function pollConversationUpdates() {
                    if (!isPolling || isRequestInProgress) return;
                    
                    isRequestInProgress = true;
                    
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'chatbot_check_conversation_updates',
                            visitor_id: chatbot_admin_vars.current_visitor_id,
                            last_message_id: lastMessageId,
                            nonce: chatbot_admin_vars.nonce
                        },
                        success: function(response) {
                            if (response.success && response.data.messages.length > 0) {
                                // Update last message ID
                                lastMessageId = response.data.last_message_id;
                                
                                // Append new messages
                                response.data.messages.forEach(function(message) {
                                    appendMessageToConversation(message);
                                });
                                
                                // Scroll to bottom
                                scrollToBottom();
                            }
                        },
                        complete: function() {
                            isRequestInProgress = false;
                            // Continue polling
                            setTimeout(pollConversationUpdates, chatbot_admin_vars.polling_interval);
                        }
                    });
                }
                
                // Function to append a new message to the conversation (prevents duplicates)
                function appendMessageToConversation(message) {
                    const historyContainer = $('.message-history');
                    const isUserMessage = !message.response;

                    // Prevent duplicate messages by checking if message ID already exists
                    if (historyContainer.find('.message[data-message-id="' + message.id + '"]').length > 0) {
                        return;
                    }

                    // Remove typing indicator if it exists
                    historyContainer.find('.typing-indicator').closest('.message').remove();

                    // Format the message HTML
                    let messageClass = isUserMessage ? 'user-message' : 'admin-message';
                    let messageContent = isUserMessage ? message.message : message.response;
                    let messageTime = isUserMessage ? message.created_at : message.responded_at;

                    let messageHtml = `
                        <div class="message ${messageClass}" data-message-id="${message.id}">
                            <div class="message-content">
                                <div class="message-text">${formatResponse(messageContent)}</div>
                                <div class="message-time">${formatTime(messageTime)}</div>
                            </div>
                        </div>
                    `;

                    // Append the message and animate
                    const $newMessage = $(messageHtml).appendTo(historyContainer);
                    setTimeout(function() {
                        $newMessage.addClass('visible');
                    }, 50);

                    // If this was a user message and there's no response yet, show typing indicator
                    if (isUserMessage && message.status === 'pending') {
                        const typingHtml = `
                            <div class="message bot-message">
                                <div class="message-content">
                                    <div class="typing-indicator">
                                        <div class="typing-dot"></div>
                                        <div class="typing-dot"></div>
                                        <div class="typing-dot"></div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $(typingHtml).appendTo(historyContainer);
                    }

                    // Scroll to bottom after new message
                    historyContainer.parent()[0].scrollTop = historyContainer.parent()[0].scrollHeight;
                }
                
                // Helper function to format response text
                function formatResponse(text) {
                    // Simple formatting - replace newlines with <br>
                    return text.replace(/\n/g, '<br>');
                }
                
                // Helper function to format time
                function formatTime(timestamp) {
                    const date = new Date(timestamp);
                    return date.toLocaleTimeString([], { 
                        month: 'short', 
                        day: 'numeric', 
                        year: 'numeric', 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                }
                
                // Helper function to scroll to bottom of messages
                function scrollToBottom() {
                    const container = $('.message-history-container')[0];
                    container.scrollTop = container.scrollHeight;
                }
                
                // Start polling
                pollConversationUpdates();
                
                // Stop polling when leaving the page
                $(window).on('beforeunload', function() {
                    isPolling = false;
                });
            }
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
                    <th scope="row">Real-time Communication</th>
                    <td>
                        <label>
                            <input type="checkbox" name="use_websocket" value="1" <?php checked($use_websocket, 1); ?> />
                            Use WebSocket for real-time updates (experimental)
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">WebSocket URL</th>
                    <td>
                        <input type="url" name="websocket_url" value="<?php echo esc_attr($websocket_url); ?>" class="regular-text" />
                        <p class="description">Enter your WebSocket server URL</p>
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
        conversation_id varchar(32) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        responded_at datetime NULL,
        PRIMARY KEY  (id),
        KEY visitor_id (visitor_id),
        KEY conversation_id (conversation_id)
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
    $pending_count = $wpdb->get_var("SELECT COUNT(DISTINCT conversation_id) FROM $table_name WHERE status = 'pending'");
    
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