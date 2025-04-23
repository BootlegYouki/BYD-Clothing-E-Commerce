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
$shipping_fee = 50;
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
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    
    <section id="checkout" class="my-5 py-5">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2>Checkout</h2>
                    <hr class="body-hr mx-auto">
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
                                        <label for="address" class="form-label">Complete Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['full_address'] ?? '') ?>" required>
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
                                                id="payment_ewallet" value="ewallet" required>
                                            <label class="form-check-label d-flex align-items-center" for="payment_ewallet">
                                                <div>
                                                    <strong>Pay Online</strong>
                                                    <p class="mb-0 text-muted small">Pay using your e-wallet account or card</p>
                                                </div>
                                                <span class="ms-auto"><i class="fas fa-credit-card text-info"></i></span>
                                            </label>
                                        </div>
                                        
                                        <!-- Payment information section -->
                                            <div id="ewallet-payment-info" class="d-none">
                                                <p class="small mb-0"><i class="fas fa-info-circle me-2"></i>You will be redirected to PayMongo payment gateway to complete your payment.</p>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column - Order summary -->
                    <div class="col-md-5 pb-3">
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
    <script>
    // Store shipping fee as a global constant
    const SHIPPING_FEE = <?= $shipping_fee ?>;
    
    // Handle payment method selection
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle payment info sections based on selected payment method
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const paymentInfos = {
            'card': document.getElementById('card-payment-info'),
            'ewallet': document.getElementById('ewallet-payment-info'),
            'cod': document.getElementById('cod-payment-info')
        };
        
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                // Hide all payment info sections
                Object.values(paymentInfos).forEach(info => {
                    if (info) info.classList.add('d-none');
                });
                
                // Show selected payment info
                const selectedInfo = paymentInfos[this.value];
                if (selectedInfo) selectedInfo.classList.remove('d-none');
            });
        });
    });
    </script>
    <script src="js/checkout.js"></script>
    <script src="js/url-cleaner.js"></script>
</body>
</html>