<?php
require_once '../../../admin/config/dbcon.php';

// Set content type to JSON
header('Content-Type: application/json');

// Start session to get user ID
session_start();
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Function to get cart information
function getCartInfo($userId) {
    global $conn;
    
    if (!$userId) {
        return [
            'status' => 'error',
            'message' => 'User not logged in',
            'items' => [],
            'total' => 0
        ];
    }
    
    // First, get the active cart for this user
    $stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [
            'status' => 'success',
            'message' => 'Cart is empty',
            'items' => [],
            'total' => 0
        ];
    }
    
    $cartData = $result->fetch_assoc();
    $cartId = $cartData['id'];
    
    // Now get all items in this cart
    $stmt = $conn->prepare("
        SELECT ci.product_id, ci.quantity, ci.size,
               p.name, p.original_price, p.discount_percentage, p.image_url
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_id = ?
    ");
    
    $stmt->bind_param("i", $cartId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $finalPrice = $row['original_price'] * (1 - ($row['discount_percentage'] / 100));
        $itemTotal = $finalPrice * $row['quantity'];
        $total += $itemTotal;
        
        $items[] = [
            'id' => $row['product_id'],
            'name' => $row['name'],
            'quantity' => $row['quantity'],
            'size' => $row['size'],
            'price' => number_format($finalPrice, 2),
            'image' => $row['image_url']
        ];
    }
    
    return [
        'status' => 'success',
        'items' => $items,
        'total' => number_format($total, 2),
        'item_count' => count($items)
    ];
}

// Return cart information
echo json_encode(getCartInfo($userId));
?>
