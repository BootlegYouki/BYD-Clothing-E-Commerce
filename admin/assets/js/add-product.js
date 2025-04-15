// Primary image handling
document.getElementById('primary_image_btn').addEventListener('click', function() {
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
                                   onclick="removePrimaryImage(${i})"
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

// Additional images handling
document.getElementById('additional_images_btn').addEventListener('click', function() {
    document.getElementById('additional_images').click();
});

document.getElementById('additional_images').addEventListener('change', async function() {
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
        document.getElementById('additional_images_text').value = 'Converting images...';
        
        try {
            // Convert images to WebP
            const webpFiles = await convertMultipleImagesToWebP(files);
            this.files = webpFiles;
            
            document.getElementById('additional_images_text').value = webpFiles.length + ' files selected (WebP)';
            
            for (let i = 0; i < webpFiles.length; i++) {
                const file = webpFiles[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewHtml += `
                        <div style="display:inline-block; margin-right:5px; text-align: center; position: relative;">
                            <img src="${e.target.result}" style="max-width: 150px; max-height: 150px; border-radius: 0.5rem;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 rounded-circle d-flex align-items-center justify-content-center" 
                                   style="width: 24px; height: 24px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                                   onclick="removeAdditionalImage(${i})"
                                   title="Remove image">
                                <i class="material-symbols-rounded" style="font-size: 16px; margin: 0; padding: 0; line-height: 1;">close</i>
                            </button>
                            <small class="d-block mt-1 text-success">Converted to WebP</small>
                        </div>
                    `;
                    document.getElementById('additional_images_preview').innerHTML = previewHtml;
                }
                reader.readAsDataURL(file);
            }
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

// SKU generation
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

// Fabric dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const newCategoryContainer = document.getElementById('new_category_container');
    const newCategoryInput = document.getElementById('new_category');
    const addCategoryBtn = document.getElementById('add_category_btn');
    const removeCategoryContainer = document.getElementById('remove_category_container');
    const removeCategorySelect = document.getElementById('remove_category');
    const removeCategoryBtn = document.getElementById('remove_category_btn');
    
    if(categorySelect) {
        categorySelect.addEventListener('change', function() {
            if(this.value === 'new') {
                newCategoryContainer.style.display = 'block';
                removeCategoryContainer.style.display = 'none';
                // Don't add required here if it causes validation issues
                // newCategoryInput.setAttribute('required', 'required');
                newCategoryInput.focus();
            } else if(this.value === 'remove') {
                newCategoryContainer.style.display = 'none';
                removeCategoryContainer.style.display = 'block';
                // Make sure to remove required when hiding
                if(newCategoryInput.hasAttribute('required')) {
                    newCategoryInput.removeAttribute('required');
                }
                newCategoryInput.value = '';
            } else {
                newCategoryContainer.style.display = 'none';
                removeCategoryContainer.style.display = 'none';
                // Make sure to remove required when hiding
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
            
            // Update SKU based on new category
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
            .then(response => response.json())
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
                msgContainer.innerHTML = '<div class="alert alert-danger">An error occurred while removing the category</div>';
            });
        });
    }

    // Fabric dropdown functionality
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
                newFabricInput.focus();
            } else if(this.value === 'remove') {
                newFabricContainer.style.display = 'none';
                removeFabricContainer.style.display = 'block';
                // Make sure to remove required when hiding
                if(newFabricInput.hasAttribute('required')) {
                    newFabricInput.removeAttribute('required');
                }
                newFabricInput.value = '';
            } else {
                newFabricContainer.style.display = 'none';
                removeFabricContainer.style.display = 'none';
                // Make sure to remove required when hiding
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
            .then(response => response.json())
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
                msgContainer.innerHTML = '<div class="alert alert-danger">An error occurred while removing the fabric</div>';
            });
        });
    }

    // Set page title based on current page
    // Get the current page from PHP
    const currentPagePath = window.location.pathname;
    const currentPage = currentPagePath.split('/').pop().replace('.php', '');
    
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
    
    // Handle alert auto-dismiss
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(function() {
            successAlert.classList.remove('show');
            setTimeout(function() {
                if (successAlert) successAlert.remove();
            }, 150);
        }, 3000);
    }
    const productForm = document.querySelector('form[action="functions/code.php"]');
    if (productForm) {
        productForm.addEventListener('submit', function(event) {
            // Remove required attribute from all inputs in hidden containers
            const hiddenContainers = document.querySelectorAll('[style*="display: none"]');
            hiddenContainers.forEach(container => {
                const requiredInputs = container.querySelectorAll('[required]');
                requiredInputs.forEach(input => {
                    input.removeAttribute('required');
                });
            });
        });
    }
});