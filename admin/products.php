<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}
include 'config/dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Admin | Beyond Doubt Clothing</title>
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  
  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/sidebar.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid">
    <?php if(isset($_SESSION['message'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
            <strong>Success!</strong> <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('success-alert').classList.remove('show');
                setTimeout(function() {
                    document.getElementById('success-alert')?.remove();
                }, 150);
            }, 3000);
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-0 position-relative mt-n8 mx-3 z-index-2">
                    <div class="pt-4 pb-3">
                        <div class="row px-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="text-black text-capitalize mb-0 ms-0">Products List</h5>
                                        <h6 class="text-b text-xs mb-0 opacity-8">Manage your products inventory</h6>
                                    </div>
                                    <div>
                                        <a href="add-product.php" class="btn" 
                                        style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white; box-shadow: 0 3px 6px rgba(255, 99, 71, 0.3);">
                                            <i class="material-symbols-rounded" style="vertical-align: middle;">add</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive px-4">
                        <div class="mx-auto">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Product</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Category</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Price</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Stock</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Featured</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">New Release</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query to get all products with their images and stock information
                                    $products_query = "SELECT p.*, 
                                                      (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
                                                      (SELECT SUM(stock) FROM product_sizes WHERE product_id = p.id) as total_stock
                                                      FROM products p ORDER BY id DESC";
                                    $products_result = mysqli_query($conn, $products_query);

                                    if(mysqli_num_rows($products_result) > 0) {
                                        while($product = mysqli_fetch_assoc($products_result)) {
                                            // Calculate sale price if discount exists
                                            $sale_price = $product['original_price'];
                                            if($product['discount_percentage'] > 0) {
                                                $discount_amount = ($product['original_price'] * $product['discount_percentage']) / 100;
                                                $sale_price = $product['original_price'] - $discount_amount;
                                            }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-lg-flex align-items-center justify-content-start px-2 py-1">
                                                <?php if(!empty($product['primary_image'])): ?>
                                                    <img src="../<?= $product['primary_image'] ?>" class="border-radius-lg d-lg-flex d-none" 
                                                        alt="<?= $product['name'] ?>" style="width: 40px; height: 40px; object-fit: cover; margin-right: 12px;">
                                                <?php else: ?>
                                                    <div class="border-radius-lg bg-gradient-secondary align-items-center justify-content-center" 
                                                        style="width: 40px; height: 40px; margin-right: 12px;">
                                                        <i class="material-symbols-rounded opacity-10 text-white" style="font-size: 18px;">image_not_supported</i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0 text-sm text-uppercase"><?= $product['name'] ?></h6>
                                                    <p class="text-xs text-secondary mb-0">SKU: <?= $product['sku'] ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0"><?= $product['category'] ?></p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if($product['discount_percentage'] > 0): ?>
                                            <p class="text-xs font-weight-bold mb-0">₱<?= number_format($sale_price, 2) ?></p>
                                            <p class="text-xs text-secondary mb-0"><del>₱<?= number_format($product['original_price'], 2) ?></del> (<?= $product['discount_percentage'] ?>% off)</p>
                                            <?php else: ?>
                                            <p class="text-xs font-weight-bold mb-0">₱<?= number_format($product['original_price'], 2) ?></p>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0"><?= $product['total_stock'] ?> units</p>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center py-lg-2 py-4">
                                                <div class="form-check form-switch text-center ps-0">
                                                    <input class="form-check-input mx-auto feature-toggle" type="checkbox" 
                                                        data-product-id="<?= $product['id'] ?>" 
                                                        <?= $product['is_featured'] == 1 ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center py-lg-2 py-4">
                                                <div class="form-check form-switch text-center ps-0">
                                                    <input class="form-check-input mx-auto release-toggle" type="checkbox" 
                                                        data-product-id="<?= $product['id'] ?>" 
                                                        <?= $product['is_new_release'] == 1 ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center pt-lg-2 pt-4">
                                                <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm text-secondary p-1 me-1" title="Edit product">
                                                    <i class="material-symbols-rounded" style="font-size: 20px;">edit</i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="confirmDelete(<?= $product['id'] ?>)" class="btn btn-sm text-danger p-1" title="Delete product">
                                                    <i class="material-symbols-rounded" style="font-size: 20px;">delete</i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-sm mb-0">No products found</p>
                                            <a href="add-product.php" class="btn btn-sm btn-primary mt-3">Add Your First Product</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/products.js"></script>
</body>
</html>