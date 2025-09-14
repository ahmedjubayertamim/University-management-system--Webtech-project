<?php
session_start();
require_once __DIR__ . '/config.php';
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') { http_response_code(403); exit('Forbidden'); }

$student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute(); $st->bind_result($student_id); $st->fetch(); $st->close();

$rows=[];
$sql = "
  SELECT lr.leave_id, lr.reason, lr.start_date, lr.end_date, lr.status,
         u.first_name AS t_first, u.last_name AS t_last
  FROM leave_requests lr
  JOIN users u ON u.id = lr.teacher_id
  WHERE lr.student_id = ?
  ORDER BY lr.start_date DESC
";
$q=$conn->prepare($sql);
$q->bind_param("i", $student_id);
$q->execute();
$rows=$q->get_result()->fetch_all(MYSQLI_ASSOC);
$q->close();
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>My Applications</title>
<link rel="stylesheet" href="../../css/Attendance.css"></head>
<body>
<header><h1>My Applications</h1></header>
<div class="content"><div class="wrap">
  <?php if(empty($rows)): ?><div class="note">No applications.</div>
  <?php else: ?>
  <table>
    <thead><tr><th>#</th><th>Teacher</th><th>Reason</th><th>From</th><th>To</th><th>Status</th></tr></thead>
    <tbody>
      <?php $i=1; foreach($rows as $r): ?>
      <tr>
        <td style="text-align:center;"><?= $i++ ?></td>
        <td><?= h(trim(($r['t_first']??'').' '.($r['t_last']??''))) ?></td>
        <td style="white-space:pre-wrap"><?= h($r['reason']) ?></td>
        <td style="text-align:center;"><?= h($r['start_date']) ?></td>
        <td style="text-align:center;"><?= h($r['end_date']) ?></td>
        <td style="text-align:center;"><?= h($r['status']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div></div>
</body></html>
