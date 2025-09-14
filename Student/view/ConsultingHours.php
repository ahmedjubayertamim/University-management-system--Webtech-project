<?php
if (!isset($__hours)) 
  { header("Location: /Project/Student/php/ConsultingHours.php"); 
    exit; 
  }
if (!function_exists('h')) { function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Consulting Hours</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/CourseMaterials.css">
</head>
<body>
<header><h1>Consulting Hours</h1></header>

<div class="sidebar">
    <ul>
      <li><a href="../view/StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
       <li><a href="/Project/Student/php/CourseMaterials.php">Materials</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="StudentApplication.php">Student Application</a></li>
      <li><a href="../view/MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
       <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="/Project/Student/php/ConsultingHours.php">Consulting Hours</a></li>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

<div class="content">
  <div class="wrap">
    <h2>Teachers' Consulting Hours</h2>
    <?php if (empty($__hours)): ?>
      <p class="note">No consulting hours shared yet.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Teacher</th>
          <th>Day</th>
          <th>Start</th>
          <th>End</th>
        </tr>
        <?php foreach ($__hours as $h): ?>
          <tr>
            <td><?= h(trim(($h['first_name'] ?? '').' '.($h['last_name'] ?? ''))) ?></td>
            <td><?= h($h['day_of_week']) ?></td>
            <td><?= h($h['start_time']) ?></td>
            <td><?= h($h['end_time']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
