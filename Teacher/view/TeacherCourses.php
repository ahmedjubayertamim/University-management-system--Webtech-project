<?php
$teacherName = $teacherName ?? 'Teacher';
$courses = $courses ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Assigned Courses</title>
  <link rel="stylesheet" href="../css/teacher.css">
</head>
<body>
<header>
  <h1>Welcome, <?= htmlspecialchars($teacherName) ?></h1>
</header>

<div class="sidebar">
  <ul>
    <li><a href="../php/TeacherDashboard.php">Dashboard</a></li>
    <li><a href="../php/CourseMaterials.php">Manage Course Materials</a></li>
    <li><a href="../php/TeacherAttendance.php">Manage Attendance</a></li>
    <li><a href="../php/SubmitGrades.php">Submit Grades</a></li>
    <li><a href="../php/SetConsulting.php">Consulting Hours</a></li>
    <li><a href="../php/StudentApplications.php">Approve Student Requests</a></li>
    <li><a href="../php/ViewSalary.php">View Salary</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <h2>Assigned Subjects</h2>
    <?php if (!$courses || $courses->num_rows === 0): ?>
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
              <a class="btn" href="../php/TeacherCourseStudents.php?course_id=<?= (int)$c['id'] ?>">View List</a>
              <a class="btn" href="../php/TeacherAttendanceView.php?course_id=<?= (int)$c['id'] ?>">Attendance</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
