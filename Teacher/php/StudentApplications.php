<?php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') {
    http_response_code(403);
    exit('Please log in as a teacher.');
}

$teacher_id = 0;
$stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE user_id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($teacher_id);
$stmt->fetch();
$stmt->close();
if (!$teacher_id) exit("Teacher profile not found.");

$sql = "
  SELECT lr.leave_id, lr.reason, lr.start_date, lr.status,
         u.first_name, u.last_name, u.email
  FROM leave_requests lr
  JOIN students s ON s.student_id = lr.student_id
  JOIN users u    ON u.id = s.user_id
  WHERE lr.teacher_id = ?
  ORDER BY lr.start_date DESC, lr.leave_id DESC
";
$q = $conn->prepare($sql);
$q->bind_param("i", $teacher_id);
$q->execute();
$res  = $q->get_result();
$rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$q->close();

$ok  = isset($_GET['ok']);
$err = isset($_GET['err']);

include __DIR__ . '/../view/StudentApplications.php';
