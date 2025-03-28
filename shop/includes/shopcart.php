<div class="modal fullscreen-modal" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content d-flex flex-column h-100">
      <div class="modal-header">
        <h2 class="modal-title">Your Shopping Cart</h2>
        <div class="total-items">Total Items (x)</div>
        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
</button>
      </div>
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

<div class="empty-cart-message d-flex flex-column justify-content-center align-items-center text-center vh-100 d-none">
    <h2><strong>Your Shopping Cart</strong></h2>
    <p class="fs-6">Your cart is empty at the moment!</p>
</div>

      <div class="modal-footer">
        <div class="footer-content w-100 d-flex justify-content-between align-items-end">
          <div class="d-flex align-items-center gap-3">
            <strong>Subtotal: ₱798.00</strong>
            <button class="btn btn-checkout">Checkout</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="js/url-cleaner.js"></script>