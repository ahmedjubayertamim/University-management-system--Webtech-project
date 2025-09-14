<?php
session_start();
require_once __DIR__ . '/../php/config.php'; // gives $conn

$teachers = [];
$tq = $conn->query("
  SELECT id, first_name, last_name, email
  FROM users
  WHERE role='teacher' AND status='enabled'
  ORDER BY first_name, last_name
");
while ($r = $tq->fetch_assoc()) { $teachers[] = $r; }

$courses = $conn->query("
  SELECT oc.id, oc.department, oc.course_title, oc.student_capacity, oc.student_count, oc.course_fee,
         oc.class_time, oc.class_date, oc.duration,
         u.first_name, u.last_name, u.email, oc.teacher_id
  FROM offered_course oc
  LEFT JOIN users u ON u.id = oc.teacher_id
  ORDER BY oc.id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Assign Teacher to Offered Courses</title>
  <link rel="stylesheet" href="../css/assign_teacher.css">

</head>
<body>
<header>
  <h1>Assign Teacher</h1>
  <div class="search-box"><input placeholder="Search..."><button>Search</button></div>
</header>

<div class="sidebar">
  <ul>
    <li><a href="index.php">Dashboard</a></li>
    <li><a href="ManageCourse.php">Manage Courses</a></li>
    <li><a href="OfferCourse.php">Offered Courses</a></li>
    <li><a href="AssignTeacher.php">Assign Teacher</a></li>
    <li><a href="ManageLibrary.php">Manage Library</a></li>
    <li><a href="MangeUser.php">Manage User</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>
<div class="content">
  <div class="wrap">
    <h2>Offered Courses</h2>

    <div class="table-wrap">
      <table class="table-compact">
        <tr>
          <th class="col-id">ID</th>
          <th class="col-dept">Department</th>
          <th class="col-title">Course Title</th>
          <th class="col-cap">Capacity</th>
          <th class="col-enr">Enrolled</th>
          <th class="col-fee">Fee</th>
          <th class="col-time">Class Time</th>
          <th class="col-date">Class Date</th>
          <th class="col-dur">Duration</th>
          <th class="col-teacher">Current Teacher</th>
          <th class="col-assign">Assign / Change</th>
        </tr>

        <?php while ($c = $courses->fetch_assoc()):
          $current = $c['teacher_id']
            ? trim(($c['first_name'] ?? '').' '.($c['last_name'] ?? ''))
            : '—';
        ?>
        <tr>
          <td class="col-id"><?= (int)$c['id'] ?></td>
          <td class="col-dept truncate"><?= htmlspecialchars($c['department']) ?></td>
          <td class="col-title truncate" style="text-align:left">
            <?= htmlspecialchars($c['course_title']) ?>
          </td>
          <td class="col-cap"><?= (int)$c['student_capacity'] ?></td>
          <td class="col-enr"><?= (int)$c['student_count'] ?></td>
          <td class="col-fee"><?= number_format((float)$c['course_fee'], 2) ?></td>
          <td class="col-time"><?= htmlspecialchars($c['class_time']) ?></td>
          <td class="col-date"><?= htmlspecialchars($c['class_date']) ?></td>
          <td class="col-dur"><?= htmlspecialchars($c['duration']) ?></td>
          <td class="col-teacher truncate" style="text-align:left">
            <?= htmlspecialchars($current) ?>
          </td>
          <td class="col-assign">
            <form action="../php/assign_teacher.php" method="post" class="assign-inline">
              <input type="hidden" name="course_id" value="<?= (int)$c['id'] ?>">
              <select name="teacher_id" required>
                <option value="">-- Select teacher --</option>
                <?php foreach ($teachers as $t):
                  $tid   = (int)$t['id'];
                  $tname = trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? ''));
                  $label = $tname ?: $t['email']; // fallback if name is empty
                ?>
                  <option value="<?= $tid ?>" <?= $tid === (int)$c['teacher_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <button class="btn" type="submit">Save</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>
