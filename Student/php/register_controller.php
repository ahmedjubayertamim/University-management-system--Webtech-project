<?php
session_start();
if (!isset($_SESSION['student_id'])) { header("Location: ../login.php"); exit; }

$conn = new mysqli("localhost","root","","universitydb");

$student_id = (int)$_SESSION['student_id'];

function window_active($conn,$dept,$course_key){
    $now = date('Y-m-d H:i:s');
    $sql = "SELECT 1 FROM add_drop_deadline 
            WHERE department='$dept' AND course='$course_key' 
              AND start_date<='$now' AND end_date>='$now' LIMIT 1";
    $res = $conn->query($sql);
    return $res && $res->num_rows>0;
}

function is_locked($conn,$student_id){
    $r = $conn->query("SELECT locked FROM student_registration_locks WHERE student_id=$student_id")->fetch_assoc();
    return $r ? (bool)$r['locked'] : false;
}

if ($_SERVER['REQUEST_METHOD']==='POST'){
    if (is_locked($conn,$student_id)){ header("Location: ../view/RegisterView.php?msg=Locked"); exit; }

    $action = $_POST['action'];
    $cid = (int)$_POST['offered_course_id'];

    $r = $conn->query("SELECT department,course_title FROM offered_course WHERE id=$cid")->fetch_assoc();
    $dept = $r['department'];
    $key  = strtolower(str_replace(' ','',$r['course_title']));

    if (!window_active($conn,$dept,$key)){ header("Location: ../view/RegisterView.php?msg=Window closed"); exit; }

    if ($action==='add'){
        $conn->query("INSERT IGNORE INTO student_course_registrations(student_id,offered_course_id) VALUES($student_id,$cid)");
        header("Location: ../view/RegisterView.php?msg=Course added"); exit;
    }
    if ($action==='drop'){
        $conn->query("DELETE FROM student_course_registrations WHERE student_id=$student_id AND offered_course_id=$cid");
        header("Location: ../view/RegisterView.php?msg=Course dropped"); exit;
    }
    if ($action==='finalize'){
        $conn->query("INSERT INTO student_registration_locks(student_id,locked,locked_at) VALUES($student_id,1,NOW()) 
                      ON DUPLICATE KEY UPDATE locked=1,locked_at=NOW()");
        header("Location: ../view/RegisterView.php?msg=Finalized"); exit;
    }
}

$offered = [];
$or = $conn->query("SELECT * FROM offered_course ORDER BY id DESC");
while ($row = $or->fetch_assoc()) $offered[]=$row;

$mine = [];
$mr = $conn->query("SELECT scr.offered_course_id, oc.course_title 
                    FROM student_course_registrations scr 
                    JOIN offered_course oc ON oc.id=scr.offered_course_id 
                    WHERE scr.student_id=$student_id");
while ($row = $mr->fetch_assoc()) $mine[]=$row;

$locked = is_locked($conn,$student_id);

$conn->close();
include __DIR__ . '/../view/RegisterView.php';
