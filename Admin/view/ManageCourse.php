<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <title>Set Adding Dropping Time</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/AddDropDeadline.css">
  <link rel="stylesheet" href="../css/DeadlineTableShow.css">

</head>
<body>
  <header>
    <h1>University Management System</h1>
  </header>

  <div class="sidebar">
    <ul>
      <li><a href="index.php">Dashboard</a></li>
      <li><a href="MangeUser.php">Manage Users</a></li>
      <li><a href="OfferCourse.php">Offered Course</a></li>
      <li><a href="ManageCourse.php">Manage Courses</a></li>
      <li><a href="Finance.php">Finance & Accounts</a></li>
      <li><a href="SetDeadline.php">Set Add/Drop Deadline</a></li>
      <li><a href="settings.php">Settings</a></li>
      <li><a href="studentLoginPage.php">Logout</a></li>
    </ul>
  </div>

  <div class="content">
    <div class="form-container">
      <h2>Set Course Add/Drop Deadline</h2>
     
      <form method="POST" action="../php/CourseAddDrop.php">
        <div class="form-group">
          <label for="department">Select Department</label>
          <select id="department" name="department" required>
            <option value="all">All Department</option>
            <option value="CSE">CSE</option>
            <option value="EEE">EEE</option>
            <option value="Architecture">Architecture</option>
            <option value="BBA">BBA</option>
            <option value="English">English</option>
            <option value="Economics">Economics</option>
            <option value="Pharmaci">Pharmaci</option>
          </select>
        </div>
        <div class="form-group">
          <label for="course">Select Course</label>
          <select id="course" name="course" required>
            <option value="all">All Courses</option>
            <option value="cse101">CSE101 - Intro to Programming</option>
            <option value="cse201">CSE201 - Database Systems</option>
            <option value="CSE301">CSE301 - Software Engineering</option>
          </select>
        </div>
        <div class="form-group">
          <label for="start_date">Start Date & Time</label>
          <input type="datetime-local" id="start_date" name="start_date" required>
        </div>
        <div class="form-group">
          <label for="end_date">End Date & Time</label>
          <input type="datetime-local" id="end_date" name="end_date" required>
        </div>
        <button type="submit" class="btn edit">Set Deadline</button>
      </form>
    </div>

   <?php 
if (isset($_GET['message'])) {
  echo "<p style='color:green; font-weight:bold; text-align:center; margin:15px 0;'>"
        . htmlspecialchars($_GET['message']) . 
       "</p>";
}
?>


    <div class="deadline-table">
      <h2>All Course Deadlines</h2>
      <table>
        <tr>
          <th>Department</th>
          <th>Course</th>
          <th>Start Date & Time</th>
          <th>End Date & Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>

        <?php
          
          $conn = new mysqli("localhost", "root", "", "universitymanagementsystem");
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }

        
          if (isset($_GET['delete_id'])) {
              $delete_id = intval($_GET['delete_id']);
              $conn->query("DELETE FROM add_drop_deadline WHERE id = $delete_id");
              header("Location: SetDeadline.php?message=Record Deleted Successfully");
              exit;
          }

          $sql = "SELECT * FROM add_drop_deadline ORDER BY id ASC";
          $result = $conn->query($sql);

          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $status = (strtotime($row['end_date']) > time()) ? "Active" : "Expired";
                  echo "<tr>
                          <td>".htmlspecialchars($row['department'])."</td>
                          <td>".htmlspecialchars($row['course'])."</td>
                          <td>".htmlspecialchars($row['start_date'])."</td>
                          <td>".htmlspecialchars($row['end_date'])."</td>
                          <td class='".($status=='Active'?'status-active':'status-expired')."'>$status</td>
                          <td>
                            <a href='../php/editCourseDeadline.php?id=".$row['id']."' class='btn edit'>Edit</a>
                            <a href='../php/deleteCourseDeadline.php?delete_id=".$row['id']."' class='btn delete' onclick=\"return confirm('Are you sure to delete this record?');\">Delete</a>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No records found</td></tr>";
          }

          $conn->close();
        ?>
      </table>
    </div>
  </div>
</body>
</html>
