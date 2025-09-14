<?php
// Project/Teacher/view/StudentApplications.php
session_start();
require_once __DIR__ . '/../php/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ---- Guard: must be logged in as teacher ----
$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') {
    http_response_code(403);
    exit("Please log in as a teacher.");
}

// ---- Resolve this teacher's teacher_id from users.id ----
$teacher_id = 0;
$stmt = $conn->prepare("SELECT teacher_id FROM teachers WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($teacher_id);
$stmt->fetch();
$stmt->close();
if (!$teacher_id) {
    exit("Teacher profile not found (no row in 'teachers' for this user).");
}

// ---- Fetch applications addressed to this teacher ----
// NOTE: leave_requests has start_date/end_date, no application_date
$sql = "
  SELECT lr.leave_id, lr.reason, lr.start_date, lr.status,
         u.first_name, u.last_name, u.email
  FROM leave_requests lr
  JOIN students s ON s.student_id = lr.student_id
  JOIN users u    ON u.id = s.user_id
  WHERE lr.teacher_id = ?
  ORDER BY lr.start_date DESC, lr.leave_id DESC
";
$q = $conn->prepare($sql);
$q->bind_param("i", $teacher_id);
$q->execute();
$res = $q->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
$q->close();

$ok  = isset($_GET['ok']);
$err = isset($_GET['err']);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Applications</title>
  <link rel="stylesheet" href="../../css/Attendance.css">
  <style>
    header{background:#3b5998;color:#fff;padding:15px;text-align:center}
    .content{margin-left:240px;padding:20px}
    .wrap{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.08);padding:20px}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{border:1px solid #e6e6e6;padding:10px;text-align:left}
    th{background:#3b5998;color:#fff}
    .btn{padding:8px 12px;border:0;border-radius:6px;cursor:pointer}
    .approve{background:#2d9c3c;color:#fff}
    .reject{background:#c0392b;color:#fff}
    .pending{color:#666}
    .approved{color:#137333}
    .rejected{color:#b00020}
    pre{white-space:pre-wrap;margin:0}
    .flash-ok{background:#e9ffe9;border:1px solid #b8f0b8;padding:10px;border-radius:8px;margin-bottom:10px}
    .flash-err{background:#ffe9e9;border:1px solid #ffc3c3;padding:10px;border-radius:8px;margin-bottom:10px}
    .sidebar{width:220px;background:#2c3e50;color:#fff;position:fixed;top:0;left:0;height:100%;padding-top:70px;box-shadow:2px 0 5px rgba(0,0,0,.2)}
    .sidebar ul{list-style:none;margin:0;padding:0}
    .sidebar li{border-bottom:1px solid rgba(255,255,255,.1)}
    .sidebar a{display:block;color:#fff;text-decoration:none;padding:12px 20px}
    .sidebar a:hover{background:#3b5998}
  </style>
</head>
<body>
<header><h1>Student Applications</h1></header>

<div class="sidebar">
  <ul>
    <li><a href="Teacherdashboard.php">Dashboard</a></li>
    <li><a href="MyCourses.php">My Assigned Courses</a></li>
    <li><a href="StudentApplications.php">Student Applications</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
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
            <td class="<?= h($r['status']) ?>"><?= h(ucfirst($r['status'])) ?></td>
            <td>
              <?php if (($r['status'] ?? '') === 'pending'): ?>
                <form method="post" action="../php/update_application.php" style="display:inline">
                  <input type="hidden" name="leave_id" value="<?= (int)$r['leave_id'] ?>">
                  <input type="hidden" name="action" value="approved">
                  <button type="submit" class="btn approve">Approve</button>
                </form>
                <form method="post" action="../php/update_application.php" style="display:inline">
                  <input type="hidden" name="leave_id" value="<?= (int)$r['leave_id'] ?>">
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
