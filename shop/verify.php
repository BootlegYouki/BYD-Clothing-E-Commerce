<?php
session_start();
require_once '../admin/config/dbcon.php';
require_once 'functions/otp_verification.php';

// Check if user is already logged in
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['verify_email'] ?? '';
$firstname = $_SESSION['verify_firstname'] ?? '';
$message = '';
$messageType = 'danger';

// If no email in session, redirect to login
if (empty($email)) {
    header("Location: index.php");
    exit();
}

// For testing - get the OTP from database
$test_otp = '';
if (isset($conn)) {
    $query = "SELECT otp FROM otp_verification WHERE email = '$email' ORDER BY created_at DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $test_otp = $row['otp'];
    }
}

// Handle messages based on URL parameters
if(isset($_GET['invalidOTP'])) {
    $message = "Invalid or expired verification code. Please try again.";
} elseif(isset($_GET['resendSuccess'])) {
    $message = "A new verification code has been sent to your email.";
    $messageType = 'info';
} elseif(isset($_GET['resendFailed'])) {
    $message = "Failed to send verification code. Please try again.";
} elseif(isset($_GET['verifyRequired'])) {
    $message = "Please verify your email before logging in.";
    $messageType = 'warning';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - BYD Clothing</title>
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/logo/logo.ico" type="image/x-icon">
    <!-- ICONSCSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <style>
        .verification-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .btn-verify {
            background-color: #ff7f50;
            color: white;
            border: none;
        }
        .btn-verify:hover {
            background-color: #ff6b3d;
        }
        .resend-link {
            color: #ff7f50;
            text-decoration: none;
        }
        .resend-link:hover {
            text-decoration: underline;
        }
        .test-otp {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

    <section class="my-5 py-5">
        <div class="container mt-5">
            <div class="verification-container">
                <h2 class="text-center mb-4">Email Verification</h2>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>
                
                <p class="text-center">We've sent a verification code to <strong><?= htmlspecialchars($email) ?></strong></p>
                <p class="text-center">Please enter the 6-digit code below:</p>
                
                <form method="POST" action="functions/authcode.php">
                    <div class="mb-4">
                        <input type="text" name="otp" class="form-control form-control-lg text-center" 
                               maxlength="6" placeholder="Enter 6-digit code" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="verify_otp" class="btn btn-verify">Verify Email</button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p>Didn't receive the code?</p>
                    <form method="POST" action="functions/authcode.php">
                        <button type="submit" name="resend_otp" class="btn btn-link resend-link">Resend Code</button>
                    </form>
                </div>
                
                <?php if (!empty($test_otp)): ?>
                <!-- For testing purposes only - remove in production -->
                <div class="test-otp mt-4">
                    <p class="mb-0"><small>For testing: Your OTP is <strong><?= $test_otp ?></strong></small></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>