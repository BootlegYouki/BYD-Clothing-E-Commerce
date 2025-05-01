<?php
include('../config/dbcon.php');
header('Content-Type: application/json');

if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Image ID is required']);
    exit();
}

$image_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get image details before deleting
$query = "SELECT image_url, is_primary, product_id FROM product_images WHERE id = '$image_id'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Image not found']);
    exit();
}

$image = mysqli_fetch_assoc($result);
$image_path = '../../' . $image['image_url'];

// Start transaction for database operations
mysqli_autocommit($conn, false);
$success = true;

// Delete from database
$delete_query = "DELETE FROM product_images WHERE id = '$image_id'";
if(!mysqli_query($conn, $delete_query)) {
    $success = false;
    $error = mysqli_error($conn);
}

if($success) {
    mysqli_commit($conn);
    // Try to delete the physical file
    if(file_exists($image_path) && unlink($image_path)) {
        echo json_encode(['success' => true]);
    } else {
        // Still return success even if file deletion fails - database record is gone
        echo json_encode(['success' => true, 'message' => 'Image deleted from database, but file may remain']);
    }
} else {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to delete image: ' . $error]);
}

mysqli_autocommit($conn, true);
?>