<?php
// Include database connection
require_once '../../../admin/config/dbcon.php';
require_once __DIR__ . '/EmailConfirmation.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
                    
                    // Extract order data from metadata
                    $orderData = [
                        'user_id' => (int)$metadata['user_id'],
                        'firstname' => $metadata['firstname'],
                        'lastname' => $metadata['lastname'],
                        'email' => $metadata['email'],
                        'phone' => $metadata['phone'],
                        'address' => $metadata['address'],
                        'zipcode' => $metadata['zipcode'],
                        'subtotal' => (float)$metadata['subtotal'],
                        'shipping_cost' => (float)$metadata['shipping_cost'],
                        'total_amount' => (float)$metadata['total_amount'],
                        'payment_method' => $displayMethod,
                        'payment_id' => $sessionId,
                        'payment_intent_id' => $paymentIntent['id'] ?? null,
                        'reference_number' => $metadata['reference_number'],
                        'status' => 'processing', // Payment successful, order is now processing
                        'cart_items' => json_decode($metadata['cart_items'], true)
                    ];
                    
                    // Insert the order into the database
                    $orderId = insertOrderToDatabase($conn, $orderData);
                    
                    // Send order confirmation email
                    EmailConfirmation::sendOrderConfirmationEmail($orderData, $orderId, $sessionId);
                    
                    // Log the successful order
                    error_log("Order #$orderId created from webhook payment success");
                }
            }
            break;
            
        case 'payment.paid':
            // Handle direct payment paid event (if needed)
            $payment = $data['data']['attributes']['data'] ?? [];
            if (!empty($payment)) {
                $paymentId = $payment['id'] ?? null;
                // Log this event but don't process it as we're focusing on checkout sessions
                error_log("Payment.paid event received: $paymentId");
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
 * Insert order data into database
 * 
 * @param mysqli $conn Database connection
 * @param array $data Order data
 * @return int Order ID
 */
function insertOrderToDatabase($conn, $data) {
    // Begin database transaction for data integrity
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Prepare SQL for orders table insertion
        $order_query = "INSERT INTO orders (
            user_id, 
            firstname, 
            lastname, 
            email, 
            phone, 
            address, 
            zipcode, 
            payment_method, 
            payment_id, 
            subtotal, 
            shipping_cost, 
            total_amount, 
            reference_number,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $order_query);
        
        // Bind parameters to prepared statement
        mysqli_stmt_bind_param(
            $stmt, 
            "issssssssdddss", 
            $data['user_id'],
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['zipcode'],
            $data['payment_method'],
            $data['payment_id'],
            $data['subtotal'],
            $data['shipping_cost'],
            $data['total_amount'],
            $data['reference_number'],
            $data['status']
        );
        
        // Execute order insertion
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);
        
        // 2. Insert individual order items
        $item_query = "INSERT INTO order_items (
            order_id, 
            product_id, 
            product_name, 
            size, 
            quantity, 
            price, 
            subtotal
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $item_stmt = mysqli_prepare($conn, $item_query);
        
        // Process each cart item
        foreach ($data['cart_items'] as $item) {
            $product_id = isset($item['id']) ? $item['id'] : 0;
            $product_name = $item['name'];
            $size = $item['size'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $item_subtotal = $price * $quantity;
            
            // Bind parameters for each item
            mysqli_stmt_bind_param(
                $item_stmt, 
                "iissidd", 
                $order_id,
                $product_id,
                $product_name,
                $size,
                $quantity,
                $price,
                $item_subtotal
            );
            
            // Execute item insertion
            mysqli_stmt_execute($item_stmt);
        }
        
        // Commit all database changes
        mysqli_commit($conn);
        
        return $order_id;
    } catch (Exception $e) {
        // Rollback all changes if any error occurs
        mysqli_rollback($conn);
        throw $e;
    }
}
?>