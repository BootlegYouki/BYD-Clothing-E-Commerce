<?php
/**
 * Order Confirmation Page
 * 
 * This page displays the order confirmation message based on payment status.
 * It receives the status parameter from payment_return.php.
 */
session_start();

// Get payment status from URL parameters (default to 'pending')
$status = $_GET['status'] ?? 'pending';
$reason = $_GET['reason'] ?? '';

// Get payment ID and order ID if available
$paymentId = $_GET['payment_id'] ?? null;
$orderId = $_SESSION['last_order_id'] ?? $_GET['order_id'] ?? 'N/A';

// If we have a payment ID, verify the payment status with PayMongo
if ($paymentId) {
    try {
        require_once 'functions/PayMongoHelper.php';
        $paymongo = new PayMongoHelper();
        $paymentData = $paymongo->getPaymentIntent($paymentId);
        
        // Update status based on payment data
        $apiStatus = $paymentData['data']['attributes']['status'] ?? 'unknown';
        
        if ($apiStatus === 'succeeded') {
            $status = 'success';
            
            // Update order status in database
            require_once '../admin/config/dbcon.php';
            $stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid', status = 'processing' WHERE payment_id = ?");
            $stmt->bind_param('s', $paymentId);
            $stmt->execute();
        } elseif ($apiStatus === 'awaiting_payment_method') {
            $status = 'pending';
        } else {
            $status = 'failed';
            $reason = 'Payment verification failed: ' . $apiStatus;
        }
    } catch (Exception $e) {
        error_log("Payment verification error: " . $e->getMessage());
        // Don't change status if verification fails
    }
}

// Page title based on status
$pageTitle = "Order Confirmation";

// Include header
include 'includes/header.php';
?>

<section class="py-5 my-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center p-5">
                        <?php if ($status === 'success'): ?>
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Payment Successful!</h2>
                            <p class="lead mb-4">Thank you for your order. Your payment has been processed successfully.</p>
                            <p class="mb-4">Order #<?= htmlspecialchars($orderId) ?></p>
                            <p>A confirmation email has been sent to your email address.</p>
                            <div class="mt-4">
                                <a href="order-tracking.php" class="btn btn-primary me-2">Track Your Order</a>
                                <a href="shop.php" class="btn btn-outline-dark">Continue Shopping</a>
                            </div>
                        <?php elseif ($status === 'pending'): ?>
                            <div class="mb-4">
                                <i class="fas fa-clock text-warning" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Payment Processing</h2>
                            <p class="lead mb-4">Your payment is being processed. We'll update you once it's complete.</p>
                            <p class="mb-4">Order #<?= htmlspecialchars($orderId) ?></p>
                            <div class="mt-4">
                                <a href="order-tracking.php" class="btn btn-primary me-2">Track Your Order</a>
                                <a href="shop.php" class="btn btn-outline-dark">Continue Shopping</a>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <i class="fas fa-times-circle text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Payment Failed</h2>
                            <p class="lead mb-4">We couldn't process your payment. Please try again.</p>
                            <?php if ($reason): ?>
                                <p class="mb-4">Reason: <?= htmlspecialchars($reason) ?></p>
                            <?php endif; ?>
                            <div class="mt-4">
                                <a href="checkout.php" class="btn btn-primary me-2">Try Again</a>
                                <a href="shop.php" class="btn btn-outline-dark">Continue Shopping</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>