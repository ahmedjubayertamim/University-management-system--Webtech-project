<?php
// Project/Admin/View/TPEReport.php
session_start();
require_once __DIR__ . '/../php/config.php';

// 1) Fetch all windows for selector
$windows = $conn->query("SELECT id, name, status, start_date, end_date FROM tpe_windows ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// If no windows at all
if (!$windows) {
  echo "No TPE windows exist. Create one first in Admin.";
  exit;
}

// 2) Resolve selected window: ?window=ID or auto-pick latest (prefer open)
$selected = isset($_GET['window']) ? (int)$_GET['window'] : 0;

if ($selected <= 0) {
  // prefer an open window
  $open = $conn->query("SELECT id FROM tpe_windows WHERE status='open' ORDER BY id DESC LIMIT 1");
  if ($row = $open->fetch_assoc()) {
    $selected = (int)$row['id'];
  } else {
    // else most recent
    $selected = (int)$windows[0]['id'];
  }
}

// Ensure the selected window actually exists
$wq = $conn->prepare("SELECT id, name, status, start_date, end_date FROM tpe_windows WHERE id=?");
$wq->bind_param("i", $selected);
$wq->execute();
$w = $wq->get_result()->fetch_assoc();
$wq->close();

if (!$w) {
  echo "Invalid window id.";
  exit;
}

// 3) Summary counts for this window
// total submissions
$totStmt = $conn->prepare("
  SELECT COUNT(*) AS total_submissions
  FROM tpe_submissions
  WHERE window_id = ?
");
$totStmt->bind_param("i", $selected);
$totStmt->execute();
$totStmt->bind_result($totalSubmissions);
$totStmt->fetch();
$totStmt->close();

// per-question averages
$qa = $conn->prepare("
  SELECT q.id, q.text, AVG(a.rating) AS avg_rating, COUNT(a.id) AS responses
  FROM tpe_questions q
  LEFT JOIN tpe_answers a ON a.question_id = q.id
  LEFT JOIN tpe_submissions s ON s.id = a.submission_id AND s.window_id = ?
  WHERE q.active = 1
  GROUP BY q.id, q.text
  ORDER BY q.id ASC
");
$qa->bind_param("i", $selected);
$qa->execute();
$perQuestion = $qa->get_result()->fetch_all(MYSQLI_ASSOC);
$qa->close();

// overall average (average of all answers for this window)
$oa = $conn->prepare("
  SELECT AVG(a.rating) AS overall_avg
  FROM tpe_answers a
  JOIN tpe_submissions s ON s.id = a.submission_id
  WHERE s.window_id = ?
");
$oa->bind_param("i", $selected);
$oa->execute();
$oa->bind_result($overallAvg);
$oa->fetch();
$oa->close();

// optional: top courses by average
$top = $conn->prepare("
  SELECT oc.id AS course_id, oc.course_title,
         AVG(a.rating) AS avg_rating, COUNT(DISTINCT s.id) AS submissions
  FROM tpe_submissions s
  JOIN tpe_answers a ON a.submission_id = s.id
  JOIN offered_course oc ON oc.id = s.offered_course_id
  WHERE s.window_id = ?
  GROUP BY oc.id, oc.course_title
  ORDER BY avg_rating DESC
  LIMIT 10
");
$top->bind_param("i", $selected);
$top->execute();
$topCourses = $top->get_result()->fetch_all(MYSQLI_ASSOC);
$top->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>TPE Report</title>
  <link rel="stylesheet" href="/Project/css/style.css">
  <style>
    .wrap{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);padding:20px;margin:20px}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #e6e6e6;padding:10px;text-align:left}
    th{background:#3b5998;color:#fff}
    .header{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
    .note{background:#eef7ff;border:1px solid #cfe4ff;padding:8px 10px;border-radius:8px;margin:12px 0}
    .muted{color:#666}
    .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .stat{background:#f8fafc;border:1px solid #e6eef5;border-radius:8px;padding:14px}
    .stat h3{margin:0 0 8px}
  </style>
</head>
<body>
<header>
  <h1>TPE Report</h1>
</header>

<div class="content">
  <div class="wrap">
    <div class="header">
      <form method="get">
        <label for="window">Select window:</label>
        <select id="window" name="window" onchange="this.form.submit()">
          <?php foreach ($windows as $win): ?>
            <option value="<?= (int)$win['id'] ?>" <?= $win['id']==$selected?'selected':'' ?>>
              <?= htmlspecialchars($win['name']) ?> (<?= htmlspecialchars($win['status']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <noscript><button type="submit">Go</button></noscript>
      </form>
      <div class="muted">
        <?= htmlspecialchars($w['name']) ?> — status: <strong><?= htmlspecialchars($w['status']) ?></strong>
        <?php if (!empty($w['start_date']) || !empty($w['end_date'])): ?>
          | <?= htmlspecialchars($w['start_date'] ?? '') ?> → <?= htmlspecialchars($w['end_date'] ?? '') ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="grid" style="margin-top:12px">
      <div class="stat">
        <h3>Total submissions</h3>
        <div><?= (int)$totalSubmissions ?></div>
      </div>
      <div class="stat">
        <h3>Overall average</h3>
        <div><?= $overallAvg !== null ? number_format((float)$overallAvg,2) : '—' ?></div>
      </div>
      <div class="stat">
        <h3>Questions</h3>
        <div><?= count($perQuestion) ?></div>
      </div>
    </div>

    <h2 style="margin-top:16px;">Per-question averages</h2>
    <table>
      <tr><th>#</th><th>Question</th><th>Average</th><th>Responses</th></tr>
      <?php foreach ($perQuestion as $row): ?>
        <tr>
          <td><?= (int)$row['id'] ?></td>
          <td><?= htmlspecialchars($row['text']) ?></td>
          <td><?= $row['avg_rating']!==null ? number_format((float)$row['avg_rating'],2) : '—' ?></td>
          <td><?= (int)$row['responses'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <h2 style="margin-top:24px;">Top courses (by average)</h2>
    <?php if ($topCourses): ?>
      <table>
        <tr><th>Course ID</th><th>Course Title</th><th>Avg Rating</th><th>Submissions</th></tr>
        <?php foreach ($topCourses as $c): ?>
          <tr>
            <td><?= (int)$c['course_id'] ?></td>
            <td><?= htmlspecialchars($c['course_title']) ?></td>
            <td><?= number_format((float)$c['avg_rating'],2) ?></td>
            <td><?= (int)$c['submissions'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="note">No submissions for this window yet.</div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
