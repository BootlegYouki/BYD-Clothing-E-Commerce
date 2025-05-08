<?php
session_start();
include('../../config/dbcon.php');

// Function to handle product addition
if(isset($_POST['add_product'])) {
    // Get form data
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $original_price = mysqli_real_escape_string($conn, $_POST['original_price']);
    
    // Change: Set discount_price to NULL when empty instead of using original price
    if(!empty($_POST['discount_price'])) {
        $discount_price = mysqli_real_escape_string($conn, $_POST['discount_price']);
    } else {
        $discount_price = "NULL"; // SQL NULL value for direct insertion into query
    }
    
    // Calculate discount percentage based on prices
    $discount_percentage = 0;
    if($original_price > 0 && !empty($_POST['discount_price']) && $_POST['discount_price'] < $original_price) {
        $discount_percentage = round((($original_price - $_POST['discount_price']) / $original_price) * 100);
    }

    // Process category selection - make it required
    if(!isset($_POST['category']) || empty($_POST['category'])) {
        $_SESSION['message'] = "Category selection is required";
        $_SESSION['message_type'] = "error";
        header('Location: ../../add-product.php');
        exit();
    }
    
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Process fabric selection
    $fabric = '';
    if(isset($_POST['fabric']) && !empty($_POST['fabric'])) {
        // Use the selected fabric
        $fabric = mysqli_real_escape_string($conn, $_POST['fabric']);
    }
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new_release = isset($_POST['is_new_release']) ? 1 : 0;
    $stock = $_POST['stock'];
    
    // Validate required fields
    if(empty($name) || empty($original_price) || empty($category)) {
        $_SESSION['message'] = "All required fields must be filled";
        $_SESSION['message_type'] = "error";
        header('Location: ../../add-product.php');
        exit();
    }

    // Begin transaction for multiple table inserts
    mysqli_begin_transaction($conn);
    
    try {
        // Insert into products table - updated to handle NULL discount_price
        $product_query = "INSERT INTO products (sku, name, description, original_price, discount_percentage, discount_price, category, fabric, is_featured, is_new_release) 
                          VALUES ('$sku', '$name', '$description', '$original_price', '$discount_percentage', " . ($discount_price === "NULL" ? "NULL" : "'$discount_price'") . ", '$category', '$fabric', '$is_featured', '$is_new_release')";
        $product_result = mysqli_query($conn, $product_query);
        
        if(!$product_result) {
            throw new Exception("Error inserting product: " . mysqli_error($conn));
        }
        
        // Get the inserted product ID
        $product_id = mysqli_insert_id($conn);
        
        // Process and upload images
        // Create directory if it doesn't exist
        $upload_dir = '../../../uploads/products/' . $product_id . '/';
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Process primary image
        if(isset($_FILES['primary_image']) && $_FILES['primary_image']['name'][0] != '') {
            $file = $_FILES['primary_image']['tmp_name'][0];
            $filename = $_FILES['primary_image']['name'][0];
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = 'primary_' . time() . '.' . $extension;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($file, $upload_path)) {
                $db_image_path = 'uploads/products/' . $product_id . '/' . $new_filename;
                $image_query = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES ('$product_id', '$db_image_path', 1)";
                if(!mysqli_query($conn, $image_query)) {
                    throw new Exception("Error saving primary image: " . mysqli_error($conn));
                }
            } else {
                throw new Exception("Error uploading primary image");
            }
        }
        
        // Process additional images
        if(isset($_FILES['additional_images']) && $_FILES['additional_images']['name'][0] != '') {
            $files = $_FILES['additional_images'];
            $file_count = count($files['name']);
            
            // Limit to 4 additional images instead of 3
            $file_count = min($file_count, 4);
            
            for($i = 0; $i < $file_count; $i++) {
                if($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp_name = $files['tmp_name'][$i];
                    $filename = $files['name'][$i];
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_filename = 'additional_' . $i . '_' . time() . '.' . $extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if(move_uploaded_file($tmp_name, $upload_path)) {
                        $db_image_path = 'uploads/products/' . $product_id . '/' . $new_filename;
                        $image_query = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES ('$product_id', '$db_image_path', 0)";
                        if(!mysqli_query($conn, $image_query)) {
                            throw new Exception("Error saving additional image: " . mysqli_error($conn));
                        }
                    } else {
                        throw new Exception("Error uploading additional image $i");
                    }
                }
            }
        }
        
        // Add sizes and stock - Updated to include XXXL
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        foreach($sizes as $size) {
            $stock_qty = isset($stock[$size]) ? (int)$stock[$size] : 0;
            $size_query = "INSERT INTO product_sizes (product_id, size, stock) VALUES ('$product_id', '$size', '$stock_qty')";
            if(!mysqli_query($conn, $size_query)) {
                throw new Exception("Error saving size $size: " . mysqli_error($conn));
            }
        }
        
        // If everything is successful, commit the transaction
        mysqli_commit($conn);
        $_SESSION['message'] = "Product added successfully";
        header('Location: ../../add-product.php');
        exit();
        
    } catch (Exception $e) {
        // If there's an error, roll back the transaction
        mysqli_rollback($conn);
        $_SESSION['message'] = "Something went wrong: " . $e->getMessage();
        header('Location: ../../add-product.php');
        exit();
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'delete_product' && isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Start transaction for multiple table deletions
    mysqli_begin_transaction($conn);
    
    try {
        // Delete product sizes
        $delete_sizes_query = "DELETE FROM product_sizes WHERE product_id = '$product_id'";
        mysqli_query($conn, $delete_sizes_query);
        
        // Get image paths to delete files
        $images_query = "SELECT image_url FROM product_images WHERE product_id = '$product_id'";
        $images_result = mysqli_query($conn, $images_query);
        $image_paths = [];
        
        while($image = mysqli_fetch_assoc($images_result)) {
            // Store CORRECT image paths for file deletion
            $image_paths[] = '../../../' . $image['image_url'];
        }
        
        // Delete product images from database
        $delete_images_query = "DELETE FROM product_images WHERE product_id = '$product_id'";
        mysqli_query($conn, $delete_images_query);
        
        // Delete the product
        $delete_product_query = "DELETE FROM products WHERE id = '$product_id'";
        $delete_product_result = mysqli_query($conn, $delete_product_query);
        
        if($delete_product_result) {
            // Commit the transaction
            mysqli_commit($conn);
            
            // Delete physical image files with proper error handling
            foreach($image_paths as $path) {
                if(file_exists($path)) {
                    if(!unlink($path)) {
                        // Log failure but continue with other files
                        error_log("Failed to delete file: $path");
                    }
                }
            }
            
            // Delete product directory if it exists
            $product_dir = '../../../uploads/products/' . $product_id;
            if(is_dir($product_dir)) {
                // Remove any remaining files in directory
                $files = glob($product_dir . '/*');
                foreach($files as $file) {
                    if(is_file($file)) {
                        unlink($file);
                    }
                }
                
                // Try to remove directory
                if(!rmdir($product_dir)) {
                    error_log("Failed to delete directory: $product_dir");
                }
            }
            
            $_SESSION['message'] = "Product deleted successfully";
            $_SESSION['message_type'] = "success"; // Add this line to set message type to success
        } else {
            throw new Exception("Error deleting product");
        }
    } catch (Exception $e) {
        // Rollback transaction if error occurs
        mysqli_rollback($conn);
        $_SESSION['message'] = "Failed to delete product: " . $e->getMessage();
        $_SESSION['message_type'] = "error"; // Add this line for consistency
    }
    
    // Redirect back to products page
    header('Location: ../../products.php');
    exit();
}

