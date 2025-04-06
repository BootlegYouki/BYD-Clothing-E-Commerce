<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}
include 'config/dbcon.php';

// Get current settings from database
$settings = [];
$query = "SELECT * FROM homepage_settings";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Helper function to get setting with fallback
function get_setting($key, $default = '') {
  global $settings;
  $value = isset($settings[$key]) ? $settings[$key] : $default;
  
  // Convert <br> back to newlines for textarea fields
  $title_fields = ['hero_heading', 'banner_title', 'new_release_title', 'tshirt_title', 'longsleeve_title'];
  if (in_array($key, $title_fields)) {
      $value = str_replace("<br>", "\n", $value);
  }
  
  // Convert <span> tags to asterisks for display in form
  $value = preg_replace('/<span>(.*?)<\/span>/s', '*$1*', $value);
  
  return $value;
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
  <div class="row">
    <div class="col-lg-6 col-md-12 col-12">
      <div class="card">
      <div class="card-header p-0 position-relative mt-n8 mx-3 z-index-2">
          <div class="pt-4 pb-3">
            <div class="row px-2 align-items-center">
              <div class="col-md-6">
                <div class="d-flex align-items-center">
                  <div>
                    <h5 class="text-black text-capitalize mb-0 ms-0">Homepage content customization</h6>
                    <h6 class="text-b text-xs mb-0 opacity-8">Manage homepage content</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
  <div class="px-4 pt-3">
    <?php if(isset($_SESSION['content_message'])) : ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert" id="content-success-alert">
        <strong>Success!</strong> <?= $_SESSION['content_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <script>
        setTimeout(function() {
          document.getElementById('content-success-alert').classList.remove('show');
          setTimeout(function() {
            document.getElementById('content-success-alert')?.remove();
          }, 150);
        }, 3000);
      </script>
      <?php unset($_SESSION['content_message']); ?>
    <?php endif; ?>
  </div>

  <!-- Homepage Content Customization -->
  <div class="mx-4">
    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <button class="nav-link active" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero-content" type="button" role="tab" aria-controls="hero-content" aria-selected="true">Hero Section</button>
        <button class="nav-link" id="banner-tab" data-bs-toggle="tab" data-bs-target="#banner-content" type="button" role="tab" aria-controls="banner-content" aria-selected="false">Banner</button>
        <button class="nav-link" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections-content" type="button" role="tab" aria-controls="sections-content" aria-selected="false">Section Titles</button>
      </div>
    </nav>
    
    <form action="functions/update-homepage.php" method="POST">
      <div class="tab-content pt-3" id="nav-tabContent">
        <!-- Hero Section Content -->
        <div class="tab-pane fade show active" id="hero-content" role="tabpanel" aria-labelledby="hero-tab">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Hero Tagline</label>
              <input type="text" class="form-control" name="hero_tagline" 
                    value="<?= get_setting('hero_tagline', 'New Arrival') ?>" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Hero Heading</label>
              <textarea class="form-control" name="hero_heading" rows="3" required><?= get_setting('hero_heading', 'From casual hangouts to High-energy moments. Versatility at its best.') ?></textarea>
              <small class="text-muted">Surround text with asterisks (*) to highlight it. Example: *highlighted text*</small>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Hero Description</label>
              <textarea class="form-control" name="hero_description" required><?= get_setting('hero_description', 'Our Air-Cool Fabric T-shirt adapts to every occasion and keeps you cool.') ?></textarea>
            </div>
          </div>
        </div>
        
        <!-- Banner Content -->
        <div class="tab-pane fade" id="banner-content" role="tabpanel" aria-labelledby="banner-tab">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Banner Title</label>
              <textarea class="form-control" name="banner_title" rows="2" required><?= get_setting('banner_title', '<span>CUSTOM</span> SUBLIMATION SERVICE') ?></textarea>
              <small class="text-muted">Surround text with asterisks (*) to highlight it. Example: *highlighted text*</small>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Banner Description</label>
              <input type="text" class="form-control" name="banner_description" 
                value="<?= get_setting('banner_description', 'We offer fully customized sublimation services:') ?>" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Banner List Items</label>
              <textarea class="form-control" name="banner_list" rows="5" required><?= get_setting('banner_list', "T-shirt\nPolo Shirt\nBasketball\nJersey\nLong Sleeves") ?></textarea>
              <small class="text-muted">Enter each item on a new line</small>
            </div>
          </div>
        </div>
        
        <!-- Section Titles and Descriptions -->
        <div class="tab-pane fade" id="sections-content" role="tabpanel" aria-labelledby="sections-tab">
        <div class="sections-scrollable" style="max-height: 510px; overflow-y: auto; padding-right: 5px;">
          <div class="card mb-3">
            <div class="card-header py-2 px-3 bg-light">
              <h6 class="mb-0">New Release Section</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Section Title</label>
                  <input type="text" class="form-control" name="new_release_title" value="<?= get_setting('new_release_title', 'New Release') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Show Section</label>
                  <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" name="show_new_release" value="1" 
                    <?= get_setting('show_new_release', '1') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Visible on homepage</label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label">Section Description</label>
                  <textarea class="form-control" name="new_release_description" required><?= get_setting('new_release_description', 'Unleash the power of style with our Mecha Collection Moto Jerseys.') ?></textarea>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card mb-3">
            <div class="card-header py-2 px-3 bg-light">
              <h6 class="mb-0">T-Shirt Collection Section</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Section Title</label>
                  <input type="text" class="form-control" name="tshirt_title" 
                        value="<?= get_setting('tshirt_title', 'T-Shirt Collection') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Show Section</label>
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="show_tshirt" value="1" 
                          <?= get_setting('show_tshirt', '1') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Visible on homepage</label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label">Section Description</label>
                  <textarea class="form-control" name="tshirt_description" required><?= get_setting('tshirt_description', 'Discover stylish designs and unmatched comfort with our latest collection.') ?></textarea>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card mb-3">
            <div class="card-header py-2 px-3 bg-light">
              <h6 class="mb-0">Long Sleeve Collection Section</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Section Title</label>
                  <input type="text" class="form-control" name="longsleeve_title" 
                        value="<?= get_setting('longsleeve_title', 'Long Sleeve Collection') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Show Section</label>
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="show_longsleeve" value="1" 
                          <?= get_setting('show_longsleeve', '1') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Visible on homepage</label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label class="form-label">Section Description</label>
                  <textarea class="form-control" name="longsleeve_description" required><?= get_setting('longsleeve_description', 'Our Aircool Riders Jersey is built for everyday rides—lightweight, breathable, and made for ultimate performance.') ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
      
      <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn mb-2" 
        style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white; box-shadow: 0 3px 6px rgba(255, 99, 71, 0.3);">
          <i class="material-symbols-rounded" style="vertical-align: middle; margin-right: 4px;">save</i>
          <span style="vertical-align: middle;">Save Changes</span>
        </button>
      </div>
    </form>
  </div>
</div>
      </div>
    </div>
    <div class="col-lg-6 col-md-12 col-12">
      <div class="card">
        <!-- Enhanced card header with significantly more offset -->
        <div class="card-header p-0 position-relative mt-n8 mx-3 z-index-2">
          <div class="pt-4 pb-3">
            <div class="row px-2 align-items-center">
              <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="text-black text-capitalize mb-0 ms-0">Carousel Customization</h6>
                    <h6 class="text-b text-xs mb-0 opacity-8">Manage your carousel images</h6>
                  </div>
                  <button type="button" class="btn mb-0" data-bs-toggle="modal" data-bs-target="#addImageModal" 
                  style="background: linear-gradient(195deg, #FF7F50, #FF6347); color: white; box-shadow: 0 3px 6px rgba(255, 99, 71, 0.3);">
                  <i class="material-symbols-rounded">add</i>
                  </button>
                </div>
              </div>
              </div>
            </div>
          </div>
        
        <!-- Card body with content -->
        <div class="card-body px-0 pb-2">
          <div class="px-4 pt-3">
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
          </div>
          
          <!-- Table Layout - Centered -->
          <div class="table-responsive px-lg-4 px-2">
            <div class="mx-auto">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                      <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Preview</th>
                      <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Status</th>
                      <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                // Get all carousel images
                require_once 'config/dbcon.php';
                $query = "SELECT * FROM carousel_images ORDER BY created_at DESC";
                $result = mysqli_query($conn, $query);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($image = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td>
                      <div class="d-flex justify-content-center px-2 py-1">
                        <div>
                          <img src="../<?php echo $image['image_path']; ?>" class="border-radius-md shadow-sm" 
                              style="width: 50px; height: 50px; object-fit: cover;" alt="Carousel Image">
                        </div>
                      </div>
                    </td>
                    <td>
                    <div class="d-flex justify-content-center align-items-center py-2">
                      <div class="form-check form-switch text-center py-2">
                        <input class="form-check-input" type="checkbox" 
                            onchange="updateImageStatus(<?php echo $image['id']; ?>, this.checked)" 
                            <?php echo $image['is_active'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label d-sm-none d-lg-block">
                          <?php echo $image['is_active'] == 1 ? 'Active' : 'Inactive'; ?>
                        </label>
                      </div>
                    </div>
                    </td>
                    <td>
                    <div class="d-flex justify-content-center">
                      <form action="functions/carousel-actions.php" method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">
                          <i class='bx bx-trash bx-sm'></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                  <?php
                    }
                  } else {
                  ?>
                  <tr>
                    <td colspan="4" class="text-center py-4">
                      <div class="alert bg-gradient-dark text-muted text-center mb-0">
                        No carousel images found. Add your first image!
                      </div>
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

<!-- Add Image Modal -->
<div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="addImageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title" id="addImageModalLabel">Add New Carousel Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="functions/carousel-actions.php" method="POST" enctype="multipart/form-data">
      <div class="modal-body">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="is_active" value="1">
        <div class="mb-3">
            <label class="form-label">Upload Images (1920x800 recommended)</label>
            <input type="file" name="carousel_image[]" id="carousel_image" class="form-control d-none" multiple accept="image/*">
            <input type="text" class="form-control" id="carousel_image_text" placeholder="No files selected" readonly>
            <div class="error-message text-danger"></div>
            <button class="btn bg-gradient-primary mt-4" type="button" id="carousel_image_btn">Choose Images</button>
            <div id="carousel_image_preview" class="mt-2 d-flex flex-wrap"></div>
            <small class="form-text text-muted mt-2">The newest uploaded image will automatically become the active one.</small>
        </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn bg-gradient-primary">Upload Image</button>
        </div>
      </form>
    </div>
  </div>
</div>

</main>

<!-- Core JS Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/homepage-customize.js"></script>
</body>
</html>