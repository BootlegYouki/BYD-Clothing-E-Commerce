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

            <form id="loginForm" class="needs-validation" novalidate>
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
                    <button type="submit" name="loginButton" id="loginButton" class="btn-modal btn-lg">
                      <span class="normal-state">Login now</span>
                      <span class="loading-state" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Logging in...
                      </span>
                    </button>
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
          
          /* Style for disabled button during countdown */
          button:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            position: relative;
          }
          
          /* Add a visual overlay effect when button is disabled */
          button.btn-modal:disabled::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: inherit;
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
        
        <!-- Timer display - initially hidden -->
        <div id="resetTimerContainer" class="alert alert-info mb-3 text-center d-none mt-3">
          <i class="fas fa-clock me-2"></i> You can request another reset link in <span id="resetTimer">30</span> seconds
        </div>
        
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
      const loginBtn = document.getElementById('loginButton');
      loginBtn.querySelector('.normal-state').style.display = 'none';
      loginBtn.querySelector('.loading-state').style.display = 'inline-block';
      loginBtn.disabled = true;
      
      // Get form data
      const loginIdentifier = document.getElementById('loginidentifier').value;
      const loginPassword = document.getElementById('loginpassword').value;
      
      // Create AJAX request
      fetch('functions/account/authcode.php', {
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
        loginBtn.querySelector('.normal-state').style.display = 'inline-block';
        loginBtn.querySelector('.loading-state').style.display = 'none';
        loginBtn.disabled = false;
        
        if (data.status === 'success') {
          // Login successful - hide login modal
          const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
          loginModal.hide();
          
          // Hide any error messages
          const errorMessage = document.getElementById('loginErrorMessage');
          errorMessage.classList.add('d-none');
          
          // Update the header with username and role
          const username = data.username || loginIdentifier;
          const isAdmin = data.role === 1;
          
          // Call the header update function if it exists
          if (typeof window.updateHeaderAfterAuth === 'function') {
            window.updateHeaderAfterAuth(username, isAdmin);
            
            // Ensure notifications are initialized after header update
            if (!isAdmin && typeof window.initHeaderNotifications === 'function') {
              setTimeout(() => window.initHeaderNotifications(), 500);
            }
          }
          
          // Dispatch event to trigger notification check after login
          document.dispatchEvent(new CustomEvent('userLoggedIn', { 
            detail: { username: username, isAdmin: isAdmin } 
          }));
          
          if (data.role === 1) {
            // Admin user - show admin success modal directly
            const adminLoginModal = new bootstrap.Modal(document.getElementById('adminLoginSuccessModal'));
            // Set the adminLoginShown flag before showing modal
            sessionStorage.setItem('adminLoginShown', 'true');
            adminLoginModal.show();
          } else {
            // Regular user - show login success modal directly
            if (sessionStorage.getItem('redirectToCheckout') === 'true') {
              // Directly redirect to checkout if coming from cart
              sessionStorage.removeItem('redirectToCheckout');
              window.location.href = 'checkout.php';
            } else {
              const loginSuccessModal = new bootstrap.Modal(document.getElementById('loginsuccessmodal'));
              // Update username in the modal if function exists
              if (typeof window.updateLoginSuccessUsername === 'function') {
                window.updateLoginSuccessUsername(username);
              }
              loginSuccessModal.show();
            }
          }
        } else {
          // Login failed
          const errorMessage = document.getElementById('loginErrorMessage');
          errorMessage.textContent = 'Invalid Login Credentials. Please try again.';
          errorMessage.classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        loginBtn.querySelector('.normal-state').style.display = 'inline-block';
        loginBtn.querySelector('.loading-state').style.display = 'none';
        loginBtn.disabled = false;
        
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
          fetch('functions/account/password_reset.php', {
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
                  
                  // Start the 30 second timer
                  startResetTimer();
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

// Timer function for password reset
function startResetTimer() {
  const timerElement = document.getElementById('resetTimer');
  const timerContainer = document.getElementById('resetTimerContainer');
  const recoverBtn = document.getElementById('recoverPasswordBtn');
  let timeLeft = 30;
  
  // Show timer container
  timerContainer.classList.remove('d-none');
  
  // Disable the button and add tooltip
  recoverBtn.disabled = true;
  recoverBtn.title = "Please wait for the cooldown period to end";
  
  // No need to change the button text - keep it consistent
  const normalStateSpan = recoverBtn.querySelector('.normal-state');
  
  // Set the interval for countdown
  const timerInterval = setInterval(function() {
    timeLeft--;
    timerElement.textContent = timeLeft;
    
    if (timeLeft <= 0) {
      // Clear the interval when timer reaches 0
      clearInterval(timerInterval);
      
      // Enable the button and hide the timer container
      recoverBtn.disabled = false;
      recoverBtn.removeAttribute('title');
      timerContainer.classList.add('d-none');
    }
  }, 1000);
}
</script>
<script src="js/url-cleaner.js"></script>