if(isset($_POST['update_product'])) {
    // Get form data
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $original_price = mysqli_real_escape_string($conn, $_POST['original_price']);
    
    // Change: Set discount_price to NULL when empty instead of using original price
    if(!empty($_POST['discount_price'])) {
        $discount_price = mysqli_real_escape_string($conn, $_POST['discount_price']);
    } else {
        $discount_price = "NULL"; // SQL NULL value for direct insertion into query
    }
    
    // Calculate discount percentage based on prices
    $discount_percentage = 0;
    if($original_price > 0 && !empty($_POST['discount_price']) && $_POST['discount_price'] < $original_price) {
        $discount_percentage = round((($original_price - $_POST['discount_price']) / $original_price) * 100);
    }
    
    // Process category selection - make it required
    if(!isset($_POST['category']) || empty($_POST['category'])) {
        $_SESSION['message'] = "Category selection is required";
        header('Location: ../../edit-product.php?id=' . $product_id);
        exit();
    }
    
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Process fabric selection
    $fabric = '';
    if(isset($_POST['fabric']) && !empty($_POST['fabric'])) {
        // Use the selected fabric
        $fabric = mysqli_real_escape_string($conn, $_POST['fabric']);
    }
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_new_release = isset($_POST['is_new_release']) ? 1 : 0;
    $stock = $_POST['stock'];
    
    // Validate required fields
    if(empty($name) || empty($original_price) || empty($category)) {
        $_SESSION['message'] = "All required fields must be filled";
        header('Location: ../../edit-product.php?id=' . $product_id);
        exit();
    }

    // Begin transaction for multiple table updates
    mysqli_begin_transaction($conn);
    
    try {
        if (!empty($_POST['deleted_images'])) {
            $deleted_image_ids = explode(',', $_POST['deleted_images']);
            
            foreach ($deleted_image_ids as $image_id) {
                $image_id = mysqli_real_escape_string($conn, $image_id);
                
                // Get image path before deleting
                $image_query = "SELECT image_url FROM product_images WHERE id = '$image_id'";
                $image_result = mysqli_query($conn, $image_query);
                
                if (mysqli_num_rows($image_result) > 0) {
                    $image = mysqli_fetch_assoc($image_result);
                    $image_path = '../../../' . $image['image_url'];
                    
                    // Delete from database
                    $delete_query = "DELETE FROM product_images WHERE id = '$image_id'";
                    if (!mysqli_query($conn, $delete_query)) {
                        throw new Exception("Error deleting image: " . mysqli_error($conn));
                    }
                    
                    // Try to delete the physical file
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }
        }
        // Update product in products table - updated to handle NULL discount_price
        $product_query = "UPDATE products SET 
                            sku = '$sku', 
                            name = '$name', 
                            description = '$description', 
                            original_price = '$original_price', 
                            discount_percentage = '$discount_percentage', 
                            discount_price = " . ($discount_price === "NULL" ? "NULL" : "'$discount_price'") . ", 
                            category = '$category',
                            fabric = '$fabric',
                            is_featured = '$is_featured',
                            is_new_release = '$is_new_release'
                          WHERE id = '$product_id'";
                          
        $product_result = mysqli_query($conn, $product_query);
        
        if(!$product_result) {
            throw new Exception("Error updating product: " . mysqli_error($conn));
        }
        
        // Update stock information - Updated to include XXXL
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        foreach($sizes as $size) {
            $stock_qty = isset($stock[$size]) ? (int)$stock[$size] : 0;
            
            // Check if size exists for this product
            $check_query = "SELECT * FROM product_sizes WHERE product_id = '$product_id' AND size = '$size'";
            $check_result = mysqli_query($conn, $check_query);
            
            if(mysqli_num_rows($check_result) > 0) {
                // Update existing size record
                $update_query = "UPDATE product_sizes SET stock = '$stock_qty' WHERE product_id = '$product_id' AND size = '$size'";
                if(!mysqli_query($conn, $update_query)) {
                    throw new Exception("Error updating size $size: " . mysqli_error($conn));
                }
            } else {
                // Insert new size record
                $insert_query = "INSERT INTO product_sizes (product_id, size, stock) VALUES ('$product_id', '$size', '$stock_qty')";
                if(!mysqli_query($conn, $insert_query)) {
                    throw new Exception("Error adding size $size: " . mysqli_error($conn));
                }
            }
        }
        
        // Handle primary image upload if provided
        if(isset($_FILES['primary_image']) && $_FILES['primary_image']['name'][0] != '') {
            $primary_image = $_FILES['primary_image'];
            $primary_image_name = $primary_image['name'][0];
            $primary_image_tmp = $primary_image['tmp_name'][0];
            $primary_image_error = $primary_image['error'][0];
            
            // Check for errors
            if($primary_image_error === 0) {
                // Create directory if it doesn't exist
                $upload_dir = "../../../uploads/products/$product_id/";
                if(!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique name for image
                $image_ext = pathinfo($primary_image_name, PATHINFO_EXTENSION);
                $new_image_name = uniqid('primary_') . "." . $image_ext;
                $image_path = $upload_dir . $new_image_name;
                
                // Upload image
                if(move_uploaded_file($primary_image_tmp, $image_path)) {
                    $relative_path = "uploads/products/$product_id/$new_image_name";
                    
                    // Mark any existing primary images as non-primary
                    $update_primary_query = "UPDATE product_images SET is_primary = 0 WHERE product_id = '$product_id' AND is_primary = 1";
                    mysqli_query($conn, $update_primary_query);
                    
                    // Insert new primary image
                    $image_query = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES ('$product_id', '$relative_path', 1)";
                    if(!mysqli_query($conn, $image_query)) {
                        throw new Exception("Error saving primary image: " . mysqli_error($conn));
                    }
                } else {
                    throw new Exception("Error uploading primary image");
                }
            }
        }
        
        // Handle additional images upload if provided
        if(isset($_FILES['additional_images']) && $_FILES['additional_images']['name'][0] != '') {
            $additional_images = $_FILES['additional_images'];
            $additional_image_count = count($additional_images['name']);
            
            // Limit to 4 additional images instead of 3
            $additional_image_count = min($additional_image_count, 4);
            
            for($i = 0; $i < $additional_image_count; $i++) {
                $additional_image_name = $additional_images['name'][$i];
                $additional_image_tmp = $additional_images['tmp_name'][$i];
                $additional_image_error = $additional_images['error'][$i];
                
                // Check for errors
                if($additional_image_error === 0) {
                    // Create directory if it doesn't exist
                    $upload_dir = "../../../uploads/products/$product_id/";
                    if(!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique name for image
                    $image_ext = pathinfo($additional_image_name, PATHINFO_EXTENSION);
                    $new_image_name = uniqid('add_') . "." . $image_ext;
                    $image_path = $upload_dir . $new_image_name;
                    
                    // Upload image
                    if(move_uploaded_file($additional_image_tmp, $image_path)) {
                        $relative_path = "uploads/products/$product_id/$new_image_name";
                        
                        // Insert additional image
                        $image_query = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES ('$product_id', '$relative_path', 0)";
                        if(!mysqli_query($conn, $image_query)) {
                            throw new Exception("Error saving additional image: " . mysqli_error($conn));
                        }
                    } else {
                        throw new Exception("Error uploading additional image");
                    }
                }
            }
        }
        
        // If everything is successful, commit the transaction
        mysqli_commit($conn);
        $_SESSION['message'] = "Product updated successfully";
        $_SESSION['message_type'] = "success"; // Add message type for proper styling
        header('Location: ../../products.php');
        exit();
        
    } catch (Exception $e) {
        // If there's an error, roll back the transaction
        mysqli_rollback($conn);
        $_SESSION['message'] = "Something went wrong: " . $e->getMessage();
        header('Location: ../../edit-product.php?id=' . $product_id);
        exit();
    }
}
?>