<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

// Include database connection
include 'config/dbcon.php';
function tableExists($conn, $tableName) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$tableName'");
    return $result && mysqli_num_rows($result) > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>Admin | Beyond Doubt Clothing</title>
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  
  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel="stylesheet" href="assets/css/sidebar.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
<?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-sm mb-0 text-capitalize">Pending Orders</p>
                                        <?php
                                        $pending_count = 0;
                                        if (tableExists($conn, 'orders')) {
                                            $pending_query = "SELECT COUNT(*) as count FROM orders WHERE status='pending'";
                                            $pending_result = mysqli_query($conn, $pending_query);
                                            if ($pending_result && $row = mysqli_fetch_assoc($pending_result)) {
                                                $pending_count = $row['count'];
                                            }
                                        }
                                        ?>
                                        <h4 class="mb-0"><?= $pending_count ?></h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10 cursor-pointer">shopping_bag</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <a href="orders.php?status=pending" class="mb-0 text-sm text-coral">
                                    View pending orders <i class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sales -->
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-sm mb-0 text-capitalize">Monthly Sales</p>
                                        <?php
                                        $total_sales = 0;
                                        $percent_change = 0;
                                        $change_class = 'text-success';
                                        $change_symbol = '';
                                        
                                        if (tableExists($conn, 'orders')) {
                                            $month = date('m');
                                            $year = date('Y');
                                            $sales_query = "SELECT SUM(total_price) as total FROM orders WHERE MONTH(order_date) = '$month' AND YEAR(order_date) = '$year' AND status != 'cancelled'";
                                            $sales_result = mysqli_query($conn, $sales_query);
                                            
                                            if ($sales_result && $row = mysqli_fetch_assoc($sales_result)) {
                                                $total_sales = $row['total'] ?: 0;
                                            }
                                            
                                            // Calculate previous month sales
                                            $prev_month = $month - 1;
                                            $prev_year = $year;
                                            if ($prev_month == 0) {
                                                $prev_month = 12;
                                                $prev_year--;
                                            }
                                            
                                            $prev_sales_query = "SELECT SUM(total_price) as total FROM orders WHERE MONTH(order_date) = '$prev_month' AND YEAR(order_date) = '$prev_year' AND status != 'cancelled'";
                                            $prev_sales_result = mysqli_query($conn, $prev_sales_query);
                                            $prev_total_sales = 0;
                                            
                                            if ($prev_sales_result && $row = mysqli_fetch_assoc($prev_sales_result)) {
                                                $prev_total_sales = $row['total'] ?: 0;
                                            }
                                            
                                            // Calculate percentage change
                                            if ($prev_total_sales > 0) {
                                                $percent_change = (($total_sales - $prev_total_sales) / $prev_total_sales) * 100;
                                            }
                                            
                                            $change_class = $percent_change >= 0 ? 'text-success' : 'text-danger';
                                            $change_symbol = $percent_change >= 0 ? '+' : '';
                                        }
                                        ?>
                                        <h4 class="mb-0">₱<?= number_format($total_sales, 2) ?></h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10 cursor-pointer">payments</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm">
                                    <span class="<?= $change_class ?> font-weight-bolder text-coral"><?= $change_symbol . number_format($percent_change, 1) ?>% </span>
                                    than last month
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Items -->
                    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-sm mb-0 text-capitalize">Low Stock Items</p>
                                        <?php
                                        $low_stock_count = 0;
                                        if (tableExists($conn, 'product_sizes')) {
                                            $stock_query = "SELECT COUNT(*) as count FROM product_sizes WHERE stock <= 5";
                                            $stock_result = mysqli_query($conn, $stock_query);
                                            
                                            if ($stock_result && $row = mysqli_fetch_assoc($stock_result)) {
                                                $low_stock_count = $row['count'];
                                            }
                                        }
                                        ?>
                                        <h4 class="mb-0"><?= $low_stock_count ?></h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10 cursor-pointer">inventory</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <a href="inventory.php?filter=low_stock" class="mb-0 text-sm text-coral">
                                    View low stock items <i class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Total Products -->
                    <div class="col-xl-3 col-sm-6">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-sm mb-0 text-capitalize">Total Products</p>
                                        <?php
                                        $products_count = 0;
                                        if (tableExists($conn, 'products')) {
                                            $products_query = "SELECT COUNT(*) as count FROM products";
                                            $products_result = mysqli_query($conn, $products_query);
                                            
                                            if ($products_result && $row = mysqli_fetch_assoc($products_result)) {
                                                $products_count = $row['count'];
                                            }
                                        }
                                        ?>
                                        <h4 class="mb-0"><?= $products_count ?></h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">category</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <a href="products.php" class="mb-0 text-sm text-coral">
                                    Manage products <i class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-8 mb-4">
                <div class="card h-100"> <!-- Added h-100 for equal height -->
                    <div class="card-header p-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">Monthly Sales</h6>
                            <div>
                            <button class="btn btn-outline-coral btn-sm mb-0" id="view-sales-year">Year</button>
                            <button class="btn btn-outline-coral btn-sm mb-0 active" id="view-sales-month">Month</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container" style="position: relative; height: 350px;"> <!-- Fixed height container -->
                            <canvas id="chart-sales"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100"> <!-- Added h-100 for equal height -->
                    <div class="card-header p-3">
                        <h6 class="mb-0">Inventory by Category</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container" style="position: relative; height: 350px;"> <!-- Fixed height container -->
                            <canvas id="chart-inventory"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
  <!-- Core JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Prepare sales chart
        var ctx1 = document.getElementById("chart-sales").getContext("2d");
        var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);
        gradientStroke1.addColorStop(1, 'rgba(255, 127, 80, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(255, 127, 80, 0.05)');
        gradientStroke1.addColorStop(0, 'rgba(255, 127, 80, 0)');

        // Fetch monthly sales data
        <?php
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $sales_data = [4500, 5000, 6000, 8000, 10000, 9500, 11000, 10500, 11500, 12000, 8500, 9000];
        
        if (tableExists($conn, 'orders')) {
            $year = date('Y');
            $sales_by_month_query = "SELECT MONTH(order_date) as month, SUM(total_price) as total 
                                    FROM orders 
                                    WHERE YEAR(order_date) = '$year' AND status != 'cancelled' 
                                    GROUP BY MONTH(order_date)";
            $sales_by_month_result = mysqli_query($conn, $sales_by_month_query);
            
            if ($sales_by_month_result) {
                while ($row = mysqli_fetch_assoc($sales_by_month_result)) {
                    $month_index = $row['month'] - 1; // Adjust for 0-based array
                    $sales_data[$month_index] = floatval($row['total']);
                }
            }
        }
        ?>
        
        // Generate monthly sales chart
        var monthlySalesChart = new Chart(ctx1, {
            type: "line",
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: "Monthly Sales",
                    tension: 0.4,
                    borderWidth: 2,
                    borderColor: "#FF7F50",
                    backgroundColor: gradientStroke1,
                    fill: true,
                    data: <?= json_encode($sales_data) ?>,
                    maxBarThickness: 6
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // This is crucial
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        padding: 10,
                        titleColor: '#fff',
                        titleFont: {
                            size: 14,
                            family: "Inter"
                        },
                        bodyColor: '#fff',
                        bodyFont: {
                            size: 12,
                            family: "Inter"
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Sales: ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            },
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: {
                                size: 11,
                                family: "Inter",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Inter",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
        
        // Prepare inventory chart
        var ctx2 = document.getElementById("chart-inventory").getContext("2d");
        
        <?php
        $categories = [];
        $inventory_data = [];
        $background_colors = [
            'rgba(255, 127, 80, 1)',  
            'rgba(0, 0, 0, 0.8)',       
            'rgba(255, 127, 80, 0.7)',  
            'rgba(70, 70, 70, 0.8)',   
            'rgba(255, 127, 80, 0.5)',  
            'rgba(200, 200, 200, 0.8)' 
        ];
        
        if (tableExists($conn, 'products') && tableExists($conn, 'product_sizes')) {
            $inventory_query = "SELECT p.category, SUM(ps.stock) as total_stock 
                                FROM products p
                                JOIN product_sizes ps ON p.id = ps.product_id
                                GROUP BY p.category
                                ORDER BY total_stock DESC";
            $inventory_result = mysqli_query($conn, $inventory_query);
            
            if ($inventory_result) {
                while ($row = mysqli_fetch_assoc($inventory_result)) {
                    $categories[] = $row['category'];
                    $inventory_data[] = intval($row['total_stock']);
                }
            }
        }
        ?>
        
        // Generate inventory chart
        var inventoryChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($categories) ?>,
                datasets: [{
                    label: "Stock",
                    weight: 9,
                    cutout: 50,
                    tension: 0.9,
                    pointRadius: 2,
                    borderWidth: 2,
                    backgroundColor: <?= json_encode($background_colors) ?>,
                    data: <?= json_encode($inventory_data) ?>,
                    fill: false
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // This is crucial
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11,
                                family: "Inter"
                            },
                            boxWidth: 15 // Smaller legend color boxes
                        },
                        onClick: null // Disable clicking on legend items (optional)
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.7)',
                        padding: 10,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' items';
                            }
                        }
                    }
                },
                cutout: '60%',
                layout: {
                    padding: 20 // Add padding to ensure everything fits
                }
            },
        });
        
        document.getElementById('view-sales-year').addEventListener('click', function() {
            document.getElementById('view-sales-month').classList.remove('active');
            this.classList.add('active');
            
            monthlySalesChart.data.labels = ["2020", "2021", "2022", "2023", "2024"];
            monthlySalesChart.data.datasets[0].data = [85000, 125000, 165000, 220000, 140000];
            monthlySalesChart.options.scales.x.title = {
                display: true,
                text: 'Year'
            };
            monthlySalesChart.update();
        });
        
        document.getElementById('view-sales-month').addEventListener('click', function() {
            document.getElementById('view-sales-year').classList.remove('active');
            this.classList.add('active');
            
            // Static monthly data
            monthlySalesChart.data.labels = <?= json_encode($months) ?>;
            monthlySalesChart.data.datasets[0].data = <?= json_encode($sales_data) ?>;
            monthlySalesChart.options.scales.x.title = {
                display: true,
                text: 'Month'
            };
            monthlySalesChart.update();
        });
    });
    </script>
</body>
</html>