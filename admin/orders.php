<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

// Include database connection
include('config/dbcon.php');

// Process bulk actions if submitted
if(isset($_POST['bulk_action']) && isset($_POST['order_ids'])) {
    $action = $_POST['bulk_action'];
    $order_ids = $_POST['order_ids'];
    
    if(!empty($order_ids) && in_array($action, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
        $ids = implode(',', array_map('intval', $order_ids));
        $status = mysqli_real_escape_string($conn, $action);
        
        $update_query = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id IN ($ids)";
        mysqli_query($conn, $update_query);
        
        // Redirect to avoid form resubmission
        header("Location: orders.php?msg=bulk_updated&count=".count($order_ids));
        exit();
    }
}

// Set default filter values
$status_filter = $_GET['status'] ?? 'all';
$date_filter = $_GET['date'] ?? 'all';
$search = $_GET['search'] ?? '';

// Set sorting parameters
$sort_column = $_GET['sort'] ?? 'created_at';
$sort_direction = $_GET['direction'] ?? 'DESC';

// Validate sort column to prevent SQL injection
$allowed_columns = ['id', 'firstname', 'lastname', 'total_amount', 'created_at', 'status', 'payment_method', 'reference_number'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'created_at';
}

// Validate sort direction
$sort_direction = strtoupper($sort_direction) == 'ASC' ? 'ASC' : 'DESC';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$records_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$offset = ($page - 1) * $records_per_page;

// Start building the SQL query
$query = "SELECT * FROM orders";
$count_query = "SELECT COUNT(*) AS total FROM orders";
$where_conditions = [];
$params = [];
$types = "";

// Apply status filter
if ($status_filter != 'all') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Apply date filter
if ($date_filter != 'all') {
    switch($date_filter) {
        case 'today':
            $where_conditions[] = "created_at >= CURDATE()";
            break;
        case 'yesterday':
            $where_conditions[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND created_at < CURDATE()";
            break;
        case 'week':
            $where_conditions[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $where_conditions[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'custom':
            if(isset($_GET['date_from']) && isset($_GET['date_to'])) {
                $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);
                $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);
                $where_conditions[] = "created_at >= '$date_from' AND created_at <= '$date_to 23:59:59'";
            }
            break;
    }
}

// Apply search filter
if (!empty($search)) {
    $search_term = '%' . $search . '%';
    $where_conditions[] = "(firstname LIKE ? OR lastname LIKE ? OR email LIKE ? OR reference_number LIKE ? OR payment_id LIKE ? OR id LIKE ?)";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term, $search_term]);
    $types .= "ssssss";
}

// Combine where conditions
if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
    $count_query .= " WHERE " . implode(" AND ", $where_conditions);
}

// Add sorting
$query .= " ORDER BY $sort_column $sort_direction";

// Add pagination
$query .= " LIMIT $offset, $records_per_page";

