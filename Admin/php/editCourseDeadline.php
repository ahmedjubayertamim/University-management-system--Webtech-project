<?php
$conn = new mysqli("localhost", "root", "", "universitymanagementsystem");
    if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
    }

    $id = intval($_GET['id']); 
    $message = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $department = $_POST['department'];
       $course = $_POST['course'];
       $start_date = $_POST['start_date'];
       $end_date = $_POST['end_date'];
       $status = $_POST['status'];

       $sql = "UPDATE add_drop_deadline 
            SET department='$department', course='$course', 
                start_date='$start_date', end_date='$end_date',
                status='$status'
            WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
           header("Location: ../view/ManageCourse.php?message=Record Updated Successfully");
           exit;
        } else {
           $message = "Error: " . $conn->error;
        }
    }

$sql = "SELECT * FROM add_drop_deadline WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
   <head>
      <title>Edit Deadline</title>
      <link rel="stylesheet" href="../css/style.css">
      <link rel="stylesheet" href="../css/editCourseDeadlineStyle.css">
  </head>

   <body>
       <div class="edit-container">
          <h2>Edit Course Deadline</h2>
          <?php if ($message) echo "<p class='message'>$message</p>"; ?>

          <form method="POST">
              <div class="form-group">
                  <label>Department:</label>
                  <input type="text" name="department" value="<?php echo htmlspecialchars($row['department']); ?>" required>
             </div>

              <div class="form-group">
                  <label>Course:</label>
                  <input type="text" name="course" value="<?php echo htmlspecialchars($row['course']); ?>" required>
              </div>

              <div class="form-group">
                  <label>Start Date & Time:</label>
                  <input type="datetime-local" name="start_date" value="<?php echo date('Y-m-d\TH:i', strtotime($row['start_date'])); ?>" required>
             </div>

              <div class="form-group">
                 <label>End Date & Time:</label>
                 <input type="datetime-local" name="end_date" value="<?php echo date('Y-m-d\TH:i', strtotime($row['end_date'])); ?>" required>
             </div>

              <button type="submit" class="btn edit">Update</button>
              <a href="../view/ManageCourse.php" class="btn delete">Cancel</a>
         </form>
     </div>
  </body>
</html>
