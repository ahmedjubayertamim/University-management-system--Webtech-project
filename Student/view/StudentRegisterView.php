<?php if (!isset($mine) || !isset($available) || !array_key_exists('locked', get_defined_vars())) { header("Location: ../php/student_register_controller.php"); exit; } ?>
<!DOCTYPE html>
<html>
<head>
  <title>My Course Registration</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body{background:#f6f9fc}
    h1{margin:18px 0}
    table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden}
    th,td{padding:12px 14px;border-bottom:1px solid #e9eef3}
    th{background:#294a7a;color:#fff;text-align:left}
    tr:nth-child(even){background:#fbfdff}
    .wrap{max-width:1100px;margin:24px auto;padding:0 12px}
    .card{background:#fff;border:1px solid #e6edf5;border-radius:14px;padding:18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(20,40,80,.05)}
    .btn{border:0;padding:8px 12px;border-radius:8px;cursor:pointer}
    .add{background:#0ea5e9;color:#fff}
    .drop{background:#ef4444;color:#fff}
    .final{background:#16a34a;color:#fff}
    .pill-active{color:#16a34a;font-weight:700}
    .pill-closed{color:#ef4444;font-weight:700}
    .muted{text-align:center;color:#708095;padding:18px}
    .topbar{display:flex;justify-content:space-between;align-items:center}
  </style>
</head>
<body>
<div class="wrap">
  <div class="topbar">
    <h1>My Course Registration</h1>
    <div><a href="../php/student_logout.php">Logout</a></div>
  </div>

  <?php if (!empty($msg)): ?>
    <div class="card" style="color:#155724;background:#d4edda;border:1px solid #c3e6cb;"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="card">
    <h2 style="margin:0 0 12px 0;">My Registered Courses</h2>
    <table>
      <tr>
        <th>Course</th><th>Dept</th><th>Time</th><th>Day</th>
        <th>Seats</th><th>Fee</th><th>Window</th><th>Action</th>
      </tr>
      <?php if (empty($mine)): ?>
        <tr><td colspan="8" class="muted">No courses selected</td></tr>
      <?php else: foreach($mine as $c): ?>
        <tr>
          <td><?php echo $c['course_title']; ?></td>
          <td><?php echo $c['department']; ?></td>
          <td><?php echo $c['class_time']; ?></td>
          <td><?php echo $c['class_date']; ?></td>
          <td><?php echo $c['student_count'].'/'.$c['student_capacity']; ?></td>
          <td><?php echo $c['course_fee']; ?></td>
          <td class="<?php echo $c['window_active']==='Active'?'pill-active':'pill-closed'; ?>"><?php echo $c['window_active']; ?></td>
          <td>
            <?php if ($locked): ?>
              Locked
            <?php elseif ($c['window_active']==='Active' && $c['status']==='registered'): ?>
              <form method="post" action="../php/student_register_controller.php" style="display:inline">
                <input type="hidden" name="offered_course_id" value="<?php echo (int)$c['course_id']; ?>">
                <button class="btn drop" type="submit" name="action" value="drop">Drop</button>
              </form>
            <?php else: ?>
              Closed
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </table>
  </div>

  <div class="card">
    <h2 style="margin:0 0 12px 0;">Courses You Can Add (Active Now)</h2>
    <table>
      <tr>
        <th>Course</th><th>Dept</th><th>Time</th><th>Day</th>
        <th>Seats</th><th>Fee</th><th>Action</th>
      </tr>
      <?php if (empty($available)): ?>
        <tr><td colspan="7" class="muted">No courses currently available</td></tr>
      <?php else: foreach($available as $c): ?>
        <tr>
          <td><?php echo $c['course_title']; ?></td>
          <td><?php echo $c['department']; ?></td>
          <td><?php echo $c['class_time']; ?></td>
          <td><?php echo $c['class_date']; ?></td>
          <td><?php echo $c['student_count'].'/'.$c['student_capacity']; ?></td>
          <td><?php echo $c['course_fee']; ?></td>
          <td>
            <?php if ($locked): ?>
              Locked
            <?php else: ?>
              <form method="post" action="../php/student_register_controller.php">
                <input type="hidden" name="offered_course_id" value="<?php echo (int)$c['id']; ?>">
                <button class="btn add" type="submit" name="action" value="add">Add</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </table>
  </div>

  <form method="post" action="../php/student_register_controller.php">
    <button class="btn final" type="submit" name="action" value="finalize" <?php echo $locked?'disabled':''; ?>>Finalize Registration</button>
  </form>
</div>
</body>
</html>
