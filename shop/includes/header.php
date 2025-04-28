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
        <span class="ms-3" id="offcanvasUsername">Hello, <?php echo $username; ?></span>
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
    
    <div id="userAccountSection" data-user-status="<?php echo ($username === 'Guest') ? 'guest' : 'logged-in'; ?>" data-is-admin="<?php echo $is_admin ? 'true' : 'false'; ?>">
      <?php if ($username === 'Guest'): ?>
        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="nav-icon d-flex align-items-center me-lg-3 text-decoration-none d-lg-flex" id="guestLoginLink">
          <i class="bx bx-user fs-4"></i>
          <span class="ms-2 d-none d-md-flex d-lg-flex" id="navbarUsername">Hello, <?php echo $username; ?></span>
        </a>
      <?php else: ?>
        <div class="nav-item dropdown me-lg-3">
          <a class="dropdown-toggle nav-icon d-flex align-items-center text-decoration-none d-lg-flex" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-user fs-4"></i>
            <span class="ms-2 d-none d-md-flex d-lg-flex" id="navbarUsername">Hello, <?php echo $username; ?></span>
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
    </div>
    
    <!-- Notification Icon -->
    <?php if ($username !== 'Guest' && !$is_admin): ?>
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

<!-- Add this script to dynamically update the header -->
<script>
// Function to update header after login/signup
function updateHeaderAfterAuth(username, isAdmin = false) {
  // Update username displays
  const navbarUsername = document.getElementById('navbarUsername');
  const offcanvasUsername = document.getElementById('offcanvasUsername');
  
  if (navbarUsername) navbarUsername.textContent = `Hello, ${username}`;
  if (offcanvasUsername) offcanvasUsername.textContent = `Hello, ${username}`;
  
  // Update user account section
  const userAccountSection = document.getElementById('userAccountSection');
  if (!userAccountSection) return;
  
  // Update data attributes
  userAccountSection.dataset.userStatus = 'logged-in';
  userAccountSection.dataset.isAdmin = isAdmin ? 'true' : 'false';
  
  // Replace the guest login link with dropdown if needed
  if (userAccountSection.querySelector('#guestLoginLink')) {
    // Create the logged-in dropdown menu HTML
    const dropdownHtml = `
      <div class="nav-item dropdown me-lg-3">
        <a class="dropdown-toggle nav-icon d-flex align-items-center text-decoration-none d-lg-flex" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-user fs-4"></i>
          <span class="ms-2 d-none d-md-flex d-lg-flex" id="navbarUsername">Hello, ${username}</span>
        </a>
        <ul class="dropdown-menu">
          ${isAdmin ? 
            `<li><a class="dropdown-item" href="../admin/index.php">
              <i class="bx bx-cog me-2"></i>Admin Panel
            </a></li>
            <li><hr class="dropdown-divider"></li>` : 
            `<li><a class="dropdown-item d-flex align-items-center" href="profile.php">
              <i class="bx bx-user-circle me-2"></i>My Profile</a></li>
            <li><hr class="dropdown-divider"></li>`
          }
          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bx bx-log-out me-2"></i><span class="pb-3 justify-content-center">Logout</span></a></li>
        </ul>
      </div>
    `;
    
    userAccountSection.innerHTML = dropdownHtml;
  }
  
  // Show notification icon if not admin
  if (!isAdmin) {
    const notificationWrapper = document.querySelector('.notification-icon-wrapper');
    if (notificationWrapper && notificationWrapper.classList.contains('d-none')) {
      notificationWrapper.classList.remove('d-none');
    }
  }
}

// New function to update header after logout
function updateHeaderAfterLogout() {
  // Update username displays
  const navbarUsername = document.getElementById('navbarUsername');
  const offcanvasUsername = document.getElementById('offcanvasUsername');
  
  if (navbarUsername) navbarUsername.textContent = 'Hello, Guest';
  if (offcanvasUsername) offcanvasUsername.textContent = 'Hello, Guest';
  
  // Update user account section to show login link
  const userAccountSection = document.getElementById('userAccountSection');
  if (!userAccountSection) return;
  
  // Update data attributes
  userAccountSection.dataset.userStatus = 'guest';
  userAccountSection.dataset.isAdmin = 'false';
  
  // Replace dropdown with guest login link
  userAccountSection.innerHTML = `
    <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="nav-icon d-flex align-items-center me-lg-3 text-decoration-none d-lg-flex" id="guestLoginLink">
      <i class="bx bx-user fs-4"></i>
      <span class="ms-2 d-none d-md-flex d-lg-flex" id="navbarUsername">Hello, Guest</span>
    </a>
  `;
  
  // Hide notification icon
  const notificationWrapper = document.querySelector('.notification-icon-wrapper');
  if (notificationWrapper) {
    notificationWrapper.classList.add('d-none');
  }
  
  // Hide cart badge or reset to 0
  const cartBadge = document.querySelector('.cart-badge');
  if (cartBadge) {
    cartBadge.textContent = '0';
  }

  // Show success toast notification
  showLogoutSuccessToast();
}

// Function to show a toast notification after logout
function showLogoutSuccessToast() {
  // Create toast container if it doesn't exist
  let toastContainer = document.querySelector('.toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    document.body.appendChild(toastContainer);
  }
  
  // Create toast element
  const toastId = 'logoutToast' + Date.now();
  const toast = document.createElement('div');
  toast.className = 'toast align-items-center text-white bg-dark';
  toast.id = toastId;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');
  
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">
        <i class="fas fa-check-circle me-2"></i> You have been successfully logged out.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;
  
  toastContainer.appendChild(toast);
  
  // Initialize and show the toast
  const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
  bsToast.show();
  
  // Remove toast after it's hidden
  toast.addEventListener('hidden.bs.toast', function() {
    toast.remove();
  });
}

// Make the functions globally available
window.updateHeaderAfterAuth = updateHeaderAfterAuth;
window.updateHeaderAfterLogout = updateHeaderAfterLogout;
</script>

<script src="js/url-cleaner.js"></script>
<script src="js/header-notifications.js"></script>
<script src="js/assistant.js"></script>