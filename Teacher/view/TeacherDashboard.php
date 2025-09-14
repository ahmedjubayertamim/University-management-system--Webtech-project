<?php

session_start();
require_once __DIR__ . '/../php/config.php'; 

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0) { die("Please log in."); }

if ($role !== 'teacher') {
    die("Access denied: not a teacher account.");
}

$u = $conn->prepare("SELECT first_name, last_name, email, contact_number, status 
                     FROM users WHERE id=? LIMIT 1");
$u->bind_param("i", $user_id);
$u->execute();
$u->bind_result($first, $last, $email, $contact, $status);
if (!$u->fetch()) { $u->close(); die("User not found."); }
$u->close();

$name = trim(($first ?? '') . ' ' . ($last ?? ''));
$name = $name ?: 'Teacher';
$email   = $email ?? '';
$contact = $contact ?? '';
$enabled = ($status === 'enabled');
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Teacher Dashboard</title>
  <link rel="stylesheet" href="../css/teacher.css">
 
</head>
<body>
  <!-- Header -->
  <header>
    <h1>Teacher Dashboard</h1>
    <div class="search-box">
      <input type="text" placeholder="Search...">
      <button>Search</button>
    </div>
  </header>

  
 <div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="CourseMaterials.php">Manage Course Materials</a></li>
    <li><a href="StudentAttendance.php">Manage Attendance</a></li>
    <li><a href="SubmitGrades.php">Submit Grades</a></li>
    <li><a href="ConsultingHours.php">Consulting Hours</a></li>
    <li><a href="StudentApplications.php">Approve Student Requests</a></li>
    <li><a href="ViewSalary.php">View Salary</a></li>




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

      <a class="btn" href="MyCourses.php">Go to My Courses</a>
    </div>
  </div>
</body>
</html>
