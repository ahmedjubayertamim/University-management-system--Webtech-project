<!DOCTYPE html>
<html>
<head>
  <title>Submit Grades</title>
  <link rel="stylesheet" href="../css/Attendance.css">
  <style>
    html,body { 
      height:100%; 
      margin:0;
    }
    .frame-wrap { 
      height:100vh; 
    }
    iframe { 
      width:100%; 
      height:100%; 
      border:0; 
    }
  </style>
</head>
<body>
<header><h1>Submit Grades</h1></header>

<div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="CourseMaterials.php">Manage Course Materials</a></li>
    <li><a href="TeacherAttendance.php">Manage Attendance</a></li>
    <li><a href="SubmitGrades.php">Submit Grades</a></li>
    <li><a href="../view/SetConsulting.php">Consulting Hours</a></li>
    <li><a href="StudentApplications.php">Approve Student Requests</a></li>
    <li><a href="ViewSalary.php">View Salary</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  
  <div class="frame-wrap">
    <iframe src="../php/SubmitGrades.php" title="Submit Grades"></iframe>
  </div>
</div>
</body>
</html>
