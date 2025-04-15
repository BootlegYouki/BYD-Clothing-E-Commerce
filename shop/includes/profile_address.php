<link rel="stylesheet" href="css/profile.css">
<link rel="stylesheet" href="css/profile_address.css">

<!-- TOPSIDE-->
<div class="topside d-flex justify-content-between align-items-center mb-4">
    <h1>My Addresses</h1>
    <button class="btn-body" data-bs-toggle="modal" data-bs-target="#addressModal">
        <i class="fas fa-plus"></i> Add New Address
    </button>
</div>
<hr>

<!-- BOTTOM-->
<div class="address-card p-3 mb-4 border rounded shadow-sm">
    <div class="main-info d-flex justify-content-between align-items-center w-100">
        <!-- LEFT SIDE: Name & Phone Number -->
        <div class="d-flex align-items-center">
            <p class="mb-0"><strong>BYD_Tester</strong></p>
            <div class="vertical-separator mx-2"></div>
            <p class="mb-0">09122342432</p>
        </div>

        <!-- RIGHT SIDE: Edit, Delete & Button -->
        <div class="right-side d-flex align-items-center">
            <a href="#" class="text-decoration-underline mt-2" data-bs-toggle="modal" data-bs-target="#editAddressModal">Edit</a>
            <div class="vertical-separator mx-2"></div>
            <a href="#" class="text-decoration-underline mt-2">Delete</a>
        </div>
    </div>

    <!-- ADDRESS DETAILS -->
    <div class="contain mt-3">
        <!-- LEFT DETAILS -->
        <div class="left-side">
            <div class="Street d-flex justify-content-between align-items-center">
                <p id="strt" class="ad_deets mb-0">Blk 27 Lot 12 Pechayan Kanan Namasape HOA</p>
                <button id="st_def" class="btn-con ms-auto">Set As Default</button>
            </div>
            <p id="Brgy" class="ad_deets mb-0">Commonwealth Ave. North Fairview QC</p>
            <p id="City" class="ad_deets mb-0">Quezon City</p>
            <p id="Region" class="ad_deets mb-0">Metro Manila</p>
            <p id="Postal Code" class="ad_deets mb-0">1121</p>
            <span class="badge bg-success rounded-pill">Default</span>
        </div>
    </div>
</div>

<!-- MODAL FOR ADD ADDRESS-->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalLabel">New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addressForm">
                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" required>
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" maxlength="11" required>
                    </div>

                    <!-- Address Fields -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="region" class="form-label">Region</label>
                            <select id="region" class="form-select" required></select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="province" class="form-label">Province</label>
                            <select id="province" class="form-select" required></select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <select id="city" class="form-select" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="barangay" class="form-label">Barangay</label>
                            <select id="barangay" class="form-select" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="postalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="postalCode" required>
                        </div>
                    </div>

                    <!-- Detailed Address -->
                    <div class="mb-3">
                        <label for="streetAddress" class="form-label">Street Name, Building, House No.</label>
                        <input type="text" class="form-control" id="streetAddress" required>
                    </div>

                    <!-- Google Map -->
                    <div class="mb-3">
                        <label for="map">Add Location</label>
                        <div id="map" style="height: 250px;"></div>
                    </div>

                    <!-- Address Label -->
                    <div class="mb-3">
                        <label class="form-label">Label As:</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="addressLabel" id="home" value="Home" checked>
                                <label class="form-check-label" for="home">Home</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="addressLabel" id="work" value="Work">
                                <label class="form-check-label" for="work">Work</label>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btn_submit" class="btn btn-primary color">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FOR EDIT ADDRESS -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editAddressForm">
          <!-- Full Name -->
          <div class="mb-3">
            <label for="editFullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="editFullName" value="Justine Ritaga">
          </div>

          <!-- Phone Number -->
          <div class="mb-3">
            <label for="editPhoneNumber" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="editPhoneNumber" value="(+63) 921 370 9185">
          </div>

          <!-- Google Map (User can Pin Location) -->
          <div class="mb-3">
            <label class="form-label">Add Location</label>
            <div id="editMap" style="height: 300px; width: 100%;"></div>
          </div>

          <!-- Region, Province, City, Barangay Dropdowns (Using PSGC API) -->
          <div class="mb-3">
            <label for="editRegion" class="form-label">Region</label>
            <select class="form-select" id="editRegion"></select>
          </div>

          <div class="mb-3">
            <label for="editProvince" class="form-label">Province</label>
            <select class="form-select" id="editProvince"></select>
          </div>

          <div class="mb-3">
            <label for="editCity" class="form-label">City</label>
            <select class="form-select" id="editCity"></select>
          </div>

          <div class="mb-3">
            <label for="editBarangay" class="form-label">Barangay</label>
            <select class="form-select" id="editBarangay"></select>
          </div>

          <!-- Postal Code -->
          <div class="mb-3">
            <label for="editPostalCode" class="form-label">Postal Code</label>
            <input type="text" class="form-control" id="editPostalCode" value="1127">
          </div>

          <!-- Street Name -->
          <div class="mb-3">
            <label for="editStreet" class="form-label">Street Name, Building, House No.</label>
            <input type="text" class="form-control" id="editStreet" value="17 Faustino Street, Holy Spirit">
          </div>

         <!-- Address Label -->
         <div class="mb-3">
                        <label class="form-label">Label As:</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="addressLabel" id="home" value="Home" checked>
                                <label class="form-check-label" for="home">Home</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="addressLabel" id="work" value="Work">
                                <label class="form-check-label" for="work">Work</label>
                            </div>
                        </div>
                    </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" id="btn-submit1" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>










