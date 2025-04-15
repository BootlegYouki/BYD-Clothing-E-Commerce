<link rel="stylesheet" href="css/profile_address.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />


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










<script src="js/profile_address.js"></script>

<!-- Google Maps API (Enable Places Library) -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>







