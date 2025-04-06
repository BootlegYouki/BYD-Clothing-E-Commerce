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
  'user-profile' => 'User Profile',
  'settings' => 'Settings'
];

// Get current page title, default to Dashboard if not found
$page_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'Dashboard';

// Define breadcrumb paths
$breadcrumbs = [
  'index' => [['Main', 'index.php', false]],
  'products' => [['Home', 'index.php', true], ['Products', '', false]],
  'add-product' => [['Home', 'index.php', true], ['Products', 'products.php', true], ['Add New', '', false]],
  'edit-product' => [['Home', 'index.php', true], ['Products', 'products.php', true], ['Edit', '', false]],
  'homepage-customize' => [['Home', 'index.php', true], ['Homepage', '', false]],
  'categories' => [['Home', 'index.php', true], ['Categories', '', false]],
  'orders' => [['Home', 'index.php', true], ['Orders', '', false]],
  'customers' => [['Home', 'index.php', true], ['Customers', '', false]],
  'user-profile' => [['Home', 'index.php', true], ['Profile', '', false]],
  'settings' => [['Home', 'index.php', true], ['Settings', '', false]]
];

// Get current page breadcrumb, default to Home if not found
$current_breadcrumb = isset($breadcrumbs[$current_page]) ? $breadcrumbs[$current_page] : [['Home', 'index.php', false]];
?>

<!-- Add this style tag to prevent flash -->
<style>
  /* Critical theme styles applied immediately */
  html.theme-preload * {
    transition: none !important;
  }
  
  /* Base theme styles that should be applied immediately */
  html.theme-dark {
    background-color: #1a1f2b !important; 
    color: #e1e1e1 !important;
  }
  
  html.theme-light {
    background-color: #ffffff !important;
    color: #212529 !important;
  }
  
  /* Text and UI elements */
  html.theme-dark .text-dark {
    color: #e1e1e1 !important;
  }
  
  html.theme-dark .breadcrumb-item.active {
    color: #adb5bd !important;
  }
  
  html.theme-dark .dropdown-menu {
    background-color: #2a3042 !important;
    border-color: #343a40 !important;
  }
  
  html.theme-dark .dropdown-item {
    color: #e1e1e1 !important;
  }
  
  html.theme-dark .dropdown-item:hover {
    background-color: #3a3f53 !important;
  }
  
  /* Logo visibility control */
  .theme-light-logo, .theme-dark-logo {
    display: none !important;
  }
  
  html.theme-light .theme-light-logo {
    display: inline-block !important;
  }
  
  html.theme-dark .theme-dark-logo {
    display: inline-block !important;
  }
</style>

<script>
  // Apply theme immediately to prevent flash
  (function() {
    // Apply the preload class to prevent transitions during initial load
    document.documentElement.classList.add('theme-preload');
    
    // Get theme from localStorage
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply theme class immediately
    document.documentElement.classList.add(`theme-${currentTheme}`);
    
    // Add critical inline styles for fast rendering
    const criticalStyles = document.createElement('style');
    criticalStyles.textContent = `
      body { 
        background-color: ${currentTheme === 'dark' ? '#1a1f2b' : '#ffffff'} !important; 
        color: ${currentTheme === 'dark' ? '#e1e1e1' : '#212529'} !important;
        opacity: 1 !important;
      }
    `;
    document.head.appendChild(criticalStyles);
    
    // Set a flag to indicate the initial theme has been applied
    window.__themeInitialized = true;
    
    // Handle navigation to prevent flash between pages
    document.addEventListener('click', function(event) {
      // Only handle clicks on links within this domain
      if (event.target.tagName === 'A' && event.target.href && 
          event.target.href.startsWith(window.location.origin) && 
          !event.target.href.includes('#') &&
          !event.ctrlKey && !event.shiftKey && !event.metaKey) {
        
        // Store current theme before navigation
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        // Add a very short delay to ensure smooth transition
        event.preventDefault();
        document.body.style.opacity = '0.8';
        document.body.style.transition = 'opacity 0.1s';
        
        setTimeout(function() {
          window.location.href = event.target.href;
        }, 50);
      }
    });
  })();
  
  // On DOMContentLoaded, remove preload class
  document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      document.documentElement.classList.remove('theme-preload');
    }, 100);
  });
</script>

<nav class="top-navbar d-flex justify-content-between align-items-center">
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
        <li><a class="dropdown-item" href="#">Profile</a></li>
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