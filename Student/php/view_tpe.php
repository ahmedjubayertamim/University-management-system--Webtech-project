<?php
session_start();
require_once __DIR__ . '/config.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id<=0) die("Please log in.");

$sid=0;
$q=$conn->prepare("SELECT s.student_id FROM students s JOIN users u ON u.id=s.user_id
                   WHERE s.user_id=? LIMIT 1");
$q->bind_param("i",$user_id); $q->execute(); $q->bind_result($sid); $q->fetch(); $q->close();

$course_id=(int)($_GET['course']??0);
$window_id=(int)($_GET['window']??0);

$sub=$conn->prepare("SELECT id,overall_comment,submitted_at FROM tpe_submissions
                     WHERE student_id=? AND offered_course_id=? AND window_id=?");
$sub->bind_param("iii",$sid,$course_id,$window_id);
$sub->execute(); $res=$sub->get_result(); $S=$res->fetch_assoc(); $sub->close();
if(!$S) die("No submission found.");

$ans=$conn->prepare("SELECT q.text,a.rating
                     FROM tpe_answers a JOIN tpe_questions q ON q.id=a.question_id
                     WHERE a.submission_id=? ORDER BY q.id ASC");
$ans->bind_param("i",$S['id']); $ans->execute(); $AR=$ans->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My TPE</title>
  <link rel="stylesheet" href="/Project/css/style.css">
  <style>.box{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);padding:20px;margin:20px}</style>
</head>
<body>
<header><h1>My Evaluation</h1></header>
<div class="content">
  <div class="box">
    <p><strong>Submitted:</strong> <?= htmlspecialchars($S['submitted_at']) ?></p>
    <table>
      <tr><th>Question</th><th>Rating</th></tr>
      <?php while($r=$AR->fetch_assoc()): ?>
        <tr><td style="text-align:left;"><?= htmlspecialchars($r['text']) ?></td><td><?= (int)$r['rating'] ?></td></tr>
      <?php endwhile; ?>
    </table>
    <h3>Overall Comment</h3>
    <p><?= nl2br(htmlspecialchars($S['overall_comment'] ?? '')) ?></p>
    <a href="../view/StudentTPE.php">Back</a>
  </div>
</div>
</body>
</html>
