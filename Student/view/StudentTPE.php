<?php
session_start();
require_once __DIR__ . '/../php/config.php';

function get_or_create_student_id(mysqli $conn, int $user_id): int {
  $role=null; $status=null;
  $q=$conn->prepare("SELECT role,status FROM users WHERE id=?");
  $q->bind_param("i",$user_id);
  $q->execute(); $q->bind_result($role,$status); $q->fetch(); $q->close();
  if ($role!=='student' || $status!=='enabled') return 0;

  $sid=0;
  $q=$conn->prepare("SELECT student_id FROM students WHERE user_id=?");
  $q->bind_param("i",$user_id);
  $q->execute(); $q->bind_result($sid);
  if ($q->fetch()){ $q->close(); return (int)$sid; }
  $q->close();

  $ins=$conn->prepare("INSERT INTO students(user_id) VALUES (?)");
  $ins->bind_param("i",$user_id);
  if(!$ins->execute()){ $ins->close(); return 0; }
  $new=(int)$conn->insert_id; $ins->close(); return $new;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id<=0) die("Please log in.");
$student_id = get_or_create_student_id($conn,$user_id);
if ($student_id<=0) die("Not allowed.");

$msg = $_GET['msg'] ?? "";


$win = $conn->query("SELECT id,name FROM tpe_windows WHERE status='open' ORDER BY id DESC LIMIT 1")->fetch_assoc();
$window_id = $win['id'] ?? null;

$sql = "SELECT oc.id, oc.course_title, oc.department, oc.class_time, oc.class_date, oc.duration
        FROM student_course_registrations scr
        JOIN offered_course oc ON oc.id = scr.offered_course_id
        WHERE scr.student_id = ?
        ORDER BY oc.id DESC";

$st = $conn->prepare($sql);
$st->bind_param("i",$student_id);
$st->execute();
$courses = $st->get_result();


$submitted = [];
if ($window_id) {
  $st2=$conn->prepare("SELECT offered_course_id FROM tpe_submissions WHERE student_id=? AND window_id=?");
  $st2->bind_param("ii",$student_id,$window_id);
  $st2->execute();
  $r=$st2->get_result();
  while($row=$r->fetch_assoc()) $submitted[(int)$row['offered_course_id']] = true;
  $st2->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Student TPE</title>
  <link rel="stylesheet" href="/Project/css/style.css">
  <style>
    .box{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);padding:20px;margin-bottom:20px}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #e6e6e6;padding:10px;text-align:center}
    th{background:#3b5998;color:#fff}
    .btn{display:inline-block;padding:8px 12px;border-radius:6px;background:#2d60ff;color:#fff;text-decoration:none}
    .btn:disabled{opacity:.6;cursor:not-allowed}
    .note{background:#eef7ff;border:1px solid #cfe4ff;padding:8px 10px;border-radius:8px;margin-bottom:12px}
    .error{background:#ffe9e9;border:1px solid #ffc3c3;padding:8px 10px;border-radius:8px;margin-bottom:12px}
  </style>
</head>
<body>
<header>
  <h1>Teacher Performance Evaluation</h1>
  <div class="search-box"><input placeholder="Search..."><button>Search</button></div>
</header>

<div class="sidebar">
  <ul>
    <li><a href="StudentDashboard.php">Dashboard</a></li>
    <li><a href="CourseRegistration.php">Register Courses</a></li>
    <li><a href="PayFees.php">Pay Fees</a></li>
    <li><a href="StudentLibrary.php">Library</a></li>
    <li><a href="StudentTPE.php">Submit TPE</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <?php if($msg): ?>
    <div class="<?= stripos($msg,'fail')!==false ? 'error':'note' ?>"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <div class="box">
    <?php if(!$window_id): ?>
      <h2>No active TPE window</h2>
      <p class="muted">Admin has not opened TPE yet. Please check back later.</p>
    <?php else: ?>
      <h2>Active Window: <?= htmlspecialchars($win['name']) ?></h2>
      <table>
        <tr>
          <th>#</th><th>Department</th><th>Course Title</th>
          <th>Class Time</th><th>Class Date</th><th>Duration</th><th>Action</th>
        </tr>
        <?php while($c=$courses->fetch_assoc()): 
          $cid = (int)$c['id'];
          $done = isset($submitted[$cid]);
        ?>
        <tr>
          <td><?= $cid ?></td>
          <td><?= htmlspecialchars($c['department']) ?></td>
          <td><?= htmlspecialchars($c['course_title']) ?></td>
          <td><?= htmlspecialchars($c['class_time']) ?></td>
          <td><?= htmlspecialchars($c['class_date']) ?></td>
          <td><?= htmlspecialchars($c['duration']) ?></td>
          <td>
            <?php if($done): ?>
              <a class="btn" href="../php/view_tpe.php?course=<?= $cid ?>&window=<?= $window_id ?>">View</a>
            <?php else: ?>
              <a class="btn" href="../php/fill_tpe.php?course=<?= $cid ?>&window=<?= $window_id ?>">Evaluate</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
