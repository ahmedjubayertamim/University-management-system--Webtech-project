<?php
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$msg   = $msg   ?? '';
$slots = $slots ?? [];
$teacher_id = $teacher_id ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Set Consulting Hours</title>
  <link rel="stylesheet" href="../css/CourseMaterials.css">
  <link rel="stylesheet" href="../ConsultingHourStyle.css">
</head>
<body>
<header><h1>Set Consulting Hours</h1></header>

<div class="sidebar">
  <ul>
    <li><a href="../php/TeacherDashboard.php">Dashboard</a></li>
    <li><a href="../php/CourseMaterials.php">Manage Course Materials</a></li>
    <li><a href="../php/TeacherAttendance.php">Manage Attendance</a></li>
    <li><a href="../php/SubmitGrades.php">Submit Grades</a></li>
    <li><a href="../php/SetConsulting.php" class="active">Consulting Hours</a></li>
    <li><a href="../php/StudentApplications.php">Approve Student Requests</a></li>
    <li><a href="../php/ViewSalary.php">View Salary</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="form-box">
    <?php if ($msg): ?><div class="note"><?= h($msg) ?></div><?php endif; ?>

    <form method="post" action="../php/save_consulting.php">
      <label>Day of Week</label>
      <select name="day_of_week" required>
        <option value="">-- choose --</option>
        <?php foreach (['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d): ?>
          <option value="<?= h($d) ?>"><?= h($d) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Start Time</label>
      <input type="time" name="start_time" required>

      <label>End Time</label>
      <input type="time" name="end_time" required>

      <input type="hidden" name="teacher_id" value="<?= (int)$teacher_id ?>">
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
            <td><?= h($s['day_of_week']) ?></td>
            <td><?= h($s['start_time']) ?></td>
            <td><?= h($s['end_time']) ?></td>
            <td><a class="btn btn-danger" href="../php/delete_consulting.php?id=<?= (int)$s['consult_id'] ?>" onclick="return confirm('Delete this slot?')">Delete</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
