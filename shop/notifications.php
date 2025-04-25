<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, redirect to login if not
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Include database connection
require_once '../admin/config/dbcon.php';

// Function to get user notifications
function getUserNotifications($userId, $conn) {
    // In a real implementation, you would fetch notifications from the database
    // For now, we'll return sample notifications
    // This would be replaced with actual database queries
    
    $notifications = [
        [
            'id' => 1,
            'type' => 'order_shipped',
            'title' => 'Your order has shipped',
            'message' => 'Order #BYD78956 has been shipped',
            'icon' => 'bx bx-package text-primary',
            'created_at' => date('Y-m-d H:i:s', strtotime('-35 minutes')),
            'is_read' => 0
        ],
        [
            'id' => 2,
            'type' => 'promotion',
            'title' => 'Limited time offer',
            'message' => '20% off on all summer collection',
            'icon' => 'bx bx-heart text-danger',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'is_read' => 0
        ],
        [
            'id' => 3,
            'type' => 'account',
            'title' => 'Account verified',
            'message' => 'Your account has been successfully verified',
            'icon' => 'bx bx-check-circle text-success',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'is_read' => 0
        ],
        [
            'id' => 4,
            'type' => 'order_delivered',
            'title' => 'Order delivered',
            'message' => 'Your order #BYD45678 has been delivered',
            'icon' => 'bx bx-check-double text-success',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'is_read' => 1
        ],
        [
            'id' => 5,
            'type' => 'review_reminder',
            'title' => 'Share your experience',
            'message' => 'How was your recent purchase? Leave a review!',
            'icon' => 'bx bx-star text-warning',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 week')),
            'is_read' => 1
        ],
    ];
    
    return $notifications;
}

// Get user ID from session (assuming you store it there)
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Get notifications for the user
$notifications = getUserNotifications($userId, $conn);

// Handle marking notifications as read
if (isset($_POST['mark_read']) && !empty($_POST['notification_id'])) {
    $notificationId = $_POST['notification_id'];
    // In a real implementation, you would update the database
    // For this sample, we'll just show a success message
    $success = "Notification marked as read";
}

// Handle marking all as read
if (isset($_POST['mark_all_read'])) {
    // In a real implementation, you would update the database
    $success = "All notifications marked as read";
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
    <style>
        .notification-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .notification-item {
            transition: background-color 0.2s ease;
            border-left: 4px solid transparent;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            border-left-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .notification-time {
            font-size: 0.85rem;
        }
        
        .notification-actions {
            display: flex;
            gap: 5px;
        }
        
        .page-header {
            background-color: #f8f9fa;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        
        .no-notifications {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .mark-read-btn {
            transition: all 0.2s ease;
        }
        
        .mark-read-btn:hover {
            background-color: #e9ecef;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #f8f9fa;
        }
    </style>
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
    <section class="page-header my-5 py-5">
        <div class="container pt-5">
            <div class="row">
                <div class="col-md-12">
                    <h2>My Notifications</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Notifications Section -->
    <section class="notifications-section py-4">
        <div class="container notification-container">
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Notifications</h5>
                    <form method="post" action="">
                        <button type="submit" name="mark_all_read" class="btn btn-sm btn-outline-secondary">
                            Mark All as Read
                        </button>
                    </form>
                </div>
                
                <div class="card-body p-0">
                    <?php if (empty($notifications)): ?>
                        <div class="no-notifications">
                            <i class="bx bx-bell-off fs-1 text-muted"></i>
                            <p class="mt-3">You don't have any notifications yet</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="list-group-item notification-item <?= $notification['is_read'] ? '' : 'unread' ?>">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="notification-icon">
                                                <i class="<?= $notification['icon'] ?> fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-1 <?= $notification['is_read'] ? 'text-muted' : 'fw-bold' ?>">
                                                    <?= htmlspecialchars($notification['title']) ?>
                                                </h6>
                                                <small class="notification-time text-muted">
                                                    <?= getTimeAgo($notification['created_at']) ?>
                                                </small>
                                            </div>
                                            <p class="mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                                            
                                            <?php if (!$notification['is_read']): ?>
                                                <div class="mt-2">
                                                    <form method="post" action="" class="d-inline">
                                                        <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                                                        <button type="submit" name="mark_read" class="btn btn-sm btn-light mark-read-btn">
                                                            Mark as read
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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
    <script src="js/assistant.js"></script>
    <script src="js/shop.js"></script>
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
