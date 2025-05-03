<?php
include '/../../../admin/config/dbcon.php';;

$input = file_get_contents('php://input');
$data = json_decode($input, true);

file_put_contents('webhook.log', $input . PHP_EOL, FILE_APPEND);

if ($data && isset($data['data']['attributes']['type'])) {
    $eventType = $data['data']['attributes']['type'];

    if ($eventType == 'checkout_session.payment.paid') {
        // Extract checkout session data
        $checkoutData = $data['data']['attributes']['data'];
        $checkoutAttributes = $checkoutData['attributes'];
        $paymentIntent = $checkoutAttributes['payment_intent'];
        
        // Get reference number from checkout session
        $referenceNumber = $checkoutAttributes['reference_number'] ?? null;

        if ($referenceNumber) {
            // Update order status to "Paid"
            $stmt = $conn->prepare("
                UPDATE orders 
                SET 
                    status = 'Paid',
                    payment_id = ?,
                    updated_at = NOW()
                WHERE order_number = ?
            ");

            $checkoutSessionId = $checkoutData['id']; // payment_id
            $stmt->bind_param("ss", $checkoutSessionId, $referenceNumber);

            if ($stmt->execute()) {
                http_response_code(200);
                echo "Order status updated to Paid";
            } else {
                file_put_contents('webhook_error.log', "DB Error: " . $stmt->error, FILE_APPEND);
                http_response_code(500);
                echo "Failed to update order status";
            }
            $stmt->close();
        } else {
            http_response_code(400);
            echo "Missing reference number";
        }
    } else {
        http_response_code(400);
        echo "Unsupported event type: $eventType";
    }
} else {
    http_response_code(400);
    echo "Invalid webhook payload";
}

$conn->close();
?>