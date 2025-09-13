<?php
require_once __DIR__ . '/../php/auth_student.php';

require_once __DIR__ . '/../php/config.php';
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
  
   <link rel="stylesheet" href="../css/Dashboard.css">

   
  
</head>
<body>
 
  <header>
    <h1>Student Dashboard</h1>
    
  </header>

  
  <div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="StudentApplication.php">Student Application</a></li>
      <li><a href="MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
       <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="#">Consulting Hours</a></li>
      
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

  <div class="content">
  <div class="wrap">
    <h2>Welcome, Student</h2>

    <div class="profile-cards">
      <div class="profile-card">
        <div class="label">Full Name</div>
        <div class="value"><?= htmlspecialchars($name) ?></div>
      </div>
      <div class="profile-card">
        <div class="label">Email</div>
        <div class="value"><?= htmlspecialchars($email) ?></div>
      </div>
      <div class="profile-card">
        <div class="label">Contact Number</div>
        <div class="value"><?= htmlspecialchars($contact) ?></div>
      </div>
    </div>
  </div>
</div>


      
      
    </div>

    
  </div>
</body>
</html>
