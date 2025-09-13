<?php
// Project/Student/php/submit_application.php
session_start();
require_once __DIR__ . '/config.php';

function back($code='1'){
  header("Location: ../view/StudentApplication.php?err=1&err_code=$code");
  exit;
}
function ok(){
  header("Location: ../view/StudentApplication.php?ok=1");
  exit;
}

// ---- Guard: must be a logged-in student ----
$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') { http_response_code(403); exit('Forbidden'); }

// ---- Inputs (single date) ----
$student_id       = (int)($_POST['student_id'] ?? 0);
$teacher_id       = (int)($_POST['teacher_id'] ?? 0);         // must be teachers.teacher_id
$type             = trim($_POST['application_type'] ?? '');
$subject          = trim($_POST['subject'] ?? '');
$details          = trim($_POST['details'] ?? '');
$application_date = trim($_POST['application_date'] ?? '');   // we will store into start_date & end_date

// ---- Basic checks ----
if (!$student_id || !$teacher_id) back('missing_ids');
if ($type==='' || $subject==='' || $details==='' || $application_date==='') back('missing_fields');

// ---- Date validation (YYYY-MM-DD) ----
$dt = DateTime::createFromFormat('Y-m-d', $application_date);
$validDate = $dt && $dt->format('Y-m-d') === $application_date;
if (!$validDate) back('bad_date');

// ---- Ensure the teacher exists in TEACHERS (FK target) ----
$exists = 0;
$chk = $conn->prepare("SELECT 1 FROM teachers WHERE teacher_id=? LIMIT 1");
$chk->bind_param("i", $teacher_id);
$chk->execute();
$chk->bind_result($exists);
$chk->fetch();
$chk->close();
if (!$exists) back('no_teacher_row'); // you likely need to backfill teachers(user_id) → teachers rows

// ---- Verify this teacher is tied to the student's registered courses ----
// Supports BOTH schemas via UNION:
//   Case A: offered_course.teacher_id = teachers.teacher_id
//   Case B: offered_course.teacher_id = users.id  (map via teachers.user_id)
$okTeach = 0;
$sql = "
  SELECT 1
    FROM student_course_registrations scr
    JOIN offered_course o ON o.id = scr.offered_course_id
   WHERE scr.student_id=? AND o.teacher_id = ?           -- Case A
  UNION
  SELECT 1
    FROM student_course_registrations scr
    JOIN offered_course o ON o.id = scr.offered_course_id
    JOIN teachers t       ON t.user_id = o.teacher_id
   WHERE scr.student_id=? AND t.teacher_id = ?           -- Case B
  LIMIT 1
";
$val = $conn->prepare($sql);
$val->bind_param("iiii", $student_id, $teacher_id, $student_id, $teacher_id);
$val->execute();
$val->bind_result($okTeach);
$val->fetch();
$val->close();
if (!$okTeach) back('teacher_not_assigned_to_student');

// ---- Compose reason text (single TEXT column in leave_requests) ----
$reason = "Type: {$type}\nSubject: {$subject}\n\n{$details}";

// ---- Insert: use start_date and end_date with the SAME single date ----
try {
  $ins = $conn->prepare("
    INSERT INTO leave_requests (student_id, teacher_id, reason, start_date, end_date, status)
    VALUES (?,?,?,?,?, 'pending')
  ");
  if (!$ins) back('prepare_failed');

  $ins->bind_param("iisss", $student_id, $teacher_id, $reason, $application_date, $application_date);

  if (!$ins->execute()) {
    $ins->close();
    back('insert_failed');
  }

  $ins->close();
} catch (mysqli_sql_exception $e) {
  // Uncomment during debugging to see DB error:
  // die('DB error: '.$e->getMessage());
  back('sql_exception');
}

ok();
