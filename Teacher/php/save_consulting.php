<?php
session_start();
require_once __DIR__ . '/config.php';

$teacher_id = (int)($_POST['teacher_id'] ?? 0);
$day        = $_POST['day_of_week'] ?? '';
$start      = $_POST['start_time'] ?? '';
$end        = $_POST['end_time'] ?? '';

if ($teacher_id && $day && $start && $end) {
    $q = $conn->prepare("INSERT INTO consulting_hours (teacher_id, day_of_week, start_time, end_time) VALUES (?,?,?,?)");
    $q->bind_param("isss", $teacher_id, $day, $start, $end);
    if ($q->execute()) {
        header("Location: ../view/SetConsulting.php?msg=Added successfully");
    } else {
        header("Location: ../view/SetConsulting.php?msg=Failed to add");
    }
    $q->close();
} else {
    header("Location: ../view/SetConsulting.php?msg=Invalid input");
}
