<?php
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'violation':
        $res = $conn->query("SELECT Violation_id, Violation_name FROM violation");
        break;
    case 'vehicle':
        $res = $conn->query("SELECT vehicle_id, license_plate FROM vehicle");
        break;
    case 'area':
        $res = $conn->query("SELECT Area_id, Area_name FROM parking_area");
        break;
    default:
        echo json_encode([]);
        exit;
}

$options = [];
while ($row = $res->fetch_assoc()) $options[] = $row;
echo json_encode($options);
