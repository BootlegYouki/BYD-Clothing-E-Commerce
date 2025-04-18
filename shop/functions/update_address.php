<?php
require_once '../../admin/config/dbcon.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session
    $user_id = $_SESSION['auth_user']['user_id'];
    
    // Check the address type (default or alternative)
    $address_type = isset($_POST['address_type']) ? $_POST['address_type'] : 'default';
    
    if ($address_type === 'default') {
        // For default address
        $full_address = $_POST['full_address'] ?? '';
        $latitude = $_POST['latitude'] ?? null;
        $longitude = $_POST['longitude'] ?? null;
        $zipcode = $_POST['zipcode'] ?? '';
        
        // Update query
        $query = "UPDATE users SET 
                  full_address = ?, 
                  latitude = ?, 
                  longitude = ?, 
                  zipcode = ? 
                  WHERE id = ?";
                  
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sddsi", $full_address, $latitude, $longitude, $zipcode, $user_id);
    } else {
        // For alternative address
        $alt_full_address = $_POST['alt_full_address'] ?? '';
        $alt_latitude = $_POST['alt_latitude'] ?? null;
        $alt_longitude = $_POST['alt_longitude'] ?? null;
        $alt_zipcode = $_POST['alt_zipcode'] ?? '';
        
        // Update query
        $query = "UPDATE users SET 
                  alt_full_address = ?, 
                  alt_latitude = ?, 
                  alt_longitude = ?, 
                  alt_zipcode = ? 
                  WHERE id = ?";
                  
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sddsi", $alt_full_address, $alt_latitude, $alt_longitude, $alt_zipcode, $user_id);
    }
    
    // Execute the query
    if ($stmt->execute()) {
        // Success
        header("Location: ../profile.php?success=1&message=" . urlencode("Address updated successfully!"));
    } else {
        // Error
        header("Location: ../profile.php?error=1&message=" . urlencode("Error updating address: " . $conn->error));
    }
    
    $stmt->close();
    $conn->close();
} else {
    // Not a POST request
    header("Location: ../profile.php");
}
exit();