<nav class="navbar navbar-expand-lg navbar-light bg-light py-3 sticky-top">
    <div class="container-fluid navbar-container-padding">
        <img src="img/logo/logo.webp" alt="logo" class="img-fluid img-10">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" 
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span>
                <i id="bar" class="material-symbols-outlined">menu</i>
            </span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item d-flex align-items-center search-li">
                <div class="search-container">
                    <input class="form-control search-input" type="search" placeholder="Search" aria-label="Search">
                    <i class="material-symbols-outlined search-icon">search</i>
                </div>
                </li>
                <li class="nav-item d-flex align-items-center cart-li">
                <span class="cart-number">0</span>
                <i class="material-symbols-outlined">shopping_bag</i>
                </li>
                <li class="nav-item dropdown user-button">
                    <a class="nav-link dropdown-toggle d-flex align-items-center p-0 user-button" href="#" role="button" 
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="material-symbols-outlined person-icon">person</i>
                        Hello, User
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>