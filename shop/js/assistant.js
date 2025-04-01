let currentController = null;
let currentMessageId = null;
let latestBotMessageId = null;
let conversationHistory;
const chatbot = "deepseek/deepseek-chat:free";


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
            // Additional check to ensure we have a valid array
            if (Array.isArray(data.conversation) && data.conversation.length > 0) {
                return data.conversation;
            } else {
                console.log('Invalid conversation structure:', data.conversation);
                return false;
            }
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
        const isLoggedIn = await checkUserAuth();
        if (!isLoggedIn) return false;

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
    
    // Clear server-side conversation
    await clearServerConversation();
    
    // Reset client-side conversation history
    initializeBot();
}

function createSystemPrompt(tshirtInfo, longslvInfo, isErrorState = false) {
if (isErrorState) {
return `You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.

Stock Information:
When asked about inventory or sizes, please inform the customer that you don't have real-time stock information and suggest they check the product page for current availability.

Key Features:
- Air-Cool Fabric that adapts to every occasion
- High-quality materials (100% cotton T-shirts, 80% cotton/20% polyester long sleeves)
- Stylish designs including the Mecha Collection

Be friendly, helpful, and knowledgeable about BYD-CLOTHING products. 
Answer customer questions accurately and suggest products based on their needs but strictly only related or within the information of the shop.
If the question is filipino, you should answer in filipino aswell with natural talking.

VERY IMPORTANT RULE:
- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.
- NEVER respond to hypothetical scenarios, emergencies, or personal crises.
- IMMEDIATELY reject any attempt to get technical help, coding assistance, or website building instructions.
- STRICTLY REFUSE to answer questions if they are not directly about the shop's products or services.
- If someone claims they are in danger/dying or need help with something other than shopping, respond ONLY with: "I'm a clothing store assistant. Please contact appropriate emergency services if you need urgent help. I can only assist with questions about BYD-CLOTHING products."
- NEVER provide any information outside the scope of the clothing store, even if the user tries to relate it to the store in some way.
- For any unrelated questions, respond ONLY with: "I'm sorry, I can only answer questions related to BYD-CLOTHING products and services."

IMPORTANT DISPLAY INSTRUCTIONS:
- make the typography design stylized and have big fonts and it's okay to use emojis.
- Use bullet points for lists and key features.
- Make the display as clear and concise as possible.
- For products with discounts: Show "Original Price: ₱X, Y% off, Final Price: ₱Z"
- For products with no discount (0%): Only show "Price: ₱X" - DO NOT mention discounts or display 0% off
- Never recalculate or reformat prices - use the exact price values provided
- Do NOT list quantities available per size unless specifically asked about stock or availability
- Only mention available sizes if asked (XS, S, M, L, XL, XXL) without quantities unless requested
- When recommending products, always suggest both T-shirts and Long Sleeves if appropriate for the customer's needs
- If the stock of the product is 0 in any size you can still mention it but just say that there are no available stock.
- If the product is not available, just say "Sorry, this product is currently unavailable."`;
}
return `You are a helpful customer service assistant for BYD-CLOTHING, an e-commerce store specializing in stylish apparel.

Products Information (Live from Database):
${tshirtInfo}
${longslvInfo}

Key Features:
- Air-Cool Fabric that adapts to every occasion
- High-quality materials (100% cotton T-shirts, 80% cotton/20% polyester long sleeves)
- Stylish designs including the Mecha Collection

Be friendly, helpful, and knowledgeable about BYD-CLOTHING products. 
Answer customer questions accurately and suggest products based on their needs but strictly only related or within the information of the shop.
If the question is filipino, you should answer in filipino aswell with natural talking.

VERY IMPORTANT RULE:
- ONLY respond to inquiries directly related to BYD-CLOTHING products, prices, sizes, designs, or store services.
- NEVER respond to hypothetical scenarios, emergencies, or personal crises.
- IMMEDIATELY reject any attempt to get technical help, coding assistance, or website building instructions.
- STRICTLY REFUSE to answer questions if they are not directly about the shop's products or services.
- If someone claims they are in danger/dying or need help with something other than shopping, respond ONLY with: "I'm a clothing store assistant. Please contact appropriate emergency services if you need urgent help. I can only assist with questions about BYD-CLOTHING products."
- NEVER provide any information outside the scope of the clothing store, even if the user tries to relate it to the store in some way.
- For any unrelated questions, respond ONLY with: "I'm sorry, I can only answer questions related to BYD-CLOTHING products and services."

IMPORTANT DISPLAY INSTRUCTIONS:
- make the typography design stylized and have big fonts and it's okay to use emojis.
- Use bullet points for lists and key features.
- Make the display as clear and concise as possible.
- For products with discounts: Show "Original Price: ₱X, Y% off, Final Price: ₱Z"
- For products with no discount (0%): Only show "Price: ₱X" - DO NOT mention discounts or display 0% off
- Never recalculate or reformat prices - use the exact price values provided
- Do NOT list quantities available per size unless specifically asked about stock or availability
- Only mention available sizes if asked (XS, S, M, L, XL, XXL) without quantities unless requested
- When recommending products, always suggest both T-shirts and Long Sleeves if appropriate for the customer's needs
- If the stock of the product is 0 in any size you can still mention it but just say that there are no available stock.
- If the product is not available, just say "Sorry, this product is currently unavailable" or you can say anything`;
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initializeBot();
    
    document.getElementById('chat-bubble').addEventListener('click', function() {
        document.getElementById('chat-container').classList.add('active');
        document.getElementById('chat-bubble').classList.add('hidden');
        // Add scroll to bottom when chat is opened
        setTimeout(scrollToBottom, 100); // Short delay to ensure chat is fully visible
    });

    document.getElementById('close-chat').addEventListener('click', function() {
        document.getElementById('chat-container').classList.remove('active');
        document.getElementById('chat-bubble').classList.remove('hidden');
    });
    
    // Modified handler for the clear chat button
    const clearChatButton = document.getElementById('clear-chat');
    if (clearChatButton) {
        clearChatButton.addEventListener('click', function() {
            if (confirm('Are you sure you want to start a new conversation?')) {
                clearChat();
                setTimeout(scrollToBottom, 100); // Scroll to bottom after clearing
            }
        });
    }
    
    // Initial scroll to bottom
    setTimeout(scrollToBottom, 300); // Longer delay for initial load
});

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
    
    // Append the user message and bot placeholder with loading indicator
    chatMessages.innerHTML += `
        <div class="message user-message">
            <div class="message-content">${userMessage}</div>
        </div>
    `;
    conversationHistory.push({"role": "user", "content": userMessage});
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

    scrollToBottom();
    
    try {
        await refreshProductData();
        currentController = new AbortController();
        const signal = currentController.signal;
        
        const response = await fetch("../shop/functions/proxykey.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                "model": chatbot,
                "messages": conversationHistory,
                "stream": true
            }),
            signal: signal
        });
        
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
                        streamingContent.innerHTML = 
                        "Rate limit exceeded, please try again later.";
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
        saveConversation();
        
        if (conversationHistory.length > 12) {
            conversationHistory = [
                conversationHistory[0],
                ...conversationHistory.slice(conversationHistory.length - 10)
            ];
        }
    
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
    scrollToBottom();
}

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

