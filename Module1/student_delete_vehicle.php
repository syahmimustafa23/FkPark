<?php
require_once '../config.php';
session_start();

// Security: Student can only delete their OWN vehicles
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = (int)$_SESSION['user_id'];
$vehicle_id = isset($_GET['vehicle_id']) ? (int)$_GET['vehicle_id'] : 0;

if ($vehicle_id <= 0) {
    header("Location: student_profile.php?msg=invalid_vehicle");
    exit();
}

// Verify this vehicle belongs to the logged-in student
$check_sql = "SELECT user_id FROM vehicle WHERE vehicle_id = $vehicle_id";
$check_res = mysqli_query($conn, $check_sql);
$vehicle = mysqli_fetch_assoc($check_res);

if (!$vehicle || $vehicle['user_id'] != $student_id) {
    header("Location: student_profile.php?msg=unauthorized");
    exit();
}

// Delete approvals linked to this vehicle first
mysqli_query($conn, "DELETE FROM approval WHERE vehicle_id = $vehicle_id");

// Delete the vehicle
$delete_sql = "DELETE FROM vehicle WHERE vehicle_id = $vehicle_id AND user_id = $student_id";
if (mysqli_query($conn, $delete_sql)) {
    header("Location: student_profile.php?msg=vehicle_deleted");
} else {
    header("Location: student_profile.php?msg=error");
}
exit();
?>