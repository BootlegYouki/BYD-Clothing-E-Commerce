<?php include 'includes/header.php'; ?>

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-coral shadow-coral border-radius-lg pt-4 pb-3" 
          style="background: linear-gradient(195deg, #FF7F50, #FF6347); box-shadow: 0 4px 20px 0 rgba(255, 111, 71, 0.14), 0 7px 10px -5px rgba(255, 111, 71, 0.4);">
            <h6 class="text-white text-capitalize ps-3">Homepage Customization</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="px-3">
            <!-- Display any messages here -->
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
            
            
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="mb-0">Homepage Carousel Images</h5>
              <button type="button" class="btn bg-gradient-dark" data-bs-toggle="modal" data-bs-target="#addImageModal">
                <i class="material-symbols-rounded">add</i> Add New Image
              </button>
            </div>
            
            <!-- Table Layout -->
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
              <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Preview</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                    <th class="text-secondary opacity-7">Actions</th>
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
                    <div class="d-flex px-2 py-1">
                        <div>
                        <img src="../<?php echo $image['image_path']; ?>" class="avatar-xxl me-3 border-radius-lg" alt="Carousel Image">
                        </div>
                    </div>
                    </td>
                    <td>
                    <div class="form-check form-switch ps-0">
                        <input class="form-check-input ms-auto" type="checkbox" 
                            onchange="updateImageStatus(<?php echo $image['id']; ?>, this.checked)" 
                            <?php echo $image['is_active'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0">
                        <?php echo $image['is_active'] == 1 ? 'Active' : 'Inactive'; ?>
                        </label>
                    </div>
                    </td>
                    <td class="align-middle">
                        <form action="functions/carousel-actions.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger mx-3">
                            <i class='bx bx-trash bx-sm'></i>
                            </button>
                        </form>
                    </td>
                </tr>
                  <?php
                    }
                  } else {
                  ?>
                  <tr>
                    <td colspan="4" class="text-center py-4">
                      <div class="alert bg-gradient-dark text-white text-center mb-0">
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
    <div class="modal-content">
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
            <button class="btn btn-outline-secondary mt-4" type="button" id="carousel_image_btn">Choose Images</button>
            <div id="carousel_image_preview" class="mt-2 d-flex flex-wrap"></div>
            <small class="form-text text-muted mt-2">The newest uploaded image will automatically become the active one.</small>
        </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn bg-gradient-primary" style="background: linear-gradient(195deg, #FF7F50, #FF6347);">Upload Image</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Modify your existing script section
document.addEventListener('DOMContentLoaded', function() {
  var deleteModal = document.getElementById('deleteImageModal');
  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var imageId = button.getAttribute('data-id');
      document.getElementById('delete_image_id').value = imageId;
    });
  }
  
  // Toggle switch for active status
  window.updateImageStatus = function(imageId, isActive) {
  // Get the clicked element
  const checkbox = event.target;
  
  // Immediately update the label
  const label = checkbox.nextElementSibling;
  if (label) {
    label.textContent = isActive ? 'Active' : 'Inactive';
  }
  
  // Then send request to server
  fetch('functions/carousel-actions.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=toggle&image_id=' + imageId + '&is_active=' + (isActive ? 1 : 0)
  })
  .then(response => response.json())
  .then(data => {
    if (!data.success) {
      // If server update fails, revert the UI change
      console.error('Error:', data.message);
      checkbox.checked = !isActive;
      if (label) {
        label.textContent = !isActive ? 'Active' : 'Inactive';
      }
    }
  })
  .catch(error => {
    // If there's a network error, revert the UI change
    console.error('Error:', error);
    checkbox.checked = !isActive;
    if (label) {
      label.textContent = !isActive ? 'Active' : 'Inactive';
    }
  });
};
  
  // File upload preview functionality
  const carouselImageInput = document.getElementById('carousel_image');
  const carouselImageBtn = document.getElementById('carousel_image_btn');
  const carouselImageText = document.getElementById('carousel_image_text');
  const carouselImagePreview = document.getElementById('carousel_image_preview');
  const errorMessage = document.querySelector('.error-message');

  if (carouselImageBtn && carouselImageInput) {
    carouselImageBtn.addEventListener('click', function() {
      carouselImageInput.click();
    });
    
    carouselImageInput.addEventListener('change', function() {
      // Update text to show how many files are selected
      carouselImageText.value = this.files.length > 0 ? 
        (this.files.length === 1 ? this.files[0].name : this.files.length + ' files selected') : 
        'No files selected';
      
      // Clear previous previews
      carouselImagePreview.innerHTML = '';
      errorMessage.textContent = '';
      
      // Show preview for images
      if (this.files.length > 0) {
        let totalSize = 0;
        
        for (let i = 0; i < this.files.length; i++) {
          const file = this.files[i];
          totalSize += file.size;
          
          // Create preview for each image
          const reader = new FileReader();
          reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail m-1';
            img.style.height = '50px';
            img.style.width = 'auto';
            carouselImagePreview.appendChild(img);
          }
          reader.readAsDataURL(file);
        }
        
        // Show warning if total size is large
        if (totalSize > 10 * 1024 * 1024) {
          errorMessage.textContent = 'Warning: Total file size exceeds 10MB. The upload might be slow.';
        }
      }
    });
  }
});
</script>

<?php include 'includes/footer.php'; ?>