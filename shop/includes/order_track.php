<link rel="stylesheet" href="css/profile.css">
<link rel="stylesheet" href="css/order_track.css">

<div class="card border-0">
    <div class="card-body">
        <h2 class="mb-3">My Orders</h2>

        <!-- Search Bar -->
        <form method="POST" class="mb-4">
            <input type="text" name="search" class="form-control rounded-pill" placeholder="Search by Seller Name, Order ID, or Product Name">
        </form>

        <!-- Tab Navigation -->
        <ul class="nav nav-pills mb-4" id="orderTabs">
            <li class="nav-item me-2"><a class="nav-link active cursor-pointer rounded-pill" data-tab="all" href="#">All</a></li>
            <li class="nav-item me-2"><a class="nav-link cursor-pointer rounded-pill" data-tab="to_pay" href="#">To Pay</a></li>
            <li class="nav-item me-2"><a class="nav-link cursor-pointer rounded-pill" data-tab="to_ship" href="#">To Ship</a></li>
            <li class="nav-item me-2"><a class="nav-link cursor-pointer rounded-pill" data-tab="to_receive" href="#">To Receive</a></li>
            <li class="nav-item me-2"><a class="nav-link cursor-pointer rounded-pill" data-tab="completed" href="#">Completed</a></li>
            <li class="nav-item"><a class="nav-link cursor-pointer rounded-pill" data-tab="to_review" href="#">To Review</a></li>
        </ul>

        <!-- Orders Container -->
        <div id="ordersContainer" class="mt-3">
            <!-- Order List will be loaded dynamically via JS FUNCTION -->
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-3x mb-3" style="color: #ff7f50;"></i>
                <p>No orders found. Start shopping now!</p>
                <a href="shop.php" class="btn-con mt-2">Shop Now</a>
            </div>
        </div>
    </div>
</div>

<!-- Order Received Modal -->
<div class="modal fade" id="orderReceivedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Order Received</h5>
                <!-- Close button for modal -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Check that you received all items in satisfactory condition (no return/refund required) before confirming receipt. Once you confirm, the order is completed and we will release the payment to the seller.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Not Now</button>
                <button type="button" class="btn-con" id="confirmReceivedBtn" data-bs-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Rate Product Modal -->
<div class="modal fade" id="rateProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rate Your Product</h5>
                <!-- Close button for modal -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>How would you rate this product?</p>
                <div id="star-rating">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-con" id="submitRatingBtn" data-bs-dismiss="modal">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (should be after jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/order_track.js"></script>