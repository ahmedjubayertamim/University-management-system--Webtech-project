<?php

include "../view/ManageLibrary.php";

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "universitymanagementsystem";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $bookname = $_POST["bname"];
    $bookAuthor = $_POST["author"];
    $Pubyear = $_POST["year"];
    $bookType = $_POST["type"];
    $book_ststus = $_POST["status"];
    $bookdesc = $_POST["desc"];

    $bookFile = "";
    if(isset($_FILES["ebook"]) && $_FILES["ebook"]["error"] == 0){
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $bookFile = $targetDir . basename($_FILES["ebook"]["name"]);
        move_uploaded_file($_FILES["ebook"]["tmp_name"], $bookFile);
    }

    if(empty($bookname) || empty($bookAuthor) || empty($Pubyear) || empty($bookType) || empty($book_ststus) || empty($bookdesc)){
        echo "All fields must be filled";
    } else {
        $sql = "INSERT INTO addbook (bookname, author, pubYear, type, ebook, status, descText) 
                VALUES ('$bookname', '$bookAuthor', '$Pubyear', '$bookType', '$bookFile', '$book_ststus', '$bookdesc')";
       
        if ($conn->query($sql) === TRUE) {
            echo "Submission successful";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// 3. Close DB
$conn->close();


?>