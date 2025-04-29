document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper
    initShopSwiper();
    
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
    
    // Setup AJAX filtering
    setupFilterHandlers();
});

/**
 * Initialize the shop swiper
 */
function initShopSwiper() {
    if (document.querySelector('.shop-swiper')) {
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
    }
}

/**
 * Updates or adds a query parameter to a URL
 * @param {string} uri - The current URL
 * @param {string} key - The parameter name to update/add
 * @param {string} value - The new value for the parameter
 * @returns {string} - The updated URL
 */
function updateQueryStringParameter(uri, key, value) {
    // Check if we have a URL object supported
    if (typeof URL === 'function') {
        try {
            // Create a URL object (handles relative and absolute URLs)
            const url = new URL(uri, window.location.origin);
            // Set the parameter
            url.searchParams.set(key, value);
            // Return the updated URL as string
            return url.toString();
        } catch (e) {
            console.error("Error updating URL parameters:", e);
        }
    }
    
    // Fallback implementation for older browsers
    const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    const separator = uri.indexOf('?') !== -1 ? "&" : "?";
    
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return uri + separator + key + "=" + value;
    }
}

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
 * Setup handlers for category filtering and sorting
 */
function setupFilterHandlers() {
    // Category filters (desktop)
    document.querySelectorAll('.category-filter').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            // Update UI for active state
            document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
            this.classList.add('active');
            
            // Update state and fetch products - properly decode the category
            window.shopState.category = decodeURIComponent(category).replace(/\+/g, ' ');
            window.shopState.page = 1; // Reset to page 1
            fetchProducts();
        });
    });
    
    // Category filters (mobile)
    document.querySelectorAll('.mobile-category-filter').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            // Update UI for active state
            document.querySelectorAll('.mobile-category-filter').forEach(f => {
                f.classList.remove('btn-dark');
                f.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-dark');
            
            // Close the mobile dropdown
            const categoryFilterMobile = document.getElementById('categoryFilterMobile');
            if (categoryFilterMobile) {
                const collapse = bootstrap.Collapse.getInstance(categoryFilterMobile);
                if (collapse) collapse.hide();
            }
            
            // Update state and fetch products - properly decode the category
            window.shopState.category = decodeURIComponent(category).replace(/\+/g, ' ');
            window.shopState.page = 1; // Reset to page 1
            fetchProducts();
        });
    });
    
    // Sort dropdown
    const sortDropdown = document.getElementById('product-sort');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            window.shopState.sort = this.value;
            window.shopState.page = 1; // Reset to page 1
            fetchProducts();
        });
    }
    
    // Clear filters button
    const clearFiltersButtons = document.querySelectorAll('#clear-filters, #clear-filters-mobile');
    clearFiltersButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Reset all state
            window.shopState = {
                category: '',
                search: window.shopState.search, // Keep search query
                sort: 'default',
                page: 1,
                view_product_id: 0,
            };
            
            // Update UI
            document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
            document.querySelectorAll('.mobile-category-filter').forEach(f => {
                f.classList.remove('btn-dark');
                f.classList.add('btn-outline-secondary');
            });
            
            if (sortDropdown) sortDropdown.value = 'default';
            
            // Fetch products with reset filters
            fetchProducts();
        });
    });
    
    // Pagination links
    document.addEventListener('click', function(e) {
        if (e.target.closest('.page-link')) {
            e.preventDefault();
            const pageLink = e.target.closest('.page-link');
            const page = pageLink.dataset.page;
            
            if (page && !pageLink.closest('.disabled')) {
                window.shopState.page = parseInt(page);
                fetchProducts();
                
                // Scroll to top of products section
                document.querySelector('#products').scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
}

/**
 * Fetch products using AJAX
 */
function fetchProducts() {
    // Show loading indicator
    const productsContainer = document.getElementById('products-container');
    if (productsContainer) {
        productsContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading products...</p>
            </div>
        `;
    }
    
    // Prepare the form data
    const formData = new FormData();
    formData.append('action', 'filter_products');
    
    // Add current state
    for (const key in window.shopState) {
        formData.append(key, window.shopState[key]);
    }
    
    console.log('Fetching products with filters:', window.shopState);
    
    // Fetch using AJAX
    fetch('functions/ajax/filter_products.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Products fetched successfully:', data.total_items);
            
            // Update products container
            if (productsContainer) {
                productsContainer.innerHTML = data.products_html;
            }
            
            // Update pagination container
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer) {
                paginationContainer.innerHTML = data.pagination_html;
            }
            
            // Update filter results header
            const filterResultsHeader = document.getElementById('filter-results-header');
            if (filterResultsHeader) {
                filterResultsHeader.innerHTML = data.header_html;
            }
            
            // Update product count displays
            const productsCount = document.getElementById('products-count');
            const mobileProductsCount = document.getElementById('mobile-products-count');
            
            if (productsCount) productsCount.textContent = `${data.total_items} products`;
            if (mobileProductsCount) mobileProductsCount.textContent = `${data.total_items} products`;
            
            // Initialize swiper again for mobile view
            initShopSwiper();
            
            // Update clear filter button visibility
            updateClearFilterButtons();
            
            // Update browser URL without refresh
            updateBrowserURL();
        } else {
            console.error('Error fetching products:', data.message, data.debug);
            productsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    ${data.message || 'An error occurred while loading products.'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
        if (productsContainer) {
            productsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    An error occurred while loading products. Please try again.
                </div>
            `;
        }
    });
}

/**
 * Update visibility of clear filter buttons based on current state
 */
function updateClearFilterButtons() {
    const clearFilters = document.getElementById('clear-filters');
    const clearFiltersMobile = document.getElementById('clear-filters-mobile');
    
    const shouldShow = window.shopState.category || 
                      window.shopState.search || 
                      window.shopState.sort !== 'default' || 
                      window.shopState.view_product_id > 0;
    
    if (clearFilters) {
        clearFilters.style.display = shouldShow ? 'inline-block' : 'none';
    }
    
    if (clearFiltersMobile) {
        clearFiltersMobile.style.display = shouldShow ? 'inline-block' : 'none';
    }
    
    // Ensure parent containers are visible if needed
    if (shouldShow) {
        const clearFilterParent = clearFilters?.parentElement;
        const clearFilterMobileParent = clearFiltersMobile?.parentElement;
        
        if (clearFilterParent) clearFilterParent.style.display = 'block';
        if (clearFilterMobileParent) clearFilterMobileParent.style.display = 'flex';
    }
}

/**
 * Update the browser URL without refreshing
 */
function updateBrowserURL() {
    const params = new URLSearchParams();
    
    if (window.shopState.category) {
        // Make sure category is properly encoded for URLs
        params.set('category', window.shopState.category);
    }
    
    if (window.shopState.search) params.set('search', window.shopState.search);
    if (window.shopState.sort !== 'default') params.set('sort', window.shopState.sort);
    if (window.shopState.page > 1) params.set('page', window.shopState.page);
    if (window.shopState.view_product_id > 0) params.set('view_product', window.shopState.view_product_id);
    
    const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
    window.history.pushState({ path: newUrl }, '', newUrl);
}