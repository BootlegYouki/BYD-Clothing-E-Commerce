<?php
// paymongo-webhook.php (Temporary Testing Mode)

// 1. Set timezone to match PayMongo's servers
date_default_timezone_set('Asia/Manila');

// 2. Retrieve webhook secret from Heroku
$WEBHOOK_SECRET = getenv('PAYMONGO_WEBHOOK_SECRET');

// 3. Get raw payload and signature header
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_PAYMONGO_SIGNATURE'] ?? '';

// 4. Bypass signature validation temporarily
$isSignatureValid = false;
if (!empty($WEBHOOK_SECRET)) {
    $computedSignature = hash_hmac('sha256', $payload, $WEBHOOK_SECRET);
    $isSignatureValid = hash_equals($signature, $computedSignature);
}

// 5. Immediately return 200 to stop retries (accept all requests)
http_response_code(200);
echo 'Webhook received (Testing Mode)';

// 6. Process event for debugging
try {
    $event = json_decode($payload, true);
    error_log("Received event (Signature valid: " . ($isSignatureValid ? 'YES' : 'NO') . "): " . print_r($event, true));

    // Log event type
    $eventType = $event['data']['attributes']['type'] ?? 'unknown';
    error_log("Event Type: $eventType");

} catch (Exception $e) {
    error_log("Error processing event: " . $e->getMessage());
}
?>