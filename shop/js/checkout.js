document.addEventListener('DOMContentLoaded', function() {
    // Load cart data from localStorage
    const cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    
    // If cart is empty, redirect to shop
    if (cart.length === 0) {
        window.location.href = 'shop.php';
        return;
    }
    
    // For debugging - check cart structure
    console.log('Cart items:', cart);
    
    // Display order items
    const orderItemsContainer = document.getElementById('order-items');
    const mobileOrderSummary = document.getElementById('mobile-order-summary');
    
    // Calculate subtotal
    let subtotal = 0;
    let itemsHtml = '';
    
    // Build HTML for cart items
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        // Use item.productTitle or item.title if item.name is undefined
        const productName = item.name || item.productTitle || item.title || 'Product';
        
        itemsHtml += `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <div style="width: 50px; height: 50px; overflow: hidden;" class="flex-shrink-0">
                    <img src="${item.image}" class="img-fluid" alt="${productName}">
                </div>
                <div class="ms-3">
                    <h6 class="mb-0">${productName}</h6>
                    <p class="small text-muted mb-0">Size: ${item.size} | Qty: ${item.quantity}</p>
                </div>
            </div>
            <span>₱${(item.price * item.quantity).toFixed(2)}</span>
        </div>`;
    });
    
    // Update the order items in the UI
    if (orderItemsContainer) {
        orderItemsContainer.innerHTML = itemsHtml;
    }
    
    // Update mobile summary if it exists
    if (mobileOrderSummary) {
        mobileOrderSummary.innerHTML = itemsHtml;
    }
    
    // Update subtotal
    const orderSubtotal = document.getElementById('order-subtotal');
    if (orderSubtotal) {
        orderSubtotal.textContent = `₱${subtotal.toFixed(2)}`;
    }
    
    // Update total with shipping fee
    const orderTotal = document.getElementById('order-total');
    if (orderTotal) {
        const total = subtotal + SHIPPING_FEE; // SHIPPING_FEE is set in the PHP
        orderTotal.textContent = `₱${total.toFixed(2)}`;
    }
    
    // Set up form submission
    const checkoutForm = document.getElementById('checkout-form');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(checkoutForm);
            
            // Add cart items to form data
            formData.append('cart_items', JSON.stringify(cart));
            formData.append('subtotal', subtotal);
            formData.append('shipping_cost', SHIPPING_FEE);
            formData.append('total', subtotal + SHIPPING_FEE);
            
            // Show loading state
            document.querySelector('button[type="submit"]').innerHTML = 
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
            document.querySelector('button[type="submit"]').disabled = true;
            
            // Submit the form via AJAX
            fetch('functions/place_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear cart
                    localStorage.removeItem('shopping-cart');
                    
                    // Redirect to thank you page with order ID
                    window.location.href = `order-confirmation.php?order_id=${data.order_id}`;
                } else {
                    alert(data.message || 'Something went wrong. Please try again.');
                    // Reset button
                    document.querySelector('button[type="submit"]').innerHTML = 'Place Order';
                    document.querySelector('button[type="submit"]').disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                // Reset button
                document.querySelector('button[type="submit"]').innerHTML = 'Place Order';
                document.querySelector('button[type="submit"]').disabled = false;
            });
        });
    }
});