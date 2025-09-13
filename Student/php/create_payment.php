<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers_pay.php';

function back($m){ header("Location: ../view/PayFees.php?msg=".urlencode($m)); exit; }

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id<=0) back("Please log in.");


function get_student_id(mysqli $conn, int $user_id): int {
    $sid=0;
    $q=$conn->prepare("SELECT s.student_id
                       FROM students s JOIN users u ON u.id=s.user_id
                       WHERE s.user_id=? AND u.role='student' AND u.status='enabled' LIMIT 1");
    $q->bind_param("i",$user_id);
    $q->execute();
    $q->bind_result($sid);
    if($q->fetch()){ $q->close(); return (int)$sid; }
    $q->close();
    return 0;
}
$student_id = get_student_id($conn,$user_id);
if ($student_id<=0) back("Not allowed.");

$amount = (float)($_POST['amount'] ?? 0);
$method = trim($_POST['method'] ?? '');

$allowed = ['bkash','nagad','rocket','bank'];
if ($amount <= 0) back("Amount must be greater than 0.");
if (!in_array($method, $allowed, true)) back("Invalid payment method.");

$due = max(0, compute_course_total($conn,$student_id) + compute_library_fine($conn,$student_id) - compute_completed_paid($conn,$student_id));
if ($amount > $due + 0.01) back("Amount exceeds current due.");

/* Insert pending slip */
$st = $conn->prepare("INSERT INTO payments (student_id, amount, method, status) VALUES (?,?,?,'pending')");
$st->bind_param("ids", $student_id, $amount, $method);
if(!$st->execute()){ $st->close(); back("Failed to create slip: ".$conn->error); }
$st->close();

back("Payment slip created. Wait for admin approval.");
