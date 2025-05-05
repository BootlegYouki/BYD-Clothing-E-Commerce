<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../admin/config/dbcon.php';

// Check if user is logged in
if (!isset($_SESSION['auth_user'])) {
    $_SESSION['checkout_redirect'] = true;
    header("Location: shop.php?checkout_login=1");
    exit;
}

// Get user details for pre-filling the form
$user_id = $_SESSION['auth_user']['user_id'];
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

// Fixed shipping fee
$shipping_fee = 50;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout - Beyond Doubt Clothing</title> 
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/logo/BYD-removebg-preview.ico" type="image/x-icon">
    <!-- LEAFLET CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <style>
        .notification-dropdown {
            transform: translateX(10%);
        }
        .map-container {
            position: relative;
        }
        #map-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            z-index: 999;
            cursor: not-allowed;
        }
        #edit-address-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
    </style>
    <!-- Add checkout-specific CSS -->
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/logout.php'; ?>
    
    <section id="checkout" class="mt-5 py-5">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2>Checkout</h2>
                    <hr class="body-hr mx-auto">
                </div>
            </div>
            
            <!-- Add checkout progress steps -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="checkout-steps">
                        <div class="checkout-step completed">
                            <div class="step-number">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="step-name">Cart</div>
                        </div>
                        <div class="checkout-step active">
                            <div class="step-number">2</div>
                            <div class="step-name">Checkout</div>
                        </div>
                        <div class="checkout-step">
                            <div class="step-number">3</div>
                            <div class="step-name">Payment</div>
                        </div>
                        <div class="checkout-step">
                            <div class="step-number">4</div>
                            <div class="step-name">Confirmation</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout form -->
            <form id="checkout-form" action="functions/process_payment.php" method="POST">
                <input type="hidden" name="shipping_fee" value="<?= $shipping_fee ?>">
                <div class="row g-4">
                    <!-- Left column - Customer info -->
                    <div class="col-md-7">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Shipping Address</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="map-container mb-3">
                                            <div id="map" style="height: 300px;" class="rounded"></div>
                                            <div id="map-overlay"></div>
                                            <button type="button" id="edit-address-btn" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit me-1"></i> Edit Address
                                            </button>
                                        </div>
                                        <div class="alert alert-info small py-2 mb-2" id="map-instructions">
                                            <i class="fa-solid fa-info-circle me-1"></i> Please click on the map or search to select your exact address location.
                                        </div>
                                        <input type="hidden" id="latitude" name="latitude" required value="<?= htmlspecialchars($user['latitude'] ?? '') ?>">
                                        <input type="hidden" id="longitude" name="longitude" required value="<?= htmlspecialchars($user['longitude'] ?? '') ?>">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="address" class="form-label mb-0">Complete Address</label>
                                            <div id="map-status" class="badge bg-secondary">Locked</div>
                                        </div>
                                        
                                        <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['full_address'] ?? '') ?>" readonly required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="zipcode" class="form-label">Postal/ZIP Code</label>
                                        <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?= htmlspecialchars($user['zipcode'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Shipping</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>Standard Shipping</strong>
                                                <p class="mb-0 text-muted small">Delivery within 5-7 business days</p>
                                            </div>
                                            <span class="ms-3">₱<?= number_format($shipping_fee, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Payment Method</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        
                                        <!-- PayMongo Online Payment -->
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                id="payment_ewallet" value="ewallet" required>
                                            <label class="form-check-label d-flex align-items-center" for="payment_ewallet">
                                                <div>
                                                    <strong>Pay Online</strong>
                                                    <p class="mb-0 text-muted small">Pay using your e-wallet account or card</p>
                                                </div>
                                                <span class="ms-auto"><i class="fas fa-credit-card text-info"></i></span>
                                            </label>
                                        </div>
                                        
                                        <!-- Payment information section -->
                                        <div id="ewallet-payment-info" class="d-none">
                                            <p class="small mb-0"><i class="fas fa-info-circle me-2"></i>You will be redirected to PayMongo payment gateway to complete your payment.</p>     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column - Order summary -->
                    <div class="col-md-5">
                        <div class="card position-sticky" style="top: 150px;">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div id="order-items">
                                    <!-- Order items will be dynamically added here -->
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="order-subtotal">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span id="order-shipping">₱<?= number_format($shipping_fee, 2) ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4 fw-bold">
                                    <span>Total:</span>
                                    <span id="order-total">₱0.00</span>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark py-3">Proceed to Payment</button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <a href="shop.php" class="text-decoration-none">
                                        <i class="fa fa-arrow-left me-2"></i>Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/shop.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
    // Store shipping fee as a global constant
    const SHIPPING_FEE = <?= $shipping_fee ?>;
    
    // Handle payment method selection and map functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle payment info sections based on selected payment method
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
        
        // Map functionality
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
        editAddressBtn.addEventListener('click', function() {
            toggleMapEditing(!isMapEditable);
        });
        
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
            
            // Set initial view based on user's saved location or default to Manila
            let initialLat = <?= !empty($user['latitude']) ? $user['latitude'] : 14.6760 ?>;
            let initialLng = <?= !empty($user['longitude']) ? $user['longitude'] : 121.0437 ?>;
            map.setView([initialLat, initialLng], 16);
            
            // Add tile layer
            L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
            maxZoom: 20,
            subdomains:['mt0','mt1','mt2','mt3'],
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
            
            // Note: We don't add the geocoder to the map yet - it will be added when editing mode is enabled
            
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
            if (latInput.value && lngInput.value) {
                updateCoordinates(parseFloat(latInput.value), parseFloat(lngInput.value));
                reverseGeocode(parseFloat(latInput.value), parseFloat(lngInput.value));
            }
            
            // Set map to locked state by default
            toggleMapEditing(false);
            
            // Ensure map instructions are initially hidden (as a fallback)
            mapInstructions.style.display = 'none';
        }
        
        // Helper functions
        function updateCoordinates(lat, lng) {
            latInput.value = lat;
            lngInput.value = lng;
        }
        
        function reverseGeocode(lat, lng) {
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
    </script>
    <script src="js/checkout.js"></script>
    <script src="js/url-cleaner.js"></script>
</body>
</html>