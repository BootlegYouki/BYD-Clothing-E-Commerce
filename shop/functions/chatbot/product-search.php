<?php
require_once '../../../admin/config/dbcon.php';
header('Content-Type: application/json');

// Get the search parameters from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$category = $data['category'] ?? null;
$query = $data['query'] ?? '';
$minPrice = $data['minPrice'] ?? null;
$maxPrice = $data['maxPrice'] ?? null;
$inStock = $data['inStock'] ?? false;

// Start building the SQL query
$sql = "SELECT p.*, 
        (SELECT JSON_OBJECTAGG(ps.size, ps.stock) FROM product_sizes ps WHERE ps.product_id = p.id) as stock_by_size
        FROM products p WHERE 1=1";

// Add category filter
if ($category) {
    if ($category == 'tshirt') {
        $sql .= " AND p.category = 'tshirt'";
    } else if ($category == 'longslv') {
        $sql .= " AND p.category = 'longslv'";
    }
}

// Add text search if provided
if (!empty($query)) {
    $searchTerm = '%' . $query . '%';
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Add price filters if provided
if ($minPrice !== null) {
    $sql .= " AND p.original_price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice !== null) {
    $sql .= " AND p.original_price <= ?";
    $params[] = $maxPrice;
}

// Add in-stock filter if requested
if ($inStock) {
    $sql .= " AND EXISTS (SELECT 1 FROM product_sizes ps WHERE ps.product_id = p.id AND ps.stock > 0)";
}

// Add sorting
$sql .= " ORDER BY p.is_new_release DESC, p.name ASC";

try {
    $stmt = $conn->prepare($sql);
    
    // Bind parameters if there are any
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        // Convert stock_by_size JSON string to object
        if ($row['stock_by_size']) {
            $row['stock_by_size'] = json_decode($row['stock_by_size'], true);
        } else {
            $row['stock_by_size'] = new stdClass();
        }
        
        // Calculate final price with discount
        if ($row['discount_percentage'] > 0) {
            $row['final_price'] = round($row['original_price'] * (1 - ($row['discount_percentage'] / 100)));
        } else {
            $row['final_price'] = $row['original_price'];
        }
        
        $products[] = $row;
    }
    
    echo json_encode($products);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
