<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM users WHERE user_id = '$id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) { die("User not found."); }

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $uname = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['user_type'];

    $update_sql = "UPDATE users SET full_name='$name', username='$uname', user_type='$role' WHERE user_id='$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: admin_list_users.php?msg=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FKPark</title>
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
            max-width: 900px;
            background: white;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h2 { 
            margin-bottom: 20px; 
            color: #333; 
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        form input, form select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            max-width: 400px;
            margin-bottom: 15px;
            display: block;
        }
        .buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .save {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .save:hover {
            background: #218838;
        }
        .cancel {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .cancel:hover {
            background: #c82333;
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
        <h2>Update User</h2>
        <form method="POST" action="admin_update_user.php?id=<?= $id; ?>">
    <a class="sidebar2" href="../Module2/admin_list_area.php">Manage Area</a>
    <a class="sidebar2" href="../Module2/admin_manage_spaces.php">Manage Space</a>
    <a class="sidebar2" href="../Module2/admin_view.php">Parking Availability</a>
    <a class="sidebar2" href="../Module 3/admin_parking_report.php">Parking Report</a>
    <a class="sidebar2" href="admin_list_users.php">Manage User</a>
    </div>

    </div>
   
    <div class="container">
      <form method="POST">
    <h2>Update User Profile</h2><br>
    <label>Full Name:</label><br>
    <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required><br><br>

    <label>Username:</label><br>
    <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br><br>

    <label>Role:</label><br>
    <select name="user_type">
        <option value="Admin" <?php if($user['user_type'] == 'Admin') echo 'selected'; ?>>Admin</option>
        <option value="Student" <?php if($user['user_type'] == 'Student') echo 'selected'; ?>>Student</option>
        <option value="Safety_Staff" <?php if($user['user_type'] == 'Safety_Staff') echo 'selected'; ?>>Safety Staff</option>
    </select><br><br>

    <button class="save" type="submit" name="update">Save Changes</button>
    <a href="admin_list_users.php" class="cancel">Cancel</a>
</form>

       
    </div>
     
</body>
</html>




