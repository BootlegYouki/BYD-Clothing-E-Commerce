document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper
    const shopSwiper = new Swiper('.shop-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20,
            }
        }
    });
    
    // Setup global size button click handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-size') || e.target.parentElement.classList.contains('btn-size')) {
            const button = e.target.classList.contains('btn-size') ? e.target : e.target.parentElement;
            const sizeButtons = document.querySelectorAll('.btn-size');
            sizeButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const selectedSize = button.dataset.size;
            const stockInfo = button.dataset.stock;
            
            document.getElementById('quick-view-size').value = selectedSize;
            document.getElementById('size-error').style.display = 'none';
            
            if (stockInfo) {
                const quantityInput = document.getElementById('quick-view-quantity');
                document.getElementById('stock-info').textContent = `Available: ${stockInfo} in stock`;
                document.getElementById('stock-info').style.display = 'block';
                
                quantityInput.max = stockInfo;
                if (parseInt(quantityInput.value) > parseInt(stockInfo)) {
                    quantityInput.value = stockInfo;
                }
                updateQuantityButtonStates();
            }
        }
    });
    
    // Setup quickView behaviors
    const productQuickView = document.getElementById('productQuickView');
    if (productQuickView) {
        productQuickView.addEventListener('show.bs.collapse', function () {
            setTimeout(() => this.scrollIntoView({ behavior: 'smooth', block: 'center' }), 300);
        });
        
        setupQuantityControls();
    }
    
    setupLazyLoading();
});

/**
 * Sets up quantity controls for quick view
 */
function setupQuantityControls() {
    const quantityInput = document.getElementById('quick-view-quantity');
    const minusBtn = document.getElementById('quick-view-quantity-minus');
    const plusBtn = document.getElementById('quick-view-quantity-plus');
    
    if (!quantityInput || !minusBtn || !plusBtn) return;
    
    minusBtn.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = --currentValue;
            updateQuantityButtonStates();
        }
    });
    
    plusBtn.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        const max = parseInt(quantityInput.getAttribute('max')) || 10;
        if (currentValue < max) {
            quantityInput.value = ++currentValue;
            updateQuantityButtonStates();
        }
    });
    
    updateQuantityButtonStates();
}

function updateQuantityButtonStates() {
    const quantityInput = document.getElementById('quick-view-quantity');
    const minusBtn = document.getElementById('quick-view-quantity-minus');
    const plusBtn = document.getElementById('quick-view-quantity-plus');
    
    if (!quantityInput || !minusBtn || !plusBtn) return;
    
    const currentValue = parseInt(quantityInput.value);
    const max = parseInt(quantityInput.getAttribute('max')) || 10;
    
    minusBtn.classList.toggle('disabled', currentValue <= 1);
    plusBtn.classList.toggle('disabled', currentValue >= max);
}

function setupLazyLoading() {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    if (!lazyImages.length) return;
    
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
}

/**
 * Show Quick View function for displaying product details in collapse panel
 * @param {Object} product - The product object containing all product data
 * @param {Event} event - The click event
 */
