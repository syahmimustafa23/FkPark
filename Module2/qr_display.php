<?php
require_once '../config.php';
requireLogin();

// Get space information
$space_id = isset($_GET['space_id']) ? (int)$_GET['space_id'] : null;
$back_from = isset($_GET['back_from']) ? htmlspecialchars($_GET['back_from']) : 'qr_codes';
$area_id = isset($_GET['area_id']) ? (int)$_GET['area_id'] : null;

if (!$space_id) {
    die("Error: Space ID not provided");
}

// Fetch space details
$space_query = mysqli_query($conn, "SELECT s.*, a.Area_id, a.Area_name 
                                    FROM parking_space s 
                                    JOIN parking_area a ON s.Area_id = a.Area_id 
                                    WHERE s.Space_id = '$space_id'
                                    LIMIT 1");

if (!$space_query || mysqli_num_rows($space_query) == 0) {
    die("Error: Space not found");
}

$space = mysqli_fetch_assoc($space_query);

// Use area_id from query if provided, otherwise from space
$area_id = $area_id ?: $space['Area_id'];

// Determine back button URL based on where user came from
if ($back_from == 'manage_spaces') {
    $back_url = "admin_manage_spaces.php?area_id=" . $area_id;
    $back_text = "Back to Manage Spaces";
} else {
    $back_url = "view_qr_codes.php?area_id=" . $area_id;
    $back_text = "Back to QR Codes";
}

// Generate QR code content - use the current domain/IP so it works on phones
$server_host = $_SERVER['HTTP_HOST']; // This will be whatever the user typed (localhost, IP, domain, etc)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';

// If user accessed via localhost, get the actual WiFi IP for the QR code
if ($server_host === 'localhost' || strpos($server_host, '127.0.0.1') === 0) {
    // Try to get the server's actual IP address
    if (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
        $server_host = $_SERVER['SERVER_ADDR'];
        $protocol = 'http://'; // Local IP should use http
    } else {
        // Fallback: Try to get IPv4 address
        $server_host = gethostbyname(gethostname());
        if ($server_host === gethostname()) {
            // If that fails, keep localhost
            $server_host = 'localhost';
        }
        $protocol = 'http://'; // Localhost uses http
    }
}

$qr_content = $protocol . $server_host . "/fkpark/Module2/qr_space_info.php?area_id=" . $space['Area_id'] . "&space=" . urlencode($space['Space_num']);

// Generate QR code using Google QR Server API
$encoded_text = urlencode($qr_content);
$qr_size = 500;
$qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size={$qr_size}x{$qr_size}&data={$encoded_text}";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code - <?php echo htmlspecialchars($space['Space_num']); ?> | FKPark</title>
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

        .navbar1 {
            display: flex;
            gap: 20px;
        }

        .navbar1 a {
            color: white;
            text-decoration: none;
            font-size: 14px;
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
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .info-box p {
            margin: 8px 0;
            color: #555;
            font-size: 14px;
        }

        .qr-display {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
            margin: 20px 0;
        }

        .qr-display img {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background: white;
        }

        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
            color: #0c5aa0;
        }

        .instructions h3 {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .instructions ol {
            margin-left: 20px;
        }

        .instructions li {
            margin: 5px 0;
        }

        .url-display {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            margin-top: 15px;
            font-size: 12px;
            color: #666;
            word-break: break-all;
            border: 1px solid #ddd;
        }

        .back-button {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 20px;
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
        <a href="admin_list_area.php">Manage Area</a>
        <a href="admin_manage_spaces.php">Manage Space</a>
        <a href="admin_view.php">Parking Availability</a>
        <a href="../Module 3/admin_parking_report.php">Parking Report</a>
        <a href="../Module1/admin_list_users.php">Manage User</a>
    </div>

    <div class="container">
        <h2>📱 Scan Parking Space QR Code</h2>

        <div class="info-box">
            <p><strong>Space:</strong> <?php echo htmlspecialchars($space['Space_num']); ?> | <strong>Area:</strong> <?php echo htmlspecialchars($space['Area_name']); ?></p>
            <p><strong>Status:</strong> <span style="background: <?php echo ($space['Current_status'] == 'Available' ? '#28a745' : ($space['Current_status'] == 'Occupied' ? '#dc3545' : '#ffc107')); ?>; color: white; padding: 3px 10px; border-radius: 3px; font-size: 12px;"><?php echo htmlspecialchars($space['Current_status']); ?></span></p>
        </div>

        <div class="instructions">
            <h3>📱 How to Scan:</h3>
            <ol>
                <li>Point your phone camera at the QR code below</li>
                <li>Wait for the notification to appear</li>
                <li>Tap the notification to open space information</li>
                <li>View parking usage history and space details</li>
            </ol>
        </div>

        <div class="qr-display">
            <img src="<?php echo htmlspecialchars($qr_image_url); ?>" 
                 alt="QR Code for Space <?php echo htmlspecialchars($space['Space_num']); ?>" 
                 style="max-width: 400px; width: 100%;">
            <div class="url-display">
                URL: <?php echo htmlspecialchars($qr_content); ?>
            </div>
        </div>

        <button onclick="location.href='<?php echo $back_url; ?>';" class="back-button">
            🔙 <?php echo $back_text; ?>
        </button>
    </div>
</body>
</html>
