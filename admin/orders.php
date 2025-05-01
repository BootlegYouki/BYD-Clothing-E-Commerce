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

// Start building the base query
$query = "SELECT o.*, CONCAT(u.firstname, ' ', u.lastname) as customer_name FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE 1=1";
$params = [];
$types = "";

// Status filter
if($status_filter != 'all') {
    $query .= " AND o.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Date filter
if($date_filter == 'today') {
    $query .= " AND DATE(o.created_at) = CURDATE()";
} elseif($date_filter == 'week') {
    $query .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif($date_filter == 'month') {
    $query .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

// Search functionality with prepared statements
if(!empty($search)) {
    $query .= " AND (o.firstname LIKE ? OR o.lastname LIKE ? OR u.firstname LIKE ? OR u.lastname LIKE ? OR o.payment_method LIKE ? OR o.order_id LIKE ? OR o.email LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param]);
    $types .= "ssssssss";
}

// Order by the selected column and direction
$query .= " ORDER BY $sort_column $sort_direction";

// Prepare and execute statement
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    // Bind parameters if there are any
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Handle error
    $result = false;
    echo "Error preparing statement: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Orders | Beyond Doubt Clothing</title>
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
          <label for="status" class="form-label">Order Status</label>
          <select name="status" id="status" class="form-select">
            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Status</option>
            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= $status_filter == 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $status_filter == 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="delivered" <?= $status_filter == 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>
        
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
          <input type="text" class="form-control" id="search" name="search" placeholder="Search by order #, name" value="<?= htmlspecialchars($search) ?>">
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
          <h5 class="mb-0">Orders List</h5>
        </div>
        <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="col-auto">
          <a href="export-orders.php<?= !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ?>" class="btn btn-sm btn-outline-coral">
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
                Order ID
                <i class='bx <?= $sort_column == 'order_id' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'order_id' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="customer_name">
                Customer
                <i class='bx <?= $sort_column == 'customer_name' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'customer_name' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="total_amount">
                Total
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
                Payment
                <i class='bx <?= $sort_column == 'payment_method' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'payment_method' ? 'active-sort' : '' ?>'></i>
              </th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(mysqli_num_rows($result) > 0) {
              while($row = mysqli_fetch_assoc($result)) {
                // Define status badge
                $status_badge = '';
                switch($row['status']) {
                  case 'pending':
                    $status_badge = '<span class="badge bg-warning">Pending</span>';
                    break;
                  case 'processing':
                    $status_badge = '<span class="badge bg-info">Processing</span>';
                    break;
                  case 'shipped':
                    $status_badge = '<span class="badge bg-primary">Shipped</span>';
                    break;
                  case 'delivered':
                    $status_badge = '<span class="badge bg-success">Delivered</span>';
                    break;
                  case 'cancelled':
                    $status_badge = '<span class="badge bg-danger">Cancelled</span>';
                    break;
                  default:
                    $status_badge = '<span class="badge bg-secondary">Unknown</span>';
                }
                
                // Format date
                $order_date = date('M d, Y', strtotime($row['created_at']));
                
                // Generate table row
                echo "<tr>
                <td class='ps-3 align-middle'>
                  <p class='font-weight-bold mb-0'>#".$row['order_id']."</p>
                </td>
                <td class='align-middle'>
                  <div class='d-flex px-2 py-1'>
                    <div class='d-flex flex-column justify-content-center'>
                      <h6 class='mb-0 text-sm'>".$row['customer_name']."</h6>
                    </div>
                  </div>
                </td>
                <td class='align-middle'>
                  <p class='font-weight-bold mb-0'>₱".number_format($row['total_amount'], 2)."</p>
                </td>
                <td class='align-middle'>
                  <p class='text-secondary mb-0'>".$order_date."</p>
                </td>
                <td class='align-middle'>
                  ".$status_badge."
                </td>
                <td class='align-middle'>
                  <p class='font-weight-bold mb-0'>".$row['payment_method']."</p>
                </td>
                <td class='align-middle text-center'>
                    <a href='#' class='btn text-dark px-2 mb-0' data-bs-toggle='modal' data-bs-target='#updateStatusModal' 
                      data-orderid='".$row['order_id']."' data-status='".$row['status']."'>
                      <i class='bx bx-edit bx-sm'></i>
                    </a>
                  </td>
                </tr>";
              }
            } else {
              echo "<tr><td colspan='7' class='text-center py-4'>No orders found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="code/order-code.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="order_id" id="order_id" value="">
          <div class="form-group">
            <label for="order_status" class="form-label">Status</label>
            <select class="form-select" id="order_status" name="order_status" required>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="update_order_status_btn" class="btn btn-primary">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Handle update status modal
  document.addEventListener('DOMContentLoaded', function() {
    const updateStatusModal = document.getElementById('updateStatusModal');
    if (updateStatusModal) {
      updateStatusModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const orderId = button.getAttribute('data-orderid');
        const currentStatus = button.getAttribute('data-status');
        
        const orderIdInput = updateStatusModal.querySelector('#order_id');
        const statusSelect = updateStatusModal.querySelector('#order_status');
        
        orderIdInput.value = orderId;
        statusSelect.value = currentStatus;
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