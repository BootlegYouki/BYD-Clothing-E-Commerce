document.addEventListener('DOMContentLoaded', function() {
    console.log("Password reset script loaded");
    
    // Handle password reset form submission
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log("Form submitted");
            
            // Get form data
            const formData = new FormData(resetPasswordForm);
            const submitBtn = resetPasswordForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerHTML;
            
            // Disable button and show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            // Clear previous error messages
            const errorMessage = document.getElementById('resetPasswordErrorMessage');
            if (errorMessage) {
                errorMessage.classList.add('d-none');
            }
            
            // Convert FormData to URL-encoded string
            const urlEncodedData = new URLSearchParams(formData).toString();
            
            // Send AJAX request
            fetch('functions/reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: urlEncodedData
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response received:", data);
                
                if (data.status === "success") {
                    if (data.redirect) {
                        // Redirect to the specified URL
                        window.location.href = data.redirect;
                    } else {
                        // Hide any existing modals
                        const existingModals = document.querySelectorAll('.modal');
                        existingModals.forEach(modal => {
                            const bsModal = bootstrap.Modal.getInstance(modal);
                            if (bsModal) {
                                bsModal.hide();
                            }
                        });
                        
                        // Show success message
                        const successModal = new bootstrap.Modal(document.getElementById('passwordResetSuccessModal'));
                        successModal.show();
                        
                        // Redirect to login page when modal is closed
                        document.getElementById('passwordResetSuccessModal').addEventListener('hidden.bs.modal', function() {
                            window.location.href = 'index.php';
                        });
                    }
                } else {
                    // Show error message
                    if (errorMessage) {
                        errorMessage.textContent = data.message;
                        errorMessage.classList.remove('d-none');
                    }
                    
                    // Reset button state
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
            console.error("AJAX error:", error);
            
            // Show generic error message
            if (errorMessage) {
                errorMessage.textContent = 'An error occurred. Please try again later.';
                errorMessage.classList.remove('d-none');
            }
            
            // Reset button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });
}
    
    // Password visibility toggle
    const toggleResetPassword = document.querySelector('.toggle-reset-password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    
    if (toggleResetPassword && newPasswordInput && confirmPasswordInput) {
        toggleResetPassword.addEventListener('change', function() {
            const type = this.checked ? 'text' : 'password';
            newPasswordInput.type = type;
            confirmPasswordInput.type = type;
        });
    }
    
    // Password confirmation validation
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = newPasswordInput.value;
            if (this.value !== password) {
                this.setCustomValidity("Passwords don't match");
            } else {
                this.setCustomValidity("");
            }
        });
    }
});