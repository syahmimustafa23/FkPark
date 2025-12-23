<?php
/**
 * FKPark Admin Dashboard - User Management
 * Click-based profile dropdown
 * Session-based (NO database queries for profile)
 */

require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$searchTerm = $_GET['search'] ?? '';
$query = "SELECT user_id, username, full_name, user_type 
          FROM users 
          WHERE username LIKE '%$searchTerm%' 
          OR full_name LIKE '%$searchTerm%'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin | User Management</title>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; background: #f5f5f5; }

/* HEADER */
header {
    background: #667eea;
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 4px;
}

.navbar1{text-decoration-line: none;text-decoration: none; float: right; overflow: hidden; list-style-type: none; display: flex; text-align: center; padding: 0px; margin:0px;}
.navbar1 a{ 
    display: block;
  color: black;
  padding: 0px 20px;
  text-decoration: none;
text-align: center;
}

.navbar1 a:hover{
    background-color: black;
  color: white;
}

.dropdown a {
    text-decoration: none;
    color: red;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 100px;
    left: 20px;
    width: 200px;
    background: white;
    padding: 0;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #ddd;
}

.sidebar a {
    display: block;
    padding: 15px;
    color: black;
    text-decoration: none;
    border-bottom: 1px solid #eee;
}

.sidebar a:last-child {
    border-bottom: none;
}

.sidebar a:hover {
    background: #667eea;
    color: white;
}

.logo {
    width: 100%;
    height: auto;
    display: block;
    padding: 0;
    margin: 0;
    border-radius: 4px 4px 0 0;
    border-bottom: 2px solid #667eea;
}

/* CONTENT */
.container {
    margin-left: 250px;
    margin-top: 30px;
    background: white;
    padding: 30px;
    border-radius: 4px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}

th {
    background-color: #667eea;
    color: white;
}

.btn-add {
    background: #219bff;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
    margin-bottom: 15px;
    display: inline-block;
}

.btn-add:hover {
    background: #0078d4;
}

.search-btn {
    background: #444;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.search-btn:hover {
    background: #222;
}

input[type="text"] {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
</style>
</head>

<body>

<header>
    <h2></h2>

    <div class="navbar1">
        <a href="admin_view_profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
    </div>
</header>

<div class="sidebar">
    <a href="#home"><img class="logo" src="../photo/logoUmpsa.png" alt="University Logo"></a>
    <a href="../Module2/admin_list_area.php">List of Parking</a>
    <a href="../Module2/admin_view.php">Parking Availability</a>
    <a href="../Module 3/parkingReport.html">Parking Report</a>
    <a href="admin_list_users.php">Manage User</a>
</div>

<div class="container">
    <h2>User Management</h2><br>

    <form method="GET">
        <input type="text" name="search" placeholder="Search by username or name"
               value="<?= htmlspecialchars($searchTerm); ?>">
        <button class="search-btn" type="submit">Search</button>
    </form><br>

    <a href="admin_register.php" class="btn-add">+ Add New User</a>

    <table>
        <tr>
            <th>ID</th><th>Username</th><th>Full Name</th><th>Type</th><th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_id']); ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['full_name']); ?></td>
            <td><?= htmlspecialchars($row['user_type']); ?></td>
            <td>
                <a href="admin_update_user.php?id=<?= $row['user_id']; ?>">Update</a> |
                <a href="admin_delete_user.php?id=<?= $row['user_id']; ?>"
                   onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
