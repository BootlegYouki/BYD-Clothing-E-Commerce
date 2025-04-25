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

try {
    // Initialize PayMongo helper
    $paymongo = new PayMongoHelper();
    
    // Create checkout session through PayMongo API
    $customerInfo = [
        'email' => $formData['email'],
        'name' => $formData['firstname'] . ' ' . $formData['lastname'],
        'phone' => $formData['phone'],
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
    
    // Set up success and cancel URLs
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $successUrl = $baseUrl . "/BYD-Clothing-E-Commerce-main/shop/index.php?payment=success&order_id=" . urlencode('ORDER-' . time() . '-' . $formData['user_id']);
    $cancelUrl = $baseUrl . "/BYD-Clothing-E-Commerce-main/shop/index.php?payment=failed";
    
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
    
    // Store order in database and get order ID - Fix: use $formData instead of $data
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
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
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
        
        // Send order confirmation email with invoice
        sendOrderConfirmationEmail($data, $order_id, $paymentId);
        
        return $order_id;
    } catch (Exception $e) {
        // Rollback all changes if any error occurs
        mysqli_rollback($conn);
        throw $e;
    }
}

/**
 * Send order confirmation email with invoice to customer
 * 
 * @param array $data Order data
 * @param int $orderId Order ID
 * @param string $paymentId Payment ID
 * @return void
 */
function sendOrderConfirmationEmail($data, $orderId, $paymentId) {
    try {
        $mail = new PHPMailer(true);
        
        // For development/testing - log instead of sending
        // Comment this out and uncomment the SMTP settings below when ready for production
        // $mail->isMail();
    
        // Uncomment for production with SMTP
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // SMTP username
        $mail->Password = 'your-app-password'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        
        // Recipients
        $mail->setFrom('noreply@bydclothing.com', 'BYD Clothing');
        $mail->addAddress($data['email'], $data['firstname'] . ' ' . $data['lastname']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your Order Confirmation - BYD Clothing";
        
        // Generate invoice HTML
        $message = "
        <html>
        <head>
            <title>Order Confirmation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .invoice { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #eee; }
                .header { text-align: center; margin-bottom: 20px; }
                .order-details { margin-bottom: 20px; }
                .items-table { width: 100%; border-collapse: collapse; }
                .items-table th, .items-table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
                .total-row { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='invoice'>
                <div class='header'>
                    <h1>Order Confirmation</h1>
                    <p>Thank you for your purchase!</p>
                </div>
                
                <div class='order-details'>
                    <h2>Order Information</h2>
                    <p><strong>Order ID:</strong> {$orderId}</p>
                    <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
                    <p><strong>Payment ID:</strong> {$paymentId}</p>
                    
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> {$data['firstname']} {$data['lastname']}</p>
                    <p><strong>Email:</strong> {$data['email']}</p>
                    <p><strong>Phone:</strong> {$data['phone']}</p>
                    <p><strong>Address:</strong> {$data['address']}, {$data['zipcode']}</p>
                </div>
                
                <h3>Order Items</h3>
                <table class='items-table'>
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>";
        
        // Add each item to the invoice
        $subtotal = 0;
        foreach ($data['cart_items'] as $item) {
            $product_name = $item['name'] ?? $item['title'] ?? $item['productTitle'] ?? 'Product';
            $size = $item['size'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $item_subtotal = $price * $quantity;
            $subtotal += $item_subtotal;
            
            $message .= "
                    <tr>
                        <td>{$product_name}</td>
                        <td>{$size}</td>
                        <td>{$quantity}</td>
                        <td>₱" . number_format($price, 2) . "</td>
                        <td>₱" . number_format($item_subtotal, 2) . "</td>
                    </tr>";
        }
        
        // Add shipping and total
        $shipping_cost = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 50;
        $total = $subtotal + $shipping_cost;
        
        $message .= "
                    <tr>
                        <td colspan='4' class='total-row'>Subtotal</td>
                        <td class='total-row'>₱" . number_format($subtotal, 2) . "</td>
                    </tr>
                    <tr>
                        <td colspan='4' class='total-row'>Shipping</td>
                        <td class='total-row'>₱" . number_format($shipping_cost, 2) . "</td>
                    </tr>
                    <tr>
                        <td colspan='4' class='total-row'>Total</td>
                        <td class='total-row'>₱" . number_format($total, 2) . "</td>
                    </tr>
                </table>
                
                <div class='footer'>
                    <p>If you have any questions about your order, please contact our customer service.</p>
                    <p>Thank you for shopping with BYD Clothing!</p>
                </div>
            </div>
        </body>
        </html>";
        
        $mail->Body = $message;
        
        // For development - log email content instead of sending
        error_log("Email would be sent to: " . $data['email']);
        error_log("Email subject: Your Order Confirmation - BYD Clothing");
        
        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// Inside the createCheckoutSession call, update the success_url
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$successUrl = $baseUrl . "/BYD-Clothing-E-Commerce-main/shop/index.php?payment=success&order_id=" . urlencode($metadata['reference_number']);
$cancelUrl = $baseUrl . "/BYD-Clothing-E-Commerce-main/shop/index.php?payment=failed";

// Prepare request data
$requestData = [
    'data' => [
        'attributes' => [
            'line_items' => $lineItems,
            'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay'],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'description' => $description,
            'send_email_receipt' => true, // Enable PayMongo's email receipt
            'show_description' => true,
            'show_line_items' => true
        ]
    ]
];