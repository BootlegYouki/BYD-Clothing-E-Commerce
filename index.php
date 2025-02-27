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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'navbar.php'; ?>
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
                    <img src="https://scontent.fmnl30-1.fna.fbcdn.net/v/t39.30808-6/475698566_483764308125437_5942151304390714485_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeHnmq2u0DDEbxy2aY4Mc7TpUHKyGi-eGn5QcrIaL54afltjCAHAIR92g05SNyHfNJP1cinlUMkQbYe62WUjjYoG&_nc_ohc=4KFxsex8TEUQ7kNvgHLYl1-&_nc_oc=Adh4zt4K84NjVfAF-64Sgzj9DiC8cGizVVLqSYIm4isoDCUmxTN6wsC94Nms1vwedkc&_nc_zt=23&_nc_ht=scontent.fmnl30-1.fna&_nc_gid=ADgZ_ljGG1B3vgNxo3hkiH1&oh=00_AYD4lng4fvh1MCWJZj2dC4m0cmUUZw17KdoFHBVGxe1zTQ&oe=67BCC5C3" class="d-block w-100" alt="Image 1">
                  </div>
                  <!-- Original Image 2 -->
                  <div class="carousel-item">
                    <img src="https://scontent.fmnl30-3.fna.fbcdn.net/v/t39.30808-6/475302706_483764301458771_210396009555274866_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeEGAw8-fn-8xWdDniGKUa43m_bHQn_acZib9sdCf9pxmBoLLVPpIgnag86W-znD9oVjNNEVmQ923AoCuvgAzDub&_nc_ohc=grH39s-ptVkQ7kNvgGUtmnj&_nc_oc=AdhWbUR5C2jakhJrEPxdYxhLe5ReYKBrfbWv2CPaYBqw_7qx37m9y5lerRgb8vOLfU8&_nc_zt=23&_nc_ht=scontent.fmnl30-3.fna&_nc_gid=AOyf-aaRhIODQaS43SETmSt&oh=00_AYDfPmURNZJK1e0wtdI3CvhBlGvCY59O45NY2tyzerGQAQ&oe=67BCB8BC" class="d-block w-100" alt="Image 2">
                  </div>
                  <!-- Original Image 3 -->
                  <div class="carousel-item">
                    <img src="https://scontent.fmnl30-1.fna.fbcdn.net/v/t39.30808-6/475444267_483764304792104_5358398187843921615_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeFvGzitZbMmZ3MAzw0Zf5dxixQLiYxKV4KLFAuJjEpXgtr-s674lK7o-k2ymbfNggHCqBCd4kLqenqJ9ZnGGF9u&_nc_ohc=Ub7ecCstK7wQ7kNvgHtsGbL&_nc_oc=AdhiLc-3iwjPz7ukWcfR3DWBwTF1s9oszSJRHBoJCLXWkF4Hvu8gl_UM6Wmfid4V-Gk&_nc_zt=23&_nc_ht=scontent.fmnl30-1.fna&_nc_gid=AV4Nj1QY8vnGewsFs5VUCcp&oh=00_AYDlYbDpiatzDO3wMKDsLiPdkMrkOZ3OegdNNW4xSv0xyQ&oe=67BCC28F" class="d-block w-100" alt="Image 3">
                  </div>
                  <!-- Additional Image 4 -->
                  <div class="carousel-item">
                    <img src="https://scontent.fmnl30-2.fna.fbcdn.net/v/t39.30808-6/475298484_483764414792093_6376600474285872157_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeGIfxWfLE6WV7RlvKaJDJl1v-Mc591mGze_4xzn3WYbN1jRletJlZv4COuNmDKS7Hca4qtEjE_IRBUPaWFjich7&_nc_ohc=YSYJKsodiu8Q7kNvgGpANFt&_nc_oc=AdgB3hhcZFyCcI9PEU2VmbQZqgNqRc7CMjkne6Oqtr412fz39RnGYmS_WiJerwaTIqs&_nc_zt=23&_nc_ht=scontent.fmnl30-2.fna&_nc_gid=AwVcZKa72KDd_eGpHGDxL5w&oh=00_AYCLRGPgJnLEgKunGHuLPHCSCH0AjUB4guzy4X4ER2wuTw&oe=67BCA518" class="d-block w-100" alt="Image 4">
                  </div>
                  <!-- Additional Image 5 -->
                  <div class="carousel-item">
                    <img src="https://scontent.fmnl30-3.fna.fbcdn.net/v/t39.30808-6/475518370_483764411458760_5304720965699593668_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeG9wBL1yanrJHN6t_kAFDjNsg3UxHXugXuyDdTEde6Be4k4sa4Moej2Vp558gstyPL02IZkmfHRVJ9j2ialxtRp&_nc_ohc=kV_0W659Df8Q7kNvgGuzdIw&_nc_oc=AdhzUKSF3R9Vv3HzmIbod5ElBRyxCeTN1y9uAgmvPIWcXlJU6qYYXgR8Nd79yO_eLSs&_nc_zt=23&_nc_ht=scontent.fmnl30-3.fna&_nc_gid=Af56mrTm2Bb3Zy30akibSOa&oh=00_AYAoLuDs1W6PqaLQUhEZTtNrrZOesXHAa3z0kFaaeYjK_A&oe=67BC9829" class="d-block w-100" alt="Image 5">
                  </div>
                  <!-- Additional Image 6 -->
                  <div class="carousel-item">
                    <img src="https://scontent.fmnl30-3.fna.fbcdn.net/v/t39.30808-6/475649001_483764298125438_8848100704551623914_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeGpEMcDMyrU044bAyqb5cbTMk3TfML9l0EyTdN8wv2XQep35w4b5UnhujNdHlF1ySDDbZSbPcJ8m2SNh4xqE0_I&_nc_ohc=JZcNnNqktmwQ7kNvgF3qsjx&_nc_oc=AdgndKTSpDOkozVzlg73l8GwpI05IgkI5n-PpFU9CZnDZcXPFMwwmFa31ipnQRb6Kaw&_nc_zt=23&_nc_ht=scontent.fmnl30-3.fna&_nc_gid=ADriIbSlxVHb-72nxoKTeVq&oh=00_AYCZXFQlzJhbi_bGfEE7OyrpBIC1USua_b8enr00RYHfhg&oe=67BCC1FB" class="d-block w-100" alt="Image 6">
                  </div>
                  <!-- Additional Image 7 -->
                  <div class="carousel-item">
                    <img src="https://scontent.fmnl30-2.fna.fbcdn.net/v/t39.30808-6/475288312_483764294792105_9163152908469585123_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeFN_gv9rI-d41IBgoPvJeEezElxIDPuutXMSXEgM-661X5lyomSI48SYr1kO8pSiJ40nBnojUV40D_q4QFOdJbj&_nc_ohc=qNoiP1ASqB4Q7kNvgHgay3h&_nc_oc=AdhVUP4LLMH_GaSJH76r46iy5r7jcz2vUByg20-h65hG_tUhCk1tjm7-1ZfLR8nk8YU&_nc_zt=23&_nc_ht=scontent.fmnl30-2.fna&_nc_gid=AGrJSv2oNZBASr1IX1X8MsX&oh=00_AYDUWCbYZsG4xB7JRwuFgolPyQUUS_dMSL0Ku_Wrq_-1nw&oe=67BCA661" class="d-block w-100" alt="Image 7">
                  </div>
                  <!-- Additional Image 8 -->
                  <div class="carousel-item">
                    <img src="https://scontent.fmnl30-2.fna.fbcdn.net/v/t39.30808-6/475321729_483764271458774_7802751305663810429_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=127cfc&_nc_eui2=AeE9qu0T2MzW3RrvYhtBqkKHMuG6_wzgS7cy4br_DOBLtyZ2JHU0wXtiWk_U6_D7iPbHh_utm3J_k79Jo4dv31bY&_nc_ohc=t9eTb1fjjjYQ7kNvgEI2zqH&_nc_oc=Adj8tYHe1u0iLRTbEYeCZiUUaNPZVIFgrE18c8cDW47r6Ou5LKOjU5IewXCAex517gs&_nc_zt=23&_nc_ht=scontent.fmnl30-2.fna&_nc_gid=AgbSS2ahp_0CaG2rg2EATsG&oh=00_AYCDelGYBXUoKi7BNam5XTHWy-B5QdOG5Qa4tQwnEYZQkg&oe=67BCA358" class="d-block w-100" alt="Image 8">
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
            <img class="img-fluid img-11 mb-2" src="img/featured/gipsy.webp" alt="">
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
            <img class="img-fluid img-11 mb-2" src="img/featured/megazord.webp" alt="">
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
            <img class="img-fluid img-11 mb-2" src="img/featured/optimus.webp" alt="">
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
            <img class="img-fluid img-11 mb-2" src="img/featured/primal.webp" alt="">
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
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/sam.webp" alt="">
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
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/vale.webp" alt="">
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
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/brook.webp" alt="">
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
            <img class="img-fluid img-11 mb-2" src="img/t-shirt/retain.webp" alt="">
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
    <?php include 'footer.php'; ?>
    <!-- BOOTSTRAP JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="script.js"></script>
</body>
</html>