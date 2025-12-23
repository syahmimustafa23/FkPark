<?php
require_once '../config.php';


requireLogin();
if ($_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['reserve'])) {
    $user_id = $_SESSION['user_id'];
    $area_id = $_POST['area_id'];
    $date = $_POST['book_date'];
    $new_start = $_POST['start_time']; 
    $new_end = $_POST['end_time'];
    $specific_space_id = $_POST['space_id'] ?? null;

    // 1. One active booking restriction
    $check_active = mysqli_query($conn, "SELECT * FROM parking_usage 
                                         WHERE user_id = '$user_id' 
                                         AND status IN ('Reserved', 'Occupied')");
    
    if (mysqli_num_rows($check_active) > 0) {
        die("<script>alert('You already have an active booking!'); window.history.back();</script>");
    }

    // 2. REFINED CLASH DETECTION
    // If a student clicked a specific spot (B01), we ONLY check that spot.
    // If they used the general form, we check the whole area.
    $space_filter = $specific_space_id ? "AND Space_id = '$specific_space_id'" : "AND Area_id = '$area_id'";

    $find_space_sql = "SELECT Space_id FROM parking_space 
                       WHERE Current_status = 'Available' 
                       $space_filter
                       AND Space_id NOT IN (
                           SELECT Space_id FROM parking_usage 
                           WHERE usage_date = '$date' 
                           AND status IN ('Reserved', 'Occupied')
                           AND (
                               -- Standard Overlap Logic: 
                               -- New Start is before Existing End AND New End is after Existing Start
                               ('$new_start' < end_time AND '$new_end' > TIME(entry_time))
                           )
                       ) LIMIT 1";

    $result = mysqli_query($conn, $find_space_sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $space_id = $row['Space_id'];
        $entry_datetime = $date . ' ' . $new_start;
        
        $sql = "INSERT INTO parking_usage (Space_id, user_id, entry_time, end_time, usage_type, status, usage_date) 
                VALUES ('$space_id', '$user_id', '$entry_datetime', '$new_end', 'Parking', 'Reserved', '$date')";
        
        if (mysqli_query($conn, $sql)) {
    $new_id = mysqli_insert_id($conn);
    
    // UPDATE the physical space to 'Reserved' so the color changes in student_view
    mysqli_query($conn, "UPDATE parking_space SET Current_status = 'Reserved' WHERE Space_id = '$space_id'");
    
    header("Location: view_ticket.php?id=" . $new_id); 
    exit();
}
    } else {
        die("<script>alert('Clash detected! This space is already reserved for the selected time.'); window.history.back();</script>");
    }
}

?>
