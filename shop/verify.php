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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: coral;
            --secondary-color: #f5f5f5;
            --text-color: #333;
            --border-radius: 10px;
        }
        
        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .verification-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 40px 30px;
            transition: all 0.3s ease;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .logo h2 {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        h4 {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 15px;
        }
        
        .email-info {
            color: #666;
            font-size: 15px;
            margin-bottom: 30px;
        }
        
        .email-value {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
        }
        
        .otp-inputs input {
            width: 55px;
            height: 55px;
            border: 2px solid #ddd;
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            background-color: #fcfcfc;
            transition: all 0.2s;
        }
        
        .otp-inputs input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 127, 80, 0.2);
            outline: none;
        }
        
        .btn-verify {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            width: 100%;
            padding: 14px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 16px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .btn-verify:hover {
            background-color: #ff6347;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 127, 80, 0.3);
            color: #fff;
        }
        
        .resend {
            text-align: center;
            margin-top: 30px;
            color: #777;
        }
        
        .btn-resend {
            color: var(--primary-color);
            font-weight: 600;
            padding: 0;
            background: none;
            border: none;
            transition: all 0.2s;
        }
        
        .btn-resend:hover {
            color: #ff6347;
            text-decoration: none;
        }
        
        .timer {
            font-size: 14px;
            color: #888;
            margin-top: 5px;
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: #ffe5e5;
            color: #d63031;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin-top: 15px;
        }
        
        .spinner-border {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="verification-container">
            <div class="logo">
                <h2>BYD Clothing</h2>
            </div>
            
            <?php if(isset($_GET['invalidOTP'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle mr-2"></i> Invalid verification code. Please try again.
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['resendSuccess'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i> A new verification code has been sent to your email.
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['resendFailed'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle mr-2"></i> Failed to send verification code. Please try again.
            </div>
            <?php endif; ?>
            
            <h4 class="text-center">Verify Your Email</h4>
            <p class="text-center email-info">We've sent a verification code to <span class="email-value"><?php echo htmlspecialchars($email); ?></span></p>
            
            <form id="verificationForm" action="functions/authcode.php" method="POST">
                <div class="otp-inputs">
                    <input type="text" class="otp-input" maxlength="1" autofocus inputmode="numeric" pattern="[0-9]*">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*">
                </div>
                
                <input type="hidden" name="otp" id="otpValue">
                <input type="hidden" name="verify_otp" value="1">
                <button type="submit" id="verifyBtn" class="btn btn-verify">
                    <i class="fas fa-shield-alt mr-2"></i> Verify Email
                </button>
                
                <div class="loading mt-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span class="ml-2">Verifying...</span>
                </div>
            </form>
            
            <div class="resend">
                <p>Didn't receive the code?</p>
                <form id="resendForm" action="functions/authcode.php" method="POST">
                    <input type="hidden" name="resend_otp" value="1">
                    <button type="submit" id="resendBtn" class="btn-resend" disabled>Resend Code</button>
                </form>
                <div class="timer mt-1">You can request a new code in <span id="countdown">60</span> seconds</div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const verificationForm = document.getElementById('verificationForm');
            const otpValue = document.getElementById('otpValue');
            const resendBtn = document.getElementById('resendBtn');
            const loading = document.querySelector('.loading');
            
            // Only allow numeric input
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });
            
            // Auto focus to the next input after typing
            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1) {
                        if (index !== inputs.length - 1) {
                            inputs[index + 1].focus();
                        } else {
                            // When last input is filled, submit the form automatically
                            let allFilled = true;
                            inputs.forEach(inp => {
                                if (inp.value.length !== 1) allFilled = false;
                            });
                            
                            if (allFilled) {
                                setTimeout(() => {
                                    verificationForm.dispatchEvent(new Event('submit'));
                                }, 300);
                            }
                        }
                    }
                });
                
                // Handle backspace
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!this.value && index !== 0) {
                            inputs[index - 1].focus();
                            inputs[index - 1].select();
                        }
                    }
                });
                
                // Handle paste event
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text').trim();
                    
                    if (/^\d+$/.test(pasteData) && pasteData.length <= 6) {
                        for (let i = 0; i < Math.min(pasteData.length, inputs.length); i++) {
                            inputs[i].value = pasteData.charAt(i);
                        }
                        
                        if (pasteData.length === 6) {
                            setTimeout(() => {
                                verificationForm.dispatchEvent(new Event('submit'));
                            }, 300);
                        } else if (pasteData.length < 6) {
                            inputs[pasteData.length].focus();
                        }
                    }
                });
            });
            
            // When submitting, combine all inputs into one value
            verificationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let otp = '';
                inputs.forEach(input => {
                    otp += input.value;
                });
                
                otpValue.value = otp;
                
                if (otp.length === 6) {
                    loading.style.display = 'block';
                    this.submit();
                } else {
                    alert('Please enter the complete 6-digit code');
                }
            });
            
            // Countdown timer
            let countdown = 60;
            const countdownDisplay = document.getElementById('countdown');
            resendBtn.disabled = true;
            
            const timer = setInterval(() => {
                countdown--;
                countdownDisplay.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    document.querySelector('.timer').style.display = 'none';
                }
            }, 1000);
            
            // Handle resend form submission
            document.getElementById('resendForm').addEventListener('submit', function() {
                resendBtn.disabled = true;
            });
        });
    </script>
</body>
</html>