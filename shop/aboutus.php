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
    <link rel="stylesheet" href="css/shopcart.css">
    <link rel="stylesheet" href="css/aboutus.css">
    <link rel="stylesheet" href="css/headerfooter.css">
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


    <section class="top-image-container">
        <img src="img/logo/Banner.png" alt="Banner" class="top-image">
      </section>      
    <section id="contact-details">
<div class="details">
    <span>GET IN TOUCH</span>
    <h2>Visit our Flagship Store or contact us today</h2>
    <h3>Flagship Store</h3>
    <div>
        <li>
            <i class="fa fa-map" aria-hidden="true"></i>
            <p>Block 27 Lot 12, Pechayan Kanan Namasape HOA, Quezon City, 1121 Metro Manila</p>
        </li>
        <li>
            <i class="fa fa-envelope" aria-hidden="true"></i>
            <p>test@gmail.com</p>
        </li> <li>
            <i class="fa fa-phone" aria-hidden="true"></i>
            <p>0905 507 9634</p>
        </li> <li>
            <i class="fa fa-clock-o" aria-hidden="true"></i>
            <p>Monday to Saturday: 9:00am to 8:30pm</p>
        </li>
    </div>
</div>
<div class="map">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d781.3691204711571!2d121.0653305!3d14.706414699999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b10c977c4ab7%3A0x8fe8b06b27997eea!2sBeyond%20Doubt%20Clothing!5e1!3m2!1sen!2sph!4v1740317440833!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
</section>

<section id="form-details" class="container">
  <div class="row">
    <div class="col-md-6 mb-md-0">
      <form class="sublimation-form mx-auto">
        <span>LEAVE US A MESSAGE</span>    
        <h2>SEND US YOUR DESIGN</h2>
        <input type="text" placeholder="E-mail" class="deets">
        <input type="text" placeholder="Subject" class="deets">
        <textarea name="" id="text-a" cols="30" rows="10" placeholder="Your Message"></textarea>
        <button class="message btn-body">Submit</button>
      </form>
    </div>
    
    <!-- Right Column: Image -->
    <div class="col-md-6 pt-4 mt-2">
      <div class="custom1">
        <img src="img/sublimation.jpg" alt="sublimation">
      </div>
    </div>
  </div>
</section>
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>

    <!--SCRIPT-->
    <script src="js/shopcart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/url-cleaner.js"></script>
</body>
</html>