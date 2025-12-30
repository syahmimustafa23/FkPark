<?php
/**
 * FKPark Student Dashboard (minimal)
 */
require_once '../config.php';
requireLogin();
if ($_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];







// Get Student Data
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$user_id'");
$user = mysqli_fetch_assoc($user_res);

// Get Vehicle & Approval Status
$veh_sql = "SELECT v.*, a.status FROM vehicle v 
            LEFT JOIN approval a ON v.vehicle_id = a.vehicle_id 
            WHERE v.user_id = '$user_id' ORDER BY v.vehicle_id DESC LIMIT 1";
$veh_res = mysqli_query($conn, $veh_sql);
$vehicle = mysqli_fetch_assoc($veh_res);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard  | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #28a745; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
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

.update{
    text-decoration:  none; 
    list-style-type: none;

    background: #219bffff; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; 
}

.delete{
    text-decoration:  none; 
    list-style-type: none;

    background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; 
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
    text-align: center;
}



    </style>
</head>
<body>
    <header>
        
        <div class="navbar1">
         <a href="../dashboards/student_dashboard.php">Home</a>
            <a href="student_profile.php">Profile</a>
                <a href="../logout.php">Logout</a>
    </div>
    </header>
    <div class="sidebar">
         <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
    <a class="sidebar2" href="../Module2/student_view.php">View Parking</a>
    <a class="sidebar2" href="../Module 3/viewBooking.php">View Booking</a>
    <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
 
    </div>

    
   
    <div class="container" style="text-align: center; padding: 70px; ">
        <p>Welcome, <?php echo $user['full_name']; ?></p>
        <br>
    <p>Username: <?php echo $user['username']; ?></p>
    <br>
    <div class="vehicle-status">
    <?php if ($vehicle): ?>
        <p>Vehicle: <?php echo $vehicle['license_plate']; ?> (<?php echo $vehicle['vehicle_model']; ?>)</p>
        <p>Status: <strong><?php echo $vehicle['status'] ?? 'Pending'; ?></strong></p>
        
        <?php if ($vehicle['status'] === 'Rejected'): ?>
            <p style="color:red;">Your registration was rejected. Please <a href="student_register_vehicle.php">register again</a>.</p>
        <?php endif; ?>
        <br>
        <button type="button" class="update" onclick="window.location.href='student_edit_vehicle.php?vehicle_id=<?php echo $vehicle['vehicle_id']; ?>'">Edit Vehicle</button>
    <?php else: ?>
        <p>No vehicle registered. <a href="student_register_vehicle.php">Register Now</a></p>
    <?php endif; ?>
    </div>
    <br>
    <button type="button" class="update" name="update" onclick="window.location.href='student_profile_edit.php?id=<?php echo $user['user_id']; ?>'">Update</button>
     <a class="delete" href="student_delete.php" onclick="return confirm('Are you sure you want to delete your account?')">Delete</a>
</div>


     
</body>
</html>









