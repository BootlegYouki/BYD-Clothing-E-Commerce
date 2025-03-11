<nav class="navbar navbar-expand-lg bg-white fixed-top shadow">
  <div class="container-fluid">
    <img src="img/logo/logo.webp" alt="logo" class="imglogo me-auto">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
      <img src="img/logo/logo.webp" alt="logo" class="imglogo">
        <span class="ms-3">Hello, Username</span>
      </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>        
        <div class="offcanvas-body">
            <form class="d-block d-lg-none ms-auto">
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
    <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="nav-icon d-none align-items-center me-3 text-decoration-none d-lg-flex">
      <i class="bx bx-user fs-4"></i>
      <span class="ms-2">Hello, Username</span>
    </a>
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

<?php include 'includes/LoginModal.php'; ?>
<?php include 'includes/SignupModal.php'; ?>
<?php include 'includes/terms.php'; ?>