<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

if (isset($_POST['add_area'])) {
    $name = mysqli_real_escape_string($conn, $_POST['area_name']);
    $cat = $_POST['category'];

    $sql = "INSERT INTO parking_area (Area_name, Category) VALUES ('$name', '$cat')";
    if (mysqli_query($conn, $sql)) {
        $new_area_id = mysqli_insert_id($conn);
        header("Location: admin_generates_spaces.php?area_id=$new_area_id&msg=added");
        exit();
    }
}

$areas = mysqli_query($conn, "SELECT * FROM parking_area");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f9f9f9;
            font-weight: bold;
        }
        table a {
            color: #667eea;
            text-decoration: none;
            margin-right: 10px;
        }
        table a:hover {
            text-decoration: underline;
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        form input, form select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        form button {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        form button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar1">
            <a href="../Module1/admin_view_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
    <div class="sidebar">
        <img class="logo" src="../photo/logoUmpsa.png" alt="Logo">
        <a href="../Module2/admin_list_area.php">Manage Area</a>
        <a href="../Module2/admin_manage_spaces.php">Manage Space</a>
        <a href="../Module2/admin_view.php">Parking Availability</a>
        <a href="../Module 3/admin_parking_report.php">Parking Report</a>
        <a href="../Module1/admin_list_users.php">Manage User</a>
    </div>

    </div>
   
    <div class="container">
        <h2>Manage Parking Areas</h2>
    
    <form method="POST" style="margin-bottom: 30px; border: 1px solid #ccc; padding: 15px;">
        <h3>Add New Area</h3>
        <input type="text" name="area_name" placeholder="e.g. Block A" required>
        <select name="category">
            <option value="Student">Student Area</option>
            <option value="Staff">Staff Area</option>
        </select>
        <button type="submit" name="add_area">Create Area</button>
    </form>

    <table border="1" width="100%">
        <tr>
            <th>Area Name</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($areas)): ?>
        <tr>
            <td><?php echo $row['Area_name']; ?></td>
            <td><?php echo $row['Category']; ?></td>
            <td>
                <a href="admin_edit_area.php?id=<?php echo $row['Area_id']; ?>">Edit</a> |
                <a href="admin_manage_spaces.php?area_id=<?php echo $row['Area_id']; ?>">Manage Spaces</a> |
                <a href="admin_delete_area.php?id=<?php echo $row['Area_id']; ?>" 
                   style="color:red;" 
                   onclick="return confirm('Deleting this area will delete all slots inside it. Proceed?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
     
</body>
</html>




