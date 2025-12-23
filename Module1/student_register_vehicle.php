<?php
/**
 * FKPark Student Dashboard (minimal)
 */
require_once '../config.php';
requireLogin();
if ($_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];
if (isset($_POST['submit_vehicle'])) {
    $user_id = $_SESSION['user_id']; // Logged-in student
    $v_type = $_POST['vehicle_type']; // 'car' or 'motorcycle'
    $model = mysqli_real_escape_string($conn, $_POST['vehicle_model']);
    $plate = mysqli_real_escape_string($conn, $_POST['license_plate']);

    // File Upload Logic [cite: 55, 1168]
    $target_dir = "uploads/grants/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_name = time() . "_" . basename($_FILES["grant_document"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["grant_document"]["tmp_name"], $target_file)) {
        // Insert into vehicle table
        $sql = "INSERT INTO vehicle (user_id, vehicle_type, license_plate, vehicle_model, grant_document) 
                VALUES ('$user_id', '$v_type', '$plate', '$model', '$target_file')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Registration Submitted! Wait for Staff approval.'); window.location='student_profile.php';</script>";
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard  | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #28a745; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .welcome h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #555; }
        .buttons { margin-top: 30px; text-align: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
        img{width: 100px; }
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
    height: 100%;               /* full height */
    width: 250px;               /* sidebar width */
    width: 200px;               /* sidebar width */
    position: fixed;            /* stick to left */
    top: 20px;
    left: 20px;
    background-color: #7fffd6;
    padding-top: 20px;
    background-color: #FFFFFF;
    
    display: flex;
    flex-direction: column;
}

.sidebar a {
    padding: 15px 25px;
    padding:0px 0px;
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
td{
    padding: 10px;
    
}

.btn-add{
    text-decoration:  none; 
    list-style-type: none;

    background: #219bffff; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; 
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
 
    <a class="sidebar2" href="sidebar2">Traffic Summons</a>
 
    </div>

    
   
    <div class="container">
        <h1>Register New Vehicle</h1><br>
        <form method="POST" action="" enctype="multipart/form-data">
        <table>
            <tr>
                <td>Vehicle Type:</td>
                <td>
                    <select name="vehicle_type" required>
                        <option value="car">Car</option>
                        <option value="motorcycle">Motorcycle</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Vehicle Model:</td>
                <td><input type="text" name="vehicle_model" required></td>
            </tr>
            <tr>
                <td>License Plate:</td>
                <td><input type="text" name="license_plate" required></td>
            </tr>
            <tr>
                <td>Grant Document:</td>
                <td><input type="file" name="grant_document" accept=".pdf,.jpg,.png" required></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center;">
                    <input class="btn-add" type="submit" name="submit_vehicle" value="Register Vehicle">
                </td>
            </tr>
        </table>
        </form>
    </div>
     
</body>
</html>























