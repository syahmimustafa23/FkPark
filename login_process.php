<?php


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
// Use a safe SELECT that does not assume a specific role column name
// (some DBs use `user_type`, others use `role`). We'll SELECT the whole row
// and detect the column name at runtime to avoid "unknown column" errors.
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");

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

// Determine role column dynamically to be compatible with different schemas
if (isset($user['user_type']) && !empty($user['user_type'])) {
    $detectedRole = $user['user_type'];
} elseif (isset($user['role']) && !empty($user['role'])) {
    $detectedRole = $user['role'];
} else {
    // No standard role column found — treat as invalid role
    error_log("Login failed: No role column found for user - " . $username);
    $stmt->close();
    $conn->close();
    header("Location: login.php?error=invalid_role");
    exit();
}

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
$_SESSION['role'] = $detectedRole;
$_SESSION['login_time'] = time();

error_log("Successful login: " . $username . " (Role: " . $detectedRole . ")");

$stmt->close();
$conn->close();

// Redirect to dashboard based on role
if ($detectedRole === 'admin') {
    header("Location: dashboards/admin_dashboard.php");
} elseif ($detectedRole === 'student') {
    header("Location: dashboards/student_dashboard.php");
} elseif ($detectedRole === 'security') {
    header("Location: dashboards/security_dashboard.php");
} else {
    header("Location: login.php?error=invalid_role");
}
exit();
?>
