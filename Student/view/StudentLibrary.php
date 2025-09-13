<?php
// Project/Student/view/StudentLibrary.php

require_once dirname(__FILE__) . '/../php/auth_student.php';   // must set $_SESSION['user_id']
require_once dirname(__FILE__) . '/../php/config.php';         // $conn = new mysqli(...)

$student_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($student_id <= 0) { die("Not authorized."); }

// ---- Settings (use define for PHP 5.x compatibility) ----
if (!defined('BORROW_DAYS'))       define('BORROW_DAYS', 14);   // borrow period
if (!defined('FINE_PER_DAY'))      define('FINE_PER_DAY', 10);  // fine per late day
if (!defined('COPIES_PER_BORROW')) define('COPIES_PER_BORROW', 3); // borrow/return consumes/restores 3 copies

$msg = "";

/** Sum outstanding fines (live) for all open borrows of this student */
function outstanding_fine($conn, $student_id) {
    $sum = 0;
    $q = $conn->prepare("SELECT due_date FROM borrow_records WHERE student_id=? AND return_date IS NULL");
    $q->bind_param("i", $student_id);
    $q->execute();
    $res = $q->get_result();
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $today = strtotime(date('Y-m-d'));
            $due   = strtotime($row['due_date']);
            if ($today > $due) {
                $days = (int)floor(($today - $due) / (60*60*24));
                $sum += $days * FINE_PER_DAY;
            }
        }
    }
    $q->close();
    return (int)$sum;
}

/** Fine for a single due date; returns array($daysLate, $fineAmount) */
function compute_fine($dueYmd) {
    $today = strtotime(date('Y-m-d'));
    $due   = strtotime($dueYmd);
    if ($today <= $due) return array(0, 0);
    $days = (int)floor(($today - $due) / (60*60*24));
    return array($days, $days * FINE_PER_DAY);
}

