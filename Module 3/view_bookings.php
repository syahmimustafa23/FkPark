<?php
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$uid = $_SESSION['user_id'];

// --- SEARCH LOGIC ---
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

// Fetch bookings with optional search filter
$sql = "SELECT u.*, s.Space_num, a.Area_name 
        FROM parking_usage u
        JOIN parking_space s ON u.Space_id = s.Space_id
        JOIN parking_area a ON s.Area_id = a.Area_id
        WHERE u.user_id = '$uid' 
        AND u.status != 'Cancelled'";

// If there is a search term, filter by Area Name or Space Number
if (!empty($search_query)) {
    $sql .= " AND (a.Area_name LIKE '%$search_query%' OR s.Space_num LIKE '%$search_query%')";
}

$sql .= " ORDER BY u.usage_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Student Dashboard | FKPark</title>
    <style>
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #28a745; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .sidebar { height: 100%; width: 200px; position: fixed; top: 20px; left: 20px; background-color: #FFFFFF; display: flex; flex-direction: column; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 18px; color: black; display: block; }
        .sidebar a:hover{ background-color:black; color: white; }
        .logo{ width: 200px; height: auto; }
        td{ padding: 10px; text-align: center; }
      
        .search-container { margin-bottom: 20px; display: flex; gap: 10px; }
        .search-input { padding: 8px; width: 70%; border: 1px solid #ccc; border-radius: 4px; }
        .search-btn { padding: 8px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .clear-btn { padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; font-size: 13px; }
        table {
    margin-left: auto;
    margin-right: auto;
    width: 80%; 
}
   </style>
</head>
<body>
    <header>
        <div class="navbar1" style="float: right;">
            <a href="../dashboards/student_dashboard.php">Home</a>
            <a href="../Module1/student_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
        <a class="sidebar2" href="../Module2/student_view.php">View Parking</a>
        <a class="sidebar2" href="../Module 3/view_bookings.php">View Booking</a>
        <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
    </div>

    <div class="container" style="max-width: 800px; margin: auto;">
        <h2>My Parking Bookings</h2>
        <br>

        <form method="GET" action="" class="search-container">
            <input type="text" name="search" class="search-input" placeholder="Search by Area or Space No..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="search-btn">Search</button>
            <?php if (!empty($search_query)): ?>
                <a href="?" class="clear-btn">Clear</a>
            <?php endif; ?>
        </form>

        <table style="text-align:center; align-item:center">
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
                    <tr><td colspan="5">No results found for "<?php echo htmlspecialchars($search_query); ?>".</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>