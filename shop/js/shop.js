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