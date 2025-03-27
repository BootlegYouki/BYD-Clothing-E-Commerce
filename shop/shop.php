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
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/indexstyle.css">
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

    <!-- BANNER -->
    <section class="top-image-container">
        <img src="img/logo/Banner.png" alt="Banner" class="top-image">
      </section>      

    <!-- LABEL -->
    <section id="label" class="my-5 py-2">
        <div class="container-fluid">
            <div class="small-container mt-1 py-1 d-flex justify-content-between align-items-center">
                <h3>All Collections</h3>
                <div class="d-flex align-items-center gap-3">
                    <p class="mb-0">Sort by:</p>
                    <select class="form-select w-auto">
                        <option>All</option>
                        <option>Price</option>
                        <option>Sale</option>
                        <option>Popularity</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

<!-- PRODUCTS -->
<section id="products">
    <div class="small-container">
        <div class="product">
            <a href="details.php" id="sht1">
                <img src="img/shirt/shirt7.png" alt="shirt" loading="lazy">
            </a>
            <h4>BYD "EROS" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview" 
                    data-bs-toggle="modal" 
                    data-bs-target="#productModal"
                    data-img="img/shirt/shirt7.png"
                    data-title="BYD &quot;EROS&quot; DESIGN - DRIFIT"
                    data-price="₱399.00">
                View
            </button>
        </div>

        <div class="product">
          <a href="details.php" id="sht2">
            <img src="img/shirt/shirt2.png" alt="shirt" loading="lazy">
            </a>
            <h4>BYD "GAVIN" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview" 
                    data-bs-toggle="modal" 
                    data-bs-target="#productModal"
                    data-img="img/shirt/shirt2.png"
                    data-title="BYD &quot;GAVIN&quot; DESIGN - DRIFIT"
                    data-price="₱399.00">
                View
            </button>
        </div>

        <div class="product">
          <a href ="details.php" id="sht3">
          <img src="img/shirt/shirt3.png" alt="shirt">
          </a>

          <h4>BYD "GINO" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/shirt/shirt3.png"
          data-title="BYD &quot;GINO&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <a href="details.php">
          <img src="img/shirt/shirt4.png" alt="shirt">
          </a>
          <h4>BYD "BRAD" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/shirt/shirt4.png"
          data-title="BYD &quot;BRAD&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <a href="details.php">
          <img src="img/shirt/shirt5.png" alt="shirt">
          </a>
          <h4>BYD "ARON" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/shirt/shirt5.png"
          data-title="BYD &quot;ARON&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <a href="details.php">
          <img src="img/shirt/shirt6.png" alt="shirt">
          </a>
          <h4>BYD "MEDI" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/shirt/shirt6.png"
          data-title="BYD &quot;MEDI&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <a href="details.php">
          <img src="img/shirt/shirt1.png" alt="shirt">
          </a>
          <h4>BYD "MEYSA" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/shirt/shirt1.png"
          data-title="BYD &quot;MEYSA&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <a href="details.php">
          <img src="img/shirt/shirt8.png" alt="shirt">
          </a>
          <h4>BYD "INFERNO" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/shirt/shirt8.png"
          data-title="BYD &quot;INFERNO&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <!-- can add other product entries ... -->
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/shopscript.js"></script>
    <script src="js/indexscript.js"></script>
    <script src="js/shopcart.js"></script> 
</body>
</html>