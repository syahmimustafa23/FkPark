<?php
require_once '../config.php';


// Security: Only Admin should be allowed to delete parking infrastructure
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $area_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Start transaction for cascading delete
    mysqli_begin_transaction($conn);
    
    try {
        // First, get all spaces in this area
        $spaces_result = mysqli_query($conn, "SELECT Space_id FROM parking_space WHERE Area_id = '$area_id'");
        
        // Delete all bookings and usage records related to spaces in this area
        while ($space = mysqli_fetch_assoc($spaces_result)) {
            $space_id = $space['Space_id'];
            
            // Delete bookings
            $delete_bookings = "DELETE FROM booking WHERE Space_id = '$space_id'";
            if (!mysqli_query($conn, $delete_bookings)) {
                throw new Exception("Error deleting bookings: " . mysqli_error($conn));
            }
            
            // Delete parking usage records
            $delete_usage = "DELETE FROM parking_usage WHERE Space_id = '$space_id'";
            if (!mysqli_query($conn, $delete_usage)) {
                throw new Exception("Error deleting parking usage: " . mysqli_error($conn));
            }
        }
        
        // Delete all spaces in this area
        $delete_spaces = "DELETE FROM parking_space WHERE Area_id = '$area_id'";
        if (!mysqli_query($conn, $delete_spaces)) {
            throw new Exception("Error deleting spaces: " . mysqli_error($conn));
        }
        
        // Finally, delete the area
        $delete_area = "DELETE FROM parking_area WHERE Area_id = '$area_id'";
        if (!mysqli_query($conn, $delete_area)) {
            throw new Exception("Error deleting area: " . mysqli_error($conn));
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Redirect back to the management page with a success message
        header("Location: admin_list_area.php?msg=area_deleted");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo "Error deleting area: " . $e->getMessage();
        echo "<br><a href='admin_list_area.php'>Go Back</a>";
    }
} else {
    header("Location: admin_list_area.php");
    exit();
}
?>