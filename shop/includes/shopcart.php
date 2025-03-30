<div class="modal fullscreen-modal" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="cart-modal modal-dialog modal-fullscreen">
        <div class="cart-content modal-content d-flex flex-column h-100">
            
        <!-- Header -->
        <div class="cart-header modal-header">
                <h2 class="cart-title modal-title">Your Shopping Cart</h2>
                <div class="total-items">Total Items (0)</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Table wrapper -->
            <div class="cart-table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PRODUCTS LOADED -->
                    </tbody>
                </table>
            </div>

            <!-- Empty cart message -->
            <div class="empty-cart-container">
                <div class="empty-cart-message text-center d-none">
                    <h2>Your Shopping Cart</h2>
                    <div style="display: flex; justify-content: center; align-items: center;">
                    Cart is empty at the moment!
                </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="cart-footer modal-footer">
                <div class="footer-content w-100 d-flex justify-content-between align-items-end">
                    <div class="d-flex align-items-center gap-3">
                        <strong>Subtotal: ₱0.00</strong>
                        <a class="btn btn-checkout" href="checkout.php">CHECKOUT</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 