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
    <link rel="stylesheet" href="css/profile.css">
  


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
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>
    <!-- SHOP CART -->
    <?php include 'includes/shopcart.php'; ?>



 


    

    <!-- BANNER -->
<!--     <section class="top-image-container">
        <img src="img/logo/Banner.png" alt="Banner" class="top-image">
      </section>     -->  

<!-- Sidebar & Main Content Wrapper -->
<section class="d-flex mt-5">
    <!-- Sidebar -->
    <div class="sidebar bg-light p-3 shadow-sm mt-5" style="width: 250px; min-height: 100vh;">
        <h2 class="d-flex align-items-center justify-content-between">
            <a class="text-dark text-decoration-none d-flex align-items-center w-100" data-bs-toggle="collapse" href="#accountMenu" role="button" aria-expanded="false">
                <i class="fas fa-user-circle me-2"></i> <span>My Account</span> 
                <i class="fa fa-chevron-down ms-auto"></i>
            </a>
        </h2>

        <!-- Collapsible Menu -->
        <div class="collapse show" id="accountMenu"> 
            <div class="d-flex flex-column">
                <button class="btn sidebar-btn active" data-page="includes/profile_user_info.php">Profile</button>
                <button class="btn sidebar-btn" data-page="includes/profile_address.php">Address</button>
                <button class="btn sidebar-btn" data-page="includes/profile_changePass.php">Change Password</button>
            </div>
        </div>

        <!-- Purchases -->
        <h2 class="mt-3 d-flex align-items-center">
            <i class="fas fa-shopping-cart me-2"></i> <span>Purchases</span>
        </h2>
    </div>

    <!-- Main Content Section (Now inside flex container) -->
    <div class="p-4 flex-grow-1 mt-5">
        <div class="content shadow bg-white rounded">
            <?php include 'includes/profile_user_info.php'; ?>
        </div>
    </div>
</section>


    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <!--SCRIPT-->
    <script src="js/indexscript.js"></script>
    <script src="js/profile.js"></script>


    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Google Maps API (Enable Places Library) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>

    <!-- BOOTSTRAP JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>