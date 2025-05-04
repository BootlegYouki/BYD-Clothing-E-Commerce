<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../../config/dbcon.php');

// Check database connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

$product_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get product details - join with categories and fabrics tables
$product_query = "SELECT p.*, c.name as category_name, f.name as fabric_name
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 LEFT JOIN fabrics f ON p.fabric_id = f.id
                 WHERE p.id = '$product_id'";
$product_result = mysqli_query($conn, $product_query);

if(!$product_result) {
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

if(mysqli_num_rows($product_result) == 0) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

$product = mysqli_fetch_assoc($product_result);

// For backward compatibility, add category and fabric fields
$product['category'] = $product['category_name'];
$product['fabric'] = $product['fabric_name'];

// Get product images
$primary_image_query = "SELECT image_url FROM product_images WHERE product_id = '$product_id' AND is_primary = 1 LIMIT 1";
$primary_image_result = mysqli_query($conn, $primary_image_query);

if(!$primary_image_result) {
    echo json_encode(['error' => 'Image query failed: ' . mysqli_error($conn)]);
    exit;
}

$primary_image = mysqli_num_rows($primary_image_result) > 0 ? mysqli_fetch_assoc($primary_image_result)['image_url'] : null;

$additional_images_query = "SELECT image_url FROM product_images WHERE product_id = '$product_id' AND is_primary = 0";
$additional_images_result = mysqli_query($conn, $additional_images_query);

if(!$additional_images_result) {
    echo json_encode(['error' => 'Additional images query failed: ' . mysqli_error($conn)]);
    exit;
}

$additional_images = [];
while($image = mysqli_fetch_assoc($additional_images_result)) {
    $additional_images[] = $image['image_url'];
}

// Get sizes and stock
$sizes_query = "SELECT size, stock FROM product_sizes WHERE product_id = '$product_id'";
$sizes_result = mysqli_query($conn, $sizes_query);

if(!$sizes_result) {
    echo json_encode(['error' => 'Sizes query failed: ' . mysqli_error($conn)]);
    exit;
}

$sizes = [];
while($size = mysqli_fetch_assoc($sizes_result)) {
    $sizes[$size['size']] = $size['stock'];
}

// Combine all data
$response = $product;
$response['primary_image'] = $primary_image;
$response['additional_images'] = $additional_images;
$response['sizes'] = $sizes;

// Send response
header('Content-Type: application/json');
echo json_encode($response);
?>