function showQuickView(product, event) {
    if (!event) return;
    
    const clickedCard = event.target.closest('.product-card');
    if (!clickedCard) return;
    
    const quickViewCollapse = document.getElementById('productQuickView');
    if (!quickViewCollapse) return;
    
    const container = quickViewCollapse.closest('.container');
    if (!container) return;
    
    let targetRow;
    
    if (window.innerWidth >= 768) {
        targetRow = clickedCard.closest('.product-row');
    } else {
        targetRow = document.querySelector('.shop-swiper');
    }
    
    if (!targetRow) return;
    
    // Store the clicked product card for scroll-back functionality
    quickViewCollapse.dataset.sourceProduct = clickedCard.id || Date.now();
    
    if (!clickedCard.id) {
        clickedCard.id = 'product-' + quickViewCollapse.dataset.sourceProduct;
    }
    
    const closeButton = quickViewCollapse.querySelector('.btn-close');
    if (closeButton) {
        closeButton.removeEventListener('click', scrollBackToProduct);
        
        closeButton.addEventListener('click', function() {
            scrollBackToProduct(clickedCard);
        });
    }
    
    targetRow.after(container);
    
    document.querySelector('.quick-view-title').textContent = product.title;
    document.querySelector('.quick-view-img').src = product.image;
    document.querySelector('.quick-view-category').textContent = product.category;
    
    const skuElement = document.querySelector('.quick-view-sku');
    if (skuElement) {
        skuElement.style.display = 'none';
    }
    
    const descriptionElement = document.querySelector('.quick-view-description');
    if (descriptionElement) {
        if (product.description && product.description.trim() !== '') {
            descriptionElement.innerHTML = `
                <h6 class="fw-bold mb-2">Description</h6>
                <p class="mb-0">${product.description}</p>
            `;
            descriptionElement.style.display = 'block';
        } else {
            descriptionElement.style.display = 'none';
        }
    }
    
    document.getElementById('quick-view-size').value = '';
    document.getElementById('quick-view-quantity').value = 1;
    document.getElementById('size-error').style.display = 'none';
    
    displayPriceInfo(product);
    generateSizeButtons(product);
    generateThumbnails(product);
    setupAddToCartButton(product);
}

function displayPriceInfo(product) {
    const priceContainer = document.querySelector('.quick-view-price-container');
    const originalPrice = parseFloat(product.price);
    const discountPercentage = parseFloat(product.discount_percentage || 0);
    
    if (isNaN(originalPrice)) {
        priceContainer.innerHTML = '<span class="quick-view-current-price">Price not available</span>';
        return;
    }
    
    let discountedPrice = originalPrice;
    
    if (discountPercentage > 0) {
        discountedPrice = originalPrice - (originalPrice * (discountPercentage / 100));
        priceContainer.innerHTML = `
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center">
                <span class="quick-view-original-price d-block d-md-inline mb-1 mb-md-0 me-md-3">₱${originalPrice.toFixed(2)}</span>
                <div class="d-flex align-items-center">
                    <span class="quick-view-current-price me-2">₱${discountedPrice.toFixed(2)}</span>
                    <span class="quick-view-discount">-${discountPercentage}%</span>
                </div>
            </div>
        `;
    } else {
        priceContainer.innerHTML = `<span class="quick-view-current-price">₱${originalPrice.toFixed(2)}</span>`;
    }
}

/**
 * Generates size buttons based on available sizes
 */
function generateSizeButtons(product) {
    const sizeButtonsContainer = document.getElementById('size-buttons-container');
    const stockInfoElement = document.getElementById('stock-info');
    sizeButtonsContainer.innerHTML = '';
    
    const availableSizes = product.availableSizes || {};
    
    if (Object.keys(availableSizes).length > 0) {
        let hasAvailableStock = false;
        let hasMedium = false;
        let firstAvailableSize = null;
        let mediumButton = null;
        
        for (const size in availableSizes) {
            const stock = availableSizes[size];
            const sizeCol = document.createElement('div');
            sizeCol.className = 'col-auto';
            
            const sizeButton = document.createElement('button');
            sizeButton.type = 'button';
            sizeButton.className = 'btn-size';
            sizeButton.dataset.size = size;
            sizeButton.dataset.stock = stock;
            sizeButton.textContent = size;
            
            if (stock <= 0) {
                sizeButton.disabled = true;
                sizeButton.classList.add('out-of-stock');
            } else {
                hasAvailableStock = true;
                
                if (!firstAvailableSize) {
                    firstAvailableSize = sizeButton;
                }
                
                if (size === 'M') {
                    hasMedium = true;
                    mediumButton = sizeButton;
                }
            }
            
            sizeCol.appendChild(sizeButton);
            sizeButtonsContainer.appendChild(sizeCol);
        }
        
        if (!hasAvailableStock) {
            sizeButtonsContainer.innerHTML = '<div class="col-12"><p class="text-danger">Out of stock</p></div>';
            document.getElementById('quick-view-add-to-cart').disabled = true;
            stockInfoElement.style.display = 'none';
        } else {
            document.getElementById('quick-view-add-to-cart').disabled = false;
            
            setTimeout(() => {
                if (hasMedium && mediumButton) {
                    mediumButton.click();
                } else if (firstAvailableSize) {
                    firstAvailableSize.click();
                }
            }, 100);
        }
    } else {
        sizeButtonsContainer.innerHTML = '<div class="col-12"><p class="text-muted">No Available stocks right.</p></div>';
        document.getElementById('quick-view-size').value = 'OS';
        stockInfoElement.style.display = 'none';
    }
}

