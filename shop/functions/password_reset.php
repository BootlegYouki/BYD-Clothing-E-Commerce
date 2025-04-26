<?php
require_once __DIR__ . '/../../admin/config/dbcon.php';
require_once __DIR__ . '/otp_verification.php';
session_start();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check the action type
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'request_reset') {
            handleResetRequest($conn);
        } elseif ($_POST['action'] === 'verify_token') {
            verifyResetToken($conn);
        } elseif ($_POST['action'] === 'reset_password') {
            resetPassword($conn);
        }
    }
}

// Handle password reset request
function handleResetRequest($conn) {
    // Get the email
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }
    
    // Check if the email exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No account associated with this email address.'
        ]);
        exit;
    }
    
    // Fetch user data for email
    $user = $result->fetch_assoc();
    $firstname = $user['firstname'];
    
    // Generate OTP
    $token = generateOTP();
    
    // Delete any existing reset tokens for this email
    $reset_query = "DELETE FROM password_resets WHERE email = ?";
    $reset_stmt = $conn->prepare($reset_query);
    $reset_stmt->bind_param("s", $email);
    $reset_stmt->execute();
    
    // Store token with expiration time (1 hour)
    $insert_query = "INSERT INTO password_resets (email, token, expiry_time) 
                    VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ss", $email, $token);
    
    if ($insert_stmt->execute()) {
        // Send email with reset link
        if (sendPasswordResetEmail($email, $token, $firstname)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Password reset instructions have been sent to your email.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send email. Please try again later.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred. Please try again later.'
        ]);
    }
    exit;
}

