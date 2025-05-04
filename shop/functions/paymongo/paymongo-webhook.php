<?php
// Include database connection
require_once '../../../admin/config/dbcon.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the raw POST data
$input = file_get_contents('php://input');

// Log the raw data
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

// Get the event ID to check for duplicates
$eventId = $data['data']['id'];

// Check if this event has already been processed - IMPORTANT FOR IDEMPOTENCY
if (hasEventBeenProcessed($conn, $eventId)) {
    // Already processed, return success to prevent further retries
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Event already processed',
        'event_id' => $eventId
    ]);
    exit;
}

// Process the webhook based on the event type
$eventType = $data['data']['attributes']['type'] ?? 'unknown';
$processedData = [];

try {
    // Handle different event types
    switch ($eventType) {
        case 'checkout_session.payment.paid':
            // Extract checkout session data
            $checkoutSession = $data['data']['attributes']['data'] ?? [];
            if (!empty($checkoutSession)) {
                $sessionId = $checkoutSession['id'] ?? null;
                $attributes = $checkoutSession['attributes'] ?? [];
                
                // If we have a valid session ID and attributes
                if ($sessionId && !empty($attributes)) {
                    // Extract payment information
                    $paymentMethodUsed = $attributes['payment_method_used'] ?? 'unknown';
                    $paymentIntent = $attributes['payment_intent'] ?? [];
                    $payments = $attributes['payments'] ?? [];
                    $metadata = $attributes['metadata'] ?? [];
                    $paidAt = $attributes['paid_at'] ?? null;
                    
                    // Extract payment intent details
                    $paymentIntentId = $paymentIntent['id'] ?? null;
                    $paymentStatus = $paymentIntent['attributes']['status'] ?? 'unknown';
                    $amount = $paymentIntent['attributes']['amount'] ?? 0;
                    
                    // Format payment method for better display
                    $methodMapping = [
                        'card' => 'Credit/Debit Card',
                        'gcash' => 'GCash',
                        'paymaya' => 'PayMaya',
                        'grab_pay' => 'GrabPay',
                        'qrph' => 'QR Ph'
                    ];
                    
                    $displayMethod = isset($methodMapping[$paymentMethodUsed]) ? 
                        $methodMapping[$paymentMethodUsed] : ucfirst($paymentMethodUsed);
                    
                    // Prepare data to be stored
                    $processedData = [
                        'session_id' => $sessionId,
                        'payment_intent_id' => $paymentIntentId,
                        'payment_method' => $paymentMethodUsed,
                        'payment_method_display' => $displayMethod,
                        'status' => $paymentStatus,
                        'amount' => $amount / 100, // Convert from cents to PHP
                        'reference_number' => $metadata['reference_number'] ?? null,
                        'user_id' => $metadata['user_id'] ?? null,
                        'paid_at' => $paidAt ? date('Y-m-d H:i:s', $paidAt) : null
                    ];
                    
                    // Update the database
                    storePaymentData($conn, $processedData);
                }
            }
            break;
            
        case 'payment.paid':
            // Handle direct payment paid event
            $payment = $data['data']['attributes']['data'] ?? [];
            if (!empty($payment)) {
                $paymentId = $payment['id'] ?? null;
                $attributes = $payment['attributes'] ?? [];
                
                if ($paymentId && !empty($attributes)) {
                    // Extract payment details
                    $paymentIntentId = $attributes['payment_intent_id'] ?? null;
                    $status = $attributes['status'] ?? 'unknown';
                    $amount = $attributes['amount'] ?? 0;
                    $metadata = $attributes['metadata'] ?? [];
                    $source = $attributes['source'] ?? [];
                    $paymentMethodUsed = $source['type'] ?? 'unknown';
                    $paidAt = $attributes['paid_at'] ?? null;
                    
                    // Format payment method
                    $methodMapping = [
                        'card' => 'Credit/Debit Card',
                        'gcash' => 'GCash',
                        'paymaya' => 'PayMaya',
                        'grab_pay' => 'GrabPay',
                        'qrph' => 'QR Ph'
                    ];
                    
                    $displayMethod = isset($methodMapping[$paymentMethodUsed]) ? 
                        $methodMapping[$paymentMethodUsed] : ucfirst($paymentMethodUsed);
                    
                    // Prepare data
                    $processedData = [
                        'payment_id' => $paymentId,
                        'payment_intent_id' => $paymentIntentId,
                        'payment_method' => $paymentMethodUsed,
                        'payment_method_display' => $displayMethod,
                        'status' => $status,
                        'amount' => $amount / 100, // Convert from cents to PHP
                        'reference_number' => $metadata['reference_number'] ?? null,
                        'user_id' => $metadata['user_id'] ?? null,
                        'paid_at' => $paidAt ? date('Y-m-d H:i:s', $paidAt) : null
                    ];
                    
                    // Update the database
                    storePaymentData($conn, $processedData);
                }
            }
            break;
            
        default:
            // Log unhandled event types
            error_log("Unhandled PayMongo event type: $eventType");
    }
    
    // Record this event as processed
    markEventAsProcessed($conn, $eventId);
    
    // Return success response - RESPOND QUICKLY to prevent retries
    echo json_encode([
        'status' => 'success',
        'message' => 'Webhook processed',
        'event_type' => $eventType,
        'event_id' => $eventId
    ], JSON_PRETTY_PRINT);
    
    // Return 200 OK to PayMongo
    http_response_code(200);
    
} catch (Exception $e) {
    // Log error
    error_log("Error processing PayMongo webhook: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error processing webhook: ' . $e->getMessage()
    ]);
}

