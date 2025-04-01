
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

