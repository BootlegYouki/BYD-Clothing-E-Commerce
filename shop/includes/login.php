    <!-- LOGIN MODAL  -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h3 class="modal-title" id="loginModalLabel">Login</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" 
            onclick="window.location.href='index.php'"></button>
          </div>
          <div class="modal-body">
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
                  <div class="form-floating mb-1 position-relative">
                    <input type="password" class="form-control" name="loginpassword" id="loginpassword" placeholder="Password" required>
                    <label for="loginpassword" class="form-label">Password</label>
                    <div class="invalid-feedback">
                      Please enter your password.
                    </div>
                  </div>
                </div>
                <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input toggle-password" type="checkbox" id="show_password">
                  <label class="form-check-label text-secondary" for="show_password">
                    Show Password
                  </label>
                </div>
                </div>
                <div class="col-12">
                  <div class="d-grid">
                    <button type="submit" name="loginButton" class="btn-modal btn-lg">Login now</button>
                  </div>
                </div>
              </div>
            </form>
            <div class="mt-4 text-end">
              <a href="#!" class="modal-link text-decoration-none">Forgot password</a>
            </div>
          </div>
        </div>
      </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Get the password toggle checkbox
  const togglePassword = document.getElementById('show_password');
  
  // Get the password input
  const passwordInput = document.getElementById('loginpassword');
  
  // Add event listener to checkbox
  if(togglePassword && passwordInput) {
    togglePassword.addEventListener('change', function() {
      // Change the password input type based on checkbox state
      passwordInput.type = this.checked ? 'text' : 'password';
    });
  }
});
</script>