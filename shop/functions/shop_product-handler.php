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
    
    // Build base query
    $base_query = "SELECT p.*, pi.image_url as image
                   FROM products p
                   LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                   WHERE 1=1";
    
    // If viewing a specific product, only use that filter
    if ($view_product_id > 0) {
        $base_query .= " AND p.id = $view_product_id";
    } else {
        // Only apply these filters when NOT viewing a specific product
        // Add category filter if specified
        if (!empty($category_filter)) {
            $category_filter = mysqli_real_escape_string($conn, $category_filter);
            $base_query .= " AND p.category = '$category_filter'";
        }
        
        // Add search filter if specified
        if (!empty($search_query)) {
            $search_query = mysqli_real_escape_string($conn, $search_query);
            $base_query .= " AND (p.name LIKE '%$search_query%' OR p.description LIKE '%$search_query%' OR p.category LIKE '%$search_query%')";
        }
    }
    
    // Add sorting logic
    switch ($sort) {
        case 'price-asc':
            $base_query .= " ORDER BY CASE 
                                WHEN p.discount_percentage > 0 THEN p.original_price - (p.original_price * p.discount_percentage / 100) 
                                ELSE p.original_price 
                              END ASC";
            break;
        case 'price-desc':
            $base_query .= " ORDER BY CASE 
                                WHEN p.discount_percentage > 0 THEN p.original_price - (p.original_price * p.discount_percentage / 100) 
                                ELSE p.original_price 
                              END DESC";
            break;
        case 'name-asc':
            $base_query .= " ORDER BY p.name ASC";
            break;
        case 'name-desc':
            $base_query .= " ORDER BY p.name DESC";
            break;
        case 'discount':
            $base_query .= " ORDER BY p.discount_percentage DESC";
            break;
        default:
            $base_query .= " ORDER BY p.is_featured DESC, p.created_at DESC";
            break;
    }
    
    // Count total query - for pagination
    $count_query = str_replace("p.*, pi.image_url as image", "COUNT(*) as total", $base_query);
    $count_result = mysqli_query($conn, $count_query);
    $total_items = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_items / $items_per_page);
    
    // Add pagination to the query
    $base_query .= " LIMIT $offset, $items_per_page";
    
    // Execute the final query
    $result = mysqli_query($conn, $base_query);
    $products = [];
    
    // Fetch products with their additional images
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
                'category' => $product['category'],
                'discount_percentage' => $product['discount_percentage'],
                'description' => $product['description'],
                'image' => !empty($product['image']) ? '../' . $product['image'] : 'img/placeholder.jpg',
                'additionalImages' => $additionalImages,
                'sku' => $product['sku'],
                'is_featured' => $product['is_featured'],
                'is_new_release' => $product['is_new_release']
            ];
            
            // Get available sizes for this product
            $sizes_query = "SELECT size, stock FROM product_sizes WHERE product_id = {$product['id']} ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL')";
            $sizes_result = mysqli_query($conn, $sizes_query);
            
            $available_sizes = [];
            if ($sizes_result && mysqli_num_rows($sizes_result) > 0) {
                while ($size = mysqli_fetch_assoc($sizes_result)) {
                    $available_sizes[$size['size']] = intval($size['stock']);
                }
            }
            
            $formatted_product['availableSizes'] = $available_sizes;
            $products[] = $formatted_product;
        }
    }
    
    return [
        'products' => $products,
        'total_items' => $total_items,
        'total_pages' => $total_pages
    ];
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