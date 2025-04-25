<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log('Password reset started');

session_start();
require_once '../../admin/config/dbcon.php';

// Check if database connection is established
if (!isset($con) || $con === null) {
    // Try to get the connection variable that might have a different name
    if (isset($conn)) {
        $con = $conn;
    } else if (isset($db)) {
        $con = $db;
    } else if (isset($connection)) {
        $con = $connection;
    } else {
        // If no connection variable is found, create a new one
        $con = new mysqli("localhost", "root", "", "c3248bm8zvavug0p");
        if ($con->connect_error) {
            die(json_encode([
                'status' => 'error',
                'message' => 'Database connection failed: ' . $con->connect_error
            ]));
        }
    }
}

// Set content type to JSON before any output
header('Content-Type: application/json');

try {
    // Log received data for debugging
    error_log('Received data: ' . json_encode($_POST));
    
    // Get data from POST
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    // Try to get email and token from session if not provided
    if (empty($email) && isset($_SESSION['reset_email'])) {
        $email = $_SESSION['reset_email'];
    }
    
    if (empty($token) && isset($_SESSION['reset_token'])) {
        $token = $_SESSION['reset_token'];
    }
    
    // Validate inputs
    if (empty($email)) {
        throw new Exception('Email address is required');
    }
    
    if (empty($password)) {
        throw new Exception('Please enter a new password');
    }
    
    if (strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    
    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }
    
    // Verify token if provided
    if (!empty($token)) {
        $query = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND used = 0 AND expiry > NOW()";
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        
        $stmt->bind_param('ss', $email, $token);
        if (!$stmt->execute()) {
            throw new Exception('Database error verifying token: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Invalid or expired reset token');
        }
        
        $reset_data = $result->fetch_assoc();
    } else {
        // If no token, check if OTP was verified in this session
        if (!isset($_SESSION['reset_email']) || $_SESSION['reset_email'] !== $email) {
            throw new Exception('Password reset not authorized');
        }
    }
    
    // Update user's password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception('Database error: ' . $con->error);
    }
    
    $stmt->bind_param('ss', $hashed_password, $email);
    if (!$stmt->execute()) {
        throw new Exception('Database error updating password: ' . $stmt->error);
    }
    
    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to update password');
    }
    
    // Mark the reset request as used
    if (!empty($token)) {
        $query = "UPDATE password_resets SET used = 1 WHERE email = ? AND token = ?";
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        
        $stmt->bind_param('ss', $email, $token);
        $stmt->execute();
    }
    
    // Clear session data
    unset($_SESSION['reset_email']);
    unset($_SESSION['reset_token']);
    unset($_SESSION['test_otp']);
    
    // Return success with HTML for the modal
    $successModal = '
    <div class="modal fade" id="passwordResetSuccessModal" tabindex="-1" role="dialog" aria-labelledby="passwordResetSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="passwordResetSuccessModalLabel">Password Reset Successful</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4>Your password has been reset successfully!</h4>
                    <p class="text-muted">You can now login with your new password.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="../login.php" class="btn btn-primary btn-lg px-5">Proceed to Login</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#passwordResetSuccessModal").modal({
                backdrop: "static",
                keyboard: false
            });
            
            // Redirect to login page when modal is closed
            $("#passwordResetSuccessModal").on("hidden.bs.modal", function() {
                window.location.href = "../login.php";
            });
        });
    </script>';
    
    // For development, include the OTP in the response
    $response = [
        'status' => 'success',
        'message' => 'Your password has been reset successfully. You can now login with your new password.',
        'modal' => true,
        'modalContent' => '
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Password Reset Successful</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4>Your password has been reset successfully!</h4>
                    <p class="text-muted">You can now login with your new password.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="../login.php" class="btn btn-primary btn-lg px-5">Proceed to Login</a>
                </div>
            </div>
        </div>'
    ];
    
} catch (Exception $e) {
    error_log('Password reset error: ' . $e->getMessage());
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

// At the end of your reset_password.php file, update the success response:
$response = [
    'status' => 'success',
    'message' => 'Your password has been reset successfully. You can now login with your new password.'
];

// Always return JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;