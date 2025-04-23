<?php
require_once '../../admin/config/dbcon.php';
session_start();

if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['auth_user']['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['notification'] = [
            'message' => "New passwords do not match",
            'type' => "danger"
        ];
        header('Location: ../profile.php');
        exit();
    }
    
    // Verify password length
    if (strlen($new_password) < 8) {
        $_SESSION['notification'] = [
            'message' => "Password must be at least 8 characters long",
            'type' => "danger"
        ];
        header('Location: ../profile.php');
        exit();
    }
    
    // Get current password from database
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Verify current password
        if (password_verify($current_password, $row['password'])) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['notification'] = [
                    'message' => "Password updated successfully!",
                    'type' => "success"
                ];
            } else {
                $_SESSION['notification'] = [
                    'message' => "Failed to update password: " . $conn->error,
                    'type' => "danger"
                ];
            }
        } else {
            $_SESSION['notification'] = [
                'message' => "Current password is incorrect",
                'type' => "danger"
            ];
        }
    } else {
        $_SESSION['notification'] = [
            'message' => "User not found",
            'type' => "danger"
        ];
    }
    
    header('Location: ../profile.php');
    exit();
}