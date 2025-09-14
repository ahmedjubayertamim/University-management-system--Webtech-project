<!DOCTYPE html>
<html>
<head>
 
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <header>
    <h1>Admin Dashboard</h1>
    <div class="search-box">
    <input type="text" placeholder="Search...">
    <button> Search</button>
  </header>

  <div class="container">
    <aside class="sidebar">
      <ul>
         <li><a href="index.php">Dashboard</a></li>
    <li><a href="MangeUser.php">Manage User</a></li>
    <li><a href="ManageCourse.php">Manage Courses</a></li>
    <li><a href="ManageAddDrop.php">Manage Add/Drop Deadline </a></li>
    <li><a href="Manage Accounce.php">Manage Accounce</a></li>
    <li><a href="ManageLibrary.php">Manage Library</a></li>
    <li><a href="settings.php">Settings</a></li>
    <li><a href="logout.php">Logout</a></li>
      </ul>
    </aside>

    <main class="content">
      <h2>Welcome, Admin!</h2>
      <div class="cards">
        <div class="card">Manage Users</div>
        <div class="card">Finance & Accounting</div>
        <div class="card">Classes</div>
        <div class="card">Subjects</div>
        <div class="card">Routine</div>
        <div class="card">Transport</div>
        <div class="card">Drop/Add Courses</div>
        <div class="card">System Settings</div>
      </div>
    </main>
  </div>
</body>
</html>
