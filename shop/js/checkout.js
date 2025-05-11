/**
 * Checkout Page JavaScript
 * 
 * This script handles the checkout process:
 * 1. Loads cart data from localStorage
 * 2. Displays order summary
 * 3. Calculates totals
 * 4. Handles form submission and payment processing
 * 5. Handles payment method selection
 * 6. Manages map functionality for address selection
 */
document.addEventListener('DOMContentLoaded', function() {
    // Load cart data from localStorage - check both possible keys
    const cart = JSON.parse(localStorage.getItem('shopping-cart') || localStorage.getItem('cart')) || [];
    
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
        <div class="d-flex justify-content-between align-items-center">
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
                const response = await fetch('functions/paymongo/process_payment.php', {
                    method: 'POST',
                    body: formData
                });
                
                // Parse the JSON response
                let data;
                try {
                    const responseText = await response.text();
                    console.log('Raw API response:', responseText);
                    
                    // Try to parse the JSON
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('JSON parsing error:', e);
                    throw new Error('Invalid response from server. Please try again later.');
                }
                
                if (data.success) {
                    console.log('Payment URL received:', data.payment_url);
                    
                    // CHANGED: Don't clear the cart yet - wait until payment is confirmed
                    // Store the reference in both localStorage and sessionStorage for better persistence
                    if (data.reference) {
                        sessionStorage.setItem('order_reference', data.reference);
                        localStorage.setItem('order_reference', data.reference);
                        localStorage.setItem('pending_payment', 'true');
                    }
                    
                    // Redirect directly to the payment URL in the same tab
                    window.location.href = data.payment_url;
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
    
    // ===== PAYMENT METHOD SELECTION =====
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentInfos = {
        'card': document.getElementById('card-payment-info'),
        'ewallet': document.getElementById('ewallet-payment-info'),
        'cod': document.getElementById('cod-payment-info')
    };
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all payment info sections
            Object.values(paymentInfos).forEach(info => {
                if (info) info.classList.add('d-none');
            });
            
            // Show selected payment info
            const selectedInfo = paymentInfos[this.value];
            if (selectedInfo) selectedInfo.classList.remove('d-none');
        });
    });
    
    // Show e-wallet payment info by default
    const defaultPaymentInfo = paymentInfos['ewallet'];
    if (defaultPaymentInfo) defaultPaymentInfo.classList.remove('d-none');
    
    // ===== MAP FUNCTIONALITY =====
    const addressInput = document.getElementById('address');
    const mapDiv = document.getElementById('map');
    const mapOverlay = document.getElementById('map-overlay');
    const mapStatus = document.getElementById('map-status');
    const editAddressBtn = document.getElementById('edit-address-btn');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const zipcodeInput = document.getElementById('zipcode');
    const mapInstructions = document.getElementById('map-instructions');
    
    // Map initialization variables
    let map = null;
    let marker = null;
    let geocoder = null;
    let isMapEditable = false;
    
    // Initialize the map
    initMap();
    
    // Edit address button click handler
    if (editAddressBtn) {
        editAddressBtn.addEventListener('click', function() {
            toggleMapEditing(!isMapEditable);
        });
    }
    
    // Function to toggle map editing state
    function toggleMapEditing(enable) {
        isMapEditable = enable;
        
        if (enable) {
            // Enable map interactions
            mapOverlay.style.display = 'none';
            if (map) {
                map.dragging.enable();
                map.touchZoom.enable();
                map.doubleClickZoom.enable();
                map.scrollWheelZoom.enable();
                map.boxZoom.enable();
                map.keyboard.enable();
            }
            
            if (marker) {
                marker.dragging.enable();
            }
            
            // Add geocoder to the map when in edit mode
            if (geocoder) {
                try {
                    geocoder.addTo(map);
                } catch(e) {
                    console.log("Geocoder already added");
                }
            }
            
            // Make address field editable (remove readonly)
            addressInput.readOnly = false;
            addressInput.style.cursor = 'text';
            addressInput.style.color = '#000';
            
            // Update UI elements
            mapStatus.textContent = 'Editable';
            mapStatus.classList.remove('bg-secondary');
            mapStatus.classList.add('bg-success');
            editAddressBtn.innerHTML = '<i class="fas fa-lock me-1"></i> Lock Address';
            editAddressBtn.classList.remove('btn-primary');
            editAddressBtn.classList.add('btn-warning');
            
            // Show map instructions
            mapInstructions.style.display = 'block';
        } else {
            // Disable map interactions
            mapOverlay.style.display = 'block';
            if (map) {
                map.dragging.disable();
                map.touchZoom.disable();
                map.doubleClickZoom.disable();
                map.scrollWheelZoom.disable();
                map.boxZoom.disable();
                map.keyboard.disable();
            }
            
            if (marker) {
                marker.dragging.disable();
            }
            
            // Remove geocoder from the map when locked
            if (geocoder) {
                try {
                    geocoder.remove();
                } catch(e) {
                    console.log("Geocoder already removed");
                }
            }
            
            // Make address field readonly again
            addressInput.readOnly = true;
            addressInput.style.cursor = 'default';
            addressInput.style.color = '#495057';
            
            // Update UI elements
            mapStatus.textContent = 'Locked';
            mapStatus.classList.remove('bg-success');
            mapStatus.classList.add('bg-secondary');
            editAddressBtn.innerHTML = '<i class="fas fa-edit me-1"></i> Edit Address';
            editAddressBtn.classList.remove('btn-warning');
            editAddressBtn.classList.add('btn-primary');
            
            // Hide map instructions
            mapInstructions.style.display = 'none';
        }
    }
    
    // Function to initialize the map
    function initMap() {
        if (!mapDiv) return; // Exit if map element doesn't exist
        
        // If map already exists, destroy it first to avoid duplicates
        if (map) {
            map.remove();
            map = null;
        }
        
        // Create the map
        map = L.map('map', {
            scrollWheelZoom: false,  // Disabled by default
            dragging: false,         // Disabled by default
            touchZoom: false,        // Disabled by default
            doubleClickZoom: false,  // Disabled by default
            boxZoom: false,          // Disabled by default
            keyboard: false,         // Disabled by default
            zoomControl: true,
            attributionControl: true
        });
        
        // Get initial coordinates
        let initialLat = latInput && latInput.value ? parseFloat(latInput.value) : 14.6760; // Default: Manila
        let initialLng = lngInput && lngInput.value ? parseFloat(lngInput.value) : 121.0437;
        
        map.setView([initialLat, initialLng], 16);
        
        // Add tile layer
        L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3']
        }).addTo(map);
        
        // Add marker (not draggable by default)
        marker = L.marker([initialLat, initialLng], { draggable: false }).addTo(map);
        
        // Create geocoder control (but don't add it yet)
        geocoder = L.Control.geocoder({
            position: 'bottomright',
            defaultMarkGeocode: false,
            geocoder: L.Control.Geocoder.nominatim({
                timeout: 5000,
                serviceUrl: 'https://nominatim.openstreetmap.org/'
            }),
            placeholder: 'Search address...',
            errorMessage: 'Unable to find that address.'
        }).on('markgeocode', function(e) {
            if (isMapEditable) {
                marker.setLatLng(e.geocode.center);
                map.setView(e.geocode.center, 16);
                updateCoordinates(e.geocode.center.lat, e.geocode.center.lng);
                reverseGeocode(e.geocode.center.lat, e.geocode.center.lng);
                fetchZipcode(e.geocode.center.lat, e.geocode.center.lng);
            }
        });
        
        // Map click event - only works when map is editable
        map.on('click', function(e) {
            if (isMapEditable) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
                fetchZipcode(e.latlng.lat, e.latlng.lng);
            }
        });
        
        // Marker drag end event - only triggered when map is editable
        marker.on('dragend', function() {
            if (isMapEditable) {
                const pos = marker.getLatLng();
                updateCoordinates(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
                fetchZipcode(pos.lat, pos.lng);
            }
        });
        
        // Force map to recalculate its size
        setTimeout(function() {
            map.invalidateSize(true);
        }, 300);
        
        // If we have coordinates, use them
        if (latInput && lngInput && latInput.value && lngInput.value) {
            updateCoordinates(parseFloat(latInput.value), parseFloat(lngInput.value));
            reverseGeocode(parseFloat(latInput.value), parseFloat(lngInput.value));
        }
        
        // Set map to locked state by default
        toggleMapEditing(false);
        
        // Ensure map instructions are initially hidden (as a fallback)
        if (mapInstructions) mapInstructions.style.display = 'none';
    }
    
    // Helper functions for map
    function updateCoordinates(lat, lng) {
        if (latInput && lngInput) {
            latInput.value = lat;
            lngInput.value = lng;
        }
    }
    
    function reverseGeocode(lat, lng) {
        if (!addressInput) return;
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=en`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                }
            })
            .catch(error => {
                console.error('Error with reverse geocoding:', error);
                addressInput.value = `Location at ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            });
    }
    
    function fetchZipcode(lat, lng) {
        if (!zipcodeInput) return;
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=en`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data && data.address && data.address.postcode) {
                    zipcodeInput.value = data.address.postcode;
                }
            })
            .catch(error => {
                console.error('Error fetching zipcode:', error);
            });
    }
});
