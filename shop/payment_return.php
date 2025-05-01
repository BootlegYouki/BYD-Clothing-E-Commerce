<?php
session_start();
include_once '../admin/config/dbcon.php';

// Get payment status from URL parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$reference = isset($_GET['reference']) ? $_GET['reference'] : '';
$payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';

// Set session variables to show appropriate modals on the homepage
if ($status === 'success') {
    // Store success message in session
    $_SESSION['payment_status'] = 'success';
    $_SESSION['order_reference'] = $reference;
    
    // If payment_id is provided, update the transaction in the database
    if (!empty($payment_id)) {
        // Check if this payment already exists in our database
        $query = "SELECT id FROM transactions WHERE payment_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $payment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            // Payment doesn't exist yet, insert a placeholder until webhook updates it
            $insert_query = "INSERT INTO transactions (payment_id, status, description, created_at) 
                            VALUES (?, 'pending', 'Payment return placeholder - awaiting webhook', NOW())";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "s", $payment_id);
            mysqli_stmt_execute($stmt);
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
    if (!empty($payment_id)) {
        // Check if this payment already exists in our database
        $query = "SELECT id FROM transactions WHERE payment_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $payment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            // Payment doesn't exist yet, insert a failed record
            $insert_query = "INSERT INTO transactions (payment_id, status, description, created_at) 
                            VALUES (?, 'failed', 'Payment failed on return', NOW())";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "s", $payment_id);
            mysqli_stmt_execute($stmt);
        } else {
            // Update existing record
            $update_query = "UPDATE transactions SET status = 'failed', updated_at = NOW() WHERE payment_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "s", $payment_id);
            mysqli_stmt_execute($stmt);
        }
    }
    
    // Redirect to homepage
    echo "<script>
        window.location.href = 'index.php';
    </script>";
}
?>

// After confirming payment success, ensure we create a transaction record
if ($status == 'success' || $status == 'paid') {
    // Get order details
    $order_query = "SELECT * FROM orders WHERE tracking_no = ?";
    $stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($stmt, "s", $reference);
    mysqli_stmt_execute($stmt);
    $order_result = mysqli_stmt_get_result($stmt);
    
    if ($order = mysqli_fetch_assoc($order_result)) {
        // Check if transaction already exists
        $check_transaction = "SELECT * FROM transactions WHERE payment_id = ?";
        $stmt = mysqli_prepare($conn, $check_transaction);
        mysqli_stmt_bind_param($stmt, "s", $payment_id);
        mysqli_stmt_execute($stmt);
        $transaction_result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($transaction_result) == 0) {
            // Create transaction record
            $insert_transaction = "INSERT INTO transactions (
                payment_id, 
                customer_name, 
                email, 
                phone, 
                amount, 
                payment_method, 
                status, 
                description
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $insert_transaction);
            $description = "Payment for order #" . $reference;
            $status = 'paid';
            $payment_method = 'paymongo';
            
            mysqli_stmt_bind_param(
                $stmt, 
                "ssssdsss", 
                $payment_id, 
                $order['name'], 
                $order['email'], 
                $order['phone'], 
                $order['total_price'], 
                $payment_method, 
                $status, 
                $description
            );
            
            mysqli_stmt_execute($stmt);
            
            // Log transaction creation
            $log_file = __DIR__ . '/../paymongo/webhook/transaction_logs.txt';
            $log_data = date('Y-m-d H:i:s') . " - Created transaction record: Payment ID: $payment_id, Order: $reference\n";
            file_put_contents($log_file, $log_data, FILE_APPEND);
        }
    }
}