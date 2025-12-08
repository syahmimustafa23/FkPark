<?php
/**
 * FKPark Logout
 * Destroys session and redirects to login page
 */

require_once 'config.php';

// Log logout if user was logged in
if (isLoggedIn()) {
    error_log("User logged out: " . $_SESSION['username'] . " (ID: " . $_SESSION['user_id'] . ")");
}

// Destroy session
$_SESSION = array();
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Cache control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login
header("Location: login.php?success=logged_out");
exit();
