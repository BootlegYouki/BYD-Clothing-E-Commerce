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
    <!-- CSS -->
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/important.css">
<<<<<<< Updated upstream
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/indexstyle.css">
=======
    <link rel="stylesheet" href="css/headerfooter.css">
>>>>>>> Stashed changes
    <link rel="stylesheet" href="css/shopcart.css">

</head>
<body>
<!-- NAVBAR -->
<?php include 'includes/navbar.php'; ?>
    <!-- REGISTER MODAL  -->
     <?php include 'includes/register.php'; ?>
    <!-- LOGIN MODAL  -->
    <?php include 'includes/login.php'; ?>
    <!-- SUCCESS MODAL  -->
    <?php include 'includes/loginsuccess.php'; ?>
    <?php include 'includes/registersuccess.php'; ?>
    <!-- FAILED MODAL  -->
    <?php include 'includes/failedmodal.php'; ?>
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>
    <!-- SHOP CART -->
    <?php include 'includes/shopcart.php'; ?>

<!-- PRODUCT -->
    <section class="container sproduct my-5 pt-5">
    <div class="row mt-5">
            <div class="col-lg-5 col-md-12 col-12">
                <img class="img-fluid w-100 pb-1" src="img/featured/gipsy.webp" id="Mainimg">
                <div class="small-img-group">
                    <div class="small-img-col">
                        <img src="img/featured/gipsy.webp" width="100%" class="smol-img">
                    </div>
                    <div class="small-img-col">
                        <img src="img/featured/gipsy.webp" width="100%" class="smol-img">
                    </div>
                    <div class="small-img-col">
                        <img src="img/featured/gipsy.webp" width="100%" class="smol-img">
                    </div>
                    <div class="small-img-col">
                        <img src="img/featured/gipsy.webp" width="100%" class="smol-img">
                    </div>
                </div>
            </div>
        
            <div class="col-lg-5 col-md-12 col-12">
<h5><a href="shop.php" id="eto" style="text-decoration: none; color: black;">Shop </a>/ T-Shirt</h5>
                <h3 class="py-4" id="title1" >T-SHIRT - "EROS” Design AIRCOOL & DRIFIT Fabric - BEYOND DOUBT CLOTHING</h3>
                <h2 id="price1">₱599</h2>
                
                <!-- SIZE -->
                <div class="size-quantity-container ">
                    <select class="mb-3 " id="sizeSelector">
                        <option value="399">Small</option>
                        <option value="449">Medium</option>
                        <option value="499">Large</option>
                        <option value="499">XL</option>
                        <option value="549">XXL</option>
                        <option value="599">XXXL</option>

                    </select>

                    <!-- QUANTITY -->
                    <span class="quantity-selector">
                        <button id="decrement" class="quantity-button">-</button>
                        <span id="quantity">1</span>
                        <button id="increment" class="quantity-button">+</button>
                    </span>
                </div>

                <!-- Buttons -->
                <div class="mt-3 mb-3">
                    <button class="cart-btn">Add to Cart</button>
                    <button class="buy-btn">Buy Now</button>
                </div>
                <!-- PRODUCT DESCRIPTION -->
                <h4 class="mt-3 mb-3">Product Details</h4>
                <span id="desc1">
                    Guaranteed to make a bold statement on the road! Air Cool Fabric Riding Gear is a game-changer for riders seeking the perfect balance of comfort, style, and performance—crafted with advanced breathability and moisture-wicking properties, it keeps you cool and dry throughout your journey, while ensuring a snug and flexible fit for unrestricted movement; designed to cater riders of all gender. Crafted with Precision, Worn with Pride a Philippine-Made Product.
                </span>

    <!-- Review Section -->
    <div class="review-container mt-4">
        <div class="review-header d-flex justify-content-between align-items-center">
            <h4>Customer Reviews</h4>
            <span class="star-rating" id="average-rating">⭐⭐⭐⭐⭐ (4.8/5)</span>
            <button class="buy-btn btn-outline-primary review-toggle">View Reviews ⬇</button>
        </div>

        <!-- Hidden Review List -->
        <div class="review-content mt-3" style="display: none;">
            <div class="review-list">
                <div class="review border p-3 mb-2" data-rating="5">
                    <p><strong>mark darren oandasan</strong> - ⭐⭐⭐⭐⭐</p>
                    <p>"pakyu"</p>
                </div>

                <div class="review border p-3 mb-2" data-rating="4">
                    <p><strong>jm reyes</strong> - ⭐⭐⭐⭐</p>
                    <p>"ahahahfahfahfhafhafa"</p>
                </div>

                <div class="review border p-3 mb-2" data-rating="5">
                    <p><strong>luigi amparado</strong> - ⭐⭐⭐⭐⭐</p>
                    <p>"mama mo review"</p>
                </div>
            </div>

            <!-- Write a Review Form -->
    <div class="write-review mt-4">
        <h5>Write a Review</h5>
        <form id="review-form">
            <div class="mb-2">
                <label for="reviewer-name" class="form-label">Your Name</label>
                <input type="text" id="reviewer-name" class="form-control w-100" placeholder="Enter your name" required>
            </div>
            
            <div class="mb-2">
                <label for="review-text" class="form-label">Your Review</label>
                <textarea id="review-text" class="form-control" rows="3" placeholder="Write your review..." required></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label">Rating</label>
                <select id="review-rating" class="form-select">
                    <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                    <option value="4">⭐⭐⭐⭐ - Good</option>
                    <option value="3">⭐⭐⭐ - Average</option>
                    <option value="2">⭐⭐ - Poor</option>
                    <option value="1">⭐ - Very Bad</option>
                </select>
            </div>

            <button type="submit" class="buy-btn btn-primary">Submit Review</button>
        </form>
    </div>


            </div>
        </div>
    </section>

