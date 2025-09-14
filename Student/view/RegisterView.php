<?php
if (!isset($offered)) $offered=[];
if (!isset($mine)) $mine=[];
if (!isset($locked)) $locked=false;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Course Registration</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h1>Course Registration</h1>

<?php if (isset($_GET['msg'])): ?>
  <p style="color:green;font-weight:bold;"><?php echo $_GET['msg']; ?></p>
<?php endif; ?>

<h2>My Courses</h2>
<ul>
  <?php if (empty($mine)): ?>
    <li>No courses selected</li>
  <?php else: ?>
    <?php foreach($mine as $c): ?>
      <li><?php echo $c['course_title']; ?></li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul>

<h2>Admin Offered Courses</h2>
<table border="1" cellpadding="5">
  <tr><th>Title</th><th>Dept</th><th>Time</th><th>Day</th><th>Seats</th><th>Fee</th><th>Action</th></tr>
  <?php foreach($offered as $c): ?>
    <tr>
      <td><?php echo $c['course_title']; ?></td>
      <td><?php echo $c['department']; ?></td>
      <td><?php echo $c['class_time']; ?></td>
      <td><?php echo $c['class_date']; ?></td>
      <td><?php echo $c['student_count']."/".$c['student_capacity']; ?></td>
      <td><?php echo $c['course_fee']; ?></td>
      <td>
        <?php if ($locked): ?>
          Locked
        <?php else: ?>
          <form method="post" action="../php/register_controller.php" style="display:inline">
            <input type="hidden" name="offered_course_id" value="<?php echo $c['id']; ?>">
            <button type="submit" name="action" value="add">Add</button>
          </form>
          <form method="post" action="../php/register_controller.php" style="display:inline">
            <input type="hidden" name="offered_course_id" value="<?php echo $c['id']; ?>">
            <button type="submit" name="action" value="drop">Drop</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<form method="post" action="../php/register_controller.php" style="margin-top:16px;">
  <button type="submit" name="action" value="finalize" <?php echo $locked?'disabled':''; ?>>Finalize Registration</button>
</form>
</body>
</html>
