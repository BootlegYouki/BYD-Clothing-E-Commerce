<!-- Off-Canvas Shopping Cart -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasCartLabel">
            <i class="bx bx-shopping-bag me-2"></i>
            Your Shopping Cart
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Empty Cart State -->
        <div id="empty-cart" class="text-center py-5">
            <i class="bx bx-cart-alt fs-1 text-muted mb-3"></i>
            <p class="mb-3">Your cart is empty</p>
            <a href="shop.php" class="btn btn-outline-dark">Continue Shopping</a>
        </div>
        
        <!-- Cart Items Container -->
        <div id="cart-items-container" class="d-none">
            <div id="cart-items" class="mb-3">
                <!-- Cart items will be dynamically added here -->
            </div>
            
            <!-- Cart Summary -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span>Calculated at checkout</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3 fw-bold">
                        <span>Estimated Total:</span>
                        <span id="cart-total">₱0.00</span>
                    </div>
                    <div class="d-grid gap-2">
                    <?php if(isset($_SESSION['auth_user'])): ?>
                    <a href="checkout.php" class="btn btn-dark">Checkout</a>
                    <?php else: ?>
                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="offcanvas">Login to Checkout</button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Continue Shopping</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>