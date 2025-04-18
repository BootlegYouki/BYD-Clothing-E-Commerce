document.addEventListener('DOMContentLoaded', function() {
    // Get all sidebar buttons
    const sidebarButtons = document.querySelectorAll('.sidebar-btn');
    
    // Add click event to each button
    sidebarButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            sidebarButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get the page to load from data-page attribute
            const pageToLoad = this.getAttribute('data-page');
            
            // Load the content
            loadContent(pageToLoad);
        });
    });
    
    // Function to load content via AJAX
    function loadContent(page) {
        fetch(page)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                document.querySelector('.content').innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading page:', error);
                document.querySelector('.content').innerHTML = '<div class="p-4">Error loading content. Please try again.</div>';
            });
    }
    
    // Load default content if no hash in URL
    if (!window.location.hash) {
        // Get the active button's data-page
        const activeButton = document.querySelector('.sidebar-btn.active');
        if (activeButton) {
            loadContent(activeButton.getAttribute('data-page'));
        }
    }
});