<?php
session_start();
require_once '../../config/dbcon.php';

// Check if user is logged in as admin
if(!isset($_SESSION['auth']) || $_SESSION['auth_role'] != 1) {
    header("Location: ../../../shop/index.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addCarouselImage();
            break;
        case 'delete':
            deleteCarouselImage();
            break;
        case 'toggle':
            toggleImageStatus();
            break;
        default:
            $_SESSION['message'] = "Invalid action specified";
            $_SESSION['message_type'] = "danger";
            header("Location: ../../homepage-customize.php");
            exit();
    }
}

function addCarouselImage() {
    global $conn;
    
    $successCount = 0;
    $errorCount = 0;
    $lastInsertedId = 0;
    
    // Handle image uploads
    if (isset($_FILES['carousel_image']) && !empty($_FILES['carousel_image']['name'][0])) {
        $targetDir = "../../../uploads/carousel/";
        
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        mysqli_query($conn, "UPDATE carousel_images SET is_active = 1");
        
        // Count total files
        $countFiles = count($_FILES['carousel_image']['name']);
        
        // Loop through each file
        for($i = 0; $i < $countFiles; $i++) {
            $fileName = time() . '_' . basename($_FILES["carousel_image"]["name"][$i]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            // Allow certain file formats
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
            if (in_array(strtolower($fileType), $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES["carousel_image"]["tmp_name"][$i], $targetFilePath)) {
                    $image_path = 'uploads/carousel/' . $fileName;
                    
                    // Insert into database - only first uploaded image will be active (is_active = 1)
                    $query = "INSERT INTO carousel_images (image_path, is_active) VALUES ('$image_path', 1)";
                    
                    if (mysqli_query($conn, $query)) {
                        $successCount++;
                        $lastInsertedId = mysqli_insert_id($conn);
                    } else {
                        $errorCount++;
                    }
                } else {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
        }
        
        if ($successCount > 0) {
            $message = "$successCount carousel image(s) added successfully" . 
                      ($errorCount > 0 ? ", but $errorCount failed to upload." : ".");
            $type = "success";
        } else {
            $message = "Error uploading images.";
            $type = "error";
        }
    } else {
        $message = "Please select at least one image for the carousel.";
        $type = "error";
    }
    
    // Use toast notification instead of session message
    header("Location: ../../homepage-customize.php?toast_message=" . urlencode($message) . "&toast_type=" . $type);
    exit();
}

function deleteCarouselImage() {
    global $conn;
    
    $image_id = (int)$_POST['image_id'];
    
    // Get the image to delete it
    $query = "SELECT image_path FROM carousel_images WHERE id = $image_id";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $image_path = '../../../' . $row['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete from database
    $query = "DELETE FROM carousel_images WHERE id = $image_id";
    
    if (mysqli_query($conn, $query)) {
        // Redirect with toast parameters instead of using session
        header("Location: ../../homepage-customize.php?toast_message=Carousel+image+deleted+successfully&toast_type=success");
    } else {
        header("Location: ../../homepage-customize.php?toast_message=Error+deleting+image&toast_type=error");
    }
    
    exit();
}

function toggleImageStatus() {
    global $conn;
    
    $image_id = (int)$_POST['image_id'];
    $is_active = (int)$_POST['is_active'];
    
    $query = "UPDATE carousel_images SET is_active = $is_active WHERE id = $image_id";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    
    exit();
}
?>