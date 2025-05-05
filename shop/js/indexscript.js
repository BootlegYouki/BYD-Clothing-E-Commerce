document.addEventListener('DOMContentLoaded', function () {
  setTimeout(() => {
    const products = document.querySelectorAll('.product-card');
    
    if (products.length > 0) {
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
      
      products.forEach((product) => {
        product.classList.add('product-before-animate');
        observer.observe(product);
      });
    }
  }, 300);

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
      }
      
      document.body.removeChild(tempInput);
      
      if (success) {
        alert('Phone number copied to clipboard');
      } else {
        alert('Please copy this number manually: ' + phone);
      }
    }
  };
});