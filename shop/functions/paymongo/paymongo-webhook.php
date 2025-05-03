<?php
require_once '../../../admin/config/dbcon.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

file_put_contents('webhook.log', $input . PHP_EOL, FILE_APPEND);

if ($data && isset($data['data']['attributes']['type'])) {

    $eventType = $data['data']['attributes']['type'];

    if ($eventType == 'payment.paid') {

        $paymongoPayID = isset($data['data']['attributes']['data']['id']) && 
                         strpos($data['data']['attributes']['data']['id'], 'pay_') === 0 
                         ? $data['data']['attributes']['data']['id'] 
                         : null;

        if ($paymongoPayID) {

            $attributes = $data['data']['attributes']['data']['attributes'];
            $amount = isset($attributes['amount']) ? $attributes['amount'] / 100 : 0; 
            $status = isset($attributes['status']) ? strtoupper($attributes['status']) : 'UNKNOWN'; 
            $externalReferenceNumber = isset($attributes['external_reference_number']) ? $attributes['external_reference_number'] : null;
            $paymentMethod = isset($attributes['source']['type']) ? $attributes['source']['type'] : null;
            
            // Update the orders table with payment information
            $stmt = $conn->prepare("
                UPDATE orders 
                SET payment_id = ?, 
                    payment_method = ?, 
                    status = 'PAID',
                    updated_at = CURRENT_TIMESTAMP
                WHERE order_number = ?
            ");

            $stmt->bind_param(
                "sss",
                $paymongoPayID,
                $paymentMethod,
                $externalReferenceNumber
            );

            if ($stmt->execute()) {
                http_response_code(200); 
                echo "Updated order payment status successfully (payment.paid).";
                
                // Log successful update
                file_put_contents('webhook_success.log', "Order {$externalReferenceNumber} updated with payment {$paymongoPayID} at " . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);
            } else {
                file_put_contents('webhook_error.log', "DB Error (Update): " . $stmt->error . " for order: {$externalReferenceNumber} with payment: {$paymongoPayID}" . PHP_EOL, FILE_APPEND);
                http_response_code(500);
                echo "Failed to update order payment status (payment.paid).";
            }

            $stmt->close();
        } else {
            http_response_code(400); 
            echo "No valid payment ID found.";
        }
    } else {
        http_response_code(400);
        echo "Invalid event type.";
    }
} else {
    http_response_code(400); 
    echo "Invalid JSON data.";
}

$conn->close();
?>