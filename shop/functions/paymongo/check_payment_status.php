<?php
/**
 * AJAX Payment Status Checker
 * 
 * This script is called via AJAX to check the status of a payment session
 */
require_once '../../../admin/config/dbcon.php';
require_once __DIR__ . '/PayMongoHelper.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get session ID from query parameter
$sessionId = $_GET['session_id'] ?? null;

if (!$sessionId) {
    echo json_encode([
        'success' => false,
        'status' => 'error',
        'message' => 'No session ID provided'
    ]);
    exit;
}

try {
    // Initialize PayMongo helper
    $paymongo = new PayMongoHelper();
    
    // Get session details
    $session = $paymongo->getCheckoutSession($sessionId);
    
    // Extract payment status
    $paymentStatus = $session['data']['attributes']['payment_intent']['status'] ?? 'unknown';
    
    // If payment succeeded, update product quantities
    if ($paymentStatus === 'succeeded') {
        try {
            // Get cart items from the checkout session metadata
            $metadata = $session['data']['attributes']['metadata'] ?? [];
            if (!empty($metadata) && isset($metadata['cart_items'])) {
                $cartItems = json_decode($metadata['cart_items'], true);
                
                // Update quantity for each product
                if (is_array($cartItems)) {
                    foreach ($cartItems as $item) {
                        if (isset($item['id']) && isset($item['size']) && isset($item['quantity'])) {
                            $productId = $item['id'];
                            $size = $item['size'];
                            $quantity = $item['quantity'];
                            
                            // Update product quantity in database
                            if ($productId > 0) {  // Skip if product ID is invalid
                                $sql = "UPDATE products_variants 
                                       SET quantity = quantity - ? 
                                       WHERE product_id = ? AND size = ? AND quantity >= ?";
                                $stmt = $con->prepare($sql);
                                $stmt->bind_param("iisi", $quantity, $productId, $size, $quantity);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Log error but continue with the payment success flow
            error_log("Error updating product quantities: " . $e->getMessage());
        }
    }
    
    // Return status
    echo json_encode([
        'success' => true,
        'status' => $paymentStatus,
        'session_id' => $sessionId
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
