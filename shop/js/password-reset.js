$(document).ready(function() {
    console.log("Password reset script loaded");
    
    // Handle password reset form submission
    $("#resetPasswordForm, form[action*='reset_password']").on("submit", function(e) {
        e.preventDefault();
        console.log("Form submitted");
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find("button[type='submit']");
        var originalBtnText = submitBtn.html();
        
        // Disable button and show loading state
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop("disabled", true);
        
        // Clear previous error messages
        $(".alert").remove();
        
        // Add form data to console for debugging
        console.log("Form data:", formData);
        
        $.ajax({
            type: "POST",
            url: "functions/reset_password.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                console.log("Response received:", response);
                
                if (response.status === "success") {
                    // Create a custom styled modal instead of using the browser alert
                    var modalHTML = `
                    <div class="modal fade" id="passwordResetSuccessModal" tabindex="-1" aria-labelledby="passwordResetSuccessModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #FF7F50; color: white;">
                                    <h5 class="modal-title" id="passwordResetSuccessModalLabel">Password Reset Successful</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <div class="mb-4">
                                        <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                                    </div>
                                    <h4>Your password has been reset successfully!</h4>
                                    <p class="text-muted">You can now login with your new password.</p>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <a href="login.php" class="btn btn-lg px-5" style="background-color: #FF7F50; color: white;">Proceed to Login</a>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    
                    // Remove any existing modal
                    $("#passwordResetSuccessModal").remove();
                    
                    // Append modal to body
                    $("body").append(modalHTML);
                    
                    try {
                        // Initialize and show the modal
                        var successModal = new bootstrap.Modal(document.getElementById('passwordResetSuccessModal'));
                        successModal.show();
                        
                        // Redirect to login page when modal is closed
                        $("#passwordResetSuccessModal").on("hidden.bs.modal", function() {
                            window.location.href = "login.php";
                        });
                    } catch (e) {
                        console.error("Error showing modal:", e);
                        // Fallback to alert if modal fails
                        alert(response.message);
                        setTimeout(function() {
                            window.location.href = "login.php";
                        }, 1500);
                    }
                } else {
                    // Show error message
                    $("#resetPasswordForm, form[action*='reset_password']").prepend(
                        '<div class="alert alert-danger">' + response.message + '</div>'
                    );
                    
                    // Reset button state
                    submitBtn.html(originalBtnText).prop("disabled", false);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                console.log("Response text:", xhr.responseText);
                
                // Show generic error message
                $("#resetPasswordForm, form[action*='reset_password']").prepend(
                    '<div class="alert alert-danger">An error occurred. Please try again later.</div>'
                );
                
                // Reset button state
                submitBtn.html(originalBtnText).prop("disabled", false);
            }
        });
    });
});