<?php
require_once '../config.php';


$usage_id = mysqli_real_escape_string($conn, $_GET['id']);
$uid = $_SESSION['user_id'];

// Fetch the existing booking details
$sql = "SELECT u.*, s.Space_num, a.Area_name, s.Area_id 
        FROM parking_usage u
        JOIN parking_space s ON u.Space_id = s.Space_id
        JOIN parking_area a ON s.Area_id = a.Area_id
        WHERE u.Usage_id = '$usage_id' AND u.user_id = '$uid'";
$res = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($res);

if (!$booking || $booking['status'] !== 'Reserved') {
    die("Only pending reservations can be edited.");
}

if (isset($_POST['update_booking'])) {
    $new_date = $_POST['usage_date'];
    $new_start = $_POST['start_time']; 
    $new_end = $_POST['end_time'];
    $current_space_id = $booking['Space_id'];

    // CHECK FOR CLASH: Is the CURRENT space busy during the NEW requested time?
    // We check if any OTHER booking overlaps with this new time slot
    $check_clash = "SELECT * FROM parking_usage 
                    WHERE Space_id = '$current_space_id' 
                    AND usage_date = '$new_date' 
                    AND status IN ('Reserved', 'Occupied')
                    AND Usage_id != '$usage_id' 
                    AND (
                        ('$new_start' < end_time AND '$new_end' > TIME(entry_time))
                    )";
    
    $clash_res = mysqli_query($conn, $check_clash);

    if (mysqli_num_rows($clash_res) == 0) {
        // No clash found, proceed with update
        $entry_datetime = $new_date . ' ' . $new_start;
        
        $update_sql = "UPDATE parking_usage SET 
                       usage_date = '$new_date', 
                       entry_time = '$entry_datetime', 
                       end_time = '$new_end' 
                       WHERE Usage_id = '$usage_id'";
        
        if (mysqli_query($conn, $update_sql)) {
            header("Location: view_bookings.php?msg=updated");
            exit();
        }
    } else {
        $error = "Sorry, this spot is already reserved by someone else during that specific time.";
    }
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
         
            <a href="../Module1/student_profile.php">Profile</a>
                <a href="../logout.php">Logout</a>
    </div>
    </header>
    <div class="sidebar">
         <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
    <a class="sidebar2" href="../Module 2/student_view.php">View Parking</a>
    <a class="sidebar2" href="../Module 3/view_bookings.php">View Booking</a>

    <a class="sidebar2" href="../Module4/view-status.php">Traffic Summons</a>
 
    </div>

    </div>
   
    <div class="container" style="max-width: 500px; margin: auto; background: white; padding: 20px;">
    <h2>Update Booking #<?php echo $usage_id; ?></h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form method="POST">
        <p>Current Space: <strong><?php echo $booking['Area_name'] . " - " . $booking['Space_num']; ?></strong></p>
        <br>
        <label>New Date:</label><br>
        <input type="date" name="usage_date" value="<?php echo $booking['usage_date']; ?>" min="<?php echo date('Y-m-d'); ?>" required style="width:100%; padding:8px;"><br><br>
        
        <label>New Start Time:</label><br>
        <?php $current_start = date("H:i", strtotime($booking['entry_time'])); ?>
        <input type="time" name="start_time" value="<?php echo $current_start; ?>" required style="width:100%; padding:8px;"><br><br>

        <label>New End Time:</label><br>
        <input type="time" name="end_time" value="<?php echo $booking['end_time']; ?>" required style="width:100%; padding:8px;"><br><br>
        
        <button type="submit" name="update_booking" style="background:#28a745; color:white; padding:10px 20px; border:none; cursor:pointer; border-radius:4px;">Save Changes</button>
        <a href="view_bookings.php" style="margin-left: 10px; color: #666; text-decoration:none;">Cancel</a>
    </form>
</div>
     
</body>
</html>











