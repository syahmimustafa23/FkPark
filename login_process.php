<?php
/**
 * FKPark Login Process
 * 
 * Handles form submission from login.php
 * - Validates credentials server-side
 * - Creates session if valid
 * - Redirects with error message if invalid
 * 
 * No JavaScript, no dynamic UI
 */
session_start();
require_once 'config.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// Get form input
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Server-side validation
if (empty($username) || empty($password)) {
    header("Location: login.php?error=empty_fields");
    exit();
}

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");

if (!$stmt) {
    error_log("Database Prepare Error: " . $conn->error);
    header("Location: login.php?error=database_error");
    exit();
}

$stmt->bind_param("s", $username);

if (!$stmt->execute()) {
    error_log("Database Execute Error: " . $stmt->error);
    header("Location: login.php?error=database_error");
    exit();
}

$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 0) {
    error_log("Login failed: Username not found - " . $username);
    $stmt->close();
    $conn->close();
    header("Location: login.php?error=invalid_credentials");
    exit();
}

// Fetch user record
$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    error_log("Login failed: Invalid password for username - " . $username);
    $stmt->close();
    $conn->close();
    header("Location: login.php?error=invalid_credentials");
    exit();
}

// Password correct — create session
session_regenerate_id(true);
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
$_SESSION['login_time'] = time();

error_log("Successful login: " . $username . " (Role: " . $user['role'] . ")");

$stmt->close();
$conn->close();

// Redirect to dashboard based on role
if ($user['role'] === 'admin') {
    header("Location: dashboards/admin_dashboard.php");
} elseif ($user['role'] === 'student') {
    header("Location: dashboards/student_dashboard.php");
} elseif ($user['role'] === 'security') {
    header("Location: dashboards/security_dashboard.php");
} else {
    header("Location: login.php?error=invalid_role");
}
exit();
?>
