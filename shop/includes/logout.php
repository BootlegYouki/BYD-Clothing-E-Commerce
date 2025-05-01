<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Logout Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to logout?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-acc" id="logoutConfirmBtn">Logout</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const logoutConfirmBtn = document.getElementById('logoutConfirmBtn');
  
  if (logoutConfirmBtn) {
    logoutConfirmBtn.addEventListener('click', function() {
      // Show loading state
      logoutConfirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Logging out...';
      logoutConfirmBtn.disabled = true;
      
      // Make AJAX request to logout
      fetch('includes/logout_process.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'logout' })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Hide modal
          const logoutModal = bootstrap.Modal.getInstance(document.getElementById('logoutModal'));
          logoutModal.hide();
          
          // Update UI
          if (typeof window.updateHeaderAfterLogout === 'function') {
            window.updateHeaderAfterLogout();
          } else {
            // Fallback if function not available
            window.location.href = 'index.php';
          }
        } else {
          console.error('Logout failed:', data.message);
          // Reset button state in case of error
          logoutConfirmBtn.innerHTML = 'Logout';
          logoutConfirmBtn.disabled = false;
        }
      })
      .catch(error => {
        console.error('Error during logout:', error);
        // Reset button state in case of error
        logoutConfirmBtn.innerHTML = 'Logout';
        logoutConfirmBtn.disabled = false;
      });
    });
  }
});
</script>