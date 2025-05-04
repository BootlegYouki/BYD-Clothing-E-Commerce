<?php
session_start();
include '../../config/dbcon.php';

// Validate inputs
if(!isset($_POST['product_id']) || !isset($_POST['field']) || !isset($_POST['value'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
$field = mysqli_real_escape_string($conn, $_POST['field']);
$value = (int)$_POST['value'];

// Validate field name to prevent SQL injection
if($field !== 'is_featured' && $field !== 'is_new_release') {
    echo json_encode(['success' => false, 'message' => 'Invalid field name']);
    exit();
}

$query = "UPDATE products SET $field = $value WHERE id = '$product_id'";
$result = mysqli_query($conn, $query);

if($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}
?>