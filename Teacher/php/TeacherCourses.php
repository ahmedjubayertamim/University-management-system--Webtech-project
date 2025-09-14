<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { die('Please log in.'); }

$check = $conn->prepare("
    SELECT role, status, first_name, last_name, email 
    FROM users WHERE id=? LIMIT 1
");
$check->bind_param("i", $user_id);
$check->execute();
$check->bind_result($rRole, $rStatus, $fn, $ln, $em);
if (!$check->fetch()) { $check->close(); die('User not found.'); }
$check->close();

if ($rRole !== 'teacher' || $rStatus !== 'enabled') {
    die('Not a teacher account.');
}

$teacherName = trim(($fn ?? '') . ' ' . ($ln ?? ''));

$cstmt = $conn->prepare("
  SELECT oc.id, oc.department, oc.course_title, oc.student_capacity, oc.student_count,
         oc.course_fee, oc.class_time, oc.class_date, oc.duration
  FROM offered_course oc
  WHERE oc.teacher_id = ?
  ORDER BY oc.id DESC
");
$cstmt->bind_param("i", $user_id);
$cstmt->execute();
$courses = $cstmt->get_result();

include __DIR__ . '/../view/TeacherCourses.php';
