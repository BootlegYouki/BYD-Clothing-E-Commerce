// MODAL ANIMATION 
document.getElementById('cartModal').addEventListener('hide.bs.modal', function(e) {
    if (this.dataset.programmaticHide) return;
    
    e.preventDefault();
    this.classList.add('modal-closing');
    
    const dialog = this.querySelector('.modal-dialog');
    const onTransitionEnd = () => {
      dialog.removeEventListener('transitionend', onTransitionEnd);
      this.dataset.programmaticHide = 'true';
      bootstrap.Modal.getInstance(this).hide();
      delete this.dataset.programmaticHide;
      this.classList.remove('modal-closing');
    };
    
    dialog.addEventListener('transitionend', onTransitionEnd);
  });

  // SHOP CART
  document.addEventListener('DOMContentLoaded', function () {
    // Cart elements
    const cartBody = document.querySelector('.modal.fullscreen-modal tbody');
    const subtotalElement = document.querySelector('.modal-footer strong');
    const totalItemsElement = document.querySelector('.total-items');
    const orderNumberElement = document.querySelector('.order-number');
    const viewButtons = document.querySelectorAll('.quickview');
    const addToCartButton = document.querySelector('.add-to-cart-btn');
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));

    // Elements to Hide/Show
    const cartTable = document.querySelector('.modal.fullscreen-modal table');
    const cartFooter = document.querySelector('.modal-footer');
    const cartTitle = document.querySelector('.modal-header h2'); 
    const emptyCartMessage = document.querySelector('.empty-cart-message'); 
    const checkoutButton = document.querySelector('.btn-checkout'); 
    const subtotalText = document.querySelector('.modal-footer strong');

    let currentProduct = null;

    // Handle "View" button click to open the modal and populate product details
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            currentProduct = {
                id: this.closest('.product').dataset.id, 
                title: this.dataset.title,
                price: parseFloat(this.dataset.price.replace('₱', '')),
                image: this.dataset.img
            };

            // Populate modal with product details
            document.getElementById('modalProductImage').src = currentProduct.image;
            document.getElementById('modalProductTitle').textContent = currentProduct.title;
            document.getElementById('modalProductPrice').textContent = `₱${currentProduct.price.toFixed(2)}`;

            // Reset modal inputs
            document.getElementById('size').value = 'M';
            document.getElementById('quantity').value = '1';
        });
    });

    // Handle "Add to Cart" from the modal
    addToCartButton.addEventListener('click', function () {
        if (!currentProduct) return;

        const selectedSize = document.getElementById('size').value;
        const selectedQuantity = parseInt(document.getElementById('quantity').value);

        addToCart({
            ...currentProduct,
            size: selectedSize,
            quantity: selectedQuantity
        });

        productModal.hide();
    });

    // Function to add product to the cart
    function addToCart(product) {
        const existingItem = Array.from(cartBody.querySelectorAll('tr')).find(row => {
            return row.dataset.productId === product.id &&
                row.querySelector('.size').textContent === `Size: ${product.size}`;
        });

        if (existingItem) {
            const quantityEl = existingItem.querySelector('.num');
            const newQuantity = parseInt(quantityEl.textContent) + product.quantity;
            const price = parseFloat(existingItem.querySelector('td:nth-child(4)').dataset.basePrice);
            
            quantityEl.textContent = newQuantity;
            existingItem.querySelector('td:nth-child(4)').textContent = `₱${(price * newQuantity).toFixed(2)}`;
        } else {
            // Create new cart item
            const newRow = document.createElement('tr');
            newRow.dataset.productId = product.id;
            newRow.innerHTML = `
                <td>
                    <img src="${product.image}" alt="${product.title}" style="width: 120px; height: auto;">
                </td>
                <td class="product-info text-start">
                    <div class="text-left">
                        <strong>${product.title}</strong><br>
                        <span class="product-detail size">Size: ${product.size}</span><br>
                        <span class="product-detail">Price: ₱${product.price.toFixed(2)}</span>
                    </div>
                </td>
                <td>
                    <div class="quantity-control">
                        <span class="minus">-</span>
                        <span class="num">${product.quantity}</span>
                        <span class="plus">+</span>
                    </div>
                </td>
                <td data-base-price="${product.price}">₱${(product.price * product.quantity).toFixed(2)}</td>
                <td>
                    <button class="btn-remove custom-btn-remove"></button>
                </td>
            `;

            cartBody.appendChild(newRow);
        }

        updateCartSummary();
    }

    // Update cart summary and visibility
