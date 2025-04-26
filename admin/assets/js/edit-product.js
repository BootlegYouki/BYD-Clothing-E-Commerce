document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const categorySelect = document.getElementById('category');
    
    if (nameInput) {
        nameInput.addEventListener('input', updateSKU);
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', updateSKU);
    }

    // Primary Image Handlers
    setupPrimaryImageHandlers();
    
    // Additional Images Handlers
    setupAdditionalImagesHandlers();
    
    // Update page title based on current page
    updatePageTitle();
    
    // Setup form submission handler
    setupFormSubmissionHandler();
});

function setupFormSubmissionHandler() {
    const form = document.querySelector('form[action="functions/code.php"]');
    if (!form) return;
    
    form.addEventListener('submit', function(event) {
        // Set the deleted_images hidden input value
        if (tempDeletedImages && tempDeletedImages.length > 0) {
            document.getElementById('deleted_images').value = tempDeletedImages.join(',');
            console.log('Images marked for deletion on submit:', tempDeletedImages);
        }
    });
}

// Convert image to WebP format
function convertImageToWebP(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                // Set fixed square resolution (e.g., 800x800 pixels)
                const targetSize = 1200;
                const canvas = document.createElement('canvas');
                canvas.width = targetSize;
                canvas.height = targetSize;
                const ctx = canvas.getContext('2d');
                
                // Fill with white background
                ctx.fillStyle = "#FFFFFF";
                ctx.fillRect(0, 0, targetSize, targetSize);
                
                // Calculate scaling and positioning to maintain aspect ratio
                let sourceX = 0, sourceY = 0;
                let sourceWidth = img.width, sourceHeight = img.height;
                
                // Crop the image to square if needed
                if (sourceWidth > sourceHeight) {
                    sourceX = (sourceWidth - sourceHeight) / 2;
                    sourceWidth = sourceHeight;
                } else if (sourceHeight > sourceWidth) {
                    sourceY = (sourceHeight - sourceWidth) / 2;
                    sourceHeight = sourceWidth;
                }
                
                // Draw image centered and scaled on canvas
                ctx.drawImage(
                    img,
                    sourceX, sourceY, sourceWidth, sourceHeight,  // Source rectangle
                    0, 0, targetSize, targetSize                  // Destination rectangle
                );
                
                // Quality setting for WebP (0.8 = 80% quality)
                canvas.toBlob(function(blob) {
                    // Create a new file with WebP extension
                    const fileName = file.name.replace(/\.[^/.]+$/, "") + '.webp';
                    const webpFile = new File([blob], fileName, { type: 'image/webp' });
                    resolve(webpFile);
                }, 'image/webp', 0.5);
            };
            img.onerror = function() {
                reject(new Error('Failed to load image'));
            };
            img.src = event.target.result;
        };
        reader.onerror = function() {
            reject(new Error('Failed to read file'));
        };
        reader.readAsDataURL(file);
    });
}

// Convert multiple images to WebP
async function convertMultipleImagesToWebP(fileList) {
    const webpFiles = [];
    const dt = new DataTransfer();
    
    for (let i = 0; i < fileList.length; i++) {
        try {
            const webpFile = await convertImageToWebP(fileList[i]);
            webpFiles.push(webpFile);
            dt.items.add(webpFile);
        } catch (error) {
            console.error('Error converting image:', error);
            // Fall back to original if conversion fails
            dt.items.add(fileList[i]);
        }
    }
    
    return dt.files;
}

