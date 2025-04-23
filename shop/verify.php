<?php
session_start();
include '../admin/config/dbcon.php';
include 'functions/otp_verification.php';

// Check if user email exists in session
if (!isset($_SESSION['verify_email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['verify_email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - BYD Clothing</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .verification-container {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .otp-input {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        .otp-input input {
            width: 50px;
            height: 50px;
            margin: 0 5px;
            text-align: center;
            font-size: 24px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-verify {
            background-color: #ff7f50;
            border: none;
            width: 100%;
            padding: 12px;
            font-weight: bold;
        }
        .btn-verify:hover {
            background-color: #ff6b3d;
        }
        .resend {
            text-align: center;
            margin-top: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-container">
            <div class="logo">
                <h2>BYD Clothing</h2>
            </div>
            
            <?php if(isset($_GET['invalidOTP'])): ?>
            <div class="alert alert-danger">
                Invalid verification code. Please try again.
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['resendSuccess'])): ?>
            <div class="alert alert-success">
                A new verification code has been sent to your email.
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['resendFailed'])): ?>
            <div class="alert alert-danger">
                Failed to send verification code. Please try again.
            </div>
            <?php endif; ?>
            
            <h4 class="text-center">Verify Your Email</h4>
            <p class="text-center">We've sent a verification code to <strong><?php echo htmlspecialchars($email); ?></strong></p>
            
            <form action="functions/authcode.php" method="POST">
                <div class="form-group">
                    <label for="otp">Enter Verification Code</label>
                    <input type="text" class="form-control form-control-lg" id="otp" name="otp" maxlength="6" required>
                </div>
                
                <button type="submit" name="verify_otp" class="btn btn-primary btn-verify">Verify Email</button>
            </form>
            
            <div class="resend">
                <p>Didn't receive the code?</p>
                <form action="functions/authcode.php" method="POST">
                    <button type="submit" name="resend_otp" class="btn btn-link">Resend Code</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>