// Execute count query to get total records
$stmt = mysqli_prepare($conn, $count_query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$count_result = mysqli_stmt_get_result($stmt);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Execute main query
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Handle messages
$message = '';
if(isset($_GET['msg'])) {
    switch($_GET['msg']) {
        case 'bulk_updated':
            $count = isset($_GET['count']) ? intval($_GET['count']) : 0;
            $message = '<div class="alert alert-success">Successfully updated ' . $count . ' orders.</div>';
            break;
        case 'status_updated':
            $message = '<div class="alert alert-success">Order status updated successfully.</div>';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Orders Management | Beyond Doubt Clothing</title>
  <!-- Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  
  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/sidebar.css">
  <link rel="stylesheet" href="assets/css/orders.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
  <!-- Header and Refresh Button -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="mb-0">Orders Management</h2>
      <p class="text-muted">Manage and track customer orders</p>
    </div>
    <div class="col-auto">
      <a href="?<?= http_build_query($_GET) ?>" class="btn btn-sm btn-outline-primary">
        <i class='bx bx-refresh me-1'></i> Refresh Data
      </a>
    </div>
  </div>
  
  <?= $message ?>

  <!-- Filter Card -->
  <div class="card mb-4">
    <div class="card-body p-3">
      <form method="GET" action="" class="row g-3 align-items-center">
        <!-- Status Filter -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="status" class="form-label">Order Status</label>
          <select name="status" id="status" class="form-select">
            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Statuses</option>
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
          <select name="date" id="date" class="form-select" onchange="toggleCustomDateFields()">
            <option value="all" <?= $date_filter == 'all' ? 'selected' : '' ?>>All Time</option>
            <option value="today" <?= $date_filter == 'today' ? 'selected' : '' ?>>Today</option>
            <option value="yesterday" <?= $date_filter == 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
            <option value="week" <?= $date_filter == 'week' ? 'selected' : '' ?>>Last 7 Days</option>
            <option value="month" <?= $date_filter == 'month' ? 'selected' : '' ?>>Last 30 Days</option>
            <option value="custom" <?= $date_filter == 'custom' ? 'selected' : '' ?>>Custom Range</option>
          </select>
        </div>
        
        <!-- Custom Date Range Fields (initially hidden) -->
        <div class="col-lg-3 col-md-4 col-sm-6 custom-date-field" style="<?= $date_filter == 'custom' ? '' : 'display: none;' ?>">
          <label for="date_from" class="form-label">From Date</label>
          <input type="date" class="form-control datepicker" id="date_from" name="date_from" value="<?= $_GET['date_from'] ?? date('Y-m-d', strtotime('-7 days')) ?>">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 custom-date-field" style="<?= $date_filter == 'custom' ? '' : 'display: none;' ?>">
          <label for="date_to" class="form-label">To Date</label>
          <input type="date" class="form-control datepicker" id="date_to" name="date_to" value="<?= $_GET['date_to'] ?? date('Y-m-d') ?>">
        </div>
        
        <!-- Records Per Page -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="per_page" class="form-label">Records Per Page</label>
          <select name="per_page" id="per_page" class="form-select">
            <option value="10" <?= $records_per_page == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $records_per_page == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $records_per_page == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= $records_per_page == 100 ? 'selected' : '' ?>>100</option>
          </select>
        </div>
        
        <!-- Search -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="search" class="form-label">Search</label>
          <input type="text" class="form-control" id="search" name="search" placeholder="Search by name, email, reference #" value="<?= htmlspecialchars($search) ?>">
        </div>
        
        <!-- Submit Button -->
        <div class="col-12 d-flex justify-content-end align-items-end mt-3">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
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
          <p class="text-muted mb-0 small">Showing <?= mysqli_num_rows($result) ?> of <?= $total_records ?> orders</p>
        </div>
        <div class="col-auto">
          <div class="dropdown d-inline-block me-2">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class='bx bx-export me-1'></i> Export
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
              <li><a class="dropdown-item" href="functions/orders/export-orders.php<?= !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'].'&format=csv' : '?format=csv' ?>">CSV Format</a></li>
              <li><a class="dropdown-item" href="functions/orders/export-orders.php<?= !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'].'&format=pdf' : '?format=pdf' ?>">PDF Format</a></li>
            </ul>
          </div>
          
          <button type="button" class="btn btn-sm btn-outline-coral" id="bulkActionBtn" disabled>
            <i class='bx bx-list-check me-1'></i> Bulk Actions
          </button>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <form method="POST" action="" id="orderBulkForm">
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="ps-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllOrders">
                  </div>
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="id">
                  Order ID / Reference
                  <i class='bx <?= $sort_column == 'id' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'id' ? 'active-sort' : '' ?>'></i>
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable" data-sort="firstname">
                  Customer
                  <i class='bx <?= $sort_column == 'firstname' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'firstname' ? 'active-sort' : '' ?>'></i>
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
                  Payment
                  <i class='bx <?= $sort_column == 'payment_method' ? ($sort_direction == 'ASC' ? 'bx-caret-up' : 'bx-caret-down') : 'bx-sort' ?> sort-icon <?= $sort_column == 'payment_method' ? 'active-sort' : '' ?>'></i>
                </th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if(mysqli_num_rows($result) > 0) {
                while($order = mysqli_fetch_assoc($result)) {
                  // Format amount
                  $amount = number_format($order['total_amount'], 2);
                  
                  // Format date
                  $order_date = date('M d, Y', strtotime($order['created_at']));
                  
                  // Set status badge
                  $status_badge = '';
                  switch($order['status']) {
                    case 'processing':
                      $status_badge = '<span class="badge bg-primary">Processing</span>';
                      break;
                    case 'pending':
                      $status_badge = '<span class="badge bg-warning">Pending</span>';
                      break;
                    case 'shipped':
                      $status_badge = '<span class="badge bg-info">Shipped</span>';
                      break;
                    case 'delivered':
                      $status_badge = '<span class="badge bg-success">Delivered</span>';
                      break;
                    case 'cancelled':
                      $status_badge = '<span class="badge bg-danger">Cancelled</span>';
                      break;
                    default:
                      $status_badge = '<span class="badge bg-secondary">'.$order['status'].'</span>';
                  }
                  
                  // Generate table row
                  echo "<tr>
                  <td class='ps-3'>
                    <div class='form-check'>
                      <input class='form-check-input order-checkbox' type='checkbox' name='order_ids[]' value='".$order['id']."'>
                    </div>
                  </td>
                  <td class='align-middle'>
                    <p class='font-weight-bold mb-0 text-xs'>#".$order['id']."</p>
                    <p class='text-xs text-secondary mb-0'>".($order['reference_number'] ?? 'N/A')."</p>
                  </td>
                  <td class='align-middle'>
                    <div class='d-flex px-2 py-1'>
                      <div class='d-flex flex-column justify-content-center'>
                        <h6 class='mb-0 text-sm'>".$order['firstname']." ".$order['lastname']."</h6>
                        <p class='text-xs text-secondary mb-0'>".$order['email']."</p>
                      </div>
                    </div>
                  </td>
                  <td class='align-middle'>
                    <p class='font-weight-bold mb-0'>₱".$amount."</p>
                  </td>
                  <td class='align-middle'>
                    <p class='text-secondary mb-0'>".$order_date."</p>
                  </td>
                  <td class='align-middle'>
                    ".$status_badge."
                  </td>
                  <td class='align-middle'>
                    <p class='font-weight-bold mb-0'>".$order['payment_method']."</p>
                  </td>
                  <td class='align-middle text-center'>
                    <div class='btn-group'>
                      <select class='form-select form-select-sm quick-status-update' data-order-id='".$order['id']."' aria-label='Quick Status Update' style='min-width: 120px;'>
                        <option value=''>Update Status</option>
                        <option value='pending' ".($order['status'] == 'pending' ? 'selected' : '').">Pending</option>
                        <option value='processing' ".($order['status'] == 'processing' ? 'selected' : '').">Processing</option>
                        <option value='shipped' ".($order['status'] == 'shipped' ? 'selected' : '').">Shipped</option>
                        <option value='delivered' ".($order['status'] == 'delivered' ? 'selected' : '').">Delivered</option>
                        <option value='cancelled' ".($order['status'] == 'cancelled' ? 'selected' : '').">Cancelled</option>
                      </select>
                      <a href='#' class='btn text-primary px-2 mb-0' data-bs-toggle='modal' data-bs-target='#orderDetailsModal' 
                        data-orderid='".$order['id']."'>
                        <i class='bx bx-info-circle bx-sm'></i>
                      </a>
                    </div>
                  </td>
                  </tr>";
                }
              } else {
                echo "<tr><td colspan='8' class='text-center py-4'>No orders found</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        
        <!-- Bulk Action Modal -->
        <div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">Bulk Update Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Select an action to apply to <span id="selectedOrderCount">0</span> selected orders:</p>
                <div class="form-group">
                  <select name="bulk_action" class="form-select" required>
                    <option value="">Choose Action...</option>
                    <option value="pending">Mark as Pending</option>
                    <option value="processing">Mark as Processing</option>
                    <option value="shipped">Mark as Shipped</option>
                    <option value="delivered">Mark as Delivered</option>
                    <option value="cancelled">Mark as Cancelled</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply to Selected Orders</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <?php if ($total_pages > 1): ?>
    <div class="card-footer py-3">
      <nav>
        <ul class="pagination justify-content-center mb-0">
          <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page-1])) ?>" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          
          <?php
          // Show limited page numbers with ellipsis
          $start_page = max(1, $page - 2);
          $end_page = min($total_pages, $page + 2);
          
          if ($start_page > 1) {
              echo '<li class="page-item"><a class="page-link" href="?'.http_build_query(array_merge($_GET, ['page' => 1])).'">1</a></li>';
              if ($start_page > 2) {
                  echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
              }
          }
          
          for ($i = $start_page; $i <= $end_page; $i++) {
              echo '<li class="page-item '.($page == $i ? 'active' : '').'">
                  <a class="page-link" href="?'.http_build_query(array_merge($_GET, ['page' => $i])).'">'.$i.'</a>
                </li>';
          }
          
          if ($end_page < $total_pages) {
              if ($end_page < $total_pages - 1) {
                  echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
              }
              echo '<li class="page-item"><a class="page-link" href="?'.http_build_query(array_merge($_GET, ['page' => $total_pages])).'">'.$total_pages.'</a></li>';
          }
          ?>
          
          <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page+1])) ?>" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderDetailsMo  dalLabel">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="order-details-content" class="p-3">
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading order details...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="print-order">Print Order</button>
      </div>
    </div>
  </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="assets/js/orders.js"></script>
</body>
</html>