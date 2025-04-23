<?php
    $host = "v02yrnuhptcod7dk.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
    $username = "pwuqrmmg86eufl2s";
    $password = "m3tz7k2rogpfrdls";
    $database = "c3248bm8zvavug0p";

    // Create connection
    $conn = mysqli_connect($host, $username, $password, $database);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>