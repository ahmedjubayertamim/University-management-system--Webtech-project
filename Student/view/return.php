<?php
// return.php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /project/Home/View/login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: StudentLibrary.php');
    exit;
}

$studentId = (int) $_SESSION['student_id'];
$borrow_id = (int) ($_POST['borrow_id'] ?? 0);
$bookid    = (int) ($_POST['bookid'] ?? 0);
if ($borrow_id <= 0 || $bookid <= 0) { die('Invalid request.'); }

// Ensure this borrow belongs to the logged-in student and not already returned
$chk = $conn->prepare("SELECT return_date FROM borrow_records WHERE id = ? AND student_id = ? LIMIT 1");
$chk->bind_param("ii", $borrow_id, $studentId);
$chk->execute();
$chk->bind_result($retDate);
if (!$chk->fetch()) { $chk->close(); die('Borrow record not found.'); }
$chk->close();
if ($retDate !== null) { die('Already returned.'); }

// Mark returned now and free the book
$upd1 = $conn->prepare("UPDATE borrow_records SET return_date = NOW() WHERE id = ? AND student_id = ?");
$upd1->bind_param("ii", $borrow_id, $studentId);
$upd1->execute();
$upd1->close();

$upd2 = $conn->prepare("UPDATE addbook SET status = 'Available' WHERE id = ?");
$upd2->bind_param("i", $bookid);
$upd2->execute();
$upd2->close();

header('Location: StudentLibrary.php');
exit;
