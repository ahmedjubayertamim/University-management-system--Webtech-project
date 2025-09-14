<?php
session_start();
require_once __DIR__ . '/config.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
    {
         http_response_code(405); exit('Method Not Allowed');
         }

$id     = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';
if ($id <= 0 || !in_array($action, ['approve','reject'], true)) exit('Bad request');

$status = ($action === 'approve') ? 'completed' : 'failed';

$st = $conn->prepare("UPDATE payments SET status=? WHERE payment_id=? AND status='pending'");
$st->bind_param("si", $status, $id);
if (!$st->execute()) { $st->close(); exit('DB error: '.$conn->error); }
$st->close();

header("Location: ../view/ApprovePayments.php");
exit;
