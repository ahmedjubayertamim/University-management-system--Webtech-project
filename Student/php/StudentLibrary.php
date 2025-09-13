<?php

session_start();
require_once __DIR__ . '/auth_student.php'; 
require_once __DIR__ . '/config.php';       

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

if (!defined('BORROW_DAYS'))       define('BORROW_DAYS', 14);
if (!defined('FINE_PER_DAY'))      define('FINE_PER_DAY', 10);
if (!defined('COPIES_PER_BORROW')) define('COPIES_PER_BORROW', 3);

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) { http_response_code(403); exit('Not authorized.'); }
$__student_user_id = $user_id;



function outstanding_fine(mysqli $conn, int $student_id): int {
    $sum = 0;
    $q = $conn->prepare("SELECT due_date FROM borrow_records WHERE student_id=? AND return_date IS NULL");
    $q->bind_param("i", $student_id);
    $q->execute();
    $res = $q->get_result();
    while ($row = $res->fetch_assoc()) {
        $today = strtotime(date('Y-m-d'));
        $due   = strtotime($row['due_date']);
        if ($today > $due) {
            $days = (int)floor(($today - $due)/(60*60*24));
            $sum += $days * FINE_PER_DAY;
        }
    }
    $q->close();
    return $sum;
}
function compute_fine(string $dueYmd): array {
    $today = strtotime(date('Y-m-d'));
    $due   = strtotime($dueYmd);
    if ($today <= $due) return [0, 0];
    $days = (int)floor(($today - $due)/(60*60*24));
    return [$days, $days * FINE_PER_DAY];
}
function student_has_open_borrow(mysqli $conn, int $bookid, int $student_id): ?array {
    $q = $conn->prepare("SELECT id, borrow_date, due_date
                         FROM borrow_records
                         WHERE bookid=? AND student_id=? AND return_date IS NULL
                         ORDER BY id DESC LIMIT 1");
    $q->bind_param("ii", $bookid, $student_id);
    $q->execute();
    $res = $q->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $q->close();
    return $row ?: null;
}

$__msg = "";



if (isset($_GET['borrow'])) {
    $bookid = (int)$_GET['borrow'];

    $owed = outstanding_fine($conn, $user_id);
    if ($owed > 0) {
        $__msg = "You cannot borrow new books while you owe a fine ({$owed}). Please return overdue books and clear fines first.";
    } else {
        $conn->begin_transaction();
        try {
            // Lock the book row
            $stmt = $conn->prepare("SELECT id, type, amount FROM addbook WHERE id=? FOR UPDATE");
            $stmt->bind_param("i", $bookid);
            $stmt->execute();
            $res  = $stmt->get_result();
            $book = $res ? $res->fetch_assoc() : null;
            $stmt->close();

            if (!$book) throw new Exception("Book not found.");
            if (strtolower($book['type']) === 'ebook') throw new Exception("This is an e-book. No need to borrow.");

            $amount = (int)($book['amount'] ?? 0);
            if ($amount < COPIES_PER_BORROW) throw new Exception("Not enough copies. Need at least ".COPIES_PER_BORROW.".");

            if (student_has_open_borrow($conn, $bookid, $user_id)) {
                throw new Exception("You already have this book borrowed.");
            }

            $borrow_date = date('Y-m-d');
            $due_date    = date('Y-m-d', strtotime('+'.BORROW_DAYS.' days'));

            
            $ins = $conn->prepare("INSERT INTO borrow_records (bookid, student_id, borrow_date, due_date)
                                   VALUES (?, ?, ?, ?)");
            $ins->bind_param("iiss", $bookid, $user_id, $borrow_date, $due_date);
            if (!$ins->execute()) throw new Exception("Failed to create borrow record.");
            $ins->close();

           
            $newAmount = $amount - COPIES_PER_BORROW;
            $status    = ($newAmount > 0) ? 'Available' : 'Borrowed';
            $upd = $conn->prepare("UPDATE addbook SET amount=?, status=? WHERE id=?");
            $upd->bind_param("isi", $newAmount, $status, $bookid);
            if (!$upd->execute()) throw new Exception("Failed to update book availability.");
            $upd->close();

            $conn->commit();
            $__msg = "Borrowed successfully. ".COPIES_PER_BORROW." copies allocated. Due on {$due_date}.";
        } catch (Exception $e) {
            $conn->rollback();
            $__msg = $e->getMessage();
        }
    }
}

if (isset($_GET['return'])) {
    $bookid = (int)$_GET['return'];

    $conn->begin_transaction();
    try {
        $return_date = date('Y-m-d');
        $upd = $conn->prepare("UPDATE borrow_records
                               SET return_date=?
                               WHERE bookid=? AND student_id=? AND return_date IS NULL
                               ORDER BY id DESC LIMIT 1");
        $upd->bind_param("sii", $return_date, $bookid, $user_id);
        $upd->execute();
        $affected = $upd->affected_rows;
        $upd->close();
        if ($affected <= 0) throw new Exception("No active borrow found to return.");

        $inc = COPIES_PER_BORROW;
        $q = $conn->prepare("UPDATE addbook
                             SET amount = COALESCE(amount,0) + ?, status='Available'
                             WHERE id=?");
        $q->bind_param("ii", $inc, $bookid);
        if (!$q->execute()) throw new Exception("Failed to update book quantity.");
        $q->close();

        $conn->commit();
        $__msg = "Returned successfully. Thank you!";
    } catch (Exception $e) {
        $conn->rollback();
        $__msg = $e->getMessage();
    }
}


$__books = [];
$books = $conn->query("
  SELECT a.id, a.bookname, a.author, a.pubYear, a.type, a.amount, a.ebook, a.status, a.descText
  FROM addbook a
  ORDER BY a.id DESC
");
while ($row = $books->fetch_assoc()) $__books[] = $row;


$__myOpen = [];
$st = $conn->prepare("SELECT br.id, br.bookid, a.bookname, br.borrow_date, br.due_date
                      FROM borrow_records br
                      JOIN addbook a ON a.id = br.bookid
                      WHERE br.student_id=? AND br.return_date IS NULL
                      ORDER BY br.id DESC");
$st->bind_param("i", $user_id);
$st->execute();
$res = $st->get_result();
while ($r = $res->fetch_assoc()) $__myOpen[] = $r;
$st->close();

$__totalFine = 0;
foreach ($__myOpen as $row) {
    [$lateDays, $fine] = compute_fine($row['due_date']);
    $__totalFine += $fine;
}


$__confirmBorrowText = "Borrow ".COPIES_PER_BORROW." copy/copies for ".BORROW_DAYS." days?";

// Expose constants to view
$__FINE_PER_DAY      = FINE_PER_DAY;
$__BORROW_DAYS       = BORROW_DAYS;
$__COPIES_PER_BORROW = COPIES_PER_BORROW;


require_once __DIR__ . '/../view/StudentLibrary.php';
