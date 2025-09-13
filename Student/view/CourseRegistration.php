<?php
session_start();
require_once __DIR__ . '/../php/config.php';

function get_or_create_student_id(mysqli $conn, int $user_id): int {
    // confirm user is an enabled student
    $role = $status = null;
    $q = $conn->prepare("SELECT role, status FROM users WHERE id=? LIMIT 1");
    $q->bind_param("i", $user_id);
    $q->execute();
    $q->bind_result($role, $status);
    $q->fetch();
    $q->close();

    if ($role !== 'student' || $status !== 'enabled') {
        return 0;
    }

    // find existing link
    $sid = 0;
    $q = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
    $q->bind_param("i", $user_id);
    $q->execute();
    $q->bind_result($sid);
    if ($q->fetch()) {
        $q->close();
        return (int)$sid;
    }
    $q->close();

    // create if missing (adjust columns if your students table has NOT NULL extras)
    $ins = $conn->prepare("INSERT INTO students (user_id) VALUES (?)");
    if (!$ins) return 0;
    $ins->bind_param("i", $user_id);
    if (!$ins->execute()) { $ins->close(); return 0; }
    $newId = (int)$conn->insert_id;
    $ins->close();
    return $newId;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { die("Please log in."); }

$student_id = get_or_create_student_id($conn, $user_id);
if ($student_id <= 0) { die("Your account is not allowed to register courses (must be an enabled student)."); }

$msg = isset($_GET['msg']) ? $_GET['msg'] : "";

/* -------- Data for page ---------- */
$courses = $conn->query("SELECT * FROM offered_course ORDER BY id DESC");

/* Already registered (to show Registered badge & list) */
$my = [];
$q = $conn->prepare("SELECT offered_course_id FROM student_course_registrations WHERE student_id=?");
$q->bind_param("i", $student_id);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) $my[(int)$row['offered_course_id']] = true;
$q->close();

/* Lock status (if locked, hide the selection UI) */
$locked = 0;
$q = $conn->prepare("SELECT locked FROM student_registration_locks WHERE student_id=?");
$q->bind_param("i", $student_id);
$q->execute();
$q->bind_result($locked);
$q->fetch();
$q->close();

/* My Registered Courses with fee total */
$mineStmt = $conn->prepare("
  SELECT oc.*
  FROM student_course_registrations scr
  JOIN offered_course oc ON oc.id = scr.offered_course_id
  WHERE scr.student_id=?
  ORDER BY scr.id DESC
");
$mineStmt->bind_param("i", $student_id);
$mineStmt->execute();
$mineRes = $mineStmt->get_result();
$myCourses = [];
$totalFee = 0.0;
while ($row = $mineRes->fetch_assoc()) {
    $myCourses[] = $row;
    $totalFee += (float)$row['course_fee'];
}
$mineStmt->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Student Course Registration</title>
  
 <link rel="stylesheet" href="../css/CourseRegistration.css">


 

</head>
<body>
  <!-- Header -->
  <header>
    <h1>Student Course Registration</h1>
    <div class="search-box">
      <input type="text" placeholder="Search...">
      <button>Search</button>
    </div>
  </header>

  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="#">Consulting Hours</a></li>
      <li><a href="StudentApplecation.php">Student Applecation</a></li>
      <li><a href="#">Download Transcript</a></li>
      <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="#">Profile Settings</a></li>
      <li><a href="../php/logout.php">Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="form-container">
      <h2>Available Courses</h2>

      <?php if ($msg): ?>
        <div class="<?= (stripos($msg,'fail')!==false || stripos($msg,'error')!==false)?'error':'note' ?>">
          <?= htmlspecialchars($msg) ?>
        </div>
      <?php endif; ?>

      <?php if ((int)$locked === 1): ?>
        <div class="note">Your registration is confirmed and locked. You cannot add/drop more courses.</div>
      <?php endif; ?>

      <form action="../php/register_course.php" method="POST">
        <table>
          <thead>
            <tr>
              <th>Select</th>
              <th>Department</th>
              <th>Course Title</th>
              <th>Capacity</th>
              <th>Enrolled</th>
              <th>Available</th>
              <th>Fee</th>
              <th>Class Time</th>
              <th>Class Date</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $courses->fetch_assoc()):
              $id = (int)$row['id'];
              $cap = (int)$row['student_capacity'];
              $cnt = (int)$row['student_count'];
              $available = max(0, $cap - $cnt);
              $isMine = isset($my[$id]);
            ?>
            <tr>
              <td>
                <?php if ($locked): ?>
                  <span class="muted">Locked</span>
                <?php elseif ($isMine): ?>
                  <span style="color:#198754;font-weight:600;">Registered</span>
                <?php elseif ($available > 0): ?>
                  <input type="checkbox" name="courses[]" value="<?= $id ?>">
                <?php else: ?>
                  <span style="color:red;">Full</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['department']) ?></td>
              <td><?= htmlspecialchars($row['course_title']) ?></td>
              <td><?= $cap ?></td>
              <td><?= $cnt ?></td>
              <td><?= $available ?></td>
              <td><?= number_format((float)$row['course_fee'], 2) ?></td>
              <td><?= htmlspecialchars($row['class_time']) ?></td>
              <td><?= htmlspecialchars($row['class_date']) ?></td>
              <td><?= htmlspecialchars($row['duration']) ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <?php if (!$locked): ?>
          <p class="muted" style="margin-top:10px;">Select <strong>4–6</strong> courses and submit once to confirm.</p>
          <button type="submit" class="btn-submit">Register Selected Courses</button>
        <?php endif; ?>
      </form>
    </div>

    <div class="form-container" style="margin-top:20px;">
      <h2>My Registered Courses</h2>
      <?php if (empty($myCourses)): ?>
        <p class="muted">You have not registered any courses yet.</p>
      <?php else: ?>
        <table>
          <tr>
            <th>#</th><th>Department</th><th>Course Title</th>
            <th>Class Time</th><th>Class Date</th><th>Duration</th><th>Fee</th>
          </tr>
          <?php foreach ($myCourses as $c): ?>
            <tr>
              <td><?= (int)$c['id'] ?></td>
              <td><?= htmlspecialchars($c['department']) ?></td>
              <td><?= htmlspecialchars($c['course_title']) ?></td>
              <td><?= htmlspecialchars($c['class_time']) ?></td>
              <td><?= htmlspecialchars($c['class_date']) ?></td>
              <td><?= htmlspecialchars($c['duration']) ?></td>
              <td><?= number_format((float)$c['course_fee'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr>
            <th colspan="6" style="text-align:right;">Total Fees:</th>
            <th><?= number_format($totalFee, 2) ?></th>
          </tr>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
