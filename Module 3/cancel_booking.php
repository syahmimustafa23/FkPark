<?php
require_once '../config.php';
session_start();

if (isset($_GET['id'])) {
    // Sanitize the input
    $usage_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // 1. Security Check: Pull the record to verify ownership AND get the Space_id
    $check_sql = "SELECT Space_id FROM parking_usage WHERE Usage_id = '$usage_id' AND user_id = '$user_id'";
    $res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($res) > 0) {
        // FETCH the data so we can get the Space_id
        $row = mysqli_fetch_assoc($res);
        $space_id = $row['Space_id'];

        // 2. Physical Delete
        $delete_sql = "DELETE FROM parking_usage WHERE Usage_id = '$usage_id'";
        
        if (mysqli_query($conn, $delete_sql)) {
            // 3. NOW PHP knows what $space_id is, so this will work!
            mysqli_query($conn, "UPDATE parking_space SET Current_status = 'Available' WHERE Space_id = '$space_id'");
            
            header("Location: view_bookings.php?msg=deleted");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        die("Error: Unauthorized action or record not found.");
    }
}
?>