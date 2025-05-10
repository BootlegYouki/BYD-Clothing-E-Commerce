<?php
session_start();
// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../shop/index");
    exit();
}

include 'config/dbcon.php';

include 'functions/dashboard-functions.php';

$dashboard = getDashboardData($conn);

extract($dashboard);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
                                        <p class="text-sm mb-0 text-capitalize">Orders</p>
                                        <h4 class="mb-0"><?= $orders_count ?></h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10 cursor-pointer">shopping_bag</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <a href="orders.php" class="mb-0 text-sm text-coral">
                                    View all orders <i class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle;">arrow_forward</i>
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
                                        <h4 class="mb-0"><?= $low_stock_count ?></h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-primary shadow-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10 cursor-pointer">inventory</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <a href="products.php" class="mb-0 text-sm text-coral">
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
                <div class="card h-100">
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
                        <div class="chart-container" style="position: relative; height: 350px;">
                            <canvas id="chart-sales"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header p-3">
                        <h6 class="mb-0">Inventory by Category</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container" style="position: relative; height: 350px;">
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
                maintainAspectRatio: false,
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
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11,
                                family: "Inter"
                            },
                            boxWidth: 15
                        },
                        onClick: null
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
                    padding: 20
                }
            },
        });
        
        // Toggle between year and month views
        document.getElementById('view-sales-year').addEventListener('click', function() {
            document.getElementById('view-sales-month').classList.remove('active');
            this.classList.add('active');
            
            // Update chart title
            document.querySelector('.card-header h6').textContent = 'Yearly Sales';
            
            // Yearly data
            monthlySalesChart.data.labels = <?= json_encode($years) ?>;
            monthlySalesChart.data.datasets[0].data = <?= json_encode($yearly_sales_data) ?>;
            monthlySalesChart.options.scales.x.title = {
                display: true,
            };
            monthlySalesChart.update();
        });
        
        document.getElementById('view-sales-month').addEventListener('click', function() {
            document.getElementById('view-sales-year').classList.remove('active');
            this.classList.add('active');
            
            // Update chart title
            document.querySelector('.card-header h6').textContent = 'Monthly Sales';
            
            // Monthly data
            monthlySalesChart.data.labels = <?= json_encode($months) ?>;
            monthlySalesChart.data.datasets[0].data = <?= json_encode($sales_data) ?>;
            monthlySalesChart.options.scales.x.title = {
                display: true,
            };
            monthlySalesChart.update();
        });
    });
    </script>
</body>
</html>