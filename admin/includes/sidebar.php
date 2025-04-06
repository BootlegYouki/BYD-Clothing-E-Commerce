

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
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
          <a class="nav-link <?php echo ($currentPage == 'orders.php') ? 'active' : ''; ?>" href="orders.php">
            <i class="material-symbols-rounded">receipt_long</i>
            <span>Orders</span>
            <span class="nav-badge">5</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'customers.php') ? 'active' : ''; ?>" href="customers.php">
            <i class="material-symbols-rounded">people</i>
            <span>Customers</span>
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
              <span class="nav-badge">5</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentPage == 'customers.php') ? 'active' : ''; ?>" 
               href="customers.php" onclick="closeOffcanvas()">
              <i class="material-symbols-rounded">people</i>
              <span>Customers</span>
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

<script>
  // Function to properly close the offcanvas
  function closeOffcanvas() {
    const offcanvasElement = document.getElementById('sidebarOffcanvas');
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
    
    if (bsOffcanvas) {
      bsOffcanvas.hide();
    }
  }
  
  // Initialize offcanvas and handle older code
  document.addEventListener('DOMContentLoaded', function() {
    // Make sure the Bootstrap JS is loaded
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap JavaScript is not loaded. Make sure to include it.');
      return;
    }
    
    // Handle older toggle script for compatibility
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', function() {
        if (window.innerWidth >= 768) {
          sidebar.classList.toggle('show');
        }
      });
    }
    
    // Handle clicks outside the sidebar for desktop version
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