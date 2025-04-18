/**
 * Order Tracking JavaScript
 * 
 * This script handles the order tracking functionality:
 * 1. Loads orders from the server
 * 2. Displays orders based on selected tab
 * 3. Handles order actions (confirm receipt, rate product, etc.)
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get the active tab from URL parameters or use 'all' as default
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'all';
    
    // Set the active tab
    document.querySelector(`#orderTabs .nav-link[data-tab="${activeTab}"]`).classList.add('active');
    
    // Load orders when page loads
    loadOrders(activeTab);
    
    // Tab click event
    document.querySelectorAll('#orderTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            document.querySelectorAll('#orderTabs .nav-link').forEach(t => {
                t.classList.remove('active');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Load orders for selected tab
            const tabName = this.getAttribute('data-tab');
            loadOrders(tabName);
        });
    });
    
    // Star rating functionality
    document.querySelectorAll('#star-rating .star').forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            
            // Remove active class from all stars
            document.querySelectorAll('#star-rating .star').forEach(s => {
                s.classList.remove('active');
            });
            
            // Add active class to selected star and all stars before it
            document.querySelectorAll(`#star-rating .star[data-value="${value}"], #star-rating .star[data-value="${value}"] ~ .star`).forEach(s => {
                if (parseInt(s.getAttribute('data-value')) <= parseInt(value)) {
                    s.classList.add('active');
                }
            });
        });
    });
    
    // Confirm received button click
    document.getElementById('confirmReceivedBtn').addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');
        confirmOrderReceived(orderId);
    });
    
    // Submit rating button click
    document.getElementById('submitRatingBtn').addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');
        const productId = this.getAttribute('data-product-id');
        const rating = document.querySelectorAll('#star-rating .star.active').length;
        const review = document.getElementById('review-text').value;
        
        submitProductRating(orderId, productId, rating, review);
    });
});

/**
 * Load orders from the server
 * 
 * @param {string} status Order status to filter by
 */
function loadOrders(status = 'all') {
    // Show loading state
    document.getElementById('ordersContainer').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading orders...</p></div>';
    
    // Fetch orders from server
    fetch('functions/get_orders.php?status=' + status)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrders(data.orders, status);
            } else {
                // Show error message
                document.getElementById('ordersContainer').innerHTML = `<div class="text-center py-5"><i class="fas fa-exclamation-circle fa-3x mb-3 text-danger"></i><p>${data.message || 'Failed to load orders'}</p></div>`;
            }
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            document.getElementById('ordersContainer').innerHTML = '<div class="text-center py-5"><i class="fas fa-exclamation-circle fa-3x mb-3 text-danger"></i><p>Failed to load orders. Please try again later.</p></div>';
        });
}

/**
 * Display orders in the UI
 * 
 * @param {Array} orders Array of order objects
 * @param {string} status Current tab/status filter
 */
