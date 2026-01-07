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
$user_id = $_SESSION['user_id'];

// NEW: Capture the specific Space_id from the URL (passed when student clicks 'Book Now')
$selected_space_id = $_GET['space_id'] ?? null;

// Fetch the area and space number if a space was selected
$space_info = null;
if ($selected_space_id) {
    $space_res = mysqli_query($conn, "SELECT s.*, a.Area_name 
                                      FROM parking_space s 
                                      JOIN parking_area a ON s.Area_id = a.Area_id 
                                      WHERE s.Space_id = '$selected_space_id'");
    $space_info = mysqli_fetch_assoc($space_res);
}
// Fetch only Student-category areas for the dropdown
$areas = mysqli_query($conn, "SELECT * FROM parking_area WHERE Category = 'Student'");?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Student Dashboard  | FKPark</title>
    
</head>
<body>
    <header>
        
        <div class="navbar1">
            <a href="../dashboards/student_dashboard.php">Home</a>
            <a href="../Module1/student_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
    <div class="sidebar">
        <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
        <a class="sidebar2" href="../Module2/student_view.php">View Parking</a>
        <a class="sidebar2" href="../Module 3/view_bookings.php">View Booking</a>
        <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
    </div>

    </div>
   
    <div class="container">
       <h2>Reserve a Parking Slot</h2>
        <hr style="margin: 15px 0;">
    
        <form action="process_booking.php" method="POST">
            <input type="hidden" name="space_id" value="<?php echo $selected_space_id; ?>">

            <?php if ($space_info): ?>
                <div style="background: #e7f3ff; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    Selected: <strong><?php echo $space_info['Area_name']; ?> - <?php echo $space_info['Space_num']; ?></strong>
                </div>
            <?php else: ?>
                <label>Select Parking Block:</label>
                <select name="area_id" required style="width: 100%; padding: 10px; margin: 10px 0;">
                    <?php while($a = mysqli_fetch_assoc($areas)): ?>
                        <option value="<?php echo $a['Area_id']; ?>"><?php echo $a['Area_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            <?php endif; ?>

            <label>Date of Booking:</label>
            <input type="date" name="book_date" min="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 10px; margin: 10px 0;">

            <label>Start Time:</label>
            <input type="time" name="start_time" required style="width: 100%; padding: 10px; margin: 10px 0;">

            <label>End Time:</label>
            <input type="time" name="end_time" required style="width: 100%; padding: 10px; margin: 10px 0;">

            <button type="submit" name="reserve" style="background: #28a745; color: white; padding: 15px; width: 100%; border: none; cursor: pointer; border-radius: 4px;">
                Check Availability & Reserve
            </button>
        </form>
    </div>
     
</body>
</html>











