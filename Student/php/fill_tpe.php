<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) die("Please log in.");

// resolve student_id for enabled student
$sid = 0;
$q = $conn->prepare(
  "SELECT s.student_id
   FROM students s
   JOIN users u ON u.id = s.user_id
   WHERE s.user_id = ? AND u.role = 'student' AND u.status = 'enabled'
   LIMIT 1"
);
$q->bind_param("i", $user_id);
$q->execute();
$q->bind_result($sid);
if (!$q->fetch()) { $q->close(); die("Not allowed."); }
$q->close();

$course_id = (int)($_GET['course'] ?? 0);
$window_id = (int)($_GET['window'] ?? 0);
if ($course_id <= 0 || $window_id <= 0) die("Bad request.");

// window must be open
$w = $conn->prepare("SELECT name, status FROM tpe_windows WHERE id = ?");
$w->bind_param("i", $window_id);
$w->execute();
$w->bind_result($wname, $wstatus);
$w->fetch();
$w->close();
if ($wstatus !== 'open') die("Window is closed.");

// ✅ ensure THIS student is registered for THIS course (no teacher_id required)
$ok = 0;
$chk = $conn->prepare(
  "SELECT COUNT(*)
   FROM student_course_registrations
   WHERE student_id = ? AND offered_course_id = ?"
);
$chk->bind_param("ii", $sid, $course_id);
$chk->execute();
$chk->bind_result($ok);
$chk->fetch();
$chk->close();

if ($ok <= 0) die("You are not registered for this course.");

// prevent duplicate submission in same window
$dup = $conn->prepare(
  "SELECT id FROM tpe_submissions
   WHERE student_id = ? AND offered_course_id = ? AND window_id = ?"
);
$dup->bind_param("iii", $sid, $course_id, $window_id);
$dup->execute();
$dup->store_result();
if ($dup->num_rows > 0) {
  $dup->close();
  header("Location: ../view/StudentTPE.php?msg=" . urlencode("Already submitted."));
  exit;
}
$dup->close();

// optional: fetch course title to show in header
$ct = '';
$cq = $conn->prepare("SELECT course_title FROM offered_course WHERE id = ?");
$cq->bind_param("i", $course_id);
$cq->execute();
$cq->bind_result($ct);
$cq->fetch();
$cq->close();

// active questions
$qset = $conn->query("SELECT id, text FROM tpe_questions WHERE active = 1 ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Fill TPE</title>
  <link rel="stylesheet" href="/Project/css/style.css">
  <style>
    .box{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);padding:20px;margin:20px}
    .q{margin-bottom:14px}
    .btn{padding:10px 14px;border-radius:8px;background:#2d60ff;color:#fff;border:0;cursor:pointer}
    .back{margin-left:8px;text-decoration:none}
  </style>
</head>
<body>
<header>
  <h1>Evaluate: <?= htmlspecialchars($ct ?: ("Course #".$course_id)) ?></h1>
</header>

<div class="content">
  <div class="box">
    <form method="post" action="submit_tpe.php">
      <input type="hidden" name="course_id" value="<?= $course_id ?>">
      <input type="hidden" name="window_id" value="<?= $window_id ?>">

      <?php while ($q = $qset->fetch_assoc()): ?>
        <div class="q">
          <label style="display:block;margin-bottom:6px;">
            <?= htmlspecialchars($q['text']) ?>
          </label>
          <select name="q[<?= (int)$q['id'] ?>]" required>
            <option value="">-- rate 1 to 5 --</option>
            <option value="1">1 - Poor</option>
            <option value="2">2 - Fair</option>
            <option value="3">3 - Good</option>
            <option value="4">4 - Very Good</option>
            <option value="5">5 - Excellent</option>
          </select>
        </div>
      <?php endwhile; ?>

      <label>Overall comment (optional):</label><br>
      <textarea name="comment" rows="4" style="width:100%;max-width:680px"></textarea><br><br>

      <button class="btn" type="submit">Submit</button>
      <a class="back" href="../view/StudentTPE.php">Back</a>
    </form>
  </div>
</div>
</body>
</html>
