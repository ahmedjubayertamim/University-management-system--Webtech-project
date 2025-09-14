<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Submit Grades</title>
  <link rel="stylesheet" href="../css/Attendance.css">
  <style>
    html,body { height:100%; margin:0 }
    .frame-wrap { height:100vh; }
    iframe { width:100%; height:100%; border:0; }
  </style>
</head>
<body>
<header><h1>Submit Grades</h1></header>

<div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="TeacherCourses.php">My Assigned Courses</a></li>
    <li><a href="SubmitGrades.php">Submit Grades</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <!-- The actual page (with DB + logic) is rendered by the controller below -->
  <div class="frame-wrap">
    <iframe src="../php/SubmitGrades.php" title="Submit Grades"></iframe>
  </div>
</div>
</body>
</html>
