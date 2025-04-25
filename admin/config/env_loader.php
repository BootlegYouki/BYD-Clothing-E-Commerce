<?php
// Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Check if .env file exists (local development)
if (file_exists(__DIR__ . '/../../.env')) {
    // Load environment variables from .env file
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->safeLoad();
}

// Helper function to get environment variables from either .env or server environment
function getEnvVar($key, $default = null) {
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    } 
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    if (getenv($key) !== false) {
        return getenv($key);
    }
    return $default;
}
?>