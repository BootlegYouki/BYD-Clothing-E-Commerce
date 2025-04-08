let currentController = null;
let currentMessageId = null;
let latestBotMessageId = null;
let conversationHistory;
const chatbot = "deepseek/deepseek-chat-v3-0324:free";
//OPEN CHATBOT
document.addEventListener('DOMContentLoaded', function() {
    initializeBot();
    
    document.getElementById('chat-bubble').addEventListener('click', function() {
        document.getElementById('chat-container').classList.add('active');
        document.getElementById('chat-bubble').classList.add('hidden');
        // Store state in session storage
        sessionStorage.setItem('chatState', 'open');

    });

    document.getElementById('close-chat').addEventListener('click', function() {
        document.getElementById('chat-container').classList.remove('active');
        document.getElementById('chat-bubble').classList.remove('hidden');
        // Store state in session storage
        sessionStorage.setItem('chatState', 'closed');
    });
    
    // Modified handler for the clear chat button
    const clearChatButton = document.getElementById('clear-chat');
    if (clearChatButton) {
        clearChatButton.addEventListener('click', function() {
            if (confirm('Are you sure you want to start a new conversation?')) {
                clearChat();
            }
        });
    }
});

//FUNCTIONS TO SAVE/CLEAR CONVERSATIONS
async function checkUserAuth() {
    try {
        const response = await fetch('../shop/functions/check-auth.php');
        const data = await response.json();
        return data.isLoggedIn;
    } catch (error) {
        console.error('Error checking auth status:', error);
        return false;
    }
}
async function loadConversation() {
    try {
        // Check local storage first
        const savedConversation = localStorage.getItem('conversationHistory');
        if (savedConversation) {
            return JSON.parse(savedConversation);
        }

        // Fallback to server
        const isLoggedIn = await checkUserAuth();
        if (!isLoggedIn) {
            console.log('User not logged in, cannot load conversation');
            return false;
        }

        const response = await fetch('../shop/functions/conversation-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'load'
            })
        });

        const data = await response.json();
        console.log('Loaded conversation data:', data);

        if (data && data.status === 'success' && data.conversation) {
            return data.conversation;
        } else {
            console.log('No conversation found or invalid response structure');
            return false;
        }
    } catch (error) {
        console.error('Error loading conversation:', error);
        return false;
    }
}
async function saveConversation() {
    try {
        // Always save to localStorage for page navigation persistence
        localStorage.setItem('conversationHistory', JSON.stringify(conversationHistory));
        
        const isLoggedIn = await checkUserAuth();
        if (!isLoggedIn) return false;

        // Save to server
        const response = await fetch('../shop/functions/conversation-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'save',
                conversation: conversationHistory
            })
        });

        const data = await response.json();
        return data.status === 'success';
    } catch (error) {
        console.error('Error saving conversation:', error);
        return false;
    }
}   
async function clearServerConversation() {
    try {
        const isLoggedIn = await checkUserAuth();
        if (!isLoggedIn) return false;

        const response = await fetch('../shop/functions/conversation-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'clear'
            })
        });

        const data = await response.json();
        return data.status === 'success';
    } catch (error) {
        console.error('Error clearing conversation:', error);
        return false;
    }
}
//END FUNCTIONS TO SAVE/CLEAR CONVERSATIONS

