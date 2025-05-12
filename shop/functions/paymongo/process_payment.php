<?php
/**
 * Payment Processing Script
 * 
 * This script handles the initial payment processing:
 * 1. Collects order data from the form
 * 2. Creates a payment link via PayMongo
 * 3. Returns the payment URL to redirect the customer
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
    'latitude' => $_POST['latitude'] ?? null,  // Add latitude from form
    'longitude' => $_POST['longitude'] ?? null,  // Add longitude from form
    'total' => floatval($_POST['total']),
    'cart_items' => json_decode($_POST['cart_items'], true)
];

// Add shipping fee to the total amount
$shipping_cost = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 20;
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
    
    // Generate a unique reference for this order
    $orderReference = 'ORDER-' . time() . '-' . $formData['user_id'];
    
    // Calculate order subtotal from cart items
    $subtotal = 0;
    foreach ($formData['cart_items'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    // Create a simplified version of cart items for metadata
    $simpleCart = [];
    foreach ($formData['cart_items'] as $item) {
        $simpleCart[] = [
            'id' => isset($item['id']) ? $item['id'] : 0,
            'name' => $item['name'] ?? $item['title'] ?? $item['productTitle'] ?? 'Product',
            'size' => $item['size'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ];
    }
    
    // Store all order data in metadata - this will be available in the webhook
    $metadata = [
        'user_id' => $formData['user_id'],
        'reference_number' => $orderReference,
        'firstname' => $formData['firstname'],
        'lastname' => $formData['lastname'],
        'email' => $formData['email'],
        'phone' => $formData['phone'],
        'address' => $formData['address'],
        'zipcode' => $formData['zipcode'],
        'latitude' => $formData['latitude'],  // Add latitude to metadata
        'longitude' => $formData['longitude'],  // Add longitude to metadata
        'subtotal' => $subtotal,
        'shipping_cost' => $shipping_cost,
        'total_amount' => $subtotal + $shipping_cost,
        'cart_items' => json_encode($simpleCart),
        'order_date' => date('Y-m-d H:i:s')
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
    
    // Set up base URL for redirects - works with both local and hosted environments
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    
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
    
    // Add a special URL for the status check page for QR Ph payments
    $statusCheckUrl = $baseUrl . $projectPath . '/shop/functions/paymongo/payment_status.php?reference=' . urlencode($orderReference);
    
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
    
    // Store session ID in PHP session for reference
    $_SESSION['payment_id'] = $checkout['session_id'];
    $_SESSION['order_reference'] = $orderReference;
    
    // Generate the status check URL with the session ID
    $statusCheckUrlWithSession = $statusCheckUrl . '&session_id=' . $checkout['session_id'];
    
    // Return success response with checkout URL
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'payment_url' => $checkout['checkout_url'],
        'reference' => $orderReference,
        'session_id' => $checkout['session_id'],
        'status_check_url' => $statusCheckUrlWithSession,
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
