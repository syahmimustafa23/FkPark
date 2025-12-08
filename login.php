<?php
// Simple, minimal login page for FKPark
// - No JavaScript
// - Clean, centered form
// - Plain error messages via GET parameters

// Read optional error/success message from query string
$error = '';
$success = '';

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
}
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login | FKPark</title>
    <!-- Minimal Bootstrap for basic styling (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Center the form vertically and horizontally */
        html, body {
            height: 100%;
            margin: 0;
            background: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
        }
        .login-container {
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 24px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .login-title {
            margin-bottom: 18px;
            font-size: 20px;
            font-weight: 600;
            color: #333333;
        }
        .form-label {
            font-size: 14px;
            color: #333333;
            margin-bottom: 6px;
        }
        .btn-primary {
            width: 100%;
        }
        .error-msg {
            margin-bottom: 12px;
            color: #b00020;
            background: #fff0f0;
            padding: 8px 10px;
            border: 1px solid #f2c0c0;
            border-radius: 4px;
            font-size: 14px;
        }
        .small-note {
            margin-top: 12px;
            font-size: 13px;
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-title">FKPark — Login</div>

            <?php if (!empty($error)): ?>
                <div class="error-msg" role="alert">
                    <?php
                    // Map error codes to user-friendly messages
                    $error_messages = [
                        'empty_fields' => 'Username and password are required.',
                        'invalid_credentials' => 'Invalid username or password.',
                        'database_error' => 'Database error. Please try again later.',
                        'session_expired' => 'Your session has expired. Please login again.',
                        'invalid_role' => 'User role is invalid.'
                    ];
                    
                    $message = $error_messages[$error] ?? htmlspecialchars($error);
                    echo $message;
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div style="margin-bottom: 12px; color: #065f46; background: #f0fdf4; padding: 8px 10px; border: 1px solid #bbf7d0; border-radius: 4px; font-size: 14px;" role="alert">
                    You have been logged out successfully.
                </div>
            <?php endif; ?>

            <form method="post" action="login_process.php" novalidate>
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" maxlength="150" required>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <!-- Submit -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>

            
        </div>
    </div>

    <!-- No JavaScript included as requested -->
</body>
</html>
<?php
/**
 * FKPark - Parking Management System
 * Module 1: Login Page
 * 
 * This page displays the login form for all user roles
 * - Admin (FK Staff)
 * - Student (Car/Motorcycle Owner)
 * - Security (Safety Management Unit)
 * 
 * Features:
 * - Bootstrap responsive design
 * - Session validation (logged-in users cannot see this page)
 * - Form validation messages
 * - Secure password input
 * 
 * Date: December 2025
 */
session_start();
require_once 'config.php';

// Redirect to dashboard if already logged in
redirectIfLoggedIn();

// Get error message from URL if login failed
$error_message = '';
if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'invalid_credentials':
            $error_message = "Invalid username or password. Please try again.";
            break;
        case 'empty_fields':
            $error_message = "Username and password are required.";
            break;
        case 'session_error':
            $error_message = "Session error. Please try again.";
            break;
        default:
            $error_message = "An error occurred. Please try again.";
    }
}

// Get success message if redirected from registration
$success_message = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'user_created') {
        $success_message = "User account created successfully. Please log in.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FKPark - Login | Parking Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Additional inline styles for login page */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #667eea;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-label {
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #6c757d;
        }
        
        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .alert-dismissible .btn-close {
            padding: 0.5rem;
        }
        
        .user-roles-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
            font-size: 13px;
        }
        
        .user-roles-info strong {
            color: #667eea;
            display: block;
            margin-bottom: 8px;
        }
        
        .role-item {
            margin-bottom: 6px;
            color: #555;
        }
        
        .role-item i {
            width: 20px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1><i class="fas fa-parking"></i> FKPark</h1>
                <p>Parking Management System</p>
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
            
            <!-- User Roles Information -->
            <div class="user-roles-info">
                <strong><i class="fas fa-info-circle"></i> Test Credentials</strong>
                <div class="role-item"><i class="fas fa-user-tie"></i> <strong>Admin:</strong> admin / admin123</div>
                <div class="role-item"><i class="fas fa-user-graduate"></i> <strong>Student:</strong> student1 / student123</div>
                <div class="role-item"><i class="fas fa-user-shield"></i> <strong>Security:</strong> security1 / security123</div>
            </div>
            
            <!-- Login Form -->
            <form action="login_process.php" method="POST" novalidate>
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
                        placeholder="Enter your username"
                        required
                        autocomplete="username"
                    >
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
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <!-- Footer Information -->
            <div class="login-footer">
                <p>Need an account? <a href="#"><i class="fas fa-question-circle"></i> Contact Admin</a></p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Focus on username field on page load
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>
