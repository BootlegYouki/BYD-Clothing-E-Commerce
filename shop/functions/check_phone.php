<?php
include '../../admin/config/dbcon.php';
$phone = mysqli_real_escape_string($conn, $_GET['phone']);
$query = "SELECT id FROM users WHERE phone_number='$phone' LIMIT 1";
$result = mysqli_query($conn, $query);
echo json_encode(['exists' => mysqli_num_rows($result) > 0]);
?>