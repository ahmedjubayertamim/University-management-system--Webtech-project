<?php
// Project/Student/view/MyApplications.php
session_start();
require_once __DIR__ . '/../php/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ---- Guard: student login only ----
$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') {
    http_response_code(403);
    exit("Login as student");
}

// ---- find this student's student_id ----
$student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$st->bind_result($student_id);
$st->fetch();
$st->close();
if (!$student_id) exit("Student profile not found.");

// ---- fetch all applications submitted by this student ----
$sql = "
  SELECT lr.leave_id, lr.reason, lr.start_date, lr.status,
         u.first_name, u.last_name
  FROM leave_requests lr
  JOIN teachers t ON t.teacher_id = lr.teacher_id
  JOIN users u    ON u.id = t.user_id
  WHERE lr.student_id = ?
  ORDER BY lr.start_date DESC, lr.leave_id DESC
";
$q = $conn->prepare($sql);
$q->bind_param("i", $student_id);
$q->execute();
$res = $q->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
$q->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Applications</title>
  <link rel="stylesheet" href="../css/CourseRegistration.css">
  <style>
    header{background:#3b5998;color:#fff;padding:15px;text-align:center}
    .content{margin-left:240px;padding:20px}
    .wrap{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.08);padding:20px}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{border:1px solid #e6e6e6;padding:10px;text-align:left}
    th{background:#3b5998;color:#fff}
    pre{white-space:pre-wrap;margin:0}
    .pending{color:#666}
    .approved{color:#137333}
    .rejected{color:#b00020}
  </style>
</head>
<body>
<header><h1>My Applications</h1></header>

  <div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="StudentApplication.php">Student Application</a></li>
      <li><a href="MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
       <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="#">Consulting Hours</a></li>
      
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

<div class="content">
  <div class="wrap">
    <h2>Your Submitted Applications</h2>

    <?php if (empty($rows)): ?>
      <p class="pending">You haven’t submitted any applications yet.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Date</th>
          <th>Teacher</th>
          <th>Reason</th>
          <th>Status</th>
        </tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['start_date']) ?></td>
            <td><?= h(trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''))) ?></td>
            <td><pre><?= h($r['reason'] ?? '') ?></pre></td>
            <td class="<?= h($r['status']) ?>"><?= ucfirst($r['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
