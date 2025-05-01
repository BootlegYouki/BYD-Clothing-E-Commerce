<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['auth_user']['user_id'] ?? 0;

// Check if notification ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Notification ID required']);
    exit;
}

$notification_id = intval($_GET['id']);

// Include database connection
require_once '../../../admin/config/dbcon.php';

// Get notification details
$query = "SELECT * FROM notifications WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $notification_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Return notification info
    echo json_encode([
        'success' => true,
        'notification' => $row
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Notification not found']);
}

$stmt->close();
$conn->close();
?>
