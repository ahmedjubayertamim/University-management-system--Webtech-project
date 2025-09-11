<?php


$host = "localhost";
$user = "root";
$pass = "";
$dbname = "universitymanagementsystem";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $course = $_POST['course'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

   $sql = "INSERT INTO add_drop_deadline (department, course, start_date, end_date) 
            VALUES ('$department', '$course', '$start_date', '$end_date')";

    if ($conn->query($sql) === TRUE) {
        $message = "Deadline set successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
  include "../view/ManageCourse.php";
  $conn->close();
}

?>