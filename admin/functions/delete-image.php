<?php
include('../config/dbcon.php');
header('Content-Type: application/json');

if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Image ID is required']);
    exit();
}

$image_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get image details before deleting
$query = "SELECT image_url FROM product_images WHERE id = '$image_id'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Image not found']);
    exit();
}

$image = mysqli_fetch_assoc($result);
$image_path = '../../' . $image['image_url'];

// Delete from database
$delete_query = "DELETE FROM product_images WHERE id = '$image_id'";
$delete_result = mysqli_query($conn, $delete_query);

if($delete_result) {
    // Try to delete the physical file
    if(file_exists($image_path) && unlink($image_path)) {
        echo json_encode(['success' => true]);
    } else {
        // Still return success even if file deletion fails - database record is gone
        echo json_encode(['success' => true, 'message' => 'Image deleted from database, but file may remain']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete image: ' . mysqli_error($conn)]);
}
?>