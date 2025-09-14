<!DOCTYPE html>
<html>
<head>
  
  <title>Approve Student Requests</title>
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../ApproveStudentRequestStyle.css">

</head>
<body>

<header>
  <h1>Approve Student Requests</h1>
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
