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
    
    // Check admin status
    const userAccountSection = document.getElementById('userAccountSection');
    const isAdmin = userAccountSection && userAccountSection.dataset.isAdmin === 'true';
    
    initializeThumbnails();
    initializeMainImageNavigation();
    initializeSizeButtons();
    initializeQuantityControls();
    initializeAddToCart();
    initializeImageZoom();
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
        
        // Initially disable the add to cart button until a size is selected
        if (addToCartBtn && !isAdmin) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('disabled');
            addToCartBtn.title = 'Please select a size first';
        }
        
        sizeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all size buttons
                sizeButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Store selected size
                const selectedSize = this.getAttribute('data-size');
                const stockCount = parseInt(this.getAttribute('data-stock'));
                
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
                
                // Update add to cart button based on stock availability
                if (addToCartBtn && !isAdmin) {
                    if (stockCount <= 0) {
                        addToCartBtn.disabled = true;
                        addToCartBtn.classList.add('out-of-stock');
                        addToCartBtn.textContent = 'OUT OF STOCK';
                        addToCartBtn.title = 'This size is currently out of stock';
                    } else {
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.remove('disabled', 'out-of-stock');
                        addToCartBtn.textContent = 'ADD TO CART';
                        addToCartBtn.title = '';
                    }
                }
            });
        });
        
        // Automatically select Medium size or first available size if Medium isn't available
        let mediumButton = null;
        let firstAvailableButton = null;
        let allSizesOutOfStock = true;
        
        sizeButtons.forEach(button => {
            // Skip disabled buttons (out of stock)
            if (button.disabled) return;
            
            // If we reach here, at least one size has stock
            allSizesOutOfStock = false;
            
            // Save the first available button we find
            if (!firstAvailableButton) {
                firstAvailableButton = button;
            }
            
            // Check if this is the Medium size button
            if (button.getAttribute('data-size') === 'M') {
                mediumButton = button;
            }
        });
        
        // Show message if all sizes are out of stock
        if (allSizesOutOfStock) {
            stockInfo.textContent = "No stock available on all sizes";
            stockInfo.style.display = 'block';
            stockInfo.classList.add('out-of-stock');
            
            if (addToCartBtn && !isAdmin) {
                addToCartBtn.disabled = true;
                addToCartBtn.classList.add('out-of-stock');
                addToCartBtn.textContent = 'OUT OF STOCK';
                addToCartBtn.title = 'This product is currently out of stock';
            }
        } else {
            // Click the Medium button if available, otherwise click the first available button
            if (mediumButton) {
                mediumButton.click();
            } else if (firstAvailableButton) {
                firstAvailableButton.click();
            }
        }
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
        
        // Disable Add to Cart button if user is admin
        if (isAdmin) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('disabled');
            addToCartBtn.title = 'Admin accounts cannot add items to cart';
            addToCartBtn.innerHTML = 'ADMIN VIEW ONLY';
            return;
        }
        
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
            
            // Get the stock for the selected size
            const selectedSizeButton = document.querySelector(`.btn-size.active[data-size="${selectedSize}"]`);
            const stock = parseInt(selectedSizeButton.getAttribute('data-stock'));
            
            // Check if item already exists in cart and if adding would exceed stock
            let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
            const existingItemIndex = cart.findIndex(item => 
                item.id === productId && item.size === selectedSize
            );
            
            if (existingItemIndex > -1) {
                const currentQuantity = cart[existingItemIndex].quantity;
                if (currentQuantity >= stock) {
                    // Show error toast - can't add more than stock available
                    window.showDangerToast({
                        title: productName,
                        category: productCategory,
                        size: selectedSize,
                        image: productImage,
                        stock: stock
                    });
                    return;
                }
                
                // If adding current quantity would exceed stock, limit it
                if (currentQuantity + quantity > stock) {
                    // Show warning toast that we've adjusted quantity
                    window.showWarningToast({
                        title: productName,
                        category: productCategory,
                        size: selectedSize,
                        image: productImage,
                        stock: stock,
                        quantity: stock - currentQuantity
                    });
                    
                    // Create cart item with adjusted quantity
                    const cartItem = {
                        id: productId,
                        title: productName,
                        category: productCategory,
                        price: productPrice,
                        originalPrice: productOriginalPrice,
                        size: selectedSize,
                        quantity: stock - currentQuantity,
                        stock: stock,
                        image: productImage
                    };
                    
                    // Add to cart (the function will handle combining with existing)
                    window.addToCart(cartItem);
                    return;
                }
            }
            
            // Create cart item object
            const cartItem = {
                id: productId,
                title: productName,
                category: productCategory,
                price: productPrice,
                originalPrice: productOriginalPrice,
                size: selectedSize,
                quantity: quantity,
                stock: stock,
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