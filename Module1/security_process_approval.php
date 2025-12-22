<?php
// Database connection
require_once '../config.php';

// Check if user is logged in
requireLogin();

// Check if user is admin
if ($_SESSION['role'] !== 'security') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decision'])) {
    $veh_id = mysqli_real_escape_string($conn, $_POST['vehicle_id']);
    $status = $_POST['decision']; // 'Approved' or 'Rejected'
    $staff_id = $_SESSION['user_id']; 

    // Insert or Update the approval table
    $sql = "INSERT INTO approval (vehicle_id, staff_id, status) 
            VALUES ('$veh_id', '$staff_id', '$status') 
            ON DUPLICATE KEY UPDATE status='$status', staff_id='$staff_id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: security_list_vehicles.php?msg=" . $status);
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>