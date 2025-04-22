<div class="modal fade" id="SignupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true" aria-modal="true">
      <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h3 class="modal-title" id="signupModalLabel">Sign Up</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Already have an account? <a href="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal" class="modal-link text-decoration-none">Log in</a></p>
        <form action="functions/authcode.php" method="POST" id="signupForm" class="needs-validation" novalidate> 
          <div class="row gy-3">
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" required>
                <label for="firstname" class="form-label">First Name</label>
                <div class="invalid-feedback">
                  Please enter your first name.
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" class="form-control" name="middlename" id="middlename" placeholder="Middle Name">
                <label for="middlename" class="form-label">Middle Name</label>
                <div class="valid-feedback">
                  Middle Name is optional.
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name" required>
                <label for="lastname" class="form-label">Last Name</label>
                <div class="invalid-feedback">
                  Please enter your last name.
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="tel" class="form-control" name="phone_number" id="phone_number" placeholder="Phone Number" required>
                <label for="phone_number" class="form-label">Phone Number</label>
                <div class="invalid-feedback" id="phoneInvalidFeedback">
                  Please enter your phone number.
                </div>
                <div class="invalid-feedback d-none" id="phoneRegisteredFeedback">
                  Phone number already registered.
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-12">
              <div class="form-floating mb-3">
                <input type="email" class="form-control" name="regemail" id="regemail" placeholder="name@example.com" required>
                <label for="regemail" class="form-label">Email</label>
                <div class="invalid-feedback" id="emailInvalidFeedback">
                  Please enter a valid email.
                </div>
                <div class="invalid-feedback d-none" id="emailRegisteredFeedback">
                  Email already registered.
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-12">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                <label for="username" class="form-label">Username</label>
                <div class="invalid-feedback" id="usernameInvalidFeedback">
                  Please enter your username.
                </div>
                <div class="invalid-feedback d-none" id="usernameTakenFeedback">
                  Username taken.
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="full_address" id="full_address" placeholder="Full Address" required>
                <label for="full_address" class="form-label">Full Address</label>
                <div class="invalid-feedback">
                  Please provide your address.
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" name="zipcode" id="zipcode" placeholder="Zipcode" required>
                <label for="zipcode" class="form-label">Zipcode</label>
                <div class="invalid-feedback">
                  Please enter your zipcode.
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required minlength="8">
                <label for="password" class="form-label">Password</label>
                <div class="invalid-feedback">
                  Password must be at least 8 characters.
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="invalid-feedback">
                  Passwords don't match.
                </div>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" name="agree_terms" id="agree_terms" required>
              <label class="form-check-label text-secondary" for="agree_terms">
                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="modal-link text-decoration-none">Terms & Conditions</a> and 
                <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="modal-link text-decoration-none">Privacy Policy</a>.
              </label>
              <div class="invalid-feedback">
                You must agree before submitting.
              </div>
            </div>
          </div>
          <div class="col-12 mt-3">
            <div class="d-grid">
            <button type="submit" name="signupButton" class="btn-modal btn-lg" id="signupButton">Sign up now</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const regpassword = document.getElementById('password');
  const confirmPassword = document.getElementById('confirm_password');

  // Check password requirements as user types in the password field
  regpassword.addEventListener('input', function() {
      if (!regpassword.value) {
          regpassword.setCustomValidity("");
          regpassword.classList.remove("is-invalid", "is-valid");
      }
      else if (regpassword.value.length < 8) {
          regpassword.setCustomValidity("Password must be at least 8 characters long");
          regpassword.classList.add("is-invalid");
          regpassword.classList.remove("is-valid");
      } else {
          regpassword.setCustomValidity("");
          regpassword.classList.remove("is-invalid");
          regpassword.classList.add("is-valid");
      }

      // Validate confirm password if not empty
      if (confirmPassword.value !== "") {
          if (regpassword.value !== confirmPassword.value) {
              confirmPassword.setCustomValidity("Passwords do not match");
              confirmPassword.classList.add("is-invalid");
              confirmPassword.classList.remove("is-valid");
          } else {
              confirmPassword.setCustomValidity("");
              confirmPassword.classList.remove("is-invalid");
              confirmPassword.classList.add("is-valid");
          }
      }
  });

  // Check that both passwords match in real time
  confirmPassword.addEventListener('input', function() {
      if (!confirmPassword.value) {
          confirmPassword.setCustomValidity("");
          confirmPassword.classList.remove("is-invalid", "is-valid");
      }
      else if (regpassword.value !== confirmPassword.value) {
          confirmPassword.setCustomValidity("Passwords do not match");
          confirmPassword.classList.add("is-invalid");
          confirmPassword.classList.remove("is-valid");
      } else {
          confirmPassword.setCustomValidity("");
          confirmPassword.classList.remove("is-invalid");
          confirmPassword.classList.add("is-valid");
      }
  });
});

