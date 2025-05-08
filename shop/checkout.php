<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../admin/config/dbcon.php';

// Check if user is logged in
if (!isset($_SESSION['auth_user'])) {
    $_SESSION['checkout_redirect'] = true;
    header("Location: shop.php?checkout_login=1");
    exit;
}

// Get user details for pre-filling the form
$user_id = $_SESSION['auth_user']['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

// Fixed shipping fee
$shipping_fee = 20;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout - Beyond Doubt Clothing</title> 
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/logo/BYD-removebg-preview.ico" type="image/x-icon">
    <!-- LEAFLET CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <style>
        .notification-dropdown {
            transform: translateX(10%);
        }
        .map-container {
            position: relative;
        }
        #map-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            z-index: 999;
            cursor: not-allowed;
        }
        #edit-address-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
    </style>
    <!-- Add checkout-specific CSS -->
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/logout.php'; ?>
    
    <section id="checkout" class="mt-5 py-5">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2>Checkout</h2>
                    <hr class="body-hr mx-auto">
                </div>
            </div>
            
            <!-- Add checkout progress steps -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="checkout-steps">
                        <div class="checkout-step completed">
                            <div class="step-number">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="step-name">Cart</div>
                        </div>
                        <div class="checkout-step active">
                            <div class="step-number">2</div>
                            <div class="step-name">Checkout</div>
                        </div>
                        <div class="checkout-step">
                            <div class="step-number">3</div>
                            <div class="step-name">Payment</div>
                        </div>
                        <div class="checkout-step">
                            <div class="step-number">4</div>
                            <div class="step-name">Confirmation</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout form -->
            <form id="checkout-form" action="functions/process_payment.php" method="POST">
                <input type="hidden" name="shipping_fee" value="<?= $shipping_fee ?>">
                <div class="row g-4">
                    <!-- Left column - Customer info -->
                    <div class="col-md-7">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Shipping Address</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="map-container mb-3">
                                            <div id="map" style="height: 300px;" class="rounded"></div>
                                            <div id="map-overlay"></div>
                                            <button type="button" id="edit-address-btn" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit me-1"></i> Edit Address
                                            </button>
                                        </div>
                                        <div class="alert alert-info small py-2 mb-2" id="map-instructions">
                                            <i class="fa-solid fa-info-circle me-1"></i> Please click on the map or search to select your exact address location.
                                        </div>
                                        <input type="hidden" id="latitude" name="latitude" required value="<?= htmlspecialchars($user['latitude'] ?? '') ?>">
                                        <input type="hidden" id="longitude" name="longitude" required value="<?= htmlspecialchars($user['longitude'] ?? '') ?>">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="address" class="form-label mb-0">Complete Address</label>
                                            <div id="map-status" class="badge bg-secondary">Locked</div>
                                        </div>
                                        
                                        <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['full_address'] ?? '') ?>" readonly required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="zipcode" class="form-label">Postal/ZIP Code</label>
                                        <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?= htmlspecialchars($user['zipcode'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Shipping</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>Standard Shipping</strong>
                                                <p class="mb-0 text-muted small">Delivery within 5-7 business days</p>
                                            </div>
                                            <span class="ms-3">₱<?= number_format($shipping_fee, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Payment Method</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        
                                        <!-- PayMongo Online Payment -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                id="payment_ewallet" value="ewallet" required checked>
                                            <label class="form-check-label d-flex align-items-center" for="payment_ewallet">
                                                <div>
                                                    <strong>Pay Online</strong>
                                                    <p class="mb-0 text-muted small">Pay using your e-wallet account or card</p>
                                                </div>
                                                <span class="ms-auto"><i class="fas fa-credit-card text-info"></i></span>
                                            </label>
                                        </div>
                                        
                                        <!-- Payment information section -->
                                        <div id="ewallet-payment-info">
                                            <p class="small mb-0"><i class="fas fa-info-circle me-2"></i>You will be redirected to PayMongo payment gateway to complete your payment.</p>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column - Order summary -->
                    <div class="col-md-5">
                        <div class="card position-sticky" style="top: 150px;">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div id="order-items">
                                    <!-- Order items will be dynamically added here -->
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="order-subtotal">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span id="order-shipping">₱<?= number_format($shipping_fee, 2) ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4 fw-bold">
                                    <span>Total:</span>
                                    <span id="order-total">₱0.00</span>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark py-3">Proceed to Payment</button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <a href="shop.php" class="text-decoration-none">
                                        <i class="fa fa-arrow-left me-2"></i>Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/shop.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
    // Store shipping fee as a global constant
    const SHIPPING_FEE = <?= $shipping_fee ?>;
    </script>
    <script src="js/checkout.js"></script>
    <script src="js/url-cleaner.js"></script>
</body>
</html>