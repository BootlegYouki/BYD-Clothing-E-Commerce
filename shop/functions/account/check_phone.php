<?php
include '../../../admin/config/dbcon.php';
$phone_number = mysqli_real_escape_string($conn, $_GET['phone_number']);
$query = "SELECT id FROM users WHERE phone_number='$phone_number' LIMIT 1";
$result = mysqli_query($conn, $query);
echo json_encode(['exists' => mysqli_num_rows($result) > 0]);
?>