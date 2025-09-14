<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0) { die('Please log in.'); }
if ($role !== 'teacher') { die('Access denied: not a teacher account.'); }

$u = $conn->prepare("
    SELECT first_name, last_name, email, contact_number, status
    FROM users WHERE id=? LIMIT 1
");
$u->bind_param("i", $user_id);
$u->execute();
$u->bind_result($first, $last, $email, $contact, $status);
if (!$u->fetch()) { $u->close(); die('User not found.'); }
$u->close();

$name = trim(($first ?? '') . ' ' . ($last ?? ''));
$name = $name !== '' ? $name : 'Teacher';
$email   = $email ?? '';
$contact = $contact ?? '';
$enabled = ($status === 'enabled');

include __DIR__ . '/../view/TeacherDashboard.php';
