<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../../../shop/index");
    exit();
}

// Include database connection
include('../../config/dbcon.php');

// Set default filter values
$status_filter = $_GET['status'] ?? 'all';
$date_filter = $_GET['date'] ?? 'all';
$search = $_GET['search'] ?? '';
$format = strtolower($_GET['format'] ?? 'csv');

// Start building the SQL query
$query = "SELECT o.*, COUNT(oi.id) as item_count 
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id";
$where_conditions = [];
$params = [];
$types = "";

// Apply status filter
if ($status_filter != 'all') {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// Apply date filter
if ($date_filter != 'all') {
    switch($date_filter) {
        case 'today':
            $where_conditions[] = "o.created_at >= CURDATE()";
            break;
        case 'yesterday':
            $where_conditions[] = "o.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND o.created_at < CURDATE()";
            break;
        case 'week':
            $where_conditions[] = "o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $where_conditions[] = "o.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'custom':
            if(isset($_GET['date_from']) && isset($_GET['date_to'])) {
                $date_from = mysqli_real_escape_string($conn, $_GET['date_from']);
                $date_to = mysqli_real_escape_string($conn, $_GET['date_to']);
                $where_conditions[] = "o.created_at >= '$date_from' AND o.created_at <= '$date_to 23:59:59'";
            }
            break;
    }
}

// Apply search filter
if (!empty($search)) {
    $search_term = '%' . $search . '%';
    $where_conditions[] = "(o.firstname LIKE ? OR o.lastname LIKE ? OR o.email LIKE ? OR o.reference_number LIKE ? OR o.payment_id LIKE ? OR o.id LIKE ?)";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term, $search_term]);
    $types .= "ssssss";
}

// Combine where conditions
if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

// Group by order ID
$query .= " GROUP BY o.id";

// Order by created_at desc
$query .= " ORDER BY o.created_at DESC";

// Execute query
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Define column headers for the export
$headers = [
    'Order ID', 
    'Reference', 
    'Date', 
    'Customer Name', 
    'Email', 
    'Phone',
    'Address',
    'Coordinates',
    'Items Count',
    'Subtotal',
    'Shipping',
    'Total Amount',
    'Payment Method',
    'Payment ID',
    'Status'
];

// Check if we have any orders to export
if (mysqli_num_rows($result) == 0) {
    header('Content-Type: text/html');
    echo '<script>alert("No orders found matching your criteria."); window.history.back();</script>';
    exit;
}

// Determine export format
if ($format === 'pdf') {
    exportPDF($result, $headers);
} else {
    exportCSV($result, $headers);
}

/**
 * Export orders data as CSV file
 * 
 * @param mysqli_result $result Query result with orders
 * @param array $headers Column headers
 */
function exportCSV($result, $headers) {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders-export-'.date('Y-m-d').'.csv"');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, $headers);
    
    // Add order data rows
    while ($row = mysqli_fetch_assoc($result)) {
        // Format coordinates for display
        $coordinates = (!empty($row['latitude']) && !empty($row['longitude'])) 
            ? $row['latitude'] . ', ' . $row['longitude']
            : 'N/A';
            
        fputcsv($output, [
            $row['id'],
            $row['reference_number'],
            $row['created_at'],
            $row['firstname'] . ' ' . $row['lastname'],
            $row['email'],
            $row['phone'],
            $row['address'] . ', ' . $row['zipcode'],
            $coordinates,
            $row['item_count'],
            $row['subtotal'],
            $row['shipping_cost'],
            $row['total_amount'],
            $row['payment_method'],
            $row['payment_id'],
            $row['status']
        ]);
    }
    
    // Close file pointer
    fclose($output);
    exit;
}

/**
 * Export orders data as PDF file
 * 
 * @param mysqli_result $result Query result with orders
 * @param array $headers Column headers
 */
