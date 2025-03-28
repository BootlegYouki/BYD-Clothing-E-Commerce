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
            <!-- SWIPPER CSS -->
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
    <link rel="stylesheet" href="css/new_arrival.css">
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


    <!-- BANNER -->
    <section class="top-image-container">
        <img src="img/logo/Banner.png" alt="Banner" class="top-image">
      </section>      

    <!-- LABEL -->
    <section id="label" class="my-5 py-2">
        <div class="container-fluid">
          <div class="selection justify-content-center">
          <button class="flt-1">All Products</button>
          <button class="flt-1">T-Shirts Collections</button>
          <button class="flt-1">First Released - Long Sleeve Collections</button>
          <button class="flt-1">Mecha - Long Sleeve Collection</button>
          <br>
          </div>
          <h3 class="text-center mt-2">New Arrivals</h3>
          <hr class="body-hr mx-auto">
    </section>

<!-- PRODUCTS -->
<section id="new-arrivals" class="my-5 py-2">
  <div class="container">
    <div class="row"> <!-- Row to arrange products in a grid -->

      <!-- Product 1 -->
      <div class="col-md-4 mb-4">
        <div class="product-card">
          <div class="product-image">
            <div id="carousel1" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/longsleeve/brook-ls.jpg" class="d-block w-100" alt="Image 1">
                </div>
                <div class="carousel-item">
                  <img src="img/longsleeve/gul-ls.jpg" class="d-block w-100" alt="Image 2">
                </div>
                <div class="carousel-item">
                  <img src="img/longsleeve/leve-ls.jpg" class="d-block w-100" alt="Image 3">
                </div>
              </div>
              <a class="carousel-control-prev" href="#carousel1" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </a>
              <a class="carousel-control-next" href="#carousel1" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </a>
            </div>
            <button class="buy-now">BUY NOW</button> <!-- Buy Now Button -->
          </div>
          <div class="product-info">
            <h4>LONG-SLEEVES "JAP” AIRCOOL Jersey</h4>
            <p>₱780</p>
          </div>
        </div>
      </div>

      <!-- Product 2 -->
      <div class="col-md-4 mb-4">
        <div class="product-card">
          <div class="product-image">
            <div id="carousel2" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/new_arrival/jap_2.png" class="d-block w-100" alt="Image 1">
                </div>
                <div class="carousel-item">
                  <img src="img/new_arrival/jap_3.png" class="d-block w-100" alt="Image 2">
                </div>
                <div class="carousel-item">
                  <img src="img/new_arrival/jap_4.png" class="d-block w-100" alt="Image 3">
                </div>
              </div>
              <a class="carousel-control-prev" href="#carousel2" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </a>
              <a class="carousel-control-next" href="#carousel2" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </a>
            </div>
            <button class="buy-now">BUY NOW</button> <!-- Buy Now Button -->
          </div>
          <div class="product-info">
            <h4>LONG-SLEEVES "JAP” AIRCOOL Jersey</h4>
            <p>₱780</p>
          </div>
        </div>
      </div>

       <!-- Product 3 -->
       <div class="col-md-4 mb-4">
        <div class="product-card">
          <div class="product-image">
            <div id="carousel1" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="img/new_arrival/jap_1.png" class="d-block w-100" alt="Image 1">
                </div>
                <div class="carousel-item">
                  <img src="img/new_arrival/jap_2.png" class="d-block w-100" alt="Image 2">
                </div>
                <div class="carousel-item">
                  <img src="img/new_arrival/jap_3.png" class="d-block w-100" alt="Image 3">
                </div>
              </div>
              <a class="carousel-control-prev" href="#carousel1" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </a>
              <a class="carousel-control-next" href="#carousel1" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </a>
            </div>
            <button class="buy-now">BUY NOW</button> <!-- Buy Now Button -->
          </div>
          <div class="product-info">
            <h4>LONG-SLEEVES "JAP” AIRCOOL Jersey</h4>
            <p>₱780</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center gap-4 p-4">
                <img src="" alt="product image" class="img-fluid product-img" id="modalProductImage">
                <div class="product-details">
                    <h2 class="product-title" id="modalProductTitle"></h2>
                    <p class="product-price" id="modalProductPrice"></p>
                    <div class="mb-3">
                        <label for="size" class="form-label">Size</label>
                        <select id="size" class="form-select">
                          <option value="S">Small</option>
                          <option value="M">Medium</option>
                          <option value="L">Large</option>
                        </select>
                    </div>
          
                    <div class="mb-4">
                      <label for="quantity" class="form-label">Quantity</label>
                      <select id="quantity" class="form-select quantity-selector"> 
                          <option>1</option>
                          <option>2</option>
                          <option>3</option>
                          <option>4</option>
                          <option>5</option>
                      </select>
                    </div>
          
                    <button type="button" class="btn btn-dark add-to-cart-btn">Add to Cart</button>

                    <!-- can add rest of modal content ... -->
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
    <!--SCRIPT-->
    <script src="js/new_arrival.js"></script>
    <script src="js/indexscript.js"></script>
    <script src="js/shopcart.js"></script>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- BOOTSTRAP JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>