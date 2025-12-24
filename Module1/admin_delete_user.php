<?php
require_once '../config.php';
session_start();

// Security check [cite: 50]
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Delete related records to avoid foreign key constraint errors
    $delete_summon = "DELETE FROM traffic_summon WHERE user_id = '$id'";
    mysqli_query($conn, $delete_summon);
    
    $delete_usage = "DELETE FROM parking_usage WHERE user_id = '$id'";
    mysqli_query($conn, $delete_usage);
    
    $delete_booking = "DELETE FROM booking WHERE User_id = '$id'";
    mysqli_query($conn, $delete_booking);
    
    $delete_approval = "DELETE FROM approval WHERE staff_id = '$id' OR vehicle_id IN (SELECT vehicle_id FROM vehicle WHERE user_id = '$id')";
    mysqli_query($conn, $delete_approval);
    
    // SQL to delete user [cite: 57, 638]
    $sql = "DELETE FROM users WHERE user_id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_list_users.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_list_users.php");
}
?>