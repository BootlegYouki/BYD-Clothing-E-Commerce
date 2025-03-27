<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
        <span class="ms-1 text-sm text-dark">Beyond Doubt Clothing</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'index.php') ? 'active bg-gradient-dark text-white' : 'text-dark'; ?>" href="index.php">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'homepage-customize.php') ? 'active bg-gradient-dark text-white' : 'text-dark'; ?>" href="homepage-customize.php">
            <i class="material-symbols-rounded opacity-5">design_services</i>
            <span class="nav-link-text ms-1">Homepage Design</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'products.php') ? 'active bg-gradient-dark text-white' : 'text-dark'; ?>" href="products.php">
            <i class="material-symbols-rounded opacity-5">inventory_2</i>
            <span class="nav-link-text ms-1">Products</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'add-product.php') ? 'active bg-gradient-dark text-white' : 'text-dark'; ?>" href="add-product.php">
          <i class="material-symbols-rounded opacity-5">add_box</i>
            <span class="nav-link-text ms-1">Add Products</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentPage == 'tables.html') ? 'active bg-gradient-dark text-white' : 'text-dark'; ?>" href="../pages/tables.html">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Tables</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
    <div class="mx-3">
      <a class="btn bg-gradient-dark w-100" href="../shop/includes/logout_process.php" type="button">
        <i class="material-symbols-rounded me-2" style="vertical-align: middle; font-size: 16px;">logout</i>
        Logout
      </a>
    </div>
    </div>
  </aside>
  