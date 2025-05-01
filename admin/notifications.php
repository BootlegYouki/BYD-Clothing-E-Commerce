<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}
include 'config/dbcon.php';

// Set default filter values
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$user_filter = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Build the query based on filters
$query = "SELECT n.*, u.email, u.username, CONCAT(u.firstname, ' ', u.lastname) AS fullname 
          FROM notifications n 
          LEFT JOIN users u ON n.user_id = u.id 
          WHERE 1=1";

if ($type_filter != 'all') {
    $query .= " AND n.type = '" . mysqli_real_escape_string($conn, $type_filter) . "'";
}

if (!empty($search)) {
    $query .= " AND (n.title LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' 
                OR n.message LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
}

if (!empty($user_filter)) {
    $query .= " AND n.user_id = " . mysqli_real_escape_string($conn, $user_filter);
}

// Add sorting
$query .= " ORDER BY n.created_at DESC";

$result = mysqli_query($conn, $query);

// Get all notification types for filter dropdown
$type_query = "SELECT DISTINCT type FROM notifications";
$type_result = mysqli_query($conn, $type_query);
$types = [];
while ($type = mysqli_fetch_assoc($type_result)) {
    $types[] = $type['type'];
}

// Get users for recipient dropdown
$users_query = "SELECT id, CONCAT(firstname, ' ', lastname) AS fullname, email, username FROM users WHERE role_as = 0";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Notifications | Beyond Doubt Clothing</title>
  
  <!-- Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  
  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/sidebar.css">
  <link rel="stylesheet" href="assets/css/notifications.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
    <?php if(isset($_SESSION['message'])) : ?>
        <?php 
        $messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : "error";
        $alertClass = ($messageType == "success") ? "alert-success" : "alert-danger";
        $alertTitle = ($messageType == "success") ? "Success!" : "Error!";
        ?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert" id="alert-message">
            <strong><?= $alertTitle ?></strong> <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('alert-message').classList.remove('show');
                setTimeout(function() {
                    document.getElementById('alert-message')?.remove();
                }, 150);
            }, 3000);
        </script>
        <?php 
        unset($_SESSION['message']); 
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <!-- Create Notification Card -->
    <div class="card mb-4">
        <div class="card-header p-3">
            <h5 class="mb-0">Create New Notification</h5>
        </div>
        <div class="card-body">
            <form action="functions/manage_notifications.php" method="POST" id="createNotificationForm">
                <input type="hidden" name="action" value="create">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="notification_type" class="form-label">Notification Type</label>
                        <select class="form-select" id="notification_type" name="type" required>
                            <option value="">Select a type</option>
                            <option value="order_status">Order Status Update</option>
                            <option value="order_shipped">Order Shipped</option>
                            <option value="order_delivered">Order Delivered</option>
                            <option value="promotion">Promotion</option>
                            <option value="account">Account Update</option>
                            <option value="review_reminder">Review Reminder</option>
                            <option value="new_release">New Product Release</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Recipient</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="recipient_type" id="all_users" value="all" checked>
                                <label class="form-check-label" for="all_users">All Users</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="recipient_type" id="specific_user" value="specific">
                                <label class="form-check-label" for="specific_user">Specific Users</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="specific_user_container" style="display: none;">
                    <div class="col-md-12">
                        <label class="form-label">Select Users <span id="selected-users-count" class="selected-count">0</span></label>
                        <div class="users-container">
                            <div class="user-search">
                                <input type="text" class="form-control" id="user-search-input" placeholder="Search users...">
                            </div>
                            <div class="users-grid">
                                <?php 
                                mysqli_data_seek($users_result, 0); // Reset pointer
                                while ($user = mysqli_fetch_assoc($users_result)) {
                                    $name_parts = explode(' ', trim($user['fullname']));
                                    $first_initial = isset($name_parts[0]) && !empty($name_parts[0]) ? strtoupper(substr($name_parts[0], 0, 1)) : '';
                                    $last_initial = isset($name_parts[1]) && !empty($name_parts[1]) ? strtoupper(substr($name_parts[1], 0, 1)) : '';
                                    $initials = $first_initial . $last_initial;
                                    $initials = !empty($initials) ? $initials : 'U';
                                    echo '<div class="user-card" data-user-id="'.$user['id'].'" data-user-name="'.$user['fullname'].'" data-user-email="'.$user['email'].'">';
                                    echo '<input type="checkbox" name="user_ids[]" id="user_'.$user['id'].'" value="'.$user['id'].'">';
                                    echo '<div class="user-card-content">';
                                    echo '<div class="user-avatar">'.$initials.'</div>';
                                    echo '<div class="user-info">';
                                    echo '<p class="user-name">'.$user['fullname'].'</p>';
                                    echo '<p class="user-email">'.$user['email'].'</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllUsers">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="deselectAllUsers">Deselect All</button>
                            </div>
                            <small class="text-muted">Click on a user card to select/deselect</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="notification_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="notification_title" name="title" required maxlength="100">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="notification_message" class="form-label">Message</label>
                        <textarea class="form-control" id="notification_message" name="message" rows="4" required maxlength="500"></textarea>
                        <div class="message-counter"><span id="message_count">0</span>/500</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6>Preview</h6>
                        <div class="notification-preview" id="notification_preview">
                            <div class="d-flex">
                                <div class="notification-icon">
                                    <i class="bx bx-bell"></i>
                                </div>
                                <div>
                                    <div class="fw-bold" id="preview_title">Notification Title</div>
                                    <div id="preview_message">Your notification message will appear here.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="material-symbols-rounded me-1">send</i> Send Notification
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header p-3">
            <h5 class="mb-0">Notifications Management</h5>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="" class="row g-3">
                <!-- Search -->
                <div class="col-lg-4 col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="material-symbols-rounded">search</i></span>
                        <input type="text" class="form-control" id="search" name="search" 
                            placeholder="Search by title or message" value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <!-- Type Filter -->
                <div class="col-lg-3 col-md-6">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="all">All Types</option>
                        <option value="order_status" <?= $type_filter == 'order_status' ? 'selected' : '' ?>>Order Status Update</option>
                        <option value="order_shipped" <?= $type_filter == 'order_shipped' ? 'selected' : '' ?>>Order Shipped</option>
                        <option value="order_delivered" <?= $type_filter == 'order_delivered' ? 'selected' : '' ?>>Order Delivered</option>
                        <option value="promotion" <?= $type_filter == 'promotion' ? 'selected' : '' ?>>Promotion</option>
                        <option value="account" <?= $type_filter == 'account' ? 'selected' : '' ?>>Account Update</option>
                        <option value="review_reminder" <?= $type_filter == 'review_reminder' ? 'selected' : '' ?>>Review Reminder</option>
                        <option value="new_release" <?= $type_filter == 'new_release' ? 'selected' : '' ?>>New Product Release</option>
                    </select>
                </div>

                <!-- User Filter -->
                <div class="col-lg-3 col-md-6">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id_filter" class="form-select">
                        <option value="">All Users</option>
                        <?php 
                        mysqli_data_seek($users_result, 0); // Reset pointer
                        while ($user = mysqli_fetch_assoc($users_result)) {
                            $selected = ($user_filter == $user['id']) ? 'selected' : '';
                            echo '<option value="'.$user['id'].'" '.$selected.'>'.$user['fullname'].' ('.$user['email'].')</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-lg-2 col-md-6 d-flex align-items-end">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center p-3">
            <div>
                <h5 class="mb-0">Notifications List</h5>
                <p class="text-sm mb-0">
                    <?php echo mysqli_num_rows($result); ?> notifications found
                </p>
            </div>
            <div class="bulk-actions" id="bulk-actions">
                <form method="POST" action="functions/manage_notifications.php" id="bulk-action-form">
                    <input type="hidden" name="action" value="bulk_delete">
                    <button type="button" class="btn btn-danger btn-sm" id="bulk-delete-btn" disabled>
                        <i class="material-symbols-rounded">delete</i> Delete Selected (<span id="selected-count">0</span>)
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input select-all-checkbox" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-3">Type</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title & Message</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Recipient</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Sent</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                // Get the right icon and label for the notification type
                                $type_label = "Notification";
                                $icon_class = "bx bx-bell";
                                
                                switch($row['type']) {
                                    case 'order_status':
                                        $type_label = "Order Status";
                                        $icon_class = "bx bx-package";
                                        $badge_class = "bg-primary";
                                        break;
                                    case 'order_shipped':
                                        $type_label = "Order Shipped";
                                        $icon_class = "bx bx-package";
                                        $badge_class = "bg-info";
                                        break;
                                    case 'order_delivered':
                                        $type_label = "Order Delivered";
                                        $icon_class = "bx bx-check-double";
                                        $badge_class = "bg-success";
                                        break;
                                    case 'promotion':
                                        $type_label = "Promotion";
                                        $icon_class = "bx bx-heart";
                                        $badge_class = "bg-danger";
                                        break;
                                    case 'account':
                                        $type_label = "Account Update";
                                        $icon_class = "bx bx-user";
                                        $badge_class = "bg-secondary";
                                        break;
                                    case 'review_reminder':
                                        $type_label = "Review Reminder";
                                        $icon_class = "bx bx-star";
                                        $badge_class = "bg-warning";
                                        break;
                                    case 'new_release':
                                        $type_label = "New Release";
                                        $icon_class = "bx bx-gift";
                                        $badge_class = "bg-info";
                                        break;
                                }
                                
                                // Format date and time
                                $date_sent = date('M d, Y', strtotime($row['created_at']));
                                $time_sent = date('h:i A', strtotime($row['created_at']));
                                
                                // Display recipient information
                                if ($row['user_id']) {
                                    $recipient = $row['fullname'] ? $row['fullname'] : ($row['username'] ? $row['username'] : $row['email']);
                                    $recipient_label = "Single User";
                                    $recipient_badge = "bg-primary";
                                } else {
                                    $recipient = "All Users";
                                    $recipient_label = "Mass Notification";
                                    $recipient_badge = "bg-warning";
                                }
                                
                                // Display read status
                                $status_badge = $row['is_read'] ? 
                                    '<span class="badge bg-success">Read</span>' : 
                                    '<span class="badge bg-warning">Unread</span>';
                                
                                echo "<tr>
                                    <td>
                                        <div class='form-check'>
                                            <input class='form-check-input notification-checkbox' type='checkbox' name='selected_notifications[]' value='{$row['id']}' form='bulk-action-form'>
                                        </div>
                                    </td>
                                    <td class='px-3'>
                                        <div class='d-flex align-items-center'>
                                            <div class='notification-icon {$row['type']}'>
                                                <i class='{$icon_class}'></i>
                                            </div>
                                            <div class='ms-3'>
                                                <span class='badge {$badge_class}'>{$type_label}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class='mb-1'>{$row['title']}</h6>
                                        <p class='text-sm mb-0 text-secondary'>" . nl2br(htmlspecialchars($row['message'])) . "</p>
                                    </td>
                                    <td>
                                        <span class='badge {$recipient_badge}'>{$recipient_label}</span><br>
                                        <span class='text-xs'>{$recipient}</span>
                                    </td>
                                    <td>
                                        <p class='text-sm mb-0'>{$date_sent}</p>
                                        <p class='text-xs text-secondary mb-0'>{$time_sent}</p>
                                    </td>
                                    <td>
                                        {$status_badge}
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4'>No notifications found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Notification Modal -->
<div class="modal fade" id="deleteNotificationModal" tabindex="-1" aria-labelledby="deleteNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteNotificationModalLabel">Delete Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this notification? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="functions/manage_notifications.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="notification_id" id="delete_notification_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">Delete Multiple Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="bulk-delete-count">0</span> notification(s)? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-bulk-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Mass Notification Modal -->
<div class="modal fade" id="massNotificationModal" tabindex="-1" aria-labelledby="massNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="massNotificationModalLabel">Send Mass Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="functions/manage_notifications.php" method="POST" id="massNotificationForm">
                    <input type="hidden" name="action" value="mass_create">
                    <input type="hidden" name="recipient_type" value="all">
                    
                    <div class="mb-3">
                        <label for="mass_notification_type" class="form-label">Notification Type</label>
                        <select class="form-select" id="mass_notification_type" name="type" required>
                            <option value="">Select a type</option>
                            <option value="promotion">Promotion</option>
                            <option value="new_release">New Product Release</option>
                            <option value="account">Account Update</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mass_notification_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="mass_notification_title" name="title" required maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label for="mass_notification_message" class="form-label">Message</label>
                        <textarea class="form-control" id="mass_notification_message" name="message" rows="4" required maxlength="500"></textarea>
                        <div class="message-counter"><span id="mass_message_count">0</span>/500</div>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <i class="material-symbols-rounded me-2">info</i>
                        This notification will be sent to all users.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitMassNotification">Send to All Users</button>
            </div>
        </div>
    </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/notifications.js"></script>
<script>
    $(document).ready(function() {
        // Handle user card selection
        $('.user-card').on('click', function() {
            const checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));
            $(this).toggleClass('selected');
            updateSelectedCount();
        });
        
        // Prevent checkbox click from triggering card click
        $('.user-card input').on('click', function(e) {
            e.stopPropagation();
            $(this).closest('.user-card').toggleClass('selected');
            updateSelectedCount();
        });
        
        // Handle select/deselect all buttons
        $('#selectAllUsers').on('click', function() {
            $('.user-card input').prop('checked', true);
            $('.user-card').addClass('selected');
            updateSelectedCount();
        });
        
        $('#deselectAllUsers').on('click', function() {
            $('.user-card input').prop('checked', false);
            $('.user-card').removeClass('selected');
            updateSelectedCount();
        });
        
        // Search functionality
        $('#user-search-input').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.user-card').each(function() {
                const name = $(this).data('user-name').toLowerCase();
                const email = $(this).data('user-email').toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        function updateSelectedCount() {
            const count = $('.user-card input:checked').length;
            $('#selected-users-count').text(count);
        }

        // Handle select all checkbox
        $('#select-all').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.notification-checkbox').prop('checked', isChecked);
            updateBulkActions();
        });
        
        // Handle individual notification checkboxes
        $('.notification-checkbox').on('change', function() {
            updateBulkActions();
            
            // Check/uncheck "select all" if all checkboxes are checked/unchecked
            const totalCheckboxes = $('.notification-checkbox').length;
            const checkedCheckboxes = $('.notification-checkbox:checked').length;
            $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        });
        
        // Update the bulk actions function to enable/disable rather than show/hide
        function updateBulkActions() {
            const selectedCount = $('.notification-checkbox:checked').length;
            $('#selected-count').text(selectedCount);
            
            // Enable or disable the button based on selection count
            if (selectedCount > 0) {
                $('#bulk-delete-btn').prop('disabled', false);
            } else {
                $('#bulk-delete-btn').prop('disabled', true);
            }
        }
        
        // Replace confirmation alert with modal for bulk delete
        $('#bulk-delete-btn').on('click', function(e) {
            const selectedCount = $('.notification-checkbox:checked').length;
            
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Please select at least one notification to delete.');
                return;
            }
            
            // Update the count in the modal
            $('#bulk-delete-count').text(selectedCount);
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
            modal.show();
        });
        
        // Handle the confirm button in the bulk delete modal
        $('#confirm-bulk-delete').on('click', function() {
            $('#bulk-action-form').submit();
        });
    });
</script>

</body>
</html>
