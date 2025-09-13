<?php
include "../PHP/registrationProcess.php";
?>
<!DOCTYPE html>
<html>
<head>

  <title>Registration Page</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <form action="" method="POST">
    <div class="header">
      <img src="../img/ABCVarsityLogo.png" alt="Logo">
      <div class="title">
        <p>ABC INTERNATIONAL UNIVERSITY-BANGLADESH</p>
        <div class="leader">-Where leaders are created.</div>
      </div>
    </div>

    <?php if (!empty($success)): ?>
      <div class="success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['db'])): ?>
      <div class="error" style="margin-left:0"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <div class="form-row">
      <label for="fName">First Name:</label>
      <input type="text" id="fName" name="fName" value="<?= htmlspecialchars($_POST['fName'] ?? '') ?>">
    </div>
    <span class="error"><?= $errors['fName'] ?? '' ?></span>

    <div class="form-row">
      <label for="lName">Last Name:</label>
      <input type="text" id="lName" name="lName" value="<?= htmlspecialchars($_POST['lName'] ?? '') ?>">
    </div>
    <span class="error"><?= $errors['lName'] ?? '' ?></span>

    <div class="form-row">
      <label for="dob">Date of Birth:</label>
      <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
    </div>
    <span class="error"><?= $errors['dob'] ?? '' ?></span>

    <div class="form-row">
      <label for="cNum">Contact Number:</label>
      <input type="text" id="cNum" name="cNum" value="<?= htmlspecialchars($_POST['cNum'] ?? '') ?>">
    </div>
    <span class="error"><?= $errors['cNum'] ?? '' ?></span>

    <div class="form-row">
      <label for="email">Email:</label>
      <input type="text" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <span class="error"><?= $errors['email'] ?? '' ?></span>

    <div class="form-row">
      <label for="gender">Gender:</label>
      <select id="gender" name="gender">
        <option value="">-- Select Gender --</option>
        <option value="Male"   <?= (($_POST['gender'] ?? '')==="Male")?"selected":"" ?>>Male</option>
        <option value="Female" <?= (($_POST['gender'] ?? '')==="Female")?"selected":"" ?>>Female</option>
      </select>
    </div>
    <span class="error"><?= $errors['gender'] ?? '' ?></span>

    <!-- NEW: Department (stored in users.department) -->
    <div class="form-row">
      <label for="department">Department:</label>
      <select id="department" name="department">
        <option value="">-- Select Department --</option>
        <option value="CSE" <?= (($_POST['department'] ?? '')==="CSE")?"selected":"" ?>>CSE</option>
        <option value="EEE" <?= (($_POST['department'] ?? '')==="EEE")?"selected":"" ?>>EEE</option>
        <option value="BBA" <?= (($_POST['department'] ?? '')==="BBA")?"selected":"" ?>>BBA</option>
      </select>
    </div>
    <span class="error"><?= $errors['department'] ?? '' ?></span>

    <div class="form-row">
      <label for="role">Role:</label>
      <select id="role" name="role">
        <option value="">-- Select Role --</option>
        <option value="student" <?= (($_POST['role'] ?? '')==="student")?"selected":"" ?>>Student</option>
        <option value="teacher" <?= (($_POST['role'] ?? '')==="teacher")?"selected":"" ?>>Teacher</option>
      </select>
    </div>
    <span class="error"><?= $errors['role'] ?? '' ?></span>

    <div class="form-row">
      <label for="pass">Password:</label>
      <input type="password" id="pass" name="pass">
    </div>
    <span class="error"><?= $errors['pass'] ?? '' ?></span>

    <div class="form-row">
      <label for="cpass">Confirm Password:</label>
      <input type="password" id="cpass" name="cpass">
    </div>
    <span class="error"><?= $errors['cpass'] ?? '' ?></span>

    <button type="submit" id="signUp">Sign Up</button>
    <a href="../View/login.php" class="login-link">Already have an account? Log In.</a>
  </form>
</body>
</html>
