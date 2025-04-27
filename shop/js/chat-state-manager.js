/**
 * Chat State Manager - Handles chat state persistence across page navigation
 */
(function() {
    // Check if we need to initialize on this page
    if (!document.getElementById('chat-container')) {
        return; // Skip if chat container is not on this page
    }
    
    // Function to check if the chat was open before navigation
    function restoreChatState() {
        const chatState = sessionStorage.getItem('chatState');
        const chatContainer = document.getElementById('chat-container');
        const chatBubble = document.getElementById('chat-bubble');
        
        if (!chatContainer || !chatBubble) return;
        
        if (chatState === 'open') {
            // Immediately apply active state without animation
            chatContainer.style.transition = 'none';
            chatBubble.style.transition = 'none';
            
            chatContainer.classList.add('active');
            chatBubble.classList.add('hidden');
            
            // Force reflow
            void chatContainer.offsetWidth;
            
            // Re-enable transitions
            setTimeout(() => {
                chatContainer.style.transition = '';
                chatBubble.style.transition = '';
            }, 10);
        }
    }
    
    // Restore chat state before the page fully loads
    document.addEventListener('DOMContentLoaded', restoreChatState);
    
    // Log page navigation for debugging
    console.log('Chat state manager initialized on:', window.location.pathname);
})();
