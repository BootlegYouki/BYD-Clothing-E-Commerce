<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

// Add this line to include database connection
include 'config/dbcon.php';

function generateSKU($productName, $category) {
    $prefix = strtoupper(substr($category, 0, 3));
    $shortName = strtoupper(substr(preg_replace("/[^a-zA-Z0-9]/", '', $productName), 0, 4));
    $randomNumber = rand(100, 999);
    return $prefix . "-" . $shortName . "-" . $randomNumber;
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
                <div class="card-header p-0 position-relative mx-3 z-index-2">
                    <div class="pt-4 pb-3">
                        <div class="row px-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="text-black text-capitalize mb-0 ms-0">Add New Product</h5>
                                        <h6 class="text-b text-xs mb-0 opacity-8">Create a new product for your inventory</h6>
                                    </div>
                                    <div>
                                        <a href="products.php" class="btn mb-0" 
                                        style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white; box-shadow: 0 3px 6px rgba(255, 99, 71, 0.3);">
                                            <i class="material-symbols-rounded" style="vertical-align: middle;">arrow_back</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="card-body">
                    <form action="functions/code.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" name="sku" id="sku" class="form-control" readonly>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="original_price" class="form-label">Original Price</label>
                                <input type="number" name="original_price" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discount_percentage" class="form-label">Discount Percentage</label>
                                <input type="number" name="discount_percentage" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    // Fetch existing categories from database
                                    $category_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category";
                                    $category_result = mysqli_query($conn, $category_query);
                                    if(mysqli_num_rows($category_result) > 0) {
                                        while($category = mysqli_fetch_assoc($category_result)) {
                                            echo '<option value="'.$category['category'].'">'.$category['category'].'</option>';
                                        }
                                    }
                                    ?>
                                    <option value="new">+ Add New Category</option>
                                    <option value="remove" class="text-danger">- Remove Category</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="new_category_container" style="display: none;">
                                <label for="new_category" class="form-label">New Category Name</label>
                                <input type="text" name="new_category" id="new_category" class="form-control" placeholder="Enter new category name">
                                <button type="button" id="add_category_btn" class="btn btn-success mt-2">Add Category</button>
                                <div id="add_category_msg" class="mt-2"></div>
                            </div>
                            <div class="col-md-6 mb-3" id="remove_category_container" style="display: none;">
                                <label for="remove_category" class="form-label">Select Category to Remove</label>
                                <select name="remove_category" id="remove_category" class="form-control">
                                    <option value="">Select Category to Remove</option>
                                    <?php
                                    // Reset the result pointer to beginning
                                    mysqli_data_seek($category_result, 0);
                                    if(mysqli_num_rows($category_result) > 0) {
                                        while($category = mysqli_fetch_assoc($category_result)) {
                                            echo '<option value="'.$category['category'].'">'.$category['category'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <button type="button" id="remove_category_btn" class="btn btn-danger mt-2">Remove Selected Category</button>
                                <div id="remove_category_msg" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fabric" class="form-label">Fabric</label>
                                <select name="fabric" id="fabric" class="form-control">
                                    <option value="">Select Fabric</option>
                                    <?php
                                    // Fetch existing fabric categories from database
                                    $fabric_query = "SELECT DISTINCT fabric FROM products WHERE fabric IS NOT NULL AND fabric != '' ORDER BY fabric";
                                    $fabric_result = mysqli_query($conn, $fabric_query);
                                    if(mysqli_num_rows($fabric_result) > 0) {
                                        while($fabric = mysqli_fetch_assoc($fabric_result)) {
                                            echo '<option value="'.$fabric['fabric'].'">'.$fabric['fabric'].'</option>';
                                        }
                                    }
                                    ?>
                                    <option value="new">+ Add New Fabric</option>
                                    <option value="remove" class="text-danger">- Remove Fabric</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="new_fabric_container" style="display: none;">
                                <label for="new_fabric" class="form-label">New Fabric Type</label>
                                <input type="text" name="new_fabric" id="new_fabric" class="form-control" placeholder="Enter new fabric type">
                                <button type="button" id="add_fabric_btn" class="btn btn-success mt-2">Add Fabric</button>
                                <div id="add_fabric_msg" class="mt-2"></div>
                            </div>
                            <div class="col-md-6 mb-3" id="remove_fabric_container" style="display: none;">
                                <label for="remove_fabric" class="form-label">Select Fabric to Remove</label>
                                <select name="remove_fabric" id="remove_fabric" class="form-control">
                                    <option value="">Select Fabric to Remove</option>
                                    <?php
                                    // Reset the result pointer to beginning
                                    mysqli_data_seek($fabric_result, 0);
                                    if(mysqli_num_rows($fabric_result) > 0) {
                                        while($fabric = mysqli_fetch_assoc($fabric_result)) {
                                            echo '<option value="'.$fabric['fabric'].'">'.$fabric['fabric'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <button type="button" id="remove_fabric_btn" class="btn btn-danger mt-2">Remove Selected Fabric</button>
                                <div id="remove_fabric_msg" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input mt-2" type="checkbox" id="is_new_release" name="is_new_release" value="1">
                                    <label class="form-check-label mt-1" for="is_new_release">New Release</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input mt-2" type="checkbox" id="is_featured" name="is_featured" value="1">
                                    <label class="form-check-label mt-1" for="is_featured">Featured Product</label>
                                </div>
                            </div>
                        </div>
                            <div class="col-md-12">
                                <hr>
                                <h5 class="mt-4">Product Images</h5>
                                <div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">Primary (Max: 1)</p>
                                            <input type="file" name="primary_image[]" id="primary_image" class="form-control d-none" multiple accept="image/*">
                                            <input type="text" class="form-control" id="primary_image_text" placeholder="No files selected" readonly>
                                            <div class="error-message text-danger"></div>
                                            <button class="btn btn-primary mt-4" type="button" id="primary_image_btn">Choose Image</button>
                                            <div id="primary_image_preview" class="mt-2"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">Additional Pictures (Max: 3)</p>
                                            <input type="file" name="additional_images[]" id="additional_images" class="form-control d-none" multiple accept="image/*">
                                            <input type="text" class="form-control" id="additional_images_text" placeholder="No files selected" readonly>
                                            <div class="error-message text-danger"></div>
                                            <button class="btn btn-primary mt-4" type="button" id="additional_images_btn">Choose Images</button>
                                            <div id="additional_images_preview" class="mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 mt-4">
                                <hr>
                                <h5 class="mt-4">Product Sizes & Stock</h5>
                                <div class="row">
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">XS</label>
                                        <input type="number" name="stock[XS]" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">S</label>
                                        <input type="number" name="stock[S]" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">M</label>
                                        <input type="number" name="stock[M]" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">L</label>
                                        <input type="number" name="stock[L]" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">XL</label>
                                        <input type="number" name="stock[XL]" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">XXL</label>
                                        <input type="number" name="stock[XXL]" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">XXXL</label>
                                        <input type="number" name="stock[XXXL]" class="form-control" min="0" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" name="add_product" class="btn btn-primary" style="background: linear-gradient(195deg, #FF7F50, #FF6347);">Add Product</button>
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
<script src="assets/js/add-product.js"></script>
</body>
</html>