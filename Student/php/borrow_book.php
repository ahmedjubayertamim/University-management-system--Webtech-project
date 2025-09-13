<?php
session_start();
include("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $student_id = $_POST['student_id'];

    // Update book status
    $sql = "UPDATE addbook SET status='Borrowed' WHERE bookid='$book_id'";
    mysqli_query($conn, $sql);

    // You can create a borrow_records table for tracking
    $sql2 = "INSERT INTO borrow_records (bookid, student_id, borrow_date) VALUES ('$book_id','$student_id',NOW())";
    mysqli_query($conn, $sql2);

    header("Location: StudentLibrary.php");
    exit();
}
