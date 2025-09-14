<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') {
  http_response_code(403);
  exit('Login as teacher required.');
}

$teacher_id = 0;
$st = $conn->prepare("SELECT teacher_id FROM teachers WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$st->bind_result($teacher_id);
$st->fetch();
$st->close();
if (!$teacher_id) die('Teacher profile not found.');

$msg = $_GET['msg'] ?? '';

$slots = [];
$q = $conn->prepare("SELECT consult_id, day_of_week, start_time, end_time 
                     FROM consulting_hours 
                     WHERE teacher_id=? 
                     ORDER BY FIELD(day_of_week,'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), start_time");
$q->bind_param("i", $teacher_id);
$q->execute();
$res = $q->get_result();
$slots = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
$q->close();

include __DIR__ . '/../view/SetConsulting.php';
