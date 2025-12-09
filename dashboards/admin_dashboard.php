<?php
/**
 * FKPark Admin Dashboard
 * 
 * Simple dashboard for admin users
 * - No JavaScript, no animations, no hover effects
 * - Session-based access control
 */

require_once '../config.php';

// Check if user is logged in
requireLogin();

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #667eea; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .welcome h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #555; }
        .buttons { margin-top: 30px; text-align: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
        img{width: 100px; }
        .navbar1{text-decoration-line: none;text-decoration: none; float: right; overflow: hidden; list-style-type: none; display: flex; text-align: center; padding: 0px; margin:0px;}
        <?php
        /**
         * FKPark Admin Dashboard (minimal)
         * - No JavaScript, no hover effects
         */

        require_once '../config.php';
        requireLogin();

        if ($_SESSION['role'] !== 'admin') {
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
            <title>Admin Dashboard | FKPark</title>
            <style>
                body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px }
                header { background:#667eea; color:#fff; padding:20px; border-radius:4px; margin-bottom:20px }
                .card { max-width:800px; margin:0 auto; background:#fff; padding:20px; border-radius:4px; }
                .logout { display:inline-block; margin-top:15px; padding:8px 14px; background:#dc3545; color:#fff; text-decoration:none; border-radius:4px }
            </style>
        </head>
        <body>
            <header>
                <h1>FKPark - Admin Dashboard</h1>
            </header>
            <div class="card">
                <p>Welcome, <?php echo $username; ?>.</p>
                <p><strong>Role:</strong> Admin</p>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>

                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </body>
        </html>
}



.logo{

    width: 200px;

    height: auto;

}

td{
