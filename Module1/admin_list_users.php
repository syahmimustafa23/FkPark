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

/* PROFILE MENU */
.profile-menu {
    position: relative;
}

.profile-menu button {
    background: none;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

.profile-menu:focus-within .dropdown {
    display: block;
}

.dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 120%;
    background: white;
    color: black;
    min-width: 200px;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.dropdown div {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.dropdown div:last-child {
    border-bottom: none;
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
    padding: 20px;
    border-radius: 4px;
}

.sidebar a {
    display: block;
    padding: 10px;
    color: black;
    text-decoration: none;
}

.sidebar a:hover {
    background: #555;
    color: white;
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

.btn-add {
    background: #219bff;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
}

.search-btn {
    background: #444;
    color: white;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
}
</style>
</head>

<body>

<header>
    <h2>FKPark Admin Dashboard</h2>

    <div class="profile-menu" tabindex="0">
        <button>
            <?= htmlspecialchars($_SESSION['username']); ?>
        </button>

        <div class="dropdown">
            <div><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username']); ?></div>
            <div><strong>Role:</strong> <?= htmlspecialchars($_SESSION['role']); ?></div>
            <div><strong>User ID:</strong> <?= htmlspecialchars($_SESSION['user_id']); ?></div>
            <div><a href="../logout.php">Logout</a></div>
        </div>
    </div>
</header>

<div class="sidebar">
    <a href="../Module 2/admin_list_area.php">List of Parking</a>
    <a href="../Module 2/admin_view.php">Parking Availability</a>
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
