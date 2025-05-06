<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database connection
include('../../config/dbcon.php');

// Validate input
if (!isset($_POST['order_id']) || !is_numeric($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit();
}

if (!isset($_POST['status']) || empty($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Status cannot be empty']);
    exit();
}

$order_id = intval($_POST['order_id']);
$status = mysqli_real_escape_string($conn, $_POST['status']);
$send_notification = (isset($_POST['send_notification']) && $_POST['send_notification'] === 'yes');

// Validate status
$valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit();
}

// Get current order status for comparison
$query = "SELECT status, email, firstname, lastname, reference_number FROM orders WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$order = mysqli_fetch_assoc($result);
$old_status = $order['status'];

// If this is a notification-only request and the status matches
if ($send_notification && $old_status == $status) {
    $notification_sent = sendOrderStatusNotification($order, $status);
    
    echo json_encode([
        'success' => true,
        'message' => $notification_sent ? 'Notification sent successfully' : 'Failed to send notification',
        'notification_sent' => $notification_sent
    ]);
    
    mysqli_close($conn);
    exit();
}

// If status has changed
if ($old_status != $status) {
    // Update order status
    $update_query = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Send email notification if requested
        $notification_sent = false;
        if ($send_notification) {
            $notification_sent = sendOrderStatusNotification($order, $status);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Order status updated successfully',
            'old_status' => $old_status,
            'new_status' => $status,
            'notification_sent' => $notification_sent
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . mysqli_error($conn)
        ]);
    }
} else {
    // Status hasn't changed
    if ($send_notification) {
        // Still send notification even if status hasn't changed
        $notification_sent = sendOrderStatusNotification($order, $status);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Notification sent successfully',
            'notification_sent' => $notification_sent
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'message' => 'No changes made - status is already ' . $status
        ]);
    }
}

mysqli_close($conn);

/**
 * Send email notification to customer about order status change
 * 
 * @param array $order Order details
 * @param string $new_status New order status
 * @return bool Success status
 */
function sendOrderStatusNotification($order, $new_status) {
    $customer_email = $order['email'];
    $customer_name = $order['firstname'] . ' ' . $order['lastname'];
    $order_ref = $order['reference_number'];
    
    // Set email subject based on status
    $subject = "Your Order #$order_ref - ";
    switch($new_status) {
        case 'processing':
            $subject .= "Processing Started";
            break;
        case 'shipped':
            $subject .= "Shipped";
            break;
        case 'delivered':
            $subject .= "Delivered";
            break;
        case 'cancelled':
            $subject .= "Cancelled";
            break;
        default:
            $subject .= "Status Update";
    }
    
    // Create message body
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #f8f9fa; padding: 15px; text-align: center; }
            .content { padding: 20px 0; }
            .footer { font-size: 12px; text-align: center; margin-top: 30px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Order Status Update</h2>
                <p>Order Reference: $order_ref</p>
            </div>
            
            <div class='content'>
                <p>Dear $customer_name,</p>
                
                <p>Your order status has been updated to: <strong>" . ucfirst($new_status) . "</strong></p>";
    
    // Add specific message based on status
    switch($new_status) {
        case 'processing':
            $message .= "<p>We're now preparing your order for shipment. We'll notify you again when it ships.</p>";
            break;
        case 'shipped':
            $message .= "<p>Your order is on the way! You should receive it within the estimated delivery timeframe.</p>";
            break;
        case 'delivered':
            $message .= "<p>Your order has been delivered. We hope you enjoy your purchase!</p>
                         <p>If you have any issues with your order, please contact our customer support.</p>";
            break;
        case 'cancelled':
            $message .= "<p>Your order has been cancelled. If you did not request this cancellation or have any questions, please contact our customer support team.</p>";
            break;
    }
    
    $message .= "
                <p>Thank you for shopping with us!</p>
            </div>
            
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " BYD Clothing. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Set email headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: BYD Clothing <noreply@bydclothing.com>\r\n";
    
    // Send email
    return mail($customer_email, $subject, $message, $headers);
}
