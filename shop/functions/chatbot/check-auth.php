<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['auth_user']) && isset($_SESSION['auth_user']['user_id'])) {
    echo json_encode([
        'isLoggedIn' => true,
        'userId' => $_SESSION['auth_user']['user_id'],
        'username' => $_SESSION['username'] ?? $_SESSION['auth_user']['username'] ?? 'User'
    ]);
} else {
    echo json_encode([
        'isLoggedIn' => false
    ]);
}
?>