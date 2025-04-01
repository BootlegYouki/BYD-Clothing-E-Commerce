<!-- TOPSIDE-->
<div class="topside">
    <h1>My Addresses</h1>
    <button class="btn-body" data-bs-toggle="modal" data-bs-target="#addressModal">
        <i class="fas fa-plus"></i> Add New Address
    </button>
</div>
    <hr>

<!-- BOTTOM-->
<div class="bottom-side">
<div class="main-info d-flex justify-content-between align-items-center w-100">
        <!-- LEFT SIDE: Name & Phone Number -->
        <div class="d-flex align-items-center">
            <p class="mb-0 "><strong>BYD_Tester</strong></p>
            <div class="vertical-separator mx-2"></div>
            <p class="mb-0">09122342432</p>
        </div>

        <!-- RIGHT SIDE: Edit, Delete & Button -->
        <div class="right-side d-flex align-items-center">
            <a href="#" class=" text-decoration-underline mt-2" data-bs-toggle="modal" data-bs-target="#editAddressModal">Edit</a>
            <div class="vertical-separator mx-2 "></div>
            <a href="#" class="text-decoration-underline mt-2">Delete</a>
        </div>
    </div>

</div>

 <!-- ADDRESS DETAILS -->
<div class="contain">
    
    
        <!-- LEFT DETAILS -->
        <div class="left-side">
        <div class="Street d-flex justify-content-between align-items-center">
           <p id="strt" class="ad_deets mb-0">Blk 27 Lot 12 Pechayan Kanan Namasape HOA</p>
            <button id="st_def" class="ms-auto">Set As Default</button>
        </div>
            <p id="Brgy" class ="ad_deets mb-0">Commonwealth Ave. North Fairview QC</p>
            <p id="City" class="ad_deets mb-0">Quezon City</p>
            <p id="Region"  class="ad_deets mb-0">Metro Manila</p>
            <p id="Postal Code mb-0" class="ad_deets">1121</p>
            <button class="mb-0" disabled>Default</button>
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










<script src="js/url-cleaner.js"></script>
<!-- Google Maps API (Enable Places Library) -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>




<!-- CSS --> <!-- CSS -->  <!-- CSS -->  <!-- CSS -->  <!-- CSS -->  <!-- CSS -->  <!-- CSS -->


<style>

.topside{
    display: flex;
    justify-content: space-between;
}

.address-deets {
    display: flex;
    justify-content: space-between;
    align-items: start;
}

.right-side {
    text-align: right; /* Aligns text and buttons to the right */
}

.right-side a {
    text-decoration: none;
    color: blue; /* Adjust as needed */
    margin-bottom: 5px;
}

.right-side button {
    background-color: coral;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

.right-side button:hover {
    background-color: rgb(255, 127, 80, 0.8);
}


.btn-body {
    font-size: 0.8rem;
    font-weight: 700;
    outline: none;
    border: none;
    background-color: coral;
    color: white;
    padding: 10px 20px;
    cursor: pointer;
    text-transform: uppercase;
    transition: background-color 0.3s ease-in-out; 
}

.btn-body:hover {
    background-color: rgb(255, 150, 115); 
}


    .container{ /*whole ass container */
         border-radius: 20px;
    }
    .address-deets p {
    margin-bottom: 2px; 
    font-size: 15px;
    line-height: 1.2; 

}

.vertical-separator {
    width: 1px;
    height: 15px;
    background-color: black; /* Adjust color if needed */
}

.main-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}


.right-side {
    display: flex;
    align-items: center;
}

.right-side a {
    color: blue;
    font-size: 14px;
    text-decoration: underline;
}
.same-row{
    display: flex;
}
.modal-md, .modal-dialog {
    max-width: 600px; 
    max-height: 90vh;
    overflow-y: auto; 
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); 
}

#btn_submit, #btn-submit1{
    background-color: coral;
    border: none;


}


</style>


<!-- JS -->  <!-- JS -->  <!-- JS -->  <!-- JS -->  <!-- JS -->  <!-- JS -->  <!-- JS -->  <!-- JS -->
<script>
// Initialize Google Map for Address Modal
let map, marker;

function initMap(id) {
    map = new google.maps.Map(document.getElementById(id), {
        center: { lat: 14.5995, lng: 120.9842 }, // Default: Manila
        zoom: 12,
    });

    marker = new google.maps.Marker({
        position: { lat: 14.5995, lng: 120.9842 },
        map: map,
        draggable: true
    });

    google.maps.event.addListener(marker, 'dragend', function () {
        let position = marker.getPosition();
        console.log("Pinned Location:", position.lat(), position.lng());
    });
}

