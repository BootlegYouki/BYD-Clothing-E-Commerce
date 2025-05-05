<?php
function getShopProducts($conn, $params = []) {
    // Extract parameters with defaults
    $view_product_id = isset($params['view_product_id']) ? intval($params['view_product_id']) : 0;
    $category_filter = isset($params['category']) ? $params['category'] : '';
    $search_query = isset($params['search']) ? trim($params['search']) : '';
    $sort = isset($params['sort']) ? $params['sort'] : 'default';
    $page = isset($params['page']) ? intval($params['page']) : 1;
    $items_per_page = isset($params['items_per_page']) ? intval($params['items_per_page']) : 8;
    
    $offset = ($page - 1) * $items_per_page;
    
    // Build base query - removed ORDER BY and LIMIT for array-based sorting
    $base_query = "SELECT p.*, pi.image_url as image
                   FROM products p
                   LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                   WHERE 1=1";
    
    // If viewing a specific product, only use that filter
    if ($view_product_id > 0) {
        $base_query .= " AND p.id = $view_product_id";
    } else {
        // Only apply these filters when NOT viewing a specific product
        // Add category filter if specified - make it case-insensitive
        if (!empty($category_filter)) {
            $category_filter = mysqli_real_escape_string($conn, $category_filter);
            $base_query .= " AND LOWER(p.category) = LOWER('$category_filter')";
        }
        
        // Add search filter if specified - make it case-insensitive
        if (!empty($search_query)) {
            $search_query = mysqli_real_escape_string($conn, $search_query);
            $base_query .= " AND (
                LOWER(p.name) LIKE LOWER('%$search_query%') OR 
                LOWER(p.description) LIKE LOWER('%$search_query%') OR 
                LOWER(p.category) LIKE LOWER('%$search_query%')
            )";
        }
    }
    
    // Execute the query to get all matching products (without sorting or pagination)
    $result = mysqli_query($conn, $base_query);
    $all_products = [];
    $products = [];
    
    // Fetch all products with their additional images
    if ($result && mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            // Get additional images for this product
            $additionalImages = [];
            $image_query = "SELECT image_url FROM product_images WHERE product_id = {$product['id']} AND is_primary = 0 ORDER BY id ASC LIMIT 5";
            $image_result = mysqli_query($conn, $image_query);
            
            if ($image_result && mysqli_num_rows($image_result) > 0) {
                while ($image = mysqli_fetch_assoc($image_result)) {
                    $additionalImages[] = '../' . $image['image_url'];
                }
            }
            
            // Format the product data for display
            $formatted_product = [
                'id' => $product['id'],
                'title' => $product['name'],
                'price' => $product['original_price'],
                'discount_price' => $product['discount_price'], 
                'category' => $product['category'],
                'discount_percentage' => $product['discount_percentage'],
                'description' => $product['description'],
                'image' => !empty($product['image']) ? '../' . $product['image'] : 'img/placeholder.jpg',
                'additionalImages' => $additionalImages,
                'sku' => $product['sku'],
                'is_featured' => $product['is_featured'],
                'is_new_release' => $product['is_new_release'],
                'created_at' => $product['created_at']
            ];
            
            // Get available sizes for this product
            $sizes_query = "SELECT size, stock FROM product_sizes WHERE product_id = {$product['id']} ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL')";
            $sizes_result = mysqli_query($conn, $sizes_query);
            
            $available_sizes = [];
            if ($sizes_result && mysqli_num_rows($sizes_result) > 0) {
                while ($size = mysqli_fetch_assoc($sizes_result)) {
                    $available_sizes[$size['size']] = intval($size['stock']);
                }
            }
            
            $formatted_product['availableSizes'] = $available_sizes;
            $all_products[] = $formatted_product;
        }
    }
    
    // Apply PHP-based sorting
    sortProductsArray($all_products, $sort);
    
    // Count total items for pagination
    $total_items = count($all_products);
    $total_pages = ceil($total_items / $items_per_page);
    
    // Apply pagination to the array
    $products = array_slice($all_products, $offset, $items_per_page);
    
    return [
        'products' => $products,
        'total_items' => $total_items,
        'total_pages' => $total_pages
    ];
}

