<?php
// Include the environment loader
require_once __DIR__ . '/../../../admin/config/env_loader.php';
require_once __DIR__ . '/../../../admin/config/dbcon.php';

// Get webhook secret key from environment variables
$webhookSecretKey = getEnvVar('PAYMONGO_WEBHOOK_SECRET');

// Get PayMongo signature from headers
$headers = getallheaders();
$paymongoSignature = isset($headers['X-Paymongo-Signature']) ? $headers['X-Paymongo-Signature'] : 
                    (isset($headers['x-paymongo-signature']) ? $headers['x-paymongo-signature'] : '');

// Log all headers for debugging
file_put_contents('webhook_headers.log', date('Y-m-d H:i:s') . " - Headers: " . print_r($headers, true) . PHP_EOL, FILE_APPEND);

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the raw webhook data for debugging
file_put_contents('webhook.log', date('Y-m-d H:i:s') . " - Received webhook: " . $input . PHP_EOL, FILE_APPEND);

// Verify webhook signature if signature is present
if (!empty($paymongoSignature) && !empty($webhookSecretKey)) {
    $computedSignature = hash_hmac('sha256', $input, $webhookSecretKey);
    $isValidSignature = hash_equals($paymongoSignature, $computedSignature);
    
    file_put_contents('webhook.log', date('Y-m-d H:i:s') . " - Signature validation: " . 
        ($isValidSignature ? "Valid" : "Invalid") . PHP_EOL, FILE_APPEND);
    
    if (!$isValidSignature) {
        file_put_contents('webhook_error.log', date('Y-m-d H:i:s') . " - Invalid signature" . PHP_EOL, FILE_APPEND);
        http_response_code(401);
        echo "Invalid signature";
        exit;
    }
}

if ($data && isset($data['data']['attributes']['type'])) {
    $eventType = $data['data']['attributes']['type'];
    
    // Log the event type
    file_put_contents('webhook.log', date('Y-m-d H:i:s') . " - Event type: " . $eventType . PHP_EOL, FILE_APPEND);

    if ($eventType == 'payment.paid') {
        $paymongoPayID = isset($data['data']['attributes']['data']['id']) && 
                         strpos($data['data']['attributes']['data']['id'], 'pay_') === 0 
                         ? $data['data']['attributes']['data']['id'] 
                         : null;

        if ($paymongoPayID) {
            $attributes = $data['data']['attributes']['data']['attributes'];
            $externalReferenceNumber = isset($attributes['external_reference_number']) ? $attributes['external_reference_number'] : null;
            $paymentMethod = isset($attributes['source']['type']) ? $attributes['source']['type'] : null;
            
            // Log the payment details
            file_put_contents('webhook.log', date('Y-m-d H:i:s') . " - Payment ID: " . $paymongoPayID . ", Order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
            
            // Update the orders table with payment information and change status to PAID
            $stmt = $conn->prepare("
                UPDATE orders 
                SET payment_id = ?, 
                    payment_method = ?, 
                    status = 'PAID' 
                WHERE order_number = ?
            ");

            $stmt->bind_param(
                "sss",
                $paymongoPayID,
                $paymentMethod,
                $externalReferenceNumber
            );

            if ($stmt->execute()) {
                // Check if any rows were affected
                if ($stmt->affected_rows > 0) {
                    file_put_contents('webhook_success.log', date('Y-m-d H:i:s') . " - Successfully updated order: " . $externalReferenceNumber . " with payment: " . $paymongoPayID . PHP_EOL, FILE_APPEND);
                    
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
                        file_put_contents('webhook.log', date('Y-m-d H:i:s') . " - Email sent: " . ($emailSent ? "Yes" : "No") . PHP_EOL, FILE_APPEND);
                    } else {
                        file_put_contents('webhook_error.log', date('Y-m-d H:i:s') . " - Order not found: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                    }
                } else {
                    file_put_contents('webhook_error.log', date('Y-m-d H:i:s') . " - No rows updated for order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                }
                
                http_response_code(200);
                echo "Payment processed successfully";
            } else {
                file_put_contents('webhook_error.log', date('Y-m-d H:i:s') . " - DB Error: " . $stmt->error . " for order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                http_response_code(500);
                echo "Failed to process payment";
            }

            $stmt->close();
        } else {
            file_put_contents('webhook_error.log', date('Y-m-d H:i:s') . " - Invalid payment ID in webhook data" . PHP_EOL, FILE_APPEND);
            http_response_code(400);
            echo "Invalid payment ID";
        }
    } else {
        // Handle other event types if needed
        file_put_contents('webhook.log', date('Y-m-d H:i:s') . " - Unhandled event type: " . $eventType . PHP_EOL, FILE_APPEND);
        http_response_code(200);
        echo "Event received but not processed";
    }
} else {
    file_put_contents('webhook_error.log', date('Y-m-d H:i:s') . " - Invalid webhook data" . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo "Invalid webhook data";
}

$conn->close();
?>