<?php

require_once __DIR__ . '/../../../admin/config/dbcon.php';

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Get the raw input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the raw webhook data for debugging
file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Received webhook: " . $input . PHP_EOL, FILE_APPEND);

// Always respond with 200 OK to prevent retries
http_response_code(200);

// Verify database connection
if (!isset($conn) || $conn->connect_error) {
    file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Database connection failed: " . ($conn->connect_error ?? "Unknown error") . PHP_EOL, FILE_APPEND);
    echo "Database connection error";
    exit;
}

if ($data && isset($data['data']['attributes']['type'])) {
    $eventType = $data['data']['attributes']['type'];
    
    // Log the event type
    file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Event type: " . $eventType . PHP_EOL, FILE_APPEND);

    // Check for checkout session paid event
    if ($eventType == 'checkout_session.payment.paid' || $eventType == 'payment.paid') {
        // Extract the checkout session data
        $checkoutSessionData = $data['data']['attributes']['data'];
        
        // Get the payment ID from the checkout session
        $paymongoPayID = null;
        if (isset($checkoutSessionData['attributes']['payments']) && !empty($checkoutSessionData['attributes']['payments'])) {
            // For checkout_session.payment.paid events
            $paymentData = $checkoutSessionData['attributes']['payments'][0];
            $paymongoPayID = $paymentData['id'];
        } elseif (isset($checkoutSessionData['id']) && strpos($checkoutSessionData['id'], 'pay_') === 0) {
            // For payment.paid events
            $paymongoPayID = $checkoutSessionData['id'];
        }
        
        if ($paymongoPayID) {
            // Get metadata from the checkout session
            $metadata = isset($checkoutSessionData['attributes']['metadata']) ? 
                        $checkoutSessionData['attributes']['metadata'] : [];
            
            // Get the reference number from metadata
            $externalReferenceNumber = isset($metadata['reference_number']) ? 
                                      $metadata['reference_number'] : null;
            
            // Validate reference number
            if (empty($externalReferenceNumber)) {
                file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Missing reference number in metadata for payment: " . $paymongoPayID . PHP_EOL, FILE_APPEND);
                echo "Missing reference number";
                exit;
            }
            
            // Determine payment method
            $paymentMethod = 'card'; // Default to card
            if (isset($checkoutSessionData['attributes']['payment_method_used'])) {
                $paymentMethod = $checkoutSessionData['attributes']['payment_method_used'];
            } elseif (isset($checkoutSessionData['attributes']['source']['type'])) {
                $paymentMethod = $checkoutSessionData['attributes']['source']['type'];
            }
            
            // Log the payment details
            file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Payment ID: " . $paymongoPayID . ", Order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
            
            // Update the orders table with payment information and change status to PAID
            $stmt = $conn->prepare("
                UPDATE orders 
                SET payment_id = ?, 
                    payment_method = ?, 
                    status = 'PAID' 
                WHERE order_number = ?
            ");

            if (!$stmt) {
                file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Prepare statement failed: " . $conn->error . PHP_EOL, FILE_APPEND);
                echo "Database prepare error";
                exit;
            }

            $stmt->bind_param(
                "sss",
                $paymongoPayID,
                $paymentMethod,
                $externalReferenceNumber
            );

            if ($stmt->execute()) {
                // Check if any rows were affected
                if ($stmt->affected_rows > 0) {
                    file_put_contents($logDir . '/webhook_success.log', date('Y-m-d H:i:s') . " - Successfully updated order: " . $externalReferenceNumber . " with payment: " . $paymongoPayID . PHP_EOL, FILE_APPEND);
                    
                    // Now get the order details to send email confirmation
                    $orderQuery = $conn->prepare("SELECT * FROM orders WHERE order_number = ?");
                    $orderQuery->bind_param("s", $externalReferenceNumber);
                    $orderQuery->execute();
                    $orderResult = $orderQuery->get_result();
                    
                    if ($orderData = $orderResult->fetch_assoc()) {
                        // Get order items
                        $itemsQuery = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
                        $itemsQuery->bind_param("i", $orderData['order_id']);
                        $itemsQuery->execute();
                        $itemsResult = $itemsQuery->get_result();
                        
                        $cartItems = [];
                        while ($item = $itemsResult->fetch_assoc()) {
                            $cartItems[] = $item;
                        }
                        
                        // Prepare data for email
                        $emailData = [
                            'firstname' => $orderData['firstname'],
                            'lastname' => $orderData['lastname'],
                            'email' => $orderData['email'],
                            'phone' => $orderData['phone'],
                            'address' => $orderData['address'],
                            'zipcode' => $orderData['zipcode'],
                            'cart_items' => $cartItems
                        ];
                        
                        // Include the EmailConfirmation class
                        require_once 'EmailConfirmation.php';
                        
                        // Send confirmation email
                        $emailSent = EmailConfirmation::sendOrderConfirmationEmail($emailData, $orderData['order_id'], $paymongoPayID);
                        file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Email sent: " . ($emailSent ? "Yes" : "No") . PHP_EOL, FILE_APPEND);
                    } else {
                        file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Order not found: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                    }
                } else {
                    file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - No rows updated for order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                }
                
                echo "Payment processed successfully";
            } else {
                file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - DB Error: " . $stmt->error . " for order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                echo "Failed to process payment";
            }

            $stmt->close();
        } else {
            file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Invalid payment ID in webhook data" . PHP_EOL, FILE_APPEND);
            echo "Invalid payment ID";
        }
    } else {
        // Handle other event types if needed
        file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Unhandled event type: " . $eventType . PHP_EOL, FILE_APPEND);
        echo "Event received but not processed";
    }
} else {
    file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Invalid webhook data" . PHP_EOL, FILE_APPEND);
    echo "Invalid webhook data";
}

$conn->close();
?>