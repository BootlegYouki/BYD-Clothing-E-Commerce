<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Include dashboard functions if not already included
if (!function_exists('getPendingOrdersCount')) {
    include_once dirname(__FILE__) . '/../functions/dashboard-functions.php';
}

// Get pending orders count for badge display
$pending_orders_count = 0;
if (function_exists('getPendingOrdersCount')) {
    // Get the database connection
    include_once dirname(__FILE__) . '/../config/dbcon.php';
    $pending_orders_count = getPendingOrdersCount($conn);
}

// Get unread notifications count
$notifications_count = 0;
if (function_exists('getUnreadNotificationsCount')) {
    $notifications_count = getUnreadNotificationsCount($conn);
}
$notification_badge_display = $notifications_count > 0 ? 'inline-flex' : 'none';

// Only show badge if there are pending orders
$badge_display = $pending_orders_count > 0 ? 'inline-flex' : 'none';
?>
<script>
  // This function will be called before the document loads
  (function() {
    // Apply theme immediately to prevent flash
    document.documentElement.classList.add('theme-preload');
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.classList.add(`theme-${currentTheme}`);
    document.body.classList.add(`theme-${currentTheme}`);
    
    // Remove preload class after the page is fully loaded
    window.addEventListener('load', function() {
      setTimeout(function() {
        document.documentElement.classList.remove('theme-preload');
      }, 50);
    });
  })();

  // Global shared navigation handler for consistency across sidebar and navbar
  function handleGlobalNavigation(event, href) {
    // Only process links within the domain and not anchor links
    if (!href || href.includes('#') || 
        !href.startsWith(window.location.origin) ||
        event.ctrlKey || event.shiftKey || event.metaKey) {
      return true; // Let the browser handle it normally
    }
    
    // Prevent default link behavior
    event.preventDefault();
    
    // Store current theme before navigation
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // First close the offcanvas if it's open
    const offcanvasElement = document.getElementById('sidebarOffcanvas');
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
    
    if (bsOffcanvas) {
      bsOffcanvas.hide();
    }
    
    // Add a class that will temporarily disable all transitions during navigation
    document.documentElement.classList.add('theme-transition');
    
    // Add a consistent delay to allow dark mode processing before navigation
    // Using the same delay as navbar.php for consistency
    setTimeout(function() {
      window.location.href = href;
    }, 0.1);
    
    return false;
  }
  
  // Initialize everything when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    // Make sure the Bootstrap JS is loaded
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap JavaScript is not loaded. Make sure to include it.');
      return;
    }
    
    // Remove any inline onclick handlers for consistency
    document.querySelectorAll('[onclick="closeOffcanvas()"]').forEach(el => {
      el.removeAttribute('onclick');
    });
    
    // Update all sidebar navigation links to use our global navigation handler
    const sidebarLinks = document.querySelectorAll('.sidebar a.nav-link, .offcanvas a.nav-link, .sidebar .navbar-brand, .offcanvas .navbar-brand');
    sidebarLinks.forEach(link => {
      // Remove any existing event listeners first
      const newLink = link.cloneNode(true);
      link.parentNode.replaceChild(newLink, link);
      
      // Add our global handler
      newLink.addEventListener('click', function(event) {
        handleGlobalNavigation(event, this.href);
      });
    });
    
    // Handle the "View Shop" link as well
    const viewShopLinks = document.querySelectorAll('.view-shop');
    viewShopLinks.forEach(link => {
      // Remove any existing event listeners first
      const newLink = link.cloneNode(true);
      link.parentNode.replaceChild(newLink, link);
      
      // Add our global handler
      newLink.addEventListener('click', function(event) {
        handleGlobalNavigation(event, this.href);
      });
    });
    
    // Handle sidebar toggle for desktop
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', function() {
        if (window.innerWidth >= 768) {
          sidebar.classList.toggle('show');
        }
      });
    }
    
    // Handle clicks outside the sidebar
    document.addEventListener('click', function(event) {
      if (!sidebar) return;
      
      const isClickInside = sidebar.contains(event.target) || 
                          (toggleBtn && toggleBtn.contains(event.target));
      
      if (!isClickInside && window.innerWidth < 992 && sidebar.classList.contains('show')) {
        sidebar.classList.remove('show');
      }
    });
  });
