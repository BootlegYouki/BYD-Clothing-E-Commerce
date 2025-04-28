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
      <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="nav-icon d-flex align-items-center me-lg-3 text-decoration-none d-lg-flex">
        <i class="bx bx-user fs-4"></i>
        <span class="ms-2 d-none d-md-flex d-lg-flex">Hello, <?php echo $username; ?></span>
      </a>
    <?php else: ?>
      <div class="nav-item dropdown me-lg-3">
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
    <?php endif; ?>
    
    <!-- Notification Icon -->
    <?php if ($username !== 'Guest'): ?>
    <div class="notification-icon-wrapper mx-1">
      <div class="dropdown">
        <a class="nav-icon d-flex text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationDropdown">
          <i class="bx bx-bell fs-4"></i>
        </a>
        <span class="notification-badge" style="display: none;">0</span>
        <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0" aria-labelledby="notificationDropdown">
          <div class="notification-header p-3 border-bottom position-sticky top-0 bg-white z-index-1000">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="m-0 z">Notifications</h5>
              <h5 class="m-0"><a href="notifications.php" class="text-decoration-none text-coral">View All</a></h5>
            </div>
          </div>
          <div class="notification-body">
            <!-- If no notifications -->
            <div class="text-center py-4 empty-notification d-none">
              <i class="bx bx-bell-off fs-1 text-muted"></i>
              <p class="text-muted mt-2">No new notifications</p>
            </div>
            
            <!-- Loading indicator -->
            <div class="text-center py-4 loading-notifications">
              <div class="spinner-border spinner-border-sm text-coral" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="text-muted mt-2">Loading notifications...</p>
            </div>

            <!-- Notifications will be dynamically loaded here -->
            <div class="notification-list"></div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if (!$is_admin): ?>
    <div class="order-icon-wrapper ms-2 <?php echo $hide_cart ? 'd-none' : ''; ?>">
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
<script src="js/notifications.js"></script>
<script src="js/assistant.js"></script>