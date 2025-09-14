<?php

session_start();
require_once __DIR__ . '/../php/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
$status  = $_SESSION['status'] ?? ''; // if you store it; otherwise we’ll check DB

if ($user_id <= 0) { die('Please log in.'); }

// Ensure this is an enabled teacher
$check = $conn->prepare("SELECT role, status, first_name, last_name, email 
                         FROM users WHERE id=? LIMIT 1");
$check->bind_param("i", $user_id);
$check->execute();
$check->bind_result($rRole, $rStatus, $fn, $ln, $em);
if (!$check->fetch()) { $check->close(); die('User not found.'); }
$check->close();
if ($rRole !== 'teacher' || $rStatus !== 'enabled') { die('Not a teacher account.'); }

$teacherName = trim(($fn ?? '') . ' ' . ($ln ?? ''));

// Fetch courses assigned to this teacher
$cstmt = $conn->prepare("
  SELECT oc.id, oc.department, oc.course_title, oc.student_capacity, oc.student_count,
         oc.course_fee, oc.class_time, oc.class_date, oc.duration
  FROM offered_course oc
  WHERE oc.teacher_id = ?
  ORDER BY oc.id DESC
");
$cstmt->bind_param("i", $user_id);
$cstmt->execute();
$courses = $cstmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Assigned Courses</title>
  <link rel="stylesheet" href="../css/teacher.css">
  <style>
    
  </style>
</head>
<body>
<header>
  <h1>Welcome, <?= htmlspecialchars($teacherName ?: 'Teacher') ?></h1>
 
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
    <h2>Assigned Subjects</h2>
    <?php if ($courses->num_rows === 0): ?>
      <p class="muted">No courses assigned to you yet.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>#</th>
          <th>Department</th>
          <th>Course Title</th>
          <th>Capacity</th>
          <th>Enrolled</th>
          <th>Class Time</th>
          <th>Class Date</th>
          <th>Duration</th>
          <th>Students</th>
        </tr>
        <?php while ($c = $courses->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$c['id'] ?></td>
            <td><?= htmlspecialchars($c['department']) ?></td>
            <td style="text-align:left"><?= htmlspecialchars($c['course_title']) ?></td>
            <td><?= (int)$c['student_capacity'] ?></td>
            <td><?= (int)$c['student_count'] ?></td>
            <td><?= htmlspecialchars($c['class_time']) ?></td>
            <td><?= htmlspecialchars($c['class_date']) ?></td>
            <td><?= htmlspecialchars($c['duration']) ?></td>
            <td>
              <a class="btn" href="TeacherCourseStudents.php?course_id=<?= (int)$c['id'] ?>">
                View List
              </a>

              <a class="btn" href="TeacherAttendanceView.php?course_id=<?= (int)$course['id'] ?>"> Attendance</a>



            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
