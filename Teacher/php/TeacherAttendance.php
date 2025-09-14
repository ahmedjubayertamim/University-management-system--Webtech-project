<?php

session_start();

$projectRoot = dirname(__DIR__, 2);
$configTried = [
    $projectRoot . '/php/config.php',
    $projectRoot . '/Student/php/config.php',
    $projectRoot . '/Admin/php/config.php',
];
$configLoaded = false;
foreach ($configTried as $cfg) {
    if (file_exists($cfg)) { require_once $cfg; $configLoaded = true; break; }
}
if (!$configLoaded) { die("Database config not found."); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) {
    die("Please log in as a teacher.");
}

$role = $status = $teacherFirst = $teacherLast = '';
$q = $conn->prepare("SELECT role, status, first_name, last_name FROM users WHERE id=? LIMIT 1");
$q->bind_param("i", $user_id);
$q->execute();
$q->bind_result($role, $status, $teacherFirst, $teacherLast);
if (!$q->fetch()) {
    $q->close();
    die("User not found.");
}
$q->close();

if ($role !== 'teacher' || $status !== 'enabled') {
    die("Access denied (not an enabled teacher).");
}
$teacherName = trim($teacherFirst . ' ' . $teacherLast);

$courses_rs = $conn->prepare("
    SELECT id, course_title
    FROM offered_course
    WHERE teacher_id = ?
    ORDER BY id DESC
");
$courses_rs->bind_param("i", $user_id);
$courses_rs->execute();
$courses_list = $courses_rs->get_result()->fetch_all(MYSQLI_ASSOC);
$courses_rs->close();

if (empty($courses_list)) {
    $teacherCourses = [];
} else {
    $teacherCourses = $courses_list;
}

$selectedCourseId = 0;
$attendanceDate   = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedCourseId = (int)($_POST['course_id'] ?? 0);
    $attendanceDate   = $_POST['date'] ?? date('Y-m-d');

    $chk = $conn->prepare("SELECT COUNT(*) FROM offered_course WHERE id=? AND teacher_id=?");
    $chk->bind_param("ii", $selectedCourseId, $user_id);
    $chk->execute();
    $chk->bind_result($ok);
    $chk->fetch(); $chk->close();
    if ($ok <= 0) {
        $error = "Invalid course selection.";
        $students_rows = [];
        include __DIR__ . '/../view/TeacherAttendanceView.php';
        exit;
    }

    $students = $conn->prepare("
        SELECT s.student_id, u.first_name, u.last_name, u.email
        FROM student_course_registrations scr
        JOIN students s ON s.student_id = scr.student_id
        JOIN users u    ON u.id        = s.user_id
        WHERE scr.offered_course_id = ?
        ORDER BY u.first_name, u.last_name
    ");
    $students->bind_param("i", $selectedCourseId);
    $students->execute();
    $students_rs = $students->get_result();
    $students_rows = $students_rs->fetch_all(MYSQLI_ASSOC);
    $students->close();

    $statusMap = $_POST['status'] ?? []; 

    foreach ($students_rows as $stu) {
        $sid = (int)$stu['student_id'];
        $st  = $statusMap[$sid] ?? null; 

        if (!in_array($st, ['present','absent','late'], true)) {
            continue;
        }

        $sel = $conn->prepare("SELECT attendance_id FROM attendance WHERE student_id=? AND course_id=? AND date=? LIMIT 1");
        $sel->bind_param("iis", $sid, $selectedCourseId, $attendanceDate);
        $sel->execute(); $sel->bind_result($attId);
        if ($sel->fetch()) {
            $sel->close();
            $upd = $conn->prepare("UPDATE attendance SET status=? WHERE attendance_id=?");
            $upd->bind_param("si", $st, $attId);
            $upd->execute(); $upd->close();
        } else {
            $sel->close();
            $ins = $conn->prepare("INSERT INTO attendance (student_id, course_id, date, status) VALUES (?,?,?,?)");
            $ins->bind_param("iiss", $sid, $selectedCourseId, $attendanceDate, $st);
            $ins->execute(); $ins->close();
        }
    }

    $success = "Attendance saved for $attendanceDate.";
} else {
    $selectedCourseId = (int)($_GET['course_id'] ?? 0);
    $attendanceDate   = $_GET['date'] ?? date('Y-m-d');
}


if ($selectedCourseId <= 0 && !empty($teacherCourses)) {
    $selectedCourseId = (int)$teacherCourses[0]['id'];
}

$students_rows = [];
if ($selectedCourseId > 0) {
    $chk = $conn->prepare("SELECT COUNT(*) FROM offered_course WHERE id=? AND teacher_id=?");
    $chk->bind_param("ii", $selectedCourseId, $user_id);
    $chk->execute(); $chk->bind_result($ok2); $chk->fetch(); $chk->close();
    if ($ok2 > 0) {
        $students = $conn->prepare("
            SELECT s.student_id, u.first_name, u.last_name, u.email
            FROM student_course_registrations scr
            JOIN students s ON s.student_id = scr.student_id
            JOIN users u    ON u.id        = s.user_id
            WHERE scr.offered_course_id = ?
            ORDER BY u.first_name, u.last_name
        ");
        $students->bind_param("i", $selectedCourseId);
        $students->execute();
        $students_rs = $students->get_result();
        $students_rows = $students_rs->fetch_all(MYSQLI_ASSOC);
        $students->close();

        $att_map = [];
        $att = $conn->prepare("SELECT student_id, status FROM attendance WHERE course_id=? AND date=?");
        $att->bind_param("is", $selectedCourseId, $attendanceDate);
        $att->execute();
        $att_rs = $att->get_result();
        while ($r = $att_rs->fetch_assoc()) {
            $att_map[(int)$r['student_id']] = $r['status'];
        }
        $att->close();
    }
}
include __DIR__ . '/../view/TeacherAttendanceView.php';
