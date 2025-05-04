<?php
session_start();
include_once '../admin/config/dbcon.php';
require_once 'functions/paymongo/PayMongoHelper.php';

// Get payment status from URL parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$reference = isset($_GET['reference']) ? $_GET['reference'] : '';
$payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';

// Log the return parameters for debugging
error_log("Payment return: status=$status, reference=$reference, payment_id=$payment_id, session_id=$session_id");

// Initialize payment method variable
$paymentMethodUsed = 'unknown';

// If we have a session_id but no payment_id, try to get payment details from PayMongo
if (!empty($session_id) || !empty($payment_id)) {
    try {
        $paymongo = new PayMongoHelper();
        $identifier = !empty($session_id) ? $session_id : $payment_id;
        
        if (strpos($identifier, 'cs_') === 0) {
            // This is a checkout session ID
            $sessionData = $paymongo->getCheckoutSession($identifier);
            
            // Extract payment ID from session data if available
            if (isset($sessionData['data']['attributes']['payment_intent']['id']) && empty($payment_id)) {
                $payment_id = $sessionData['data']['attributes']['payment_intent']['id'];
                error_log("Retrieved payment_id from session: $payment_id");
            }
            
            // Get payment method used (including QR code)
            $paymentMethodUsed = $sessionData['data']['attributes']['payment_method_used'] ?? 'unknown';
            error_log("Payment method used: $paymentMethodUsed");
            
            // Store payment method in session for analytics
            $_SESSION['payment_method_used'] = $paymentMethodUsed;
        } else if (strpos($identifier, 'pi_') === 0) {
            // This is a payment intent ID
            $paymentData = $paymongo->getPaymentIntent($identifier);
            
            if (isset($paymentData['data']['attributes']['payment_method_used'])) {
                $paymentMethodUsed = $paymentData['data']['attributes']['payment_method_used'];
                $_SESSION['payment_method_used'] = $paymentMethodUsed;
                error_log("Payment method used from payment intent: $paymentMethodUsed");
            }
        }
        
        // Update the order with the specific payment method used
        if ($paymentMethodUsed != 'unknown') {
            // Map PayMongo payment methods to more user-friendly names
            $methodMapping = [
                'card' => 'Credit/Debit Card',
                'gcash' => 'GCash',
                'paymaya' => 'PayMaya',
                'grab_pay' => 'GrabPay',
                'qrph' => 'QR Ph'
            ];
            
            $displayMethod = isset($methodMapping[$paymentMethodUsed]) ? 
                $methodMapping[$paymentMethodUsed] : ucfirst($paymentMethodUsed);
            
            // Update order with specific payment method
            $update_method_query = "UPDATE orders SET payment_method = ? WHERE payment_id = ?";
            $stmt = mysqli_prepare($conn, $update_method_query);
            mysqli_stmt_bind_param($stmt, "ss", $displayMethod, $identifier);
            mysqli_stmt_execute($stmt);
            
            error_log("Updated order payment method to: $displayMethod for payment ID: $identifier");
        }
    } catch (Exception $e) {
        error_log("Error retrieving session data: " . $e->getMessage());
    }
}

// Set session variables to show appropriate modals on the homepage
if ($status === 'success') {
    // Store success message in session
    $_SESSION['payment_status'] = 'success';
    $_SESSION['order_reference'] = $reference;
    
    // If payment_id is provided, update the transaction in the database
    if (!empty($payment_id) || !empty($session_id)) {
        // Use payment_id or session_id for reference
        $identifier = !empty($payment_id) ? $payment_id : $session_id;
        
        // Check if this payment already exists in our database
        $query = "SELECT id FROM transactions WHERE payment_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $identifier);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            // Get payment method from session if available
            $paymentMethod = $_SESSION['payment_method_used'] ?? 'unknown';
            $description = "Payment completed via " . $paymentMethod;
            
            // Payment doesn't exist yet, insert a new record
            $insert_query = "INSERT INTO transactions (payment_id, status, description, created_at) 
                            VALUES (?, 'successful', ?, NOW())";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "ss", $identifier, $description);
            mysqli_stmt_execute($stmt);
            
            // Also update the order status if possible
            if (!empty($reference)) {
                // Extract order ID from reference if it follows the format ORDER-timestamp-userID
                $refParts = explode('-', $reference);
                if (count($refParts) >= 3) {
                    $update_order_query = "UPDATE orders SET status = 'processing' WHERE payment_id = ? OR payment_id = ?";
                    $stmt = mysqli_prepare($conn, $update_order_query);
                    mysqli_stmt_bind_param($stmt, "ss", $session_id, $payment_id);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }
    
    // Clear the shopping cart on successful payment
    if (isset($_SESSION['shopping-cart'])) {
        unset($_SESSION['shopping-cart']);
    }
    
    // Also clear localStorage cart via JavaScript
    echo "<script>
        localStorage.removeItem('shopping-cart');
        localStorage.removeItem('cart');
        window.location.href = 'index.php';
    </script>";
} else {
    // Store failure message in session
    $_SESSION['payment_status'] = 'failed';
    
    // If payment_id is provided, update the transaction in the database
    if (!empty($payment_id) || !empty($session_id)) {
        // Use payment_id or session_id for reference
        $identifier = !empty($payment_id) ? $payment_id : $session_id;
        
        // Check if this payment already exists in our database
        $query = "SELECT id FROM transactions WHERE payment_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $identifier);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Get payment method from session if available
        $paymentMethod = $_SESSION['payment_method_used'] ?? 'unknown';
        $description = "Payment failed via " . $paymentMethod;
        
        if (mysqli_num_rows($result) == 0) {
            // Payment doesn't exist yet, insert a failed record
            $insert_query = "INSERT INTO transactions (payment_id, status, description, created_at) 
                            VALUES (?, 'failed', ?, NOW())";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "ss", $identifier, $description);
            mysqli_stmt_execute($stmt);
        } else {
            // Update existing record
            $update_query = "UPDATE transactions SET status = 'failed', description = ?, updated_at = NOW() WHERE payment_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "ss", $description, $identifier);
            mysqli_stmt_execute($stmt);
        }
        
        // Also update the order status if possible
        if (!empty($reference)) {
            $update_order_query = "UPDATE orders SET status = 'failed' WHERE payment_id = ? OR payment_id = ?";
            $stmt = mysqli_prepare($conn, $update_order_query);
            mysqli_stmt_bind_param($stmt, "ss", $session_id, $payment_id);
            mysqli_stmt_execute($stmt);
        }
    }
    
    // Redirect to homepage
    echo "<script>
        window.location.href = 'index.php';
    </script>";
}
?>