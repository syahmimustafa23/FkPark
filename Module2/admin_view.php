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

// Inside the while loop in admin_view.php

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            padding: 20px;
            margin-left: 240px;
        }
        header { 
            background: #667eea; 
            color: white; 
            padding: 20px 30px; 
            margin-bottom: 30px; 
            border-radius: 4px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .navbar1 { 
            display: flex; 
            gap: 20px;
        }
        .navbar1 a { 
            color: white; 
            text-decoration: none;
            cursor: pointer;
        }
        .navbar1 a:hover { 
            text-decoration: underline; 
        }
        .sidebar {
            width: 220px;
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
        .sidebar a {
            display: block;
            padding: 12px;
            margin: 5px 0;
            color: black;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .sidebar a:hover {
            background: #667eea;
            color: white;
        }
        .logo {
            width: 100%;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .container {
            max-width: 1200px;
            background: white;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
        <div class="navbar1">
            <a href="../Module1/admin_view_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
    <div class="sidebar">
        <img class="logo" src="../photo/logoUmpsa.png" alt="Logo">
        <a href="../Module2/admin_list_area.php">Manage Area</a>
        <a href="../Module2/admin_manage_spaces.php">Manage Space</a>
        <a href="../Module2/admin_view.php">Parking Availability</a>
        <a href="../Module 3/admin_parking_report.php">Parking Report</a>
        <a href="../Module1/admin_list_users.php">Manage User</a>
    </div>

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
                        

                    <div class="space-card" style="background: <?php echo $color; ?>; color: <?php echo $text_color; ?>;">
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
</body>
</html>




