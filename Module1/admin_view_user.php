<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_list_users.php");
    exit();
}

$user_id = (int)$_GET['id'];
$query = "SELECT user_id, username, full_name, user_type FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: admin_list_users.php");
    exit();
}

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User | FKPark</title>
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
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100%;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .logo {
            width: 100%;
            max-width: 200px;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: #333;
            text-decoration: none;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .sidebar a:hover {
            background: #f9f9f9;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .user-details {
            margin-bottom: 30px;
        }
        .user-details h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .back-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        .back-btn:hover {
            background: #5568d3;
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
        <div class="user-details">
            <h2>User Details</h2>
            <div class="detail-row">
                <div class="detail-label">User ID:</div>
                <div class="detail-value"><?php echo htmlspecialchars($user['user_id']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Username:</div>
                <div class="detail-value"><?php echo htmlspecialchars($user['username']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Full Name:</div>
                <div class="detail-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">User Type:</div>
                <div class="detail-value"><?php echo htmlspecialchars($user['user_type']); ?></div>
            </div>
        </div>
        <a href="admin_list_users.php" class="back-btn">Go Back</a>
    </div>
</body>
</html>