// Function to fetch Regions
async function fetchRegions(modalType) {
    let response = await fetch("https://psgc.gitlab.io/api/regions/");
    let data = await response.json();
    let regionSelect = modalType === 'edit' ? document.getElementById("editRegion") : document.getElementById("region");

    regionSelect.innerHTML = "<option value=''>Select Region</option>";
    data.forEach(region => {
        let option = document.createElement("option");
        option.value = region.code;
        option.textContent = region.name;
        regionSelect.appendChild(option);
    });
}

// Function to fetch Provinces based on selected Region
async function fetchProvinces(regionCode, modalType) {
    let provinceSelect = modalType === 'edit' ? document.getElementById("editProvince") : document.getElementById("province");
    let citySelect = modalType === 'edit' ? document.getElementById("editCity") : document.getElementById("city");
    let barangaySelect = modalType === 'edit' ? document.getElementById("editBarangay") : document.getElementById("barangay");

    // Clear dependent dropdowns
    citySelect.innerHTML = "<option value=''>Select City</option>";
    barangaySelect.innerHTML = "<option value=''>Select Barangay</option>";

    if (regionCode === "130000000") { // If NCR is selected
        provinceSelect.innerHTML = '<option value="NCR">NCR</option>';
        provinceSelect.value = "NCR";
        fetchCities(regionCode, modalType);
    } else {
        let response = await fetch(`https://psgc.gitlab.io/api/regions/${regionCode}/provinces/`);
        let data = await response.json();
        provinceSelect.innerHTML = "<option value=''>Select Province</option>";

        data.forEach(province => {
            let option = document.createElement("option");
            option.value = province.code;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
        });
    }
}

// Function to fetch Cities based on selected Province
async function fetchCities(code, modalType) {
    let citySelect = modalType === 'edit' ? document.getElementById("editCity") : document.getElementById("city");
    citySelect.innerHTML = "<option value=''>Select City</option>";

    let url;
    if (code === "130000000") { // If NCR, fetch cities directly under NCR
        url = `https://psgc.gitlab.io/api/regions/${code}/cities-municipalities/`;
    } else {
        url = `https://psgc.gitlab.io/api/provinces/${code}/cities-municipalities/`;
    }

    let response = await fetch(url);
    let data = await response.json();

    data.forEach(city => {
        let option = document.createElement("option");
        option.value = city.code;
        option.textContent = city.name;
        citySelect.appendChild(option);
    });
}

// Function to fetch Barangays based on selected City
async function fetchBarangays(cityCode, modalType) {
    let barangaySelect = modalType === 'edit' ? document.getElementById("editBarangay") : document.getElementById("barangay");
    barangaySelect.innerHTML = "<option value=''>Select Barangay</option>";

    let response = await fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`);
    let data = await response.json();

    data.forEach(barangay => {
        let option = document.createElement("option");
        option.value = barangay.name;
        option.textContent = barangay.name;
        barangaySelect.appendChild(option);
    });
}

// When Add Address Modal is shown
document.getElementById('addressModal').addEventListener('shown.bs.modal', function () {
    fetchRegions('add'); // Load regions for Add Address
    initMap('map'); // Initialize Google Map for Add Address
});

// When Edit Address Modal is shown
document.getElementById('editAddressModal').addEventListener('shown.bs.modal', function () {
    fetchRegions('edit'); // Load regions for Edit Address
    initMap('editMap'); // Initialize Google Map for Edit Address
});

// Event listener for Region dropdown in Add/Edit Address modals
document.getElementById("region").addEventListener("change", function () {
    fetchProvinces(this.value, 'add');
});
document.getElementById("editRegion").addEventListener("change", function () {
    fetchProvinces(this.value, 'edit');
});

// Event listener for Province dropdown in Add/Edit Address modals
document.getElementById("province").addEventListener("change", function () {
    fetchCities(this.value, 'add');
});
document.getElementById("editProvince").addEventListener("change", function () {
    fetchCities(this.value, 'edit');
});

// Event listener for City dropdown in Add/Edit Address modals
document.getElementById("city").addEventListener("change", function () {
    fetchBarangays(this.value, 'add');
});
document.getElementById("editCity").addEventListener("change", function () {
    fetchBarangays(this.value, 'edit');
});

</script>