/**
 * Check if an event has already been processed
 * 
 * @param mysqli $conn Database connection
 * @param string $eventId PayMongo event ID
 * @return bool True if already processed
 */
function hasEventBeenProcessed($conn, $eventId) {
    // Check if we have a record for this event ID
    $query = "SELECT id FROM webhook_events WHERE event_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $eventId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_num_rows($result) > 0;
}

/**
 * Mark an event as processed
 * 
 * @param mysqli $conn Database connection
 * @param string $eventId PayMongo event ID
 * @return void
 */
function markEventAsProcessed($conn, $eventId) {
    // Insert a record for this event
    $query = "INSERT INTO webhook_events (event_id, processed_at) VALUES (?, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $eventId);
    mysqli_stmt_execute($stmt);
}

/**
 * Store payment data in the database
 * 
 * @param mysqli $conn Database connection
 * @param array $data Payment data
 * @return bool Success status
 */
function storePaymentData($conn, $data) {
    // Begin transaction for data integrity
    mysqli_begin_transaction($conn);
    
    try {
        // First, check if this payment has already been processed
        $checkQuery = "SELECT id FROM transactions WHERE 
            (payment_id = ? OR payment_intent_id = ? OR session_id = ?)";
        
        $stmt = mysqli_prepare($conn, $checkQuery);
        $paymentId = $data['payment_id'] ?? null;
        $paymentIntentId = $data['payment_intent_id'] ?? null;
        $sessionId = $data['session_id'] ?? null;
        
        mysqli_stmt_bind_param($stmt, "sss", $paymentId, $paymentIntentId, $sessionId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Payment already exists, update it
            $row = mysqli_fetch_assoc($result);
            $transactionId = $row['id'];
            
            $updateQuery = "UPDATE transactions SET 
                status = ?, 
                payment_method = ?,
                description = ?,
                amount = ?,
                paid_at = ?,
                updated_at = NOW()
                WHERE id = ?";
            
            $stmt = mysqli_prepare($conn, $updateQuery);
            
            $status = $data['status'] === 'succeeded' || $data['status'] === 'paid' ? 'successful' : $data['status'];
            $paymentMethod = $data['payment_method_display'] ?? 'Unknown';
            $description = "Payment completed via " . $paymentMethod;
            $amount = $data['amount'] ?? 0;
            $paidAt = $data['paid_at'] ?? null;
            
            mysqli_stmt_bind_param($stmt, "sssdsi", $status, $paymentMethod, $description, $amount, $paidAt, $transactionId);
            mysqli_stmt_execute($stmt);
            
            error_log("Updated transaction ID: $transactionId with payment method: $paymentMethod and status: $status");
        } else {
            // Insert new transaction record
            $insertQuery = "INSERT INTO transactions (
                payment_id, 
                payment_intent_id,
                session_id,
                status,
                payment_method,
                description,
                amount,
                paid_at,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($conn, $insertQuery);
            
            $paymentId = $data['payment_id'] ?? null;
            $paymentIntentId = $data['payment_intent_id'] ?? null;
            $sessionId = $data['session_id'] ?? null;
            $status = $data['status'] === 'succeeded' || $data['status'] === 'paid' ? 'successful' : $data['status'];
            $paymentMethod = $data['payment_method_display'] ?? 'Unknown';
            $description = "Payment completed via " . $paymentMethod;
            $amount = $data['amount'] ?? 0;
            $paidAt = $data['paid_at'] ?? null;
            
            mysqli_stmt_bind_param($stmt, "ssssssds", 
                $paymentId, 
                $paymentIntentId,
                $sessionId,
                $status,
                $paymentMethod,
                $description,
                $amount,
                $paidAt
            );
            mysqli_stmt_execute($stmt);
            
            $transactionId = mysqli_insert_id($conn);
            error_log("Created new transaction ID: $transactionId for payment method: $paymentMethod");
        }
        
        // Now update the corresponding order
        $orderUpdateQuery = "UPDATE orders SET 
            status = ?,
            payment_method = ?
            WHERE payment_id = ? OR payment_id = ? OR payment_id = ?";
        
        $stmt = mysqli_prepare($conn, $orderUpdateQuery);
        $orderStatus = ($data['status'] === 'succeeded' || $data['status'] === 'paid') ? 'processing' : 'pending';
        
        mysqli_stmt_bind_param($stmt, "sssss", 
            $orderStatus,
            $paymentMethod, 
            $paymentId, 
            $paymentIntentId, 
            $sessionId
        );
        mysqli_stmt_execute($stmt);
        
        $affectedOrders = mysqli_stmt_affected_rows($stmt);
        error_log("Updated $affectedOrders orders with status: $orderStatus and payment method: $paymentMethod");
        
        // If we have a reference number, try to update by that as well
        if (!empty($data['reference_number']) && $affectedOrders == 0) {
            $refParts = explode('-', $data['reference_number']);
            if (count($refParts) >= 3) {
                $timestamp = $refParts[1] ?? '';
                $userId = $refParts[2] ?? '';
                
                if (!empty($timestamp) && !empty($userId)) {
                    $updateByRefQuery = "UPDATE orders SET 
                        status = ?,
                        payment_method = ?
                        WHERE user_id = ? 
                        AND created_at >= FROM_UNIXTIME(?) - INTERVAL 10 MINUTE
                        AND created_at <= FROM_UNIXTIME(?) + INTERVAL 10 MINUTE
                        ORDER BY created_at DESC LIMIT 1";
                    
                    $stmt = mysqli_prepare($conn, $updateByRefQuery);
                    mysqli_stmt_bind_param($stmt, "sssss", 
                        $orderStatus,
                        $paymentMethod, 
                        $userId,
                        $timestamp,
                        $timestamp
                    );
                    mysqli_stmt_execute($stmt);
                    
                    $affectedOrders = mysqli_stmt_affected_rows($stmt);
                    error_log("Updated $affectedOrders orders by reference pattern with status: $orderStatus");
                }
            }
        }
        
        // Commit all database changes
        mysqli_commit($conn);
        return true;
        
    } catch (Exception $e) {
        // Roll back in case of error
        mysqli_rollback($conn);
        error_log("Database error while storing payment: " . $e->getMessage());
        throw $e;
    }
}
?>