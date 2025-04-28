<?php
require_once '../../../admin/config/dbcon.php';
session_start();

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['auth_user']['user_id'];
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if username is already taken by another user
    $check_username = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt = $conn->prepare($check_username);
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['notification'] = [
            'message' => "Username already taken",
            'type' => "danger"
        ];
        header('Location: ../../profile.php');
        exit();
    }
    
    // Check if email is already registered by another user
    $check_email = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['notification'] = [
            'message' => "Email already registered",
            'type' => "danger"
        ];
        header('Location: ../../profile.php');
        exit();
    }
    
    // Check if phone is already registered by another user
    $check_phone = "SELECT id FROM users WHERE phone_number = ? AND id != ?";
    $stmt = $conn->prepare($check_phone);
    $stmt->bind_param("si", $phone_number, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['notification'] = [
            'message' => "Phone number already registered",
            'type' => "danger"
        ];
        header('Location: ../../profile.php');
        exit();
    }
    
    // Update user profile
    $query = "UPDATE users SET 
              firstname = ?, 
              middlename = ?, 
              lastname = ?, 
              username = ?, 
              phone_number = ?, 
              email = ? 
              WHERE id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $firstname, $middlename, $lastname, $username, $phone_number, $email, $user_id);
    
    if ($stmt->execute()) {
        // Update session data
        $_SESSION['auth_user']['firstname'] = $firstname;
        $_SESSION['auth_user']['lastname'] = $lastname;
        $_SESSION['auth_user']['username'] = $username;
        
        $_SESSION['notification'] = [
            'message' => "Profile updated successfully!",
            'type' => "success"
        ];
    } else {
        $_SESSION['notification'] = [
            'message' => "Failed to update profile: " . $conn->error,
            'type' => "danger"
        ];
    }
    
    header('Location: ../../profile.php');
    exit();
}