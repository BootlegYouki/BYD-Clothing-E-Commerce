// PRODUCT ANIMATION WHEN FIRST LOADED
// PRODUCT ANIMATION WHEN FIRST LOADED
document.addEventListener('DOMContentLoaded', function () {
  // Add some delay to ensure DOM is fully processed
  setTimeout(() => {
    // Select all product cards
    const products = document.querySelectorAll('.product-card');
    
    if (products.length > 0) {
      console.log(`Found ${products.length} product cards`);
      
      // Create intersection observer
      const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            console.log('Product visible:', entry.target);
            entry.target.classList.add('animate');
            observer.unobserve(entry.target);
          }
        });
      }, { 
        threshold: 0.1,
        rootMargin: '0px 0px -10% 0px' 
      });
      
      // Observe each product card
      products.forEach((product, index) => {
        console.log(`Observing product ${index}`);
        // Add initial class before animation
        product.classList.add('product-before-animate');
        // Start observing
        observer.observe(product);
      });
    } else {
      console.warn('No product cards found on page');
    }
  }, 100);
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
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();
    document.getElementById('loginErrorMessage').classList.remove('d-none');
  }
