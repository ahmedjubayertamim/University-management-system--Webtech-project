<?php

session_start();
require_once __DIR__ . '/config.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$course_id  = (int)($_POST['course_id'] ?? 0);
$teacher_id = (int)($_POST['teacher_id'] ?? 0);
if ($course_id <= 0 || $teacher_id <= 0) {
    exit('Invalid request.');
}


$ok = 0;
$chk = $conn->prepare("SELECT COUNT(*) FROM users WHERE id=? AND role='teacher' AND status='enabled'");
$chk->bind_param("i", $teacher_id);
$chk->execute();
$chk->bind_result($ok);
$chk->fetch();
$chk->close();
if ($ok <= 0) {
    exit('Selected teacher is not valid/enabled.');
}

// update
$u = $conn->prepare("UPDATE offered_course SET teacher_id=? WHERE id=?");
$u->bind_param("ii", $teacher_id, $course_id);
if (!$u->execute()) {
    $u->close();
    exit('DB error: ' . $conn->error);
}
$u->close();

header('Location: ../View/AssignTeacher.php');
exit;
