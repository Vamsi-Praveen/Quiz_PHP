<?php
    $server_address = "127.0.0.1:3307";
    $username = "root";
    $password = "";
    $database = "quiz";

    try {
        $conn = @new mysqli($server_address,$username,$password,$database);
        if($conn->connect_error){
            die("Database Connection failed");
            exit();
        }
    } catch (Exception $e) {
        echo 'Db Connection failed';
    }
?>