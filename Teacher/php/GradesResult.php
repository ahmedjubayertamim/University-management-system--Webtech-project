<?php

session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') { http_response_code(403); exit('Forbidden'); }

$offered_id = isset($_GET['offered_id']) ? (int)$_GET['offered_id'] : 0;
$semester   = isset($_GET['semester'])   ? (int)$_GET['semester']   : 0;
$year       = isset($_GET['year'])       ? (int)$_GET['year']       : 0;

if (!$offered_id || !$semester || !$year) exit('Invalid request.');

$own = $conn->prepare("SELECT course_title, department FROM offered_course WHERE id=? AND teacher_id=? LIMIT 1");
$own->bind_param("ii", $offered_id, $user_id);
$own->execute();
$own->bind_result($offered_title, $offered_dept);
if (!$own->fetch()) { $own->close(); exit('Unauthorized course.'); }
$own->close();


function ensureCourseId(mysqli $conn, string $title, ?string $dept): int {
    $cid = 0;
    $sel = $conn->prepare("SELECT course_id FROM courses WHERE course_name=? AND (department <=> ?) LIMIT 1");
    $sel->bind_param("ss", $title, $dept);
    $sel->execute(); $sel->bind_result($cid);
    if ($sel->fetch()) { $sel->close(); return (int)$cid; }
    $sel->close();

    $sel2 = $conn->prepare("SELECT course_id FROM courses WHERE course_name=? LIMIT 1");
    $sel2->bind_param("s", $title);
    $sel2->execute(); $sel2->bind_result($cid);
    if ($sel2->fetch()) { $sel2->close(); return (int)$cid; }
    $sel2->close();

    $ins = $conn->prepare("INSERT INTO courses (course_code, course_name, credit, department) VALUES (NULL, ?, NULL, ?)");
    $ins->bind_param("ss", $title, $dept);
    $ins->execute();
    $newId = (int)$conn->insert_id;
    $ins->close();
    return $newId;
}
$course_id = ensureCourseId($conn, (string)$offered_title, $offered_dept ?: null);


$sql = "
  SELECT s.student_id,
         u.first_name, u.last_name, u.email,
         g.grade
  FROM student_course_registrations scr
  JOIN students s ON s.student_id = scr.student_id
  JOIN users   u ON u.id = s.user_id
  LEFT JOIN grades g
         ON g.student_id = s.student_id
        AND g.course_id  = ?
        AND g.semester   = ?
        AND g.year       = ?
  WHERE scr.offered_course_id = ?
  ORDER BY u.first_name, u.last_name
";
$st = $conn->prepare($sql);
$st->bind_param("iiii", $course_id, $semester, $year, $offered_id);
$st->execute();
$res = $st->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
$st->close();
?>
<!DOCTYPE html>
<html>
<head>

  <title>Grades Result</title>
  <link rel="stylesheet" href="../css/Attendance.css">
</head>
<body>

<div class="content">
  <div class="wrap">
    <h2><?= h($offered_title) ?> <span class="muted">(Semester <?= (int)$semester ?>, Year <?= (int)$year ?>)</span></h2>
    <?php if (empty($rows)): ?>
      <div class="note">No registered students found.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Email</th>
            <th>Letter Grade</th>
          </tr>
        </thead>
        <tbody>
          <?php $i=1; foreach ($rows as $r): ?>
            <tr>
              <td style="text-align:center;"><?= $i++ ?></td>
              <td><?= h(trim(($r['first_name']??'').' '.($r['last_name']??''))) ?></td>
              <td><?= h($r['email'] ?? '') ?></td>
              <td style="text-align:center;"><?= h($r['grade'] ?? '—') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="margin-top:12px">
        <a class="btn" href="../view/SubmitGrades.php?offered_id=<?= (int)$offered_id ?>&semester=<?= (int)$semester ?>&year=<?= (int)$year ?>">Back</a>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
