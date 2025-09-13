<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: ../View/login.php?e=Invalid+request"); exit;
}

$conn = new mysqli("localhost","root","","universitydb");
if ($conn->connect_error) { die("DB error: ".$conn->connect_error); }

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header("Location: ../View/login.php?e=Enter+a+valid+email&email=".urlencode($email)); exit;
}
if ($password === '') {
  header("Location: ../View/login.php?e=Password+required&email=".urlencode($email)); exit;
}


if ($email === "admin@gmail.com" && $password === "Admin@123") {
  $_SESSION['role']  = "admin";
  $_SESSION['email'] = $email;
  $_SESSION['name']  = "Administrator";
  $_SESSION['contact'] = "";
  header("Location: ../../Admin/View/AdminDashboard.php"); exit;
}

/* Student/Teacher from DB */
$stmt = $conn->prepare("SELECT id, first_name, last_name, contact_number, email, password, role, status
                        FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows !== 1) {
  header("Location: ../View/login.php?e=No+account+found&email=".urlencode($email)); exit;
}
$u = $res->fetch_assoc();
$stmt->close();

if (!password_verify($password, $u['password'])) {
  header("Location: ../View/login.php?e=Invalid+email+or+password&email=".urlencode($email)); exit;
}
if ($u['status'] !== 'enabled') {
  header("Location: ../View/login.php?e=Account+disabled+by+admin&email=".urlencode($email)); exit;
}
if ($u['role'] !== 'student' && $u['role'] !== 'teacher') {
  header("Location: ../View/login.php?e=Role+not+allowed&email=".urlencode($email)); exit;
}

/* success */
$_SESSION['user_id'] = (int)$u['id'];
$_SESSION['role']    = $u['role'];
$_SESSION['email']   = $u['email'];
$_SESSION['name']    = trim(($u['first_name'] ?? '').' '.($u['last_name'] ?? ''));
$_SESSION['contact'] = $u['contact_number'] ?? '';
$_SESSION['status']  = $u['status'];

if ($u['role'] === 'teacher') {
  header("Location: ../../Teacher/View/Teacherdashboard.php");  
} else {
  header("Location: ../../Student/view/StudentDashboard.php");   
}
exit;
