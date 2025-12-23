<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];
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
        <h2>Parking Report</h2>
        
        <div class="stats">
            <div class="stat-box">
                <h3>Total Areas</h3>
                <div class="number" id="total-areas">0</div>
            </div>
            <div class="stat-box">
                <h3>Total Spaces</h3>
                <div class="number" id="total-spaces">0</div>
            </div>
            <div class="stat-box">
                <h3>Available</h3>
                <div class="number" id="available-spaces">0</div>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="parkingChart"></canvas>
        </div>

        <p style="text-align: center; color: #666; margin-top: 20px;">
            This report shows the current parking area and space statistics.
        </p>
    </div>

    <script>
        // Fetch data and populate stats
        fetch('../Module2/admin_manage_spaces.php')
            .then(() => {
                // For now, show placeholder data
                document.getElementById('total-areas').textContent = '-';
                document.getElementById('total-spaces').textContent = '-';
                document.getElementById('available-spaces').textContent = '-';
            });

        // Sample chart data
        const ctx = document.getElementById('parkingChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Available', 'Occupied', 'Maintenance'],
                datasets: [{
                    label: 'Parking Spaces',
                    data: [45, 12, 8],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
