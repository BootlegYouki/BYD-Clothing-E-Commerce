<?php
include '../../admin/config/dbcon.php';
$username = mysqli_real_escape_string($conn, $_GET['username']);
$query = "SELECT id FROM users WHERE username='$username' LIMIT 1";
$result = mysqli_query($conn, $query);
echo json_encode(['exists' => mysqli_num_rows($result) > 0]);
?>