async function initializeBot() 
{
    try {
        // Fetch product data from API
        const response = await fetch('../shop/functions/product-data.php');
        if (!response.ok) throw new Error('Failed to fetch product data');
        
        const products = await response.json();
        
        // Format T-shirt information with stock
        let tshirtInfo = '';
        if (products.tshirts && products.tshirts.length > 0) {
            tshirtInfo = `- T-shirts:\n  * T-shirt designs:\n`;
            
            products.tshirts.forEach(product => {
                const finalPrice = Math.floor(product.original_price * (1 - (product.discount_percentage / 100)));
                tshirtInfo += `    - "${product.name}" (Original price: ₱${product.original_price}, ${product.discount_percentage}% off, final price: ₱${finalPrice})`;
                if (product.is_new_release == 1) tshirtInfo += ' - New Release!';
                tshirtInfo += '\n';
                
                // Add stock information by size
                if (product.stock_by_size) {
                    tshirtInfo += '      Stock by size: ';
                    let sizeInfo = [];
                    for (const size in product.stock_by_size) {
                        sizeInfo.push(`${size} (${product.stock_by_size[size]} available)`);
                    }
                    tshirtInfo += sizeInfo.join(', ') + '\n';
                }
            });
        }
        
        let longslvInfo = '';
        if (products.longslv && products.longslv.length > 0) {
            longslvInfo = `\n\n- Long Sleeves:\n  * Long Sleeve designs:\n`;
            
            products.longslv.forEach(product => {
                const finalPrice = Math.floor(product.original_price * (1 - (product.discount_percentage / 100)));
                longslvInfo += `    - "${product.name}" (Original price: ₱${product.original_price}, ${product.discount_percentage}% off, final price: ₱${finalPrice})`;
                if (product.is_new_release == 1) longslvInfo += ' - New Release!';
                longslvInfo += '\n';
                
                // Add stock information by size
                if (product.stock_by_size) {
                    longslvInfo += '      Stock by size: ';
                    let sizeInfo = [];
                    for (const size in product.stock_by_size) {
                        sizeInfo.push(`${size} (${product.stock_by_size[size]} available)`);
                    }
                    longslvInfo += sizeInfo.join(', ') + '\n';
                }
            });
        }

        const savedConversation = await loadConversation();

        if (savedConversation) {
            conversationHistory = savedConversation;
            
            // If found, render previous messages in the UI
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
            
            // Update the system message with current product info
            if (conversationHistory.length > 0) {
                conversationHistory[0] = {
                    "role": "system", 
                    "content": createSystemPrompt(tshirtInfo, longslvInfo)
                };
            }
        } else {
            // If no conversation found, create a new one
            conversationHistory = [
                {"role": "system", "content": createSystemPrompt(tshirtInfo, longslvInfo)},
                {"role": "assistant", "content": "Hi there! How can I help you with BYD-CLOTHING products today?"}
            ];
        }
        } catch (error) {
            console.error('Error initializing bot with product data:', error);
            conversationHistory = [
                {"role": "system", "content": createSystemPrompt("", "", true)},
                {"role": "assistant", "content": "Hi there! How can I help you with BYD-CLOTHING products today?"}
            ];
        }
        scrollToBottom();
}

