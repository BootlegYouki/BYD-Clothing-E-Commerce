<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['auth_user'])) {
    header("Location: index.php");
    exit();
}

require_once '../admin/config/dbcon.php';

// Get user ID
$user_id = $_SESSION['auth_user']['user_id'];

// Function to mark notification as read
if (isset($_POST['mark_read']) && isset($_POST['notification_id'])) {
    $notification_id = mysqli_real_escape_string($conn, $_POST['notification_id']);
    $update_query = "UPDATE notifications SET is_read = 1 WHERE id = $notification_id AND user_id = $user_id";
    mysqli_query($conn, $update_query);
    // Redirect to prevent form resubmission
    header("Location: notifications.php");
    exit();
}

// Function to mark all notifications as read
if (isset($_POST['mark_all_read'])) {
    $update_all_query = "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id";
    mysqli_query($conn, $update_all_query);
    // Redirect to prevent form resubmission
    header("Location: notifications.php");
    exit();
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
            padding-top: 120px; /* Adjust based on navbar height */
            min-height: calc(100vh - 300px); /* Ensure enough space for content */
        }
        
        .notification-card {
            border-left: 4px solid #FF7F50;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .notification-card.read {
            border-left-color: #ccc;
            background-color: #f8f9fa;
        }
        
        .notification-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .notification-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .notification-actions {
            display: flex;
            justify-content: flex-end;
        }
        
        .notification-badge-all {
            position: relative;
            top: -2px;
            padding: 3px 6px;
            border-radius: 10px;
            background-color: #FF7F50;
            color: white;
            font-size: 0.7rem;
        }
        
        .filter-active {
            background-color: #FF7F50 !important;
            color: white !important;
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

    <!-- NOTIFICATIONS SECTION -->
    <section class="notification-container">
        <div class="container py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="mb-3">Notifications</h2>
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="filter-tabs mb-3">
                            <a href="?filter=all" class="filter-tab <?php echo (!isset($_GET['filter']) || $_GET['filter'] == 'all') ? 'active' : ''; ?>">
                                <i class="bx bx-list-ul me-1"></i> All
                            </a>
                            <a href="?filter=unread" class="filter-tab <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'unread') ? 'active' : ''; ?>">
                                <i class="bx bx-envelope me-1"></i> Unread
                                <?php 
                                $unread_count = count(array_filter($notifications ?? [], function($notif) {
                                    return $notif['is_read'] == 0;
                                }));
                                if ($unread_count > 0): 
                                ?>
                                <span class="unread-badge"><?php echo $unread_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <form method="post" class="mb-3">
                            <button type="submit" name="mark_all_read" class="btn mark-all-btn">
                                <i class="bx bx-check-double me-1"></i> Mark all as read
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <style>
                .filter-tabs {
                    display: flex;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                    border: 1px solid #e0e0e0;
                }
                
                .filter-tab {
                    padding: 10px 20px;
                    background-color: #ffffff;
                    color: #555;
                    text-decoration: none;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                    font-weight: 500;
                    position: relative;
                }
                
                .filter-tab:first-child {
                    border-right: 1px solid #e0e0e0;
                }
                
                .filter-tab.active {
                    background-color: #FF7F50;
                    color: white;
                }
                
                .filter-tab:hover:not(.active) {
                    background-color: #f8f8f8;
                }
                
                .unread-badge {
                    background-color: #dc3545;
                    color: white;
                    border-radius: 50%;
                    min-width: 18px;
                    height: 18px;
                    font-size: 10px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    margin-left: 5px;
                    padding: 2px;
                }
                
                .mark-all-btn {
                    background-color: transparent;
                    border: 1px solid #FF7F50;
                    color: #FF7F50;
                    border-radius: 6px;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                }
                
                .mark-all-btn:hover {
                    background-color: #FF7F50;
                    color: white;
                }
            </style>
            
            <div class="row">
                <div class="col-12">
                    <?php
                    // Get notifications for the user
                    $filter_condition = "";
                    if (isset($_GET['filter']) && $_GET['filter'] == 'unread') {
                        $filter_condition = "AND is_read = 0";
                    }
                    
                    // In production, replace with a real query from your notifications table
                    // For now, using dummy data for demonstration
                    
                    /*
                    $query = "SELECT * FROM notifications 
                              WHERE user_id = $user_id $filter_condition 
                              ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);
                    $notifications = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $notifications[] = $row;
                    }
                    */
                    
                    // Dummy notifications for demonstration
                    $notifications = [
                        ['id' => 1, 'message' => 'Your order #1234 has been shipped!', 'created_at' => '2023-10-01 14:23:45', 'is_read' => 0],
                        ['id' => 2, 'message' => 'New arrivals in your favorite category', 'created_at' => '2023-09-29 09:15:22', 'is_read' => 0],
                        ['id' => 3, 'message' => '20% off on all t-shirts this weekend!', 'created_at' => '2023-09-27 18:30:00', 'is_read' => 1],
                        ['id' => 4, 'message' => 'Your payment for order #1122 was successful', 'created_at' => '2023-09-25 11:42:10', 'is_read' => 1],
                        ['id' => 5, 'message' => 'Welcome to BYD Clothing! Start exploring our collections', 'created_at' => '2023-09-20 08:00:00', 'is_read' => 1]
                    ];
                    
                    // Apply filter for demonstration
                    if (isset($_GET['filter']) && $_GET['filter'] == 'unread') {
                        $notifications = array_filter($notifications, function($notification) {
                            return $notification['is_read'] == 0;
                        });
                    }
                    
                    if (empty($notifications)) {
                        echo '<div class="text-center p-5">
                                <i class="bx bx-bell-off fs-1 text-muted"></i>
                                <p class="mt-3">No notifications to display</p>
                              </div>';
                    } else {
                        $unread_count = count(array_filter($notifications, function($notification) {
                            return $notification['is_read'] == 0;
                        }));
                        
                        foreach ($notifications as $notification) {
                            $read_class = $notification['is_read'] ? 'read' : '';
                            $date = date('F j, Y', strtotime($notification['created_at']));
                            $time = date('g:i A', strtotime($notification['created_at']));
                            
                            echo '<div class="card notification-card ' . $read_class . '">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-9">
                                                <p class="card-text mb-1">' . $notification['message'] . '</p>
                                                <span class="notification-date">' . $date . ' at ' . $time . '</span>
                                            </div>
                                            <div class="col-md-3 notification-actions">';
                            
                            if (!$notification['is_read']) {
                                echo '<form method="post">
                                        <input type="hidden" name="notification_id" value="' . $notification['id'] . '">
                                        <button type="submit" name="mark_read" class="btn btn-sm btn-outline-secondary">
                                            <i class="bx bx-check"></i> Mark as read
                                        </button>
                                      </form>';
                            } else {
                                echo '<span class="text-muted"><i class="bx bx-check"></i> Read</span>';
                            }
                            
                            echo '        </div>
                                    </div>
                                </div>
                            </div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- UTILITY SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/url-cleaner.js"></script>
    <script src="js/shop.js"></script>
</body>
</html>
