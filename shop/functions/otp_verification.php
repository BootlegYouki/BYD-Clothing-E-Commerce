<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Function to generate a 6-digit OTP
function generateOTP() {
    return sprintf("%06d", mt_rand(1, 999999));
}

// Function to save OTP in the database
function storeOTP($conn, $email, $otp) {
    // First, invalidate any existing OTPs for this email
    $invalidate_query = "UPDATE otp_verification SET is_expired = 1 WHERE email = ?";
    $stmt = $conn->prepare($invalidate_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Now insert the new OTP
    $insert_query = "INSERT INTO otp_verification (email, otp, created_at, expiry_time) 
                     VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE))";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ss", $email, $otp);
    return $stmt->execute();
}

// Function to send OTP via email
function sendOTPEmail($email, $otp, $firstname) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = 0; // Set to 0 in production
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'darrenjade24@gmail.com'; // Your email
        $mail->Password = 'ezyz zcbe lzmx xzgr'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('darrenjade24@gmail.com', 'BYD Clothing');
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Email Verification Code';
        
        // Email body with professional formatting
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h1 style="color: #ff7f50;">BYD Clothing</h1>
            </div>
            <h2 style="color: #ff7f50;">Email Verification</h2>
            <p>Hello ' . htmlspecialchars($firstname) . ',</p>
            <p>Thank you for creating an account with BYD Clothing. To complete your registration, please use the verification code below:</p>
            <div style="background-color: #f7f7f7; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px;">
                <h2 style="color: #ff7f50; margin: 0; letter-spacing: 5px;">' . $otp . '</h2>
            </div>
            <p>This code will expire in 15 minutes.</p>
            <p>If you did not create an account with us, please ignore this email.</p>
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777;">
                <p>© ' . date('Y') . ' BYD Clothing. All rights reserved.</p>
                <p>This is an automated message, please do not reply to this email.</p>
            </div>
        </div>';
        
        $mail->AltBody = "Hello $firstname,\n\nYour verification code is: $otp\n\nThis code will expire in 15 minutes.\n\nBYD Clothing";
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// Function to validate OTP
function validateOTP($conn, $email, $otp) {
    $query = "SELECT * FROM otp_verification WHERE email = ? AND otp = ? AND is_expired = 0 AND expiry_time > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Mark OTP as expired after successful validation
        $update_query = "UPDATE otp_verification SET is_expired = 1 WHERE email = ? AND otp = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $email, $otp);
        $update_stmt->execute();
        
        return true;
    }
    
    return false;
}
?>