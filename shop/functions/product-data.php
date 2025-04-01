<?php
header('Content-Type: application/json');
require_once '../../admin/config/dbcon.php';

// Get T-shirt products with stock by size
$tshirt_query = "SELECT p.*, 
                    GROUP_CONCAT(DISTINCT ps.size ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL')) as sizes,
                    GROUP_CONCAT(DISTINCT ps.size, ':', ps.stock ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL')) as size_stock
                FROM products p
                LEFT JOIN product_sizes ps ON p.id = ps.product_id
                WHERE p.category = 'T-Shirt'
                GROUP BY p.id";
$tshirt_result = mysqli_query($conn, $tshirt_query);

// Get Long Sleeve products with stock by size
$longslv_query = "SELECT p.*, 
                    GROUP_CONCAT(DISTINCT ps.size ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL')) as sizes,
                    GROUP_CONCAT(DISTINCT ps.size, ':', ps.stock ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL')) as size_stock
                FROM products p
                LEFT JOIN product_sizes ps ON p.id = ps.product_id
                WHERE p.category = 'Long Sleeve'
                GROUP BY p.id";
$longslv_result = mysqli_query($conn, $longslv_query);

$products = [
    'tshirts' => [],
    'longslv' => []
];

// Process T-shirt results
if ($tshirt_result) {
    while ($row = mysqli_fetch_assoc($tshirt_result)) {
        // Parse size_stock into a more usable format
        $size_stock_array = [];
        $size_stock_pairs = explode(',', $row['size_stock'] ?? '');
        foreach ($size_stock_pairs as $pair) {
            $parts = explode(':', $pair);
            if (count($parts) == 2 && !empty($parts[0]) && is_numeric($parts[1])) {
                $size_stock_array[$parts[0]] = intval($parts[1]);
            }
        }
        
        // Only add stock_by_size if we actually have data
        if (!empty($size_stock_array)) {
            $row['stock_by_size'] = $size_stock_array;
        }
        
        $products['tshirts'][] = $row;
    }
}

// Process Long Sleeve results
if ($longslv_result) {
    while ($row = mysqli_fetch_assoc($longslv_result)) {
        // Parse size_stock into a more usable format
        $size_stock_array = [];
        $size_stock_pairs = explode(',', $row['size_stock'] ?? '');
        foreach ($size_stock_pairs as $pair) {
            $parts = explode(':', $pair);
            if (count($parts) == 2 && !empty($parts[0]) && is_numeric($parts[1])) {
                $size_stock_array[$parts[0]] = intval($parts[1]);
            }
        }
        
        // Only add stock_by_size if we actually have data
        if (!empty($size_stock_array)) {
            $row['stock_by_size'] = $size_stock_array;
        }
        
        $products['longslv'][] = $row;
    }
}

echo json_encode($products);
?>