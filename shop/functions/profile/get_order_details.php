<?php
require_once '../../../admin/config/dbcon.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please log in to view order details'
    ]);
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Order ID is required'
    ]);
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['auth_user']['user_id'];

// Get order details
$order_query = "SELECT 
                    o.*, 
                    o.id as order_id,
                    o.reference_number,
                    u.firstname, 
                    u.lastname, 
                    u.email, 
                    u.full_address as address,
                    u.phone_number as phone
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Order not found or access denied'
    ]);
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$items_query = "SELECT 
                  oi.*, 
                  p.name as product_name,
                  (oi.price * oi.quantity) as subtotal
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$items = [];
while ($item = $items_result->fetch_assoc()) {
    $items[] = $item;
}

// Return data
echo json_encode([
    'status' => 'success',
    'order' => $order,
    'items' => $items
]);
?>