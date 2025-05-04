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
require_once '../../../admin/config/dbcon.php';
require_once __DIR__ . '/PayMongoHelper.php';
require_once __DIR__ . '/EmailConfirmation.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    'zipcode' => $_POST['zipcode'],
    'total' => floatval($_POST['total']),
    'cart_items' => json_decode($_POST['cart_items'], true)
];

// Add shipping fee to the total amount
$shipping_cost = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 50;
$formData['total'] += $shipping_cost;
$formData['shipping_cost'] = $shipping_cost; // Store shipping cost in formData

try {
    // Initialize PayMongo helper
    $paymongo = new PayMongoHelper();
    
    // Create checkout session through PayMongo API
    $customerInfo = [
        'email' => $formData['email'],
        'name' => $formData['firstname'] . ' ' . $formData['lastname'],
        'phone' => preg_replace('/^0/', '', $formData['phone']),
        'address' => [
            'line1' => $formData['address'],
            'postal_code' => $formData['zipcode'],
            'country' => 'PH'
        ]
    ];
    
    $metadata = [
        'user_id' => $formData['user_id'],
        'reference_number' => 'ORDER-' . time() . '-' . $formData['user_id']
    ];
    
    // Define the description and line items before using them
    $description = 'Order Payment for '.$formData['firstname'].' '.$formData['lastname'];
    
    // Create line items from cart items
    $lineItems = [];
    foreach ($formData['cart_items'] as $item) {
        $lineItems[] = [
            'name' => $item['name'] ?? $item['title'] ?? $item['productTitle'] ?? 'Product',
            'quantity' => $item['quantity'],
            'amount' => $item['price'] * 100, // Convert to cents as required by PayMongo
            'currency' => 'PHP'
        ];
    }
    
    // Add shipping fee as a separate line item
    $lineItems[] = [
        'name' => 'Shipping Fee',
        'quantity' => 1,
        'amount' => $shipping_cost * 100, // Convert to cents as required by PayMongo
        'currency' => 'PHP'
    ];
    
    // Inside the createCheckoutSession call, update the success_url
    // Set up base URL for redirects - works with both local and hosted environments
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    
    // Generate a unique reference for this order
    $orderReference = 'ORDER-' . time() . '-' . $formData['user_id'];
    
    // Determine the correct path based on server environment
    $projectPath = '';
    
    // Check if we're on localhost and determine the correct path structure
    if (strpos($baseUrl, 'localhost') !== false) {
        // Get the current script path and extract the project path
        $currentPath = $_SERVER['SCRIPT_NAME'];
        $pathParts = explode('/', $currentPath);
        
        // Find the project folder in the path
        $projectIndex = array_search('BYD-Clothing-E-Commerce-main', $pathParts);
        if ($projectIndex !== false) {
            // Reconstruct the project path
            $projectPath = '';
            for ($i = 1; $i <= $projectIndex; $i++) {
                if (!empty($pathParts[$i])) {
                    $projectPath .= '/' . $pathParts[$i];
                }
            }
        }
    }
    
    // Construct the full URLs with the correct path
    $successUrl = $baseUrl . $projectPath . '/shop/payment_return.php?status=success&reference=' . urlencode($orderReference);
    $cancelUrl = $baseUrl . $projectPath . '/shop/payment_return.php?status=failed&reference=' . urlencode($orderReference);
    
    // Debug the URLs (remove in production)
    error_log("Success URL: " . $successUrl);
    error_log("Cancel URL: " . $cancelUrl);
    
    // Store reference in metadata for future reference
    $metadata['reference_number'] = $orderReference;
    
    // Create checkout session with all required parameters
    $checkout = $paymongo->createCheckoutSession(
        $formData['total'],
        $description,
        $metadata,
        $lineItems,
        $customerInfo,
        $successUrl,
        $cancelUrl
    );
    
    // Store order in database and get order ID
    $orderId = insertOrderToDatabase($conn, $formData, $checkout['session_id']);
    
    // Store payment ID in session for reference
    $_SESSION['payment_id'] = $checkout['session_id'];
    $_SESSION['last_order_id'] = $orderId;
    
    // Return success response with checkout URL
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'payment_url' => $checkout['checkout_url'],
        'order_id' => $orderId,
        'open_in_new_tab' => false  // Changed from true to false to open in same tab
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
            zipcode, 
            payment_method, 
            payment_id, 
            subtotal, 
            shipping_cost, 
            total_amount, 
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($conn, $order_query);
        
        // Calculate order subtotal from cart items
        $subtotal = 0;
        foreach ($data['cart_items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Set additional order details
        // Use shipping cost from the data array instead of getting it from $_POST again
        $shipping_cost = $data['shipping_cost'];
        $payment_method = 'Pending Selection'; // Changed from 'paymongo' to 'Pending Selection'
        $status = 'pending'; // Initial status
        
        // Make sure total_amount is exactly subtotal + shipping_cost to avoid any discrepancies
        $total_amount = $subtotal + $shipping_cost;
        
        // Bind parameters to prepared statement
        mysqli_stmt_bind_param(
            $stmt, 
            "issssssssddds", 
            $data['user_id'],
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['zipcode'],
            $payment_method,
            $paymentId,
            $subtotal,
            $shipping_cost,
            $total_amount,  // Use the calculated total instead of $data['total']
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
        
        // 3. Create initial transaction record
        $transaction_query = "INSERT INTO transactions (
            payment_intent_id,
            session_id,
            order_reference_number,
            status,
            payment_method,
            description,
            amount,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $trans_stmt = mysqli_prepare($conn, $transaction_query);
        
        $reference_number = 'ORDER-' . time() . '-' . $data['user_id'];
        $status = 'pending';
        $payment_method = 'Pending Selection';
        $description = 'Payment initiated';
        $amount = $total_amount;
        
        mysqli_stmt_bind_param(
            $trans_stmt, 
            "ssssssd", 
            $paymentId,
            $paymentId,
            $reference_number,
            $status,
            $payment_method,
            $description,
            $amount
        );
        mysqli_stmt_execute($trans_stmt);
        
        // Commit all database changes
        mysqli_commit($conn);
        
        // Send order confirmation email with invoice using the EmailConfirmation class
        EmailConfirmation::sendOrderConfirmationEmail($data, $order_id, $paymentId);
        
        return $order_id;
    } catch (Exception $e) {
        // Rollback all changes if any error occurs
        mysqli_rollback($conn);
        throw $e;
    }
}
