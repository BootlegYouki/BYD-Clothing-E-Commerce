<div class="modal fade" id="loginsuccessmodal" tabindex="-1" aria-labelledby="loginsuccessmodalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="loginsuccessmodalLabel">Login Successful!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <p>Welcome back, <span id="login-username"><?php echo isset($_SESSION['auth_user']['username']) ? $_SESSION['auth_user']['username'] : $_SESSION['username'] ?? 'User'; ?></span>! You have successfully logged in.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" id="loginSuccessContinueBtn" style="background-color: #FF7F50; color: white;">Continue</button>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Show login success modal if session flag is set
    <?php if(isset($_SESSION['login_success'])): ?>
    var loginSuccessModal = new bootstrap.Modal(document.getElementById('loginsuccessmodal'));
    loginSuccessModal.show();
    <?php 
    // Clear the flag after showing the modal
    unset($_SESSION['login_success']);
    endif; ?>
    
    // Update the username in the modal with the latest from AJAX response if available
    window.updateLoginSuccessUsername = function(username) {
      const usernameElement = document.getElementById('login-username');
      if (usernameElement && username) {
        usernameElement.textContent = username;
      }
    };
    
    // Handle the continue button click based on redirect flag
    const continueBtn = document.getElementById('loginSuccessContinueBtn');
    if (continueBtn) {
      continueBtn.addEventListener('click', function() {
        if (sessionStorage.getItem('redirectToCheckout') === 'true') {
          sessionStorage.removeItem('redirectToCheckout');
          window.location.href = 'checkout.php';
        } else {
          const loginSuccessModal = bootstrap.Modal.getInstance(document.getElementById('loginsuccessmodal'));
          if (loginSuccessModal) {
            loginSuccessModal.hide();
          }
        }
      });
    }
  });
</script>

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
    <?php if(isset($_SESSION['admin_login_success'])): ?>
    // Check if we've already shown this modal in the current session
    if (!sessionStorage.getItem('adminLoginShown')) {
        var adminLoginModal = new bootstrap.Modal(document.getElementById('adminLoginSuccessModal'));
        adminLoginModal.show();
        // Set a flag in sessionStorage so we don't show it again
        sessionStorage.setItem('adminLoginShown', 'true');
    }
    
    <?php 
    unset($_SESSION['admin_login_success']);
    endif; ?>
});
</script>