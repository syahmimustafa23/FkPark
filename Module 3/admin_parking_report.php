<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

// 1. Capture Filters from GET
$selected_month = $_GET['month'] ?? date('m');
$selected_year = $_GET['year'] ?? date('Y');
$selected_area = $_GET['area_id'] ?? 'all';

// 2. Build the Area Filter SQL snippet
$area_filter = ($selected_area !== 'all') ? " AND Area_id = '$selected_area'" : "";
$area_filter_space = ($selected_area !== 'all') ? " WHERE Area_id = '$selected_area'" : "";

// 3. Fetch Area List for the new dropdown
$areas_list = mysqli_query($conn, "SELECT * FROM parking_area ORDER BY Area_name");

// 4. Fetch Summary Card Data (Filtered by Area)
$total_areas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parking_area"))['count'];
$total_spaces = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parking_space $area_filter_space"))['count'];
$total_available = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parking_space WHERE Current_status = 'Available' $area_filter"))['count'];

// 5. Fetch Chart Data (Filtered by Month, Year, and Area)
$chart_sql = "SELECT 
    SUM(CASE WHEN Current_status = 'Available' THEN 1 ELSE 0 END) as avail,
    SUM(CASE WHEN Current_status = 'Occupied' THEN 1 ELSE 0 END) as occ,
    SUM(CASE WHEN Current_status = 'Maintenance' THEN 1 ELSE 0 END) as maint
    FROM parking_space 
    $area_filter_space";

// Note: If you want historical usage for the chart instead of live status:
// 5. Fetch Historical Usage Data (Filtered by Month, Year, and Area)
$history_sql = "SELECT 
    SUM(CASE WHEN (status = 'Occupied' OR status = 'Completed') THEN 1 ELSE 0 END) as total_parked,
    SUM(CASE WHEN status = 'Reserved' THEN 1 ELSE 0 END) as total_reserved,
    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as total_cancelled
    FROM parking_usage 
    WHERE MONTH(usage_date) = '$selected_month' 
    AND YEAR(usage_date) = '$selected_year' 
    " . (($selected_area !== 'all') ? " AND Space_id IN (SELECT Space_id FROM parking_space WHERE Area_id = '$selected_area')" : "");

$history_res = mysqli_fetch_assoc(mysqli_query($conn, $history_sql));

// Set variables to 0 if no records are found (this ensures the graph is empty for 2026)
$count_parked = $history_res['total_parked'] ?? 0;
$count_reserved = $history_res['total_reserved'] ?? 0;
$count_cancelled = $history_res['total_cancelled'] ?? 0;

$live_res = mysqli_fetch_assoc(mysqli_query($conn, $chart_sql));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Report | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #667eea; color: white; padding: 20px 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .navbar1 { float: right; display: flex; gap: 20px; }
        .navbar1 a { color: white; text-decoration: none; }
        .navbar1 a:hover { text-decoration: underline; }
        .sidebar {
            width: 200px;
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .sidebar a {
            display: block;
            padding: 12px;
            margin: 5px 0;
            color: black;
            text-decoration: none;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background: #667eea;
            color: white;
        }
        .sidebar a.active {
            background: #667eea;
            color: white;
        }
        .logo {
            width: 100%;
            margin-bottom: 20px;
        }
        .container {
            margin-left: 250px;
            max-width: 1000px;
            background: white;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 20px; color: #333; }
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 30px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .stat-box h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .stat-box .number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; padding: 20px; }
        .report-container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 8px; shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        /* Top Summary Cards */
        .summary-row { display: flex; gap: 20px; margin-bottom: 30px; justify-content: space-between; }
        .card { flex: 1; background: #fff; padding: 20px; text-align: center; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-top: 4px solid #667eea; }
        .card h4 { margin: 0; color: #666; font-size: 14px; text-transform: uppercase; }
        .card .value { font-size: 24px; font-weight: bold; color: #4e73df; margin-top: 10px; }

        /* Filters */
        .filter-row { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; }
        select { padding: 8px; border-radius: 4px; border: 1px solid #ddd; }

        .chart-box { position: relative; height: 400px; width: 100%; }
        .footer-note { text-align: center; color: #888; margin-top: 20px; font-size: 14px; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight current menu item
            const currentFile = 'admin_parking_report.php';
            const menuLinks = document.querySelectorAll('.sidebar a');
            menuLinks.forEach(link => {
                if (link.getAttribute('href').includes('admin_parking_report')) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>FKPark</h1>
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
        <a href="../Module 3/admin_parking_report.php" class="active">Parking Report</a>
        <a href="../Module1/admin_list_users.php">Manage User</a>
    </div>

    <div class="container">
        <div class="report-container">
    <h2>Parking Report</h2>

    <div class="summary-row">
        <div class="card"><h4>Total Areas</h4><div class="value"><?php echo $total_areas; ?></div></div>
        <div class="card"><h4>Total Spaces</h4><div class="value"><?php echo $total_spaces; ?></div></div>
        <div class="card" style="border-top-color: #28a745;"><h4>Available</h4><div class="value"><?php echo $total_available; ?></div></div>
    </div>

    <form method="GET" class="filter-section">
        <div>
            <label>Parking Area:</label>
            <select name="area_id" onchange="this.form.submit()">
                <option value="all">-- All Areas --</option>
                <?php while($row = mysqli_fetch_assoc($areas_list)): ?>
                    <option value="<?php echo $row['Area_id']; ?>" <?php if($selected_area == $row['Area_id']) echo 'selected'; ?>>
                        <?php echo $row['Area_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label>Month:</label>
            <select name="month" onchange="this.form.submit()">
                <?php for($m=1; $m<=12; $m++): ?>
                    <option value="<?php echo sprintf('%02d', $m); ?>" <?php if($selected_month == $m) echo 'selected'; ?>>
                        <?php echo date('F', mktime(0,0,0,$m,1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div>
            <label>Year:</label>
            <select name="year" onchange="this.form.submit()">
                <?php for($y=2024; $y<=2026; $y++): ?>
                    <option value="<?php echo $y; ?>" <?php if($selected_year == $y) echo 'selected'; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </form>

    <div class="chart-container">
        <canvas id="parkingChart"></canvas>
    </div>

    <p style="text-align:center; color:#666; margin-top:20px;">
        Showing statistics for <strong><?php echo ($selected_area == 'all' ? 'All Areas' : 'Selected Area'); ?></strong> 
        during <strong><?php echo date('F Y', mktime(0,0,0,$selected_month, 1, $selected_year)); ?></strong>.
    </p>
</div>

<script>
const ctx = document.getElementById('parkingChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Actual Parking', 'Reservations', 'Cancellations'], // Updated Labels
        datasets: [{
            label: 'Total Usage Records',
            data: [
                <?php echo $count_parked; ?>, 
                <?php echo $count_reserved; ?>, 
                <?php echo $count_cancelled; ?>
            ],
            backgroundColor: ['#dc3545', '#ffc107', '#6c757d'], // Red, Yellow, Grey
            barThickness: 80
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { 
            y: { 
                beginAtZero: true, 
                ticks: { stepSize: 1 },
                title: { display: true, text: 'Number of Records' }
            } 
        }
    }
});
</script>
</body>
</html>
