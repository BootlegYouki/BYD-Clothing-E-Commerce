/**
 * Product management functionality for BYD Clothing Admin
 */

function confirmDelete(productId) {
    if(confirm("Are you sure you want to delete this product? This action cannot be undone.")) {
        window.location.href = "functions/code.php?action=delete_product&id=" + productId;
    }
}

// View product in modal instead of separate page
document.addEventListener('DOMContentLoaded', function() {
    // Get all view buttons
    const viewButtons = document.querySelectorAll('.view-product');
    
    // Add click event listener to each button
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const productId = this.getAttribute('data-product-id');
            viewProduct(productId);
        });
    });

    // Handle Featured toggle
    const featureToggles = document.querySelectorAll('.feature-toggle');
    featureToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const isChecked = this.checked ? 1 : 0;
            updateProductStatus(productId, 'is_featured', isChecked, this);
        });
    });
    
    // Handle New Release toggle
    const releaseToggles = document.querySelectorAll('.release-toggle');
    releaseToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const isChecked = this.checked ? 1 : 0;
            updateProductStatus(productId, 'is_new_release', isChecked, this);
        });
    });
});

function viewProduct(productId) {
    // Show loading indicator
    document.getElementById('viewProductModal').classList.add('loading');
    
    // Fetch product details
    fetch(`functions/products/get-product-details.php?id=${productId}`)
        .then(response => {
            // Check if response is OK (status in 200-299 range)
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json().catch(err => {
                throw new Error('Invalid JSON response: ' + err.message);
            });
        })
        .then(data => {
            // Check if there's an error in the response
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Populate modal with product details
            document.getElementById('productName').textContent = data.name || 'Unnamed Product';
            document.getElementById('productNameDetail').textContent = data.name || 'Unnamed Product';
            document.getElementById('productSku').textContent = data.sku || 'N/A';
            document.getElementById('productCategory').textContent = data.category || 'Uncategorized';
            document.getElementById('productDescription').textContent = data.description || 'No description available';
            
            // Set product image
            if (data.primary_image) {
                document.getElementById('productImage').src = '../' + data.primary_image;
                document.getElementById('productImage').alt = data.name;
            } else {
                document.getElementById('productImage').src = '../assets/img/no-image.png';
                document.getElementById('productImage').alt = 'No image available';
            }
            
            // Set additional images
            const additionalImagesContainer = document.getElementById('additionalImagesContainer');
            additionalImagesContainer.innerHTML = '';
            if (data.additional_images && data.additional_images.length > 0) {
                data.additional_images.forEach(img => {
                    additionalImagesContainer.innerHTML += `
                        <img src="../${img}" class="img-fluid border-radius-md shadow-sm" 
                             style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                             onclick="document.getElementById('productImage').src='../${img}'">
                    `;
                });
            }
            
            // Set price information with safe number handling
            const priceContainer = document.getElementById('priceContainer');
            const originalPrice = parseFloat(data.original_price) || 0;
            const discountPercentage = parseFloat(data.discount_percentage) || 0;
            
            if (discountPercentage > 0) {
                const salePrice = originalPrice - (originalPrice * discountPercentage / 100);
                priceContainer.innerHTML = `
                    <h6 class="font-weight-bold mb-0 me-2">₱${salePrice.toFixed(2)}</h6>
                    <p class="text-sm text-secondary mb-0"><del>₱${originalPrice.toFixed(2)}</del> (${discountPercentage}% off)</p>
                `;
            } else {
                priceContainer.innerHTML = `<h6 class="font-weight-bold mb-0">₱${originalPrice.toFixed(2)}</h6>`;
            }
            
            // Set featured and new release badges
            document.getElementById('featuredBadge').innerHTML = data.is_featured == 1 ? 
                '<span class="badge bg-gradient-dark">Featured</span>' : 
                '<span class="badge bg-gradient-secondary">Regular</span>';
                
            document.getElementById('newReleaseBadge').innerHTML = data.is_new_release == 1 ? 
                '<span class="badge" style="background: linear-gradient(195deg, #FF7F50, #FF6347)">New Release</span>' : 
                '<span class="badge bg-gradient-secondary">Standard</span>';
            
            // Set sizes and stock
            const sizeStockContainer = document.getElementById('sizeStockContainer');
            sizeStockContainer.innerHTML = '';
            if (data.sizes && Object.keys(data.sizes).length > 0) {
                Object.entries(data.sizes).forEach(([size, stock]) => {
                    const stockQty = parseInt(stock) || 0;
                    // Use dark background with white text for all stock badges
                    sizeStockContainer.innerHTML += `
                        <div class="text-center">
                            <span class="badge text-dark" style="background: white; border: 1px solid rgb(34, 34, 34);">${size}</span>
                            <span class="badge bg-gradient-dark"  style="border: 1px solid rgb(34, 34, 34);">${stockQty}</span>
                        </div>
                    `;
                });
            } else {
                sizeStockContainer.innerHTML = '<p class="text-sm text-danger">No stock information available</p>';
            }
            
            // Set edit button link
            document.getElementById('editProductBtn').href = `edit-product.php?id=${data.id}`;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching product details:', error);
            alert('Failed to load product details: ' + error.message);
        });
}

function updateProductStatus(productId, field, value, toggleElement) {
    // Disable the toggle while updating
    toggleElement.disabled = true;
    
    // Send AJAX request to update the status
    fetch('functions/products/update-product-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&field=${field}&value=${value}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success indicator - highlight the row briefly
            const row = toggleElement.closest('tr');
            row.classList.add('bg-light');
            setTimeout(() => {
                row.classList.remove('bg-light');
            }, 1000);
        } else {
            // Show error and revert toggle
            alert('Error updating product status: ' + data.message);
            toggleElement.checked = !toggleElement.checked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the product status.');
        toggleElement.checked = !toggleElement.checked;
    })
    .finally(() => {
        // Re-enable the toggle
        toggleElement.disabled = false;
    });
}