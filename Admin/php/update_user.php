<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = intval($_POST['user_id'] ?? 0);
    $role    = $_POST['role'] ?? 'not set';
    $status  = $_POST['status'] ?? 'disabled';

    if ($user_id <= 0) {
        echo " Invalid user!";
        exit;
    }

    // Update users table
    $stmt = $conn->prepare("UPDATE users SET role=?, status=? WHERE id=?");
    $stmt->bind_param("ssi", $role, $status, $user_id);
    $stmt->execute();
    $stmt->close();

    // If role is not set, don’t insert anywhere
    if ($role !== "not set" && $status === "enabled") {
        if ($role === "student") {
            $conn->query("INSERT IGNORE INTO students (user_id, student_number, department, program, semester, admission_year)
                         VALUES ($user_id, CONCAT('STU', $user_id), 'Unknown', 'Unknown', 1, YEAR(CURDATE()))");
        } elseif ($role === "teacher") {
            $conn->query("INSERT IGNORE INTO teachers (user_id, teacher_number, department, designation, hire_date, salary)
                         VALUES ($user_id, CONCAT('TCH', $user_id), 'Unknown', 'Lecturer', CURDATE(), 0.00)");
        } elseif ($role === "admin") {
            $conn->query("INSERT IGNORE INTO admins (user_id, designation)
                         VALUES ($user_id, 'System Admin')");
        }
    }

    echo "User updated successfully!";
}
?>
