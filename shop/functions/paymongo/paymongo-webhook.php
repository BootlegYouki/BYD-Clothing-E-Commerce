<?php
// paymongo-webhook.php

// 1. Set timezone to match PayMongo's servers
date_default_timezone_set('Asia/Manila');

// 2. Retrieve webhook secret from Heroku
$WEBHOOK_SECRET = getenv('PAYMONGO_WEBHOOK_SECRET');

// 2a. Check if secret is configured
if (empty($WEBHOOK_SECRET)) {
    error_log("❌ PAYMONGO_WEBHOOK_SECRET is not set in Heroku config");
    http_response_code(500);
    die("Server error: Missing webhook secret");
}

// 3. Get raw payload and signature header
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_PAYMONGO_SIGNATURE'] ?? '';

// 4. Debug logging (temporary)
error_log("Raw Payload: " . $payload);
error_log("Received Signature: " . $signature);
error_log("Stored Secret: " . $WEBHOOK_SECRET);

// 5. Signature validation function
function verifySignature($payload, $signature, $secret) {
    $computedSignature = hash_hmac('sha256', $payload, $secret);
    error_log("Computed Signature: " . $computedSignature);
    return hash_equals($signature, $computedSignature);
}

// 6. Validate signature
if (!verifySignature($payload, $signature, $WEBHOOK_SECRET)) {
    error_log("❌ Signature validation failed");
    http_response_code(403);
    die('Invalid signature');
}

// 7. Process valid event
try {
    $event = json_decode($payload, true);
    error_log("Valid event received: " . print_r($event, true));

    switch ($event['data']['attributes']['type'] ?? '') {
        case 'payment.paid':
            $paymentId = $event['data']['attributes']['data']['id'];
            error_log("✅ Payment succeeded: $paymentId");
            // Add order fulfillment logic
            break;
            
        case 'payment.failed':
            $paymentId = $event['data']['attributes']['data']['id'];
            error_log("❌ Payment failed: $paymentId");
            // Add failure handling
            break;
            
        default:
            error_log("⚠️ Unhandled event type: " . ($event['data']['attributes']['type'] ?? 'unknown'));
    }

    http_response_code(200);
    echo 'Webhook processed';

} catch (Exception $e) {
    error_log("Error processing event: " . $e->getMessage());
    http_response_code(500);
    echo 'Error processing webhook';
}
?>