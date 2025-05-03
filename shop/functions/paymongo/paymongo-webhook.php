<?php
// paymongo-webhook.php

// 1. Set timezone to match PayMongo's server timezone
date_default_timezone_set('Asia/Manila');

// 2. Retrieve the webhook secret key from Heroku config variables
$WEBHOOK_SECRET = getenv('PAYMONGO_WEBHOOK_SECRET');

// 2a. Check if the secret is set
if (empty($WEBHOOK_SECRET)) {
    error_log("❌ PAYMONGO_WEBHOOK_SECRET is not set in Heroku config");
    http_response_code(500);
    exit("Server error: Missing webhook secret");
}

// 3. Get the raw JSON payload sent by PayMongo
$payload = file_get_contents('php://input');

// 4. Get the PayMongo signature from the headers
$signature = $_SERVER['HTTP_X_PAYMONGO_SIGNATURE'] ?? '';

// 5. Set response header type to plain text
header('Content-Type: text/plain');

// 6. Log payload and signature for debugging (remove in production)
error_log("📦 Raw Payload: " . $payload);
error_log("🔐 Received Signature: " . $signature);
error_log("🗝️  Stored Secret: " . $WEBHOOK_SECRET);

// 7. Signature validation function using HMAC SHA-256
function verifySignature($payload, $signature, $secret) {
    // Compute the HMAC hash of the payload using the shared secret
    $computedSignature = hash_hmac('sha256', $payload, $secret);
    error_log("🧮 Computed Signature: " . $computedSignature);

    // Securely compare signatures to prevent timing attacks
    return hash_equals($signature, $computedSignature);
}

// 8. Verify the signature
if (!verifySignature($payload, $signature, $WEBHOOK_SECRET)) {
    error_log("❌ Signature validation failed");
    http_response_code(403);
    exit('Invalid signature');
}

// 9. Process the webhook event only if signature is valid
try {
    // Decode the JSON payload into an associative array
    $event = json_decode($payload, true);
    error_log("✅ Valid event received: " . print_r($event, true));

    // Extract event type
    $eventType = $event['data']['attributes']['type'] ?? 'unknown';

    switch ($eventType) {
        case 'payment.paid':
            // Safely extract payment ID from payload
            $paymentId = $event['data']['attributes']['data']['id'] ?? 'unknown';
            error_log("💰 Payment succeeded: $paymentId");

            // TODO: Add your order fulfillment or database update logic here
            break;

        case 'payment.failed':
            $paymentId = $event['data']['attributes']['data']['id'] ?? 'unknown';
            error_log("❌ Payment failed: $paymentId");

            // TODO: Handle failed payments (e.g., notify user, mark as failed)
            break;

        default:
            // For unhandled events, just log them
            error_log("⚠️ Unhandled event type: $eventType");
            break;
    }

    // 10. Respond with 200 OK to tell PayMongo the event was received successfully
    http_response_code(200);
    echo 'Webhook processed';

} catch (Exception $e) {
    // 11. Catch and log any unexpected errors
    error_log("🚨 Error processing event: " . $e->getMessage());
    http_response_code(500);
    echo 'Error processing webhook';
}
?>
