<?php
require_once '../config.php';
requireLogin();

$area_id = isset($_GET['area_id']) ? (int)$_GET['area_id'] : null;

if (!$area_id) {
    header("Location: admin_list_area.php?error=select_area");
    exit();
}

// Get area details
$area_query = mysqli_query($conn, "SELECT * FROM parking_area WHERE Area_id = '$area_id'");
$area = mysqli_fetch_assoc($area_query);

if (!$area) {
    header("Location: admin_list_area.php?error=area_not_found");
    exit();
}

// Get all spaces for this area
$spaces_query = mysqli_query($conn, "SELECT * FROM parking_space WHERE Area_id = '$area_id' ORDER BY Space_num");

// Detect user role for styling and sidebar
$user_role = $_SESSION['role'] ?? 'student';
$role_colors = [
    'student' => '#28a745',
    'security' => '#fd7e14',
    'admin' => '#667eea'
];
$header_color = $role_colors[$user_role] ?? '#667eea';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes - <?php echo htmlspecialchars($area['Area_name']); ?> | FKPark</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            margin-left: 240px;
        }

        header {
            background: <?php echo $header_color; ?>;
            color: white;
            padding: 20px 30px;
            margin-bottom: 30px;
            border-radius: 4px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .navbar {
            display: flex;
            gap: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .navbar a:hover {
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
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .area-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .area-info p {
            color: #555;
            font-size: 14px;
        }

        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .parking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .space-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .space-card:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102,126,234,0.2);
        }

        .space-card strong {
            font-size: 18px;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            color: white;
            margin-bottom: 10px;
        }

        .status-available {
            background: #28a745;
        }

        .status-occupied {
            background: #dc3545;
        }

        .status-maintenance {
            background: #ffc107;
            color: #333;
        }

        .qr-button {
            background: #667eea;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            width: 100%;
            margin-top: 8px;
        }

        .qr-button:hover {
            background: #5568d3;
        }

        .no-spaces {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        @media (max-width: 768px) {
            body {
                margin-left: 0;
            }
            .sidebar {
                width: 100%;
                position: static;
                max-height: none;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <a href="../Module1/admin_view_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <img class="logo" src="../photo/logoUmpsa.png" alt="Logo">
        <?php if ($user_role === 'admin'): ?>
            <a href="admin_list_area.php">Manage Area</a>
            <a href="admin_manage_spaces.php">Manage Space</a>
            <a href="admin_view.php">Parking Availability</a>
            <a href="../Module 3/admin_parking_report.php">Parking Report</a>
            <a href="../Module1/admin_list_users.php">Manage User</a>
        <?php elseif ($user_role === 'security'): ?>
            <a href="security_view.php">Parking Availability</a>
            <a href="../Module 3/manage_report.php">View Report</a>
        <?php else: ?>
            <a href="student_view.php">Parking Availability</a>
            <a href="../Module 3/book_parking.php">Book Parking</a>
            <a href="../Module 3/view_bookings.php">My Bookings</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>📱 QR Codes - <?php echo htmlspecialchars($area['Area_name']); ?></h2>

        <div class="area-info">
            <p><strong>Area:</strong> <?php echo htmlspecialchars($area['Area_name']); ?> | <strong>Category:</strong> <?php echo htmlspecialchars($area['Category']); ?> | <strong>Total Spaces:</strong> <?php echo mysqli_num_rows($spaces_query); ?></p>
        </div>

        <div class="controls">
            <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'admin_manage_spaces.php?area_id=' . $area_id; ?>" class="btn btn-secondary">← Back</a>
        </div>

        <?php if (mysqli_num_rows($spaces_query) > 0): ?>
            <div class="parking-grid">
                <?php 
                while ($space = mysqli_fetch_assoc($spaces_query)): 
                    // Determine status styling
                    if ($space['Current_status'] == 'Available') {
                        $status_class = 'status-available';
                        $status_label = 'Available';
                    } elseif ($space['Current_status'] == 'Occupied') {
                        $status_class = 'status-occupied';
                        $status_label = 'Occupied';
                    } else {
                        $status_class = 'status-maintenance';
                        $status_label = 'Maintenance';
                    }
                ?>
                    <div class="space-card">
                        <strong><?php echo htmlspecialchars($space['Space_num']); ?></strong>
                        <div class="status-badge <?php echo $status_class; ?>">
                            <?php echo $status_label; ?>
                        </div>
                        <a href="qr_display.php?space_id=<?php echo $space['Space_id']; ?>" class="qr-button">
                            📱 View QR Code
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-spaces">
                <p>No parking spaces in this area yet</p>
                <a href="admin_generates_spaces.php?area_id=<?php echo $area_id; ?>" class="btn btn-primary">Create Spaces</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
