// MODAL ANIMATION 
document.getElementById('cartModal').addEventListener('hide.bs.modal', function(e) {
    if (this.dataset.programmaticHide) return;
    
    e.preventDefault();
    this.classList.add('modal-closing');
    
    const dialog = this.querySelector('.cart-modal');
    const onTransitionEnd = () => {
      dialog.removeEventListener('transitionend', onTransitionEnd);
      this.dataset.programmaticHide = 'true';
      bootstrap.Modal.getInstance(this).hide();
      delete this.dataset.programmaticHide;
      this.classList.remove('modal-closing');
    };
    
    dialog.addEventListener('transitionend', onTransitionEnd);
  });

// ======================
// Cart Functionality
// ======================

// Get DOM elements
const cartModal = document.getElementById('cartModal');
const cartTable = cartModal.querySelector('.cart-table-wrapper');
const emptyCartMessage = cartModal.querySelector('.empty-cart-message');
const cartFooter = cartModal.querySelector('.cart-footer');
const cartHeader = cartModal.querySelector('.cart-header');
const totalItems = cartHeader.querySelector('.total-items');
const modalTitle = cartHeader.querySelector('.cart-title');
const orderNumberElement = document.querySelector('.order-number');

// ======================
// Cart Display Toggle
// ======================
function toggleCartDisplay() {
    const hasItems = cartTable.querySelector('tbody tr') !== null;
    
    if (!hasItems) {
        cartTable.classList.add('d-none');
        cartFooter.classList.add('d-none');
        totalItems.classList.add('d-none');
        modalTitle.classList.add('d-none');
        emptyCartMessage.classList.remove('d-none');
    } else {
        cartTable.classList.remove('d-none');
        cartFooter.classList.remove('d-none');
        totalItems.classList.remove('d-none');
        modalTitle.classList.remove('d-none');
        emptyCartMessage.classList.add('d-none');
    }
}

// ======================
// Local Storage Functions
// ======================
function saveCartToStorage(cartItems) {
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
}

function getCartFromStorage() {
    const cartItems = localStorage.getItem('cartItems');
    return cartItems ? JSON.parse(cartItems) : [];
}

function loadCartFromStorage() {
    const cartItems = getCartFromStorage();
    const tbody = cartTable.querySelector('tbody');
    tbody.innerHTML = ''; // Clear current cart

    cartItems.forEach(item => {
        const newRow = document.createElement('tr');
        newRow.dataset.productId = item.id;
        newRow.innerHTML = `
            <td>
                <img src="${item.image}" alt="${item.title}" style="width: 100px; height: auto;">
            </td>
            <td class="product-info text-start">
                <div class="text-left">
                    <strong>${item.title}</strong><br>
                    <span class="product-detail size">Size: ${item.size}</span><br>
                    <span class="product-detail">Price: ₱${parseFloat(item.price.replace('₱', '')).toFixed(2)}</span>
                </div>
            </td>
            <td>
                <div class="quantity-control">
                    <span class="minus">-</span>
                    <span class="num">${item.quantity}</span>
                    <span class="plus">+</span>
                </div>
            </td>
            <td data-base-price="${item.price}">₱${(parseFloat(item.price.replace('₱', '')) * item.quantity).toFixed(2)}</td>
            <td>
                <button class="btn-remove custom-btn-remove"></button>
            </td>
        `;
        tbody.appendChild(newRow);
    });

    toggleCartDisplay();
    updateCartTotals();
}

// ======================
// Quantity Adjustment (With Removal)
// ======================
cartTable.querySelector('tbody').addEventListener('click', function(event) {
    const target = event.target;
    const isDecrement = target.classList.contains('minus');
    const isIncrement = target.classList.contains('plus');
    
    // Only handle quantity buttons
    if (!isDecrement && !isIncrement) return;

    const row = target.closest('tr');
    const quantityElement = row.querySelector('.num');
    let quantity = parseInt(quantityElement.textContent);

    // Handle quantity changes
    if (isDecrement) quantity--;
    if (isIncrement) quantity++;

    // Prevent negative quantities
    quantity = Math.max(quantity, 0);

    // Update storage and handle removal
    const cartItems = getCartFromStorage();
    const productId = row.dataset.productId;
    const size = row.querySelector('.size').textContent.replace('Size: ', '');
    const itemIndex = cartItems.findIndex(item => 
        item.id === productId && item.size === size
    );

    if (itemIndex > -1) {
        if (quantity <= 0) {
            // Remove item completely
            cartItems.splice(itemIndex, 1);
            row.remove();
        } else {
            // Update UI
            quantityElement.textContent = quantity;
            const priceElement = row.querySelector('td:nth-child(4)');
            const basePrice = parseFloat(priceElement.dataset.basePrice.replace('₱', ''));
            priceElement.textContent = `₱${(basePrice * quantity).toFixed(2)}`;
            
            // Update quantity
            cartItems[itemIndex].quantity = quantity;
        }
        
        saveCartToStorage(cartItems);
        updateCartTotals();
        toggleCartDisplay();
    }
});

