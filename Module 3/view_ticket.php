<?php
require_once '../config.php';
$id = mysqli_real_escape_string($conn, $_GET['id']);

// JOIN with the users table to get the name and username
$sql = "SELECT u.*, us.username, us.full_name 
        FROM parking_usage u 
        JOIN users us ON u.user_id = us.user_id 
        WHERE u.Usage_id = '$id'";

$res = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($res);

if (!$booking) {
    die("Booking not found.");
}

// The content for this QR is the Booking ID for verification
$qr_content = "BOOKING_REF_" . $booking['Usage_id'];
$qr_url = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($qr_content);?>

<div style="text-align: center; padding: 40px; border: 2px solid #28a745; max-width: 450px; margin: 40px auto; font-family: Arial; border-radius: 10px; background: white;">
    <h2 style="color: #28a745;">FKPark Booking Ticket</h2>
    <hr>
    
    <div style="display: flex; align-items: center; justify-content: center; gap: 20px; margin: 20px 0; text-align: left;">
        <img src="<?php echo $qr_url; ?>" alt="Booking QR" style="border: 1px solid #ddd; padding: 5px;">
        
        <div style="font-size: 16px;">
            <p><strong>Full Name:</strong><br> <?php echo htmlspecialchars($booking['full_name']); ?></p>
            <p><strong>Username:</strong><br> <?php echo htmlspecialchars($booking['username']); ?></p>
            <p><strong>Booking ID:</strong> #<?php echo $booking['Usage_id']; ?></p>
        </div>
    </div>

    <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <p><strong>Status:</strong> <span style="color: blue;"><?php echo $booking['status']; ?></span></p>
        <p><strong>Date:</strong> <?php echo $booking['usage_date']; ?></p>
        <p><strong>Time:</strong> <?php echo date('H:i', strtotime($booking['entry_time'])); ?> - <?php echo $booking['end_time']; ?></p>
    </div>

    <button onclick="window.print()" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
        Download / Print Ticket
    </button>
    <br><br>
    <a href="../dashboards/student_dashboard.php" style="color: #666; text-decoration: none; font-size: 14px;">&larr; Back to Dashboard</a>
</div>