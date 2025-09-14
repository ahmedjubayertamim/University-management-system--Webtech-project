<?php
session_start();
require_once __DIR__ . '/config.php';

$action = $_POST['action'] ?? '';
if ($action==='create') {
  $name = trim($_POST['name'] ?? '');
  $status = $_POST['status'] ?? 'closed';
  $sd = $_POST['start_date'] ?? null;
  $ed = $_POST['end_date'] ?? null;

  if ($name==='') { header("Location: ../view/ManageTPE.php"); exit; }
  $st=$conn->prepare("INSERT INTO tpe_windows(name,status,start_date,end_date) VALUES (?,?,?,?)");
  $st->bind_param("ssss",$name,$status,$sd,$ed);
  $st->execute(); $st->close();
  header("Location: ../view/ManageTPE.php"); exit;
}
if ($action==='toggle') {
  $id=(int)($_POST['id']??0);
  $status=$_POST['status'] ?? 'closed';
  if ($id>0) {
    $st=$conn->prepare("UPDATE tpe_windows SET status=? WHERE id=?");
    $st->bind_param("si",$status,$id);
    $st->execute(); $st->close();
  }
  header("Location: ../view/ManageTPE.php"); exit;
}
http_response_code(400);
echo "Bad request";
