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
                                        <a href="add-product.php" class="btn mb-0" 
                                        style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white; box-shadow: 0 3px 6px rgba(255, 99, 71, 0.3);">
                                            <i class="material-symbols-rounded" style="vertical-align: middle; margin-right: 4px;">add</i>
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
                                            <div class="d-flex align-items-center justify-content-start px-2 py-1">
                                                <?php if(!empty($product['primary_image'])): ?>
                                                    <img src="../<?= $product['primary_image'] ?>" class="border-radius-lg" 
                                                        alt="<?= $product['name'] ?>" style="width: 40px; height: 40px; object-fit: cover; margin-right: 12px;">
                                                <?php else: ?>
                                                    <div class="border-radius-lg bg-gradient-secondary d-flex align-items-center justify-content-center" 
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
                                            <div class="d-flex justify-content-center align-items-center py-2">
                                                <div class="form-check form-switch text-center ps-0">
                                                    <input class="form-check-input mx-auto feature-toggle" type="checkbox" 
                                                        data-product-id="<?= $product['id'] ?>" 
                                                        <?= $product['is_featured'] == 1 ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center py-2">
                                                <div class="form-check form-switch text-center ps-0">
                                                    <input class="form-check-input mx-auto release-toggle" type="checkbox" 
                                                        data-product-id="<?= $product['id'] ?>" 
                                                        <?= $product['is_new_release'] == 1 ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center pt-2">
                                                <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-link text-secondary p-1 me-1" title="Edit product">
                                                    <i class="material-symbols-rounded" style="font-size: 20px;">edit</i>
                                                </a>
                                                <a href="javascript:void(0);" class="view-product btn btn-sm btn-link text-secondary p-1 me-1" title="View product" data-product-id="<?= $product['id'] ?>">
                                                    <i class="material-symbols-rounded" style="font-size: 20px;">visibility</i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="confirmDelete(<?= $product['id'] ?>)" class="btn btn-sm btn-link text-danger p-1" title="Delete product">
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
                                            <a href="add-product.php" class="btn btn-sm bg-gradient-dark mt-3">Add Your First Product</a>
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

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" role="dialog" aria-labelledby="viewProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header" style="background: linear-gradient(195deg, #FF7F50, #FF6347);">
        <h5 class="modal-title text-white text-uppercase" id="productName">Product Details</h5>
        <button type="button" class="btn btn-icon btn-sm ms-auto my-auto" data-bs-dismiss="modal" aria-label="Close">
        <span class="material-symbols-rounded text-white" style="font-size: 25px; line-height: 1; display: block;">close</span>
        </button>
    </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-5">
            <div id="productImageContainer" class="text-center mb-3">
              <img id="productImage" src="" class="img-fluid border-radius-lg shadow" alt="Product Image" style="max-height: 250px;">
            </div>
            <div id="additionalImagesContainer" class="text-center d-flex flex-wrap justify-content-center gap-2">
              <!-- Additional images will be loaded here -->
            </div>
          </div>
          <div class="col-md-7">
            <h5 id="productNameDetail" class="font-weight-bold text-uppercase"></h5>
            <p class="text-sm mb-1">SKU: <span id="productSku"></span></p>
            <p class="text-sm mb-1">Category: <span id="productCategory"></span></p>
            
            <div class="d-flex align-items-center mb-2">
              <div id="priceContainer"></div>
            </div>
            
            <div class="d-flex gap-2 mb-3">
              <div id="featuredBadge"></div>
              <div id="newReleaseBadge"></div>
            </div>

            <h6 class="font-weight-bold mt-3">Description</h6>
            <p id="productDescription" class="text-sm"></p>
            
            <h6 class="font-weight-bold mt-3">Available Sizes & Stock</h6>
            <div id="sizeStockContainer" class="d-flex flex-wrap gap-2">
              <!-- Size and stock info will be loaded here -->
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
        <a id="editProductBtn" href="#" class="btn bg-gradient-coral" style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white; box-shadow: 0 3px 6px rgba(255, 99, 71, 0.3);">Edit Product</a>
      </div>
    </div>
  </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function confirmDelete(productId) {
    if(confirm("Are you sure you want to delete this product? This action cannot be undone.")) {
        window.location.href = "functions/code.php?action=delete_product&id=" + productId;
    }
}

// View product in modal instead of separate page
document.addEventListener('DOMContentLoaded', function() {
    // Get all view buttons
    const viewButtons = document.querySelectorAll('.view-product');
    
    // Add click event listener to each button
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const productId = this.getAttribute('data-product-id');
            viewProduct(productId);
        });
    });

    // Handle Featured toggle
    const featureToggles = document.querySelectorAll('.feature-toggle');
    featureToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const isChecked = this.checked ? 1 : 0;
            updateProductStatus(productId, 'is_featured', isChecked, this);
        });
    });
    
    // Handle New Release toggle
    const releaseToggles = document.querySelectorAll('.release-toggle');
    releaseToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const isChecked = this.checked ? 1 : 0;
            updateProductStatus(productId, 'is_new_release', isChecked, this);
        });
    });
});

