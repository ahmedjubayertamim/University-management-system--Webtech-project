<?php

session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') {
  http_response_code(403);
  exit('Login as student');
}

$__student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$st->bind_result($__student_id);
$st->fetch();
$st->close();
if (!($__student_id > 0)) { exit('Student profile not found.'); }


$__teachers = [];
$sql = "
  SELECT DISTINCT
    COALESCE(tA.teacher_id, tB.teacher_id) AS teacher_id,
    COALESCE(uA.first_name, uB.first_name, uTeacher.first_name) AS first_name,
    COALESCE(uA.last_name,  uB.last_name,  uTeacher.last_name)  AS last_name,
    CASE WHEN COALESCE(tA.teacher_id, tB.teacher_id) IS NULL THEN 0 ELSE 1 END AS selectable
  FROM student_course_registrations scr
  JOIN offered_course o ON o.id = scr.offered_course_id

  /* Case A: offered_course.teacher_id stores teachers.teacher_id */
  LEFT JOIN teachers tA ON tA.teacher_id = o.teacher_id
  LEFT JOIN users    uA ON uA.id = tA.user_id

  /* Case B: offered_course.teacher_id stores users.id (teacher as user) */
  LEFT JOIN users    uTeacher ON uTeacher.id = o.teacher_id
  LEFT JOIN teachers tB       ON tB.user_id = uTeacher.id
  LEFT JOIN users    uB       ON uB.id = tB.user_id

  WHERE scr.student_id = ?
  AND (uA.id IS NOT NULL OR uB.id IS NOT NULL OR uTeacher.id IS NOT NULL)
  ORDER BY
    COALESCE(uA.first_name, uB.first_name, uTeacher.first_name),
    COALESCE(uA.last_name,  uB.last_name,  uTeacher.last_name)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $__student_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $__teachers[] = $row; }
$stmt->close();

$__msg = isset($_GET['ok'])  ? 'Application submitted.' : '';
$__err = isset($_GET['err']) ? 'Failed to submit.'      : '';

$__selectableCount = 0;
foreach ($__teachers as $t) if ((int)$t['selectable'] === 1) $__selectableCount++;

require_once __DIR__ . '/../view/StudentApplication.php';
