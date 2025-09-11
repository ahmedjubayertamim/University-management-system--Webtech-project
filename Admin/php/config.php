<?php
$host = "localhost";       
$user = "root";            
$pass = "";                
$dbname = "universitymanagementsystem";  

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}
?>
