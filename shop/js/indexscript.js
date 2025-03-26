// PRODUCT ANIMATION WHEN FIRST LOADED
document.addEventListener('DOMContentLoaded', function () {
  const products = document.querySelectorAll('.product');

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });

  products.forEach(product => observer.observe(product));
});

// PHONE NUMBER COPY TO CLIPBOARD IN FOOTER
function copyPhoneNumber() {
  const phone = "0905 507 9634";
  navigator.clipboard.writeText(phone)
      .then(() => {
          alert('Phone number copied to clipboard');
      })
      .catch(err => {
          alert('Failed to copy text: ' + err);
      });
}

// PASSWORD VALIDATION
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
  
  const params = new URLSearchParams(window.location.search);
  if (params.get('signupSuccess') === '1') {
    var registerSuccessModal = new bootstrap.Modal(document.getElementById('registersuccessmodal'));
    registerSuccessModal.show();
  }

  if (params.get('loginSuccess') === '1') {
    var loginSuccessModal = new bootstrap.Modal(document.getElementById('loginsuccessmodal'));
    loginSuccessModal.show();
  }

  if (params.get('loginFailed') === '1') {
    var failedModal = new bootstrap.Modal(document.getElementById('failedModal'));
    failedModal.show();
  }
});