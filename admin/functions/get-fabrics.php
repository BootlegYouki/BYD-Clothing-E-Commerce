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

// Query to fetch fabrics
$query = "SELECT DISTINCT fabric FROM products WHERE fabric IS NOT NULL AND fabric != '' ORDER BY fabric";
$result = mysqli_query($conn, $query);

$fabrics = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['fabric'] != '' && $row['fabric'] != '_placeholder_') {
            $fabrics[] = $row['fabric'];
        }
    }
}

// Return the fabrics as JSON
$response = [
    'status' => 'success',
    'fabrics' => $fabrics
];

echo json_encode($response);
?>
