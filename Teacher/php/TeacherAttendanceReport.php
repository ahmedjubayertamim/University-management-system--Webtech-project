<?php
session_start();
require_once __DIR__ . '/config.php';


$user_id = (int)($_SESSION['user_id'] ?? 0);
$role = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') {
    http_response_code(403);
    exit('Please log in as a teacher.');
}

$courses = [];
$stmt = $conn->prepare("SELECT id, course_title, department FROM offered_course WHERE teacher_id=? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$courses = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');
if ($from === '') $from = date('Y-m-01');
if ($to === '') $to = date('Y-m-d');

$course_ok = false;
$course_title = '';
if ($course_id > 0) {
    $chk = $conn->prepare("SELECT course_title FROM offered_course WHERE id=? AND teacher_id=? LIMIT 1");
    $chk->bind_param("ii", $course_id, $user_id);
    $chk->execute();
    $chk->bind_result($course_title);
    if ($chk->fetch()) $course_ok = true;
    $chk->close();
}

$rows = [];
if ($course_ok) {
    $fromSql = min($from, $to);
    $toSql = max($from, $to);
    $q = $conn->prepare("
        SELECT a.attendance_id, a.date, a.status,
               s.student_id,
               u.first_name, u.last_name, u.email
        FROM attendance a
        JOIN students s ON s.student_id = a.student_id
        JOIN users u    ON u.id = s.user_id
        WHERE a.course_id = ?
          AND a.date BETWEEN ? AND ?
        ORDER BY a.date DESC, u.first_name, u.last_name
    ");
    $q->bind_param("iss", $course_id, $fromSql, $toSql);
    $q->execute();
    $r = $q->get_result();
    $rows = $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
    $q->close();
}

include dirname(__DIR__) . '/view/TeacherAttendanceReport.php';
