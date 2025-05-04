<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

// Include database connection
include('config/dbcon.php');

// Set default filter values
$status_filter = $_GET['status'] ?? 'all';
$date_filter = $_GET['date'] ?? 'all';
$search = $_GET['search'] ?? '';
$payment_source = 'paymongo'; // Always use Paymongo as the source

// Set sorting parameters
$sort_column = $_GET['sort'] ?? 'created_at';
$sort_direction = $_GET['direction'] ?? 'DESC';

// Validate sort column to prevent SQL injection
$allowed_columns = ['order_id', 'customer_name', 'total_amount', 'created_at', 'status', 'payment_method'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'created_at';
}

// Validate sort direction
$sort_direction = strtoupper($sort_direction) == 'ASC' ? 'ASC' : 'DESC';

// Function to fetch data from Paymongo API using the provided curl configuration
function fetchPaymongoPayments() {
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.paymongo.com/v1/payments?limit=100",
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

// Fetch payment data from Paymongo API
$paymongo_data = fetchPaymongoPayments();

// Apply filters to the Paymongo data
$filtered_data = ["data" => []];

if (!empty($paymongo_data['data'])) {
    foreach ($paymongo_data['data'] as $payment) {
        $attrs = $payment['attributes'];
        $include_payment = true;
        
        // Apply status filter
        if ($status_filter != 'all') {
            // Map Paymongo status to your system's status
            $payment_status = $attrs['status'];
            if ($payment_status == 'paid' && $status_filter != 'delivered') {
                $include_payment = false;
            } elseif ($payment_status != 'paid' && $status_filter != 'pending') {
                $include_payment = false;
            }
        }
        
        // Apply date filter
        if ($date_filter != 'all') {
            $payment_date = $attrs['created_at'];
            $today = strtotime('today');
            
            if ($date_filter == 'today' && $payment_date < $today) {
                $include_payment = false;
            } elseif ($date_filter == 'week' && $payment_date < strtotime('-7 days')) {
                $include_payment = false;
            } elseif ($date_filter == 'month' && $payment_date < strtotime('-30 days')) {
                $include_payment = false;
            }
        }
        
        // Apply search filter
        if (!empty($search)) {
            $customer_name = strtolower($attrs['billing']['name']);
            $customer_email = strtolower($attrs['billing']['email']);
            $payment_id = strtolower($payment['id']);
            $reference_number = isset($attrs['metadata']['reference_number']) ? 
                strtolower($attrs['metadata']['reference_number']) : '';
            
            $search_term = strtolower($search);
            
            if (strpos($customer_name, $search_term) === false && 
                strpos($customer_email, $search_term) === false && 
                strpos($payment_id, $search_term) === false &&
                strpos($reference_number, $search_term) === false) {
                $include_payment = false;
            }
        }
        
        // Include payment if it passes all filters
        if ($include_payment) {
            $filtered_data['data'][] = $payment;
        }
    }
}

// Sort the filtered data
if (!empty($filtered_data['data'])) {
    usort($filtered_data['data'], function($a, $b) use ($sort_column, $sort_direction) {
        $a_attrs = $a['attributes'];
        $b_attrs = $b['attributes'];
        
        switch($sort_column) {
            case 'order_id':
                $a_val = $a['id'];
                $b_val = $b['id'];
                break;
            case 'customer_name':
                $a_val = $a_attrs['billing']['name'];
                $b_val = $b_attrs['billing']['name'];
                break;
            case 'total_amount':
                $a_val = $a_attrs['amount'];
                $b_val = $b_attrs['amount'];
                break;
            case 'created_at':
                $a_val = $a_attrs['created_at'];
                $b_val = $b_attrs['created_at'];
                break;
            case 'status':
                $a_val = $a_attrs['status'];
                $b_val = $b_attrs['status'];
                break;
            case 'payment_method':
                $a_val = isset($a_attrs['source']['type']) ? $a_attrs['source']['type'] : '';
                $b_val = isset($b_attrs['source']['type']) ? $b_attrs['source']['type'] : '';
                break;
            default:
                $a_val = $a_attrs['created_at'];
                $b_val = $b_attrs['created_at'];
        }
        
        if ($a_val == $b_val) {
            return 0;
        }
        
        // Sort based on direction
        if ($sort_direction == 'ASC') {
            return ($a_val < $b_val) ? -1 : 1;
        } else {
            return ($a_val > $b_val) ? -1 : 1;
        }
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Paymongo Payments | Beyond Doubt Clothing</title>
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
  <!-- Header and Refresh Button -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="mb-0">Paymongo Payments</h2>
      <p class="text-muted">Displaying payments processed through Paymongo</p>
    </div>
    <div class="col-auto">
      <a href="?<?= http_build_query($_GET) ?>" class="btn btn-sm btn-outline-primary">
        <i class='bx bx-refresh me-1'></i> Refresh Data
      </a>
    </div>
  </div>

  <!-- Filter Card -->
  <div class="card mb-4">
    <div class="card-body p-3">
      <form method="GET" action="" class="row g-3 align-items-center">
        <!-- Date Filter -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="date" class="form-label">Date Range</label>
          <select name="date" id="date" class="form-select">
            <option value="all" <?= $date_filter == 'all' ? 'selected' : '' ?>>All Time</option>
            <option value="today" <?= $date_filter == 'today' ? 'selected' : '' ?>>Today</option>
            <option value="week" <?= $date_filter == 'week' ? 'selected' : '' ?>>Last 7 Days</option>
            <option value="month" <?= $date_filter == 'month' ? 'selected' : '' ?>>Last 30 Days</option>
          </select>
        </div>
        
        <!-- Search -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="search" class="form-label">Search</label>
          <input type="text" class="form-control" id="search" name="search" placeholder="Search by name, email, reference #" value="<?= htmlspecialchars($search) ?>">
        </div>
        
        <!-- Submit Button -->
        <div class="col align-items-end justify-content-end align-self-end">
          <button type="submit" class="btn btn-primary">Apply</button>
          <a href="orders.php" class="btn btn-secondary ms-2">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Orders Table -->
  <div class="card">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col">
          <h5 class="mb-0">Payments List</h5>
        </div>
        <?php if (!empty($filtered_data['data'])): ?>
        <div class="col-auto">
          <a href="export-payments.php<?= !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ?>" class="btn btn-sm btn-outline-coral">
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
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 sortable" data-sort="order_id">
                Payment ID / Reference
                <i class='bx <?= $sort_column == 'order_id' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'order_id' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="customer_name">
                Customer
                <i class='bx <?= $sort_column == 'customer_name' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'customer_name' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="total_amount">
                Amount
                <i class='bx <?= $sort_column == 'total_amount' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'total_amount' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="created_at">
                Date
                <i class='bx <?= $sort_column == 'created_at' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'created_at' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="status">
                Status
                <i class='bx <?= $sort_column == 'status' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'status' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="payment_method">
                Method
                <i class='bx <?= $sort_column == 'payment_method' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'payment_method' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(!empty($filtered_data['data'])) {
              // Display Paymongo payments
              foreach($filtered_data['data'] as $payment) {
                $attrs = $payment['attributes'];
                
                // Format amount (convert from cents to actual amount)
                $amount = number_format($attrs['amount'] / 100, 2);
                
                // Format date
                $payment_date = date('M d, Y', $attrs['created_at']);
                
                // Get payment method
                $payment_method = ucfirst($attrs['source']['type'] ?? 'Unknown');
                
                // Set status badge
                $status_badge = '';
                switch($attrs['status']) {
                  case 'paid':
                    $status_badge = '<span class="badge bg-success">Paid</span>';
                    break;
                  case 'pending':
                    $status_badge = '<span class="badge bg-warning">Pending</span>';
                    break;
                  case 'failed':
                    $status_badge = '<span class="badge bg-danger">Failed</span>';
                    break;
                  default:
                    $status_badge = '<span class="badge bg-secondary">'.$attrs['status'].'</span>';
                }
                
                // Generate table row for Paymongo payment
                echo "<tr>
                <td class='ps-3 align-middle'>
                  <p class='font-weight-bold mb-0 text-xs'>".$payment['id']."</p>
                  <p class='text-xs text-secondary mb-0'>".($attrs['metadata']['reference_number'] ?? 'N/A')."</p>
                </td>
                <td class='align-middle'>
                  <div class='d-flex px-2 py-1'>
                    <div class='d-flex flex-column justify-content-center'>
                      <h6 class='mb-0 text-sm'>".$attrs['billing']['name']."</h6>
                      <p class='text-xs text-secondary mb-0'>".$attrs['billing']['email']."</p>
                    </div>
                  </div>
                </td>
                <td class='align-middle'>
                  <p class='font-weight-bold mb-0'>₱".$amount."</p>
                </td>
                <td class='align-middle'>
                  <p class='text-secondary mb-0'>".$payment_date."</p>
                </td>
                <td class='align-middle'>
                  ".$status_badge."
                </td>
                <td class='align-middle'>
                  <p class='font-weight-bold mb-0'>".$payment_method."</p>
                </td>
                <td class='align-middle text-center'>
                  <a href='#' class='btn text-dark px-2 mb-0' data-bs-toggle='modal' data-bs-target='#paymentDetailsModal' 
                    data-paymentid='".$payment['id']."'>
                    <i class='bx bx-info-circle bx-sm'></i>
                  </a>
                </td>
                </tr>";
              }
            } else {
              echo "<tr><td colspan='7' class='text-center py-4'>No payments found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentDetailsModalLabel">Payment Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="payment-details-content" class="p-3">
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading payment details...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle payment details modal
    const paymentDetailsModal = document.getElementById('paymentDetailsModal');
    if (paymentDetailsModal) {
      paymentDetailsModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const paymentId = button.getAttribute('data-paymentid');
        const contentDiv = document.getElementById('payment-details-content');
        
        // Fetch payment details
        fetch(`fetch_payment_details.php?payment_id=${paymentId}`)
          .then(response => response.text())
          .then(data => {
            contentDiv.innerHTML = data;
          })
          .catch(error => {
            contentDiv.innerHTML = `<div class="alert alert-danger">Error loading payment details: ${error}</div>`;
          });
      });
    }
    
    // Add sorting functionality
    const sortableHeaders = document.querySelectorAll('.sortable');
    sortableHeaders.forEach(header => {
      header.style.cursor = 'pointer';
      
      header.addEventListener('click', function() {
        const column = this.getAttribute('data-sort');
        let direction = 'ASC';
        
        // If this column is already sorted, toggle direction
        if (column === '<?= $sort_column ?>') {
          direction = '<?= $sort_direction ?>' === 'ASC' ? 'DESC' : 'ASC';
        }
        
        // Create URL with current filters plus new sort parameters
        let url = new URL(window.location.href);
        let params = new URLSearchParams(url.search);
        
        // Update or add sort parameters
        params.set('sort', column);
        params.set('direction', direction);
        
        // Redirect to the new URL
        window.location.href = `${url.pathname}?${params.toString()}`;
      });
    });
  });
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
  
  .sortable:hover .sort-icon {
    opacity: 1;
  }
</style>
</body>
</html>