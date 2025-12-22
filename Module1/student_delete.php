<?php
require_once '../config.php';
session_start();

// 1. Security check: Ensure the student is the one deleting their own account
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Use the session ID to ensure they can only delete THEMSELVES
$id = $_SESSION['user_id'];

// --- STEP 1: DELETE CHILD RECORDS FIRST ---
// Delete bookings linked to this user
mysqli_query($conn, "DELETE FROM booking WHERE User_id = '$id'");

// Delete summons linked to this user
mysqli_query($conn, "DELETE FROM traffic_summon WHERE user_id = '$id'");

// Delete approvals and vehicles
mysqli_query($conn, "DELETE FROM approval WHERE vehicle_id IN (SELECT vehicle_id FROM vehicle WHERE user_id = '$id')");
mysqli_query($conn, "DELETE FROM vehicle WHERE user_id = '$id'");

// --- STEP 2: DELETE THE ACTUAL USER ---
$sql = "DELETE FROM users WHERE user_id = '$id'";

if (mysqli_query($conn, $sql)) {
    // --- STEP 3: LOGOUT AND REDIRECT ---
    session_destroy(); // Destroy the session so they are officially "logged out"
    header("Location: ../login.php?msg=account_deleted");
    exit();
} else {
    echo "Error deleting account: " . mysqli_error($conn);
}
?>