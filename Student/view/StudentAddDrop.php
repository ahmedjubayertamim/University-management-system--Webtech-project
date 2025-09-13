<!DOCTYPE html>
<html>
<head>
  
  <title>Student Add/Drop Courses</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table th, table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }
    table th {
      background: #3b5998;
      color: white;
    }
    table tr:nth-child(even) {
      background: #f9f9f9;
    }
    .btn-action {
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    .btn-add {
      background: green;
      color: white;
    }
    .btn-drop {
      background: red;
      color: white;
    }
    .deadline-msg {
      background: #f1f1f1;
      padding: 10px;
      margin-bottom: 15px;
      border-left: 5px solid #3b5998;
      font-size: 15px;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <h1>Student Add/Drop Courses</h1>
    <div class="search-box">
      <input type="text" placeholder="Search...">
      <button>Search</button>
    </div>
  </header>

  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
    <li><a href="StudentDashboard.php">Dashboard</a></li>
    <li><a href="CourseRegistration.php">Register Courses</a></li>
    <li><a href="PayFees.php">Pay Fees</a></li>
    <li><a href="StudentAddDrop.php">Add/Drop</a></li>
    <li><a href="StudentLibrary.php">Library</a></li>
    

      <li><a href="#">Consulting Hours</a></li>
      <li><a href="StudentApplecation.php">Student Applecation</a></li>
      <li><a href="#">Download Transcript</a></li>
      <li><a href="StudentTPE.php">Submit TPE</a></li>
      <li><a href="#">Profile Settings</a></li>
      <li><a href="logout.php">Logout</a></li>
      
    </ul>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="form-container">
      <h2>Add/Drop Courses</h2>

      <!-- Message showing Add/Drop Deadline -->
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
    // Simulate Admin-set deadline (for demo)
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
      // Disable all buttons if closed
      document.querySelectorAll(".btn-action").forEach(btn => btn.disabled = true);
    }
  </script>
</body>
</html>
