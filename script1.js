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
  
  // Modal Update Logic
  document.addEventListener('DOMContentLoaded', () => {
    const productModal = document.getElementById('productModal');
    
    productModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        document.getElementById('modalProductImage').src = button.dataset.img;
        document.getElementById('modalProductTitle').textContent = button.dataset.title;
        document.getElementById('modalProductPrice').textContent = button.dataset.price;
    });
  
    // Lazy Loading for images
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                observer.unobserve(img);
            }
        });
    });
  
    lazyImages.forEach(img => {
        if (!img.src) img.src = img.dataset.src;
        observer.observe(img);
    });
  });