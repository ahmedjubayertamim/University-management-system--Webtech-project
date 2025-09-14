<?php
$rows = $rows ?? [];
function h($s){ 
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Applications</title>
  <link rel="stylesheet" href="../css/CourseRegistration.css">
  <link rel="stylesheet" href="../css/MyApplicationStyle.css">
</head>
<body>
<header><h1>My Applications</h1></header>

<div class="sidebar">
  <ul>
    <li><a href="../php/StudentDashboard.php">Dashboard</a></li>
    <li><a href="../php/CourseRegistration.php">Register Courses</a></li>
    <li><a href="../php/CourseMaterials.php">Materials</a></li>
    <li><a href="../php/StudentAddDrop.php">Add/Drop</a></li>
    <li><a href="../php/StudentLibrary.php">Library</a></li>
    <li><a href="../php/StudentApplication.php">Student Application</a></li>
    <li><a href="../php/MyApplications.php" class="active">My Applications</a></li>
    <li><a href="../php/MyResults.php">My Results</a></li>
    <li><a href="../php/PayFees.php">Pay Fees</a></li>
    <li><a href="../php/StudentTPE.php">Submit TPE</a></li>
    <li><a href="../php/ConsultingHours.php">Consulting Hours</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="wrap">
    <h2>Your Submitted Applications</h2>

    <?php if (empty($rows)): ?>
      <p class="pending">You haven’t submitted any applications yet.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Date</th>
          <th>Teacher</th>
          <th>Reason</th>
          <th>Status</th>
        </tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['start_date'] ?? '') ?></td>
            <td><?= h(trim(($r['first_name'] ?? '').' '.($r['last_name'] ?? ''))) ?></td>
            <td><pre><?= h($r['reason'] ?? '') ?></pre></td>
            <td class="<?= h($r['status'] ?? '') ?>"><?= h(ucfirst($r['status'] ?? '')) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
