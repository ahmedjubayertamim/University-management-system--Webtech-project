<?php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') { http_response_code(403); exit('Login as student'); }

$__student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute(); $st->bind_result($__student_id); $st->fetch(); $st->close();
if (!$__student_id) { exit('Student profile not found.'); }

$__selected_course_id = isset($_GET['offered_id']) ? (int)$_GET['offered_id'] : 0;

$__courses = [];
$q = $conn->prepare("
  SELECT oc.id, oc.course_title, oc.department
  FROM student_course_registrations scr
  JOIN offered_course oc ON oc.id = scr.offered_course_id
  WHERE scr.student_id=?
  ORDER BY oc.id DESC
");
$q->bind_param("i", $__student_id);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) $__courses[] = $row;
$q->close();

$__materials = [];
$sql = "
  SELECT m.id, m.offered_course_id, m.title, m.file_name, m.file_path, m.uploaded_at,
         oc.course_title, oc.department
  FROM course_materials m
  JOIN offered_course oc ON oc.id = m.offered_course_id
  WHERE m.offered_course_id IN (
    SELECT offered_course_id FROM student_course_registrations WHERE student_id=?
  )
";
if ($__selected_course_id > 0) $sql .= " AND m.offered_course_id = ? ";
$sql .= " ORDER BY m.id DESC";

if ($__selected_course_id > 0) {
  $st = $conn->prepare($sql);
  $st->bind_param("ii", $__student_id, $__selected_course_id);
} else {
  $st = $conn->prepare($sql);
  $st->bind_param("i", $__student_id);
}
$st->execute();
$res = $st->get_result();
while ($row = $res->fetch_assoc()) $__materials[] = $row;
$st->close();

// Render
require_once __DIR__ . '/../view/CourseMaterials.php';
