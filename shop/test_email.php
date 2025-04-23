<?php
/**
 * Email Test Script
 * 
 * This script tests the email sending functionality directly.
 */
require_once '../admin/config/dbcon.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/email_test.log');

// Include PHPMailer
$phpmailerPath = __DIR__ . '/PHPMailer-6.9.3/src/PHPMailer.php';
if (file_exists($phpmailerPath)) {
    require_once $phpmailerPath;
    require_once __DIR__ . '/PHPMailer-6.9.3/src/SMTP.php';
    require_once __DIR__ . '/PHPMailer-6.9.3/src/Exception.php';
} else {
    die("PHPMailer not found at: $phpmailerPath");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Test email function
function sendTestEmail($to) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->Debugoutput = function($str, $level) { error_log("PHPMailer ($level): $str"); };
        $mail->isSMTP();
        $mail->Host = 'smtp.elasticemail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bydclothing@gmail.com';
        $mail->Password = 'DD35482182C57339179CC38EA81908D0B285';
        // Fix the encryption constant
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525;
        
        // Recipients
        $mail->setFrom('noreply@bydclothing.com', 'BYD Clothing');
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email from BYD Clothing';
        $mail->Body = '<h1>Test Email</h1><p>This is a test email to verify the email sending functionality.</p>';
        $mail->AltBody = 'This is a test email to verify the email sending functionality.';
        
        // Send email
        $mail->send();
        echo "Test email sent successfully to $to<br>";
        return true;
    } catch (Exception $e) {
        echo "Test email failed: " . $mail->ErrorInfo . "<br>";
        error_log("Test email error: " . $mail->ErrorInfo);
        return false;
    }
}

// Get email from form or use default
$testEmail = $_POST['email'] ?? '';

// Display form
echo '<!DOCTYPE html>
<html>
<head>
    <title>Email Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="email"] { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Test Tool</h1>
        <form method="post">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="' . htmlspecialchars($testEmail) . '" required>
            </div>
            <button type="submit">Send Test Email</button>
        </form>';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($testEmail)) {
    echo '<div class="result">';
    echo '<h2>Test Results</h2>';
    
    // Check if PHPMailer files exist
    echo '<p>PHPMailer path: ' . $phpmailerPath . ' - ' . (file_exists($phpmailerPath) ? 'Found' : 'Not Found') . '</p>';
    
    // Send test email
    $result = sendTestEmail($testEmail);
    
    echo '</div>';
}

echo '</div></body></html>';
?>