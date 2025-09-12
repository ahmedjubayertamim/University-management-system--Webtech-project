<?php
include "../PHP/config.php"; 

$result = $conn->query("SELECT * FROM offered_course ORDER BY id ASC");
if(!$result){
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
       <head>
          <title>Set Offered Course</title>
          <link rel="stylesheet" href="../css/style.css">
          <link rel="stylesheet" href="../css/OfferedCourseStyle.css">
          <link rel="stylesheet" href="../css/OfferCourseActionButton.css">  
      </head>

      <body>
            <header>
               <h1>Set Offered Section: 2025-2026, Spring</h1>
               <div class="search-box">
               <input type="text" id="searchInput" placeholder="Search by Course title, Department">
           </header>

            <div class="container">
                  <aside class="sidebar">
                      <ul>
                         <li><a href="index.php">Dashboard</a></li>
                         <li><a href="MangeUser.php">Manage User</a></li>
                         <li><a href="OfferCourse.php">Offered Course</a></li>
                         <li><a href="ManageCourse.php">Manage Courses</a></li>
                         <li><a href="ManageAddDrop.php">Manage Add/Drop Deadline </a></li>
                         <li><a href="Manage Accounce.php">Manage Accounce</a></li>
                         <li><a href="ManageLibrary.php">Manage Library</a></li>
                         <li><a href="ViewAllBooks.php">View All Books</a></li>
                         <li><a href="settings.php">Settings</a></li>
                         <li><a href="studentLoginPage.php">Logout</a></li>
                      </ul>
                  </aside>
 
                   <main>
                      <h2>Set Offered Course</h2>
                       <div class = "content">
                      <div class="form-container">

                        <form method="POST" action="../php/OfferedCourse.php">
                            <label>Department:</label>
                            <select name="department" required>
                            <option value="">-- Select Department --</option>
                            <option value="CSE">CSE</option>
                            <option value="EEE">EEE</option>
                           <option value="Architecture ">Architecture</option>
                           <option value="BBA">BBA</option>
                           <option value="English">English</option>
                           </select><br><br>

                           <label>Course Title:</label>
                           <select name="course_title" required>
                           <option value="">-- Select Course --</option>
                           <option value="Programming Fundamentals">CSE101 - Programming Fundamentals</option>
                           <option value="Data Structures">CSE201 - Data Structures</option>
                           <option value="Database Systems">CSE301 - Database Systems</option>
                           <option value="Compiler Design">CSE401 - Compiler Design</option>
                           <option value="Graphics Design">CSE401 - Graphics Design</option>
                           <option value="Device">EEE101 - Device</option>
                           <option value="Microprocessor">EEE102 - Microprocessor</option>
                           <option value="Introduction to Architecture">ARC101 - Introduction to Architecture</option>
                           <option value="Business">BBA101- Business</option>
                           <option value="English Writting">ENG101- English Writting</option>
                           </select><br><br>

                           <label>Student Capacity:</label>
                           <select name="student_capacity" required>
                           <option value="">-- Select Capacity --</option>
                           <option value="30">30</option>
                           <option value="40">40</option>
                           <option value="50">50</option>
                           <option value="60">60</option>
                           </select><br><br>

                           <label>Class Time:</label>
                           <select name="class_time" required>
                           <option value="">-- Select Time --</option>
                           <option value="08:00 AM">08:00 AM</option>
                           <option value="10:00 AM">10:00 AM</option>
                           <option value="12:00 PM">12:00 PM</option>
                           <option value="2:00 PM">02:00 PM</option>
                           <option value="4:00 PM">04:00 PM</option>
                           </select><br><br>

                           <label>Class Date:</label>
                           <select name="class_date" required>
                           <option value="">-- Select Day --</option>
                           <option value="Sunday">Sunday</option>
                           <option value="Monday">Monday</option>
                           <option value="Tuesday">Tuesday</option>
                           <option value="Wednesday">Wednesday</option>
                           <option value="Thursday">Thursday</option>
                           </select><br><br>

                           <label>Class Duration (hours):</label>
                           <select name="duration" required>
                           <option value="">-- Select Duration --</option>
                           <option value="1">1 Hour</option>
                           <option value="2">2 Hours</option>
                           <option value="3">3 Hours</option>
                           </select><br><br>

                           <label>Course Fee(BDT):</label>
                           <input type="number" name="course_fee" placeholder="Enter Course Fee" required>
                           <br><br>

                           <button type="submit">Offer Course</button>
                     </form>
                  </div>

            <table id = "userTable">
                <thead>
                   <tr>
                      <th>ID</th>
                      <th>Department</th>
                      <th>Course Title</th>
                      <th>Capacity</th>
                     <th>Count</th>
                     <th>Class Time</th>
                     <th>Class Date</th>
                     <th>Duration</th>  
                     <th>Amount</th> 
                     <th>Action</th>
     
                    </tr>
               </thead>
               <tbody>
                  <?php 
                  while ($row = $result->fetch_assoc()) { 
                  ?>
              </tbody>
                  <tr>
                     <td><?php echo $row['id']; ?></td>
                     <td><?php echo $row['department']; ?></td>
                     <td><?php echo $row['course_title']; ?></td>
                     <td><?php echo $row['student_capacity']; ?></td>
                     <td><?php echo $row['student_count']; ?></td>
                     <td><?php echo $row['class_time']; ?></td>
                     <td><?php echo $row['class_date']; ?></td>
                     <td><?php echo $row['duration']; ?> hrs</td>
                     <td><?php echo $row['course_fee']; ?> TK</td>

                      <td>
                         <a href="../php/editCourse.php?id=<?php echo $row['id']; ?>" 
                         class="btn-edit">Edit</a>
                         <a href="../php/deleteCourse.php?id=<?php echo $row['id']; ?>" 
                         class="btn-delete" 
                         onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                      </td>
               
                    </tr>
                      <?php } ?>
             </table>
         </div>
     </body>
       <script>
           window.onload = function() {
              document.getElementById("searchInput").addEventListener("keyup", function() {
              const filter = this.value.toLowerCase();
              const rows = document.querySelectorAll("#userTable tbody tr");

              rows.forEach(row => {
              const department = row.cells[1].innerText.toLowerCase();
              const course_title = row.cells[2].innerText.toLowerCase();
              row.style.display = (department.includes(filter) || course_title.includes(filter)) ? "" : "none";
              });
            });
           };
     
        </script>
</html>
