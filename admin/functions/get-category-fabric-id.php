<?php
session_start();
include '../config/dbcon.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    $response = [
        'status' => 'error',
        'message' => 'Unauthorized access'
    ];
    echo json_encode($response);
    exit();
}

// Function to get or create a category
if (isset($_POST['get_category_id']) && !empty($_POST['get_category_id'])) {
    $categoryName = mysqli_real_escape_string($conn, $_POST['get_category_id']);
    
    // Check if category exists
    $query = "SELECT id FROM categories WHERE name = '$categoryName'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Category exists, return its ID
        $category = mysqli_fetch_assoc($result);
        echo json_encode([
            'status' => 'success',
            'id' => $category['id']
        ]);
    } else {
        // Category doesn't exist, create it
        $insert_query = "INSERT INTO categories (name) VALUES ('$categoryName')";
        if (mysqli_query($conn, $insert_query)) {
            echo json_encode([
                'status' => 'success',
                'id' => mysqli_insert_id($conn)
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create category: ' . mysqli_error($conn)
            ]);
        }
    }
    exit();
}

// Function to get or create a fabric
if (isset($_POST['get_fabric_id']) && !empty($_POST['get_fabric_id'])) {
    $fabricName = mysqli_real_escape_string($conn, $_POST['get_fabric_id']);
    
    // Check if fabric exists
    $query = "SELECT id FROM fabrics WHERE name = '$fabricName'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Fabric exists, return its ID
        $fabric = mysqli_fetch_assoc($result);
        echo json_encode([
            'status' => 'success',
            'id' => $fabric['id']
        ]);
    } else {
        // Fabric doesn't exist, create it
        $insert_query = "INSERT INTO fabrics (name) VALUES ('$fabricName')";
        if (mysqli_query($conn, $insert_query)) {
            echo json_encode([
                'status' => 'success',
                'id' => mysqli_insert_id($conn)
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create fabric: ' . mysqli_error($conn)
            ]);
        }
    }
    exit();
}

// If no action specified
echo json_encode([
    'status' => 'error',
    'message' => 'No action specified'
]);
?>
