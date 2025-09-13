<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers_pay.php';

function get_or_create_student_id(mysqli $conn, int $user_id): int {
    $role = $status = null;
    $q = $conn->prepare("SELECT role, status FROM users WHERE id=?");
    $q->bind_param("i", $user_id);
    $q->execute();
    $q->bind_result($role, $status);
    $q->fetch(); $q->close();
    if ($role !== 'student' || $status !== 'enabled') return 0;

    $sid = 0;
    $q = $conn->prepare("SELECT student_id FROM students WHERE user_id=?");
    $q->bind_param("i", $user_id);
    $q->execute(); $q->bind_result($sid);
    if ($q->fetch()) { $q->close(); return (int)$sid; }
    $q->close();

    $ins = $conn->prepare("INSERT INTO students (user_id) VALUES (?)");
    $ins->bind_param("i", $user_id);
    if (!$ins->execute()) { $ins->close(); return 0; }
    $sid = (int)$conn->insert_id; $ins->close();
    return $sid;
}


$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { http_response_code(401); exit("Please log in."); }

$student_id = get_or_create_student_id($conn, $user_id);
if ($student_id <= 0) { http_response_code(403); exit("Not allowed."); }


$course_total = compute_course_total($conn, $student_id);    
$library_fine = compute_library_fine($conn, $student_id);    
$paid_total   = compute_completed_paid($conn, $student_id);  
$total_due    = max(0, $course_total + $library_fine - $paid_total);


$payments = [];
$hist = $conn->prepare("SELECT * FROM payments WHERE student_id=? ORDER BY payment_id DESC");
$hist->bind_param("i", $student_id);
$hist->execute();
$res = $hist->get_result();
while ($row = $res->fetch_assoc()) { $payments[] = $row; }
$hist->close();

$msg = $_GET['msg'] ?? "";
