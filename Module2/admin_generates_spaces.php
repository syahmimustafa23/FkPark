<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

$area_id = isset($_GET['area_id']) ? (int)$_GET['area_id'] : null;

if (!$area_id) {
    header("Location: admin_list_area.php?error=select_area");
    exit();
}

// Create QR codes directory if it doesn't exist
$qr_codes_dir = __DIR__ . '/../qr_codes/';
if (!is_dir($qr_codes_dir)) {
    @mkdir($qr_codes_dir, 0755, true);
}

// Check if form was submitted with confirmation
if (isset($_POST['bulk_generate']) && isset($_POST['submit_generate']) && $_POST['submit_generate'] == 1) {
    $count = (int)$_POST['num_spaces'];
    $prefix = mysqli_real_escape_string($conn, $_POST['prefix']);
    $generated_count = 0;

    // Get the actual server host for QR code generation
    $server_host = $_SERVER['HTTP_HOST'];
    if ($server_host === 'localhost' || strpos($server_host, '127.0.0.1') === 0) {
        if (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
            $server_host = $_SERVER['SERVER_ADDR'];
        } else {
            $server_host = gethostbyname(gethostname());
            if ($server_host === gethostname()) {
                $server_host = 'localhost';
            }
        }
    }

    for ($i = 1; $i <= $count; $i++) {
        $space_num = $prefix . str_pad($i, 2, '0', STR_PAD_LEFT);
        
        // Generate QR code content - point to parking availability page with space parameter
        $qr_content = "http://" . $server_host . "/fkpark/Module2/qr_space_info.php?area_id=" . $area_id . "&space=" . urlencode($space_num);
        
        // Generate QR code using Google QR Server API
        $encoded_text = urlencode($qr_content);
        $qr_size = 400;
        $qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size={$qr_size}x{$qr_size}&data={$encoded_text}";
        
        // Download and save QR code image
        $qr_filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $space_num);
        $qr_filepath = $qr_codes_dir . $qr_filename . '.png';
        
        $image_data = @file_get_contents($qr_image_url);
        if ($image_data !== false && strlen($image_data) > 100) {
            // Only save if we got valid image data
            file_put_contents($qr_filepath, $image_data);
            $qr_code_path = "../qr_codes/" . $qr_filename . ".png";
        } else {
            // Fallback: store the URL if image download fails
            $qr_code_path = $qr_image_url;
        }

        // Insert into database
        $sql = "INSERT INTO parking_space (Area_id, Space_num, Space_qrCode, Current_status) 
                VALUES ('$area_id', '$space_num', '$qr_code_path', 'Available')";
        
        if (mysqli_query($conn, $sql)) {
            $generated_count++;
        }
    }
    
    // Redirect with success message
    if ($generated_count > 0) {
        header("Location: admin_list_area.php?msg=spaces_generated&count=$generated_count");
    } else {
        header("Location: admin_generates_spaces.php?area_id=$area_id&msg=error");
    }
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FKPark</title>
    <script>
        function confirmGenerateSpaces(event) {
            event.preventDefault();
            const prefix = document.getElementById('prefix').value.trim();
            const count = document.getElementById('num_spaces').value.trim();
            
            if (!validateSpacePrefix() || !validateSpaceCount()) {
                return false;
            }
            
            const message = `Generate ${count} parking spaces with prefix "${prefix}"?`;
            if (confirm(message)) {
                // Set the hidden field to indicate JavaScript confirmed
                document.getElementById('submit_generate').value = '1';
                document.getElementById('generateForm').submit();
            }
            return false;
        }
        
        function validateSpacePrefix() {
            const prefix = document.getElementById('prefix').value.trim();
            const prefixError = document.getElementById('prefixError');
            
            if (!prefix) {
                prefixError.textContent = 'Prefix is required';
                prefixError.style.display = 'block';
                return false;
            }
            
            if (!/^[a-zA-Z0-9]+$/.test(prefix)) {
                prefixError.textContent = 'Prefix can only contain letters and numbers';
                prefixError.style.display = 'block';
                return false;
            }
            
            prefixError.style.display = 'none';
            return true;
        }
        
        function validateSpaceCount() {
            const count = document.getElementById('num_spaces').value.trim();
            const countError = document.getElementById('countError');
            
            if (!count || parseInt(count) < 1) {
                countError.textContent = 'Number of spaces must be at least 1';
                countError.style.display = 'block';
                return false;
            }
            
            countError.style.display = 'none';
            return true;
        }
    </script>
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
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        form input, form select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        form button {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        form button:hover {
            background: #5568d3;
        }
        .error {
            color: #dc3545;
            font-size: 12px;
            display: none;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f9f9f9;
            font-weight: bold;
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
       <h2>Generate Spaces for Area ID: <?php echo $area_id; ?></h2>
    <form id="generateForm" method="POST" onsubmit="confirmGenerateSpaces(event)">
        <label>Prefix (e.g., A for Block A):</label>
        <input type="text" id="prefix" name="prefix" required>
        <div id="prefixError" class="error"></div>
        
        <label>How many spaces to create?</label>
        <input type="number" id="num_spaces" name="num_spaces" value="10" min="1" max="50">
        <div id="countError" class="error"></div>
        
        <input type="hidden" id="submit_generate" name="submit_generate" value="0">
        <button type="submit" name="bulk_generate">Generate Spaces Now</button>
    </form>
    </div>
     
</body>
</html>




