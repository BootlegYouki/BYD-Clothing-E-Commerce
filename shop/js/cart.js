/**
 * Shopping Cart Functionality
 * Handles all cart-related operations like adding, removing items, updating quantities, etc.
 */

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Ensure consistent localStorage key usage
    migrateCartData();
    updateCartCount();
    updateShoppingCart();
    initializeViewCartButton();
});

/**
 * Migrate cart data to ensure consistent key usage
 */
function migrateCartData() {
    // Check if we have data under the old key and not under the new one
    const oldCart = localStorage.getItem('cart');
    const newCart = localStorage.getItem('shopping-cart');
    
    if (oldCart && !newCart) {
        // Migrate data from old key to new key
        localStorage.setItem('shopping-cart', oldCart);
    } else if (newCart && !oldCart) {
        // Ensure data exists in both places for backwards compatibility
        localStorage.setItem('cart', newCart);
    }
    
    // From now on, we'll use 'shopping-cart' as the primary key
}

/**
 * Update the cart count in the header
 */
function updateCartCount() {
    const cartCountElement = document.querySelector('.cart-badge');
    if (cartCountElement) {
        const cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
        const totalItems = cart.reduce((acc, item) => acc + item.quantity, 0);
        cartCountElement.textContent = totalItems;
        
        // Show or hide based on count
        if (totalItems > 0) {
            cartCountElement.style.display = 'flex';
        } else {
            cartCountElement.style.display = 'none';
        }
    }
}

/**
 * Add an item to the cart
 * @param {Object} item - The product item to add
 */
function addToCart(item) {
    let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    
    // Check if product with same ID and size already exists
    const existingItemIndex = cart.findIndex(cartItem => 
        cartItem.id === item.id && cartItem.size === item.size
    );
    
    if (existingItemIndex > -1) {
        // Update quantity of existing item, but respect stock limits
        const newQuantity = cart[existingItemIndex].quantity + item.quantity;
        // Make sure we don't exceed available stock
        cart[existingItemIndex].quantity = Math.min(newQuantity, item.stock);
        // Always ensure we store the latest stock information
        cart[existingItemIndex].stock = item.stock;
    } else {
        // Add as new item (make sure stock info is included)
        cart.push(item);
    }
    
    // Save cart back to localStorage (use consistent key)
    localStorage.setItem('shopping-cart', JSON.stringify(cart));
    // For backwards compatibility
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update UI
    updateCartCount();
    updateShoppingCart();
}

/**
 * Remove an item from the cart
 * @param {number} index - The index of the item to remove
 */
function removeCartItem(index) {
    let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    
    // Remove the item
    cart.splice(index, 1);
    localStorage.setItem('shopping-cart', JSON.stringify(cart));
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update displays
    updateCartCount();
    updateShoppingCart();
}

/**
 * Update the quantity of an item in the cart
 * @param {number} index - The index of the item to update
 * @param {number} change - The amount to change the quantity by (+1 or -1)
 */
function updateCartItemQuantity(index, change) {
    let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    if (!cart[index]) return;
    
    const newQuantity = cart[index].quantity + change;
    
    // Get the stock from the cart item (or default to 10 if not set)
    const maxStock = cart[index].stock || 10;
    
    // Ensure quantity is between 1 and available stock
    cart[index].quantity = Math.max(1, Math.min(maxStock, newQuantity));
    
    // Save cart back to localStorage (use both keys for consistency)
    localStorage.setItem('shopping-cart', JSON.stringify(cart));
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update displays
    updateCartCount();
    updateShoppingCart();
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
    
    // Get cart data (from the standardized key)
    const cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    
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
        
        // Get max stock (or default to 10)
        const maxStock = item.stock || 10;
        
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
                            <button class="btn btn-sm btn-outline-secondary cart-qty-btn plus" data-index="${index}" ${item.quantity >= maxStock ? 'disabled' : ''}>+</button>
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
            removeCartItem(index);
        });
    });
    
    // Quantity adjustment buttons
    const qtyButtons = document.querySelectorAll('.cart-qty-btn');
    qtyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-index'));
            const isPlus = this.classList.contains('plus');
            updateCartItemQuantity(index, isPlus ? 1 : -1);
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

/**
 * Show toast notification for added item
 * @param {Object} item - The cart item that was added
 */
function showAddToCartToast(item) {
    const toastEl = document.getElementById('cartAddedToast');
    if (!toastEl) return;
    
    // Set toast content
    document.getElementById('toast-product-name').textContent = item.title;
    document.getElementById('toast-product-category').textContent = item.category;
    document.getElementById('toast-product-size').textContent = item.size;
    document.getElementById('toast-product-quantity').textContent = item.quantity;
    document.getElementById('toast-product-image').src = item.image;
    
    // Show toast
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Make functions available globally
window.updateCartCount = updateCartCount;
window.addToCart = addToCart;
window.removeCartItem = removeCartItem;
window.updateCartItemQuantity = updateCartItemQuantity;
window.updateShoppingCart = updateShoppingCart;
window.showAddToCartToast = showAddToCartToast;
