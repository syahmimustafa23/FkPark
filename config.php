<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
// CHANGE THESE VALUES WHEN DEPLOYING TO LIVE SERVER
$db_host = "localhost";      // Change to your live server host if needed
$db_user = "root";           // Change to your live server username
$db_pass = "";               // Change to your live server password
$db_name = "fkpark";         // Change to your live server database name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

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

/**
 * LOCAL vs LIVE SERVER CONFIG GUIDE:
 * 
 * LOCAL (XAMPP - Now):
 * $db_host = "localhost"
 * $db_user = "root"
 * $db_pass = ""
 * $db_name = "fkpark"
 * 
 * LIVE SERVER (Example from Hostinger):
 * $db_host = "mysql.parkingapp.com"
 * $db_user = "u1234567_user"
 * $db_pass = "YourSecurePassword123"
 * $db_name = "u1234567_fkpark"
 * 
 * Just change the 4 variables above and FTP upload this file!
 */

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