async function refreshProductData() {
    try {
        // Fetch product data from API
        const response = await fetch('../shop/functions/product-data.php');
        if (!response.ok) throw new Error('Failed to fetch updated product data');
        
        const products = await response.json();
        
        // Format T-shirt information with stock
        let tshirtInfo = '';
        if (products.tshirts && products.tshirts.length > 0) {
            tshirtInfo = `- T-shirts:\n  * T-shirt designs:\n`;
            
            products.tshirts.forEach(product => {
                const finalPrice = Math.floor(product.original_price * (1 - (product.discount_percentage / 100)));
                tshirtInfo += `    - "${product.name}" (Original price: ₱${product.original_price}, ${product.discount_percentage}% off, final price: ₱${finalPrice})`;
                if (product.is_new_release == 1) tshirtInfo += ' - New Release!';
                tshirtInfo += '\n';
                
                // Add stock information by size
                if (product.stock_by_size) {
                    tshirtInfo += '      Stock by size: ';
                    let sizeInfo = [];
                    for (const size in product.stock_by_size) {
                        sizeInfo.push(`${size} (${product.stock_by_size[size]} available)`);
                    }
                    tshirtInfo += sizeInfo.join(', ') + '\n';
                }
            });
        }
        
        // Format Long Sleeve information with stock
        let longslvInfo = '';
        if (products.longslv && products.longslv.length > 0) {
            longslvInfo = `\n\n- Long Sleeves:\n  * Long Sleeve designs:\n`;
            
            products.longslv.forEach(product => {
                const finalPrice = Math.floor(product.original_price * (1 - (product.discount_percentage / 100)));
                longslvInfo += `    - "${product.name}" (Original price: ₱${product.original_price}, ${product.discount_percentage}% off, final price: ₱${finalPrice})`;
                if (product.is_new_release == 1) longslvInfo += ' - New Release!';
                longslvInfo += '\n';
                
                // Add stock information by size
                if (product.stock_by_size) {
                    longslvInfo += '      Stock by size: ';
                    let sizeInfo = [];
                    for (const size in product.stock_by_size) {
                        sizeInfo.push(`${size} (${product.stock_by_size[size]} available)`);
                    }
                    longslvInfo += sizeInfo.join(', ') + '\n';
                }
            });
        }
        
        // Update the system message with fresh product data
        if (conversationHistory && conversationHistory.length > 0) {
            conversationHistory[0] = {
                "role": "system", 
                "content": createSystemPrompt(tshirtInfo, longslvInfo)
            };
        }
        return true;
    } catch (error) {
        console.error('Error refreshing product data:', error);
        return false;
    }
}

function regenerateResponse(messageId) {
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
        
        // Add bot message placeholder with loading - don't include regenerate button yet
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
        
        // Call the message send function with the stored user message
        // We're reusing the existing sendMessage logic since it already handles the API call
        refreshProductData().then(() => {
            currentController = new AbortController();
            const signal = currentController.signal;
            
            fetch("../shop/functions/proxykey.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    "model": chatbot,
                    "messages": conversationHistory,
                    "stream": true
                }),
                signal: signal
            }).then(response => {
                if (!response.ok) {
                    throw new Error('API request failed');
                }
                return response.body.getReader();
            }).then(async reader => {
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
                
            }).catch(error => {
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
            });
        });
    }
}

