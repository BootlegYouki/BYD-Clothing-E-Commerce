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
            // Add new fabric
            if (isset($_POST['new_fabric']) && !empty($_POST['new_fabric'])) {
                $newFabric = mysqli_real_escape_string($conn, $_POST['new_fabric']);
                
                // Check if fabric already exists
                $check_query = "SELECT COUNT(*) as count FROM fabrics WHERE name = '$newFabric'";
                $check_result = mysqli_query($conn, $check_query);
                $check_data = mysqli_fetch_assoc($check_result);
                
                if ($check_data['count'] > 0) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Fabric already exists'
                    ];
                } else {
                    // Insert new fabric
                    $query = "INSERT INTO fabrics (name) VALUES ('$newFabric')";
                    if (mysqli_query($conn, $query)) {
                        $response = [
                            'status' => 'success',
                            'message' => 'Fabric added successfully'
                        ];
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Failed to add fabric: ' . mysqli_error($conn)
                        ];
                    }
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Fabric name is required'
                ];
            }
            break;
            
        case 'rename':
            // Rename fabric
            if (isset($_POST['old_fabric']) && isset($_POST['new_fabric']) && 
                !empty($_POST['old_fabric']) && !empty($_POST['new_fabric'])) {
                
                $oldFabric = mysqli_real_escape_string($conn, $_POST['old_fabric']);
                $newFabric = mysqli_real_escape_string($conn, $_POST['new_fabric']);
                
                // Check if new fabric name already exists
                $check_query = "SELECT COUNT(*) as count FROM fabrics WHERE name = '$newFabric' AND name != '$oldFabric'";
                $check_result = mysqli_query($conn, $check_query);
                $check_data = mysqli_fetch_assoc($check_result);
                
                if ($check_data['count'] > 0) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Fabric already exists'
                    ];
                } else {
                    $query = "UPDATE fabrics SET name = '$newFabric' WHERE name = '$oldFabric'";
                    if (mysqli_query($conn, $query)) {
                        $response = [
                            'status' => 'success',
                            'message' => 'Fabric renamed successfully'
                        ];
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Failed to rename fabric: ' . mysqli_error($conn)
                        ];
                    }
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Both old and new fabric names are required'
                ];
            }
            break;
            
        case 'delete':
            // Delete fabric
            if (isset($_POST['fabric']) && !empty($_POST['fabric'])) {
                $fabric = mysqli_real_escape_string($conn, $_POST['fabric']);
                
                // Check if products use this fabric
                $check_query = "SELECT COUNT(*) as count FROM products p 
                                JOIN fabrics f ON p.fabric_id = f.id 
                                WHERE f.name = '$fabric'";
                $check_result = mysqli_query($conn, $check_query);
                $check_data = mysqli_fetch_assoc($check_result);
                
                if ($check_data['count'] > 0) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Cannot delete fabric that is being used by products'
                    ];
                } else {
                    // Delete fabric
                    $query = "DELETE FROM fabrics WHERE name = '$fabric'";
                    if (mysqli_query($conn, $query)) {
                        $response = [
                            'status' => 'success',
                            'message' => 'Fabric deleted successfully'
                        ];
                    } else {
                        $response = [
                            'status' => 'error',
                            'message' => 'Failed to delete fabric: ' . mysqli_error($conn)
                        ];
                    }
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Fabric name is required'
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
