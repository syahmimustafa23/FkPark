<?php
require_once '../config.php';
session_start();

// Security: Only Admin should be allowed to delete parking infrastructure
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $area_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // This query removes the area. 
    // Foreign Key constraints in your SQL will handle the rest (Spaces, Bookings linked to spaces).
    $sql = "DELETE FROM parking_area WHERE Area_id = '$area_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Redirect back to the management page with a success message
        header("Location: admin_list_area.php?msg=area_deleted");
        exit();
    } else {
        echo "Error deleting area: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_list_area.php");
    exit();
}
?>