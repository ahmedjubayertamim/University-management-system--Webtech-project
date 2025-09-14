<?php

session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Forbidden'); }

$leave_id = (int)($_POST['leave_id'] ?? 0);
$action   = $_POST['action'] ?? '';

if (!$leave_id || !in_array($action, ['approved','rejected'], true)) {
    header("Location: ../view/StudentApplications.php?err=1"); exit;
}

$stmt = $conn->prepare("UPDATE leave_requests SET status=? WHERE leave_id=?");
$stmt->bind_param("si", $action, $leave_id);
$ok = $stmt->execute();
$stmt->close();

header("Location: ../view/StudentApplications.php?".($ok ? "ok=1" : "err=1"));