//MAJOR CHATBOT FUNCTIONS
async function createDynamicSystemPrompt(dataNeeds = {}) {
    // Default to false if no specific requirements provided
    const requiresProductData = dataNeeds.requiresProductData === true;
    const requiresStockData = dataNeeds.requiresStockData === true;
    const requiresPriceData = dataNeeds.requiresPriceData === true;
    
    // Base prompt without product data
    let basePrompt = `You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.

Be friendly, helpful, and knowledgeable about BYD-CLOTHING products. 
Answer customer questions accurately and suggest products based on their needs but strictly only related to the shop's products.
If the question is in Filipino, respond in Filipino with natural conversational style.

VERY IMPORTANT RULES:
- YOUR SCOPE IS ONLY T-SHIRTS AND LONG SLEEVES.
- NEVER HALLUCINATE OR MAKE UP ANY PRODUCT INFORMATION OR EVEN ADD A RANDOM PRODUCT. IF YOU DONT KNOW THE INFORMATION, SAY "I DON'T KNOW".
- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.
- For any unrelated questions, respond ONLY with: "I'm sorry, I can only answer questions related to BYD-CLOTHING products and services."

IMPORTANT DISPLAY INSTRUCTIONS:
- Use stylized typography with appropriate font sizes and emojis where suitable.
- Use bullet points for lists and key features.
- Keep displays clear and concise.
- For products with discounts: Show "Original Price: ₱X, Y% off, Final Price: ₱Z"
- For products with no discount (0%): Only show "Price: ₱X" without mentioning discounts
- Only mention available sizes if asked about specific products`;

    // Only fetch product data if the prompt requires it
    if (requiresProductData) {
        try {
            const response = await fetch('../shop/functions/product-data.php');
            if (!response.ok) throw new Error('Failed to fetch product data');
            
            const products = await response.json();
            
            // Format T-shirt information with appropriate details based on what's needed
            let tshirtInfo = '';
            if (products.tshirts && products.tshirts.length > 0) {
                tshirtInfo = `- T-shirts:\n`;
                
                products.tshirts.forEach(product => {
                    // Basic product information always included if product data is requested
                    tshirtInfo += `  "${product.name}"`;
                    
                    // Add price info only if specifically requested
                    if (requiresPriceData) {
                        const finalPrice = Math.round(product.original_price * (1 - (product.discount_percentage / 100)));
                        tshirtInfo += ` (Price: ₱${product.original_price}${product.discount_percentage > 0 ? ', ' + product.discount_percentage + '% off, Final: ₱' + finalPrice : ''})`;
                    }
                    
                    // Add new release tag if applicable
                    if (product.is_new_release == 1) tshirtInfo += ' - New!';
                    
                    // Add stock information only if specifically requested
                    if (requiresStockData && product.stock_by_size) {
                        tshirtInfo += ' | Stock: ';
                        let availableSizes = [];
                        let hasStock = false; // This line is missing in your code
                        
                        for (const size in product.stock_by_size) {
                            if (product.stock_by_size[size] > 0) {
                                availableSizes.push(`${size}:${product.stock_by_size[size]}`);
                                hasStock = true; // This sets hasStock to true when at least one size has stock
                            }
                        }
                        
                        if (hasStock) {
                            tshirtInfo += availableSizes.join(', ');
                        } else {
                            tshirtInfo += 'OUT OF STOCK';
                        }
                    }
                    
                    tshirtInfo += '\n';
                });
            }
            
            // Format Long Sleeve information with appropriate details
            let longslvInfo = '';
            if (products.longslv && products.longslv.length > 0) {
                longslvInfo = `- Long Sleeves:\n`;
                
                products.longslv.forEach(product => {
                    // Basic product information
                    longslvInfo += `  "${product.name}"`;
                    
                    // Add price info only if specifically requested
                    if (requiresPriceData) {
                        const finalPrice = Math.floor(product.original_price * (1 - (product.discount_percentage / 100)));
                        longslvInfo += ` (Price: ₱${product.original_price}${product.discount_percentage > 0 ? ', ' + product.discount_percentage + '% off, Final: ₱' + finalPrice : ''})`;
                    }
                    
                    // Add new release tag if applicable
                    if (product.is_new_release == 1) longslvInfo += ' - New!';
                    
                    // Add stock information only if specifically requested
                    if (requiresStockData && product.stock_by_size) {
                        longslvInfo += ' | Stock: ';
                        let availableSizes = [];
                        let hasStock = false; // Add this line
                        
                        for (const size in product.stock_by_size) {
                            if (product.stock_by_size[size] > 0) {
                                availableSizes.push(`${size}:${product.stock_by_size[size]}`);
                                hasStock = true; // Add this line
                            }
                        }
                        
                        if (hasStock) {
                            longslvInfo += availableSizes.join(', ');
                        } else {
                            longslvInfo += 'OUT OF STOCK';
                        }
                    }
                    
                    longslvInfo += '\n';
                });
            }
            
            return basePrompt + `\n\nProducts Information (Live from Database):\n${tshirtInfo}\n${longslvInfo}`;
        } catch (error) {
            console.error('Error fetching product data:', error);
            return basePrompt;
        }
    }
    return basePrompt;
}
async function initializeBot() {
    // Check if conversation is already loaded in session storage
    const conversationLoaded = sessionStorage.getItem('conversationLoaded');
    
    // If conversation already loaded in this session, just restore UI
    if (conversationLoaded === 'true') {
        try {
            // Get saved messages to restore UI
            const savedConversation = JSON.parse(localStorage.getItem('conversationHistory'));
            
            if (savedConversation && savedConversation.length > 1) {
                conversationHistory = savedConversation;
                
                // Render previous messages in the UI
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.innerHTML = ''; // Clear default greeting
                
                // Skip the system message (index 0) when rendering
                for (let i = 1; i < conversationHistory.length; i++) {
                    const message = conversationHistory[i];
                    const messageClass = message.role === 'assistant' ? 'bot-message' : 'user-message';
                    
                    chatMessages.innerHTML += `
                        <div class="message ${messageClass}">
                            <div class="message-content">${
                                message.role === 'assistant' 
                                    ? marked.parse(message.content) 
                                    : message.content
                            }</div>
                        </div>
                    `;
                }
                return; // Exit early since we restored from session
            }
        } catch (error) {
            console.error('Error restoring chat UI from session:', error);
        }
    }

    // Original initialization code continues here
    try {
        // Create initial basic system prompt without product data
        const baseSystemPrompt = await createDynamicSystemPrompt(false);
        
        const savedConversation = await loadConversation();

        if (savedConversation) {
            conversationHistory = savedConversation;
            
            // Ensure system prompt is at index 0
            if (conversationHistory.length > 0) {
                conversationHistory[0] = {
                    "role": "system", 
                    "content": baseSystemPrompt
                };
            }
            
            // Render previous messages in the UI
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML = ''; // Clear default greeting
            
            // Skip the system message (index 0) when rendering
            for (let i = 1; i < conversationHistory.length; i++) {
                const message = conversationHistory[i];
                const messageClass = message.role === 'assistant' ? 'bot-message' : 'user-message';
                
                chatMessages.innerHTML += `
                    <div class="message ${messageClass}">
                        <div class="message-content">${
                            message.role === 'assistant' 
                                ? marked.parse(message.content) 
                                : message.content
                        }</div>
                    </div>
                `;
            }
        } else {
            // If no conversation found, create a new one with minimal tokens
            conversationHistory = [
                {"role": "system", "content": baseSystemPrompt},
                {"role": "assistant", "content": "Hi there! How can I help you with BYD-CLOTHING products today?"}
            ];
        }
        
        // Mark that we've loaded the conversation for this session
        sessionStorage.setItem('conversationLoaded', 'true');
        
    } catch (error) {
        console.error('Error initializing bot:', error);
        // Use base system prompt on error
        conversationHistory = [
            {"role": "system", "content": await createDynamicSystemPrompt(false)},
            {"role": "assistant", "content": "Hi there! How can I help you with BYD-CLOTHING products today?"}
        ];
    }
}
async function sendMessage() {
    const inputElem = document.getElementById('userInput');
    if (!inputElem.value.trim()) return;
    
    // Save trimmed message before clearing it
    const userMessage = inputElem.value.trim();
    inputElem.value = '';

    inputElem.disabled = true;

    const allFeedbackElements = document.querySelectorAll('.message-feedback');
    allFeedbackElements.forEach(element => {
        element.style.display = 'none';
    });
    
    const chatMessages = document.getElementById('chat-messages');
    const messageId = 'msg-' + Date.now();

    currentMessageId = messageId;
    latestBotMessageId = messageId;
    
    // Abort any previous ongoing requests
    if (currentController) {
        currentController.abort();
    }
    
    // Append the user message
    chatMessages.innerHTML += `
        <div class="message user-message">
            <div class="message-content">${userMessage}</div>
        </div>
    `;
    
    // Add user message to conversation history
    conversationHistory.push({"role": "user", "content": userMessage});

    // Check what kind of product data is needed based on user's message
    const dataNeeds = messageRequiresProductData(userMessage);
    
    // Add bot message with loading indicator
    chatMessages.innerHTML += `
    <div class="message bot-message" id="${messageId}">
        <div class="message-content" id="${messageId}-content">
            <div class="loading-dots py-2" id="${messageId}-loading">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <div class="message-feedback" id="${messageId}-feedback" style="display:none;">
        <button class="feedback-btn regenerate" onclick="regenerateResponse('${messageId}')">
            <i class="fas fa-redo-alt"></i> Regenerate
        </button>
    </div>
    `;
    
    document.getElementById("sendIcon").classList.add("d-none");
    document.getElementById("stopIcon").classList.remove("d-none");

    
    try {
        // Create system prompt with only the required data
        const systemPrompt = await createDynamicSystemPrompt(dataNeeds);
        conversationHistory[0] = {"role": "system", "content": systemPrompt};
        
        // Trim conversation history to reduce tokens
        const trimmedHistory = trimConversationHistory(conversationHistory);
        
        // Rest of the function remains the same
        currentController = new AbortController();
        const signal = currentController.signal;
        
        const response = await fetch("../shop/functions/openrouter-proxy.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                "model": chatbot,
                "messages": trimmedHistory,
                "stream": true
            }),
            signal: signal
        });
        
        // Rest of the existing streaming code remains unchanged
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error?.message || 'API request failed');
        }
        
        const reader = response.body.getReader();
        const decoder = new TextDecoder("utf-8");
        
        let fullMessage = "";
        const streamingContent = document.getElementById(`${messageId}-content`);
        let isFirstChunk = true;
        
        while (true) {
            const { done, value } = await reader.read();
            if (done) break;
            
            if (!document.getElementById(messageId)) break;
            
            const chunk = decoder.decode(value, { stream: true });
            const lines = chunk.split('\n').filter(line => line.trim() !== '');
            
            for (const line of lines) {
                if (line.startsWith('data: ')) {
                    const jsonStr = line.slice(6);
                    if (jsonStr === '[DONE]') continue;
                    
                    try {
                        const jsonData = JSON.parse(jsonStr);
                        const contentDelta = jsonData.choices[0]?.delta?.content || '';
                        if (contentDelta) {
                            if (isFirstChunk) {
                                const loadingIndicator = document.getElementById(`${messageId}-loading`);
                                if (loadingIndicator) loadingIndicator.remove();
                                isFirstChunk = false;
                            }
                            
                            fullMessage += contentDelta;
                            streamingContent.innerHTML = marked.parse(fullMessage);
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    } catch (e) {
                        streamingContent.innerHTML = "Rate limit exceeded, please try again later.";
                        console.error('Error parsing JSON:', e);
                    }
                }
            }
        }
        
        if (currentController.signal.aborted) {
            document.getElementById("sendIcon").classList.remove("d-none");
            document.getElementById("stopIcon").classList.add("d-none");
            inputElem.disabled = false;
            return;
        }
        
        currentController = null;
        conversationHistory.push({"role": "assistant", "content": fullMessage});
        
        // Save the full conversation history but send trimmed history to the API
        saveConversation();
        
        const feedbackElement = document.getElementById(`${messageId}-feedback`);
        if (feedbackElement) {
            feedbackElement.style.display = 'flex';
        }
        
        document.getElementById("sendIcon").classList.remove("d-none");
        document.getElementById("stopIcon").classList.add("d-none");
        inputElem.disabled = false;
        
    } catch (error) {
        if (error.name === 'AbortError') {
            console.log('Request was aborted');
        } else {
            console.error(error);
            const streamingContent = document.getElementById(`${messageId}-content`);
            if (streamingContent) {
                streamingContent.innerHTML = 
                    "Sorry, there was an error connecting to the AI service. Please try again later.";
            }
        }
        
        document.getElementById("sendIcon").classList.remove("d-none");
        document.getElementById("stopIcon").classList.add("d-none");
        inputElem.disabled = false;
    }
}
async function regenerateResponse(messageId) {
    const lastUserMessage = conversationHistory[conversationHistory.length - 2];
    if (lastUserMessage && lastUserMessage.role === 'user') {
        // Remove the last bot message from conversation history
        conversationHistory.pop();
        // Remove both the message and its feedback element
        const messageElement = document.getElementById(messageId);
        if (messageElement) {
            messageElement.remove();
        }
        
        // Also remove the feedback element that's a sibling to the message
        const feedbackElement = document.getElementById(`${messageId}-feedback`);
        if (feedbackElement) {
            feedbackElement.remove();
        }

        const allFeedbackElements = document.querySelectorAll('.message-feedback');
        allFeedbackElements.forEach(element => {
            element.style.display = 'none';
        });
        
        // Create a new message ID
        const newMessageId = 'msg-' + Date.now();
        currentMessageId = newMessageId;
        
        const chatMessages = document.getElementById('chat-messages');
        
        // Add bot message placeholder with loading
        chatMessages.innerHTML += `
            <div class="message bot-message" id="${newMessageId}">
                <div class="message-content" id="${newMessageId}-content">
                    <div class="loading-dots py-2" id="${newMessageId}-loading">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        // Create a separate feedback div that we'll show later
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'message-feedback';
        feedbackDiv.id = `${newMessageId}-feedback`;
        feedbackDiv.style.display = 'none';
        feedbackDiv.innerHTML = `
            <button class="feedback-btn regenerate" onclick="regenerateResponse('${newMessageId}')">
                <i class="fas fa-redo-alt"></i> Regenerate
            </button>
        `;
        
        chatMessages.appendChild(feedbackDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Disable input and update icons
        const inputElem = document.getElementById('userInput');
        inputElem.disabled = true;
        document.getElementById("sendIcon").classList.add("d-none");
        document.getElementById("stopIcon").classList.remove("d-none");
        
        // Check if we need product data for the regenerated message
        const dataNeeds = messageRequiresProductData(lastUserMessage.content);
        
        try {
            // Update system prompt based on whether product data is needed
            conversationHistory[0] = {
                "role": "system", 
                "content": await createDynamicSystemPrompt(dataNeeds)
            };
            
            // Trim history for API request
            const trimmedHistory = trimConversationHistory(conversationHistory);
            
            currentController = new AbortController();
            const signal = currentController.signal;
            
            const response = await fetch("../shop/functions/openrouter-proxy.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    "model": chatbot,
                    "messages": trimmedHistory,
                    "stream": true
                }),
                signal: signal
            });
            
            if (!response.ok) {
                throw new Error('API request failed');
            }
            
            const reader = response.body.getReader();
            const decoder = new TextDecoder("utf-8");
            let fullMessage = "";
            const streamingContent = document.getElementById(`${newMessageId}-content`);
            let isFirstChunk = true;
            
            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                
                if (!document.getElementById(newMessageId)) break;
                
                const chunk = decoder.decode(value, { stream: true });
                const lines = chunk.split('\n').filter(line => line.trim() !== '');
                
                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const jsonStr = line.slice(6);
                        if (jsonStr === '[DONE]') continue;
                        
                        try {
                            const jsonData = JSON.parse(jsonStr);
                            const contentDelta = jsonData.choices[0]?.delta?.content || '';
                            if (contentDelta) {
                                if (isFirstChunk) {
                                    const loadingIndicator = document.getElementById(`${newMessageId}-loading`);
                                    if (loadingIndicator) loadingIndicator.remove();
                                    isFirstChunk = false;
                                }
                                
                                fullMessage += contentDelta;
                                streamingContent.innerHTML = marked.parse(fullMessage);
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                        }
                    }
                }
            }
            
            // Show regenerate button after response completes
            const feedbackElement = document.getElementById(`${newMessageId}-feedback`);
            if (feedbackElement) {
                feedbackElement.style.display = 'flex';
            }
            
            conversationHistory.push({"role": "assistant", "content": fullMessage});
            saveConversation();
            
            document.getElementById("sendIcon").classList.remove("d-none");
            document.getElementById("stopIcon").classList.add("d-none");
            inputElem.disabled = false;
            
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error(error);
                const streamingContent = document.getElementById(`${newMessageId}-content`);
                if (streamingContent) {
                    streamingContent.innerHTML = "Sorry, there was an error regenerating the response. Please try again.";
                }
            }
            
            document.getElementById("sendIcon").classList.remove("d-none");
            document.getElementById("stopIcon").classList.add("d-none");
            inputElem.disabled = false;
        }
    }
}
//END MAJOR CHATBOT FUNCTIONS

//ONLY SCANS PRODUCT DATA WHEN ASKED
function messageRequiresProductData(message) {
    // Base product keywords that don't necessarily need stock information
    const productKeywords = [
        'product', 'shirt', 't-shirt', 'tshirt', 'long sleeve', 
        'design', 'color', 'pattern', 'new release', 'latest',
        'what do you sell', 'what do you have', 'collection',
        'clothing', 'apparel', 'outfit', 'wear'
    ];
    
    // Keywords specific to pricing information
    const priceKeywords = [
        'price', 'cost', 'how much', 'discount', 'sale', 'expensive', 'cheap'
    ];
    
    // Keywords specific to stock/availability information
    const stockKeywords = [
        'available', 'stock', 'size', 'inventory', 'in store', 'left',
        'how many', 'quantity', 'sold out', 'still have', 'run out'
    ];
    
    // Convert message to lowercase for case-insensitive matching
    const lowerMessage = message.toLowerCase();
    
    // Check what type of information is needed
    const needsBasicInfo = productKeywords.some(keyword => lowerMessage.includes(keyword));
    const needsPriceInfo = priceKeywords.some(keyword => lowerMessage.includes(keyword));
    const needsStockInfo = stockKeywords.some(keyword => lowerMessage.includes(keyword));
    
    return {
        requiresProductData: needsBasicInfo || needsPriceInfo || needsStockInfo,
        requiresStockData: needsStockInfo,
        requiresPriceData: needsPriceInfo
    };
}
function trimConversationHistory(history, messageLimit = 6) {
    // Always keep the system message (index 0)
    // Keep only the last N message pairs (user + assistant) to save tokens
    if (history.length <= messageLimit + 1) {
        return history;
    }
    
    return [
        history[0], // system prompt
        // Keep the most recent message pairs
        ...history.slice(-messageLimit)
    ];
}
//REFRESH PRODUCT DATA FOR DYNAMIC SYSTEM PROMPT
async function refreshProductData() {
    try {
        // Update the system message with fresh complete product data
        if (conversationHistory && conversationHistory.length > 0) {
            conversationHistory[0] = {
                "role": "system", 
                "content": await createDynamicSystemPrompt({
                    requiresProductData: true,
                    requiresPriceData: true,
                    requiresStockData: true
                })
            };
        }
        return true;
    } catch (error) {
        console.error('Error refreshing product data:', error);
        return false;
    }
}

//BASIC FUNCTIONS
function sendORstop() {
    if (currentController && !currentController.signal.aborted) {
        // Stop the current response generation
        currentController.abort();
        console.log('Stopping the current response.');
        if (currentMessageId) {
            const loadingIndicator = document.getElementById(`${currentMessageId}-loading`);
            if (loadingIndicator) {
                loadingIndicator.remove();
            }
            const streamingContent = document.getElementById(`${currentMessageId}-content`);
            if (streamingContent) {
                streamingContent.innerHTML = "<p>Response Canceled</p>";
            }
        }
        // Update icons immediately after stopping
        document.getElementById("sendIcon").classList.remove("d-none");
        document.getElementById("stopIcon").classList.add("d-none");
        // Re-enable the input field
        document.getElementById('userInput').disabled = false;
    } else {
        sendMessage();
    }
}
async function clearChat() {
    // Clear UI
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.innerHTML = `
        <div class="message bot-message">
            <div class="message-content">
                <p>Hi there! How can I help you with BYD-CLOTHING products today?</p>
            </div>
        </div>
    `;
    
    // Clear all storage
    localStorage.removeItem('conversationHistory');
    sessionStorage.removeItem('conversationLoaded');
    
    // Clear server-side conversation
    await clearServerConversation();
    
    // Reset client-side conversation history with a fresh system prompt
    const baseSystemPrompt = await createDynamicSystemPrompt(false);
    conversationHistory = [
        {"role": "system", "content": baseSystemPrompt},
        {"role": "assistant", "content": "Hi there! How can I help you with BYD-CLOTHING products today?"}
    ];
    
    // Mark as new conversation
    sessionStorage.setItem('conversationLoaded', 'true');
    
    // Save the new empty conversation
    saveConversation();
}

