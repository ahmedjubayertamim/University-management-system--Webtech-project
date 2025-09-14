<?php
// Project/Teacher/php/LeaveRequests.php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Forbidden'); }

/* Fetch all leave requests addressed to this teacher */
$sql = "
  SELECT lr.leave_id, lr.student_id, lr.reason, lr.start_date, lr.end_date, lr.status,
         u.first_name, u.last_name, u.email
  FROM leave_requests lr
  JOIN students s ON s.student_id = lr.student_id
  JOIN users u    ON u.id = s.user_id
  WHERE lr.teacher_id = ?
  ORDER BY lr.status='pending' DESC, lr.start_date DESC
";
$q = $conn->prepare($sql);
$q->bind_param("i", $user_id);
$q->execute();
$rows = $q->get_result()->fetch_all(MYSQLI_ASSOC);
$q->close();

/* Flash */
$msg = isset($_GET['ok']) ? 'Updated.' : '';
$err = isset($_GET['err'])? 'Failed.'  : '';
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Student Applications</title>
  <link rel="stylesheet" href="../css/Attendance.css">
</head>
<body>
<header><h1>Student Applications</h1></header>

<div class="content">
  <div class="wrap">
    <?php if ($msg): ?><div class="success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="error"><?= h($err) ?></div><?php endif; ?>

    <?php if (empty($rows)): ?>
      <div class="note">No applications.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Email</th>
            <th>Reason</th>
            <th>From</th>
            <th>To</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php $i=1; foreach ($rows as $r): ?>
          <tr>
            <td style="text-align:center;"><?= $i++ ?></td>
            <td><?= h(trim(($r['first_name']??'').' '.($r['last_name']??''))) ?></td>
            <td><?= h($r['email'] ?? '') ?></td>
            <td style="text-align:left; white-space:pre-wrap;"><?= h($r['reason']) ?></td>
            <td style="text-align:center;"><?= h($r['start_date']) ?></td>
            <td style="text-align:center;"><?= h($r['end_date']) ?></td>
            <td style="text-align:center;"><?= h($r['status']) ?></td>
            <td style="text-align:center;">
              <?php if ($r['status']==='pending'): ?>
                <form method="post" action="update_leave_status.php" style="display:inline">
                  <input type="hidden" name="leave_id" value="<?= (int)$r['leave_id'] ?>">
                  <input type="hidden" name="action" value="approved">
                  <button class="btn" type="submit">Approve</button>
                </form>
                <form method="post" action="update_leave_status.php" style="display:inline">
                  <input type="hidden" name="leave_id" value="<?= (int)$r['leave_id'] ?>">
                  <input type="hidden" name="action" value="rejected">
                  <button class="btn" type="submit">Reject</button>
                </form>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
