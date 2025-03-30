document.addEventListener('DOMContentLoaded', function () {
  // PRODUCT ANIMATION WHEN FIRST LOADED
  setTimeout(() => {
    const products = document.querySelectorAll('.product-card');
    
    if (products.length > 0) {
      // Create intersection observer
      const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate');
            observer.unobserve(entry.target);
          }
        });
      }, { 
        threshold: 0.1,
        rootMargin: '0px 0px -10% 0px' 
      });
      
      // Observe each product card
      products.forEach((product) => {
        // Add initial class before animation
        product.classList.add('product-before-animate');
        // Start observing
        observer.observe(product);
      });
    }
  }, 300);

  // PHONE NUMBER COPY TO CLIPBOARD IN FOOTER
  window.copyPhoneNumber = function() {
    const phone = "0905 507 9634";
    
    if (navigator.clipboard) {
      navigator.clipboard.writeText(phone)
        .then(() => {
          alert('Phone number copied to clipboard');
        })
        .catch(() => {
          fallbackCopy();
        });
    } else {
      fallbackCopy();
    }
    
    function fallbackCopy() {
      const tempInput = document.createElement('input');
      tempInput.value = phone;
      document.body.appendChild(tempInput);
      tempInput.select();
      
      let success = false;
      try {
        success = document.execCommand('copy');
      } catch (e) {
        // Silent catch
      }
      
      document.body.removeChild(tempInput);
      
      if (success) {
        alert('Phone number copied to clipboard');
      } else {
        alert('Please copy this number manually: ' + phone);
      }
    }
  };
  
  // URL PARAMETER PROCESSING
  try {
    const params = new URLSearchParams(window.location.search);
    
    // Handle signup success
    if (params.get('signupSuccess') === '1') {
      const modalElement = document.getElementById('registersuccessmodal');
      if (modalElement) {
        const registerSuccessModal = new bootstrap.Modal(modalElement);
        registerSuccessModal.show();
      }
    }

    // Handle login success
    if (params.get('loginSuccess') === '1') {
      const modalElement = document.getElementById('loginsuccessmodal');
      if (modalElement) {
        const loginSuccessModal = new bootstrap.Modal(modalElement);
        loginSuccessModal.show();
      }
    }

    // Handle login failed
    if (params.get('loginFailed') === '1') {
      const modalElement = document.getElementById('loginModal');
      if (modalElement) {
        const loginModal = new bootstrap.Modal(modalElement);
        loginModal.show();
        
        const errorMessageElement = document.getElementById('loginErrorMessage');
        if (errorMessageElement) {
          errorMessageElement.classList.remove('d-none');
        }
      }
    }
  } catch (e) {
    // Silent catch
  }
});