/**
 * Generates thumbnails for the product
 */
function generateThumbnails(product) {
    const thumbnailContainer = document.querySelector('.thumbnail-container');
    const mainImg = document.querySelector('.quick-view-img');
    thumbnailContainer.innerHTML = '';
    
    const additionalImages = product.additionalImages || [];
    const allImages = [product.image, ...additionalImages];
    
    if (additionalImages.length === 0) {
        const thumbnailDiv = document.createElement('div');
        thumbnailDiv.className = 'thumbnail active';
        thumbnailDiv.dataset.imgSrc = product.image;
        
        const img = document.createElement('img');
        img.src = product.image;
        img.alt = product.title;
        img.className = 'img-fluid';
        
        thumbnailDiv.appendChild(img);
        thumbnailContainer.appendChild(thumbnailDiv);
    } else {
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
            
            thumbnailDiv.addEventListener('click', function() {
                mainImg.src = this.dataset.imgSrc;
                
                document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
}

function resetQuantitySelection() {
    const quantityInput = document.getElementById('quick-view-quantity');
    if (quantityInput) {
        quantityInput.value = 1;
        updateQuantityButtonStates();
    }
}

/**
 * Scrolls back to the originally clicked product
 */
function scrollBackToProduct(clickedProduct) {
    setTimeout(() => {
        if (clickedProduct) {
            clickedProduct.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center'
            });
        }
    }, 300);
}

/**
 * Closes the product details quick view
 */
function closeProductDetails() {
    const quickView = document.getElementById('productQuickView');
    if (quickView) {
        const bsCollapse = bootstrap.Collapse.getInstance(quickView);
        if (bsCollapse) {
            bsCollapse.hide();
            
            const sourceProductId = quickView.dataset.sourceProduct;
            if (sourceProductId) {
                const sourceProduct = document.getElementById(sourceProductId) || 
                                     document.getElementById('product-' + sourceProductId);
                if (sourceProduct) {
                    scrollBackToProduct(sourceProduct);
                }
            }
        }
    }
}

/**
 * Sets up the Add to Cart button functionality
 * @param {Object} product - The product object containing product details
 */
function setupAddToCartButton(product) {
    const addToCartBtn = document.getElementById('quick-view-add-to-cart');
    if (!addToCartBtn) return;
    
    // Remove any previous event listeners
    const newAddToCartBtn = addToCartBtn.cloneNode(true);
    addToCartBtn.parentNode.replaceChild(newAddToCartBtn, addToCartBtn);
    
    newAddToCartBtn.addEventListener('click', function() {
        const selectedSize = document.getElementById('quick-view-size').value;
        const quantity = parseInt(document.getElementById('quick-view-quantity').value);
        
        // Validate size selection
        if (!selectedSize) {
            document.getElementById('size-error').style.display = 'block';
            return;
        }
        
        // Calculate the actual price (considering discounts)
        let price = parseFloat(product.price);
        if (product.discount_percentage > 0) {
            price = price - (price * (product.discount_percentage / 100));
        }
        
        // Create cart item object
        const cartItem = {
            id: product.id,
            title: product.title,
            price: price,
            image: product.image,
            size: selectedSize,
            quantity: quantity,
            category: product.category,
            sku: product.sku || '',
            maxQuantity: parseInt(document.querySelector('.btn-size.active')?.dataset.stock || 10)
        };
        
        // Add to cart
        addItemToCart(cartItem);
        
        // Show success feedback on button
        const originalText = newAddToCartBtn.textContent;
        newAddToCartBtn.innerHTML = '<i class="fa fa-check me-2"></i>ADDED TO CART';
        newAddToCartBtn.classList.add('btn-success');
        
        // Show toast notification
        showAddToCartNotification(product.title, selectedSize, quantity);
        
        setTimeout(() => {
            newAddToCartBtn.textContent = originalText;
            newAddToCartBtn.classList.remove('btn-success');
            
            // Close the quick view after adding to cart
            closeProductDetails();
        }, 1500);
    });
}

/**
 * Shows a notification that item was added to cart
 * @param {string} productTitle - The product title
 * @param {string} size - Selected size
 * @param {number} quantity - Selected quantity
 */
function showAddToCartNotification(productTitle, size, quantity) {
    // Check if a notification container exists, if not create one
    let notificationContainer = document.getElementById('cart-notification-container');
    
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'cart-notification-container';
        notificationContainer.className = 'position-fixed top-0 end-0 p-3';
        notificationContainer.style.zIndex = '1060';
        document.body.appendChild(notificationContainer);
    } else {
        // Make sure the container is visible
        notificationContainer.style.display = 'block';
    }
    
    // Create a unique ID for this toast
    const toastId = 'cart-toast-' + Date.now();
    
    // Create the toast element
    const toastElement = document.createElement('div');
    toastElement.id = toastId;
    toastElement.className = 'toast cart-notification show';
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');
    toastElement.innerHTML = `
        <div class="toast-header bg-success text-white">
            <i class="fa fa-check-circle me-2"></i>
            <strong class="me-auto">Success</strong>
            <small>just now</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-0"><strong>${productTitle}</strong> added to cart</p>
                    <p class="mb-0 small text-muted">Size: ${size} | Quantity: ${quantity}</p>
                </div>
                <button class="btn btn-sm btn-outline-dark ms-3 view-cart-btn">
                    View Cart
                </button>
            </div>
        </div>
    `;
    
    // Append the toast to the container
    notificationContainer.appendChild(toastElement);
    
    // Create a new Bootstrap Toast instance
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    
    // Show the toast
    toast.show();
    
    // Add event listener for View Cart button
    const viewCartBtn = toastElement.querySelector('.view-cart-btn');
    viewCartBtn.addEventListener('click', function() {
        toast.dispose(); // Properly dispose of toast instance
        toastElement.remove(); // Remove the element
        
        // Show cart
        const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
        cartOffcanvas.show();
    });
    
    // When toast is hidden, remove it from the DOM
    toastElement.addEventListener('hidden.bs.toast', function() {
        toast.dispose(); // Properly dispose of toast instance
        toastElement.remove();
        
        // If there are no more toasts in the container, hide the container
        if (notificationContainer.children.length === 0) {
            notificationContainer.style.display = 'none';
        }
    });
    
    // Also add an event listener to the close button
    const closeButton = toastElement.querySelector('.btn-close');
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            toast.dispose(); // Properly dispose of toast instance
            toastElement.remove();
            
            // If there are no more toasts in the container, hide the container
            if (notificationContainer.children.length === 0) {
                notificationContainer.style.display = 'none';
            }
        });
    }
}

