<?php
/**
 * FKPark - Parking Management System
 * Configuration File - Database Connection
 * 
 * This file handles all database connections and configurations
 * Uses MySQLi procedural approach for secure database operations
 * 
 * Date: December 2025
 */

// =======================
// DATABASE CONFIGURATION
// =======================

// XAMPP Local Server Credentials
define('DB_HOST', 'localhost');      // MySQL server host
define('DB_USER', 'root');           // MySQL username (XAMPP default)
define('DB_PASS', '');               // MySQL password (XAMPP default - empty)
define('DB_NAME', 'fkpark');         // Database name

// =======================
// CONNECTION & ERROR HANDLING
// =======================

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Log error (in production, don't show details to users)
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Connection Error: Unable to connect to database. Please contact administrator.");
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");

// =======================
// SECURITY HEADERS
// =======================

// Prevent clickjacking
header("X-Frame-Options: SAMEORIGIN");

// Prevent MIME type sniffing
header("X-Content-Type-Options: nosniff");

// Enable XSS protection
header("X-XSS-Protection: 1; mode=block");

// =======================
// SESSION CONFIGURATION
// =======================

// Session security settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_lifetime', 3600);      // 1 hour session timeout
    // Align server-side session storage lifetime with cookie lifetime
    ini_set('session.gc_maxlifetime', 3600);       // 1 hour (seconds)

    session_start();

    // Application-wide session timeout constant (seconds)
    if (!defined('SESSION_TIMEOUT')) {
        define('SESSION_TIMEOUT', 3600);
    }
}

// =======================
// UTILITY FUNCTIONS
// =======================

/**
 * Check if user is logged in
 * 
 * @return bool True if user session exists and is valid
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['role']);
}

/**
 * Redirect to login if not authenticated
 * 
 * @return void Redirects to login.php if not logged in
 */
function requireLogin() {
    // Redirect if not logged in
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }

    // Enforce inactivity timeout (absolute inactivity)
    $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : (int) ini_get('session.cookie_lifetime');

    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
        // Session expired due to inactivity - destroy session and redirect
        $_SESSION = array();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        header("Location: ../login.php?error=session_expired");
        exit();
    }

    // Refresh activity timestamp to implement sliding expiration
    $_SESSION['login_time'] = time();

    // Refresh cookie expiration so the browser keeps the session alive on activity
    // Note: preserves HTTPOnly and path settings
    setcookie(session_name(), session_id(), time() + $timeout, "/");
}

/**
 * Redirect to specific dashboard based on user role
 * 
 * @param string $userRole User's role (admin, student, security)
 * @return void
 */
function redirectToDashboard($userRole) {
    switch($userRole) {
        case 'admin':
            header("Location: dashboards/admin_dashboard.php");
            break;
        case 'student':
            header("Location: dashboards/student_dashboard.php");
            break;
        case 'security':
            header("Location: dashboards/security_dashboard.php");
            break;
        default:
            header("Location: login.php");
    }
    exit();
}

/**
 * Check if user is already logged in
 * If yes, redirect to appropriate dashboard
 * 
 * @return void
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        redirectToDashboard($_SESSION['role']);
    }
}

/**
 * Sanitize user input to prevent XSS attacks
 * 
 * @param string $input User input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

?>
