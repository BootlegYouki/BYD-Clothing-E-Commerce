document.addEventListener('DOMContentLoaded', function() {
    // File upload preview functionality
    const primaryImageInput = document.getElementById('primary_image');
    const primaryImageBtn = document.getElementById('primary_image_btn');
    const primaryImageText = document.getElementById('primary_image_text');
    const primaryImagePreview = document.getElementById('primary_image_preview');
    const errorMessage = document.querySelector('.error-message');
    
    if (primaryImageBtn) {
        primaryImageBtn.addEventListener('click', function() {
            primaryImageInput.click();
        });
        
        primaryImageInput.addEventListener('change', function() {
            primaryImageText.value = this.files.length > 0 ? 
                (this.files.length === 1 ? this.files[0].name : this.files.length + ' files selected') : 
                'No files selected';
            
            // Clear previous previews
            primaryImagePreview.innerHTML = '';
            errorMessage.textContent = '';
            
            // Check file size
            let totalSize = 0;
            for (let i = 0; i < this.files.length; i++) {
                totalSize += this.files[i].size;
                
                // Create preview for each image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail mt-2 me-2';
                    img.style.height = '100px';
                    primaryImagePreview.appendChild(img);
                }
                reader.readAsDataURL(this.files[i]);
            }
            
            // Show warning if total size is over 2MB
            if (totalSize > 2 * 1024 * 1024) {
                errorMessage.textContent = 'Warning: Total file size exceeds 2MB. The upload might be slow.';
            }
        });
    }
});