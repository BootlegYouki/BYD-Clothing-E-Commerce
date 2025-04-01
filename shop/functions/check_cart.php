<?php
require_once '../../admin/config/dbcon.php'; // Correct path with double dots
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['auth_user']) && isset($_SESSION['auth_user']['user_id']);
}

// Get user ID if logged in
function getUserId() {
    return isLoggedIn() ? $_SESSION['auth_user']['user_id'] : null;
}

// Save cart to database for logged-in users
function saveCartToDatabase($cartData) {
    global $conn;
    $userId = getUserId();
    
    if (!$userId) {
        return ['success' => false, 'message' => 'User not logged in'];
    }
    
    // Convert to JSON string if it's not already
    $cartJson = is_string($cartData) ? $cartData : json_encode($cartData);
    $cartJson = mysqli_real_escape_string($conn, $cartJson);
    
    // Check if user already has a cart
    $checkQuery = "SELECT id FROM user_carts WHERE user_id = $userId";
    $checkResult = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        // Update existing cart
        $query = "UPDATE user_carts SET cart_data = '$cartJson', last_updated = NOW() WHERE user_id = $userId";
    } else {
        // Create new cart
        $query = "INSERT INTO user_carts (user_id, cart_data, last_updated) VALUES ($userId, '$cartJson', NOW())";
    }
    
    if (mysqli_query($conn, $query)) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => mysqli_error($conn)];
    }
}

// Load cart from database for logged-in users
function loadCartFromDatabase() {
    global $conn;
    $userId = getUserId();
    
    if (!$userId) {
        return null;
    }
    
    $query = "SELECT cart_data FROM user_carts WHERE user_id = $userId";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['cart_data']; // Return the JSON string directly
    }
    
    return null;
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    header('Content-Type: application/json');
    
    switch ($action) {
        case 'save':
            $cartData = $_POST['cart_data'] ?? '[]';
            $result = saveCartToDatabase($cartData);
            echo json_encode($result);
            break;
            
        case 'load':
            $cartData = loadCartFromDatabase();
            echo json_encode(['success' => true, 'cart' => $cartData]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>