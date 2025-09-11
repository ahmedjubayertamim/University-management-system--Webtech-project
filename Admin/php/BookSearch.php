<?php
// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "universitymanagementsystem";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection Failed: " . $conn->connect_error);

// Handle search
$searchQuery = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = $conn->real_escape_string($_GET['search']);
    $searchQuery = "WHERE bookname LIKE '%$searchTerm%' OR author LIKE '%$searchTerm%' OR type LIKE '%$searchTerm%'";
}

$booksResult = $conn->query("SELECT * FROM addbook $searchQuery ORDER BY id ASC");
?>