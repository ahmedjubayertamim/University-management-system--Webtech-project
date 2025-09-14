<?php
session_start();
if (!isset($_SESSION['student_id'])) { header("Location: student_login.php?msg=Login first"); exit; }

$conn = new mysqli("localhost","root","","universitydb");
$student_id = (int)$_SESSION['student_id'];

function k($t){ return strtolower(str_replace(' ','',$t)); }
function win_active_dc($conn,$dept,$key){
  $now = date('Y-m-d H:i:s');
  $q = "SELECT 1 FROM add_drop_deadline WHERE department='$dept' AND course='$key' AND start_date<='$now' AND end_date>='$now' LIMIT 1";
  $r = $conn->query($q);
  return $r && $r->num_rows>0;
}
function win_active_by_course_id($conn,$cid){
  $x = $conn->query("SELECT department, course_title FROM offered_course WHERE id=$cid")->fetch_assoc();
  if (!$x) return false;
  return win_active_dc($conn,$x['department'],k($x['course_title']));
}
function is_locked($conn,$sid){
  $x = $conn->query("SELECT locked FROM student_registration_locks WHERE student_id=$sid")->fetch_assoc();
  return $x ? (bool)$x['locked'] : false;
}

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
  $action = $_POST['action'];
  if ($action==='finalize') {
    $conn->query("INSERT INTO student_registration_locks(student_id,locked,locked_at) VALUES($student_id,1,NOW()) ON DUPLICATE KEY UPDATE locked=1,locked_at=NOW()");
    header("Location: student_register_controller.php?msg=Finalized"); exit;
  }
  $cid = (int)($_POST['offered_course_id'] ?? 0);
  if (!win_active_by_course_id($conn,$cid)) { header("Location: student_register_controller.php?msg=Window closed"); exit; }
  if ($action==='add') {
    $conn->query("INSERT INTO course_registrations(student_id,course_id,semester,year,status) VALUES($student_id,$cid,1,YEAR(CURDATE()),'registered')");
    header("Location: student_register_controller.php?msg=Added"); exit;
  }
  if ($action==='drop') {
    $conn->query("UPDATE course_registrations SET status='dropped' WHERE student_id=$student_id AND course_id=$cid AND status='registered'");
    header("Location: student_register_controller.php?msg=Dropped"); exit;
  }
}

$mine = [];
$mr = $conn->query("
  SELECT cr.course_id, oc.course_title, oc.department, oc.class_time, oc.class_date,
         oc.student_count, oc.student_capacity, oc.course_fee, cr.status
  FROM course_registrations cr
  JOIN offered_course oc ON oc.id = cr.course_id
  WHERE cr.student_id=$student_id AND cr.status='registered'
  ORDER BY oc.course_title
");
if ($mr) { while($row=$mr->fetch_assoc()){ $row['window_active']=win_active_dc($conn,$row['department'],k($row['course_title']))?'Active':'Closed'; $mine[]=$row; } }

$available = [];
$qr = $conn->query("
  SELECT oc.id, oc.department, oc.course_title, oc.class_time, oc.class_date,
         oc.student_capacity, oc.student_count, oc.course_fee
  FROM offered_course oc
  JOIN add_drop_deadline d
    ON d.department = oc.department
   AND d.course = LOWER(REPLACE(oc.course_title,' ',''))
  WHERE d.start_date <= NOW() AND d.end_date >= NOW()
    AND oc.id NOT IN (
      SELECT course_id FROM course_registrations
      WHERE student_id=$student_id AND status='registered'
    )
  ORDER BY oc.course_title
");
if ($qr) { while($row=$qr->fetch_assoc()) $available[]=$row; }

$locked = is_locked($conn,$student_id);
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$conn->close();

include __DIR__ . '/../view/StudentRegisterView.php';
