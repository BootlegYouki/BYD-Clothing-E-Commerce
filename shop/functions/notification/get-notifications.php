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
    'notifications' => array(),
    'unread_count' => 0,
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
    // Check if only count is requested
    if (isset($_GET['count_only']) && $_GET['count_only'] == 1) {
        $count_query = "SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $conn->prepare($count_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $response['success'] = true;
        $response['unread_count'] = $row['unread'];
        echo json_encode($response);
        exit;
    }
    
    // Get limit parameter, default is 5
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    
    // Check if only unread notifications are requested
    $unread_only = isset($_GET['unread_only']) && $_GET['unread_only'] == 1;
    
    // Build query
    $query = "SELECT id, type, title, message, created_at, is_read FROM notifications WHERE user_id = ?";
    
    if ($unread_only) {
        $query .= " AND is_read = 0";
    }
    
    $query .= " ORDER BY created_at DESC LIMIT ?";
    
    // Prepare and execute query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch notifications
    $notifications = array();
    while ($row = $result->fetch_assoc()) {
        // Calculate time ago
        $row['time_ago'] = getTimeAgo($row['created_at']);
        
        // Set icon based on notification type
        switch ($row['type']) {
            case 'order_status':
            case 'order_shipped':
                $row['icon'] = 'bx bx-package text-primary';
                break;
            case 'order_delivered':
                $row['icon'] = 'bx bx-check-double text-success';
                break;
            case 'promotion':
                $row['icon'] = 'bx bx-heart text-danger';
                break;
            case 'account':
                $row['icon'] = 'bx bx-user text-info';
                break;
            case 'review_reminder':
                $row['icon'] = 'bx bx-star text-warning';
                break;
            case 'new_release':
                $row['icon'] = 'bx bx-gift text-info';
                break;
            default:
                $row['icon'] = 'bx bx-bell text-primary';
                break;
        }
        
        $notifications[] = $row;
    }
    
    // Count unread notifications
    $count_query = "SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Set response data
    $response['success'] = true;
    $response['notifications'] = $notifications;
    $response['unread_count'] = $row['unread'];
    
} catch (Exception $e) {
    $response['message'] = 'Error fetching notifications: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($response);
exit;

/**
 * Helper function to calculate time ago
 */
function getTimeAgo($timestamp) {
    $time = strtotime($timestamp);
    $current = time();
    $diff = $current - $time;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . " month" . ($months > 1 ? "s" : "") . " ago";
    } else {
        $years = floor($diff / 31536000);
        return $years . " year" . ($years > 1 ? "s" : "") . " ago";
    }
}
?>
