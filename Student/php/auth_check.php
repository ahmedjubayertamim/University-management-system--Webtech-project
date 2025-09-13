<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['student_id'])) {

    header("Location: /project/Home/View/login.php");
    exit();
}


$student_id = $_SESSION['student_id'];
$student_name = isset($_SESSION['student_name']) ? $_SESSION['student_name'] : '';
