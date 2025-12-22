<?php
require_once '../config.php';
// Removed session_start() because it's already in config.php

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_SESSION['user_id'];

// --- STEP 1: SOLVE THE COLUMN NAME MYSTERY ---
// We will try to delete from approval using the most likely column name.
// In your SQL, it is likely 'staff_id' or 'admin_id'.
// Let's try 'staff_id' first based on typical FKPark naming conventions.

mysqli_query($conn, "DELETE FROM approval WHERE staff_id = '$id'");

// If the above fails, you can check your phpMyAdmin for the approval table column names.
// But usually, it's staff_id.

// --- STEP 2: DELETE THE USER ---
$sql = "DELETE FROM users WHERE user_id = '$id'";

if (mysqli_query($conn, $sql)) {
    session_destroy(); 
    header("Location: ../login.php?msg=account_deleted");
    exit();
} else {
    // If it still fails, this error will tell us the EXACT column name needed
    echo "Error: " . mysqli_error($conn);
}
?>