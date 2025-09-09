<?php

$errors = [];
$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "universitydb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $first_name = trim($_POST['fName'] ?? '');
    $last_name  = trim($_POST['lName'] ?? '');
    $dob        = $_POST['dob'] ?? '';
    $contact    = trim($_POST['cNum'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $gender     = $_POST['gender'] ?? '';
    $pass       = $_POST['pass'] ?? '';
    $cpass      = $_POST['cpass'] ?? '';

    // Validation
    if(empty($first_name)) $errors['fName'] = "First name required!";
    elseif(!preg_match("/^[a-zA-Z-' ]*$/", $first_name)) $errors['fName'] = "Only letters allowed!";

    if(empty($last_name)) $errors['lName'] = "Last name required!";
    elseif(!preg_match("/^[a-zA-Z-' ]*$/", $last_name)) $errors['lName'] = "Only letters allowed!";

    if(empty($dob)) $errors['dob'] = "Date of birth required!";

    if(empty($contact)) $errors['cNum'] = "Contact number required!";
    elseif(!preg_match("/^[0-9]{10,15}$/", $contact)) $errors['cNum'] = "Must be 10–15 digits!";

    if(empty($email)) $errors['email'] = "Email required!";
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email!";

    if(empty($gender)) $errors['gender'] = "Gender required!";

    if(empty($pass)) $errors['pass'] = "Password required!";
    elseif(strlen($pass) < 6 || !preg_match("/[0-9]/",$pass) || !preg_match("/[A-Z]/",$pass) || !preg_match("/[a-z]/",$pass)){
        $errors['pass'] = "Password must have 6+ chars, uppercase, lowercase & number!";
    }

    if(empty($cpass)) $errors['cpass'] = "Confirm password required!";
    elseif($pass !== $cpass) $errors['cpass'] = "Passwords do not match!";

    // Email check
    if(empty($errors)){
        $check_email = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        if($check_email->num_rows > 0){
            $errors['email'] = "Email already registered!";
        }
        $check_email->close();
    }

    if(empty($errors)){
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users 
            (first_name, last_name, dob, contact_number, email, gender, password, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'disabled')");
        $stmt->bind_param("sssssss", $first_name, $last_name, $dob, $contact, $email, $gender, $hashed_password);

        if($stmt->execute()){
            $success = "Registration successful! Your account is disabled until admin approval.";
        } else {
            $errors['db'] = "Database error: ".$conn->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>
