// Global variables
let tempDeletedImages = [];
let additionalFilesCollection = [];

// Primary image handling
document.getElementById('primary_image_container_clickable').addEventListener('click', function(e) {
    // Don't trigger file input if clicking on any element within the preview section
    if (e.target.closest('#primary_image_preview') || 
        e.target.id === 'primary_image_text' ||
        e.target.classList.contains('file-name-display')) {
        return;
    }
    
    // Add active state visual feedback
    this.classList.add('uploading');
    setTimeout(() => {
        this.classList.remove('uploading');
    }, 300);
    
    document.getElementById('primary_image').click();
});

// Add drag and drop event listeners for primary image container
const primaryImageContainer = document.getElementById('primary_image_container_clickable');
primaryImageContainer.addEventListener('dragover', handleDragOver);
primaryImageContainer.addEventListener('dragenter', handleDragEnter);
primaryImageContainer.addEventListener('dragleave', handleDragLeave);
primaryImageContainer.addEventListener('drop', function(e) {
    handleDrop(e, 'primary');
});

// Additional images container drag and drop
const additionalImagesContainer = document.getElementById('additional_images_container_clickable');
additionalImagesContainer.addEventListener('dragover', handleDragOver);
additionalImagesContainer.addEventListener('dragenter', handleDragEnter);
additionalImagesContainer.addEventListener('dragleave', handleDragLeave);
additionalImagesContainer.addEventListener('drop', function(e) {
    handleDrop(e, 'additional');
});

// Drag and drop handler functions
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDragEnter(e) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.add('uploading');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    // Only remove the class if we're leaving the entire container
    if (!e.relatedTarget || !this.contains(e.relatedTarget)) {
        this.classList.remove('uploading');
    }
}

function handleDrop(e, type) {
    e.preventDefault();
    e.stopPropagation();
    
    const container = e.currentTarget;
    container.classList.remove('uploading');
    
    // Get dropped files
    const files = e.dataTransfer.files;
    if (!files.length) return;
    
    if (type === 'primary') {
        // Allow only one file for primary image
        if (files.length > 1) {
            const errorContainer = container.querySelector('.error-message');
            errorContainer.textContent = 'You can only upload one primary image.';
            return;
        }
        
        // Set the files to the input
        const input = document.getElementById('primary_image');
        
        // Create a new DataTransfer object
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        input.files = dt.files;
        
        // Trigger the change event manually
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    } else if (type === 'additional') {
        // For additional images
        const input = document.getElementById('additional_images');
        
        // Create a new DataTransfer object
        const dt = new DataTransfer();
        
        // Add up to 3 files or as many as allowed
        const availableSlots = 3 - additionalFilesCollection.length;
        const filesToAdd = Math.min(files.length, availableSlots);
        
        if (filesToAdd <= 0) {
            const errorContainer = container.querySelector('.error-message');
            errorContainer.textContent = `You can only have up to 3 additional images. You already have ${additionalFilesCollection.length}.`;
            return;
        }
        
        for (let i = 0; i < filesToAdd; i++) {
            dt.items.add(files[i]);
        }
        
        input.files = dt.files;
        
        // Trigger the change event manually
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    }
}

