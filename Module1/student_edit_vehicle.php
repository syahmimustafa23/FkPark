<?php
/**
 * FKPark Student Edit Vehicle
 */
require_once '../config.php';
requireLogin();
if ($_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['vehicle_id'])) {
    header("Location: student_profile.php");
    exit();
}

$vehicle_id = mysqli_real_escape_string($conn, $_GET['vehicle_id']);

// Fetch vehicle data
$query = "SELECT v.*, a.status FROM vehicle v 
          LEFT JOIN approval a ON v.vehicle_id = a.vehicle_id 
          WHERE v.vehicle_id = '$vehicle_id' AND v.user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$vehicle = mysqli_fetch_assoc($result);

if (!$vehicle) {
    header("Location: student_profile.php");
    exit();
}

if (isset($_POST['update_vehicle'])) {
    $v_type = $_POST['vehicle_type'];
    $model = mysqli_real_escape_string($conn, $_POST['vehicle_model']);
    $plate = mysqli_real_escape_string($conn, $_POST['license_plate']);

    $update_sql = "UPDATE vehicle SET vehicle_type='$v_type', license_plate='$plate', vehicle_model='$model' WHERE vehicle_id='$vehicle_id'";

    // If file is uploaded, update grant document
    if (!empty($_FILES["grant_document"]["name"])) {
        $target_dir = "uploads/grants/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $file_name = time() . "_" . basename($_FILES["grant_document"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["grant_document"]["tmp_name"], $target_file)) {
            $update_sql = "UPDATE vehicle SET vehicle_type='$v_type', license_plate='$plate', vehicle_model='$model', grant_document='$target_file' WHERE vehicle_id='$vehicle_id'";
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    if (mysqli_query($conn, $update_sql)) {
        // If previously approved, reset to pending
        if ($vehicle['status'] === 'Approved') {
            $reset_sql = "UPDATE approval SET status='Pending' WHERE vehicle_id='$vehicle_id'";
            mysqli_query($conn, $reset_sql);
        }
        echo "<script>alert('Vehicle updated successfully!'); window.location='student_profile.php';</script>";
    } else {
        echo "Error updating vehicle: " . mysqli_error($conn);
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #28a745; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .navbar1{text-decoration-line: none;text-decoration: none; float: right; overflow: hidden; list-style-type: none; display: flex; text-align: center; padding: 0px; margin:0px;}
        .navbar1 a{ 
            display: block;
            color: black;
            padding: 0px 20px;
            text-decoration: none;
            text-align: center;
        }
        .navbar1 a:hover{
            background-color: black;
            color: white;
        }
        .sidebar {
            height: 100%;
            width: 200px;
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #FFFFFF;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: black;
            display: block;
        }
        .sidebar a:hover{
            background-color:black;
            color: white;
        }
        a.sidebar2{
            padding: 15px 20px;
        }
        .logo{
            width: 200px;
            height: auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .form-group input[type="text"],
        .form-group select,
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input[type="file"] {
            padding: 8px;
        }
        .btn-update {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        .btn-update:hover {
            background: #0056b3;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .status-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: #856404;
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar1">
            <a href="student_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
    <div class="sidebar">
        <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
        <a class="sidebar2" href="../Module2/student_view.php">View Parking</a>
        <a class="sidebar2" href="../Module 3/viewBooking.php">View Booking</a>
        <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
    </div>

    <div class="container">
        <h1>Edit Vehicle Information</h1>
        <?php if ($vehicle['status'] === 'Approved'): ?>
            <div class="status-info">
                <strong>Note:</strong> Editing your vehicle will reset its approval status to pending and require re-approval by security staff.
            </div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="vehicle_type">Vehicle Type:</label>
                <select name="vehicle_type" id="vehicle_type" required>
                    <option value="car" <?php echo ($vehicle['vehicle_type'] == 'car') ? 'selected' : ''; ?>>Car</option>
                    <option value="motorcycle" <?php echo ($vehicle['vehicle_type'] == 'motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                </select>
            </div>
            <div class="form-group">
                <label for="vehicle_model">Vehicle Model:</label>
                <input type="text" name="vehicle_model" id="vehicle_model" value="<?php echo htmlspecialchars($vehicle['vehicle_model']); ?>" required>
            </div>
            <div class="form-group">
                <label for="license_plate">License Plate:</label>
                <input type="text" name="license_plate" id="license_plate" value="<?php echo htmlspecialchars($vehicle['license_plate']); ?>" required>
            </div>
            <div class="form-group">
                <label for="grant_document">Grant Document (leave empty to keep current):</label>
                <input type="file" name="grant_document" id="grant_document" accept=".pdf,.jpg,.png">
                <small>Current file: <?php echo basename($vehicle['grant_document']); ?></small>
            </div>
            <button class="btn-update" type="submit" name="update_vehicle">Update Vehicle</button>
        </form>
    </div>
</body>
</html>