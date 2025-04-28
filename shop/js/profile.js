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
        // Reset form state and validation classes
        document.getElementById('update-profile-form').classList.remove('was-validated');
        const inputs = document.getElementById('update-profile-form').querySelectorAll('input');
        inputs.forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
            input.setCustomValidity('');
        });
    });

    // Edit Address Toggle
    document.getElementById('edit-address-btn').addEventListener('click', function() {
        document.getElementById('address-view').style.display = 'none';
        document.getElementById('address-edit').style.display = 'block';
        
        
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
        // Reset form state and validation classes
        document.getElementById('update-address-form').classList.remove('was-validated');
        const inputs = document.getElementById('update-address-form').querySelectorAll('input');
        inputs.forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
            input.setCustomValidity('');
        });
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

    // ENHANCED PASSWORD VALIDATION
    const currentPassword = document.getElementById('current-password');
    const newPassword = document.getElementById('new-password');
    const confirmPassword = document.getElementById('confirm-new-password');
    const passwordForm = document.getElementById('change-password-form');

    // Add enhanced validation with Bootstrap classes
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    }

    // Add comprehensive input event listeners for real-time validation
    if (newPassword) {
        newPassword.addEventListener('input', function() {
            if (!this.value) {
                this.setCustomValidity("");
                this.classList.remove("is-invalid", "is-valid");
            }
            else if (this.value.length < 8) {
                this.setCustomValidity("Password must be at least 8 characters long");
                this.classList.add("is-invalid");
                this.classList.remove("is-valid");
            } else {
                this.setCustomValidity("");
                this.classList.remove("is-invalid");
                this.classList.add("is-valid");
            }

            // Validate confirm password if not empty
            if (confirmPassword && confirmPassword.value !== "") {
                validateConfirmPassword();
            }
        });
    }

    if (confirmPassword) {
        confirmPassword.addEventListener('input', validateConfirmPassword);
    }

    function validateConfirmPassword() {
        if (!confirmPassword.value) {
            confirmPassword.setCustomValidity("");
            confirmPassword.classList.remove("is-invalid", "is-valid");
        }
        else if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords do not match");
            confirmPassword.classList.add('is-invalid');
            confirmPassword.classList.remove('is-valid');
        } else {
            confirmPassword.setCustomValidity("");
            confirmPassword.classList.remove('is-invalid');
            confirmPassword.classList.add('is-valid');
        }
    }

    // Password toggle visibility - Enhanced with better accessibility
    const passwordToggles = document.querySelectorAll('.password-toggle-btn');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            // Toggle password visibility
            if (passwordInput.type === 'password') {
                // Show password
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
                this.setAttribute('aria-label', 'Hide password');
            } else {
                // Hide password
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                this.setAttribute('aria-label', 'Show password');
            }
        });
    });

    // ENHANCED PROFILE FORM VALIDATION
    const profileForm = document.getElementById('update-profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    }

    // ENHANCED ADDRESS FORM VALIDATION
    const addressForm = document.getElementById('update-address-form');
    if (addressForm) {
        addressForm.addEventListener('submit', function(event) {
            // Check if coordinates are missing
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const addressInput = document.getElementById('edit-full-address');
            
            if (!latInput.value || !lngInput.value || !addressInput.value.trim()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Show validation error
                addressInput.classList.add('is-invalid');
                
                // Scroll to map and highlight it
                document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
                document.getElementById('map').classList.add('map-invalid');
                setTimeout(() => {
                    document.getElementById('map').classList.remove('map-invalid');
                }, 2000);
                
                // Show alert message
                const alertMessage = document.createElement('div');
                alertMessage.className = 'alert alert-danger mt-2';
                alertMessage.innerText = 'Please select your address location on the map';
                addressInput.parentNode.after(alertMessage);
                setTimeout(() => alertMessage.remove(), 3000);
            }
            
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    }

    // Enhanced Map initialization for the address section
    const addressInput = document.getElementById('edit-full-address');
    const mapDiv = document.getElementById('map');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const zipcodeInput = document.getElementById('edit-zipcode');

    // Initialize map with better options
    const map = L.map('map', {
        scrollWheelZoom: true,
        zoomControl: true
    }).setView([14.5995, 120.9842], 13);
    
    // Primary tile layer with fallback options
    const mainLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
        crossOrigin: true
    }).addTo(map);
    
    // Fallback tile layer if primary fails
    const fallbackLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors, © CARTO',
        maxZoom: 19,
        crossOrigin: true
    });
    
    // Handle tile error
    mainLayer.on('tileerror', function(error) {
        console.log("Tile error detected, switching to fallback");
        map.removeLayer(mainLayer);
        fallbackLayer.addTo(map);
    });

    // Force map to recalculate container size
    setTimeout(() => {
        map.invalidateSize(true);
    }, 300);

    // Add a draggable marker
    const marker = L.marker([14.5995, 120.9842], { draggable: true }).addTo(map);

    // Enhanced geocoder control
    const geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        geocoder: L.Control.Geocoder.nominatim(),
        placeholder: 'Search address...',
        errorMessage: 'Address not found, please try another search or click on the map'
    }).on('markgeocode', function(e) {
        marker.setLatLng(e.geocode.center);
        map.setView(e.geocode.center, 16);
        updateCoordinates(e.geocode.center.lat, e.geocode.center.lng);
        fetchAddressAndZipcode(e.geocode.center.lat, e.geocode.center.lng);
        
        // Add visual feedback
        addressInput.classList.add('is-valid');
        addressInput.classList.remove('is-invalid');
    }).addTo(map);

    function updateCoordinates(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
    }

    function fetchAddressAndZipcode(lat, lng) {
        // Show loading indicator
        addressInput.placeholder = 'Fetching address details...';
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)
            .then(r => r.json())
            .then(data => {
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                    addressInput.setAttribute('placeholder', 'Address selected from map');
                    addressInput.classList.add('is-valid');
                    addressInput.classList.remove('is-invalid');
                }
                if (data && data.address && data.address.postcode) {
                    zipcodeInput.value = data.address.postcode;
                } else {
                    console.log('No zipcode found for this location');
                }
            })
            .catch(error => {
                console.error('Error fetching address:', error);
                addressInput.placeholder = 'Error fetching address. Please try again.';
            });
    }

    // Map click => move marker + get address/zipcode with enhanced feedback
    map.on('click', e => {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
        fetchAddressAndZipcode(e.latlng.lat, e.latlng.lng);
        
        // Update placeholder and visual feedback
        addressInput.setAttribute('placeholder', 'Getting address from map...');
        addressInput.classList.add('bg-light');
    });

    // Marker drag end => same as click with enhanced feedback
    marker.on('dragend', () => {
        const pos = marker.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
        fetchAddressAndZipcode(pos.lat, pos.lng);
        
        // Update placeholder and visual feedback
        addressInput.setAttribute('placeholder', 'Getting address from map...');
    });

    // Update address field appearance
    if (addressInput) {
        addressInput.style.backgroundColor = "#f8f9fa";
        
        // Remove input event listener since it's read-only now
        // Instead, focus should show instructions
        addressInput.addEventListener('focus', function() {
            // Show focus hint
            addressInput.setAttribute('placeholder', 'Click on the map to select your address');
        });
        
        addressInput.addEventListener('blur', function() {
            // Reset placeholder
            addressInput.setAttribute('placeholder', 'Click on map to select your address');
        });
    }

    // Set initial map location if coordinates exist with better handling
    if (latInput && lngInput) {
        if (latInput.value && lngInput.value) {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                
                // Pre-validate the address field since we have coordinates
                if (addressInput.value.trim()) {
                    addressInput.classList.add('is-valid');
                }
            } else {
                console.log('Invalid coordinates, using default view');
            }
        }
    }

    // Update map on tab/section changes
    document.getElementById('nav-address').addEventListener('click', function() {
        setTimeout(() => {
            if (map) {
                map.invalidateSize(true);
                
                // Center on existing coordinates or default
                const lat = parseFloat(latInput.value) || 14.5995;
                const lng = parseFloat(lngInput.value) || 120.9842;
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
            }
        }, 300);
    });

    // ENHANCED REAL-TIME FIELD VALIDATION
    const usernameInput = document.getElementById('edit-username');
    const phoneInput = document.getElementById('edit-phone');
    const emailInput = document.getElementById('edit-email');

    // Store original values when page loads
    const originalUsername = usernameInput ? usernameInput.defaultValue : '';
    const originalPhone = phoneInput ? phoneInput.defaultValue : '';
    const originalEmail = emailInput ? emailInput.defaultValue : '';

    // Username validation with comprehensive feedback
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            if (!this.value.trim()) {
                this.setCustomValidity('Username is required');
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                document.getElementById('username-feedback').textContent = 'Username is required';
            } else if (this.value.trim().length < 3) {
                this.setCustomValidity('Username must be at least 3 characters');
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                document.getElementById('username-feedback').textContent = 'Username must be at least 3 characters';
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                if (this.value !== originalUsername) {
                    // Check username availability with delay
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        checkUsername(this.value.trim());
                    }, 500);
                } else {
                    this.classList.add('is-valid');
                }
            }
        });

        usernameInput.addEventListener('blur', function() {
            if (this.value.trim() && this.value !== originalUsername && !this.classList.contains('is-invalid')) {
                checkUsername(this.value.trim());
            }
        });
    }

    // Phone validation with comprehensive feedback
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            if (!this.value.trim()) {
                this.setCustomValidity('Phone number is required');
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                document.getElementById('phone-feedback').textContent = 'Phone number is required';
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                if (this.value !== originalPhone) {
                    // Check phone availability with delay
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        checkPhone(this.value.trim());
                    }, 500);
                } else {
                    this.classList.add('is-valid');
                }
            }
        });

        phoneInput.addEventListener('blur', function() {
            if (this.value.trim() && this.value !== originalPhone && !this.classList.contains('is-invalid')) {
                checkPhone(this.value.trim());
            }
        });
    }

    // Email validation with comprehensive feedback
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!this.value.trim()) {
                this.setCustomValidity('Email is required');
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                document.getElementById('email-feedback').textContent = 'Email is required';
            } else if (!emailRegex.test(this.value.trim())) {
                this.setCustomValidity('Please enter a valid email address');
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                document.getElementById('email-feedback').textContent = 'Please enter a valid email address';
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                if (this.value !== originalEmail) {
                    // Check email availability with delay
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        checkEmail(this.value.trim());
                    }, 500);
                } else {
                    this.classList.add('is-valid');
                }
            }
        });

        emailInput.addEventListener('blur', function() {
            if (this.value.trim() && this.value !== originalEmail && !this.classList.contains('is-invalid')) {
                checkEmail(this.value.trim());
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
                    usernameInput.classList.remove('is-valid');
                } else {
                    usernameInput.setCustomValidity("");
                    usernameInput.classList.remove('is-invalid');
                    usernameInput.classList.add('is-valid');
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
                    phoneInput.classList.remove('is-valid');
                } else {
                    phoneInput.setCustomValidity("");
                    phoneInput.classList.remove('is-invalid');
                    phoneInput.classList.add('is-valid');
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
                    emailInput.classList.remove('is-valid');
                } else {
                    emailInput.setCustomValidity("");
                    emailInput.classList.remove('is-invalid');
                    emailInput.classList.add('is-valid');
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