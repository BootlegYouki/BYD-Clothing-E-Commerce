<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

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
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="T-Shirt">T-Shirt</option>
                                    <option value="Long Sleeve">Long Sleeve</option>
                                </select>
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
                                <h5>Product Images</h5>
                                <div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2">Primary (Max: 1)</p>
                                            <input type="file" name="primary_image[]" id="primary_image" class="form-control d-none" multiple accept="image/*">
                                            <input type="text" class="form-control" id="primary_image_text" placeholder="No files selected" readonly>
                                            <div class="error-message text-danger"></div>
                                            <button class="btn btn-coral mt-4" type="button" id="primary_image_btn">Choose Image</button>
                                            <div id="primary_image_preview" class="mt-2"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2">Additional Pictures (Max: 3)</p>
                                            <input type="file" name="additional_images[]" id="additional_images" class="form-control d-none" multiple accept="image/*">
                                            <input type="text" class="form-control" id="additional_images_text" placeholder="No files selected" readonly>
                                            <div class="error-message text-danger"></div>
                                            <button class="btn btn-coral mt-4" type="button" id="additional_images_btn">Choose Images</button>
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
                                </div>
                            </div>

                            <div class="col-md-12">
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

<script>
document.getElementById('primary_image_btn').addEventListener('click', function() {
    document.getElementById('primary_image').click();
});

document.getElementById('primary_image').addEventListener('change', function() {
    const files = this.files;
    let previewHtml = '';
    const errorContainer = this.parentNode.querySelector('.error-message');
    errorContainer.textContent = ''; // Clear previous error

    if (files.length > 1) {
        errorContainer.textContent = 'You can only upload one primary image.';
        this.value = ''; // Clear the input
        document.getElementById('primary_image_text').value = 'No files selected';
        document.getElementById('primary_image_preview').innerHTML = '';
        return;
    }
    if (files.length > 0) {
        document.getElementById('primary_image_text').value = files.length + ' files selected';
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                previewHtml += `
                    <div style="display:inline-block; margin-right:5px; text-align: center; position: relative;">
                        <img src="${e.target.result}" style="max-width: 150px; max-height: 150px; border-radius: 0.5rem;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                               style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                               onclick="removePrimaryImage(${i})"
                               title="Remove image">
                            <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                        </button>
                    </div>
                `;
                document.getElementById('primary_image_preview').innerHTML = previewHtml;
            }
            reader.readAsDataURL(file);
        }
    } else {
        document.getElementById('primary_image_text').value = 'No files selected';
        document.getElementById('primary_image_preview').innerHTML = '';
    }
});

function removePrimaryImage(index) {
    const dt = new DataTransfer()
    const input = document.getElementById('primary_image')
    const { files } = input

    for (let i = 0; i < files.length; i++) {
        const file = files[i]
        if (index !== i)
            dt.items.add(file)
    }

    input.files = dt.files
    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
}

document.getElementById('additional_images_btn').addEventListener('click', function() {
    document.getElementById('additional_images').click();
});

document.getElementById('additional_images').addEventListener('change', function() {
    const files = this.files;
    let previewHtml = '';
    const errorContainer = this.parentNode.querySelector('.error-message');
    errorContainer.textContent = ''; // Clear previous error
    if (files.length > 3) {
        errorContainer.textContent = 'You can only upload up to 3 additional images.';
        this.value = ''; // Clear the input
        document.getElementById('additional_images_text').value = 'No files selected';
        document.getElementById('additional_images_preview').innerHTML = '';
        return;
    }
    if (files.length > 0) {
        document.getElementById('additional_images_text').value = files.length + ' files selected';
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) {
                previewHtml += `
                    <div style="display:inline-block; margin-right:5px; text-align: center;">
                        <img src="${e.target.result}" style="max-width: 150px; max-height: 150px;">
                        <br>
                        <button type="button" class="btn btn-danger btn-md remove-image-btn mt-2 py-2 px-5" onclick="removeAdditionalImage(${i})">Remove</button>
                    </div>
                `;
                document.getElementById('additional_images_preview').innerHTML = previewHtml;
            }
            reader.readAsDataURL(file);
        }
    } else {
        document.getElementById('additional_images_text').value = 'No files selected';
        document.getElementById('additional_images_preview').innerHTML = '';
    }
});

function removeAdditionalImage(index) {
    const dt = new DataTransfer()
    const input = document.getElementById('additional_images')
    const { files } = input

    for (let i = 0; i < files.length; i++) {
        const file = files[i]
        if (index !== i)
            dt.items.add(file)
    }

    input.files = dt.files
    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
}

// JavaScript function to generate SKU
function generateSKU_JS(productName, category) {
    const prefix = category.substring(0, 3).toUpperCase();
    const shortName = productName.replace(/[^a-zA-Z0-9]/g, '').substring(0, 4).toUpperCase();
    const randomNumber = Math.floor(Math.random() * (999 - 100 + 1)) + 100;
    return prefix + "-" + shortName + "-" + randomNumber;
}

document.getElementById('name').addEventListener('input', updateSKU);
document.getElementById('category').addEventListener('change', updateSKU);

function updateSKU() {
    const productName = document.getElementById('name').value;
    const category = document.getElementById('category').value;
    const sku = generateSKU_JS(productName, category);
    document.getElementById('sku').value = sku;
}

// Set page title based on current page
document.addEventListener('DOMContentLoaded', function() {
  const currentPage = '<?php echo basename($_SERVER["PHP_SELF"], ".php"); ?>';
  const titleMap = {
    'index': 'Dashboard',
    'products': 'Products Management',
    'add-product': 'Add New Product',
    'homepage-customize': 'Homepage Customization',
    'categories': 'Categories',
    'orders': 'Orders Management',
    'customers': 'Customer Management'
  };
  
  // Update navbar title
  const navbarTitle = document.querySelector('.top-navbar h4');
  if (navbarTitle && titleMap[currentPage]) {
    navbarTitle.textContent = titleMap[currentPage];
  }
});
</script>
</body>
</html>