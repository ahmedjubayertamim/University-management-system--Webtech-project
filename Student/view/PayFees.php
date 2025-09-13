<?php
session_start();
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/helpers_pay.php';

/* Resolve or create students.student_id for this user (enabled student only) */
function get_or_create_student_id(mysqli $conn, int $user_id): int {
    $role = $status = null;
    $q = $conn->prepare("SELECT role, status FROM users WHERE id=?");
    $q->bind_param("i", $user_id);
    $q->execute();
    $q->bind_result($role, $status);
    $q->fetch(); $q->close();
    if ($role !== 'student' || $status !== 'enabled') return 0;

    $sid = 0;
    $q = $conn->prepare("SELECT student_id FROM students WHERE user_id=?");
    $q->bind_param("i", $user_id);
    $q->execute(); $q->bind_result($sid);
    if ($q->fetch()) { $q->close(); return (int)$sid; }
    $q->close();

    $ins = $conn->prepare("INSERT INTO students (user_id) VALUES (?)");
    $ins->bind_param("i", $user_id);
    if (!$ins->execute()) { $ins->close(); return 0; }
    $sid = (int)$conn->insert_id; $ins->close();
    return $sid;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) die("Please log in.");
$student_id = get_or_create_student_id($conn, $user_id);
if ($student_id <= 0) die("Not allowed.");

$msg = $_GET['msg'] ?? "";

/* Totals */
$course_total = compute_course_total($conn, $student_id);
$library_fine = compute_library_fine($conn, $student_id);
$paid_total   = compute_completed_paid($conn, $student_id);
$total_due    = max(0, $course_total + $library_fine - $paid_total);

/* My payment history */
$hist = $conn->prepare("SELECT * FROM payments WHERE student_id=? ORDER BY payment_id DESC");
$hist->bind_param("i", $student_id);
$hist->execute();
$payments = $hist->get_result();
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
  <div class="search-box"><input placeholder="Search..."><button>Search</button></div>
</header>

<div class="sidebar">
  <ul>
    <li><a href="StudentDashboard.php">Dashboard</a></li>
    <li><a href="CourseRegistration.php">Register Courses</a></li>
    <li><a href="PayFees.php">Pay Fees</a></li>
    <li><a href="StudentLibrary.php">Library</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <?php if ($msg): ?>
    <div class="<?= stripos($msg,'fail')!==false ? 'error':'note' ?>"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <div class="box">
    <div class="grid">
      <div class="stat"><h3>Course Fees</h3><div><?= number_format($course_total,2) ?></div></div>
      <div class="stat"><h3>Library Fine</h3><div><?= number_format($library_fine,2) ?></div></div>
      <div class="stat"><h3>Completed Paid</h3><div><?= number_format($paid_total,2) ?></div></div>
    </div>
    <h2 style="margin-top:16px;">Total Due: <?= number_format($total_due,2) ?></h2>
    <p class="muted">Create a payment slip. Admin will approve or reject it.</p>

    <form action="../php/create_payment.php" method="post" class="box" style="margin-top:12px;">
      <label>Amount to pay (you can pay partially):</label><br>
      <input type="number" step="0.01" min="0" name="amount" value="<?= htmlspecialchars($total_due) ?>" style="padding:8px;width:220px"><br><br>

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
      <?php while($p=$payments->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$p['payment_id'] ?></td>
        <td><?= number_format($p['amount'],2) ?></td>
        <td><?= htmlspecialchars($p['method']) ?></td>
        <td><?= htmlspecialchars($p['status']) ?></td>
        <td><?= htmlspecialchars($p['payment_date']) ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>
