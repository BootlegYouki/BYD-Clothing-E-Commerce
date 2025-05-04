<?php
session_start();
include '../../config/dbcon.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    $response = [
        'status' => 'error',
        'message' => 'Unauthorized access'
    ];
    echo json_encode($response);
    exit();
}

// Query to fetch categories from the new categories table
$query = "SELECT name FROM categories ORDER BY name";
$result = mysqli_query($conn, $query);

$categories = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row['name'];
    }
}

// Return the categories as JSON
$response = [
    'status' => 'success',
    'categories' => $categories
];

echo json_encode($response);
?>
