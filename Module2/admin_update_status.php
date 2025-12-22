<?php
require_once '../config.php';
requireLogin();

if (isset($_GET['id']) && isset($_GET['current'])) {
    $id = $_GET['id'];
    $new_status = ($_GET['current'] == 'Available') ? 'Maintenance' : 'Available';

    $sql = "UPDATE parking_space SET Current_status = '$new_status' WHERE Space_id = '$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_view.php?area_id=" . $_GET['area_id']);
    }
}
?>