// SIGN UP MODAL FORM VALIDATION (Bootstrap custom validation)
(function () {
  'use strict'
  window.addEventListener('load', function () {
    const forms = document.getElementsByClassName('needs-validation');

    Array.prototype.forEach.call(forms, function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

// REAL-TIME EMAIL, PHONE NUMBER, USERNAME VALIDATION
document.addEventListener("DOMContentLoaded", function() {
  const emailInput = document.getElementById('regemail');
  if(emailInput) {
    emailInput.addEventListener("blur", function() {
      const emailVal = emailInput.value.trim();
      if(emailVal !== "") {
        fetch("functions/check_email.php?email=" + encodeURIComponent(emailVal))
          .then(response => response.json())
          .then(data => {
            const feedback = document.getElementById('emailRegisteredFeedback');
            const invalidFeedback = document.getElementById('emailInvalidFeedback');
            if(data.exists) {
              emailInput.setCustomValidity("This email is already registered");
              feedback.classList.remove("d-none");
              emailInput.classList.add("is-invalid");
              if(invalidFeedback) invalidFeedback.classList.add("d-none");
            } else {
              emailInput.setCustomValidity("");
              feedback.classList.add("d-none");
              emailInput.classList.remove("is-invalid");
              if(invalidFeedback) invalidFeedback.classList.remove("d-none");
            }
          })
          .catch(error => console.error("Error checking email:", error));
      } else {
        emailInput.setCustomValidity("");
      }
    });
  }
  
  // Real-time Phone Number Validation
  const phoneInput = document.getElementById('phone_number');
  if(phoneInput) {
    phoneInput.addEventListener("blur", function() {
      const phoneVal = phoneInput.value.trim();
      if(phoneVal !== "") {
        fetch("functions/check_phone.php?phone_number=" + encodeURIComponent(phoneVal))
          .then(response => response.json())
          .then(data => {
            const feedback = document.getElementById('phoneRegisteredFeedback');
            const invalidFeedback = document.getElementById('phoneInvalidFeedback');
            if(data.exists) {
              phoneInput.setCustomValidity("This phone number is already registered");
              feedback.classList.remove("d-none");
              phoneInput.classList.add("is-invalid");
              if(invalidFeedback) invalidFeedback.classList.add("d-none");
            } else {
              phoneInput.setCustomValidity("");
              feedback.classList.add("d-none");
              phoneInput.classList.remove("is-invalid");
              if(invalidFeedback) invalidFeedback.classList.remove("d-none");
            }
          })
          .catch(error => console.error("Error checking phone number:", error));
      } else {
        phoneInput.setCustomValidity("");
      }
    });
  }
  
  // Real-time Username Validation
  const usernameInput = document.getElementById('username');
  if(usernameInput) {
    usernameInput.addEventListener("blur", function() {
      const usernameVal = usernameInput.value.trim();
      if(usernameVal !== "") {
        fetch("functions/check_username.php?username=" + encodeURIComponent(usernameVal))
          .then(response => response.json())
          .then(data => {
            const feedback = document.getElementById('usernameTakenFeedback');
            const invalidFeedback = document.getElementById('usernameInvalidFeedback');
            if(data.exists) {
              usernameInput.setCustomValidity("This username is already taken");
              feedback.classList.remove("d-none");
              usernameInput.classList.add("is-invalid");
              if(invalidFeedback) invalidFeedback.classList.add("d-none");
            } else {
              usernameInput.setCustomValidity("");
              feedback.classList.add("d-none");
              usernameInput.classList.remove("is-invalid");
              if(invalidFeedback) invalidFeedback.classList.remove("d-none");
            }
          })
          .catch(error => console.error("Error checking username:", error));
      } else {
        usernameInput.setCustomValidity("");
      }
    });
  }
});
</script>

<script src="js/url-cleaner.js"></script>
