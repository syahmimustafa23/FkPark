<?php
/**
 * FKPark Security Dashboard
 * Simple security interface with no animations
 */

require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'security') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? '';
$sql = "SELECT v.*, u.full_name, a.status 
FROM vehicle v 
JOIN users u ON v.user_id = u.user_id 
LEFT JOIN (
    SELECT vehicle_id, status 
    FROM approval 
    WHERE approval_id IN (SELECT MAX(approval_id) FROM approval GROUP BY vehicle_id)
) a ON v.vehicle_id = a.vehicle_id 
WHERE v.license_plate LIKE '%$search%' OR v.vehicle_type LIKE '%$search%';";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #fd7e14; color: white; padding: 20px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .welcome h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #555; }
        .buttons { margin-top: 30px; text-align: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
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
    background-color:black;
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
    text-align: center;}
   </style>
</head>
<body>
    <header>
        
         <div class="navbar1">
            <a href="../Module1/security_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
               
    </div>
    </header>
    <div class="sidebar">
         <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
    <a class="sidebar2" href="../Module2/security_view.php">View Parking</a>
    <a class="sidebar2" href="security_list_vehicles.php">Vehicle Approval</a>
    <a class="sidebar2" href="../Module4/manage-summon.php">Manage Traffic Summon</a>
    <a class="sidebar2" href="../Module4/dashboard.php">Manage Dashboard</a>
    <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
    </div>

    </div>
    </header>
    <div class="container">
        <h2>Vehicle Management</h2>
        <?php if (isset($_GET['msg'])): ?>
            <p style="color: green;">Vehicle <?php echo htmlspecialchars($_GET['msg']); ?> successfully.</p>
        <?php endif; ?>
        <input type="button" value="Add Vehicle" onclick="location.href='student_register_vehicle.php'"/>
        <form method="GET" action="security_list_vehicles.php">
            <input type="text" name="search" placeholder="Search by license plate or vehicle type" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <table border="1" cellpadding="10" cellspacing="0" style="margin-top:20px; width:100%;">
            <tr>
                <th>Vehicle ID</th>
                <th>User Full Name</th>
                <th>License Plate</th>
                <th>Vehicle Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['vehicle_id']; ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['license_plate']); ?></td>
                <td><?php echo htmlspecialchars($row['vehicle_type']); ?></td>
                <td><?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?></td>
                <td><a href="security_view_vehicle.php?id=<?= $row['vehicle_id'] ?>">View Details</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
       
    </div>
</body>
</html>