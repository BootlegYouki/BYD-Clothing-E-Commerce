<?php
// paymongo-webhook.php

// Load environment variables (use vlucas/phpdotenv if installed)
$WEBHOOK_SECRET = getenv('PAYMONGO_WEBHOOK_SECRET'); // Get this from PayMongo dashboard

// Get raw payload and headers
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_PAYMONGO_SIGNATURE'] ?? '';

// Verify signature
function verifySignature($payload, $signature, $secret) {
    $computedSignature = hash_hmac('sha256', $payload, $secret);
    return hash_equals($signature, $computedSignature);
}

if (!verifySignature($payload, $signature, $WEBHOOK_SECRET)) {
    http_response_code(403);
    die('Invalid signature');
}

// Parse JSON payload
$event = json_decode($payload, true);

// Process event
switch ($event['data']['attributes']['type']) {
    case 'payment.paid':
        $paymentData = $event['data']['attributes']['data'];
        // Update database, send email, etc.
        error_log("Payment succeeded: " . $paymentData['id']);
        break;
        
    case 'payment.failed':
        $paymentData = $event['data']['attributes']['data'];
        // Notify admin or handle failure
        error_log("Payment failed: " . $paymentData['id']);
        break;
        
    default:
        error_log("Unhandled event type: " . $event['data']['attributes']['type']);
}

http_response_code(200);
echo 'Webhook processed';
?>