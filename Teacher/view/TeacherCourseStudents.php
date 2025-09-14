<?php
// Project/Teacher/view/TeacherCourseStudents.php
session_start();
require_once __DIR__ . '/../php/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { die('Please log in.'); }

// Verify teacher and get name
$u = $conn->prepare("SELECT role, status, first_name, last_name FROM users WHERE id=? LIMIT 1");
$u->bind_param("i", $user_id);
$u->execute();
$u->bind_result($urole, $ustatus, $fn, $ln);
if (!$u->fetch()) { $u->close(); die('User not found.'); }
$u->close();
if ($urole !== 'teacher' || $ustatus !== 'enabled') { die('Not a teacher account.'); }
$teacherName = trim(($fn ?? '').' '.($ln ?? ''));

// Course id
$course_id = (int)($_GET['course_id'] ?? 0);
if ($course_id <= 0) { die('Invalid course.'); }

// Ensure this course is assigned to this teacher
$chk = $conn->prepare("SELECT course_title FROM offered_course WHERE id=? AND teacher_id=? LIMIT 1");
$chk->bind_param("ii", $course_id, $user_id);
$chk->execute();
$chk->bind_result($course_title);
if (!$chk->fetch()) { $chk->close(); die('You are not assigned to this course.'); }
$chk->close();

// Pull registered students
$st = $conn->prepare("
  SELECT s.student_id, u.first_name, u.last_name, u.email, u.contact_number
  FROM student_course_registrations scr
  JOIN students s ON s.student_id = scr.student_id
  JOIN users u    ON u.id = s.user_id
  WHERE scr.offered_course_id = ?
  ORDER BY u.first_name, u.last_name
");
$st->bind_param("i", $course_id);
$st->execute();
$rs = $st->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Students – <?= htmlspecialchars($course_title) ?></title>
  <link rel="stylesheet" href="../css/teacher.css">
 
</head>
<body>
<header>
  <h1><?= htmlspecialchars($course_title) ?> – Registered Students</h1>
  <div class="search-box"><input placeholder="Search..."><button>Search</button></div>
</header>

<div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="TeacherCourses.php">My Courses</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <?php if ($rs->num_rows === 0): ?>
      <p class="muted">No students have registered for this course yet.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>#Student ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Contact</th>
        </tr>
        <?php while ($s = $rs->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$s['student_id'] ?></td>
            <td style="text-align:left">
              <?= htmlspecialchars(trim(($s['first_name'] ?? '').' '.($s['last_name'] ?? ''))) ?>
            </td>
            <td><?= htmlspecialchars($s['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($s['contact_number'] ?? '—') ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>

    <br>
    <a class="btn" href="TeacherCourses.php">Back to My Courses</a>
  </div>
</div>
</body>
</html>
