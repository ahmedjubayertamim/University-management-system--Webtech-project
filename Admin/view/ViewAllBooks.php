<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "universitymanagementsystem";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$searchQuery = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = $conn->real_escape_string($_GET['search']);
    $searchQuery = "WHERE bookname LIKE '%$searchTerm%' 
                    OR author LIKE '%$searchTerm%' 
                    OR type LIKE '%$searchTerm%' 
                    OR status LIKE '%$searchTerm%'";
}


$sql = "SELECT * FROM addbook $searchQuery ORDER BY id ASC";
$booksResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Library Books</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/ManageLibraryStyle.css">
    <link rel="stylesheet" href="../css/viewAllBookStyle.css">

</head>

<body>

  <div class="container">

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
        <h1>All Library Books</h1>

        <div class="search-bar">

            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by title and author name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit">Search</button>
            </form>

        </div>

        <table>
            <tr>
                <th>ID</th><th>Title</th><th>Author</th><th>Year</th>
                <th>Type</th><th>Status</th><th>eBook</th><th>Description</th>
            </tr>

            
         <?php
            if($booksResult->num_rows > 0){
                while($row = $booksResult->fetch_assoc()){
                    $statusClass = $row['status'] == 'Available' ? 'status-available' : 'status-borrowed';
                    $ebookLink = $row['ebook'] ? "<a href='../php/{$row['ebook']}' target='_blank'>Download</a>" : "-";
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['bookname']}</td>
                        <td>{$row['author']}</td>
                        <td>{$row['pubYear']}</td>
                        <td>{$row['type']}</td>
                        <td class='{$statusClass}'>{$row['status']}</td>
                        <td>{$ebookLink}</td>
                        <td>{$row['descText']}</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='text-align:center;'>No books found</td></tr>";
            }
         ?>
        </table>
    </div>

 </div>

  <?php 
    $conn->close(); 
  ?>
</body>
</html>
