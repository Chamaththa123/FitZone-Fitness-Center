<?php

define('DB_HOST', 'localhost');     
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');  
define('DB_NAME', 'your_database'); 

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

echo "Connected successfully";

?>