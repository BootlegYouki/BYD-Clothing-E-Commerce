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
    
    // Get the fabric ID
    $fabric_query = "SELECT id FROM fabrics WHERE name = '$fabric'";
    $fabric_result = mysqli_query($conn, $fabric_query);
    
    if(!$fabric_result || mysqli_num_rows($fabric_result) == 0) {
        echo json_encode([
            'success' => false, 
            'message' => "Fabric not found"
        ]);
        exit();
    }
    
    $fabric_id = mysqli_fetch_assoc($fabric_result)['id'];
    
    // Check if fabric is being used by any products
    $check_query = "SELECT COUNT(*) as count FROM products WHERE fabric_id = $fabric_id";
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
        $update_query = "UPDATE products SET fabric_id = NULL WHERE fabric_id = $fabric_id";
        if(mysqli_query($conn, $update_query)) {
            // Now delete the fabric
            if(mysqli_query($conn, "DELETE FROM fabrics WHERE id = $fabric_id")) {
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
            echo json_encode([
                'success' => false, 
                'message' => "Error updating products: " . mysqli_error($conn)
            ]);
        }
    } else {
        // If no products use this fabric, just delete it
        if(mysqli_query($conn, "DELETE FROM fabrics WHERE id = $fabric_id")) {
            echo json_encode([
                'success' => true, 
                'message' => "Fabric \"$fabric\" has been removed. No products were affected."
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => "Error removing fabric: " . mysqli_error($conn)
            ]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No fabric specified']);
}
?>