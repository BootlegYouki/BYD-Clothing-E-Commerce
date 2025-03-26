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
  console.log("DOM Loaded - Script Running"); // Debugging check

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

   // Function to animate item flying to cart
   function animateToCart(imageElement) {
    const cartIcon = document.querySelector('.bx-shopping-bag'); // Correct cart icon target

    if (!cartIcon || !imageElement) {
        console.error("Animation Error: Missing imageElement or cartIcon.");
        return;
    }

    console.log("Animating to cart...");

    // Clone the image and append to the body
    const flyingImage = imageElement.cloneNode(true);
    document.body.appendChild(flyingImage);

    // Get positions relative to the viewport
    const imageRect = imageElement.getBoundingClientRect();
    const cartRect = cartIcon.getBoundingClientRect();

    // Set initial styles for cloned image
    flyingImage.style.position = "fixed";
    flyingImage.style.top = `${imageRect.top}px`; // Start from item's position
    flyingImage.style.left = `${imageRect.left}px`;
    flyingImage.style.width = `${imageRect.width}px`; // Maintain original size
    flyingImage.style.height = `${imageRect.height}px`;
    flyingImage.style.opacity = "1";
    flyingImage.style.transition = "transform 0.8s ease-in-out, opacity 0.8s ease-in-out";
    flyingImage.style.zIndex = "1000";
    flyingImage.style.borderRadius = "8px"; // Optional styling

    // Calculate translation values
    const translateX = cartRect.left - imageRect.left + (cartRect.width / 2 - imageRect.width / 2);
    const translateY = cartRect.top - imageRect.top + (cartRect.height / 2 - imageRect.height / 2);

    // Move the image to the cart position
    setTimeout(() => {
        flyingImage.style.transform = `translate(${translateX}px, ${translateY}px) scale(0.1)`;
        flyingImage.style.opacity = "0";
    }, 50);

    // Remove image after animation
    setTimeout(() => {
        flyingImage.remove();
    }, 800);
}

  // Handle "View" button click to open the modal and populate product details
  viewButtons.forEach(button => {
      button.addEventListener('click', function () {
          currentProduct = {
              id: this.closest('.product').dataset.productId,
              title: this.dataset.title,
              price: parseFloat(this.dataset.price.replace('₱', '')),
              image: this.dataset.img
          };

          console.log("Viewing Product:", currentProduct); // Debugging

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
  if (addToCartButton) {
      addToCartButton.addEventListener('click', function () {
          console.log("Add to Cart Button Clicked"); // Debugging

          if (!currentProduct) {
              console.error("Error: No product selected.");
              return;
          }

          const selectedSize = document.getElementById('size').value;
          let selectedQuantity = parseInt(document.getElementById('quantity').value);

          addToCart({
              ...currentProduct,
              size: selectedSize,
              quantity: selectedQuantity
          });

          // Trigger animation
          const productImage = document.getElementById('modalProductImage');
          animateToCart(productImage);

          productModal.hide();
      });
  } else {
      console.error("Add to Cart button not found in DOM.");
  }

  // Function to add product to the cart
  function addToCart(product) {
      console.log("Adding to cart:", product); // Debugging

      // Check for existing item by ID and size
      const existingItem = Array.from(cartBody.querySelectorAll('tr')).find(row => {
          return row.dataset.productId === product.id && row.querySelector('.size').dataset.size === product.size;
      });

      if (existingItem) {
          // Update quantity and total price
          const quantityEl = existingItem.querySelector('.num');
          let newQuantity = parseInt(quantityEl.textContent) + product.quantity;

          // Enforce max limit of 9
          if (newQuantity > 9) newQuantity = 9;

          quantityEl.textContent = newQuantity;

          const priceCell = existingItem.querySelector('td:nth-child(4)');
          const basePrice = parseFloat(priceCell.dataset.basePrice);
          priceCell.textContent = `₱${(basePrice * newQuantity).toFixed(2)}`;
      } else {
          // Create new row
          const newRow = document.createElement('tr');
          newRow.dataset.productId = product.id;
          newRow.innerHTML = `
              <td>
                  <img src="${product.image}" alt="${product.title}" style="width: 120px; height: auto;">
              </td>
              <td class="product-info text-start">
                  <div class="text-left">
                      <strong>${product.title}</strong><br>
                      <span class="size" data-size="${product.size}">Size: ${product.size}</span><br>
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

  // Ensure cart updates on page load
  setTimeout(updateCartSummary, 500);

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

      if (orderNumberElement) {
          orderNumberElement.textContent = totalItems;
      }

      if (rows.length === 0) {
          cartTable.style.display = 'none';
          cartFooter.style.display = 'none';
          cartTitle.style.display = 'none';
          totalItemsElement.style.display = 'none';
          checkoutButton.style.display = 'none';
          subtotalText.style.display = 'none';
          emptyCartMessage.classList.remove('d-none');
      } else {
          cartTable.style.display = '';
          cartFooter.style.display = '';
          cartTitle.style.display = '';
          totalItemsElement.style.display = '';
          checkoutButton.style.display = '';
          subtotalText.style.display = '';
          emptyCartMessage.classList.add('d-none');
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

      if (target.classList.contains('minus')) {
          quantity -= 1;
          if (quantity <= 0) {
              removeItemWithAnimation(row);
              return;
          }
      } else if (target.classList.contains('plus')) {
          if (quantity < 9) {
              quantity += 1;
          }
      }

      if (quantity > 0) {
          quantityElement.textContent = quantity;
          priceElement.textContent = `₱${(basePrice * quantity).toFixed(2)}`;
      }
  }

  if (target.classList.contains('btn-remove')) {
      removeItemWithAnimation(row);
  }
});

// Function to remove item with slide animation
function removeItemWithAnimation(row) {
  row.style.transition = "transform 0.4s ease-out, opacity 0.4s ease-out";
  row.style.transform = "translateX(100%)";
  row.style.opacity = "0";

  setTimeout(() => {
      row.remove();
      updateCartSummary();
  }, 300); // Wait for animation to complete before removing the row
}

  // Ensure cart updates on page load
  setTimeout(updateCartSummary, 500);
});
