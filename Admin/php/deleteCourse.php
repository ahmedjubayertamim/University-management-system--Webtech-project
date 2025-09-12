<?php
include "../PHP/config.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    $sql = "DELETE FROM offered_course WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: ../view/OfferCourse.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request!";
    }
?>
 