<?php
session_start();
require_once __DIR__ . '/auth_student.php';
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') {
    http_response_code(403);
    exit('Login as student');
}

$student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$st->bind_result($student_id);
$st->fetch();
$st->close();
if (!$student_id) {
    exit('Student profile not found.');
}

$sql = "
  SELECT lr.leave_id, lr.reason, lr.start_date, lr.status,
         u.first_name, u.last_name
  FROM leave_requests lr
  JOIN teachers t ON t.teacher_id = lr.teacher_id
  JOIN users u    ON u.id = t.user_id
  WHERE lr.student_id = ?
  ORDER BY lr.start_date DESC, lr.leave_id DESC
";
$q = $conn->prepare($sql);
$q->bind_param("i", $student_id);
$q->execute();
$res  = $q->get_result();
$rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$q->close();

include __DIR__ . '/../view/MyApplications.php';
