<?php
include '../../../admin/config/dbcon.php';
$email = mysqli_real_escape_string($conn, $_GET['email']);
$query = "SELECT id FROM users WHERE email='$email' LIMIT 1";
$result = mysqli_query($conn, $query);
echo json_encode(['exists' => mysqli_num_rows($result) > 0]);
?>