</script>
<aside class="sidebar d-none d-md-flex">
  <div class="sidebar-header">
    <a class="navbar-brand" href="index.php">
      <img src="../shop/img/logo/logo_admin_light.png" alt="BYD Clothing Logo" class="sidebar-logo theme-light-logo">
      <img src="../shop/img/logo/logo_admin_dark.png" alt="BYD Clothing Logo" class="sidebar-logo theme-dark-logo">
      <span>BYD Clothing</span>
    </a>
  </div>
  
  <div class="sidebar-content">
    <!-- Main Navigation -->
    <div class="nav-section">
      <span class="nav-section-title">Main</span>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" href="index.php">
            <i class="material-symbols-rounded">dashboard</i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'homepage-customize.php') ? 'active' : ''; ?>" href="homepage-customize.php">
            <i class="material-symbols-rounded">design_services</i>
            <span>Homepage Design</span>
          </a>
        </li>
      </ul>
    </div>
    
    <!-- Product Management -->
    <div class="nav-section">
      <span class="nav-section-title">Products</span>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'products.php') ? 'active' : ''; ?>" href="products.php">
            <i class="material-symbols-rounded">inventory_2</i>
            <span>View Products</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'add-product.php') ? 'active' : ''; ?>" href="add-product.php">
            <i class="material-symbols-rounded">add_box</i>
            <span>Add Products</span>
          </a>
        </li>
      </ul>
    </div>
    
    <!-- Customer Management -->
    <div class="nav-section">
      <span class="nav-section-title">Customers</span>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'customers.php') ? 'active' : ''; ?>" href="customers.php">
            <i class="material-symbols-rounded">people</i>
            <span>Customers</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'orders.php') ? 'active' : ''; ?>" href="orders.php">
            <i class="material-symbols-rounded">receipt_long</i>
            <span>Orders</span>
            <?php if ($pending_orders_count > 0): ?>
                <span class="nav-badge" style="display: <?= $badge_display ?>;"><?= $pending_orders_count ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'notifications.php') ? 'active' : ''; ?>" href="notifications.php">
            <i class="material-symbols-rounded">notifications</i>
            <span>Notifications</span>
            <?php if ($notifications_count > 0): ?>
                <span class="nav-badge" style="display: <?= $notification_badge_display ?>;"><?= $notifications_count ?></span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </div>
    
    <!-- Payment Management Section -->
    <div class="nav-section">
      <span class="nav-section-title">Payments</span>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'payment-settings.php') ? 'active' : ''; ?>" href="transactions.php">
            <i class="material-symbols-rounded">payments</i>
            <span>Transactions</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
  
<div class="sidebar-footer">
  <div class="footer-buttons">
    <a href="../shop/index.php" class="footer-btn view-shop">
      <i class="material-symbols-rounded">storefront</i>
      <span>View Shop</span>
    </a>
  </div>
</div>
</aside>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
<div class="offcanvas-header">
  <a class="navbar-brand" href="index.php">
    <img src="../shop/img/logo/logo_admin_light.png" alt="BYD Clothing Logo" class="sidebar-logo theme-light-logo">
    <img src="../shop/img/logo/logo_admin_dark.png" alt="BYD Clothing Logo" class="sidebar-logo theme-dark-logo">
    <span>BYD Clothing</span>
  </a>
  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
  <div class="offcanvas-body p-0">
    <!-- Same sidebar content as desktop -->
    <div class="sidebar-content">
      <!-- Main Navigation -->
      <div class="nav-section">
        <span class="nav-section-title">Main</span>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>" 
               href="index.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">dashboard</i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'homepage-customize.php') ? 'active' : ''; ?>" 
               href="homepage-customize.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">design_services</i>
              <span>Homepage Design</span>
            </a>
          </li>
        </ul>
      </div>
      
      <!-- Product Management -->
      <div class="nav-section">
        <span class="nav-section-title">Products</span>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'products.php') ? 'active' : ''; ?>" 
               href="products.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">inventory_2</i>
              <span>View Products</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'add-product.php') ? 'active' : ''; ?>" 
               href="add-product.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">add_box</i>
              <span>Add Products</span>
            </a>
          </li>
        </ul>
      </div>
      
      <!-- Customer Management -->
      <div class="nav-section">
        <span class="nav-section-title">Customers</span>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'orders.php') ? 'active' : ''; ?>" 
               href="orders.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">receipt_long</i>
              <span>Orders</span>
              <?php if ($pending_orders_count > 0): ?>
                <span class="nav-badge" style="display: <?= $badge_display ?>;"><?= $pending_orders_count ?></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'customers.php') ? 'active' : ''; ?>" 
               href="customers.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">people</i>
              <span>Customers</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'notifications.php') ? 'active' : ''; ?>" 
               href="notifications.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">notifications</i>
              <span>Notifications</span>
              <?php if ($notifications_count > 0): ?>
                <span class="nav-badge" style="display: <?= $notification_badge_display ?>;"><?= $notifications_count ?></span>
              <?php endif; ?>
            </a>
          </li>
        </ul>
      </div>
      
      <!-- Payment Management (New Section for mobile) -->
      <div class="nav-section">
        <span class="nav-section-title">Payments</span>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'transactions.php') ? 'active' : ''; ?>" 
               href="payment-settings.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">payments</i>
              <span>Transactions</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
    
    <!-- Sidebar footer for mobile -->
    <div class="sidebar-footer">
    <div class="footer-buttons">
      <a href="../shop/index.php" class="footer-btn view-shop">
        <i class="material-symbols-rounded">storefront</i>
        <span>View Shop</span>
      </a>
    </div>
  </div>
  </div>
</div>
