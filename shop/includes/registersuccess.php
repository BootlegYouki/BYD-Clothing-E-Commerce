<!-- Registration Success Modal -->
<div class="modal fade" id="registersuccessmodal" tabindex="-1" aria-labelledby="registersuccessmodalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center border-bottom-0">
        <i class="fas fa-check-circle" style="font-size: 4rem; margin-bottom: 1rem; color: #FF7F50;"></i>
        <h4>Welcome to BYD Clothing!</h4>
        <p>Your account has been created successfully and you are now logged in.</p>
        <p>Thank you for joining us!</p>
      </div>
      <div class="modal-footer justify-content-center border-top-0">
        <button type="button" class="btn" style="background-color: #FF7F50; color: white;" data-bs-dismiss="modal">Start Shopping</button>
      </div>
    </div>
  </div>
</div>

<!-- Script to show modal based on session variable -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['registration_success'])): ?>
    var registrationModal = new bootstrap.Modal(document.getElementById('registersuccessmodal'));
    registrationModal.show();
    <?php 
    // Clear the flag after showing the modal
    unset($_SESSION['registration_success']);
    endif; ?>
});
</script>