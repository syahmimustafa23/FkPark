<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
// Always use localhost for database - it works from any network access
$conn = mysqli_connect("localhost", "root", "", "fkpark");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/**
 * Security function used by your dashboards
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php?error=unauthorized");
        exit();
    }
}

// --- SESSION SECURITY: 1 MINUTE TIMEOUT ---
$timeout_duration = 60; 
if (isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../login.php?error=timeout");
        exit();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();
?>