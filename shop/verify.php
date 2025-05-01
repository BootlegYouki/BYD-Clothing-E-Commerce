<?php
session_start();
include '../admin/config/dbcon.php';
include 'functions/account/otp_verification.php';

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
    <link rel="icon" href="img/logo/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: coral;
            --secondary-color: #f5f5f5;
            --text-color: #333;
            --border-radius: 10px;
            --font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--secondary-color);
            font-family: var(--font-family);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .verification-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 20px 15px;
            transition: all 0.3s ease;
            margin: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: clamp(20px, 4vw, 25px);
        }
        
        .logo img {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .logo h2 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: clamp(24px, 5vw, 28px);
        }
        
        h4 {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 15px;
            font-size: clamp(18px, 4vw, 22px);
        }
        
        .email-info {
            color: #666;
            font-size: clamp(14px, 3vw, 15px);
            margin-bottom: 30px;
        }
        
        .email-value {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .otp-inputs {
            display: flex;
            gap: clamp(4px, 1vw, 10px);
            justify-content: center;
            margin: 30px 0;
            flex-wrap: nowrap;
        }
        
        .otp-inputs input {
            width: clamp(30px, 10vw, 55px); 
            height: clamp(30px, 10vw, 55px);
            border: 2px solid #ddd;
            border-radius: 12px;
            text-align: center;
            font-size: clamp(16px, 4vw, 24px);
            font-weight: 600;
            color: var(--primary-color);
            background-color: #fcfcfc;
            transition: all 0.2s;
            flex: 0 0 auto;
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
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 127, 80, 0.3);
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
        
        /* Add styles for message display */
        .message {
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
            border-radius: var(--border-radius);
            border: none;
            display: none;
        }
        
        .message-danger {
            background-color: #ffe5e5;
            color: #d63031;
        }
        
        .message-success {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        /* Styling for spam reminder */
        .spam-note {
            color: #777;
            font-size: 13px;
            margin-top: -10px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .spam-note i {
            color: var(--primary-color);
            font-size: 14px;
            margin-right: 4px;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="verification-container">
            <div class="logo">
                <img src="img/logo/logo_admin_light.png" alt="BYD Clothing Logo">
                <h2>BYD Clothing</h2>
            </div>
            
            <!-- Replace static alerts with dynamic message containers -->
            <div id="message-container"></div>
            
            <h4 class="text-center">Verify Your Email</h4>
            <p class="text-center email-info">We've sent a verification code to <span class="email-value"><?php echo htmlspecialchars($email); ?></span></p>
            <p class="spam-note"><i class="fas fa-info-circle"></i> If you don't see the email, please check your spam or junk folder.</p>
            
            <form id="verificationForm" action="functions/account/authcode.php" method="POST">
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
                    <span class="normal-state">
                        <i class="fas fa-shield-alt mr-2"></i> Verify Email
                    </span>
                    <span class="loading-state pb-1" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                    </span>
                </button>
            </form>
            
            <div class="resend">
                <p>Didn't receive the code?</p>
                <form id="resendForm" action="functions/account/authcode.php" method="POST">
                    <input type="hidden" name="resend_otp" value="1">
                    <button type="submit" id="resendBtn" class="btn-resend" disabled>
                        <span class="resend-normal-state">Resend Code</span>
                        <span class="resend-loading-state" style="display: none;">
                            <span id="sending-text">Sending</span>
                        </span>
                    </button>
                </form>
                <div class="timer mt-1">You can request a new code in <span id="countdown">30</span> seconds</div>
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
            const verifyBtn = document.getElementById('verifyBtn');
            const normalState = verifyBtn.querySelector('.normal-state');
            const loadingState = verifyBtn.querySelector('.loading-state');
            const messageContainer = document.getElementById('message-container');
            
            let sendingAnimation;
            const sendingText = document.getElementById('sending-text');
            
            function startSendingAnimation() {
                let dots = 0;
                sendingAnimation = setInterval(() => {
                    dots = (dots + 1) % 4;
                    let text = "Sending";
                    for (let i = 0; i < dots; i++) {
                        text += ".";
                    }
                    sendingText.textContent = text;
                }, 400);
            }
            
            function stopSendingAnimation() {
                if (sendingAnimation) {
                    clearInterval(sendingAnimation);
                    sendingAnimation = null;
                }
            }
            
            // Function to show messages
            function showMessage(type, message) {
                messageContainer.innerHTML = `
                    <div class="alert alert-${type}">
                        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle mr-2"></i> ${message}
                    </div>
                `;
            }
            
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
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!this.value && index !== 0) {
                            inputs[index -1].focus();
                            inputs[index -1].select();
                        }
                    }
                });
                
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
            
            verificationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let otp = '';
                inputs.forEach(input => {
                    otp += input.value;
                });
                
                otpValue.value = otp;
                
                if (otp.length === 6) {
                    normalState.style.display = 'none';
                    loadingState.style.display = 'inline-block';
                    verifyBtn.disabled = true;
                    
                    // AJAX for OTP verification
                    $.ajax({
                        url: 'functions/account/authcode.php',
                        type: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                showMessage('success', response.message);
                                setTimeout(function() {
                                    window.location.href = response.redirect;
                                }, 1500);
                            } else {
                                showMessage('danger', response.message);
                                // Reset the verification button
                                normalState.style.display = 'inline-block';
                                loadingState.style.display = 'none';
                                verifyBtn.disabled = false;
                                // Focus on first input and clear all inputs
                                inputs.forEach(input => input.value = '');
                                inputs[0].focus();
                            }
                        },
                        error: function() {
                            showMessage('danger', 'An error occurred. Please try again.');
                            normalState.style.display = 'inline-block';
                            loadingState.style.display = 'none';
                            verifyBtn.disabled = false;
                        }
                    });
                } else {
                    showMessage('danger', 'Please enter the complete 6-digit code');
                }
            });
            
            document.getElementById('resendForm').addEventListener('submit', function(e) {
                e.preventDefault();
                resendBtn.disabled = true;
                
                // Show loading state
                const resendNormalState = resendBtn.querySelector('.resend-normal-state');
                const resendLoadingState = resendBtn.querySelector('.resend-loading-state');
                resendNormalState.style.display = 'none';
                resendLoadingState.style.display = 'inline-block';
                
                // Start the sending animation
                startSendingAnimation();
                
                // AJAX for resending OTP
                $.ajax({
                    url: 'functions/account/authcode.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        // Stop the animation
                        stopSendingAnimation();
                        
                        if (response.status === 'success') {
                            showMessage('success', response.message);
                            // Reset countdown
                            startCountdown();
                        } else {
                            showMessage('danger', response.message);
                            resendBtn.disabled = false;
                        }
                        // Reset button state
                        resendNormalState.style.display = 'inline-block';
                        resendLoadingState.style.display = 'none';
                    },
                    error: function() {
                        // Stop the animation
                        stopSendingAnimation();
                        
                        showMessage('danger', 'Failed to resend code. Please try again.');
                        resendBtn.disabled = false;
                        // Reset button state
                        resendNormalState.style.display = 'inline-block';
                        resendLoadingState.style.display = 'none';
                    }
                });
            });
            
            // Countdown timer functionality
            let countdown = 30;
            const countdownDisplay = document.getElementById('countdown');
            resendBtn.disabled = true;
            
            function startCountdown() {
                countdown = 30;
                countdownDisplay.textContent = countdown;
                resendBtn.disabled = true;
                document.querySelector('.timer').style.display = 'block';
                
                const timer = setInterval(() => {
                    countdown--;
                    countdownDisplay.textContent = countdown;
                    
                    if (countdown <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        document.querySelector('.timer').style.display = 'none';
                    }
                }, 1000);
            }
            
            // Start countdown on page load
            startCountdown();
            
            // Check URL parameters for existing messages (for backward compatibility)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('invalidOTP')) {
                showMessage('danger', 'Invalid verification code. Please try again.');
            } else if (urlParams.has('resendSuccess')) {
                showMessage('success', 'A new verification code has been sent to your email.');
            } else if (urlParams.has('resendFailed')) {
                showMessage('danger', 'Failed to send verification code. Please try again.');
            }
            
            // Remove URL parameters after processing them
            if (urlParams.toString()) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>
</html>