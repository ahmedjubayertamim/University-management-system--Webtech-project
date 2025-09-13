
<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <img src="../img/ABCVarsityLogo.png" alt="ABC Logo">
            <h2>Login</h2>
        </div>

        <form action="../PHP/loginProcess.php" method="POST">
            <label>Email:</label>
            <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <span class="error"><?= $errors['email'] ?? '' ?></span>

            <label>Password:</label>
            <input type="password" name="password">
            <span class="error"><?= $errors['password'] ?? '' ?></span>

            <button class="btn" type="submit">Login</button>
        </form>

        <?php if(isset($errors['login'])): ?>
            <p class="error" style="text-align:center;"><?= $errors['login'] ?></p>
        <?php endif; ?>

        <a class="link-btn" href="registration.php">Create an Account</a>
    </div>
</body>
</html>
