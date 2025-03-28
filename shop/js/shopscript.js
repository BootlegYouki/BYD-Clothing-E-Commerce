// Product Display
  
  
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


  