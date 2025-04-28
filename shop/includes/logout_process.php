<?php
session_start();

// Check if this is an AJAX request
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Clear all session data
$_SESSION = array();
session_destroy();

if ($is_ajax) {
    // For AJAX requests, return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Successfully logged out'
    ]);
    exit;
}
?>