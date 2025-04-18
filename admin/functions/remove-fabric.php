<?php
session_start();
include('../config/dbcon.php');

// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if(isset($_POST['fabric']) && !empty($_POST['fabric'])) {
    $fabric = mysqli_real_escape_string($conn, $_POST['fabric']);
    
    // Check if fabric is being used by any products
    $check_query = "SELECT COUNT(*) as count FROM products WHERE fabric = '$fabric'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(!$check_result) {
        echo json_encode([
            'success' => false, 
            'message' => "Database error: " . mysqli_error($conn)
        ]);
        exit();
    }
    
    $product_count = mysqli_fetch_assoc($check_result)['count'];
    
    if($product_count > 0) {
        // Update all products to have no fabric
        $update_query = "UPDATE products SET fabric = NULL WHERE fabric = '$fabric'";
        if(mysqli_query($conn, $update_query)) {
            echo json_encode([
                'success' => true, 
                'message' => "Fabric \"$fabric\" has been removed. $product_count product(s) have been updated."
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => "Error removing fabric: " . mysqli_error($conn)
            ]);
        }
    } else {
        // Even if there are no products with this fabric, we need to return success
        // since the fabric has been effectively "removed"
        echo json_encode([
            'success' => true, 
            'message' => "Fabric \"$fabric\" has been removed. No products were affected."
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No fabric specified']);
}
?>