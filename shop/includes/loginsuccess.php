<div class="modal fade" id="loginsuccessmodal" tabindex="-1" aria-labelledby="loginsuccessmodalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="loginsuccessmodalLabel">Login Successful!</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" 
            onclick="window.location.href='index'"></button>
      </div>
      <div class="modal-body">
      <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'; ?>! You have successfully logged in.</p>
      </div>
      <div class="modal-footer">
        <a href="index" class="btn" style="background-color: #FF7F50; color: white;">Continue</a>
      </div>
    </div>
  </div>
</div>
<script src="js/url-cleaner.js"></script>

<!-- Admin Login Success Modal -->
<div class="modal fade" id="adminLoginSuccessModal" tabindex="-1" aria-labelledby="adminLoginSuccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center border-bottom-0">
        <i class="fas fa-user-shield" style="font-size: 4rem; margin-bottom: 1rem; color: #FF7F50;"></i>
        <h4>Admin Login Successful!</h4>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; ?>!</p>
      </div>
      <div class="modal-footer justify-content-center border-top-0">
        <a href="../admin/index.php" class="btn" style="background-color: #FF7F50; color: white;">Go to Dashboard</a>
      </div>
    </div>
  </div>
</div>

<!-- Script to show admin login modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_SESSION['admin_login_success']) || isset($_GET['adminLogin'])): ?>
    var adminLoginModal = new bootstrap.Modal(document.getElementById('adminLoginSuccessModal'));
    adminLoginModal.show();
    
    <?php 
    // Clear the flag after showing the modal
    unset($_SESSION['admin_login_success']);
    endif; ?>
});
</script>