// Update cart summary and visibility
function updateCartSummary() {
    let subtotal = 0;
    let totalItems = 0;

    const rows = document.querySelectorAll('.modal.fullscreen-modal tbody tr');

    if (totalItemsElement) {
        totalItemsElement.textContent = `Total Items (${rows.length})`;
    }

    rows.forEach(row => {
        const quantityElement = row.querySelector('.num');
        const priceCell = row.querySelector('td:nth-child(4)');

        if (quantityElement && priceCell) {
            const quantity = parseInt(quantityElement.textContent);
            const price = parseFloat(priceCell.textContent.replace('₱', '')) || 0;
            
            totalItems += quantity;
            subtotal += price;
        }
    });

    if (subtotalElement) {
        subtotalElement.textContent = `Subtotal: ₱${subtotal.toFixed(2)}`;
    }

    // Update order number display
    if (orderNumberElement) {
        orderNumberElement.textContent = totalItems;
    }

    // Hide modal elements if cart is empty - use more specific selectors
    if (rows.length === 0) {
        // Use more specific selectors that only target the cart modal elements
        const cartModal = document.getElementById('cartModal'); // Make sure your cart modal has this ID
        
        if (cartModal) {
            const cartTable = cartModal.querySelector('table');
            const cartFooter = cartModal.querySelector('.modal-footer');
            const cartTitle = cartModal.querySelector('.modal-header h2');
            
            if (cartTable) cartTable.style.display = 'none';
            if (cartFooter) cartFooter.style.display = 'none';
            if (cartTitle) cartTitle.style.display = 'none';
            if (totalItemsElement) totalItemsElement.style.display = 'none';
            if (checkoutButton) checkoutButton.style.display = 'none';
            if (subtotalText) subtotalText.style.display = 'none';
            if (emptyCartMessage) emptyCartMessage.classList.remove('d-none');
        }
    } else {
        const cartModal = document.getElementById('cartModal');
        
        if (cartModal) {
            const cartTable = cartModal.querySelector('table');
            const cartFooter = cartModal.querySelector('.modal-footer');
            const cartTitle = cartModal.querySelector('.modal-header h2');
            
            if (cartTable) cartTable.style.display = '';
            if (cartFooter) cartFooter.style.display = '';
            if (cartTitle) cartTitle.style.display = '';
            if (totalItemsElement) totalItemsElement.style.display = '';
            if (checkoutButton) checkoutButton.style.display = '';
            if (subtotalText) subtotalText.style.display = '';
            if (emptyCartMessage) emptyCartMessage.classList.add('d-none');
        }
    }
}

    // Handle minus, plus, and remove button clicks
    cartBody.addEventListener('click', function (e) {
        const target = e.target;
        const row = target.closest('tr');

        if (!row) return;

        if (target.classList.contains('minus') || target.classList.contains('plus')) {
            const quantityElement = row.querySelector('.num');
            let quantity = parseInt(quantityElement.textContent);
            const priceElement = row.querySelector('td:nth-child(4)');
            const basePrice = parseFloat(priceElement.dataset.basePrice);

            // Update quantity
            quantity = target.classList.contains('minus')
                ? Math.max(1, quantity - 1)
                : quantity + 1;

            // Update display
            quantityElement.textContent = quantity;
            priceElement.textContent = `₱${(basePrice * quantity).toFixed(2)}`;
        }

        if (target.classList.contains('btn-remove')) {
            row.remove();
        }

        updateCartSummary();
    });

    // Ensure cart updates on page load
    setTimeout(updateCartSummary, 500);
});
