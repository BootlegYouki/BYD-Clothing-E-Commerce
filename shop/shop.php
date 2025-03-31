<?php
// Product data array
$products = [
    [
        'id' => 1,
        'image' => 'img/featured/gipsy.webp',
        'title' => 'BYD "MEYSA" DESIGN - DRIFIT',
        'price' => 399.00,
        'category' => 'Shirts',
        'discount_percentage' => 0,
        'additionalImages' => [
            'img/carousel/1.jpg',
            'img/carousel/2.jpg',
            'img/carousel/3.jpg'
        ]
    ],
    [
        'id' => 2,
        'image' => 'img/featured/gipsy.webp',
        'title' => 'BYD "ATHENA" DESIGN - DRIFIT',
        'price' => 499.00,
        'category' => 'Shirts',
        'discount_percentage' => 10
    ],
    [
        'id' => 3,
        'image' => 'img/featured/gipsy.webp',
        'title' => 'BYD "EROS" DESIGN - DRIFIT',
        'price' => 399.00,
        'category' => 'Shirts',
        'discount_percentage' => 0
    ],
    [
        'id' => 4,
        'image' => 'img/featured/gipsy.webp',
        'title' => 'BYD "GAVIN" DESIGN - DRIFIT',
        'price' => 399.00,
        'category' => 'Shirts',
        'discount_percentage' => 15
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    
<section id="label" class="my-5 py-2">
    <div class="container text-center mt-5 py-5">
        <h3>All Collections</h3>
        <hr class="body-hr mx-auto">
        <p>Discover stylish designs and unmatched comfort with our latest collection.</p>
    </div>
</section>

<!-- PRODUCTS -->
<section id="products">
    <div class="container-fluid px-md-5 px-2">
        <!-- Products for larger screens (hidden on small screens) -->
        <div class="row justify-content-center d-none d-md-flex">
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
            <div class="product text-center col-lg-3 col-md-6 col-12 mb-4">
                <div class="product-card">
                    <a href="product-detail.php?id=<?= $product['id'] ?>" class="product-img-container">
                        <img class="product-img mb-3" src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>" loading="lazy">
                        <?php if($discountPercentage > 0): ?>
                            <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                        <?php endif; ?>
                    </a>
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
                        onclick="showQuickView(<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>)">View</button>
                    </div>
                </div>
            </div>
            <?php } ?>
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
                        <a href="product-detail.php?id=<?= $product['id'] ?>" class="product-img-container">
                            <img class="img-fluid mb-3" src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>" loading="lazy">
                            <?php if($discountPercentage > 0): ?>
                                <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                            <?php endif; ?>
                        </a>
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
                            onclick="showQuickView(<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>)">View</button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- PRODUCT QUICK VIEW COLLAPSE -->
<div class="container px-md-5 px-2 mb-5 w-75 w-md-100">
    <div class="collapse" id="productQuickView">
        <div class="card quick-view-card">
            <div class="card-body p-4">
                <!-- First row: Close button -->
                <div class="row mb-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#productQuickView" aria-label="Close"></button>
                    </div>
                </div>
                <!-- Second row: Product content -->
                <div class="row g-4">
                    <!-- Product Image Column -->
                    <div class="col-md-6">
                        <!-- Main product image -->
                        <div class="main-image-container mb-3 w-lg-50 w-md-100 w-sm-100">
                            <img src="" alt="Product image" class="quick-view-img img-fluid" loading="lazy">
                        </div>
                        
                        <!-- Thumbnail images with navigation arrows -->
                        <div class="thumbnail-navigation position-relative">
                            <div class="thumbnail-container d-flex justify-content-center">
                                <!-- These will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Info Column -->
                    <div class="col-md-6">
                        <div class="product-info">
                            <h3 class="quick-view-title mb-2"></h3>
                            
                            <!-- Star ratings -->
                            <div class="star-rating mb-2">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-half-o"></i>
                                <span class="rating-count">(24 reviews)</span>
                            </div>
                            
                            <h5 class="quick-view-category text-uppercase mb-3"></h5>
                            
                            <div class="quick-view-price-container mb-4">
                                <!-- Price container - will be populated by JavaScript -->
                            </div>
                            
                            <div class="mb-4">
                                <!-- Size Selection -->
                                <label class="form-label fw-bold d-block mb-2">Size</label>
                                <div class="size-buttons mb-4">
                                    <div class="row g-2">
                                        <div class="col-auto">
                                            <button type="button" class="btn-size" data-size="S">S</button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn-size" data-size="M">M</button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn-size" data-size="L">L</button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn-size" data-size="XL">XL</button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn-size" data-size="XXL">XXL</button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="quick-view-size" value="M">
                                
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
                            </div>
                            
                            <!-- Action Button - Only Add to Cart -->
                            <button id="quick-view-add-to-cart" class="add-to-cart-btn w-lg-50 w-md-50 w-sm-100">ADD TO CART</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PAGINATION -->
<nav aria-label="Page navigation example">
    <ul class="pagination">
        <li class="page-item">
            <a class="page-link" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
            <a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- UTILITY SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- SCRIPT -->
<script src="js/shop.js"></script>
<script src="js/shopcart.js"></script>
<script src="js/url-cleaner.js"></script>
<script>
    // Initialize Swiper
    document.addEventListener('DOMContentLoaded', function() {
        const shopSwiper = new Swiper('.shop-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                }
            }
        });
    });
</script>
</body>
</html>