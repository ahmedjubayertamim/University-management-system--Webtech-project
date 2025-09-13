<?php
$errors  = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

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
    $department = $_POST['department'] ?? '';
    $role       = $_POST['role'] ?? '';
    $pass       = $_POST['pass'] ?? '';
    $cpass      = $_POST['cpass'] ?? '';

    
    if ($first_name === '') {
        $errors['fName'] = "First name required!";
    } elseif (!preg_match("/^[a-zA-Z-' ]+$/", $first_name)) {
        $errors['fName'] = "Only letters allowed!";
    }

    if ($last_name === '') {
        $errors['lName'] = "Last name required!";
    } elseif (!preg_match("/^[a-zA-Z-' ]+$/", $last_name)) {
        $errors['lName'] = "Only letters allowed!";
    }

    if ($dob === '') {
        $errors['dob'] = "Date of birth required!";
    }

    if ($contact === '') {
        $errors['cNum'] = "Contact number required!";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $contact)) {
        $errors['cNum'] = "Must be 10–15 digits!";
    }

    if ($email === '') {
        $errors['email'] = "Email required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email!";
    }

    
    if (!in_array($gender, ['Male','Female'], true)) {
        $errors['gender'] = "Select gender (Male/Female).";
    }

    
    $allowedDepts = ['CSE','EEE','BBA'];
    if (!in_array($department, $allowedDepts, true)) {
        $errors['department'] = "Select a valid department.";
    }

    
    if (!in_array($role, ['student','teacher'], true)) {
        $errors['role'] = "Select role (student/teacher).";
    }

    if ($pass === '') {
        $errors['pass'] = "Password required!";
    } elseif (strlen($pass) < 6 || !preg_match("/[0-9]/",$pass) || !preg_match("/[A-Z]/",$pass) || !preg_match("/[a-z]/",$pass)) {
        $errors['pass'] = "Password must have 6+ chars, uppercase, lowercase & number!";
    }

    if ($cpass === '') {
        $errors['cpass'] = "Confirm password required!";
    } elseif ($pass !== $cpass) {
        $errors['cpass'] = "Passwords do not match!";
    }

    
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors['email'] = "Email already registered!";
        }
        $check->close();
    }

    // Insert
    if (empty($errors)) {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

       
        $sql = "INSERT INTO users
                (first_name, last_name, dob, contact_number, email, gender, department, password, role, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'disabled')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $errors['db'] = "Prepare failed: " . $conn->error;
        } else {
            
            $stmt->bind_param(
                "sssssssss",
                $first_name,
                $last_name,
                $dob,
                $contact,
                $email,
                $gender,
                $department,
                $hashed_password,
                $role
            );

            if ($stmt->execute()) {
                $success = "Registration successful! Your account is disabled until admin approval.";
                
                $_POST = [];
            } else {
                $errors['db'] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>
