<?php
$host = "localhost";       
$user = "root";            
$pass = "";                
$dbname = "universitydb";  

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}
?>
