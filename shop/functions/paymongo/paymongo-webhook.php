<?php
// paymongo-webhook.php

// Enforce POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Only POST requests are allowed');
}

date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/../../../admin/config/env_loader.php';

$WEBHOOK_SECRET = getEnvVar('PAYMONGO_WEBHOOK_SECRET');
if (empty($WEBHOOK_SECRET)) {
    error_log("❌ PAYMONGO_WEBHOOK_SECRET is not set");
    http_response_code(500);
    exit("Server error: Missing webhook secret");
}

$payload = file_get_contents('php://input');
$signatureHeader = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';

// Safely extract signature (v1=...)
$receivedSignature = '';
foreach (explode(',', $signatureHeader) as $part) {
    if (str_starts_with($part, 'v1=')) {
        $receivedSignature = substr($part, 3); // Get value after "v1="
        break;
    }
}

// Debugging: Log secret length
error_log("🗝️  Stored Secret Length: " . strlen($WEBHOOK_SECRET));

header('Content-Type: text/plain');
error_log("📦 Raw Payload: " . $payload);
error_log("🔐 Received Signature: " . $receivedSignature);

function verifySignature($payload, $receivedSignature, $secret) {
    $computedSignature = hash_hmac('sha256', $payload, $secret);
    error_log("🧮 Computed Signature: " . $computedSignature);
    return hash_equals($receivedSignature, $computedSignature);
}

if (!verifySignature($payload, $receivedSignature, $WEBHOOK_SECRET)) {
    error_log("❌ Signature validation failed");
    http_response_code(403);
    exit('Invalid signature');
}

// Rest of your processing logic...

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
