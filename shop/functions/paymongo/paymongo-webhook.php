<?php

try {
    require_once __DIR__ . '/../../../admin/config/dbcon.php';

    // Create logs directory if it doesn't exist
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    // Set content type to JSON
    header('Content-Type: application/json');

    // Get the raw input
    $input = file_get_contents('php://input');
    
    // Log the raw webhook data for debugging
    file_put_contents($logDir . '/webhook_raw.log', date('Y-m-d H:i:s') . " - Received webhook: " . $input . PHP_EOL, FILE_APPEND);

    // Decode the JSON
    $data = json_decode($input, true);

    // Check if it's valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON payload: " . json_last_error_msg());
    }

    // Basic validation that it might be from PayMongo
    if (!isset($data['data']) || !isset($data['data']['id'])) {
        throw new Exception("Invalid PayMongo webhook format - missing data or ID");
    }

    // Always respond with 200 OK to prevent retries
    http_response_code(200);

    // Verify database connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed: " . ($conn->connect_error ?? "Unknown error"));
    }

    // Process the webhook based on the event type
    $eventType = $data['data']['attributes']['type'] ?? 'unknown';
    
    // Log the event type
    file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Event type: " . $eventType . PHP_EOL, FILE_APPEND);

    // Check for checkout session paid event
    if ($eventType == 'checkout_session.payment.paid' || $eventType == 'payment.paid') {
        try {
            // Extract the checkout session ID
            $checkoutSessionId = $data['data']['id'] ?? '';
            file_put_contents($logDir . '/webhook_session.log', date('Y-m-d H:i:s') . " - Checkout session ID: " . $checkoutSessionId . PHP_EOL, FILE_APPEND);
            
            // Get payment ID - different paths depending on event type
            $paymongoPayID = null;
            
            if ($eventType == 'checkout_session.payment.paid') {
                // For checkout session events, payment ID is in the payments array
                if (isset($data['data']['attributes']['payments']) && !empty($data['data']['attributes']['payments'])) {
                    $paymongoPayID = $data['data']['attributes']['payments'][0]['id'] ?? null;
                    file_put_contents($logDir . '/webhook_payment.log', date('Y-m-d H:i:s') . " - Found payment in checkout session: " . $paymongoPayID . PHP_EOL, FILE_APPEND);
                }
            } else if ($eventType == 'payment.paid') {
                // For payment events, the ID is the data ID
                $paymongoPayID = $data['data']['id'] ?? null;
                file_put_contents($logDir . '/webhook_payment.log', date('Y-m-d H:i:s') . " - Found payment ID in event: " . $paymongoPayID . PHP_EOL, FILE_APPEND);
            }
            
            if (!$paymongoPayID) {
                throw new Exception("Could not find payment ID in webhook data");
            }
            
            // Get metadata from the appropriate location
            $metadata = [];
            
            if (isset($data['data']['attributes']['metadata'])) {
                $metadata = $data['data']['attributes']['metadata'];
                file_put_contents($logDir . '/webhook_metadata.log', date('Y-m-d H:i:s') . " - Found metadata in data attributes: " . print_r($metadata, true) . PHP_EOL, FILE_APPEND);
            } else if (isset($data['data']['attributes']['payments'][0]['attributes']['metadata'])) {
                $metadata = $data['data']['attributes']['payments'][0]['attributes']['metadata'];
                file_put_contents($logDir . '/webhook_metadata.log', date('Y-m-d H:i:s') . " - Found metadata in payment attributes: " . print_r($metadata, true) . PHP_EOL, FILE_APPEND);
            }
            
            // Get the reference number from metadata
            $externalReferenceNumber = isset($metadata['reference_number']) ? 
                                      $metadata['reference_number'] : null;
            
            // If reference number is not in metadata, try to use customer_number as fallback
            if (empty($externalReferenceNumber) && isset($metadata['customer_number'])) {
                $externalReferenceNumber = $metadata['customer_number'];
                file_put_contents($logDir . '/webhook_metadata.log', date('Y-m-d H:i:s') . " - Using customer_number as reference: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
            }
            
            // If still no reference number, try to use the checkout session ID as a last resort
            if (empty($externalReferenceNumber) && !empty($checkoutSessionId)) {
                // Try to find an order with this checkout session ID as payment_id
                $findOrderQuery = $conn->prepare("SELECT order_number FROM orders WHERE payment_id = ? LIMIT 1");
                $findOrderQuery->bind_param("s", $checkoutSessionId);
                $findOrderQuery->execute();
                $findOrderResult = $findOrderQuery->get_result();
                
                if ($orderRow = $findOrderResult->fetch_assoc()) {
                    $externalReferenceNumber = $orderRow['order_number'];
                    file_put_contents($logDir . '/webhook_recovery.log', date('Y-m-d H:i:s') . " - Found order by checkout session ID: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                } else {
                    // If we still can't find it, try to update the most recent pending order
                    file_put_contents($logDir . '/webhook_recovery.log', date('Y-m-d H:i:s') . " - Attempting to update most recent pending order" . PHP_EOL, FILE_APPEND);
                    
                    $findPendingQuery = $conn->query("SELECT order_number FROM orders WHERE status = 'pending' ORDER BY created_at DESC LIMIT 1");
                    if ($pendingOrder = $findPendingQuery->fetch_assoc()) {
                        $externalReferenceNumber = $pendingOrder['order_number'];
                        file_put_contents($logDir . '/webhook_recovery.log', date('Y-m-d H:i:s') . " - Using most recent pending order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
                    }
                }
            }
            
            // Validate reference number
            if (empty($externalReferenceNumber)) {
                throw new Exception("Could not determine order reference number for payment: " . $paymongoPayID);
            }
            
            // Determine payment method
            $paymentMethod = 'paymongo'; // Default to paymongo
            if (isset($data['data']['attributes']['payment_method_used'])) {
                $paymentMethod = $data['data']['attributes']['payment_method_used'];
            } else if (isset($data['data']['attributes']['source']['type'])) {
                $paymentMethod = $data['data']['attributes']['source']['type'];
            }
            
            // Log the payment details
            file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Payment ID: " . $paymongoPayID . ", Order: " . $externalReferenceNumber . PHP_EOL, FILE_APPEND);
            
            try {
                // Update the orders table with payment information and change status to PAID
                $stmt = $conn->prepare("
                    UPDATE orders 
                    SET payment_id = ?, 
                        payment_method = ?, 
                        status = 'PAID' 
                    WHERE order_number = ?
                ");

                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $conn->error);
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
                        
                        try {
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
                                throw new Exception("Order not found: " . $externalReferenceNumber);
                            }
                        } catch (Exception $emailEx) {
                            file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Email error: " . $emailEx->getMessage() . PHP_EOL, FILE_APPEND);
                            // Continue execution - email failure shouldn't stop the process
                        }
                    } else {
                        throw new Exception("No rows updated for order: " . $externalReferenceNumber);
                    }
                    
                    // Return success response
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Payment processed successfully',
                        'order' => $externalReferenceNumber,
                        'payment_id' => $paymongoPayID
                    ]);
                } else {
                    throw new Exception("DB Error: " . $stmt->error . " for order: " . $externalReferenceNumber);
                }

                $stmt->close();
            } catch (Exception $dbEx) {
                file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Database operation error: " . $dbEx->getMessage() . PHP_EOL, FILE_APPEND);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Database error: ' . $dbEx->getMessage()
                ]);
            }
        } catch (Exception $paymentEx) {
            file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Payment processing error: " . $paymentEx->getMessage() . PHP_EOL, FILE_APPEND);
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment processing error: ' . $paymentEx->getMessage()
            ]);
        }
    } else {
        // Handle other event types if needed
        file_put_contents($logDir . '/webhook.log', date('Y-m-d H:i:s') . " - Unhandled event type: " . $eventType . PHP_EOL, FILE_APPEND);
        echo json_encode([
            'status' => 'success',
            'message' => 'Event received but not processed',
            'event_type' => $eventType
        ]);
    }
} catch (Exception $e) {
    // Create logs directory if it doesn't exist (in case the error happens before directory creation)
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logDir . '/webhook_error.log', date('Y-m-d H:i:s') . " - Critical error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    
    // Return error response in JSON format
    http_response_code(200); // Still return 200 to prevent PayMongo from retrying
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid webhook data: ' . $e->getMessage()
    ]);
} finally {
    // Close database connection if it exists
    if (isset($conn)) {
        $conn->close();
    }
}
?>