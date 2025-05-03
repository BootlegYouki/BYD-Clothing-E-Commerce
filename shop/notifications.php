<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, redirect to login if not
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header("Location: index.php");
    exit;
}

// Include database connection
require_once '../admin/config/dbcon.php';

// Get user ID from session
$user_id = $_SESSION['auth_user']['user_id'] ?? 0;

// Query to get all notifications for the user
$query = "SELECT id, type, title, message, created_at, is_read 
          FROM notifications 
          WHERE user_id = ? 
          ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    // Get appropriate icon based on notification type
    $icon = 'bx bx-bell text-primary';
    
    switch ($row['type']) {
        case 'order_status':
        case 'order_shipped':
            $icon = 'bx bx-package text-primary';
            break;
        case 'order_delivered':
            $icon = 'bx bx-check-double text-success';
            break;
        case 'promotion':
            $icon = 'bx bx-heart text-danger';
            break;
        case 'account':
            $icon = 'bx bx-user text-info';
            break;
        case 'review_reminder':
            $icon = 'bx bx-star text-warning';
            break;
        case 'new_release':
            $icon = 'bx bx-gift text-info';
            break;
        default:
            $icon = 'bx bx-bell text-primary';
            break;
    }
    
    $row['icon'] = $icon;
    $notifications[] = $row;
}

// Group notifications by date category
$groupedNotifications = [
    'Earlier' => []
];

// Include timezone initialization
require_once 'includes/timezone.php';

// Current date for comparison
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$weekAgo = date('Y-m-d', strtotime('-7 days'));

// Group each notification
foreach ($notifications as $notification) {
    $notifDate = date('Y-m-d', strtotime($notification['created_at']));
    
    if ($notifDate == $today) {
        $groupedNotifications['Today'][] = $notification;
    } else if ($notifDate == $yesterday) {
        $groupedNotifications['Yesterday'][] = $notification;
    } else if ($notifDate > $weekAgo) {
        $groupedNotifications['This Week'][] = $notification;
    } else {
        $groupedNotifications['Earlier'][] = $notification;
    }
}

// Count unread notifications
$unreadCount = 0;
foreach ($notifications as $notification) {
    if ($notification['is_read'] == 0) {
        $unreadCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Notifications - Beyond Doubt Clothing</title> 
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
    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="css/important.css">
    <link rel="stylesheet" href="css/headerfooter.css">
    <link rel="stylesheet" href="css/shopcart.css">
    <link rel="stylesheet" href="css/assistant.css">
    <link rel="stylesheet" href="css/notifications.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'includes/header.php'; ?>
    <!-- CHATBOT  -->
    <?php include 'includes/assistant.php'; ?>
    <!-- SHOPPING CART MODAL  -->
    <?php include 'includes/shopcart.php'; ?>
    <!-- REGISTER MODAL  -->
    <?php include 'includes/register.php'; ?>
    <!-- LOGIN MODAL  -->
    <?php include 'includes/login.php'; ?>
    <!-- LOGOUT MODAL  -->
    <?php include 'includes/logout.php'; ?>
    <!-- SUCCESS MODAL  -->
    <?php include 'includes/loginsuccess.php'; ?>
    <?php include 'includes/registersuccess.php'; ?>
    <!-- TERMS MODAL  -->
    <?php include 'includes/terms.php'; ?>

    <!-- Page Header -->
    <section class="page-header py-5">
        <div class="container pt-5 mt-4">
            <div class="row">
                <div class="col-md-12 pt-5">
                    <h2 class="fw-bold">My Notifications <?php if($unreadCount > 0): ?><span class="notification-counter"><?= $unreadCount ?> New</span><?php endif; ?></h2>
                    <p class="text-muted">Stay updated with your orders, promotions, and our new release products.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Notifications Section -->
    <section class="notifications-section py-4 mb-5">
        <div class="container notification-container">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center p-3">
                    <div class="notification-header">
                        <h5 class="mb-0">All Notifications</h5>
                    </div>
                    <button type="button" name="mark_all_read" class="btn btn-sm btn-primary">
                        <i class="bx bx-check-double me-1"></i> Mark All as Read
                    </button>
                </div>
                
                <div class="card-body">
                    <!-- Notification filters -->
                    <div class="notification-filters">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="order_shipped">Orders Shipped</button>
                        <button class="filter-btn" data-filter="promotion">Promotions</button>
                        <button class="filter-btn" data-filter="new_release">New Release</button>
                        <button class="filter-btn" data-filter="order_delivered">Delivered</button>
                    </div>
                    
                    <?php if (empty($notifications)): ?>
                        <div class="no-notifications">
                            <i class="bx bx-bell-off fs-1 text-muted mb-3"></i>
                            <h5>No Notifications Yet</h5>
                            <p class="mt-2">You don't have any notifications at this moment</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($groupedNotifications as $dateGroup => $dateNotifications): ?>
                            <?php if (!empty($dateNotifications)): ?>
                                <div class="notification-date-header">
                                    <span><?= $dateGroup ?></span>
                                </div>
                                
                                <div class="notification-group">
                                    <?php foreach ($dateNotifications as $notification): ?>
                                        <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>" 
                                             data-type="<?= $notification['type'] ?>"
                                             data-id="<?= $notification['id'] ?>">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="notification-icon">
                                                        <i class="<?= $notification['icon'] ?> fs-4"></i>
                                                        <?php if (!$notification['is_read']): ?>
                                                            <div class="notification-badge-page"></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-1 <?= $notification['is_read'] ? 'text-muted' : 'fw-bold' ?>">
                                                            <?= htmlspecialchars($notification['title']) ?>
                                                        </h6>
                                                        <small class="notification-time">
                                                            <?= getTimeAgo($notification['created_at']) ?>
                                                        </small>
                                                    </div>
                                                    <p class="mb-1 <?= $notification['is_read'] ? 'text-secondary' : '' ?>"><?= htmlspecialchars($notification['message']) ?></p>
                                                    
                                                    <?php if (!$notification['is_read']): ?>
                                                        <div class="mt-2">
                                                            <button type="button" class="btn btn-sm btn-light mark-read-btn" 
                                                                    data-notification-id="<?= $notification['id'] ?>">
                                                                <i class="bx bx-check me-1"></i> Mark as read
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- UTILITY SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <!-- SCRIPT -->
    <script src="js/url-cleaner.js"></script>
    <script src="js/shop.js"></script>
    <script src="js/page-notifications.js"></script>
</body>
</html>

<?php
// Helper function to calculate time ago
function getTimeAgo($timestamp) {
    $time = strtotime($timestamp);
    $current = time();
    $diff = $current - $time;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . " month" . ($months > 1 ? "s" : "") . " ago";
    } else {
        $years = floor($diff / 31536000);
        return $years . " year" . ($years > 1 ? "s" : "") . " ago";
    }
}
?>
