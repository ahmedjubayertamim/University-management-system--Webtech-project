<?php
// Guard: only logged-in students
require_once __DIR__ . '/../php/auth_student.php';
// DB
require_once __DIR__ . '/../php/config.php';

/**
 * We try user_id first (best), then email as a fallback.
 */
$name = $email = $contact = '';
$uid  = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$semail = isset($_SESSION['email']) ? trim($_SESSION['email']) : '';

if ($uid > 0) {
    $stmt = $conn->prepare(
        "SELECT first_name, last_name, email, contact_number
           FROM users
          WHERE id = ?
          LIMIT 1"
    );
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $name    = htmlspecialchars(trim(($row['first_name'] ?? '').' '.($row['last_name'] ?? '')));
        $email   = htmlspecialchars($row['email'] ?? '');
        $contact = htmlspecialchars($row['contact_number'] ?? '');
    }
    $stmt->close();
}

/* Fallback by email (in case user_id wasn't set but email is) */
if ($name === '' && $semail !== '') {
    $stmt = $conn->prepare(
        "SELECT first_name, last_name, email, contact_number
           FROM users
          WHERE email = ?
          LIMIT 1"
    );
    $stmt->bind_param("s", $semail);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $name    = htmlspecialchars(trim(($row['first_name'] ?? '').' '.($row['last_name'] ?? '')));
        $email   = htmlspecialchars($row['email'] ?? '');
        $contact = htmlspecialchars($row['contact_number'] ?? '');
    }
    $stmt->close();
}

/* Final safety: if still empty, use session values set at login (if any) */
if ($name === '' && isset($_SESSION['name']))    $name    = htmlspecialchars($_SESSION['name']);
if ($email === '' && isset($_SESSION['email']))  $email   = htmlspecialchars($_SESSION['email']);
if ($contact === '' && isset($_SESSION['contact'])) $contact = htmlspecialchars($_SESSION['contact']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    body { margin:0; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f4f6f9; }
    header { background:#3b5998; color:white; padding:15px; text-align:center; position:relative; }
    .sidebar { width:220px; background:#2c3e50; color:white; position:fixed; top:0; left:0; height:100%; padding-top:70px; box-shadow:2px 0 5px rgba(0,0,0,0.2);}
    .sidebar ul { list-style:none; padding:0; margin:0;}
    .sidebar ul li { border-bottom:1px solid rgba(255,255,255,0.1);}
    .sidebar ul li a { display:block; padding:12px 20px; color:white; text-decoration:none; font-size:16px; transition:background 0.3s;}
    .sidebar ul li a:hover { background:#3b5998; padding-left:25px; transition:0.3s;}
    .content { margin-left:240px; padding:20px; }
    .wrap { background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,.1); padding:20px; margin-bottom:20px;}
    .muted { color:#666; margin:0 0 10px;}
    .card { border:1px solid #eee; border-radius:8px; padding:16px;}
    .row { display:flex; gap:16px; flex-wrap:wrap;}
    .col { flex:1 1 260px;}
    .label { color:#666; font-size:13px; margin-bottom:4px;}
    .value { font-size:16px; font-weight:600;}
    .btn { display:inline-block; margin-top:14px; padding:10px 14px; border-radius:8px; text-decoration:none; background:#2d60ff; color:#fff;}
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <h1>Student Dashboard</h1>
    <div class="search-box">
      <input type="text" placeholder="Search...">
      <button>Search</button>
    </div>
  </header>

  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="#">Consulting Hours</a></li>
      <li><a href="StudentApplecation.php">Student Application</a></li>
      <li><a href="#">Download Transcript</a></li>
      <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="#">Profile Settings</a></li>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="wrap">
      <h2>Student – Welcome <?php echo $name ?: 'Student'; ?></h2>
      <p class="muted">Your profile details.</p>

      <div class="card">
        <div class="row">
          <div class="col">
            <div class="label">Full Name</div>
            <div class="value"><?php echo $name; ?></div>
          </div>
          <div class="col">
            <div class="label">Email</div>
            <div class="value"><?php echo $email; ?></div>
          </div>
          <div class="col">
            <div class="label">Contact Number</div>
            <div class="value"><?php echo $contact ?: '—'; ?></div>
          </div>
        </div>
      </div>

      
      
    </div>

    
  </div>
</body>
</html>
