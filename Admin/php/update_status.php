<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    if (empty($user_id) || empty($role) || empty($status)) {
        echo "Invalid request!";
        exit;
    }

    // Update in users table
    $sql = "UPDATE users SET status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $user_id);

    if ($stmt->execute()) {
        // Role-based update
        if ($role === "student") {
            $sql2 = "UPDATE student SET status=? WHERE user_id=?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("si", $status, $user_id);
            $stmt2->execute();
        } elseif ($role === "teacher") {
            $sql2 = "UPDATE teacher SET status=? WHERE user_id=?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("si", $status, $user_id);
            $stmt2->execute();
        }

        echo "Status updated successfully for User ID: U{$user_id}";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
?>
