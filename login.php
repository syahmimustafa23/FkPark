<?php
require_once 'config.php';


$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $selected_role = $_POST['user_type']; // From the dropdown

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($user = mysqli_fetch_assoc($result)) {
        // Verify password and ensure the role matches the database 
        if (password_verify($password, $user['password']) && $user['user_type'] == $selected_role) {
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Redirect based on database role
            if ($user['user_type'] == 'Admin') {
                $_SESSION['role'] = 'admin';
                header("Location: dashboards/admin_dashboard.php");
            } elseif ($user['user_type'] == 'Student') {
                $_SESSION['role'] = 'student';
                header("Location: dashboards/student_dashboard.php");
            } elseif ($user['user_type'] == 'Safety_Staff') {
                $_SESSION['role'] = 'security';
                header("Location: dashboards/security_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid Credentials or Incorrect Role Selected";
        }
    } else {
        $error = "Invalid Login Credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FKPark — Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('./images/fkbackground.png');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        /* Dark overlay for better form visibility */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            z-index: 1;
            pointer-events: none;
        }

        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 32px;
            color: #222;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .login-header p {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .error-message {
            background-color: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #c33;
            text-align: center;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            color: #333;
            font-family: inherit;
        }

        select{
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            color: #333;
            font-family: inherit;
        }

        .form-group input::placeholder {
            color: #aaa;
            font-weight: 400;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2b6cb0;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.1);
        }

        .form-group input:hover:not(:focus) {
            border-color: #bbb;
        }

        .login-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2b6cb0 0%, #1e4d7f 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(43, 108, 176, 0.3);
            margin-top: 12px;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #1e4d7f 0%, #153657 100%);
            box-shadow: 0 6px 20px rgba(43, 108, 176, 0.4);
            transform: translateY(-2px);
        }

        .login-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(43, 108, 176, 0.3);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 26px;
            }

            .form-group input {
                padding: 11px 14px;
                font-size: 14px;
            }

            .login-btn {
                padding: 12px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>FKPark</h1>
                <p>Parking Management System</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        autocomplete="username"
                        placeholder="Enter your username"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    >
                </div>

                <div class="form-group">
    <label for="user_type">Role</label>
    <select id="user_type" name="user_type" required>
        <option value="Admin">Admin</option>
        <option value="Safety_Staff">Safety Security Management Staff</option>
        <option value="Student">Student</option>
    </select>
</div>

                <button type="submit" name="login" class="login-btn">Login</button>
            </form>

            <div class="login-footer">
                <p>© 2025 FKPark. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>