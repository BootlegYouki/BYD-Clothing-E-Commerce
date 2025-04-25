<?php
session_start();
include '../admin/config/dbcon.php';

// Check if token and email are provided
if (!isset($_GET['token']) || !isset($_GET['email'])) {
    $_SESSION['reset_error'] = "Invalid reset link. Please request a new password reset.";
    header("Location: index.php");
    exit();
}

$token = $_GET['token'];
$email = $_GET['email'];

// Basic validation
if (empty($token) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reset_error'] = "Invalid reset link parameters. Please request a new password reset.";
    header("Location: index.php");
    exit();
}

// Check if the token exists and is valid in the database
$query = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expiry_time > NOW()";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();

$tokenValid = ($result->num_rows > 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - BYD Clothing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
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
            min-height: 100vh;
        }
        
        .reset-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 30px 25px;
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
            text-align: center;
        }
        
        .info-text {
            color: #666;
            font-size: clamp(14px, 3vw, 15px);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-group label {
            font-weight: 500;
            color: var(--text-color);
            font-size: 15px;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 127, 80, 0.2);
            outline: none;
        }
        
        .password-field-container {
            position: relative;
        }
        
        .password-toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }
        
        .btn-reset {
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
        
        .btn-reset:hover {
            background-color: #ff6347;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 127, 80, 0.3);
        }
        
        .btn-reset:disabled {
            background-color: #cccccc;
            transform: none;
            box-shadow: none;
            cursor: not-allowed;
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
        
        .strength-meter {
            margin-top: 8px;
            height: 6px;
            border-radius: 3px;
            background-color: #eee;
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: width 0.5s, background-color 0.5s;
            border-radius: 3px;
        }
        
        .password-feedback {
            color: #777;
            font-size: 13px;
            margin-top: 5px;
        }
        
        .password-hint {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }
        
        .password-hint ul {
            padding-left: 20px;
            margin-bottom: 0;
        }
        
        .password-hint li {
            margin-bottom: 3px;
        }
        
        .back-to-login {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .back-to-login:hover {
            color: #ff6347;
            text-decoration: none;
        }

        .expired-token {
            text-align: center;
            padding: 30px 20px;
        }

        .expired-token .icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .expired-token h4 {
            color: #dc3545;
            margin-bottom: 15px;
        }

        .expired-token p {
            color: #666;
            margin-bottom: 25px;
        }

        .expired-token .btn {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="logo">
                <img src="img/logo/logo_admin_light.png" alt="BYD Clothing Logo">
                <h2>BYD Clothing</h2>
            </div>
            
            <div id="message-container"></div>
            
            <?php if (!$tokenValid): ?>
            <div class="expired-token">
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h4>Link Expired</h4>
                <p>The password reset link has expired or is invalid. Please request a new password reset link.</p>
                <a href="index.php" class="btn btn-primary">Back to Login</a>
            </div>
            <?php else: ?>
            <h4>Reset Your Password</h4>
            <p class="info-text">Enter a new password for your account.</p>
            
            <form id="resetPasswordForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="action" value="reset_password">
                
                <div class="form-group mb-3">
                    <label for="password">New Password</label>
                    <div class="password-field-container">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button type="button" class="password-toggle-btn" tabindex="-1">
                            <i class="fa-regular fa-eye-slash" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="strength-meter mt-2">
                        <div class="strength-meter-fill"></div>
                    </div>
                    <div class="password-feedback"></div>
                    <div class="password-hint">
                        <small>Your password should:</small>
                        <ul>
                            <li>Be at least 8 characters long</li>
                            <li>Include at least one uppercase letter</li>
                            <li>Include at least one number</li>
                            <li>Include at least one special character</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-field-container">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="password-toggle-btn" tabindex="-1">
                            <i class="fa-regular fa-eye-slash" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="password-match-feedback"></div>
                </div>
                
                <button type="submit" id="resetBtn" class="btn btn-reset" disabled>
                    <span class="normal-state">Reset Password</span>
                    <span class="loading-state" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Resetting...
                    </span>
                </button>
            </form>
            <?php endif; ?>
            
            <a href="index.php" class="back-to-login">Back to Login</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!$tokenValid): ?>
                return; // Don't run script for invalid tokens
            <?php endif; ?>

            const resetForm = document.getElementById('resetPasswordForm');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');
            const resetBtn = document.getElementById('resetBtn');
            const messageContainer = document.getElementById('message-container');
            const strengthMeter = document.querySelector('.strength-meter-fill');
            const passwordFeedback = document.querySelector('.password-feedback');
            const passwordMatchFeedback = document.getElementById('password-match-feedback');
            
            // Function to show messages
            function showMessage(type, message) {
                messageContainer.innerHTML = `
                    <div class="alert alert-${type}">
                        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i> ${message}
                    </div>
                `;
            }
            
            // Password visibility toggle
            const toggleBtns = document.querySelectorAll('.password-toggle-btn');
            toggleBtns.forEach((btn, index) => {
                btn.addEventListener('click', function() {
                    const input = index === 0 ? passwordInput : confirmInput;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    // Toggle the eye icon
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye-slash');
                    icon.classList.toggle('fa-eye');
                });
            });
            
            // Check password strength
            function checkPasswordStrength(password) {
                let strength = 0;
                let feedback = '';
                
                // Length check
                if (password.length >= 8) {
                    strength += 25;
                }
                
                // Uppercase check
                if (/[A-Z]/.test(password)) {
                    strength += 25;
                }
                
                // Numbers check
                if (/[0-9]/.test(password)) {
                    strength += 25;
                }
                
                // Special character check
                if (/[^A-Za-z0-9]/.test(password)) {
                    strength += 25;
                }
                
                // Update strength meter
                strengthMeter.style.width = strength + '%';
                
                // Set color based on strength
                if (strength === 0) {
                    strengthMeter.style.backgroundColor = '#eee';
                    feedback = '';
                } else if (strength <= 25) {
                    strengthMeter.style.backgroundColor = '#ff4d4d';
                    feedback = 'Weak password';
                } else if (strength <= 50) {
                    strengthMeter.style.backgroundColor = '#ffa64d';
                    feedback = 'Fair password';
                } else if (strength <= 75) {
                    strengthMeter.style.backgroundColor = '#99cc33';
                    feedback = 'Good password';
                } else {
                    strengthMeter.style.backgroundColor = '#4CAF50';
                    feedback = 'Strong password';
                }
                
                passwordFeedback.textContent = feedback;
                return strength;
            }
            
            // Check if passwords match
            function checkPasswordsMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmInput.value;
                
                if (confirmPassword) {
                    if (password !== confirmPassword) {
                        passwordMatchFeedback.textContent = 'Passwords do not match';
                        passwordMatchFeedback.style.display = 'block';
                        return false;
                    } else {
                        passwordMatchFeedback.style.display = 'none';
                        return true;
                    }
                }
                return false;
            }
            
            // Check if the form is valid
            function checkFormValidity() {
                const password = passwordInput.value;
                const passwordStrength = checkPasswordStrength(password);
                const passwordsMatch = checkPasswordsMatch();
                
                // Enable button if password is strong enough and passwords match
                resetBtn.disabled = !(passwordStrength >= 75 && passwordsMatch);
            }
            
            // Event listeners
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkFormValidity();
            });
            
            confirmInput.addEventListener('input', function() {
                checkPasswordsMatch();
                checkFormValidity();
            });
            
            // Handle form submission
            resetForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                resetBtn.querySelector('.normal-state').style.display = 'none';
                resetBtn.querySelector('.loading-state').style.display = 'inline-block';
                resetBtn.disabled = true;
                
                // Get form data
                const formData = new FormData(resetForm);
                const serialized = new URLSearchParams(formData).toString();
                
                // Send AJAX request
                fetch('functions/password_reset.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: serialized
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button state
                    resetBtn.querySelector('.normal-state').style.display = 'inline-block';
                    resetBtn.querySelector('.loading-state').style.display = 'none';
                    
                    if (data.status === 'success') {
                        // Success message
                        showMessage('success', data.message);
                        resetForm.style.display = 'none';
                        
                        // Redirect after a short delay
                        setTimeout(function() {
                            window.location.href = data.redirect || 'index.php?resetSuccess=1';
                        }, 3000);
                    } else {
                        // Error message
                        showMessage('danger', data.message || 'An error occurred. Please try again.');
                        resetBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Reset button state
                    resetBtn.querySelector('.normal-state').style.display = 'inline-block';
                    resetBtn.querySelector('.loading-state').style.display = 'none';
                    resetBtn.disabled = false;
                    
                    showMessage('danger', 'An error occurred. Please try again later.');
                });
            });
        });
    </script>
</body>
</html>
