<?php
$courses = $courses ?? [];
$course_id = $course_id ?? 0;
$from = $from ?? date('Y-m-01');
$to = $to ?? date('Y-m-d');
$course_ok = $course_ok ?? false;
$course_title = $course_title ?? '';
$rows = $rows ?? [];
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html>
<head>
 
  <title>Attendance Report</title>
  <link rel="stylesheet" href="../css/Attendance.css">
</head>
<body>
<header>
  <h1>Attendance</h1>
</header>

<div class="sidebar">
  <ul>
    <li><a href="Teacherdashboard.php">Dashboard</a></li>
    <li><a href="MyCourses.php">My Assigned Courses</a></li>
    <li><a href="../php/TeacherAttendanceReport.php" class="active">Attendance Report</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <h2>Filter</h2>
    <form method="get" action="../php/TeacherAttendanceReport.php" class="grid">
      <div>
        <label for="course_id">Course</label>
        <select id="course_id" name="course_id" required>
          <option value="">-- choose --</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $course_id===(int)$c['id']?'selected':'' ?>>
              <?= h(($c['department'] ?? '').' — '.($c['course_title'] ?? '')) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="from">From</label>
        <input type="date" id="from" name="from" value="<?= h($from) ?>">
      </div>
      <div>
        <label for="to">To</label>
        <input type="date" id="to" name="to" value="<?= h($to) ?>">
      </div>
      <div>
        <label>&nbsp;</label>
        <button class="btn" type="submit">Load</button>
      </div>
    </form>
  </div>

  <div class="wrap">
    <h2>
      <?= $course_ok ? h($course_title) : 'Select a course' ?>
      <?php if ($course_ok): ?>
        <span class="muted" style="font-size:14px;">(<?= h($from) ?> → <?= h($to) ?>)</span>
      <?php endif; ?>
    </h2>

    <?php if ($course_id && !$course_ok): ?>
      <p class="muted">You are not assigned to this course.</p>
    <?php elseif ($course_ok && empty($rows)): ?>
      <p class="muted">No attendance records in the selected range.</p>
    <?php elseif ($course_ok): ?>
      <table>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Student ID</th>
          <th>Student Name</th>
          <th>Email</th>
          <th>Status</th>
        </tr>
        <?php $i=1; foreach ($rows as $r):
          $name = trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''));
          $status = strtolower((string)($r['status'] ?? ''));
          $tagClass = $status==='present'?'present':($status==='late'?'late':'absent');
        ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= h($r['date'] ?? '') ?></td>
            <td><?= (int)($r['student_id'] ?? 0) ?></td>
            <td style="text-align:left;"><?= h($name ?: '—') ?></td>
            <td style="text-align:left;"><?= h($r['email'] ?? '') ?></td>
            <td><span class="tag <?= h($tagClass) ?>"><?= h(ucfirst($status)) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
