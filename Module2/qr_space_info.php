<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone for Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

// Direct database connection (NO SESSION - this page is public for QR scanning)
$conn = mysqli_connect("localhost", "root", "", "fkpark");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get space information from QR code
$space_num = isset($_GET['space']) ? mysqli_real_escape_string($conn, $_GET['space']) : null;
$area_id = isset($_GET['area_id']) ? (int)$_GET['area_id'] : null;

// Debug: Log the parameters
error_log("QR DEBUG - space_num: " . $space_num . ", area_id: " . $area_id);

if (!$space_num || !$area_id) {
    die("Error: Invalid space information. Received space_num='" . htmlspecialchars($_GET['space'] ?? 'EMPTY') . "' and area_id='" . htmlspecialchars($_GET['area_id'] ?? 'EMPTY') . "'");
}

// Fetch space details
$space_query = mysqli_query($conn, "SELECT s.*, a.Area_name, a.Category 
                                    FROM parking_space s 
                                    JOIN parking_area a ON s.Area_id = a.Area_id 
                                    WHERE s.Space_num = '$space_num' AND s.Area_id = '$area_id'
                                    LIMIT 1");

if (!$space_query) {
    die("Error: Database query failed - " . mysqli_error($conn));
}
if (mysqli_num_rows($space_query) == 0) {
    die("Error: Space not found for space_num='$space_num' and area_id='$area_id'");
}

$space = mysqli_fetch_assoc($space_query);

// Get parking usage information for this space
$today = date('Y-m-d');
$usage_query = mysqli_query($conn, "SELECT pu.*, u.full_name, v.license_plate, ps.Space_num
                                    FROM parking_usage pu
                                    LEFT JOIN users u ON pu.user_id = u.user_id
                                    LEFT JOIN vehicle v ON pu.user_id = v.user_id
                                    LEFT JOIN parking_space ps ON pu.Space_id = ps.Space_id
                                    WHERE pu.Space_id = '{$space['Space_id']}'
                                    ORDER BY pu.entry_time DESC
                                    LIMIT 5");

$usage_records = [];
if ($usage_query) {
    while ($record = mysqli_fetch_assoc($usage_query)) {
        $usage_records[] = $record;
    }
}

// Get current status
$status = $space['Current_status'];
$status_color = '';
$status_text = '';

if ($status == 'Available') {
    $status_color = '#28a745';
    $status_text = 'Available';
} elseif ($status == 'Occupied') {
    $status_color = '#dc3545';
    $status_text = 'Occupied';
} elseif ($status == 'Maintenance') {
    $status_color = '#ffc107';
    $status_text = 'Maintenance';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Space <?php echo htmlspecialchars($space['Space_num']); ?> | FKPark</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 15px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .space-info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .space-info-box p {
            margin: 8px 0;
            font-size: 14px;
            color: #555;
        }

        .space-info-box strong {
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 13px;
            margin-top: 10px;
        }

        .status-available {
            background-color: #28a745;
        }

        .status-occupied {
            background-color: #dc3545;
        }

        .status-maintenance {
            background-color: #ffc107;
            color: #333;
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
            text-align: right;
        }

        .usage-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .usage-table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }

        .usage-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .usage-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .usage-table tbody tr:hover {
            background: #f0f4ff;
        }

        .status-badge-small {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-reserved {
            background: #cfe2ff;
            color: #084298;
        }

        .status-occupied-badge {
            background: #dc3545;
            color: white;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }

        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px;
            }

            .space-number {
                font-size: 42px;
            }

            .usage-table {
                font-size: 13px;
            }

            .usage-table th, .usage-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🅿️ Parking Space Information</h1>
        </div>

        <div style="text-align: center;">
            <div class="space-number">
                <?php echo htmlspecialchars($space['Space_num']); ?>
            </div>
            <div class="status-badge status-<?php echo strtolower($status); ?>">
                <?php echo htmlspecialchars($status_text); ?>
            </div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">📍 Area</span>
                <span class="info-value"><?php echo htmlspecialchars($space['Area_name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">🎯 Category</span>
                <span class="info-value"><?php echo htmlspecialchars($space['Category']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">📊 Space ID</span>
                <span class="info-value"><?php echo htmlspecialchars($space['Space_id']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">⏰ Updated</span>
                <span class="info-value"><?php echo date('Y-m-d H:i'); ?></span>
            </div>
        </div>

        <?php if ($status == 'Occupied'): ?>
            <div class="alert alert-info">
                ⚠️ This space is currently occupied. No bookings available at this moment.
            </div>
        <?php elseif ($status == 'Maintenance'): ?>
            <div class="alert alert-info">
                🔧 This space is under maintenance and temporarily unavailable.
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                ✅ This space is available for booking!
            </div>
        <?php endif; ?>

        <h3 style="margin-top: 30px; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            📋 Parking Usage History
        </h3>

        <?php if (!empty($usage_records)): ?>
            <table class="usage-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Entry Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usage_records as $record): ?>
                        <tr>
                            <td>
                                <?php 
                                    $user_name = $record['full_name'] 
                                        ? htmlspecialchars($record['full_name'])
                                        : 'Unknown';
                                    echo $user_name;
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo $record['entry_time'] 
                                        ? date('M d, H:i', strtotime($record['entry_time']))
                                        : '-';
                                ?>
                            </td>
                            <td>
                                <?php 
                                    echo $record['end_time'] && $record['end_time'] != '00:00:00'
                                        ? htmlspecialchars($record['end_time'])
                                        : '-';
                                ?>
                            </td>
                            <td>
                                <span class="status-badge-small status-<?php echo strtolower(str_replace(' ', '-', $record['status'])); ?>">
                                    <?php echo htmlspecialchars($record['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                No usage history for this space yet.
            </div>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="qr_display.php?space_id=<?php echo $space['Space_id']; ?>" class="back-button">
                🔙 Back to QR Code
            </a>
        </div>
    </div>
</body>
</html>
