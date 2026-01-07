<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];


$selected_month = $_GET['month'] ?? date('m');
$selected_year = $_GET['year'] ?? date('Y');
$selected_area = $_GET['area_id'] ?? 'all';


$area_filter = ($selected_area !== 'all') ? " AND Area_id = '$selected_area'" : "";
$area_filter_space = ($selected_area !== 'all') ? " WHERE Area_id = '$selected_area'" : "";
$areas_list = mysqli_query($conn, "SELECT * FROM parking_area ORDER BY Area_name");
$history_sql = "SELECT 
    SUM(CASE WHEN (status = 'Occupied' OR status = 'Completed') THEN 1 ELSE 0 END) as total_parked,
    SUM(CASE WHEN status = 'Reserved' THEN 1 ELSE 0 END) as total_reserved,
    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as total_cancelled
    FROM parking_usage 
    WHERE MONTH(usage_date) = '$selected_month' 
    AND YEAR(usage_date) = '$selected_year' 
    " . (($selected_area !== 'all') ? " AND Space_id IN (SELECT Space_id FROM parking_space WHERE Area_id = '$selected_area')" : "");

$history_res = mysqli_fetch_assoc(mysqli_query($conn, $history_sql));
$count_parked = $history_res['total_parked'] ?? 0;
$count_reserved = $history_res['total_reserved'] ?? 0;


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
            font-size: 14px;
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
            max-width: 900px;
            background: white;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h2 { 
            margin-bottom: 20px; 
            color: #333; 
        }
        p {
            margin: 10px 0;
            color: #555;
            font-size: 14px;
        }
        .buttons { 
            margin-top: 30px; 
            text-align: center; 
        }
        .logout-btn { 
            background: #dc3545; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            text-decoration: none; 
            cursor: pointer; 
            font-size: 14px; 
        }
        .logout-btn:hover {
            background: #c82333;
        }
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
        <a href="../admin_parking_report.php">Parking Report</a>
        <a href="../reserve_report.php">Reserve Report</a>
        <a href="../Module1/admin_list_users.php">Manage User</a>
    </div>
    <div class="container">
        <div class="container">
    <style>
        /* Internal styles specifically for the report content */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .report-header h2 { margin: 0; color: #2c3e50; font-size: 1.8rem; }
        
        .summary-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            border-top: 4px solid #3498db;
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-5px); }
        .card h4 { margin: 0; color: #7f8c8d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        .card .value { font-size: 2rem; font-weight: bold; color: #2c3e50; margin-top: 8px; }

        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 8px; }
        .filter-group label { font-weight: 600; color: #34495e; font-size: 0.9rem; }
        .filter-group select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #dcdde1;
            background: #fdfdfd;
            outline: none;
        }

        .chart-wrapper {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            height: 400px;
        }
    </style>

    <div class="report-header">
        <h2>Parking Analytics</h2>
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer; border-radius: 5px; border: 1px solid #ccc; background: #fff;">
            Print Report
        </button>
    </div>

    

    <form method="GET" class="filter-section">
        <div class="filter-group">
            <label>Parking Area</label>
            <select name="area_id" onchange="this.form.submit()">
                <option value="all">-- All Locations --</option>
                <?php mysqli_data_seek($areas_list, 0); while($row = mysqli_fetch_assoc($areas_list)): ?>
                    <option value="<?php echo $row['Area_id']; ?>" <?php if($selected_area == $row['Area_id']) echo 'selected'; ?>>
                        <?php echo $row['Area_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Timeframe (Month/Year)</label>
            <div style="display: flex; gap: 10px;">
                <select name="month" onchange="this.form.submit()">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?php echo sprintf('%02d', $m); ?>" <?php if($selected_month == $m) echo 'selected'; ?>>
                            <?php echo date('F', mktime(0,0,0,$m,1)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select name="year" onchange="this.form.submit()">
                    <?php for($y=2024; $y<=2026; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php if($selected_year == $y) echo 'selected'; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    </form>

    <div class="chart-wrapper">
        <canvas id="parkingChart"></canvas>
    </div>

    <p style="text-align:center; color:#7f8c8d; margin-top:20px; font-size: 0.9rem;">
        Showing results for <strong><?php echo ($selected_area == 'all' ? 'All Areas' : 'Selected Area'); ?></strong> 
        in <strong><?php echo date('F Y', mktime(0,0,0,$selected_month, 1, $selected_year)); ?></strong>.
    </p>

    <script>
        
        const ctx = document.getElementById('parkingChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed Stays', 'Reserved'],
                datasets: [{
                    data: [<?php echo $count_parked; ?>, <?php echo $count_reserved; ?>],
                    backgroundColor: ['#2ecc71', '#3498db', '#e74c3c'],
                    hoverOffset: 15,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    title: {
                        display: true,
                        text: 'Usage Status Breakdown',
                        font: { size: 16 }
                    }
                },
                cutout: '65%'
            }
        });
    </script>
</div>
</div>

<script>
const ctx = document.getElementById('parkingChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Actual Parking', 'Reservations'], 
        datasets: [{
            label: 'Total Usage Records',
            data: [
                <?php echo $count_parked; ?>, 
                <?php echo $count_reserved; ?>, 
                <?php echo $count_cancelled; ?>
            ],
            backgroundColor: ['#dc3545', '#ffc107'], 
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
