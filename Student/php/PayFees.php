<?php

session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers_pay.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function get_or_create_student_id(mysqli $conn, int $user_id): int {
    $role = $status = null;
    $q = $conn->prepare("SELECT role, status FROM users WHERE id=?");
    $q->bind_param("i", $user_id);
    $q->execute();
    $q->bind_result($role, $status);
    $q->fetch(); $q->close();
    if ($role !== 'student' || $status !== 'enabled') return 0;

    $sid = 0;
    $q = $conn->prepare("SELECT student_id FROM students WHERE user_id=?");
    $q->bind_param("i", $user_id);
    $q->execute(); $q->bind_result($sid);
    if ($q->fetch()) { $q->close(); return (int)$sid; }
    $q->close();

    $ins = $conn->prepare("INSERT INTO students (user_id) VALUES (?)");
    $ins->bind_param("i", $user_id);
    if (!$ins->execute()) { $ins->close(); return 0; }
    $sid = (int)$conn->insert_id; $ins->close();
    return $sid;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { http_response_code(403); exit("Please log in."); }
$__student_id = get_or_create_student_id($conn, $user_id);
if ($__student_id <= 0) { http_response_code(403); exit("Not allowed."); }

$__msg = $_GET['msg'] ?? "";

/* Totals */
$__course_total = compute_course_total($conn, $__student_id);
$__library_fine = compute_library_fine($conn, $__student_id);
$__paid_total   = compute_completed_paid($conn, $__student_id);
$__total_due    = max(0, $__course_total + $__library_fine - $__paid_total);

$hist = $conn->prepare("SELECT payment_id, amount, method, status, payment_date
                        FROM payments WHERE student_id=? ORDER BY payment_id DESC");
$hist->bind_param("i", $__student_id);
$hist->execute();
$__payments_res = $hist->get_result();
$__payments = [];
while ($row = $__payments_res->fetch_assoc()) $__payments[] = $row;
$hist->close();

require_once __DIR__ . '/../view/PayFees.php';
