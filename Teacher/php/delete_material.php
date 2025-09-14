<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Please log in as a teacher.'); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: /Project/Teacher/php/CourseMaterials.php?err=Invalid+id"); exit; }

$teacher_tid = null;
$q = $conn->prepare("SELECT teacher_id FROM teachers WHERE user_id=? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute(); $q->bind_result($teacher_tid); $q->fetch(); $q->close();


$st = $conn->prepare("
  SELECT m.file_path, o.teacher_id, m.offered_course_id
  FROM course_materials m
  JOIN offered_course o ON o.id = m.offered_course_id
  WHERE m.id=?
  LIMIT 1
");
$st->bind_param("i", $id);
$st->execute();
$st->bind_result($file_path, $oc_tid, $offered_id);
if (!$st->fetch()) {
  $st->close();
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=Not+found");
  exit;
}
$st->close();

$allowed = ($oc_tid == $user_id) || ($teacher_tid && $oc_tid == $teacher_tid);
if (!$allowed) {
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=You+do+not+own+this+material");
  exit;
}

$del = $conn->prepare("DELETE FROM course_materials WHERE id=? LIMIT 1");
$del->bind_param("i", $id);
$del->execute(); $del->close();

$abs = $_SERVER['DOCUMENT_ROOT'] . $file_path;
if (is_file($abs)) @unlink($abs);

header("Location: /Project/Teacher/php/CourseMaterials.php?ok=Deleted&offered_id=".$offered_id);
