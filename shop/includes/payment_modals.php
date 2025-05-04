<!-- Payment Success Modal -->
<div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 10px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center px-4 pt-0">
        <div class="mb-4">
          <i class="fas fa-check-circle" style="font-size: 70px; color: #4CAF50;"></i>
        </div>
        <h4 style="font-weight: 600; color: #333; font-size: 24px; margin-bottom: 15px;">Payment Successful</h4>
        <p style="color: #666; font-size: 15px; margin-bottom: 15px;">Your order has been successfully processed. Thank you for shopping with BYD Clothing!</p>
        <p style="color: #666; font-size: 15px; margin-bottom: 5px;">Your order reference is:</p>
        <p><strong id="orderReferenceDisplay" style="font-size: 18px; color: coral;"></strong></p>
        <p style="color: #666; font-size: 15px; margin-bottom: 25px;">You will receive an email confirmation with your order details shortly.</p>
        <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: coral; color: #fff; border: none; width: 100%; padding: 14px; font-weight: 600; border-radius: 8px; font-size: 16px; letter-spacing: 0.5px; transition: all 0.3s; margin-bottom: 20px;">Continue Shopping</button>
      </div>
    </div>
  </div>
</div>

<!-- Payment Failed Modal -->
<div class="modal fade" id="paymentFailedModal" tabindex="-1" aria-labelledby="paymentFailedModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 10px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center px-4 pt-0">
        <div class="mb-4">
          <i class="fas fa-times-circle" style="font-size: 70px; color: #dc3545;"></i>
        </div>
        <h4 style="font-weight: 600; color: #333; font-size: 24px; margin-bottom: 15px;">Payment Failed</h4>
        <p style="color: #666; font-size: 15px; margin-bottom: 15px;">Your payment was not processed successfully. Your items are still in your cart.</p>
        <p style="color: #666; font-size: 15px; margin-bottom: 25px;">You can try again or contact our customer support if you need assistance.</p>
        <div class="d-grid gap-2">
          <a href="checkout.php" class="btn" style="background-color: coral; color: #fff; border: none; width: 100%; padding: 14px; font-weight: 600; border-radius: 8px; font-size: 16px; letter-spacing: 0.5px; transition: all 0.3s; margin-bottom: 10px;">Try Again</a>
          <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #f5f5f5; color: #333; border: none; width: 100%; padding: 14px; font-weight: 600; border-radius: 8px; font-size: 16px; letter-spacing: 0.5px; transition: all 0.3s; margin-bottom: 20px;">Close</button>
        </div>
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