<?php

if (!isset($__MYRESULTS_ROWS, $__MYRESULTS_HAS)) {
  header("Location: /Project/Student/php/MyResults.php");
  exit;
}

if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$rows  = $__MYRESULTS_ROWS;
$G_HAS = $__MYRESULTS_HAS;
?>

<!DOCTYPE html>
<html>
<head>
 
  <title>My Course Results</title>
  <link rel="stylesheet" href="../css/CourseRegistration.css">

</head>
<body>
<header><h1>My Course Results</h1></header>

<div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
       <li><a href="/Project/Student/php/CourseMaterials.php">Materials</a></li>
      <li><a href="../view/StudentAddDrop.php">Add/Drop</a></li>
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
    <h2>Your Registered Courses & Grades</h2>
    <p class="muted">If a course shows “Pending”, your teacher hasn’t published a grade yet.</p>

    <?php if (empty($rows)): ?>
      <p class="muted">No course registrations found.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Department</th>
          <th>Course</th>
          <th>Semester</th>
          <th>Year</th>
          <?php if ($G_HAS['exam'])        echo "<th>Exam</th>"; ?>
          <?php if ($G_HAS['quiz'])        echo "<th>Quiz</th>"; ?>
          <?php if ($G_HAS['attendance'])  echo "<th>Attendance</th>"; ?>
          <?php if ($G_HAS['performance']) echo "<th>Performance</th>"; ?>
          <?php if ($G_HAS['total'])       echo "<th>Total</th>"; ?>
          <th>Letter</th>
        </tr>
        <?php foreach ($rows as $r): ?>
          <?php
            $letter = trim((string)($r['letter'] ?? ''));
            $isPending = ($letter === '' || $letter === null);
          ?>
          <tr>
            <td><?= h($r['department'] ?? '') ?></td>
            <td><?= h($r['course_title'] ?? '') ?></td>
            <td><?= h($r['semester'] ?? '—') ?></td>
            <td><?= h($r['year'] ?? '—') ?></td>

            <?php if ($G_HAS['exam'])        : ?><td><?= h(isset($r['exam'])        ? $r['exam']        : '—') ?></td><?php endif; ?>
            <?php if ($G_HAS['quiz'])        : ?><td><?= h(isset($r['quiz'])        ? $r['quiz']        : '—') ?></td><?php endif; ?>
            <?php if ($G_HAS['attendance'])  : ?><td><?= h(isset($r['attendance'])  ? $r['attendance']  : '—') ?></td><?php endif; ?>
            <?php if ($G_HAS['performance']) : ?><td><?= h(isset($r['performance']) ? $r['performance'] : '—') ?></td><?php endif; ?>
            <?php if ($G_HAS['total'])       : ?><td><?= h(isset($r['total'])       ? $r['total']       : '—') ?></td><?php endif; ?>

            <td>
              <?php if ($isPending): ?>
                <span class="tag pending">Pending</span>
              <?php else: ?>
                <span class="tag ok"><?= h($letter) ?></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

      <?php if (array_filter($G_HAS)): ?>
        <p class="gridnote">Breakdown columns are shown only if your <code>grades</code> table contains them.</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
