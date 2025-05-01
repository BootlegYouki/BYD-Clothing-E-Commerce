<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

include 'config/dbcon.php';

// Check if transactions table exists, if not create it
// This ensures the database structure is ready for transaction management
$check_table_query = "SHOW TABLES LIKE 'transactions'";
$table_result = mysqli_query($conn, $check_table_query);

if(mysqli_num_rows($table_result) == 0) {
    // Table doesn't exist, create it with all necessary fields for transaction tracking
    $create_table_query = "CREATE TABLE `transactions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `payment_id` varchar(255) NOT NULL,
        `customer_name` varchar(255) DEFAULT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `amount` decimal(10,2) NOT NULL,
        `payment_method` varchar(50) DEFAULT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'pending',
        `description` text DEFAULT NULL,
        `refund_id` varchar(255) DEFAULT NULL,
        `refund_amount` decimal(10,2) DEFAULT NULL,
        `refund_reason` text DEFAULT NULL,
        `refunded_at` datetime DEFAULT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `payment_id` (`payment_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    mysqli_query($conn, $create_table_query);
}

// Debug: Check if there are any transactions in the database
$debug_query = "SELECT COUNT(*) as count FROM transactions";
$debug_result = mysqli_query($conn, $debug_query);
$debug_data = mysqli_fetch_assoc($debug_result);
$transaction_count = $debug_data['count'];

// Set page title for the browser tab
$page_title = "Transactions";

// Get current filter values from URL parameters to maintain state between page loads
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the base SQL query to retrieve transaction data
$query = "SELECT * FROM transactions WHERE 1=1";

// Add dynamic filters based on user selections
if (!empty($status_filter)) {
    $query .= " AND status = '$status_filter'";
}

if (!empty($date_from)) {
    $query .= " AND created_at >= '$date_from 00:00:00'";
}

if (!empty($date_to)) {
    $query .= " AND created_at <= '$date_to 23:59:59'";
}

if (!empty($search)) {
    $query .= " AND (payment_id LIKE '%$search%' OR customer_name LIKE '%$search%' OR email LIKE '%$search%')";
}

// Sort transactions by creation date (newest first)
$query .= " ORDER BY created_at DESC";

// Execute the query and store results in an array for display
$result = mysqli_query($conn, $query);
$transactions = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = $row;
    }
}

// Get transaction statistics for dashboard metrics
// Calculates totals for different transaction statuses and financial summaries
$stats_query = "SELECT 
                    COUNT(*) as total_count,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                    SUM(CASE WHEN status = 'refunded' THEN 1 ELSE 0 END) as refunded_count,
                    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN status = 'refunded' THEN refund_amount ELSE 0 END) as total_refunded
                FROM transactions";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<!-- HTML structure begins here -->
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Transactions | Beyond Doubt Clothing</title>
  <!-- Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  
  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/sidebar.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
  <!-- Filter Card -->
  <div class="card mb-4">
    <div class="card-body p-3">
      <form method="GET" action="" class="row g-3 align-items-center">
        <!-- Status Filter -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="status" class="form-label">Transaction Status</label>
          <select name="status" id="status" class="form-select">
            <option value="" <?= $status_filter == '' ? 'selected' : '' ?>>All Status</option>
            <option value="paid" <?= $status_filter == 'paid' ? 'selected' : '' ?>>Paid</option>
            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="failed" <?= $status_filter == 'failed' ? 'selected' : '' ?>>Failed</option>
            <option value="refunded" <?= $status_filter == 'refunded' ? 'selected' : '' ?>>Refunded</option>
          </select>
        </div>
        
        <!-- Date From Filter -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="date_from" class="form-label">Date From</label>
          <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $date_from ?>">
        </div>
        
        <!-- Date To Filter -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="date_to" class="form-label">Date To</label>
          <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $date_to ?>">
        </div>
        
        <!-- Search -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="search" class="form-label">Search</label>
          <input type="text" class="form-control" id="search" name="search" placeholder="Search by ID, name, email" value="<?= htmlspecialchars($search) ?>">
        </div>
        
        <!-- Submit Button -->
        <div class="col align-items-end justify-content-end align-self-end">
          <button type="submit" class="btn btn-primary">Apply</button>
          <a href="payment-settings.php" class="btn btn-secondary ms-2">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Transactions Table -->
  <div class="card">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col">
          <h5 class="mb-0">Transactions List</h5>
        </div>
        <?php if(!empty($transactions)): ?>
        <div class="col-auto">
          <a href="#" class="btn btn-sm btn-outline-coral" onclick="exportTransactions()">
            <i class='bx bx-export me-1'></i> Export
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-items-center mb-0">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                Transaction ID
                <i class='bx bx-sort sort-icon'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Customer
                <i class='bx bx-sort sort-icon'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Total
                <i class='bx bx-sort sort-icon'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Date
                <i class='bx bx-caret-down sort-icon active-sort'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Status
                <i class='bx bx-sort sort-icon'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                Payment
                <i class='bx bx-sort sort-icon'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(!empty($transactions)) {
              foreach($transactions as $transaction) {
                // Define status badge
                $status_badge = '';
                switch($transaction['status']) {
                  case 'pending':
                    $status_badge = '<span class="badge bg-warning">Pending</span>';
                    break;
                  case 'paid':
                    $status_badge = '<span class="badge bg-success">Paid</span>';
                    break;
                  case 'failed':
                    $status_badge = '<span class="badge bg-danger">Failed</span>';
                    break;
                  case 'refunded':
                    $status_badge = '<span class="badge bg-info">Refunded</span>';
                    break;
                  default:
                    $status_badge = '<span class="badge bg-secondary">Unknown</span>';
                }
                
                // Format date
                $transaction_date = date('M d, Y', strtotime($transaction['created_at']));
                
                // Generate table row
                echo "<tr>
                <td class='ps-3 align-middle'>
                  <p class='font-weight-bold mb-0'>#".$transaction['payment_id']."</p>
                </td>
                <td class='align-middle'>
                  <div class='d-flex px-2 py-1'>
                    <div class='d-flex flex-column justify-content-center'>
                      <h6 class='mb-0 text-sm'>".htmlspecialchars($transaction['customer_name'] ?? 'N/A')."</h6>
                      <p class='text-xs text-secondary mb-0'>".htmlspecialchars($transaction['email'] ?? 'N/A')."</p>
                    </div>
                  </div>
                </td>
                <td class='align-middle'>
                  <p class='font-weight-bold mb-0'>₱".number_format($transaction['amount'], 2)."</p>
                </td>
                <td class='align-middle'>
                  <p class='text-secondary mb-0'>".$transaction_date."</p>
                </td>
                <td class='align-middle'>
                  ".$status_badge."
                </td>
                <td class='align-middle'>
                  <p class='font-weight-bold mb-0'>".ucfirst($transaction['payment_method'] ?? 'paymongo')."</p>
                </td>
                <td class='align-middle text-center'>
                    <a href='#' class='btn text-dark px-2 mb-0' data-bs-toggle='modal' data-bs-target='#transactionModal".$transaction['id']."'>
                      <i class='bx bx-info-circle bx-sm'></i>
                    </a>
                  </td>
                </tr>";
                
                // Transaction Details Modal
                echo "<div class='modal fade' id='transactionModal".$transaction['id']."' tabindex='-1' aria-labelledby='transactionModalLabel".$transaction['id']."' aria-hidden='true'>
                  <div class='modal-dialog modal-lg'>
                    <div class='modal-content'>
                      <div class='modal-header'>
                        <h5 class='modal-title' id='transactionModalLabel".$transaction['id']."'>
                          Transaction Details
                        </h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                      </div>
                      <div class='modal-body'>
                        <div class='row'>
                          <div class='col-md-6'>
                            <h6>Transaction Information</h6>
                            <table class='table table-sm'>
                              <tr>
                                <th>Transaction ID:</th>
                                <td>".$transaction['payment_id']."</td>
                              </tr>
                              <tr>
                                <th>Amount:</th>
                                <td>₱".number_format($transaction['amount'], 2)."</td>
                              </tr>
                              <tr>
                                <th>Description:</th>
                                <td>".htmlspecialchars($transaction['description'] ?? 'N/A')."</td>
                              </tr>
                              <tr>
                                <th>Payment Method:</th>
                                <td>
                                  ".ucfirst($transaction['payment_method'] ?? 'paymongo')."
                                </td>
                              </tr>
                              <tr>
                                <th>Status:</th>
                                <td>
                                  ".$status_badge."
                                </td>
                              </tr>
                              <tr>
                                <th>Date:</th>
                                <td>".date('M j, Y, g:i a', strtotime($transaction['created_at']))."</td>
                              </tr>
                            </table>
                          </div>
                          <div class='col-md-6'>
                            <h6>Customer Information</h6>
                            <table class='table table-sm'>
                              <tr>
                                <th>Name:</th>
                                <td>".htmlspecialchars($transaction['customer_name'] ?? 'N/A')."</td>
                              </tr>
                              <tr>
                                <th>Email:</th>
                                <td>".htmlspecialchars($transaction['email'] ?? 'N/A')."</td>
                              </tr>
                              <tr>
                                <th>Phone:</th>
                                <td>".htmlspecialchars($transaction['phone'] ?? 'N/A')."</td>
                              </tr>
                            </table>
                          </div>
                        </div>";
                        
                        if ($transaction['status'] == 'refunded') {
                          echo "<div class='mt-4'>
                            <h6>Refund Information</h6>
                            <table class='table table-sm'>
                              <tr>
                                <th>Refund ID:</th>
                                <td>".$transaction['refund_id']."</td>
                              </tr>
                              <tr>
                                <th>Refund Amount:</th>
                                <td>₱".number_format($transaction['refund_amount'], 2)."</td>
                              </tr>
                              <tr>
                                <th>Refund Reason:</th>
                                <td>".htmlspecialchars($transaction['refund_reason'] ?? 'N/A')."</td>
                              </tr>
                              <tr>
                                <th>Refunded At:</th>
                                <td>".date('M j, Y, g:i a', strtotime($transaction['refunded_at']))."</td>
                              </tr>
                            </table>
                          </div>";
                        }
                        
                      echo "</div>
                      <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>";
                        
                        if ($transaction['status'] == 'paid') {
                          echo "<button type='button' class='btn btn-warning' onclick='processRefund(\"".$transaction['payment_id']."\", ".$transaction['id'].", ".$transaction['amount'].")'>
                            <i class='bx bx-money-withdraw'></i> Process Refund
                          </button>";
                        }
                        
                      echo "</div>
                    </div>
                  </div>
                </div>";
              }
            } else {
              echo "<tr><td colspan='7' class='text-center py-4'>No transactions found. Transactions will appear here when customers make payments.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</main>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="refundForm" method="post" action="process_refund.php">
        <div class="modal-body">
          <input type="hidden" id="refund_payment_id" name="payment_id">
          <input type="hidden" id="refund_transaction_id" name="transaction_id">
          
          <div class="mb-3">
            <label for="refund_amount" class="form-label">Refund Amount</label>
            <div class="input-group">
              <span class="input-group-text">₱</span>
              <input type="number" class="form-control" id="refund_amount" name="refund_amount" step="0.01" required>
            </div>
            <div class="form-text">Enter the amount to refund. Maximum is the original transaction amount.</div>
          </div>
          
          <div class="mb-3">
            <label for="refund_reason" class="form-label">Reason for Refund</label>
            <textarea class="form-control" id="refund_reason" name="refund_reason" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Process Refund</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function exportTransactions() {
    // Implement export functionality
    alert('Export functionality will be implemented here');
  }
  
  function processRefund(paymentId, transactionId, amount) {
    // Set values in the refund modal
    document.getElementById('refund_payment_id').value = paymentId;
    document.getElementById('refund_transaction_id').value = transactionId;
    document.getElementById('refund_amount').value = amount;
    document.getElementById('refund_amount').max = amount;
    
    // Show the refund modal
    var refundModal = new bootstrap.Modal(document.getElementById('refundModal'));
    refundModal.show();
  }
</script>

<style>
  .sortable {
    position: relative;
    white-space: nowrap;
  }
  
  .sort-icon {
    font-size: 0.85rem;
    margin-left: 5px;
    vertical-align: middle;
    opacity: 0.5;
    transition: opacity 0.2s ease;
  }
  
  .sort-icon.active-sort {
    opacity: 1;
    color: #eb5d1e;
  }
  
  th:hover .sort-icon {
    opacity: 1;
  }
</style>
</body>
</html>