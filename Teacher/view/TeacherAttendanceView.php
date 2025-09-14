<?php

if (!isset($teacherName)) {
  echo '<div style="background:#fee;border:1px solid #fbb;padding:10px;border-radius:6px;margin:12px">
  Please access this page via <code>/Project/Teacher/php/TeacherAttendance.php</code>.
</div>';
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Attendance</title>
  <link rel="stylesheet" href="../css/Attendance.css">


</head>
<body>
<header>
  <h1>Attendance</h1>
  
</header>

<div class="sidebar">
  <ul>
    <li><a href="/Project/Teacher/view/TeacherDashboard.php">Dashboard</a></li>
    <li><a href="/Project/Teacher/view/TeacherAttendanceView.php">Attendance</a></li>
    <li><a href="/Project/Teacher/php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <h2>Welcome Teacher</h2>
    <p class="note">Hello <strong><?= htmlspecialchars($teacherName) ?></strong></p>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (empty($teacherCourses)): ?>
      <div class="note">No courses are currently assigned to you.</div>
    <?php else: ?>
      <h3>Select one of your courses</h3>

      <form method="get" action="/Project/Teacher/php/TeacherAttendance.php" class="controls">
        <label>Course:</label>
        <select name="course_id" required>
          <option value="">-- choose --</option>
          <?php foreach ($teacherCourses as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= ((int)$c['id'] === (int)$selectedCourseId) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['course_title']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($attendanceDate) ?>" required>

        <button type="submit" class="btn">Load</button>
      </form>

      <?php if ($selectedCourseId > 0): ?>
        <?php if (empty($students_rows)): ?>
          <div class="note">No students have registered for this course.</div>
        <?php else: ?>
          <form method="post" action="/Project/Teacher/php/TeacherAttendance.php" class="wrap" style="margin-top:10px">
            <input type="hidden" name="course_id" value="<?= (int)$selectedCourseId ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($attendanceDate) ?>">

            <h3>Mark attendance for <?= htmlspecialchars($attendanceDate) ?></h3>

            <table>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Email</th>
                <th>Status</th>
              </tr>
              <?php
              $n=1;
              foreach ($students_rows as $stu):
                  $sid = (int)$stu['student_id'];
                  $full = trim(($stu['first_name'] ?? '') . ' ' . ($stu['last_name'] ?? ''));
                  $email = $stu['email'] ?? '';
                  $cur = $att_map[$sid] ?? ''; // present/absent/late or ''
              ?>
                <tr>
                  <td style="text-align:center;"><?= $n++ ?></td>
                  <td><?= htmlspecialchars($full) ?></td>
                  <td><?= htmlspecialchars($email) ?></td>
                  <td>
                    <select name="status[<?= $sid ?>]">
                      <option value=""        <?= $cur===''          ? 'selected':'' ?>>— choose —</option>
                      <option value="present" <?= $cur==='present'   ? 'selected':'' ?>>present</option>
                      <option value="absent"  <?= $cur==='absent'    ? 'selected':'' ?>>absent</option>
                      <option value="late"    <?= $cur==='late'      ? 'selected':'' ?>>late</option>
                    </select>
                  </td>
                </tr>
              <?php endforeach; ?>
            </table>

            <div style="margin-top:12px">
              <button type="submit" name="save_attendance" class="btn">Save Attendance</button>
            </div>
          </form>
        <?php endif; ?>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</div>
</body>
</html>
