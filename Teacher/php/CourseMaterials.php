<?php

session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Auth guard
$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Please log in as a teacher.'); }

// Resolve teachers.teacher_id for this user, if exists
$teacher_tid = null;
$q = $conn->prepare("SELECT teacher_id FROM teachers WHERE user_id=? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute(); $q->bind_result($teacher_tid); $q->fetch(); $q->close();

$__courses = [];
$sql = "
  SELECT o.id, o.course_title, o.department
  FROM offered_course o
  WHERE (o.teacher_id = ? " . ($teacher_tid ? " OR o.teacher_id = ?" : "") . ")
  ORDER BY o.id DESC
";
if ($teacher_tid) {
  $st = $conn->prepare($sql);
  $st->bind_param("ii", $user_id, $teacher_tid);
} else {
  $st = $conn->prepare("
    SELECT o.id, o.course_title, o.department
    FROM offered_course o
    WHERE o.teacher_id = ?
    ORDER BY o.id DESC
  ");
  $st->bind_param("i", $user_id);
}
$st->execute();
$res = $st->get_result();
while ($row = $res->fetch_assoc()) $__courses[] = $row;
$st->close();

$__selected_course_id = isset($_GET['offered_id']) ? (int)$_GET['offered_id'] : 0;

$__materials = [];
$params = [];
$query = "
  SELECT m.id, m.offered_course_id, m.title, m.file_name, m.file_path, m.uploaded_at,
         o.course_title, o.department
  FROM course_materials m
  JOIN offered_course o ON o.id = m.offered_course_id
  WHERE (o.teacher_id = ? " . ($teacher_tid ? " OR o.teacher_id = ?" : "") . ")
";
if ($__selected_course_id > 0) {
  $query .= " AND m.offered_course_id = ? ";
}
$query .= " ORDER BY m.id DESC";

if ($teacher_tid) {
  if ($__selected_course_id > 0) {
    $st = $conn->prepare($query);
    $st->bind_param("iii", $user_id, $teacher_tid, $__selected_course_id);
  } else {
    $st = $conn->prepare($query);
    $st->bind_param("ii", $user_id, $teacher_tid);
  }
} else {
  if ($__selected_course_id > 0) {
    $st = $conn->prepare($query);
    $st->bind_param("ii", $user_id, $__selected_course_id);
  } else {
    $st = $conn->prepare($query);
    $st->bind_param("i", $user_id);
  }
}
$st->execute();
$res = $st->get_result();
while ($row = $res->fetch_assoc()) $__materials[] = $row;
$st->close();

// messages
$__msg = $_GET['ok'] ?? '';
$__err = $_GET['err'] ?? '';

require_once __DIR__ . '/../view/CourseMaterials.php';
