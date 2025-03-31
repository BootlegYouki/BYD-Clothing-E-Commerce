// Product Display
  
// Lazy Loading for images
document.addEventListener('DOMContentLoaded', () => {
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
    
    // Add event listener for the collapse element
    const productQuickView = document.getElementById('productQuickView');
    if (productQuickView) {
        productQuickView.addEventListener('show.bs.collapse', function () {
            // Smooth scroll to the quick view after a short delay
            setTimeout(() => {
                this.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        });
        
        // Size buttons functionality
        const sizeButtons = productQuickView.querySelectorAll('.btn-size');
        const hiddenSizeInput = document.getElementById('quick-view-size');
        
        // Add click handlers to size buttons
        sizeButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                sizeButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Update the hidden input with the selected size
                if (hiddenSizeInput) {
                    hiddenSizeInput.value = button.dataset.size;
                }
            });
        });
        
        // Quantity selector functionality
        const quantityInput = document.getElementById('quick-view-quantity');
        const minusBtn = document.getElementById('quick-view-quantity-minus');
        const plusBtn = document.getElementById('quick-view-quantity-plus');
        
        if (quantityInput && minusBtn && plusBtn) {
            // Decrease quantity
            minusBtn.addEventListener('click', () => {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = --currentValue;
                    updateQuantityButtonStates();
                }
            });
            
            // Increase quantity
            plusBtn.addEventListener('click', () => {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue < 10) {
                    quantityInput.value = ++currentValue;
                    updateQuantityButtonStates();
                }
            });
            
            // Update button states based on quantity
            function updateQuantityButtonStates() {
                const currentValue = parseInt(quantityInput.value);
                
                // Disable minus button when quantity is 1
                if (currentValue <= 1) {
                    minusBtn.classList.add('disabled');
                } else {
                    minusBtn.classList.remove('disabled');
                }
                
                // Disable plus button when quantity is 10 (max)
                if (currentValue >= 10) {
                    plusBtn.classList.add('disabled');
                } else {
                    plusBtn.classList.remove('disabled');
                }
            }
            
            // Initialize button states
            updateQuantityButtonStates();
        }
        
        // Add to cart button handler
        const addToCartBtn = document.getElementById('quick-view-add-to-cart');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const selectedSize = hiddenSizeInput ? hiddenSizeInput.value : 'M';
                const quantitySelect = document.getElementById('quick-view-quantity');
                const selectedQuantity = quantitySelect ? parseInt(quantitySelect.value) : 1;
                
                // Get product data from data attributes
                const product = {
                    id: productQuickView.dataset.productId,
                    title: productQuickView.dataset.productTitle,
                    image: productQuickView.dataset.productImage,
                    price: productQuickView.dataset.discountedPrice || productQuickView.dataset.originalPrice,
                    size: selectedSize,
                    quantity: selectedQuantity
                };
                
                console.log('Selected product:', product);
                
                // Here you would integrate with your cart functionality
                // For now, just close the quick view
                const bsCollapse = bootstrap.Collapse.getInstance(productQuickView);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            });
        }
    }
});

/**
 * Show Quick View function for displaying product details in collapse panel
 * @param {Object} product - The product object containing all product data
 */
