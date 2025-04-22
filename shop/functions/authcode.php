<?php 
session_start();
include '../../admin/config/dbcon.php';
include 'otp_verification.php';

/* HELPER FUNCTIONS FOR LOGIN */
function getUserByIdentifier($conn, $identifier) {
    $safeIdentifier = mysqli_real_escape_string($conn, $identifier);
    $query = "SELECT * FROM users WHERE email='$safeIdentifier' OR username='$safeIdentifier' LIMIT 1";
    $result = mysqli_query($conn, $query);
    return (mysqli_num_rows($result) > 0) ? $result : false;
}

function displayInvalidCredentials() {
   header("Location: ../index?loginFailed=1");
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

   if ($password !== $confirm_password) {
       echo "Passwords do not match.";
       exit;
   }
   
   // Check if email already exists
   $check_email_query = "SELECT email FROM users WHERE email='$regemail'";
   $check_email_query_run = mysqli_query($conn, $check_email_query);
   
   if(mysqli_num_rows($check_email_query_run) > 0) {
       header("Location: ../index.php?emailExists=1");
       exit;
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
       'password' => $hashedPassword
   ];
   
   // Store OTP in database (create a temporary entry)
   if(storeOTP($conn, $regemail, $otp)) {
       // Send OTP email
       if(sendOTPEmail($regemail, $otp, $firstname)) {
           // Store email in session for verification page
           $_SESSION['verify_email'] = $regemail;
           $_SESSION['verify_firstname'] = $firstname;
           
           // Redirect to verification page
           header('Location: ../verify.php');
           exit();
       } else {
           // Failed to send email
           header("Location: ../index.php?emailFailed=1");
           exit();
       }
   } else {
       // Failed to store OTP
       header("Location: ../index.php?otpFailed=1");
       exit();
   }
}
else if (isset($_POST['loginButton'])) {
    // Login process
    $loginidentifier = mysqli_real_escape_string($conn, $_POST['loginidentifier']);
    $loginpassword = mysqli_real_escape_string($conn, $_POST['loginpassword']);
    
    $result = getUserByIdentifier($conn, $loginidentifier);
    if (!$result) {
        displayInvalidCredentials();
    }
    
    $user = mysqli_fetch_assoc($result);
    
    // Check if email is verified
    if(isset($user['email_verified']) && $user['email_verified'] == 0) {
        // Email not verified, generate new OTP
        $otp = generateOTP();
        
        if(storeOTP($conn, $user['email'], $otp) && sendOTPEmail($user['email'], $otp, $user['firstname'])) {
            $_SESSION['verify_email'] = $user['email'];
            $_SESSION['verify_firstname'] = $user['firstname'];
            
            header("Location: ../verify.php?verifyRequired=1");
            exit();
        } else {
            header("Location: ../index.php?emailFailed=1");
            exit();
        }
    }
    
    if (!password_verify($loginpassword, $user['password'])) {
        displayInvalidCredentials();
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
        
        // Redirect to index page to show the modal first
        header("Location: ../index.php?adminLogin=1");
        exit();
    }
    else {
        // Regular user - redirect to shop homepage
        header("Location: ../index.php?loginSuccess=1");
        exit();
    }
}

// Handle OTP verification
if(isset($_POST['verify_otp'])) {
    $email = $_SESSION['verify_email'] ?? '';
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);
    
    // Debug: Check what values we're working with
    error_log("Verifying OTP: Email=$email, OTP=$otp");
    
    // Check directly in the database for this OTP
    $check_query = "SELECT * FROM otp_verification WHERE email = '$email' AND otp = '$otp'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        // OTP exists in database, manually update user verification status
        
        // If we have temporary user data, create the account now
        if(isset($_SESSION['temp_user_data'])) {
            $userData = $_SESSION['temp_user_data'];
            
            $query = "INSERT INTO users 
                     (firstname, middlename, lastname, phone_number, email, username, full_address, zipcode, password, email_verified, created_at) 
                     VALUES 
                     ('{$userData['firstname']}', '{$userData['middlename']}', '{$userData['lastname']}', 
                      '{$userData['phone_number']}', '{$userData['email']}', '{$userData['username']}', 
                      '{$userData['full_address']}', '{$userData['zipcode']}', '{$userData['password']}', 
                      1, NOW())";
            
            if(mysqli_query($conn, $query)) {
                // Get the new user's ID
                $new_user_id = mysqli_insert_id($conn);
                
                // Delete the used OTP
                mysqli_query($conn, "DELETE FROM otp_verification WHERE email = '$email'");
                
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
                
                header("Location: ../index.php?registrationSuccess=1");
                exit();
            } else {
                $_SESSION['message'] = "Error creating account: " . mysqli_error($conn);
                header("Location: ../index.php?accountCreationFailed=1");
                exit();
            }
        } else {
            // Just email verification for existing account
            // Update the user's verification status
            mysqli_query($conn, "UPDATE users SET email_verified = 1 WHERE email = '$email'");
            
            // Delete the used OTP
            mysqli_query($conn, "DELETE FROM otp_verification WHERE email = '$email'");
            
            // Clear verification session variables
            unset($_SESSION['verify_email']);
            unset($_SESSION['verify_firstname']);
            
            $_SESSION['message'] = "Email verified successfully! You can now login.";
            header("Location: ../index.php?verificationSuccess=1");
            exit();
        }
    } else {
        // Debug: Log the failed verification attempt
        error_log("OTP verification failed: No matching OTP found in database");
        header("Location: ../verify.php?invalidOTP=1");
        exit();
    }
}

// Handle OTP resend
if(isset($_POST['resend_otp'])) {
    $email = $_SESSION['verify_email'] ?? '';
    $firstname = $_SESSION['verify_firstname'] ?? '';
    
    if(!empty($email)) {
        $otp = generateOTP();
        if(storeOTP($conn, $email, $otp) && sendOTPEmail($email, $otp, $firstname)) {
            header("Location: ../verify.php?resendSuccess=1");
            exit();
        } else {
            header("Location: ../verify.php?resendFailed=1");
            exit();
        }
    } else {
        header("Location: ../index.php");
        exit();
    }
}
?>

<script src="../js/url-cleaner.js"></script>