<?php
session_start();

// Check if user is logged in properly
$isLoggedIn = isset($_SESSION['auth_user']) && isset($_SESSION['auth_user']['user_id']);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'isLoggedIn' => $isLoggedIn,
    'userId' => $isLoggedIn ? $_SESSION['auth_user']['user_id'] : null,
    'sessionData' => $isLoggedIn ? [
        'username' => $_SESSION['auth_user']['username'] ?? null,
        // Don't include sensitive data like passwords
    ] : null
]);
?>