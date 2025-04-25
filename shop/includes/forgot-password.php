<!-- FORGOT PASSWORD MODAL -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h3 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info mb-3">
          <i class="fas fa-info-circle me-2"></i> Enter your email address below. We'll send you a verification code to reset your password.
        </div>
        
        <div class="alert alert-danger mb-3 d-none" id="forgotPasswordErrorMessage"></div>
        <div class="alert alert-success mb-3 d-none" id="forgotPasswordSuccessMessage"></div>
        
        <form id="forgotPasswordForm" class="needs-validation" novalidate>
          <div class="row gy-3 overflow-hidden">
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="forgot-email" placeholder="Email Address" required>
                <label for="forgot-email" class="form-label">Email Address</label>
                <div class="invalid-feedback">
                  Please enter your email address.
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="d-grid">
                <button type="submit" id="forgotPasswordButton" class="btn-modal btn-lg">Send Reset Link</button>
              </div>
            </div>
          </div>
        </form>
        <div class="mt-4 text-center">
          <p>Remember your password? <a href="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal" class="modal-link text-decoration-none">Login here</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- OTP VERIFICATION MODAL -->
<div class="modal fade" id="otpVerificationModal" tabindex="-1" aria-labelledby="otpVerificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h3 class="modal-title" id="otpVerificationModalLabel">Verify Your Email</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info mb-3">
          <i class="fas fa-info-circle me-2"></i> We've sent a verification code to your email. Please enter it below.
        </div>
        
        <div class="alert alert-danger mb-3 d-none" id="otpErrorMessage"></div>
        
        <form id="otpVerificationForm" class="needs-validation" novalidate>
          <input type="hidden" id="reset-email" name="email">
          <div class="row gy-3 overflow-hidden">
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="otp" id="otp-code" placeholder="Verification Code" required>
                <label for="otp-code" class="form-label">Verification Code</label>
                <div class="invalid-feedback">
                  Please enter the verification code.
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="d-grid">
                <button type="submit" id="verifyOtpButton" class="btn-modal btn-lg">Verify Code</button>
              </div>
            </div>
          </div>
        </form>
        <div class="mt-4 text-center">
          <p>Didn't receive the code? <a href="#" id="resendOtpLink" class="modal-link text-decoration-none">Resend Code</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- RESET PASSWORD MODAL -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h3 class="modal-title" id="resetPasswordModalLabel">Reset Your Password</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info mb-3">
          <i class="fas fa-info-circle me-2"></i> Create a new password for your account.
        </div>
        
        <div class="alert alert-danger mb-3 d-none" id="resetPasswordErrorMessage"></div>
        
        <form id="resetPasswordForm" class="needs-validation" novalidate>
          <input type="hidden" id="reset-email-final" name="email">
          <input type="hidden" id="reset-token" name="token">
          <div class="row gy-3 overflow-hidden">
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" id="new-password" placeholder="New Password" required minlength="8">
                <label for="new-password" class="form-label">New Password</label>
                <div class="invalid-feedback">
                  Password must be at least 8 characters.
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="password" class="form-control" name="confirm_password" id="confirm-password" placeholder="Confirm Password" required>
                <label for="confirm-password" class="form-label">Confirm Password</label>
                <div class="invalid-feedback">
                  Passwords don't match.
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input toggle-reset-password" type="checkbox" id="show_reset_password">
                <label class="form-check-label text-secondary" for="show_reset_password">
                  Show Password
                </label>
              </div>
            </div>
            <div class="col-12">
              <div class="d-grid">
                <button type="submit" id="resetPasswordButton" class="btn-modal btn-lg">Reset Password</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Update the script section at the bottom of the file -->

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password visibility toggle
  const toggleResetPassword = document.querySelector('.toggle-reset-password');
  const newPasswordInput = document.getElementById('new-password');
  const confirmPasswordInput = document.getElementById('confirm-password');
  
  // Get form references once
  const resetPasswordForm = document.getElementById('resetPasswordForm');
  const forgotPasswordForm = document.getElementById('forgotPasswordForm');
  const otpVerificationForm = document.getElementById('otpVerificationForm');
  
  // OTP timer functionality
  let otpTimer;
  let remainingTime = 60; // 60 seconds cooldown
  
  function startOtpTimer() {
    const timerDisplay = document.createElement('span');
    timerDisplay.id = 'otp-timer';
    timerDisplay.className = 'ms-2 text-muted';
    
    const resendLink = document.getElementById('resendOtpLink');
    if (resendLink) {
      resendLink.parentNode.appendChild(timerDisplay);
      resendLink.style.pointerEvents = 'none';
      resendLink.classList.add('text-muted');
      
      remainingTime = 60;
      updateTimerDisplay();
      
      otpTimer = setInterval(function() {
        remainingTime--;
        updateTimerDisplay();
        
        if (remainingTime <= 0) {
          clearInterval(otpTimer);
          timerDisplay.remove();
          resendLink.style.pointerEvents = '';
          resendLink.classList.remove('text-muted');
        }
      }, 1000);
    }
  }
  
  function updateTimerDisplay() {
    const timerDisplay = document.getElementById('otp-timer');
    if (timerDisplay) {
      timerDisplay.textContent = `(${remainingTime}s)`;
    }
  }
  
  if(toggleResetPassword && newPasswordInput && confirmPasswordInput) {
    toggleResetPassword.addEventListener('change', function() {
      const type = this.checked ? 'text' : 'password';
      newPasswordInput.type = type;
      confirmPasswordInput.type = type;
    });
  }
  
  // Forgot Password Form Submission
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (!forgotPasswordForm.checkValidity()) {
        e.stopPropagation();
        forgotPasswordForm.classList.add('was-validated');
        return;
      }
      
      // Show loading state
      const button = document.getElementById('forgotPasswordButton');
      button.innerHTML = 'Sending...';
      button.disabled = true;
      
      const email = document.getElementById('forgot-email').value;
      
      // Send request to server
      fetch('functions/forgot_password.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'email=' + encodeURIComponent(email)
      })
      .then(response => response.json())
      .then(data => {
        button.innerHTML = 'Send Reset Link';
        button.disabled = false;
        
        if (data.status === 'success') {
          // Hide error message if visible
          document.getElementById('forgotPasswordErrorMessage').classList.add('d-none');
          
          // Show success message
          const successMessage = document.getElementById('forgotPasswordSuccessMessage');
          successMessage.textContent = data.message;
          successMessage.classList.remove('d-none');
          
          // Store email for OTP verification
          document.getElementById('reset-email').value = email;
          
          // Show OTP verification modal after a short delay
          setTimeout(() => {
            const forgotPasswordModal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
            forgotPasswordModal.hide();
            
            const otpVerificationModal = new bootstrap.Modal(document.getElementById('otpVerificationModal'));
            otpVerificationModal.show();
            
            // Start the OTP timer
            startOtpTimer();
          }, 1500);
        } else {
          // Show error message
          const errorMessage = document.getElementById('forgotPasswordErrorMessage');
          errorMessage.textContent = data.message;
          errorMessage.classList.remove('d-none');
          
          // Hide success message if visible
          document.getElementById('forgotPasswordSuccessMessage').classList.add('d-none');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        button.innerHTML = 'Send Reset Link';
        button.disabled = false;
        
        const errorMessage = document.getElementById('forgotPasswordErrorMessage');
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.classList.remove('d-none');
      });
    });
  }
  
  // OTP Verification Form Submission
  if (otpVerificationForm) {
    otpVerificationForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (!otpVerificationForm.checkValidity()) {
        e.stopPropagation();
        otpVerificationForm.classList.add('was-validated');
        return;
      }
      
      // Show loading state
      const button = document.getElementById('verifyOtpButton');
      button.innerHTML = 'Verifying...';
      button.disabled = true;
      
      const email = document.getElementById('reset-email').value;
      const otp = document.getElementById('otp-code').value;
      
      // Send request to server
      fetch('functions/verify_reset_otp.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'email=' + encodeURIComponent(email) + '&otp=' + encodeURIComponent(otp)
      })
      .then(response => response.json())
      .then(data => {
        button.innerHTML = 'Verify Code';
        button.disabled = false;
        
        if (data.status === 'success') {
          // Hide error message if visible
          document.getElementById('otpErrorMessage').classList.add('d-none');
          
          // Store email and token for password reset
          document.getElementById('reset-email-final').value = data.email;
          document.getElementById('reset-token').value = data.token;
          
          // Show reset password modal after a short delay
          setTimeout(() => {
            // Hide OTP verification modal
            const otpVerificationModal = bootstrap.Modal.getInstance(document.getElementById('otpVerificationModal'));
            otpVerificationModal.hide();
            
            // Show password reset modal
            const resetPasswordModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            resetPasswordModal.show();
          }, 1000);
        } else {
          // Show error message
          const errorMessage = document.getElementById('otpErrorMessage');
          errorMessage.textContent = data.message;
          errorMessage.classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        button.innerHTML = 'Verify Code';
        button.disabled = false;
        
        const errorMessage = document.getElementById('otpErrorMessage');
        errorMessage.textContent = 'An error occurred. Please try again later.';
        errorMessage.classList.remove('d-none');
      });
    });
  }  
  // Add Reset Password Form Submission
  if (resetPasswordForm) {
    resetPasswordForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (!resetPasswordForm.checkValidity()) {
        e.stopPropagation();
        resetPasswordForm.classList.add('was-validated');
        return;
      }
      
      const password = document.getElementById('new-password').value;
      const confirmPassword = document.getElementById('confirm-password').value;
      
      if (password !== confirmPassword) {
        const errorMessage = document.getElementById('resetPasswordErrorMessage');
        errorMessage.textContent = "Passwords don't match";
        errorMessage.classList.remove('d-none');
        return;
      }
      
      // Show loading state
      const button = document.getElementById('resetPasswordButton');
      button.innerHTML = 'Resetting...';
      button.disabled = true;
      
      const email = document.getElementById('reset-email-final').value;
      const token = document.getElementById('reset-token').value;
      
      // Send request to server
      fetch('functions/reset_password.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'email=' + encodeURIComponent(email) + 
              '&token=' + encodeURIComponent(token) + 
              '&password=' + encodeURIComponent(password) + 
              '&confirm_password=' + encodeURIComponent(confirmPassword)
      })
      .then(response => response.json())
      .then(data => {
        button.innerHTML = 'Reset Password';
        button.disabled = false;
        
        if (data.status === 'success') {
          // Hide error message if visible
          document.getElementById('resetPasswordErrorMessage').classList.add('d-none');
          
          // Show success message and redirect to login
          alert(data.message);
          
          // Close modal and show login modal
          const resetPasswordModal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
          resetPasswordModal.hide();
          
          const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
          loginModal.show();
        } else {
          // Show error message
          const errorMessage = document.getElementById('resetPasswordErrorMessage');
          errorMessage.textContent = data.message;
          errorMessage.classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        button.innerHTML = 'Reset Password';
        button.disabled = false;
        
        const errorMessage = document.getElementById('resetPasswordErrorMessage');
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.classList.remove('d-none');
      });
    });
  }
  
  // Add Resend OTP Link functionality
  const resendOtpLink = document.getElementById('resendOtpLink');
  if (resendOtpLink) {
    resendOtpLink.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Disable the link
      resendOtpLink.style.pointerEvents = 'none';
      resendOtpLink.classList.add('text-muted');
      
      const email = document.getElementById('reset-email').value;
      
      // Send request to server
      fetch('functions/forgot_password.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'email=' + encodeURIComponent(email) + '&resend=1'
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Show success message
          const errorMessage = document.getElementById('otpErrorMessage');
          errorMessage.textContent = 'Verification code resent successfully!';
          errorMessage.classList.remove('alert-danger');
          errorMessage.classList.add('alert-success');
          errorMessage.classList.remove('d-none');
          
          // Start the OTP timer
          startOtpTimer();
          
          // Reset to error styling after a delay
          setTimeout(() => {
            errorMessage.classList.remove('alert-success');
            errorMessage.classList.add('alert-danger');
            errorMessage.classList.add('d-none');
          }, 3000);
        } else {
          // Show error message
          const errorMessage = document.getElementById('otpErrorMessage');
          errorMessage.textContent = data.message;
          errorMessage.classList.remove('d-none');
          
          // Enable the link again
          resendOtpLink.style.pointerEvents = '';
          resendOtpLink.classList.remove('text-muted');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        
        const errorMessage = document.getElementById('otpErrorMessage');
        errorMessage.textContent = 'An error occurred. Please try again later.';
        errorMessage.classList.remove('d-none');
        
        // Enable the link again
        resendOtpLink.style.pointerEvents = '';
        resendOtpLink.classList.remove('text-muted');
      });
    });
  }
});
</script>