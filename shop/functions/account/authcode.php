<?php 
session_start();
include '../../../admin/config/dbcon.php';
include 'otp_verification.php';

/* HELPER FUNCTIONS FOR LOGIN */
function getUserByIdentifier($conn, $identifier) {
    $safeIdentifier = mysqli_real_escape_string($conn, $identifier);
    $query = "SELECT * FROM users WHERE email='$safeIdentifier' OR username='$safeIdentifier' LIMIT 1";
    $result = mysqli_query($conn, $query);
    return (mysqli_num_rows($result) > 0) ? $result : false;
}

function displayInvalidCredentials() {
   header("Location: ../index.php?loginFailed=1");
   exit;
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function sendJsonResponse($status, $message, $data = []) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    
    if (!empty($data)) {
        $response = array_merge($response, $data);
    }
    
    // Include username in response if it's in the session
    if (isset($_SESSION['username'])) {
        $response['username'] = $_SESSION['username'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// New function to redirect without URL parameters
function redirectWithSessionFlag($location, $flagName, $flagValue = true) {
    $_SESSION[$flagName] = $flagValue;
    header("Location: $location");
    exit;
}

/* PROCESS REQUEST */
if (isset($_POST['signupButton'])) {
   $firstname       = mysqli_real_escape_string($conn, $_POST['firstname']);
   $middlename      = isset($_POST['middlename']) ? mysqli_real_escape_string($conn, $_POST['middlename']) : '';
   $lastname        = mysqli_real_escape_string($conn, $_POST['lastname']);
   $phone_number    = mysqli_real_escape_string($conn, $_POST['phone_number']);
   $regemail        = mysqli_real_escape_string($conn, $_POST['regemail']);
   $username        = mysqli_real_escape_string($conn, $_POST['username']);
   $full_address    = mysqli_real_escape_string($conn, $_POST['full_address']);
   $zipcode         = mysqli_real_escape_string($conn, $_POST['zipcode']);
   $password        = mysqli_real_escape_string($conn, $_POST['password']);
   $confirm_password= mysqli_real_escape_string($conn, $_POST['confirm_password']);
   $latitude        = mysqli_real_escape_string($conn, $_POST['latitude']);
   $longitude       = mysqli_real_escape_string($conn, $_POST['longitude']);

   if ($password !== $confirm_password) {
       if (isAjaxRequest()) {
           sendJsonResponse('error', 'Passwords do not match.');
       } else {
           $_SESSION['error_message'] = "Passwords do not match.";
           redirectWithSessionFlag("../index.php", "signup_error");
       }
   }
   
   // Check if email already exists
   $check_email_query = "SELECT email FROM users WHERE email='$regemail'";
   $check_email_query_run = mysqli_query($conn, $check_email_query);
   
   if(mysqli_num_rows($check_email_query_run) > 0) {
       if (isAjaxRequest()) {
           sendJsonResponse('error', 'Email already exists. Please use a different email or login.');
       } else {
           $_SESSION['error_message'] = "Email already exists. Please use a different email or login.";
           redirectWithSessionFlag("../index.php", "email_exists");
       }
   }
   
   $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
   
   // Generate OTP
   $otp = generateOTP();
   
   // First, store user data in session for later use after verification
   $_SESSION['temp_user_data'] = [
       'firstname' => $firstname,
       'middlename' => $middlename,
       'lastname' => $lastname,
       'phone_number' => $phone_number,
       'email' => $regemail,
       'username' => $username,
       'full_address' => $full_address,
       'zipcode' => $zipcode,
       'password' => $hashedPassword,
       'latitude' => $latitude,
       'longitude' => $longitude
   ];
   
   // Store OTP in database (create a temporary entry)
   if(storeOTP($conn, $regemail, $otp)) {
       // Send OTP email
       if(sendOTPEmail($regemail, $otp, $firstname)) {
           // Store email in session for verification page
           $_SESSION['verify_email'] = $regemail;
           $_SESSION['verify_firstname'] = $firstname;
           
           // Redirect to verification page or respond with JSON for AJAX
           if (isAjaxRequest()) {
               sendJsonResponse('success', 'Please check your email for verification code.', ['redirect' => '/shop/verify.php']);
           } else {
               redirectWithSessionFlag('/shop/verify.php', 'verify_needed');
           }
       } else {
           // Failed to send email
           if (isAjaxRequest()) {
               sendJsonResponse('error', 'Failed to send verification email. Please try again later.');
           } else {
               $_SESSION['error_message'] = "Failed to send verification email. Please try again later.";
               redirectWithSessionFlag("../index.php", "email_failed");
           }
       }
   } else {
       // Failed to store OTP
       if (isAjaxRequest()) {
           sendJsonResponse('error', 'An error occurred during registration. Please try again later.');
       } else {
           $_SESSION['error_message'] = "An error occurred during registration. Please try again later.";
           redirectWithSessionFlag("../index.php", "otp_failed");
       }
   }
}
else if (isset($_POST['loginButton'])) {
    // Login process
    $loginidentifier = mysqli_real_escape_string($conn, $_POST['loginidentifier']);
    $loginpassword = mysqli_real_escape_string($conn, $_POST['loginpassword']);
    
    $result = getUserByIdentifier($conn, $loginidentifier);
    if (!$result) {
        if (isAjaxRequest()) {
            sendJsonResponse('error', 'Invalid username or email address.');
        } else {
            $_SESSION['error_message'] = "Invalid username or email address.";
            redirectWithSessionFlag("../index.php", "login_failed");
        }
    }
    
    $user = mysqli_fetch_assoc($result);
    
    // Check if email is verified
    if(isset($user['email_verified']) && $user['email_verified'] == 0) {
        // Email not verified, generate new OTP
        $otp = generateOTP();
        
        if(storeOTP($conn, $user['email'], $otp) && sendOTPEmail($user['email'], $otp, $user['firstname'])) {
            $_SESSION['verify_email'] = $user['email'];
            $_SESSION['verify_firstname'] = $user['firstname'];
            
            if (isAjaxRequest()) {
                sendJsonResponse('error', 'Please verify your email address first. A new verification code has been sent to your email.');
            } else {
                redirectWithSessionFlag("/shop/verify.php", "verify_required");
            }
        } else {
            if (isAjaxRequest()) {
                sendJsonResponse('error', 'Failed to send verification email. Please try again later.');
            } else {
                $_SESSION['error_message'] = "Failed to send verification email. Please try again later.";
                redirectWithSessionFlag("../index.php", "email_failed");
            }
        }
    }
    
    if (!password_verify($loginpassword, $user['password'])) {
        if (isAjaxRequest()) {
            sendJsonResponse('error', 'Invalid password. Please try again.');
        } else {
            $_SESSION['error_message'] = "Invalid password. Please try again.";
            redirectWithSessionFlag("../index.php", "login_failed");
        }
    }
    
    // Set session variables for authentication
    $_SESSION['auth'] = true;
    $_SESSION['auth_role'] = $user['role_as'];
    $_SESSION['auth_user'] = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email']
    ];
    $_SESSION['username'] = $user['username'];
    
    if($user['role_as'] == 1) {
        // For admin users, set specific admin session variables
        $_SESSION['admin_auth'] = true;
        $_SESSION['admin_login_success'] = true; // Flag for showing admin login modal
        
        if (isAjaxRequest()) {
            sendJsonResponse('success', 'Login successful. Redirecting to admin area.', ['role' => 1, 'username' => $user['username']]);
        } else {
            redirectWithSessionFlag("../index.php", "admin_login_success");
        }
    }
    else {
        // Regular user - redirect to shop homepage
        if (isAjaxRequest()) {
            sendJsonResponse('success', 'Login successful.', ['role' => 0, 'username' => $user['username']]);
        } else {
            redirectWithSessionFlag("../index.php", "login_success");
        }
    }
}

// Handle OTP verification
if(isset($_POST['verify_otp'])) {
    $email = $_SESSION['verify_email'] ?? '';
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);
    
    // Debug: Check what values we're working with
    error_log("Verifying OTP: Email=$email, OTP=$otp");
    
    // Use the validateOTP function instead of direct database query
    if(validateOTP($conn, $email, $otp)) {
        // OTP is valid
        
        // If we have temporary user data, create the account now
        if(isset($_SESSION['temp_user_data'])) {
            $userData = $_SESSION['temp_user_data'];
            
            $query = "INSERT INTO users 
                     (firstname, middlename, lastname, phone_number, email, username, full_address, zipcode, password, latitude, longitude, email_verified, created_at) 
                     VALUES 
                     ('{$userData['firstname']}', '{$userData['middlename']}', '{$userData['lastname']}', 
                      '{$userData['phone_number']}', '{$userData['email']}', '{$userData['username']}', 
                      '{$userData['full_address']}', '{$userData['zipcode']}', '{$userData['password']}', 
                      '{$userData['latitude']}', '{$userData['longitude']}', 1, NOW())";
            
            if(mysqli_query($conn, $query)) {
                // Get the new user's ID
                $new_user_id = mysqli_insert_id($conn);
                
                // Clear temporary user data
                unset($_SESSION['temp_user_data']);
                
                // Clear verification session variables
                unset($_SESSION['verify_email']);
                unset($_SESSION['verify_firstname']);
                
                // Auto login the user
                $_SESSION['auth'] = true;
                $_SESSION['auth_role'] = 0; // Regular user
                $_SESSION['auth_user'] = [
                    'user_id' => $new_user_id,
                    'username' => $userData['username'],
                    'email' => $userData['email']
                ];
                $_SESSION['username'] = $userData['username'];
                
                // Set success message for modal
                $_SESSION['registration_success'] = true;
                
                if(isAjaxRequest()) {
                    sendJsonResponse('success', 'Your account has been created successfully!', ['username' => $userData['username']]);
                } else {
                    redirectWithSessionFlag("../index.php", "registration_success");
                }
            } else {
                $_SESSION['message'] = "Error creating account: " . mysqli_error($conn);
                
                if(isAjaxRequest()) {
                    sendJsonResponse('error', 'Error creating account: ' . mysqli_error($conn));
                } else {
                    $_SESSION['error_message'] = "Error creating account: " . mysqli_error($conn);
                    redirectWithSessionFlag("../index.php", "account_creation_failed");
                }
            }
        } else {
            // Just email verification for existing account
            // Update the user's verification status
            mysqli_query($conn, "UPDATE users SET email_verified = 1 WHERE email = '$email'");
            
            // Clear verification session variables
            unset($_SESSION['verify_email']);
            unset($_SESSION['verify_firstname']);
            
            $_SESSION['message'] = "Email verified successfully! You can now login.";
            
            if(isAjaxRequest()) {
                sendJsonResponse('success', 'Email verified successfully! You can now login.');
            } else {
                redirectWithSessionFlag("../index.php", "verification_success");
            }
        }
    } else {
        // Debug: Log the failed verification attempt
        error_log("OTP verification failed: Invalid or expired OTP");
        
        if(isAjaxRequest()) {
            sendJsonResponse('error', 'Invalid or expired verification code. Please try again.');
        } else {
            $_SESSION['error_message'] = "Invalid or expired verification code. Please try again.";
            redirectWithSessionFlag("/shop/verify.php", "invalid_otp");
        }
    }
}

// Handle OTP resend
if(isset($_POST['resend_otp'])) {
    $email = $_SESSION['verify_email'] ?? '';
    $firstname = $_SESSION['verify_firstname'] ?? '';
    
    if(!empty($email)) {
        $otp = generateOTP();
        if(storeOTP($conn, $email, $otp) && sendOTPEmail($email, $otp, $firstname)) {
            if(isAjaxRequest()) {
                sendJsonResponse('success', 'A new verification code has been sent to your email.');
            } else {
                redirectWithSessionFlag("../verify.php", "resend_success");
            }
        } else {
            if(isAjaxRequest()) {
                sendJsonResponse('error', 'Failed to send verification code. Please try again.');
            } else {
                $_SESSION['error_message'] = "Failed to send verification code. Please try again.";
                redirectWithSessionFlag("../verify.php", "resend_failed");
            }
        }
    } else {
        if(isAjaxRequest()) {
            sendJsonResponse('error', 'Email address not found. Please try again.');
        } else {
            $_SESSION['error_message'] = "Email address not found. Please try again.";
            redirectWithSessionFlag("../index.php", "email_not_found");
        }
    }
}
?>

<script src="../js/url-cleaner.js"></script>