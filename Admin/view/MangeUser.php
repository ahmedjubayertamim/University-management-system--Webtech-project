<?php
include "../PHP/config.php"; 
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE HTML>
<html>
   <head>
     <title>Manage Users - Admin</title>
     <link rel="stylesheet" href="../css/style.css">
     <link rel="stylesheet" href="../css/ManageUser.css">
      <link rel="stylesheet" href="../css/ManageUserStyle.css">
    </head>

    <body>
      <header>
        <center>
          <h1>University Management System - Admin</h1>
        </center>
      </header>

      <div class="container">
          <aside class="sidebar">
              <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="MangeUser.php" class="active">Manage User</a></li>
                <li><a href="ManageCourse.php">Manage Courses</a></li>
                <li><a href="ManageAddDrop.php">Manage Add/Drop Deadline</a></li>
                <li><a href="ManageAccounce.php">Manage Accounce</a></li>
                <li><a href="ManageLibrary.php">Manage Library</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
              </ul>
            </aside>

            <main class="content">
               <h2>Manage Users</h2>
           
               <a href="RegistrationPage.php">
                <button id="addUserBtn">+ Add User</button></a><br><br>

              <div id = "SearchRole">
                 <input type="text" id="searchInput" placeholder="Search by name or email..." >
    
                 <select id="roleFilter">
                   <option value="">All Roles</option>
                   <option value="student">Students</option>
                   <option value="teacher">Teachers</option>
                   <option value="admin">Admins</option>
                 </select>
            
                </div>

        
                <table id="userTable">
                  <thead>
                      <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                 <tbody>

                    <?php 
                    while ($row = $result->fetch_assoc()): 
                    ?>

                        <tr id="row-<?= $row['id'] ?>">
                            <td>U<?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['first_name'] ?? '') . " " . htmlspecialchars($row['last_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['role'] ?? 'not set') ?></td>
                            <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                            <td>
                                <select class="status" data-id="<?= $row['id'] ?>" data-role="<?= $row['role'] ?>">
                                    <option value="enabled" <?= ($row['status'] ?? '') == "enabled" ? "selected" : "" ?>>Enabled</option>
                                    <option value="disabled" <?= ($row['status'] ?? '') == "disabled" ? "selected" : "" ?>>Disabled</option>
                                </select>
                            </td>
                            <td class="actions">
                                 <button class="edit-btn" onclick="editUser(<?= $row['id'] ?>)">Edit</button>
                                 <button class="delete-btn" onclick="deleteUser(<?= $row['id'] ?>)">Delete</button>
                             </td>

                            
                        </tr>

                    <?php 
                      endwhile; 
                    ?>

                        <tr id="noResults">
                            <td colspan="6" style="text-align:center; color:red;">No matching users found</td>
                        </tr>

                  </tbody>
                </table>
            </main>
        </div>

        <div id="editModal">
            <div id = "Table">
             <h3>Edit User</h3>
              <form method="POST" action="../PHP/edit_user.php">
                 <input type="hidden" name="id" id="edit_id">

                 <label>First Name :</label>
                  <input type="text" name="first_name" id="edit_fname" required><br><br>

                  <label>Last Name :</label>
                  <input type="text" name="last_name" id="edit_lname" required><br><br>

                 <label>Role:</label>
                  <select name="role" id="edit_role">
                     <option value="student">Student</option>
                     <option value="teacher">Teacher</option>
                     <option value="admin">Admin</option>
                  </select><br><br>

                  <label>Status:</label>
                  <select name="status" id="edit_status">
                      <option value="enabled">Enabled</option>
                      <option value="disabled">Disabled</option>
                 </select>

                 <br><br>
                 <button type="submit" id = "save">Save</button>
                 <button type="button" id = "cancle" onclick="closeModal()">Cancel</button>
              </form>
         </div>
     </div>

       <script>

           document.querySelectorAll(".status").forEach(select => {
             select.addEventListener("change", async function() {
              const user_id = this.dataset.id;
              const role = this.dataset.role; 
              const status = this.value;

              const formData = new FormData();
              formData.append("user_id", user_id);
              formData.append("role", role);
              formData.append("status", status);

              const response = await fetch("../PHP/update_status.php", {
              method: "POST",
              body: formData});

              const result = await response.text();
              alert(result);
              });
            });


           async function deleteUser(user_id) {
             if(confirm("Are you sure you want to delete this user?")) {
               const formData = new FormData();
               formData.append("user_id", user_id);

               const response = await fetch("../PHP/delete_user.php", {
                method: "POST",
                body: formData});
       
                const result = await response.text();
                alert(result);
                document.getElementById("row-" + user_id).remove();
              }
            }



          async function editUser(user_id) {
             const response = await fetch("../PHP/get_user.php?id=" + user_id);
             const user = await response.json();

              if (user.error) {
                 alert(user.error);
                 return;
                }

              document.getElementById("edit_id").value = user.id;
              document.getElementById("edit_fname").value = user.first_name;
              document.getElementById("edit_lname").value = user.last_name;
              document.getElementById("edit_role").value = user.role;
              document.getElementById("edit_status").value = user.status;
              document.getElementById("editModal").style.display = "flex";
            }

            function closeModal() {
               document.getElementById("editModal").style.display = "none";
            }

            document.getElementById("searchInput").addEventListener("keyup", function() {
              const filter = this.value.toLowerCase();
              const rows = document.querySelectorAll("#userTable tbody tr");

              rows.forEach(row => {
              const name = row.cells[1].innerText.toLowerCase();
              const email = row.cells[3].innerText.toLowerCase();
              row.style.display = (name.includes(filter) || email.includes(filter)) ? "" : "none";
            });
            });


          document.getElementById("roleFilter").addEventListener("change", function() {
              const filterRole = this.value.toLowerCase();
              const rows = document.querySelectorAll("#userTable tbody tr");

              rows.forEach(row => {
              const role = row.cells[2].innerText.toLowerCase();
              row.style.display = (filterRole === "" || role === filterRole) ? "" : "none";
             });
            });



           function filterTable() {
              const searchValue = document.getElementById("searchInput").value.toLowerCase();
              const roleFilter = document.getElementById("roleFilter").value.toLowerCase();
              const rows = document.querySelectorAll("#userTable tbody tr:not(#noResults)");
              let anyVisible = false;

              rows.forEach(row => {
              const name = row.cells[1].innerText.toLowerCase();
              const email = row.cells[3].innerText.toLowerCase();
              const role = row.cells[2].innerText.toLowerCase();

              const matchesSearch = name.includes(searchValue) || email.includes(searchValue);
              const matchesRole = roleFilter === "" || role === roleFilter;

              if (matchesSearch && matchesRole) {
                  row.style.display = "";
                  anyVisible = true;
                } else {
                   row.style.display = "none";
                }
              });
               document.getElementById("noResults").style.display = anyVisible ? "none" : "";
            }

            document.getElementById("searchBtn").addEventListener("click", filterTable);

            document.getElementById("roleFilter").addEventListener("change", filterTable);

            document.getElementById("searchInput").addEventListener("keypress", function(e) {
             if (e.key === "Enter") filterTable();
            });

            document.getElementById("searchInput").addEventListener("keyup", filterTable);

        </script>
   </body>
</html>
