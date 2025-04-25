<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$username = isset($_SESSION['username']) ? htmlentities($_SESSION['username']) : 'Guest';
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = isset($_SESSION['auth_role']) && $_SESSION['auth_role'] == 1;
$hide_cart = ($current_page == 'checkout.php');
?>

<nav class="navbar navbar-expand-lg bg-white fixed-top shadow">
  <div class="container-fluid px-lg-4 px-1">
    <a href="index.php" class="me-auto">
    <img src="img/logo/logo.webp" alt="logo" class="imglogo">
    </a>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
      <a href="index.php">
        <img src="img/logo/logo.webp" alt="logo" class="imglogo">
      </a>
        <span class="ms-3">Hello, <?php echo $username; ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>        
      <div class="offcanvas-body">
        <!-- Search form and nav items here... -->
        <form class="d-block d-lg-none ms-auto pe-2" action="shop.php" method="GET">
          <div class="search-icon-wrapper">
            <input class="form-control" type="search" name="search" placeholder="Search" aria-label="Search">
            <button type="submit" class="search-icon border-0 bg-transparent">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
        <ul class="navbar-nav justify-content-end flex-grow-1 text-center">
      <li class="nav-item my-2">
        <a class="nav-link mx-lg-2 <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" 
          <?php echo ($current_page == 'index.php') ? 'aria-current="page"' : ''; ?> 
          href="index.php">Home</a>
      </li>
      <li class="nav-item my-2">
        <a class="nav-link mx-lg-2 <?php echo ($current_page == 'shop.php') ? 'active' : ''; ?>" 
          <?php echo ($current_page == 'shop.php') ? 'aria-current="page"' : ''; ?> 
          href="shop.php">Shop</a>
      </li>
      <li class="nav-item my-2">
        <a class="nav-link mx-lg-2 <?php echo ($current_page == 'aboutus.php') ? 'active' : ''; ?>" 
          <?php echo ($current_page == 'aboutus.php') ? 'aria-current="page"' : ''; ?> 
          href="aboutus.php">About</a>
      </li>
    </ul>
      </div>
    </div>
    <form class="d-none d-lg-flex ms-auto pe-2" action="shop.php" method="GET">
      <div class="search-icon-wrapper">
        <input class="form-control" type="search" name="search" placeholder="Search" aria-label="Search">
        <button type="submit" class="search-icon border-0 bg-transparent">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </form>
    
    <?php if ($username === 'Guest'): ?>
      <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="nav-icon d-flex align-items-center mx-lg-3 text-decoration-none d-lg-flex">
        <i class="bx bx-user fs-4"></i>
        <span class="ms-2 d-none d-md-flex d-lg-flex">Hello, <?php echo $username; ?></span>
      </a>
    <?php else: ?>
      <div class="nav-item dropdown">
        <a class="dropdown-toggle nav-icon d-flex align-items-center text-decoration-none d-lg-flex" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-user fs-4"></i>
          <span class="ms-2 d-none d-md-flex d-lg-flex">Hello, <?php echo $username; ?></span>
        </a>
        <ul class="dropdown-menu">
        <?php if ($is_admin): ?>
          <li><a class="dropdown-item" href="../admin/index.php">
          <i class="bx bx-cog me-2"></i>Admin Panel
          </a></li>
          <li><hr class="dropdown-divider"></li>
        <?php else: ?>
          <li><a class="dropdown-item d-flex align-items-center" href="profile.php">
          <i class="bx bx-user-circle me-2"></i>My Profile</a></li>
          <li><hr class="dropdown-divider"></li>
        <?php endif; ?>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="bx bx-log-out me-2"></i><span class="pb-3 justify-content-center">Logout</span></a></li>
        </ul>
      </div>
      
      <!-- Notification Icon - Only shown when logged in --> 
      <div class="notification-icon-wrapper mx-lg-2 dropdown">
        <a class="nav-icon d-flex text-decoration-none" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-bell fs-4"></i>
        </a>
        <span class="notification-badge d-none">0</span>
        
        <!-- Notification Dropdown -->
        <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0" aria-labelledby="notificationDropdown">
          <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <h6 class="m-0">Notifications</h6>
            <?php if (!empty($notifications)): ?>
            <button class="btn btn-sm text-primary" id="markAllAsRead">Mark all as read</button>
            <?php endif; ?>
          </div>
          <div class="notification-body">
            <?php
            $hasNotifications = false;
            $notifications = [
              // Sample notifications - in production these would come from database
              ['id' => 1, 'message' => 'Your order #1234 has been shipped!', 'date' => '2023-10-01', 'is_read' => false],
              ['id' => 2, 'message' => 'New arrivals in your favorite category', 'date' => '2023-09-29', 'is_read' => false],
              ['id' => 3, 'message' => 'New arrivals in your favorite category', 'date' => '2023-09-29', 'is_read' => false],
              ['id' => 4, 'message' => 'New arrivals in your favorite category', 'date' => '2023-09-29', 'is_read' => false]
            ];

            if (empty($notifications)) {
              echo '<div class="text-center p-4 empty-notifications">
                      <i class="bx bx-bell-off fs-1 text-muted"></i>
                      <p class="mt-2 mb-0">No notifications right now</p>
                    </div>';
            } else {
              $hasNotifications = true;
              echo '<div class="notification-list">';
              foreach ($notifications as $notification) {
                $readClass = $notification['is_read'] ? 'notification-read' : 'notification-unread';
                echo '<div class="notification-item '.$readClass.'" data-notification-id="'.$notification['id'].'">
                        <div class="notification-content">
                          <p class="mb-1">'.$notification['message'].'</p>
                          <small class="text-muted">'.$notification['date'].'</small>
                        </div>
                        <button class="btn btn-sm mark-as-read" title="Mark as read">
                          <i class="bx bx-check"></i>
                        </button>
                      </div>';
              }
              echo '</div>';
            }
            ?>
          </div>
          <?php if (!empty($notifications)): ?>
          <div class="notification-footer p-2 text-center border-top">
            <a href="notifications.php" class="small" style="color:coral;">View all notifications</a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
    
    <?php if (!$is_admin): ?>
    <div class="order-icon-wrapper ms-lg-2 ms-2 <?php echo $hide_cart ? 'd-none' : ''; ?>">
    <a class="nav-icon d-flex text-decoration-none">
        <i class="bx bx-shopping-bag fs-4"
           style="cursor: pointer;"
           data-bs-toggle="offcanvas"
           data-bs-target="#offcanvasCart"
           aria-controls="offcanvasCart">
        </i>
    </a>
    <span class="cart-badge">0</span>
    </div>
    <?php endif; ?>
    <button class="navbar-toggler pe-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<script src="js/url-cleaner.js"></script>