function exportPDF($result, $headers) {
    // Check if mPDF is available, if not, fall back to CSV
    if (!class_exists('Mpdf\Mpdf')) {
        // Try to load via composer autoload
        $autoloadPath = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        } else {
            // Fall back to CSV if mPDF is not available
            exportCSV($result, $headers);
            return;
        }
    }
    
    // Initialize mPDF
    $mpdf = new \Mpdf\Mpdf([
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 15,
        'margin_bottom' => 15,
    ]);
    
    // Set document metadata
    $mpdf->SetTitle('BYD Clothing - Orders Export');
    $mpdf->SetAuthor('BYD Clothing Admin');
    
    // Start building the HTML content
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Orders Export</title>
        <style>
            body { font-family: sans-serif; font-size: 10pt; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background-color: #f2f2f2; font-weight: bold; text-align: left; }
            th, td { border: 1px solid #ddd; padding: 5px; font-size: 8pt; }
            h1 { font-size: 16pt; color: #333; }
            .text-center { text-align: center; }
            .small { font-size: 8pt; }
            .status-pending { color: #ff9800; }
            .status-processing { color: #2196F3; }
            .status-shipped { color: #00BCD4; }
            .status-delivered { color: #4CAF50; }
            .status-cancelled { color: #f44336; }
            a { color: #0000FF; text-decoration: underline; }
        </style>
    </head>
    <body>
        <h1 class="text-center">BYD Clothing - Orders Report</h1>
        <p class="text-center small">Generated on ' . date('Y-m-d H:i:s') . '</p>
        
        <table>
            <thead>
                <tr>';
    
    // Add table headers
    foreach ($headers as $header) {
        // Skip some columns to make PDF fit better
        if (in_array($header, ['Payment ID'])) continue;
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }
    
    $html .= '
                </tr>
            </thead>
            <tbody>';
    
    // Add table rows
    $row_count = 0;
    mysqli_data_seek($result, 0); // Reset result pointer
    
    while ($row = mysqli_fetch_assoc($result)) {
        $row_count++;
        
        // Get status CSS class
        $statusClass = 'status-' . $row['status'];
        
        $html .= '<tr>';
        
        // Order ID
        $html .= '<td>' . $row['id'] . '</td>';
        
        // Reference
        $html .= '<td>' . htmlspecialchars($row['reference_number'] ?? 'N/A') . '</td>';
        
        // Date
        $html .= '<td>' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</td>';
        
        // Customer Name
        $html .= '<td>' . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . '</td>';
        
        // Email
        $html .= '<td>' . htmlspecialchars($row['email']) . '</td>';
        
        // Phone
        $html .= '<td>' . htmlspecialchars($row['phone']) . '</td>';
        
        // Address
        $html .= '<td>' . htmlspecialchars($row['address'] . ', ' . $row['zipcode']) . '</td>';
        
        // Coordinates
        $coordinates = (!empty($row['latitude']) && !empty($row['longitude'])) 
            ? '<a href="https://www.google.com/maps?q=' . $row['latitude'] . ',' . $row['longitude'] . '" target="_blank">' . $row['latitude'] . ', ' . $row['longitude'] . '</a>'
            : 'N/A';
        $html .= '<td>' . $coordinates . '</td>';
        
        // Items Count
        $html .= '<td>' . $row['item_count'] . '</td>';
        
        // Subtotal
        $html .= '<td>₱' . number_format($row['subtotal'], 2) . '</td>';
        
        // Shipping
        $html .= '<td>₱' . number_format($row['shipping_cost'], 2) . '</td>';
        
        // Total Amount
        $html .= '<td>₱' . number_format($row['total_amount'], 2) . '</td>';
        
        // Payment Method
        $html .= '<td>' . htmlspecialchars($row['payment_method']) . '</td>';
        
        // Status
        $html .= '<td class="' . $statusClass . '">' . ucfirst(htmlspecialchars($row['status'])) . '</td>';
        
        $html .= '</tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <p class="text-center small">Total Orders: ' . $row_count . '</p>
    </body>
    </html>';
    
    // Generate PDF
    $mpdf->WriteHTML($html);
    
    // Output PDF
    $mpdf->Output('orders-export-' . date('Y-m-d') . '.pdf', 'D');
    exit;
}
?>
