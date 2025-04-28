<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../../../admin/config/dbcon.php';

// JSON response array
$response = array(
    'success' => false,
    'message' => '',
    'unread_count' => 0
);

// Check if user is logged in
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    $response['message'] = 'User not authenticated';
    echo json_encode($response);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['auth_user']['user_id'] ?? 0;

// Check if notification ID is provided
if (!isset($_POST['notification_id']) || empty($_POST['notification_id'])) {
    $response['message'] = 'Notification ID is required';
    echo json_encode($response);
    exit;
}

try {
    $notification_id = $_POST['notification_id'];
    
    // Verify the notification belongs to this user
    $verify_query = "SELECT id FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Invalid notification ID';
        echo json_encode($response);
        exit;
    }
    
    // Update notification to mark as read
    $update_query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ii", $notification_id, $user_id);
    
    if ($stmt->execute()) {
        // Count remaining unread notifications
        $count_query = "SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $conn->prepare($count_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $response['success'] = true;
        $response['message'] = 'Notification marked as read';
        $response['unread_count'] = $row['unread'];
    } else {
        $response['message'] = 'Error marking notification as read';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;
?>
