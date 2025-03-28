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
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CUSTOM CSS/JS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/indexstyle.css">
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
    <!-- HOME SECTION -->
<!-- HOME SECTION -->
<section id="home">
  <div class="container-fluid px-3 pb-5">
    <div class="small-container">
      <div class="row align-items-center">
        <!-- Left Column: Text -->
        <div class="col-md-6">
          <h4>New Arrival</h4>
          <h1>
            From casual hangouts to<span> High-energy moments.</span>
            <br> Versatility at its best.
          </h1>
          <p>Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.</p>
          <button class="btn-body" onclick="window.location.href='shop.php'">Shop Now</button>
          <div class="mt-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-grip-horizontal" viewBox="0 0 16 16">
              <path d="M2 8a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </svg>
          </div>
        </div>
        
        <!-- Right Column: Carousel -->
        <div class="col-md-6 mb-5">
          <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <?php
            // Get active carousel images
            require_once '../admin/config/dbcon.php';
            
            // Verify database connection
            if (!$conn) {
              echo '<div class="alert alert-danger">Database connection failed. Please try again later.</div>';
            } else {
              // Query for active carousel images
              $query = "SELECT * FROM carousel_images WHERE is_active = 1 ORDER BY id DESC";
              $result = mysqli_query($conn, $query);
              
              if (!$result) {
                echo '<div class="alert alert-danger">Error retrieving carousel images: ' . mysqli_error($conn) . '</div>';
              } else {
                // Count how many images we have
                $slide_count = mysqli_num_rows($result);
                
                if ($slide_count > 0) {
                  // Get all images for the carousel
                  $images = [];
                  while ($row = mysqli_fetch_assoc($result)) {
                    $images[] = $row;
                  }
            ?>
            <div class="carousel-indicators">
              <?php for ($i = 0; $i < count($images); $i++) { ?>
                <button type="button" data-bs-target="#carouselExampleIndicators" 
                      data-bs-slide-to="<?= $i ?>" 
                      <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?> 
                      aria-label="Slide <?= $i + 1 ?>"></button>
              <?php } ?>
            </div>
            <div class="carousel-inner">
              <?php 
                foreach ($images as $index => $image) { 
                  $imagePath = $image['image_path'];
                  if (strpos($imagePath, 'uploads/') === 0) {
                    $imagePath = '../' . $imagePath;
                  }
              ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <img src="<?= $imagePath ?>" class="d-block w-100" alt="Carousel Image">
                </div>
              <?php } ?>
            </div>
            <?php if (count($images) > 1) { ?>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            <?php } ?>
            <?php 
                } else { 
            ?>
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/placeholder.jpg" class="d-block w-100" alt="BYD Clothing" loading="lazy">
                  <div class="carousel-caption d-none d-md-block">
                    <h5>Welcome to BYD Clothing</h5>
                    <p>Premium quality sportswear and casual apparel</p>
                  </div>
                </div>
              </div>
            <?php 
                }
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- NEW RELEASE SECTION -->
<section id="newrelease" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>New Release</h3>
        <hr class="body-hr mx-auto">
        <p>Unleash the power of style with our Mecha Collection Moto Jerseys.</p>
    </div>
    <div class="container-fluid px-5">
        <div class="row justify-content-center">
            <?php
            require_once '../admin/config/dbcon.php';
            
            // Get new release products from database
            $query = "SELECT p.*, pi.image_url 
                      FROM products p
                      LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                      WHERE p.is_new_release = 1
                      ORDER BY p.id DESC
                      LIMIT 4";
                      
            $result = mysqli_query($conn, $query);
            
            if(mysqli_num_rows($result) > 0) {
                while($product = mysqli_fetch_assoc($result)) {
                    // Calculate discounted price if discount_percentage exists
                    $originalPrice = $product['original_price'];
                    $discountPercentage = $product['discount_percentage'];
                    $discountedPrice = $originalPrice;
                    
                    if($discountPercentage > 0) {
                        $discountedPrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));
                    }
                    
                    // Image URL with fallback to placeholder if not available
                    $imageUrl = !empty($product['image_url']) ? '../' . $product['image_url'] : 'img/placeholder.jpg';
            ?>
                <div class="product text-center col-lg-3 col-md-6 col-12 mb-4">
                    <div class="product-card">
                        <a href="product-detail.php?id=<?= $product['id'] ?>" class="product-img-container">
                            <img class="img-fluid product-img mb-3" src="<?= $imageUrl ?>" alt="<?= $product['name'] ?>" loading="lazy">
                            <?php if($discountPercentage > 0): ?>
                                <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                            <?php endif; ?>
                        </a>
                        <div class="product-info">
                            <div class="star mb-2">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <h5 class="text-uppercase mb-2"><?= $product['category'] ?> - "<?= $product['name'] ?>"</h5>
                            <div class="price-container mb-3">
                                <?php if($discountPercentage > 0): ?>
                                    <div class="price-wrapper">
                                        <span class="original-price">₱<?= number_format($originalPrice, 2) ?></span>
                                        <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="buy-btn" onclick="window.location.href='product-detail.php?id=<?= $product['id'] ?>'">Buy now</button>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center"><p>No new release products available.</p></div>';
            }
            ?>
        </div>
    </div>
</section>
<!-- Banner -->
    <section id="banner">
      <div class="container px-5">
        <h1><span>CUSTOM</span> SUBLIMATION<br>SERVICE</h1>
        <p>We offer fully customized sublimation services:</p>
        <ul class="list-unstyled">
          <li><h4>T-shirt</li>
          <li><h4>Polo Shirt</li>
          <li><h4>Basketball</li>
          <li><h4>Jersey</li>
          <li><h4>Long Sleeves</li>
        </ul>
        <button class="btn-body">Learn More</button>
      </div>
    </section>
<!-- T-SHIRT SECTION  -->
<section id="t-shirt" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>T-Shirt Collection</h3>
        <hr class="body-hr mx-auto">
        <p>Discover stylish designs and unmatched comfort with our latest collection.</p>
    </div>
    <div class="container-fluid px-5">
        <div class="row justify-content-center">
            <?php
            require_once '../admin/config/dbcon.php';
            
            $category = 'T-Shirt';
            $query = "SELECT p.*, pi.image_url 
                      FROM products p
                      LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                      WHERE p.is_featured = 1 AND p.category = '$category'
                      ORDER BY p.id DESC
                      LIMIT 4";
                      
            $result = mysqli_query($conn, $query);
            
            if(mysqli_num_rows($result) > 0) {
                while($product = mysqli_fetch_assoc($result)) {
                    // Calculate discounted price if discount_percentage exists
                    $originalPrice = $product['original_price'];
                    $discountPercentage = $product['discount_percentage'];
                    $discountedPrice = $originalPrice;
                    
                    if($discountPercentage > 0) {
                        $discountedPrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));
                    }
                    
                    // Image URL with fallback to placeholder if not available
                    $imageUrl = !empty($product['image_url']) ? '../' . $product['image_url'] : 'img/placeholder.jpg';
            ?>
                <div class="product text-center col-lg-3 col-md-6 col-12 mb-4">
                    <div class="product-card">
                        <a href="product-detail.php?id=<?= $product['id'] ?>" class="product-img-container">
                            <img class="img-fluid product-img mb-3" src="<?= $imageUrl ?>" alt="<?= $product['name'] ?>" loading="lazy">
                            <?php if($discountPercentage > 0): ?>
                                <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                            <?php endif; ?>
                        </a>
                        <div class="product-info">
                            <div class="star mb-2">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <h5 class="text-uppercase mb-2"><?= $product['category'] ?> - "<?= $product['name'] ?>"</h5>
                            <div class="price-container mb-3">
                                <?php if($discountPercentage > 0): ?>
                                    <div class="price-wrapper">
                                        <span class="original-price">₱<?= number_format($originalPrice, 2) ?></span>
                                        <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="buy-btn" onclick="window.location.href='product-detail.php?id=<?= $product['id'] ?>'">Buy now</button>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center"><p>No new release products available.</p></div>';
            }
            ?>
        </div>
    </div>