function viewProduct(productId) {
    // Show loading indicator
    document.getElementById('viewProductModal').classList.add('loading');
    
    // Fetch product details
    fetch(`functions/get-product-details.php?id=${productId}`)
        .then(response => {
            // Check if response is OK (status in 200-299 range)
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json().catch(err => {
                throw new Error('Invalid JSON response: ' + err.message);
            });
        })
        .then(data => {
            // Check if there's an error in the response
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Populate modal with product details
            document.getElementById('productName').textContent = data.name || 'Unnamed Product';
            document.getElementById('productNameDetail').textContent = data.name || 'Unnamed Product';
            document.getElementById('productSku').textContent = data.sku || 'N/A';
            document.getElementById('productCategory').textContent = data.category || 'Uncategorized';
            document.getElementById('productDescription').textContent = data.description || 'No description available';
            
            // Set product image
            if (data.primary_image) {
                document.getElementById('productImage').src = '../' + data.primary_image;
                document.getElementById('productImage').alt = data.name;
            } else {
                document.getElementById('productImage').src = '../assets/img/no-image.png';
                document.getElementById('productImage').alt = 'No image available';
            }
            
            // Set additional images
            const additionalImagesContainer = document.getElementById('additionalImagesContainer');
            additionalImagesContainer.innerHTML = '';
            if (data.additional_images && data.additional_images.length > 0) {
                data.additional_images.forEach(img => {
                    additionalImagesContainer.innerHTML += `
                        <img src="../${img}" class="img-fluid border-radius-md shadow-sm" 
                             style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                             onclick="document.getElementById('productImage').src='../${img}'">
                    `;
                });
            }
            
            // Set price information with safe number handling
            const priceContainer = document.getElementById('priceContainer');
            const originalPrice = parseFloat(data.original_price) || 0;
            const discountPercentage = parseFloat(data.discount_percentage) || 0;
            
            if (discountPercentage > 0) {
                const salePrice = originalPrice - (originalPrice * discountPercentage / 100);
                priceContainer.innerHTML = `
                    <h6 class="font-weight-bold mb-0 me-2">₱${salePrice.toFixed(2)}</h6>
                    <p class="text-sm text-secondary mb-0"><del>₱${originalPrice.toFixed(2)}</del> (${discountPercentage}% off)</p>
                `;
            } else {
                priceContainer.innerHTML = `<h6 class="font-weight-bold mb-0">₱${originalPrice.toFixed(2)}</h6>`;
            }
            
            // Set featured and new release badges
            document.getElementById('featuredBadge').innerHTML = data.is_featured == 1 ? 
                '<span class="badge bg-gradient-dark">Featured</span>' : 
                '<span class="badge bg-gradient-secondary">Regular</span>';
                
            document.getElementById('newReleaseBadge').innerHTML = data.is_new_release == 1 ? 
                '<span class="badge" style="background: linear-gradient(195deg, #FF7F50, #FF6347)">New Release</span>' : 
                '<span class="badge bg-gradient-secondary">Standard</span>';
            
            // Set sizes and stock
            const sizeStockContainer = document.getElementById('sizeStockContainer');
            sizeStockContainer.innerHTML = '';
            if (data.sizes && Object.keys(data.sizes).length > 0) {
                Object.entries(data.sizes).forEach(([size, stock]) => {
                    const stockQty = parseInt(stock) || 0;
                    // Use dark background with white text for all stock badges
                    sizeStockContainer.innerHTML += `
                        <div class="text-center">
                            <span class="badge text-dark" style="background: white; border: 1px solid rgb(34, 34, 34);">${size}</span>
                            <span class="badge bg-gradient-dark"  style="border: 1px solid rgb(34, 34, 34);">${stockQty}</span>
                        </div>
                    `;
                });
            } else {
                sizeStockContainer.innerHTML = '<p class="text-sm text-danger">No stock information available</p>';
            }
            
            // Set edit button link
            document.getElementById('editProductBtn').href = `edit-product.php?id=${data.id}`;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching product details:', error);
            alert('Failed to load product details: ' + error.message);
        });
}

function updateProductStatus(productId, field, value, toggleElement) {
    // Disable the toggle while updating
    toggleElement.disabled = true;
    
    // Send AJAX request to update the status
    fetch('functions/update-product-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&field=${field}&value=${value}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success indicator - highlight the row briefly
            const row = toggleElement.closest('tr');
            row.classList.add('bg-light');
            setTimeout(() => {
                row.classList.remove('bg-light');
            }, 1000);
        } else {
            // Show error and revert toggle
            alert('Error updating product status: ' + data.message);
            toggleElement.checked = !toggleElement.checked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the product status.');
        toggleElement.checked = !toggleElement.checked;
    })
    .finally(() => {
        // Re-enable the toggle
        toggleElement.disabled = false;
    });
}
</script>
</body>
</html>