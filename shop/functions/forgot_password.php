<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_log('Forgot password request started');

session_start();
require_once '../../admin/config/dbcon.php';

// Load environment variables from .env file
$env = parse_ini_file('../../.env');

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
    // Get email from POST data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $resend = isset($_POST['resend']) ? (bool)$_POST['resend'] : false;
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }
    
    // Check if email exists in database
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception('Database error: ' . $con->error);
    }
    
    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) {
        throw new Exception('Database error executing query: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        throw new Exception('No account found with that email address');
    }
    
    // Get user data
    $user = $result->fetch_assoc();
    
    // Generate OTP
    $otp = sprintf("%06d", mt_rand(1, 999999));
    $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // Store OTP in database
    if ($resend) {
        $query = "UPDATE password_resets SET otp = ?, expiry = ? WHERE email = ? AND used = 0";
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        $stmt->bind_param('sss', $otp, $expiry, $email);
    } else {
        // Delete any existing unused reset requests
        $query = "DELETE FROM password_resets WHERE email = ? AND used = 0";
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        $stmt->bind_param('s', $email);
        if (!$stmt->execute()) {
            throw new Exception('Database error deleting old requests: ' . $stmt->error);
        }
        
        // Insert new reset request - note we're only setting otp and expiry, not token yet
        $query = "INSERT INTO password_resets (email, otp, expiry) VALUES (?, ?, ?)";
        $stmt = $con->prepare($query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $con->error);
        }
        $stmt->bind_param('sss', $email, $otp, $expiry);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Database error saving OTP: ' . $stmt->error);
    }
    
    // Send email with OTP using PHPMailer
    require_once '../../vendor/autoload.php';
    
    // If vendor/autoload.php doesn't exist or PHPMailer is not installed
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        // Use PHP's mail() function as fallback
        $to = $email;
        $subject = "Password Reset Verification Code";
        
        // Create HTML email body
        $htmlBody = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="color: #333;">BYD Clothing</h2>
            </div>
            <h2 style="color: #333; text-align: center;">Password Reset Verification</h2>
            <p style="color: #666; line-height: 1.5;">Hello ' . htmlspecialchars($user['name'] ?? 'Customer') . ',</p>
            <p style="color: #666; line-height: 1.5;">We received a request to reset your password. Please use the verification code below to continue with the password reset process:</p>
            <div style="background-color: #f7f7f7; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px;">
                <h1 style="color: #333; letter-spacing: 5px; margin: 0;">' . $otp . '</h1>
            </div>
            <p style="color: #666; line-height: 1.5;">This code will expire in 15 minutes. If you did not request a password reset, please ignore this email or contact us if you have concerns.</p>
            <p style="color: #666; line-height: 1.5;">Thank you,<br>BYD Clothing Team</p>
        </div>';
        
        // Email headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . ($env['SMTP_FROM_NAME'] ?? 'BYD Clothing') . " <" . ($env['SMTP_FROM_EMAIL'] ?? 'noreply@bydclothing.com') . ">\r\n";
        $headers .= "Reply-To: support@bydclothing.com\r\n";
        
        // Send email
        $mailSent = mail($to, $subject, $htmlBody, $headers);
        
        if (!$mailSent) {
            error_log("Failed to send email to $email using mail() function");
        }
    } else {
        // Use PHPMailer with SMTP
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $env['SMTP_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $env['SMTP_USERNAME'] ?? '';
            $mail->Password = $env['SMTP_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $env['SMTP_PORT'] ?? 587;
            
            // Recipients
            $mail->setFrom($env['SMTP_FROM_EMAIL'] ?? 'noreply@bydclothing.com', $env['SMTP_FROM_NAME'] ?? 'BYD Clothing');
            $mail->addAddress($email, $user['name'] ?? '');
            $mail->addReplyTo('support@bydclothing.com', 'BYD Support');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="color: #333;">BYD Clothing</h2>
                </div>
                <h2 style="color: #333; text-align: center;">Password Reset Verification</h2>
                <p style="color: #666; line-height: 1.5;">Hello ' . htmlspecialchars($user['name'] ?? 'Customer') . ',</p>
                <p style="color: #666; line-height: 1.5;">We received a request to reset your password. Please use the verification code below to continue with the password reset process:</p>
                <div style="background-color: #f7f7f7; padding: 15px; text-align: center; margin: 20px 0; border-radius: 5px;">
                    <h1 style="color: #333; letter-spacing: 5px; margin: 0;">' . $otp . '</h1>
                </div>
                <p style="color: #666; line-height: 1.5;">This code will expire in 15 minutes. If you did not request a password reset, please ignore this email or contact us if you have concerns.</p>
                <p style="color: #666; line-height: 1.5;">Thank you,<br>BYD Clothing Team</p>
            </div>';
            $mail->AltBody = "Hello " . ($user['name'] ?? 'Customer') . ",\n\n" .
                        "We received a request to reset your password. Please use the verification code below to continue with the password reset process:\n\n" .
                        $otp . "\n\n" .
                        "This code will expire in 15 minutes. If you did not request a password reset, please ignore this email or contact us if you have concerns.\n\n" .
                        "Thank you,\nBYD Clothing Team";
            
            $mail->send();
            $mailSent = true;
        } catch (Exception $e) {
            error_log("Failed to send email using PHPMailer: " . $mail->ErrorInfo);
            $mailSent = false;
        }
    }
    
    // For development, include the OTP in the response
    $response = [
        'status' => 'success',
        'message' => 'Verification code sent to your email' . (isset($env['APP_ENV']) && $env['APP_ENV'] === 'production' ? '' : ' (Code: ' . $otp . ')')
    ];
    
    // Store the OTP in session for easy access during testing
    $_SESSION['test_otp'] = $otp;
    $_SESSION['reset_email'] = $email;
    
} catch (Exception $e) {
    error_log('Forgot password error: ' . $e->getMessage());
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

// Always return JSON
echo json_encode($response);
exit;