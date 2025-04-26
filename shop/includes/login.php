<!-- LOGIN MODAL  -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h3 class="modal-title" id="loginModalLabel">Login</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Custom CSS to hide validation icons -->
            <style>
                .form-control.is-invalid, 
                .was-validated, 
                .form-control:invalid,
                .form-control.is-valid, 
                .was-validated, 
                .form-control:valid {
                background-image: none !important;
              }
              
              /* Fix for password toggle button position */
              .password-field-container {
                position: relative;
              }
              
              .password-toggle-btn-login {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                color: #6c757d; /* Bootstrap's default text color */
              }
              
              /* Adjust position when invalid feedback is shown */
              .form-control.is-invalid ~ .password-toggle-btn,
              .was-validated .form-control:invalid ~ .password-toggle-btn {
                top: calc(50% - 13px);
              }
            </style>
            
            <p>Don't have an account? 
              <a href="#SignupModal" data-bs-toggle="modal" data-bs-dismiss="modal" class="modal-link text-decoration-none">
                Sign up</a>
            </p>

            <div class="alert alert-danger mb-3 d-none" id="loginErrorMessage">
              Invalid Login Credentials. Please try again.
            </div>

            <form action="functions/authcode.php" method="POST" id="loginForm" class="needs-validation" novalidate>
              <div class="row gy-3 overflow-hidden">
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="loginidentifier" id="loginidentifier" placeholder="Email or Username" required>
                    <label for="loginidentifier" class="form-label">Email or Username</label>
                    <div class="invalid-feedback">
                      Please enter your email or username.
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3 password-field-container">
                    <input type="password" class="form-control" name="loginpassword" id="loginpassword" placeholder="Password" required>
                    <label for="loginpassword" class="form-label">Password</label>
                    <button type="button" class="password-toggle-btn-login" tabindex="-1">
                      <i class="fa-regular fa-eye-slash" aria-hidden="true"></i>
                    </button>
                    <div class="invalid-feedback">
                      Please enter your password.
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="d-grid">
                    <button type="submit" name="loginButton" id="loginButton" class="btn-modal btn-lg">Login now</button>
                  </div>
                </div>
              </div>
            </form>
            <div class="mt-4 text-end">
              <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal" class="modal-link text-decoration-none">Forgot password</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- FORGOT PASSWORD MODAL -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h3 class="modal-title" id="forgotPasswordModalLabel">Password Recovery</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Custom CSS to hide validation icons -->
        <style>
            .form-control.is-invalid, 
            .was-validated, 
            .form-control:invalid,
            .form-control.is-valid, 
            .was-validated, 
            .form-control:valid {
            background-image: none !important;
          }
        </style>
        
        <div class="alert alert-danger mb-3 d-none" id="forgotPasswordErrorMessage"></div>
        <div class="alert alert-success mb-3 d-none" id="forgotPasswordSuccessMessage"></div>

        <p>Enter your email address below to receive a password reset link.</p>
        <p class="text-muted small mb-3 text-center"><i class="fas fa-info-circle"></i> If you don't see the email, please check your spam or junk folder.</p>
        
        <form id="forgotPasswordForm" class="needs-validation" novalidate>
          <div class="row gy-3 overflow-hidden">
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="email" class="form-control" name="recovery_email" id="recovery_email" placeholder="Your Email" required>
                <label for="recovery_email" class="form-label">Email Address</label>
                <div class="invalid-feedback">
                  Please enter a valid email address.
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="d-grid">
                <button type="submit" name="recoverPasswordBtn" id="recoverPasswordBtn" class="btn-modal btn-lg">
                  <span class="normal-state">Send Recovery Link</span>
                  <span class="loading-state" style="display: none;">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Sending...
                  </span>
                </button>
              </div>
            </div>
          </div>
        </form>
        <div class="mt-4 text-center">
          <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal" class="modal-link text-decoration-none">Back to login</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
  // Add password visibility toggle functionality
  const passwordToggleBtn = document.querySelector('.password-toggle-btn-login');
  const passwordInput = document.getElementById('loginpassword');
  
  if (passwordToggleBtn && passwordInput) {
    passwordToggleBtn.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Toggle the eye icon
      const icon = this.querySelector('i');
      icon.classList.toggle('fa-eye-slash');
      icon.classList.toggle('fa-eye');
    });
  }
  
  // Handle login form submission with AJAX
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent the default form submission
      
      // Check if form is valid
      if (!loginForm.checkValidity()) {
        e.stopPropagation();
        loginForm.classList.add('was-validated');
        return;
      }

      // Show loading state
      document.getElementById('loginButton').innerHTML = 'Logging in...';
      document.getElementById('loginButton').disabled = true;
      
      // Get form data
      const loginIdentifier = document.getElementById('loginidentifier').value;
      const loginPassword = document.getElementById('loginpassword').value;
      
      // Create AJAX request
      fetch('functions/authcode.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'loginButton=1&loginidentifier=' + encodeURIComponent(loginIdentifier) + 
              '&loginpassword=' + encodeURIComponent(loginPassword)
      })
      .then(response => response.json())
      .then(data => {
        // Reset button state
        document.getElementById('loginButton').innerHTML = 'Login now';
        document.getElementById('loginButton').disabled = false;
        
        if (data.status === 'success') {
          // Login successful
          const errorMessage = document.getElementById('loginErrorMessage');
          errorMessage.classList.add('d-none');
          
          // Redirect or reload based on role
          if (data.role === 1) {
            // Admin user
            window.location.href = 'index.php?adminLogin=1';
          } else {
            // Regular user
            if (sessionStorage.getItem('redirectToCheckout') === 'true') {
              // Directly redirect to checkout if coming from cart
              sessionStorage.removeItem('redirectToCheckout');
              window.location.href = 'checkout.php';
            } else {
              window.location.href = 'index.php?loginSuccess=1';
            }
          }
        } else {
          // Login failed
          const errorMessage = document.getElementById('loginErrorMessage');
          errorMessage.textContent = data.message || 'Invalid Login Credentials. Please try again.';
          errorMessage.classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('loginButton').innerHTML = 'Login now';
        document.getElementById('loginButton').disabled = false;
        
        const errorMessage = document.getElementById('loginErrorMessage');
        errorMessage.textContent = 'An error occurred during login. Please try again.';
        errorMessage.classList.remove('d-none');
      });
    });
  }
  
  // Handle forgot password form submission
  const forgotPasswordForm = document.getElementById('forgotPasswordForm');
  if (forgotPasswordForm) {
      forgotPasswordForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          // Check if form is valid
          if (!forgotPasswordForm.checkValidity()) {
              e.stopPropagation();
              forgotPasswordForm.classList.add('was-validated');
              return;
          }
          
          // Show loading state
          const recoverBtn = document.getElementById('recoverPasswordBtn');
          recoverBtn.querySelector('.normal-state').style.display = 'none';
          recoverBtn.querySelector('.loading-state').style.display = 'inline-block';
          recoverBtn.disabled = true;
          
          // Hide previous messages
          document.getElementById('forgotPasswordErrorMessage').classList.add('d-none');
          document.getElementById('forgotPasswordSuccessMessage').classList.add('d-none');
          
          // Get form data
          const email = document.getElementById('recovery_email').value;
          
          // Create AJAX request
          fetch('functions/password_reset.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                  'X-Requested-With': 'XMLHttpRequest'
              },
              body: 'action=request_reset&email=' + encodeURIComponent(email)
          })
          .then(response => response.json())
          .then(data => {
              // Reset button state
              recoverBtn.querySelector('.normal-state').style.display = 'inline-block';
              recoverBtn.querySelector('.loading-state').style.display = 'none';
              recoverBtn.disabled = false;
              
              if (data.status === 'success') {
                  // Success message
                  const successMessage = document.getElementById('forgotPasswordSuccessMessage');
                  successMessage.textContent = data.message;
                  successMessage.classList.remove('d-none');
                  
                  // Reset form
                  forgotPasswordForm.reset();
                  forgotPasswordForm.classList.remove('was-validated');
              } else {
                  // Error message
                  const errorMessage = document.getElementById('forgotPasswordErrorMessage');
                  errorMessage.textContent = data.message;
                  errorMessage.classList.remove('d-none');
              }
          })
          .catch(error => {
              console.error('Error:', error);
              recoverBtn.querySelector('.normal-state').style.display = 'inline-block';
              recoverBtn.querySelector('.loading-state').style.display = 'none';
              recoverBtn.disabled = false;
              
              const errorMessage = document.getElementById('forgotPasswordErrorMessage');
              errorMessage.textContent = 'An error occurred. Please try again later.';
              errorMessage.classList.remove('d-none');
          });
      });
  }
  
  // Check if we should redirect to checkout after login
  if (<?php echo isset($_SESSION['auth_user']) ? 'true' : 'false'; ?> && 
      sessionStorage.getItem('redirectToCheckout') === 'true') {
    sessionStorage.removeItem('redirectToCheckout');
    window.location.href = 'checkout.php';
  }
});

const checkoutLogin = <?php echo isset($_GET['checkout_login']) ? 'true' : 'false'; ?>;
if (checkoutLogin) {
    sessionStorage.setItem('redirectToCheckout', 'true');
    
    // Display message about checkout login requirement
    const loginModalBody = document.querySelector('#loginModal .modal-body');
    if (loginModalBody) {
        const checkoutMessage = document.createElement('div');
        checkoutMessage.id = 'checkout-login-message';
        checkoutMessage.className = 'alert alert-info mb-3';
        checkoutMessage.innerHTML = '<i class="fas fa-info-circle me-2"></i> Please log in to continue with checkout.';
        loginModalBody.insertBefore(checkoutMessage, loginModalBody.firstChild);
        
        // Auto-hide message after 5 seconds
        setTimeout(() => {
            checkoutMessage.style.transition = 'opacity 0.5s';
            checkoutMessage.style.opacity = '0';
            setTimeout(() => {
                if (checkoutMessage.parentNode) {
                    checkoutMessage.remove();
                }
            }, 500);
        }, 5000);
    }
    
    // Show login modal
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
}
</script>
<script src="js/url-cleaner.js"></script>