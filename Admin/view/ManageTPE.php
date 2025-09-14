<?php
session_start();
require_once __DIR__ . '/../php/config.php';

// Windows list
$wins = $conn->query("SELECT * FROM tpe_windows ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage TPE</title>
  <link rel="stylesheet" href="/Project/css/style.css">
  <style>
    .box{background:#fff;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);padding:20px;margin:20px}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #e6e6e6;padding:10px;text-align:center}
    th{background:#3b5998;color:#fff}
    .btn{padding:8px 12px;border-radius:6px;background:#2d60ff;color:#fff;border:0;cursor:pointer}
  </style>
</head>
<body>
<header><h1>TPE Windows</h1></header>
<div class="content">
  <div class="box">
    <form action="../php/tpe_window.php" method="post" style="margin-bottom:16px;">
      <input type="text" name="name" placeholder="Window name (e.g., Fall 2025 TPE)" required>
      <input type="date" name="start_date">
      <input type="date" name="end_date">
      <select name="status">
        <option value="open">open</option>
        <option value="closed">closed</option>
      </select>
      <button class="btn" type="submit" name="action" value="create">Create</button>
    </form>

    <table>
      <tr><th>ID</th><th>Name</th><th>Status</th><th>Start</th><th>End</th><th>Action</th></tr>
      <?php while($w=$wins->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$w['id'] ?></td>
        <td><?= htmlspecialchars($w['name']) ?></td>
        <td><?= htmlspecialchars($w['status']) ?></td>
        <td><?= htmlspecialchars($w['start_date']) ?></td>
        <td><?= htmlspecialchars($w['end_date']) ?></td>
        <td>
          <form action="../php/tpe_window.php" method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= (int)$w['id'] ?>">
            <input type="hidden" name="status" value="<?= $w['status']==='open'?'closed':'open' ?>">
            <button class="btn" name="action" value="toggle">
              <?= $w['status']==='open'?'Close':'Open' ?>
            </button>
          </form>
          <a class="btn" href="TPEReport.php?window=<?= (int)$w['id'] ?>">Report</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>
