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
    $_SESSION['message_type'] = "error";
    $_SESSION['message'] = "Product ID is required";
    header('Location: products.php');
    exit;
}

$product_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get product details
$product_query = "SELECT * FROM products WHERE id = '$product_id'";
$product_result = mysqli_query($conn, $product_query);

if(mysqli_num_rows($product_result) == 0) {
    $_SESSION['message_type'] = "error";
    $_SESSION['message'] = "Product not found";
    header('Location: products.php');
    exit;
}

$product = mysqli_fetch_assoc($product_result);

// Get primary image
$primary_image_query = "SELECT id, image_url FROM product_images WHERE product_id = '$product_id' AND is_primary = 1 LIMIT 1";
$primary_image_result = mysqli_query($conn, $primary_image_query);
$primary_image = mysqli_num_rows($primary_image_result) > 0 ? mysqli_fetch_assoc($primary_image_result) : null;

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

// Add query to fetch categories
$category_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category";
$category_result = mysqli_query($conn, $category_query);

// Keep this query even if removing the fabric dropdown from UI
$fabric_query = "SELECT DISTINCT fabric FROM products WHERE fabric IS NOT NULL AND fabric != '' ORDER BY fabric";
$fabric_result = mysqli_query($conn, $fabric_query);

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
  <link rel="stylesheet" href="assets/css/product.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="container-fluid py-3">
    <?php if(isset($_SESSION['message'])) : ?>
        <?php 
        // Default to error if message_type isn't set
        $messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : "error";
        $alertClass = ($messageType == "success") ? "alert-success" : "alert-danger";
        $alertTitle = ($messageType == "success") ? "Success!" : "Error!";
        ?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert" id="alert-message">
            <strong><?= $alertTitle ?></strong> <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('alert-message').classList.remove('show');
                setTimeout(function() {
                    document.getElementById('alert-message')?.remove();
                }, 150);
            }, 3000);
        </script>
        <?php 
        unset($_SESSION['message']); 
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Edit Product</h4>
                            <p class="text-muted mb-0 small">Update product information</p>
                        </div>
                        <a href="products.php" class="btn btn-primary">
                            <i class="material-symbols-rounded me-1">arrow_back</i>
                            Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form action="functions/products/code.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= $product_id ?>">
        <input type="hidden" name="deleted_images" id="deleted_images" value="">
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="material-symbols-rounded align-middle me-2">info</i>
                        Basic Information
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control" value="<?= htmlspecialchars($product['sku']) ?>" readonly>
                            <small class="text-muted">Auto-generated from product name and category</small>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter a detailed product description"><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Pricing Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="material-symbols-rounded align-middle me-2">payments</i>
                        Pricing
                    </h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="original_price" class="form-label">Original Price (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" name="original_price" id="original_price" class="form-control" min="0" step="0.01" value="<?= $product['original_price'] ?>" required>
                            </div>
                            <small class="text-muted mt-1 d-block">Enter the regular price before any discounts</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="discount_price" class="form-label">Discount Price (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <?php
                                    // Use discount_price if available, otherwise calculate from percentage
                                    $discountPrice = $product['original_price'];
                                    if(isset($product['discount_price']) && $product['discount_price'] > 0) {
                                        $discountPrice = $product['discount_price'];
                                    } else if($product['discount_percentage'] > 0) {
                                        $discountPrice = $product['original_price'] - ($product['original_price'] * $product['discount_percentage'] / 100);
                                    }
                                ?>
                                <input type="number" name="discount_price" id="discount_price" class="form-control" min="0" step="0.01" value="<?= $discountPrice ?>">
                            </div>
                            <small class="text-muted mt-1 d-block">Enter the final price after discount</small>
                        </div>
                        
                        <div class="col-12">
                            <div class="price-preview">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex flex-column">
                                            <div class="mb-1">
                                                <span class="original-price" id="original_price_display">₱<?= number_format($product['original_price'], 2) ?></span>
                                                <span class="text-muted ms-2" id="discount_text"><?= $product['discount_percentage'] > 0 ? "({$product['discount_percentage']}% off)" : '' ?></span>
                                            </div>
                                            <div class="discounted-price" id="final_price_display">₱<?= number_format($discountPrice, 2) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
                                            <div class="price-savings <?= $product['discount_percentage'] > 0 ? '' : 'd-none' ?>" id="savings_container">
                                                <i class="material-symbols-rounded align-middle me-1">savings</i>
                                                Customer saves: <strong id="savings_amount">₱<?= number_format($product['original_price'] - $discountPrice, 2) ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Categories Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="material-symbols-rounded align-middle me-2">category</i>
                        Categories & Attributes
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category_display" class="form-label">Category</label>
                            <input type="hidden" name="category" id="category" value="<?= htmlspecialchars($product['category']) ?>">
                            
                            <div class="custom-dropdown">
                                <div class="form-control d-flex justify-content-between align-items-center" id="category_display" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                                    <span id="selected_category"><?= $product['category'] ? htmlspecialchars($product['category']) : 'Select Category' ?></span>
                                    <i class="material-symbols-rounded">expand_more</i>
                                </div>
                                
                                <ul class="dropdown-menu w-100 p-0" id="category_dropdown">
                                    <li><button type="button" class="dropdown-item py-4" data-value="">Select Category</button></li>
                                    <hr>
                                    <li class="category-items-container">
                                        <!-- Categories will be populated here by JavaScript -->
                                    </li>
                                    
                                    <li><hr class="dropdown-divider m-0"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-coral" id="add_new_category">
                                            <i class="material-symbols-rounded me-2 my-3 align-middle">add_circle</i> Add New Category
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="fabric_display" class="form-label">Fabric</label>
                            <input type="hidden" name="fabric" id="fabric" value="<?= htmlspecialchars($product['fabric']) ?>">
                            
                            <div class="custom-dropdown">
                                <div class="form-control d-flex justify-content-between align-items-center" id="fabric_display" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                                    <span id="selected_fabric"><?= $product['fabric'] ? htmlspecialchars($product['fabric']) : 'Select Fabric' ?></span>
                                    <i class="material-symbols-rounded">expand_more</i>
                                </div>
                                
                                <ul class="dropdown-menu w-100 p-0" id="fabric_dropdown">
                                    <li><button type="button" class="dropdown-item py-4" data-value="">Select Fabric</button></li>
                                    <hr>
                                    <li class="fabric-items-container">
                                        <!-- Fabrics will be populated here by JavaScript -->
                                    </li>
                                    
                                    <li><hr class="dropdown-divider m-0"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-coral" id="add_new_fabric">
                                            <i class="material-symbols-rounded me-2 my-3 align-middle">add_circle</i> Add New Fabric
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_new_release" name="is_new_release" value="1" <?= $product['is_new_release'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_new_release">
                                    <span class="badge bg-success me-1">New</span> Mark as New Release
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_featured">
                                    <span class="badge bg-warning me-1">Featured</span> Mark as Featured Product
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Inventory Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="material-symbols-rounded align-middle me-2">inventory_2</i>
                        Sizes & Inventory
                    </h5>
                    
                    <p class="text-muted mb-3">Enter the available stock quantity for each size</p>
                    
                    <div class="size-stock-grid">
                        <div class="size-input-container">
                            <span class="size-label">XS</span>
                            <input type="number" name="stock[XS]" class="form-control text-center" min="0" value="<?= isset($sizes['XS']) ? $sizes['XS'] : 0 ?>">
                        </div>
                        <div class="size-input-container">
                            <span class="size-label">S</span>
                            <input type="number" name="stock[S]" class="form-control text-center" min="0" value="<?= isset($sizes['S']) ? $sizes['S'] : 0 ?>">
                        </div>
                        <div class="size-input-container">
                            <span class="size-label">M</span>
                            <input type="number" name="stock[M]" class="form-control text-center" min="0" value="<?= isset($sizes['M']) ? $sizes['M'] : 0 ?>">
                        </div>
                        <div class="size-input-container">
                            <span class="size-label">L</span>
                            <input type="number" name="stock[L]" class="form-control text-center" min="0" value="<?= isset($sizes['L']) ? $sizes['L'] : 0 ?>">
                        </div>
                        <div class="size-input-container">
                            <span class="size-label">XL</span>
                            <input type="number" name="stock[XL]" class="form-control text-center" min="0" value="<?= isset($sizes['XL']) ? $sizes['XL'] : 0 ?>">
                        </div>
                        <div class="size-input-container">
                            <span class="size-label">XXL</span>
                            <input type="number" name="stock[XXL]" class="form-control text-center" min="0" value="<?= isset($sizes['XXL']) ? $sizes['XXL'] : 0 ?>">
                        </div>
                        <div class="size-input-container">
                            <span class="size-label">XXXL</span>
                            <input type="number" name="stock[XXXL]" class="form-control text-center" min="0" value="<?= isset($sizes['XXXL']) ? $sizes['XXXL'] : 0 ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Primary Image Upload -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="material-symbols-rounded align-middle me-2">image</i>
                        Primary Image
                    </h5>
                    
                    <div class="image-upload-container mb-3" id="primary_image_container_clickable">
                        <div class="upload-icon">
                            <i class="material-symbols-rounded">file_upload</i>
                        </div>
                        <p class="mb-2">Primary Product Image</p>
                        <small class="text-muted d-block mb-3">Click to select or drag & drop<br>(Max: 1)</small>
                        
                        <input type="file" name="primary_image[]" id="primary_image" class="form-control d-none" accept="image/*">
                        <input type="text" class="form-control file-name-display" id="primary_image_text" placeholder="No files selected" readonly>
                        <div class="error-message text-danger mt-2"></div>
                        
                        <!-- Text-based current image indicator -->
                        <div id="primary_image_preview" class="mt-3">
                            <?php if($primary_image): ?>
                                <div id="primary_image_container" data-image-id="<?= $primary_image['id'] ?>" class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="material-symbols-rounded me-2">photo</i>
                                        <div class="flex-grow-1">
                                            <strong>Current Primary Image</strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Images Upload -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="material-symbols-rounded align-middle me-2">collections</i>
                        Additional Images
                    </h5>
                    
                    <div class="image-upload-container mb-3" id="additional_images_container_clickable">
                        <div class="upload-icon">
                            <i class="material-symbols-rounded">photo_library</i>
                        </div>
                        <p class="mb-2">Additional Product Images</p>
                        <small class="text-muted d-block mb-3">Click to select or drag & drop<br>(Max: 4)</small>
                        
                        <input type="file" name="additional_images[]" id="additional_images" class="form-control d-none" multiple accept="image/*">
                        <input type="text" class="form-control file-name-display" id="additional_images_text" placeholder="No files selected" readonly>
                        <div class="error-message text-danger mt-2"></div>
                        
                        <!-- Text-based additional images display -->
                        <div id="additional_images_preview" class="mt-3">
                            <div id="additional-images-container">
                                <?php if(count($additional_images) > 0): ?>
                                    <?php foreach($additional_images as $index => $image): ?>
                                        <div id="image_container_<?= $image['id'] ?>" class="alert alert-info mb-2 removable-image"
                                             onclick="event.stopPropagation(); removeExistingImage('<?= $image['id'] ?>');"
                                             title="Click to remove image">
                                            <div class="d-flex align-items-center">
                                                <i class="material-symbols-rounded me-2">photo</i>
                                                <div class="flex-grow-1 min-width-0">
                                                    <strong>Additional Image <?= $index + 1 ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p id="no-images-msg" class="alert alert-warning">No additional images</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" name="update_product" class="btn btn-primary submit-btn d-flex align-items-center justify-content-center">
                        <i class="material-symbols-rounded me-2">save</i>
                        Update Product
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

</main>

<!-- Category Modals -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_category_modal" class="form-label">New Category Name</label>
                    <input type="text" id="new_category_modal" class="form-control" placeholder="Enter new category name">
                </div>
                <div id="add_category_msg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="add_category_btn" class="btn btn-success">Add Category</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="removeCategoryModal" tabindex="-1" aria-labelledby="removeCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeCategoryModalLabel">Remove Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="remove_category_modal" class="form-label">Select Category to Remove</label>
                    <select id="remove_category_modal" class="form-control">
                        <option value="">Select Category to Remove</option>
                        <?php
                        mysqli_data_seek($category_result, 0);
                        if(mysqli_num_rows($category_result) > 0) {
                            while($category = mysqli_fetch_assoc($category_result)) {
                                echo '<option value="'.$category['category'].'">'.$category['category'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div id="remove_category_msg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="remove_category_btn" class="btn btn-danger">Remove Category</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="renameCategoryModal" tabindex="-1" aria-labelledby="renameCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameCategoryModalLabel">Rename Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="old_category_modal" class="form-label">Select Category to Rename</label>
                    <select id="old_category_modal" class="form-select">
                        <option value="">Select Category</option>
                        <?php
                        mysqli_data_seek($category_result, 0);
                        if(mysqli_num_rows($category_result) > 0) {
                            while($category = mysqli_fetch_assoc($category_result)) {
                                echo '<option value="'.$category['category'].'">'.$category['category'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="new_category_name_modal" class="form-label">New Category Name</label>
                    <input type="text" id="new_category_name_modal" class="form-control" placeholder="Enter new category name">
                </div>
                <div id="rename_category_msg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="rename_category_btn" class="btn btn-warning">Rename Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Fabric Modals -->
<div class="modal fade" id="addFabricModal" tabindex="-1" aria-labelledby="addFabricModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFabricModalLabel">Add New Fabric</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new_fabric_modal" class="form-label">New Fabric Type</label>
                    <input type="text" id="new_fabric_modal" class="form-control" placeholder="Enter new fabric type">
                </div>
                <div id="add_fabric_msg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="add_fabric_btn" class="btn btn-success">Add Fabric</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="removeFabricModal" tabindex="-1" aria-labelledby="removeFabricModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeFabricModalLabel">Remove Fabric</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="remove_fabric_modal" class="form-label">Select Fabric to Remove</label>
                    <select id="remove_fabric_modal" class="form-control">
                        <option value="">Select Fabric to Remove</option>
                        <?php
                        mysqli_data_seek($fabric_result, 0);
                        if(mysqli_num_rows($fabric_result) > 0) {
                            while($fabric = mysqli_fetch_assoc($fabric_result)) {
                                echo '<option value="'.$fabric['fabric'].'">'.$fabric['fabric'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div id="remove_fabric_msg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="remove_fabric_btn" class="btn btn-danger">Remove Fabric</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="renameFabricModal" tabindex="-1" aria-labelledby="renameFabricModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renameFabricModalLabel">Rename Fabric</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="old_fabric_modal" class="form-label">Select Fabric to Rename</label>
                    <select id="old_fabric_modal" class="form-select">
                        <option value="">Select Fabric</option>
                        <?php
                        mysqli_data_seek($fabric_result, 0);
                        if(mysqli_num_rows($fabric_result) > 0) {
                            while($fabric = mysqli_fetch_assoc($fabric_result)) {
                                echo '<option value="'.$fabric['fabric'].'">'.$fabric['fabric'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="new_fabric_name_modal" class="form-label">New Fabric Name</label>
                    <input type="text" id="new_fabric_name_modal" class="form-control" placeholder="Enter new fabric name">
                </div>
                <div id="rename_fabric_msg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="rename_fabric_btn" class="btn btn-warning">Rename Fabric</button>
            </div>
        </div>
    </div>
</div>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/edit-product.js"></script>

</body>
</html>