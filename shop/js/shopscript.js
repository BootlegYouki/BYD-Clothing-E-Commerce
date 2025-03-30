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

document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for quickview buttons
    document.querySelectorAll('.quickview').forEach(button => {
        button.addEventListener('click', function() {
            // Get product data from button attributes
            const productModal = document.getElementById('productModal');
            const modalImage = document.getElementById('modalProductImage');
            const modalTitle = document.getElementById('modalProductTitle');
            const modalPrice = document.getElementById('modalProductPrice');

            // Update modal content with product data
            modalImage.src = this.dataset.img;
            modalTitle.textContent = this.dataset.title;
            modalPrice.textContent = this.dataset.price;

            // Create and show modal using Bootstrap
            const modal = new bootstrap.Modal(productModal);
            modal.show();
        });
    });
});

// Function to toggle product details
function toggleProductDetails(button) {
    const productDetails = document.getElementById('productDetails');
    const detailImg = productDetails.querySelector('.detail-img');
    const detailTitle = productDetails.querySelector('.detail-title');
    const detailPrice = productDetails.querySelector('.detail-price');
    const card = productDetails.querySelector('.card');
    
    // Store product data in the modal for later use by the cart
    productDetails.dataset.productId = button.dataset.id;
    productDetails.dataset.productImage = button.dataset.img;
    productDetails.dataset.productTitle = button.dataset.title;
    productDetails.dataset.productPrice = button.dataset.price;
    
    // Set the modal content
    detailImg.src = button.dataset.img;
    detailTitle.textContent = button.dataset.title;
    detailPrice.textContent = button.dataset.price;
    
    // Reset animation classes
    card.classList.remove('animate-slide-up');
    card.classList.add('animate-slide-down');
    
    // Show the modal
    productDetails.classList.add('show');
    document.body.classList.add('modal-open');
    
    // Set focus to the product details container
    setTimeout(() => {
        productDetails.focus();
        
        // Scroll to the product details
        productDetails.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }, 100);
}

// Function to close product details
function closeProductDetails() {
    const productDetails = document.getElementById('productDetails');
    const card = productDetails.querySelector('.card');
    
    // Add slide up animation
    card.classList.remove('animate-slide-down');
    card.classList.add('animate-slide-up');
    
    // Get the product ID to focus back on the original product
    const productId = productDetails.dataset.productId;
    const originalProduct = document.querySelector(`.product[data-product-id="${productId}"]`);
    
    // Wait for animation to complete before hiding
    setTimeout(() => {
        productDetails.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Focus back on the original product
        if (originalProduct) {
            originalProduct.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            originalProduct.focus();
            
            // Add a temporary highlight effect
            originalProduct.classList.add('highlight-product');
            setTimeout(() => {
                originalProduct.classList.remove('highlight-product');
            }, 1000);
        }
    }, 450); // Slightly less than animation duration
}


