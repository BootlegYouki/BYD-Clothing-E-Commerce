<?php
/**
 * Payment Processing Script
 * 
 * This script handles the initial payment processing:
 * 1. Collects order data from the form
 * 2. Creates a payment link via PayMongo
 * 3. Stores order information in the database
 * 4. Returns the payment URL to redirect the customer
 */
require_once '../../admin/config/dbcon.php';
require_once 'PayMongoHelper.php';

session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Collect form data into structured array
$formData = [
    'user_id' => $_SESSION['auth_user']['user_id'],
    'firstname' => $_POST['firstname'],
    'lastname' => $_POST['lastname'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
    'address' => $_POST['address'],
    'city' => $_POST['city'],
    'zipcode' => $_POST['zipcode'],
    'total' => floatval($_POST['total']),
    'cart_items' => json_decode($_POST['cart_items'], true)
];

try {
    // Initialize PayMongo helper
    $paymongo = new PayMongoHelper();
    
    // Create payment link through PayMongo API
    $paymentLink = $paymongo->createPaymentLink(
        $formData['total'],
        'Order Payment for '.$formData['firstname'].' '.$formData['lastname'],
        [
            'user_id' => $formData['user_id'],
            'email' => $formData['email']
        ]
    );
    
    // Store order in database and get order ID
    $orderId = insertOrderToDatabase($conn, $formData, $paymentLink['data']['id']);
    
    // Return success response with payment URL
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'payment_url' => $paymentLink['data']['attributes']['checkout_url'],
        'order_id' => $orderId
    ]);
    
} catch (Exception $e) {
    // Return error response if payment creation fails
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Payment processing failed: '.$e->getMessage()
    ]);
}

/**
 * Insert order data into database
 * 
 * @param mysqli $conn Database connection
 * @param array $data Order data
 * @param string $paymentId PayMongo payment ID
 * @return int Order ID
 */
function insertOrderToDatabase($conn, $data, $paymentId) {
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
            city, 
            zipcode, 
            payment_method, 
            payment_id, 
            subtotal, 
            shipping_cost, 
            total_amount, 
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $order_query);
        
        // Calculate order subtotal from cart items
        $subtotal = 0;
        foreach ($data['cart_items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Set additional order details
        $shipping_cost = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 50;
        $payment_method = 'paymongo';
        $status = 'pending'; // Initial status
        
        // Bind parameters to prepared statement
        mysqli_stmt_bind_param(
            $stmt, 
            "isssssssssddss", 
            $data['user_id'],
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $data['zipcode'],
            $payment_method,
            $paymentId,
            $subtotal,
            $shipping_cost,
            $data['total'],
            $status
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
            $product_name = $item['name'] ?? $item['title'] ?? $item['productTitle'] ?? 'Product';
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