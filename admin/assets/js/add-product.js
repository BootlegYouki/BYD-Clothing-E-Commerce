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

document.getElementById('primary_image').addEventListener('change', async function() {
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
        document.getElementById('primary_image_text').value = 'Converting image...';
        
        try {
            // Convert images to WebP
            const webpFiles = await convertMultipleImagesToWebP(files);
            this.files = webpFiles;
            
            document.getElementById('primary_image_text').value = webpFiles.length + ' file selected (WebP)';
            
            for (let i = 0; i < webpFiles.length; i++) {
                const file = webpFiles[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewHtml += `
                        <div style="display:inline-block; margin-right:5px; text-align: center; position: relative;">
                            <img src="${e.target.result}" style="max-width: 150px; max-height: 150px; border-radius: 0.5rem;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                                   style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                                   onclick="event.stopPropagation(); removePrimaryImage(${i});"
                                   title="Remove image">
                                <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                            </button>
                            <small class="d-block mt-1 text-success">Converted to WebP</small>
                        </div>
                    `;
                    document.getElementById('primary_image_preview').innerHTML = previewHtml;
                }
                reader.readAsDataURL(file);
            }
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

function removePrimaryImage(index, e) {
    // If an event is passed, stop propagation
    if (e) e.stopPropagation();

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

// Additional images handling
let additionalFilesCollection = [];

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
    let previewHtml = '';
    const errorContainer = this.parentNode.querySelector('.error-message');
    errorContainer.textContent = ''; // Clear previous error
    
    // Count how many files will be in total after this upload
    const totalFilesAfterUpload = additionalFilesCollection.length + files.length;
    
    if (totalFilesAfterUpload > 4) {
        errorContainer.textContent = `You can only upload up to 4 additional images. You already have ${additionalFilesCollection.length}.`;
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
            
            // Generate preview HTML for all files
            previewHtml = '';
            for (let i = 0; i < additionalFilesCollection.length; i++) {
                const file = additionalFilesCollection[i];
                const reader = new FileReader();
                
                // Use a closure to preserve the index value
                (function(index) {
                    reader.onload = function(e) {
                        const newPreviewItem = `
                            <div id="preview_${index}" style="display:inline-block; margin-right:5px; text-align: center; position: relative;">
                                <img src="${e.target.result}" style="max-width: 150px; max-height: 150px; border-radius: 0.5rem;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                                       style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                                       onclick="event.stopPropagation(); removeAdditionalImage(${index});"
                                       title="Remove image">
                                    <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                                </button>
                                <small class="d-block mt-1 text-success">Converted to WebP</small>
                            </div>
                        `;
                        
                        // Append to the preview area
                        document.getElementById('additional_images_preview').insertAdjacentHTML('beforeend', newPreviewItem);
                    };
                    reader.readAsDataURL(file);
                })(i);
            }
            
            // Clear existing preview content
            document.getElementById('additional_images_preview').innerHTML = '';
            
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

// Update the removeAdditionalImage function to remove from our collection
function removeAdditionalImage(index, e) {
    // If an event is passed, stop propagation
    if (e) e.stopPropagation();
    
    // Remove the specific file from our collection
    additionalFilesCollection.splice(index, 1);
    
    // Rebuild the DataTransfer object
    const dt = new DataTransfer();
    for (let i = 0; i < additionalFilesCollection.length; i++) {
        dt.items.add(additionalFilesCollection[i]);
    }
    
    // Update the file input
    const input = document.getElementById('additional_images');
    input.files = dt.files;
    
    // Update the text display
    document.getElementById('additional_images_text').value = 
        additionalFilesCollection.length > 0 ? additionalFilesCollection.length + ' files selected (WebP)' : 'No files selected';
    
    // Remove the preview element
    const previewElement = document.getElementById(`preview_${index}`);
    if (previewElement) previewElement.remove();
    
    // Rebuild all preview elements with updated indices
    document.getElementById('additional_images_preview').innerHTML = '';
    
    for (let i = 0; i < additionalFilesCollection.length; i++) {
        const file = additionalFilesCollection[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const newPreviewItem = `
                <div id="preview_${i}" style="display:inline-block; margin-right:5px; text-align: center; position: relative;">
                    <img src="${e.target.result}" style="max-width: 150px; max-height: 150px; border-radius: 0.5rem;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                           style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                           onclick="event.stopPropagation(); removeAdditionalImage(${i});"
                           title="Remove image">
                        <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                    </button>
                    <small class="d-block mt-1 text-success">Converted to WebP</small>
                </div>
            `;
            
            // Append to the preview area
            document.getElementById('additional_images_preview').insertAdjacentHTML('beforeend', newPreviewItem);
        };
        reader.readAsDataURL(file);
    }
}

// SKU generation and category/fabric management
document.addEventListener('DOMContentLoaded', function() {
    // Existing code for product name and SKU generation
    const productNameInput = document.getElementById('name');
    const categorySelect = document.getElementById('category');
    const skuInput = document.getElementById('sku');
    const fabricSelect = document.getElementById('fabric');

    let lastSelectedCategory = '';
    let lastSelectedFabric = '';

    function updateSKU() {
        if (productNameInput.value && categorySelect.value && 
            !['add_new_category', 'rename_category', 'delete_category'].includes(categorySelect.value)) {
            
            lastSelectedCategory = categorySelect.value;
            
            fetch('functions/generate_sku.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `name=${encodeURIComponent(productNameInput.value)}&category=${encodeURIComponent(categorySelect.value)}`
            })
            .then(response => response.text())
            .then(sku => {
                skuInput.value = sku;
            })
            .catch(error => console.error('Error:', error));
        }
    }

    productNameInput.addEventListener('blur', updateSKU);
    
    // Handle category dropdown special options
    categorySelect.addEventListener('change', function() {
        if (this.value === 'add_new_category') {
            showInlineAddForm(this, 'category');
        } 
        else if (this.value === 'rename_category') {
            const renameCategoryModal = new bootstrap.Modal(document.getElementById('renameCategoryModal'));
            renameCategoryModal.show();
            setTimeout(() => {
                this.value = lastSelectedCategory || '';
            }, 100);
        } 
        else if (this.value === 'delete_category') {
            const removeCategoryModal = new bootstrap.Modal(document.getElementById('removeCategoryModal'));
            removeCategoryModal.show();
            setTimeout(() => {
                this.value = lastSelectedCategory || '';
            }, 100);
        } 
        else {
            lastSelectedCategory = this.value;
            updateSKU();
        }
    });
    
    // Handle fabric dropdown special options
    fabricSelect.addEventListener('change', function() {
        if (this.value === 'add_new_fabric') {
            showInlineAddForm(this, 'fabric');
        } 
        else if (this.value === 'rename_fabric') {
            const renameFabricModal = new bootstrap.Modal(document.getElementById('renameFabricModal'));
            renameFabricModal.show();
            setTimeout(() => {
                this.value = lastSelectedFabric || '';
            }, 100);
        } 
        else if (this.value === 'delete_fabric') {
            const removeFabricModal = new bootstrap.Modal(document.getElementById('removeFabricModal'));
            removeFabricModal.show();
            setTimeout(() => {
                this.value = lastSelectedFabric || '';
            }, 100);
        }
        else {
            lastSelectedFabric = this.value;
        }
    });

    // Helper function to update dropdown options
    function updateDropdownOptions(selectElement, oldValue, newValue) {
        for (let i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === oldValue) {
                selectElement.options[i].value = newValue;
                selectElement.options[i].textContent = newValue;
                break;
            }
        }
    }

    function fetchCategories() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'functions/get-categories.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        console.log('Categories loaded:', response.categories); // Debug log
                        resolve(response.categories);
                    } else {
                        console.error('Error fetching categories:', response.message);
                        resolve([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading categories:', error);
                    console.log('Response:', xhr.responseText); // Debug log
                    resolve([]);
                }
            });
        });
    }

    function fetchFabrics() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'functions/get-fabrics.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        console.log('Fabrics loaded:', response.fabrics); // Debug log
                        resolve(response.fabrics);
                    } else {
                        console.error('Error fetching fabrics:', response.message);
                        resolve([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading fabrics:', error);
                    console.log('Response:', xhr.responseText); // Debug log
                    resolve([]);
                }
            });
        });
    }

    initializeCategoryDropdown = function() {
        const categoryContainer = document.querySelector('.category-items-container');
        const categoryInput = document.getElementById('category');
        const categoryDisplay = document.getElementById('selected_category');
        
        categoryContainer.innerHTML = '<li class="px-3 py-2 text-center text-muted">Loading categories...</li>';
        
        fetchCategories().then(categories => {
            categoryContainer.innerHTML = `
                <li class="alert-container px-3"></li>
            `;
            
            if (!categories || categories.length === 0) {
                categoryContainer.innerHTML += '<li class="px-3 py-2 text-center text-muted">No categories found</li>';
                return;
            }
            
            categories.forEach(category => {
                const categoryItem = document.createElement('li');
                categoryItem.className = 'category-item';
                categoryItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="category-name" data-value="${category}">${category}</span>
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
                
                categoryItem.querySelector('.category-name').addEventListener('click', function() {
                    if (!this.classList.contains('editing')) {
                        const value = this.getAttribute('data-value');
                        categoryInput.value = value;
                        categoryDisplay.textContent = value;
                        
                        const productName = document.getElementById('name').value;
                        if (productName) {
                            updateSKU(productName, value);
                        }
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
                        url: 'functions/manage-categories.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            category: categoryToDelete
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
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
        
        document.getElementById('add_new_category').addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            showInlineAddForm(this, 'category');
        });
    };

    initializeFabricDropdown = function() {
        const fabricContainer = document.querySelector('.fabric-items-container');
        const fabricInput = document.getElementById('fabric');
        const fabricDisplay = document.getElementById('selected_fabric');
        
        fabricContainer.innerHTML = '<li class="px-3 py-2 text-center text-muted">Loading fabrics...</li>';
        
        fetchFabrics().then(fabrics => {
            fabricContainer.innerHTML = `
                <li class="alert-container px-3"></li>
            `;
            
            if (!fabrics || fabrics.length === 0) {
                fabricContainer.innerHTML += '<li class="px-3 py-2 text-center text-muted">No fabrics found</li>';
                return;
            }
            
            fabrics.forEach(fabric => {
                const fabricItem = document.createElement('li');
                fabricItem.className = 'fabric-item';
                fabricItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="fabric-name" data-value="${fabric}">${fabric}</span>
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
                
                fabricItem.querySelector('.fabric-name').addEventListener('click', function() {
                    if (!this.classList.contains('editing')) {
                        const value = this.getAttribute('data-value');
                        fabricInput.value = value;
                        fabricDisplay.textContent = value;
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
                        url: 'functions/manage-fabrics.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            fabric: fabricToDelete
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
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
        
        document.getElementById('add_new_fabric').addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            showInlineAddForm(this, 'fabric');
        });
    };

    function enableInlineEdit(element, currentValue, type) {
        element.classList.add('editing');
        
        const originalContent = element.innerHTML;
        let isSaving = false; // Flag to prevent multiple save operations
        
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
        
        function saveChanges() {
            if (isSaving) return; // Prevent multiple save operations
            isSaving = true;
            
            const newValue = input.value.trim();
            
            if (newValue && newValue !== currentValue) {
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
                
                const url = type === 'category' ? 'functions/manage-categories.php' : 'functions/manage-fabrics.php';
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            element.innerHTML = newValue;
                            element.setAttribute('data-value', newValue);
                            
                            const editBtn = element.closest('.' + type + '-item').querySelector('.edit-btn');
                            if (editBtn) {
                                editBtn.setAttribute('data-' + type, newValue);
                            }
                            
                            const deleteBtn = element.closest('.' + type + '-item').querySelector('.delete-btn');
                            if (deleteBtn) {
                                deleteBtn.setAttribute('data-' + type, newValue);
                            }
                            
                            // Show success message only for category operations
                            if (type === 'category') {
                                showBootstrapAlert('.category-items-container .alert-container', 'success', 'Renamed successfully');
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
        
        function cancelEdit() {
            if (isSaving) return; // Prevent canceling during save
            isSaving = true;
            element.innerHTML = originalContent;
            element.classList.remove('editing');
        }
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveChanges();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEdit();
            }
        });
        
        input.addEventListener('focusout', function() {
            if (!isSaving) {
                saveChanges();
            }
        });
        
        input.addEventListener('click', function(e) {
            e.stopPropagation();
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
                    const dropdownToggle = document.querySelector(`[data-bs-toggle="dropdown"][aria-expanded="true"]`);
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
        
        // Remove blur handler to avoid conflicts with button clicks
        // We'll rely on the explicit save/cancel buttons instead
        
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
        const endpoint = type === 'category' ? 'functions/manage-categories.php' : `functions/manage-${type}s.php`;
        
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
                            const categoryInput = document.getElementById('category');
                            const categoryDisplay = document.getElementById('selected_category');
                            categoryInput.value = newValue;
                            categoryDisplay.textContent = newValue;
                            
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
                            const fabricInput = document.getElementById('fabric');
                            const fabricDisplay = document.getElementById('selected_fabric');
                            fabricInput.value = newValue;
                            fabricDisplay.textContent = newValue;
                            
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

    initializeCategoryDropdown();
    initializeFabricDropdown();

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
});

// Pricing calculator and dropdown initialization
let openRenameCategoryModal, openDeleteCategoryModal, openRenameFabricModal, openDeleteFabricModal;

document.addEventListener('DOMContentLoaded', function() {
    const originalPriceInput = document.getElementById('original_price');
    const discountPercentageInput = document.getElementById('discount_percentage');
    const originalPriceDisplay = document.getElementById('original_price_display');
    const finalPriceDisplay = document.getElementById('final_price_display');
    const discountText = document.getElementById('discount_text');
    const savingsContainer = document.getElementById('savings_container');
    const savingsAmount = document.getElementById('savings_amount');
    const presetButtons = document.querySelectorAll('.discount-preset-btn');
    
    function updatePricePreview() {
        const originalPrice = parseFloat(originalPriceInput.value) || 0;
        const discountPercentage = parseInt(discountPercentageInput.value) || 0;
        
        if (discountPercentage < 0) discountPercentageInput.value = 0;
        if (discountPercentage > 100) discountPercentageInput.value = 100;
        
        originalPriceDisplay.textContent = `₱${originalPrice.toFixed(2)}`;
        
        const finalPrice = originalPrice * (1 - discountPercentage / 100);
        finalPriceDisplay.textContent = `₱${finalPrice.toFixed(2)}`;
        
        if (discountPercentage > 0) {
            discountText.textContent = `(${discountPercentage}% off)`;
            savingsContainer.classList.remove('d-none');
            savingsAmount.textContent = `₱${(originalPrice - finalPrice).toFixed(2)}`;
        } else {
            discountText.textContent = '';
            savingsContainer.classList.add('d-none');
        }
    }
    
    originalPriceInput.addEventListener('input', updatePricePreview);
    discountPercentageInput.addEventListener('input', updatePricePreview);
    
    presetButtons.forEach(button => {
        button.addEventListener('click', function() {
            const discountValue = parseInt(this.dataset.value);
            discountPercentageInput.value = discountValue;
            updatePricePreview();
        });
    });
    
    updatePricePreview();

    // Define empty functions to prevent errors in case they're called elsewhere
    openDeleteCategoryModal = function() {};
    openDeleteFabricModal = function() {};
});