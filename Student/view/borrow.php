<?php

session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: /project/Home/View/login.php"');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: StudentLibrary.php');
    exit;
}

$studentId = (int) $_SESSION['student_id'];
$bookid = (int) ($_POST['bookid'] ?? 0);
if ($bookid <= 0) { die('Invalid book.'); }


$checkStmt = $conn->prepare("SELECT status FROM addbook WHERE id = ? LIMIT 1");
$checkStmt->bind_param("i", $bookid);
$checkStmt->execute();
$checkStmt->bind_result($status);
if (!$checkStmt->fetch()) { $checkStmt->close(); die('Book not found.'); }
$checkStmt->close();

if (strtolower($status) !== 'available') {
    die('Book is not available.');
}

$insertStmt = $conn->prepare("
    INSERT INTO borrow_records (bookid, student_id, borrow_date, due_date)
    VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))
");
$insertStmt->bind_param("ii", $bookid, $studentId);
if (!$insertStmt->execute()) {
    die('DB error (insert): ' . $conn->error);
}
$insertStmt->close();

$updStmt = $conn->prepare("UPDATE addbook SET status = 'Borrowed' WHERE id = ?");
$updStmt->bind_param("i", $bookid);
$updStmt->execute();
$updStmt->close();

header('Location: StudentLibrary.php');
exit;
