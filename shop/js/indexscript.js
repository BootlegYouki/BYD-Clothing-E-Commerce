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

document.addEventListener('DOMContentLoaded', function() {
  // Initialize Product Swipers for mobile view
  const productSwipers = [
    { selector: '.new-release-swiper', id: 'newReleaseSwiper' },
    { selector: '.t-shirt-swiper', id: 'tShirtSwiper' },
    { selector: '.longsleeve-swiper', id: 'longsleeveSwiper' }
  ];
  
  // Only initialize if we're on mobile
  if (window.innerWidth < 768) {
    productSwipers.forEach(config => {
      const swiperElement = document.querySelector(config.selector);
      if (swiperElement) {
        window[config.id] = new Swiper(config.selector, {
          slidesPerView: 1, // Show exactly one slide
          spaceBetween: 0,
          centeredSlides: true, // Center the active slide
          loop: false, // Optional: set to true if you want infinite loop
          grabCursor: true,
          preventClicks: false, // Allow clicks on buttons
          preventClicksPropagation: false, // Allow click propagation
          touchReleaseOnEdges: true,
          touchStartPreventDefault: false,
          cssMode: true, // Better performance on iOS
          pagination: {
            el: `${config.selector} .swiper-pagination`,
            clickable: true,
          },
          scrollbar: {
            el: `${config.selector} .swiper-scrollbar`,
            draggable: true,
          },
          // Add navigation arrows for better UX (optional)
          navigation: {
            nextEl: `${config.selector} .swiper-button-next`,
            prevEl: `${config.selector} .swiper-button-prev`,
          }
        });
      }
    });
  }
  
  // Re-init on resize (optional)
  let resizeTimer;
  window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      const isMobile = window.innerWidth < 768;
      
      productSwipers.forEach(config => {
        const swiperElement = document.querySelector(config.selector);
        
        // If mobile and swiper doesn't exist, create it
        if (isMobile && window[config.id] === undefined && swiperElement) {
          window[config.id] = new Swiper(config.selector, {
            slidesPerView: 1, // Show exactly one slide
            spaceBetween: 0,
            centeredSlides: true, // Center the active slide
            loop: false,
            grabCursor: true,
            preventClicks: false,
            preventClicksPropagation: false,
            touchReleaseOnEdges: true,
            touchStartPreventDefault: false,
            cssMode: true,
            pagination: {
              el: `${config.selector} .swiper-pagination`,
              clickable: true,
            },
            scrollbar: {
              el: `${config.selector} .swiper-scrollbar`,
              draggable: true,
            },
            // Add navigation arrows for better UX (optional)
            navigation: {
              nextEl: `${config.selector} .swiper-button-next`,
              prevEl: `${config.selector} .swiper-button-prev`,
            }
          });
        }
        // If desktop and swiper exists, destroy it
        else if (!isMobile && window[config.id] !== undefined) {
          window[config.id].destroy(true, true);
          window[config.id] = undefined;
        }
      });
    }, 200);
  });
});