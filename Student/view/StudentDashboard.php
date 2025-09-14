<?php
$name = $name ?? '';
$email = $email ?? '';
$contact = $contact ?? '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="../css/Dashboard.css">
</head>
<body>
<header>
  <h1>Student Dashboard</h1>
</header>

<div class="sidebar">
  <ul>
    <li><a href="../php/StudentDashboard.php" class="active">Dashboard</a></li>
    <li><a href="../php/CourseRegistration.php">Register Courses</a></li>
    <li><a href="../php/CourseMaterials.php">Materials</a></li>
    <li><a href="../php/StudentAddDrop.php">Add/Drop</a></li>
    <li><a href="../php/StudentLibrary.php">Library</a></li>
    <li><a href="../php/StudentApplication.php">Student Application</a></li>
    <li><a href="../php/MyApplications.php">My Applications</a></li>
    <li><a href="../php/MyResults.php">My Results</a></li>
    <li><a href="../php/PayFees.php">Pay Fees</a></li>
    <li><a href="../php/StudentTPE.php">Submit TPE</a></li>
    <li><a href="../php/ConsultingHours.php">Consulting Hours</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <h2>Welcome, Student</h2>
    <div class="profile-cards">
      <div class="profile-card">
        <div class="label">Full Name</div>
        <div class="value"><?= htmlspecialchars($name) ?></div>
      </div>
      <div class="profile-card">
        <div class="label">Email</div>
        <div class="value"><?= htmlspecialchars($email) ?></div>
      </div>
      <div class="profile-card">
        <div class="label">Contact Number</div>
        <div class="value"><?= htmlspecialchars($contact) ?></div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
