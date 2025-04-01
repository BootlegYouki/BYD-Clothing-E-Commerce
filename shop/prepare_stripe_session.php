<?php
session_start();
require_once '../admin/config/dbcon.php';

// Retrieve data
$cart_items = json_decode($_POST['cart_items'], true);
$subtotal = $_POST['subtotal'];
$shipping_fee = $_POST['shipping_cost'];
$total = $_POST['total'];

// Store in session
$_SESSION['cart_items'] = $cart_items;
$_SESSION['shipping_fee'] = $shipping_fee;
$_SESSION['order_total'] = $total;

echo json_encode(['success' => true]);
exit;
?>