// Convert image to WebP format with fixed square resolution
function convertImageToWebP(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
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

// Add file size formatting utility function
function formatFileSize(bytes) {
    if (bytes < 1024) {
        return bytes + ' B';
    } else if (bytes < (1024 * 1024)) {
        return (bytes / 1024).toFixed(1) + ' KB';
    } else {
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
}

document.getElementById('primary_image').addEventListener('change', async function() {
    const files = this.files;
    const errorContainer = this.parentNode.querySelector('.error-message');
    errorContainer.textContent = ''; // Clear previous error

    if (files.length > 1) {
        errorContainer.textContent = 'You can only upload one primary image.';
        this.value = ''; // Clear the input
        document.getElementById('primary_image_text').value = 'No files selected';
        document.getElementById('primary_image_preview').innerHTML = '';
        return;
    }
    
    // Handle existing primary image container if it exists
    const existingPrimaryImage = document.getElementById('primary_image_container');
    if (existingPrimaryImage) {
        // Add image ID to delete list if it has one
        const imageId = existingPrimaryImage.getAttribute('data-image-id');
        if (imageId && !tempDeletedImages.includes(imageId)) {
            tempDeletedImages.push(imageId);
            document.getElementById('deleted_images').value = tempDeletedImages.join(',');
        }
        // Hide the container
        existingPrimaryImage.style.display = 'none';
    }
    
    if (files.length > 0) {
        document.getElementById('primary_image_text').value = 'Converting image...';
        
        try {
            // Convert images to WebP
            const webpFiles = await convertMultipleImagesToWebP(files);
            this.files = webpFiles;
            
            // Get file size for display
            const fileSize = formatFileSize(webpFiles[0].size);
            
            document.getElementById('primary_image_text').value = webpFiles.length + ' file selected';
            
            // Create a simple text-based preview with improved responsive layout
            document.getElementById('primary_image_preview').innerHTML = `
                <div class="alert alert-success removable-image" 
                     onclick="event.stopPropagation(); removePrimaryImage(0, event);" 
                     title="Click to remove image">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 min-width-0">
                            <strong>New Primary Image</strong>
                            <div class="small">File converted to WebP (${fileSize})</div>
                            <div class="small">Click to remove image</div>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Error during WebP conversion:', error);
            errorContainer.textContent = 'Image conversion failed. Please try again.';
            this.value = '';
            document.getElementById('primary_image_text').value = 'No files selected';
        }
    } else {
        document.getElementById('primary_image_text').value = 'No files selected';
        document.getElementById('primary_image_preview').innerHTML = '';
    }
});

// Update the removePrimaryImage function to prevent event propagation
function removePrimaryImage(index, e) {
    // If there's an event object, stop propagation
    if (e) e.stopPropagation();
    
    const input = document.getElementById('primary_image');
    input.value = '';
    document.getElementById('primary_image_text').value = 'No files selected';
    document.getElementById('primary_image_preview').innerHTML = '';
    
    // Show the existing primary image container if it was hidden
    const existingPrimaryImage = document.getElementById('primary_image_container');
    if (existingPrimaryImage) {
        existingPrimaryImage.style.display = 'block';
        // Remove from delete list if it was there
        const imageId = existingPrimaryImage.getAttribute('data-image-id');
        if (imageId) {
            tempDeletedImages = tempDeletedImages.filter(id => id !== imageId);
            document.getElementById('deleted_images').value = tempDeletedImages.join(',');
        }
    }
}

// Additional images handling
document.getElementById('additional_images_container_clickable').addEventListener('click', function(e) {
    // Don't trigger file input if clicking on any element within the preview section
    if (e.target.closest('#additional_images_preview') || 
        e.target.id === 'additional_images_text' ||
        e.target.classList.contains('file-name-display')) {
        return;
    }
    
    // Add active state visual feedback
    this.classList.add('uploading');
    setTimeout(() => {
        this.classList.remove('uploading');
    }, 300);
    
    document.getElementById('additional_images').click();
});

document.getElementById('additional_images').addEventListener('change', async function() {
    const files = this.files;
    const errorContainer = this.parentNode.querySelector('.error-message');
    errorContainer.textContent = ''; // Clear previous error
    
    // Count existing visible images (not marked for deletion)
    const existingVisibleImages = document.querySelectorAll('#additional-images-container [id^="image_container_"]:not(.temp-deleted)').length;
    
    // Count how many files will be in total after this upload
    const totalFilesAfterUpload = existingVisibleImages + additionalFilesCollection.length + files.length;
    
    if (totalFilesAfterUpload > 3) {
        errorContainer.textContent = `You can only have up to 3 additional images. You already have ${existingVisibleImages + additionalFilesCollection.length}.`;
        this.value = ''; // Clear the input
        return;
    }
    
    if (files.length > 0) {
        document.getElementById('additional_images_text').value = 'Converting images...';
        
        try {
            // Convert images to WebP
            const webpFiles = await convertMultipleImagesToWebP(files);
            
            // Create combined list of all files (existing + new)
            const dt = new DataTransfer();
            
            // Add existing files to collection
            for (let i = 0; i < additionalFilesCollection.length; i++) {
                dt.items.add(additionalFilesCollection[i]);
            }
            
            // Add new files to collection
            for (let i = 0; i < webpFiles.length; i++) {
                dt.items.add(webpFiles[i]);
                additionalFilesCollection.push(webpFiles[i]);
            }
            
            // Update the file input with all files
            this.files = dt.files;
            
            document.getElementById('additional_images_text').value = additionalFilesCollection.length + ' files selected (WebP)';
            
            // Clear the "No additional images" message if it exists
            const noImagesMsg = document.getElementById('no-images-msg');
            if (noImagesMsg) noImagesMsg.remove();
            
            // Generate text-based preview HTML for all files with improved responsive layout
            let previewHtml = '';
            for (let i = 0; i < additionalFilesCollection.length; i++) {
                const fileSize = formatFileSize(additionalFilesCollection[i].size);
                previewHtml += `
                    <div id="preview_${i}" class="alert alert-success mb-2 removable-image" 
                         onclick="event.stopPropagation(); removeAdditionalImage(${i}, event);" 
                         title="Click to remove image">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 min-width-0">
                                <strong>New Image ${i+1}</strong>
                                <div class="small">File converted to WebP (${fileSize})</div>
                                <div class="small">Click to remove image</div>
                            </div>
                        </div>
                    </div>
                `;
            }
            document.getElementById('additional_images_preview').innerHTML = previewHtml;
            
        } catch (error) {
            console.error('Error during WebP conversion:', error);
            errorContainer.textContent = 'Image conversion failed. Please try again.';
            this.value = '';
            document.getElementById('additional_images_text').value = 'No files selected';
        }
    } else {
        document.getElementById('additional_images_text').value = 'No files selected';
        document.getElementById('additional_images_preview').innerHTML = '';
    }
});

// Update the removeAdditionalImage function to handle our collection
function removeAdditionalImage(index, e) {
    // If there's an event object, stop propagation
    if (e) e.stopPropagation();
    
    // Remove the file from our collection
    additionalFilesCollection.splice(index, 1);
    
    // Create a new DataTransfer object
    const dt = new DataTransfer();
    
    // Add the remaining files
    for (let i = 0; i < additionalFilesCollection.length; i++) {
        dt.items.add(additionalFilesCollection[i]);
    }
    
    // Update the input files
    const input = document.getElementById('additional_images');
    input.files = dt.files;
    
    // Update the text display
    document.getElementById('additional_images_text').value = 
        additionalFilesCollection.length > 0 ? additionalFilesCollection.length + ' files selected (WebP)' : 'No files selected';
    
    // Regenerate preview with updated indices and improved responsive layout
    let previewHtml = '';
    for (let i = 0; i < additionalFilesCollection.length; i++) {
        const fileSize = formatFileSize(additionalFilesCollection[i].size);
        previewHtml += `
            <div id="preview_${i}" class="alert alert-success mb-2 removable-image" 
                 onclick="event.stopPropagation(); removeAdditionalImage(${i}, event);" 
                 title="Click to remove image">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 min-width-0">
                        <strong>New Image ${i+1}</strong>
                        <div class="small">File converted to WebP (${fileSize})</div>
                        <div class="small">Click to remove image</div>
                    </div>
                </div>
            </div>
        `;
    }
    document.getElementById('additional_images_preview').innerHTML = previewHtml;
}

// Existing Image Deletion - update to use X icon instead of button
function removeExistingImage(imageId, e) {
    // If there's an event object, stop propagation
    if (e) e.stopPropagation();
    
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
                container.insertAdjacentHTML('beforeend', '<p class="alert alert-warning" id="no-images-msg">No additional images</p>');
            }
        }
    }
}

// SKU generation and category/fabric management
document.addEventListener('DOMContentLoaded', function() {
    // Existing code for product name and SKU generation
    const productNameInput = document.getElementById('name');
    const categoryInput = document.getElementById('category');
    const skuInput = document.getElementById('sku');
    const fabricInput = document.getElementById('fabric');

    let lastSelectedCategory = categoryInput.value || '';
    let lastSelectedFabric = fabricInput.value || '';

    function updateSKU() {
        if (productNameInput.value && categoryInput.value && 
            !['add_new_category', 'rename_category', 'delete_category'].includes(categoryInput.value)) {
            
            lastSelectedCategory = categoryInput.value;
            
            fetch('functions/products/generate_sku.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `name=${encodeURIComponent(productNameInput.value)}&category=${encodeURIComponent(categoryInput.value)}`
            })
            .then(response => response.text())
            .then(sku => {
                skuInput.value = sku;
            })
            .catch(error => console.error('Error:', error));
        }
    }

    productNameInput.addEventListener('blur', updateSKU);
    
    // Set up category dropdown
    const categoryDisplay = document.getElementById('category_display');
    const categoryDropdown = document.getElementById('category_dropdown');
    
    categoryDisplay.addEventListener('click', function() {
        // Update categories before showing dropdown
        initializeCategoryDropdown();
    });
    
    // Add new category button
    document.getElementById('add_new_category').addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        showInlineAddForm(this, 'category');
    });
    
    // Set up fabric dropdown
    const fabricDisplay = document.getElementById('fabric_display');
    const fabricDropdown = document.getElementById('fabric_dropdown');
    
    fabricDisplay.addEventListener('click', function() {
        // Update fabrics before showing dropdown
        initializeFabricDropdown();
    });
    
    // Add new fabric button
    document.getElementById('add_new_fabric').addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        showInlineAddForm(this, 'fabric');
    });

    function fetchCategories() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'functions/products/get-categories.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        console.log('Categories loaded:', response.categories);
                        resolve(response.categories);
                    } else {
                        console.error('Error fetching categories:', response.message);
                        resolve([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading categories:', error);
                    console.log('Response:', xhr.responseText);
                    resolve([]);
                }
            });
        });
    }

    function fetchFabrics() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'functions/products/get-fabrics.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        console.log('Fabrics loaded:', response.fabrics);
                        resolve(response.fabrics);
                    } else {
                        console.error('Error fetching fabrics:', response.message);
                        resolve([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading fabrics:', error);
                    console.log('Response:', xhr.responseText);
                    resolve([]);
                }
            });
        });
    }

    initializeCategoryDropdown = function() {
        const categoryContainer = document.querySelector('.category-items-container');
        
        categoryContainer.innerHTML = '<li class="px-3 py-2 text-center text-muted">Loading categories...</li>';
        
        fetchCategories().then(categories => {
            categoryContainer.innerHTML = `
                <li class="alert-container px-3"></li>
            `;
            
            // Add event listener for the "Select Category" option
            const selectCategoryButton = document.querySelector('#category_dropdown button[data-value=""]');
            if (selectCategoryButton) {
                selectCategoryButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    categoryInput.value = '';
                    document.getElementById('selected_category').textContent = 'Select Category';
                    // Close dropdown manually
                    setTimeout(() => {
                        document.querySelector('#category_dropdown').classList.remove('show');
                    }, 100);
                });
            }
            
            if (!categories || categories.length === 0) {
                categoryContainer.innerHTML += '<li class="px-3 py-2 text-center text-muted">No categories found</li>';
                return;
            }
            
            categories.forEach(category => {
                const categoryItem = document.createElement('li');
                categoryItem.className = 'category-item';
                categoryItem.style.cursor = 'pointer'; // Make cursor show it's clickable
                categoryItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center px-3 py-2">
                        <button type="button" class="category-name btn btn-link p-0 text-start text-dark border-0 w-100 text-decoration-none" 
                                data-value="${category}" style="background: none; box-shadow: none;">${category}</button>
                        <div class="category-actions">
                            <button type="button" class="category-action-btn edit-btn" title="Rename Category" data-category="${category}">
                                <i class="material-symbols-rounded">edit</i>
                            </button>
                            <button type="button" class="category-action-btn delete-btn" title="Delete Category" data-category="${category}">
                                <i class="material-symbols-rounded">delete</i>
                            </button>
                        </div>
                    </div>
                `;
                categoryContainer.appendChild(categoryItem);
                
                // Add click event to the entire category item
                categoryItem.addEventListener('click', function(e) {
                    // Don't trigger selection if clicking on action buttons or if already in edit mode
                    if (e.target.closest('.category-actions') || 
                        this.querySelector('.category-name').classList.contains('editing')) {
                        return;
                    }
                    
                    const nameButton = this.querySelector('.category-name');
                    const value = nameButton.getAttribute('data-value');
                    categoryInput.value = value;
                    document.getElementById('selected_category').textContent = value;
                    
                    const productName = document.getElementById('name').value;
                    if (productName) {
                        updateSKU();
                    }
                    
                    // Manually close the dropdown
                    setTimeout(() => {
                        const dropdown = document.querySelector('#category_dropdown');
                        if (dropdown && dropdown.classList.contains('show')) {
                            dropdown.classList.remove('show');
                            document.getElementById('category_display').setAttribute('aria-expanded', 'false');
                        }
                    }, 100);
                });
                
                // Use direct click handler with explicit actions
                categoryItem.querySelector('.category-name').addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (!this.classList.contains('editing')) {
                        const value = this.getAttribute('data-value');
                        categoryInput.value = value;
                        document.getElementById('selected_category').textContent = value;
                        
                        const productName = document.getElementById('name').value;
                        if (productName) {
                            updateSKU();
                        }
                        
                        // Manually close the dropdown
                        setTimeout(() => {
                            const dropdown = document.querySelector('#category_dropdown');
                            if (dropdown && dropdown.classList.contains('show')) {
                                dropdown.classList.remove('show');
                                document.getElementById('category_display').setAttribute('aria-expanded', 'false');
                            }
                        }, 100);
                    }
                });
                
                categoryItem.querySelector('.edit-btn').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const nameElement = this.closest('.category-item').querySelector('.category-name');
                    const currentName = nameElement.getAttribute('data-value');
                    enableInlineEdit(nameElement, currentName, 'category');
                });
                
                categoryItem.querySelector('.delete-btn').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const categoryToDelete = this.getAttribute('data-category');
                    const button = this;
                    const originalHTML = button.innerHTML;
                    
                    // Show loading state on the button
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    
                    // Make AJAX request to delete the category
                    $.ajax({
                        url: 'functions/products/manage-categories.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            category: categoryToDelete
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                // Check if the deleted category is currently selected
                                if (categoryInput.value === categoryToDelete) {
                                    // Reset the selection
                                    categoryInput.value = '';
                                    document.getElementById('selected_category').textContent = 'Select Category';
                                }
                                
                                // Refresh the category dropdown
                                initializeCategoryDropdown();
                                // Show success message
                                showBootstrapAlert('.category-items-container .alert-container', 'success', response.message);
                            } else {
                                // Show error and restore button
                                showBootstrapAlert('.category-items-container .alert-container', 'danger', response.message);
                                button.disabled = false;
                                button.innerHTML = originalHTML;
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.log('Response:', xhr.responseText);
                            showBootstrapAlert('.category-items-container .alert-container', 'danger', 'Server error occurred. Please try again.');
                            button.disabled = false;
                            button.innerHTML = originalHTML;
                        }
                    });
                });
            });
        });
    };

    initializeFabricDropdown = function() {
        const fabricContainer = document.querySelector('.fabric-items-container');
        
        fabricContainer.innerHTML = '<li class="px-3 py-2 text-center text-muted">Loading fabrics...</li>';
        
        fetchFabrics().then(fabrics => {
            fabricContainer.innerHTML = `
                <li class="alert-container px-3"></li>
            `;
            
            // Add event listener for the "Select Fabric" option
            const selectFabricButton = document.querySelector('#fabric_dropdown button[data-value=""]');
            if (selectFabricButton) {
                selectFabricButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fabricInput.value = '';
                    document.getElementById('selected_fabric').textContent = 'Select Fabric';
                    // Close dropdown manually
                    setTimeout(() => {
                        document.querySelector('#fabric_dropdown').classList.remove('show');
                    }, 100);
                });
            }
            
            if (!fabrics || fabrics.length === 0) {
                fabricContainer.innerHTML += '<li class="px-3 py-2 text-center text-muted">No fabrics found</li>';
                return;
            }
            
            fabrics.forEach(fabric => {
                const fabricItem = document.createElement('li');
                fabricItem.className = 'fabric-item';
                fabricItem.style.cursor = 'pointer'; // Make cursor show it's clickable
                fabricItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center px-3 py-2">
                        <button type="button" class="fabric-name btn btn-link p-0 text-start text-dark border-0 w-100 text-decoration-none" 
                                data-value="${fabric}" style="background: none; box-shadow: none;">${fabric}</button>
                        <div class="fabric-actions">
                            <button type="button" class="fabric-action-btn edit-btn" title="Rename Fabric" data-fabric="${fabric}">
                                <i class="material-symbols-rounded">edit</i>
                            </button>
                            <button type="button" class="fabric-action-btn delete-btn" title="Delete Fabric" data-fabric="${fabric}">
                                <i class="material-symbols-rounded">delete</i>
                            </button>
                        </div>
                    </div>
                `;
                fabricContainer.appendChild(fabricItem);
                
                // Add click event to the entire fabric item
                fabricItem.addEventListener('click', function(e) {
                    // Don't trigger selection if clicking on action buttons or if already in edit mode
                    if (e.target.closest('.fabric-actions') || 
                        this.querySelector('.fabric-name').classList.contains('editing')) {
                        return;
                    }
                    
                    const nameButton = this.querySelector('.fabric-name');
                    const value = nameButton.getAttribute('data-value');
                    fabricInput.value = value;
                    document.getElementById('selected_fabric').textContent = value;
                    
                    // Manually close the dropdown
                    setTimeout(() => {
                        const dropdown = document.querySelector('#fabric_dropdown');
                        if (dropdown && dropdown.classList.contains('show')) {
                            dropdown.classList.remove('show');
                            document.getElementById('fabric_display').setAttribute('aria-expanded', 'false');
                        }
                    }, 100);
                });
                
                // Use direct click handler with explicit actions
                fabricItem.querySelector('.fabric-name').addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (!this.classList.contains('editing')) {
                        const value = this.getAttribute('data-value');
                        fabricInput.value = value;
                        document.getElementById('selected_fabric').textContent = value;
                        
                        // Manually close the dropdown
                        setTimeout(() => {
                            const dropdown = document.querySelector('#fabric_dropdown');
                            if (dropdown && dropdown.classList.contains('show')) {
                                dropdown.classList.remove('show');
                                document.getElementById('fabric_display').setAttribute('aria-expanded', 'false');
                            }
                        }, 100);
                    }
                });
                
                fabricItem.querySelector('.edit-btn').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const nameElement = this.closest('.fabric-item').querySelector('.fabric-name');
                    const currentName = nameElement.getAttribute('data-value');
                    enableInlineEdit(nameElement, currentName, 'fabric');
                });
                
                fabricItem.querySelector('.delete-btn').addEventListener('click', function(e) {
                    e.stopPropagation();
                    const fabricToDelete = this.getAttribute('data-fabric');
                    const button = this;
                    const originalHTML = button.innerHTML;
                    
                    // Show loading state on the button
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    
                    // Make AJAX request to delete the fabric
                    $.ajax({
                        url: 'functions/products/manage-fabrics.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            fabric: fabricToDelete
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                // Check if the deleted fabric is currently selected
                                if (fabricInput.value === fabricToDelete) {
                                    // Reset the selection
                                    fabricInput.value = '';
                                    document.getElementById('selected_fabric').textContent = 'Select Fabric';
                                }
                                
                                // Refresh the fabric dropdown
                                initializeFabricDropdown();
                                // Show success message just like categories
                                showBootstrapAlert('.fabric-items-container .alert-container', 'success', response.message);
                            } else {
                                // Show error and restore button - identical to categories
                                showBootstrapAlert('.fabric-items-container .alert-container', 'danger', response.message);
                                button.disabled = false;
                                button.innerHTML = originalHTML;
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.log('Response:', xhr.responseText);
                            showBootstrapAlert('.fabric-items-container .alert-container', 'danger', 'Server error occurred. Please try again.');
                            button.disabled = false;
                            button.innerHTML = originalHTML;
                        }
                    });
                });
            });
        });
    };

    function enableInlineEdit(element, currentValue, type) {
        // Don't allow editing if already in edit mode
        if (element.classList.contains('editing')) return;
        
        element.classList.add('editing');
        
        const originalContent = element.innerHTML;
        
        // Store a reference to the parent element that contains our buttons
        const listItem = element.closest('.' + type + '-item');
        const editBtn = listItem.querySelector('.edit-btn');
        const editBtnParent = editBtn.parentNode; // Store parent reference
        const originalEditBtnHTML = editBtn.innerHTML;
        
        // Replace edit icon with save icon
        editBtn.innerHTML = '<i class="material-symbols-rounded">check</i>';
        editBtn.title = "Save changes";
        
        const deleteBtn = listItem.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.style.display = 'none'; // Hide delete button during edit mode
        }

        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control form-control-sm inline-edit-input';
        input.value = currentValue;
        
        element.innerHTML = '';
        element.appendChild(input);
        
        input.focus();
        input.select();
        
        const dropdownMenu = element.closest('.dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.classList.add('show');
        }
        
        // Create a fresh save handler function
        function saveChanges() {
            // If save was already triggered, bail out
            if (input._saveTriggered) return;
            input._saveTriggered = true;
            
            const newValue = input.value.trim();
            
            // Reset the button back to edit mode first
            editBtn.innerHTML = originalEditBtnHTML;
            editBtn.title = "Rename " + type.charAt(0).toUpperCase() + type.slice(1);
            
            // Create a new button but don't replace it yet - check if parent exists
            const newEditBtn = editBtn.cloneNode(true);
            
            const deleteBtn = listItem.querySelector('.delete-btn');
            if (deleteBtn) {
                deleteBtn.style.display = '';
            }
            
            // Safely handle button replacement
            if (editBtnParent && editBtnParent.contains(editBtn)) {
                try {
                    editBtnParent.replaceChild(newEditBtn, editBtn);
                    
                    // Re-add click handler for edit mode to the new button
                    newEditBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const nameElement = this.closest('.' + type + '-item').querySelector('.' + type + '-name');
                        if (nameElement) {
                            const currentName = nameElement.getAttribute('data-value');
                            enableInlineEdit(nameElement, currentName, type);
                        }
                    });
                } catch (err) {
                    console.warn('Error replacing edit button:', err);
                    // Just restore the original content and avoid further DOM manipulation
                }
            } else {
                // If parent is gone, we can't replace - just restore content
                console.warn('Edit button parent not available - restoring content only');
            }
            
            if (newValue && newValue !== currentValue) {
                // Show loading state in element
                element.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...';
                
                const data = {
                    action: 'rename'
                };
                
                if (type === 'category') {
                    data.old_category = currentValue;
                    data.new_category = newValue;
                } else {
                    data.old_fabric = currentValue;
                    data.new_fabric = newValue;
                }
                
                const url = type === 'category' ? 'functions/products/manage-categories.php' : 'functions/products/manage-fabrics.php';
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            element.innerHTML = newValue;
                            element.setAttribute('data-value', newValue);
                            
                            // Only update button attributes if we successfully replaced the button
                            const currentEditBtn = listItem.querySelector('.edit-btn');
                            if (currentEditBtn) {
                                currentEditBtn.setAttribute('data-' + type, newValue);
                            }
                            
                            const deleteBtn = listItem.querySelector('.delete-btn');
                            if (deleteBtn) {
                                deleteBtn.setAttribute('data-' + type, newValue);
                            }
                            
                            // Update the selected value if it was the current one
                            const categoryInput = document.getElementById('category');
                            const fabricInput = document.getElementById('fabric');
                            
                            if (type === 'category' && categoryInput && categoryInput.value === currentValue) {
                                categoryInput.value = newValue;
                                const selectedCategory = document.getElementById('selected_category');
                                if (selectedCategory) {
                                    selectedCategory.textContent = newValue;
                                }
                                
                                // Update SKU if needed
                                const productName = document.getElementById('name');
                                if (productName && productName.value) {
                                    updateSKU();
                                }
                            } else if (type === 'fabric' && fabricInput && fabricInput.value === currentValue) {
                                fabricInput.value = newValue;
                                const selectedFabric = document.getElementById('selected_fabric');
                                if (selectedFabric) {
                                    selectedFabric.textContent = newValue;
                                }
                            }
                        } else {
                            element.innerHTML = originalContent;
                            showBootstrapAlert(
                                type === 'category' ? '.category-items-container .alert-container' : '.fabric-items-container .alert-container',
                                'danger',
                                response.message || 'Error occurred'
                            );
                        }
                        element.classList.remove('editing');
                    },
                    error: function() {
                        element.innerHTML = originalContent;
                        element.classList.remove('editing');
                        showBootstrapAlert(
                            type === 'category' ? '.category-items-container .alert-container' : '.fabric-items-container .alert-container',
                            'danger',
                            'Server error occurred'
                        );
                    }
                });
            } else {
                element.innerHTML = originalContent;
                element.classList.remove('editing');
            }
        }
        
        // Simplify the cancel function to avoid DOM replacement errors
        function cancelEdit() {
            // Don't try again if already triggered
            if (input._saveTriggered) return;
            input._saveTriggered = true;
            
            // Reset the button back to edit mode
            if (editBtnParent && editBtnParent.contains(editBtn)) {
                editBtn.innerHTML = originalEditBtnHTML;
                editBtn.title = "Rename " + type.charAt(0).toUpperCase() + type.slice(1);
            }
            
            // Just restore original content - don't do complex DOM replacements
            element.innerHTML = originalContent;
            element.classList.remove('editing');
            
            // Re-initialize click handlers on the items
            initializeDropdownClickHandlers();
        }
        
        // Replace with a simpler approach for the edit-save button
        // Just add a click handler directly to the existing button
        editBtn.onclick = function(e) {
            e.stopPropagation();
            saveChanges();
        };
        
        // Handle keyboard events
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveChanges();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEdit();
            }
        });
        
        // Handle focusout event
        input.addEventListener('focusout', function(e) {
            // Check if we're clicking the save button or save already triggered
            if ((editBtn && editBtn.contains(e.relatedTarget)) || input._saveTriggered) {
                return;
            }
            
            // Small delay to let other handlers execute first
            setTimeout(() => {
                if (!input._saveTriggered && element.contains(input)) {
                    saveChanges();
                }
            }, 50);
        });
        
        // Stop propagation on input click
        input.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Helper function to re-initialize click handlers after edits
    function initializeDropdownClickHandlers() {
        // Re-add click handlers to edit buttons
        document.querySelectorAll('.category-item .edit-btn, .fabric-item .edit-btn').forEach(button => {
            button.onclick = function(e) {
                e.stopPropagation();
                const type = this.closest('.category-item') ? 'category' : 'fabric';
                const nameElement = this.closest('.' + type + '-item').querySelector('.' + type + '-name');
                if (nameElement) {
                    const currentName = nameElement.getAttribute('data-value');
                    enableInlineEdit(nameElement, currentName, type);
                }
            };
        });
    }

    function showInlineAddForm(button, type) {
        // Remove any existing forms first
        const existingForms = document.querySelectorAll('.inline-add-form');
        existingForms.forEach(form => form.remove());
        
        // Create inline form element with check and close icons, matching the style of existing items
        const inlineForm = document.createElement('li');
        inlineForm.className = 'dropdown-item inline-add-form';
        inlineForm.innerHTML = `
            <div class="d-flex justify-content-between align-items-center px-3 py-2">
                <span class="${type}-name" style="position: relative;">
                    <input type="text" class="form-control form-control-sm inline-edit-input" 
                           placeholder="Enter new ${type} name" 
                           id="new_${type}_inline" 
                           style="width: 100%;">
                </span>
                <div class="${type}-actions">
                    <button type="button" class="${type}-action-btn save-btn" title="Save ${type}" id="save_${type}_btn">
                        <i class="material-symbols-rounded">check</i>
                    </button>
                    <button type="button" class="${type}-action-btn cancel-btn" title="Cancel" id="cancel_${type}_btn">
                        <i class="material-symbols-rounded">close</i>
                    </button>
                </div>
            </div>
            <div class="text-danger small px-3 mt-1" id="new_${type}_error"></div>
        `;
        
        // Find the "Add New" button's list item
        const addNewBtn = type === 'category' ? 
            document.getElementById('add_new_category') : 
            document.getElementById('add_new_fabric');
        
        if (addNewBtn) {
            // Insert form right before the "Add New" button's list item
            const addNewBtnLi = addNewBtn.closest('li');
            if (addNewBtnLi && addNewBtnLi.parentNode) {
                addNewBtnLi.parentNode.insertBefore(inlineForm, addNewBtnLi);
                
                // Make sure the dropdown stays open
                const dropdownMenu = addNewBtnLi.closest('.dropdown-menu');
                if (dropdownMenu) {
                    // Force dropdown to stay open
                    dropdownMenu.classList.add('show');
                    
                    // Ensure the dropdown toggle button shows as active
                    const dropdownToggleId = type === 'category' ? 'category_display' : 'fabric_display';
                    const dropdownToggle = document.getElementById(dropdownToggleId);
                    if (dropdownToggle) {
                        dropdownToggle.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        }
        
        // Focus the input
        const input = inlineForm.querySelector(`#new_${type}_inline`);
        setTimeout(() => {
            input.focus();
        }, 10);
        
        // Setup event handlers for keyboard actions
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveNewItem(type);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                inlineForm.remove();
            }
        });
        
        // Add event handler for the save button
        const saveBtn = inlineForm.querySelector(`#save_${type}_btn`);
        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                saveNewItem(type);
            });
        }
        
        // Add event handler for the cancel button
        const cancelBtn = inlineForm.querySelector(`#cancel_${type}_btn`);
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                inlineForm.remove();
            }); 
        }
        
        // Prevent dropdown from closing when clicking on form
        inlineForm.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    function saveNewItem(type) {
        const input = document.getElementById(`new_${type}_inline`);
        const errorElem = document.getElementById(`new_${type}_error`);
        const newValue = input.value.trim();
        
        if (!newValue) {
            errorElem.textContent = `Please enter a ${type} name`;
            return;
        }
        
        // Show minimal loading state - just disable the input
        input.disabled = true;
        
        // Fix URL construction to handle "category" -> "categories" pluralization
        const endpoint = type === 'category' ? 'functions/products/manage-categories.php' : `functions/products/manage-${type}s.php`;
        
        $.ajax({
            url: endpoint,
            type: 'POST',
            data: {
                action: 'add',
                [`new_${type}`]: newValue
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // No visual feedback except for the dropdown refreshing
                    if (type === 'category') {
                        initializeCategoryDropdown();
                        
                        setTimeout(() => {
                            categoryInput.value = newValue;
                            document.getElementById('selected_category').textContent = newValue;
                            
                            const productName = document.getElementById('name').value;
                            if (productName) {
                                updateSKU();
                            }
                            
                            // Show success message only for categories
                            setTimeout(() => {
                                showBootstrapAlert('.category-items-container .alert-container', 'success', `${type} added successfully`);
                            }, 400);
                        }, 300);
                    } else {
                        initializeFabricDropdown();
                        
                        setTimeout(() => {
                            fabricInput.value = newValue;
                            document.getElementById('selected_fabric').textContent = newValue;
                            
                            // Add success message for fabrics (matching category)
                            setTimeout(() => {
                                showBootstrapAlert('.fabric-items-container .alert-container', 'success', `${type} added successfully`);
                            }, 400);
                        }, 300);
                    }
                    
                    // Simply remove the form on success
                    document.querySelector('.inline-add-form').remove();
                } else {
                    errorElem.textContent = response.message || `Failed to add ${type}`;
                    input.disabled = false;
                }
            },
            error: function(xhr, status, errorThrown) {
                console.error(`AJAX Error when adding ${type}:`, status, errorThrown);
                console.log("Server Response:", xhr.responseText);
                
                try {
                    // Try to parse the response as JSON to get detailed error message
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse && errorResponse.message) {
                        errorElem.textContent = errorResponse.message;
                    } else {
                        errorElem.textContent = `Server error: ${status}`;
                    }
                } catch (e) {
                    // If not valid JSON, show the raw response or a generic message
                    if (xhr.responseText) {
                        errorElem.textContent = `Server error: ${xhr.responseText.substring(0, 50)}...`;
                    } else {
                        errorElem.textContent = `Server error occurred. Check console for details.`;
                    }
                }
                
                input.disabled = false;
            }
        });
    }

    // Function to show Bootstrap alerts
    function showBootstrapAlert(containerSelector, type, message) {
        const container = document.querySelector(containerSelector);
        if (!container) return;
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} fade show my-2 p-2`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = message;
        
        container.innerHTML = '';
        container.appendChild(alert);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => {
                if (container.contains(alert)) {
                    container.removeChild(alert);
                }
            }, 150);
        }, 3000);

        // Make sure dropdown stays open
        const dropdown = container.closest('.dropdown-menu');
        if (dropdown) {
            dropdown.classList.add('show');
        }
    }

    // Set up price calculation
    const originalPriceInput = document.getElementById('original_price');
    const discountPriceInput = document.getElementById('discount_price');
    const originalPriceDisplay = document.getElementById('original_price_display');
    const finalPriceDisplay = document.getElementById('final_price_display');
    const discountText = document.getElementById('discount_text');
    const savingsContainer = document.getElementById('savings_container');
    const savingsAmount = document.getElementById('savings_amount');
    
    function updatePricePreview() {
        const originalPrice = parseFloat(originalPriceInput.value) || 0;
        let discountPrice = parseFloat(discountPriceInput.value) || originalPrice;
        
        // Don't allow discount price to be higher than original price
        if (discountPrice > originalPrice) {
            discountPrice = originalPrice;
            discountPriceInput.value = originalPrice;
        }
        
        // Calculate discount percentage for display only
        let discountPercentage = 0;
        if (originalPrice > 0 && discountPrice < originalPrice) {
            discountPercentage = Math.round(((originalPrice - discountPrice) / originalPrice) * 100);
        }
        
        // Update display
        originalPriceDisplay.textContent = `₱${originalPrice.toFixed(2)}`;
        finalPriceDisplay.textContent = `₱${discountPrice.toFixed(2)}`;
        
        if (discountPercentage > 0) {
            discountText.textContent = `(${discountPercentage}% off)`;
            savingsContainer.classList.remove('d-none');
            savingsAmount.textContent = `₱${(originalPrice - discountPrice).toFixed(2)}`;
        } else {
            discountText.textContent = '';
            savingsContainer.classList.add('d-none');
        }
    }
    
    originalPriceInput.addEventListener('input', updatePricePreview);
    discountPriceInput.addEventListener('input', updatePricePreview);

    // Add arrow key navigation for size inputs
    function setupSizeInputNavigation() {
        const sizeInputs = document.querySelectorAll('.size-stock-grid input[type="number"]');
        const sizeArray = Array.from(sizeInputs);
        
        sizeArray.forEach((input, index) => {
            input.addEventListener('keydown', function(e) {
                // Right arrow key - move to next input
                if (e.key === 'ArrowRight' && index < sizeArray.length - 1) {
                    e.preventDefault();
                    sizeArray[index + 1].focus();
                }
                
                // Left arrow key - move to previous input
                if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault();
                    sizeArray[index - 1].focus();
                }
            });
            
            // Position cursor at the end of the input value when focused
            input.addEventListener('focus', function(e) {
                // Move cursor to the end by temporarily storing the value and reassigning it
                const val = this.value;
                this.value = '';
                this.value = val;
            });
        });
    }
    
    // Call the function to set up navigation
    setupSizeInputNavigation();
    
    // Initialize dropdowns when document is ready
    initializeCategoryDropdown();
    initializeFabricDropdown();
    
    // Set up form handler to track deleted images
    const form = document.querySelector('form[action="functions/products/code.php"]');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (tempDeletedImages && tempDeletedImages.length > 0) {
                document.getElementById('deleted_images').value = tempDeletedImages.join(',');
                console.log('Images marked for deletion on submit:', tempDeletedImages);
            }
        });
    }
});

// Make these functions global so they can be called from HTML
window.removePrimaryImage = removePrimaryImage;
window.removeAdditionalImage = removeAdditionalImage;
window.removeExistingImage = removeExistingImage;