function displayOrders(orders, status) {
    const container = document.getElementById('ordersContainer');
    
    // Clear container
    container.innerHTML = '';
    
    // If no orders, show message
    if (orders.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-3x mb-3" style="color: #ff7f50;"></i>
                <p>No ${status !== 'all' ? status.replace('_', ' ') + ' ' : ''}orders found.</p>
                <a href="shop.php" class="btn-con mt-2">Shop Now</a>
            </div>
        `;
        return;
    }
    
    // Get order template
    const orderTemplate = document.getElementById('order-template');
    const orderItemTemplate = document.getElementById('order-item-template');
    
    // Create and append order cards
    orders.forEach(order => {
        // Clone order template
        const orderCard = orderTemplate.cloneNode(true);
        orderCard.classList.remove('d-none');
        orderCard.removeAttribute('id');
        
        // Set order details
        orderCard.querySelector('.order-id').textContent = order.id;
        orderCard.querySelector('.order-date').textContent = formatDate(order.created_at);
        
        // Set status badge
        const statusBadge = orderCard.querySelector('.status-badge');
        statusBadge.textContent = formatStatus(order.status);
        statusBadge.classList.add('status-' + order.status.replace(' ', '-'));
        
        // Set total amount
        orderCard.querySelector('.total-amount').textContent = parseFloat(order.total_amount).toFixed(2);
        
        // Add order items
        const itemsContainer = orderCard.querySelector('.order-items-container');
        
        order.items.forEach(item => {
            // Clone item template
            const itemElement = orderItemTemplate.cloneNode(true);
            itemElement.classList.remove('d-none');
            itemElement.removeAttribute('id');
            
            // Set item details
            itemElement.querySelector('.product-name').textContent = item.product_name;
            itemElement.querySelector('.product-size').textContent = 'Size: ' + item.size;
            itemElement.querySelector('.product-quantity').textContent = item.quantity;
            itemElement.querySelector('.product-price').textContent = '₱' + parseFloat(item.price).toFixed(2) + ' each';
            itemElement.querySelector('.product-subtotal').textContent = '₱' + parseFloat(item.subtotal).toFixed(2);
            
            // Set product image if available
            if (item.image) {
                itemElement.querySelector('.product-image').src = item.image;
            } else {
                itemElement.querySelector('.product-image').src = 'img/products/placeholder.jpg';
            }
            
            // Add item to container
            itemsContainer.appendChild(itemElement);
        });
        
        // Add action buttons based on order status
        const actionsContainer = orderCard.querySelector('.order-actions');
        
        switch (order.status) {
            case 'to_pay':
                actionsContainer.innerHTML = `<a href="checkout.php?order_id=${order.id}" class="btn-con">Pay Now</a>`;
                break;
                
            case 'to_ship':
                actionsContainer.innerHTML = `<button class="btn-con" onclick="viewOrderDetails(${order.id})">View Details</button>`;
                break;
                
            case 'to_receive':
                actionsContainer.innerHTML = `
                    <button class="btn-con me-2" onclick="showReceiveModal(${order.id})">Received</button>
                    <button class="btn btn-outline-dark" onclick="viewOrderDetails(${order.id})">View Details</button>
                `;
                break;
                
            case 'completed':
                actionsContainer.innerHTML = `
                    <button class="btn-con me-2" onclick="viewOrderDetails(${order.id})">View Details</button>
                    <button class="btn btn-outline-dark" onclick="buyAgain(${order.id})">Buy Again</button>
                `;
                break;
                
            case 'to_review':
                actionsContainer.innerHTML = `
                    <button class="btn-con me-2" onclick="showRateModal(${order.id}, ${order.items[0].product_id}, '${order.items[0].product_name}')">Rate</button>
                    <button class="btn btn-outline-dark" onclick="viewOrderDetails(${order.id})">View Details</button>
                `;
                break;
                
            default:
                actionsContainer.innerHTML = `<button class="btn-con" onclick="viewOrderDetails(${order.id})">View Details</button>`;
        }
        
        // Add order card to container
        container.appendChild(orderCard);
    });
}

/**
 * Format date to readable string
 * 
 * @param {string} dateString Date string from server
 * @return {string} Formatted date string
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Format status to readable string
 * 
 * @param {string} status Status from server
 * @return {string} Formatted status string
 */
function formatStatus(status) {
    switch (status) {
        case 'to_pay':
            return 'To Pay';
        case 'to_ship':
            return 'To Ship';
        case 'to_receive':
            return 'To Receive';
        case 'completed':
            return 'Completed';
        case 'to_review':
            return 'To Review';
        default:
            return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
    }
}

/**
 * Show order received confirmation modal
 * 
 * @param {number} orderId Order ID
 */
function showReceiveModal(orderId) {
    document.getElementById('confirmReceivedBtn').setAttribute('data-order-id', orderId);
    new bootstrap.Modal(document.getElementById('orderReceivedModal')).show();
}

/**
 * Show rate product modal
 * 
 * @param {number} orderId Order ID
 * @param {number} productId Product ID
 * @param {string} productName Product name
 */
function showRateModal(orderId, productId, productName) {
    // Reset star rating
    document.querySelectorAll('#star-rating .star').forEach(star => {
        star.classList.remove('active');
    });
    
    // Reset review text
    document.getElementById('review-text').value = '';
    
    // Set product details
    document.getElementById('rating-product-name').textContent = productName;
    
    // Set order and product IDs
    document.getElementById('submitRatingBtn').setAttribute('data-order-id', orderId);
    document.getElementById('submitRatingBtn').setAttribute('data-product-id', productId);
    
    // Show modal
    new bootstrap.Modal(document.getElementById('rateProductModal')).show();
}

/**
 * Confirm order received
 * 
 * @param {number} orderId Order ID
 */
function confirmOrderReceived(orderId) {
    fetch('functions/update_order_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_id=${orderId}&status=completed`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload orders
            const activeTab = document.querySelector('#orderTabs .nav-link.active').getAttribute('data-tab');
            loadOrders(activeTab);
            
            // Show success message
            alert('Order marked as received. Thank you for shopping with us!');
        } else {
            alert(data.message || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error updating order status:', error);
        alert('Failed to update order status. Please try again later.');
    });
}

/**
 * Submit product rating
 * 
 * @param {number} orderId Order ID
 * @param {number} productId Product ID
 * @param {number} rating Rating value (1-5)
 * @param {string} review Review text
 */
