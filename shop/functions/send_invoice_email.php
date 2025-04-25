<?php
require_once '../../admin/config/dbcon.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

function getEnvVar($name, $default = '') {
    $envFile = __DIR__ . '/../../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if ($key === $name) {
                    return $value;
                }
            }
        }
    }
    return $default;
}

function sendInvoiceEmail($order_id, $conn) {
    try {
        // Get order details
        $order_query = "SELECT o.*, u.firstname, u.lastname, u.email, u.phone_number, u.full_address 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        WHERE o.order_id = ?";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order_result = $stmt->get_result();
        
        if ($order_result->num_rows === 0) {
            error_log("Order not found: $order_id");
            return false;
        }
        
        $order = $order_result->fetch_assoc();
        
        // Get order items
        $items_query = "SELECT oi.*, p.name, p.sku 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?";
        $stmt = $conn->prepare($items_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items_result = $stmt->get_result();
        
        $items = [];
        while ($item = $items_result->fetch_assoc()) {
            $items[] = $item;
        }
        
        // Format date
        $order_date = date('F j, Y', strtotime($order['created_at']));
        
        // Initialize PHPMailer
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = 0; // Set to 0 in production
        $mail->isSMTP();
        $mail->Host = getEnvVar('SMTP_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = getEnvVar('SMTP_USERNAME', '');
        $mail->Password = getEnvVar('SMTP_PASSWORD', '');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getEnvVar('SMTP_PORT', '587');
        
        // Recipients
        $mail->setFrom(getEnvVar('SMTP_FROM_EMAIL', ''), getEnvVar('SMTP_FROM_NAME', 'BYD Clothing'));
        $mail->addAddress($order['email'], $order['firstname'] . ' ' . $order['lastname']);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your BYD Clothing Order #' . $order_id . ' - Invoice';
        
        // Build items HTML
        $items_html = '';
        foreach ($items as $item) {
            $items_html .= '
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($item['name']) . ' (' . htmlspecialchars($item['size']) . ')</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center;">' . $item['quantity'] . '</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">₱' . number_format($item['price'], 2) . '</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">₱' . number_format($item['price'] * $item['quantity'], 2) . '</td>
            </tr>';
        }
        
        // Enhanced Email Template
        $mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #FF7F50;
            padding: 20px;
            text-align: center;
            color: white;
        }
        .invoice-details {
            margin: 20px 0;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .invoice-table th {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
        .customer-info, .shipping-info {
            margin-bottom: 20px;
        }
        .section-title {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #FF7F50;
        }
        .thank-you {
            text-align: center;
            margin: 30px 0;
            font-size: 18px;
            color: #FF7F50;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your purchase!</p>
        </div>
        
        <div class="invoice-details">
            <table width="100%">
                <tr>
                    <td width="50%">
                        <h2>BYD Clothing</h2>
                        <p>Premium quality sportswear and casual apparel</p>
                    </td>
                    <td width="50%" style="text-align: right;">
                        <h3>INVOICE</h3>
                        <p><strong>Order #:</strong> ' . $order_id . '</p>
                        <p><strong>Date:</strong> ' . $order_date . '</p>
                        <p><strong>Payment Method:</strong> ' . ucfirst($order['payment_method']) . '</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="customer-info">
            <h3 class="section-title">Customer Information</h3>
            <p><strong>Name:</strong> ' . htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($order['email']) . '</p>
            <p><strong>Phone:</strong> ' . htmlspecialchars($order['phone_number']) . '</p>
        </div>
        
        <div class="shipping-info">
            <h3 class="section-title">Shipping Information</h3>
            <p><strong>Address:</strong> ' . htmlspecialchars($order['full_address']) . '</p>
        </div>
        
        <h3 class="section-title">Order Summary</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                ' . $items_html . '
                <tr class="total-row">
                    <td colspan="3" style="padding: 10px; text-align: right; border-top: 2px solid #ddd;"><strong>Subtotal:</strong></td>
                    <td style="padding: 10px; text-align: right; border-top: 2px solid #ddd;">₱' . number_format($order['total_amount'] - $order['shipping_fee'], 2) . '</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="padding: 10px; text-align: right;"><strong>Shipping Fee:</strong></td>
                    <td style="padding: 10px; text-align: right;">₱' . number_format($order['shipping_fee'], 2) . '</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="padding: 10px; text-align: right;"><strong>Total:</strong></td>
                    <td style="padding: 10px; text-align: right;">₱' . number_format($order['total_amount'], 2) . '</td>
                </tr>
            </tbody>
        </table>
        
        <div class="thank-you">
            <p>Thank you for shopping with BYD Clothing!</p>
        </div>
        
        <div class="footer">
            <p>If you have any questions about your order, please contact our customer support.</p>
            <p>&copy; ' . date('Y') . ' BYD Clothing. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
        $mail->isHTML(true);
        $mail->send();
    }
    catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}