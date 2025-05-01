<?php
// Include the required files
require_once '../../../admin/config/dbcon.php';
require_once 'shop_product-handler.php';

// Get parameters from request
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'filter_products') {
    try {
        $view_product_id = isset($_POST['view_product_id']) ? intval($_POST['view_product_id']) : 0;
        
        // Make sure to clean up category to handle spaces correctly
        $category_filter = isset($_POST['category']) ? str_replace('+', ' ', $_POST['category']) : '';
        
        $search_query = isset($_POST['search']) ? trim($_POST['search']) : '';
        $sort = isset($_POST['sort']) ? $_POST['sort'] : 'default';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $items_per_page = 8;

        // Debug info
        $debug_info = [
            'category' => $category_filter,
            'search' => $search_query,
            'sort' => $sort,
            'page' => $page
        ];

        // Get products using the same function as the main page
        $result = getShopProducts($conn, [
            'view_product_id' => $view_product_id,
            'category' => $category_filter,
            'search' => $search_query,
            'sort' => $sort,
            'page' => $page,
            'items_per_page' => $items_per_page
        ]);

        $products = $result['products'];
        $total_items = $result['total_items'];
        $total_pages = $result['total_pages'];

        // Get all categories
        $categories = getAllCategories($conn);

        // Start output buffer to capture products HTML
        ob_start();

        if (empty($products)) {
            ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <h4>No products found</h4>
                    <p>Try a different category or check back later for new arrivals.</p>
                </div>
            </div>
            <?php
        } else {
            // Products for larger screens (hidden on small screens)
            ?>
            <div class="d-none d-md-block">
                <?php 
                $productCount = 0;
                $productsPerRow = 4; // 4 products per row for better visual layout
                $totalProducts = count($products);
                
                // Loop through products in groups to create rows
                for ($i = 0; $i < $totalProducts; $i += $productsPerRow) { 
                    echo '<div class="row justify-content-center product-row">';
                    
                    // Add up to 4 products per row
                    for ($j = $i; $j < min($i + $productsPerRow, $totalProducts); $j++) {
                        $product = $products[$j];
                        // Use discount_price if available
                        $originalPrice = $product['price'];
                        $discountPercentage = $product['discount_percentage'];
                        $discountPrice = isset($product['discount_price']) && $product['discount_price'] > 0 ? $product['discount_price'] : 0;
                        
                        // Calculate discount percentage for display if not provided
                        if($discountPrice > 0 && $discountPercentage <= 0) {
                            $discountPercentage = round(($originalPrice - $discountPrice) / $originalPrice * 100);
                        }
                        ?>
                        <div class="product text-center col-lg-3 col-md-6 col-12 mb-4">
                            <div class="product-card" data-product-id="<?= $product['id'] ?>">
                                <div class="product-img-container">
                                    <img class="product-img mb-3" src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>" loading="lazy">
                                    <?php if($discountPrice > 0): ?>
                                        <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h5 class="text-uppercase mb-2"><?= $product['category'] ?> - "<?= $product['title'] ?>"</h5>
                                    <div class="price-container mb-3">
                                        <?php if($discountPrice > 0): ?>
                                            <div class="price-wrapper">
                                                <span class="original-price">₱<?= number_format($originalPrice, 2) ?></span>
                                                <span class="current-price">₱<?= number_format($discountPrice, 2) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="current-price">₱<?= number_format($originalPrice, 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="buy-btn" onclick="viewProduct(<?= $product['id'] ?>)">View</button>
                                </div>
                            </div>
                        </div>
                    <?php }
                    
                    echo '</div>'; // Close the row
                }
                ?>
            </div>
            
            <!-- Swiper for mobile view (hidden on larger screens) -->
            <div class="swiper-container shop-swiper d-block d-md-none">
                <div class="swiper-wrapper">
                    <?php 
                    foreach($products as $product) { 
                        // Use discount_price if available
                        $originalPrice = $product['price'];
                        $discountPrice = isset($product['discount_price']) && $product['discount_price'] > 0 ? $product['discount_price'] : 0;
                        $discountPercentage = $product['discount_percentage'];
                        
                        // Calculate discount percentage for display if not provided
                        if($discountPrice > 0 && $discountPercentage <= 0) {
                            $discountPercentage = round(($originalPrice - $discountPrice) / $originalPrice * 100);
                        }
                    ?>
                    <div class="swiper-slide">
                        <div class="product-card" data-product-id="<?= $product['id'] ?>">
                        <div class="product-img-container">
                            <img class="product-img mb-3" src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>" loading="lazy">
                            <?php if($discountPrice > 0): ?>
                                <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                            <?php endif; ?>
                        </div>
                            <div class="product-info">
                                <h5 class="text-uppercase mb-2 text-center"><?= $product['category'] ?> - "<?= $product['title'] ?>"</h5>
                                <div class="price-container mb-3">
                                    <?php if($discountPrice > 0): ?>
                                        <div class="price-wrapper">
                                            <span class="original-price">₱<?= number_format($originalPrice, 2) ?></span>
                                            <span class="current-price">₱<?= number_format($discountPrice, 2) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="current-price">₱<?= number_format($originalPrice, 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="buy-btn" onclick="viewProduct(<?= $product['id'] ?>)">View</button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <?php
        }

        $products_html = ob_get_clean();

        // Generate pagination HTML
        ob_start();
        if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="javascript:void(0)" data-page="<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif;
        $pagination_html = ob_get_clean();
        
        // Generate header HTML
        ob_start();
        if (!empty($search_query)): ?>
            <h4 class="mb-0">Search results for: "<?= htmlspecialchars($search_query) ?>"</h4>
            
            <?php if(empty($products)): ?>
                <div class="alert alert-info mt-3">
                    <i class="fa fa-info-circle me-2"></i>No products found matching your search.
                    <a href="javascript:void(0)" class="alert-link ms-2" id="view-all-products">View all products</a>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0 mt-2">Found <?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?> matching your search</p>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!empty($category_filter) && empty($search_query)): ?>
            <!-- Display the category name with spaces instead of plus signs -->
            <h4 class="mb-0">Category: "<?= htmlspecialchars($category_filter) ?>"</h4>
            
            <?php if(empty($products)): ?>
                <div class="alert alert-info mt-3">
                    <i class="fa fa-info-circle me-2"></i>No products found in this category.
                    <a href="javascript:void(0)" class="alert-link ms-2" id="view-all-products">View all products</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($sort !== 'default' && empty($search_query) && empty($category_filter)): ?>
            <h4 class="mb-0">
                Sorted by: 
                <?php 
                    switch ($sort) {
                        case 'price-asc': echo 'Price: Low to High'; break;
                        case 'price-desc': echo 'Price: High to Low'; break;
                        case 'name-asc': echo 'Name: A to Z'; break;
                        case 'name-desc': echo 'Name: Z to A'; break;
                        case 'discount': echo 'Best Discount'; break;
                    }
                ?>
            </h4>
        <?php endif;
        $header_html = ob_get_clean();
        
        // Generate clear filter button HTML
        $show_clear_buttons = !empty($category_filter) || !empty($search_query) || $view_product_id > 0;

        // Send the JSON response
        echo json_encode([
            'success' => true,
            'products_html' => $products_html,
            'pagination_html' => $pagination_html,
            'header_html' => $header_html,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'debug' => $debug_info,
            'show_clear_buttons' => $show_clear_buttons
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'debug' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
