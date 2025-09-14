<?php
// Project/Teacher/view/TeacherAttendanceReport.php
session_start();
require_once __DIR__ . '/../php/config.php';   // $conn = new mysqli(...)

// -- simple guard: must be logged in as enabled teacher
$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') {
    http_response_code(403);
    exit("Please log in as a teacher.");
}

// fetch courses assigned to this teacher
$courses = [];
$stmt = $conn->prepare("SELECT id, course_title, department FROM offered_course WHERE teacher_id=? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $courses[] = $row;
$stmt->close();

// input filters
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$from      = trim($_GET['from'] ?? '');
$to        = trim($_GET['to']   ?? '');
if ($from === '') $from = date('Y-m-01');          // default: first day of this month
if ($to   === '') $to   = date('Y-m-d');           // default: today

// verify the selected course belongs to this teacher
$course_ok = false;
$course_title = '';
if ($course_id > 0) {
    $chk = $conn->prepare("SELECT course_title FROM offered_course WHERE id=? AND teacher_id=? LIMIT 1");
    $chk->bind_param("ii", $course_id, $user_id);
    $chk->execute();
    $chk->bind_result($course_title);
    if ($chk->fetch()) $course_ok = true;
    $chk->close();
}

// load attendance if a valid course is selected
$rows = [];
if ($course_ok) {
    // ensure date order
    $fromSql = min($from, $to);
    $toSql   = max($from, $to);

    $q = $conn->prepare("
        SELECT a.attendance_id, a.date, a.status,
               s.student_id,
               u.first_name, u.last_name, u.email
        FROM attendance a
        JOIN students s ON s.student_id = a.student_id
        JOIN users u    ON u.id = s.user_id
        WHERE a.course_id = ?
          AND a.date BETWEEN ? AND ?
        ORDER BY a.date DESC, u.first_name, u.last_name
    ");
    $q->bind_param("iss", $course_id, $fromSql, $toSql);
    $q->execute();
    $r = $q->get_result();
    while ($row = $r->fetch_assoc()) $rows[] = $row;
    $q->close();
}
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
    <li><a href="TeacherAttendanceReport.php">Attendance Report</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <h2>Filter</h2>
    <form method="get" class="grid">
      <div>
        <label for="course_id">Course</label>
        <select id="course_id" name="course_id" required>
          <option value="">-- choose --</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $course_id===(int)$c['id']?'selected':'' ?>>
              <?= htmlspecialchars($c['department'].' — '.$c['course_title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="from">From</label>
        <input type="date" id="from" name="from" value="<?= htmlspecialchars($from) ?>">
      </div>
      <div>
        <label for="to">To</label>
        <input type="date" id="to" name="to" value="<?= htmlspecialchars($to) ?>">
      </div>
      <div>
        <label>&nbsp;</label>
        <button class="btn" type="submit">Load</button>
      </div>
    </form>
  </div>

  <div class="wrap">
    <h2>
      <?= $course_ok ? htmlspecialchars($course_title) : 'Select a course' ?>
      <?php if ($course_ok): ?>
        <span class="muted" style="font-size:14px;">(<?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?>)</span>
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
        <?php
          $i=1;
          foreach ($rows as $r):
            $name = trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''));
            $tagClass = ($r['status']==='present'?'present':($r['status']==='late'?'late':'absent'));
        ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($r['date']) ?></td>
            <td><?= (int)$r['student_id'] ?></td>
            <td style="text-align:left;"><?= htmlspecialchars($name ?: '—') ?></td>
            <td style="text-align:left;"><?= htmlspecialchars($r['email'] ?? '') ?></td>
            <td><span class="tag <?= $tagClass ?>"><?= htmlspecialchars(ucfirst($r['status'])) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
