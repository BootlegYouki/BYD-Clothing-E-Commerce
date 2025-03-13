<?php
session_start();
$username = isset($_SESSION['username']) ? htmlentities($_SESSION['username']) : 'Guest';
?>

<nav class="navbar navbar-expand-lg bg-white fixed-top shadow">
  <div class="container-fluid">
    <img src="img/logo/logo.webp" alt="logo" class="imglogo me-auto">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <img src="img/logo/logo.webp" alt="logo" class="imglogo">
        <span class="ms-3">Hello, <?php echo $username; ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>        
      <div class="offcanvas-body">
        <!-- Search form and nav items here... -->
        <form class="d-block d-lg-none ms-auto pe-2">
      <div class="search-icon-wrapper">
        <input class="form-control" type="search" placeholder="Search" aria-label="Search">
        <span class="search-icon">
          <i class="fas fa-search"></i>
        </span>
      </div>
    </form>
        <ul class="navbar-nav justify-content-end flex-grow-1 text-center">
          <li class="nav-item my-2">
            <a class="nav-link mx-lg-2 active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item my-2">
            <a class="nav-link mx-lg-2" href="#">Shop</a>
          </li>
          <li class="nav-item my-2">
            <a class="nav-link mx-lg-2" href="#">About</a>
          </li>
          <li class="nav-item my-2">
            <a class="nav-link mx-lg-2" href="#">New Arrivals</a>
          </li>
        </ul>
      </div>
    </div>
    <form class="d-none d-lg-flex ms-auto pe-2">
      <div class="search-icon-wrapper">
        <input class="form-control" type="search" placeholder="Search" aria-label="Search">
        <span class="search-icon">
          <i class="fas fa-search"></i>
        </span>
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
          <li><a class="dropdown-item" href="includes/logout.php">Logout</a></li>
        </ul>
      </div>
    <?php endif; ?>
    
    <div class="order-icon-wrapper ms-2">
      <a href="#" class="nav-icon d-flex text-decoration-none">
        <i class="bx bx-shopping-bag fs-4"></i>
      </a>
      <span class="order-number">0</span>
    </div>
    <button class="navbar-toggler pe-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>