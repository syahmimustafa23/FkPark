<?php
/**
 * FKPark Admin Dashboard
 * 
 * Simple dashboard for admin users
 * - No JavaScript, no animations, no hover effects
 * - Session-based access control
 */

require_once '../config.php';

// Check if user is logged in
requireLogin();

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Process the Update
if (isset($_POST['update_area'])) {
    $name = mysqli_real_escape_string($conn, $_POST['area_name']);
    $cat = $_POST['category'];

    $sql = "UPDATE parking_area SET Area_name='$name', Category='$cat' WHERE Area_id='$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_list_area.php?msg=updated");
        exit();
    }
}

// Fetch current details
$result = mysqli_query($conn, "SELECT * FROM parking_area WHERE Area_id = '$id'");
$area = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #667eea; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .welcome h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #555; }
        .buttons { margin-top: 30px; text-align: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
        img{width: 100px; }
        .navbar1{text-decoration-line: none;text-decoration: none; float: right; overflow: hidden; list-style-type: none; display: flex; text-align: center; padding: 0px; margin:0px;}
        .navbar1 a{ 
            display: block;
  color: black;
  padding: 0px 20px;
  text-decoration: none;
text-align: center;
}

.navbar1 a:hover{
    background-color: #555555;
  color: white;
}
  .sidebar {
    height: 100%;               /* full height */
    width: 250px;               /* sidebar width */
    width: 200px;               /* sidebar width */
    position: fixed;            /* stick to left */
    top: 20px;
    left: 20px;
    background-color: #7fffd6;
    padding-top: 20px;
    background-color: #FFFFFF;
    
    display: flex;
    flex-direction: column;
}

.sidebar a {
    padding: 15px 25px;
    padding:0px 0px;
    text-decoration: none;
    font-size: 18px;
    color: black;
    display: block;
}

.sidebar a:hover{
    background-color: #555555;
  color: white;
}

a.sidebar2{
    padding: 15px 20px;
}

.logo{
    width: 200px;
    height: auto;
}
td{
    padding: 10px;
    text-align: center;
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
        <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
        <a class="sidebar2" href="../Module2/admin_list_area.php">List of Parking</a>
        <a class="sidebar2" href="../Module2/admin_view.php">Parking Availability</a>
        <a class="sidebar2" href="../Module 3/parkingReport.html">Parking Report</a>
        <a class="sidebar2" href="../Module1/admin_list_users.php">Manage User</a>
    </div>

    </div>
   
    <div class="container">
       <h2>Edit Parking Area</h2><br>
    <form method="POST">
            <label>Area Name:</label>
            <input type="text" name="area_name" value="<?php echo $area['Area_name']; ?>" required>

            <label>Category:</label>
            <select name="category">
                <option value="Student" <?php if($area['Category'] == 'Student') echo 'selected'; ?>>Student Area</option>
                <option value="Staff" <?php if($area['Category'] == 'Staff') echo 'selected'; ?>>Staff Area</option>
            </select>

            <button type="submit" name="update_area">Save Changes</button>
            <a href="admin_list_area.php">Cancel</a>
        </form>
    </div>
     
</body>
</html>




