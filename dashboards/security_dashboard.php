<?php
/**
 * FKPark Security Dashboard
 * Simple security interface with no animations
 */

require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'security') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #fd7e14; color: white; padding: 20px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .welcome h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #555; }
        .buttons { margin-top: 30px; text-align: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
    </style>
</head>
<body>
    <header>
        <h1>FKPark - Security Dashboard</h1>
    </header>
    <div class="container">
        <div class="welcome">
            <h2>Welcome, <?php echo $username; ?>!</h2>
            <p>You are logged in as Security Staff</p>
        </div>
        <div class="user-info">
            <strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?> |
            <strong>Role:</strong> Security
        </div>
        <p>Security dashboard ready for development. Future features: parking monitoring, violation management, vehicle verification.</p>
        <div class="buttons">
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>
