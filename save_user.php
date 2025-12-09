<?php
/**
 * FKPark - Parking Management System
 * Module 1: Save User Registration
 * 
 * This file handles:
 * - Form validation for new user registration
 * - Username uniqueness check
 * - Secure password hashing using password_hash()
 * - Insert new user into database
 * - Error handling and redirection
 * 
 * Security Features:
 * - Access control (admin only)
 * - SQL Prepared Statements
 * - password_hash() with default algorithm
 * - Input validation and sanitization
 * 
 * Date: December 2025
 */

require_once 'config.php';

// =======================
// ACCESS CONTROL & REQUEST VALIDATION
// =======================

// Check if user is logged in
requireLogin();

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit();
}

// =======================
// GET AND VALIDATE INPUT
// =======================

// Get form data
$username = isset($_POST['username']) ? sanitizeInput($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$role = isset($_POST['role']) ? sanitizeInput($_POST['role']) : '';

// =======================
// VALIDATION CHECKS
// =======================

// Check for empty fields
if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
    header("Location: register.php?error=empty_fields");
    exit();
}

// Validate username length
if (strlen($username) < 3 || strlen($username) > 20) {
    header("Location: register.php?error=empty_fields");
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    header("Location: register.php?error=weak_password");
    exit();
}

// Check if passwords match
if ($password !== $confirm_password) {
    header("Location: register.php?error=weak_password");
    exit();
}

// Validate role
$valid_roles = array('admin', 'student', 'security');
if (!in_array($role, $valid_roles)) {
    header("Location: register.php?error=invalid_role");
    exit();
}

// =======================
// CHECK USERNAME UNIQUENESS
// =======================

$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");

if (!$stmt) {
    error_log("Database Prepare Error: " . $conn->error);
    header("Location: register.php?error=database_error");
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Username already exists
    error_log("Registration failed: Username already exists: " . $username);
    $stmt->close();
    header("Location: register.php?error=username_exists");
    exit();
}

$stmt->close();

// =======================
// HASH PASSWORD & INSERT USER
// =======================

// Hash the password securely
// password_hash() uses bcrypt algorithm by default
// Default cost is 10 (good balance between security and performance)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare INSERT statement
// Database `users` table uses `user_type` column for role
$stmt = $conn->prepare("INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)");

if (!$stmt) {
    error_log("Database Prepare Error: " . $conn->error);
    header("Location: register.php?error=database_error");
    exit();
}

// Bind parameters
$stmt->bind_param("sss", $username, $hashed_password, $role);

// Execute query
if ($stmt->execute()) {
    // User created successfully
    $new_user_id = $stmt->insert_id;
    
    // Log the action
    error_log("New user created: Username=" . $username . ", Role=" . $role . ", Created by Admin=" . $_SESSION['username']);
    
    $stmt->close();
    $conn->close();
    
    // Redirect to registration page with success message
    header("Location: register.php?success=user_created");
    exit();
} else {
    // Error inserting user
    error_log("Database Insert Error: " . $stmt->error);
    $stmt->close();
    $conn->close();
    
    header("Location: register.php?error=database_error");
    exit();
}

?>
