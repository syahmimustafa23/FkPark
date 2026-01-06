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

// 1. Fetch areas for the dropdown
$areas_query = mysqli_query($conn, "SELECT * FROM parking_area");

$selected_area = $_GET['area_id'] ?? null;
$spaces_query = null;

if ($selected_area) {
    // We use a Subquery to find the LATEST active booking ID for each space
    $sql = "SELECT s.*, 
            (SELECT Usage_id FROM parking_usage 
             WHERE Space_id = s.Space_id 
             AND status IN ('Reserved', 'Occupied') 
             ORDER BY Usage_id DESC LIMIT 1) as active_booking_id
            FROM parking_space s 
            WHERE s.Area_id = '$selected_area' 
            ORDER BY s.Space_num";
    $spaces_query = mysqli_query($conn, $sql);
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
h2 { 
            margin-bottom: 20px; 
            color: #333; 
        }
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            max-width: 300px;
        }
        .parking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .space-card {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .space-card strong {
            font-size: 18px;
            display: block;
            margin-bottom: 5px;
        }
        .space-card small {
            display: block;
            margin-bottom: 8px;
        }
        .space-card img {
            width: 80px;
            height: 80px;
            margin-bottom: 5px;
        }
        .space-card a {
            font-size: 11px;
            display: block;
            text-decoration: underline;
            cursor: pointer;
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
    
        <form method="GET" style="margin-bottom: 20px;">
            <select name="area_id" onchange="this.form.submit()">
                <option value="">-- Select Area --</option>
                <?php while($a = mysqli_fetch_assoc($areas_query)): ?>
                    <option value="<?php echo $a['Area_id']; ?>" <?php echo ($selected_area == $a['Area_id']) ? 'selected' : ''; ?>>
                        <?php echo $a['Area_name']; ?> (<?php echo $a['Category']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
            <?php if($selected_area): ?>
                <a href="view_qr_codes.php?area_id=<?php echo $selected_area; ?>" style="margin-left: 10px; padding: 8px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">View QR Codes</a>
            <?php endif; ?>
        </form>

        <?php if($selected_area): ?>
            <div style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center;">
                <input type="text" id="searchInput" placeholder="🔍 Search by space number or status..." 
                       style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; flex: 1; max-width: 400px;">
                <button onclick="clearSearch()" style="padding: 8px 15px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer;">Clear Search</button>
            </div>
        <?php endif; ?>

        <?php if ($selected_area): ?>
            <div class="parking-grid">
                <?php 
                if ($spaces_query && mysqli_num_rows($spaces_query) > 0):
                   while($s = mysqli_fetch_assoc($spaces_query)): 
    // Define text color globally for the cards
    $text_color = '#ffffff'; 

    if ($s['Current_status'] == 'Available') {
        $color = '#28a745'; // Green
        $status_label = "Available";
    } elseif ($s['Current_status'] == '') {
        $color = '#ffc107'; // Yellow
        $status_label = "Reserved";
    } elseif ($s['Current_status'] == 'Occupied') {
        $color = '#dc3545'; // Red
        $status_label = "Occupied";
    } else {
        $color = '#6c757d'; // Grey for Maintenance
        $status_label = "Maintenance";
    }

    // Determine what the QR code should show
    if ($s['active_booking_id']) {
        // Points to the occupant's ticket for Admin verification
        $qr_link = "http://localhost/fkpark/Module 3/view_ticket.php?id=" . $s['active_booking_id'];
    } else {
        // Points to the check-in page for physical printing
        $qr_link = $s['Space_qrCode']; 
    }
    $google_qr_api = "https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=" . urlencode($qr_link);
?>
                        

                    <div class="space-card" data-space-name="<?php echo htmlspecialchars($s['Space_num']); ?>" data-status="<?php echo htmlspecialchars($status_label); ?>" style="background: <?php echo $color; ?>; color: <?php echo $text_color; ?>;">
                        <strong><?php echo $s['Space_num']; ?></strong>
                        <small><?php echo $status_label; ?></small>
                        <img src="<?php echo $google_qr_api; ?>" alt="QR Code">
                        <?php if ($s['active_booking_id']): ?>
    <a href="../Module 3/view_ticket.php?id=<?php echo $s['active_booking_id']; ?>" 
       style="color: white; font-weight: bold;">View Ticket</a>
<?php endif; ?>
                        <a href="admin_update_status.php?id=<?php echo $s['Space_id']; ?>&current=<?php echo $s['Current_status']; ?>&area_id=<?php echo $selected_area; ?>" 
                           style="color: <?php echo $text_color; ?>;">
                           <?php echo ($s['Current_status'] == 'Available') ? 'Close Space' : 'Open Space'; ?>
                        </a>
                    </div>
                <?php 
                    endwhile;
                else:
                    echo "<p>Please select an area to view spaces.</p>";
                endif; 
                ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-top: 20px; color: #666;">Please select an area to view spaces.</p>
        <?php endif; ?>
    </div>

    <script>
        function searchSpaces(searchTerm) {
            const spaces = document.querySelectorAll('.space-card');
            let foundCount = 0;

            spaces.forEach(space => {
                const spaceName = space.getAttribute('data-space-name').toLowerCase();
                const spaceStatus = space.getAttribute('data-status').toLowerCase();
                
                if (spaceName.includes(searchTerm.toLowerCase()) || spaceStatus.includes(searchTerm.toLowerCase())) {
                    space.style.display = 'block';
                    foundCount++;
                } else {
                    space.style.display = 'none';
                }
            });

            if (foundCount === 0 && searchTerm.length > 0) {
                alert('No parking spaces found matching your search.');
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            const spaces = document.querySelectorAll('.space-card');
            spaces.forEach(space => {
                space.style.display = 'block';
            });
        }

        // Add real-time search
        if (document.getElementById('searchInput')) {
            document.getElementById('searchInput').addEventListener('keyup', function() {
                searchSpaces(this.value);
            });
        }
    </script>
</body>
</html>
