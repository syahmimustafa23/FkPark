<?php
/**
 * FKPark - Parking Management System
 * Project Index / Welcome Page
 * 
 * This page provides quick access to main application components
 * and displays important project information.
 * 
 * Date: December 2025
 */

// Check if user is already logged in
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : '';
$role = $isLoggedIn ? htmlspecialchars($_SESSION['role']) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FKPark - Welcome</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .welcome-container {
            background: white;
            border-radius: 15px;
            padding: 50px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #6c757d;
            font-size: 18px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .feature-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        
        .feature-card i {
            font-size: 36px;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .feature-card h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .feature-card p {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }
        
        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #13e494ff 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #17a2b8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .info-box strong {
            color: #0c5460;
        }
        
        .credentials {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .credential-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .credential-row:last-child {
            border-bottom: none;
        }
        
        .role-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-admin {
            background: #667eea;
            color: white;
        }
        
        .badge-student {
            background: #28a745;
            color: white;
        }
        
        .badge-security {
            background: #fd7e14;
            color: white;
        }
        
        .user-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-parking"></i> FKPark</h1>
            <p>Fakulti Komputeran Parking Management System</p>
        </div>
        
        <!-- User Info (if logged in) -->
        <?php if ($isLoggedIn): ?>
            <div class="user-info">
                <i class="fas fa-check-circle"></i> 
                <strong>Welcome, <?php echo $username; ?>!</strong> You are logged in as 
                <span class="role-badge badge-<?php echo $role; ?>"><?php echo ucfirst($role); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Info Box -->
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>Module 1: Authentication & User Management</strong> is now complete and ready to use.
            This module provides secure login, user registration, and role-based access control.
        </div>
        
        <!-- Features -->
        <h2 style="text-align: center; margin-bottom: 30px; color: #333;">Key Features</h2>
        <div class="features">
            <div class="feature-card">
                <i class="fas fa-lock"></i>
                <h3>Secure Login</h3>
                <p>Bcrypt password hashing and SQL injection prevention</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Three User Roles</h3>
                <p>Admin, Student, and Security with role-based dashboards</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Session Management</h3>
                <p>Secure session handling with timeout and validation</p>
            </div>
            
            <div class="feature-card">
                <i class="fas fa-mobile-alt"></i>
                <h3>Responsive Design</h3>
                <p>Bootstrap UI that works on mobile, tablet, and desktop</p>
            </div>
        </div>
        
        <!-- Test Credentials -->
        <h2 style="color: #333; margin-bottom: 20px;">Test Credentials</h2>
        <div class="credentials">
            <div class="credential-row">
                <strong>Admin User:</strong>
                <div>
                    Username: <code>admin</code> | Password: <code>admin123</code>
                    <span class="role-badge badge-admin">Admin</span>
                </div>
            </div>
            <div class="credential-row">
                <strong>Student User:</strong>
                <div>
                    Username: <code>student1</code> | Password: <code>student123</code>
                    <span class="role-badge badge-student">Student</span>
                </div>
            </div>
            <div class="credential-row">
                <strong>Security User:</strong>
                <div>
                    Username: <code>security1</code> | Password: <code>security123</code>
                    <span class="role-badge badge-security">Security</span>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <h2 style="color: #333; margin-bottom: 20px;">Quick Access</h2>
        <div class="buttons">
            <?php if ($isLoggedIn): ?>
                <!-- User is logged in - show dashboard and logout -->
                <?php
                    $dashboardUrl = match($role) {
                        'admin' => 'dashboards/admin_dashboard.php',
                        'student' => 'dashboards/student_dashboard.php',
                        'security' => 'dashboards/security_dashboard.php',
                        default => 'login.php'
                    };
                ?>
                <a href="<?php echo $dashboardUrl; ?>" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
                <a href="logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            <?php else: ?>
                <!-- User not logged in - show login button -->
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="login.php" class="btn btn-secondary">
                    <i class="fas fa-question-circle"></i> Try Demo
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Documentation Links -->
        <h2 style="color: #333; margin-bottom: 20px; margin-top: 40px;">Documentation</h2>
        <div class="buttons">
            <a href="README.md" class="btn btn-secondary" download>
                <i class="fas fa-book"></i> README
            </a>
            <a href="TEST_PLAN.md" class="btn btn-secondary" download>
                <i class="fas fa-vial"></i> Test Plan
            </a>
            <a href="INSTALL.md" class="btn btn-secondary" download>
                <i class="fas fa-tools"></i> Installation
            </a>
            <a href="DELIVERY_SUMMARY.md" class="btn btn-secondary" download>
                <i class="fas fa-check"></i> Summary
            </a>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>FKPark Module 1 - Authentication & User Management | Version 1.0.0 | December 2025</p>
            <p>Secure • Professional • Ready for Production</p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
