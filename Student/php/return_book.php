<?php
session_start();
include("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $student_id = $_POST['student_id'];

    // Update book status
    $sql = "UPDATE addbook SET status='Available' WHERE bookid='$book_id'";
    mysqli_query($conn, $sql);

    // Update borrow_records table (set return date)
    $sql2 = "UPDATE borrow_records SET return_date=NOW() WHERE bookid='$book_id' AND student_id='$student_id' AND return_date IS NULL";
    mysqli_query($conn, $sql2);

    header("Location: StudentLibrary.php");
    exit();
}
