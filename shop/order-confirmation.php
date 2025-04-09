<?php
/**
 * Order Confirmation Page
 * 
 * This page displays the order confirmation message based on payment status.
 * It receives the status parameter from payment_return.php.
 */

// Get payment status from URL parameters (default to 'pending')
$status = $_GET['status'] ?? 'pending';

// Display appropriate message based on payment status
switch ($status) {
    case 'success':
        echo "<h2>Payment Successful!</h2>";
        break;
    case 'failed':
        echo "<h2>Payment Failed</h2>";
        break;
    default:
        echo "<h2>Payment Processing</h2>";
}