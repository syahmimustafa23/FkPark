<?php
require_once 'config.php';

$users = [
    ['admin1', 'admin123', 'Admin', 'System Administrator'],
    ['student1', 'student123', 'Student', 'Ahmad Bin Zaid'],
    ['staff1', 'staff123', 'Safety_Staff', 'Officer Razak']
];

foreach ($users as $u) {
    $hashed_password = password_hash($u[1], PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, user_type, full_name) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $u[0], $hashed_password, $u[2], $u[3]);
    mysqli_stmt_execute($stmt);
}
echo "Accounts created! Now delete this file and go to login.php";
?>