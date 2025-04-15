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


