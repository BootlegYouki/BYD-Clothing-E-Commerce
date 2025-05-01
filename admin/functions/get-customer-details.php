<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

include '../config/dbcon.php';

if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Customer ID is required']);
    exit();
}

$customer_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get customer details
$query = "SELECT u.*,
          (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
          (SELECT MAX(created_at) FROM orders WHERE user_id = u.id) as last_order_date
          FROM users u
          WHERE u.id = '$customer_id' AND u.role_as = 0";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Customer not found']);
    exit();
}

$customer = mysqli_fetch_assoc($result);

// Format dates for display
$joined_date = date('M d, Y', strtotime($customer['created_at']));
$last_order_date = $customer['last_order_date'] ? date('M d, Y', strtotime($customer['last_order_date'])) : null;

// Prepare response
$response = [
    'id' => $customer['id'],
    'firstname' => $customer['firstname'],
    'lastname' => $customer['lastname'],
    'email' => $customer['email'],
    'phone' => isset($customer['phone_number']) ? $customer['phone_number'] : null,
    'address' => isset($customer['full_address']) ? $customer['full_address'] : null, // Changed from address to full_address
    'city' => isset($customer['city']) ? $customer['city'] : null,
    'state' => isset($customer['state']) ? $customer['state'] : null,
    'zipcode' => isset($customer['zipcode']) ? $customer['zipcode'] : null,
    'joined_date' => $joined_date,
    'order_count' => $customer['order_count'],
    'last_order_date' => $last_order_date
];

header('Content-Type: application/json');
echo json_encode($response);
?>
