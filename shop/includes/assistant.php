<div class="chat-bubble" id="chat-bubble">
        <i class='bx bxs-chat'></i>
    </div>
<div class="chat-container" id="chat-container">
    <div class="chat-header">
        <i class="fas fa-robot"></i>
        <h2>BYD Clothing AI Assistant</h2>
        <button class="close-btn" id="close-chat">
            <i class="fas fa-times"></i>
        </button>
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