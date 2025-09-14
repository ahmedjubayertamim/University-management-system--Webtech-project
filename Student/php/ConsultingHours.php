<?php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') {
  http_response_code(403);
  exit("Login as student required.");
}

$__hours = [];
$sql = "
  SELECT u.first_name, u.last_name, ch.day_of_week, ch.start_time, ch.end_time
  FROM student_course_registrations scr
  JOIN offered_course o ON o.id = scr.offered_course_id
  JOIN teachers t ON t.teacher_id = o.teacher_id
  JOIN users u ON u.id = t.user_id
  JOIN consulting_hours ch ON ch.teacher_id = t.teacher_id
  WHERE scr.student_id = (SELECT student_id FROM students WHERE user_id=?)
  ORDER BY FIELD(ch.day_of_week,'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
           ch.start_time
";
$q = $conn->prepare($sql);
$q->bind_param("i", $user_id);
$q->execute();
$res = $q->get_result();
while ($row = $res->fetch_assoc()) $__hours[] = $row;
$q->close();

/* Render view */
require_once __DIR__ . '/../view/ConsultingHours.php';
