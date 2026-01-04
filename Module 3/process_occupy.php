<?php
require_once '../config.php';


if (isset($_POST['confirm_parking'])) {
    $user_id = $_SESSION['user_id'];
    $space_id = $_POST['space_id'];
    $usage_id = $_POST['usage_id']; // This comes from scan_qr.php if a reservation exists
    $new_start = $_POST['start_time']; 
    $new_end = $_POST['end_time'];
    $today = date('Y-m-d');
    
    // Combine date and time for the database format YYYY-MM-DD HH:MM
    $entry_datetime = $today . ' ' . $new_start;

    if (!empty($usage_id)) {
        // CASE A: UPDATE existing reservation to Occupied
        $sql = "UPDATE parking_usage SET 
                status = 'Occupied', 
                entry_time = '$entry_datetime', 
                end_time = '$new_end' 
                WHERE Usage_id = '$usage_id'";
        $final_id = $usage_id;
    } else {
        // CASE B: INSERT new walk-in session
        $sql = "INSERT INTO parking_usage (Space_id, user_id, entry_time, end_time, usage_type, status, usage_date) 
                VALUES ('$space_id', '$user_id', '$entry_datetime', '$new_end', 'Walk-in', 'Occupied', '$today')";
    }

    // Execute the query decided above
    if (mysqli_query($conn, $sql)) {
        // If it was a walk-in, we need to get the new ID
        if (empty($usage_id)) {
            $final_id = mysqli_insert_id($conn);
        }

        // UPDATE physical space to RED (Occupied) for the live map
        mysqli_query($conn, "UPDATE parking_space SET Current_status = 'Occupied' WHERE Space_id = '$space_id'");
        
        // Redirect to the ticket view so they have proof of parking
        header("Location: view_ticket.php?id=" . $final_id);
        exit();
    }
}
date_default_timezone_set('Asia/Kuala_Lumpur');
$current_time = date('H:i');



if (isset($_POST['leave_parking'])) {
    $usage_id = mysqli_real_escape_string($conn, $_POST['usage_id']);
    $space_id = mysqli_real_escape_string($conn, $_POST['space_id']);

    // We only update status to 'Completed'. 
    // We do not reference 'exit_time' to avoid the SQL error.
    $sql_usage = "UPDATE parking_usage SET 
                  status = 'Completed' 
                  WHERE Usage_id = '$usage_id'";
    
    // Reset the physical space to Available
    $sql_space = "UPDATE parking_space SET 
                  Current_status = 'Available' 
                  WHERE Space_id = '$space_id'";

    if (mysqli_query($conn, $sql_usage) && mysqli_query($conn, $sql_space)) {
        header("Location: ../dashboards/student_dashboard.php?msg=vacancy_recorded");
        exit();
    } else {
        echo "Error recording vacancy: " . mysqli_error($conn);
    }
}

?>