
// Sample Orders
const orders = {
    all: [
        { id: 12345, seller: "REGALHOME Store", status: "To Pay", buttonText: "Payment Pending", disabled: true },
        { id: 12346, seller: "SPORTS HUB", status: "To Ship", buttonText: "Parcel to be Delivered", disabled: true },
        { id: 12347, seller: "FASHION TREND", status: "To Receive", buttonText: "Order Received", disabled: false },
        { id: 12348, seller: "TECH STORE", status: "Completed", buttonText: "Buy Again", disabled: false },
        { id: 12349, seller: "GADGET WORLD", status: "To Review", buttonText: "To Rate", disabled: false }
    ],
    to_pay: [{ id: 12345, seller: "REGALHOME Store", status: "To Pay", buttonText: "Payment Pending", disabled: true }],
    to_ship: [{ id: 12346, seller: "SPORTS HUB", status: "To Ship", buttonText: "Parcel to be Delivered", disabled: true }],
    to_receive: [{ id: 12347, seller: "FASHION TREND", status: "To Receive", buttonText: "Order Received", disabled: false }],
    completed: [{ id: 12348, seller: "TECH STORE", status: "Completed", buttonText: "Buy Again", disabled: false }],
    to_review: [{ id: 12349, seller: "GADGET WORLD", status: "To Review", buttonText: "To Rate", disabled: false }]
};

// Function to load orders dynamically based on the tab clicked
function loadOrders(status) {
    const container = document.getElementById("ordersContainer");
    const activeTab = document.querySelector(`#orderTabs .nav-link.active`).dataset.tab;
    let filteredOrders = orders[status];

    // Clear current orders
    container.innerHTML = "";

    // Loop through the orders and load them dynamically based on the selected status
    filteredOrders.forEach(order => {
        const orderHTML = `
            <div class="order-item mb-4 p-3 border rounded">
                <div class="d-flex justify-content-between">
                    <div>Order ID: ${order.id}</div>
                    <div>Status: ${order.status}</div>
                </div>
               <div class="d-flex mt-3">
    <img src="img/t-shirt/brook.webp" alt="Product Image" class="my-2 me-3" style="width: 100px; height: 100px;">
    <div>
        <p class="mb-0"><strong>Product Name</strong></p>
        <p class="mb-0">Size: XXL</p>
        <p class="mb-0">Quantity: x1</p>
        <p class="mb-0 order-total">₱780</p>
        <p class="mb-0">Order Total: ₱780</p>
    </div>
    </div>
                <div class="mt-3">
                  <button id="btn-dynamic" class="btn btn-primary order-action mb-1 position-relative move-up" data-id="${order.id}" ${order.disabled ? "disabled" : ""}>${order.buttonText}</button>
                  <button id="btn-contact-seller" class="btn btn-info mb-1 position-relative move-up" id="contactSellerBtn" >Contact Seller</button>

                </div>
                </div>
               
            </div>
        `;
        container.innerHTML += orderHTML;
    });

    // Add event listeners for order actions (e.g., "Order Received")
    document.querySelectorAll(".order-action").forEach(button => {
        button.addEventListener("click", function () {
            let orderId = this.getAttribute("data-id");

            if (this.textContent === "Order Received") {
                $("#orderReceivedModal").modal("show");

                // Handle "Confirm Received" action in the modal
                document.getElementById("confirmReceivedBtn").onclick = function () {
                    // Move the order from 'To Receive' to 'Completed'
                    orders.to_receive = orders.to_receive.filter(o => o.id != orderId);
                    orders.completed.push({ id: orderId, seller: "FASHION TREND", status: "Completed", buttonText: "Buy Again", disabled: false });

                    // Only update the active tab
                    if (activeTab === "to_receive") {
                        loadOrders("to_receive");  // Reload "To Receive" tab only
                    } else if (activeTab === "completed") {
                        loadOrders("completed");  // Reload "Completed" tab only
                    }

                    // Close the modal
                    $("#orderReceivedModal").modal("hide");
                };
            } else if (this.textContent === "To Rate") {
                $("#rateProductModal").modal("show");
            }
        });
    });
}

