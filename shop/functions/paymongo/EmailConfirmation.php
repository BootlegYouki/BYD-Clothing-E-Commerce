<?php
/**
 * Email Service
 * 
 * This class handles sending order confirmation emails with invoices
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailConfirmation {
    /**
     * Send order confirmation email with invoice to customer
     * 
     * @param array $data Order data
     * @param int $orderId Order ID
     * @param string $paymentId Payment ID
     * @return bool Success status
     */
    public static function sendOrderConfirmationEmail($data, $orderId, $paymentId) {
        try {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->SMTPDebug = 0; // Set to 0 in production
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'beyond.doubtclothing1@gmail.com'; // Replace with your actual email
            $mail->Password = 'fbpu skxv fkgf bzik'; // Replace with your actual app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Recipients
            $mail->setFrom('beyond.doubtclothing1@gmail.com', 'BYD Clothing');
            $mail->addAddress($data['email'], $data['firstname'] . ' ' . $data['lastname']);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your BYD Clothing Order #" . $orderId . " - Invoice";
            
            // Build items HTML
            $items_html = '';
            foreach ($data['cart_items'] as $item) {
                $product_name = $item['name'] ?? $item['title'] ?? $item['productTitle'] ?? 'Product';
                $items_html .= '
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; color: #333333; font-family: Arial, Helvetica, sans-serif;">' . htmlspecialchars($product_name) . ' (' . htmlspecialchars($item['size']) . ')</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center; color: #333333; font-family: Arial, Helvetica, sans-serif;">' . $item['quantity'] . '</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right; color: #333333; font-family: Arial, Helvetica, sans-serif;">₱' . number_format($item['price'], 2) . '</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right; color: #333333; font-family: Arial, Helvetica, sans-serif;">₱' . number_format($item['price'] * $item['quantity'], 2) . '</td>
                </tr>';
            }
            
            // Calculate totals
            $subtotal = 0;
            foreach ($data['cart_items'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping_cost = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 50;
            $total = $subtotal + $shipping_cost;
            $order_date = date('Y-m-d H:i:s');
            
            // Updated Email Template with table-based layout for better email client compatibility
            $mail->Body = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <!--[if mso]>
    <style type="text/css">
        table {border-collapse: collapse; border-spacing: 0; margin: 0;}
        div, td {padding: 0;}
        div {margin: 0 !important;}
    </style>
    <noscript>
    <xml>
        <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: none; text-size-adjust: none;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <!-- Email Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 0; background-color: #ff7f50; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; font-family: Arial, Helvetica, sans-serif;">Order Confirmation</h1>
                            <p style="color: #ffffff; margin: 10px 0 0; font-size: 16px; font-family: Arial, Helvetica, sans-serif;">Thank you for your purchase!</p>
                        </td>
                    </tr>
                    
                    <!-- Invoice Details -->
                    <tr>
                        <td style="padding: 30px 30px 20px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9f9f9; border-radius: 8px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="50%" valign="top">
                                                    <h2 style="color: #333333; margin-top: 0; margin-bottom: 10px; font-size: 20px; font-family: Arial, Helvetica, sans-serif;">BYD Clothing</h2>
                                                    <p style="color: #666666; font-size: 14px; line-height: 1.5; margin: 0; font-family: Arial, Helvetica, sans-serif;">Premium quality sportswear and casual apparel</p>
                                                </td>
                                                <td width="50%" valign="top" style="text-align: right;">
                                                    <h3 style="color: #ff7f50; margin-top: 0; margin-bottom: 10px; font-size: 18px; font-family: Arial, Helvetica, sans-serif;">INVOICE</h3>
                                                    <p style="color: #333333; font-size: 14px; line-height: 1.5; margin: 0 0 5px; font-family: Arial, Helvetica, sans-serif;"><strong>Order #:</strong> ' . $orderId . '</p>
                                                    <p style="color: #333333; font-size: 14px; line-height: 1.5; margin: 0 0 5px; font-family: Arial, Helvetica, sans-serif;"><strong>Date:</strong> ' . $order_date . '</p>
                                                    <p style="color: #333333; font-size: 14px; line-height: 1.5; margin: 0; font-family: Arial, Helvetica, sans-serif;"><strong>Payment Method:</strong> PayMongo</p>
                                                    <p style="color: #333333; font-size: 12px; line-height: 1.5; margin: 5px 0 0; font-family: Arial, Helvetica, sans-serif;"><strong>Location:</strong> ' . (isset($data['latitude']) && isset($data['longitude']) ? 
                                                    '<a href="https://maps.google.com/?q='.$data['latitude'].','.$data['longitude'].'" target="_blank">View on Map</a>' : 'Not specified') . '</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Customer Information -->
                            <h3 style="color: #ff7f50; margin-top: 0; margin-bottom: 15px; font-size: 18px; border-bottom: 1px solid #e9ecef; padding-bottom: 8px; font-family: Arial, Helvetica, sans-serif;">Customer Information</h3>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td width="100px" style="padding-bottom: 8px; color: #666666; font-size: 14px; font-family: Arial, Helvetica, sans-serif;"><strong>Name:</strong></td>
                                    <td style="padding-bottom: 8px; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">' . htmlspecialchars($data['firstname'] . ' ' . $data['lastname']) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 8px; color: #666666; font-size: 14px; font-family: Arial, Helvetica, sans-serif;"><strong>Email:</strong></td>
                                    <td style="padding-bottom: 8px; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">' . htmlspecialchars($data['email']) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 8px; color: #666666; font-size: 14px; font-family: Arial, Helvetica, sans-serif;"><strong>Phone:</strong></td>
                                    <td style="padding-bottom: 8px; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">' . htmlspecialchars($data['phone']) . '</td>
                                </tr>
                            </table>
                            
                            <!-- Shipping Information -->
                            <h3 style="color: #ff7f50; margin-top: 0; margin-bottom: 15px; font-size: 18px; border-bottom: 1px solid #e9ecef; padding-bottom: 8px; font-family: Arial, Helvetica, sans-serif;">Shipping Information</h3>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 20px;">
                                <tr>
                                    <td width="100px" style="padding-bottom: 8px; color: #666666; font-size: 14px; font-family: Arial, Helvetica, sans-serif;"><strong>Address:</strong></td>
                                    <td style="padding-bottom: 8px; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">' . htmlspecialchars($data['address']) . ', ' . htmlspecialchars($data['zipcode']) . '</td>
                                </tr>
                            </table>
                            
                            <!-- Order Summary -->
                            <h3 style="color: #ff7f50; margin-top: 0; margin-bottom: 15px; font-size: 18px; border-bottom: 1px solid #e9ecef; padding-bottom: 8px; font-family: Arial, Helvetica, sans-serif;">Order Summary</h3>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <th style="background-color: #f0f0f0; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">Product</th>
                                    <th style="background-color: #f0f0f0; padding: 10px; text-align: center; border-bottom: 2px solid #ddd; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">Quantity</th>
                                    <th style="background-color: #f0f0f0; padding: 10px; text-align: right; border-bottom: 2px solid #ddd; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">Price</th>
                                    <th style="background-color: #f0f0f0; padding: 10px; text-align: right; border-bottom: 2px solid #ddd; color: #333333; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">Total</th>
                                </tr>
                                ' . $items_html . '
                                <tr>
                                    <td colspan="3" style="padding: 10px; text-align: right; border-top: 2px solid #ddd; color: #333333; font-weight: bold; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">Subtotal:</td>
                                    <td style="padding: 10px; text-align: right; border-top: 2px solid #ddd; color: #333333; font-weight: bold; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">₱' . number_format($subtotal, 2) . '</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding: 10px; text-align: right; color: #333333; font-weight: bold; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">Shipping Fee:</td>
                                    <td style="padding: 10px; text-align: right; color: #333333; font-weight: bold; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">₱' . number_format($shipping_cost, 2) . '</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding: 10px; text-align: right; color: #333333; font-weight: bold; font-size: 14px; font-family: Arial, Helvetica, sans-serif;">Total:</td>
                                    <td style="padding: 10px; text-align: right; color: #ff7f50; font-weight: bold; font-size: 16px; font-family: Arial, Helvetica, sans-serif;">₱' . number_format($total, 2) . '</td>
                                </tr>
                            </table>
                            
                            <!-- Thank You Message -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center" style="padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
                                        <p style="color: #ff7f50; font-size: 18px; font-weight: bold; margin: 0; font-family: Arial, Helvetica, sans-serif;">Thank you for shopping with BYD Clothing!</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Divider -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                <tr>
                                    <td style="border-bottom: 1px solid #e9ecef;"></td>
                                </tr>
                            </table>
                            
                            <p style="color: #666666; font-size: 14px; line-height: 1.5; margin-bottom: 15px; font-family: Arial, Helvetica, sans-serif;">If you have any questions about your order, please contact our customer support at <a href="mailto:beyond.doubtclothing1@gmail.com" style="color: #ff7f50; text-decoration: none;">beyond.doubtclothing1@gmail.com</a>.</p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="color: #777777; font-size: 12px; margin: 0 0 10px; font-family: Arial, Helvetica, sans-serif;">© ' . date('Y') . ' BYD Clothing. All rights reserved.</p>
                            <p style="color: #777777; font-size: 12px; margin: 0; font-family: Arial, Helvetica, sans-serif;">This email was sent to you because you made a purchase at BYD Clothing.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
            
            // Send email
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}