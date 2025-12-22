<?php
require_once '../config.php';
session_start();

// Security check [cite: 50]
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // SQL to delete user [cite: 57, 638]
    $sql = "DELETE FROM users WHERE user_id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_list_users.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_list_users.php");
}
?>