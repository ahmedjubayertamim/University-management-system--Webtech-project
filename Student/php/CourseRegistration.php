<?php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function get_or_create_student_id(mysqli $conn, int $user_id): int {
    $role = $status = null;
    $q = $conn->prepare("SELECT role, status FROM users WHERE id=? LIMIT 1");
    $q->bind_param("i", $user_id);
    $q->execute();
    $q->bind_result($role, $status);
    $q->fetch();
    $q->close();

    if ($role !== 'student' || $status !== 'enabled') {
        return 0;
    }

    // existing link?
    $sid = 0;
    $q = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
    $q->bind_param("i", $user_id);
    $q->execute();
    $q->bind_result($sid);
    if ($q->fetch()) { $q->close(); return (int)$sid; }
    $q->close();

    // create
    $ins = $conn->prepare("INSERT INTO students (user_id) VALUES (?)");
    if (!$ins) return 0;
    $ins->bind_param("i", $user_id);
    if (!$ins->execute()) { $ins->close(); return 0; }
    $newId = (int)$conn->insert_id;
    $ins->close();
    return $newId;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { die("Please log in."); }

$student_id = get_or_create_student_id($conn, $user_id);
if ($student_id <= 0) { die("Your account is not allowed to register courses (must be an enabled student)."); }

// flash/message
$__msg = $_GET['msg'] ?? "";

/** Load all offered courses (as array) */
$__courses = [];
$res = $conn->query("SELECT * FROM offered_course ORDER BY id DESC");
while ($row = $res->fetch_assoc()) $__courses[] = $row;

/** Map of my current registrations */
$__myMap = [];
$q = $conn->prepare("SELECT offered_course_id FROM student_course_registrations WHERE student_id=?");
$q->bind_param("i", $student_id);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) $__myMap[(int)$row['offered_course_id']] = true;
$q->close();

/** Locked flag */
$__locked = 0;
$q = $conn->prepare("SELECT locked FROM student_registration_locks WHERE student_id=?");
$q->bind_param("i", $student_id);
$q->execute();
$q->bind_result($__locked);
$q->fetch();
$q->close();

/** My registered courses + total fee */
$mineStmt = $conn->prepare("
  SELECT oc.*
  FROM student_course_registrations scr
  JOIN offered_course oc ON oc.id = scr.offered_course_id
  WHERE scr.student_id=?
  ORDER BY scr.id DESC
");
$mineStmt->bind_param("i", $student_id);
$mineStmt->execute();
$mineRes = $mineStmt->get_result();
$__myCourses = [];
$__totalFee = 0.0;
while ($row = $mineRes->fetch_assoc()) {
    $__myCourses[] = $row;
    $__totalFee += (float)$row['course_fee'];
}
$mineStmt->close();

/** Expose IDs needed by forms */
$__student_id = $student_id;

/** Render view */
require_once __DIR__ . '/../view/CourseRegistration.php';
