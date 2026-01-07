<?php
require_once '../config.php';
// Ensure session is started to get user_id

$space_id = mysqli_real_escape_string($conn, $_GET['space_id']);
$user_id = $_SESSION['user_id'];

// 1. FETCH SPACE PHYSICAL STATUS FIRST (Crucial for blocking Student 5)
$space_query = mysqli_query($conn, "SELECT s.*, a.Area_name FROM parking_space s JOIN parking_area a ON s.Area_id = a.Area_id WHERE s.Space_id = '$space_id'");
$space = mysqli_fetch_assoc($space_query);

// CHECK IF PHYSICALLY OCCUPIED
if ($space['Current_status'] == 'Occupied') {
    die("<script>alert('Error: This space is currently occupied by another vehicle.'); window.location.href='../Module2/student_view.php';</script>");
}

// 2. Fetch User Plate 
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT license_plate FROM vehicle WHERE user_id = '$user_id'"));

$today = date('Y-m-d');

// 3. CHECK IF RESERVED BY SOMEONE ELSE
$check_reservation = mysqli_query($conn, "SELECT * FROM parking_usage 
                                          WHERE Space_id = '$space_id' 
                                          AND usage_date = '$today' 
                                          AND status = 'Reserved' 
                                          AND user_id != '$user_id'");

if (mysqli_num_rows($check_reservation) > 0) {
    die("<script>alert('Error: This space is reserved by another student.'); window.location.href='../Module2/student_view.php';</script>");
}

// 4. Check for current user's own 'Reserved' booking to pre-fill the form
$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM parking_usage WHERE user_id = '$user_id' AND Space_id = '$space_id' AND usage_date = '$today' AND status = 'Reserved'"));

// Inside scan_qr.php
$user_id = $_SESSION['user_id'];

// NEW: Check if the student already has an 'Occupied' session somewhere else
$active_check = mysqli_query($conn, "SELECT u.*, s.Space_num 
                                     FROM parking_usage u 
                                     JOIN parking_space s ON u.Space_id = s.Space_id 
                                     WHERE u.user_id = '$user_id' 
                                     AND u.status = 'Occupied'");

if (mysqli_num_rows($active_check) > 0) {
    $current_parked = mysqli_fetch_assoc($active_check);
    $parked_spot = $current_parked['Space_num'];
    
    // Block the user and tell them where they are currently parked
    die("<script>
        alert('Error: You are already parked at spot $parked_spot. You must check-out from your current spot before occupying a new one.');
        window.location.href='../Module 3/view_bookings.php';
    </script>");
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="styles.css">    
<title>Start Parking | FKPark</title></head>
<body style="font-family: Arial; padding: 40px; background: #f4f4f4;">
    <div style="max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px;">
        <h2>Confirm Your Parking</h2>
        <p>Space: <strong><?php echo $space['Space_num']; ?> (<?php echo $space['Area_name']; ?>)</strong></p>
        <hr>
        <form action="process_occupy.php" method="POST">
            <input type="hidden" name="space_id" value="<?php echo $space_id; ?>">
            <input type="hidden" name="usage_id" value="<?php echo $booking['Usage_id'] ?? ''; ?>">

            <p>Vehicle: <strong><?php echo $user_data['license_plate'] ?? 'No Plate Found'; ?></strong></p>
            <input type="hidden" name="license_plate" value="<?php echo $user_data['license_plate'] ?? ''; ?>">


           <?php date_default_timezone_set('Asia/Kuala_Lumpur'); ?>

<label>Start Time (Set to Current Time):</label><br>
<input type="time" name="start_time" 
       value="<?php echo date('H:i'); ?>" 
       readonly 
       style="width:100%; padding:10px; margin:10px 0; background-color: #e9ecef; cursor: not-allowed;"><br>

<label>Expected End Time (Choose when you will leave):</label><br>
<input type="time" id="end_time" name="end_time" 
       value="<?php echo $booking['end_time'] ?? ''; ?>" 
       required 
       style="width:100%; padding:10px; margin:10px 0;"><br>

            <button type="submit" name="confirm_parking" style="width:100%; padding:15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Confirm & Park
            </button>
        </form>
    </div>
    <script>
    const endTimeInput = document.getElementById('end_time');

    endTimeInput.addEventListener('change', function() {
        const now = new Date();
        const currentTime = now.getHours().toString().padStart(2, '0') + ":" + 
                            now.getMinutes().toString().padStart(2, '0');

        if (this.value <= currentTime) {
            alert("Error: End time must be later than the current start time.");
            this.value = ""; // Clear the invalid time
        }
    });
</script>
</body>
</html>