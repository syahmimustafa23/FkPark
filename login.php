<?php
// Minimal, single-version login page for FKPark
// No JavaScript, no duplicated content. Form posts to login_process.php

// Read optional error/success messages from query string
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
    <style>
        html, body { height: 100%; margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f5f5f5; }
        .login-container { min-height: 100%; display:flex; align-items:center; justify-content:center; padding:20px; }
        .card { background:#fff; padding:24px; border:1px solid #e0e0e0; border-radius:6px; width:100%; max-width:420px; }
        h1 { margin:0 0 12px 0; font-size:20px; color:#333; }
        label { display:block; margin-bottom:6px; color:#333; font-size:14px }
        input[type="text"], input[type="password"] { width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; margin-bottom:12px }
        .btn { display:block; width:100%; padding:10px; background:#2b6cb0; color:white; border:none; border-radius:4px; text-align:center }
        .error { background:#fff0f0; color:#b00020; padding:8px; border:1px solid #f2c0c0; border-radius:4px; margin-bottom:12px }
        .success { background:#f0fdf4; color:#065f46; padding:8px; border:1px solid #bbf7d0; border-radius:4px; margin-bottom:12px }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <h1>FKPark — Login</h1>
            <?php if (!empty($error)): ?>
                <div class="error"><?php
                    $error_messages = [
                        'empty_fields' => 'Username and password are required.',
                        'invalid_credentials' => 'Invalid username or password.',
                        'database_error' => 'Database error. Please try again later.',
                        'session_expired' => 'Your session has expired. Please login again.',
                        'invalid_role' => 'User role is invalid.'
                    ];
                    echo $error_messages[$error] ?? htmlspecialchars($error);
                ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success">You have been logged out successfully.</div>
            <?php endif; ?>

            <form method="post" action="login_process.php" novalidate>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="150" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
        <style>
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
