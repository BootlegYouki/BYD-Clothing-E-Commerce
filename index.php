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
    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="icon" href="img/logo/BYD-removebg-preview.ico" type="image/x-icon">
    <!-- ICONSCSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=account_circle,close,menu,person,search,shopping_bag" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/indexstyle.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/navbar.php'; ?>
    <!-- HOME SECTION -->
    <section id="home">
      <div class="container-fluid px-3">
        <div class="small-container">
        <div class="row align-items-center">
          <!-- Left Column: Text -->
          <div class="col-md-6 mb-5">
            <h4>New Arrival</h5>
            <h1>
                From casual hangouts to<span> High-energy moments.</span>
                <br> Versatility at its best.
            </h1>
            <p>Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.</p>
            <button>Shop Now</button>
          </div>
          <!-- Right Column: Carousel -->
          <div class="col-md-6 pl-md-4">
              <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="6"></li>
                  <li data-target="#carouselExampleIndicators" data-slide-to="7"></li>
                </ol>
                <div class="carousel-inner">
                            <!-- Original Image 1 -->
                            <div class="carousel-item active">
                                <img src="img/carousel/1.jpg" class="d-block w-100" alt="Image 1" loading="lazy">
                            </div>
                            <!-- Original Image 2 -->
                            <div class="carousel-item">
                                <img src="img/carousel/2.jpg" class="d-block w-100" alt="Image 2" loading="lazy">
                            </div>
                            <!-- Original Image 3 -->
                            <div class="carousel-item">
                                <img src="img/carousel/3.jpg" class="d-block w-100" alt="Image 3" loading="lazy">
                            </div>
                            <!-- Additional Image 4 -->
                            <div class="carousel-item">
                                <img src="img/carousel/4.jpg" class="d-block w-100" alt="Image 4" loading="lazy">
                            </div>
                            <!-- Additional Image 5 -->
                            <div class="carousel-item">
                                <img src="img/carousel/5.jpg" class="d-block w-100" alt="Image 5" loading="lazy">
                            </div>
                            <!-- Additional Image 6 -->
                            <div class="carousel-item">
                                <img src="img/carousel/6.jpg" class="d-block w-100" alt="Image 6" loading="lazy">
                            </div>
                            <!-- Additional Image 7 -->
                            <div class="carousel-item">
                                <img src="img/carousel/7.jpg" class="d-block w-100" alt="Image 7" loading="lazy">
                            </div>
                            <!-- Additional Image 8 -->
                            <div class="carousel-item">
                                <img src="img/carousel/8.jpg" class="d-block w-100" alt="Image 8" loading="lazy">
                            </div>
                        </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
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
          <hr class="mx-auto">
          <p>Explore our new collection and experience premium quality at prices you'll love.</p>
        </div>
        <div class="row mx-auto container-fluid">
          <div class="product text-center col-lg-3 col-md-4 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/gipsy.webp" alt="" loading="lazy">
            <div class="star">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
          </div>
            <h5 class="p-name">LONG-SLEEVES - "GIPSY”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-4 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/megazord.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">LONG-SLEEVES - "MEGAZORD</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-4 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/optimus.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">LONG-SLEEVES - "OPTIMUS”</h5>
            <h4 class="p-price mb-4">
              <?php echo "₱" . $price; ?>
              <?php if (isset($discount) && $discount > 0): ?>
                <span class="discount"><?php echo "-" . $discount . "%"; ?></span>
              <?php endif; ?>
            </h4>
            <button class="buy-btn ">Buy now</button>
          </div>

          <div class="product text-center col-lg-3 col-md-4 col-12">
            <img class="img-fluid img-11 mb-2" src="img/featured/primal.webp" alt="" loading="lazy">
            <div class="star">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
            </div>
            <h5 class="p-name">LONG-SLEEVES - "PRIMAL”</h5>
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
        <button class="text-uppercase mt-5">Learn More</button>
      </div>
    </section>
    <!-- t-shirt section -->
    <section id="featured" class="my-5 pb-5">
        <div class="container text-center mt-5 py-5">
        <h3>T-Shirt Collection</h3>
        <hr class="mx-auto">
        <p>Discover stylish designs and unmatched comfort with our latest collection.</p>
        </div>
        <div class="row mx-auto container-fluid">
          <div class="product text-center col-lg-3 col-md-4 col-12">
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

          <div class="product text-center col-lg-3 col-md-4 col-12">
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

          <div class="product text-center col-lg-3 col-md-4 col-12">
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

          <div class="product text-center col-lg-3 col-md-4 col-12">
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
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <!-- BOOTSTRAP JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="js/indexscript.js"></script>
</body>
</html>