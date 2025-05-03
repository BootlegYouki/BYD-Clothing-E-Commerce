<?php
// Set the default timezone for the application
date_default_timezone_set('Asia/Manila'); // Philippines timezone (UTC+8:00)

// Include the environment loader
require_once __DIR__ . '/env_loader.php';

// Get database credentials from environment variables
$host = getEnvVar('DB_HOST');
$username = getEnvVar('DB_USERNAME');
$password = getEnvVar('DB_PASSWORD');
$database = getEnvVar('DB_NAME');

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set MySQL timezone to match PHP timezone
mysqli_query($conn, "SET time_zone = '+08:00'");
?>