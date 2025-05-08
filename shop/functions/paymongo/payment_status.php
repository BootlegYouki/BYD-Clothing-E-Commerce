<?php
/**
 * Payment Status Checker
 * 
 * This script checks the status of a PayMongo checkout session
 * and redirects the user accordingly.
 */
require_once '../../../admin/config/dbcon.php';
require_once __DIR__ . '/PayMongoHelper.php';

session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get session ID from query parameter
$sessionId = $_GET['session_id'] ?? null;

// Get reference number
$reference = $_GET['reference'] ?? null;

// If no session ID provided, redirect to home page
if (!$sessionId) {
    header("Location: ../../index.php");
    exit;
}

try {
    // Initialize PayMongo helper
    $paymongo = new PayMongoHelper();
    
    // Get session details
    $session = $paymongo->getCheckoutSession($sessionId);
    
    // Extract payment status
    $paymentStatus = $session['data']['attributes']['payment_intent']['status'] ?? 'unknown';
    $paymentMethodUsed = $session['data']['attributes']['payment_method_used'] ?? 'unknown';
    
    // Set up base URL for redirects
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    
    // Determine project path
    $projectPath = '';
    if (strpos($baseUrl, 'localhost') !== false) {
        $currentPath = $_SERVER['SCRIPT_NAME'];
        $pathParts = explode('/', $currentPath);
        $projectIndex = array_search('BYD-Clothing-E-Commerce-main', $pathParts);
        if ($projectIndex !== false) {
            for ($i = 1; $i <= $projectIndex; $i++) {
                if (!empty($pathParts[$i])) {
                    $projectPath .= '/' . $pathParts[$i];
                }
            }
        }
    }
    
    // Construct URLs
    $successUrl = $baseUrl . $projectPath . '/shop/payment_return.php?status=success&reference=' . urlencode($reference);
    $pendingUrl = $baseUrl . $projectPath . '/shop/payment_return.php?status=pending&reference=' . urlencode($reference);
    $failedUrl = $baseUrl . $projectPath . '/shop/payment_return.php?status=failed&reference=' . urlencode($reference);
    
    // Check payment status and redirect accordingly
    if ($paymentStatus === 'succeeded') {
        // Payment succeeded, redirect to success page
        header("Location: $successUrl");
        exit;
    } else if ($paymentStatus === 'awaiting_payment_method' && $paymentMethodUsed === 'qrph') {
        // For QR Ph payments, show polling page to wait for payment confirmation
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Processing Payment</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f8f9fa;
                    padding-top: 50px;
                }
                .payment-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .spinner-border {
                    width: 4rem;
                    height: 4rem;
                    margin: 30px auto;
                }
                .progress {
                    height: 10px;
                    margin: 30px 0;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="payment-container">
                    <h1 class="mb-4">Processing Payment</h1>
                    <p class="lead">Your QR Ph payment is being processed.</p>
                    <p>Please do not close this page until the process is complete.</p>
                    
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    
                    <div class="progress">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <p id="status-message">Checking payment status...</p>
                    
                    <div id="timer-container" class="mt-4 mb-3">
                        <p>Next check in: <span id="timer">10</span> seconds</p>
                    </div>
                </div>
            </div>
            
            <script>
                // Set up variables
                const sessionId = '<?php echo $sessionId; ?>';
                const successUrl = '<?php echo $successUrl; ?>';
                const pendingUrl = '<?php echo $pendingUrl; ?>';
                const failedUrl = '<?php echo $failedUrl; ?>';
                const maxAttempts = 20;
                let attempts = 0;
                let checkInterval = 10000; // 10 seconds initially
                let timer = 10;
                
                // Update timer display
                function updateTimer() {
                    document.getElementById('timer').textContent = timer;
                    timer--;
                    
                    if (timer < 0) {
                        checkPaymentStatus();
                        timer = 10;
                    } else {
                        setTimeout(updateTimer, 1000);
                    }
                }
                
                // Start timer
                updateTimer();
                
                // Function to check payment status
                function checkPaymentStatus() {
                    attempts++;
                    
                    // Update progress bar
                    const progressPercentage = (attempts / maxAttempts) * 100;
                    document.getElementById('progress-bar').style.width = progressPercentage + '%';
                    document.getElementById('progress-bar').setAttribute('aria-valuenow', progressPercentage);
                    
                    document.getElementById('status-message').textContent = 'Checking payment status... (Attempt ' + attempts + ' of ' + maxAttempts + ')';
                    
                    // Make AJAX request to check payment status
                    fetch('check_payment_status.php?session_id=' + sessionId, {
                        method: 'GET'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'succeeded') {
                            // Payment successful, redirect to success page
                            document.getElementById('status-message').textContent = 'Payment successful! Redirecting...';
                            window.location.href = successUrl;
                        } else if (data.status === 'failed') {
                            // Payment failed, redirect to failed page
                            document.getElementById('status-message').textContent = 'Payment failed! Redirecting...';
                            window.location.href = failedUrl;
                        } else {
                            // Payment still pending
                            if (attempts < maxAttempts) {
                                // Increase interval after several attempts
                                if (attempts > 5) {
                                    timer = 15; // Wait longer between checks
                                }
                                if (attempts > 10) {
                                    timer = 20; // Wait even longer
                                }
                                
                                // Update timer and schedule next check
                                document.getElementById('status-message').textContent = 'Payment still processing...';
                                updateTimer();
                            } else {
                                // Max attempts reached, redirect to pending page
                                document.getElementById('status-message').textContent = 'Payment is taking longer than expected! Redirecting...';
                                window.location.href = pendingUrl;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking status:', error);
                        // Continue checking despite errors
                        if (attempts < maxAttempts) {
                            updateTimer();
                        } else {
                            window.location.href = pendingUrl;
                        }
                    });
                }
            </script>
        </body>
        </html>
        <?php
        exit;
    } else if ($paymentStatus === 'awaiting_payment_method') {
        // Payment is still pending, keep on payment page or redirect to pending page
        header("Location: $pendingUrl");
        exit;
    } else {
        // Payment failed or other status
        header("Location: $failedUrl");
        exit;
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Error checking payment status: " . $e->getMessage());
    
    // Redirect to failed page
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $failedUrl = $baseUrl . "/shop/payment_return.php?status=error";
    header("Location: $failedUrl");
    exit;
}
