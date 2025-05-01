<!-- Payment Success Modal -->
<div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="paymentSuccessModalLabel">Payment Successful</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-4">
          <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
        </div>
        <p>Your order has been successfully processed. Thank you for shopping with BYD Clothing!</p>
        <p>Your order reference is: <strong id="orderReferenceDisplay"></strong></p>
        <p>You will receive an email confirmation with your order details shortly.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continue Shopping</button>
      </div>
    </div>
  </div>
</div>

<!-- Payment Failed Modal -->
<div class="modal fade" id="paymentFailedModal" tabindex="-1" aria-labelledby="paymentFailedModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="paymentFailedModalLabel">Payment Failed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-4">
          <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
        </div>
        <p>Your payment was not processed successfully. Your items are still in your cart.</p>
        <p>You can try again or contact our customer support if you need assistance.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="checkout.php" class="btn btn-primary">Try Again</a>
      </div>
    </div>
  </div>
</div>

<!-- Script to show payment status modals -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['payment_status']) && $_SESSION['payment_status'] === 'success'): ?>
    // Show success modal
    const successModal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
    
    // Set order reference if available
    if (document.getElementById('orderReferenceDisplay')) {
        document.getElementById('orderReferenceDisplay').textContent = '<?php echo isset($_SESSION['order_reference']) ? htmlspecialchars($_SESSION['order_reference']) : ""; ?>';
    }
    
    successModal.show();
    
    <?php unset($_SESSION['payment_status']); unset($_SESSION['order_reference']); ?>
    <?php elseif(isset($_SESSION['payment_status']) && $_SESSION['payment_status'] === 'failed'): ?>
    // Show failed modal
    const failedModal = new bootstrap.Modal(document.getElementById('paymentFailedModal'));
    failedModal.show();
    <?php unset($_SESSION['payment_status']); ?>
    <?php endif; ?>
});
</script>