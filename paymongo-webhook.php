<?php
// paymongo-webhook.php

// 1. Handle requests to old and new URLs
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$validPaths = [
    '/paymongo-webhook.php', // Correct path (with validation)
    '/shop/functions/paymongo/paymongo-webhook.php', // Old path 1 (no validation)
    '/shop/functions/paymongo/paymongo-webhook' // Old path 2 (no validation)
];

if (in_array($requestUri, $validPaths)) {
    // 2. Set timezone
    date_default_timezone_set('Asia/Manila');
    
    // 3. Get payload and signature
    $payload = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_PAYMONGO_SIGNATURE'] ?? '';
    
    // 4. Validate signature ONLY for correct path
    $isValidSignature = false;
    if ($requestUri === '/paymongo-webhook.php') {
        $WEBHOOK_SECRET = getenv('PAYMONGO_WEBHOOK_SECRET');
        if (!empty($WEBHOOK_SECRET)) {
            $computedSignature = hash_hmac('sha256', $payload, $WEBHOOK_SECRET);
            $isValidSignature = hash_equals($signature, $computedSignature);
            
            // Reject invalid signatures for correct path
            if (!$isValidSignature) {
                error_log("❌ Invalid signature for correct path");
                http_response_code(403);
                die('Invalid signature');
            }
        }
    }

    // 5. Always return 200 first (except for invalid sig on correct path)
    http_response_code(200);
    echo 'Webhook received';
    
    // 6. Process event after response
    try {
        $event = json_decode($payload, true);
        error_log("Received event at {$requestUri}: " . print_r($event, true));
        
        $eventType = $event['data']['attributes']['type'] ?? 'unknown';
        error_log("Event Type: {$eventType}");
        
        // For correct path only
        if ($requestUri === '/paymongo-webhook.php') {
            error_log("✅ Valid signature confirmed");
            // Add your payment processing logic here
        }
        
    } catch (Exception $e) {
        error_log("Error processing event: " . $e->getMessage());
    }
    
    exit();
}

// 7. Return 404 for other paths
http_response_code(404);
echo 'Not Found';
?>