// ======================
// Cart Modification Functions
// ======================
function updateCartTotals() {
    const cartItems = getCartFromStorage();
    let subtotal = 0;
    let totalQuantity = 0;

    cartItems.forEach(item => {
        const price = parseFloat(item.price.replace('₱', ''));
        const quantity = parseInt(item.quantity);
        subtotal += price * quantity;
        totalQuantity += quantity;
    });

    const subtotalElement = cartFooter.querySelector('strong');
    subtotalElement.textContent = `Subtotal: ₱${subtotal.toFixed(2)}`;

    const totalItemsElement = cartHeader.querySelector('.total-items');
    totalItemsElement.textContent = `Total Items (${totalQuantity})`;

    orderNumberElement.textContent = totalQuantity;
}

// Function to add items to cart
function addToCart(button) {
    const productDetails = document.getElementById('productDetails');
    const cartItems = getCartFromStorage();
    
    // Get product information from the product details modal
    const product = {
        id: productDetails.dataset.productId || new Date().getTime().toString(),
        image: productDetails.dataset.productImage || productDetails.querySelector('.detail-img').src,
        title: productDetails.dataset.productTitle || productDetails.querySelector('.detail-title').textContent,
        price: productDetails.dataset.productPrice || productDetails.querySelector('.detail-price').textContent,
        size: productDetails.querySelector('.size-select').value,
        quantity: parseInt(productDetails.querySelector('.quantity-select').value)
    };
    
    // Ensure numeric quantity
    product.quantity = Math.max(1, product.quantity || 1);
    
    // Normalize price format
    product.price = `₱${parseFloat(product.price.replace('₱', '')).toFixed(2)}`;
    
    const existingItemIndex = cartItems.findIndex(item => 
        item.id === product.id && item.size === product.size
    );

    if (existingItemIndex > -1) {
        // Update existing item quantity
        cartItems[existingItemIndex].quantity += product.quantity;
    } else {
        // Add new item with clean data structure
        cartItems.push({
            id: product.id,
            image: product.image,
            title: product.title,
            price: product.price,
            size: product.size,
            quantity: product.quantity
        });
    }

    saveCartToStorage(cartItems);
    loadCartFromStorage(); // Refresh entire cart display
    updateCartTotals();
    
    // Show success message
    const toast = document.createElement('div');
    toast.className = 'toast show position-fixed bottom-0 end-0 m-3';
    toast.innerHTML = `
        <div class="toast-body bg-success text-white">
            Item added to cart successfully!
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
    
    // Close the product details modal
    closeProductDetails();
}

function removeCartItem(button) {
    const row = button.closest('tr');
    const productId = row.dataset.productId;
    const size = row.querySelector('.product-detail.size').textContent.replace('Size: ', '');
    
    // Add animation class
    row.classList.add('cart-item-removing');
    
    // Wait for animation to complete before removing
    row.addEventListener('animationend', () => {
        const cartItems = getCartFromStorage();
        const updatedCart = cartItems.filter(item => 
            !(item.id === productId && item.size === size)
        );
        
        saveCartToStorage(updatedCart);
        row.remove();
        toggleCartDisplay();
        updateCartTotals();
    });
}

// Event listeners
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove') || 
        e.target.closest('.btn-remove')) {
        const button = e.target.classList.contains('btn-remove') ? 
                      e.target : 
                      e.target.closest('.btn-remove');
        removeCartItem(button);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    loadCartFromStorage();
    toggleCartDisplay();
    updateCartTotals();
});

document.querySelector('.add-to-cart-btn').addEventListener('click', function() {
    const productModal = document.getElementById('productModal');
    
    // Generate stable ID based on product characteristics
    const product = {
        id: productModal.dataset.productId || 
            `${btoa(productModal.querySelector('.product-title').textContent)}-${productModal.querySelector('#size').value}`,
        image: productModal.querySelector('.product-img').src,
        title: productModal.querySelector('.product-title').textContent,
        price: productModal.querySelector('.product-price').textContent,
        size: productModal.querySelector('#size').value,
        quantity: parseInt(productModal.querySelector('#quantity').value)
    };

    // Ensure numeric quantity
    product.quantity = Math.max(1, product.quantity || 1);

    addToCart(product);

    // Show success message
    const toast = document.createElement('div');
    toast.className = 'toast show position-fixed bottom-0 end-0 m-3';
    toast.innerHTML = `
        <div class="toast-body bg-success text-white">
            Item added to cart successfully!
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);

    bootstrap.Modal.getInstance(productModal).hide();
});