function showQuickView(product) {
    // Get elements
    const quickView = document.getElementById('productQuickView');
    const mainImg = quickView.querySelector('.quick-view-img');
    const title = quickView.querySelector('.quick-view-title');
    const category = quickView.querySelector('.quick-view-category');
    const priceContainer = quickView.querySelector('.quick-view-price-container');
    const addToCartBtn = document.getElementById('quick-view-add-to-cart');
    const thumbnailContainer = quickView.querySelector('.thumbnail-container');
    
    // Set image and title
    mainImg.src = product.image;
    mainImg.alt = product.title;
    title.textContent = product.title;
    category.textContent = product.category;
    
    // Set product ID for the add to cart button
    if (addToCartBtn) {
        addToCartBtn.setAttribute('data-product-id', product.id);
    }
    
    // Handle price display based on discount
    const originalPrice = product.price;
    const discountPercentage = product.discount_percentage || 0;
    let discountedPrice = originalPrice;
    
    if (discountPercentage > 0) {
        discountedPrice = originalPrice - (originalPrice * (discountPercentage / 100));
        priceContainer.innerHTML = `
            <span class="quick-view-original-price">₱${originalPrice.toFixed(2)}</span>
            <span class="quick-view-current-price">₱${discountedPrice.toFixed(2)}</span>
            <span class="quick-view-discount">-${discountPercentage}%</span>
        `;
    } else {
        priceContainer.innerHTML = `
            <span class="quick-view-current-price">₱${originalPrice.toFixed(2)}</span>
        `;
    }
    
    // Generate thumbnails
    // For now we'll use the same image for all thumbnails since we don't have additional images
    // This can be updated when you have multiple product images
    const additionalImages = product.additionalImages || [];
    const allImages = [product.image, ...additionalImages];
    
    // If there are not enough images, duplicate the main image
    while (allImages.length < 4) {
        allImages.push(product.image);
    }
    
    // Generate thumbnail HTML
    thumbnailContainer.innerHTML = '';
    allImages.forEach((imgSrc, index) => {
        const thumbnailDiv = document.createElement('div');
        thumbnailDiv.className = `thumbnail ${index === 0 ? 'active' : ''}`;
        thumbnailDiv.dataset.imgSrc = imgSrc;
        
        const img = document.createElement('img');
        img.src = imgSrc;
        img.alt = `${product.title} - Image ${index + 1}`;
        img.className = 'img-fluid';
        
        thumbnailDiv.appendChild(img);
        thumbnailContainer.appendChild(thumbnailDiv);
        
        // Add event listeners to thumbnails
        thumbnailDiv.addEventListener('mouseenter', function() {
            // Update main image
            mainImg.src = this.dataset.imgSrc;
            
            // Update active state
            thumbnailContainer.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            this.classList.add('active');
        });
        
        thumbnailDiv.addEventListener('click', function() {
            // Update main image
            mainImg.src = this.dataset.imgSrc;
            
            // Update active state
            thumbnailContainer.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    // Store product data in data attributes for later use
    quickView.dataset.productId = product.id;
    quickView.dataset.productTitle = product.title;
    quickView.dataset.productImage = product.image;
    quickView.dataset.productCategory = product.category;
    quickView.dataset.originalPrice = originalPrice.toFixed(2);
    
    if (discountPercentage > 0) {
        quickView.dataset.discountPercentage = discountPercentage;
        quickView.dataset.discountedPrice = discountedPrice.toFixed(2);
    }
    
    // Reset size selection to Medium or first available size
    resetSizeSelection();
    
    // Reset quantity to 1
    resetQuantitySelection();
}

/**
 * Reset size selection to default (Medium)
 */
function resetSizeSelection() {
    const sizeButtons = document.querySelectorAll('.btn-size');
    const hiddenSizeInput = document.getElementById('quick-view-size');
    
    if (sizeButtons.length > 0) {
        // Remove active class from all buttons
        sizeButtons.forEach(btn => btn.classList.remove('active'));
        
        // Find the Medium size button or the first button
        const defaultSize = Array.from(sizeButtons).find(btn => btn.dataset.size === 'M') || sizeButtons[0];
        
        // Set active class
        defaultSize.classList.add('active');
        
        // Update hidden input
        if (hiddenSizeInput) {
            hiddenSizeInput.value = defaultSize.dataset.size;
        }
    }
}

/**
 * Reset quantity selection to 1
 */
function resetQuantitySelection() {
    const quantityInput = document.getElementById('quick-view-quantity');
    const minusBtn = document.getElementById('quick-view-quantity-minus');
    const plusBtn = document.getElementById('quick-view-quantity-plus');
    
    if (quantityInput) {
        // Reset to 1
        quantityInput.value = 1;
        
        // Update button states
        if (minusBtn && plusBtn) {
            minusBtn.classList.add('disabled'); // Disable minus button at quantity 1
            plusBtn.classList.remove('disabled');
        }
    }
}

// Legacy functions kept for compatibility
function toggleProductDetails(button) {
    // This function can now be a wrapper for showQuickView if needed
    // or left for backward compatibility
    console.log("toggleProductDetails is deprecated, use showQuickView instead");
}

function closeProductDetails() {
    // Close the quick view collapse
    const quickView = document.getElementById('productQuickView');
    if (quickView) {
        const bsCollapse = bootstrap.Collapse.getInstance(quickView);
        if (bsCollapse) {
            bsCollapse.hide();
        }
    }
}