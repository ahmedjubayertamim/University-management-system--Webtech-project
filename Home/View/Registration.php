<?php
include "../PHP/registrationProcess.php";  // This file handles validation + DB insert
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" href="../css/Registration.css">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <img src="../img/ABCVarsityLogo.png" alt="ABC Logo">
            <h2>Registration</h2>
            <h3>ABC INTERNATIONAL UNIVERSITY-BANGLADESH</h3>
            <div id="leader">-Where leaders are created.</div>
        </div>

        <form action="" method="POST">
            <label>First Name:</label>
            <input type="text" name="fName" value="<?= htmlspecialchars($_POST['fName'] ?? '') ?>">
            <span class="error"><?= $errors['fName'] ?? '' ?></span>

            <label>Last Name:</label>
            <input type="text" name="lName" value="<?= htmlspecialchars($_POST['lName'] ?? '') ?>">
            <span class="error"><?= $errors['lName'] ?? '' ?></span>

            <label>Date of Birth:</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
            <span class="error"><?= $errors['dob'] ?? '' ?></span>

            <label>Contact Number:</label>
            <input type="text" name="cNum" value="<?= htmlspecialchars($_POST['cNum'] ?? '') ?>">
            <span class="error"><?= $errors['cNum'] ?? '' ?></span>

            <label>Email:</label>
            <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <span class="error"><?= $errors['email'] ?? '' ?></span>

            <label>Gender:</label>
            <select name="gender">
                <option value="">Select Gender</option>
                <option value="Male" <?= (($_POST['gender'] ?? '')=="Male")?"selected":"" ?>>Male</option>
                <option value="Female" <?= (($_POST['gender'] ?? '')=="Female")?"selected":"" ?>>Female</option>
            </select>
            <span class="error"><?= $errors['gender'] ?? '' ?></span>

            <label>Password:</label>
            <input type="password" name="pass">
            <span class="error"><?= $errors['pass'] ?? '' ?></span>

            <label>Confirm Password:</label>
            <input type="password" name="cpass">
            <span class="error"><?= $errors['cpass'] ?? '' ?></span>

            <button class="btn" type="submit">Sign Up</button>
        </form>

        <?php if(!empty($success)): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>
        <?php if(isset($errors['db'])): ?>
            <p class="error"><?= $errors['db'] ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
