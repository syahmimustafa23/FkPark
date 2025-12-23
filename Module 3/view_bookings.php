<?php
require_once '../config.php';

$uid = $_SESSION['user_id'];

// Fetch all bookings for the logged-in student
$sql = "SELECT u.*, s.Space_num, a.Area_name 
        FROM parking_usage u
        JOIN parking_space s ON u.Space_id = s.Space_id
        JOIN parking_area a ON s.Area_id = a.Area_id
        WHERE u.user_id = '$uid' 
        AND u.status != 'Cancelled' 
        ORDER BY u.usage_date DESC";
$result = mysqli_query($conn, $sql);
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
         
            <a href="../Module1/student/student_profile.php">Profile</a>
                <a href="../logout.php">Logout</a>
    </div>
    </header>
    <div class="sidebar">
         <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
    <a class="sidebar2" href="../Module 2/student_view.php">View Parking</a>
    <a class="sidebar2" href="../Module 3/viewBooking.php">View Booking</a>
 
    <a class="sidebar2" href="sidebar2">Traffic Summons</a>
 
    </div>

    </div>
   
    <div class="container" style="max-width: 800px; margin: auto;">
    <h2>My Parking Bookings</h2>
    <br>
    <table width="100%">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time (Start - End)</th>
                <th>Space</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['usage_date'])); ?></td>
                        <td>
                            <?php 
                                $start = date('H:i', strtotime($row['entry_time']));
                                $end = date('H:i', strtotime($row['end_time']));
                                echo "$start - $end";
                            ?>
                        </td>
                        <td><strong><?php echo $row['Area_name'] . " (" . $row['Space_num'] . ")"; ?></strong></td>
                        <td><span class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                        <td class="action-links">
                            <?php if($row['status'] == 'Reserved'): ?>
                                <a href="view_ticket.php?id=<?php echo $row['Usage_id']; ?>">View QR</a> |
                                <a href="edit_bookings.php?id=<?php echo $row['Usage_id']; ?>">Update</a> |
                                <a href="cancel_booking.php?id=<?php echo $row['Usage_id']; ?>" 
                                   style="color:red;" 
                                   onclick="return confirm('Confirm permanent deletion of this booking?')">Delete</a>
                            <?php elseif($row['status'] == 'Occupied'): ?>
                                <small>Currently Parked</small>
                            <?php else: ?>
                                <small>No actions</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">You have no active booking records.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
     
</body>
</html>











