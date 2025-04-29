/**
 * Product Page Functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements
    const quantityInput = document.getElementById('quantity');
    const minusBtn = document.getElementById('quantity-minus');
    const plusBtn = document.getElementById('quantity-plus');
    const sizeButtons = document.querySelectorAll('.btn-size');
    const selectedSizeInput = document.getElementById('selected-size');
    const sizeError = document.getElementById('size-error');
    const stockInfo = document.getElementById('stock-info');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainProductImage = document.getElementById('main-product-image');
    const mainImageContainer = document.querySelector('.main-image-container');
    
    initializeThumbnails();
    initializeMainImageNavigation();
    initializeSizeButtons();
    initializeQuantityControls();
    initializeAddToCart();
    initializeImageZoom();
    initializeViewCartButton();
    updateCartCount();
    updateShoppingCart();
    
    /**
     * Handle thumbnail image switching
     */
    function initializeThumbnails() {
        if (!thumbnails.length || !mainProductImage) return;
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Remove active class from all thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked thumbnail
                this.classList.add('active');
                
                // Update main image
                const imageUrl = this.getAttribute('data-image');
                mainProductImage.src = imageUrl;
            });
        });
    }
    
    /**
     * Add navigation buttons to main product image
     */
    function initializeMainImageNavigation() {
        if (!mainImageContainer || !thumbnails.length) return;
        
        // Create navigation buttons
        const prevBtn = document.createElement('button');
        prevBtn.className = 'main-nav-btn prev';
        prevBtn.innerHTML = '<i class="fa fa-angle-left"></i>';
        prevBtn.setAttribute('title', 'Previous image');
        prevBtn.setAttribute('aria-label', 'Previous image');
        
        const nextBtn = document.createElement('button');
        nextBtn.className = 'main-nav-btn next';
        nextBtn.innerHTML = '<i class="fa fa-angle-right"></i>';
        nextBtn.setAttribute('title', 'Next image');
        nextBtn.setAttribute('aria-label', 'Next image');
        
        // Add buttons to container
        mainImageContainer.appendChild(prevBtn);
        mainImageContainer.appendChild(nextBtn);
        
        // Navigation function
        function navigateMainImage(direction) {
            if (!thumbnails.length) return;
            
            // Find the currently active thumbnail
            let activeIndex = -1;
            thumbnails.forEach((thumb, index) => {
                if (thumb.classList.contains('active')) {
                    activeIndex = index;
                }
            });
            
            if (activeIndex === -1) return;
            
            // Calculate next index based on direction
            let nextIndex;
            if (direction === 'next') {
                nextIndex = (activeIndex + 1) % thumbnails.length;
            } else {
                nextIndex = activeIndex - 1;
                if (nextIndex < 0) nextIndex = thumbnails.length - 1;
            }
            
            // Simulate click on the next/prev thumbnail
            thumbnails[nextIndex].click();
        }
        
        // Set up button event handlers
        prevBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent zoom from triggering
            navigateMainImage('prev');
        });
        
        nextBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent zoom from triggering
            navigateMainImage('next');
        });
        
        // Add keyboard navigation for main image
        document.addEventListener('keydown', function(e) {
            // Only respond to arrow keys when not in zoom mode
            const zoomOverlay = document.getElementById('image-zoom-overlay');
            if (zoomOverlay && zoomOverlay.style.display === 'flex') return;
            
            // Check if focus is within the product detail section
            const productSection = document.querySelector('.product-detail');
            if (!productSection.contains(document.activeElement) && 
                document.activeElement !== document.body) return;
                
            if (e.key === 'ArrowLeft') {
                navigateMainImage('prev');
            } else if (e.key === 'ArrowRight') {
                navigateMainImage('next');
            }
        });
        
        // If only one image, hide the navigation buttons
        if (thumbnails.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        }
    }
    
    /**
     * Handle size selection
     */
    function initializeSizeButtons() {
        if (!sizeButtons.length) return;
        
        sizeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all size buttons
                sizeButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Store selected size
                const selectedSize = this.getAttribute('data-size');
                const stockCount = this.getAttribute('data-stock');
                
                selectedSizeInput.value = selectedSize;
                sizeError.style.display = 'none';
                
                // Update stock info
                stockInfo.textContent = `Available: ${stockCount} in stock`;
                stockInfo.style.display = 'block';
                
                // Update max quantity
                quantityInput.max = stockCount;
                
                // Reset quantity if it exceeds stock
                if (parseInt(quantityInput.value) > parseInt(stockCount)) {
                    quantityInput.value = stockCount;
                }
                
                // Update button states
                updateQuantityButtonStates();
            });
        });
    }
    
    /**
     * Handle quantity controls
     */
    function initializeQuantityControls() {
        if (!minusBtn || !plusBtn || !quantityInput) return;
        
        minusBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = --currentValue;
                updateQuantityButtonStates();
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            const max = parseInt(quantityInput.getAttribute('max')) || 10;
            if (currentValue < max) {
                quantityInput.value = ++currentValue;
                updateQuantityButtonStates();
            }
        });
        
        updateQuantityButtonStates();
    }
    
    /**
     * Update the state of quantity buttons
     */
    function updateQuantityButtonStates() {
        if (!minusBtn || !plusBtn || !quantityInput) return;
        
        const currentValue = parseInt(quantityInput.value);
        const max = parseInt(quantityInput.getAttribute('max')) || 10;
        
        minusBtn.classList.toggle('disabled', currentValue <= 1);
        plusBtn.classList.toggle('disabled', currentValue >= max);
    }
    
    /**
     * Handle add to cart functionality
     */
    function initializeAddToCart() {
        if (!addToCartBtn) return;
        
        addToCartBtn.addEventListener('click', function() {
            const selectedSize = selectedSizeInput.value;
            const quantity = parseInt(quantityInput.value);
            
            // Validate size selection
            if (!selectedSize) {
                sizeError.style.display = 'block';
                return;
            }
            
            // Get product data from hidden inputs or data attributes
            const productId = parseInt(this.getAttribute('data-product-id'));
            const productName = this.getAttribute('data-product-name');
            const productCategory = this.getAttribute('data-product-category');
            const productPrice = parseFloat(this.getAttribute('data-product-price'));
            const productOriginalPrice = parseFloat(this.getAttribute('data-product-original-price'));
            const productImage = this.getAttribute('data-product-image');
            
            // Create cart item object
            const cartItem = {
                id: productId,
                title: productName,
                category: productCategory,
                price: productPrice,
                originalPrice: productOriginalPrice,
                size: selectedSize,
                quantity: quantity,
                image: productImage
            };
            
            // Add to cart using the cart.js function
            window.addToCart(cartItem);
            
            // Show success toast notification
            window.showAddToCartToast(cartItem);
        });
    }
    
    /**
     * Initialize image zoom functionality
     */
    function initializeImageZoom() {
        if (!mainProductImage || !mainImageContainer) return;
        
        // Create zoom overlay if it doesn't exist
        if (!document.getElementById('image-zoom-overlay')) {
            const zoomOverlay = document.createElement('div');
            zoomOverlay.id = 'image-zoom-overlay';
            
            const zoomedImage = document.createElement('img');
            zoomedImage.id = 'zoomed-image';
            zoomOverlay.appendChild(zoomedImage);
            
            // Create navigation buttons for the zoom overlay
            const prevBtn = document.createElement('button');
            prevBtn.className = 'zoom-nav-btn prev';
            prevBtn.innerHTML = '<i class="fa fa-angle-left"></i>';
            prevBtn.setAttribute('title', 'Previous image');
            
            const nextBtn = document.createElement('button');
            nextBtn.className = 'zoom-nav-btn next';
            nextBtn.innerHTML = '<i class="fa fa-angle-right"></i>';
            nextBtn.setAttribute('title', 'Next image');
            
            zoomOverlay.appendChild(prevBtn);
            zoomOverlay.appendChild(nextBtn);
            
            // Create zoom controls (close button)
            const zoomControls = document.createElement('div');
            zoomControls.className = 'zoom-controls';
            
            const closeBtn = document.createElement('button');
            closeBtn.className = 'zoom-btn';
            closeBtn.innerHTML = '<i class="fa fa-times"></i>';
            closeBtn.setAttribute('title', 'Close zoom');
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                zoomOverlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
            
            zoomControls.appendChild(closeBtn);
            zoomOverlay.appendChild(zoomControls);
            
            document.body.appendChild(zoomOverlay);
            
            // Set up navigation button handlers
            prevBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                navigateZoomedImage('prev');
            });
            
            nextBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                navigateZoomedImage('next');
            });
        }
        
        const zoomOverlay = document.getElementById('image-zoom-overlay');
        const zoomedImage = document.getElementById('zoomed-image');
        
        // Function to navigate images in the zoom overlay
        function navigateZoomedImage(direction) {
            if (!thumbnails.length) return;
            
            // Find the currently active thumbnail
            let activeIndex = -1;
            thumbnails.forEach((thumb, index) => {
                if (thumb.classList.contains('active')) {
                    activeIndex = index;
                }
            });
            
            if (activeIndex === -1) return;
            
            // Calculate next index based on direction
            let nextIndex;
            if (direction === 'next') {
                nextIndex = (activeIndex + 1) % thumbnails.length;
            } else {
                nextIndex = activeIndex - 1;
                if (nextIndex < 0) nextIndex = thumbnails.length - 1;
            }
            
            // Simulate click on the next/prev thumbnail
            thumbnails[nextIndex].click();
        }
        
        // Add keyboard navigation for the zoom overlay
        document.addEventListener('keydown', function(e) {
            if (zoomOverlay.style.display !== 'flex') return;
            
            if (e.key === 'ArrowLeft') {
                navigateZoomedImage('prev');
            } else if (e.key === 'ArrowRight') {
                navigateZoomedImage('next');
            } else if (e.key === 'Escape') {
                zoomOverlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
        
        // Handle main image click to zoom
        mainImageContainer.addEventListener('click', function() {
            zoomedImage.src = mainProductImage.src;
            zoomOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent page scrolling
        });
        
        // Close zoom on overlay click
        zoomOverlay.addEventListener('click', function(e) {
            // Only close if clicking the background (not on image or controls)
            if (e.target === zoomOverlay) {
                zoomOverlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
        
        // Update zoomed image when thumbnail changes
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                if (zoomedImage) {
                    zoomedImage.src = this.getAttribute('data-image');
                }
            });
        });
    }

    /**
     * Initialize the View Cart button in toast notification
     */
    function initializeViewCartButton() {
        const viewCartBtn = document.getElementById('view-cart-btn');
        if (viewCartBtn) {
            viewCartBtn.addEventListener('click', function() {
                // Open the offcanvas cart
                const offcanvasCart = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
                offcanvasCart.show();
            });
        }
    }

    // Initialize the related products swiper for mobile view
    if (document.querySelector('.related-products-swiper')) {
        const relatedProductsSwiper = new Swiper('.related-products-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            centeredSlides: true,
            pagination: {
                el: '.related-products-swiper .swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                480: {
                    slidesPerView: 1,
                    centeredSlides: false
                }
            }
        });
    }
});

