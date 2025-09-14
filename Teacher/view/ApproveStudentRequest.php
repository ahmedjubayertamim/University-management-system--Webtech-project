<!DOCTYPE html>
<html>
<head>
  
  <title>Approve Student Requests</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    .content {
      margin-left: 240px;
      padding: 20px;
    }
    .form-container {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    .form-container h2 {
      margin-bottom: 15px;
      color: #2c3e50;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
    }
    table th, table td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    table th {
      background: #3b5998;
      color: #fff;
    }
    table tr:hover {
      background: #f1f1f1;
    }
    .action-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      margin: 2px;
    }
    .approve-btn {
      background: #27ae60;
      color: white;
    }
    .reject-btn {
      background: #e74c3c;
      color: white;
    }
    .pending {
      color: orange;
      font-weight: bold;
    }
    .approved {
      color: green;
      font-weight: bold;
    }
    .rejected {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>

<header>
  <h1>Approve Student Requests</h1>
</header>

<div class="sidebar">
  <ul>
    <li><a href="TeacherDashboard.php">Dashboard</a></li>
    <li><a href="CourseMaterials.php">Manage Course Materials</a></li>
    <li><a href="StudentAttendance.php">Manage Attendance</a></li>
    <li><a href="SubmitResult.php">Submit Grades</a></li>
    <li><a href="ViewSalary.php">View Salary</a></li>
    <li><a href="SetConsulting.php">Consulting Hours</a></li>
    <li><a href="ApproveStudentRequest.php">Approve Student Requests</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="form-container">
    <h2>Student Applications</h2>
    <table>
      <thead>
        <tr>
          <th>Student ID</th>
          <th>Student Name</th>
          <th>Application Type</th>
          <th>Subject</th>
          <th>Details</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- Example -->
        <tr>
          <td>ST12345</td>
          <td>Ahmed Jubayer</td>
          <td>Leave Request</td>
          <td>Sick Leave</td>
          <td>Requesting 3 days sick leave due to illness.</td>
          <td class="pending">Pending</td>
          <td>
            <form action="approve_request.php" method="POST" style="display:inline;">
              <input type="hidden" name="app_id" value="1">
              <button type="submit" name="action" value="approve" class="action-btn approve-btn">Approve</button>
              <button type="submit" name="action" value="reject" class="action-btn reject-btn">Reject</button>
            </form>
          </td>
        </tr>

        <!-- Example Approved -->
        <tr>
          <td>ST56789</td>
          <td>Sarah Khan</td>
          <td>Project Extension</td>
          <td>Database Project</td>
          <td>Requesting 1-week extension due to data issues.</td>
          <td class="approved">Approved</td>
          <td>
            <button class="action-btn approve-btn" disabled>Approve</button>
            <button class="action-btn reject-btn" disabled>Reject</button>
          </td>
        </tr>

        <!-- Example Rejected -->
        <tr>
          <td>ST98765</td>
          <td>Ali Hassan</td>
          <td>Special Consideration</td>
          <td>Exam Resit</td>
          <td>Requesting resit exam opportunity.</td>
          <td class="rejected">Rejected</td>
          <td>
            <button class="action-btn approve-btn" disabled>Approve</button>
            <button class="action-btn reject-btn" disabled>Reject</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
