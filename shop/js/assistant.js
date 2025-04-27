let currentController = null;
let currentMessageId = null;
let latestBotMessageId = null;
let conversationHistory;
const chatbot = "deepseek/deepseek-chat-v3-0324:free";
//OPEN CHATBOT
document.addEventListener('DOMContentLoaded', function() {
    // Check if chat elements exist on this page before initializing
    if (document.getElementById('chat-container')) {
        initializeBot();
        
        // Set up event listeners only if elements exist
        document.getElementById('chat-bubble')?.addEventListener('click', function() {
            document.getElementById('chat-container').classList.add('active');
            document.getElementById('chat-bubble').classList.add('hidden');
            sessionStorage.setItem('chatState', 'open');
        });

        document.getElementById('close-chat')?.addEventListener('click', function() {
            document.getElementById('chat-container').classList.remove('active');
            document.getElementById('chat-bubble').classList.remove('hidden');
            sessionStorage.setItem('chatState', 'closed');
            
            // Save conversation when closing chat
            if (conversationHistory) {
                saveConversation();
            }
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

        // New agent tool event listeners
        document.getElementById('search-products')?.addEventListener('click', function() {
            document.getElementById('userInput').value = "Show me your t-shirts";
            sendMessage();
        });
        
        document.getElementById('show-cart')?.addEventListener('click', function() {
            window.location.href = '../shop/cart.php';
        });
        
        document.getElementById('track-order')?.addEventListener('click', function() {
            document.getElementById('userInput').value = "I want to track my order";
            sendMessage();
        });
        
        document.getElementById('get-recommendations')?.addEventListener('click', function() {
            document.getElementById('userInput').value = "Recommend some clothing for me";
            sendMessage();
        });
        
        // Add event listener for page unload to ensure state is saved
        window.addEventListener('beforeunload', function() {
            if (conversationHistory) {
                // Force synchronous localStorage save before navigating away
                try {
                    localStorage.setItem('conversationHistory', JSON.stringify(conversationHistory));
                    localStorage.setItem('conversationTimestamp', Date.now().toString());
                    console.log('Saved conversation on page unload');
                } catch (e) {
                    console.error('Error saving conversation on page unload:', e);
                }
                
                // Server-side save happens in the background
                // but may not complete if page is navigating away
                saveConversation();
            }
        });
        
        // Add a periodic save every 30 seconds for safeguard
        setInterval(() => {
            if (conversationHistory) {
                saveConversation();
            }
        }, 30000);
    } else {
        console.log('Chat container not found on this page, skipping initialization');
    }
});

//FUNCTIONS TO SAVE/CLEAR CONVERSATIONS
async function checkUserAuth() {
    try {
        const response = await fetch('/shop/functions/chatbot/check-auth.php');
        const data = await response.json();
        return data.isLoggedIn;
    } catch (error) {
        console.error('Error checking auth status:', error);
        return false;
    }
}
async function loadConversation() {
    try {
        // Always check local storage first with improved error handling
        try {
            const savedConversation = localStorage.getItem('conversationHistory');
            if (savedConversation) {
                const parsed = JSON.parse(savedConversation);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    console.log('Successfully loaded conversation from localStorage');
                    return parsed;
                }
            }
        } catch (localStorageError) {
            console.error('Error reading from localStorage:', localStorageError);
        }

        // Fallback to server with absolute paths
        const isLoggedIn = await checkUserAuth();
        if (!isLoggedIn) {
            console.log('User not logged in, cannot load conversation from server');
            return false;
        }

        // Use absolute path starting with / to ensure consistency across pages
        const response = await fetch('/shop/functions/chatbot/conversation-handler.php', {
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
        // Robust localStorage saving first (always try this)
        try {
            if (conversationHistory && Array.isArray(conversationHistory)) {
                localStorage.setItem('conversationHistory', JSON.stringify(conversationHistory));
                localStorage.setItem('conversationTimestamp', Date.now().toString());
                console.log('Saved conversation to localStorage at', new Date().toISOString());
            }
        } catch (localStorageError) {
            console.error('Error saving to localStorage:', localStorageError);
        }
        
        // Then try server-side if user is logged in
        const isLoggedIn = await checkUserAuth();
        if (!isLoggedIn) {
            console.log('User not logged in, saving only to local storage');
            return false;
        }

        const response = await fetch('/shop/functions/chatbot/conversation-handler.php', {
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
        console.log('Server save response:', data);
        
        return data.status === 'success';
    } catch (error) {
        console.error('Exception saving conversation:', error);
        return false;
    }
} 
async function clearServerConversation() {
    try {
        const isLoggedIn = await checkUserAuth();
        if (!isLoggedIn) return false;

        const response = await fetch('/shop/functions/chatbot/conversation-handler.php', {
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
            const response = await fetch('/shop/functions/chatbot/product-data.php');
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
                        let hasStock = false;
                        
                        for (const size in product.stock_by_size) {
                            if (product.stock_by_size[size] > 0) {
                                availableSizes.push(`${size}:${product.stock_by_size[size]}`);
                                hasStock = true;
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
                    // Basic product information always included if product data is requested
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
    // First, check if user is logged in
    const isLoggedIn = await checkUserAuth();
    
    // Try to load conversation from localStorage first
    try {
        const savedConversation = localStorage.getItem('conversationHistory');
        if (savedConversation) {
            const parsed = JSON.parse(savedConversation);
            if (Array.isArray(parsed) && parsed.length > 0) {
                console.log('Successfully loaded conversation from localStorage');
                conversationHistory = parsed;
                
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
                
                // Mark that we've loaded the conversation for this session
                sessionStorage.setItem('conversationLoaded', 'true');
                return; // Exit early since we restored from localStorage
            }
        }
    } catch (error) {
        console.error('Error loading from localStorage:', error);
    }

    // If localStorage failed, try server-side storage (if user is logged in)
    if (isLoggedIn) {
        try {
            const savedConversation = await loadConversation();
            if (savedConversation) {
                conversationHistory = savedConversation;
                
                // Create initial basic system prompt without product data
                const baseSystemPrompt = await createDynamicSystemPrompt(false);
                
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
                
                // Mark that we've loaded the conversation for this session
                sessionStorage.setItem('conversationLoaded', 'true');
                return; // Exit early since we restored from server
            }
        } catch (serverError) {
            console.error('Error loading from server:', serverError);
        }
    }

    // If we get here, both localStorage and server loading failed, so create a new conversation
    try {
        // Create initial basic system prompt without product data
        const baseSystemPrompt = await createDynamicSystemPrompt(false);
        
        // Try to get username
        let username = null;
        try {
            const userResponse = await fetch('/shop/functions/chatbot/get-username.php');
            const userData = await userResponse.json();
            if (userData.status === 'success' && userData.username) {
                username = userData.username;
            }
        } catch (error) {
            console.error('Error getting username:', error);
        }
        
        // Get dynamic greeting
        const greeting = getDynamicGreeting(username);
        
        // Create new conversation with dynamic greeting
        conversationHistory = [
            {"role": "system", "content": baseSystemPrompt},
            {"role": "assistant", "content": greeting}
        ];
        
        // Update the UI to show the greeting
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML = `
            <div class="message bot-message">
                <div class="message-content">
                    <p>${greeting}</p>
                </div>
            </div>
        `;
        
        // Mark that we've loaded the conversation for this session
        sessionStorage.setItem('conversationLoaded', 'true');
        
        // Save this initial conversation
        await saveConversation();
        
    } catch (error) {
        console.error('Error initializing bot:', error);
        // Fallback to generic greeting on error
        const genericGreeting = getDynamicGreeting();
        conversationHistory = [
            {"role": "system", "content": await createDynamicSystemPrompt(false)},
            {"role": "assistant", "content": genericGreeting}
        ];
        
        // Update UI with generic greeting on error
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.innerHTML = `
            <div class="message bot-message">
                <div class="message-content">
                    <p>${genericGreeting}</p>
                </div>
            </div>
        `;
    }
}

// AGENT FUNCTIONALITY
const agentTools = {
    searchProducts: async (params) => {
        showAgentAction("Searching products...");
        try {
            const response = await fetch('/shop/functions/chatbot/product-search.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(params)
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const results = await response.json();
            return {
                status: 'success',
                data: results,
                message: `Found ${results.length} products matching your criteria.`
            };
        } catch (error) {
            console.error('Error searching products:', error);
            return { 
                status: 'error', 
                message: 'Sorry, I could not search for products at this time.' 
            };
        } finally {
            hideAgentAction();
        }
    },
    
    addToCart: async (productId, size, quantity = 1) => {
        showAgentAction("Adding to cart...");
        try {
            const response = await fetch('/shop/functions/cart_functions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    size: size,
                    quantity: quantity
                })
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const result = await response.json();
            return {
                status: result.status || 'error',
                message: result.message || 'Could not add product to cart.'
            };
        } catch (error) {
            console.error('Error adding to cart:', error);
            return { 
                status: 'error', 
                message: 'Sorry, I could not add this item to your cart.' 
            };
        } finally {
            hideAgentAction();
        }
    },
    
    trackOrder: async (orderId) => {
        showAgentAction("Tracking order...");
        try {
            const response = await fetch('/shop/functions/chatbot/track-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const result = await response.json();
            return {
                status: result.status || 'error',
                data: result.data,
                message: result.message || 'Could not track your order.'
            };
        } catch (error) {
            console.error('Error tracking order:', error);
            return { 
                status: 'error', 
                message: 'Sorry, I could not track your order at this time.' 
            };
        } finally {
            hideAgentAction();
        }
    },
    
    getRecommendations: async (preferences = {}) => {
        showAgentAction("Finding recommendations...");
        try {
            const response = await fetch('/shop/functions/chatbot/recommendations.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(preferences)
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const results = await response.json();
            return {
                status: 'success',
                data: results,
                message: `Here are some recommendations based on your preferences.`
            };
        } catch (error) {
            console.error('Error getting recommendations:', error);
            return { 
                status: 'error', 
                message: 'Sorry, I could not get recommendations at this time.' 
            };
        } finally {
            hideAgentAction();
        }
    }
};

// Action indicators
function showAgentAction(message) {
    const indicator = document.getElementById('agent-action-indicator');
    const actionText = document.getElementById('agent-action-text');
    
    if (actionText) actionText.textContent = message || "Performing action...";
    if (indicator) indicator.classList.add('active');
}

function hideAgentAction() {
    const indicator = document.getElementById('agent-action-indicator');
    if (indicator) indicator.classList.remove('active');
}

// Process function call response from the AI
async function processFunctionCall(functionCall) {
    try {
        const functionName = functionCall.name;
        const argumentsStr = functionCall.arguments;
        let args;
        
        try {
            args = JSON.parse(argumentsStr);
        } catch (error) {
            console.error('Invalid function arguments:', error);
            return {
                status: 'error',
                message: 'Could not parse function arguments.'
            };
        }
        
        console.log(`Executing agent function: ${functionName}`, args);
        
        switch (functionName) {
            case 'searchProducts':
                return await agentTools.searchProducts(args);
                
            case 'addToCart':
                return await agentTools.addToCart(args.productId, args.size, args.quantity);
                
            case 'trackOrder':
                return await agentTools.trackOrder(args.orderId);
                
            case 'getRecommendations':
                return await agentTools.getRecommendations(args.preferences);
                
            default:
                console.error('Unknown function call:', functionName);
                return {
                    status: 'error',
                    message: `Unknown function: ${functionName}`
                };
        }
    } catch (error) {
        console.error('Error processing function call:', error);
        return {
            status: 'error',
            message: 'An error occurred while processing your request.'
        };
    }
}

// Enhanced sendMessage function with function calling capability
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
        
        // Create controller for the fetch request
        currentController = new AbortController();
        const signal = currentController.signal;
        
        // Add function calling capabilities to the API request
        const requestBody = {
            "model": chatbot,
            "messages": trimmedHistory,
            "stream": true,
            "tools": [
                {
                    "type": "function",
                    "function": {
                        "name": "searchProducts",
                        "description": "Search for products with specified filters",
                        "parameters": {
                            "type": "object",
                            "properties": {
                                "category": {
                                    "type": "string",
                                    "enum": ["tshirt", "longslv"],
                                    "description": "Product category (t-shirt or long sleeve)"
                                },
                                "query": {
                                    "type": "string",
                                    "description": "Text to search for in product names or descriptions"
                                },
                                "minPrice": {
                                    "type": "number",
                                    "description": "Minimum price filter"
                                },
                                "maxPrice": {
                                    "type": "number",
                                    "description": "Maximum price filter"
                                },
                                "inStock": {
                                    "type": "boolean",
                                    "description": "Filter for products in stock only"
                                }
                            },
                            "required": ["category"]
                        }
                    }
                },
                {
                    "type": "function",
                    "function": {
                        "name": "addToCart",
                        "description": "Add a product to the user's shopping cart",
                        "parameters": {
                            "type": "object",
                            "properties": {
                                "productId": {
                                    "type": "integer",
                                    "description": "ID of the product to add to cart"
                                },
                                "size": {
                                    "type": "string",
                                    "enum": ["S", "M", "L", "XL"],
                                    "description": "Size of the product"
                                },
                                "quantity": {
                                    "type": "integer",
                                    "description": "Quantity to add (defaults to 1)"
                                }
                            },
                            "required": ["productId", "size"]
                        }
                    }
                },
                {
                    "type": "function",
                    "function": {
                        "name": "trackOrder",
                        "description": "Track the status of a user's order",
                        "parameters": {
                            "type": "object",
                            "properties": {
                                "orderId": {
                                    "type": "string",
                                    "description": "Order ID to track"
                                }
                            },
                            "required": ["orderId"]
                        }
                    }
                },
                {
                    "type": "function",
                    "function": {
                        "name": "getRecommendations",
                        "description": "Get personalized product recommendations",
                        "parameters": {
                            "type": "object",
                            "properties": {
                                "preferences": {
                                    "type": "object",
                                    "properties": {
                                        "style": {
                                            "type": "string",
                                            "description": "Preferred style (casual, formal, etc.)"
                                        },
                                        "color": {
                                            "type": "string",
                                            "description": "Preferred color"
                                        },
                                        "priceRange": {
                                            "type": "string",
                                            "description": "Price range (budget, mid-range, premium)"
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            ]
        };
        
        // Send the enhanced request with function calling capability
        const response = await fetch("/shop/functions/chatbot/openrouter-proxy.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(requestBody),
            signal: signal
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error?.message || 'API request failed');
        }
        
        const reader = response.body.getReader();
        const decoder = new TextDecoder("utf-8");
        
        let fullMessage = "";
        let fullResponse = {}; // Store the complete response object
        let functionCallDetected = false;
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
                        
                        // Handle function calling
                        if (jsonData.choices[0]?.delta?.tool_calls) {
                            functionCallDetected = true;
                            
                            // Build the complete function call object
                            if (!fullResponse.tool_calls) {
                                fullResponse.tool_calls = jsonData.choices[0].delta.tool_calls;
                            } else {
                                const newToolCalls = jsonData.choices[0].delta.tool_calls;
                                for (let i = 0; i < newToolCalls.length; i++) {
                                    if (!fullResponse.tool_calls[i]) {
                                        fullResponse.tool_calls[i] = newToolCalls[i];
                                    } else {
                                        // Append to function arguments if they exist
                                        if (newToolCalls[i].function?.arguments) {
                                            if (!fullResponse.tool_calls[i].function) {
                                                fullResponse.tool_calls[i].function = {};
                                            }
                                            
                                            if (!fullResponse.tool_calls[i].function.arguments) {
                                                fullResponse.tool_calls[i].function.arguments = '';
                                            }
                                            
                                            fullResponse.tool_calls[i].function.arguments += 
                                                newToolCalls[i].function.arguments;
                                        }
                                        
                                        // Set function name if it exists
                                        if (newToolCalls[i].function?.name) {
                                            if (!fullResponse.tool_calls[i].function) {
                                                fullResponse.tool_calls[i].function = {};
                                            }
                                            
                                            fullResponse.tool_calls[i].function.name = 
                                                newToolCalls[i].function.name;
                                        }
                                    }
                                }
                            }
                            
                            // Show "thinking" message while building function call
                            if (isFirstChunk) {
                                const loadingIndicator = document.getElementById(`${messageId}-loading`);
                                if (loadingIndicator) loadingIndicator.remove();
                                isFirstChunk = false;
                                streamingContent.innerHTML = "<p>Thinking...</p>";
                            }
                        } 
                        // Regular content delta
                        else if (jsonData.choices[0]?.delta?.content) {
                            const contentDelta = jsonData.choices[0].delta.content || '';
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
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                    }
                }
            }
        }
        
        // After the stream completes, handle any function calls
        if (functionCallDetected && fullResponse.tool_calls && fullResponse.tool_calls.length > 0) {
            // Process the first function call
            const functionCall = fullResponse.tool_calls[0].function;
            
            // Execute the function
            const result = await processFunctionCall(functionCall);
            
            // Show the function result in a structured format
            let resultMessage = '';
            
            if (result.status === 'success') {
                if (functionCall.name === 'searchProducts' && result.data && result.data.length > 0) {
                    resultMessage = `<div class="agent-result-header">Here are the products I found:</div>
                    <div class="agent-results">`;
                    
                    result.data.forEach(product => {
                        const finalPrice = product.discount_percentage > 0 
                            ? Math.round(product.original_price * (1 - (product.discount_percentage / 100)))
                            : product.original_price;
                        
                        resultMessage += `
                        <div class="agent-result-item" onclick="window.location.href='/shop/product.php?id=${product.id}'">
                            <div class="agent-card">
                                <img src="/shop/assets/img/products/${product.image}" class="agent-card-image" alt="${product.name}">
                                <div class="agent-card-content">
                                    <div class="agent-card-title">${product.name}</div>
                                    <div class="agent-card-price">
                                        ${product.discount_percentage > 0 
                                            ? `<span class="original">₱${product.original_price}</span> 
                                              <span class="discount">₱${finalPrice}</span>`
                                            : `<span>₱${product.original_price}</span>`}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    
                    resultMessage += `</div>
                    <div class="agent-action-prompt">
                        <p>Click on any product to view details. Is there a specific product you'd like to know more about?</p>
                    </div>`;
                }
                else if (functionCall.name === 'addToCart') {
                    resultMessage = `<p>${result.message}</p>
                    <div class="agent-action-buttons">
                        <button class="bot-action-button" onclick="window.location.href='/shop/cart.php'">
                            View Cart
                        </button>
                        <button class="bot-action-button" onclick="window.location.href='/shop/products.php'">
                            Continue Shopping
                        </button>
                    </div>`;
                }
                else if (functionCall.name === 'trackOrder' && result.data) {
                    resultMessage = `<p>Order #${result.data.order_id}:</p>
                    <ul>
                        <li>Status: ${result.data.status}</li>
                        <li>Date: ${result.data.date}</li>
                        ${result.data.tracking_number ? `<li>Tracking #: ${result.data.tracking_number}</li>` : ''}
                        <li>Estimated Delivery: ${result.data.estimated_delivery || 'Not available'}</li>
                    </ul>`;
                }
                else if (functionCall.name === 'getRecommendations' && result.data && result.data.length > 0) {
                    resultMessage = `<div class="agent-result-header">Here are some recommendations for you:</div>
                    <div class="agent-results">`;
                    
                    result.data.forEach(product => {
                        const finalPrice = product.discount_percentage > 0 
                            ? Math.round(product.original_price * (1 - (product.discount_percentage / 100)))
                            : product.original_price;
                        
                        resultMessage += `
                        <div class="agent-result-item" onclick="window.location.href='/shop/product.php?id=${product.id}'">
                            <div class="agent-card">
                                <img src="/shop/assets/img/products/${product.image}" class="agent-card-image" alt="${product.name}">
                                <div class="agent-card-content">
                                    <div class="agent-card-title">${product.name}</div>
                                    <div class="agent-card-price">
                                        ${product.discount_percentage > 0 
                                            ? `<span class="original">₱${product.original_price}</span> 
                                              <span class="discount">₱${finalPrice}</span>`
                                            : `<span>₱${product.original_price}</span>`}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    
                    resultMessage += `</div>`;
                }
                else {
                    resultMessage = `<p>${result.message || 'Action completed successfully.'}</p>`;
                }
            } else {
                resultMessage = `<p class="error-message">${result.message || 'Error performing action.'}</p>`;
            }
            
            // Display the result in the chat
            streamingContent.innerHTML = resultMessage;
            
            // Add a follow-up message from the assistant
            // Request a follow-up message from the AI based on the function result
            const followUpPrompt = `The function ${functionCall.name} has been executed with result: ${JSON.stringify(result)}. Please provide a helpful follow-up response to the user.`;
            
            // Add the follow-up message to conversation history
            conversationHistory.push({
                "role": "assistant",
                "content": resultMessage
            });
            
            // Now request a follow-up message
            const followUpMessageId = 'msg-' + (Date.now() + 1);
            chatMessages.innerHTML += `
            <div class="message bot-message" id="${followUpMessageId}">
                <div class="message-content" id="${followUpMessageId}-content">
                    <div class="loading-dots py-2" id="${followUpMessageId}-loading">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>`;
            
            // Create a new request for follow-up
            const followUpResponse = await fetch("/shop/functions/chatbot/openrouter-proxy.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    "model": chatbot,
                    "messages": [...trimmedHistory, 
                        {"role": "assistant", "content": "I need to use a function to answer this."},
                        {"role": "assistant", "content": resultMessage},
                        {"role": "system", "content": followUpPrompt}
                    ],
                    "stream": true
                })
            });
            
            if (!followUpResponse.ok) {
                throw new Error('Follow-up API request failed');
            }
            
            // Process the follow-up response stream
            const followUpReader = followUpResponse.body.getReader();
            let followUpMessage = "";
            const followUpStreamingContent = document.getElementById(`${followUpMessageId}-content`);
            let isFollowUpFirstChunk = true;
            
            while (true) {
                const { done, value } = await followUpReader.read();
                if (done) break;
                
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
                                if (isFollowUpFirstChunk) {
                                    const loadingIndicator = document.getElementById(`${followUpMessageId}-loading`);
                                    if (loadingIndicator) loadingIndicator.remove();
                                    isFollowUpFirstChunk = false;
                                }
                                
                                followUpMessage += contentDelta;
                                followUpStreamingContent.innerHTML = marked.parse(followUpMessage);
                                chatMessages.scrollTop = chatMessages.scrollHeight;
                            }
                        } catch (e) {
                            console.error('Error parsing follow-up JSON:', e);
                        }
                    }
                }
            }
            
            // Add the follow-up to conversation history
            conversationHistory.push({
                "role": "assistant", 
                "content": followUpMessage
            });
        }
        else {
            // No function call, just add the regular message to history
            conversationHistory.push({
                "role": "assistant", 
                "content": fullMessage
            });
        }
        
        // Save full conversation
        await saveConversation();
        
        const feedbackElement = document.getElementById(`${messageId}-feedback`);
        if (feedbackElement) {
            feedbackElement.style.display = 'flex';
        }
        
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
    } finally {
        document.getElementById("sendIcon").classList.remove("d-none");
        document.getElementById("stopIcon").classList.add("d-none");
        inputElem.disabled = false;
        currentController = null;
    }
}

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

function getDynamicGreeting(username = null) {
    const hour = new Date().getHours();
    let greeting = "";
    
    if (hour >= 5 && hour < 12) {
        greeting = "Good morning";
    } else if (hour >= 12 && hour < 18) {
        greeting = "Good afternoon";
    } else {
        greeting = "Good evening";
    }
    
    if (username) {
        return `${greeting}, ${username}! How can I help you with BYD-CLOTHING products today?`;
    } else {
        return `${greeting}! How can I help you with BYD-CLOTHING products today?`;
    }
}

function sendORstop() {
    // Check if there's an active generation happening
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
        // No active generation, just send the message
        sendMessage();
    }
}
async function clearChat() {
    // Get the username if available
    let username = null;
    try {
        const response = await fetch('/shop/functions/chatbot/get-username.php');
        const data = await response.json();
        if (data.status === 'success' && data.username) {
            username = data.username;
        }
    } catch (error) {
        console.error('Error getting username:', error);
    }
    
    // Get dynamic greeting
    const greeting = getDynamicGreeting(username);
    
    // Clear UI with dynamic greeting
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.innerHTML = `
        <div class="message bot-message">
            <div class="message-content">
                <p>${greeting}</p>
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
        {"role": "assistant", "content": greeting}
    ];
    
    // Mark as new conversation
    sessionStorage.setItem('conversationLoaded', 'true');
    
    // Save the new empty conversation
    saveConversation();
}