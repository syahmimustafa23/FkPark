<?php
/**
 * FKPark - Parking Management System
 * Module 1: User Registration Form
 * 
 * This page displays the user registration form
 * Features:
 * - Admin-only access
 * - Form for creating new users (admin, student, security)
 * - Role selection dropdown
 * - Client-side and server-side validation
 * - Bootstrap responsive design
 * 
 * Access Control:
 * - Only logged-in admins can access
 * - Redirects unauthorized users
 * 
 * Date: December 2025
 */

require_once 'config.php';

// =======================
// ACCESS CONTROL
// =======================

// Check if user is logged in
requireLogin();

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    // Redirect unauthorized users
    header("Location: login.php");
    exit();
}

// Get admin username for display
$admin_name = htmlspecialchars($_SESSION['username']);

// Get error/success messages from URL if present
$error_message = '';
$success_message = '';

if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'username_exists':
            $error_message = "Username already exists. Please choose a different username.";
            break;
        case 'empty_fields':
            $error_message = "All fields are required.";
            break;
        case 'invalid_role':
            $error_message = "Invalid role selected.";
            break;
        case 'weak_password':
            $error_message = "Password must be at least 6 characters long.";
            break;
        case 'database_error':
            $error_message = "Database error. Please try again.";
            break;
        default:
            $error_message = "An error occurred. Please try again.";
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'user_created') {
        $success_message = "User account created successfully!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User | FKPark</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-custom .navbar-brand {
            font-weight: bold;
            font-size: 24px;
            color: white !important;
        }
        
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            margin-left: 20px;
        }
        
        .navbar-custom .nav-link:hover {
            color: white !important;
        }
        
        .register-container {
            padding: 40px 0;
        }
        
        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 35px;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h2 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-label {
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
            margin-top: 10px;
        }
        
        .btn-back:hover {
            background: #5a6268;
            color: white;
        }
        
        .password-strength {
            font-size: 12px;
            margin-top: 5px;
        }
        
        .password-strength.weak {
            color: #dc3545;
        }
        
        .password-strength.medium {
            color: #ffc107;
        }
        
        .password-strength.strong {
            color: #28a745;
        }
        
        .role-description {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="login.php">
                <i class="fas fa-parking"></i> FKPark
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user-tie"></i> <?php echo $admin_name; ?> (Admin)
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Register Container -->
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="register-header">
                <h2><i class="fas fa-user-plus"></i> Create New User</h2>
                <p>Register a new system user</p>
            </div>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Error Message -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Registration Form -->
            <form action="save_user.php" method="POST" novalidate id="registerForm">
                <!-- Username Field -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="username" 
                        name="username" 
                        placeholder="Enter username (3-20 characters)"
                        required
                        minlength="3"
                        maxlength="20"
                    >
                    <small class="form-text text-muted">Username must be 3-20 characters</small>
                </div>
                
                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Enter password (minimum 6 characters)"
                        required
                        minlength="6"
                    >
                    <div class="password-strength" id="passwordStrength"></div>
                    <small class="form-text text-muted">Password must be at least 6 characters</small>
                </div>
                
                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Confirm password"
                        required
                    >
                </div>
                
                <!-- Role Selection -->
                <div class="form-group">
                    <label for="role" class="form-label">
                        <i class="fas fa-shield-alt"></i> User Role
                    </label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="admin">Admin (FK Staff)</option>
                        <option value="student">Student (Car/Motorcycle Owner)</option>
                        <option value="security">Security (Safety Management Unit)</option>
                    </select>
                    <div class="role-description" id="roleDescription"></div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-register">
                    <i class="fas fa-plus"></i> Create User
                </button>
                
                <!-- Back Button -->
                <a href="dashboards/admin_dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            let strength = 'weak';
            if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password)) {
                strength = 'strong';
            } else if (password.length >= 6) {
                strength = 'medium';
            }
            
            const strengthText = {
                'weak': '<i class="fas fa-circle"></i> Weak password',
                'medium': '<i class="fas fa-circle"></i> Medium strength',
                'strong': '<i class="fas fa-circle"></i> Strong password'
            };
            
            strengthDiv.className = 'password-strength ' + strength;
            strengthDiv.innerHTML = strengthText[strength];
        });
        
        // Role description
        document.getElementById('role').addEventListener('change', function() {
            const descriptions = {
                'admin': '👨‍💼 Admin can manage users and system settings',
                'student': '👨‍🎓 Student can book parking slots and manage bookings',
                'security': '👮 Security staff can monitor parking and report violations'
            };
            
            const roleDesc = document.getElementById('roleDescription');
            if (this.value && descriptions[this.value]) {
                roleDesc.innerHTML = descriptions[this.value];
            } else {
                roleDesc.innerHTML = '';
            }
        });
        
        // Form submission validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
        
        // Auto-dismiss alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
