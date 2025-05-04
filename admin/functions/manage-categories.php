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

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            // Add new category
            if (isset($_POST['new_category']) && !empty($_POST['new_category'])) {
                $newCategory = mysqli_real_escape_string($conn, $_POST['new_category']);
                
                // Check if category already exists
                $check_query = "SELECT COUNT(*) as count FROM categories WHERE name = '$newCategory'";
                $check_result = mysqli_query($conn, $check_query);
                $check_data = mysqli_fetch_assoc($check_result);
                
                if ($check_data['count'] > 0) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Category already exists'
                    ];
                } else {
                    // Insert new category
                    $query = "INSERT INTO categories (name) VALUES ('$newCategory')";
                    if (mysqli_query($conn, $query)) {
                        $response = [
                            'status' => 'success',
                            'message' => 'Category added successfully'
                        ];
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Failed to add category: ' . mysqli_error($conn)
                        ];
                    }
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Category name is required'
                ];
            }
            break;
            
        case 'rename':
            // Rename category
            if (isset($_POST['old_category']) && isset($_POST['new_category']) && 
                !empty($_POST['old_category']) && !empty($_POST['new_category'])) {
                
                $oldCategory = mysqli_real_escape_string($conn, $_POST['old_category']);
                $newCategory = mysqli_real_escape_string($conn, $_POST['new_category']);
                
                // Check if new category name already exists
                $check_query = "SELECT COUNT(*) as count FROM categories WHERE name = '$newCategory' AND name != '$oldCategory'";
                $check_result = mysqli_query($conn, $check_query);
                $check_data = mysqli_fetch_assoc($check_result);
                
                if ($check_data['count'] > 0) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Category already exists'
                    ];
                } else {
                    $query = "UPDATE categories SET name = '$newCategory' WHERE name = '$oldCategory'";
                    if (mysqli_query($conn, $query)) {
                        $response = [
                            'status' => 'success',
                            'message' => 'Category renamed successfully'
                        ];
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Failed to rename category: ' . mysqli_error($conn)
                        ];
                    }
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Both old and new category names are required'
                ];
            }
            break;
            
        case 'delete':
            // Delete category
            if (isset($_POST['category']) && !empty($_POST['category'])) {
                $category = mysqli_real_escape_string($conn, $_POST['category']);
                
                // Check if products use this category
                $check_query = "SELECT COUNT(*) as count FROM products p 
                                JOIN categories c ON p.category_id = c.id 
                                WHERE c.name = '$category'";
                $check_result = mysqli_query($conn, $check_query);
                $check_data = mysqli_fetch_assoc($check_result);
                
                if ($check_data['count'] > 0) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Cannot delete category that is being used by products'
                    ];
                } else {
                    // Delete category
                    $query = "DELETE FROM categories WHERE name = '$category'";
                    if (mysqli_query($conn, $query)) {
                        $response = [
                            'status' => 'success',
                            'message' => 'Category deleted successfully'
                        ];
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Failed to delete category: ' . mysqli_error($conn)
                        ];
                    }
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Category name is required'
                ];
            }
            break;
            
        default:
            $response = [
                'status' => 'error',
                'message' => 'Invalid action'
            ];
    }
    
    echo json_encode($response);
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method'
    ];
    echo json_encode($response);
}
?>
