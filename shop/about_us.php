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
    <link rel="stylesheet" href="css/about_us.css">

</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <!-- REGISTER MODAL  -->
     <?php include 'includes/register.php'; ?>
    <!-- LOGIN MODAL  -->
    <?php include 'includes/login.php'; ?>
    <!-- SUCCESS MODAL  -->
    <?php include 'includes/loginsuccess.php'; ?>
    <?php include 'includes/registersuccess.php'; ?>
    <!-- FAILED MODAL  -->
<<<<<<< Updated upstream
    <?php include 'includes/failedmodal.php'; ?>
=======
>>>>>>> Stashed changes
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>
    <!-- SHOP CART -->
    <?php include 'includes/shopcart.php'; ?>

    <!-- BANNER -->
    <section class="top-image-container">
        <img src="img/logo/Banner.png" alt="Banner" class="top-image">
      </section>      

 <!-- ABOUT US SECTION 1-->
<section id="about-head" class="bg-black">
    <div class="container" >
        <div class="left-side">
            <h1>Who We Are</h1>
            <p>
                Beyond Doubt Clothing (BYD) is a local brand specializing in moto-inspired shirts and long sleeves. 
                Designed for riders and streetwear enthusiasts, our apparel combines style, comfort, and durability. Based in Quezon City, we take pride in creating quality gear that reflects the fearless moto lifestyle. Ride beyond limits—wear Beyond Doubt.
            </p>
            <a href="visit_us.php">
            <button class="btn-body" > VISIT US</button>
            </a>
        </div>
        <div class="right-side">
            <img src="img/featured/abt_1.png" class="abt-img">
        </div>
    </div>
</section>

<!-- ABOUT US SECTION 2 -->
<section id="about-head-2">
    <div class="container">
        <div class="left-side-2">
            <img src="img/featured/custom1.png" class="abt-img-2">
        </div>
        <div class="right-side-2 md-6 mb-md-0">
            <h1>Custom Designs & Sublimation</h1>
            <p >We provide high-quality custom designs and sublimation printing for long-lasting, vibrant prints. 
                Using premium materials and expert techniques, we ensure durable, comfortable, and well-crafted apparel for personal wear, teams, or events.                
            </p>
           <button type="button" class="btn-form" data-bs-toggle="modal" data-bs-target="#contactModal">
    Send us your Design 
</button>

        </div>
    </div>
</section>

<!-- ABOUT US SECTION 3---- TAGLINE -->
<section id="about-us-3">
    <div class="container-2">
        <!-- LEFT COLUMN -->
        <div class="left-text">
            <h1>Find Fashion <br>
                <span id="orange"> That Make a Statement</span></h1>
            <p><strong>What do we make?</strong></p>
            <p>We craft high-quality apparel inspired by moto culture. 
                Our collection includes stylish and durable shirts and long sleeves designed for riders and streetwear enthusiasts. 
                Every piece is made for comfort, performance, and everyday wear, ensuring both function and style on and off the roa</p>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-text">
            <br>
            <br><br>
            <p><strong>How do we create our products?</strong></p>
            <p>
                We use quality fabrics, 
                precise stitching, and advanced printing to ensure durable, comfortable, and long-lasting apparel. 
                Every piece is carefully crafted and quality-checked for the best fit and design.</p>
        </div>
    </div>
</section>


<!-- ABOUT US SECTION 3 --- FEATURED PRODUCTS-->
<section id="about-us-3">
    <div class="container-3">
        <div class="column">
            <a>
            <img src="img/featured/latest_new.png">
            </a>
            <p><strong>New Arrivals</strong></p>
            <p>The Latest BYD Dropped; Ready to be Shopped</p>
            <a href="shop.php" class="shop-now" id="orange">Shop Now</a>
        </div>

        <div class="column">
            <a>
            <img src="img/featured/top.png">
            </a>
            <p><strong>Top Rated</strong></p>
            <p>Our Best Reviewed Products love by our Customers</p>
            <a href="shop.php" class="shop-now" id="orange">Shop Now</a>

        </div>

        <div class="column">
            <a>
            <img src="img/featured/popular.png">
            </a>
            <p><strong>Best Sellers</strong></p>
            <p>Our most popular Products, Always Popular, Always a good idea</p>
            <a href="shop.php" class="shop-now" id="orange">Shop Now</a>

        </div>
    </div>
    
</section>

<!--  ABOUT US SECTION 3 --- NEWSLETTER-->
<!-- <section id="Newsletter">
    <div class="newsletter">
        <h2>Subscribe to Our Newsletter</h2>
        <p>Get the latest updates and offers.</p>
        <form >
            <input type="email" placeholder="Enter your email" required>
            <button class="btn-body">Subscribe</button>
        </form>
    </div>
</section>  -->


<!-- Modal -->
<section>
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">  <!-- Responsive Modal -->
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-uppercase" id="contactModalLabel" style="color: coral;">Contact Us / Request for Quotation / Submit Your Designs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="text-danger small"><strong>* Indicates required field</strong></p>
                    <form>
                        <div class="row g-2">
                            <!-- Company -->
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Company <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <!-- First & Last Name -->
                            <div class="col-md-6 col-12">
                                <label class="form-label small">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <!-- Email & Phone -->
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Phone Number</label>
                                <input type="text" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <!-- Mobile Number & Product -->
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Product for Quotation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <!-- Quantity & Address -->
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 col-12">
<<<<<<< Updated upstream
                                <label class="form-label small">City / Address <span class="text-danger">*</span></label>
=======
                                <label class="form-label small">Address <span class="text-danger">*</span></label>
>>>>>>> Stashed changes
                                <input type="text" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <!-- Delivery Date -->
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Delivery Date Required <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-sm" required>
                            </div>
                            <!-- File Upload -->
                            <div class="col-md-6 col-12">
                                <label class="form-label small">Upload File</label>
                                <input type="file" class="form-control form-control-sm">
                                <small class="text-muted">Max file size: 20MB</small>
                            </div>
                        </div>

                        <!-- Comments -->
                        <div class="mt-2">
                            <label class="form-label small">Comment</label>
                            <textarea class="form-control form-control-sm" rows="2"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class=" btn-form btn w-100 p-2 mt-3" style="background-color: coral; color: white; font-size: 1rem;">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <!--SCRIPT-->
    <script src="js/indexscript.js"></script>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<<<<<<< Updated upstream
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
=======
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>

>>>>>>> Stashed changes

    <!-- BOOTSTRAP JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<<<<<<< Updated upstream
=======
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

>>>>>>> Stashed changes
</body>
</html>