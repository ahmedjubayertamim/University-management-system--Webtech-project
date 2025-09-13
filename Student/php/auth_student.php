<?php
// Project/Student/php/auth_student.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// If not logged-in student, send to login
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
  header('Location: ../../Home/View/login.php');
  exit;
}

// Optional: also ensure account is still enabled
if (isset($_SESSION['status']) && $_SESSION['status'] !== 'enabled') {
  // If admin later disables the account mid-session, force logout:
  session_unset();
  session_destroy();
  header('Location: ../../Home/View/login.php?e=Account+disabled');
  exit;
}