<!-- RECOMMENDED PRODUCTS -->
<section class="container mt-5">
    <h2 class="text-center mb-4 fw-bold text-uppercase">Recommended Products</h2>

    <div class="row justify-content-center">
        <!-- Product Items -->
        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <div class="card shadow-sm border-0">
                <a href="details.php">
                    <img src="img/featured/gipsy.webp" class="card-img-top rounded" alt="shirt">
                </a>
                <div class="card-body text-center">
                    <h5 class="fw-bold">BYD "GINO" DESIGN - DRIFIT</h5>
                    <p class="text-danger fw-bold fs-5">₱399.00</p>
                    <button class="btn-buy w-100">View</button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <div class="card shadow-sm border-0">
                <a href="details.php">
                    <img src="img/featured/gipsy.webp" class="card-img-top rounded" alt="shirt">
                </a>
                <div class="card-body text-center">
                    <h5 class="fw-bold">BYD "BRAD" DESIGN - DRIFIT</h5>
                    <p class="text-danger fw-bold fs-5">₱399.00</p>
                    <button class="btn-buy  w-100">View</button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
            <div class="card shadow-sm border-0">
                <a href="details.php">
                    <img src="img/featured/gipsy.webp" class="card-img-top rounded" alt="shirt">
                </a>
                <div class="card-body text-center">
                    <h5 class="fw-bold">BYD "ARON" DESIGN - DRIFIT</h5>
                    <p class="text-danger fw-bold fs-5">₱399.00</p>
                    <button class="btn-buy  w-100">View</button>
                </div>
            </div>
        </div>

        <!-- Initially Hidden Products -->
        <div class="col-lg-4 col-md-6 col-sm-6 mb-4 d-none more-products">
            <div class="card shadow-sm border-0">
                <a href="details.php">
                    <img src="img/featured/gipsy.webp" class="card-img-top rounded" alt="shirt">
                </a>
                <div class="card-body text-center">
                    <h5 class="fw-bold">BYD "ZED" DESIGN - DRIFIT</h5>
                    <p class="text-danger fw-bold fs-5">₱399.00</p>
                    <button class="btn-buy w-100">View</button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 mb-4 d-none more-products">
            <div class="card shadow-sm border-0">
                <a href="details.php">
                    <img src="img/featured/gipsy.webp" class="card-img-top rounded" alt="shirt">
                </a>
                <div class="card-body text-center">
                    <h5 class="fw-bold">BYD "KEN" DESIGN - DRIFIT</h5>
                    <p class="text-danger fw-bold fs-5">₱399.00</p>
                    <button class="btn-buy w-100">View</button>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6 mb-4 d-none more-products">
            <div class="card shadow-sm border-0">
                <a href="details.php">
                    <img src="img/featured/gipsy.webp" class="card-img-top rounded" alt="shirt">
                </a>
                <div class="card-body text-center">
                    <h5 class="fw-bold">BYD "TONY" DESIGN - DRIFIT</h5>
                    <p class="text-danger fw-bold fs-5">₱399.00</p>
                    <button class="btn-buy w-100">View</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Show More Button -->
    <div class="text-center mt-4">
        <button id="showMoreBtn" class="btn btn-coral px-4">Show More</button>
    </div>
</section>


    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src =script1.js></script>
<script src="js/indexscript.js"></script>
<<<<<<< Updated upstream
<script src="js/shopcart.js"></script>
<script src="js/shopscript.js"></script>
=======
<script src="js/details.js"></script>

<script src="js/shopcart.js"></script>
<script src="js/shopscript.js"></script>
<script src="js/url-cleaner.js"></script>
>>>>>>> Stashed changes
</body>
</html>
