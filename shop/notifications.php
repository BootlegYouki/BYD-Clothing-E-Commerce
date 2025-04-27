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
            'message' => 'Order #BYD78956 has been shipped. Track your package for delivery updates.',
            'icon' => 'bx bx-package text-primary',
            'created_at' => date('Y-m-d H:i:s', strtotime('-35 minutes')),
            'is_read' => 0
        ],
        [
            'id' => 2,
            'type' => 'promotion',
            'title' => 'Limited time offer',
            'message' => '20% off on all summer collection items. Shop now before the offer ends!',
            'icon' => 'bx bx-heart text-danger',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'is_read' => 0
        ],
        [
            'id' => 3,
            'type' => 'account',
            'title' => 'Account verified',
            'message' => 'Your account has been successfully verified. You now have full access to all features.',
            'icon' => 'bx bx-check-circle text-success',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'is_read' => 0
        ],
        [
            'id' => 4,
            'type' => 'order_delivered',
            'title' => 'Order delivered',
            'message' => 'Your order #BYD45678 has been delivered. We hope you enjoy your purchase!',
            'icon' => 'bx bx-check-double text-success',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'is_read' => 1
        ],
        [
            'id' => 5,
            'type' => 'review_reminder',
            'title' => 'Share your experience',
            'message' => 'How was your recent purchase? Leave a review and help other shoppers make decisions!',
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

// Group notifications by date category
$groupedNotifications = [
    'Today' => [],
    'Yesterday' => [],
    'This Week' => [],
    'Earlier' => []
];

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
    <style>
        .notification-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .notification-item {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            border-left: 0;
            padding: 18px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .notification-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .notification-item.unread {
            background-color: rgba(255, 127, 80, 0.03);
            border-left: 5px solid #FF7F50;
        }

        /* Style for read notifications */
        .notification-item:not(.unread) {
            background-color: #fafafa;
            border-left: 5px solid #e9e9e9;
            opacity: 0.9;
        }

        .notification-item:not(.unread):hover {
            opacity: 1;
        }
        
        .notification-time {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Read status indicator */
        .read-status {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
            display: flex;
            align-items: center;
        }
        
        .read-status i {
            font-size: 0.9rem;
            margin-right: 3px;
        }
        
        .notification-actions {
            display: flex;
            gap: 5px;
        }
        
        .page-header {
            background-color: #fff5f2; 
            padding: 40px 0;
            margin-bottom: 30px;
            position: relative;
            border-bottom: 1px solid rgba(255, 127, 80, 0.1);
        }
        
        .page-header:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('img/patterns/pattern-light.png');
            opacity: 0.4;
            pointer-events: none;
        }
        
        .no-notifications {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
            background: linear-gradient(to bottom, #fff5f2, #ffffff);
            border-radius: 8px;
        }
        
        .mark-read-btn {
            transition: all 0.3s ease;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 0.85rem;
            border: 1px solid rgba(0,0,0,0.1);
        }
        
        .mark-read-btn:hover {
            background-color: #FF7F50;
            color: white;
        }
        
        .notification-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #fff5f2;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: relative; /* Explicitly set position to relative */
        }
        
        .notification-item:hover .notification-icon {
            transform: scale(1.1);
        }
        
        .notification-badge-page {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #FF7F50;
            transform: translateZ(0); /* Force hardware acceleration, prevent transform inheritance */
            z-index: 1; /* Ensure badge stays on top */
        }
        
        .notification-date-header {
            font-weight: 500;
            color: #FF7F50;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ffece7;
        }
        
        .notification-filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background-color: #fff5f2;
            border: 1px solid #FF7F50;
            color: #FF7F50;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background-color: #FF7F50;
            color: white;
            border-color: #FF7F50;
        }
        
        .notification-header {
            position: relative;
        }
        
        .notification-counter {
            background-color: #FF7F50;
            color: white;
            border-radius: 20px;
            padding: 6px 10px;
            font-size: 0.8rem;
            margin-left: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
            text-align: center;
        }

        .btn-primary {
            background-color: #FF7F50;
            border-color: #FF7F50;
        }

        .btn-primary:hover {
            background-color: #FF6347;
            border-color: #FF6347;
        }
        
        @media (max-width: 767.98px) {
            .notification-item {
                padding: 15px;
            }
            
            .notification-icon {
                width: 40px;
                height: 40px;
            }
            
            .page-header {
                padding: 30px 0;
            }
            
            .notification-filters {
                overflow-x: auto;
                padding-bottom: 10px;
            }
        }
        
        /* Animation for new notifications */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .notification-item {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* Staggered animation delay for items */
        .notification-item:nth-child(1) { animation-delay: 0.1s; }
        .notification-item:nth-child(2) { animation-delay: 0.2s; }
        .notification-item:nth-child(3) { animation-delay: 0.3s; }
        .notification-item:nth-child(4) { animation-delay: 0.4s; }
        .notification-item:nth-child(5) { animation-delay: 0.5s; }
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
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center p-3">
                    <div class="notification-header">
                        <h5 class="mb-0">All Notifications</h5>
                    </div>
                    <form method="post" action="">
                        <button type="submit" name="mark_all_read" class="btn btn-sm btn-primary">
                            <i class="bx bx-check-double me-1"></i> Mark All as Read
                        </button>
                    </form>
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
                                        <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>" data-type="<?= $notification['type'] ?>">
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
                                                            <form method="post" action="" class="d-inline">
                                                                <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                                                                <button type="submit" name="mark_read" class="btn btn-sm btn-light mark-read-btn">
                                                                    <i class="bx bx-check me-1"></i> Mark as read
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
    
    <!-- Notification filter script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const notifications = document.querySelectorAll('.notification-item');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter notifications
                notifications.forEach(notification => {
                    if (filter === 'all' || notification.getAttribute('data-type') === filter) {
                        notification.style.display = 'block';
                        // Add animation
                        notification.style.animation = 'none';
                        setTimeout(() => {
                            notification.style.animation = 'fadeIn 0.5s ease forwards';
                        }, 10);
                    } else {
                        notification.style.display = 'none';
                    }
                });
                
                // Check if there are any visible notifications in each date group
                document.querySelectorAll('.notification-group').forEach(group => {
                    const visibleNotifications = group.querySelectorAll('.notification-item[style="display: block"]').length;
                    const dateHeader = group.previousElementSibling;
                    if (dateHeader && dateHeader.classList.contains('notification-date-header')) {
                        dateHeader.style.display = visibleNotifications > 0 ? 'block' : 'none';
                    }
                });
            });
        });

        // Add event listeners for marking notifications as read
        document.querySelectorAll('.mark-read-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const notificationId = form.querySelector('input[name="notification_id"]').value;
                const notificationItem = this.closest('.notification-item');
                
                // Create form data for AJAX request
                const formData = new FormData(form);
                
                // Send AJAX request
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Mark notification as read in UI without removing it from view
                    notificationItem.classList.remove('unread');
                    
                    // Update title and message styling
                    const title = notificationItem.querySelector('h6');
                    const message = notificationItem.querySelector('p.mb-1');
                    
                    if (title) {
                        title.classList.remove('fw-bold');
                        title.classList.add('text-muted');
                    }
                    
                    if (message) {
                        message.classList.add('text-secondary');
                    }
                    
                    // Remove notification badge
                    const badge = notificationItem.querySelector('.notification-badge-page');
                    if (badge) badge.remove();
                    
                    // Remove the mark as read button
                    const markReadBtnContainer = this.closest('.mt-2');
                    if (markReadBtnContainer) markReadBtnContainer.remove();
                    
                    // Update counter if exists
                    const counter = document.querySelector('.notification-counter');
                    if (counter) {
                        let countText = counter.textContent.trim();
                        let count = parseInt(countText);
                        if (count > 1) {
                            count -= 1;
                            counter.textContent = count + ' New';
                        } else {
                            counter.remove();
                        }
                    }
                    
                    // Ensure notification remains visible with the proper style
                    notificationItem.style.display = 'block';
                    notificationItem.style.backgroundColor = '#fafafa';
                    notificationItem.style.borderLeft = '5px solid #e9e9e9';
                    notificationItem.style.opacity = '0.9';
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
            });
        });
        
        // Add event listener for "Mark All as Read" button
        const markAllButton = document.querySelector('button[name="mark_all_read"]');
        if (markAllButton) {
            // All event listener functionality moved to notifications.js
        }
    });
    </script>
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
