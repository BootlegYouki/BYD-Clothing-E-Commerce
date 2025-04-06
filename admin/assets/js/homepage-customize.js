/**
 * Homepage Customization JavaScript
 * Handles UI interactions for the homepage customization admin panel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize success message auto-dismissal
    initializeAlerts();
    
    // Initialize modal functionality
    initializeModals();
    
    // Initialize carousel image upload functionality
    initializeImageUpload();
  });
  
  /**
   * Initialize auto-dismissal for success alerts
   */
  function initializeAlerts() {
    const successAlerts = ['success-alert', 'content-success-alert'];
    
    successAlerts.forEach(alertId => {
      const alert = document.getElementById(alertId);
      if (alert) {
        setTimeout(function() {
          alert.classList.remove('show');
          setTimeout(function() {
            alert?.remove();
          }, 150);
        }, 3000);
      }
    });
  }
  
  /**
   * Initialize modal event handlers
   */
  function initializeModals() {
    const deleteModal = document.getElementById('deleteImageModal');
    if (deleteModal) {
      deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const imageId = button.getAttribute('data-id');
        document.getElementById('delete_image_id').value = imageId;
      });
    }
  }
  
  /**
   * Toggle carousel image active status
   * @param {number} imageId - The ID of the carousel image
   * @param {boolean} isActive - Whether the image should be active
   */
  window.updateImageStatus = function(imageId, isActive) {
    // Get the clicked element
    const checkbox = event.target;
    
    // Immediately update the label
    const label = checkbox.nextElementSibling;
    if (label) {
      label.textContent = isActive ? 'Active' : 'Inactive';
    }
    
    // Send request to server
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
  
/**
 * Initialize image upload functionality
 */
function initializeImageUpload() {
    const carouselImageInput = document.getElementById('carousel_image');
    const carouselImageBtn = document.getElementById('carousel_image_btn');
    const carouselImageText = document.getElementById('carousel_image_text');
    const carouselImagePreview = document.getElementById('carousel_image_preview');
    const errorMessage = document.querySelector('.error-message');
  
    if (!carouselImageBtn || !carouselImageInput) return;
  
    // Trigger file input when button is clicked
    carouselImageBtn.addEventListener('click', function() {
      carouselImageInput.click();
    });
    
    // Handle file selection
    carouselImageInput.addEventListener('change', function() {
      // Update text to show how many files are selected initially
      carouselImageText.value = this.files.length > 0 ? 
        (this.files.length === 1 ? this.files[0].name : this.files.length + ' files selected') : 
        'No files selected';
      
      // Clear previous previews and error messages
      carouselImagePreview.innerHTML = '';
      errorMessage.textContent = '';
      
      // Show preview for selected images and convert to WebP
      if (this.files.length > 0) {
        let totalSize = 0;
        const originalFiles = Array.from(this.files);
        const convertedFiles = [];
        let filesProcessed = 0;
        
        // Display loading indicator
        errorMessage.textContent = 'Converting images to WebP format...';
        
        originalFiles.forEach(file => {
          totalSize += file.size;
          
          // Validate file type
          if (!file.type.match('image.*')) {
            errorMessage.textContent = 'Please select only image files.';
            carouselImageText.value = 'Invalid file type selected';
            return;
          }
          
          // Convert image to WebP
          convertToWebP(file, function(webpFile) {
            // Add converted file to the array
            convertedFiles.push(webpFile);
            filesProcessed++;
            
            // Create preview with the converted image
            createImagePreview(webpFile, carouselImagePreview);
            
            // When all files are processed, update the input
            if (filesProcessed === originalFiles.length) {
              updateFileInputWithWebP(carouselImageInput, originalFiles, convertedFiles);
              errorMessage.textContent = 'Images converted to WebP format successfully.';
              
              // Show warning if total size is still large
              if (totalSize > 10 * 1024 * 1024) {
                errorMessage.textContent += ' Warning: Total file size exceeds 10MB. The upload might be slow.';
              }
              
              // Clear the message after a few seconds
              setTimeout(() => {
                if (errorMessage.textContent.includes('converted')) {
                  errorMessage.textContent = '';
                }
              }, 3000);
            }
          }, 0.25);
        });
      }
    });
  }
  
/**
 * Create a preview thumbnail for an image file
 * @param {File} file - The image file to preview
 * @param {HTMLElement} container - The container element for the preview
 */
function createImagePreview(file, container) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const previewContainer = document.createElement('div');
      previewContainer.className = 'position-relative m-1';
      
      const img = document.createElement('img');
      img.src = e.target.result;
      img.className = 'img-thumbnail';
      img.style.height = '50px';
      img.style.width = 'auto';
      
      previewContainer.appendChild(img);
      container.appendChild(previewContainer);
    };
    reader.readAsDataURL(file);
  }
  
  /**
   * Convert an image to WebP format
   * @param {File} file - The original image file
   * @param {Function} callback - Callback function with the converted image blob
   * @param {number} quality - Quality of the WebP image (0-1)
   */
  function convertToWebP(file, callback, quality = 0.25) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = new Image();
      img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        
        // Convert to WebP format
        canvas.toBlob(function(blob) {
          // Create a new File object from the blob
          const webpFile = new File([blob], file.name.replace(/\.(jpg|jpeg|png|gif)$/i, '.webp'), {
            type: 'image/webp',
            lastModified: new Date().getTime()
          });
          callback(webpFile);
        }, 'image/webp', quality);
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
  
  /**
   * Update the file input's files with WebP converted images
   * @param {HTMLInputElement} inputElement - The file input element
   * @param {FileList} originalFiles - The original file list
   * @param {Array<File>} convertedFiles - Array of WebP converted files
   */
  function updateFileInputWithWebP(inputElement, originalFiles, convertedFiles) {
    // Create a new DataTransfer object
    const dataTransfer = new DataTransfer();
    
    // Add all converted files to the DataTransfer object
    convertedFiles.forEach(file => {
      dataTransfer.items.add(file);
    });
    
    // Update the file input's files property
    inputElement.files = dataTransfer.files;
    
    // Update text to show files selected
    const fileText = document.getElementById('carousel_image_text');
    if (fileText) {
      fileText.value = convertedFiles.length > 0 ? 
        (convertedFiles.length === 1 ? convertedFiles[0].name : convertedFiles.length + ' files selected (WebP)') : 
        'No files selected';
    }
  }