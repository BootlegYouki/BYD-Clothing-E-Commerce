<?php 
session_start();
include '../../admin/config/dbcon.php';

/* HELPER FUNCTIONS FOR LOGIN */
function isEmailRegistered($conn, $email) {
    $safeEmail = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM users WHERE email='$safeEmail' LIMIT 1";
    $result = mysqli_query($conn, $query);
    return (mysqli_num_rows($result) > 0) ? $result : false;
}

function displayInvalidCredentials() {
   header("Location: ../index.php?loginFailed=1");
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
    $loginemail    = mysqli_real_escape_string($conn, $_POST['loginemail']);
    $loginpassword = mysqli_real_escape_string($conn, $_POST['loginpassword']);
    
    $result = isEmailRegistered($conn, $loginemail);
    if (!$result) {
        displayInvalidCredentials();
    }
    
    $user = mysqli_fetch_assoc($result);
    if (!password_verify($loginpassword, $user['password'])) {
        displayInvalidCredentials();
    }
    
    $_SESSION['username'] = $user['username'];
    header("Location: ../index.php?loginSuccess=1");
    exit;
}
?>