</section>
    <!-- Long Sleeve Section -->
    <section id="longsleeve" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>Long Sleeve Collection</h3>
        <hr class="body-hr mx-auto">
        <p>Our Aircool Riders Jersey is built for everyday rides—lightweight, breathable, and made for ultimate performance.</p>
    </div>
    <div class="container-fluid px-5">
        <div class="row justify-content-center">
            <?php
            require_once '../admin/config/dbcon.php';
            
            $category = 'Long Sleeve';
            $query = "SELECT p.*, pi.image_url 
                      FROM products p
                      LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                      WHERE p.is_featured = 1 AND p.category = '$category'
                      ORDER BY p.id DESC
                      LIMIT 4";
                      
            $result = mysqli_query($conn, $query);
            
            if(mysqli_num_rows($result) > 0) {
                while($product = mysqli_fetch_assoc($result)) {
                    // Calculate discounted price if discount_percentage exists
                    $originalPrice = $product['original_price'];
                    $discountPercentage = $product['discount_percentage'];
                    $discountedPrice = $originalPrice;
                    
                    if($discountPercentage > 0) {
                        $discountedPrice = $originalPrice - ($originalPrice * ($discountPercentage / 100));
                    }
                    
                    // Image URL with fallback to placeholder if not available
                    $imageUrl = !empty($product['image_url']) ? '../' . $product['image_url'] : 'img/placeholder.jpg';
            ?>
                <div class="product text-center col-lg-3 col-md-6 col-12 mb-4">
                    <div class="product-card">
                        <a href="product-detail.php?id=<?= $product['id'] ?>" class="product-img-container">
                            <img class="img-fluid product-img mb-3" src="<?= $imageUrl ?>" alt="<?= $product['name'] ?>" loading="lazy">
                            <?php if($discountPercentage > 0): ?>
                                <span class="discount-badge">-<?= $discountPercentage ?>%</span>
                            <?php endif; ?>
                        </a>
                        <div class="product-info">
                            <div class="star mb-2">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <h5 class="text-uppercase mb-2"><?= $product['category'] ?> - "<?= $product['name'] ?>"</h5>
                            <div class="price-container mb-3">
                                <?php if($discountPercentage > 0): ?>
                                    <div class="price-wrapper">
                                        <span class="original-price">₱<?= number_format($originalPrice, 2) ?></span>
                                        <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="current-price">₱<?= number_format($discountedPrice, 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="buy-btn" onclick="window.location.href='product-detail.php?id=<?= $product['id'] ?>'">Buy now</button>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center"><p>No new release products available.</p></div>';
            }
            ?>
        </div>
    </div>
</section>
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- SCRIPT -->
    <script src="js/shopcart.js"></script>
    <script src="js/indexscript.js"></script>
    <script src="js/url-cleaner.js"></script>
</body>
</html>