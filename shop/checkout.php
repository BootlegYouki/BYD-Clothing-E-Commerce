<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyond Doubt Clothing</title> 
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="img/logo/BYD-removebg-preview.ico" type="image/x-icon">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/checkout.css">
    <script>
        function togglePayment(targetId) {
            document.querySelectorAll('.collapse').forEach(el => {
                if (el.id !== targetId) {
                    el.classList.remove('show');
                }
            });
            document.getElementById(targetId).classList.toggle('show');
        }
    </script>
</head>
<body>
    <div class="checkout-container container">
        <!-- Delivery Address Section -->
        <div class="user-info d-flex justify-content-between align-items-center p-3">
            <div>
                <h5 class="text-danger">
                    <i class="bi bi-geo-alt"></i> Delivery Address
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <strong>Jm Reyes</strong>
                    <strong>(+63) 9682296846</strong>
                </div>
                <p class="mb-0">20 F Sampaguita St, Apolonio Samson,</p>
                <p class="mb-0">Quezon City, Metro Manila 1106</p>
            </div>
            <a href="#" class="btn btn-sm btn-outline-primary">Change</a>
        </div>

        <!-- Products Ordered -->
        <div class="ordered-products">
            <h5 class="fw-normal">Products Ordered</h5>
            <table class="table checkout-table text-center">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody id="checkout-items">
                    <!-- PRODUCTS LOADED -->
                </tbody>
            </table>
        </div>

        <!-- Payment Method Section -->
        <div class="order-summary p-3">
            <h5 class="text-start">Payment Method</h5>
            <div class="text-start justify-content-between">
                <button class="btn btn-outline-danger flex-grow-1 mx-1" onclick="togglePayment('cod')">Cash on Delivery</button>
                <button class="btn btn-outline-secondary flex-grow-1 mx-1" onclick="togglePayment('credit')">Credit/Debit Card</button>
                <button class="btn btn-outline-secondary flex-grow-1 mx-1" onclick="togglePayment('ewallet')">E-Wallet</button>
            </div>
            
            <div class="collapse mt-3" id="cod">
                <div class="card card-body">Cash on Delivery is available.</div>
            </div>
            
            <div class="collapse mt-3" id="credit">
                <div class="card card-body">
                    <button class="btn btn-outline-primary">Pay With New Card</button>
                </div>
            </div>
            
            <div class="collapse mt-3" id="ewallet">
    <div class="card card-body">
        <label class="d-block payment-option">
            <input type="radio" name="payment-method" value="gcash">
            <img src="img/e-wallets/gcash.png" alt="GCash" class="payment-logo">
            GCash 
        </label>
        <label class="d-block payment-option">
            <input type="radio" name="payment-method" value="maya">
            <img src="img/e-wallets/maya.png" alt="Maya" class="payment-logo">
            Maya
        </label>
        <label class="d-block payment-option">
            <input type="radio" name="payment-method" value="7-eleven">
            <img src="img/e-wallets/cliqq.png" alt="7-Eleven CLiQQ" class="payment-logo">
            7-Eleven CLiQQ 
        </label>
    </div>
</div>

            <hr>
            <!-- Order Summary -->
            <div class="text-end">
                <p>Merchandise Subtotal: <strong>₱399</strong></p>
                <p>Shipping Fee: <strong>₱36</strong></p>
                <p class="total-price"><strong>Total Payment: ₱435</strong></p>
                <hr>
                <a href="confirm.php" class="btn btn-sm btn-place d-block ms-auto">PLACE ORDER</a>
            </div>
        </div>
    </div>

    <!--SCRIPT-->
    <script src="js/checkoutscript.js"></script>
    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>