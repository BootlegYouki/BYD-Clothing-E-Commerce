<link rel="stylesheet" href="css/order_track.css">




<div class="container mt-5">
    <h2>My Orders</h2>

    <!-- Search Bar -->
    <form method="POST" class="mb-4">
        <input type="text" name="search" class="form-control" placeholder="Search by Seller Name, Order ID, or Product Name">
    </form>

    <!-- Tab Navigation -->
    <ul class="nav nav-pills mb-3" id="orderTabs">
    <li class="nav-item"><a class="nav-link active cursor-pointer" data-tab="all" href="#">All</a></li>
    <li class="nav-item"><a class="nav-link cursor-pointer" data-tab="to_pay" href="#">To Pay</a></li>
    <li class="nav-item"><a class="nav-link cursor-pointer" data-tab="to_ship" href="#">To Ship</a></li>
    <li class="nav-item"><a class="nav-link cursor-pointer" data-tab="to_receive" href="#">To Receive</a></li>
    <li class="nav-item"><a class="nav-link cursor-pointer" data-tab="completed" href="#">Completed</a></li>
    <li class="nav-item"><a class="nav-link cursor-pointer" data-tab="to_review" href="#">To Review</a></li>
</ul>

    <!-- Orders Container -->
    <div id="ordersContainer">
        <!-- Order List will be loaded dynamically -->
    </div>
</div>

<!-- Order Received Modal -->
<div class="modal fade" id="orderReceivedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Order Received</h5>
                <!-- Close button for modal -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Check that you received all items in satisfactory condition (no return/refund required) before confirming receipt. Once you confirm, the order is completed and we will release the payment to the seller.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-1" data-dismiss="modal">Not Now</button>
                <button type="button" class="btn btn-primary btn-2" id="confirmReceivedBtn" data-dismiss="modal">Confirm</button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                <button type="button" class=" btn btn-secondary btn-1" data-dismiss="modal">Cancel</button>
                <button type="button" class=" btn btn-primary btn-2" id="submitRatingBtn" data-dismiss="modal">Submit</button>
            </div>
        </div>
    </div>
</div>





<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (should be after jQuery) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="js/order_track.js"></script>