/**
 * Adds an item to the shopping cart
 * @param {Object} item - The item to add to the cart
 */
function addItemToCart(item) {
    // Get current cart from localStorage or initialize empty array
    let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    
    // Check if item already exists in cart (same product ID and size)
    const existingItemIndex = cart.findIndex(cartItem => 
        cartItem.id === item.id && cartItem.size === item.size
    );
    
    if (existingItemIndex !== -1) {
        // Item exists, update quantity (not exceeding max)
        const newQuantity = Math.min(
            cart[existingItemIndex].quantity + item.quantity,
            item.maxQuantity
        );
        cart[existingItemIndex].quantity = newQuantity;
    } else {
        // Item doesn't exist, add to cart
        cart.push(item);
    }
    
    // Save updated cart to localStorage
    localStorage.setItem('shopping-cart', JSON.stringify(cart));
    
    // Update cart UI
    updateCartUI();
}
/**
 * Updates the shopping cart UI
 */
function updateCartUI() {
    const cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    const cartItemsContainer = document.getElementById('cart-items');
    const emptyCart = document.getElementById('empty-cart');
    const cartItemsWrapper = document.getElementById('cart-items-container');
    
    // Show/hide empty cart message
    if (cart.length === 0) {
        emptyCart.classList.remove('d-none');
        cartItemsWrapper.classList.add('d-none');
    } else {
        emptyCart.classList.add('d-none');
        cartItemsWrapper.classList.remove('d-none');
    }
    
    // Clear existing items
    if (cartItemsContainer) {
        cartItemsContainer.innerHTML = '';
    }
    
    // Calculate totals
    let subtotal = 0;
    
    // Add each item to the cart UI
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        if (cartItemsContainer) {
            const cartItemElement = document.createElement('div');
            cartItemElement.className = 'cart-item mb-3';
            cartItemElement.innerHTML = `
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="cart-item-img me-3">
                                <img src="${item.image}" alt="${item.title}" class="img-fluid rounded">
                            </div>
                            <div class="cart-item-details flex-grow-1">
                                <h6 class="mb-1">${item.title}</h6>
                                <p class="mb-1 text-muted small">Size: ${item.size} | Qty: ${item.quantity}</p>
                                <p class="mb-0 fw-bold">₱${(item.price * item.quantity).toFixed(2)}</p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            cartItemsContainer.appendChild(cartItemElement);
            
            // Add remove item event listener
            const removeBtn = cartItemElement.querySelector('.remove-item');
            removeBtn.addEventListener('click', function() {
                removeCartItem(index);
            });
        }
    });
    
    // Update totals in the UI
    const subtotalElement = document.getElementById('cart-subtotal');
    const totalElement = document.getElementById('cart-total');
    if (subtotalElement) subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
    if (totalElement) totalElement.textContent = `₱${subtotal.toFixed(2)}`;
    
    // Update all cart badges on the page
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartBadges = document.querySelectorAll('.cart-badge');
    
    cartBadges.forEach(badge => {
        if (badge) {
            badge.textContent = totalItems;
            badge.style.display = totalItems > 0 ? '' : 'none';
        }
    });
}

/**
 * Removes an item from the shopping cart
 * @param {number} index - The index of the item to remove
 */
function removeCartItem(index) {
    // Get current cart
    let cart = JSON.parse(localStorage.getItem('shopping-cart')) || [];
    
    // Remove the item at the specified index
    if (index >= 0 && index < cart.length) {
        cart.splice(index, 1);
        
        // Save updated cart
        localStorage.setItem('shopping-cart', JSON.stringify(cart));
        
        // Update UI immediately
        updateCartUI();
    }
}

// Initialize cart UI when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
    
    // Check if offcanvasCart exists before trying to use it
    const offcanvasCart = document.getElementById('offcanvasCart');
    if (offcanvasCart) {
        offcanvasCart.addEventListener('show.bs.offcanvas', function() {
            // Refresh cart UI when cart is opened
            updateCartUI();
            
            // Hide notification container when cart is open to prevent overlap
            const notificationContainer = document.getElementById('cart-notification-container');
            if (notificationContainer) {
                notificationContainer.style.display = 'none';
            }
        });
        
        // Ensure the close button is accessible by giving it a higher z-index
        const closeButton = offcanvasCart.querySelector('.btn-close');
        if (closeButton) {
            closeButton.style.zIndex = '1070'; // Higher than notification container
            closeButton.style.position = 'relative'; // Ensure z-index works
        }
    }
    
    // Handle click on the cart close button explicitly
    document.querySelectorAll('#offcanvasCart .btn-close').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const offcanvasCart = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasCart'));
            if (offcanvasCart) {
                offcanvasCart.hide();
            }
        });
    });
});