<?php
/**
 * Timezone initialization file
 * Sets the default timezone for all PHP date/time functions
 */

// Set Philippines timezone (UTC+8:00)
date_default_timezone_set('Asia/Manila');

// Function to initialize MySQL timezone on a connection
function initializeTimezone($connection) {
    if ($connection) {
        mysqli_query($connection, "SET time_zone = '+08:00'");
    }
}
?>
