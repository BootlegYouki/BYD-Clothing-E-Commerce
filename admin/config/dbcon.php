<?php
// Load environment variables
require_once __DIR__ . '/env_loader.php';
loadEnvFile(__DIR__ . '/../../.env');

// Database connection
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_DATABASE') ?: 'byd_clothing_db';

// Define connection parameters
$local_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'dbecomm'
];

$remote_config = [
    'host' => 'sql206.infinityfree.com',
    'username' => 'if0_38370362',
    'password' => 'Vj4Ur3D61Hb',
    'database' => 'if0_38370362_dbecomm'
];

function try_connection($config) {
    // Try to establish the connection
    $conn = @mysqli_connect(
        $config['host'], 
        $config['username'], 
        $config['password'], 
        $config['database']
    );
    
    // If connection failed, log the error for debugging
    if (!$conn) {
        error_log("Connection failed to {$config['host']}: " . mysqli_connect_error());
    }
    
    return $conn;
}

// Try local connection first if we're on localhost
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    $conn = try_connection($local_config);
    
    // If local connection fails, try remote connection
    if (!$conn) {
        $conn = try_connection($remote_config);
        if ($conn) {
            error_log("Local connection failed, using remote connection");
        }
    } else {
        error_log("Using local database connection");
    }
} 
else {
    $conn = try_connection($remote_config);
    
    // If remote connection fails, try local connection
    if (!$conn) {
        $conn = try_connection($local_config);
        if ($conn) {
            error_log("Remote connection failed, using local connection");
        }
    } else {
        error_log("Using remote database connection");
    }
}

// If both connections fail, show error
if (!$conn) {
    die("Connection failed: Unable to connect to either local or remote database.");
}
?>