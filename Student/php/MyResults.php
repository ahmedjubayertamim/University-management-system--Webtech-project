<?php
// Project/Student/php/MyResults.php
session_start();
require_once __DIR__ . '/config.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ---- Guard: logged-in student only ----
$user_id = (int)($_SESSION['user_id'] ?? 0);
$role    = $_SESSION['role'] ?? '';
if ($user_id <= 0 || $role !== 'student') {
    http_response_code(403);
    exit("Login as student");
}

// ---- Resolve student_id from users.id ----
$student_id = 0;
$st = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
$st->bind_param("i", $user_id);
$st->execute();
$st->bind_result($student_id);
$st->fetch();
$st->close();
if (!$student_id) exit("Student profile not found.");

/** Helpers to probe optional columns on 'grades' **/
function has_col(mysqli $conn, string $table, string $col): bool {
    $sql = "SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
            LIMIT 1";
    $p = $conn->prepare($sql);
    $p->bind_param("ss", $table, $col);
    $p->execute();
    $p->store_result();
    $ok = $p->num_rows === 1;
    $p->close();
    return $ok;
}
function pick_col(mysqli $conn, string $a, string $b): ?string {
    if (has_col($conn, 'grades', $a)) return $a;
    if (has_col($conn, 'grades', $b)) return $b;
    return null;
}

/* Detect optional numeric columns */
$G_HAS = [
  'exam'        => has_col($conn, 'grades', 'exam')        || has_col($conn, 'grades', 'exam_score'),
  'quiz'        => has_col($conn, 'grades', 'quiz')        || has_col($conn, 'grades', 'quiz_score'),
  'attendance'  => has_col($conn, 'grades', 'attendance')  || has_col($conn, 'grades', 'attendance_score'),
  'performance' => has_col($conn, 'grades', 'performance') || has_col($conn, 'grades', 'performance_score'),
  'total'       => has_col($conn, 'grades', 'total')       || has_col($conn, 'grades', 'total_score'),
];
$COLS = [
  'exam'        => pick_col($conn, 'exam',        'exam_score'),
  'quiz'        => pick_col($conn, 'quiz',        'quiz_score'),
  'attendance'  => pick_col($conn, 'attendance',  'attendance_score'),
  'performance' => pick_col($conn, 'performance', 'performance_score'),
  'total'       => pick_col($conn, 'total',       'total_score'),
];

/* Build SELECT with optional columns aliased */
$extras = [];
foreach (['exam','quiz','attendance','performance','total'] as $k) {
    if ($COLS[$k]) $extras[] = "g.`{$COLS[$k]}` AS `$k`";
}
$extras_sql = empty($extras) ? "" : ", " . implode(", ", $extras);

/* NOTE: We assume grades.course_id references offered_course.id */
$sql = "
  SELECT
    o.id            AS offered_id,
    o.course_title  AS course_title,
    o.department    AS department,
    g.grade         AS letter,
    g.semester      AS semester,
    g.year          AS year
    $extras_sql
  FROM student_course_registrations scr
  JOIN offered_course o
    ON o.id = scr.offered_course_id
  LEFT JOIN grades g
    ON g.student_id = scr.student_id
   AND g.course_id  = o.id
  WHERE scr.student_id = ?
  ORDER BY o.department, o.course_title
";
$q = $conn->prepare($sql);
$q->bind_param("i", $student_id);
$q->execute();
$res = $q->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
$q->close();

/* Expose data to the view */
$__MYRESULTS_ROWS = $rows;
$__MYRESULTS_HAS  = $G_HAS;

/* Render view (no DB in the view) */
require_once __DIR__ . '/../view/MyResults.php';
