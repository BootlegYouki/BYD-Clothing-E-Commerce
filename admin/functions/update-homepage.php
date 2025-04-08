<?php
session_start();
require_once '../config/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form fields
    $fields = [
        'hero_tagline', 'hero_heading', 'hero_description',
        'banner_title', 'banner_description', 'banner_list',
        'new_release_title', 'new_release_description',
        'tshirt_title', 'tshirt_description',
        'longsleeve_title', 'longsleeve_description'
    ];
    
    // Fields that should have newlines converted to <br> tags
    $title_fields = ['hero_heading', 'banner_title', 'new_release_title', 'tshirt_title', 'longsleeve_title'];
    
    $checkboxes = ['show_new_release', 'show_tshirt', 'show_longsleeve'];
    
    // Update text fields
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            
            // Convert newlines to <br> for title fields
            if (in_array($field, $title_fields)) {
                $value = str_replace("\r\n", "\n", $value); // Normalize line endings
                $value = str_replace("\n", "<br>", $value); // Convert newlines to <br>
            }
            $value = preg_replace('/\*(.*?)\*/s', '<span>$1</span>', $value);
            $value = mysqli_real_escape_string($conn, $value);
            
            // Check if setting exists
            $check_query = "SELECT COUNT(*) as count FROM homepage_settings WHERE setting_key = '$field'";
            $check_result = mysqli_query($conn, $check_query);
            $row = mysqli_fetch_assoc($check_result);
            
            if ($row['count'] > 0) {
                // Update existing setting
                $query = "UPDATE homepage_settings SET setting_value = '$value' WHERE setting_key = '$field'";
            } else {
                // Insert new setting
                $query = "INSERT INTO homepage_settings (setting_key, setting_value) VALUES ('$field', '$value')";
            }
            
            mysqli_query($conn, $query);
        }
    }
    
    // Update checkbox fields (0 or 1)
    foreach ($checkboxes as $checkbox) {
        $value = isset($_POST[$checkbox]) ? '1' : '0';
        
        // Check if setting exists
        $check_query = "SELECT COUNT(*) as count FROM homepage_settings WHERE setting_key = '$checkbox'";
        $check_result = mysqli_query($conn, $check_query);
        $row = mysqli_fetch_assoc($check_result);
        
        if ($row['count'] > 0) {
            // Update existing setting
            $query = "UPDATE homepage_settings SET setting_value = '$value' WHERE setting_key = '$checkbox'";
        } else {
            // Insert new setting
            $query = "INSERT INTO homepage_settings (setting_key, setting_value) VALUES ('$checkbox', '$value')";
        }
        
        mysqli_query($conn, $query);
    }
    
    // Set success message
    $_SESSION['content_message'] = 'Homepage content updated successfully!';
    
    // Redirect back to homepage customization
    header('Location: ../homepage-customize.php');
    exit();
}
?>