<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4 position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body d-flex flex-column align-items-center">
                <i class="fas fa-check-circle text-success" style="font-size: 50px;"></i>
                <p class="mt-3 fw-bold">Address saved successfully!</p>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Delete -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this address?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize maps when the modals are shown
let addMap, editMap;
let addMarker, editMarker;

// Philippine coordinates (center of Philippines)
const phCenter = { lat: 12.8797, lng: 121.7740 };

// Initialize the maps when the respective modals are shown
document.addEventListener('DOMContentLoaded', function() {
    // Initialize address dropdowns
    initializeAddressDropdowns();
    
    // Initialize maps when modals are shown
    $('#addressModal').on('shown.bs.modal', function() {
        if (!addMap) {
            initializeAddMap();
        }
    });
    
    $('#editAddressModal').on('shown.bs.modal', function() {
        if (!editMap) {
            initializeEditMap();
        }
    });
    
    // Handle form submissions
    document.getElementById('addressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAddress();
    });
    
    document.getElementById('btn-submit1').addEventListener('click', function() {
        updateAddress();
    });
    
    // Handle delete confirmation
    document.querySelectorAll('.delete-address').forEach(btn => {
        btn.addEventListener('click', function() {
            const addressId = this.dataset.id;
            document.getElementById('confirmDeleteBtn').dataset.id = addressId;
            $('#deleteConfirmModal').modal('show');
        });
    });
    
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const addressId = this.dataset.id;
        deleteAddress(addressId);
        $('#deleteConfirmModal').modal('hide');
    });
    
    // Handle "Set as Default" buttons
    document.querySelectorAll('#st_def').forEach(btn => {
        btn.addEventListener('click', function() {
            const addressId = this.dataset.id;
            setDefaultAddress(addressId);
        });
    });
});

