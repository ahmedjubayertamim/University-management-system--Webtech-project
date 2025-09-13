<?php
// Project/Student/php/register_course.php
session_start();
require_once __DIR__ . '/config.php';

function back($msg) {
  header("Location: ../view/CourseRegistration.php?msg=" . urlencode($msg));
  exit;
}

function get_or_create_student_id(mysqli $conn, int $user_id): int {
  $role = $status = null;
  $q = $conn->prepare("SELECT role, status FROM users WHERE id=? LIMIT 1");
  $q->bind_param("i", $user_id);
  $q->execute();
  $q->bind_result($role, $status);
  $q->fetch();
  $q->close();

  if ($role !== 'student' || $status !== 'enabled') return 0;

  $sid = 0;
  $q = $conn->prepare("SELECT student_id FROM students WHERE user_id=? LIMIT 1");
  $q->bind_param("i", $user_id);
  $q->execute();
  $q->bind_result($sid);
  if ($q->fetch()) { $q->close(); return (int)$sid; }
  $q->close();

  // create mapping if missing (adjust to your table if needed)
  $ins = $conn->prepare("INSERT INTO students (user_id) VALUES (?)");
  if (!$ins) return 0;
  $ins->bind_param("i", $user_id);
  if (!$ins->execute()) { $ins->close(); return 0; }
  $newId = (int)$conn->insert_id;
  $ins->close();
  return $newId;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) back("Please log in");

$student_id = get_or_create_student_id($conn, $user_id);
if ($student_id <= 0) back("Not a student or not enabled");

/* Ensure lock table exists */
$conn->query("
  CREATE TABLE IF NOT EXISTS student_registration_locks (
    student_id INT(11) PRIMARY KEY,
    locked TINYINT(1) NOT NULL DEFAULT 0,
    locked_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_lock_student FOREIGN KEY (student_id)
      REFERENCES students(student_id) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* Is this student already locked (confirmed)? */
$locked = 0;
$q = $conn->prepare("SELECT locked FROM student_registration_locks WHERE student_id=?");
$q->bind_param("i", $student_id);
$q->execute();
$q->bind_result($locked);
$q->fetch();
$q->close();
if ((int)$locked === 1) back("Registration already confirmed. You cannot register again.");

/* Validate selection: 4–6 */
if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['courses']) || !is_array($_POST['courses'])) {
  back("No courses selected");
}
$selected = array_map('intval', $_POST['courses']);
$selected = array_values(array_unique($selected));
if (count($selected) < 4 || count($selected) > 6) {
  back("You must select between 4 and 6 courses in one submission.");
}

/* Register with checks */
$conn->begin_transaction();
try {
  $totalInserted = 0;
  $totalFee = 0.0;

  foreach ($selected as $course_id) {
    // lock the course row to keep counts consistent
    $cq = $conn->prepare("SELECT department, student_capacity, student_count, course_fee, class_time, class_date
                          FROM offered_course WHERE id=? FOR UPDATE");
    $cq->bind_param("i", $course_id);
    $cq->execute();
    $cq->bind_result($dep, $cap, $cnt, $fee, $ctime, $cdate);
    if (!$cq->fetch()) {
      $cq->close();
      throw new Exception("Course not found (ID: $course_id)");
    }
    $cq->close();

    if ((int)$cnt >= (int)$cap) {
      throw new Exception("Course full: $course_id");
    }

    // time-slot conflict (same department + date + time)
    $conf = $conn->prepare("
      SELECT COUNT(*)
      FROM student_course_registrations scr
      JOIN offered_course oc ON oc.id = scr.offered_course_id
      WHERE scr.student_id=? AND oc.department=? AND oc.class_date=? AND oc.class_time=?
    ");
    $conf->bind_param("isss", $student_id, $dep, $cdate, $ctime);
    $conf->execute();
    $conf->bind_result($confCount);
    $conf->fetch();
    $conf->close();
    if ($confCount > 0) {
      throw new Exception("Slot conflict for {$dep} on {$cdate} at {$ctime}");
    }

    // insert registration (ignore dup)
    $ins = $conn->prepare("INSERT IGNORE INTO student_course_registrations (student_id, offered_course_id) VALUES (?, ?)");
    $ins->bind_param("ii", $student_id, $course_id);
    if (!$ins->execute()) {
      $ins->close();
      throw new Exception("Insert failed: " . $conn->error);
    }
    $changed = $ins->affected_rows > 0;
    $ins->close();

    if ($changed) {
      $upd = $conn->prepare("UPDATE offered_course SET student_count = student_count + 1 WHERE id=?");
      $upd->bind_param("i", $course_id);
      if (!$upd->execute()) {
        $upd->close();
        throw new Exception("Seat update failed: " . $conn->error);
      }
      $upd->close();

      $totalInserted++;
      $totalFee += (float)$fee;
    } else {
      throw new Exception("You already registered course ID: {$course_id}");
    }
  }

  if ($totalInserted !== count($selected)) {
    throw new Exception("Some courses could not be registered.");
  }

  // lock the student once all succeed
  $lock = $conn->prepare("
    INSERT INTO student_registration_locks (student_id, locked, locked_at)
    VALUES (?, 1, NOW())
    ON DUPLICATE KEY UPDATE locked=1, locked_at=NOW()
  ");
  $lock->bind_param("i", $student_id);
  if (!$lock->execute()) {
    $lock->close();
    throw new Exception("Failed to confirm registration.");
  }
  $lock->close();

  $conn->commit();
  back("Registered successfully. Total fees: " . number_format($totalFee, 2));
} catch (Exception $e) {
  $conn->rollback();
  back("Registration failed: " . $e->getMessage());
}
