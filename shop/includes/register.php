<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<div class="modal fade" id="SignupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true" aria-modal="true">
      <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h3 class="modal-title" id="signupModalLabel">Sign Up</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Custom CSS for password toggle buttons -->
        <style>
          .password-field-container {
            position: relative;
          }
          
          .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
          }
        </style>
        
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
              <div class="form-floating mb-1">
                <input type="text" class="form-control" name="full_address" id="full_address" placeholder="Full Address" required>
                <label for="full_address" class="form-label">Full Address</label>
                <div class="invalid-feedback">
                  Please provide your address.
                </div>
              </div>
              <div id="map" style="height: 300px; display: none;" class="rounded mb-3"></div>
              <input type="hidden" id="latitude" name="latitude">
              <input type="hidden" id="longitude" name="longitude">
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
              <div class="form-floating mb-3 password-field-container">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required minlength="8">
                <label for="password" class="form-label">Password</label>
                <button type="button" class="password-toggle-btn" id="passwordToggleBtn" tabindex="-1">
                  <i class="fa-regular fa-eye-slash" aria-hidden="true"></i>
                </button>
                <div class="invalid-feedback">
                  Password must be at least 8 characters.
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3 password-field-container">
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <button type="button" class="password-toggle-btn" tabindex="-1">
                  <i class="fa-regular fa-eye-slash" aria-hidden="true"></i>
                </button>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const addressInput = document.getElementById('full_address');
  const mapDiv       = document.getElementById('map');
  const latInput     = document.getElementById('latitude');
  const lngInput     = document.getElementById('longitude');
  const zipcodeInput = document.getElementById('zipcode');

  // 1) Initialize map and tile layer
  const map = L.map('map').setView([14.5995, 120.9842], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  // 2) Add a draggable marker
  const marker = L.marker([14.5995, 120.9842], { draggable: true }).addTo(map);

  // 3) Helper text under the address field
  const formFloating = addressInput.closest('.form-floating');
  const helpText = document.createElement('div');
  helpText.className = 'form-text text-muted small';
  helpText.innerText = 'Start typing your address; the map will update automatically.';
  formFloating.insertAdjacentElement('afterend', helpText);

  // 4) Geocoder control (no default marker)
  const geocoder = L.Control.geocoder({
    defaultMarkGeocode: false,
    geocoder: L.Control.Geocoder.nominatim(),
    placeholder: 'Search address...'
  }).on('markgeocode', function(e) {
    marker.setLatLng(e.geocode.center);
    map.setView(e.geocode.center, 16);
    updateCoordinates(e.geocode.center.lat, e.geocode.center.lng);
    fetchZipcode(e.geocode.center.lat, e.geocode.center.lng);
  }).addTo(map);

  // 5) Core helper functions
  function updateCoordinates(lat, lng) {
  latInput.value = lat;
  lngInput.value = lng;
}

  function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
      .then(r => r.json())
      .then(data => {
        if (data && data.display_name) addressInput.value = data.display_name;
      })
      .catch(console.error);
  }

  function fetchZipcode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
      .then(r => r.json())
      .then(data => {
        if (data && data.address && data.address.postcode) {
          zipcodeInput.value = data.address.postcode;
        }
      })
      .catch(console.error);
  }

  // 6) Map click => move marker + reverse geocode + zipcode
  map.on('click', e => {
    marker.setLatLng(e.latlng);
    updateCoordinates(e.latlng.lat, e.latlng.lng);
    reverseGeocode(e.latlng.lat, e.latlng.lng);
    fetchZipcode(e.latlng.lat, e.latlng.lng);
  });

  // 7) Marker drag end => same as click
  marker.on('dragend', () => {
    const pos = marker.getLatLng();
    updateCoordinates(pos.lat, pos.lng);
    reverseGeocode(pos.lat, pos.lng);
    fetchZipcode(pos.lat, pos.lng);
  });

  // 8) Show map when focusing the address field
  addressInput.addEventListener('focus', () => {
    if (mapDiv.style.display === 'none') {
      mapDiv.style.display = 'block';
      map.invalidateSize();
    }
  });

  // 9) Debounced auto-search as you type
  let typingTimer;
  const doneTypingInterval = 200; // ms

  addressInput.addEventListener('keydown', () => clearTimeout(typingTimer));
  addressInput.addEventListener('input', function() {
    clearTimeout(typingTimer);
    const val = this.value.trim();

    // ensure map is visible
    if (mapDiv.style.display === 'none') {
      mapDiv.style.display = 'block';
      map.invalidateSize();
    }

    if (val.length > 2) {
      typingTimer = setTimeout(() => {
        // Use Nominatim API directly for address search
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(val)}&limit=1`)
          .then(response => response.json())
          .then(data => {
            if (data && data.length > 0) {
              const result = data[0];
              const lat = parseFloat(result.lat);
              const lng = parseFloat(result.lon);
              const latlng = L.latLng(lat, lng);
              
              map.setView(latlng, 16);
              marker.setLatLng(latlng);
              updateCoordinates(lat, lng);
              fetchZipcode(lat, lng);
            }
          })
          .catch(console.error);
      }, doneTypingInterval);
    }
  });

  // 10) Fallback on change (paste + blur)
  addressInput.addEventListener('change', function() {
    clearTimeout(typingTimer);
    const val = this.value.trim();
    if (!val) return;
    
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(val)}&limit=1`)
      .then(response => response.json())
      .then(data => {
        if (data && data.length > 0) {
          const result = data[0];
          const lat = parseFloat(result.lat);
          const lng = parseFloat(result.lon);
          const latlng = L.latLng(lat, lng);
          
          map.setView(latlng, 16);
          marker.setLatLng(latlng);
          updateCoordinates(lat, lng);
          fetchZipcode(lat, lng);
        }
      })
      .catch(console.error);
  });
});

document.addEventListener("DOMContentLoaded", function() {
  const regpassword = document.getElementById('password');
  const confirmPassword = document.getElementById('confirm_password');

  // Get individual password toggle buttons
  const passwordToggleBtn = document.getElementById('passwordToggleBtn');
  const confirmPasswordToggleBtn = document.querySelector('.col-md-6:nth-child(2) .password-toggle-btn');
  
  // Handle password field toggle separately (works with eye-slash for visible, eye for hidden)
  if (passwordToggleBtn) {
    passwordToggleBtn.addEventListener('click', function() {
      const passwordField = document.getElementById('password');
      if (passwordField.type === 'password') {
        // Show password
        passwordField.type = 'text';
        this.innerHTML = '<i class="fa-regular fa-eye" aria-hidden="true"></i>';
      } else {
        // Hide password
        passwordField.type = 'password';
        this.innerHTML = '<i class="fa-regular fa-eye-slash" aria-hidden="true"></i>';
      }
    });
  }
  
  // Handle confirm password field toggle separately (works with opposite logic)
  if (confirmPasswordToggleBtn) {
    confirmPasswordToggleBtn.addEventListener('click', function() {
      const confirmField = document.getElementById('confirm_password');
      if (confirmField.type === 'password') {
        // Show password
        confirmField.type = 'text';
        this.innerHTML = '<i class="fa-regular fa-eye" aria-hidden="true"></i>';
      } else {
        // Hide password
        confirmField.type = 'password';
        this.innerHTML = '<i class="fa-regular fa-eye-slash" aria-hidden="true"></i>';
      }
    });
  }

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