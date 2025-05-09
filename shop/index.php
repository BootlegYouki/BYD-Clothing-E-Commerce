<?php
// At the top of your file after requiring dbcon.php
require_once '../admin/config/dbcon.php';
require_once 'functions/productfetching/index_product-handler.php'; // Move this line here

// Get homepage settings
$settings = [];
$query = "SELECT * FROM homepage_settings";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Helper function to get setting with fallback
function get_setting($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? $settings[$key] : $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <title>Beyond Doubt Clothing</title>
    <meta name="description" content="Beyond Doubt Clothing offers premium streetwear apparel that blends style, comfort, and identity. Shop urban fashion made to stand out.">
    
    <!-- Structured Data for Google Site Name -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Beyond Doubt Clothing",
      "url": "https://byd-clothing.com",
      "logo": "https://byd-clothing.com/logo/logo_admin_light.png"
    }
    </script>

    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Favicon -->
    <link rel="icon" href="img/logo/logo.ico" type="image/x-icon">

    <!-- UTILITY CSS  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    
    <!-- ICONS CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/indexstyle.css">
    <link rel="stylesheet" href="css/shopcart.css">
    <link rel="stylesheet" href="css/assistant.css">
</head>

<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <!-- CHATBOT  -->
    <?php include 'includes/assistant.php'; ?>
    <!-- SHOPPING CART MODAL  -->
    <?php include 'includes/shopcart.php'; ?>
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
    <!-- PAYMENT MODALS -->
    <?php include 'includes/payment_modals.php'; ?>
<!-- HOME SECTION -->
<section id="home">
  <div class="container-fluid py-3 pb-5 px-lg-0 px-5">
    <div class="small-container">
      <div class="row align-items-center">
        <!-- Left Column: Text -->
        <div class="col-md-6">
          <h4><?= get_setting('hero_tagline', 'New Arrival') ?></h4>
          <h1><?= get_setting('hero_heading', 'From casual hangouts to<span> High-energy moments.</span><br> Versatility at its best.') ?></h1>
          <p><?= get_setting('hero_description', 'Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.') ?></p>
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
<?php if(get_setting('show_new_release', '1') === '1'): ?>
  <section id="newrelease" class="my-5 pb-5">
      <div class="container text-center mt-5 py-5">
          <h3><?= get_setting('new_release_title', 'New Release') ?></h3>
          <hr class="body-hr mx-auto">
          <p><?= get_setting('new_release_description', 'Unleash the power of style with our Mecha Collection Moto Jerseys.') ?></p>
      </div>
      
      <div class="container-fluid px-md-5 px-2">
          <!-- Products for larger screens (hidden on small screens) -->
          <div class="row justify-content-center d-none d-md-flex">
              <?php
              // Get new release products from database
              $products = getIndexProducts($conn, [
                  'is_new_release' => true,
                  'limit' => 4
              ]);
              
              if(!empty($products)) {
                  foreach($products as $product) {
                      echo renderProductCard($product);
                  }
              } else {
                  echo '<div class="col-12 text-center"><p>No new release products available.</p></div>';
              }
              ?>
          </div>
          
          <!-- Swiper for mobile view (hidden on larger screens) -->
          <div class="swiper-container new-release-swiper d-block d-md-none">
              <div class="swiper-wrapper">
                  <?php
                  if (!empty($products)) {
                      foreach ($products as $product) {
                          echo renderProductCard($product, true);
                      }
                  } else {
                      echo '<div class="swiper-slide text-center"><p>No new release products available.</p></div>';
                  }
                  ?>
              </div>
              <div class="swiper-pagination"></div>
          </div>
      </div>
  </section>
<?php endif; ?>
<!-- BANNER -->
<section id="banner">
  <div class="container px-5">
    <h1><?= get_setting('banner_title', '<span>CUSTOM</span> SUBLIMATION<br>SERVICE') ?></h1>
    <p><?= get_setting('banner_description', 'We offer fully customized sublimation services:') ?></p>
    <ul class="list-unstyled">
      <?php 
      $list_items = explode("\n", get_setting('banner_list', 'T-shirt'));
      foreach($list_items as $item): 
        $item = trim($item);
        if(!empty($item)):
      ?>
        <li><h4><?= $item ?></h4></li>
      <?php 
        endif;
      endforeach; 
      ?>
    </ul>
    <button class="btn-body" onclick="window.location.href='aboutus.php'">Learn More</button>
  </div>
