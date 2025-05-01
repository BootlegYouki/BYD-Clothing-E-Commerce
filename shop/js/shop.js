document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper
    initShopSwiper();
    
    // Setup filtering functionalities
    setupFilterHandlers();
    setupLazyLoading();
    
    // Initialize clear filter buttons visibility
    updateClearFilterButtons();
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
    if (typeof URL === 'function') {
        try {
            const url = new URL(uri, window.location.origin);
            url.searchParams.set(key, value);
            return url.toString();
        } catch (e) {
            console.error("Error updating URL parameters:", e);
        }
    }
    
    const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    const separator = uri.indexOf('?') !== -1 ? "&" : "?";
    
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return uri + separator + key + "=" + value;
    }
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
    document.querySelectorAll('.category-filter').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
            this.classList.add('active');
            
            window.shopState.category = decodeURIComponent(category).replace(/\+/g, ' ');
            window.shopState.page = 1;
            updateClearFilterButtons();
            fetchProducts();
        });
    });
    
    document.querySelectorAll('.mobile-category-filter').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            document.querySelectorAll('.mobile-category-filter').forEach(f => {
                f.classList.remove('btn-dark');
                f.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-dark');
            
            const categoryFilterMobile = document.getElementById('categoryFilterMobile');
            if (categoryFilterMobile) {
                const collapse = bootstrap.Collapse.getInstance(categoryFilterMobile);
                if (collapse) collapse.hide();
            }
            
            window.shopState.category = decodeURIComponent(category).replace(/\+/g, ' ');
            window.shopState.page = 1;
            updateClearFilterButtons();
            fetchProducts();
        });
    });
    
    const sortDropdown = document.getElementById('product-sort');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            window.shopState.sort = this.value;
            window.shopState.page = 1;
            fetchProducts();
        });
    }
    
    const clearFiltersButtons = document.querySelectorAll('#clear-filters, #clear-filters-mobile');
    clearFiltersButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            window.shopState = {
                category: '',
                search: '', // Changed: Clear search parameter as well
                sort: 'default',
                page: 1,
                view_product_id: 0,
            };
            
            document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
            document.querySelectorAll('.mobile-category-filter').forEach(f => {
                f.classList.remove('btn-dark');
                f.classList.add('btn-outline-secondary');
            });
            
            if (sortDropdown) sortDropdown.value = 'default';
            
            fetchProducts();
        });
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('.page-link')) {
            e.preventDefault();
            const pageLink = e.target.closest('.page-link');
            const page = pageLink.dataset.page;
            
            if (page && !pageLink.closest('.disabled')) {
                window.shopState.page = parseInt(page);
                fetchProducts();
                
                document.querySelector('#products').scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        // Handle "View all products" link clicks
        if (e.target.closest('#view-all-products')) {
            e.preventDefault();
            
            window.shopState = {
                category: '',
                search: '',
                sort: 'default',
                page: 1,
                view_product_id: 0,
            };
            
            document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
            document.querySelectorAll('.mobile-category-filter').forEach(f => {
                f.classList.remove('btn-dark');
                f.classList.add('btn-outline-secondary');
            });
            
            if (sortDropdown) sortDropdown.value = 'default';
            
            fetchProducts();
        }
    });
}

/**
 * Fetch products using AJAX
 */
function fetchProducts() {
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
    
    const formData = new FormData();
    formData.append('action', 'filter_products');
    
    for (const key in window.shopState) {
        formData.append(key, window.shopState[key]);
    }
    
    fetch('functions/productfetching/filter_products.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            
            if (productsContainer) {
                productsContainer.innerHTML = data.products_html;
            }
            
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer) {
                paginationContainer.innerHTML = data.pagination_html;
            }
            
            const filterResultsHeader = document.getElementById('filter-results-header');
            if (filterResultsHeader) {
                filterResultsHeader.innerHTML = data.header_html;
            }
            
            const productsCount = document.getElementById('products-count');
            const mobileProductsCount = document.getElementById('mobile-products-count');
            
            if (productsCount) productsCount.textContent = `${data.total_items} products`;
            if (mobileProductsCount) mobileProductsCount.textContent = `${data.total_items} products`;
            
            initShopSwiper();
            updateClearFilterButtons();
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
    
    // Only show clear buttons when category, search, or specific product view is active
    const shouldShow = window.shopState.category || 
                      window.shopState.search || 
                      window.shopState.view_product_id > 0;
    
    if (clearFilters) {
        clearFilters.style.display = shouldShow ? 'inline-block' : 'none';
    }
    
    if (clearFiltersMobile) {
        // Fix for mobile: Make sure to show the button itself, not just its parent
        clearFiltersMobile.style.display = shouldShow ? 'inline-block' : 'none';
        
        // Also ensure the parent container is visible
        if (clearFiltersMobile.parentElement) {
            clearFiltersMobile.parentElement.style.display = 'flex';
        }
    }
}

/**
 * Update the browser URL without refreshing
 */
function updateBrowserURL() {
    const params = new URLSearchParams();
    
    if (window.shopState.category) {
        params.set('category', window.shopState.category);
    }
    
    if (window.shopState.search) params.set('search', window.shopState.search);
    if (window.shopState.sort !== 'default') params.set('sort', window.shopState.sort);
    if (window.shopState.page > 1) params.set('page', window.shopState.page);
    if (window.shopState.view_product_id > 0) params.set('view_product', window.shopState.view_product_id);
    
    const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
    window.history.pushState({ path: newUrl }, '', newUrl);
}

/**
 * Redirects to product page
 * @param {number} productId - The ID of the product to view
 */
function viewProduct(productId) {
    window.location.href = `product.php?id=${productId}`;
}