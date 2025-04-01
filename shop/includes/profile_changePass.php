<h2>Change Password</h2>
<p>For your account's security, do not share your password with anyone else</p>

<!-- Current Password -->
<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">Current Password</p>
    <input type="text" class="form-control" id="Current-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('Current-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<!-- New Password -->
<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">New Password</p>
    <input type="text" class="form-control" id="New-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('New-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<!-- Confirm Password -->
<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">Confirm Password</p>
    <input type="text" class="form-control" id="Confirm-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('Confirm-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<button id="saveProfile" class="btn-con btn btn-primary mt-2">Confirm</button>

<!-- CSS --> 
<style>
.btn-con {
    background-color: #000;
    color: white;
    border-color: #000;
}

.btn-con:hover{
    background-color: coral;
  
}

</style>

<!-- JS -->
<script>
// Function to mask the input value completely
function maskValue(value) {
    return "*".repeat(value.length); // Mask all characters
}

// Initialize the fields' data-current attribute with the value before showing or hiding
function initializeDataCurrent(inputId) {
    let input = document.getElementById(inputId);
    if (!input.dataset.current && input.value) {
        input.dataset.current = input.value; // Initialize if it's empty
    }
}

// Toggle Visibility Without Resetting Input
function toggleVisibility(fieldId, iconElement) {
    let input = document.getElementById(fieldId);
    let icon = iconElement.querySelector("i");

    // Initialize data-current if it's not already set
    initializeDataCurrent(fieldId);

    // If data-current is empty or not set yet, return
    if (!input.dataset.current) {
        return;
    }

    // Toggle the visibility
    if (input.value.includes("*")) {
        input.value = input.dataset.current; // Show full value
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.dataset.current = input.value; // Store the latest user input
        input.value = maskValue(input.dataset.current); // Mask the entire input
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

// Call the initialize function on page load to set data-current for all fields
document.addEventListener("DOMContentLoaded", function () {
    initializeDataCurrent('Current-P');
    initializeDataCurrent('New-P');
    initializeDataCurrent('Confirm-P');
});
</script>


