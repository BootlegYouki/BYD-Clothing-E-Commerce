    <!-- LOGIN MODAL  -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content rounded-4">
          <div class="modal-header">
            <h3 class="modal-title" id="loginModalLabel">Login</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Don't have an account? 
              <a href="#SignupModal" data-bs-toggle="modal" data-bs-dismiss="modal" class="modal-link text-decoration-none">
                Sign up</a>
            </p>
            <form action="functions/authcode.php" method="POST" id="loginForm" class="needs-validation" novalidate>
              <div class="row gy-3 overflow-hidden">
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="loginemail" id="loginemail" placeholder="name@example.com" required>
                    <label for="loginemail" class="form-label">Email</label>
                    <div class="invalid-feedback">
                      Please enter your email.
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-1">
                    <input type="password" class="form-control" name="loginpassword" id="loginpassword" placeholder="Password" required>
                    <label for="loginpassword" class="form-label">Password</label>
                    <div class="invalid-feedback">
                      Please enter your password.
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="remember_me" id="remember_me">
                    <label class="form-check-label text-secondary" for="remember_me">
                      Keep me logged in
                    </label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="d-grid">
                    <button type="submit" name="loginButton" class="btn-modal btn btn-primary btn-lg">Login now</button>
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