<?php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'teacher') {
    http_response_code(403);
    exit("Please log in as a teacher.");
}

$courses = [];
$q = $conn->prepare("SELECT id, course_title, department
                     FROM offered_course
                     WHERE teacher_id=?
                     ORDER BY id DESC");
$q->bind_param("i", $user_id);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) $courses[] = $row;
$q->close();

$offered_id = isset($_GET['offered_id']) ? (int)$_GET['offered_id'] : 0;
$semester   = isset($_GET['semester'])   ? (int)$_GET['semester']   : 1;
$year       = isset($_GET['year'])       ? (int)$_GET['year']       : (int)date('Y');

$course_ok = false; $course_title = '';
if ($offered_id > 0) {
    $chk = $conn->prepare("SELECT course_title FROM offered_course
                            WHERE id=? AND teacher_id=? LIMIT 1");
    $chk->bind_param("ii", $offered_id, $user_id);
    $chk->execute();
    $chk->bind_result($course_title);
    if ($chk->fetch()) $course_ok = true;
    $chk->close();
}

$students = [];
if ($course_ok) {
    $sql = "
      SELECT s.student_id, u.first_name, u.last_name, u.email
      FROM student_course_registrations scr
      JOIN students s ON s.student_id = scr.student_id
      JOIN users u    ON u.id = s.user_id
      WHERE scr.offered_course_id = ?
      ORDER BY u.first_name, u.last_name
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("i", $offered_id);
    $st->execute();
    $res = $st->get_result();
    while ($row = $res->fetch_assoc()) $students[] = $row;
    $st->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Submit Grades</title>
  <link rel="stylesheet" href="../css/Attendance.css">
  <script>
    function computeRow(tr){
      const num = n => Math.max(0, Math.min(100, parseFloat(n||'0')||0));
      const exam = num(tr.querySelector('[name="exam[]"]').value);
      const quiz = num(tr.querySelector('[name="quiz[]"]').value);
      const att  = num(tr.querySelector('[name="attendance[]"]').value);
      const perf = num(tr.querySelector('[name="performance[]"]').value);
      const total = exam*0.60 + quiz*0.15 + att*0.10 + perf*0.15;
      tr.querySelector('.total-cell').textContent = total.toFixed(2);
      let L='F';
      if (total>=80) L='A+'; else if (total>=75) L='A'; else if (total>=70) L='A-';
      else if (total>=65) L='B+'; else if (total>=60) L='B'; else if (total>=55) L='B-';
      else if (total>=50) L='C'; else if (total>=45) L='D';
      tr.querySelector('.letter-cell').textContent = L;
    }
    document.addEventListener('DOMContentLoaded', ()=>{
      document.querySelectorAll('tr.data-row').forEach(tr=>{
        tr.querySelectorAll('input[type="number"]').forEach(inp=>{
          inp.addEventListener('input', ()=>computeRow(tr));
        });
        computeRow(tr);
      });
    });
  </script>
</head>
<body>


<div class="content">
  <div class="wrap">
    <h2>Filter</h2>
    <form method="get" class="controls" action="">
      <label>Course:</label>
      <select name="offered_id" required>
        <option value="">-- choose --</option>
        <?php foreach ($courses as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= $offered_id===(int)$c['id']?'selected':'' ?>>
            <?= h($c['department'].' — '.$c['course_title']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Semester:</label>
      <input type="number" name="semester" min="1" max="3" value="<?= (int)$semester ?>" required>

      <label>Year:</label>
      <input type="number" name="year" min="2000" max="2099" value="<?= (int)$year ?>" required>

      <button class="btn" type="submit">Load</button>
    </form>
  </div>

  <div class="wrap">
    <h2>
      <?= $course_ok ? h($course_title) : 'Select a course' ?>
      <?php if ($course_ok): ?>
        <span class="muted" style="font-size:14px;">(Semester <?= (int)$semester ?>, Year <?= (int)$year ?>)</span>
      <?php endif; ?>
    </h2>

    <?php if ($offered_id && !$course_ok): ?>
      <div class="error">You are not assigned to this course.</div>
    <?php elseif ($course_ok && empty($students)): ?>
      <div class="note">No registered students for this course.</div>
    <?php elseif ($course_ok): ?>
      <form method="post" action="save_grades.php">
        <input type="hidden" name="offered_id" value="<?= (int)$offered_id ?>">
        <input type="hidden" name="semester"   value="<?= (int)$semester ?>">
        <input type="hidden" name="year"       value="<?= (int)$year ?>">

        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Student</th>
              <th>Email</th>
              <th>Exam (0–100)</th>
              <th>Quiz (0–100)</th>
              <th>Attendance (0–100)</th>
              <th>Performance (0–100)</th>
              <th>Total</th>
              <th>Letter</th>
            </tr>
          </thead>
          <tbody>
          <?php $i=1; foreach ($students as $s): ?>
            <tr class="data-row">
              <td style="text-align:center;"><?= $i++ ?></td>
              <td>
                <?= h(trim(($s['first_name']??'').' '.($s['last_name']??''))) ?>
                <input type="hidden" name="student_id[]" value="<?= (int)$s['student_id'] ?>">
              </td>
              <td><?= h($s['email'] ?? '') ?></td>
              <td><input type="number" name="exam[]" min="0" max="100" step="0.01" value="0"></td>
              <td><input type="number" name="quiz[]" min="0" max="100" step="0.01" value="0"></td>
              <td><input type="number" name="attendance[]" min="0" max="100" step="0.01" value="0"></td>
              <td><input type="number" name="performance[]" min="0" max="100" step="0.01" value="0"></td>
              <td class="total-cell"   style="text-align:center;">0.00</td>
              <td class="letter-cell"  style="text-align:center;">F</td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <div style="margin-top:12px">
          <button type="submit" class="btn">Save Grades</button>
        </div>
      </form>
      <p class="muted" style="margin-top:8px">Weights: Exam 60%, Quiz 15%, Attendance 10%, Performance 15%.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
