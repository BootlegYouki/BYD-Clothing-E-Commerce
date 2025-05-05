<?php
require_once '../admin/config/dbcon.php';
require_once 'functions/productfetching/shop_product-handler.php';

// Get parameters from URL
$view_product_id = isset($_GET['view_product']) ? intval($_GET['view_product']) : 0;

// Clean up category filter to handle spaces correctly
$category_filter = isset($_GET['category']) ? str_replace('+', ' ', $_GET['category']) : '';

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 8;

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

$categories = getAllCategories($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Beyond Doubt Clothing</title> 
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/logo/BYD-removebg-preview.ico" type="image/x-icon">
    <!-- UTILITY CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/shop.css">
    <link rel="stylesheet" href="css/shopcart.css">
    <link rel="stylesheet" href="css/assistant.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <!-- REGISTER MODAL  -->
     <?php include 'includes/register.php'; ?>
    <!-- LOGIN MODAL  -->
    <?php include 'includes/login.php'; ?>
    <!-- LOGOUT MODAL  -->
    <?php include 'includes/logout.php'; ?>
    <!-- SUCCESS MODAL  -->
    <?php include 'includes/loginsuccess.php'; ?>
    <?php include 'includes/registersuccess.php'; ?>
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>
    <!-- SHOP CART -->
    <?php include 'includes/shopcart.php'; ?>   
    <!-- ASSISTANT  -->
    <?php include 'includes/assistant.php'; ?>
    
    <section id="label" class="my-5 py-2">
    <!-- Title Section -->
    <div class="container text-center mt-5 py-5">
    <?php if (!empty($category_filter)): ?>
        <h3><?= htmlspecialchars($category_filter) ?></h3>
        <hr class="body-hr mx-auto">
        <p>Discover stylish designs and unmatched comfort with our latest collection.</p>
    <?php else: ?>
        <h3>All Collections</h3>
        <hr class="body-hr mx-auto">
        <p>Discover stylish designs and unmatched comfort with our latest collection.</p>
    <?php endif; ?>
    </div>
    
    <!-- Filter and Sort Section -->
    <div class="container-fluid">
        <div class="label-container small-container py-3 px-4">
            <!-- Desktop View -->
            <div class="row align-items-center">
                <!-- Left side: Categories (desktop only) -->
                <div class="col-md-7 d-none d-md-block">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php foreach($categories as $category): ?>
                            <a href="javascript:void(0)" 
                               class="category-filter <?= $category_filter === $category ? 'active' : '' ?>"
                               data-category="<?= urlencode($category) ?>">
                                <?= htmlspecialchars($category) ?>
                            </a>
                        <?php endforeach; ?>
                        <a href="javascript:void(0)" class="clear-filter ms-2" id="clear-filters" style="<?= (!empty($category_filter) || !empty($search_query) || !empty($view_product_id)) ? '' : 'display: none;' ?>">
                            <i class="fa fa-times-circle me-1"></i>Clear
                        </a>
                    </div>
                </div>
                
                <!-- Right side: Sort and Count -->
                <div class="col-md-5 py-2">
                    <div class="d-flex justify-content-md-end justify-content-center align-items-center">
                        <!-- Sort dropdown -->
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            <label for="product-sort" class="form-label mb-0">Sort by:</label>
                            <select id="product-sort" class="form-select">
                                <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>Default</option>
                                <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
                                <option value="name-asc" <?= $sort === 'name-asc' ? 'selected' : '' ?>>Name: A to Z</option>
                                <option value="name-desc" <?= $sort === 'name-desc' ? 'selected' : '' ?>>Name: Z to A</option>
                                <option value="discount" <?= $sort === 'discount' ? 'selected' : '' ?>>Discount</option>
                            </select>
                            <span id="products-count" class="text-muted me-3 d-none d-md-block">
                            <?= $total_items ?> products
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Category Filter (collapsible) -->
            <div class="d-md-none mt-3">
                <!-- Mobile product count -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span id="mobile-products-count" class="text-muted"><?= $total_items ?> products</span>
                    <a href="javascript:void(0)" class="clear-filter btn btn-sm btn-outline-danger" id="clear-filters-mobile" style="<?= (!empty($category_filter) || !empty($search_query) || !empty($view_product_id)) ? '' : 'display: none;' ?>">
                        <i class="fa fa-times-circle me-1"></i>Clear Filter
                    </a>
                </div>
                
                <!-- Categories dropdown -->
                <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="collapse" data-bs-target="#categoryFilterMobile">
                    Categories <i class="fa fa-chevron-down ms-2"></i>
                </button>
                <div class="collapse" id="categoryFilterMobile">
                    <div class="card card-body mt-2">
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($categories as $category): ?>
                                <a href="javascript:void(0)" 
                                class="btn <?= $category_filter === $category ? 'btn-dark' : 'btn-outline-secondary' ?> btn-sm mobile-category-filter"
                                data-category="<?= urlencode($category) ?>">
                                    <?= htmlspecialchars($category) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="products">
<div class="Results">
    <div class="container-fluid px-md-5 px-2">
        <div class="row">
            <div class="col-12">
                <div id="filter-results-header" class="search-results-header my-3">
                <?php if (!empty($search_query)): ?>
                    <h4 class="mb-0">Search results for: "<?= htmlspecialchars($search_query) ?>"</h4>
                    
                    <?php if(empty($products)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa fa-info-circle me-2"></i>No products found matching your search.
                            <a href="shop.php" class="alert-link ms-2">View all products</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0 mt-2">Found <?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?> matching your search</p>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (!empty($category_filter) && empty($search_query)): ?>
                    <h4 class="mb-0">Category: "<?= htmlspecialchars($category_filter) ?>"</h4>
                    
                    <?php if(empty($products)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa fa-info-circle me-2"></i>No products found in this category.
                            <a href="shop.php" class="alert-link ms-2">View all products</a>
                        </div>
                    <?php else: ?>
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
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="container-fluid px-md-5 px-2">
        <div id="products-container">
        <?php if(empty($products)): ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <h4>No products found</h4>
                    <p>Try a different category or check back later for new arrivals.</p>
                </div>
            </div>
        <?php else: ?>
            <!-- Products for larger screens (hidden on small screens) -->
            <div class="d-none d-md-block">
                <?php 
                $productCount = 0;
                $productsPerRow = 4; // 4 products per row for better visual layout
                $totalProducts = count($products);
                
                // Loop through products in groups to create rows
                for ($i = 0; $i < $totalProducts; $i += $productsPerRow) { 
                    echo '<div class="row product-row">';
                    
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
        <?php endif; ?>
        </div>
        
        <!-- Pagination container -->
        <div id="pagination-container">
            <?php if ($total_pages > 1): ?>
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
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const viewProductId = urlParams.get('view_product');
    
    if (viewProductId) {
        // Redirect to product page if view_product parameter is present
        window.location.href = `product.php?id=${viewProductId}`;
    }
    
    // Store initial state for use with filters
    window.shopState = {
        category: '<?= addslashes($category_filter) ?>',
        search: '<?= addslashes($search_query) ?>',
        sort: '<?= addslashes($sort) ?>',
        page: <?= $page ?>,
        view_product_id: <?= $view_product_id ?>,
    };
    
    // Initialize clear filter buttons
    updateClearFilterButtons();
});
</script>
<!-- UTILITY SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<!-- SCRIPT -->
<script src="js/shop.js"></script>
<script src="js/url-cleaner.js"></script>
