<?php

    $host = "sql206.infinityfree.com";
    $username = "if0_38370362";
    $password = "Vj4Ur3D61Hb";
    $database = "if0_38370362_dbecomm";

    // Create connection
    $conn = mysqli_connect($host, $username, $password, $database);
    
    //check con
    if(!$conn){
        die("Connection failed: " . mysqli_connect_error());
    }
?>