<script>
  // Notification functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Get notification badge
    const notificationBadge = document.querySelector('.notification-badge');
    
    // Function to update notification badge
    function updateNotificationBadge() {
      <?php if (isset($hasNotifications) && $hasNotifications): ?>
      const unreadNotifications = document.querySelectorAll('.notification-unread').length;
      if (unreadNotifications > 0) {
        notificationBadge.textContent = unreadNotifications;
        notificationBadge.classList.remove('d-none');
      } else {
        notificationBadge.classList.add('d-none');
      }
      <?php endif; ?>
    }
    
    // Initial update
    updateNotificationBadge();
    
    // Event listeners for mark as read buttons
    document.querySelectorAll('.mark-as-read').forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop event from bubbling up and closing the dropdown
        const notificationItem = this.closest('.notification-item');
        const notificationId = notificationItem.dataset.notificationId;
        
        // Here you would make an AJAX call to mark as read in the database
        // For now just update the UI
        notificationItem.classList.remove('notification-unread');
        notificationItem.classList.add('notification-read');
        updateNotificationBadge();
      });
    });
    
    // Mark all as read
    const markAllBtn = document.getElementById('markAllAsRead');
    if (markAllBtn) {
      markAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop event from bubbling up and closing the dropdown
        
        // Here you would make an AJAX call to mark all as read
        document.querySelectorAll('.notification-unread').forEach(item => {
          item.classList.remove('notification-unread');
          item.classList.add('notification-read');
        });
        updateNotificationBadge();
      });
    }
    
    // Prevent dropdown from closing when clicking inside it
    const notificationDropdown = document.querySelector('.notification-dropdown');
    if (notificationDropdown) {
      notificationDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    }
  });
</script>