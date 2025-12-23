<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

// 1. Fetch areas for the dropdown
$areas_query = mysqli_query($conn, "SELECT * FROM parking_area");

$selected_area = $_GET['area_id'] ?? null;
$spaces_query = null;

if ($selected_area) {
    // Fetch all spaces in the selected area
    $sql = "SELECT * FROM parking_space WHERE Area_id = '$selected_area' ORDER BY Space_num";
    $spaces_query = mysqli_query($conn, $sql);
}

// Inside the while loop in admin_view.php

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

.parking-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}

.space-card {
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    width: 120px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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

    <div class="container" style="max-width: 800px;"> <h2>Live Parking Availability</h2>
    
        <form method="GET">
            <select name="area_id" onchange="this.form.submit()">
                <option value="">-- Select Area --</option>
                <?php while($a = mysqli_fetch_assoc($areas_query)): ?>
                    <option value="<?php echo $a['Area_id']; ?>" <?php echo ($selected_area == $a['Area_id']) ? 'selected' : ''; ?>>
                        <?php echo $a['Area_name']; ?> (<?php echo $a['Category']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

            <?php 
            if ($spaces_query && mysqli_num_rows($spaces_query) > 0):
                while($s = mysqli_fetch_assoc($spaces_query)): 
                    // Determine display status and color based on space status
                    $display_status = $s['Current_status'];
                    
                    if ($s['Current_status'] == 'Maintenance') {
                        $color = '#ffc107'; // Yellow for Maintenance
                    } elseif ($s['Current_status'] == 'Available') {
                        $color = '#28a745'; // Green for Available
                    } else {
                        $color = '#dc3545'; // Red for Occupied
                    }
                    
                    $qr_link = $s['Space_qrCode']; 
                    $google_qr_api = "https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=" . urlencode($qr_link);
            ?>
                <div class="space-card" style="background: <?php echo $color; ?>; color: <?php echo ($s['Current_status'] == 'Maintenance') ? 'black' : 'white'; ?>;">
                    <strong><?php echo $s['Space_num']; ?></strong><br>
                    <small><?php echo $display_status; ?></small>
                    <img src="<?php echo $google_qr_api; ?>" alt="QR Code">
                    <a href="admin_update_status.php?id=<?php echo $s['Space_id']; ?>&current=<?php echo $s['Current_status']; ?>&area_id=<?php echo $selected_area; ?>" 
                       style="color: <?php echo ($s['Current_status'] == 'Maintenance') ? 'black' : 'white'; ?>; font-size: 10px; display: block; margin-top: 5px; text-decoration: underline;">
                       <?php echo ($s['Current_status'] == 'Available') ? 'Close Space' : 'Open Space'; ?>
                    </a>
                </div>
            <?php 
                endwhile; 
            elseif ($selected_area):
                echo "<p>No spaces found for this area.</p>";
            else:
                echo "<p>Please select an area to view spaces.</p>";
            endif; 
            ?>
    </div>
</body>
</html>




