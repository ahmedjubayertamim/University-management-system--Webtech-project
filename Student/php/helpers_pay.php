<?php

const FINE_PER_DAY = 10;


function compute_course_total(mysqli $conn, int $student_id): float {
    $sql = "SELECT COALESCE(SUM(oc.course_fee),0)
            FROM student_course_registrations scr
            JOIN offered_course oc ON oc.id = scr.offered_course_id
            WHERE scr.student_id=?";
    $st = $conn->prepare($sql);
    $st->bind_param("i", $student_id);
    $st->execute();
    $st->bind_result($total);
    $st->fetch();
    $st->close();
    return (float)$total;
}

function compute_library_fine(mysqli $conn, int $student_id): float {
    $sum = 0.0;
    $q = $conn->prepare("SELECT due_date
                         FROM borrow_records
                         WHERE student_id=? AND return_date IS NULL");
    $q->bind_param("i", $student_id);
    $q->execute();
    $res = $q->get_result();
    $today = strtotime(date('Y-m-d'));
    while ($row = $res->fetch_assoc()) {
        $due = strtotime($row['due_date']);
        if ($today > $due) {
            $days = (int)floor(($today - $due) / (60*60*24));
            $sum += $days * FINE_PER_DAY;
        }
    }
    $q->close();
    return (float)$sum;
}


function compute_completed_paid(mysqli $conn, int $student_id): float {
    $st = $conn->prepare("SELECT COALESCE(SUM(amount),0)
                          FROM payments
                          WHERE student_id=? AND status='completed'");
    $st->bind_param("i", $student_id);
    $st->execute();
    $st->bind_result($paid);
    $st->fetch();
    $st->close();
    return (float)$paid;
}
