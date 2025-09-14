<?php

session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Forbidden'); }

$offered_id = isset($_POST['offered_id']) ? (int)$_POST['offered_id'] : 0;
$semester   = isset($_POST['semester'])   ? (int)$_POST['semester']   : 0;
$year       = isset($_POST['year'])       ? (int)$_POST['year']       : 0;

$student_ids = $_POST['student_id']  ?? [];
$exams       = $_POST['exam']        ?? [];
$quizzes     = $_POST['quiz']        ?? [];
$attends     = $_POST['attendance']  ?? [];
$perfs       = $_POST['performance'] ?? [];

if (!$offered_id || !$semester || !$year) exit('Invalid request.');


$own = $conn->prepare("SELECT course_title, department FROM offered_course WHERE id=? AND teacher_id=? LIMIT 1");
$own->bind_param("ii", $offered_id, $user_id);
$own->execute();
$own->bind_result($offered_title, $offered_dept);
if (!$own->fetch()) { $own->close(); exit('Unauthorized course.'); }
$own->close();


function ensureCourseId(mysqli $conn, string $title, ?string $dept): int {
    
    $cid = 0;
    $sel = $conn->prepare("SELECT course_id FROM courses WHERE course_name=? AND (department <=> ?) LIMIT 1");
    $sel->bind_param("ss", $title, $dept);
    $sel->execute();
    $sel->bind_result($cid);
    if ($sel->fetch()) { $sel->close(); return (int)$cid; }
    $sel->close();

    $sel2 = $conn->prepare("SELECT course_id FROM courses WHERE course_name=? LIMIT 1");
    $sel2->bind_param("s", $title);
    $sel2->execute();
    $sel2->bind_result($cid);
    if ($sel2->fetch()) { $sel2->close(); return (int)$cid; }
    $sel2->close();

    
    $ins = $conn->prepare("INSERT INTO courses (course_code, course_name, credit, department) VALUES (NULL, ?, NULL, ?)");
    $ins->bind_param("ss", $title, $dept);
    $ins->execute();
    $newId = (int)$conn->insert_id;
    $ins->close();
    return $newId;
}

$course_id = ensureCourseId($conn, (string)$offered_title, $offered_dept ?: null);

/* Weighted total -> letter grade */
function letter_grade($t) {
  if ($t >= 90) return 'A+';
  if ($t >= 85) return 'A';
  
  if ($t >= 80) return 'B+';
  if ($t >= 75) return 'B';
  if ($t >= 70) return 'C+';
  if ($t >= 65) return 'C';
  if ($t >= 60) return 'D+';
  if ($t >= 50) return 'D';
  if ($t >= 49) return 'F';
  return 'F';
}
$WE=0.60; $WQ=0.15; $WA=0.10; $WP=0.15;

/* Manual upsert into grades (FK: grades.course_id → courses.course_id) :contentReference[oaicite:3]{index=3} */
$sel = $conn->prepare("SELECT grade_id FROM grades WHERE student_id=? AND course_id=? AND semester=? AND year=? LIMIT 1");
$upd = $conn->prepare("UPDATE grades SET grade=? WHERE grade_id=?");
$ins = $conn->prepare("INSERT INTO grades (student_id, course_id, semester, year, grade) VALUES (?,?,?,?,?)");

$rows = min(count($student_ids), count($exams), count($quizzes), count($attends), count($perfs));
for ($i=0; $i<$rows; $i++){
    $sid  = (int)$student_ids[$i];
    $exam = max(0, min(100, floatval($exams[$i]   ?? 0)));
    $quiz = max(0, min(100, floatval($quizzes[$i] ?? 0)));
    $att  = max(0, min(100, floatval($attends[$i] ?? 0)));
    $perf = max(0, min(100, floatval($perfs[$i]   ?? 0)));

    $total = $exam*$WE + $quiz*$WQ + $att*$WA + $perf*$WP;
    $lg = letter_grade($total);

    $gid = null;
    $sel->bind_param("iiii", $sid, $course_id, $semester, $year);
    $sel->execute();
    $sel->bind_result($gid);
    if ($sel->fetch() && $gid) {
        $sel->free_result();
        $upd->bind_param("si", $lg, $gid);
        $upd->execute();
    } else {
        $sel->free_result();
        $ins->bind_param("iiiis", $sid, $course_id, $semester, $year, $lg);
        $ins->execute();
    }
}
$sel->close(); $upd->close(); $ins->close();

/* After saving, show the course student result table */
header("Location: GradesResult.php?offered_id={$offered_id}&semester={$semester}&year={$year}");
exit;
