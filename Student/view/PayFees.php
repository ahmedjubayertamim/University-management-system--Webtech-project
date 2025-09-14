<?php


if (!isset($__student_id, $__course_total, $__library_fine, $__paid_total, $__total_due, $__payments)) {
  header("Location: /Project/Student/php/PayFees.php");
  exit;
}
if (!function_exists('h')) { function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
?>
<!DOCTYPE html>
<html>
<head>
 
  <title>Pay Tuition Fees</title>
  
  <link rel="stylesheet" href="../css/PayFees.css">
</head>
<body>
<header>
  <h1>Pay Tuition Fees</h1>
 
</header>

<div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
       <li><a href="/Project/Student/php/CourseMaterials.php" class="active">Materials</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="StudentApplication.php">Student Application</a></li>
      <li><a href="../view/MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
       <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="/Project/Student/php/ConsultingHours.php" class="active">Consulting Hours</a></li>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

<div class="content">
  <?php if (!empty($__msg)): ?>
    <div class="<?= stripos($__msg,'fail')!==false ? 'error':'note' ?>"><?= h($__msg) ?></div>
  <?php endif; ?>

  <div class="box">
    <div class="grid">
      <div class="stat"><h3>Course Fees</h3><div><?= number_format($__course_total, 2) ?></div></div>
      <div class="stat"><h3>Library Fine</h3><div><?= number_format($__library_fine, 2) ?></div></div>
      <div class="stat"><h3>Completed Paid</h3><div><?= number_format($__paid_total, 2) ?></div></div>
    </div>
    <h2 style="margin-top:16px;">Total Due: <?= number_format($__total_due, 2) ?></h2>
    <p class="muted">Create a payment slip. Admin will approve or reject it.</p>

    <form action="../php/create_payment.php" method="post" class="box" style="margin-top:12px;">
      <input type="hidden" name="student_id" value="<?= (int)$__student_id ?>">
      <label>Amount to pay (you can pay partially):</label><br>
      <input type="number" step="0.01" min="0" name="amount" value="<?= h($__total_due) ?>" style="padding:8px;width:220px"><br><br>

      <label>Payment Method:</label>
      <select name="method" style="padding:8px">
        <option value="bkash">bKash</option>
        <option value="nagad">Nagad</option>
        <option value="rocket">Rocket</option>
        <option value="bank">Bank</option>
      </select>
      <br><br>

      <button class="btn" type="submit">Generate Payment Slip</button>
    </form>
  </div>

  <div class="box">
    <h2>My Payment Slips</h2>
    <table>
      <tr>
        <th>ID</th><th>Amount</th><th>Method</th><th>Status</th><th>Created</th>
      </tr>
      <?php if (empty($__payments)): ?>
        <tr><td colspan="5" style="text-align:center" class="muted">No payments yet.</td></tr>
      <?php else: ?>
        <?php foreach ($__payments as $p): ?>
          <tr>
            <td><?= (int)$p['payment_id'] ?></td>
            <td><?= number_format((float)$p['amount'], 2) ?></td>
            <td><?= h($p['method']) ?></td>
            <td><?= h($p['status']) ?></td>
            <td><?= h($p['payment_date']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </table>
  </div>
</div>
</body>
</html>
