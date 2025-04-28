<?php
require_once '../admin/config/dbcon.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: index.php');
    exit();
}

// Initialize notification variables
$notification = null;
$notificationType = null;

// Check for success/error messages in URL parameters
if (isset($_GET['success'])) {
    $notification = urldecode($_GET['message'] ?? 'Changes saved successfully!');
    $notificationType = 'success';
} elseif (isset($_GET['error'])) {
    $notification = urldecode($_GET['message'] ?? 'An error occurred. Please try again.');
    $notificationType = 'danger';
}

// Check for messages in session (alternative approach)
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification']['message'];
    $notificationType = $_SESSION['notification']['type'];
    unset($_SESSION['notification']);
}

// Get user data
$user_id = $_SESSION['auth_user']['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user orders
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$orders = [];
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>My Profile | Beyond Doubt Clothing</title> 
    <!-- BOOTSTRAP CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/logo/logo.ico" type="image/x-icon">
    <!-- UTILITY CSS  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- ICONSCSS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet"href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- LEAFLET CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/shopcart.css">
    <link rel="stylesheet" href="css/assistant.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <!-- CHATBOT  -->
    <?php include 'includes/assistant.php'; ?>
    <!-- SHOPPING CART MODAL  -->
    <?php include 'includes/shopcart.php'; ?>
    <!-- LOGOUT MODAL  -->
    <?php include 'includes/logout.php'; ?>
    
    <!-- PROFILE CONTENT -->
    <div class="container pt-5">
        <div class="row pt-5 mt-5">
            <!-- Notification Area -->
            <?php if ($notification): ?>
            <div class="col-12 mb-4">
                <div class="alert alert-<?php echo $notificationType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($notification); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            <?php endif; ?>
            <!-- Profile Sidebar -->
            <div class="col-md-3">
                <div class="card profile-sidebar mb-4">
                    <div class="card-body text-center">
                        <div class="profile-icon mb-3">
                            <i class="fa fa-user-circle fa-5x"></i>
                        </div>
                        <h5><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></h5>
                        <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item active" id="nav-profile">
                            <i class="fa fa-user me-2"></i> My Profile
                        </li>
                        <li class="list-group-item" id="nav-orders">
                            <i class="fa fa-shopping-bag me-2"></i> My Orders
                        </li>
                        <li class="list-group-item" id="nav-address">
                            <i class="fa fa-map-marker me-2"></i> My Address
                        </li>
                        <li class="list-group-item" id="nav-password">
                            <i class="fa fa-lock me-2"></i> Change Password
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <!-- Profile Information -->
                <div id="profile-section" class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Account Information</h5>
                        <button class="btn btn-sm btn-primary" id="edit-profile-btn">
                            <i class="fa fa-pencil"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- View Mode -->
                        <div id="profile-view">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Full Name</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['middlename'] ? $user['middlename'] . ' ' : '') . htmlspecialchars($user['lastname']); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Username</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Email</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Phone</div>
                                <div class="col-md-8"><?php echo htmlspecialchars($user['phone_number']); ?></div>
                            </div>
                        </div>
                        
                        <!-- Edit Mode -->
                        <div id="profile-edit" style="display: none;">
                            <form id="update-profile-form" action="functions/profile/update_profile.php" method="post">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="edit-firstname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="edit-firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit-middlename" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="edit-middlename" name="middlename" value="<?php echo htmlspecialchars($user['middlename']); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit-lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="edit-lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit-username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="edit-username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                        <div class="invalid-feedback" id="username-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit-phone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="edit-phone" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                                        <div class="invalid-feedback" id="phone-feedback"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="edit-email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="edit-email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        <div class="invalid-feedback" id="email-feedback"></div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <button type="button" class="btn btn-secondary" id="cancel-edit">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Orders Section -->
                <div id="orders-section" class="card mb-4" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0">My Orders</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['order_id']; ?></td>
                                                <td><?php echo date("M d, Y", strtotime($order['created_at'])); ?></td>
                                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <?php if ($order['status'] == 'pending'): ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php elseif ($order['status'] == 'completed'): ?>
                                                        <span class="badge bg-success">Completed</span>
                                                    <?php elseif ($order['status'] == 'cancelled'): ?>
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?php echo ucfirst($order['status']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                <button class="btn btn-sm btn-outline-coral view-order" data-order-id="<?php echo $order['order_id']; ?>">
                                                    View Details
                                                </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                You haven't placed any orders yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Address Section -->
                <div id="address-section" class="card mb-4" style="display: none;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Address</h5>
                        <button class="btn btn-sm btn-primary" id="edit-address-btn">
                            <i class="fa fa-pencil"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- View Mode -->
                        <div id="address-view">
                            <div class="row mb-3">
                                <div class="col-md-3 fw-bold">Address</div>
                                <div class="col-md-9"><?php echo htmlspecialchars($user['full_address']); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 fw-bold">Zipcode</div>
                                <div class="col-md-9"><?php echo htmlspecialchars($user['zipcode']); ?></div>
                            </div>
                        </div>
                        
                        <!-- Edit Mode -->
                        <div id="address-edit" style="display: none;">
                            <form id="update-address-form" action="functions/profile/update_address.php" method="post">
                                <div class="mb-4">
                                    <label for="edit-full-address" class="form-label">Full Address</label>
                                    <input type="text" class="form-control" id="edit-full-address" name="full_address" value="<?php echo htmlspecialchars($user['full_address']); ?>" required>
                                    <div class="form-text text-muted mt-2">Use the pin to select your location</div>
                                </div>
                                <div id="map" class="mb-4 rounded shadow-sm"></div>
                                <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($user['latitude']); ?>">
                                <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($user['longitude']); ?>">
                                <div class="mb-4">
                                    <label for="edit-zipcode" class="form-label">Zipcode</label>
                                    <input type="text" class="form-control" id="edit-zipcode" name="zipcode" value="<?php echo htmlspecialchars($user['zipcode']); ?>" readonly required>
                                    <div class="form-text text-muted mt-2">Zipcode is automatically determined from your map location.</div>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary">Save Address</button>
                                    <button type="button" class="btn btn-secondary" id="cancel-address-edit">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password Section -->
                <div id="password-section" class="card" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form id="change-password-form" action="functions/profile/update_password.php" method="post">
                            <div class="mb-4">
                                <label for="current-password" class="form-label">Current Password</label>
                                <input type="password" class="form-control profile-form-control" id="current-password" name="current_password" required>
                                <div class="invalid-feedback" id="current-password-feedback"></div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="new-password" class="form-label">New Password</label>
                                    <input type="password" class="form-control profile-form-control" id="new-password" name="new_password" required minlength="8">
                                    <div class="form-text text-muted mt-2">Password must be at least 8 characters.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm-new-password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control profile-form-control" id="confirm-new-password" name="confirm_password" required>
                                    <div class="invalid-feedback">Passwords don't match.</div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="order-details-content">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- UTILITY SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- LEAFLET SCRIPTS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<!-- CUSTOM SCRIPTS -->
<script src="js/url-cleaner.js"></script>
<script src="js/profile.js"></script>
</body>
</html>