function submitProductRating(orderId, productId, rating, review) {
    fetch('functions/submit_rating.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_id=${orderId}&product_id=${productId}&rating=${rating}&review=${review}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload orders
            const activeTab = document.querySelector('#orderTabs .nav-link.active').getAttribute('data-tab');
            loadOrders(activeTab);
            
            // Show success message
            alert('Thank you for your feedback!');
        } else {
            alert(data.message || 'Failed to submit rating');
        }
    })
    .catch(error => {
        console.error('Error submitting rating:', error);
        alert('Failed to submit rating. Please try again later.');
    });
}

/**
 * View order details
 * 
 * @param {number} orderId Order ID
 */
function viewOrderDetails(orderId) {
    fetch(`functions/get_order_details.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrderDetails(data.order);
                new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
            } else {
                alert(data.message || 'Failed to load order details');
            }
        })
        .catch(error => {
            console.error('Error loading order details:', error);
            alert('Failed to load order details. Please try again later.');
        });
}

/**
 * Display order details in modal
 * 
 * @param {Object} order Order details
 */
function displayOrderDetails(order) {
    // Set order details
    document.getElementById('detail-order-id').textContent = order.id;
    document.getElementById('detail-order-date').textContent = formatDate(order.created_at);
    document.getElementById('detail-order-status').textContent = formatStatus(order.status);
    document.getElementById('detail-payment-method').textContent = order.payment_method;
    document.getElementById('detail-payment-id').textContent = order.payment_id;
    document.getElementById('detail-payment-status').textContent = order.payment_status;
    
    // Set customer details
    document.getElementById('detail-customer-name').textContent = order.firstname + ' ' + order.lastname;
    document.getElementById('detail-customer-email').textContent = order.email;
    document.getElementById('detail-customer-phone').textContent = order.phone;
    document.getElementById('detail-customer-address').textContent = order.address;
    document.getElementById('detail-customer-city').textContent = order.city;
    document.getElementById('detail-customer-zipcode').textContent = order.zipcode;
    
    // Set order items
    const itemsContainer = document.getElementById('detail-order-items');
    itemsContainer.innerHTML = '';
    
    order.items.forEach(item => {
        itemsContainer.innerHTML += `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <div style="width: 50px; height: 50px; overflow: hidden;" class="flex-shrink-0 me-3">
                        <img src="${item.image || 'img/products/placeholder.jpg'}" class="img-fluid" alt="${item.product_name}">
                    </div>
                    <div>
                        <h6 class="mb-0">${item.product_name}</h6>
                        <p class="small text-muted mb-0">Size: ${item.size} | Qty: ${item.quantity}</p>
                    </div>
                </div>
                <div class="text-end">
                    <p class="mb-0">₱${parseFloat(item.subtotal).toFixed(2)}</p>
                </div>
            </div>
        `;
    });
    
    // Set order totals
    document.getElementById('detail-subtotal').textContent = parseFloat(order.subtotal).toFixed(2);
    document.getElementById('detail-shipping').textContent = parseFloat(order.shipping_cost).toFixed(2);
    document.getElementById('detail-total').textContent = parseFloat(order.total_amount).toFixed(2);
    
    // Set action buttons based on order status
    const actionsContainer = document.getElementById('detail-action-buttons');
    
    switch (order.status) {
        case 'to_pay':
            actionsContainer.innerHTML = `<a href="checkout.php?order_id=${order.id}" class="btn-con">Pay Now</a>`;
            break;
            
        case 'to_receive':
            actionsContainer.innerHTML = `<button class="btn-con" onclick="showReceiveModal(${order.id})">Confirm Received</button>`;
            break;
            
        case 'to_review':
            actionsContainer.innerHTML = `<button class="btn-con" onclick="showRateModal(${order.id}, ${order.items[0].product_id}, '${order.items[0].product_name}')">Rate Product</button>`;
            break;
            
        default:
            actionsContainer.innerHTML = '';
    }
}

/**
 * Buy again functionality
 * 
 * @param {number} orderId Order ID
 */
function buyAgain(orderId) {
    fetch(`functions/get_order_items.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add items to cart
                addItemsToCart(data.items);
                
                // Redirect to cart page
                window.location.href = 'cart.php';
            } else {
                alert(data.message || 'Failed to load order items');
            }
        })
        .catch(error => {
            console.error('Error loading order items:', error);
            alert('Failed to load order items. Please try again later.');
        });
}

/**
 * Add items to cart
 * 
 * @param {Array} items Array of order items
 */
function addItemsToCart(items) {
    // Get current cart
    let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    
    // Add items to cart
    items.forEach(item => {
        cart.push({
            id: item.product_id,
        })
    });
}