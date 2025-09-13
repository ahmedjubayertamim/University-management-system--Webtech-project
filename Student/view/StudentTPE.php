<?php

if (!isset($__student_id, $__courses, $__submitted)) {
  header("Location: /Project/Student/php/StudentTPE.php");
  exit;
}
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Student TPE</title>
  <link rel="stylesheet" href="../css/CourseRegistration.css">
<link rel="stylesheet" href="../css/StudentTPE.css">
 
</head>
<body>
<header>
  <h1>Teacher Performance Evaluation</h1>
 
</header>



<div class="sidebar">
  <ul>
    <li><a href="/Project/Student/view/StudentDashboard.php">Dashboard</a></li>
    <li><a href="/Project/Student/php/CourseRegistration.php">Register Courses</a></li>
    <li><a href="/Project/Student/view/PayFees.php">Pay Fees</a></li>
    <li><a href="/Project/Student/view/StudentAddDrop.php">Add/Drop</a></li>
    <li><a href="/Project/Student/php/StudentLibrary.php">Library</a></li>
    <li><a href="/Project/Student/view/StudentApplication.php">Student Application</a></li>
    <li><a href="/Project/Student/view/MyApplications.php">My Applications</a></li>
    <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
     <li><a href="/Project/Student/php/StudentTPE.php">Submit TPE</a></li>
    <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
     



    <li><a href="/Project/Student/php/logout.php"style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  <?php if (!empty($__msg)): ?>
    <div class="<?= stripos($__msg,'fail')!==false ? 'error':'note' ?>"><?= h($__msg) ?></div>
  <?php endif; ?>

  <div class="box">
    <?php if (!$__window_id): ?>
      <h2>No active TPE window</h2>
      <p class="muted">Admin has not opened TPE yet. Please check back later.</p>
    <?php else: ?>
      <h2>Active Window: <?= h($__win['name']) ?></h2>
      <table>
        <tr>
          <th>#</th>
          <th>Department</th>
          <th>Course Title</th>
          <th>Class Time</th>
          <th>Class Date</th>
          <th>Duration</th>
          <th>Action</th>
        </tr>
        <?php foreach ($__courses as $c):
          $cid  = (int)$c['id'];
          $done = isset($__submitted[$cid]);
        ?>
        <tr>
          <td><?= $cid ?></td>
          <td><?= h($c['department']) ?></td>
          <td><?= h($c['course_title']) ?></td>
          <td><?= h($c['class_time']) ?></td>
          <td><?= h($c['class_date']) ?></td>
          <td><?= h($c['duration']) ?></td>
          <td>
            <?php if ($done): ?>
              <a class="btn" href="/Project/Student/php/view_tpe.php?course=<?= $cid ?>&window=<?= (int)$__window_id ?>">View</a>
            <?php else: ?>
              <a class="btn" href="/Project/Student/php/fill_tpe.php?course=<?= $cid ?>&window=<?= (int)$__window_id ?>">Evaluate</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
