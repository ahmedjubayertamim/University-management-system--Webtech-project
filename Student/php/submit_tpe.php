<?php
// Project/Student/php/submit_tpe.php
session_start();
require_once __DIR__ . '/config.php';

function back($m) {
    header("Location: ../view/StudentTPE.php?msg=" . urlencode($m));
    exit;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) back("Please log in.");

// Resolve student_id for an enabled student
$sid = 0;
$q = $conn->prepare(
  "SELECT s.student_id
   FROM students s
   JOIN users u ON u.id = s.user_id
   WHERE s.user_id = ? AND u.role = 'student' AND u.status = 'enabled'
   LIMIT 1"
);
$q->bind_param("i", $user_id);
$q->execute();
$q->bind_result($sid);
if (!$q->fetch()) { $q->close(); back("Not allowed."); }
$q->close();

// Inputs
$course_id = (int)($_POST['course_id'] ?? 0);
$window_id = (int)($_POST['window_id'] ?? 0);
$answers   = $_POST['q'] ?? [];
$comment   = trim($_POST['comment'] ?? '');

if ($course_id <= 0 || $window_id <= 0) back("Bad request.");

// Window must be open
$ws = null;
$w = $conn->prepare("SELECT status FROM tpe_windows WHERE id=?");
$w->bind_param("i", $window_id);
$w->execute();
$w->bind_result($ws);
$w->fetch();
$w->close();
if ($ws !== 'open') back("This TPE window is closed.");

// Ensure THIS student is registered for THIS offered course (no teacher_id)
$ok = 0;
$chk = $conn->prepare(
  "SELECT COUNT(*) FROM student_course_registrations
   WHERE student_id = ? AND offered_course_id = ?"
);
$chk->bind_param("ii", $sid, $course_id);
$chk->execute();
$chk->bind_result($ok);
$chk->fetch();
$chk->close();
if ($ok <= 0) back("You are not registered for this course.");

// Prevent duplicate submission in same window
$dup = $conn->prepare(
  "SELECT id FROM tpe_submissions
   WHERE student_id = ? AND offered_course_id = ? AND window_id = ?"
);
$dup->bind_param("iii", $sid, $course_id, $window_id);
$dup->execute();
$dup->store_result();
if ($dup->num_rows > 0) { $dup->close(); back("Already submitted."); }
$dup->close();

// Validate answers: all active questions must be answered 1..5
$act = $conn->query("SELECT id FROM tpe_questions WHERE active=1 ORDER BY id ASC");
$requiredQ = [];
while ($row = $act->fetch_assoc()) $requiredQ[] = (int)$row['id'];
$act->close();

foreach ($requiredQ as $qid) {
    if (!isset($answers[$qid])) back("Please answer all questions.");
    $r = (int)$answers[$qid];
    if ($r < 1 || $r > 5) back("Invalid rating.");
}

// Insert submission + answers (no teacher_id column)
$conn->begin_transaction();
try {
    $ins = $conn->prepare(
      "INSERT INTO tpe_submissions
         (student_id, offered_course_id, window_id, overall_comment)
       VALUES (?,?,?,?)"
    );
    $ins->bind_param("iiis", $sid, $course_id, $window_id, $comment);
    if (!$ins->execute()) throw new Exception("Insert submission failed.");
    $submission_id = (int)$conn->insert_id;
    $ins->close();

    $ans = $conn->prepare(
      "INSERT INTO tpe_answers (submission_id, question_id, rating)
       VALUES (?,?,?)"
    );
    foreach ($answers as $qid => $rating) {
        $qid = (int)$qid; $rating = (int)$rating;
        $ans->bind_param("iii", $submission_id, $qid, $rating);
        if (!$ans->execute()) throw new Exception("Insert answer failed.");
    }
    $ans->close();

    $conn->commit();
    back("Thanks! Your evaluation has been submitted.");
} catch (Exception $e) {
    $conn->rollback();
    back("Submit failed: " . $e->getMessage());
}
