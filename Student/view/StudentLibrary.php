<?php


if (!isset($__books, $__myOpen, $__totalFine, $__confirmBorrowText)) {
  header("Location: /Project/Student/php/StudentLibrary.php");
  exit;
}
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Student Library</title>
  <link rel="stylesheet" href="../css/CourseRegistration.css">
  <link rel="stylesheet" href="../css/library.css">
 
</head>
<body>
<header>
  <h1>Student Library</h1>
  
</header>

<div class="sidebar">
    <ul>
      <li><a href="../view/StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
       <li><a href="/Project/Student/php/CourseMaterials.php" class="active">Materials</a></li>
      <li><a href="../view/StudentAddDrop.php">Add/Drop</a></li>
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
    <div class="note"><?= h($__msg) ?></div>
  <?php endif; ?>

  <!-- My Borrows & Fines -->
  <div class="wrap">
    <h2>My Borrowed Books</h2>
    <?php if (empty($__myOpen)): ?>
      <p class="muted">You have no active borrows.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Book</th>
          <th>Borrowed On</th>
          <th>Due Date</th>
          <th>Late</th>
          <th>Fine</th>
          <th>Action</th>
        </tr>
        <?php foreach ($__myOpen as $row): ?>
          <?php
           
            $today = strtotime(date('Y-m-d'));
            $due   = strtotime($row['due_date']);
            $lateDays = ($today > $due) ? floor(($today-$due)/(60*60*24)) : 0;
            $fine = $lateDays * $__FINE_PER_DAY;
          ?>
          <tr>
            <td style="text-align:left;"><?= h($row['bookname']) ?></td>
            <td><?= h($row['borrow_date']) ?></td>
            <td><?= h($row['due_date']) ?></td>
            <td><?= $lateDays > 0 ? "<span class='fine'>{$lateDays} day(s)</span>" : "<span class='muted'>On time</span>" ?></td>
            <td><?= $lateDays > 0 ? "<span class='fine'>{$fine}</span>" : "<span class='muted'>0</span>" ?></td>
            <td>
              <a class="btn btn-return" href="/Project/Student/php/StudentLibrary.php?return=<?= (int)$row['bookid'] ?>"
                 onclick="return confirm('Return this book?');">Return</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <th colspan="4" style="text-align:right;">Total Fine (open borrows):</th>
          <th colspan="2"><?= $__totalFine > 0 ? "<span class='fine'>{$__totalFine}</span>" : "<span class='muted'>0</span>" ?></th>
        </tr>
      </table>
    <?php endif; ?>
  </div>

  <!-- All Books -->
  <div class="wrap">
    <h2>All Books</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Book</th>
        <th>Author</th>
        <th>Year</th>
        <th>Type</th>
        <th>Copies</th>
        <th>Status</th>
        <th>Description</th>
        <th>Action</th>
      </tr>

      <?php foreach ($__books as $b): ?>
        <?php
          $id     = (int)$b['id'];
          $type   = strtolower($b['type']);
          $amount = (int)($b['amount'] ?? 0);
          $status = $b['status'];
        ?>
        <tr>
          <td><?= $id ?></td>
          <td style="text-align:left;"><?= h($b['bookname']) ?></td>
          <td><?= h($b['author']) ?></td>
          <td><?= h($b['pubYear']) ?></td>
          <td><?= h($b['type']) ?></td>
          <td><?= $type === 'ebook' ? '-' : $amount ?></td>
          <td><?= h($status) ?></td>
          <td class="desc"><?= h($b['descText']) ?></td>
          <td>
            <?php if ($type === 'ebook'): ?>
              <?php if (!empty($b['ebook'])): ?>
                <a class="btn btn-ebook" href="<?= h($b['ebook']) ?>" target="_blank">Read / Download</a>
              <?php else: ?>
                <span class="muted">No file</span>
              <?php endif; ?>
            <?php else: ?>
              <?php if ($amount >= $__COPIES_PER_BORROW): ?>
                <a class="btn btn-borrow"
                   href="/Project/Student/php/StudentLibrary.php?borrow=<?= $id ?>"
                   onclick="return confirm('<?= h($__confirmBorrowText) ?>');">Borrow</a>
              <?php else: ?>
                <span class="muted">Not enough copies</span>
              <?php endif; ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body>
</html>
