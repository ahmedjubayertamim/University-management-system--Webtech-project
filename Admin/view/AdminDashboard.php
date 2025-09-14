<?php
if (!isset($totalStudents,$totalTeachers,$activeCourses,$departments,$adminName,$adminEmail)) {
  header("Location: ../php/admin_dashboard_controller.php");
  exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
      <title>Admin Dashboard</title>
      <link rel="stylesheet" href="../css/style.css">
   </head>

   <body>
      <header>
        <h1>Admin Dashboard</h1>
      </header>

      <div class="container">
        <aside class="sidebar">
          <ul>
            <li><a href="AdminDashBoard.php">Dashboard</a></li>
            <li><a href="MangeUser.php">Manage User</a></li>
            <li><a href="OfferCourse.php">Set Offer Course</a></li>
            <li><a href="ManageCourse.php">Set Add/Drop Deadline</a></li>
            <li><a href="AssignTeacher.php">Assign Teacher</a></li>
            <li><a href="ApprovePayments.php">Approve Payment</a></li>
            <li><a href="ManageLibrary.php">Manage Library</a></li>
            <li><a href="ViewAllBooks.php">View All Books</a></li>
            <li><a href="ManageTPE.php">ManageTPE</a></li>
            <li><a href="TPEReport.php">TPE Report</a></li>
           <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
         </ul>
       </aside>
     </div>


      <div class="content">
        <div class="form-container">
         <h2 style="margin:0 0 10px 0;">Welcome, <?php echo $adminName; ?></h2>
         <?php 
         if ($adminEmail): 
         ?>
          <div style="color:#555;margin-bottom:10px;">
            <?php 
             echo $adminEmail; 
            ?>
         </div>

          <?php endif; ?>

          <table>
            <tr>
              <th>Total Students</th>
              <th>Faculty Members</th>
              <th>Active Courses</th>
              <th>Departments</th>
           </tr>

           <tr>
             <td><?php echo $totalStudents; ?></td>
             <td><?php echo $totalTeachers; ?></td>
             <td><?php echo $activeCourses; ?></td>
             <td><?php echo $departments; ?></td>
           </tr>
         </table>
       </div>

        <div class="form-container">
          <h2>Quick Actions</h2>
          <table>
          <tr>
            <th>Section</th>
            <th>Go</th>
            <th>Section</th>
            <th>Go</th>
         </tr>
         <tr>
           <td>Manage Users</td>
           <td><a href="MangeUser.php">Open</a></td>
           <td>Offer Courses</td>
           <td><a href="OfferCourse.php">Open</a></td>
         </tr>
         <tr>
           <td>Set Add/Drop Deadline</td>
           <td><a href="ManageCourse.php">Open</a></td>
           <td>Approve Payments</td>
           <td><a href="ApprovePayments.php">Open</a></td>
         </tr>
         <tr>
           <td>Manage Library</td>
           <td><a href="ManageLibrary.php">Open</a></td>
           <td>View All Books</td>
           <td><a href="ViewAllBooks.php">Open</a></td>
         </tr>
         <tr>
           <td>Manage TPE</td>
           <td><a href="ManageTPE.php">Open</a></td>
           <td>TPE Report</td>
           <td><a href="TPEReport.php">Open</a></td>
         </tr>
        </table>
        </div>
     </div>

    </body>
 </html>
