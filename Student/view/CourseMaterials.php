<?php
if (!isset($__courses, $__materials)) { 
  header("Location: /Project/Student/php/CourseMaterials.php"); 
  exit; }
if (!function_exists('h')) { 
  function h($s){ 
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Course Materials</title>
  <link rel="stylesheet" href="../css/teacher.css">
  <link rel="stylesheet" href="../css/CourseMaterials.css">
</head>
<body>
<header>
  <h1>Course Materials</h1>
 
</header>

<div class="sidebar">
    <ul>
      <li><a href="../view/StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
       <li><a href="/Project/Student/php/CourseMaterials.php">Materials</a></li>
      <li><a href="../view/StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="StudentApplication.php">Student Application</a></li>
      <li><a href="MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
       <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="/Project/Student/php/ConsultingHours.php">Consulting Hours</a></li>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

<div class="content">
  <div class="form-container">
    <h2>Available Materials</h2>
    <form method="get" action="/Project/Student/php/CourseMaterials.php" style="margin-bottom:12px;">
      <label>Filter by Course:</label>
      <select name="offered_id" onchange="this.form.submit()">
        <option value="0">All</option>
        <?php foreach($__courses as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= ($__selected_course_id===(int)$c['id'])?'selected':'' ?>>
            <?= h($c['department'].' — '.$c['course_title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <noscript><button class="btn" type="submit">Filter</button></noscript>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Course</th>
          <th>Title</th>
          <th>File</th>
          <th>Uploaded</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($__materials)): ?>
          <tr><td colspan="5" class="muted">No materials yet.</td></tr>
        <?php else: foreach($__materials as $m): ?>
          <tr>
            <td><?= (int)$m['id'] ?></td>
            <td><?= h($m['department'].' — '.$m['course_title']) ?></td>
            <td style="text-align:left;"><?= h($m['title']) ?></td>
            <td><a class="btn" href="<?= h($m['file_path']) ?>" target="_blank">Download</a></td>
            <td><?= h($m['uploaded_at']) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
