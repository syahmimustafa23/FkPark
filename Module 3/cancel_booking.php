<?php
require_once '../config.php';
session_start();

if (isset($_GET['id'])) {
    // Sanitize the input
    $usage_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // 1. Security Check: Ensure the record belongs to the logged-in student
    $check_sql = "SELECT * FROM parking_usage WHERE Usage_id = '$usage_id' AND user_id = '$user_id'";
    $res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($res) > 0) {
        // 2. Physical Delete: Remove regardless of 'Reserved', 'Occupied', or 'Completed'
        $delete_sql = "DELETE FROM parking_usage WHERE Usage_id = '$usage_id'";
        
        if (mysqli_query($conn, $delete_sql)) {
            header("Location: view_bookings.php?msg=deleted");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        // This triggers if the Usage_id doesn't exist or belongs to another student
        die("Error: Unauthorized action or record not found.");
    }
}
?>