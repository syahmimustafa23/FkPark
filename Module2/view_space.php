<?php
require_once '../config.php';

// Get space information from QR code
$space_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : null;

if (!$space_id) {
    die("Error: Space ID not provided");
}

// Fetch space details
$space_query = mysqli_query($conn, "SELECT s.*, a.Area_name, a.Category FROM parking_space s 
                                    JOIN parking_area a ON s.Area_id = a.Area_id 
                                    WHERE s.Space_num = '$space_id' 
                                    LIMIT 1");

if (!$space_query || mysqli_num_rows($space_query) == 0) {
    die("Error: Space not found");
}

$space = mysqli_fetch_assoc($space_query);

// Get current status color
$status = $space['Current_status'];
$status_color = '';
$status_text = '';

if ($status == 'Available') {
    $status_color = '#28a745';
    $status_text = 'Available for Booking';
} elseif ($status == 'Occupied') {
    $status_color = '#dc3545';
    $status_text = 'Currently Occupied';
} elseif ($status == 'Maintenance') {
    $status_color = '#ffc107';
    $status_text = 'Under Maintenance';
}

// Check if user is logged in for actions
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Space: <?php echo htmlspecialchars($space['Space_num']); ?> | FKPark</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .space-number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
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
            flex: 1;
        }

        .info-value {
            color: #333;
            flex: 1;
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 14px;
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

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .location-icon {
            font-size: 16px;
            margin-right: 5px;
        }

        .qr-generated-by {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .login-notice {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 13px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            color: #721c24;
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px;
            }

            .space-number {
                font-size: 36px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🅿️ Parking Space</h1>
            <p>FKPark Management System</p>
        </div>

        <div class="space-number">
            <?php echo htmlspecialchars($space['Space_num']); ?>
        </div>

        <div class="info-box">
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
                <span class="info-label">⏰ Last Updated</span>
                <span class="info-value"><?php echo date('Y-m-d H:i'); ?></span>
            </div>
        </div>

        <div style="text-align: center;">
            <strong>Current Status</strong>
            <div class="status-badge status-<?php echo strtolower($status); ?>">
                <?php echo htmlspecialchars($status_text); ?>
            </div>
        </div>

        <?php if ($status == 'Occupied'): ?>
            <div class="alert alert-danger">
                ⚠️ This space is currently occupied. You cannot book this space at the moment.
            </div>
        <?php elseif ($status == 'Maintenance'): ?>
            <div class="alert alert-warning">
                🔧 This space is under maintenance. It is temporarily unavailable for booking.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                ✅ This space is available for booking! You can proceed with your reservation.
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <?php if ($is_logged_in && $user_role == 'student'): ?>
                <?php if ($status == 'Available'): ?>
                    <a href="../Module 3/book_parking.php?space_id=<?php echo $space['Space_id']; ?>" class="btn btn-primary">
                        📅 Book Space
                    </a>
                <?php else: ?>
                    <button class="btn btn-primary" disabled>
                        📅 Book Space (Unavailable)
                    </button>
                <?php endif; ?>
            <?php endif; ?>

            <a href="../Module2/student_view.php" class="btn btn-secondary">
                🔙 Back to Map
            </a>
        </div>

        <?php if (!$is_logged_in): ?>
            <div class="login-notice">
                💡 <strong>Tip:</strong> Log in to your account to book this parking space. 
                <a href="../login.php">Click here to login</a>
            </div>
        <?php endif; ?>

        <div class="qr-generated-by">
            Generated by FKPark QR System
        </div>
    </div>
</body>
</html>
