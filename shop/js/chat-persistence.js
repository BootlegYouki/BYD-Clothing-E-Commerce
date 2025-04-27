/**
 * Chat Persistence Helper - Ensures conversation history persists across pages
 * This file should be included on all pages that have the chat component
 */
(function() {
    // Make conversationHistory accessible from outside for emergency saves
    window.persistChatBeforeUnload = function() {
        if (window.conversationHistory && Array.isArray(window.conversationHistory)) {
            try {
                localStorage.setItem('conversationHistory', JSON.stringify(window.conversationHistory));
                localStorage.setItem('conversationTimestamp', Date.now().toString());
                console.log('Emergency conversation save completed');
                return true;
            } catch (e) {
                console.error('Emergency save failed:', e);
                return false;
            }
        }
        return false;
    };

    // Monitor page visibility changes to save when tab is switched
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden' && window.conversationHistory) {
            window.persistChatBeforeUnload();
        }
    });

    // Capture navigation events when possible
    window.addEventListener('beforeunload', window.persistChatBeforeUnload);
    window.addEventListener('pagehide', window.persistChatBeforeUnload);
    
    // For SPAs and dynamic navigation
    const originalPushState = history.pushState;
    if (originalPushState) {
        history.pushState = function() {
            window.persistChatBeforeUnload();
            return originalPushState.apply(history, arguments);
        };
    }
    
    // For single page applications using replaceState
    const originalReplaceState = history.replaceState;
    if (originalReplaceState) {
        history.replaceState = function() {
            window.persistChatBeforeUnload();
            return originalReplaceState.apply(history, arguments);
        };
    }
    
    console.log('Chat persistence helpers installed');
})();