/** Check if this student currently holds this book (open borrow) */
function student_has_open_borrow($conn, $bookid, $student_id) {
    $q = $conn->prepare("SELECT id, borrow_date, due_date 
                         FROM borrow_records 
                         WHERE bookid=? AND student_id=? AND return_date IS NULL 
                         ORDER BY id DESC LIMIT 1");
    $q->bind_param("ii", $bookid, $student_id);
    $q->execute();
    $res = $q->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $q->close();
    return $row ? $row : null;
}

/* ===========================
   Actions: Borrow / Return
   =========================== */

// Borrow a hard book (only if copies available AND no outstanding fine)
if (isset($_GET['borrow'])) {
    $bookid = (int)$_GET['borrow'];

    // Block borrowing if any outstanding fine
    $owed = outstanding_fine($conn, $student_id);
    if ($owed > 0) {
        $msg = "You cannot borrow new books while you owe a fine (" . $owed . "). Please return overdue books and clear fines first.";
    } else {
        $conn->begin_transaction();
        try {
            // Lock the book row and check availability/type
            $stmt = $conn->prepare("SELECT id, type, amount FROM addbook WHERE id=? FOR UPDATE");
            $stmt->bind_param("i", $bookid);
            $stmt->execute();
            $res  = $stmt->get_result();
            $book = $res ? $res->fetch_assoc() : null;
            $stmt->close();

            if (!$book) throw new Exception("Book not found.");
            if (strtolower($book['type']) === 'ebook') throw new Exception("This is an e-book. No need to borrow.");
            $amount = (int)(isset($book['amount']) ? $book['amount'] : 0);

            if ($amount < COPIES_PER_BORROW) {
                throw new Exception("Not enough copies available. Need at least " . COPIES_PER_BORROW . " copy/copies.");
            }

            // Don’t allow duplicate open borrow of same book by same student
            if (student_has_open_borrow($conn, $bookid, $student_id)) {
                throw new Exception("You already have this book borrowed.");
            }

            $borrow_date = date('Y-m-d');
            $due_date    = date('Y-m-d', strtotime('+' . BORROW_DAYS . ' days'));

            // 1) insert borrow record
            $ins = $conn->prepare("INSERT INTO borrow_records (bookid, student_id, borrow_date, due_date) VALUES (?, ?, ?, ?)");
            $ins->bind_param("iiss", $bookid, $student_id, $borrow_date, $due_date);
            if (!$ins->execute()) { throw new Exception("Failed to create borrow record."); }
            $ins->close();

            // 2) decrement amount by COPIES_PER_BORROW and set status
            $newAmount = $amount - COPIES_PER_BORROW;
            $status    = ($newAmount > 0) ? 'Available' : 'Borrowed';

            $upd = $conn->prepare("UPDATE addbook SET amount=?, status=? WHERE id=?");
            $upd->bind_param("isi", $newAmount, $status, $bookid);
            if (!$upd->execute()) { throw new Exception("Failed to update book availability."); }
            $upd->close();

            $conn->commit();
            $msg = "Borrowed successfully. " . COPIES_PER_BORROW . " copies allocated. Due on " . $due_date . ".";
        } catch (Exception $e) {
            $conn->rollback();
            $msg = $e->getMessage();
        }
    }
}

// Return a hard book (restore same number of copies)
if (isset($_GET['return'])) {
    $bookid = (int)$_GET['return'];

    $conn->begin_transaction();
    try {
        // close the student's open borrow record
        $return_date = date('Y-m-d');
        $upd = $conn->prepare("UPDATE borrow_records 
                               SET return_date=? 
                               WHERE bookid=? AND student_id=? AND return_date IS NULL 
                               ORDER BY id DESC LIMIT 1");
        $upd->bind_param("sii", $return_date, $bookid, $student_id);
        $upd->execute();
        $affected = $upd->affected_rows;
        $upd->close();

        if ($affected <= 0) throw new Exception("No active borrow found to return.");

        // increment amount by COPIES_PER_BORROW and set status available
        $inc = COPIES_PER_BORROW;
        $q = $conn->prepare("UPDATE addbook 
                             SET amount = COALESCE(amount,0) + ?, status='Available' 
                             WHERE id=?");
        $q->bind_param("ii", $inc, $bookid);
        if (!$q->execute()) { throw new Exception("Failed to update book quantity."); }
        $q->close();

        $conn->commit();
        $msg = "Returned successfully. Thank you!";
    } catch (Exception $e) {
        $conn->rollback();
        $msg = $e->getMessage();
    }
}

/* ===========================
   Query data for display
   =========================== */

// All books with current open borrow indicator (for info column)
$books = $conn->query("
  SELECT a.id, a.bookname, a.author, a.pubYear, a.type, a.amount, a.ebook, a.status, a.descText,
         br.student_id AS current_borrower,
         br.borrow_date AS current_borrow_date,
         br.due_date    AS current_due_date
  FROM addbook a
  LEFT JOIN (
    SELECT bookid, student_id, borrow_date, due_date
    FROM borrow_records
    WHERE return_date IS NULL
  ) br ON br.bookid = a.id
  ORDER BY a.id DESC
");

// Student's own open borrows (to show fines & return buttons)
$myOpen = array();
$st = $conn->prepare("SELECT br.id, br.bookid, a.bookname, br.borrow_date, br.due_date
                      FROM borrow_records br
                      JOIN addbook a ON a.id = br.bookid
                      WHERE br.student_id=? AND br.return_date IS NULL
                      ORDER BY br.id DESC");
$st->bind_param("i", $student_id);
$st->execute();
$res = $st->get_result();
if ($res) {
    while ($r = $res->fetch_assoc()) { $myOpen[] = $r; }
}
$st->close();

// Sum fines (open borrows only, computed live)
$totalFine = 0;
foreach ($myOpen as $row) {
    $cf = compute_fine($row['due_date']);
    $late = $cf[0];
    $fine = $cf[1];
    $totalFine += $fine;
}

// For the borrow confirm prompt (avoid inline <?= inside JS)
$confirmBorrowText = "Borrow " . COPIES_PER_BORROW . " copy/copies for " . BORROW_DAYS . " days?";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Library</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .content { margin-left: 240px; padding: 20px; }
    .wrap    { background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,.1); padding:20px; margin-bottom:20px; }
    table{ width:100%; border-collapse:collapse; margin-top:10px; }
    th,td{ border:1px solid #e6e6e6; padding:10px; text-align:center; }
    th{ background:#3b5998; color:#fff; }
    tr:nth-child(even){ background:#f9f9f9; }
    .btn{ display:inline-block; padding:6px 10px; border-radius:6px; text-decoration:none; color:#fff; }
    .btn-borrow{ background:#198754; }
    .btn-return{ background:#fd7e14; }
    .btn-ebook{ background:#0d6efd; }
    .muted{ color:#666; }
    .fine{ color:#c1121f; font-weight:600; }
    .note{ background:#eef7ff; border:1px solid #cfe4ff; padding:8px 10px; border-radius:8px; margin:10px 0; }
  </style>
</head>
<body>
  <!-- Header -->
  <header style="background:#3b5998;color:#fff;padding:15px;text-align:center;position:relative;">
    <h1>Student Library</h1>
  </header>

  <!-- Sidebar (unchanged) -->
  <div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="#">Consulting Hours</a></li>
      <li><a href="StudentApplecation.php">Student Applecation</a></li>
      <li><a href="#">Download Transcript</a></li>
      <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="#">Profile Settings</a></li>
      <li><a href="../php/logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <?php if (!empty($msg)): ?>
      <div class="note"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <!-- My Borrows & Fines -->
    <div class="wrap">
      <h2>My Borrowed Books</h2>
      <?php if (empty($myOpen)): ?>
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
          <?php foreach ($myOpen as $row): 
            $cf2 = compute_fine($row['due_date']);
            $lateDays = $cf2[0];
            $fine     = $cf2[1];
          ?>
          <tr>
            <td style="text-align:left;"><?php echo htmlspecialchars($row['bookname']); ?></td>
            <td><?php echo htmlspecialchars($row['borrow_date']); ?></td>
            <td><?php echo htmlspecialchars($row['due_date']); ?></td>
            <td><?php echo ($lateDays > 0 ? "<span class='fine'>" . $lateDays . " day(s)</span>" : "<span class='muted'>On time</span>"); ?></td>
            <td><?php echo ($lateDays > 0 ? "<span class='fine'>" . $fine . "</span>" : "<span class='muted'>0</span>"); ?></td>
            <td>
              <a class="btn btn-return" href="?return=<?php echo (int)$row['bookid']; ?>" onclick="return confirm('Return this book?');">Return</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <th colspan="4" style="text-align:right;">Total Fine (open borrows):</th>
            <th colspan="2"><?php echo ($totalFine > 0 ? "<span class='fine'>" . $totalFine . "</span>" : "<span class='muted'>0</span>"); ?></th>
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
          <th>Fine (if borrowed by you)</th>
        </tr>

        <?php while ($b = $books->fetch_assoc()): 
          $id     = (int)$b['id'];
          $type   = strtolower($b['type']);
          $amount = (int)(isset($b['amount']) ? $b['amount'] : 0);
          $status = $b['status'];

          // Is this student currently holding this book?
          $mine = student_has_open_borrow($conn, $id, $student_id);
          $fineCell = "<span class='muted'>—</span>";
          if ($mine) {
              $cf3 = compute_fine($mine['due_date']);
              $ld  = $cf3[0];
              $fa  = $cf3[1];
              $fineCell = ($ld > 0) ? "<span class='fine'>" . $fa . "</span>" : "<span class='muted'>0</span>";
          }
        ?>
        <tr>
          <td><?php echo $id; ?></td>
          <td style="text-align:left;"><?php echo htmlspecialchars($b['bookname']); ?></td>
          <td><?php echo htmlspecialchars($b['author']); ?></td>
          <td><?php echo htmlspecialchars($b['pubYear']); ?></td>
          <td><?php echo htmlspecialchars($b['type']); ?></td>
          <td><?php echo ($type === 'ebook' ? '-' : $amount); ?></td>
          <td><?php echo htmlspecialchars($status); ?></td>
          <td style="text-align:left; max-width:360px;"><?php echo htmlspecialchars($b['descText']); ?></td>
          <td>
            <?php if ($type === 'ebook'): ?>
              <?php if (!empty($b['ebook'])): ?>
                <a class="btn btn-ebook" href="<?php echo htmlspecialchars($b['ebook']); ?>" target="_blank">Read / Download</a>
              <?php else: ?>
                <span class="muted">No file</span>
              <?php endif; ?>
            <?php else: ?>
              <?php if ($mine): ?>
                <a class="btn btn-return" href="?return=<?php echo $id; ?>" onclick="return confirm('Return this book?');">Return</a>
              <?php else: ?>
                <?php if ($amount >= COPIES_PER_BORROW): ?>
                  <a class="btn btn-borrow"
                     href="?borrow=<?php echo $id; ?>"
                     onclick="return confirm('<?php echo htmlspecialchars($confirmBorrowText); ?>');">
                     Borrow
                  </a>
                <?php else: ?>
                  <span class="muted">Not enough copies</span>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <td><?php echo $fineCell; ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>
</html>
