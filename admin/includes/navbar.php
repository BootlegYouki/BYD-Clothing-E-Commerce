<?php
// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Define page titles and their display names
$page_titles = [
  'index' => 'Dashboard',
  'products' => 'Products Management',
  'add-product' => 'Add New Product',
  'edit-product' => 'Edit Product',
  'homepage-customize' => 'Homepage Customization',
  'categories' => 'Categories Management',
  'orders' => 'Orders Management',
  'customers' => 'Customers Management',
  'profile' => 'Admin Profile',
  'settings' => 'Settings'
];

// Get current page title, default to Dashboard if not found
$page_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'Dashboard';

// Define breadcrumb paths
$breadcrumbs = [
  'index' => [['Home', 'index.php', false]],
  'products' => [['Home', 'index.php', true], ['Products', '', false]],
  'add-product' => [['Home', 'index.php', true], ['Products', 'products.php', true], ['Add New', '', false]],
  'edit-product' => [['Home', 'index.php', true], ['Products', 'products.php', true], ['Edit', '', false]],
  'homepage-customize' => [['Home', 'index.php', true], ['Homepage', '', false]],
  'categories' => [['Home', 'index.php', true], ['Categories', '', false]],
  'orders' => [['Home', 'index.php', true], ['Orders', '', false]],
  'customers' => [['Home', 'index.php', true], ['Customers', '', false]],
  'profile' => [['Home', 'index.php', true], ['Profile', '', false]],
  'settings' => [['Home', 'index.php', true], ['Settings', '', false]]
];

// Get current page breadcrumb, default to Home if not found
$current_breadcrumb = isset($breadcrumbs[$current_page]) ? $breadcrumbs[$current_page] : [['Home', 'index.php', false]];
?>

<nav class="top-navbar sticky-top" style="top: 25px;">
  <div class="d-flex align-items-center">
    <!-- Add sidebar toggle button for mobile -->
    <button class="btn sidebar-toggle d-md-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
      <i class="material-symbols-rounded">menu</i>
    </button>
    
    <div>
      <h5 class="mb-0 text-dark"><?= $page_title ?></h5>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 d-lg-flex d-none">
          <?php foreach($current_breadcrumb as $index => $crumb): 
            list($name, $link, $has_link) = $crumb;
            $is_last = ($index == count($current_breadcrumb) - 1);
          ?>
            <?php if($has_link): ?>
              <li class="breadcrumb-item">
                <a href="<?= $link ?>" class="text-decoration-none"><?= $name ?></a>
              </li>
            <?php else: ?>
              <li class="breadcrumb-item <?= $is_last ? 'active' : '' ?>" <?= $is_last ? 'aria-current="page"' : '' ?>>
                <?= $name ?>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
      </nav>
    </div>
  </div>
  
  <div class="d-flex align-items-center">
    <div class="mode-toggle">
      <button id="darkModeToggle" class="btn-mode-toggle" aria-label="Toggle Dark Mode">
        <i class="material-symbols-rounded mode-icon mode-icon-light">light_mode</i>
        <i class="material-symbols-rounded mode-icon mode-icon-dark">dark_mode</i>
      </button>
    </div>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="avatar rounded-circle bg-light me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
          <i class="material-symbols-rounded">person</i>
        </div>
        <div>
          <span class="d-lg-block d-none mb-0 text-sm text-dark">Admin</span>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
        <li><a class="dropdown-item" href="#">Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="../shop/includes/logout_process.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Check for saved theme preference or use default light theme
  const currentTheme = localStorage.getItem('theme') || 'light';
  
  // Apply the theme class to the body
  document.body.classList.add(`theme-${currentTheme}`);
  
  // Make sure html element also has the theme class
  document.documentElement.classList.add(`theme-${currentTheme}`);
  
  // Set the toggle to match current theme
  if (currentTheme === 'dark') {
    document.getElementById('darkModeToggle').classList.add('active');
  }
  
  // Toggle dark/light mode
  document.getElementById('darkModeToggle').addEventListener('click', function() {
    this.classList.toggle('active');
    
    // If body has dark theme, switch to light, else switch to dark
    if (document.body.classList.contains('theme-dark')) {
      document.body.classList.replace('theme-dark', 'theme-light');
      document.documentElement.classList.replace('theme-dark', 'theme-light');
      localStorage.setItem('theme', 'light');
    } else {
      document.body.classList.replace('theme-light', 'theme-dark');
      document.documentElement.classList.replace('theme-light', 'theme-dark');
      localStorage.setItem('theme', 'dark');
    }
  });
});
</script>