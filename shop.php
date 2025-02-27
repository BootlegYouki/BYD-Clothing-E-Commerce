<?php
if (!isset($originalPrice)) {
    $originalPrice = 1200;
}

if (!isset($price)) {
    $price = 780;
}

$discount = round((($originalPrice - $price) / $originalPrice) * 100);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyond Doubt Clothing</title> 
    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="icon" href="img/logo/BYD-removebg-preview.ico" type="image/x-icon">
    <!-- ICONSCSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=account_circle,menu,person,search,shopping_bag" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="shop.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'navbar.php'; ?>

    <!-- BANNER -->
    <section class="top-image-container">
        <img src="img/logo/Banner.png" alt="Banner" class="top-image">
      </section>      

    <!-- LABEL -->
    <section id="label" class="my-5 py-2">
        <div class="container-fluid">
            <div class="small-container mt-1 py-1 d-flex justify-content-between align-items-center">
                <h3>All Collections</h3>
                <div class="d-flex align-items-center gap-3">
                    <p class="mb-0">Sort by:</p>
                    <select class="form-select w-auto">
                        <option>All</option>
                        <option>Price</option>
                        <option>Sale</option>
                        <option>Popularity</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

  <!-- PRODUCTS
    <section id="products">
        <div class="small-container">
          <div class="product">
            <a href="details.html">
            <img src="img/t-shirt/shirt7.png" alt="shirt">
          </a>
            <h4>BYD "EROS" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview" data-bs-toggle="modal" data-bs-target="#productModal">View</button>
          </div>

          <div class="product">
            <img src="img/t-shirt/shirt2.png" alt="shirt">
            <h4>BYD "GAVIN" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview">View</button>
          </div>

          <div class="product">
            <img src="img/t-shirt/shirt3.png" alt="shirt">
            <h4>BYD "GINO" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview">View</button>
          </div>

          <div class="product">
            <img src="img/t-shirt/shirt4.png" alt="shirt">
            <h4>BYD "BRAD" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview">View</button>
          </div>

          <div class="product">
            <img src="img/t-shirt/shirt5.png" alt="shirt">
            <h4>BYD "ARON" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview">View</button>
          </div>

          <div class="product">
            <img src="img/t-shirt/shirt6.png" alt="shirt">
            <h4>BYD "MEDI" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview">View</button>
          </div>

          <div class="product">
            <img src="img/t-shirt/shirt1.png" alt="shirt">
            <h4>BYD "MEYSA" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview">View</button>
          </div>

          <div class="product">
            <img src="img/t-shirt/shirt8.png" alt="shirt">
            <h4>BYD "INFERNO" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview">View</button>
          </div>
        </div>
      </section>

  Modal
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fa-solid fa-x"></i>
        </button>
      </div>

      <div class="modal-body d-flex align-items-center gap-4 p-4">
        <img src="img/t-shirt/shirt7.png" alt="shirt" class="img-fluid product-img">
        <div class="product-details">
          <h2 class="product-title">BYD "EROS" DESIGN - DRIFIT</h2>
          <p class="product-price">₱399.00</p>

          <div class="mb-3">
            <label for="size" class="form-label">Size:</label>
            <select id="size" class="form-select">
              <option value="S">Small</option>
              <option value="M">Medium</option>
              <option value="L">Large</option>
              <option value="XL">X-Large</option>
            </select>
          </div>

          <div class="mb-4">
            <label for="quantity" class="form-label">Quantity</label>
            <select id="quantity" class="form-select quantity-selector">
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
            </select>
          </div>

          <button type="button" class="btn btn-dark w-100 add-to-cart-btn">Add to Cart</button>
        </div>
      </div>
    </div>
  </div>
</div> -->

