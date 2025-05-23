<?php
// Prevent direct output of PHP errors that would break JSON format
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Function to handle and return JSON errors
function return_json_error($message, $status_code = 500) {
    http_response_code($status_code);
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

// Custom error handler to catch PHP errors and return them as JSON
function json_error_handler($errno, $errstr, $errfile, $errline) {
    $error_message = "PHP Error ($errno): $errstr in $errfile on line $errline";
    error_log($error_message); // Log to server error log
    return_json_error($error_message);
    return true; // Don't execute PHP internal error handler
}

// Set the custom error handler
set_error_handler("json_error_handler");

// Wrap the entire script in a try-catch to catch any uncaught exceptions
try {
    session_start();
    // Check if user is logged in and is an admin
    if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
        exit();
    }

    // Include database connection
    include('../../config/dbcon.php');

    // Process AJAX request for updating order status
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get order ID and new status
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $new_status = isset($_POST['status']) ? $_POST['status'] : '';
        $notify_customer = isset($_POST['notify_customer']) && $_POST['notify_customer'] === 'yes';
        
        // Validate input
        if ($order_id <= 0 || !in_array($new_status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid order ID or status']);
            exit();
        }
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Get current order status first
            $current_status_query = "SELECT status FROM orders WHERE id = ?";
            $stmt = mysqli_prepare($conn, $current_status_query);
            mysqli_stmt_bind_param($stmt, 'i', $order_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $current_status_row = mysqli_fetch_assoc($result);
            $current_status = $current_status_row ? $current_status_row['status'] : '';
            
            // Update order status in database
            $update_query = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, 'si', $new_status, $order_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update order status: " . mysqli_stmt_error($stmt));
            }
            
            // If changing to cancelled and order was previously not cancelled,
            // we need to restore the stock
            if ($new_status === 'cancelled' && $current_status !== 'cancelled') {
                restoreProductStock($conn, $order_id);
            }
            
            // Get customer information for the order
            $customer_query = "SELECT user_id, firstname, lastname, email, reference_number FROM orders WHERE id = ?";
            $stmt = mysqli_prepare($conn, $customer_query);
            mysqli_stmt_bind_param($stmt, 'i', $order_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $order = mysqli_fetch_assoc($result);
            
            $notification_sent = false;
            
            // Only proceed with notification if there's a user_id (registered customer) and notification is requested
            if (!empty($order['user_id']) && $notify_customer) {
                // Create notification for the customer
                $notification_type = '';
                $notification_title = '';
                $notification_message = '';
                
                // Set appropriate notification details based on order status
                switch ($new_status) {
                    case 'processing':
                        $notification_type = 'order_status';
                        $notification_title = 'Your Order '.$order['reference_number'].' is Being Processed';
                        $notification_message = 'Good news! We\'re now processing your order '.$order['reference_number'].'. We\'ll update you again when it ships.';
                        break;
                        
                    case 'shipped':
                        $notification_type = 'order_shipped';
                        $notification_title = 'Your Order '.$order['reference_number'].' Has Been Shipped';
                        $notification_message = 'Great news! Your order '.$order['reference_number'].' has been shipped and is on its way to you.';
                        break;
                        
                    case 'delivered':
                        $notification_type = 'order_delivered';
                        $notification_title = 'Your Order '.$order['reference_number'].' Has Been Delivered';
                        $notification_message = 'Your order '.$order['reference_number'].' has been marked as delivered. We hope you enjoy your purchase! If you have any issues, please contact our customer service.';
                        break;
                        
                    case 'cancelled':
                        $notification_type = 'order_status';
                        $notification_title = 'Your Order '.$order['reference_number'].' Has Been Cancelled';
                        $notification_message = 'Your order '.$order['reference_number'].' has been cancelled. If you did not request this cancellation, please contact our customer service.';
                        break;
                        
                    case 'pending':
                        $notification_type = 'order_status';
                        $notification_title = 'Your Order '.$order['reference_number'].' is Pending';
                        $notification_message = 'Your order '.$order['reference_number'].' has been updated to pending status. We\'ll notify you when it starts processing.';
                        break;
                }
                
                // Insert notification if we have valid content
                if (!empty($notification_type) && !empty($notification_title)) {
                    $insert_query = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insert_query);
                    mysqli_stmt_bind_param($stmt, 'isss', $order['user_id'], $notification_type, $notification_title, $notification_message);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $notification_sent = true;
                    } else {
                        // Log the error but don't fail the transaction
                        error_log("Failed to create notification: " . mysqli_stmt_error($stmt));
                    }
                }
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Return success response
            echo json_encode([
                'status' => 'success', 
                'message' => 'Order status updated successfully',
                'notification_sent' => $notification_sent
            ]);
            
        } catch (Exception $e) {
            // Roll back transaction on error
            mysqli_rollback($conn);
            
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        
        exit();
    }

    // Handle bulk status updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['order_ids'])) {
        $action = $_POST['bulk_action'];
        $order_ids = $_POST['order_ids'];
        $notify_customer = isset($_POST['notify_customer']) && $_POST['notify_customer'] === 'yes';
        
        if(!empty($order_ids) && in_array($action, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            $status = mysqli_real_escape_string($conn, $action);
            $notifications_created = 0;
            
            // Start transaction
            mysqli_begin_transaction($conn);
            
            try {
                // Get all orders with customer information
                $ids = implode(',', array_map('intval', $order_ids));
                $orders_query = "SELECT id, user_id, firstname, lastname, email, reference_number FROM orders WHERE id IN ($ids)";
                $orders_result = mysqli_query($conn, $orders_query);
                
                if (!$orders_result) {
                    throw new Exception("Failed to fetch orders: " . mysqli_error($conn));
                }
                
                // Update order statuses
                $update_query = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id IN ($ids)";
                if (!mysqli_query($conn, $update_query)) {
                    throw new Exception("Failed to update order statuses: " . mysqli_error($conn));
                }
                
                // Create notifications for each order with a user_id
                while ($order = mysqli_fetch_assoc($orders_result)) {
                    if (empty($order['user_id']) || !$notify_customer) {
                        continue; // Skip guests or if notification is not requested
                    }
                    
                    // Determine notification content based on status
                    $notification_type = '';
                    $notification_title = '';
                    $notification_message = '';
                    
                    switch ($status) {
                        case 'processing':
                            $notification_type = 'order_status';
                            $notification_title = 'Your Order '.$order['reference_number'].' is Being Processed';
                            $notification_message = 'Good news! We\'re now processing your order '.$order['reference_number'].'. We\'ll update you again when it ships.';
                            break;
                            
                        case 'shipped':
                            $notification_type = 'order_shipped';
                            $notification_title = 'Your Order '.$order['reference_number'].' Has Been Shipped';
                            $notification_message = 'Great news! Your order '.$order['reference_number'].' has been shipped and is on its way to you.';
                            break;
                            
                        case 'delivered':
                            $notification_type = 'order_delivered';
                            $notification_title = 'Your Order '.$order['reference_number'].' Has Been Delivered';
                            $notification_message = 'Your order '.$order['reference_number'].' has been marked as delivered. We hope you enjoy your purchase! If you have any issues, please contact our customer service.';
                            break;
                            
                        case 'cancelled':
                            $notification_type = 'order_status';
                            $notification_title = 'Your Order '.$order['reference_number'].' Has Been Cancelled';
                            $notification_message = 'Your order '.$order['reference_number'].' has been cancelled. If you did not request this cancellation, please contact our customer service.';
                            break;
                            
                        case 'pending':
                            $notification_type = 'order_status';
                            $notification_title = 'Your Order '.$order['reference_number'].' is Pending';
                            $notification_message = 'Your order '.$order['reference_number'].' has been updated to pending status. We\'ll notify you when it starts processing.';
                            break;
                    }
                    
                    // Insert notification
                    if (!empty($notification_type) && !empty($notification_title)) {
                        $insert_query = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $insert_query);
                        mysqli_stmt_bind_param($stmt, 'isss', $order['user_id'], $notification_type, $notification_title, $notification_message);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $notifications_created++;
                        } else {
                            // Log the error but continue
                            error_log("Failed to create notification for order " . $order['id'] . ": " . mysqli_stmt_error($stmt));
                        }
                    }
                }
                
                // Commit transaction
                mysqli_commit($conn);
                
                // Redirect back with success message
                header("Location: ../../orders.php?msg=bulk_updated&count=".count($order_ids)."&notifications=".$notifications_created);
                exit();
                
            } catch (Exception $e) {
                // Roll back transaction on error
                mysqli_rollback($conn);
                
                // Redirect back with error
                header("Location: ../../orders.php?msg=error&error=".urlencode($e->getMessage()));
                exit();
            }
        }
    }

    // If reached here, method not allowed
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit();
} catch (Throwable $e) {
    // Catch any uncaught exceptions and return as JSON error
    error_log("Uncaught exception: " . $e->getMessage());
    return_json_error("System error: " . $e->getMessage());
}

/**
 * Restore product stock for cancelled orders
 *
 * @param mysqli $conn Database connection
 * @param int $orderId Order ID
 * @return bool True on success, false on failure
 */
function restoreProductStock($conn, $orderId) {
    // Get order items
    $query = "SELECT product_id, size, quantity FROM order_items WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Failed to fetch order items for stock restoration: " . mysqli_error($conn));
        return false;
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    // Process each order item
    while ($item = mysqli_fetch_assoc($result)) {
        $productId = $item['product_id'];
        $size = $item['size'];
        $quantity = $item['quantity'];
        
        // Update stock in database (add back the quantity)
        $updateQuery = "UPDATE product_sizes SET stock = stock + ? 
                      WHERE product_id = ? AND size = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "iis", $quantity, $productId, $size);
        
        if (!mysqli_stmt_execute($updateStmt)) {
            error_log("Error restoring stock for product $productId size $size: " . mysqli_error($conn));
        } else {
            error_log("Restored stock for product $productId size $size: +$quantity (order cancelled)");
        }
        
        mysqli_stmt_close($updateStmt);
    }
    
    return true;
}
?>
