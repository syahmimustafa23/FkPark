<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get total users count
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($total_users_query)['total'];

// Get users count by type
$type_query = mysqli_query($conn, "SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type");
$user_types = [];
$type_labels = [];
$type_counts = [];
$type_colors = ['#667eea', '#28a745', '#fd7e14']; // Admin (blue), Student (green), Staff (orange)

$color_index = 0;
while ($row = mysqli_fetch_assoc($type_query)) {
    $user_types[] = [
        'type' => ucfirst(str_replace('_', ' ', $row['user_type'])),
        'count' => $row['count'],
        'color' => $type_colors[$color_index % count($type_colors)]
    ];
    $type_labels[] = ucfirst(str_replace('_', ' ', $row['user_type']));
    $type_counts[] = $row['count'];
    $color_index++;
}

// Get recent users
$recent_users_query = mysqli_query($conn, "SELECT user_id, username, full_name, user_type FROM users ORDER BY user_id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Statistics Report | FKPark</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
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
            justify-content: space-between;
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
        .sidebar a:first-child {
            padding: 0;
            margin: 0 0 20px 0;
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
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 5px solid #667eea;
        }
        .stat-card.student {
            border-left-color: #28a745;
        }
        .stat-card.staff {
            border-left-color: #fd7e14;
        }
        .stat-number {
            font-size: 42px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .stat-card.student .stat-number {
            color: #28a745;
        }
        .stat-card.staff .stat-number {
            color: #fd7e14;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .chart-card h3 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 15px;
        }
        .recent-users-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .recent-users-section h3 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }
        .table {
            margin-bottom: 0;
        }
        .table-light thead {
            background-color: #f8f9fa;
        }
        .user-type-badge {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-admin {
            background-color: #e7e9f5;
            color: #667eea;
        }
        .badge-student {
            background-color: #e7f5ed;
            color: #28a745;
        }
        .badge-staff {
            background-color: #fff5e6;
            color: #fd7e14;
        }
    </style>
</head>

<body>
    <header>
        <h1 style="margin: 0;">User Statistics Report</h1>
        <div class="navbar1">
            <a href="admin_view_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <img class="logo" src="../photo/logoUmpsa.png" alt="Logo">
        <a href="../Module2/admin_list_area.php">Manage Area</a>
        <a href="../Module2/admin_manage_spaces.php">Manage Space</a>
        <a href="../Module2/admin_view.php">Parking Availability</a>
        <a href="../Module 3/admin_parking_report.php">Parking Report</a>
        <a href="admin_list_users.php">Manage User</a>
        <a href="admin_user_statistics.php" style="background: #667eea; color: white;">User Statistics</a>
    </div>

    <div class="container-fluid">
        <h1>User Management Statistics</h1>

        <!-- Statistics Cards -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label" style="color: #999; text-transform: none;">Active Users in System</div>
            </div>

            <?php foreach ($user_types as $type): ?>
            <div class="stat-card <?php echo strtolower(str_replace(' ', '', $type['type'])); ?>">
                <div class="stat-label"><?php echo $type['type']; ?> Users</div>
                <div class="stat-number"><?php echo $type['count']; ?></div>
                <div class="stat-label" style="color: #999; text-transform: none;">
                    <?php echo round(($type['count'] / $total_users) * 100, 1); ?>% of total
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <!-- Pie Chart -->
            <div class="chart-card">
                <h3>User Distribution by Type</h3>
                <div class="chart-container">
                    <canvas id="pieChart"></canvas>
                </div>
                <p style="color: #666; font-size: 13px; margin-top: 15px;">
                    Shows the percentage breakdown of each user type in the system.
                </p>
            </div>

            <!-- Bar Chart -->
            <div class="chart-card">
                <h3>User Count by Type</h3>
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
                <p style="color: #666; font-size: 13px; margin-top: 15px;">
                    Displays the actual count of users for each user type.
                </p>
            </div>
        </div>

        <!-- Recent Users Section -->
        <div class="recent-users-section">
            <h3>Recent Registered Users</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($recent_users_query)): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td>
                                <span class="user-type-badge badge-<?php echo strtolower($user['user_type']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $user['user_type'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="admin_update_user.php?id=<?php echo $user['user_id']; ?>" class="link-primary small">Edit</a>
                                <a href="admin_view_user.php?id=<?php echo $user['user_id']; ?>" class="link-primary small ms-2">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($type_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($type_counts); ?>,
                    backgroundColor: [
                        '#667eea',
                        '#28a745',
                        '#fd7e14',
                        '#dc3545',
                        '#17a2b8'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($type_labels); ?>,
                datasets: [{
                    label: 'Number of Users',
                    data: <?php echo json_encode($type_counts); ?>,
                    backgroundColor: [
                        '#667eea',
                        '#28a745',
                        '#fd7e14'
                    ],
                    borderColor: [
                        '#667eea',
                        '#28a745',
                        '#fd7e14'
                    ],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            padding: 15,
                            font: { size: 12 }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
