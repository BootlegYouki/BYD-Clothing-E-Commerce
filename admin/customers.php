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
$date_filter = $_GET['date'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build the query based on filters
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
          (SELECT MAX(created_at) FROM orders WHERE user_id = u.id) as last_order_date
          FROM users u 
          WHERE u.role_as = 0"; // Only show regular customers (role_as = 0)

// Date filter
if($date_filter == 'today') {
    $query .= " AND DATE(u.created_at) = CURDATE()";
} elseif($date_filter == 'week') {
    $query .= " AND u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif($date_filter == 'month') {
    $query .= " AND u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

// Search functionality
if(!empty($search)) {
    $query .= " AND (u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%' OR u.email LIKE '%$search%' OR u.phone_number LIKE '%$search%')";
}

// Order by newest first
$query .= " ORDER BY u.created_at DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Customers | Beyond Doubt Clothing</title>
  <!-- Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  
  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- LEAFLET CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
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
        <!-- Date Filter -->
        <div class="col-lg-3 col-md-4 col-sm-6">
          <label for="date" class="form-label">Registration Date</label>
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
          <input type="text" class="form-control" id="search" name="search" placeholder="Search by name, email, phone" value="<?= htmlspecialchars($search) ?>">
        </div>
        
        <!-- Submit Button -->
        <div class="col align-items-end justify-content-end align-self-end">
          <button type="submit" class="btn btn-primary">Apply</button>
          <a href="customers.php" class="btn btn-secondary ms-2">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Customer Statistics Row -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
      <div class="card">
        <div class="card-body py-3">
          <?php 
            $total_query = "SELECT COUNT(*) as total FROM users WHERE role_as = 0";
            $total_result = mysqli_query($conn, $total_query);
            $total_customers = mysqli_fetch_assoc($total_result)['total'];
          ?>
          <div class="d-flex align-items-center">
            <div class="icon-shape bg-gradient-primary shadow text-center">
              <i class="material-symbols-rounded opacity-10">group</i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">Total Customers</p>
              <h5 class="font-weight-bolder mb-0"><?= $total_customers ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
      <div class="card">
        <div class="card-body py-3">
          <?php 
            $new_query = "SELECT COUNT(*) as total FROM users WHERE role_as = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $new_result = mysqli_query($conn, $new_query);
            $new_customers = mysqli_fetch_assoc($new_result)['total'];
          ?>
          <div class="d-flex align-items-center">
            <div class="icon-shape bg-gradient-primary shadow text-center">
              <i class="material-symbols-rounded opacity-10">person_add</i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">New This Month</p>
              <h5 class="font-weight-bolder mb-0"><?= $new_customers ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
      <div class="card">
        <div class="card-body py-3">
          <?php 
            $repeat_query = "SELECT COUNT(DISTINCT user_id) as total FROM orders o 
                        JOIN users u ON o.user_id = u.id
                        WHERE u.role_as = 0 AND o.user_id IN 
                        (SELECT user_id FROM orders GROUP BY user_id HAVING COUNT(*) > 1)";
            $repeat_result = mysqli_query($conn, $repeat_query);
            $repeat_customers = mysqli_fetch_assoc($repeat_result)['total'];
          ?>
          <div class="d-flex align-items-center">
            <div class="icon-shape bg-gradient-primary shadow text-center">
              <i class="material-symbols-rounded opacity-10">repeat</i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">Repeat Customers</p>
              <h5 class="font-weight-bolder mb-0"><?= $repeat_customers ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
      <div class="card">
        <div class="card-body py-3">
          <?php 
            $average_query = "SELECT ROUND(AVG(o.total_amount), 2) as avg_value 
                          FROM orders o 
                          JOIN users u ON o.user_id = u.id 
                          WHERE u.role_as = 0";
            $average_result = mysqli_query($conn, $average_query);
            $average_order = mysqli_fetch_assoc($average_result)['avg_value'] ?? 0;
          ?>
          <div class="d-flex align-items-center">
            <div class="icon-shape bg-gradient-primary shadow text-center">
              <i class="material-symbols-rounded opacity-10">payments</i>
            </div>
            <div class="ms-3">
              <p class="text-sm mb-0 text-capitalize">Avg. Order Value</p>
              <h5 class="font-weight-bolder mb-0">₱<?= number_format($average_order, 2) ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Customers Table -->
  <div class="card">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col">
          <h5 class="mb-0">Customer List</h5>
        </div>
        <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="col-auto">
          <a href="export-customers.php<?= !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ?>" class="btn btn-sm btn-outline-coral">
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
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4">Customer</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Phone</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Joined Date</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last Order</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Orders</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(mysqli_num_rows($result) > 0) {
              while($row = mysqli_fetch_assoc($result)) {
                // Format dates
                $joined_date = date('M d, Y', strtotime($row['created_at']));
                $last_order_date = $row['last_order_date'] ? date('M d, Y', strtotime($row['last_order_date'])) : 'Never';
                
                // Generate table row
                echo "<tr>
                <td class='ps-3'>
                  <div class='d-flex px-2 py-1'>
                    <div class='d-flex flex-column justify-content-center'>
                      <h6 class='mb-0 text-sm'>".$row['firstname']." ".$row['lastname']."</h6>
                    </div>
                  </div>
                </td>
                <td>
                  <p class='text-sm font-weight-normal mb-0'>".$row['email']."</p>
                </td>
                <td>
                  <p class='text-sm font-weight-normal mb-0'>".(isset($row['phone_number']) && $row['phone_number'] ? $row['phone_number'] : 'Not provided')."</p>
                </td>
                <td>
                  <p class='text-sm text-secondary mb-0'>".$joined_date."</p>
                </td>
                <td>
                  <p class='text-sm text-secondary mb-0'>".$last_order_date."</p>
                </td>
                <td class='text-center'>
                  <p class='text-sm font-weight-normal mb-0'>".$row['order_count']."</p>
                </td>
                <td class='align-middle text-center'>
                  <div class='dropdown'>
                    <a href='#' class='text-secondary font-weight-bold text-xs' 
                       data-bs-toggle='dropdown' aria-expanded='false'>
                      <i class='material-symbols-rounded'>more_vert</i>
                    </a>
                    <ul class='dropdown-menu'>
                      <li><a class='dropdown-item' href='#' data-bs-toggle='modal' data-bs-target='#viewCustomerModal' 
                            data-customer-id='".$row['id']."'>View Details</a></li>
                      <li><a class='dropdown-item' href='orders.php?search=".$row['email']."'>View Orders</a></li>
                    </ul>
                  </div>
                </td>
                </tr>";
              }
            } else {
              echo "<tr><td colspan='7' class='text-center py-4'>No customers found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewCustomerModalLabel">Customer Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center mb-4">
          <div class="avatar rounded-circle bg-light mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
            <i class="material-symbols-rounded" style="font-size: 40px;">person</i>
          </div>
          <h5 class="mt-3 mb-0" id="customerName">Loading...</h5>
          <p class="text-muted small" id="customerEmail">loading@example.com</p>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label text-muted small">Phone Number</label>
            <p class="mb-0" id="customerPhone">Not provided</p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label text-muted small">Date Joined</label>
            <p class="mb-0" id="customerJoined">Jan 01, 2023</p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label text-muted small">Total Orders</label>
            <p class="mb-0" id="customerOrders">0</p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label text-muted small">Last Order Date</label>
            <p class="mb-0" id="customerLastOrder">Never</p>
          </div>
        </div>
        
        <hr>
        
        <div class="mb-3">
          <label class="form-label text-muted small">Default Shipping Address</label>
          <p class="mb-0" id="customerAddress">No address on file</p>
        </div>
        
        <!-- Customer Location Map -->
        <div id="customer-location-container" class="mt-3" style="display: none;">
          <label class="form-label text-muted small">Location</label>
          <div id="customer-map" class="rounded shadow-sm" style="height: 250px;"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <a href="#" id="viewOrdersBtn" class="btn btn-primary">View Orders</a>
      </div>
    </div>
  </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- LEAFLET SCRIPTS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Customer map variable
    let customerMap = null;
    let customerMarker = null;
    
    // Handle view customer modal
    const viewCustomerModal = document.getElementById('viewCustomerModal');
    if (viewCustomerModal) {
      viewCustomerModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const customerId = button.getAttribute('data-customer-id');
        
        // Reset modal content
        document.getElementById('customerName').textContent = 'Loading...';
        document.getElementById('customerEmail').textContent = 'loading@example.com';
        document.getElementById('customerPhone').textContent = 'Not provided';
        document.getElementById('customerJoined').textContent = 'Jan 01, 2023';
        document.getElementById('customerOrders').textContent = '0';
        document.getElementById('customerLastOrder').textContent = 'Never';
        document.getElementById('customerAddress').textContent = 'No address on file';
        document.getElementById('customer-location-container').style.display = 'none';
        
        // Fetch customer details
        fetch(`functions/get-customer-details.php?id=${customerId}`)
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              console.error('Error:', data.error);
              return;
            }
            
            // Update modal with customer details
            document.getElementById('customerName').textContent = `${data.firstname} ${data.lastname}`;
            document.getElementById('customerEmail').textContent = data.email;
            document.getElementById('customerPhone').textContent = data.phone_number || 'Not provided';
            document.getElementById('customerJoined').textContent = data.joined_date;
            document.getElementById('customerOrders').textContent = data.order_count;
            document.getElementById('customerLastOrder').textContent = data.last_order_date || 'Never';
            
            // Format address if available
            if (data.full_address) {
              let address = data.full_address;
              if (data.zipcode) address += ` ${data.zipcode}`;
              
              document.getElementById('customerAddress').textContent = address;
            } else {
              document.getElementById('customerAddress').textContent = 'No address on file';
            }
            
            // Update view orders link
            document.getElementById('viewOrdersBtn').href = `orders.php?search=${encodeURIComponent(data.email)}`;
            
            // Initialize and show map if latitude and longitude are available
            if (data.latitude && data.longitude) {
              document.getElementById('customer-location-container').style.display = 'block';
              
              // Add location coordinates text display
              const locationText = document.createElement('p');
              locationText.className = 'mb-2 text-secondary';
              locationText.innerHTML = `<strong>Coordinates:</strong> ${data.latitude}, ${data.longitude}`;
              document.getElementById('customer-location-container').prepend(locationText);
              
              // Initialize map if not already done
              if (!customerMap) {
                customerMap = L.map('customer-map').setView([data.latitude, data.longitude], 15);
                
                // Use HTTPS instead of HTTP for the tile layer to avoid mixed content warnings
                L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                  maxZoom: 20,
                  subdomains:['mt0','mt1','mt2','mt3']
                }).addTo(customerMap);
              } else {
                customerMap.setView([data.latitude, data.longitude], 15);
              }
              
              // Clear previous marker if it exists
              if (customerMarker) {
                customerMap.removeLayer(customerMarker);
              }
              
              // Add marker for customer location
              customerMarker = L.marker([data.latitude, data.longitude]).addTo(customerMap)
                .bindPopup(`${data.firstname} ${data.lastname}'s Location`);
                
              // Fix Leaflet display issue when map is initialized in a hidden container
              setTimeout(() => {
                customerMap.invalidateSize();
              }, 300);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      });
    }
    
    // Enable tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
</body>
</html>
