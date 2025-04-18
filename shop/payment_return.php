<?php
/**
 * Payment Return Handler
 * 
 * This script processes the return from PayMongo after payment completion.
 * It updates the order status in the database based on the payment result.
 */
require_once '../admin/config/dbcon.php';
require_once 'functions/PayMongoHelper.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/payment_errors.log');

// Get payment ID from URL parameters
$paymentId = $_GET['payment_id'] ?? null;
$paymentStatus = $_GET['status'] ?? null;

// Log the incoming request
error_log("Payment return called with ID: $paymentId, Status: $paymentStatus");

// Validate payment ID
if (!$paymentId) {
    error_log("Invalid payment: No payment_id provided");
    header('Location: order-confirmation.php?status=failed&reason=invalid_id');
    exit;
}

try {
    // Initialize PayMongo helper
    $paymongo = new PayMongoHelper();
    
    // Fetch payment details from PayMongo API
    $paymentData = $paymongo->getPaymentIntent($paymentId);
    error_log("Payment data retrieved: " . json_encode($paymentData));
    
    // Get payment status from API response
    $apiPaymentStatus = $paymentData['data']['attributes']['status'] ?? 'unknown';
    error_log("Payment status from API: $apiPaymentStatus");

    // Update order status in database
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ?, status = ? WHERE payment_id = ?");
    $orderStatus = ($apiPaymentStatus == 'paid') ? 'to_ship' : 'pending';
    $stmt->bind_param('sss', $apiPaymentStatus, $orderStatus, $paymentId);
    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Database update failed: " . $conn->error);
    } else {
        error_log("Database updated successfully for payment ID: $paymentId");
    }
    
    // If payment is successful, send email receipt
    if ($apiPaymentStatus == 'paid') {
        // Get order details from database
        $orderQuery = "SELECT o.*, GROUP_CONCAT(oi.product_name, ' (', oi.size, ') x', oi.quantity SEPARATOR '\n') as items 
                      FROM orders o 
                      LEFT JOIN order_items oi ON o.id = oi.order_id 
                      WHERE o.payment_id = ? 
                      GROUP BY o.id";
        
        $orderStmt = $conn->prepare($orderQuery);
        $orderStmt->bind_param('s', $paymentId);
        $orderStmt->execute();
        $orderResult = $orderStmt->get_result();
        
        if ($orderData = $orderResult->fetch_assoc()) {
            // Store order ID in session for reference
            $_SESSION['last_order_id'] = $orderData['id'];
            
            // Send email receipt
            $emailSent = sendOrderConfirmationEmail($orderData);
            error_log("Email sent: " . ($emailSent ? "Yes" : "No"));
        } else {
            error_log("Order data not found for payment ID: $paymentId");
        }
        
        // Redirect to confirmation page with success status
        header('Location: order-confirmation.php?status=success');
    } else {
        // Redirect to confirmation page with pending/failed status
        $redirectStatus = ($apiPaymentStatus == 'pending') ? 'pending' : 'failed';
        header("Location: order-confirmation.php?status=$redirectStatus");
    }
    
} catch (Exception $e) {
    // Log error for debugging
    error_log("Payment return error: " . $e->getMessage());
    
    // Redirect to confirmation page with failure status if any error occurs
    header('Location: order-confirmation.php?status=failed&reason=exception');
}

/**
 * Send order confirmation email to customer
 * 
 * @param array $orderData Order details
 * @return bool Whether email was sent successfully
 */
function sendOrderConfirmationEmail($orderData) {
    // Set email headers
    $to = $orderData['email'];
    $subject = "Your BYD Clothing Order #" . $orderData['id'] . " Receipt";
    
    // Format date
    $orderDate = date("F j, Y", strtotime($orderData['created_at'] ?? date("Y-m-d H:i:s")));
    
    // Build email body
    $message = "
    <html>
    <head>
        <title>Order Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background-color: #000; color: #fff; padding: 15px; text-align: center; }
            .content { padding: 20px; }
            .order-details { margin-bottom: 20px; }
            .order-items { margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
            .footer { background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Order Confirmation</h1>
            </div>
            <div class='content'>
                <p>Dear {$orderData['firstname']} {$orderData['lastname']},</p>
                <p>Thank you for your purchase from BYD Clothing. Your order has been confirmed and payment has been received.</p>
                
                <div class='order-details'>
                    <h2>Order Details</h2>
                    <p><strong>Order Number:</strong> #{$orderData['id']}</p>
                    <p><strong>Order Date:</strong> {$orderDate}</p>
                    <p><strong>Payment Method:</strong> Credit/Debit Card (PayMongo)</p>
                    <p><strong>Payment Status:</strong> Paid</p>
                </div>
                
                <div class='order-items'>
                    <h2>Order Items</h2>
                    <table>
                        <tr>
                            <th>Item</th>
                        </tr>";
    
    // Add order items
    $items = explode("\n", $orderData['items']);
    foreach ($items as $item) {
        $message .= "<tr><td>{$item}</td></tr>";
    }
    
    $message .= "
                    </table>
                </div>
                
                <div class='order-summary'>
                    <h2>Order Summary</h2>
                    <table>
                        <tr>
                            <td>Subtotal</td>
                            <td>₱" . number_format($orderData['subtotal'], 2) . "</td>
                        </tr>
                        <tr>
                            <td>Shipping</td>
                            <td>₱" . number_format($orderData['shipping_cost'], 2) . "</td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong>₱" . number_format($orderData['total_amount'], 2) . "</strong></td>
                        </tr>
                    </table>
                </div>
                
                <div class='shipping-info'>
                    <h2>Shipping Information</h2>
                    <p>{$orderData['firstname']} {$orderData['lastname']}</p>
                    <p>{$orderData['address']}</p>
                    <p>{$orderData['city']}, {$orderData['zipcode']}</p>
                    <p>Phone: {$orderData['phone']}</p>
                </div>
                
                <p>If you have any questions about your order, please contact our customer service at support@bydclothing.com</p>
                <p>Thank you for shopping with BYD Clothing!</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " BYD Clothing. All rights reserved.</p>
                <p>This is an automated email, please do not reply.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Set email headers for HTML content
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: BYD Clothing <noreply@bydclothing.com>" . "\r\n";
    
    // Send email
    return mail($to, $subject, $message, $headers);
}