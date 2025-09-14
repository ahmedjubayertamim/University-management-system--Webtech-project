<?php
require_once __DIR__ . '/../php/auth_student.php';
require_once __DIR__ . '/../php/config.php';

$name = $email = $contact = '';
$uid = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$semail = isset($_SESSION['email']) ? trim($_SESSION['email']) : '';

function fetch_user(mysqli $conn, ?int $id = null, ?string $email = null): ?array {
    if ($id) {
        $stmt = $conn->prepare("SELECT first_name, last_name, email, contact_number FROM users WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $id);
    } elseif ($email) {
        $stmt = $conn->prepare("SELECT first_name, last_name, email, contact_number FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
    } else {
        return null;
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $row = ($res && $res->num_rows === 1) ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row;
}

$row = $uid > 0 ? fetch_user($conn, $uid, null) : null;
if (!$row && $semail !== '') {
    $row = fetch_user($conn, null, $semail);
}

if ($row) {
    $first = trim($row['first_name'] ?? '');
    $last = trim($row['last_name'] ?? '');
    $name = htmlspecialchars(trim($first.' '.$last));
    $email = htmlspecialchars($row['email'] ?? '');
    $contact = htmlspecialchars($row['contact_number'] ?? '');
}

if ($name === '' && isset($_SESSION['name'])) $name = htmlspecialchars($_SESSION['name']);
if ($email === '' && isset($_SESSION['email'])) $email = htmlspecialchars($_SESSION['email']);
if ($contact === '' && isset($_SESSION['contact'])) $contact = htmlspecialchars($_SESSION['contact']);

include __DIR__ . '/../view/StudentDashboard.php';
