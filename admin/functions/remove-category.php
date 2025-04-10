<?php
session_start();
include('../config/dbcon.php');

// Check if user is logged in and is an admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if(isset($_POST['category']) && !empty($_POST['category'])) {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Check if category is being used by any products
    $check_query = "SELECT COUNT(*) as count FROM products WHERE category = '$category'";
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
        // Update all products to have a default category
        $update_query = "UPDATE products SET category = 'Uncategorized' WHERE category = '$category'";
        if(mysqli_query($conn, $update_query)) {
            echo json_encode([
                'success' => true, 
                'message' => "Category \"$category\" has been removed. $product_count product(s) have been updated to 'Uncategorized'."
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => "Error removing category: " . mysqli_error($conn)
            ]);
        }
    } else {
        // Even if there are no products with this category, we need to return success
        echo json_encode([
            'success' => true, 
            'message' => "Category \"$category\" has been removed. No products were affected."
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No category specified']);
}
?>