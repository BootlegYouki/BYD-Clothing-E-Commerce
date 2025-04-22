<?php
/**
 * OTP Verification Functions
 */

// Generate a random OTP code
function generateOTP($length = 6) {
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= mt_rand(0, 9);
    }
    return $otp;
}

// Send OTP via email
function sendOTPEmail($email, $otp, $firstname) {
    // For testing purposes, store OTP in session
    $_SESSION['test_otp'] = $otp;
    
    // Log the OTP for debugging
    error_log("OTP for $email: $otp");
    
    // Display the OTP on screen for testing (remove in production)
    echo "<script>alert('Your OTP is: $otp\\nThis is shown for testing only.');</script>";
    
    // Return true to simulate successful sending
    return true;
    
    /* 
    // Uncomment and configure this code when ready to use real email sending
    
    // Use PHPMailer for reliable email delivery
    require '../vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@gmail.com'; // Replace with your email
        $mail->Password   = 'your-app-password'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('noreply@bydclothing.com', 'BYD Clothing');
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "BYD Clothing - Email Verification Code";
        
        $message = "
        <html>
        <head>
            <title>Email Verification</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background-color: #000; color: #fff; padding: 15px; text-align: center; }
                .content { padding: 20px; }
                .otp-code { font-size: 24px; font-weight: bold; text-align: center; 
                            padding: 10px; margin: 20px 0; background-color: #f4f4f4; }
                .footer { background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Email Verification</h1>
                </div>
                <div class='content'>
                    <p>Hello " . htmlspecialchars($firstname) . ",</p>
                    <p>Thank you for registering with BYD Clothing. To complete your registration, please use the verification code below:</p>
                    
                    <div class='otp-code'>" . $otp . "</div>
                    
                    <p>This code will expire in 15 minutes.</p>
                    <p>If you did not request this code, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " BYD Clothing. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->Body = $message;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
    */
}

// Store OTP in database
function storeOTP($conn, $email, $otp) {
    // Delete any existing OTP for this email
    $delete_query = "DELETE FROM otp_verification WHERE email = '$email'";
    mysqli_query($conn, $delete_query);
    
    // Insert new OTP with expiration time (15 minutes from now)
    $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    $insert_query = "INSERT INTO otp_verification (email, otp, expiry) VALUES ('$email', '$otp', '$expiry')";
    return mysqli_query($conn, $insert_query);
}

// Verify OTP
function verifyOTP($conn, $email, $otp) {
    $query = "SELECT * FROM otp_verification WHERE email = '$email' AND otp = '$otp' AND expiry > NOW()";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // OTP is valid, update user's email_verified status
        $update_query = "UPDATE users SET email_verified = 1 WHERE email = '$email'";
        $update_result = mysqli_query($conn, $update_query);
        
        // Delete the used OTP
        $delete_query = "DELETE FROM otp_verification WHERE email = '$email'";
        mysqli_query($conn, $delete_query);
        
        return $update_result;
    }
    
    return false;
}