<?php
session_start();
include '../config/dbcon.php';

// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    echo "Unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['category'])) {
    $productName = $_POST['name'];
    $category = $_POST['category'];
    
    // Generate SKU
    $prefix = strtoupper(substr($category, 0, 3));
    $shortName = strtoupper(substr(preg_replace("/[^a-zA-Z0-9]/", '', $productName), 0, 4));
    $randomNumber = rand(100, 999);
    $sku = $prefix . "-" . $shortName . "-" . $randomNumber;
    
    echo $sku;
}
?>
