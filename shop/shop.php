<?php
require_once '../admin/config/dbcon.php';

$items_per_page = 8;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

// Get category filter from URL if present
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Get sorting parameter from URL if present
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Build base query
$base_query = "SELECT p.*, pi.image_url as image
               FROM products p
               LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
               WHERE 1=1";

// Add category filter if specified
if (!empty($category_filter)) {
    $category_filter = mysqli_real_escape_string($conn, $category_filter);
    $base_query .= " AND p.category = '$category_filter'";
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
                $available_sizes[$size['size']] = intval($size['stock']);  // Convert stock to integer
            }
        }
        
        // Debug output to see sizes in PHP
        echo "<!-- Debug: Product {$product['id']} sizes: " . json_encode($available_sizes) . " -->";
        
        $formatted_product['availableSizes'] = $available_sizes;
        $products[] = $formatted_product;
    }
}

// Get all categories for filter menu
$category_query = "SELECT DISTINCT category FROM products ORDER BY category ASC";
$category_result = mysqli_query($conn, $category_query);
$categories = [];

if ($category_result && mysqli_num_rows($category_result) > 0) {
    while ($row = mysqli_fetch_assoc($category_result)) {
        $categories[] = $row['category'];
    }
}
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
        <h3><?= !empty($category_filter) ? htmlspecialchars($category_filter) : 'All Collections' ?></h3>
        <hr class="body-hr mx-auto">
        <p>Discover stylish designs and unmatched comfort with our latest collection.</p>
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
                            <a href="?category=<?= urlencode($category) ?>" 
                               class="category-filter <?= $category_filter === $category ? 'active' : '' ?>">
                                <?= htmlspecialchars($category) ?>
                            </a>
                        <?php endforeach; ?>
                        <?php if (!empty($category_filter)): ?>
                            <a href="shop.php" class="clear-filter ms-2">
                                <i class="fa fa-times-circle me-1"></i>Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right side: Sort and Count -->
                <div class="col-md-5">
                    <div class="d-flex justify-content-md-end justify-content-center align-items-center">
                        <!-- Sort dropdown -->
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            <label for="product-sort" class="form-label mb-0">Sort by:</label>
                            <select id="product-sort" class="form-select" 
                                    onchange="window.location.href = updateQueryStringParameter(window.location.href, 'sort', this.value)">
                                <option value="default" <?= $sort === 'default' ? 'selected' : '' ?>>Featured</option>
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
                    <?php if (!empty($category_filter)): ?>
                        <a href="shop.php" class="clear-filter btn btn-sm btn-outline-danger">
                            <i class="fa fa-times-circle me-1"></i>Clear Filter
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Categories dropdown -->
                <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="collapse" data-bs-target="#categoryFilterMobile">
                    Categories <i class="fa fa-chevron-down ms-2"></i>
                </button>
                <div class="collapse" id="categoryFilterMobile">
                    <div class="card card-body mt-2">
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($categories as $category): ?>
                                <a href="?category=<?= urlencode($category) ?>" 
                                   class="btn <?= $category_filter === $category ? 'btn-dark' : 'btn-outline-secondary' ?> btn-sm">
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
    <div class="container-fluid px-md-5 px-2">
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
                    echo '<div class="row justify-content-center product-row">';
                    
                    // Add up to 4 products per row
                    for ($j = $i; $j < min($i + $productsPerRow, $totalProducts); $j++) {
                        $product = $products[$j];
                        // Calculate discounted price if discount_percentage exists
                        $originalPrice = $product['price'];
                        $discountPercentage = $product['discount_percentage'];
                        $discountedPrice = $originalPrice;
                        
                        if($discountPercentage > 0) {
                            $discountedPrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));
                        }
                        ?>
                        <div class="product text-center col-lg-3 col-md-6 col-12 mb-4">
                            <div class="product-card">
                                <div class="product-img-container">
                                    <img class="product-img mb-3" src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>" loading="lazy">
                                    <?php if($discountPercentage > 0): ?>
                                        <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h5 class="text-uppercase mb-2"><?= $product['category'] ?> - "<?= $product['title'] ?>"</h5>
                                    <div class="price-container mb-3">
                                        <?php if($discountPercentage > 0): ?>
                                            <div class="price-wrapper">
                                                <span class="original-price">₱<?= number_format($originalPrice, 2) ?></span>
                                                <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="current-price">₱<?= number_format($originalPrice, 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="buy-btn" data-bs-toggle="collapse" data-bs-target="#productQuickView" 
                                    data-row-index="<?= floor($j / $productsPerRow) ?>" 
                                    onclick="showQuickView(<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>, event)">View</button>
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
                        // Calculate discounted price if discount_percentage exists
                        $originalPrice = $product['price'];
                        $discountPercentage = $product['discount_percentage'];
                        $discountedPrice = $originalPrice;
                        
                        if($discountPercentage > 0) {
                            $discountedPrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));
                        }
                    ?>
                    <div class="swiper-slide">
                        <div class="product-card">
                        <div class="product-img-container">
                            <img class="product-img mb-3" src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>" loading="lazy">
                            <?php if($discountPercentage > 0): ?>
                                <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                            <?php endif; ?>
                        </div>
                            <div class="product-info">
                                <h5 class="text-uppercase mb-2 text-center"><?= $product['category'] ?> - "<?= $product['title'] ?>"</h5>
                                <div class="price-container mb-3">
                                    <?php if($discountPercentage > 0): ?>
                                        <div class="price-wrapper">
                                            <span class="original-price">₱<?= number_format($originalPrice, 2) ?></span>
                                            <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="current-price">₱<?= number_format($originalPrice, 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="buy-btn" data-bs-toggle="collapse" data-bs-target="#productQuickView" 
                                onclick="showQuickView(<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>, event)">View</button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- PRODUCT QUICK VIEW COLLAPSE -->
<div class="container px-md-5 pt-sm-3 px-4 mb-5 w-90 w-md-100">
    <div class="collapse" id="productQuickView">
        <div class="card quick-view-card">
            <div class="card-body p-4">
                <div class="row">
                    <!-- Image Column -->
                    <div class="col-md-6 mb-4 mb-md-0">
                        <!-- Main product image -->
                        <div class="main-image-container mb-3">
                            <img src="" alt="Product image" class="quick-view-img img-fluid" loading="lazy">
                        </div>
                        
                        <!-- Thumbnail images -->
                        <div class="thumbnail-navigation position-relative">
                            <div class="thumbnail-container d-flex justify-content-center">
                                <!-- These will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Info Column -->
                    <div class="col-md-6">
                        <div class="product-info">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h3 class="quick-view-title m-0 text-uppercase"></h3>
                                <button type="button" class="btn-close ms-2" data-bs-toggle="collapse" data-bs-target="#productQuickView" aria-label="Close"></button>
                            </div>
                            
                            <!-- Star ratings -->
                            <!-- <div class="star-rating mb-2">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-half-o"></i>
                                <span class="rating-count">(24 reviews)</span>
                            </div> -->
                            
                            <h5 class="quick-view-category text-uppercase mb-3"></h5>
                            <p class="quick-view-sku text-muted mb-2"></p>
                            
                            <div class="quick-view-description mb-4">
                                <!-- Description will be populated by JavaScript -->
                            </div>
                            
                            <div class="quick-view-price-container mb-4">
                                <!-- Price container - will be populated by JavaScript -->
                            </div>
                            
                            <div class="mb-4">
                                <!-- Size Selection -->
                                <label class="form-label fw-bold d-block mb-2">Size</label>
                                <div class="size-buttons mb-4">
                                    <div class="row g-2" id="size-buttons-container">
                                        <!-- Will be populated dynamically by JavaScript -->
                                    </div>
                                </div>
                                <input type="hidden" id="quick-view-size" value="">
                                <div id="size-error" class="text-danger mb-2" style="display: none;">Please select a size</div>
                                
                                <!-- Quantity Selection -->
                                <label class="form-label fw-bold d-block mb-2">Quantity</label>
                                <div class="quantity-selector d-flex mb-4">
                                    <button type="button" class="btn-quantity minus" id="quick-view-quantity-minus">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" id="quick-view-quantity" value="1" min="1" max="10" readonly>
                                    <button type="button" class="btn-quantity plus" id="quick-view-quantity-plus">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <p id="stock-info" class="text-muted"></p>
                            </div>
                            
                            <!-- Action Button - Only Add to Cart -->
                            <button id="quick-view-add-to-cart" class="add-to-cart-btn w-100">ADD TO CART</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- PAGINATION -->
<?php if ($total_pages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
        
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- UTILITY SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<!-- SCRIPT -->
<script>
function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return uri + separator + key + "=" + value;
    }
}
</script>
<script src="js/shop.js"></script>
<script src="js/shopcart.js"></script>
<script src="js/url-cleaner.js"></script>
<script src="js/assistant.js"></script>
