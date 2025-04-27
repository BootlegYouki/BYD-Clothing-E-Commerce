<?php
// Include the database connection
require_once '../../../admin/config/dbcon.php';

// Function to get user browsing history
function getUserBrowsingHistory($userId, $limit = 5) {
    global $conn;
    
    if (!$userId) return [];
    
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.original_price, p.discount_percentage, p.image_url, 
               p.category, h.timestamp
        FROM products p
        JOIN browsing_history h ON p.id = h.product_id
        WHERE h.user_id = ?
        ORDER BY h.timestamp DESC
        LIMIT ?
    ");
    
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    return $history;
}

// Function to get personalized product recommendations
function getPersonalizedRecommendations($userId, $limit = 3) {
    global $conn;
    
    if (!$userId) {
        // For non-logged in users, return popular products
        return getPopularProducts($limit);
    }
    
    // Get the categories the user has viewed most often
    $stmt = $conn->prepare("
        SELECT p.category, COUNT(*) as category_count
        FROM browsing_history h
        JOIN products p ON h.product_id = p.id
        WHERE h.user_id = ?
        GROUP BY p.category
        ORDER BY category_count DESC
        LIMIT 2
    ");
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $favoriteCategories = [];
    while ($row = $result->fetch_assoc()) {
        $favoriteCategories[] = $row['category'];
    }
    
    // If we have favorite categories, get products from those categories
    if (!empty($favoriteCategories)) {
        $placeholders = str_repeat('?,', count($favoriteCategories) - 1) . '?';
        
        $stmt = $conn->prepare("
            SELECT id, name, original_price, discount_percentage, image_url, category
            FROM products
            WHERE category IN ($placeholders)
            AND id NOT IN (
                SELECT product_id FROM browsing_history WHERE user_id = ?
            )
            ORDER BY RAND()
            LIMIT ?
        ");
        
        $types = str_repeat('s', count($favoriteCategories)) . 'ii';
        $params = array_merge($favoriteCategories, [$userId, $limit]);
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $recommendations = [];
        while ($row = $result->fetch_assoc()) {
            $recommendations[] = $row;
        }
        
        // If we found enough recommendations, return them
        if (count($recommendations) >= $limit) {
            return $recommendations;
        }
    }
    
    // Fallback to popular products
    return getPopularProducts($limit);
}

// Function to get popular products
function getPopularProducts($limit = 3) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.original_price, p.discount_percentage, p.image_url, p.category
        FROM products p
        LEFT JOIN order_items oi ON p.id = oi.product_id
        GROUP BY p.id
        ORDER BY COUNT(oi.id) DESC, p.id
        LIMIT ?
    ");
    
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    return $products;
}

// Function to get cart info for the current user
function getCartInfo() {
    global $conn;
    
    session_start();
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    if (!$userId) {
        return [
            'status' => 'error',
            'message' => 'User not logged in'
        ];
    }
    
    $stmt = $conn->prepare("
        SELECT c.id as cart_id, c.user_id, c.created_at,
               ci.product_id, ci.quantity, ci.size,
               p.name, p.original_price, p.discount_percentage, p.image_url
        FROM carts c
        JOIN cart_items ci ON c.id = ci.cart_id
        JOIN products p ON ci.product_id = p.id
        WHERE c.user_id = ? AND c.status = 'active'
    ");
    
    $stmt->bind_param("i", $userId);
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
            'price' => $finalPrice,
            'original_price' => $row['original_price'],
            'discount' => $row['discount_percentage'],
            'image' => $row['image_url'],
            'item_total' => $itemTotal
        ];
    }
    
    return [
        'status' => 'success',
        'items' => $items,
        'total' => $total,
        'item_count' => count($items)
    ];
}

// API Endpoint handling
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch ($action) {
        case 'cart':
            echo json_encode(getCartInfo());
            break;
            
        case 'recommendations':
            session_start();
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;
            
            echo json_encode([
                'status' => 'success',
                'recommendations' => getPersonalizedRecommendations($userId, $limit)
            ]);
            break;
            
        case 'history':
            session_start();
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            
            echo json_encode([
                'status' => 'success',
                'history' => getUserBrowsingHistory($userId, $limit)
            ]);
            break;
            
        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid action specified'
            ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
