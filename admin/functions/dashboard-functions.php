<?php
/**
 * Dashboard data functions
 * Functions to fetch and calculate data for the admin dashboard
 */

/**
 * Check if a table exists in the database
 *
 * @param mysqli $conn Database connection
 * @param string $tableName Table name to check
 * @return bool Whether table exists
 */
function tableExists($conn, $tableName) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return $result && mysqli_num_rows($result) > 0;
}

/**
 * Get all data required for the admin dashboard
 *
 * @param mysqli $conn Database connection
 * @return array Dashboard data
 */
function getDashboardData($conn) {
    $data = [
        'orders_count' => 0,
        'total_sales' => 0,
        'percent_change' => 0,
        'change_class' => 'text-success',
        'change_symbol' => '',
        'low_stock_count' => 0,
        'products_count' => 0,
        'months' => ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        'sales_data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        'years' => ["2023", "2024", "2025", "2026", "2027", "2028", "2029", "2030"],
        'yearly_sales_data' => [0, 0, 0, 0, 0, 0, 0, 0]
    ];
    
    // Fetch total orders count
    if (tableExists($conn, 'orders')) {
        $orders_query = "SELECT COUNT(*) as count FROM orders";
        $orders_result = mysqli_query($conn, $orders_query);
        if ($orders_result && $row = mysqli_fetch_assoc($orders_result)) {
            $data['orders_count'] = $row['count'];
        }
        
        // Calculate monthly sales
        $month = date('m');
        $year = date('Y');
        $sales_query = "SELECT SUM(total_amount) as total FROM orders WHERE MONTH(created_at) = '$month' AND YEAR(created_at) = '$year' AND status != 'cancelled'";
        $sales_result = mysqli_query($conn, $sales_query);
        
        if ($sales_result && $row = mysqli_fetch_assoc($sales_result)) {
            $data['total_sales'] = $row['total'] ?: 0;
        }
        
        // Calculate previous month sales
        $prev_month = $month - 1;
        $prev_year = $year;
        if ($prev_month == 0) {
            $prev_month = 12;
            $prev_year--;
        }
        
        $prev_sales_query = "SELECT SUM(total_amount) as total FROM orders WHERE MONTH(created_at) = '$prev_month' AND YEAR(created_at) = '$prev_year' AND status != 'cancelled'";
        $prev_sales_result = mysqli_query($conn, $prev_sales_query);
        $prev_total_sales = 0;
        
        if ($prev_sales_result && $row = mysqli_fetch_assoc($prev_sales_result)) {
            $prev_total_sales = $row['total'] ?: 0;
        }
        
        // Calculate percentage change
        if ($prev_total_sales > 0) {
            $data['percent_change'] = (($data['total_sales'] - $prev_total_sales) / $prev_total_sales) * 100;
        }
        
        $data['change_class'] = $data['percent_change'] >= 0 ? 'text-success' : 'text-danger';
        $data['change_symbol'] = $data['percent_change'] >= 0 ? '+' : '';
        
        // Get monthly sales chart data
        $year = date('Y');
        $sales_by_month_query = "SELECT MONTH(created_at) as month, SUM(total_amount) as total 
                                FROM orders 
                                WHERE YEAR(created_at) = '$year' AND status != 'cancelled' 
                                GROUP BY MONTH(created_at)";
        $sales_by_month_result = mysqli_query($conn, $sales_by_month_query);
        
        if ($sales_by_month_result) {
            while ($row = mysqli_fetch_assoc($sales_by_month_result)) {
                $month_index = $row['month'] - 1; // Adjust for 0-based array
                $data['sales_data'][$month_index] = floatval($row['total']);
            }
        }
        
        // Get yearly sales data
        $yearly_sales_query = "SELECT YEAR(created_at) as year, SUM(total_amount) as total 
                            FROM orders 
                            WHERE YEAR(created_at) >= 2023 AND YEAR(created_at) <= 2030 AND status != 'cancelled' 
                            GROUP BY YEAR(created_at)";
        $yearly_sales_result = mysqli_query($conn, $yearly_sales_query);
        
        if ($yearly_sales_result) {
            while ($row = mysqli_fetch_assoc($yearly_sales_result)) {
                $year_index = array_search($row['year'], $data['years']);
                if ($year_index !== false) {
                    $data['yearly_sales_data'][$year_index] = floatval($row['total']);
                }
            }
        }
    }
    
    // Get low stock count
    if (tableExists($conn, 'product_sizes')) {
        // Count products where TOTAL stock across all sizes is ≤ 50
        $stock_query = "SELECT COUNT(*) as count 
                    FROM (
                        SELECT product_id, SUM(stock) as total_stock 
                        FROM product_sizes 
                        GROUP BY product_id
                        HAVING total_stock <= 50 AND total_stock > 0
                    ) as low_stock_products";
        
        $stock_result = mysqli_query($conn, $stock_query);
        
        if ($stock_result && $row = mysqli_fetch_assoc($stock_result)) {
            $data['low_stock_count'] = $row['count'];
        }
    }
    
    // Get products count
    if (tableExists($conn, 'products')) {
        $products_query = "SELECT COUNT(*) as count FROM products";
        $products_result = mysqli_query($conn, $products_query);
        
        if ($products_result && $row = mysqli_fetch_assoc($products_result)) {
            $data['products_count'] = $row['count'];
        }
    }
    
    return $data;
}

/**
 * Get pending orders count
 * 
 * @param mysqli $conn Database connection
 * @return int Number of pending orders
 */
function getPendingOrdersCount($conn) {
    $pending_count = 0;
    
    if (tableExists($conn, 'orders')) {
        $pending_query = "SELECT COUNT(*) as count FROM orders WHERE status='pending'";
        $pending_result = mysqli_query($conn, $pending_query);
        if ($pending_result && $row = mysqli_fetch_assoc($pending_result)) {
            $pending_count = $row['count'];
        }
    }
    
    return $pending_count;
}

/**
 * Get count of unread notifications
 * @param mysqli $conn Database connection
 * @return int Count of unread notifications
 */
function getUnreadNotificationsCount($conn) {
    $query = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
    $result = mysqli_query($conn, $query);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return (int)$row['count'];
    }
    
    return 0;
}