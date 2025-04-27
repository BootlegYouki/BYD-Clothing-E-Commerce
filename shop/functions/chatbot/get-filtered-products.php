<?php
require_once '../../../admin/config/dbcon.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : null;
$minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : null;
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : null;
$sizes = isset($_GET['sizes']) ? explode(',', $_GET['sizes']) : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;

// Start building the query
$query = "SELECT id, name, original_price, discount_percentage, image_url, category, is_new_release FROM products WHERE 1=1";
$params = [];
$types = "";

// Add category filter if provided
if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// Add price filters if provided
if ($minPrice !== null) {
    $query .= " AND original_price >= ?";
    $params[] = $minPrice;
    $types .= "d";
}

if ($maxPrice !== null) {
    $query .= " AND original_price <= ?";
    $params[] = $maxPrice;
    $types .= "d";
}

// Add size filters if provided - this requires joins to the inventory table
if ($sizes && !empty($sizes)) {
    $placeholders = str_repeat('?,', count($sizes) - 1) . '?';
    $query .= " AND id IN (
        SELECT product_id FROM product_inventory 
        WHERE size IN ($placeholders) AND quantity > 0
    )";
    
    foreach ($sizes as $size) {
        $params[] = $size;
        $types .= "s";
    }
}

// Add sorting and limit
$query .= " ORDER BY is_new_release DESC, id DESC LIMIT ?";
$params[] = $limit;
$types .= "i";

// Prepare and execute the query
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch the results
$products = [];
while ($row = $result->fetch_assoc()) {
    // Calculate final price with discount
    $finalPrice = $row['original_price'] * (1 - ($row['discount_percentage'] / 100));
    $row['final_price'] = number_format($finalPrice, 2);
    
    // Get available sizes for this product
    $sizeStmt = $conn->prepare("
        SELECT size, quantity 
        FROM product_inventory 
        WHERE product_id = ? AND quantity > 0
    ");
    
    $sizeStmt->bind_param("i", $row['id']);
    $sizeStmt->execute();
    $sizeResult = $sizeStmt->get_result();
    
    $availableSizes = [];
    while ($sizeRow = $sizeResult->fetch_assoc()) {
        $availableSizes[$sizeRow['size']] = $sizeRow['quantity'];
    }
    
    $row['available_sizes'] = $availableSizes;
    
    $products[] = $row;
}

// Return the products as JSON
echo json_encode($products);
?>
