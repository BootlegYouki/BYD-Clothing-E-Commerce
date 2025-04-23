document.addEventListener('DOMContentLoaded', function() {
    // Navigation between sections
    const navItems = document.querySelectorAll('.list-group-item');
    const sections = {
        'nav-profile': document.getElementById('profile-section'),
        'nav-orders': document.getElementById('orders-section'),
        'nav-address': document.getElementById('address-section'),
        'nav-password': document.getElementById('password-section')
    };

    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Reset active state
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            
            // Hide all sections
            Object.values(sections).forEach(section => {
                if (section) section.style.display = 'none';
            });
            
            // Show selected section
            const sectionToShow = sections[this.id];
            if (sectionToShow) sectionToShow.style.display = 'block';
            
            // If showing address section with map, we need to invalidate size for proper rendering
            if (this.id === 'nav-address' && map) {
                setTimeout(() => {
                    map.invalidateSize();
                    
                    // Center the map on user's coordinates or default if not set
                    const lat = parseFloat(document.getElementById('latitude').value) || 14.5995;
                    const lng = parseFloat(document.getElementById('longitude').value) || 120.9842;
                    map.setView([lat, lng], 15);
                    marker.setLatLng([lat, lng]);
                }, 100);
            }
        });
    });

    // Edit Profile Toggle
    document.getElementById('edit-profile-btn').addEventListener('click', function() {
        document.getElementById('profile-view').style.display = 'none';
        document.getElementById('profile-edit').style.display = 'block';
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
        document.getElementById('profile-edit').style.display = 'none';
        document.getElementById('profile-view').style.display = 'block';
    });

    // Edit Address Toggle
    document.getElementById('edit-address-btn').addEventListener('click', function() {
        document.getElementById('address-view').style.display = 'none';
        document.getElementById('address-edit').style.display = 'block';
        
        // Make zipcode field readonly
        document.getElementById('edit-zipcode').readOnly = true;
        
        setTimeout(() => {
            map.invalidateSize();
            
            // Center the map on user's coordinates or default if not set
            const lat = parseFloat(document.getElementById('latitude').value) || 14.5995;
            const lng = parseFloat(document.getElementById('longitude').value) || 120.9842;
            map.setView([lat, lng], 15);
            marker.setLatLng([lat, lng]);
        }, 100);
    });

    document.getElementById('cancel-address-edit').addEventListener('click', function() {
        document.getElementById('address-edit').style.display = 'none';
        document.getElementById('address-view').style.display = 'block';
    });

    // View Order Details
    const orderButtons = document.querySelectorAll('.view-order');
    const orderModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    
    orderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderContentDiv = document.getElementById('order-details-content');
            
            // Show loading spinner
            orderContentDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Open modal
            orderModal.show();
            
            // Fetch order details
            fetch(`functions/get_order_details.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        let html = `
                            <div class="order-info mb-4">
                                <h6>Order #${data.order.order_id}</h6>
                                <p><strong>Date:</strong> ${new Date(data.order.created_at).toLocaleDateString()}</p>
                                <p><strong>Status:</strong> <span class="badge ${data.order.status === 'pending' ? 'bg-warning' : data.order.status === 'completed' ? 'bg-success' : 'bg-danger'}">${data.order.status.charAt(0).toUpperCase() + data.order.status.slice(1)}</span></p>
                                <p><strong>Payment Method:</strong> ${data.order.payment_method}</p>
                            </div>
                            <div class="shipping-info mb-4">
                                <h6>Shipping Information</h6>
                                <p><strong>Name:</strong> ${data.order.firstname} ${data.order.lastname}</p>
                                <p><strong>Address:</strong> ${data.order.address}</p>
                                <p><strong>City:</strong> ${data.order.city}</p>
                                <p><strong>Zipcode:</strong> ${data.order.zipcode}</p>
                                <p><strong>Phone:</strong> ${data.order.phone}</p>
                                <p><strong>Email:</strong> ${data.order.email}</p>
                            </div>
                            <div class="order-items mb-4">
                                <h6>Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Size</th>
                                                <th>Qty</th>
                                                <th>Price</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                        
                        data.items.forEach(item => {
                            html += `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.size}</td>
                                    <td>${item.quantity}</td>
                                    <td>₱${parseFloat(item.price).toFixed(2)}</td>
                                    <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                                </tr>`;
                        });
                        
                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="order-summary">
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Subtotal:</td>
                                                <td class="text-end">₱${parseFloat(data.order.subtotal).toFixed(2)}</td>
                                            </tr>
                                            <tr>
                                                <td>Shipping:</td>
                                                <td class="text-end">₱${parseFloat(data.order.shipping_cost).toFixed(2)}</td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>Total:</td>
                                                <td class="text-end">₱${parseFloat(data.order.total_amount).toFixed(2)}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>`;
                        
                        orderContentDiv.innerHTML = html;
                    } else {
                        orderContentDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    orderContentDiv.innerHTML = '<div class="alert alert-danger">There was an error loading order details. Please try again later.</div>';
                });
        });
    });

    // Password validation
    const newPassword = document.getElementById('new-password');
    const confirmPassword = document.getElementById('confirm-new-password');

    newPassword.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    function validatePasswords() {
        if (newPassword.value.length < 8) {
            newPassword.setCustomValidity("Password must be at least 8 characters long");
        } else {
            newPassword.setCustomValidity("");
        }

        if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords do not match");
        } else {
            confirmPassword.setCustomValidity("");
        }
    }

    // Map initialization for the address section
    const addressInput = document.getElementById('edit-full-address');
    const mapDiv = document.getElementById('map');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const zipcodeInput = document.getElementById('edit-zipcode');

    // Initialize map 
    const map = L.map('map').setView([14.5995, 120.9842], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add a draggable marker
    const marker = L.marker([14.5995, 120.9842], { draggable: true }).addTo(map);

    // Geocoder control
    const geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        geocoder: L.Control.Geocoder.nominatim(),
        placeholder: 'Search address...'
    }).on('markgeocode', function(e) {
        marker.setLatLng(e.geocode.center);
        map.setView(e.geocode.center, 16);
        updateCoordinates(e.geocode.center.lat, e.geocode.center.lng);
        fetchAddressAndZipcode(e.geocode.center.lat, e.geocode.center.lng);
    }).addTo(map);

    function updateCoordinates(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
    }

    function fetchAddressAndZipcode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
            .then(r => r.json())
            .then(data => {
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                }
                if (data && data.address && data.address.postcode) {
                    zipcodeInput.value = data.address.postcode;
                }
            })
            .catch(console.error);
    }

    // Map click => move marker + get address/zipcode
    map.on('click', e => {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
        fetchAddressAndZipcode(e.latlng.lat, e.latlng.lng);
    });

    // Marker drag end => same as click
    marker.on('dragend', () => {
        const pos = marker.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
        fetchAddressAndZipcode(pos.lat, pos.lng);
    });

    // Address input change => search on map
    addressInput.addEventListener('input', function() {
        clearTimeout(this.timer);
        const val = this.value.trim();

        if (val.length > 2) {
            this.timer = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(val)}&limit=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const result = data[0];
                            const lat = parseFloat(result.lat);
                            const lng = parseFloat(result.lon);
                            const latlng = L.latLng(lat, lng);
                            
                            map.setView(latlng, 16);
                            marker.setLatLng(latlng);
                            updateCoordinates(lat, lng);
                            
                            // Don't update the address itself since user is typing it
                            if (data[0].address && data[0].address.postcode) {
                                zipcodeInput.value = data[0].address.postcode;
                            }
                        }
                    })
                    .catch(console.error);
            }, 500);
        }
    });

    // Set initial map location if coordinates exist
    if (latInput.value && lngInput.value) {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        map.setView([lat, lng], 15);
        marker.setLatLng([lat, lng]);
    }

    // Form validation for profile update - Add this from PHP to JS
    const usernameInput = document.getElementById('edit-username');
    const phoneInput = document.getElementById('edit-phone');
    const emailInput = document.getElementById('edit-email');

    // Store original values when page loads
    const originalUsername = usernameInput ? usernameInput.defaultValue : '';
    const originalPhone = phoneInput ? phoneInput.defaultValue : '';
    const originalEmail = emailInput ? emailInput.defaultValue : '';

    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            if (usernameInput.value !== originalUsername) {
                checkUsername(usernameInput.value);
            }
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            if (phoneInput.value !== originalPhone) {
                checkPhone(phoneInput.value);
            }
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (emailInput.value !== originalEmail) {
                checkEmail(emailInput.value);
            }
        });
    }

    function checkUsername(username) {
        fetch(`functions/check_username.php?username=${encodeURIComponent(username)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    usernameInput.setCustomValidity("Username already taken");
                    document.getElementById('username-feedback').textContent = "Username already taken";
                    usernameInput.classList.add('is-invalid');
                } else {
                    usernameInput.setCustomValidity("");
                    usernameInput.classList.remove('is-invalid');
                }
            })
            .catch(error => console.error("Error checking username:", error));
    }

    function checkPhone(phone) {
        fetch(`functions/check_phone.php?phone_number=${encodeURIComponent(phone)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    phoneInput.setCustomValidity("Phone number already registered");
                    document.getElementById('phone-feedback').textContent = "Phone number already registered";
                    phoneInput.classList.add('is-invalid');
                } else {
                    phoneInput.setCustomValidity("");
                    phoneInput.classList.remove('is-invalid');
                }
            })
            .catch(error => console.error("Error checking phone:", error));
    }

    function checkEmail(email) {
        fetch(`functions/check_email.php?email=${encodeURIComponent(email)}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    emailInput.setCustomValidity("Email already registered");
                    document.getElementById('email-feedback').textContent = "Email already registered";
                    emailInput.classList.add('is-invalid');
                } else {
                    emailInput.setCustomValidity("");
                    emailInput.classList.remove('is-invalid');
                }
            })
            .catch(error => console.error("Error checking email:", error));
    }

    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});