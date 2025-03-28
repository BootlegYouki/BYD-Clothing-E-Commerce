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
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/indexstyle.css">
    <link rel="stylesheet" href="css/shopcart.css">

</head>
<body>
<!-- NAVBAR -->
    <!-- REGISTER MODAL  -->
     <?php include("includes/header.php"); ?>
     <?php include 'includes/register.php'; ?>
    <!-- LOGIN MODAL  -->
    <?php include 'includes/login.php'; ?>
    <!-- SUCCESS MODAL  -->
    <?php include 'includes/loginsuccess.php'; ?>
    <?php include 'includes/registersuccess.php'; ?>
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>
    <!-- SHOP CART -->
    <?php include 'includes/shopcart.php'; ?>

    <section class="container sproduct my-5 pt-5">
    <div class="row mt-5">
        <div class="col-lg-5 col-md-12 col-12">
            <img class="img-fluid w-100 pb-1" src="img/t-shirt_details/s1.webp" id="Mainimg">
            <div class="small-img-group">
                <div class="small-img-col">
                    <img src="img/t-shirt_details/s5.webp" width="100%" class="smol-img">
                </div>
                <div class="small-img-col">
                    <img src="img/t-shirt_details/s2.webp" width="100%" class="smol-img">
                </div>
                <div class="small-img-col">
                    <img src="img/t-shirt_details/size.webp" width="100%" class="smol-img">
                </div>
                <div class="small-img-col">
                    <img src="img/t-shirt_details/s3.webp" width="100%" class="smol-img">
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-md-12 col-12">
            <h5 class="col-s-5 mt-sm-4">Shop / T-Shirt</h5>
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

            <h4 class="mt-3 mb-3">Product Details</h4>
            <span id="desc1">
                Guaranteed to make a bold statement on the road! Air Cool Fabric Riding Gear is a game-changer for riders seeking the perfect balance of comfort, style, and performance—crafted with advanced breathability and moisture-wicking properties, it keeps you cool and dry throughout your journey, while ensuring a snug and flexible fit for unrestricted movement; designed to cater riders of all gender. Crafted with Precision, Worn with Pride a Philippine-Made Product.
            </span>
        </div>
    </div>
</section>
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src =script1.js></script>
<script src =productStorage.js></script>
<script src="js/indexscript.js"></script>
<script src="js/shopcart.js"></script>
<script src="js/shopscript.js"></script>
</body>
</html>
