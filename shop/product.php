<?php
require_once '../admin/config/dbcon.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    // Redirect to shop page if no valid product ID is provided
    header('Location: shop.php');
    exit;
}

// Get product details from database
$query = "SELECT p.*, pi.image_url AS primary_image 
          FROM products p
          LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
          WHERE p.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    // Product not found, redirect to shop
    header('Location: shop.php');
    exit;
}

$product = mysqli_fetch_assoc($result);

// Get additional product images
$images_query = "SELECT image_url FROM product_images WHERE product_id = ? AND is_primary = 0 ORDER BY id ASC";
$stmt = mysqli_prepare($conn, $images_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$images_result = mysqli_stmt_get_result($stmt);

$additional_images = [];
while ($image = mysqli_fetch_assoc($images_result)) {
    $additional_images[] = '../' . $image['image_url'];
}

// Get available sizes
$sizes_query = "SELECT size, stock FROM product_sizes WHERE product_id = ? ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL')";
$stmt = mysqli_prepare($conn, $sizes_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$sizes_result = mysqli_stmt_get_result($stmt);

$available_sizes = [];
while ($size = mysqli_fetch_assoc($sizes_result)) {
    $available_sizes[$size['size']] = intval($size['stock']);
}

// Get related products (from all categories, excluding current product)
$random_seed = mt_rand(); // Generate a random seed for each page load
$related_query = "SELECT p.id, p.name, p.category, p.original_price, p.discount_price, p.discount_percentage, pi.image_url 
                 FROM products p
                 LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                 WHERE p.id != ?
                 ORDER BY RAND($random_seed)
                 LIMIT 4";

$stmt = mysqli_prepare($conn, $related_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$related_result = mysqli_stmt_get_result($stmt);

$related_products = [];
while ($related = mysqli_fetch_assoc($related_result)) {
    $related_products[] = $related;
}

// Format product image URL
$primary_image = !empty($product['primary_image']) ? '../' . $product['primary_image'] : 'img/placeholder.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($product['name']) ?> | Beyond Doubt Clothing</title> 
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
    <link rel="stylesheet" href="css/shopcart.css">
    <link rel="stylesheet" href="css/assistant.css">
    <link rel="stylesheet" href="css/product.css">
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
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>
    <!-- SHOP CART -->
    <?php include 'includes/shopcart.php'; ?>   
    <!-- ASSISTANT  -->
    <?php include 'includes/assistant.php'; ?>
    
    <!-- Breadcrumb -->
     <div class="my-5 py-4"></div>
    <div class="container-fluid px-md-5 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item"><a href="shop.php?category=<?= urlencode($product['category']) ?>"><?= htmlspecialchars($product['category']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
    </div>
    
    <!-- Product Detail Section -->
    <section class="product-detail pt-3 pb-5">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6 mb-4 mb-lg-0 mt-2">
                    <div class="product-images">
                        <!-- Main product image -->
                        <div class="main-image-container" title="Click to zoom">
                            <img id="main-product-image" src="<?= $primary_image ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid">
                        </div>
                        
                        <!-- Thumbnail images -->
                        <?php if (!empty($additional_images) || !empty($primary_image)): ?>
                        <div class="thumbnail-navigation position-relative">
                            <div class="thumbnail-container d-flex justify-content-center">
                                <!-- Primary image thumbnail -->
                                <div class="thumbnail active" data-image="<?= $primary_image ?>">
                                    <img src="<?= $primary_image ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid">
                                </div>
                                
                                <!-- Additional thumbnails -->
                                <?php foreach($additional_images as $index => $image): ?>
                                <div class="thumbnail" data-image="<?= $image ?>">
                                    <img src="<?= $image ?>" alt="<?= htmlspecialchars($product['name']) ?> - Image <?= $index+2 ?>" class="img-fluid">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info px-3">
                        <h1 class="product-title mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                        <h5 class="product-category text-uppercase text-muted mb-3"><?= htmlspecialchars($product['category']) ?></h5>
                        
                        <?php 
                        // Check for an actual discount (price difference)
                        $has_discount = (isset($product['discount_price']) && 
                                        $product['discount_price'] > 0 && 
                                        $product['discount_price'] < $product['original_price']);
                        
                        // Only show original price if there's a real discount
                        if($has_discount): ?>
                            <span class="original-price">₱<?= number_format($product['original_price'], 2) ?></span>
                        <?php endif; ?>
                        <!-- Price display -->
                        <div class="price-container mb-4 align-items-center">
                            <?php if($has_discount): ?>
                                <div class="price-wrapper">
                                    <span class="current-price">₱<?= number_format($product['discount_price'], 2) ?></span>
                                </div>
                                <span class="discount-label">Save <?= $product['discount_percentage'] ?>%</span>
                            <?php else: ?>
                                <div class="price-wrapper">
                                    <span class="current-price">₱<?= number_format($product['original_price'], 2) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Product description -->
                        <label class="form-label fw-bold d-block">Description</label>
                        <div class="product-description mb-4">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                        
                        <form id="add-to-cart-form">
                            <!-- Size Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-2">Size</label>
                                <div class="size-buttons">
                                    <div class="row g-2" id="size-buttons-container">
                                        <?php if (!empty($available_sizes)): ?>
                                            <?php foreach($available_sizes as $size => $stock): ?>
                                                <?php $isOutOfStock = $stock <= 0; ?>
                                                <div class="col-auto">
                                                    <button type="button" 
                                                        class="btn-size w-50 <?= $isOutOfStock ? 'out-of-stock' : '' ?>" 
                                                        data-size="<?= $size ?>" 
                                                        data-stock="<?= $stock ?>" 
                                                        <?= $isOutOfStock ? 'disabled' : '' ?>>
                                                        <?= $size ?>
                                                        <?php if ($isOutOfStock): ?>
                                                        <span class="out-of-stock-label"></span>
                                                        <?php endif; ?>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <p class="text-muted">No size information available</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="hidden" id="selected-size" name="size" value="">
                                <div id="size-error" class="text-danger mt-2" style="display: none;">Please select a size</div>
                                <div id="stock-info" class="text-muted mt-2" style="display: none;"></div>
                            </div>
                            
                            <!-- Quantity Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-2">Quantity</label>
                                <div class="quantity-selector d-flex">
                                    <button type="button" class="btn-quantity minus" id="quantity-minus">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" id="quantity" name="quantity" value="1" min="1" max="10" readonly>
                                    <button type="button" class="btn-quantity plus" id="quantity-plus">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Add to Cart Button -->
                            <button type="button" id="add-to-cart-btn" class="add-to-cart-btn"
                                data-product-id="<?= $product_id ?>"
                                data-product-name="<?= htmlspecialchars($product['name']) ?>"
                                data-product-category="<?= htmlspecialchars($product['category']) ?>"
                                data-product-price="<?= $product['discount_price'] > 0 ? $product['discount_price'] : $product['original_price'] ?>"
                                data-product-original-price="<?= $product['original_price'] ?>"
                                data-product-image="<?= htmlspecialchars($primary_image) ?>">
                                ADD TO CART
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Related Products Section -->
    <?php if (!empty($related_products)): ?>
    <section class="related-products py-5">
        <div class="container">
            <h3 class="text-center mb-4">You May Also Like</h3>
            
            <!-- Products for larger screens (hidden on small screens) -->
            <div class="row d-none d-md-flex">
                <?php foreach($related_products as $related): 
                    $relatedImageUrl = !empty($related['image_url']) ? '../' . $related['image_url'] : 'img/placeholder.jpg';
                    $discountPrice = $related['discount_price'] > 0 ? $related['discount_price'] : 0;
                    $originalPrice = $related['original_price'];
                    $discountPercentage = $related['discount_percentage'];
                ?>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="product-card">
                        <div class="product-img-container">
                            <img class="product-img mb-3" src="<?= $relatedImageUrl ?>" alt="<?= htmlspecialchars($related['name']) ?>">
                            <?php if($discountPrice > 0): ?>
                                <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info text-center">
                            <h5 class="text-uppercase mb-2"><?= htmlspecialchars($related['category']) ?> - "<?= htmlspecialchars($related['name']) ?>"</h5>
                            <div class="price-container mb-3 justify-content-center">
                                <?php if($discountPrice > 0): ?>
                                    <div class="price-wrapper-related">
                                        <span class="original-price-related">₱<?= number_format($originalPrice, 2) ?></span>
                                        <span class="current-price-related">₱<?= number_format($discountPrice, 2) ?></span>
                                    </div>
                                <?php else: ?>
                                    <h5 class="current-price-related">₱<?= number_format($originalPrice, 2) ?></h5>
                                <?php endif; ?>
                            </div>
                            <button class="buy-btn" onclick="window.location.href='product.php?id=<?= $related['id'] ?>'">View</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Swiper for mobile view (hidden on larger screens) -->
            <div class="swiper-container related-products-swiper d-block d-md-none">
                <div class="swiper-wrapper">
                    <?php foreach($related_products as $related): 
                        $relatedImageUrl = !empty($related['image_url']) ? '../' . $related['image_url'] : 'img/placeholder.jpg';
                        $discountPrice = $related['discount_price'] > 0 ? $related['discount_price'] : 0;
                        $originalPrice = $related['original_price'];
                        $discountPercentage = $related['discount_percentage'];
                    ?>
                    <div class="swiper-slide">
                        <div class="product-card">
                            <div class="product-img-container">
                                <img class="product-img mb-3" src="<?= $relatedImageUrl ?>" alt="<?= htmlspecialchars($related['name']) ?>">
                                <?php if($discountPrice > 0): ?>
                                    <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info text-center">
                                <h5 class="text-uppercase mb-2"><?= htmlspecialchars($related['category']) ?> - "<?= htmlspecialchars($related['name']) ?>"</h5>
                                <div class="price-container mb-3 justify-content-center">
                                    <?php if($discountPrice > 0): ?>
                                        <div class="price-wrapper-related">
                                            <span class="original-price-related">₱<?= number_format($originalPrice, 2) ?></span>
                                            <span class="current-price-related">₱<?= number_format($discountPrice, 2) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <h5 class="current-price-related">₱<?= number_format($originalPrice, 2) ?></h5>
                                    <?php endif; ?>
                                </div>
                                <button class="buy-btn" onclick="window.location.href='product.php?id=<?= $related['id'] ?>'">View</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Toast notification for cart -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <!-- Success toast -->
        <div id="cartAddedToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fa fa-check-circle text-success me-2"></i>
                <strong class="me-auto">Added to Cart</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                        <div class="toast-product-image me-3">
                            <img id="toast-product-image" src="" alt="Product Image" class="img-fluid rounded">
                        </div>
                        <div>
                            <p class="mb-1 fw-bold" id="toast-product-name"></p>
                            <p class="mb-0 small text-muted text-uppercase" id="toast-product-category"></p>
                            <p class="mt-1 mb-0 text-muted">Size: <span id="toast-product-size"></span> | Qty: <span id="toast-product-quantity"></span></p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ms-3" id="view-cart-btn" data-bs-dismiss="toast">View Cart</button>
                </div>
            </div>
        </div>
        
        <!-- Error toast - for maximum quantity reached -->
        <div id="cartErrorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fa fa-exclamation-circle text-danger me-2"></i>
                <strong class="me-auto">Maximum Quantity Reached</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div class="alert alert-danger py-2 px-3 mb-2">
                    You already have the maximum available quantity in your cart.
                </div>
            </div>
        </div>
    </div>
    
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- UTILITY SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    
    <!-- SCRIPT -->
    <script src="js/url-cleaner.js"></script>
    <script src="js/cart.js"></script>
    <script src="js/product.js"></script>
</body>
</html>
