<?php
require_once '../config.php';
session_start();

// 1. Security check: Ensure the student is the one deleting their own account
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Use the session ID to ensure they can only delete THEMSELVES
$id = (int)$_SESSION['user_id'];
if ($id <= 0) {
    header("Location: ../login.php?msg=invalid_session");
    exit();
}

// --- STEP 1: DELETE CHILD RECORDS FIRST ---
// Delete bookings linked to this user
if (!mysqli_query($conn, "DELETE FROM booking WHERE User_id = $id")) {
    echo "Error deleting bookings: " . mysqli_error($conn);
    exit();
}

// Delete parking usage linked to this user
if (!mysqli_query($conn, "DELETE FROM parking_usage WHERE user_id = $id")) {
    echo "Error deleting parking usage: " . mysqli_error($conn);
    exit();
}

// Delete summons linked to this user
if (!mysqli_query($conn, "DELETE FROM traffic_summon WHERE user_id = $id")) {
    echo "Error deleting summons: " . mysqli_error($conn);
    exit();
}

// Delete approvals linked to this user's vehicles
if (!mysqli_query($conn, "DELETE FROM approval WHERE vehicle_id IN (SELECT vehicle_id FROM vehicle WHERE user_id = $id)")) {
    echo "Error deleting approvals: " . mysqli_error($conn);
    exit();
}

// Delete vehicles linked to this user
if (!mysqli_query($conn, "DELETE FROM vehicle WHERE user_id = $id")) {
    echo "Error deleting vehicles: " . mysqli_error($conn);
    exit();
}

// --- STEP 2: DELETE THE ACTUAL USER ---
$sql = "DELETE FROM users WHERE user_id = $id";

if (mysqli_query($conn, $sql)) {
    // --- STEP 3: LOGOUT AND REDIRECT ---
    session_destroy(); // Destroy the session so they are officially "logged out"
    header("Location: ../login.php?msg=account_deleted");
    exit();
} else {
    echo "Error deleting account: " . mysqli_error($conn);
}
?>