// Tab Clicks
document.querySelectorAll("#orderTabs .nav-link").forEach(tab => {
    tab.addEventListener("click", function () {
        document.querySelectorAll("#orderTabs .nav-link").forEach(el => el.classList.remove("active"));
        this.classList.add("active");
        loadOrders(this.dataset.tab);  // Load orders for the clicked tab
    });
});

// Load the default tab (All Orders)
loadOrders("all");


// Function to close the modal
function closeModal(modalId) {
    // Use Bootstrap's modal hide method to close the modal
    $('#' + modalId).modal('hide');
}

// Add event listeners to buttons to close the modal when clicked
document.addEventListener("DOMContentLoaded", function() {
    // Close modal when 'X' button is clicked in "Order Received Modal"
    const closeModalButton = document.querySelector("#orderReceivedModal .close");
    if (closeModalButton) {
        closeModalButton.addEventListener("click", function() {
            closeModal("orderReceivedModal");
        });
    }

    // Close modal when 'Not Now' button is clicked in "Order Received Modal"
    const notNowButton = document.querySelector("#orderReceivedModal .btn-secondary");
    if (notNowButton) {
        notNowButton.addEventListener("click", function() {
            closeModal("orderReceivedModal");
        });
    }

    // Close modal when 'Cancel' button is clicked in "Rate Product Modal"
    const cancelButton = document.querySelector("#rateProductModal .btn-secondary");
    if (cancelButton) {
        cancelButton.addEventListener("click", function() {
            closeModal("rateProductModal");
        });
    }

    // Close modal when 'Submit' button is clicked in "Rate Product Modal"
    const submitButton = document.querySelector("#rateProductModal .btn-primary");
    if (submitButton) {
        submitButton.addEventListener("click", function() {
            closeModal("rateProductModal");
        });
    }

    // Close modal when 'X' button is clicked in "Rate Product Modal"
    const closeRateProductModalButton = document.querySelector("#rateProductModal .close");
    if (closeRateProductModalButton) {
        closeRateProductModalButton.addEventListener("click", function() {
            closeModal("rateProductModal");
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const stars = document.querySelectorAll(".star");
    let selectedRating = 0;

    // Function to handle the hover effect on the stars
    stars.forEach(star => {
        star.addEventListener("mouseover", function() {
            const rating = parseInt(star.getAttribute("data-value"));
            highlightStars(rating);
        });

        // Function to handle the click event on the stars
        star.addEventListener("click", function() {
            selectedRating = parseInt(star.getAttribute("data-value"));
            setRating(selectedRating);
        });

        // Reset the star colors when mouse leaves
        star.addEventListener("mouseout", function() {
            highlightStars(selectedRating); // Keep the selected rating highlighted
        });
    });

    // Function to highlight stars based on the rating
    function highlightStars(rating) {
        stars.forEach(star => {
            const starRating = parseInt(star.getAttribute("data-value"));
            if (starRating <= rating) {
                star.classList.add("active");
            } else {
                star.classList.remove("active");
            }
        });
    }

    // Function to set the final rating and keep it filled after clicking
    function setRating(rating) {
        selectedRating = rating; // Update selected rating
        highlightStars(rating); // Highlight the stars based on the selected rating
    }

    // Handle Submit Rating button click
    document.getElementById("submitRatingBtn").addEventListener("click", function() {
        // You can add the rating submission logic here (e.g., AJAX or form submission)
        console.log("User rating: " + selectedRating); // For debugging, showing the selected rating
        closeModal("rateProductModal"); // Close the modal after submission
    });
});

//MESSENGER
document.getElementById("contactSellerBtn").addEventListener("click", function() {
    // Replace 'yourpageusername' with your actual Facebook Page username
    window.open("https://www.facebook.com/share/1ASQ6TT9qM/", "_blank");
});