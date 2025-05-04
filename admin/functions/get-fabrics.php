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

// Query to fetch fabrics from the new fabrics table
$query = "SELECT name FROM fabrics ORDER BY name";
$result = mysqli_query($conn, $query);

$fabrics = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fabrics[] = $row['name'];
    }
}

// Return the fabrics as JSON
$response = [
    'status' => 'success',
    'fabrics' => $fabrics
];

echo json_encode($response);
?>
