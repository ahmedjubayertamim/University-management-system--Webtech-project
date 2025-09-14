<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Please log in as a teacher.'); }

$offered_course_id = isset($_POST['offered_course_id']) ? (int)$_POST['offered_course_id'] : 0;
$title = trim($_POST['title'] ?? '');

if ($offered_course_id <= 0 || $title==='') {
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=Missing+course+or+title");
  exit;
}

$teacher_tid = null;
$q = $conn->prepare("SELECT teacher_id FROM teachers WHERE user_id=? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute(); $q->bind_result($teacher_tid); $q->fetch(); $q->close();

$ok = false;
$chk = $conn->prepare("
  SELECT 1 FROM offered_course
  WHERE id=? AND (teacher_id=? " . ($teacher_tid ? " OR teacher_id=? " : "") . ")
  LIMIT 1
");
if ($teacher_tid) {
  $chk->bind_param("iii", $offered_course_id, $user_id, $teacher_tid);
} else {
  $chk->bind_param("ii", $offered_course_id, $user_id);
}
$chk->execute();
$chk->bind_result($dummy);
$ok = $chk->fetch() ? true : false;
$chk->close();

if (!$ok) {
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=You+are+not+assigned+to+this+course");
  exit;
}

// File checks
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=No+file+uploaded");
  exit;
}

$allowed = ['pdf','doc','docx','ppt','pptx','zip'];
$origName = $_FILES['file']['name'];
$tmpPath  = $_FILES['file']['tmp_name'];
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=Invalid+file+type");
  exit;
}

$baseDir = $_SERVER['DOCUMENT_ROOT'] . '/Project/uploads/materials';
if (!is_dir($baseDir)) { @mkdir($baseDir, 0775, true); }

$slug = preg_replace('/[^a-zA-Z0-9-_]/','_', pathinfo($origName, PATHINFO_FILENAME));
$unique = $slug . '_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
$destAbs = $baseDir . '/' . $unique;
$destRel = '/Project/uploads/materials/' . $unique;

if (!move_uploaded_file($tmpPath, $destAbs)) {
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=Failed+to+save+file");
  exit;
}


$store_tid = $teacher_tid ?: $user_id;

$ins = $conn->prepare("INSERT INTO course_materials (offered_course_id, teacher_id, title, file_name, file_path) VALUES (?,?,?,?,?)");
$ins->bind_param("iisss", $offered_course_id, $store_tid, $title, $origName, $destRel);
if (!$ins->execute()) {
  @unlink($destAbs);
  header("Location: /Project/Teacher/php/CourseMaterials.php?err=DB+insert+failed");
  exit;
}
$ins->close();

header("Location: /Project/Teacher/php/CourseMaterials.php?ok=Uploaded+successfully&offered_id=".$offered_course_id);
