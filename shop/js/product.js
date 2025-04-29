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
    
    initializeThumbnails();
    initializeSizeButtons();
    initializeQuantityControls();
    initializeAddToCart();
    updateCartCount();
    
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
            
            // Add to cart in localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Check if product with same ID and size already exists
            const existingItemIndex = cart.findIndex(item => 
                item.id === cartItem.id && item.size === cartItem.size
            );
            
            if (existingItemIndex > -1) {
                // Update quantity of existing item
                cart[existingItemIndex].quantity += quantity;
            } else {
                // Add as new item
                cart.push(cartItem);
            }
            
            // Save cart back to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update cart count in header
            updateCartCount();
            
            // Show success toast notification
            const toast = new bootstrap.Toast(document.getElementById('cartAddedToast'));
            document.getElementById('toast-product-name').textContent = cartItem.title;
            document.getElementById('toast-product-size').textContent = cartItem.size;
            document.getElementById('toast-product-quantity').textContent = cartItem.quantity;
            toast.show();
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
