<?php
/**
 * AJAX Payment Status Checker
 * 
 * This script is called via AJAX to check the status of a payment session
 */
require_once '../../../admin/config/dbcon.php';
require_once __DIR__ . '/PayMongoHelper.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get session ID from query parameter
$sessionId = $_GET['session_id'] ?? null;

if (!$sessionId) {
    echo json_encode([
        'success' => false,
        'status' => 'error',
        'message' => 'No session ID provided'
    ]);
    exit;
}

try {
    // Initialize PayMongo helper
    $paymongo = new PayMongoHelper();
    
    // Get session details
    $session = $paymongo->getCheckoutSession($sessionId);
    
    // Extract payment status
    $paymentStatus = $session['data']['attributes']['payment_intent']['status'] ?? 'unknown';
    
    // Return status
    echo json_encode([
        'success' => true,
        'status' => $paymentStatus,
        'session_id' => $sessionId
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