// Primary Image Functions
function setupPrimaryImageHandlers() {
    const primaryImageBtn = document.getElementById('primary_image_btn');
    const primaryImageInput = document.getElementById('primary_image');
    const primaryImageText = document.getElementById('primary_image_text');
    const primaryImagePreview = document.getElementById('primary_image_preview');
    
    if (!primaryImageBtn || !primaryImageInput) return;
    
    // Click handler for the button to trigger file input
    primaryImageBtn.addEventListener('click', () => primaryImageInput.click());
    
    // Change handler for when a file is selected
    primaryImageInput.addEventListener('change', async function() {
        const errorContainer = this.parentNode.querySelector('.error-message');
        errorContainer.textContent = '';
        const files = this.files;
        
        // Handle existing primary image deletion if present
        await handleExistingPrimaryImageDeletion();
        
        // Validate file selection
        if (files.length > 1) {
            displayError(errorContainer, 'You can only upload one primary image.');
            resetPrimaryImageInput();
            return;
        }
        
        if (files.length === 0) {
            resetPrimaryImageInput();
            return;
        }
        
        // Process the selected image
        primaryImageText.value = 'Converting image...';
        
        try {
            // Convert the image to WebP format
            const webpFiles = await convertMultipleImagesToWebP(files);
            this.files = webpFiles;
            primaryImageText.value = webpFiles.length + ' file selected (WebP)';
            
            // Generate preview for the converted image
            generatePrimaryImagePreview(webpFiles[0]);
        } catch (error) {
            console.error('Error during WebP conversion:', error);
            displayError(errorContainer, 'Image conversion failed. Please try again.');
            resetPrimaryImageInput();
        }
    });
    
    // Helper function to handle existing primary image deletion
    async function handleExistingPrimaryImageDeletion() {
        const existingPrimaryImage = document.querySelector('#primary_image_container');
        if (!existingPrimaryImage || !existingPrimaryImage.dataset.imageId) return;
        
        const imageId = existingPrimaryImage.dataset.imageId;
        
        try {
            const response = await fetch(`../functions/delete-image.php?id=${imageId}`);
            const data = await response.json();
            
            if (data.success) {
                console.log('Primary image deleted successfully');
                existingPrimaryImage.dataset.imageId = '';
                
                if (document.getElementById('primary_image_id')) {
                    document.getElementById('primary_image_id').value = '';
                }
            } else {
                console.error('Failed to delete primary image:', data.message);
            }
        } catch (error) {
            console.error('Error deleting primary image:', error);
        }
        
        // Add to tempDeletedImages array as fallback
        if (!tempDeletedImages.includes(imageId)) {
            tempDeletedImages.push(imageId);
        }
        
        // Update hidden input for deleted images
        document.getElementById('deleted_images').value = tempDeletedImages.join(',');
    }
    
    // Helper function to generate image preview
    function generatePrimaryImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            primaryImagePreview.innerHTML = `
                <div style="display:inline-block; margin-right:5px; text-align: center; position: relative;">
                    <img src="${e.target.result}" style="max-width: 106px; max-height: 106px; border-radius: 0.5rem;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                        style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                        onclick="removePrimaryImage()"
                        title="Remove image">
                        <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                    </button>
                    <small class="d-block mt-1 text-success">Converted to WebP</small>
                    <small class="d-block text-info">New primary image</small>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
    
    // Helper function to display error
    function displayError(container, message) {
        container.textContent = message;
    }
    
    // Helper function to reset the primary image input
    function resetPrimaryImageInput() {
        primaryImageInput.value = '';
        primaryImageText.value = 'No files selected';
        primaryImagePreview.innerHTML = '';
    }
}

function removePrimaryImage() {
    document.getElementById('primary_image').value = '';
    document.getElementById('primary_image_text').value = 'No files selected';
    document.getElementById('primary_image_preview').innerHTML = '';
}

// Additional Images Functions
function setupAdditionalImagesHandlers() {
    const additionalImagesBtn = document.getElementById('additional_images_btn');
    const additionalImagesInput = document.getElementById('additional_images');
    const additionalImagesText = document.getElementById('additional_images_text');
    const additionalImagesPreview = document.getElementById('additional_images_preview');
    
    if (!additionalImagesBtn || !additionalImagesInput) return;
    
    // Click handler for the button to trigger file input
    additionalImagesBtn.addEventListener('click', () => additionalImagesInput.click());
    
    // Change handler for when files are selected
    additionalImagesInput.addEventListener('change', async function() {
        const errorContainer = this.parentNode.querySelector('.error-message');
        errorContainer.textContent = '';
        const files = this.files;
        
        // Validate file selection
        if (files.length > 4) {
            errorContainer.textContent = 'You can only upload up to 4 additional images.';
            resetAdditionalImagesInput();
            return;
        }
        
        if (files.length === 0) {
            resetAdditionalImagesInput();
            return;
        }
        
        // Process the selected images
        additionalImagesText.value = 'Converting images...';
        
        try {
            // Convert images to WebP format
            const webpFiles = await convertMultipleImagesToWebP(files);
            this.files = webpFiles;
            additionalImagesText.value = webpFiles.length + ' files selected (WebP)';
            
            // Generate previews for the converted images
            generateAdditionalImagesPreview(webpFiles);
        } catch (error) {
            console.error('Error during WebP conversion:', error);
            errorContainer.textContent = 'Image conversion failed. Please try again.';
            resetAdditionalImagesInput();
        }
    });
    
    // Helper function to generate image previews
    function generateAdditionalImagesPreview(files) {
        additionalImagesPreview.innerHTML = '';
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewDiv = document.createElement('div');
                previewDiv.id = `preview_${i}`;
                previewDiv.style.display = 'inline-block';
                previewDiv.style.marginRight = '10px';
                previewDiv.style.marginBottom = '10px';
                previewDiv.style.textAlign = 'center';
                previewDiv.style.position = 'relative';
                
                previewDiv.innerHTML = `
                    <img src="${e.target.result}" style="max-width: 106px; max-height: 106px; border-radius: 0.5rem;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                        style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                        onclick="removeAdditionalImage(${i})"
                        title="Remove image">
                        <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                    </button>
                    <small class="d-block mt-1 text-success">Converted to WebP</small>
                `;
                
                additionalImagesPreview.appendChild(previewDiv);
            };
            
            reader.readAsDataURL(file);
        }
    }
    
    // Helper function to reset the additional images input
    function resetAdditionalImagesInput() {
        additionalImagesInput.value = '';
        additionalImagesText.value = 'No files selected';
        additionalImagesPreview.innerHTML = '';
    }
}

function removeAdditionalImage(index) {
    const dt = new DataTransfer();
    const input = document.getElementById('additional_images');
    const { files } = input;

    // Create a new FileList without the removed file
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }

    // Update the input files
    input.files = dt.files;
    document.getElementById('additional_images_text').value = dt.files.length > 0 ? 
        dt.files.length + ' files selected (WebP)' : 'No files selected';
    
    // Remove the preview
    document.getElementById(`preview_${index}`).remove();
    
    // Recreate previews if needed to fix indexes
    if (dt.files.length === 0) {
        document.getElementById('additional_images_preview').innerHTML = '';
    } else {
        // Regenerate all previews with correct indices
        const previewContainer = document.getElementById('additional_images_preview');
        previewContainer.innerHTML = '';
        
        for (let i = 0; i < dt.files.length; i++) {
            const file = dt.files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewDiv = document.createElement('div');
                previewDiv.id = `preview_${i}`;
                previewDiv.style.display = 'inline-block';
                previewDiv.style.marginRight = '5px';
                previewDiv.style.textAlign = 'center';
                previewDiv.style.position = 'relative';
                
                previewDiv.innerHTML = `
                    <img src="${e.target.result}" style="max-width: 106px; max-height: 106px; border-radius: 0.5rem;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                        style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                        onclick="removeAdditionalImage(${i})"
                        title="Remove image">
                        <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                    </button>
                    <small class="d-block mt-1 text-success">Converted to WebP</small>
                `;
                
                previewContainer.appendChild(previewDiv);
            };
            
            reader.readAsDataURL(file);
        }
    }
}

// Existing Image Deletion
let tempDeletedImages = [];

function removeExistingImage(imageId) {
    // Add to our temporary deleted images array
    if (!tempDeletedImages.includes(imageId)) {
        tempDeletedImages.push(imageId);
    }
    
    // Update the hidden input with comma-separated IDs
    document.getElementById('deleted_images').value = tempDeletedImages.join(',');
    
    // Hide the image (instead of removing it from the DOM)
    const imageContainer = document.getElementById(`image_container_${imageId}`);
    if (imageContainer) {
        imageContainer.style.display = 'none';
        imageContainer.classList.add('temp-deleted');
        
        // Check if all existing images are now hidden
        const container = document.getElementById('additional-images-container');
        const visibleImages = container.querySelectorAll('[id^="image_container_"]:not(.temp-deleted)');
        
        if (visibleImages.length === 0) {
            // All images are temporarily deleted, show the message
            if (!document.getElementById('no-images-msg')) {
                container.insertAdjacentHTML('beforeend', '<p class="text-danger mb-5" id="no-images-msg">No additional images</p>');
                container.classList.remove('mb-3');
                container.classList.add('mb-5');
            }
        }
        
        // Count total visible images (existing + new)
        const totalVisibleImages = visibleImages.length + 
            (document.getElementById('additional_images').files.length || 0);
        
        // Update UI based on total visible images
        if (totalVisibleImages === 0) {
            if (!document.getElementById('no-images-msg')) {
                container.insertAdjacentHTML('beforeend', '<p class="text-danger mb-5" id="no-images-msg">No additional images</p>');
            }
        }
        
        // Update the remaining images text
        updateRemainingImagesText();
    } else {
        console.error('Image container not found:', imageId);
    }
}

function updateRemainingImagesText() {
    const visibleImages = document.querySelectorAll('#additional-images-container [id^="image_container_"]:not(.temp-deleted)').length;
    const maxImages = 4;
    const remaining = maxImages - visibleImages;
    const remainingTextElement = document.querySelector('p.text-muted.small.mb-2');
    
    if (remainingTextElement) {
        if (remaining > 0) {
            remainingTextElement.textContent = `You can add ${remaining} more image${remaining !== 1 ? 's' : ''}`;
        } else {
            remainingTextElement.textContent = "You've reached the maximum number of additional images";
        }
    }
    
    // Also update the "Choose Images" button state based on availability
    const additionalImagesBtn = document.getElementById('additional_images_btn');
    if (additionalImagesBtn) {
        if (remaining > 0) {
            additionalImagesBtn.disabled = false;
            additionalImagesBtn.classList.remove('btn-secondary');
            additionalImagesBtn.classList.add('btn-outline-secondary');
        } else {
            additionalImagesBtn.disabled = true;
            additionalImagesBtn.classList.remove('btn-outline-secondary');
            additionalImagesBtn.classList.add('btn-secondary');
        }
    }
}

// Page Title Update
function updatePageTitle() {
    // Get the current page from the URL
    const path = window.location.pathname;
    const page = path.substring(path.lastIndexOf('/') + 1).replace('.php', '');
    
    const titleMap = {
        'index': 'Dashboard',
        'products': 'Products Management',
        'add-product': 'Add New Product',
        'edit-product': 'Edit Product',
        'homepage-customize': 'Homepage Customization',
        'categories': 'Categories',
        'orders': 'Orders Management',
        'customers': 'Customer Management'
    };
    
    // Update navbar title
    const navbarTitle = document.querySelector('.top-navbar h4');
    if (navbarTitle && titleMap[page]) {
        navbarTitle.textContent = titleMap[page];
    }
}

function generateSKU_JS(productName, category) {
    const prefix = category.substring(0, 3).toUpperCase();
    const shortName = productName.replace(/[^a-zA-Z0-9]/g, '').substring(0, 4).toUpperCase();
    const randomNumber = Math.floor(Math.random() * (999 - 100 + 1)) + 100;
    return prefix + "-" + shortName + "-" + randomNumber;
}

// Add SKU update functions
function updateSKU() {
    const productName = document.getElementById('name').value;
    const category = document.getElementById('category').value;
    
    // Only update SKU if both name and category are provided
    if (productName && category) {
        const sku = generateSKU_JS(productName, category);
        document.getElementById('sku').value = sku;
    }
}

// Make these functions global so they can be called from HTML
window.removePrimaryImage = removePrimaryImage;
window.removeAdditionalImage = removeAdditionalImage;
window.removeExistingImage = removeExistingImage;
window.generateSKU_JS = generateSKU_JS;
window.updateSKU = updateSKU;