// Send password reset email
function sendPasswordResetEmail($email, $token, $firstname) {
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = 0; // Set to 0 in production
        $mail->isSMTP();
        $mail->Host = getEnvVar('SMTP_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = getEnvVar('SMTP_USERNAME', '');
        $mail->Password = getEnvVar('SMTP_PASSWORD', '');
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getEnvVar('SMTP_PORT', '587');
        
        // Recipients
        $fromEmail = getEnvVar('SMTP_FROM_EMAIL', '');
        $fromName = getEnvVar('SMTP_FROM_NAME', 'BYD Clothing');
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($email);
        
        // Anti-spam headers - Expanded and consistent with OTP emails
        $mail->addReplyTo($fromEmail, $fromName . ' Support');
        $mail->Sender = $fromEmail;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->MessageID = '<' . time() . '.' . uniqid() . '@' . $_SERVER['HTTP_HOST'] . '>';
        $mail->addCustomHeader('List-Unsubscribe', '<mailto:' . getEnvVar('SMTP_FROM_EMAIL', '') . '?subject=Unsubscribe>');
        $mail->addCustomHeader('X-Mailer', 'BYD Clothing Customer Service');
        $mail->addCustomHeader('Precedence', 'bulk');
        $mail->addCustomHeader('Auto-Submitted', 'auto-generated');
        $mail->addCustomHeader('X-Auto-Response-Suppress', 'All');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password - BYD Clothing';
        
        // Reset link - make sure to use the correct path
        $serverName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        $resetLink = 'https://' . $serverName . '/shop/reset-password.php?token=' . $token . '&email=' . urlencode($email);
        
        // Email template
        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <!--[if mso]>
    <style type="text/css">
        table {border-collapse: collapse; border-spacing: 0; margin: 0;}
        div, td {padding: 0;}
        div {margin: 0 !important;}
    </style>
    <noscript>
    <xml>
        <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: none; text-size-adjust: none;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <!-- Email Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 0; background-color: #ff7f50; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">BYD Clothing</h1>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #ff7f50; margin-top: 0; margin-bottom: 20px; font-size: 22px; font-family: Arial, Helvetica, sans-serif;">Reset Your Password</h2>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">Hello <strong>' . htmlspecialchars($firstname) . '</strong>,</p>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">We received a request to reset your password for your BYD Clothing account. If you didn\'t make this request, you can safely ignore this email.</p>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">To reset your password, click the button below:</p>
                            
                            <!-- Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="' . $resetLink . '" target="_blank" style="display: inline-block; background-color: #ff7f50; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: 600; font-size: 16px; font-family: Arial, Helvetica, sans-serif;">Reset Password</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 15px; font-family: Arial, Helvetica, sans-serif;">This link and code will expire in <strong style="color: #ff7f50;">60 minutes</strong>.</p>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">If you didn\'t request a password reset, please ignore this email or contact our support team if you have any concerns.</p>
                            
                            <!-- Additional text content for better text-to-HTML ratio -->
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">At BYD Clothing, we\'re committed to the security of your account and personal information. We recommend using a strong, unique password for your account.</p>
                            
                            <!-- Divider -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td style="border-bottom: 1px solid #e9ecef;"></td>
                                </tr>
                            </table>
                            
                            <p style="color: #666666; font-size: 14px; line-height: 1.5; margin-bottom: 15px; font-family: Arial, Helvetica, sans-serif;">If you have any questions or need assistance, please contact our support team at <a href="mailto:' . $fromEmail . '" style="color: #ff7f50; text-decoration: none;">' . $fromEmail . '</a>.</p>
                            
                            <p style="color: #666666; font-size: 14px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Thank you for shopping with BYD Clothing!</p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; border-top: 1px solid #e9ecef;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0; color: #777777; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                            &copy; ' . date('Y') . ' BYD Clothing. All rights reserved.
                                        </p>
                                        <p style="margin: 10px 0 0; color: #777777; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                            You\'re receiving this email because you requested a password reset for your BYD Clothing account.
                                        </p>
                                        <p style="margin: 10px 0 0; color: #777777; font-size: 12px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">
                                            ' . htmlspecialchars(getEnvVar('COMPANY_ADDRESS', 'BYD Clothing, 123 Fashion Street, Style City')) . '
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
        
        // Plain text alternative
        $mail->AltBody = "Hello $firstname,

We received a request to reset your password for your BYD Clothing account.

To reset your password, please visit this link:
$resetLink

This link will expire in 60 minutes.

At BYD Clothing, we're committed to the security of your account and personal information. We recommend using a strong, unique password for your account.

If you didn't request a password reset, please ignore this email or contact our support team at $fromEmail if you have any concerns.

Thank you for choosing BYD Clothing!

© " . date('Y') . " BYD Clothing. All rights reserved.
" . getEnvVar('COMPANY_ADDRESS', 'BYD Clothing, 123 Fashion Street, Style City') . "

You're receiving this email because you requested a password reset for your BYD Clothing account.";
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// Verify reset token
function verifyResetToken($conn) {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Validate inputs
    if (empty($token) || empty($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request. Missing required parameters.'
        ]);
        exit;
    }
    
    // Check if token is valid and not expired
    $query = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expiry_time > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Token verified successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or expired token. Please request a new password reset.'
        ]);
    }
    exit;
}

// Reset user password
function resetPassword($conn) {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    if (empty($token) || empty($email) || empty($password)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit;
    }
    
    // Validate password length
    if (strlen($password) < 8) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password must be at least 8 characters long.'
        ]);
        exit;
    }
    
    // Check if token is valid and not expired
    $query = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expiry_time > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or expired token. Please request a new password reset.'
        ]);
        exit;
    }
    
    // Update the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $update_query = "UPDATE users SET password = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ss", $hashed_password, $email);
    
    if ($update_stmt->execute()) {
        // Delete the used token
        $delete_query = "DELETE FROM password_resets WHERE email = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("s", $email);
        $delete_stmt->execute();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Your password has been updated successfully. You can now log in with your new password.',
            'redirect' => '../index.php?resetSuccess=1'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update password. Please try again.'
        ]);
    }
    exit;
}
?>
