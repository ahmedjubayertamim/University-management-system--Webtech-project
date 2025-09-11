<?php
include "../PHP/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'];
    $course_title = $_POST['course_title'];
    $student_capacity = $_POST['student_capacity'];
    $class_time = $_POST['class_time'];
    $class_date = $_POST['class_date'];
    $duration = $_POST['duration'];
   
   $sql = "INSERT INTO offered_course (department, course_title, student_capacity,student_count, class_time, class_date, duration)
        VALUES ('$department', '$course_title', '$student_capacity',0, '$class_time', '$class_date', '$duration')";


    if ($conn->query($sql) === TRUE) {
        header("Location: ../view/OfferCourse.php"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$search = "";
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM offered_course 
            WHERE course_title LIKE '%$search%' OR department LIKE '%$search%'
            ORDER BY id ASC";
} else {
    $sql = "SELECT * FROM offered_course ORDER BY id ASC";
}

$result = $conn->query($sql);
if(!$result){
    die("Query Error: " . $conn->error);
}
?>