/**
 * Custom sorting function for product arrays
 * 
 * @param array &$products Reference to the products array to be sorted
 * @param string $sort Sort option
 * @return void
 */
function sortProductsArray(&$products, $sort) {
    if (count($products) <= 1) {
        return;
    }
    
    // Use quicksort algorithm
    quicksortProducts($products, 0, count($products) - 1, $sort);
}

/**
 * Quicksort implementation for product arrays
 * 
 * @param array &$products Reference to the products array
 * @param int $left Start index
 * @param int $right End index
 * @param string $sort Sort option
 * @return void
 */
function quicksortProducts(&$products, $left, $right, $sort) {
    if ($left < $right) {
        // Get the partition index
        $partitionIndex = partitionProducts($products, $left, $right, $sort);
        
        // Sort the left part
        quicksortProducts($products, $left, $partitionIndex - 1, $sort);
        // Sort the right part
        quicksortProducts($products, $partitionIndex + 1, $right, $sort);
    }
}

/**
 * Partition function for quicksort
 * 
 * @param array &$products Reference to the products array
 * @param int $left Start index
 * @param int $right End index
 * @param string $sort Sort option
 * @return int The partition index
 */
function partitionProducts(&$products, $left, $right, $sort) {
    // Choose the rightmost element as pivot
    $pivot = $products[$right];
    $i = $left - 1;
    
    for ($j = $left; $j < $right; $j++) {
        // Compare based on sort option
        if (compareProducts($products[$j], $pivot, $sort) <= 0) {
            $i++;
            // Swap elements
            $temp = $products[$i];
            $products[$i] = $products[$j];
            $products[$j] = $temp;
        }
    }
    
    // Swap the pivot element
    $temp = $products[$i + 1];
    $products[$i + 1] = $products[$right];
    $products[$right] = $temp;
    
    return $i + 1;
}

/**
 * Compare two products based on sort criteria
 * 
 * @param array $a First product
 * @param array $b Second product
 * @param string $sort Sort option
 * @return int Negative if $a < $b, positive if $a > $b, 0 if equal
 */
function compareProducts($a, $b, $sort) {
    switch ($sort) {
        case 'price-asc':
            $price_a = ($a['discount_price'] > 0) ? $a['discount_price'] : $a['price'];
            $price_b = ($b['discount_price'] > 0) ? $b['discount_price'] : $b['price'];
            return $price_a - $price_b;
            
        case 'price-desc':
            $price_a = ($a['discount_price'] > 0) ? $a['discount_price'] : $a['price'];
            $price_b = ($b['discount_price'] > 0) ? $b['discount_price'] : $b['price'];
            return $price_b - $price_a;
            
        case 'name-asc':
            return strcasecmp($a['title'], $b['title']);
            
        case 'name-desc':
            return strcasecmp($b['title'], $a['title']);
            
        case 'discount':
            $discount_a = $a['price'] - ($a['discount_price'] > 0 ? $a['discount_price'] : $a['price']);
            $discount_b = $b['price'] - ($b['discount_price'] > 0 ? $b['discount_price'] : $b['price']);
            return $discount_b - $discount_a;
            
        default: // default is newest first
            return strtotime($b['created_at']) - strtotime($a['created_at']);
    }
}

function getAllCategories($conn) {
    $category_query = "SELECT DISTINCT category FROM products ORDER BY category ASC";
    $category_result = mysqli_query($conn, $category_query);
    $categories = [];
    
    if ($category_result && mysqli_num_rows($category_result) > 0) {
        while ($row = mysqli_fetch_assoc($category_result)) {
            $categories[] = $row['category'];
        }
    }
    
    return $categories;
}
?>