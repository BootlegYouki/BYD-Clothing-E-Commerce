<?php 
session_start();
include '../../admin/config/dbcon.php';

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
   
   $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
   
   $query = "INSERT INTO users 
             (firstname, middlename, lastname, phone_number, email, username, full_address, zipcode, password, created_at) 
             VALUES 
             ('$firstname', '$middlename', '$lastname', '$phone_number', '$regemail', '$username', '$full_address', '$zipcode', '$hashedPassword', NOW())";
   
   if (mysqli_query($conn, $query)) {
     // Get the new user's ID
     $new_user_id = mysqli_insert_id($conn);
     
     // Set all auth session variables, matching the login process
     $_SESSION['auth'] = true;
     $_SESSION['reload_cart'] = true;
     $_SESSION['auth_role'] = 0; // Default role for new users
     $_SESSION['auth_user'] = [
         'user_id' => $new_user_id,
         'username' => $username,
         'email' => $regemail
     ];
     $_SESSION['username'] = $username;
     
     header("Location: ../index.php?signupSuccess=1");
     exit;
   } else {
     echo "Error: " . mysqli_error($conn);
     exit;
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
    if (!password_verify($loginpassword, $user['password'])) {
        displayInvalidCredentials();
    }
    
    $_SESSION['auth'] = true;
    $_SESSION['reload_cart'] = true;
    $_SESSION['auth_role'] = $user['role_as'];
    $_SESSION['auth_user'] = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email']
    ];
    
    if($user['role_as'] == 1) {
        $_SESSION['username'] = $user['username'];
        header("Location: ../index?loginSuccess=1");
    }
    else {
        $_SESSION['username'] = $user['username'];
        header("Location: ../index?loginSuccess=1");
    }
    exit;
}
?>

<script src="../js/url-cleaner.js"></script>