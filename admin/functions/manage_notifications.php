<?php
session_start();
require_once '../config/dbcon.php';

// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../index.php");
    exit();
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            createNotification($conn);
            break;
            
        case 'mass_create':
            createMassNotification($conn);
            break;
            
        case 'delete':
            deleteNotification($conn);
            break;
            
        default:
            setMessage('Invalid action specified', 'error');
            header('Location: ../notifications.php');
            exit();
    }
}

/**
 * Create a notification for a specific user or all users
 */
function createNotification($conn) {
    // Get form data
    $type = mysqli_real_escape_string($conn, $_POST['type'] ?? '');
    $title = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
    $message = mysqli_real_escape_string($conn, $_POST['message'] ?? '');
    $recipient_type = $_POST['recipient_type'] ?? 'all';
    
    // Validate required fields
    if (empty($type) || empty($title) || empty($message)) {
        setMessage('All fields are required', 'error');
        header('Location: ../notifications.php');
        exit();
    }
    
    if ($recipient_type === 'specific') {
        // Send to specific user
        $user_id = $_POST['user_id'] ?? '';
        
        if (empty($user_id)) {
            setMessage('Please select a user', 'error');
            header('Location: ../notifications.php');
            exit();
        }
        
        // Create notification for specific user
        $query = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $type, $title, $message);
        
        if ($stmt->execute()) {
            setMessage('Notification created successfully', 'success');
        } else {
            setMessage('Error creating notification: ' . $stmt->error, 'error');
        }
        
        $stmt->close();
    } else {
        // Send to all users
        
        // Get all users excluding admins
        $users_query = "SELECT id FROM users WHERE role_as = 0";
        $users_result = mysqli_query($conn, $users_query);
        
        if (!$users_result) {
            setMessage('Error fetching users: ' . mysqli_error($conn), 'error');
            header('Location: ../notifications.php');
            exit();
        }
        
        $success_count = 0;
        $error_count = 0;
        
        // Prepare the insertion statement outside the loop
        $query = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $type, $title, $message);
        
        // Create notification for each user
        while ($user = mysqli_fetch_assoc($users_result)) {
            $user_id = $user['id'];
            
            if ($stmt->execute()) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
        
        $stmt->close();
        
        if ($error_count === 0) {
            setMessage("Notification sent successfully to {$success_count} users", 'success');
        } else {
            setMessage("Notification sent to {$success_count} users with {$error_count} errors", 'error');
        }
    }
    
    header('Location: ../notifications.php');
    exit();
}

/**
 * Create a notification for all users (mass notification)
 */
function createMassNotification($conn) {
    // Get form data
    $type = mysqli_real_escape_string($conn, $_POST['type'] ?? '');
    $title = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
    $message = mysqli_real_escape_string($conn, $_POST['message'] ?? '');
    
    // Validate required fields
    if (empty($type) || empty($title) || empty($message)) {
        setMessage('All fields are required', 'error');
        header('Location: ../notifications.php');
        exit();
    }
    
    // Get all users excluding admins
    $users_query = "SELECT id FROM users WHERE role_as = 0";
    $users_result = mysqli_query($conn, $users_query);
    
    if (!$users_result) {
        setMessage('Error fetching users: ' . mysqli_error($conn), 'error');
        header('Location: ../notifications.php');
        exit();
    }
    
    $success_count = 0;
    $error_count = 0;
    
    // Prepare the insertion statement outside the loop
    $query = "INSERT INTO notifications (user_id, type, title, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $type, $title, $message);
    
    // Create notification for each user
    while ($user = mysqli_fetch_assoc($users_result)) {
        $user_id = $user['id'];
        
        if ($stmt->execute()) {
            $success_count++;
        } else {
            $error_count++;
        }
    }
    
    $stmt->close();
    
    if ($error_count === 0) {
        setMessage("Mass notification sent successfully to {$success_count} users", 'success');
    } else {
        setMessage("Notification sent to {$success_count} users with {$error_count} errors", 'error');
    }
    
    header('Location: ../notifications.php');
    exit();
}

/**
 * Delete a notification
 */
function deleteNotification($conn) {
    $notification_id = $_POST['notification_id'] ?? '';
    
    if (empty($notification_id)) {
        setMessage('Invalid notification ID', 'error');
        header('Location: ../notifications.php');
        exit();
    }
    
    $query = "DELETE FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notification_id);
    
    if ($stmt->execute()) {
        setMessage('Notification deleted successfully', 'success');
    } else {
        setMessage('Error deleting notification: ' . $stmt->error, 'error');
    }
    
    $stmt->close();
    header('Location: ../notifications.php');
    exit();
}

/**
 * Set a message to be displayed after redirect
 */
function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}
?>
