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
    
    // Get the category ID
    $category_query = "SELECT id FROM categories WHERE name = '$category'";
    $category_result = mysqli_query($conn, $category_query);
    
    if(!$category_result || mysqli_num_rows($category_result) == 0) {
        echo json_encode([
            'success' => false, 
            'message' => "Category not found"
        ]);
        exit();
    }
    
    $category_id = mysqli_fetch_assoc($category_result)['id'];
    
    // Check if category is being used by any products
    $check_query = "SELECT COUNT(*) as count FROM products WHERE category_id = $category_id";
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
        // Find the 'Uncategorized' category or create it if it doesn't exist
        $uncategorized_query = "SELECT id FROM categories WHERE name = 'Uncategorized'";
        $uncategorized_result = mysqli_query($conn, $uncategorized_query);
        
        if(mysqli_num_rows($uncategorized_result) == 0) {
            // Create Uncategorized category
            mysqli_query($conn, "INSERT INTO categories (name) VALUES ('Uncategorized')");
            $uncategorized_id = mysqli_insert_id($conn);
        } else {
            $uncategorized_id = mysqli_fetch_assoc($uncategorized_result)['id'];
        }
        
        // Update all products to have a default category
        $update_query = "UPDATE products SET category_id = $uncategorized_id WHERE category_id = $category_id";
        if(mysqli_query($conn, $update_query)) {
            // Now delete the category
            if(mysqli_query($conn, "DELETE FROM categories WHERE id = $category_id")) {
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
            echo json_encode([
                'success' => false, 
                'message' => "Error updating products: " . mysqli_error($conn)
            ]);
        }
    } else {
        // If no products use this category, just delete it
        if(mysqli_query($conn, "DELETE FROM categories WHERE id = $category_id")) {
            echo json_encode([
                'success' => true, 
                'message' => "Category \"$category\" has been removed. No products were affected."
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => "Error removing category: " . mysqli_error($conn)
            ]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No category specified']);
}
?>