<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "UPDATE users SET first_name=?, last_name=?, role=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $fname, $lname, $role, $status, $id);

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!'); window.location.href='../View/MangeUser.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
