<div class="chat-bubble" id="chat-bubble">
        <i class='bx bxs-chat'></i>
    </div>
<div class="chat-container" id="chat-container">
    <div class="chat-header">
        <i class="fas fa-robot"></i>
        <h2>BYD Clothing AI Assistant</h2>
        <div class="chat-controls">
            <button class="clear-btn" id="clear-chat" title="Start new conversation">
                <i class="fas fa-square-plus"></i>
            </button>
            <button class="close-btn" id="close-chat">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="chat-messages" id="chat-messages">
        <div class="message bot-message">
            <div class="message-content">
            <?php
            $hour = date('H');
            if ($hour >= 5 && $hour < 12) {
                $greeting = "Good morning";
            } elseif ($hour >= 12 && $hour < 18) {
                $greeting = "Good afternoon";
            } else {
                $greeting = "Good evening";
            }
            
            if (isset($username) && !empty($username)) {
                echo "<p>{$greeting}, {$username}! How can I help you with BYD-CLOTHING products today?</p>";
            } else {
                echo "<p>{$greeting}! How can I help you with BYD-CLOTHING products today?</p>";
            }
            ?>
            </div>
        </div>
    </div>
    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Type your message..." onkeypress="if(event.key === 'Enter') sendORstop()">
        <button class="send-btn" id="sendBtn" onclick="sendORstop()">
            <i class='bx bxs-paper-plane' id="sendIcon"></i>
            <i class='bx bx-stop-circle d-none' id="stopIcon" style="font-size: 1.3rem;"></i>
        </button>
    </div>
</div>

<!-- Add script for state persistence -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply saved state for chat UI
    const chatState = sessionStorage.getItem('chatState');
    const chatContainer = document.getElementById('chat-container');
    const chatBubble = document.getElementById('chat-bubble');
    const chatMessages = document.getElementById('chat-messages');
    
    if (chatState === 'open') {
        // Apply the active class immediately without animation
        chatContainer.style.transition = 'none'; // Disable transition temporarily
        chatBubble.style.transition = 'none';
        
        chatContainer.classList.add('active');
        chatBubble.classList.add('hidden');
        
        // Force a reflow to apply the styles without animation
        void chatContainer.offsetWidth;
        void chatBubble.offsetWidth;
        
        // Re-enable transitions after a small delay
        setTimeout(() => {
            chatContainer.style.transition = '';
            chatBubble.style.transition = '';
        }, 10);
    } else {
        chatContainer.classList.remove('active');
        chatBubble.classList.remove('hidden');
    }
    
    // Scroll chat to the bottom by default
    scrollChatToBottom();
});

// Function to scroll chat to bottom
function scrollChatToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Add an event listener for when new messages are added
const chatMessagesObserver = new MutationObserver(function(mutations) {
    scrollChatToBottom();
});

// Start observing the chat messages container for added nodes
chatMessagesObserver.observe(document.getElementById('chat-messages'), { 
    childList: true,
    subtree: true 
});
</script>