/**
 * Update the cart count in the header
 */
function updateCartCount() {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const totalItems = cart.reduce((acc, item) => acc + item.quantity, 0);
        cartCountElement.textContent = totalItems;
        
        // Show or hide based on count
        if (totalItems > 0) {
            cartCountElement.style.display = 'inline';
        } else {
            cartCountElement.style.display = 'none';
        }
    }
}

/**
 * Update the shopping cart display
 */
function updateShoppingCart() {
    const cartItemsContainer = document.getElementById('cart-items');
    const emptyCart = document.getElementById('empty-cart');
    const cartItemsWrapper = document.getElementById('cart-items-container');
    const cartSubtotalElement = document.getElementById('cart-subtotal');
    const cartTotalElement = document.getElementById('cart-total');
    
    if (!cartItemsContainer || !emptyCart || !cartItemsWrapper) return;
    
    // Get cart data
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Clear existing items
    cartItemsContainer.innerHTML = '';
    
    if (cart.length === 0) {
        // Show empty cart message
        emptyCart.classList.remove('d-none');
        cartItemsWrapper.classList.add('d-none');
        return;
    }
    
    // Hide empty cart, show items
    emptyCart.classList.add('d-none');
    cartItemsWrapper.classList.remove('d-none');
    
    // Calculate total
    let subtotal = 0;
    
    // Add each item to the cart
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        const cartItemElement = document.createElement('div');
        cartItemElement.className = 'cart-item mb-3 pb-3 border-bottom';
        cartItemElement.innerHTML = `
            <div class="d-flex">
                <div class="cart-item-img me-3">
                    <img src="${item.image}" alt="${item.title}" class="img-fluid rounded">
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-0 fw-semibold">${item.title}</h6>
                            <small class="text-muted text-uppercase">${item.category}</small>
                        </div>
                        <button class="btn btn-sm text-danger p-0 ms-2 remove-item" data-index="${index}" title="Remove item">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="text-muted small mt-1">Size: ${item.size}</div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="item-price">₱${item.price.toFixed(2)}</div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary cart-qty-btn minus" data-index="${index}">-</button>
                            <input type="text" class="form-control form-control-sm mx-1 text-center cart-item-quantity" value="${item.quantity}" readonly>
                            <button class="btn btn-sm btn-outline-secondary cart-qty-btn plus" data-index="${index}">+</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        cartItemsContainer.appendChild(cartItemElement);
    });
    
    // Update totals
    cartSubtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
    cartTotalElement.textContent = `₱${subtotal.toFixed(2)}`;
    
    // Add event listeners for cart item buttons
    setupCartButtonListeners();
}

/**
 * Set up event listeners for cart buttons
 */
function setupCartButtonListeners() {
    // Remove item buttons
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-index'));
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Remove the item
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update displays
            updateCartCount();
            updateShoppingCart();
        });
    });
    
    // Quantity adjustment buttons
    const qtyButtons = document.querySelectorAll('.cart-qty-btn');
    qtyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-index'));
            const isPlus = this.classList.contains('plus');
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart[index]) {
                if (isPlus) {
                    // Increase quantity (max 10)
                    cart[index].quantity = Math.min(cart[index].quantity + 1, 10);
                } else {
                    // Decrease quantity (min 1)
                    cart[index].quantity = Math.max(cart[index].quantity - 1, 1);
                }
                
                // Update cart
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
                updateShoppingCart();
            }
        });
    });
}

// Initialize cart display when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    updateShoppingCart();
});
