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
if ($_SESSION['role'] !== 'security') {
    header("Location: ../login.php");
    exit();
}

// $username = htmlspecialchars($_SESSION['username']);

// $user_id = $_SESSION['user_id'];

// handle_search.php

// manage_vehicles.php
$veh_id = $_GET['id'];

// SQL to get vehicle details and the student's name 
$sql = "SELECT v.*, u.full_name FROM vehicle v 
        JOIN users u ON v.user_id = u.user_id 
        WHERE v.vehicle_id = '$veh_id'";
$result = mysqli_query($conn, $sql);
$vehicle = mysqli_fetch_assoc($result);

if (!$vehicle) {
    die("Vehicle not found.");
}
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
        header { background:  #fd7e14; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
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
            <a href="../Module1/security_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
               
    </div>
    </header>
    <div class="sidebar">
         <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
    <a class="sidebar2" href="../Module 2/security_view.php">View Parking</a>
    <a class="sidebar2" href="security_list_vehicles.php">Vehicle Approval</a>
     <a class="sidebar2" href="sidebar2">Traffic Summons</a>
    </div>

    
   
    <div class="container">
        <h2>Vehicle Registration Details</h2>
    <p><strong>Owner:</strong> <?php echo $vehicle['full_name']; ?></p>
    <p><strong>Plate Number:</strong> <?php echo $vehicle['license_plate']; ?></p>
    <p><strong>Model:</strong> <?php echo $vehicle['vehicle_model']; ?></p>
    <p><strong>Type:</strong> <?php echo $vehicle['vehicle_type']; ?></p>
    
    <p><strong>Grant Document:</strong> 
        <a href="<?php echo $vehicle['grant_document']; ?>" target="_blank">View Uploaded Grant</a>
    </p>

    <hr>
    <h3>Action</h3>
    <form action="security_process_approval.php" method="POST">
        <input type="hidden" name="vehicle_id" value="<?php echo $veh_id; ?>">
        
        <button type="submit" name="decision" value="Approved" style="background-color: green; color: white;">
            Approve Vehicle
        </button>
        
        <button type="submit" name="decision" value="Rejected" style="background-color: red; color: white;">
            Reject Vehicle
        </button>
    </form>
    
    <br>
    <button onclick="location.href='security_list_vehicles.php'">Back to List</button>

       

       
    </div>
     
</body>
</html>



























