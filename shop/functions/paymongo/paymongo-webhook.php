<?php
// Set content type to JSON
header('Content-Type: application/json');

// Get the raw POST data
$input = file_get_contents('php://input');

// Log the raw data (optional)
file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Raw data: " . $input . PHP_EOL, FILE_APPEND);

// Decode the JSON
$data = json_decode($input, true);

// Check if it's valid JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid JSON payload: ' . json_last_error_msg()
    ]);
    exit;
}

// Basic validation that it might be from PayMongo
if (!isset($data['data']) || !isset($data['data']['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid PayMongo webhook format'
    ]);
    exit;
}

// In production, you should also validate the PayMongo signature
// using the webhook signing key from your PayMongo Dashboard
// https://developers.paymongo.com/docs/webhook-security

// Process the webhook based on the event type
$eventType = $data['data']['attributes']['type'] ?? 'unknown';

// Check if the event type is "checkout_session.payment.paid"
if ($eventType === 'checkout_session.payment.paid') {
    // Output message to terminal
    error_log('Received checkout_session.payment.paid event from PayMongo');
    
    // Extract checkout session ID
    $checkoutSessionId = $data['data']['id'] ?? '';
    
    // Extract payment details if available
    $paymentId = $data['data']['attributes']['payment_id'] ?? '';
    $amount = $data['data']['attributes']['amount'] ?? 0;
    $currency = $data['data']['attributes']['currency'] ?? 'PHP';
    
    // Log the payment details
    error_log("Payment successful: ID: $paymentId, Amount: $amount $currency");
    
    // You can add additional processing for this specific event type here
    // For example, update your database, send notifications, etc.
}

// Output the webhook data
echo json_encode([
    'status' => 'success',
    'message' => 'Webhook received',
    'event_type' => $eventType,
    'data' => $data
], JSON_PRETTY_PRINT);

// Return 200 OK to PayMongo
http_response_code(200);
?>