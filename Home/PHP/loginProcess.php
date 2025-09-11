<?php
session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "universitydb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // VALIDATION
    if (empty($email)) {
        $errors['email'] = "Email required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email!";
    }

    if (empty($password)) {
        $errors['password'] = "Password required!";
    }

    if (empty($errors)) {
        //  ADMIN LOGIN (hardcoded)
        if ($email === "admin@gmail.com" && $password === "Admin@123") {
            $_SESSION['role'] = "admin";
            $_SESSION['email'] = $email;
            header("Location: ../../Admin/View/AdminDashboard.php");
            exit();
        }

        //  TEACHER / STUDENT LOGIN (from DB)
        $stmt = $conn->prepare("SELECT id, password, role, status FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password, $role, $status);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                if ($status === 'enabled') {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $role;

                    if ($role === "teacher") {
                        header("Location: ../../Teacher/View/Teacherdashboard.php");
                    } elseif ($role === "student") {
                        header("Location: ../../Student/view/StudentDashboard.php");
                    } else {
                        $errors['login'] = "Unknown role assigned!";
                    }
                    exit();
                } else {
                    $errors['login'] = "Your account is disabled. Please wait for admin approval.";
                }
            } else {
                $errors['login'] = "Incorrect password!";
            }
        } else {
            $errors['login'] = "No account found with this email!";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
