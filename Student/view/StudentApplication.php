<?php
// Project/Student/view/StudentApplication.php
session_start();
require_once __DIR__ . '/../php/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') { http_response_code(403); exit('Login as student'); }

/* Find this student's student_id */
$student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$st->bind_result($student_id);
$st->fetch(); $st->close();
if (!$student_id) { exit('Student profile not found.'); }

/* Teachers from student's registered courses */
$teachers = [];
$sql = "
  SELECT DISTINCT u.id AS teacher_id, u.first_name, u.last_name
  FROM student_course_registrations scr
  JOIN offered_course o ON o.id = scr.offered_course_id
  JOIN users u ON u.id = o.teacher_id
  WHERE scr.student_id = ?
  ORDER BY u.first_name, u.last_name
";
$q = $conn->prepare($sql);
$q->bind_param("i", $student_id);
$q->execute();
$res = $q->get_result();
while ($row = $res->fetch_assoc()) $teachers[] = $row;
$q->close();

/* Flash messages via query string (optional) */
$msg  = isset($_GET['ok'])   ? 'Application submitted.' : '';
$err  = isset($_GET['err'])  ? 'Failed to submit.'      : '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Application</title>
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../../css/Attendance.css">
</head>
<body>
<header>
  <h1>Student Dashboard</h1>
  <div class="search-box"><input type="text" placeholder="Search..."><button>Search</button></div>
</header>

<div class="sidebar">
  <ul>
    <li><a href="StudentDashboard.php">Dashboard</a></li>
    <li><a href="CourseRegistration.php">Register Courses</a></li>
    <li><a href="PayFees.php">Pay Fees</a></li>
    <li><a href="StudentAddDrop.php">Add/Drop</a></li>
    <li><a href="StudentLibrary.php">Library</a></li>
    <li><a href="StudentApplication.php">Student Application</a></li>
    <li><a href="StudentTPE.php">Submit TPE</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap" style="max-width:720px;margin:20px auto;">
    <h2>Student Application Form</h2>
    <?php if ($msg): ?><div class="success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="error"><?= h($err) ?></div><?php endif; ?>

    <form action="../php/submit_application.php" method="POST">
      <input type="hidden" name="student_id" value="<?= (int)$student_id ?>">

      <label for="teacher">Select Teacher</label>
      <select id="teacher" name="teacher_id" required>
        <option value="">-- Choose Teacher --</option>
        <?php foreach ($teachers as $t): ?>
          <option value="<?= (int)$t['teacher_id'] ?>">
            <?= h(trim(($t['first_name']??'').' '.($t['last_name']??''))) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="application_type">Application Type</label>
      <select id="application_type" name="application_type" required>
        <option value="">-- Select Type --</option>
        <option value="leave">Leave Request</option>
        <option value="extension">Project Extension</option>
        <option value="special">Special Consideration</option>
        <option value="other">Other</option>
      </select>

      <label for="subject">Subject</label>
      <input type="text" id="subject" name="subject" placeholder="Enter subject" required>

      <label for="details">Details</label>
      <textarea id="details" name="details" placeholder="Write your application here..." required></textarea>

      <label>Start Date</label>
      <input type="date" name="start_date" required>

      <label>End Date</label>
      <input type="date" name="end_date" required>

      <button type="submit" class="btn">Submit Application</button>
    </form>
  </div>
</div>
</body>
</html>
