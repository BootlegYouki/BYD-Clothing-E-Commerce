<?php

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "dbecomm";

    // Create connection
    $conn = mysqli_connect($host, $username, $password, $database);
    
    //check con
    if(!$conn){
        die("Connection failed: " . mysqli_connect_error());
    }
    else
    {
        echo "Connected successfully";
    }
?>