<!DOCTYPE html>
<html>
<head>
  <title>TPE Report</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/TPEReport.css">
</head>
<body>
<header>
  <h1>TPE Report</h1>
</header>

  <div class="container">
    <aside class="sidebar">
      <ul>
         <li><a href="../php/admin_dashboard_controller.php">Dashboard</a><li>
    <li><a href="../view/MangeUser.php">Manage User</a></li>
<li><a href="../view/OfferCourse.php">Set Offer Course</a></li>
<li><a href="../view/ManageCourse.php">Set Add/Drop Deadline</a></li>
<li><a href="../view/AssignTeacher.php">Assign Teacher</a></li>
<li><a href="../view/ApprovePayments.php">Approve Payment</a></li>
<li><a href="../view/ManageLibrary.php">Manage Library</a></li>
<li><a href="../view/ViewAllBooks.php">View All Books</a></li>
<li><a href="../view/ManageTPE.php">ManageTPE</a></li>
<li><a href="../php/TPEReport.php">TPE Report</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
      </ul>
    </aside></div>

  <div class="content">
    <div class="wrap">
      <div class="header">
        <form method="get">
          <label for="window">Select window:</label>
          <select id="window" name="window" onchange="this.form.submit()">
            <?php if (!empty($windows)): ?>
              <?php foreach ($windows as $win): ?>
                <option value="<?= (int)$win['id'] ?>" <?= ((int)$win['id'] === (int)$selected) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($win['name']) ?> (<?= htmlspecialchars($win['status']) ?>)
                </option>
              <?php endforeach; ?>
            <?php else: ?>
              <option value="0" selected>No windows available</option>
            <?php endif; ?>
          </select>
          <noscript><button type="submit">Go</button></noscript>
        </form>

        <div class="muted">
          <?php if ($w): ?>
            <?= htmlspecialchars($w['name']) ?> — status: <strong><?= htmlspecialchars($w['status']) ?></strong>
            <?php if (!empty($w['start_date']) || !empty($w['end_date'])): ?>
              | <?= htmlspecialchars($w['start_date'] ?? '') ?> → <?= htmlspecialchars($w['end_date'] ?? '') ?>
            <?php endif; ?>
          <?php else: ?>
            <em>No window selected.</em>
          <?php endif; ?>
        </div>
      </div>

      <div class="grid" style="margin-top:12px">
        <div class="stat">
          <h3>Total submissions</h3>
          <div><?= isset($totalSubmissions) ? (int)$totalSubmissions : 0 ?></div>
        </div>
        <div class="stat">
          <h3>Overall average</h3>
          <div><?= ($overallAvg !== null) ? number_format((float)$overallAvg, 2) : '—' ?></div>
        </div>
        <div class="stat">
          <h3>Questions</h3>
          <div><?= !empty($perQuestion) ? count($perQuestion) : 0 ?></div>
        </div>
      </div>

      <h2 style="margin-top:16px;">Per-question averages</h2>
      <?php if (!empty($perQuestion)): ?>
        <table>
          <tr><th>#</th><th>Question</th><th>Average</th><th>Responses</th></tr>
          <?php foreach ($perQuestion as $row): ?>
            <tr>
              <td><?= (int)$row['id'] ?></td>
              <td><?= htmlspecialchars($row['text']) ?></td>
              <td><?= ($row['avg_rating'] !== null) ? number_format((float)$row['avg_rating'], 2) : '—' ?></td>
              <td><?= (int)$row['responses'] ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php else: ?>
        <div class="note">No questions or no responses for this window.</div>
      <?php endif; ?>

      <h2 style="margin-top:24px;">Top courses (by average)</h2>
      <?php if (!empty($topCourses)): ?>
        <table>
          <tr><th>Course ID</th><th>Course Title</th><th>Avg Rating</th><th>Submissions</th></tr>
          <?php foreach ($topCourses as $c): ?>
            <tr>
              <td><?= (int)$c['course_id'] ?></td>
              <td><?= htmlspecialchars($c['course_title']) ?></td>
              <td><?= number_format((float)$c['avg_rating'], 2) ?></td>
              <td><?= (int)$c['submissions'] ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php else: ?>
        <div class="note">No submissions for this window yet.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
