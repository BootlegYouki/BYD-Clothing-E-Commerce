<?php
// Start session for authentication
session_start();
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Include database connection
include('../../config/dbcon.php');

// Validate order ID
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    echo '<div class="alert alert-danger">Invalid order ID</div>';
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order details - use coordinates directly from orders table
$order_query = "SELECT o.* FROM orders o WHERE o.id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo '<div class="alert alert-danger">Order not found</div>';
    exit();
}

$order = mysqli_fetch_assoc($result);

// Fetch order items
$items_query = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

// Format status badge
$status_badge = '';
switch($order['status']) {
    case 'processing':
        $status_badge = '<span class="badge bg-primary">Processing</span>';
        break;
    case 'pending':
        $status_badge = '<span class="badge bg-warning">Pending</span>';
        break;
    case 'shipped':
        $status_badge = '<span class="badge bg-info">Shipped</span>';
        break;
    case 'delivered':
        $status_badge = '<span class="badge bg-success">Delivered</span>';
        break;
    case 'cancelled':
        $status_badge = '<span class="badge bg-danger">Cancelled</span>';
        break;
    default:
        $status_badge = '<span class="badge bg-secondary">'.$order['status'].'</span>';
}

// Calculate days since order
$order_date = new DateTime($order['created_at']);
$now = new DateTime();
$days_since_order = $order_date->diff($now)->days;

// Prepare address display
$address_text = $order['address'] . ', ' . $order['zipcode'];
$has_coordinates = !empty($order['latitude']) && !empty($order['longitude']);
$address_html = '';

if ($has_coordinates) {
    $maps_url = "https://www.google.com/maps?q={$order['latitude']},{$order['longitude']}";
    $address_html = '<strong>Address:</strong> <a href="' . $maps_url . '" target="_blank" class="text-primary">' . 
                    $address_text . ' <i class="bx bx-map-pin"></i></a>';
} else {
    $address_html = '<strong>Address:</strong> ' . $address_text;
}
?>

<div class="order-details">
    <!-- Order Summary Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-uppercase text-muted">Order Information</h6>
            <div class="card bg-light">
                <div class="card-body p-3">
                    <p class="mb-1"><strong>Order ID:</strong> #<?= $order['id'] ?></p>
                    <p class="mb-1"><strong>Reference:</strong> <?= $order['reference_number'] ?? 'N/A' ?></p>
                    <p class="mb-1"><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></p>
                    <p class="mb-1"><strong>Elapsed Time:</strong> <?= $days_since_order ?> days old</p>
                    <p class="mb-1"><strong>Status:</strong> <?= $status_badge ?></p>
                    <p class="mb-1"><strong>Payment Method:</strong> <?= $order['payment_method'] ?></p>
                    <p class="mb-0"><strong>Payment ID:</strong> <?= $order['payment_id'] ?? 'N/A' ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <h6 class="text-uppercase text-muted">Customer Information</h6>
            <div class="card bg-light">
                <div class="card-body p-3">
                    <p class="mb-1"><strong>Name:</strong> <?= $order['firstname'] . ' ' . $order['lastname'] ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= $order['email'] ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?= $order['phone'] ?></p>
                    <p class="mb-0"><?= $address_html ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Items Section -->
    <h6 class="text-uppercase text-muted mb-3">Order Items</h6>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_items = 0;
                
                while ($item = mysqli_fetch_assoc($items_result)) {
                    $total_items += $item['quantity'];
                    echo '<tr>
                        <td>' . $item['product_name'] . '</td>
                        <td>' . $item['size'] . '</td>
                        <td class="text-center">' . $item['quantity'] . '</td>
                        <td class="text-end">₱' . number_format($item['price'], 2) . '</td>
                        <td class="text-end">₱' . number_format($item['subtotal'], 2) . '</td>
                    </tr>';
                }
                ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td class="text-end">₱<?= number_format($order['subtotal'], 2) ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                    <td class="text-end">₱<?= number_format($order['shipping_cost'], 2) ?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                    <td class="text-end"><strong>₱<?= number_format($order['total_amount'], 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
