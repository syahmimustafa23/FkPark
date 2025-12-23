<?php
require_once '../config.php';


$space_id = mysqli_real_escape_string($conn, $_GET['space_id']);
$user_id = $_SESSION['user_id'];

// Fetch Space and User Plate 
$space = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, a.Area_name FROM parking_space s JOIN parking_area a ON s.Area_id = a.Area_id WHERE s.Space_id = '$space_id'"));
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT license_plate FROM vehicle WHERE user_id = '$user_id'"));

// Check for existing 'Reserved' booking 
$today = date('Y-m-d');
$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM parking_usage WHERE user_id = '$user_id' AND Space_id = '$space_id' AND usage_date = '$today' AND status = 'Reserved'"));
?>

<!DOCTYPE html>
<html>
<head><title>Start Parking | FKPark</title></head>
<body style="font-family: Arial; padding: 40px; background: #f4f4f4;">
    <div style="max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px;">
        <h2>Confirm Your Parking</h2>
        <hr>
        <form action="process_occupy.php" method="POST">
            <input type="hidden" name="space_id" value="<?php echo $space_id; ?>">
            <input type="hidden" name="usage_id" value="<?php echo $booking['Usage_id'] ?? ''; ?>">

            <p>Vehicle: <strong><?php echo $user_data['license_plate']; ?></strong></p>
            <input type="hidden" name="license_plate" value="<?php echo $user_data['license_plate']; ?>">

            <label>Start Time (Now):</label><br>
            <input type="time" name="start_time" value="<?php echo date('H:i'); ?>" required style="width:100%; padding:10px; margin:10px 0;"><br>

            <label>Expected End Time:</label><br>
            <input type="time" name="end_time" value="<?php echo $booking['end_time'] ?? ''; ?>" required style="width:100%; padding:10px; margin:10px 0;"><br>

            <button type="submit" name="confirm_parking" style="width:100%; padding:15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Confirm & Park
            </button>
        </form>
    </div>
</body>
</html>