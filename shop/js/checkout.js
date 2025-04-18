/**
 * Checkout Page JavaScript
 * 
 * This script handles the checkout process:
 * 1. Loads cart data from localStorage
 * 2. Displays order summary
 * 3. Calculates totals
 * 4. Handles form submission and payment processing
 */
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
        
        // Generate HTML for each cart item
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
    
    // Update subtotal display
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
        // Modify the form submission handler
        checkoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing...';
            submitBtn.disabled = true;

            try {
                // Get form data
                const formData = new FormData(checkoutForm);
                
                // Add cart items to form data
                formData.append('cart_items', JSON.stringify(cart));
                formData.append('subtotal', subtotal.toFixed(2));
                formData.append('shipping_cost', SHIPPING_FEE.toFixed(2));
                formData.append('total', (subtotal + SHIPPING_FEE).toFixed(2));
                
                console.log('Submitting payment with total:', (subtotal + SHIPPING_FEE).toFixed(2));
                
                // Submit order data to backend
                const response = await fetch('functions/process_payment.php', {
                    method: 'POST',
                    body: formData
                });
                
                // Log the raw response for debugging
                const responseText = await response.text();
                console.log('Raw API response:', responseText);
                
                // Parse the JSON response - handle potential double JSON issue
                let data;
                try {
                    // Check if response contains multiple JSON objects
                    if (responseText.includes('}{')) {
                        // Take only the first JSON object
                        const firstJsonEnd = responseText.indexOf('}{') + 1;
                        const firstJsonPart = responseText.substring(0, firstJsonEnd);
                        console.log('Parsing first JSON part:', firstJsonPart);
                        data = JSON.parse(firstJsonPart);
                    } else {
                        data = JSON.parse(responseText);
                    }
                } catch (e) {
                    throw new Error('Invalid JSON response from server: ' + responseText);
                }
                
                if (data.success) {
                    console.log('Payment URL received:', data.payment_url);
                    // Clear cart before redirecting
                    localStorage.removeItem('shopping-cart');
                    
                    // Store the order ID in session storage for retrieval after payment
                    sessionStorage.setItem('pending_order_id', data.order_id);
                    
                    // Open payment URL in a new tab
                    window.open(data.payment_url, '_blank');
                    
                    // Redirect the current page to homepage
                    window.location.href = data.redirect_url || 'index.php';
                } else {
                    throw new Error(data.message || 'Payment processing failed');
                }
            } catch (error) {
                // Handle errors
                console.error('Payment Error:', error);
                alert('Payment failed: ' + (error.message || 'Unknown error occurred'));
                submitBtn.innerHTML = 'Proceed to Payment';
                submitBtn.disabled = false;
            }
        });
    }
});