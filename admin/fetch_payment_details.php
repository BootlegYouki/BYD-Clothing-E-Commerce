<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    echo "<div class='alert alert-danger'>Unauthorized access</div>";
    exit();
}

// Get payment ID from request
$payment_id = $_GET['payment_id'] ?? '';

if(empty($payment_id)) {
    echo "<div class='alert alert-danger'>Payment ID is required</div>";
    exit();
}

// Function to fetch payment details from Paymongo API
function fetchPaymentDetails($payment_id) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.paymongo.com/v1/payments/{$payment_id}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Basic c2tfdGVzdF9lYkp6d1JIem5LaXJvRW5BN0N0dDhVbnM6"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return ["error" => "cURL Error #:" . $err];
    } else {
        return json_decode($response, true);
    }
}

// Fetch payment details
$payment_data = fetchPaymentDetails($payment_id);

// Check if payment data is available
if(isset($payment_data['data'])) {
    $payment = $payment_data['data'];
    $attrs = $payment['attributes'];
    
    // Format currency amounts
    function formatAmount($amount, $currency) {
        return '₱' . number_format($amount / 100, 2);
    }
    
    // Format date
    function formatDate($timestamp) {
        return date('F j, Y - g:i A', $timestamp);
    }
    
    // Format address
    $address = $attrs['billing']['address'];
    $formatted_address = $address['line1'];
    if(!empty($address['line2'])) $formatted_address .= ', ' . $address['line2'];
    if(!empty($address['city'])) $formatted_address .= ', ' . $address['city'];
    if(!empty($address['state'])) $formatted_address .= ', ' . $address['state'];
    if(!empty($address['postal_code'])) $formatted_address .= ' ' . $address['postal_code'];
    if(!empty($address['country'])) $formatted_address .= ', ' . $address['country'];
    
    // Get status class
    $status_class = '';
    switch($attrs['status']) {
        case 'paid': $status_class = 'text-success'; break;
        case 'pending': $status_class = 'text-warning'; break;
        case 'failed': $status_class = 'text-danger'; break;
    }
    
    // Output payment details in a well-formatted UI
    ?>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card bg-light">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Payment ID: <span class="text-muted"><?= $payment['id'] ?></span></h6>
                            <p class="mb-0 small">Reference: <?= $attrs['metadata']['reference_number'] ?? 'N/A' ?></p>
                        </div>
                        <div>
                            <span class="badge bg-<?= $attrs['status'] == 'paid' ? 'success' : ($attrs['status'] == 'pending' ? 'warning' : 'danger') ?> fs-6">
                                <?= ucfirst($attrs['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Payment Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="fw-bold border-0">Amount:</td>
                            <td class="border-0"><?= formatAmount($attrs['amount'], $attrs['currency']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Fee:</td>
                            <td><?= formatAmount($attrs['fee'], $attrs['currency']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Net Amount:</td>
                            <td><?= formatAmount($attrs['net_amount'], $attrs['currency']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Payment Method:</td>
                            <td><?= ucfirst($attrs['source']['type'] ?? 'Unknown') ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Description:</td>
                            <td><?= $attrs['description'] ?></td>
                        </tr>
                        <?php if(!empty($attrs['statement_descriptor'])): ?>
                        <tr>
                            <td class="fw-bold">Statement Descriptor:</td>
                            <td><?= $attrs['statement_descriptor'] ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Customer Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="fw-bold border-0">Name:</td>
                            <td class="border-0"><?= $attrs['billing']['name'] ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email:</td>
                            <td><?= $attrs['billing']['email'] ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Phone:</td>
                            <td><?= $attrs['billing']['phone'] ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Address:</td>
                            <td><?= $formatted_address ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Payment Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Created</h6>
                                <p class="text-muted small"><?= formatDate($attrs['created_at']) ?></p>
                            </div>
                        </div>
                        
                        <?php if(isset($attrs['paid_at'])): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Paid</h6>
                                <p class="text-muted small"><?= formatDate($attrs['paid_at']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(isset($attrs['available_at'])): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Available</h6>
                                <p class="text-muted small"><?= formatDate($attrs['available_at']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(isset($attrs['credited_at'])): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Credited</h6>
                                <p class="text-muted small"><?= formatDate($attrs['credited_at']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 15px;
        }
        .timeline-marker {
            position: absolute;
            left: -30px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 5px;
        }
        .timeline-item:not(:last-child):before {
            content: '';
            position: absolute;
            left: -24px;
            top: 20px;
            height: 100%;
            width: 1px;
            background-color: #dee2e6;
        }
    </style>
    <?php
} else {
    // Display error message if payment data couldn't be retrieved
    echo '<div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i>
            Unable to load payment details. Please try again.
          </div>';
    
    if(isset($payment_data['error'])) {
        echo '<div class="alert alert-warning">
                <strong>Error:</strong> ' . htmlspecialchars($payment_data['error']) . '
              </div>';
    }
}
?>
