<?php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function get_or_create_student_id(mysqli $conn, int $user_id): int {
  $role=null; $status=null;
  $q=$conn->prepare("SELECT role, status FROM users WHERE id=?");
  $q->bind_param("i", $user_id);
  $q->execute(); $q->bind_result($role, $status); $q->fetch(); $q->close();
  if ($role !== 'student' || $status !== 'enabled') return 0;

  $sid=0;
  $q=$conn->prepare("SELECT student_id FROM students WHERE user_id=?");
  $q->bind_param("i", $user_id);
  $q->execute(); $q->bind_result($sid);
  if ($q->fetch()) { $q->close(); return (int)$sid; }
  $q->close();

  $ins=$conn->prepare("INSERT INTO students (user_id) VALUES (?)");
  $ins->bind_param("i", $user_id);
  if (!$ins->execute()) { $ins->close(); return 0; }
  $new = (int)$conn->insert_id; $ins->close();
  return $new;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { http_response_code(403); exit("Please log in."); }

$__student_id = get_or_create_student_id($conn, $user_id);
if ($__student_id <= 0) { http_response_code(403); exit("Not allowed."); }

$__msg = $_GET['msg'] ?? "";

$winRow = $conn->query("SELECT id, name FROM tpe_windows WHERE status='open' ORDER BY id DESC LIMIT 1");
$__win = $winRow ? $winRow->fetch_assoc() : null;
$__window_id = $__win['id'] ?? null;

$__courses = [];
$sql = "SELECT oc.id, oc.course_title, oc.department, oc.class_time, oc.class_date, oc.duration
        FROM student_course_registrations scr
        JOIN offered_course oc ON oc.id = scr.offered_course_id
        WHERE scr.student_id = ?
        ORDER BY oc.id DESC";
$st = $conn->prepare($sql);
$st->bind_param("i", $__student_id);
$st->execute();
$res = $st->get_result();
while ($row = $res->fetch_assoc()) $__courses[] = $row;
$st->close();

$__submitted = [];
if ($__window_id) {
  $st2 = $conn->prepare("SELECT offered_course_id FROM tpe_submissions WHERE student_id=? AND window_id=?");
  $st2->bind_param("ii", $__student_id, $__window_id);
  $st2->execute();
  $r = $st2->get_result();
  while ($row = $r->fetch_assoc()) $__submitted[(int)$row['offered_course_id']] = true;
  $st2->close();
}


require_once __DIR__ . '/../view/StudentTPE.php';
