<?php
require_once '../../../admin/config/dbcon.php';
require_once 'PayMongoHelper.php';

// Initialize PayMongo API
$payMongo = new PayMongoHelper();

// Get the base URL of your website
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$webhookUrl = $baseUrl . "/BYD-Clothing-E-Commerce-main/shop/functions/paymongo/webhook_handler.php";

try {
    // Create webhook for payment events
    $response = $payMongo->createWebhook($webhookUrl, ['payment.paid', 'payment.failed']);
    
    echo "<h2>Webhook Setup Result</h2>";
    echo "<pre>";
    print_r($response);
    echo "</pre>";
    
    // Store webhook ID in database for future reference
    if (isset($response['data']['id'])) {
        $webhookId = $response['data']['id'];
        
        // Check if webhooks table exists, create if not
        $conn->query("CREATE TABLE IF NOT EXISTS webhooks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            webhook_id VARCHAR(255) NOT NULL,
            url VARCHAR(255) NOT NULL,
            events TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Insert webhook info
        $stmt = $conn->prepare("INSERT INTO webhooks (webhook_id, url, events) VALUES (?, ?, ?)");
        $eventsJson = json_encode(['payment.paid', 'payment.failed']);
        $stmt->bind_param("sss", $webhookId, $webhookUrl, $eventsJson);
        
        if ($stmt->execute()) {
            echo "<p>Webhook information saved to database.</p>";
        } else {
            echo "<p>Error saving webhook information: " . $stmt->error . "</p>";
        }
        
        $stmt->close();
    }
    
} catch (Exception $e) {
    echo "<h2>Error Setting Up Webhook</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}

// List existing webhooks
try {
    $webhooks = $payMongo->listWebhooks();
    
    echo "<h2>Existing Webhooks</h2>";
    echo "<pre>";
    print_r($webhooks);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p>Error listing webhooks: " . $e->getMessage() . "</p>";
}