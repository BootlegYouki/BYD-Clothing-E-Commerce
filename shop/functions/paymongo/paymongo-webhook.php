<?php
try {
    // Decode the payload and validate structure
    $event = json_decode($payload, true);
    
    // Validate payload structure
    if (!isset($event['data']['attributes']['type'], $event['data']['attributes']['data'])) {
        throw new Exception("Invalid PayMongo event structure");
    }

    // Extract event type (e.g., "checkout_session.payment.paid")
    $eventType = $event['data']['attributes']['type'];
    error_log("✅ Valid event received: $eventType");

    switch ($eventType) {
        case 'checkout_session.payment.paid':
            // Validate checkout session data structure
            $checkoutData = $event['data']['attributes']['data'];
            if (!isset($checkoutData['id'], $checkoutData['attributes'])) {
                throw new Exception("Invalid checkout session data");
            }

            $checkoutSessionId = $checkoutData['id'];
            error_log("💰 Checkout Session Paid: $checkoutSessionId");

            // Extract payment intent safely
            $paymentIntent = $checkoutData['attributes']['payment_intent'] ?? [];
            $paymentIntentId = $paymentIntent['id'] ?? 'unknown';
            error_log("🔗 Payment Intent ID: $paymentIntentId");

            // Extract metadata with validation
            $metadata = $checkoutData['attributes']['metadata'] ?? [];
            $userId = $metadata['user_id'] ?? 'unknown';
            error_log("👤 User ID: $userId");

            // TODO: Add order fulfillment logic here
            break;

        case 'checkout_session.payment.failed':
            // Add failure handling logic (e.g., notify user)
            error_log("❌ Checkout Session Failed: " . print_r($event, true));
            break;

        default:
            error_log("⚠️ Unhandled event type: $eventType");
            break;
    }

    http_response_code(200);
    echo 'Webhook processed';

} catch (Exception $e) {
    error_log("🚨 Critical Error: " . $e->getMessage());
    error_log("📦 Raw Payload: " . $payload); // Debugging aid
    http_response_code(400); // Bad Request for invalid payloads
    echo 'Error processing webhook';
}
?>