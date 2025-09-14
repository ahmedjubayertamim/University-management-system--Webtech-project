<?php
session_start();
require_once __DIR__ . '/../php/config.php';

// Ensure teacher login
$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') {
  http_response_code(403);
  exit("Login as teacher required.");
}

// Resolve teacher_id
$teacher_id = 0;
$st = $conn->prepare("SELECT teacher_id FROM teachers WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute(); $st->bind_result($teacher_id); $st->fetch(); $st->close();
if (!$teacher_id) die("Teacher profile not found.");

$msg = $_GET['msg'] ?? "";

// Fetch existing slots
$slots = [];
$q = $conn->prepare("SELECT consult_id, day_of_week, start_time, end_time 
                     FROM consulting_hours WHERE teacher_id=? ORDER BY FIELD(day_of_week,'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), start_time");
$q->bind_param("i", $teacher_id);
$q->execute();
$res = $q->get_result();
while($row=$res->fetch_assoc()) $slots[]=$row;
$q->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Set Consulting Hours</title>
  <link rel="stylesheet" href="../css/CourseMaterials.css">
  <style>
    .form-box { background:#fff; padding:20px; border-radius:8px; margin:20px auto; max-width:700px; box-shadow:0 2px 6px rgba(0,0,0,.1); }
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #ddd;padding:8px;text-align:center}
    th{background:#3b5998;color:#fff}
    .btn{padding:8px 12px;border-radius:6px;background:#2d60ff;color:#fff;text-decoration:none;border:none;cursor:pointer}
    .btn-danger{background:#c1121f}
  </style>
</head>
<body>
<header><h1>Set Consulting Hours</h1></header>

<div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="SetConsulting.php" class="active">Consulting Hours</a></li>
    <li><a href="CourseMaterials.php">Course Materials</a></li>
    <li><a href="SubmitGrades.php">Submit Grades</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="form-box">
    <?php if ($msg): ?><div class="note"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <form method="post" action="../php/save_consulting.php">
      <label>Day of Week</label>
      <select name="day_of_week" required>
        <option value="">-- choose --</option>
        <?php foreach (['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d): ?>
          <option value="<?= $d ?>"><?= $d ?></option>
        <?php endforeach; ?>
      </select>

      <label>Start Time</label>
      <input type="time" name="start_time" required>

      <label>End Time</label>
      <input type="time" name="end_time" required>

      <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
      <button type="submit" class="btn">Add Slot</button>
    </form>
  </div>

  <div class="form-box">
    <h2>My Consulting Hours</h2>
    <?php if (empty($slots)): ?>
      <p class="muted">No consulting hours set.</p>
    <?php else: ?>
      <table>
        <tr><th>Day</th><th>Start</th><th>End</th><th>Action</th></tr>
        <?php foreach ($slots as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['day_of_week']) ?></td>
            <td><?= htmlspecialchars($s['start_time']) ?></td>
            <td><?= htmlspecialchars($s['end_time']) ?></td>
            <td><a class="btn btn-danger" href="../php/delete_consulting.php?id=<?= $s['consult_id'] ?>" onclick="return confirm('Delete this slot?')">Delete</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
