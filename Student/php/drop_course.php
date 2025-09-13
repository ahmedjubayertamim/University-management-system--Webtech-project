<?php
/
session_start();
require_once __DIR__ . '/config.php';

function back($msg) {
  header("Location: ../view/CourseRegistration.php?msg=" . urlencode($msg));
  exit;
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id <= 0) back("Please log in");


$student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$st->bind_result($student_id);
$st->fetch();
$st->close();
if ($student_id <= 0) back("Not a student account");


$locked = 0;
$q = $conn->prepare("SELECT locked FROM student_registration_locks WHERE student_id=?");
$q->bind_param("i", $student_id);
$q->execute();
$q->bind_result($locked);
$q->fetch();
$q->close();
if ((int)$locked === 1) back("Registration is confirmed/locked. You cannot drop courses.");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['offered_course_id'])) {
  back("Invalid request");
}

$cid = (int)$_POST['offered_course_id'];

$conn->begin_transaction();
try {
  // Delete registration
  $del = $conn->prepare("DELETE FROM student_course_registrations WHERE student_id=? AND offered_course_id=?");
  $del->bind_param("ii", $student_id, $cid);
  if (!$del->execute()) {
    $del->close();
    throw new Exception("Drop failed: " . $conn->error);
  }
  $affected = $del->affected_rows;
  $del->close();

  
  if ($affected > 0) {
    $upd = $conn->prepare("UPDATE offered_course SET student_count = GREATEST(student_count - 1, 0) WHERE id=?");
    $upd->bind_param("i", $cid);
    if (!$upd->execute()) {
      $upd->close();
      throw new Exception("Seat update failed: " . $conn->error);
    }
    $upd->close();
  }

  $conn->commit();
  back("Course dropped");
} catch (Exception $e) {
  $conn->rollback();
  back("Drop failed: " . $e->getMessage());
}
