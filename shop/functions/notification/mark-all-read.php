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
    'message' => ''
);

// Check if user is logged in
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    $response['message'] = 'User not authenticated';
    echo json_encode($response);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['auth_user']['user_id'] ?? 0;

try {
    // Update all user's notifications to mark as read
    $update_query = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        $response['success'] = true;
        $response['message'] = $affected_rows > 0 ? 
            'All notifications marked as read' : 
            'No unread notifications found';
    } else {
        $response['message'] = 'Error marking notifications as read';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;
?>
