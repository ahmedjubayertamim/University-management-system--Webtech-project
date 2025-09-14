<!DOCTYPE html>
<html>
<head>
  
  <title>Salary History</title>
 <link rel="stylesheet" href="../css/teacher.css">
  <style>
    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #3b5998;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table th, table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    table th {
      background: #3b5998;
      color: #fff;
    }
    .paid {
      color: green;
      font-weight: bold;
    }
    .pending {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <h1>Teacher Dashboard</h1>
  </header>

  <div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="CourseMaterials.php">Manage Course Materials</a></li>
    <li><a href="TeacherAttendance.php">Manage Attendance</a></li>
    <li><a href="SubmitGrades.php">Submit Grades</a></li>
    <li><a href="../view/SetConsulting.php">Consulting Hours</a></li>
    <li><a href="StudentApplications.php">Approve Student Requests</a></li>
    <li><a href="ViewSalary.php">View Salary</a></li>
    <li><a href="../php/logout.php" style="background:#ff3b30">Logout</a></li>
  </ul>
</div>
  <div class="container">
    <h2>Salary History</h2>

    <table>
      <thead>
        <tr>
          <th>Month/Year</th>
          <th>Basic Salary</th>
          <th>Allowances</th>
          <th>Deductions</th>
          <th>Net Salary</th>
          <th>Payment Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <!-- Example Data  -->
        <tr>
          <td>July 2025</td>
          <td>$1,000</td>
          <td>$200</td>
          <td>$50</td>
          <td>$1,150</td>
          <td>30-07-2025</td>
          <td class="paid">Paid</td>
        </tr>
        <tr>
          <td>June 2025</td>
          <td>$1,000</td>
          <td>$180</td>
          <td>$30</td>
          <td>$1,150</td>
          <td>30-06-2025</td>
          <td class="paid">Paid</td>
        </tr>
        <tr>
          <td>May 2025</td>
          <td>$1,000</td>
          <td>$150</td>
          <td>$100</td>
          <td>$1,050</td>
          <td>30-05-2025</td>
          <td class="pending">Pending</td>
        </tr>
      </tbody>
    </table>
    
  </div>
  <center>
  <div class="form-popup" id="withdrawForm">
    <h3>Withdraw Salary</h3>
    <form method="post" action="withdraw_request.php">
      <div class="radio-group">
        <label><input type="radio" name="method" value="bkash" required> bKash</label>
        <label><input type="radio" name="method" value="nagad"> Nagad</label>
        <label><input type="radio" name="method" value="rocket"> Rocket</label>
        <label><input type="radio" name="method" value="bank"> Bank Transfer</label>
      </div>

      <input type="text" name="account" placeholder="Enter Account / Mobile Number" required>
      <input type="hidden" name="salary_id" value="2"> <!-- Dynamic Salary ID -->

      <button type="submit" class="submit-btn">Submit Request</button>
      <button type="button" class="close-btn" onclick="closeForm()">Cancel</button>
    </form>
  </div>
</center>
  



</body>
</html>
