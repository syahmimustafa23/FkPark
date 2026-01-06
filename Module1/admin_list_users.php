<?php
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FKPark</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            padding: 20px;
            margin-left: 240px;
        }
        header { 
            background: #667eea; 
            color: white; 
            padding: 20px 30px; 
            margin-bottom: 30px; 
            border-radius: 4px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .navbar1 { 
            display: flex; 
            gap: 20px;
        }
        .navbar1 a { 
            color: white; 
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .navbar1 a:hover { 
            text-decoration: underline; 
        }
        .sidebar {
            width: 220px;
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
        .sidebar a {
            display: block;
            padding: 12px;
            margin: 5px 0;
            color: black;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .sidebar a:first-child {
            padding: 0;
            margin: 0 0 20px 0;
        }
        .sidebar a:hover {
            background: #667eea;
            color: white;
        }
        .logo {
            width: 100%;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .container {
            max-width: 1200px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 { 
            margin-bottom: 25px; 
            color: #333;
            font-weight: 600;
        }
        .controls-section {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            align-items: center;
            flex-wrap: wrap;
        }
        .controls-section form {
            flex: 1;
            display: flex;
            gap: 10px;
            min-width: 300px;
        }
    </style>
</head>

<body>
    <header>
        <div class="navbar1">
            <a href="admin_view_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <img class="logo" src="../photo/logoUmpsa.png" alt="Logo">
        <a href="../Module2/admin_list_area.php">Manage Area</a>
        <a href="../Module2/admin_manage_spaces.php">Manage Space</a>
        <a href="../Module2/admin_view.php">Parking Availability</a>
        <a href="../Module 3/admin_parking_report.php">Parking Report</a>
        <a href="admin_list_users.php">Manage User</a>
    </div>

    <div class="container">
        <h2>User Management</h2>

        <div class="controls-section">
            <form method="GET" class="flex-grow-1">
                <input type="text" class="form-control" name="search" placeholder="Search by username or name..." value="<?= htmlspecialchars($searchTerm); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
            <a href="admin_register.php" class="btn btn-success">+ Add New User</a>
            <a href="admin_user_statistics.php" class="btn btn-info">📊 Show Report</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']); ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['full_name']); ?></td>
                        <td>
                            <span class="badge bg-info text-dark"><?= htmlspecialchars($row['user_type']); ?></span>
                        </td>
                        <td>
                            <a href="admin_view_user.php?id=<?= $row['user_id']; ?>" class="link-primary small">View</a>
                            <span class="text-muted ms-2 me-2">·</span>
                            <a href="admin_update_user.php?id=<?= $row['user_id']; ?>" class="link-primary small">Update</a>
                            <span class="text-muted ms-2 me-2">·</span>
                            <a href="admin_delete_user.php?id=<?= $row['user_id']; ?>" class="link-danger small" onclick="return confirm('Delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>