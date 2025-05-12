document.addEventListener('DOMContentLoaded', function () {
  // Initialize Swiper instances for mobile product views
  const initSwipers = () => {
    // New Release Swiper
    if (document.querySelector('.new-release-swiper')) {
      const newReleaseSwiper = new Swiper('.new-release-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
            spaceBetween: 20,
          }
        }
      });
    }
    
    // T-Shirt Swiper
    if (document.querySelector('.t-shirt-swiper')) {
      const tShirtSwiper = new Swiper('.t-shirt-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
            spaceBetween: 20,
          }
        }
      });
    }
    
    // Long sleeve Swiper
    if (document.querySelector('.longsleeve-swiper')) {
      const longsleeveSwiper = new Swiper('.longsleeve-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
            spaceBetween: 20,
          }
        }
      });
    }
    
    // Shop Swiper (matching the provided code)
    if (document.querySelector('.shop-swiper')) {
      const shopSwiper = new Swiper('.shop-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
            spaceBetween: 20,
          }
        }
      });
    }
  };
  
  // Initialize swiper components
  initSwipers();
  
  // Animation for products
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