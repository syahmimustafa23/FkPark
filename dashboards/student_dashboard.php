<?php
/**
 * FKPark Student Dashboard (minimal)
 */
require_once '../config.php';
requireLogin();
if ($_SESSION['role'] !== 'student') {
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
    <title>Student Dashboard | FKPark</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px }
        header { background:#28a745; color:#fff; padding:20px; border-radius:4px; margin-bottom:20px }
        .card { max-width:800px; margin:0 auto; background:#fff; padding:20px; border-radius:4px }
        .logout { display:inline-block; margin-top:15px; padding:8px 14px; background:#dc3545; color:#fff; text-decoration:none; border-radius:4px }
    </style>
</head>
<body>
    <header>
        <h1>FKPark - Student Dashboard</h1>
    </header>
    <div class="card">
        <p>Welcome, <?php echo $username; ?>.</p>
        <p><strong>Role:</strong> Student</p>
        <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>

        <a href="../logout.php" class="logout">Logout</a>
    </div>
</body>
</html>



