<?php
if (!isset($__courses, $__materials)) { header("Location: /Project/Teacher/php/CourseMaterials.php"); exit; }
if (!function_exists('h')) { function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Course Materials</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/CourseMaterials.css">
</head>
<body>
<header><center><h1>Manage Course Materials</h1></center>
  
  
</header>

<div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="/Project/Teacher/php/CourseMaterials.php" class="active">Course Materials</a></li>
    <li><a href="TeacherAttendanceReport.php">Attendance Report</a></li>
    <li><a href="SubmitGrades.php">Submit Grades</a></li>
    <li><a href="StudentApplications.php">Student Applications</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  <?php if (!empty($__msg)): ?><div class="success"><?= h($__msg) ?></div><?php endif; ?>
  <?php if (!empty($__err)): ?><div class="error"><?= h($__err) ?></div><?php endif; ?>

  <div class="form-container">
    <h2>Upload Course Materials</h2>
    <form action="/Project/Teacher/php/upload_material.php" method="POST" enctype="multipart/form-data">
      <label for="course">Select Course:</label>
      <select id="course" name="offered_course_id" required>
        <option value="">-- Choose Course --</option>
        <?php foreach($__courses as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= ($__selected_course_id===(int)$c['id'])?'selected':'' ?>>
            <?= h($c['department'].' — '.$c['course_title']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="title">Material Title:</label>
      <input type="text" id="title" name="title" placeholder="e.g., Week 1 Lecture Notes" required>

      <label for="file">Upload File:</label>
      <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip" required>

      <button type="submit">Upload</button>
    </form>
  </div>

  <div class="box">
    <h2>Uploaded Materials</h2>

    <form method="get" action="/Project/Teacher/php/CourseMaterials.php" style="margin-bottom:12px;">
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
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($__materials)): ?>
          <tr><td colspan="6" class="muted">No materials uploaded yet.</td></tr>
        <?php else: foreach($__materials as $m): ?>
          <tr>
            <td><?= (int)$m['id'] ?></td>
            <td><?= h($m['department'].' — '.$m['course_title']) ?></td>
            <td style="text-align:left;"><?= h($m['title']) ?></td>
            <td><a class="btn" href="<?= h($m['file_path']) ?>" target="_blank">Download</a></td>
            <td><?= h($m['uploaded_at']) ?></td>
            <td><a class="btn" style="background:#c62828" href="/Project/Teacher/php/delete_material.php?id=<?= (int)$m['id'] ?>" onclick="return confirm('Delete this material?');">Delete</a></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
