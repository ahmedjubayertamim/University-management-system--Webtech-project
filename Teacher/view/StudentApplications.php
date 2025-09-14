<?php
$rows = $rows ?? [];
$ok   = $ok   ?? false;
$err  = $err  ?? false;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Applications</title>
  <link rel="stylesheet" href="../css/Attendance.css">
  <link rel="stylesheet" href="../css/StudentApplicationStyle.css">
</head>
<body>
<header><h1>Student Applications</h1></header>

<div class="sidebar">
  <ul>
    <li><a href="../php/TeacherDashboard.php">Dashboard</a></li>
    <li><a href="../php/CourseMaterials.php">Manage Course Materials</a></li>
    <li><a href="../php/TeacherAttendance.php">Manage Attendance</a></li>
    <li><a href="../php/SubmitGrades.php">Submit Grades</a></li>
    <li><a href="../php/SetConsulting.php">Consulting Hours</a></li>
    <li><a href="../php/StudentApplications.php" class="active">Approve Student Requests</a></li>
    <li><a href="../php/ViewSalary.php">View Salary</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <h2>Applications addressed to you</h2>

    <?php if ($ok): ?><div class="flash-ok">Status updated.</div><?php endif; ?>
    <?php if ($err): ?><div class="flash-err">Could not update status.</div><?php endif; ?>

    <?php if (empty($rows)): ?>
      <p class="pending">No applications found.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Student</th>
          <th>Email</th>
          <th>Date</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h(trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''))) ?></td>
            <td><?= h($r['email'] ?? '') ?></td>
            <td><?= h($r['start_date'] ?? '') ?></td>
            <td><pre><?= h($r['reason'] ?? '') ?></pre></td>
            <td class="<?= h($r['status'] ?? '') ?>"><?= h(ucfirst($r['status'] ?? '')) ?></td>
            <td>
              <?php if (($r['status'] ?? '') === 'pending'): ?>
                <form method="post" action="../php/update_application.php" style="display:inline">
                  <input type="hidden" name="leave_id" value="<?= (int)($r['leave_id'] ?? 0) ?>">
                  <input type="hidden" name="action" value="approved">
                  <button type="submit" class="btn approve">Approve</button>
                </form>
                <form method="post" action="../php/update_application.php" style="display:inline">
                  <input type="hidden" name="leave_id" value="<?= (int)($r['leave_id'] ?? 0) ?>">
                  <input type="hidden" name="action" value="rejected">
                  <button type="submit" class="btn reject">Reject</button>
                </form>
              <?php else: ?>
                —
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
