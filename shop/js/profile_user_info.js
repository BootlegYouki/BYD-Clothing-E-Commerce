// Store the latest values instead of resetting to defaults
document.addEventListener("DOMContentLoaded", function () {
    let emailInput = document.getElementById("email");
    let phoneInput = document.getElementById("phone");

    emailInput.dataset.original = "test123@gmail.com";
    phoneInput.dataset.original = "09123456789";

    emailInput.dataset.current = emailInput.dataset.original;
    phoneInput.dataset.current = phoneInput.dataset.original;

    emailInput.value = maskValue(emailInput.dataset.current, "email");
    phoneInput.value = maskValue(phoneInput.dataset.current, "phone");
});

// Function to mask email & phone number
function maskValue(value, fieldId) {
    if (fieldId === "email") {
        let parts = value.split("@");
        return parts[0].slice(0, 3) + "*****@" + parts[1]; // Show first 3 letters, mask rest before "@"
    } else if (fieldId === "phone") {
        return "*******" + value.slice(-2); // Mask everything except last 2 digits
    }
}

// Toggle Visibility Without Resetting Input
function toggleVisibility(fieldId, iconElement) {
    let input = document.getElementById(fieldId);
    let icon = iconElement.querySelector("i");

    if (input.value.includes("*")) {
        input.value = input.dataset.current; // Show full value
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.dataset.current = input.value; // Store latest user input
        input.value = maskValue(input.dataset.current, fieldId);
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

// Enable Editing Mode
document.getElementById("editProfile").addEventListener("click", function () {
    document.getElementById("fullname").removeAttribute("disabled");
    document.getElementById("email").removeAttribute("disabled");
    document.getElementById("phone").removeAttribute("disabled");
    
    document.getElementById("saveProfile").style.display = "block"; // Show Save button
    this.style.display = "none"; // Hide Edit button
});

// Real-Time Validation
document.getElementById("email").addEventListener("input", function () {
    validateInput(this, document.querySelector(".errorEmail"), /^[a-zA-Z0-9._%+-]+@gmail\.com$/, "Enter a valid Gmail address.");
});

document.getElementById("phone").addEventListener("input", function () {
    validateInput(this, document.querySelector(".errorPhone"), /^\d{11}$/, "Phone number must be exactly 11 digits.");
});

// Generic Validation Function
function validateInput(input, errorElement, regex, errorMessage) {
    if (!input.value.trim()) {
        showError(errorElement, "This field is required!");
    } else if (!regex.test(input.value.trim())) {
        showError(errorElement, errorMessage);
    } else {
        hideError(errorElement);
    }
}

// Function to Show Error Message
function showError(element, message) {
    element.innerText = message;
    element.style.display = "block";
    element.style.color = "red";
    element.classList.add("error-message"); 
}

// Function to Hide Error Message
function hideError(element) {
    element.style.display = "none";
}

// Validate and Save Profile
document.getElementById("saveProfile").addEventListener("click", function () {
    let fullnameInput = document.getElementById("fullname");
    let emailInput = document.getElementById("email");
    let phoneInput = document.getElementById("phone");
    let emailError = document.querySelector(".errorEmail");
    let phoneError = document.querySelector(".errorPhone");

    let gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
    let phoneRegex = /^\d{11}$/;

    let isValid = true;

    if (!emailInput.value.trim() || !gmailRegex.test(emailInput.value.trim())) {
        showError(emailError, "Enter a valid Gmail address.");
        isValid = false;
    } else {
        hideError(emailError);
    }

    if (!phoneInput.value.trim() || !phoneRegex.test(phoneInput.value.trim())) {
        showError(phoneError, "Phone number must be 11 digits.");
        isValid = false;
    } else {
        hideError(phoneError);
    }

    if (!isValid) return;

    // Disable inputs after saving
    fullnameInput.setAttribute("disabled", "true"); // Make Fullname non-editable again
    emailInput.setAttribute("disabled", "true");
    phoneInput.setAttribute("disabled", "true");

    emailInput.dataset.current = emailInput.value;
    phoneInput.dataset.current = phoneInput.value;

    emailInput.value = maskValue(emailInput.value, "email");
    phoneInput.value = maskValue(phoneInput.value, "phone");

    // Show success modal
    let successModal = new bootstrap.Modal(document.getElementById("successModal"));
    successModal.show();

    // Hide Save button and show Edit button again
    this.style.display = "none";
    document.getElementById("editProfile").style.display = "inline-block";
});