// Initialize Google Map for Add Address
function initializeAddMap() {
    addMap = new google.maps.Map(document.getElementById('map'), {
        center: phCenter,
        zoom: 6,
        mapTypeControl: false
    });
    
    // Add a marker that can be dragged
    addMarker = new google.maps.Marker({
        position: phCenter,
        map: addMap,
        draggable: true,
        title: 'Drag to set your location'
    });
    
    // Add search box
    const input = document.getElementById('streetAddress');
    const searchBox = new google.maps.places.SearchBox(input);
    
    // Bias the SearchBox results towards current map's viewport
    addMap.addListener('bounds_changed', function() {
        searchBox.setBounds(addMap.getBounds());
    });
    
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place
    searchBox.addListener('places_changed', function() {
        const places = searchBox.getPlaces();
        
        if (places.length === 0) {
            return;
        }
        
        // For each place, get the location
        const bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
            if (!place.geometry) {
                console.log("Returned place contains no geometry");
                return;
            }
            
            // Update marker position
            addMarker.setPosition(place.geometry.location);
            
            if (place.geometry.viewport) {
                // Only geocodes have viewport
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        addMap.fitBounds(bounds);
    });
}

// Initialize Google Map for Edit Address
function initializeEditMap() {
    editMap = new google.maps.Map(document.getElementById('editMap'), {
        center: phCenter,
        zoom: 6,
        mapTypeControl: false
    });
    
    // Add a marker that can be dragged
    editMarker = new google.maps.Marker({
        position: phCenter,
        map: editMap,
        draggable: true,
        title: 'Drag to set your location'
    });
    
    // Add search box
    const input = document.getElementById('editStreet');
    const searchBox = new google.maps.places.SearchBox(input);
    
    // Bias the SearchBox results towards current map's viewport
    editMap.addListener('bounds_changed', function() {
        searchBox.setBounds(editMap.getBounds());
    });
    
    // Listen for the event fired when the user selects a prediction
    searchBox.addListener('places_changed', function() {
        const places = searchBox.getPlaces();
        
        if (places.length === 0) {
            return;
        }
        
        // For each place, get the location
        const bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
            if (!place.geometry) {
                console.log("Returned place contains no geometry");
                return;
            }
            
            // Update marker position
            editMarker.setPosition(place.geometry.location);
            
            if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        editMap.fitBounds(bounds);
    });
}

// Initialize Philippine address dropdowns (Region, Province, City, Barangay)
function initializeAddressDropdowns() {
    // Fetch regions
    fetch('https://psgc.gitlab.io/api/regions/')
        .then(response => response.json())
        .then(data => {
            const regionSelect = document.getElementById('region');
            const editRegionSelect = document.getElementById('editRegion');
            
            // Sort regions by name
            data.sort((a, b) => a.name.localeCompare(b.name));
            
            // Add regions to dropdown
            data.forEach(region => {
                const option = document.createElement('option');
                option.value = region.code;
                option.textContent = region.name;
                regionSelect.appendChild(option);
                
                const editOption = document.createElement('option');
                editOption.value = region.code;
                editOption.textContent = region.name;
                editRegionSelect.appendChild(editOption);
            });
            
            // Set default selection for NCR
            const ncrOption = Array.from(regionSelect.options).find(option => option.textContent.includes('NCR'));
            if (ncrOption) {
                ncrOption.selected = true;
                // Trigger change event to load provinces
                regionSelect.dispatchEvent(new Event('change'));
            }
            
            // Set default selection for edit form
            const editNcrOption = Array.from(editRegionSelect.options).find(option => option.textContent.includes('NCR'));
            if (editNcrOption) {
                editNcrOption.selected = true;
                // Trigger change event to load provinces
                editRegionSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => console.error('Error loading regions:', error));
    
    // Event listeners for cascading dropdowns
    document.getElementById('region').addEventListener('change', function() {
        loadProvinces(this.value, 'province');
    });
    
    document.getElementById('province').addEventListener('change', function() {
        loadCities(this.value, 'city');
    });
    
    document.getElementById('city').addEventListener('change', function() {
        loadBarangays(this.value, 'barangay');
    });
    
    document.getElementById('editRegion').addEventListener('change', function() {
        loadProvinces(this.value, 'editProvince');
    });
    
    document.getElementById('editProvince').addEventListener('change', function() {
        loadCities(this.value, 'editCity');
    });
    
    document.getElementById('editCity').addEventListener('change', function() {
        loadBarangays(this.value, 'editBarangay');
    });
}

// Load provinces based on region
function loadProvinces(regionCode, targetId) {
    fetch(`https://psgc.gitlab.io/api/regions/${regionCode}/provinces/`)
        .then(response => response.json())
        .then(data => {
            const provinceSelect = document.getElementById(targetId);
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            
            // Sort provinces by name
            data.sort((a, b) => a.name.localeCompare(b.name));
            
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });
            
            // If NCR, load cities directly since NCR doesn't have provinces
            if (data.length === 0 && regionCode === '130000000') {
                loadCitiesForNCR(targetId === 'editProvince' ? 'editCity' : 'city');
            }
        })
        .catch(error => console.error('Error loading provinces:', error));
}

// Load cities for NCR directly
function loadCitiesForNCR(targetId) {
    fetch('https://psgc.gitlab.io/api/regions/130000000/cities/')
        .then(response => response.json())
        .then(data => {
            const citySelect = document.getElementById(targetId);
            citySelect.innerHTML = '<option value="">Select City</option>';
            
            // Sort cities by name
            data.sort((a, b) => a.name.localeCompare(b.name));
            
            data.forEach(city => {
                const option = document.createElement('option');
                option.value = city.code;
                option.textContent = city.name;
                citySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading NCR cities:', error));
}

// Load cities based on province
function loadCities(provinceCode, targetId) {
    fetch(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities/`)
        .then(response => response.json())
        .then(data => {
            const citySelect = document.getElementById(targetId);
            citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
            
            // Sort cities by name
            data.sort((a, b) => a.name.localeCompare(b.name));
            
            data.forEach(city => {
                const option = document.createElement('option');
                option.value = city.code;
                option.textContent = city.name;
                citySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading cities:', error));
}

// Load barangays based on city
function loadBarangays(cityCode, targetId) {
    fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`)
        .then(response => response.json())
        .then(data => {
            const barangaySelect = document.getElementById(targetId);
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            
            // Sort barangays by name
            data.sort((a, b) => a.name.localeCompare(b.name));
            
            data.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.code;
                option.textContent = barangay.name;
                barangaySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading barangays:', error));
}

// Save new address
function saveAddress() {
    // Get form values
    const fullName = document.getElementById('fullName').value;
    const phoneNumber = document.getElementById('phoneNumber').value;
    const region = document.getElementById('region').options[document.getElementById('region').selectedIndex].text;
    const province = document.getElementById('province').options[document.getElementById('province').selectedIndex].text;
    const city = document.getElementById('city').options[document.getElementById('city').selectedIndex].text;
    const barangay = document.getElementById('barangay').options[document.getElementById('barangay').selectedIndex].text;
    const postalCode = document.getElementById('postalCode').value;
    const streetAddress = document.getElementById('streetAddress').value;
    const addressLabel = document.querySelector('input[name="addressLabel"]:checked').value;
    
    // Get coordinates from marker
    const lat = addMarker ? addMarker.getPosition().lat() : null;
    const lng = addMarker ? addMarker.getPosition().lng() : null;
    
    // Create address data object
    const addressData = {
        fullName,
        phoneNumber,
        region,
        province,
        city,
        barangay,
        postalCode,
        streetAddress,
        addressLabel,
        lat,
        lng
    };
    
    // Here you would normally send this data to your server
    console.log('Saving address:', addressData);
    
    // For demo purposes, we'll just show a success message
    $('#addressModal').modal('hide');
    $('#successModal').modal('show');
    
    // Reset form
    document.getElementById('addressForm').reset();
    
    // Reload page after a delay to show the new address
    setTimeout(() => {
        location.reload();
    }, 2000);
}

// Update existing address
function updateAddress() {
    // Get form values
    const fullName = document.getElementById('editFullName').value;
    const phoneNumber = document.getElementById('editPhoneNumber').value;
    const region = document.getElementById('editRegion').options[document.getElementById('editRegion').selectedIndex].text;
    const province = document.getElementById('editProvince').options[document.getElementById('editProvince').selectedIndex].text;
    const city = document.getElementById('editCity').options[document.getElementById('editCity').selectedIndex].text;
    const barangay = document.getElementById('editBarangay').options[document.getElementById('editBarangay').selectedIndex].text;
    const postalCode = document.getElementById('editPostalCode').value;
    const streetAddress = document.getElementById('editStreet').value;
    const addressLabel = document.querySelector('input[name="addressLabel"]:checked').value;
    
    // Get coordinates from marker
    const lat = editMarker ? editMarker.getPosition().lat() : null;
    const lng = editMarker ? editMarker.getPosition().lng() : null;
    
    // Create address data object
    const addressData = {
        fullName,
        phoneNumber,
        region,
        province,
        city,
        barangay,
        postalCode,
        streetAddress,
        addressLabel,
        lat,
        lng
    };
    
    // Here you would normally send this data to your server
    console.log('Updating address:', addressData);
    
    // For demo purposes, we'll just show a success message
    $('#editAddressModal').modal('hide');
    $('#successModal').modal('show');
    
    // Reload page after a delay to show the updated address
    setTimeout(() => {
        location.reload();
    }, 2000);
}

// Delete address
function deleteAddress(addressId) {
    // Here you would normally send a delete request to your server
    console.log('Deleting address with ID:', addressId);
    
    // For demo purposes, we'll just show a success message
    $('#successModal .modal-body p').text('Address deleted successfully!');
    $('#successModal').modal('show');
    
    // Reload page after a delay to show the address is gone
    setTimeout(() => {
        location.reload();
    }, 2000);
}

// Set address as default
function setDefaultAddress(addressId) {
    // Here you would normally send a request to your server to set this address as default
    console.log('Setting address with ID as default:', addressId);
    
    // For demo purposes, we'll just show a success message
    $('#successModal .modal-body p').text('Default address updated successfully!');
    $('#successModal').modal('show');
    
    // Reload page after a delay to show the updated default status
    setTimeout(() => {
        location.reload();
    }, 2000);
}
</script>

<!-- Replace with your actual Google Maps API key -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places&callback=initializeAddressDropdowns" async defer></script>