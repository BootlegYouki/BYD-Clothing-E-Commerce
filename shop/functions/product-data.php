<?php
header('Content-Type: application/json');
require_once '../../admin/config/dbcon.php';

// Get T-shirt products with stock by size
$tshirt_query = "SELECT p.*, 
                    GROUP_CONCAT(DISTINCT ps.size ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL')) as sizes,
                    GROUP_CONCAT(DISTINCT ps.size, ':', ps.stock ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL')) as size_stock,
                    DATE(p.created_at) as created_date,
                    CURRENT_DATE() as today_date,
                    DATEDIFF(CURRENT_DATE(), DATE(p.created_at)) as days_since_creation
                FROM products p
                LEFT JOIN product_sizes ps ON p.id = ps.product_id
                WHERE p.category = 'T-Shirt'
                GROUP BY p.id";
$tshirt_result = mysqli_query($conn, $tshirt_query);

// Get Long Sleeve products with stock by size
$longslv_query = "SELECT p.*, 
                    GROUP_CONCAT(DISTINCT ps.size ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL')) as sizes,
                    GROUP_CONCAT(DISTINCT ps.size, ':', ps.stock ORDER BY FIELD(ps.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL')) as size_stock,
                    DATE(p.created_at) as created_date,
                    CURRENT_DATE() as today_date,
                    DATEDIFF(CURRENT_DATE(), DATE(p.created_at)) as days_since_creation
                FROM products p
                LEFT JOIN product_sizes ps ON p.id = ps.product_id
                WHERE p.category = 'Long Sleeve'
                GROUP BY p.id";
$longslv_result = mysqli_query($conn, $longslv_query);

$products = [
    'tshirts' => [],
    'longslv' => []
];

// Variables to track if we have any new products
$has_new_tshirts = false;
$has_new_longslv = false;

// Process T-shirt results
if ($tshirt_result) {
    $tshirt_data = [];
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
        
        // Check if this product would be considered new
        if ($row['days_since_creation'] <= 7 || $row['is_new_release'] == 1) {
            $row['is_new'] = true;
            $has_new_tshirts = true;
        } else {
            $row['is_new'] = false;
        }
        
        $tshirt_data[] = $row;
    }
    
    // If no new t-shirts, mark the most recent one as new
    if (!$has_new_tshirts && count($tshirt_data) > 0) {
        // Find the most recent t-shirt by date
        $newest_index = 0;
        $newest_date = 0;
        
        foreach ($tshirt_data as $index => $product) {
            $date_created = strtotime($product['created_at']);
            if ($date_created > $newest_date) {
                $newest_date = $date_created;
                $newest_index = $index;
            }
        }
        
        // Mark the most recent t-shirt as new
        $tshirt_data[$newest_index]['is_new'] = true;
    }
    
    $products['tshirts'] = $tshirt_data;
}

// Process Long Sleeve results
if ($longslv_result) {
    $longslv_data = [];
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
        
        // Check if this product would be considered new
        if ($row['days_since_creation'] <= 7 || $row['is_new_release'] == 1) {
            $row['is_new'] = true;
            $has_new_longslv = true;
        } else {
            $row['is_new'] = false;
        }
        
        $longslv_data[] = $row;
    }
    
    // If no new long sleeves, mark the most recent one as new
    if (!$has_new_longslv && count($longslv_data) > 0) {
        // Find the most recent long sleeve by date
        $newest_index = 0;
        $newest_date = 0;
        
        foreach ($longslv_data as $index => $product) {
            $date_created = strtotime($product['created_at']);
            if ($date_created > $newest_date) {
                $newest_date = $date_created;
                $newest_index = $index;
            }
        }
        
        // Mark the most recent long sleeve as new
        $longslv_data[$newest_index]['is_new'] = true;
    }
    
    $products['longslv'] = $longslv_data;
}

echo json_encode($products);
?>