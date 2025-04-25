<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log('OTP verification started');

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
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    
    // For testing, also check if OTP is in session
    if (empty($otp) && isset($_SESSION['test_otp'])) {
        $otp = $_SESSION['test_otp'];
    }
    
    // Validate inputs
    if (empty($email)) {
        // Try to get email from session
        if (isset($_SESSION['reset_email'])) {
            $email = $_SESSION['reset_email'];
        } else {
            throw new Exception('Email address is required');
        }
    }
    
    if (empty($otp)) {
        throw new Exception('Please enter the verification code');
    }
    
    // Check if OTP exists and is valid
    $query = "SELECT * FROM password_resets WHERE email = ? AND otp = ? AND used = 0 AND expiry > NOW()";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception('Database error: ' . $con->error);
    }
    
    $stmt->bind_param('ss', $email, $otp);
    if (!$stmt->execute()) {
        throw new Exception('Database error checking OTP: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Invalid or expired verification code');
    }
    
    // OTP is valid, generate a reset token
    $reset_data = $result->fetch_assoc();
    $token = bin2hex(random_bytes(32));
    
    // Update the password_resets record with the token
    $query = "UPDATE password_resets SET token = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception('Database error: ' . $con->error);
    }
    
    $stmt->bind_param('si', $token, $reset_data['id']);
    if (!$stmt->execute()) {
        throw new Exception('Database error generating token: ' . $stmt->error);
    }
    
    // Store token in session for easy access
    $_SESSION['reset_token'] = $token;
    $_SESSION['reset_email'] = $email;
    
    // Return success with the token
    $response = [
        'status' => 'success',
        'message' => 'Verification successful',
        'token' => $token,
        'email' => $email
    ];
    
} catch (Exception $e) {
    error_log('OTP verification error: ' . $e->getMessage());
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

// Always return JSON
echo json_encode($response);
exit;