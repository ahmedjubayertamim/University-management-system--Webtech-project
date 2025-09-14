<?php
// Project/Teacher/php/update_leave_status.php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Forbidden'); }

$leave_id = (int)($_POST['leave_id'] ?? 0);
$action   = $_POST['action'] ?? '';

if (!$leave_id || !in_array($action, ['approved','rejected'], true)) {
  header("Location: LeaveRequests.php?err=1"); exit;
}

/* Update only if this leave belongs to current teacher and still pending */
$upd = $conn->prepare("
  UPDATE leave_requests
     SET status = ?
   WHERE leave_id = ?
     AND teacher_id = ?
     AND status = 'pending'
  LIMIT 1
");
$upd->bind_param("sii", $action, $leave_id, $user_id);
$ok = $upd->execute(); $upd->close();

header("Location: LeaveRequests.php?".($ok ? "ok=1":"err=1"));
exit;
