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

$areas_query = mysqli_query($conn, "SELECT * FROM parking_area");

// Get selected area from filter, default to the first one found if not set
$selected_area = $_GET['area_id'] ?? null;

$spaces_query = null;
if ($selected_area) {
    $spaces_query = mysqli_query($conn, "SELECT * FROM parking_space WHERE Area_id = '$selected_area'");
}
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
   </style>
</head>
<body>
    <header>
     
    <header>
        <div class="navbar1">
            <a href="../Module1/security_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
    <div class="sidebar">
        <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
        <a class="sidebar2" href="../Module2/security_view.php">View Parking</a>
        <a class="sidebar2" href="../Module1/security_list_vehicles.php">Vehicle Approval</a>
        <a class="sidebar2" href="../Module4/manage-summon.php">Manage Traffic Summon</a>
        <a class="sidebar2" href="../Module4/dashboard.php">Manage Dashboard</a>
        <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
    </div>

    </div>
    </header>
    <div class="container">
       <h2>Live Parking Availability</h2>
    
    <form method="GET">
        <select name="area_id" onchange="this.form.submit()">
            <option value="">-- Select Area --</option>
            <?php while($a = mysqli_fetch_assoc($areas_query)): ?>
                <option value="<?php echo $a['Area_id']; ?>" <?php if($selected_area == $a['Area_id']) echo 'selected'; ?>>
                    <?php echo $a['Area_name']; ?> (<?php echo $a['Category']; ?>)
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <div class="parking-grid" style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 20px;">
        <?php 
        if ($spaces_query):
            while($s = mysqli_fetch_assoc($spaces_query)): 
                $color = ($s['Current_status'] == 'Available') ? '#28a745' : '#dc3545';
        ?>
            <div class="space-card" style="background: <?php echo $color; ?>; color: white; padding: 20px; border-radius: 8px; text-align: center; width: 100px;">
                <strong><?php echo $s['Space_num']; ?></strong><br>
                <small><?php echo $s['Current_status']; ?></small>
                
                
            </div>
        <?php 
            endwhile; 
        else:
            echo "<p>Please select an area to view spaces.</p>";
        endif; 
        ?>
    </div>
    </div>
</body>
</html>
