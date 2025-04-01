<h2>Change Password</h2>
<p>For your account's security, do not share your password with anyone else</p>

<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">Current Password</p>
    <input type="text" class="form-control" id="Current-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('Current-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">New Password</p>
    <input type="text" class="form-control" id="New-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('New-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>

<div class="input-group mb-3 d-flex align-items-center">
    <p class="mb-0 mr-2" style="width: 150px;">Confirm Password</p>
    <input type="text" class="form-control" id="Confirm-P" data-current="" style="flex-grow: 1;" />
    <span class="input-group-text" style="background: none; border: none; cursor: pointer;" onclick="toggleVisibility('Confirm-P', this)">
        <i class="fas fa-eye"></i>
    </span>
</div>
<button id="saveProfile" class="btn btn-primary mt-2" style="display: none;">Confirm</button>

<!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS --> <!-- CSS -->


<!-- JS --> <!-- JS --> <!-- JS -->  <!-- JS --> <!-- JS --> <!-- JS --> <!-- JS --> <!-- JS --> <!-- JS --> <!-- JS --> <!-- JS --> <!-- JS -->
<script>
// Function to mask the input value completely
function maskValue(value) {
    return "*".repeat(value.length); // Mask all characters
}

// Toggle Visibility Without Resetting Input
function toggleVisibility(fieldId, iconElement) {
    let input = document.getElementById(fieldId);
    let icon = iconElement.querySelector("i");

    if (input.value.includes("*")) {
        input.value = input.dataset.current; // Show full value
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.dataset.current = input.value; // Store the latest user input
        input.value = maskValue(input.dataset.current); // Mask the entire input
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

</script>
