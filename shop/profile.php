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

    <section id="profile" class="my-5 py-5">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2>Profile</h2>
                    <hr class="body-hr mx-auto">
                </div>
            </div>

            <div class="row g-4">
                <!-- Sidebar -->
                <div class="col-md-4 col-lg-3">
                    <div class="sidebar p-3 shadow-sm">
                        <h2 class="d-flex align-items-center justify-content-between mb-3 fw-bold">
                            <a class="text-dark text-decoration-none d-flex align-items-center w-100" data-bs-toggle="collapse" href="#accountMenu" role="button" aria-expanded="false">
                                <i class="fas fa-user-circle me-2"></i> <span class="fs-6">My Account</span> 
                                
                            </a>
                        </h2>

                        <!-- Collapsible Menu -->
                        <div class="collapse show" id="accountMenu"> 
                            <div class="d-flex flex-column">
                                <button class="btn sidebar-btn active" data-page="includes/profile_user_info.php">Profile</button>
                                <button class="btn sidebar-btn" data-page="includes/profile_address.php">Address</button>
                                <button class="btn sidebar-btn" data-page="includes/profile_changePass.php">Change Password</button>
                                <button class="btn sidebar-btn" data-page="includes/order_track.php">Orders</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Section -->
                <div class="col-md-8 col-lg-9">
                    <div class="content shadow bg-white rounded">
                        <?php 
                        // Check if a specific tab is requested via URL parameter
                        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
                        
                        if ($tab === 'order_track') {
                            include 'includes/order_track.php';
                        } else {
                            include 'includes/profile_user_info.php';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    <!--SCRIPT-->
    <script src="js/indexscript.js"></script>
    <script src="js/profile.js"></script>
  
    <!-- Google Maps API (Enable Places Library) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Check if we need to activate a specific tab based on URL parameter
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        if (tab === 'order_track') {
            // Activate the Orders tab
            document.querySelectorAll('.sidebar-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-page') === 'includes/order_track.php') {
                    btn.classList.add('active');
                }
            });
        }
    });
    </script>
</body>
</html>