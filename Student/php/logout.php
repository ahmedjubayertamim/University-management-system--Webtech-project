<?php
// Project/Student/php/logout.php
session_start();
session_unset();
session_destroy();
header('Location: ../../Home/View/login.php');
exit;
