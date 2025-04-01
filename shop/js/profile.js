document.addEventListener("DOMContentLoaded", function () {
    // Select all buttons with "sidebar-btn" class
    document.querySelectorAll(".sidebar-btn").forEach(button => {
        button.addEventListener("click", function () {
            let page = this.getAttribute("data-page"); // Get PHP file path
            loadContent(page); // Call function to load content

            // Remove active class from all buttons, then add to the clicked one
            document.querySelectorAll(".sidebar-btn").forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
        });
    });

    // Handle Purchases <span> click (since it's not a button)
    document.querySelector("[data-page='includes/order_track.php']").addEventListener("click", function () {
        loadContent("includes/order_track.php");
    });

    // Function to load content dynamically
    function loadContent(page) {
        fetch(page) // Fetch PHP file
            .then(response => response.text()) // Convert response to text
            .then(data => {
                document.querySelector(".content").innerHTML = data; // Update content
            })
            .catch(error => console.error("Error loading content:", error));
    }
});


