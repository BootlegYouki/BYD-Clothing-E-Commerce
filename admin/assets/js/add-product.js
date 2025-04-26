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
    
    if (files.length > 4) {
        errorContainer.textContent = 'You can only upload up to 4 additional images.';
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
    
    // Only update SKU if both name and category are provided
    if (productName && category) {
        const sku = generateSKU_JS(productName, category);
        document.getElementById('sku').value = sku;
    }
}

document.addEventListener('DOMContentLoaded', function() {
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