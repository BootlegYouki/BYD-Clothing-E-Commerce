<?php
if (!isset($originalPrice)) {
    $originalPrice = 1200;
}

if (!isset($price)) {
    $price = 780;
}
$discount = round((($originalPrice - $price) / $originalPrice) * 100);
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
    <!-- ICONSCSS -->
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
    <!-- FAILED MODAL  -->
    <?php include 'includes/failedmodal.php'; ?>
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>
    <!-- SHOP CART -->
    <?php include 'includes/shopcart.php'; ?>
    <!-- HOME SECTION -->
    <section id="home">
      <div class="container-fluid px-3">
        <div class="small-container">
        <div class="row align-items-center">
          <!-- Left Column: Text -->
          <div class="col-md-6">
            <h4>New Arrival</h5>
            <h1>
                From casual hangouts to<span> High-energy moments.</span>
                <br> Versatility at its best.
            </h1>
            <p>Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.</p>
            <button class="btn-body">Shop Now</button>
            <div class="mt-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-grip-horizontal" viewBox="0 0 16 16">
                  <path d="M2 8a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                </svg>
              </div>
          </div>
          <!-- Right Column: Carousel -->
          <div class="col-md-6 mb-5">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" 
                        class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" 
                        aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" 
                        aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" 
                        aria-label="Slide 4"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" 
                        aria-label="Slide 5"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="5" 
                        aria-label="Slide 6"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="6" 
                        aria-label="Slide 7"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="7" 
                        aria-label="Slide 8"></button>
              </div>
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/carousel/1.jpg" class="d-block w-100" alt="Image 1" loading="lazy">
                </div>
                <div class="carousel-item" inert>
                  <img src="img/carousel/2.jpg" class="d-block w-100" alt="Image 2" loading="lazy">
                </div>
                <div class="carousel-item" inert>
                  <img src="img/carousel/3.jpg" class="d-block w-100" alt="Image 3" loading="lazy">
                </div>
                <div class="carousel-item" inert>
                  <img src="img/carousel/4.jpg" class="d-block w-100" alt="Image 4" loading="lazy">
                </div>
                <div class="carousel-item" inert>
                  <img src="img/carousel/5.jpg" class="d-block w-100" alt="Image 5" loading="lazy">
                </div>
                <div class="carousel-item" inert>
                  <img src="img/carousel/6.jpg" class="d-block w-100" alt="Image 6" loading="lazy">
                </div>
                <div class="carousel-item" inert>
                  <img src="img/carousel/7.jpg" class="d-block w-100" alt="Image 7" loading="lazy">
                </div>
                <div class="carousel-item" inert>
                  <img src="img/carousel/8.jpg" class="d-block w-100" alt="Image 8" loading="lazy">
                </div>
              </div>
              <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
              </a>
            </div>
          </div>
        </div>
        </div>
      </div>
    </section>
    <!-- FEATURED SECTION -->
    <section id="featured" class="my-5 pb-5">
        <div class="container text-center mt-5 py-5">
          <h3>Our featured</h3>
          <hr class="body-hr mx-auto">
          <p>Unleash the power of style with our Mecha Collection Moto Jerseys.</p>
        </div>
        <div class="row mx-auto container-fluid">
          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/gipsy.webp" alt="" loading="lazy">
            <div class="star">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
          </div>
            <h5 class="p-name">T-SHIRT - "GIPSY”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/megazord.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">T-SHIRT - "MEGAZORD</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/optimus.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">T-SHIRT - "OPTIMUS”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn ">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/primal.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">T-SHIRT - "PRIMAL”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
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
    <!-- t-shirt section -->
    <section id="t-shirt" class="my-5 pb-5">
        <div class="container text-center mt-5 py-5">
        <h3>T-Shirt Collection</h3>
        <hr class="body-hr mx-auto">
        <p>Discover stylish designs and unmatched comfort with our latest collection.</p>
        </div>
        <div class="row mx-auto container-fluid">
          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/sam.webp" alt="" loading="lazy">
            <div class="star">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
          </div>
            <h5 class="p-name">T-SHIRT - "SAM”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/vale.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">T-SHIRT - "VALE”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/brook.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">T-SHIRT - "BROOK”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn ">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/retain.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">T-SHIRT - "RETAIN”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
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
    <div class="row mx-auto container-fluid">
        <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/longsleeve/brook.webp" alt="BROOK" loading="lazy">
            <div class="star">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">LONGSLEEVE - "BROOK"</h5>
            <h4 class="p-price mb-4">
                <?php echo "₱" . $price; ?>
                <?php if (isset($discount) && $discount > 0): ?>
                    <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
                <?php endif; ?>
            </h4>
            <button class="buy-btn">BUY NOW</button>
        </div>

        <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/longsleeve/jap.webp" alt="JAP" loading="lazy">
            <div class="star">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">LONGSLEEVE - "JAP"</h5>
            <h4 class="p-price mb-4">
                <?php echo "₱" . $price; ?>
                <?php if (isset($discount) && $discount > 0): ?>
                    <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
                <?php endif; ?>
            </h4>
            <button class="buy-btn">BUY NOW</button>
        </div>

        <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/longsleeve/seud.webp" alt="SEUD" loading="lazy">
            <div class="star">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">LONGSLEEVE - "SEUD"</h5>
            <h4 class="p-price mb-4">
                <?php echo "₱" . $price; ?>
                <?php if (isset($discount) && $discount > 0): ?>
                    <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
                <?php endif; ?>
            </h4>
            <button class="buy-btn">BUY NOW</button>
        </div>

        <div class="product text-center col-lg-3 col-md-6 col-12">
            <img class="img-fluid img-11 mb-2" src="img/longsleeve/toyo.webp" alt="TOYO" loading="lazy">
            <div class="star">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">LONGSLEEVE - "TOYO"</h5>
            <h4 class="p-price mb-4">
                <?php echo "₱" . $price; ?>
                <?php if (isset($discount) && $discount > 0): ?>
                    <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
                <?php endif; ?>
            </h4>
            <button class="buy-btn">BUY NOW</button>
        </div>
    </div>
</section>
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- SCRIPT -->
    <script src="js/indexscript.js"></script>
    <script src="js/shopcart.js"></script>
</body>
</html>