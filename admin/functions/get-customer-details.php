<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

require_once '../config/dbcon.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Customer ID is required']);
    exit();
}

$customer_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get customer details including latitude and longitude
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
          (SELECT MAX(created_at) FROM orders WHERE user_id = u.id) as last_order_date
          FROM users u WHERE u.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
    
    // Format the dates
    $customer['joined_date'] = date('M d, Y', strtotime($customer['created_at']));
    $customer['last_order_date'] = $customer['last_order_date'] ? date('M d, Y', strtotime($customer['last_order_date'])) : null;
    
    // Prepare response
    $response = [
        'id' => $customer['id'],
        'firstname' => $customer['firstname'],
        'lastname' => $customer['lastname'],
        'email' => $customer['email'],
        'phone_number' => isset($customer['phone_number']) ? $customer['phone_number'] : null,
        'full_address' => isset($customer['full_address']) ? $customer['full_address'] : null, // CHANGED from 'address' to 'full_address'
        'city' => isset($customer['city']) ? $customer['city'] : null,
        'state' => isset($customer['state']) ? $customer['state'] : null,
        'zipcode' => isset($customer['zipcode']) ? $customer['zipcode'] : null,
        'latitude' => isset($customer['latitude']) ? $customer['latitude'] : null,
        'longitude' => isset($customer['longitude']) ? $customer['longitude'] : null,
        'joined_date' => $customer['joined_date'],
        'order_count' => $customer['order_count'],
        'last_order_date' => $customer['last_order_date']
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Customer not found']);
}

$stmt->close();
$conn->close();
?>
