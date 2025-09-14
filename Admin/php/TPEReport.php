<?php

session_start();
require_once __DIR__ . '/config.php'; 

function safe_assoc_all(mysqli_result $res = null): array {
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}


$windows = safe_assoc_all(
    $conn->query("SELECT id, name, status, start_date, end_date FROM tpe_windows ORDER BY id DESC")
);


$selected = isset($_GET['window']) ? (int)$_GET['window'] : 0;

if ($selected <= 0) {
    $open = $conn->query("SELECT id FROM tpe_windows WHERE status='open' ORDER BY id DESC LIMIT 1");
    $openRow = $open ? $open->fetch_assoc() : null;
    if ($openRow) {
        $selected = (int)$openRow['id'];
    } elseif (!empty($windows)) {
        $selected = (int)$windows[0]['id'];
    } else {
        $selected = 0; 
    }
}

$w = null;
if ($selected > 0) {
    $wq = $conn->prepare("SELECT id, name, status, start_date, end_date FROM tpe_windows WHERE id=?");
    $wq->bind_param("i", $selected);
    $wq->execute();
    $w = $wq->get_result()->fetch_assoc();
    $wq->close();
}

$totalSubmissions = 0;
$perQuestion = [];
$overallAvg = null;
$topCourses = [];

if ($w && !empty($w['id'])) {
    $totStmt = $conn->prepare("SELECT COUNT(*) FROM tpe_submissions WHERE window_id=?");
    $totStmt->bind_param("i", $w['id']);
    $totStmt->execute();
    $totStmt->bind_result($totalSubmissions);
    $totStmt->fetch();
    $totStmt->close();

    $qa = $conn->prepare("
        SELECT 
            q.id,
            q.text,
            AVG(CASE WHEN s.window_id = ? THEN a.rating END) AS avg_rating,
            COUNT(CASE WHEN s.window_id = ? THEN a.id END)    AS responses
        FROM tpe_questions q
        LEFT JOIN tpe_answers a ON a.question_id = q.id
        LEFT JOIN tpe_submissions s ON s.id = a.submission_id
        WHERE q.active = 1
        GROUP BY q.id, q.text
        ORDER BY q.id ASC
    ");
    $qa->bind_param("ii", $w['id'], $w['id']);
    $qa->execute();
    $perQuestion = safe_assoc_all($qa->get_result());
    $qa->close();

    $oa = $conn->prepare("
        SELECT AVG(a.rating)
        FROM tpe_answers a
        JOIN tpe_submissions s ON s.id = a.submission_id
        WHERE s.window_id = ?
    ");
    $oa->bind_param("i", $w['id']);
    $oa->execute();
    $oa->bind_result($overallAvg);
    $oa->fetch();
    $oa->close();

    $top = $conn->prepare("
        SELECT 
            oc.id AS course_id,
            oc.course_title,
            AVG(a.rating) AS avg_rating,
            COUNT(DISTINCT s.id) AS submissions
        FROM tpe_submissions s
        JOIN tpe_answers a     ON a.submission_id = s.id
        JOIN offered_course oc ON oc.id = s.offered_course_id
        WHERE s.window_id = ?
        GROUP BY oc.id, oc.course_title
        HAVING COUNT(a.id) > 0
        ORDER BY avg_rating DESC
        LIMIT 10
    ");
    $top->bind_param("i", $w['id']);
    $top->execute();
    $topCourses = safe_assoc_all($top->get_result());
    $top->close();
}

include __DIR__ . '/../view/TPEReport.php';
