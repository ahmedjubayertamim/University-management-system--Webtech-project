<!DOCTYPE html>
<html>
<head>
  
  <title>Student Add/Drop Courses</title>
  <link rel="stylesheet" href="../css/Dashboard.css">
  <style>
   
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <h1>Student Add/Drop Courses</h1>
    
  </header>

  <div class="sidebar">
    <ul>
      <li><a href="StudentDashboard.php">Dashboard</a></li>
      <li><a href="CourseRegistration.php">Register Courses</a></li>
       <li><a href="/Project/Student/php/CourseMaterials.php" class="active">Materials</a></li>
      <li><a href="StudentAddDrop.php">Add/Drop</a></li>
      <li><a href="StudentLibrary.php">Library</a></li>
      <li><a href="StudentApplication.php">Student Application</a></li>
      <li><a href="MyApplications.php">My Applications</a></li>
      <li><a href="/Project/Student/php/MyResults.php">My Results</a></li>
      <li><a href="PayFees.php">Pay Fees</a></li>
       <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="/Project/Student/php/ConsultingHours.php" class="active">Consulting Hours</a></li>
      <li><a href="../php/logout.php"style="background:#ff3b30">Logout</a></li>
    </ul>
  </div>
  <!-- Content -->
  <div class="content">
    <div class="form-container">
      <h2>Add/Drop Courses</h2>

      <div class="deadline-msg" id="deadline-status">
        Checking Add/Drop availability...
      </div>

      <form action="add_drop_action.php" method="POST">
        <table>
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Course Title</th>
              <th>Section</th>
              <th>Capacity</th>
              <th>Enrolled</th>
              <th>Available Seats</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>CSE101</td>
              <td>Intro to Programming</td>
              <td>A</td>
              <td>40</td>
              <td>35</td>
              <td>5</td>
              <td>
                <button type="submit" name="add" value="CSE101-A" class="btn-action btn-add">Add</button>
                <button type="submit" name="drop" value="CSE101-A" class="btn-action btn-drop">Drop</button>
              </td>
            </tr>
            <tr>
              <td>CSE202</td>
              <td>Database Systems</td>
              <td>B</td>
              <td>40</td>
              <td>40</td>
              <td>0</td>
              <td>
                <button type="button" class="btn-action btn-add" disabled>Full</button>
                <button type="submit" name="drop" value="CSE202-B" class="btn-action btn-drop">Drop</button>
              </td>
            </tr>
            <tr>
              <td>CSE303</td>
              <td>Operating Systems</td>
              <td>A</td>
              <td>35</td>
              <td>28</td>
              <td>7</td>
              <td>
                <button type="submit" name="add" value="CSE303-A" class="btn-action btn-add">Add</button>
                <button type="submit" name="drop" value="CSE303-A" class="btn-action btn-drop">Drop</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
    </div>
  </div>

  <script>
  
    const deadlineStart = new Date("2025-09-01T09:00:00");
    const deadlineEnd   = new Date("2025-09-10T23:59:00");
    const now = new Date();

    const statusDiv = document.getElementById("deadline-status");

    if (now >= deadlineStart && now <= deadlineEnd) {
      statusDiv.innerHTML = 
        "<strong>Status:</strong> Add/Drop is <span style='color:green;font-weight:bold;'>OPEN</span> until " + deadlineEnd.toLocaleString();
    } else {
      statusDiv.innerHTML = 
        "<strong>Status:</strong> Add/Drop is <span style='color:red;font-weight:bold;'>CLOSED</span>";
      
      document.querySelectorAll(".btn-action").forEach(btn => btn.disabled = true);
    }
  </script>
</body>
</html>
