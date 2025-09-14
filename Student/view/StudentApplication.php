<?php

if (!isset($__student_id, $__teachers, $__selectableCount)) {
  header("Location: /Project/Student/php/StudentApplication.php");
  exit;
}
if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
?>
<!DOCTYPE html>
<html>
<head>
  
  <title>Student Application</title>
  
  <link rel="stylesheet" href="../css/CourseRegistration.css">
  <link rel="stylesheet" href="../css/StudentApplication.css.css">
  
</head>
<body>
<header>
  <h1>Student Application Form</h1>
  
</header>

<div class="sidebar">
    <ul>
      <li><a href="../view/StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
       <li><a href="/Project/Student/php/CourseMaterials.php" class="active">Materials</a></li>
      <li><a href="../view/StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="StudentApplication.php">Student Application</a></li>
      <li><a href="../view/MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
       <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="/Project/Student/php/ConsultingHours.php" class="active">Consulting Hours</a></li>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>

<div class="content">
  <div class="wrap">
   

    <?php if (!empty($__msg)): ?><div class="success"><?= h($__msg) ?></div><?php endif; ?>
    <?php if (!empty($__err)): ?><div class="error"><?= h($__err) ?></div><?php endif; ?>

    <form action="../php/submit_application.php" method="POST" class="application-form">
      <input type="hidden" name="student_id" value="<?= (int)$__student_id ?>">

      <table class="form-table">
        <tr>
          <td><label for="teacher">Select Teacher</label></td>
          <td>
            <select id="teacher" name="teacher_id" required>
              <option value="">-- Choose Teacher --</option>
              <?php foreach ($__teachers as $t):
                $tid = (int)$t['teacher_id'];
                $name = trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? ''));
                $selectable = (int)$t['selectable'] === 1;
              ?>
                <?php if ($selectable): ?>
                  <option value="<?= $tid ?>"><?= h($name ?: 'Unnamed Teacher') ?></option>
                <?php else: ?>
                  <option value="" class="dim" disabled><?= h(($name ?: 'Teacher') . ' (not set up)') ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>

        <tr>
          <td><label for="application_type">Application Type</label></td>
          <td>
            <select id="application_type" name="application_type" required>
              <option value="">-- Select Type --</option>
              <option value="leave">Leave Request</option>
              <option value="extension">Project Extension</option>
              <option value="special">Special Consideration</option>
              <option value="other">Other</option>
            </select>
          </td>
        </tr>

        <tr>
          <td><label for="subject">Subject</label></td>
          <td><input type="text" id="subject" name="subject" placeholder="Enter subject" required></td>
        </tr>

        <tr>
          <td><label for="details">Details</label></td>
          <td><textarea id="details" name="details" placeholder="Write your application here..." required></textarea></td>
        </tr>

        <tr>
          <td><label for="application_date">Application Date</label></td>
          <td><input type="date" id="application_date" name="application_date" required></td>
        </tr>

        <tr>
          <td></td>
          <td>
            <button type="submit" class="btn" <?= $__selectableCount === 0 ? 'disabled title="No selectable teachers yet"' : '' ?>>
              Submit Application
            </button>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>
</body>
</html>
