<?php


if (!isset($__courses, $__myMap, $__locked, $__myCourses, $__totalFee, $__student_id)) {
  header("Location: /Project/Student/php/CourseRegistration.php");
  exit;
}
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Student Course Registration</title>
  
  <link rel="stylesheet" href="../css/CourseRegistration.css">
</head>
<body>

  
  <header>
    <h1>Student Course Registration</h1>
    
  </header>

 
  <div class="sidebar">
    <ul>
      <li><a href="/Project/Student/view/StudentDashboard.php">Dashboard</a></li>
      <li><a href="/Project/Student/php/CourseRegistration.php">Register Courses</a></li>
      <li><a href="/Project/Student/view/StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="/Project/Student/view/StudentLibrary.php">Library</a></li>
      <li><a href="/Project/Student/view/StudentApplication.php">Student Application</a></li>
      <li><a href="/Project/Student/view/MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="/Project/Student/view/PayFees.php">Pay Fees</a></li>
      <li><a href="/Project/Student/view/StudentTPE.php">Submit TPE</a></li>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>




      <li><a href="/Project/Student/php/logout.php" style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <div class="form-container">
      <h2>Available Courses</h2>

      <?php if (!empty($__msg)): ?>
        <div class="<?= (stripos($__msg,'fail')!==false || stripos($__msg,'error')!==false)?'error':'note' ?>">
          <?= h($__msg) ?>
        </div>
      <?php endif; ?>

      <?php if ((int)$__locked === 1): ?>
        <div class="note">Your registration is confirmed and locked. You cannot add/drop more courses.</div>
      <?php endif; ?>

      <form action="../php/register_course.php" method="POST">
        <input type="hidden" name="student_id" value="<?= (int)$__student_id ?>">
        <table>
          <thead>
            <tr>
              <th>Select</th>
              <th>Department</th>
              <th>Course Title</th>
              <th>Capacity</th>
              <th>Enrolled</th>
              <th>Available</th>
              <th>Fee</th>
              <th>Class Time</th>
              <th>Class Date</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($__courses as $row):
              $id = (int)$row['id'];
              $cap = (int)$row['student_capacity'];
              $cnt = (int)$row['student_count'];
              $available = max(0, $cap - $cnt);
              $isMine = isset($__myMap[$id]);
            ?>
            <tr>
              <td>
                <?php if ($__locked): ?>
                  <span class="muted">Locked</span>
                <?php elseif ($isMine): ?>
                  <span style="color:#198754;font-weight:600;">Registered</span>
                <?php elseif ($available > 0): ?>
                  <input type="checkbox" name="courses[]" value="<?= $id ?>">
                <?php else: ?>
                  <span style="color:red;">Full</span>
                <?php endif; ?>
              </td>
              <td><?= h($row['department']) ?></td>
              <td><?= h($row['course_title']) ?></td>
              <td><?= $cap ?></td>
              <td><?= $cnt ?></td>
              <td><?= $available ?></td>
              <td><?= number_format((float)$row['course_fee'], 2) ?></td>
              <td><?= h($row['class_time']) ?></td>
              <td><?= h($row['class_date']) ?></td>
              <td><?= h($row['duration']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php if (!$__locked): ?>
          <p class="muted" style="margin-top:10px;">Select <strong>4–6</strong> courses and submit once to confirm.</p>
          <button type="submit" class="btn-submit">Register Selected Courses</button>
        <?php endif; ?>
      </form>
    </div>

    <div class="form-container" style="margin-top:20px;">
      <h2>My Registered Courses</h2>
      <?php if (empty($__myCourses)): ?>
        <p class="muted">You have not registered any courses yet.</p>
      <?php else: ?>
        <table>
          <tr>
            <th>#</th><th>Department</th><th>Course Title</th>
            <th>Class Time</th><th>Class Date</th><th>Duration</th><th>Fee</th>
          </tr>
          <?php foreach ($__myCourses as $c): ?>
            <tr>
              <td><?= (int)$c['id'] ?></td>
              <td><?= h($c['department']) ?></td>
              <td><?= h($c['course_title']) ?></td>
              <td><?= h($c['class_time']) ?></td>
              <td><?= h($c['class_date']) ?></td>
              <td><?= h($c['duration']) ?></td>
              <td><?= number_format((float)$c['course_fee'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <th colspan="6" style="text-align:right;">Total Fees:</th>
            <th><?= number_format($__totalFee, 2) ?></th>
          </tr>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
