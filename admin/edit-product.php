<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}
include 'config/dbcon.php';

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Product ID is required";
    header('Location: products.php');
    exit;
}

$product_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get product details
$product_query = "SELECT * FROM products WHERE id = '$product_id'";
$product_result = mysqli_query($conn, $product_query);

if(mysqli_num_rows($product_result) == 0) {
    $_SESSION['message'] = "Product not found";
    header('Location: products.php');
    exit;
}

$product = mysqli_fetch_assoc($product_result);

// Get primary image
$primary_image_query = "SELECT image_url FROM product_images WHERE product_id = '$product_id' AND is_primary = 1 LIMIT 1";
$primary_image_result = mysqli_query($conn, $primary_image_query);
$primary_image = mysqli_num_rows($primary_image_result) > 0 ? mysqli_fetch_assoc($primary_image_result)['image_url'] : null;

// Get additional images
$additional_images_query = "SELECT id, image_url FROM product_images WHERE product_id = '$product_id' AND is_primary = 0";
$additional_images_result = mysqli_query($conn, $additional_images_query);
$additional_images = [];
while($image = mysqli_fetch_assoc($additional_images_result)) {
    $additional_images[] = $image;
}

// Get product sizes and stock
$sizes_query = "SELECT size, stock FROM product_sizes WHERE product_id = '$product_id'";
$sizes_result = mysqli_query($conn, $sizes_query);
$sizes = [];
while($size = mysqli_fetch_assoc($sizes_result)) {
    $sizes[$size['size']] = $size['stock'];
}
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

<div class="container-fluid py-4">
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
            <div class="card my-4">
                <div class="card-header p-0 position-relative mx-3 z-index-2">
                    <div class="pt-4 pb-3">
                        <div class="row px-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="text-black text-capitalize mb-0 ms-0">Edit Product</h5>
                                        <h6 class="text-b text-xs mb-0 opacity-8">Update product information</h6>
                                    </div>
                                    <div>
                                        <a href="products.php" class="btn mb-0" 
                                        style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white; box-shadow: 0 3px 6px rgba(255, 99, 71, 0.3);">
                                            <i class="material-symbols-rounded" style="vertical-align: middle; margin-right: 4px;">arrow_back</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="functions/code.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="deleted_images" id="deleted_images" value="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control" required value="<?= $product['sku'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" required value="<?= $product['name'] ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" rows="4" class="form-control"><?= $product['description'] ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="original_price" class="form-label">Original Price</label>
                                <input type="number" name="original_price" class="form-control" step="0.01" required value="<?= $product['original_price'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discount_percentage" class="form-label">Discount Percentage</label>
                                <input type="number" name="discount_percentage" class="form-control" value="<?= $product['discount_percentage'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="T-Shirt" <?= $product['category'] == 'T-Shirt' ? 'selected' : '' ?>>T-Shirt</option>
                                    <option value="Long Sleeve" <?= $product['category'] == 'Long Sleeve' ? 'selected' : '' ?>>Long Sleeve</option>
                                </select>
                            </div>
                            <div class="col-md-6"></div>
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input mt-2" type="checkbox" id="is_featured" name="is_featured" value="1" <?= $product['is_featured'] == 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label mt-1" for="is_featured">Featured Product</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input mt-2" type="checkbox" id="is_new_release" name="is_new_release" value="1" <?= $product['is_new_release'] == 1 ? 'checked' : '' ?>>
                                                <label class="form-check-label mt-1" for="is_new_release">New Release</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-md-12">
                                <hr>
                                <h5>Product Images</h5>
                                <div class="row mb-4">
                                        <div class="col-md-6">
                                        <p class="mb-2">Current Primary Image</p>
                                        <?php if($primary_image): ?>
                                            <?php
                                            // Get the primary image ID
                                            $primary_image_id_query = "SELECT id FROM product_images WHERE product_id = '$product_id' AND is_primary = 1 LIMIT 1";
                                            $primary_image_id_result = mysqli_query($conn, $primary_image_id_query);
                                            $primary_image_id = mysqli_num_rows($primary_image_id_result) > 0 ? mysqli_fetch_assoc($primary_image_id_result)['id'] : '';
                                            ?>
                                            <div class="mb-3 position-relative" id="primary_image_container" data-image-id="<?= $primary_image_id ?>">
                                                <div class="image-preview-container">
                                                    <img src="../<?= $primary_image ?>" alt="Primary Image" class="img-fluid border-radius-md" style="width: 106px; height: 106px; object-fit: cover;">
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-danger">No primary image set</p>
                                            <div id="primary_image_container" data-image-id=""></div>
                                        <?php endif; ?>
                                        <p class="mb-2">Change Primary Image (Optional)</p>
                                        <input type="file" name="primary_image[]" id="primary_image" class="form-control d-none" accept="image/*">
                                        <input type="text" class="form-control" id="primary_image_text" placeholder="No files selected" readonly>
                                        <div class="error-message text-danger"></div>
                                        <button class="btn btn-primary mt-4" type="button" id="primary_image_btn">Choose Image</button>
                                        <div id="primary_image_preview" class="mt-2"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">Current Additional Images</p>
                                        <div class="d-flex flex-wrap gap-2 <?= count($additional_images) > 0 ? 'mb-3' : 'mb-5' ?>" id="additional-images-container">
                                            <?php if(count($additional_images) > 0): ?>
                                                <?php foreach($additional_images as $image): ?>
                                                    <div class="position-relative" id="image_container_<?= $image['id'] ?>">
                                                        <img src="../<?= $image['image_url'] ?>" alt="Additional Image" class="img-fluid border-radius-md" style="width: 106px; height: 106px; object-fit: cover;">
                                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                                                                style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                                                                onclick="removeExistingImage(<?= $image['id'] ?>)"
                                                                title="Remove image">
                                                            <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-danger mb-4" id="no-images-msg">No additional images</p>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-2">Add More Additional Images (Optional - Max Total: 3)</p>
                                        <p class="text-muted small mb-2">
                                            <?php
                                            $existing_count = count($additional_images);
                                            $remaining = 3 - $existing_count;
                                            if ($remaining > 0) {
                                                echo "You can add $remaining more image" . ($remaining !== 1 ? "s" : "");
                                            } else {
                                                echo "You've reached the maximum number of additional images";
                                            }
                                            ?>
                                        </p>
                                        <input type="file" name="additional_images[]" id="additional_images" class="form-control d-none" multiple accept="image/*">
                                        <input type="text" class="form-control" id="additional_images_text" placeholder="No files selected" readonly>
                                        <div class="error-message text-danger"></div>
                                        <button class="btn btn-primary mt-4" type="button" id="additional_images_btn">Choose Images</button>
                                        <div id="additional_images_preview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                                <h5>Stock Information</h5>
                                <div class="row">
                                    <?php 
                                    $all_sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                    foreach($all_sizes as $size): 
                                        $stock_value = isset($sizes[$size]) ? $sizes[$size] : 0;
                                    ?>
                                    <div class="col-md-2 mb-3">
                                        <label for="stock_<?= $size ?>" class="form-label"><?= $size ?></label>
                                        <input type="number" name="stock[<?= $size ?>]" id="stock_<?= $size ?>" class="form-control" value="<?= $stock_value ?>" min="0">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-12 mt-4 text-end">
                                <a href="products.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/edit-product.js"></script>
</body>
</html>