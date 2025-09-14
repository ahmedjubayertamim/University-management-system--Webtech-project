<?php
session_start();
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $q = $conn->prepare("DELETE FROM consulting_hours WHERE consult_id=?");
    $q->bind_param("i", $id);
    if ($q->execute()) {
        header("Location: ../view/SetConsulting.php?msg=Deleted");
    } else {
        header("Location: ../view/SetConsulting.php?msg=Delete failed");
    }
    $q->close();
}
