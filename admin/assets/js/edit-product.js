document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const categorySelect = document.getElementById('category');
    const newCategoryContainer = document.getElementById('new_category_container');
    const newCategoryInput = document.getElementById('new_category');
    const addCategoryBtn = document.getElementById('add_category_btn');
    const removeCategoryContainer = document.getElementById('remove_category_container');
    const removeCategorySelect = document.getElementById('remove_category');
    const removeCategoryBtn = document.getElementById('remove_category_btn');
    
    if (nameInput) {
        nameInput.addEventListener('input', updateSKU);
    }

    if (categorySelect) {
        // Update your existing categorySelect change event to include SKU update
        categorySelect.addEventListener('change', function() {
            if(this.value === 'new') {
                newCategoryContainer.style.display = 'block';
                removeCategoryContainer.style.display = 'none';
                // Don't set required here - we'll handle that during submission
                newCategoryInput.focus();
            } else if(this.value === 'remove') {
                newCategoryContainer.style.display = 'none';
                removeCategoryContainer.style.display = 'block';
                // Make sure to remove required attribute
                if(newCategoryInput.hasAttribute('required')) {
                    newCategoryInput.removeAttribute('required');
                }
                newCategoryInput.value = '';
            } else {
                newCategoryContainer.style.display = 'none';
                removeCategoryContainer.style.display = 'none';
                // Make sure to remove required attribute
                if(newCategoryInput.hasAttribute('required')) {
                    newCategoryInput.removeAttribute('required');
                }
                newCategoryInput.value = '';
                
                // Update SKU when category changes
                updateSKU();
            }
        });
    }
    
    // Handle add category button
    if(addCategoryBtn) {
        addCategoryBtn.addEventListener('click', function() {
            const newCategoryValue = newCategoryInput.value.trim();
            const msgContainer = document.getElementById('add_category_msg');
            
            if(!newCategoryValue) {
                msgContainer.innerHTML = '<div class="alert alert-warning">Please enter a category name</div>';
                return;
            }
            
            // Check if category already exists in the dropdown
            let categoryExists = false;
            Array.from(categorySelect.options).forEach(option => {
                if(option.value === newCategoryValue) {
                    categoryExists = true;
                }
            });
            
            if(categoryExists) {
                msgContainer.innerHTML = '<div class="alert alert-warning">This category already exists</div>';
                return;
            }
            
            // Add the new category to both dropdowns
            const newOption = document.createElement('option');
            newOption.value = newCategoryValue;
            newOption.text = newCategoryValue;
            
            // Insert before the "+ Add New Category" option
            const addNewIndex = Array.from(categorySelect.options).findIndex(option => option.value === 'new');
            categorySelect.add(newOption.cloneNode(true), addNewIndex);
            
            // Add to the remove category dropdown as well
            if(removeCategorySelect) {
                removeCategorySelect.add(newOption.cloneNode(true));
            }
            
            // Set the main dropdown to the new category
            categorySelect.value = newCategoryValue;
            
            // Reset the new category input and hide the container
            newCategoryInput.value = '';
            newCategoryContainer.style.display = 'none';

            updateSKU();
            
            // Show success message
            msgContainer.innerHTML = '<div class="alert alert-success">New category added successfully</div>';
            
            // Hide container after a delay
            setTimeout(() => {
                newCategoryContainer.style.display = 'none';
                msgContainer.innerHTML = '';
            }, 2000);
        });
    }
    
    // Handle category removal
    if(removeCategoryBtn) {
        removeCategoryBtn.addEventListener('click', function() {
            const selectedCategory = removeCategorySelect.value;
            const msgContainer = document.getElementById('remove_category_msg');
            
            if(!selectedCategory) {
                msgContainer.innerHTML = '<div class="alert alert-warning">Please select a category to remove</div>';
                return;
            }
            
            // Confirm before removal
            if(!confirm(`Are you sure you want to remove "${selectedCategory}" category? All products in this category will be set to "Uncategorized".`)) {
                return;
            }
            
            // Send AJAX request to remove the category
            fetch('functions/remove-category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `category=${encodeURIComponent(selectedCategory)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    msgContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    
                    // Remove the category option from both dropdowns
                    const options = document.querySelectorAll(`option[value="${selectedCategory}"]`);
                    options.forEach(option => option.remove());
                    
                    // Reset selects
                    categorySelect.value = '';
                    removeCategorySelect.value = '';
                    
                    // Hide the removal container after successful removal
                    setTimeout(() => {
                        removeCategoryContainer.style.display = 'none';
                    }, 2000);
                } else {
                    msgContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                msgContainer.innerHTML = '<div class="alert alert-danger">An error occurred while removing the category. Check the console for details.</div>';
            });
        });
    }

    const fabricSelect = document.getElementById('fabric');
    const newFabricContainer = document.getElementById('new_fabric_container');
    const newFabricInput = document.getElementById('new_fabric');
    const addFabricBtn = document.getElementById('add_fabric_btn');
    const removeFabricContainer = document.getElementById('remove_fabric_container');
    const removeFabricSelect = document.getElementById('remove_fabric');
    const removeFabricBtn = document.getElementById('remove_fabric_btn');
    
    if(fabricSelect) {
        fabricSelect.addEventListener('change', function() {
            if(this.value === 'new') {
                newFabricContainer.style.display = 'block';
                removeFabricContainer.style.display = 'none';
                // Don't set required here - we'll handle that during submission
                newFabricInput.focus();
            } else if(this.value === 'remove') {
                newFabricContainer.style.display = 'none';
                removeFabricContainer.style.display = 'block';
                // Make sure to remove required attribute
                if(newFabricInput.hasAttribute('required')) {
                    newFabricInput.removeAttribute('required');
                }
                newFabricInput.value = '';
            } else {
                newFabricContainer.style.display = 'none';
                removeFabricContainer.style.display = 'none';
                // Make sure to remove required attribute
                if(newFabricInput.hasAttribute('required')) {
                    newFabricInput.removeAttribute('required');
                }
                newFabricInput.value = '';
            }
        });
    }
    
    // Handle add fabric button
    if(addFabricBtn) {
        addFabricBtn.addEventListener('click', function() {
            const newFabricValue = newFabricInput.value.trim();
            const msgContainer = document.getElementById('add_fabric_msg');
            
            if(!newFabricValue) {
                msgContainer.innerHTML = '<div class="alert alert-warning">Please enter a fabric type</div>';
                return;
            }
            
            // Check if fabric already exists in the dropdown
            let fabricExists = false;
            Array.from(fabricSelect.options).forEach(option => {
                if(option.value === newFabricValue) {
                    fabricExists = true;
                }
            });
            
            if(fabricExists) {
                msgContainer.innerHTML = '<div class="alert alert-warning">This fabric already exists</div>';
                return;
            }
            
            // Add the new fabric to both dropdowns
            const newOption = document.createElement('option');
            newOption.value = newFabricValue;
            newOption.text = newFabricValue;
            
            // Insert before the "+ Add New Fabric" option
            const addNewIndex = Array.from(fabricSelect.options).findIndex(option => option.value === 'new');
            fabricSelect.add(newOption.cloneNode(true), addNewIndex);
            
            // Add to the remove fabric dropdown as well
            if(removeFabricSelect) {
                removeFabricSelect.add(newOption.cloneNode(true));
            }
            
            // Set the main dropdown to the new fabric
            fabricSelect.value = newFabricValue;
            
            // Reset the new fabric input and hide the container
            newFabricInput.value = '';
            newFabricContainer.style.display = 'none';
            
            // Show success message
            msgContainer.innerHTML = '<div class="alert alert-success">New fabric added successfully</div>';
            
            // Hide container after a delay
            setTimeout(() => {
                newFabricContainer.style.display = 'none';
                msgContainer.innerHTML = '';
            }, 2000);
        });
    }
    
    // Handle fabric removal
    if(removeFabricBtn) {
        removeFabricBtn.addEventListener('click', function() {
            const selectedFabric = removeFabricSelect.value;
            const msgContainer = document.getElementById('remove_fabric_msg');
            
            if(!selectedFabric) {
                msgContainer.innerHTML = '<div class="alert alert-warning">Please select a fabric to remove</div>';
                return;
            }
            
            // Confirm before removal
            if(!confirm(`Are you sure you want to remove "${selectedFabric}" fabric? This will affect all products using this fabric.`)) {
                return;
            }
            
            // Send AJAX request to remove the fabric
            fetch('functions/remove-fabric.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `fabric=${encodeURIComponent(selectedFabric)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    msgContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    
                    // Remove the fabric option from both dropdowns
                    const options = document.querySelectorAll(`option[value="${selectedFabric}"]`);
                    options.forEach(option => option.remove());
                    
                    // Reset selects
                    fabricSelect.value = '';
                    removeFabricSelect.value = '';
                    
                    // Hide the removal container after successful removal
                    setTimeout(() => {
                        removeFabricContainer.style.display = 'none';
                    }, 2000);
                } else {
                    msgContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                msgContainer.innerHTML = '<div class="alert alert-danger">An error occurred while removing the fabric. Check the console for details.</div>';
            });
        });
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
        // First check if new_fabric is set to required but hidden
        const newFabricInput = document.getElementById('new_fabric');
        const newFabricContainer = document.getElementById('new_fabric_container');
        if (newFabricInput && newFabricContainer && 
            newFabricInput.hasAttribute('required') && 
            window.getComputedStyle(newFabricContainer).display === 'none') {
            newFabricInput.removeAttribute('required');
        }
        
        // Also check new_category
        const newCategoryInput = document.getElementById('new_category');
        const newCategoryContainer = document.getElementById('new_category_container');
        if (newCategoryInput && newCategoryContainer && 
            newCategoryInput.hasAttribute('required') && 
            window.getComputedStyle(newCategoryContainer).display === 'none') {
            newCategoryInput.removeAttribute('required');
        }
        
        // More general approach - remove required from all inputs in hidden containers
        const hiddenContainers = document.querySelectorAll('[style*="display: none"]');
        hiddenContainers.forEach(container => {
            const requiredInputs = container.querySelectorAll('[required]');
            requiredInputs.forEach(input => {
                input.removeAttribute('required');
            });
        });
        
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
        if (files.length > 3) {
            errorContainer.textContent = 'You can only upload up to 3 additional images.';
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
    const maxImages = 3;
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
    
    // Only update SKU if both name and category are provided and it's not in "new" or "remove" mode
    if (productName && category && category !== 'new' && category !== 'remove') {
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