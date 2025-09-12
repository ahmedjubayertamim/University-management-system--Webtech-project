<?php
 $conn = new mysqli("localhost", "root", "", "universitymanagementsystem");
    if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $conn->query("DELETE FROM add_drop_deadline WHERE id = $delete_id");
        header("Location: ../view/ManageCourse.php?message=Record Deleted Successfully");
        exit;
    }
?>
