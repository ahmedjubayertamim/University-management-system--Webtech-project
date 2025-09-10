<?php


$host = "localhost";
$user = "root";
$pass = "";
$dbname = "universitymanagementsystem";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}


$totalBooksResult = $conn->query("SELECT COUNT(*) AS total FROM addbook");
$totalBooksRow = $totalBooksResult->fetch_assoc();
$totalBooks = $totalBooksRow['total'];


$borrowedBooksResult = $conn->query("SELECT COUNT(*) AS borrowed FROM addbook WHERE status='Borrowed'");
$borrowedBooksRow = $borrowedBooksResult->fetch_assoc();
$borrowedBooks = $borrowedBooksRow['borrowed'];


$availableBooks = $totalBooks - $borrowedBooks;

$booksResult = $conn->query("SELECT * FROM addbook ORDER BY id ASC");
?>


<!DOCTYPE HTML>
<html>
<head>
  
  <title>Library Management</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/ManageLibraryStyle.css">

</head>
<body>

<header>
  <h1>University Library Management</h1>
</header>

<div class="sidebar">
  <ul>
    <li><a href="index.php">Dashboard</a></li>
    <li><a href="MangeUser.php">Manage User</a></li>
    <li><a href="ManageCourse.php">Manage Courses</a></li>
    <li><a href="ManageAddDrop.php">Manage Add/Drop Deadline </a></li>
    <li><a href="Manage Accounce.php">Manage Accounce</a></li>
    <li><a href="ManageLibrary.php">Manage Library</a></li>
    <li><a href="ViewAllBooks.php">View All Books</a></li>
    <li><a href="settings.php">Settings</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>

<div class="content">
  <div class="form-container">
    <h2>Add / Share eBook</h2>

    <form action = "../php/AddBooks.php" method = "POST" enctype="multipart/form-data"> 
      <div class="form-group">
        <label for="bname">Book Title</label>
        <input type="text" id="bname" name="bname" placeholder="Enter book title" required>
      </div>

      <div class="form-group">
        <label for="author">Author</label>
        <input type="text" id="author" name="author" placeholder="Enter author's name" required>
      </div>

      <div class="form-group">
        <label for="year">Published Year</label>
        <input type="number" id="year" name="year" placeholder="e.g. 2022" min="1500" max="2099" required>
      </div>

      <div class="form-group">
        <label for="type">Book Type</label>
        <select id="type" name="type">
          <option value="ebook">eBook</option>
          <option value="hardcopy">Hardcopy</option>
        </select>
      </div>

      <div class="form-group">
        <label for="ebook">eBook File (PDF)</label>
        <input type="file" id="ebook" name="ebook" accept=".pdf">
      </div>

      <div class="form-group">
       <label for="status">Status</label>
       <select id="status" name="status" required>
       <option value="Available" selected>Available</option>
       <option value="Borrowed">Borrowed</option>
       </select>
      </div>


      <div class="form-group">
        <label for="desc">Description</label>
        <textarea id="desc" name="desc" placeholder="Write short description..."></textarea>
      </div>

      <button type="submit" class="btn">Add Book</button>
    </form>
  </div>

 
<div class="stats">

  <div class="stat-box">
    <h3>Total Books</h3>
    <p><?php echo $totalBooks; ?></p>
  </div>

  <div class="stat-box">
    <h3>Available</h3>
    <p><?php echo $availableBooks; ?></p>
  </div>

  <div class="stat-box">
    <h3>Borrowed</h3>
    <p><?php echo $borrowedBooks; ?></p>
  </div>

</div>

  <div class="book-list">
    <h2>Library Books</h2>
    <table>

      <tr>
        <th>Book Title</th>
        <th>Author</th>
        <th>Year</th>
        <th>Type</th>
        <th>Borrower ID</th>
        <th>Borrow Duration</th>
        <th>Borrow Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <tr>
        <td>Introduction to AI</td>
        <td>Jdddddh</td>
        <td>2021</td>
        <td>Hardcopy</td>
        <td>ST12345</td>
        <td>14 Days</td>
        <td>2025-08-15</td>
        <td class="status-borrowed">Borrowed</td>
        <td>
          <button class="action-btn edit-btn">Edit</button>
          <button class="action-btn delete-btn">Delete</button>
        </td>

      </tr>

      <tr>
        <td>Database Systems</td>
        <td>Dr. Rrrrrrrr</td>
        <td>2019</td>
        <td>eBook</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
        <td class="status-available">Available</td>
        <td>
          <button class="action-btn edit-btn">Edit</button>
          <button class="action-btn delete-btn">Delete</button>
        </td>

      </tr>
    </table>
  </div>
</div>

</body>
</html>
