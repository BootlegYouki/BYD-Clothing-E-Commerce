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

// Get order ID if available
$orderId = $_SESSION['last_order_id'] ?? 'N/A';

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