<!-- PRODUCTS -->
<section id="products">
    <div class="small-container">
        <div class="product">
            <a href="details.html">
                <img src="img/t-shirt/shirt7.png" alt="shirt" loading="lazy">
            </a>
            <h4>BYD "EROS" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview" 
                    data-bs-toggle="modal" 
                    data-bs-target="#productModal"
                    data-img="img/t-shirt/shirt7.png"
                    data-title="BYD &quot;EROS&quot; DESIGN - DRIFIT"
                    data-price="₱399.00">
                View
            </button>
        </div>

        <div class="product">
            <img src="img/t-shirt/shirt2.png" alt="shirt" loading="lazy">
            <h4>BYD "GAVIN" DESIGN - DRIFIT</h4>
            <p>₱399.00</p>
            <button class="quickview" 
                    data-bs-toggle="modal" 
                    data-bs-target="#productModal"
                    data-img="img/t-shirt/shirt2.png"
                    data-title="BYD &quot;GAVIN&quot; DESIGN - DRIFIT"
                    data-price="₱399.00">
                View
            </button>
        </div>

        <div class="product">
          <img src="img/t-shirt/shirt3.png" alt="shirt">
          <h4>BYD "GINO" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/t-shirt/shirt3.png"
          data-title="BYD &quot;GINO&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <img src="img/t-shirt/shirt4.png" alt="shirt">
          <h4>BYD "BRAD" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/t-shirt/shirt4.png"
          data-title="BYD &quot;BRAD&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <img src="img/t-shirt/shirt5.png" alt="shirt">
          <h4>BYD "ARON" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/t-shirt/shirt5.png"
          data-title="BYD &quot;ARON&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <img src="img/t-shirt/shirt6.png" alt="shirt">
          <h4>BYD "MEDI" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/t-shirt/shirt6.png"
          data-title="BYD &quot;MEDI&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <img src="img/t-shirt/shirt1.png" alt="shirt">
          <h4>BYD "MEYSA" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/t-shirt/shirt1.png"
          data-title="BYD &quot;MEYSA&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <div class="product">
          <img src="img/t-shirt/shirt8.png" alt="shirt">
          <h4>BYD "INFERNO" DESIGN - DRIFIT</h4>
          <p>₱399.00</p>
          <button class="quickview" data-bs-toggle="modal" 
          data-bs-target="#productModal"
          data-img="img/t-shirt/shirt8.png"
          data-title="BYD &quot;INFERNO&quot; DESIGN - DRIFIT"
          data-price="₱399.00">View</button>
        </div>

        <!-- can add other product entries ... -->
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-x"></i>
                </button>
            </div>
            <div class="modal-body d-flex align-items-center gap-4 p-4">
                <img src="" alt="product image" class="img-fluid product-img" id="modalProductImage">
                <div class="product-details">
                    <h2 class="product-title" id="modalProductTitle"></h2>
                    <p class="product-price" id="modalProductPrice"></p>
                    <div class="mb-3">
                        <label for="size" class="form-label">Size:</label>
                        <select id="size" class="form-select">
                          <option value="S">Small</option>
                          <option value="M">Medium</option>
                          <option value="L">Large</option>
                          <option value="XL">X-Large</option>
                        </select>
                    </div>
          
                    <div class="mb-4">
                      <label for="quantity" class="form-label">Quantity</label>
                      <select id="quantity" class="form-select quantity-selector">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                      </select>
                    </div>
          
                    <button type="button" class="btn btn-dark w-100 add-to-cart-btn">Add to Cart</button>

                    <!-- can add rest of modal content ... -->
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- PAGINATION -->
    <nav aria-label="Page navigation example">
        <ul class="pagination">
          <li class="page-item">
            <a class="page-link" href="#" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>
          <li class="page-item"><a class="page-link" href="#">1</a></li>
          <li class="page-item"><a class="page-link" href="#">2</a></li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item">
            <a class="page-link" href="#" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </nav>

    <!-- FOOTER -->
    <footer class="py-2">
        <div class="row container mx-auto pt-5 mb-5">
            <div class="footer-one col-lg-3 col-md-7 col-12">
              <img src="img/logo/logo2.webp" alt="logo" class="img-fluid img-10">
              <p class="my-3">We offer fully customized sublimation services:</p>
              <ul class="text-uppercase list-unstyled">
                <li>T-shirt</li>
                <li>Polo Shirt</li>
                <li>Basketball Jersey</li>
                <li>Long Sleeves</li>
              </ul>
            </div>
            <!-- Products --> 
            <div class="footer-one col-lg-5 col-md-6 col-12">
              <h5 class="pb-2">Products</h5>
              <ul class="text-uppercase list-unstyled">
                <li class="my-1"><a href="#">T-SHIRT Collections</a></li>
                <li class="my-1"><a href="#">FIRST Released - LONG SLEEVE Collections</a></li>
                <li class="my-1"><a href="#">MECHA - LONG SLEEVES Collections</a></li>
              </ul>
            </div>
            <!-- Contact -->
            <div class="footer-one col-lg-3 col-md-6 col-12">
              <h5 class="pb-2">Contact Us</h5>
              <div>
                <h6 class="text-uppercase">address</h6>
                <li class="list-unstyled mb-3 text-uppercase"><a
                    href="https://maps.app.goo.gl/A3EBEo5AkcxrYoMh6" target="_blank" rel="noopener noreferrer">
                  Blk 27 Lot 12 Pechayan Kanan Namasape HOA, Commonwealth Ave. North Fairview QC, Quezon City, Philippines
                </a>
              </li>
              </div>
              <div>
                <h6 class="text-uppercase">phone</h6>
                <p>0905 507 9634</p>
              </div>
            </div>
          </div>
        </div>
        <div class="copyright mt-5 mb-3">
            <div class="row container mx-auto">
                <div class="col-lg-3 col-md-6 col-12 d-flex align-items-center mb-3">
                    <img src="img/payment.png" alt="" class="img-fluid img-11">
                </div>
                <div class="col-lg-5 col-md-6 col-12 d-flex align-items-center mb-3">
                    <p class="mb-0">&copy; 2025 Beyond Doubt Clothing. All Rights Reserved.</p>
                  </div>
                <div class="col-lg-4 col-md-6 col-12 d-flex align-items-center mb-3">
                    <a href="https://www.facebook.com/profile.php?id=100094756167660"><i class="fa fa-facebook"></i></a>
                    <a href="https://www.instagram.com/beyonddoubt.clothing"><i class="fa fa-instagram"></i></a>
                </div>
                </div>
            </div>
        </div>
    </footer>
    <!--SCRIPT-->
    <script src="script1.js"></script>
    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- BOOTSTRAP JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaqYfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>