</section>
<!-- T-SHIRT SECTION  -->
<?php if(get_setting('show_tshirt', '1') === '1'): ?>
  <section id="t-shirt" class="my-5 pb-5">
      <div class="container text-center mt-5 py-5">
          <h3><?= get_setting('tshirt_title', 'T-Shirt Collection') ?></h3>
          <hr class="body-hr mx-auto">
          <p><?= get_setting('tshirt_description', 'Discover stylish designs and unmatched comfort with our latest collection.') ?></p>
      </div>
      <div class="container-fluid px-md-5 px-2">
          <!-- Products for larger screens (hidden on small screens) -->
          <div class="row justify-content-center d-none d-md-flex">
              <?php
              $tshirtProducts = getIndexProducts($conn, [
                  'category' => 'T-Shirt',
                  'is_featured' => true,
                  'limit' => 4
              ]);
              
              if(!empty($tshirtProducts)) {
                  foreach($tshirtProducts as $product) {
                      echo renderProductCard($product);
                  }
              } else {
                  echo '<div class="col-12 text-center"><p>No t-shirt products available.</p></div>';
              }
              ?>
          </div>
          
          <!-- Swiper for mobile view (hidden on larger screens) -->
          <div class="swiper-container t-shirt-swiper d-block d-md-none">
              <div class="swiper-wrapper">
                  <?php
                  if (!empty($tshirtProducts)) {
                      foreach ($tshirtProducts as $product) {
                          echo renderProductCard($product, true);
                      }
                  } else {
                      echo '<div class="swiper-slide text-center"><p>No t-shirt products available.</p></div>';
                  }
                  ?>
              </div>
              <div class="swiper-pagination"></div>
          </div>
      </div>
  </section>
<?php endif; ?>
<!-- Long Sleeve Section -->
<?php if(get_setting('show_longsleeve', '1') === '1'): ?>
  <section id="longsleeve" class="my-5 pb-5">
      <div class="container text-center mt-5 py-5">
          <h3><?= get_setting('longsleeve_title', 'Long Sleeve Collection') ?></h3>
          <hr class="body-hr mx-auto">
          <p><?= get_setting('longsleeve_description', 'Our Aircool Riders Jersey is built for everyday rides—lightweight, breathable, and made for ultimate performance.') ?></p>
      </div>
      <div class="container-fluid px-md-5 px-2">
          <!-- Products for larger screens (hidden on small screens) -->
          <div class="row justify-content-center d-none d-md-flex">
              <?php
              $longsleeveProducts = getIndexProducts($conn, [
                  'category' => 'Long Sleeve',
                  'is_featured' => true,
                  'limit' => 4
              ]);
              
              if(!empty($longsleeveProducts)) {
                  foreach($longsleeveProducts as $product) {
                      echo renderProductCard($product);
                  }
              } else {
                  echo '<div class="col-12 text-center"><p>No long sleeve products available.</p></div>';
              }
              ?>
          </div>
          
          <!-- Swiper for mobile view (hidden on larger screens) -->
          <div class="swiper-container longsleeve-swiper d-block d-md-none">
              <div class="swiper-wrapper">
                  <?php
                  if (!empty($longsleeveProducts)) {
                      foreach ($longsleeveProducts as $product) {
                          echo renderProductCard($product, true);
                      }
                  } else {
                      echo '<div class="swiper-slide text-center"><p>No long sleeve products available.</p></div>';
                  }
                  ?>
              </div>
              <div class="swiper-pagination"></div>
          </div>
      </div>
  </section>
<?php endif; ?>
<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- UTILITY SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- SCRIPT -->
<script src="js/indexscript.js"></script>
<script src="js/url-cleaner.js"></script>
</body>
</html>