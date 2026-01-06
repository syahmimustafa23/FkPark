

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
$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];
date_default_timezone_set('Asia/Kuala_Lumpur'); // Set to Malaysia time
$areas_query = mysqli_query($conn, "SELECT * FROM parking_area");

// Get selected area from filter, default to the first one found if not set
$selected_area = $_GET['area_id'] ?? null;

$spaces_query = null;
// Top of student_view.php
// Top of student_view.php
if ($selected_area) {
    $today = date('Y-m-d');
    $current_time = date('H:i'); // 24-hour format: e.g., "18:08"

    $sql = "SELECT s.*, 
            (SELECT status FROM parking_usage 
             WHERE Space_id = s.Space_id 
             AND usage_date = '$today' 
             AND status = 'Reserved' 
             -- We format the database time to HH:mm to match PHP's current time
             AND '$current_time' >= DATE_FORMAT(entry_time, '%H:%i') 
             AND '$current_time' < DATE_FORMAT(end_time, '%H:%i')
             LIMIT 1) as active_reservation
            FROM parking_space s 
            WHERE s.Area_id = '$selected_area'";
    
    $spaces_query = mysqli_query($conn, $sql);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
            <a href="../dashboards/student_dashboard.php">Home</a>
            <a href="../Module1/student_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
        </header>
        <div class="sidebar">
            <a href="student_view.php"><img class="logo" src="../photo/logoUmpsa.png"></a>
            <a class="sidebar2" href="student_view.php">View Parking</a>
            <a class="sidebar2" href="../Module 3/view_bookings.php">View Booking</a>
            <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
        </div>
    </div>

    
   
    <div class="container">
       <h2>Live Parking Availability</h2>
    
    <form method="GET" style="margin-bottom: 20px;">
        <select name="area_id" onchange="this.form.submit()">
            <option value="">-- Select Area --</option>
            <?php while($a = mysqli_fetch_assoc($areas_query)): ?>
                <option value="<?php echo $a['Area_id']; ?>" <?php if($selected_area == $a['Area_id']) echo 'selected'; ?>>
                    <?php echo $a['Area_name']; ?> (<?php echo $a['Category']; ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <?php if($selected_area): ?>
            <a href="view_qr_codes.php?area_id=<?php echo $selected_area; ?>" style="margin-left: 10px; padding: 8px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">View QR Codes</a>
        <?php endif; ?>
    </form>

    <!-- Search Box -->
    <?php if($selected_area): ?>
        <div style="margin-bottom: 20px; text-align: center;">
            <input type="text" id="searchInput" placeholder="🔍 Search by space name (e.g., D01, A05)..." 
                   style="padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            <button onclick="clearSearch()" style="margin-left: 10px; padding: 10px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Clear Search</button>
        </div>
    <?php endif; ?>

    <div class="parking-grid" style="display: flex; flex-wrap: wrap; gap: 50px; margin-top: 20px; margin-left: auto;margin-right: auto; justify-content: center;" id="parkingGrid">
    <?php 
    if ($spaces_query):
        while($s = mysqli_fetch_assoc($spaces_query)): 
    // 1. Check for maintenance status FIRST (Gray)
    if ($s['Current_status'] == 'Maintenance') {
        $color = '#6c757d'; 
        $status_label = "Maintenance";
    }
    // 2. Check for physical occupancy (Red)
    elseif ($s['Current_status'] == 'Occupied') {
        $color = '#dc3545'; 
        $status_label = "Occupied";
    } 
    // 3. Check for the LIVE reservation status (Yellow)
    // This will only be 'Reserved' if current time is within the booking window
    elseif ($s['active_reservation'] == 'Reserved') {
        $color = '#ffc107'; 
        $status_label = "Reserved";
    } 
    // 4. Otherwise, it is Available (Green)
    else {
        $color = '#28a745'; 
        $status_label = "Available";
    }
    ?>
        <div class="space-card" data-space-name="<?php echo htmlspecialchars($s['Space_num']); ?>" data-status="<?php echo htmlspecialchars($status_label); ?>" style="background: <?php echo $color; ?>; color: white; padding: 20px; border-radius: 8px; text-align: center; width: 110px;">
        <strong><?php echo $s['Space_num']; ?></strong><br>
            <small><?php echo $status_label; ?></small>
            
            <?php if($_SESSION['role'] == 'student'): ?>
                <?php if($s['Current_status'] == 'Available'): ?>
                    <br><a href="../Module 3/book_parking.php?space_id=<?php echo $s['Space_id']; ?>" style="color: white; font-size: 10px;">Book Now</a>
                <?php endif; ?>
                <a href="../Module 3/scan_qr.php?space_id=<?php echo $s['Space_id']; ?>" 
       style="display: block; background: rgba(255,255,255,0.2); color: white; padding: 5px; border-radius: 4px; text-decoration: none; font-size: 11px; border: 1px solid white;">
     <div style="margin-top: 10px; background: white; padding: 10px; border-radius: 5px;">
    <?php 
    // This is the URL of your live InfinityFree site
    $live_url = "http://" . $_SERVER['HTTP_HOST'] . "/Module 3/scan_qr.php?space_id=" . $s['Space_id'];
    
    // The Google API will now work because your URL is public
    $qr_api = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($live_url) . "&choe=UTF-8";
    ?>
    
    <img src="<?php echo $qr_api; ?>" alt="Scan to Park" style="width: 100px; height: 100px; border: 1px solid #ddd;">
    <p style="color: black; font-size: 10px; margin-top: 5px;">Scan to Occupy</p>
</div>
    </a>

                <br>
                <a href="qr_display.php?space_id=<?php echo $s['Space_id']; ?>" 
                   style="font-size: 10px; color: white; background: rgba(0,0,0,0.5); padding: 4px; text-decoration: none; border-radius: 3px; display: inline-block; margin-top: 5px;">
                   📱 QR Code
                </a>
                
            <?php endif; ?>
        </div>
    <?php 
        endwhile; 
    else:
        echo "<p>Please select an area to view spaces.</p>";
    endif; 
    ?>
</div>
    </div>

    <script>
        // Search function for parking spaces
        document.getElementById('searchInput')?.addEventListener('input', function() {
            searchSpaces(this.value);
        });

        function searchSpaces(searchTerm) {
            const spaceCards = document.querySelectorAll('.space-card');
            let matchCount = 0;

            spaceCards.forEach(card => {
                const spaceName = card.querySelector('strong')?.textContent.toLowerCase() || '';
                
                if (spaceName.includes(searchTerm.toLowerCase())) {
                    card.style.display = 'block';
                    matchCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show message if no results
            if (matchCount === 0 && searchTerm.length > 0) {
                alert('No spaces found matching "' + searchTerm + '"');
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            document.querySelectorAll('.space-card').forEach(card => {
                card.style.display = 'block';
            });
        }
    </script>
     
</body>
</html>











