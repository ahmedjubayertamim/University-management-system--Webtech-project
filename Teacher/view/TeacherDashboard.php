<?php
$name    = $name    ?? 'Teacher';
$email   = $email   ?? '';
$contact = $contact ?? '';
$enabled = isset($enabled) ? (bool)$enabled : false;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Teacher Dashboard</title>
  <link rel="stylesheet" href="../css/teacher.css">
</head>
<body>
  <header>
    <h1>Teacher Dashboard</h1>
   
    </div>
  </header>

  <div class="sidebar">
    <ul>
      <li><a href="../php/TeacherDashboard.php" class="active">Dashboard</a></li>
      <li><a href="../php/CourseMaterials.php">Manage Course Materials</a></li>
      <li><a href="../php/TeacherAttendance.php">Manage Attendance</a></li>
      <li><a href="../php/TeacherAttendanceReport.php">Attendance Report</a></li>
      <li><a href="../php/SubmitGrades.php">Submit Grades</a></li>
      <li><a href="../php/SetConsulting.php">Consulting Hours</a></li>
      <li><a href="../php/StudentApplications.php">Approve Student Requests</a></li>
      <li><a href="../php/ViewSalary.php">View Salary</a></li>
      <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <div class="wrap">
      <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
      <?php if (!$enabled): ?>
        <div class="note">Your account is currently <strong>disabled</strong>. Please contact an administrator.</div>
      <?php else: ?>
        <p class="muted">You are logged in. Below are your profile details.</p>
      <?php endif; ?>

      <div class="card">
        <div class="row">
          <div class="col">
            <div class="label">Full Name</div>
            <div class="value"><?= htmlspecialchars($name) ?></div>
          </div>
          <div class="col">
            <div class="label">Email</div>
            <div class="value"><?= htmlspecialchars($email) ?></div>
          </div>
          <div class="col">
            <div class="label">Contact Number</div>
            <div class="value"><?= $contact !== '' ? htmlspecialchars($contact) : '—' ?></div>
          </div>
        </div>
      </div>

      <a class="btn" href="../php/TeacherCourses.php">Go to My Courses</a>
    </div>
  </div>
</body>
</html>
