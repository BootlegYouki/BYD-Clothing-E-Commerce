<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../admin/config/env_loader.php'; // Adjust path for deeper directory

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Function to generate a 6-digit OTP
function generateOTP() {
    return sprintf("%06d", mt_rand(1, 999999));
}

// Function to save OTP in the database
function storeOTP($conn, $email, $otp) {
    // First, delete any existing OTPs for this email
    $invalidate_query = "DELETE FROM otp_verification WHERE email = ?";
    $stmt = $conn->prepare($invalidate_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Check the table structure to see what columns exist
    $table_info_query = "SHOW COLUMNS FROM otp_verification";
    $table_info = $conn->query($table_info_query);
    $columns = [];
    while ($column = $table_info->fetch_assoc()) {
        $columns[] = $column['Field'];
    }
    
    // Prepare insert query based on available columns
    if (in_array('expiry_time', $columns)) {
        $insert_query = "INSERT INTO otp_verification (email, otp, created_at, expiry_time) 
                         VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 MINUTE))";
    } else {
        $insert_query = "INSERT INTO otp_verification (email, otp, created_at) 
                         VALUES (?, ?, NOW())";
    }
    
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ss", $email, $otp);
    return $stmt->execute();
}

// Function to send OTP via email with improved design
function sendOTPEmail($email, $otp, $firstname) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = 0; // Set to 0 in production
        $mail->isSMTP();
        $mail->Host = getEnvVar('SMTP_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = getEnvVar('SMTP_USERNAME', '');
        $mail->Password = getEnvVar('SMTP_PASSWORD', '');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getEnvVar('SMTP_PORT', '587');
        
        // Recipients
        $mail->setFrom(getEnvVar('SMTP_FROM_EMAIL', ''), getEnvVar('SMTP_FROM_NAME', 'BYD Clothing'));
        $mail->addAddress($email);
        
        // Anti-spam headers
        $mail->addReplyTo(getEnvVar('SMTP_FROM_EMAIL', ''), getEnvVar('SMTP_FROM_NAME', 'BYD Clothing Support'));
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->MessageID = '<' . time() . '.' . uniqid() . '@' . $_SERVER['HTTP_HOST'] . '>';
        $mail->addCustomHeader('List-Unsubscribe', '<mailto:' . getEnvVar('SMTP_FROM_EMAIL', '') . '?subject=Unsubscribe>');
        $mail->addCustomHeader('X-Mailer', 'BYD Clothing Customer Service');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code from BYD Clothing';
        
        // Enhanced Email Template
        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
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
                            <h2 style="color: #ff7f50; margin-top: 0; margin-bottom: 20px; font-size: 22px; font-family: Arial, Helvetica, sans-serif;">Verify Your Email</h2>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">Hello <strong>' . htmlspecialchars($firstname) . '</strong>,</p>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">Thank you for creating an account with BYD Clothing! To complete your registration and secure your account, please enter the verification code below:</p>
                            
                            <!-- OTP Container -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 10px; padding: 5px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td align="center" style="padding: 20px 10px;">
                                                    <div style="font-family: \'Courier New\', monospace; font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #ff7f50;">
                                                        ' . $otp . '
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 15px; font-family: Arial, Helvetica, sans-serif;">This verification code will expire in <strong style="color: #ff7f50;">15 minutes</strong>.</p>
                            
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">If you did not attempt to create an account with us, please disregard this email.</p>
                            
                            <!-- Text content for better text-to-HTML ratio -->
                            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 25px; font-family: Arial, Helvetica, sans-serif;">At BYD Clothing, we\'re committed to providing quality fashion and excellent customer service. Thank you for choosing to shop with us.</p>
                            
                            <!-- Divider -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td style="border-bottom: 1px solid #e9ecef;"></td>
                                </tr>
                            </table>
                            
                            <p style="color: #666666; font-size: 14px; line-height: 1.5; margin-bottom: 15px; font-family: Arial, Helvetica, sans-serif;">If you have any questions or need assistance, please contact our support team at <a href="mailto:' . getEnvVar('SMTP_FROM_EMAIL', '') . '" style="color: #ff7f50; text-decoration: none;">' . getEnvVar('SMTP_FROM_EMAIL', '') . '</a>.</p>
                            
                            <p style="color: #666666; font-size: 14px; line-height: 1.5; font-family: Arial, Helvetica, sans-serif;">Thank you for choosing BYD Clothing!</p>
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
                                            You\'re receiving this email because you\'re creating an account with BYD Clothing.
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
        
        // Plain text alternative for email clients that don't support HTML
        $mail->AltBody = "Hello $firstname,

Thank you for creating an account with BYD Clothing!

Your verification code is: $otp

This code will expire in 15 minutes.

If you did not attempt to create an account with us, please disregard this email.

At BYD Clothing, we're committed to providing quality fashion and excellent customer service. Thank you for choosing to shop with us.

If you have questions or need assistance, please contact our support team at " . getEnvVar('SMTP_FROM_EMAIL', '') . ".

Thank you for choosing BYD Clothing!

© " . date('Y') . " BYD Clothing. All rights reserved.
" . getEnvVar('COMPANY_ADDRESS', 'BYD Clothing, 123 Fashion Street, Style City') . "

You're receiving this email because you're creating an account with BYD Clothing.";
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// Function to validate OTP
function validateOTP($conn, $email, $otp) {
    // Check the table structure to see what columns exist
    $table_info_query = "SHOW COLUMNS FROM otp_verification";
    $table_info = $conn->query($table_info_query);
    $columns = [];
    while ($column = $table_info->fetch_assoc()) {
        $columns[] = $column['Field'];
    }
    
    // Prepare query based on available columns
    if (in_array('expiry_time', $columns)) {
        $query = "SELECT * FROM otp_verification WHERE email = ? AND otp = ? AND expiry_time > NOW()";
    } else {
        // If no expiry_time column, just check if OTP exists
        // Optionally, you could add a time-based check using created_at if available
        if (in_array('created_at', $columns)) {
            $query = "SELECT * FROM otp_verification WHERE email = ? AND otp = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        } else {
            $query = "SELECT * FROM otp_verification WHERE email = ? AND otp = ?";
        }
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Delete the used OTP after successful validation
        $delete_query = "DELETE FROM otp_verification WHERE email = ? AND otp = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ss", $email, $otp);
        $delete_stmt->execute();
        
        return true;
    }
    
    return false;
}
?>