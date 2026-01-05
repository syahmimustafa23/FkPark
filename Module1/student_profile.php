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

// Get ALL Vehicles & Approval Status (removed LIMIT 1)
$veh_sql = "SELECT v.*, a.status FROM vehicle v 
            LEFT JOIN approval a ON v.vehicle_id = a.vehicle_id 
            WHERE v.user_id = '$user_id' ORDER BY v.vehicle_id DESC";
$veh_res = mysqli_query($conn, $veh_sql);
$vehicles = array();
while ($row = mysqli_fetch_assoc($veh_res)) {
    $vehicles[] = $row;
}


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

    
   
    <div class="container" style="text-align: left; padding: 20px; ">
        <p><strong>Welcome, <?php echo $user['full_name']; ?></strong></p>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <hr>
        
        <h3>My Vehicles</h3>
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'vehicle_deleted'): ?>
                <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    ✓ Vehicle deleted successfully!
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!empty($vehicles)): ?>
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <thead>
                    <tr style="background: #007bff; color: white; border: 1px solid #0056b3;">
                        <th style="padding: 12px; text-align: left; border: 1px solid #0056b3; font-weight: 600;">License Plate</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #0056b3; font-weight: 600;">Model</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #0056b3; font-weight: 600;">Type</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #0056b3; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #0056b3; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr style="border: 1px solid #ddd; background: white; transition: background 0.2s;">
                            <td style="padding: 12px; border: 1px solid #ddd;"><strong><?php echo htmlspecialchars($vehicle['license_plate']); ?></strong></td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><?php echo htmlspecialchars($vehicle['vehicle_model']); ?></td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><?php echo ucfirst($vehicle['vehicle_type']); ?></td>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                <?php 
                                    $status = $vehicle['status'] ?? 'Pending';
                                    $status_color = ($status === 'Approved') ? '#28a745' : (($status === 'Rejected') ? '#dc3545' : '#ffc107');
                                ?>
                                <span style="color: <?php echo $status_color; ?>; font-weight: 600;"><?php echo $status; ?></span>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                <button type="button" class="update" onclick="window.location.href='student_edit_vehicle.php?vehicle_id=<?php echo $vehicle['vehicle_id']; ?>'" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; transition: background 0.3s; display: block; margin: 0 auto 8px auto;">Edit</button>
                                <button type="button" class="delete" onclick="confirmDeleteVehicle(<?php echo $vehicle['vehicle_id']; ?>)" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; transition: background 0.3s; display: block; margin: 0 auto;">Delete</button>
                            </td>
                        </tr>
                        <?php if (($vehicle['status'] ?? 'Pending') === 'Rejected'): ?>
                            <tr style="background: #fff3cd; border: 1px solid #ffc107;">
                                <td colspan="5" style="padding: 12px; color: #856404; border: 1px solid #ffc107; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 20px;">⚠️</span>
                                    <span>Registration was rejected. Please <strong>edit the vehicle details</strong> and resubmit.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin: 30px 0;">
                <button type="button" onclick="window.location.href='student_register_vehicle.php'" style="background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: 600; transition: background 0.3s; width: 100%;">
                    Register Another Vehicle
                </button>
            </div>
        <?php else: ?>
            <p style="padding: 20px; background: #e8f4f8; border-left: 4px solid #17a2b8; color: #004085;">
                No vehicles registered yet. 
                <a href="student_register_vehicle.php" style="color: #0056b3; text-decoration: none; font-weight: bold;">Register your first vehicle</a>
            </p>
        <?php endif; ?>
    </div>
    <br>
    <div style="margin-left: 240px; display: flex; gap: 10px; padding: 0 20px;">
        <button type="button" onclick="window.location.href='student_profile_edit.php?id=<?php echo $user['user_id']; ?>'" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: background 0.3s;">Update Profile</button>
        <a href="student_delete.php" onclick="return confirmDeleteAccount()" style="background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 600; transition: background 0.3s;">Delete Account</a>
    </div>


    <script>
        // Method 1: confirmDeleteVehicle() - Uses confirm() to ask user before deleting vehicle
        function confirmDeleteVehicle(vehicleId) {
            if (confirm('Are you sure you want to delete this vehicle?')) {
                window.location.href = 'student_delete_vehicle.php?vehicle_id=' + vehicleId;
                return false;
            }
            return false;
        }

        // Method 2: updateProfileSuccess() - Uses alert() to show success message
        function updateProfileSuccess() {
            alert('Profile updated successfully!');
        }

        // Method 3: validateForm() - Uses document.getElementById() to check empty fields
        function validateForm() {
            // Check Full Name field
            const fullName = document.getElementById('fullName');
            if (fullName && fullName.value.trim() === '') {
                alert('Please enter your full name!');
                fullName.focus();
                return false;
            }

            // Check Email field
            const email = document.getElementById('email');
            if (email && email.value.trim() === '') {
                alert('Please enter your email address!');
                email.focus();
                return false;
            }

            // Check Vehicle Model field
            const vehicleModel = document.getElementById('vehicle_model');
            if (vehicleModel && vehicleModel.value.trim() === '') {
                alert('Please enter the vehicle model!');
                vehicleModel.focus();
                return false;
            }

            // Check License Plate field
            const licensePlate = document.getElementById('license_plate');
            if (licensePlate && licensePlate.value.trim() === '') {
                alert('Please enter the license plate!');
                licensePlate.focus();
                return false;
            }

            // Check Grant Document field
            const grantDocument = document.getElementById('grant_document');
            if (grantDocument && grantDocument.value === '') {
                alert('Please upload a grant document!');
                grantDocument.focus();
                return false;
            }

            // All fields are filled - show success message
            alert('Form submitted successfully!');
            return true;
        }
    </script>
     
</body>
</html>