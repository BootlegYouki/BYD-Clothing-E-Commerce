<?php
/**
 * Gets products for display on the index page
 * 
 * @param mysqli $conn Database connection
 * @param array $params Parameters for product query
 * @return array Array of formatted products
 */
function getIndexProducts($conn, $params = []) {
    // Extract parameters with defaults
    $category = isset($params['category']) ? $params['category'] : '';
    $is_new_release = isset($params['is_new_release']) ? $params['is_new_release'] : false;
    $is_featured = isset($params['is_featured']) ? $params['is_featured'] : false;
    $limit = isset($params['limit']) ? intval($params['limit']) : 4;
    
    // Build query
    $query = "SELECT p.*, pi.image_url 
              FROM products p
              LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
              WHERE 1=1";
    
    // Add filters based on parameters
    if ($is_new_release) {
        $query .= " AND p.is_new_release = 1";
    }
    
    if ($is_featured) {
        $query .= " AND p.is_featured = 1";
    }
    
    if (!empty($category)) {
        $category = mysqli_real_escape_string($conn, $category);
        $query .= " AND p.category = '$category'";
    }
    
    // Add ordering and limit
    $query .= " ORDER BY p.id DESC LIMIT $limit";
    
    // Execute query
    $result = mysqli_query($conn, $query);
    $products = [];
    
    // Format products
    if ($result && mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            $products[] = $product;
        }
    }
    
    return $products;
}

/**
 * Renders a product card for display
 * 
 * @param array $product Product data
 * @param bool $for_swiper Whether the card is for a swiper or not
 * @return string HTML for the product card
 */
function renderProductCard($product, $for_swiper = false) {
    // Use discount_price directly instead of calculating it
    $originalPrice = $product['original_price'];
    $discountPercentage = $product['discount_percentage'];
    // if no discount, fall back to original price
    $discountedPrice = $discountPercentage > 0 
                       ? $product['discount_price'] 
                       : $originalPrice;
    
    // Check if there's an actual discount (prices are different)
    $hasActualDiscount = ($discountPercentage > 0 && $discountedPrice < $originalPrice);
    
    // Image URL with fallback
    $imageUrl = !empty($product['image_url']) ? '../' . $product['image_url'] : 'img/placeholder.jpg';
    
    // Start HTML output
    $output = '';
    
    if ($for_swiper) {
        $output .= '<div class="swiper-slide">';
    } else {
        $output .= '<div class="product text-center col-lg-3 col-md-6 col-12 mb-4">';
    }
    
    $output .= '
        <div class="product-card">
            <div class="product-img-container">
                <img class="img-fluid product-img mb-3" src="' . $imageUrl . '" alt="' . $product['name'] . '" loading="lazy">';
                
    if ($hasActualDiscount) {
        $output .= '<span class="discount-badge">-' . $discountPercentage . '%</span>';
    }
            
    $output .= '
            </div>
            <div class="product-info">
            <!--
                <div class="star mb-2">
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </div> 
                -->
                <h5 class="text-uppercase mb-2">' . $product['category'] . ' - "' . $product['name'] . '"</h5>
                <div class="price-container mb-3">';
                
    if ($hasActualDiscount) {
        $output .= '
                    <div class="price-wrapper">
                        <span class="original-price">₱' . number_format($originalPrice, 2) . '</span>
                        <span class="current-price">₱' . number_format($discountedPrice, 2) . '</span>
                    </div>';
    } else {
        $output .= '<span class="current-price">₱' . number_format($originalPrice, 2) . '</span>';
    }
                
    $output .= '
                </div>
                <button class="buy-btn" onclick="window.location.href=\'product.php?id=' . $product['id'] . '\'">View</button>
            </div>
        </div>
    </div>';
    
    return $output;
}
?>