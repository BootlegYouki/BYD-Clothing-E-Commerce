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
            <div class="text-center py-5" id="no-orders-message">
                <i class="fas fa-shopping-bag fa-3x mb-3" style="color: #ff7f50;"></i>
                <p>No orders found. Start shopping now!</p>
                <a href="shop.php" class="btn-con mt-2">Shop Now</a>
            </div>
            
            <!-- Order Template - Will be cloned and populated by JavaScript -->
            <div class="order-card d-none" id="order-template">
                <div class="order-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">Order #</span> <span class="order-id"></span>
                        <span class="ms-3 text-muted order-date"></span>
                    </div>
                    <span class="status-badge"></span>
                </div>
                <div class="order-body">
                    <div class="order-items-container">
                        <!-- Order items will be inserted here -->
                    </div>
                </div>
                <div class="order-footer d-flex justify-content-between align-items-center">
                    <div class="order-total">
                        <span class="fw-bold">Total:</span> ₱<span class="total-amount"></span>
                    </div>
                    <div class="order-actions">
                        <!-- Action buttons will be inserted here based on order status -->
                    </div>
                </div>
            </div>
            
            <!-- Order Item Template - Will be cloned and populated by JavaScript -->
            <div class="d-none" id="order-item-template">
                <div class="d-flex justify-content-between align-items-center mb-3 order-item">
                    <div class="d-flex align-items-center">
                        <div style="width: 60px; height: 60px; overflow: hidden;" class="flex-shrink-0 me-3">
                            <img src="" class="img-fluid product-image" alt="Product Image">
                        </div>
                        <div>
                            <h6 class="mb-0 product-name"></h6>
                            <p class="small text-muted mb-0">
                                <span class="product-size"></span> | Qty: <span class="product-quantity"></span>
                            </p>
                            <p class="small text-muted mb-0 product-price"></p>
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="mb-0 fw-bold product-subtotal"></p>
                    </div>
                </div>
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
                <button type="button" class="btn-con" id="confirmReceivedBtn" data-order-id="" data-bs-dismiss="modal">Confirm</button>
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
                <div class="product-to-rate mb-3">
                    <div style="width: 80px; height: 80px; overflow: hidden;" class="mx-auto mb-2">
                        <img src="" id="rating-product-image" class="img-fluid" alt="Product Image">
                    </div>
                    <h6 id="rating-product-name" class="mb-0"></h6>
                </div>
                <p>How would you rate this product?</p>
                <div id="star-rating">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <div class="mt-3">
                    <textarea id="review-text" class="form-control" rows="3" placeholder="Write your review here (optional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-con" id="submitRatingBtn" data-order-id="" data-product-id="" data-bs-dismiss="modal">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Order ID:</strong> <span id="detail-order-id"></span></p>
                        <p class="mb-1"><strong>Date:</strong> <span id="detail-order-date"></span></p>
                        <p class="mb-1"><strong>Status:</strong> <span id="detail-order-status"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Payment Method:</strong> <span id="detail-payment-method"></span></p>
                        <p class="mb-1"><strong>Payment ID:</strong> <span id="detail-payment-id"></span></p>
                        <p class="mb-1"><strong>Payment Status:</strong> <span id="detail-payment-status"></span></p>
                    </div>
                </div>
                
                <h6 class="border-bottom pb-2 mb-3">Shipping Information</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Name:</strong> <span id="detail-customer-name"></span></p>
                        <p class="mb-1"><strong>Email:</strong> <span id="detail-customer-email"></span></p>
                        <p class="mb-1"><strong>Phone:</strong> <span id="detail-customer-phone"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Address:</strong> <span id="detail-customer-address"></span></p>
                        <p class="mb-1"><strong>City:</strong> <span id="detail-customer-city"></span></p>
                        <p class="mb-1"><strong>Zip Code:</strong> <span id="detail-customer-zipcode"></span></p>
                    </div>
                </div>
                
                <h6 class="border-bottom pb-2 mb-3">Order Items</h6>
                <div id="detail-order-items">
                    <!-- Order items will be inserted here -->
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td>Subtotal:</td>
                                <td class="text-end">₱<span id="detail-subtotal"></span></td>
                            </tr>
                            <tr>
                                <td>Shipping:</td>
                                <td class="text-end">₱<span id="detail-shipping"></span></td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Total:</td>
                                <td class="text-end">₱<span id="detail-total"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                <div id="detail-action-buttons">
                    <!-- Action buttons will be inserted here based on order status -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (should be after jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/order_track.js"></script>