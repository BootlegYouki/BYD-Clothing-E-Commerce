<?php
require_once '../../../admin/config/dbcon.php';
require_once 'PayMongoHelper.php';

// Log all webhook requests for debugging
$logFile = __DIR__ . '/webhook_logs.txt';
$input = file_get_contents('php://input');
$timestamp = date('Y-m-d H:i:s');
file_put_contents($logFile, "[$timestamp] Received webhook: $input" . PHP_EOL, FILE_APPEND);

// Parse the webhook data
$data = json_decode($input, true);

// Verify the webhook data is valid
if ($data && isset($data['data']['attributes']['type'])) {
    $eventType = $data['data']['attributes']['type'];
    
    // Handle payment.paid events
    if ($eventType == 'payment.paid') {
        // Extract payment details
        $paymentData = $data['data']['attributes']['data'];
        $paymentId = isset($paymentData['id']) ? $paymentData['id'] : null;
        
        if ($paymentId) {
            $attributes = $paymentData['attributes'];
            
            // Convert amount from cents to actual currency
            $amount = isset($attributes['amount']) ? $attributes['amount'] / 100 : 0;
            $fee = isset($attributes['fee']) ? $attributes['fee'] / 100 : 0;
            $netAmount = isset($attributes['net_amount']) ? $attributes['net_amount'] / 100 : 0;
            
            // Get other payment details
            $status = isset($attributes['status']) ? strtoupper($attributes['status']) : 'UNKNOWN';
            $referenceNumber = isset($attributes['external_reference_number']) ? $attributes['external_reference_number'] : null;
            $sourceType = isset($attributes['source']['type']) ? $attributes['source']['type'] : null;
            
            // Format timestamps
            $createdAt = isset($attributes['created_at']) ? date('Y-m-d H:i:s', $attributes['created_at']) : date('Y-m-d H:i:s');
            $updatedAt = isset($attributes['updated_at']) ? date('Y-m-d H:i:s', $attributes['updated_at']) : date('Y-m-d H:i:s');
            
            // Get customer information if available
            $customerName = isset($attributes['billing']['name']) ? $attributes['billing']['name'] : null;
            $phone = isset($attributes['billing']['phone']) ? $attributes['billing']['phone'] : null;
            
            // Update order in the database if reference number exists
            if ($referenceNumber) {
                // Assuming reference number is the order ID
                $updateOrderQuery = "UPDATE orders SET 
                                    payment_status = 'paid',
                                    payment_details = ?,
                                    updated_at = NOW()
                                    WHERE id = ?";
                
                $paymentDetails = json_encode([
                    'payment_id' => $paymentId,
                    'amount' => $amount,
                    'fee' => $fee,
                    'net_amount' => $netAmount,
                    'source_type' => $sourceType,
                    'status' => $status
                ]);
                
                $stmt = $conn->prepare($updateOrderQuery);
                $stmt->bind_param("ss", $paymentDetails, $referenceNumber);
                
                if ($stmt->execute()) {
                    file_put_contents($logFile, "[$timestamp] Updated order #$referenceNumber with payment $paymentId" . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents($logFile, "[$timestamp] Failed to update order: " . $stmt->error . PHP_EOL, FILE_APPEND);
                }
                
                $stmt->close();
            }
            
            // Insert payment record into transactions table
            $insertQuery = "INSERT INTO transactions 
                           (payment_id, order_id, amount, fee, net_amount, status, payment_method, created_at, updated_at, customer_name, customer_phone) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param(
                "ssddsssssss",
                $paymentId,
                $referenceNumber,
                $amount,
                $fee,
                $netAmount,
                $status,
                $sourceType,
                $createdAt,
                $updatedAt,
                $customerName,
                $phone
            );
            
            if ($stmt->execute()) {
                // Send successful response
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Payment processed successfully',
                    'payment_id' => $paymentId
                ]);
                
                file_put_contents($logFile, "[$timestamp] Inserted payment record for $paymentId" . PHP_EOL, FILE_APPEND);
            } else {
                // Log error and send error response
                file_put_contents($logFile, "[$timestamp] DB Error: " . $stmt->error . PHP_EOL, FILE_APPEND);
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to process payment'
                ]);
            }
            
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid payment ID'
            ]);
        }
    } else {
        // Handle other event types if needed
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Event received but not processed',
            'event_type' => $eventType
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid webhook data'
    ]);
}

$conn->close();