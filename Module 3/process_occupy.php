<?php
require_once '../config.php';
session_start();



if (isset($_POST['confirm_parking'])) {
    $user_id = $_SESSION['user_id'];
    $space_id = $_POST['space_id'];
    $usage_id = $_POST['usage_id'];
    $plate = mysqli_real_escape_string($conn, $_POST['plate_number']);
    $new_start = $_POST['start_time']; 
    $new_end = $_POST['end_time'];
    $today = date('Y-m-d');
    
    // Combine date and time for the database 
    $entry_datetime = $today . ' ' . $new_start;

    if (!empty($usage_id)) {
        // UPDATE existing reservation to Occupied 
        $sql = "UPDATE parking_usage SET 
                status = 'Occupied', 
                entry_time = '$entry_datetime', 
                end_time = '$new_end' 
                WHERE Usage_id = '$usage_id'";
    } else {
        // INSERT new walk-in session 
        $sql = "INSERT INTO parking_usage (Space_id, user_id, entry_time, end_time, usage_type, status, usage_date) 
                VALUES ('$space_id', '$user_id', '$entry_datetime', '$new_end', 'Parking', 'Occupied', '$today')";
    }

    if (mysqli_query($conn, $sql)) {
        // Set physical space to Occupied for the map 
        mysqli_query($conn, "UPDATE parking_space SET Current_status = 'Occupied' WHERE Space_id = '$space_id'");
        header("Location: ../dashboards/student_dashboard.php?msg=parked");
    }
}


if (isset($_POST['leave_parking'])) {
    $usage_id = mysqli_real_escape_string($conn, $_POST['usage_id']);
    $space_id = mysqli_real_escape_string($conn, $_POST['space_id']);

   
    $sql_usage = "UPDATE parking_usage SET status = 'Completed' WHERE Usage_id = '$usage_id'";
    
  
    $sql_space = "UPDATE parking_space SET Current_status = 'Available' WHERE Space_id = '$space_id'";

    if (mysqli_query($conn, $sql_usage) && mysqli_query($conn, $sql_space)) {
        header("Location: ../dashboards/student_dashboard.php?msg=vacancy_recorded");
        exit();
    } else {
        echo "Error recording vacancy: " . mysqli_error($conn);
    }
}
?>