document.addEventListener("DOMContentLoaded", function () {
    const sizeSelector = document.getElementById("sizeSelector");
    const priceElement = document.getElementById("price1");

    // Define price mapping for different sizes
    const priceMapping = {
        "Small": 399,
        "Medium": 449,
        "Large": 499,
        "XL": 499,
        "XXL": 549,
        "XXXL": 599
    };

    // Function to update price 
    function updatePrice() {
        const selectedSize = sizeSelector.options[sizeSelector.selectedIndex].text; // Get selected size text
        let selectedPrice = priceMapping[selectedSize] || 399; // Default price if not found
        priceElement.textContent = `₱${selectedPrice}`; // Update the price display
        console.log(`Selected Size: ${selectedSize}, Price: ₱${selectedPrice}`);
    }

    // Event listener for size change
    sizeSelector.addEventListener("change", updatePrice);

    // Set initial price on page load
    updatePrice();
});


document.addEventListener("DOMContentLoaded", function () {
    const reviewToggle = document.querySelector(".review-toggle");
    const reviewContent = document.querySelector(".review-content");
    const starRatingDisplay = document.getElementById("average-rating");
    const reviews = document.querySelectorAll(".review");

    // Calculate average rating
    let totalRating = 0;
    let totalReviews = reviews.length;

    reviews.forEach(review => {
        totalRating += parseInt(review.getAttribute("data-rating"));
    });

    let averageRating = (totalRating / totalReviews).toFixed(1);

    // Generate stars dynamically
    function getStars(rating) {
        let fullStars = Math.floor(rating);
        let halfStar = rating % 1 !== 0 ? "⭐" : "";
        return "⭐".repeat(fullStars) + halfStar + "✩".repeat(5 - fullStars - halfStar.length);
    }

    starRatingDisplay.innerHTML = `${getStars(averageRating)} (${averageRating}/5)`;

    // Toggle review section
    reviewToggle.addEventListener("click", function () {
        if (reviewContent.style.display === "none") {
            reviewContent.style.display = "block";
            reviewToggle.innerText = "Hide Reviews ⬆";
        } else {
            reviewContent.style.display = "none";
            reviewToggle.innerText = "View Reviews ⬇";
        }
    });
});

/*RECOMMENDED*/
document.addEventListener("DOMContentLoaded", function () {
    var showMoreBtn = document.getElementById("showMoreBtn");
    var hiddenProducts = document.querySelectorAll(".more-products");
    var isExpanded = false;

    showMoreBtn.addEventListener("click", function () {
        if (!isExpanded) {
            hiddenProducts.forEach(product => product.classList.remove("d-none"));
            showMoreBtn.textContent = "Show Less";
            isExpanded = true;
        } else {
            hiddenProducts.forEach(product => product.classList.add("d-none"));
            showMoreBtn.textContent = "Show More";
            isExpanded = false;
        }
    });
});