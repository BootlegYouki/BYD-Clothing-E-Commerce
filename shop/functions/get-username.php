<?php
session_start();
header('Content-Type: application/json');

// Debug: Output the full session data to see what we're working with
error_log('SESSION DATA: ' . json_encode($_SESSION));

// Check if username is directly available in session (set during login)
if (isset($_SESSION['username'])) {
    echo json_encode([
        'status' => 'success',
        'username' => $_SESSION['username']
    ]);
} 
// Check if auth_user array is set with username
else if (isset($_SESSION['auth_user']) && isset($_SESSION['auth_user']['username'])) {
    echo json_encode([
        'status' => 'success',
        'username' => $_SESSION['auth_user']['username']
    ]);
} 
// Check if firstname is set in auth_user
else if (isset($_SESSION['auth_user']) && isset($_SESSION['auth_user']['firstname'])) {
    echo json_encode([
        'status' => 'success',
        'username' => $_SESSION['auth_user']['firstname']
    ]);
}
// Fall back to database lookup using user_id from auth_user
else if (isset($_SESSION['auth_user']) && isset($_SESSION['auth_user']['user_id'])) {
    require_once '../../admin/config/dbcon.php';
    
    try {
        // Note: In authcode.php, user ID is stored as 'id' in the database but as 'user_id' in the session
        $userId = $_SESSION['auth_user']['user_id'];
        
        $query = "SELECT firstname FROM users WHERE id='$userId' LIMIT 1";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            echo json_encode([
                'status' => 'success',
                'username' => $user['firstname']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    // User is not logged in
    echo json_encode([
        'status' => 'error',
        'message' => 'Not logged in'
    ]);
}
?>