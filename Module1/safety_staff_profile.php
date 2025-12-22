<!-- <?php
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

if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    mysqli_query($conn, "UPDATE users SET full_name='$name' WHERE user_id='$user_id'");
}

if (isset($_POST['delete_account'])) {
    mysqli_query($conn, "DELETE FROM users WHERE user_id='$user_id'");
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'"));
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
<h2>Safety Staff Profile</h2>
    <form method="POST">
        <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>">
        <button type="submit" name="update_profile">Save Changes</button>
        <button type="submit" name="delete_account" style="background:none; border:none; color:grey; cursor:pointer;">Delete My Staff Account</button>
    </form>
    </div>
</body>
</html> -->
