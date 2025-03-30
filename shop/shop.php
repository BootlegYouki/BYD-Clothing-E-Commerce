<?php
// Product data array
$products = [
    [
        'id' => 1,
        'image' => 'img/shirt/shirt1.png',
        'title' => 'BYD "MEYSA" DESIGN - DRIFIT',
        'price' => 399.00,
        'category' => 'Shirts'
    ],
    [
        'id' => 2,
        'image' => 'img/shirt/shirt2.png',
        'title' => 'BYD "ATHENA" DESIGN - DRIFIT',
        'price' => 499.00,
        'category' => 'Shirts'
    ],
    [
        'id' => 3,
        'image' => 'img/shirt/shirt3.png',
        'title' => 'BYD "EROS" DESIGN - DRIFIT',
        'price' => 399.00,
        'category' => 'Shirts'
    ],
    [
        'id' => 3,
        'image' => 'img/shirt/shirt4.png',
        'title' => 'BYD "GAVIN" DESIGN - DRIFIT',
        'price' => 399.00,
        'category' => 'Shirts'
    ],
    // Add more products as needed
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

    <!-- BANNER -->
    <section class="top-image-container">
        <img src="img/logo/Banner.png" alt="Banner" class="top-image">
      </section>      

    <!-- LABEL -->
    <section id="label" class="my-5 py-2">
        <div class="container-fluid">
            <div class="label-container small-container mt-1 py-1 d-flex justify-content-between align-items-center">
                <h3>All Collections</h3>
                <div class="d-flex align-items-center gap-3">
                    <p class="custom-label mb-0">Sort by:</p>
                    <select class="custom-form form-s w-auto">
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
        <div class="products-container">
            <?php foreach($products as $product) { ?>
                <div class="product" data-product-id="<?php echo $product['id']; ?>">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>" class="product-img">
                    <h4 class="product-title"><?php echo $product['title']; ?></h4>
                    <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
                    <button class="quickview" 
                            onclick="toggleProductDetails(this)"
                            data-img="<?php echo $product['image']; ?>"
                            data-title="<?php echo htmlspecialchars($product['title'], ENT_QUOTES); ?>"
                            data-price="₱<?php echo number_format($product['price'], 2); ?>"
                            data-id="<?php echo $product['id']; ?>">
                        VIEW
                    </button>
                </div>
            <?php } ?>
        </div>
        
       <!-- Modified Product Details with Slide Down Animation -->
       <div class="product-details collapse" id="productDetails" data-product-id="" data-product-image="" data-product-title="" data-product-price="" tabindex="-1" role="dialog" aria-labelledby="productDetailsTitle">
           <div class="details-content">
               <div class="card border-0 animate-slide-down">
                   <div class="card-body p-4">
                       <div class="row g-4">
                           <!-- Product Image Column -->
                           <div class="col-md-6 text-center">
                               <img src="" alt="product image" class="detail-img img-fluid mb-4" style="max-height: 500px; width: auto;">
                           </div>
                           
                           <!-- Product Info Column -->
                           <div class="col-md-6 position-relative">
                               <div class="d-flex flex-column h-100">
                                   <!-- Header -->
                                   <div class="d-flex justify-content-between w-100">
                                       <div class="product-info">
                                           <h2 class="detail-title fw-bold mb-1" id="productDetailsTitle"></h2>
                                           <p class="detail-price"></p>
                                       </div>
                                       <button type="button" class="btn-close custom-close" onclick="closeProductDetails()" aria-label="Close"></button>
                                   </div>

                                   <!-- Product Options -->
                                       <div class="product-options row g-3">
                                           <div class="col-12">
                                               <label for="size" class="form-size fw-bold">Size</label>
                                               <select class="form-select size-select py-2">
                                                   <option value="S">Small</option>
                                                   <option value="M">Medium</option>
                                                   <option value="L">Large</option>
                                               </select>
                                           </div>
                                           <div class="col-12">
                                               <label for="quantity" class="form-quantity fw-bold">Quantity</label>
                                               <select class="form-select quantity-select py-2">
                                                   <option value="1">1</option>
                                                   <option value="2">2</option>
                                                   <option value="3">3</option>
                                                   <option value="4">4</option>
                                                   <option value="5">5</option>
                                               </select>
                                           </div>
                                       </div>
                                       
                                       <!-- Add to Cart Button -->
                                       <button type="button" class="btn btn-dark mt-4 add-to-cart-btn py-3" onclick="addToCart(this)">
                                           ADD TO CART
                                       </button>
                               </div>
                           </div>
                       </div>
               </div>
           </div>
       </div>
    </div>
</section>

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
    <script src="js/shopcart.js"></script>
    <script src="js/url-cleaner.js"></script>
</body>
</html>