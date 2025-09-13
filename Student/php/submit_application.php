<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') { http_response_code(403); exit('Forbidden'); }

$student_id = (int)($_POST['student_id'] ?? 0);
$teacher_id = (int)($_POST['teacher_id'] ?? 0);
$type       = trim($_POST['application_type'] ?? '');
$subject    = trim($_POST['subject'] ?? '');
$details    = trim($_POST['details'] ?? '');
$start      = trim($_POST['start_date'] ?? '');
$end        = trim($_POST['end_date'] ?? '');

if (!$student_id || !$teacher_id || $type==='' || $subject==='' || $details==='' || $start==='' || $end==='') {
  header("Location: ../view/StudentApplication.php?err=1"); exit;
}

/* Reason field packs type + subject + details (your table has single text column) */
$reason = "Type: {$type}\nSubject: {$subject}\n\n{$details}";

/* (Optional) ensure this teacher is from student's registered courses */
$ok = 0;
$chk = $conn->prepare("
  SELECT 1
  FROM student_course_registrations scr
  JOIN offered_course o ON o.id = scr.offered_course_id
  WHERE scr.student_id=? AND o.teacher_id=?
  LIMIT 1
");
$chk->bind_param("ii", $student_id, $teacher_id);
$chk->execute(); $chk->bind_result($ok); $chk->fetch(); $chk->close();
if (!$ok) { header("Location: ../view/StudentApplication.php?err=1"); exit; }

$ins = $conn->prepare("
  INSERT INTO leave_requests (student_id, teacher_id, reason, start_date, end_date, status)
  VALUES (?,?,?,?,?, 'pending')
");
$ins->bind_param("issss", $student_id, $teacher_id, $reason, $start, $end);
$ok = $ins->execute(); $ins->close();

header("Location: ../view/StudentApplication.php?".($ok ? "ok=1":"err=1"));
exit;
