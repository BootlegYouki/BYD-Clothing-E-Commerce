
let mapInstances = {}; // Store multiple map instances

function initMap(id, modalType) {
    // Default coordinates (Manila)
    let defaultLat = 14.5995;
    let defaultLng = 120.9842;

    // Initialize the map
    let map = L.map(id).setView([defaultLat, defaultLng], 12);
    mapInstances[id] = map; // Store the map instance

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Function to update coordinates in the form
    function updateCoordinates(lat, lng) {
        console.log("Pinned Location:", lat, lng);
        reverseGeocode(lat, lng, modalType);
    }

    // Function to place marker
    function placeMarker(lat, lng) {
        if (mapInstances[id].marker) {
            mapInstances[id].marker.setLatLng([lat, lng]);
        } else {
            let marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                let position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });
            mapInstances[id].marker = marker;
        }
        map.setView([lat, lng], 15); // Zoom to user's location
        updateCoordinates(lat, lng);
    }

    // Get user current location
    function setUserLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    let userLat = position.coords.latitude;
                    let userLng = position.coords.longitude;
                    placeMarker(userLat, userLng);
                },
                () => {
                    console.warn("Geolocation permission denied. Using default location.");
                    placeMarker(defaultLat, defaultLng);
                }
            );
        } else {
            console.warn("Geolocation not supported. Using default location.");
            placeMarker(defaultLat, defaultLng);
        }
    }

    setUserLocation(); // Call function to set default pin location

    // Click event to place marker dynamically
    map.on('click', function (event) {
        let lat = event.latlng.lat;
        let lng = event.latlng.lng;
        placeMarker(lat, lng);
    });
}

// Function to convert lat/lng to street address
async function reverseGeocode(lat, lng, modalType) {
    try {
        let response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
        let data = await response.json();

        let address = data.display_name || "Unknown Location";
        console.log("Address:", address);

        const streetField = modalType === 'edit' ? document.getElementById("editStreet") : document.getElementById("streetAddress");
        streetField.value = address;
    } catch (error) {
        console.error("Reverse Geocoding Error:", error);
    }
}

// Initialize Maps for Add and Edit Address
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("editAddressModal").addEventListener("shown.bs.modal", function () {
        initMap("editMap", "edit");
    });
});

// Function to convert lat/lng to street address
async function reverseGeocode(lat, lng, modalType) {
    try {
        let response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
        let data = await response.json();

        let address = data.display_name || "Unknown Location";
        console.log("Address:", address);

        const streetField = modalType === 'edit' ? document.getElementById("editStreet") : document.getElementById("streetAddress");
        streetField.value = address;
    } catch (error) {
        console.error("Reverse Geocoding Error:", error);
    }
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
    fetchRegions('add'); 
    initMap('map'); 
});

// When Edit Address Modal is shown
document.getElementById('editAddressModal').addEventListener('shown.bs.modal', function () {
    fetchRegions('edit'); 
    initMap('editMap'); 
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

// DATABASE MANIPULATION DITO NA LAGAY//


